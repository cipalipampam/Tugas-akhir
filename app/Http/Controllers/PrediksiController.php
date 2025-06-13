<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\StudentValue;
use App\Models\DistanceCalculation;
use App\Models\WeightCalculation;
use App\Models\WeightRatio;
use App\Models\Prediction;
use App\Models\GraduationRule;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\Log;

class PrediksiController extends Controller
{
    // ==================== PARAMETER & KONSTANTA ====================
    private const FUZZY_STRENGTH = 2.0; // m parameter untuk fuzzy strength
    private const EPSILON = 0.001; // Small value to avoid division by zero
    private const FEATURE_WEIGHTS = [
        'avg_semester_score' => 0.4,
        'usp_score' => 0.3,
        'kerapian' => 0.1,
        'kerajinan' => 0.1,
        'sikap' => 0.1
    ];

    // ==================== ROUTE HANDLERS ====================
    public function index()
    {
        return view('pages.prediksi');
    }

    public function showResult($id)
    {
        $testStudent = Student::with('studentValues')->findOrFail($id);

        // Ambil data hasil distance dan weight yang sudah dihitung sebelumnya
        $distanceData = DistanceCalculation::with(['trainingStudent', 'weightCalculation'])
            ->where('test_student_id', $testStudent->id)
            ->get();

        $results = $distanceData->map(function ($data) {
            return [
                'nisn' => $data->trainingStudent->nisn ?? '-',
                'nama' => $data->trainingStudent->name ?? '-',
                'distance' => $data->distance,
                'weight' => optional($data->weightCalculation)->weight,
                'true_status' => optional($data->trainingStudent)->true_status ?? '-'
            ];
        });

        return view('pages.prediksi', compact('testStudent', 'results'));
    }

    public function processAndPredict(Request $request)
    {
        $data = $request->input('data');
        $k = $request->input('k_value', 5);  // Default K value to 5 if not provided

        if (!$data || !is_array($data)) {
            return back()->with('error', 'Input data tidak valid.');
        }

        // Extract name and NISN
        $name = $data['nama'] ?? '';
        $nisn = $data['nisn'] ?? '';

        if (empty($name) || empty($nisn)) {
            return back()->with('error', 'Nama dan NISN harus diisi.');
        }

        $testStudent = Student::create([
            'nisn' => $nisn,
            'name' => $name,
            'jenis_data' => 'testing',
            'true_status' => null
        ]);

        // Remove name and NISN from data before creating student values
        unset($data['nama'], $data['nisn']);

        // Create student values for remaining data
        foreach ($data as $key => $value) {
            if (trim($key) !== '' && $value !== null) {
            StudentValue::create([
                'student_id' => $testStudent->id,
                'key' => $key,
                'value' => $value
            ]);
            }
        }

        $this->predictForStudent($testStudent, $k);

        return redirect()->route('prediction.result', ['id' => $testStudent->id])
            ->with('success', 'Prediksi berhasil dilakukan.');
    }

    // ==================== FUNGSI PENENTUAN STATUS KELULUSAN ====================
    private function tentukanStatusKelulusan($fitur)
    {
        // Ambil semua aturan kelulusan dan urutkan berdasarkan priority
        $rules = GraduationRule::orderBy('priority')->get();

        foreach ($rules as $rule) {
            $attribute = $rule->attribute;
            $operator = $rule->operator;
            $value = $rule->value;
            $category = $rule->category;

            // Cek apakah atribut ada dalam fitur
            if (!isset($fitur[$attribute])) {
                continue;
            }

            $fiturValue = $this->convertToNumeric($fitur[$attribute]);
            $ruleValue = floatval($value);

            // Evaluasi aturan berdasarkan operator
            $isRuleMet = match($operator) {
                '=' => $fiturValue == $ruleValue,
                '>=' => $fiturValue >= $ruleValue,
                '<=' => $fiturValue <= $ruleValue,
                '>' => $fiturValue > $ruleValue,
                '<' => $fiturValue < $ruleValue,
                default => false
            };

            if ($isRuleMet) {
                return $category;
            }
        }

        // Jika tidak ada aturan yang terpenuhi, gunakan hasil Fuzzy KNN
        return null;
    }

    // ==================== FUNGSI UTAMA FUZZY KNN ====================
    private function predictForStudent($testStudent, $k = 5)
    {
        // 1. Persiapan Data
        $minMax = $this->getMinMaxPerFeatureFromTraining();
        if (empty($minMax)) {
            \Log::error('MinMax data kosong, pastikan data latih sudah ada.');
            return;
        }

        // 2. Normalisasi Data Testing
        $testValues = $testStudent->studentValues->pluck('value', 'key');
        $testValues = $this->processAndNormalizeData($testValues, $minMax);

        // 3. Ambil Data Training
        $trainingStudents = Student::where('jenis_data', 'training')
            ->whereNotNull('true_status')
            ->where('id', '!=', $testStudent->id)
            ->with('studentValues')
            ->get();

        // 4. Hitung Jarak ke Setiap Data Training
        $distances = [];
        foreach ($trainingStudents as $train) {
            $trainValues = $train->studentValues->pluck('value', 'key');
            $trainValues = $this->processAndNormalizeData($trainValues, $minMax);

            $distance = $this->calculateFeatureWeightedDistance($testValues, $trainValues);
            $distances[] = [
                'student' => $train,
                'distance' => $distance
            ];
        }

        // 5. Ambil K Tetangga Terdekat
        $neighbors = collect($distances)->sortBy('distance')->take($k);

        // 6. Inisialisasi Bobot Kelas
        $classWeights = [
            'lulus' => 0,
            'lulus bersyarat' => 0,
            'tidak lulus' => 0,
        ];

        // 7. Hitung Bobot Fuzzy untuk Setiap Tetangga
        foreach ($neighbors as $item) {
            $train = $item['student'];
            $distance = $item['distance'];

            // Simpan Perhitungan Jarak
            $distanceCalc = DistanceCalculation::create([
                'test_student_id' => $testStudent->id,
                'training_data_id' => $train->id,
                'distance' => $distance
            ]);

            // Hitung Bobot Fuzzy
            $weight = $this->calculateFuzzyWeight($distance);

            // Simpan Perhitungan Bobot
            WeightCalculation::create([
                'distance_calculation_id' => $distanceCalc->id,
                'weight' => $weight
            ]);

            // Akumulasi Bobot untuk Setiap Kelas
            $status = $train->true_status;
            if ($status && isset($classWeights[$status])) {
                $classWeights[$status] += $weight;
            }
        }

        // 8. Hitung Total Bobot
        $totalWeight = array_sum($classWeights);

        // 9. Simpan Rasio Bobot untuk Setiap Kelas
        WeightRatio::where('test_student_id', $testStudent->id)->delete();
        foreach ($classWeights as $status => $weight) {
            WeightRatio::create([
                'test_student_id' => $testStudent->id,
                'class' => $status,
                'total_weight' => $weight,
                'weight_ratio' => $totalWeight > 0 ? $weight / $totalWeight : 0,
            ]);
        }

        // 10. Tentukan Kelas dengan Bobot Tertinggi (Fuzzy KNN)
        $fuzzyKnnStatus = collect($classWeights)->sortDesc()->keys()->first();

        // 11. Cek Aturan Kelulusan
        $fitur = $testStudent->studentValues->pluck('value', 'key')->toArray();
        $ruleBasedStatus = $this->tentukanStatusKelulusan($fitur);

        // 12. Simpan Hasil Prediksi (Prioritaskan aturan kelulusan jika ada)
        $finalStatus = $ruleBasedStatus ?? $fuzzyKnnStatus;

        Prediction::create([
            'test_student_id' => $testStudent->id,
            'predicted_status' => $finalStatus,
            'k_value' => $k
        ]);
    }

    // ==================== FUNGSI PENDUKUNG ====================
private function normalize($value, $min, $max)
{
        if ($value === null) {
            return 0;
        }

    $value = floatval($value);
    $min = floatval($min);
    $max = floatval($max);

    if ($max - $min == 0) {
        return 0; // Hindari pembagian dengan nol
    }

    // Normalisasi dan batasi hasil antara 0 dan 1
    $normalized = ($value - $min) / ($max - $min);
    return max(0, min(1, $normalized));
}

private function processAndNormalizeData($studentValues, $minMax)
{
    $normalizedValues = [];

    foreach ($studentValues as $key => $value) {
        $val = $this->convertToNumeric($value);

        if (isset($minMax[$key])) {
            $min = $minMax[$key]['min'];
            $max = $minMax[$key]['max'];
            $normalizedValues[$key] = $this->normalize($val, $min, $max);
        } else {
                // Jika min/max tidak ditemukan, gunakan bobot default
            $normalizedValues[$key] = 0;
                \Log::warning("Feature {$key} tidak memiliki min/max values");
        }
    }

    return $normalizedValues;
}

private function getMinMaxPerFeatureFromTraining()
{
    // Ambil semua atribut dari data siswa uji
    $testStudent = Student::where('jenis_data', 'testing')
        ->latest()
        ->first();

    if (!$testStudent) {
        \Log::error('Tidak ada data siswa uji yang ditemukan.');
        return [];
    }

    $testAttributes = $testStudent->studentValues->pluck('key')->unique()->toArray();
    $minMax = [];

    // Ambil data latih
    $dataLatih = Student::where('jenis_data', 'training')
        ->whereNotNull('true_status')
        ->with('studentValues')
        ->get();

    foreach ($testAttributes as $attribute) {
        if ($dataLatih->isNotEmpty()) {
            // Jika ada data latih, gunakan min dan max dari data latih
            $values = $dataLatih->flatMap(function ($student) use ($attribute) {
                return $student->studentValues
                    ->where('key', $attribute)
                    ->pluck('value')
                    ->map(function ($value) {
                        return $this->convertToNumeric($value);
                    });
            })->filter()->values();

            if ($values->isNotEmpty()) {
                $minMax[$attribute] = [
                    'min' => $values->min(),
                    'max' => $values->max()
                ];
            }
        } else {
            // Jika tidak ada data latih, gunakan nilai default berdasarkan atribut
            switch ($attribute) {
                case 'rata_rata':
                case 'usp':
                    $minMax[$attribute] = ['min' => 0, 'max' => 100];
                    break;
                case 'sikap':
                case 'kerajinan':
                case 'kerapian':
                    $minMax[$attribute] = ['min' => 0, 'max' => 1];
                    break;
                default:
                    $minMax[$attribute] = ['min' => 0, 'max' => 100];
            }
        }
    }

    return $minMax;
}

    private function calculateFuzzyMembership($distance, $m = self::FUZZY_STRENGTH)
    {
        // Fungsi keanggotaan fuzzy menggunakan Euclidean distance
        // μ(x) = 1 / (1 + ε + d²)
        // Dimana d adalah jarak Euclidean dan ε adalah nilai kecil untuk menghindari pembagian nol
        $adjustedDistance = $distance + self::EPSILON;
        return 1 / (1 + pow($adjustedDistance, 2));
    }

    private function calculateFuzzyWeight($distance, $m = self::FUZZY_STRENGTH)
    {
        // Perhitungan bobot fuzzy menggunakan Euclidean distance
        // w = 1 / (1 + ε + d²)
        return $this->calculateFuzzyMembership($distance, $m);
    }

    private function calculateFeatureWeightedDistance($testValues, $trainValues)
    {
        $sum = 0;
        $totalWeight = 0;

        foreach ($testValues as $key => $value) {
            if (isset($trainValues[$key])) {
                $testVal = $this->convertToNumeric($value);
                $trainVal = $this->convertToNumeric($trainValues[$key]);
                
                // Pembobotan fitur sesuai kaidah
                $weight = self::FEATURE_WEIGHTS[$key] ?? 0.1;
                $sum += $weight * pow($testVal - $trainVal, 2);
                $totalWeight += $weight;
            }
        }

        // Normalisasi jarak dengan total bobot
        return $totalWeight > 0 ? sqrt($sum / $totalWeight) : 0;
    }

    private function convertToNumeric($value)
    {
        $map = [
            'baik' => 1,
            'cukup' => 0.5,
            'kurang' => 0
        ];

        if (is_numeric($value)) {
            return floatval($value);
        }

        $value = strtolower(trim($value));
        return $map[$value] ?? 0;
    }

    public function uploadExcelDanPrediksi(Request $request)
    {
        try {
            if (!$request->hasFile('excel_file')) {
                return response()->json(['status' => 'error', 'message' => 'File tidak ditemukan pada request!']);
            }

            $file = $request->file('excel_file');
            $k = 5; // Fixed K value

            // Validate file extension
            $extension = $file->getClientOriginalExtension();
            if (!in_array($extension, ['xlsx', 'xls', 'csv'])) {
                return back()->with('error', 'Format file tidak didukung. Gunakan file Excel (.xlsx, .xls) atau CSV.');
            }

            $spreadsheet = IOFactory::load($file->getPathname());
            $worksheet = $spreadsheet->getActiveSheet();

            $rows = [];
            foreach ($worksheet->getRowIterator() as $index => $row) {
                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(false);
                $cells = [];

                foreach ($cellIterator as $cell) {
                    $cells[] = $cell->getFormattedValue();
                }

                if (array_filter($cells)) {
                    $rows[] = $cells;
                }
            }

            if (count($rows) < 2) {
                return back()->with('error', 'Data tidak cukup (minimal header dan satu baris data).');
            }

            $headers = $rows[0];
            $dataRows = array_slice($rows, 1);
            $formattedRows = [];

            // Validate required headers
            $requiredHeaders = ['nama', 'nisn', 'semester_1', 'semester_2', 'semester_3', 'semester_4', 'semester_5', 'semester_6', 'usp', 'sikap', 'kerapian', 'kerajinan'];
            $missingHeaders = array_diff($requiredHeaders, array_map('strtolower', $headers));
            
            if (!empty($missingHeaders)) {
                return back()->with('error', 'Format Excel tidak sesuai. Header yang diperlukan: ' . implode(', ', $missingHeaders));
            }

            foreach ($dataRows as $dataRow) {
                $formatted = [];
                foreach ($headers as $i => $header) {
                    $formatted[strtolower(trim($header))] = trim($dataRow[$i] ?? '');
                }
                $formattedRows[] = $formatted;
            }

            DB::beginTransaction();
            $insertedStudents = [];
            $existingNISNs = [];

            foreach ($formattedRows as $row) {
                $name = trim($row['nama'] ?? '');
                $nisn = trim($row['nisn'] ?? '');

                if ($name === '' || $nisn === '') {
                    continue;
                }

                // Check for duplicate NISN
                if (in_array($nisn, $existingNISNs)) {
                    DB::rollback();
                    return back()->with('error', "NISN duplikat ditemukan: $nisn");
                }
                $existingNISNs[] = $nisn;

                // Check if NISN already exists in database
                if (Student::where('nisn', $nisn)->exists()) {
                    DB::rollback();
                    return back()->with('error', "NISN sudah terdaftar: $nisn");
                }

                // Simpan siswa sebagai testing
                $student = Student::create([
                    'nisn' => $nisn,
                    'name' => $name,
                    'true_status' => null,
                    'jenis_data' => 'testing',
                ]);

                // Simpan nilai-nilai atribut (kecuali nama dan NISN)
                $excludedFields = ['nama', 'nisn', 'status', 'jenis_data'];
                foreach ($row as $key => $value) {
                    if (!in_array(strtolower(trim($key)), $excludedFields) && trim($key) !== '' && $value !== null) {
                        StudentValue::create([
                            'student_id' => $student->id,
                            'key' => trim($key),
                            'value' => trim($value),
                        ]);
                    }
                }

                $insertedStudents[] = $student;
            }

            if (empty($insertedStudents)) {
                DB::rollback();
                return back()->with('error', 'Tidak ada data valid yang dapat diproses.');
            }

            DB::commit();

            // Jalankan prediksi untuk setiap siswa yang baru diinsert
            foreach ($insertedStudents as $testStudent) {
                $this->predictForStudent($testStudent, $k);
            }

            // If we have at least one student, redirect to the result page for the first student
            if (count($insertedStudents) > 0) {
                return redirect()->route('prediction.result', ['id' => $insertedStudents[0]->id])
                    ->with('success', 'Data Excel berhasil diproses dan diprediksi.');
            }

            return response()->json(['status' => 'success', 'message' => 'Data berhasil diproses dan diprediksi.']);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Upload & Prediksi Error: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
