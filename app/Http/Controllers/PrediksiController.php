<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\StudentValue;
use App\Models\DistanceCalculation;
use App\Models\WeightCalculation;
use App\Models\WeightRatio;
use App\Models\Prediction;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\Log;

class PrediksiController extends Controller
{

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

private function normalize($value, $min, $max)
{
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
            // Jika min/max tidak ditemukan, diasumsikan 0 (netral)
            $normalizedValues[$key] = 0;
        }
    }

    return $normalizedValues;
}

private function getMinMaxPerFeatureFromTraining()
{
    $trainingStudents = Student::where('jenis_data', 'training')
        ->whereNotNull('true_status')
        ->with('studentValues')
        ->get();

    $featureGroups = [];

    foreach ($trainingStudents as $student) {
        foreach ($student->studentValues as $value) {
            $key = $value->key;
            $numericValue = $this->convertToNumeric($value->value);
            $featureGroups[$key][] = $numericValue;
        }
    }

    $minMax = [];

    foreach ($featureGroups as $key => $values) {
        $minMax[$key] = [
            'min' => min($values),
            'max' => max($values)
        ];
    }

    return $minMax;
}


    private function predictForStudent($testStudent, $k = 5)
    {
        $minMax = $this->getMinMaxPerFeatureFromTraining();

        // Cek apakah $minMax sudah benar
        if (empty($minMax)) {
            \Log::error('MinMax data kosong, pastikan data latih sudah ada.');
            return;
        }


        $testValues = $testStudent->studentValues->pluck('value', 'key');
        $testValues = $this->processAndNormalizeData($testValues, $minMax);

        $trainingStudents = Student::where('jenis_data', 'training')
            ->whereNotNull('true_status')
            ->where('id', '!=', $testStudent->id)
            ->with('studentValues')
            ->get();

        $distances = [];

        foreach ($trainingStudents as $train) {
            $trainValues = $train->studentValues->pluck('value', 'key');
            $trainValues = $this->processAndNormalizeData($trainValues, $minMax);

            $distance = $this->calculateEuclideanDistance($testValues, $trainValues);
            $distances[] = [
                'student' => $train,
                'distance' => $distance
            ];
        }

        // Urutkan berdasarkan distance dan ambil K terdekat
        $neighbors = collect($distances)->sortBy('distance')->take($k);

        $classWeights = [
            'lulus' => 0,
            'lulus bersyarat' => 0,
            'tidak lulus' => 0,
        ];

        foreach ($neighbors as $item) {
            $train = $item['student'];
            $distance = $item['distance'];

            $distanceCalc = DistanceCalculation::create([
                'test_student_id' => $testStudent->id,
                'training_data_id' => $train->id,
                'distance' => $distance
            ]);

            $epsilon = 0.01; // nilai kecil untuk menghindari pembagian nol atau angka sangat kecil
            $adjustedDistance = $distance + $epsilon;

            $weight = 1 / ($adjustedDistance * $adjustedDistance);

            WeightCalculation::create([
                'distance_calculation_id' => $distanceCalc->id,
                'weight' => $weight
            ]);


            $status = $train->true_status;
            if ($status && isset($classWeights[$status])) {
                $classWeights[$status] += $weight;
            }
        }

        $totalWeight = array_sum($classWeights);

        WeightRatio::where('test_student_id', $testStudent->id)->delete();
        foreach ($classWeights as $status => $weight) {
            WeightRatio::create([
                'test_student_id' => $testStudent->id,
                'class' => $status,
                'total_weight' => $weight,
                'weight_ratio' => $totalWeight > 0 ? $weight / $totalWeight : 0,
            ]);
        }

        $predictedStatus = collect($classWeights)->sortDesc()->keys()->first();

        Prediction::create([
            'test_student_id' => $testStudent->id,
            'predicted_status' => $predictedStatus,
            'k_value' => $k
        ]);
    }

    private function convertToNumeric($value)
    {
        $map = [
            'baik' => 3,
            'cukup' => 2,
            'kurang' => 1
        ];

        if (is_numeric($value)) {
            return floatval($value);
        }

        $value = strtolower(trim($value));
        return $map[$value] ?? 0;
    }

    private function calculateEuclideanDistance($test, $train)
    {
        $sum = 0;
        foreach ($test as $key => $value) {
            if (isset($train[$key])) {
                $testVal = $this->convertToNumeric($value);
                $trainVal = $this->convertToNumeric($train[$key]);

                \Log::info("Key: $key | Test: $value ($testVal) | Train: " . $train[$key] . " ($trainVal)");

                $sum += pow($testVal - $trainVal, 2);
            }
        }
        return sqrt($sum);
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
