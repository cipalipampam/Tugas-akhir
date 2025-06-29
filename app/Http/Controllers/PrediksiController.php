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
        Log::info("=== MENAMPILKAN HASIL PREDIKSI ===", ['student_id' => $id]);
        
        $testStudent = Student::with('studentValues')->findOrFail($id);
        
        Log::info("Data siswa yang ditemukan", [
            'student_id' => $testStudent->id,
            'nisn' => $testStudent->nisn,
            'name' => $testStudent->name,
            'student_values_count' => $testStudent->studentValues->count()
        ]);

        // Ambil data hasil distance dan weight yang sudah dihitung sebelumnya
        $distanceData = DistanceCalculation::with(['trainingStudent', 'weightCalculation'])
            ->where('test_student_id', $testStudent->id)
            ->get();

        Log::info("Data perhitungan jarak yang ditemukan", [
            'distance_calculations_count' => $distanceData->count()
        ]);

        $results = $distanceData->map(function ($data) {
            return [
                'nisn' => $data->trainingStudent->nisn ?? '-',
                'nama' => $data->trainingStudent->name ?? '-',
                'distance' => $data->distance,
                'weight' => optional($data->weightCalculation)->weight,
                'true_status' => optional($data->trainingStudent)->true_status ?? '-'
            ];
        });

        Log::info("Hasil prediksi yang akan ditampilkan", [
            'results_count' => $results->count(),
            'results_summary' => $results->take(3)->toArray() // Ambil 3 hasil pertama untuk log
        ]);

        // Ambil prediksi final
        $prediction = Prediction::where('test_student_id', $testStudent->id)->first();
        $predictedStatus = $prediction ? $prediction->predicted_status : null;
        if ($prediction) {
            Log::info("Prediksi final", [
                'predicted_status' => $prediction->predicted_status,
                'k_value' => $prediction->k_value
            ]);
        }

        Log::info("=== SELESAI MENAMPILKAN HASIL PREDIKSI ===");

        return view('pages.prediksi', compact('testStudent', 'results', 'predictedStatus'));
    }

    public function processAndPredict(Request $request)
    {
        Log::info("=== MULAI PROSES PREDIKSI MANUAL ===");
        
        $data = $request->input('data');
        $k = $request->input('k_value', 5);  // Default K value to 5 if not provided

        Log::info("Data input yang diterima", [
            'data' => $data,
            'k_value' => $k
        ]);

        if (!$data || !is_array($data)) {
            Log::error("Input data tidak valid", ['data' => $data]);
            return back()->with('error', 'Input data tidak valid.');
        }

        // Extract name and NISN
        $name = $data['nama'] ?? '';
        $nisn = $data['nisn'] ?? '';

        Log::info("Data siswa yang akan diproses", [
            'name' => $name,
            'nisn' => $nisn
        ]);

        if (empty($name) || empty($nisn)) {
            Log::error("Nama atau NISN kosong", ['name' => $name, 'nisn' => $nisn]);
            return back()->with('error', 'Nama dan NISN harus diisi.');
        }

        Log::info("Membuat record siswa baru");
        $testStudent = Student::create([
            'nisn' => $nisn,
            'name' => $name,
            'jenis_data' => 'testing',
            'true_status' => null
        ]);

        Log::info("Siswa berhasil dibuat", [
            'student_id' => $testStudent->id,
            'nisn' => $testStudent->nisn,
            'name' => $testStudent->name
        ]);

        // Remove name and NISN from data before creating student values
        unset($data['nama'], $data['nisn']);

        Log::info("Menyimpan nilai-nilai siswa");
        // Create student values for remaining data
        foreach ($data as $key => $value) {
            if (trim($key) !== '' && $value !== null) {
                StudentValue::create([
                    'student_id' => $testStudent->id,
                    'key' => $key,
                    'value' => $value
                ]);
                
                Log::info("Nilai siswa disimpan", [
                    'student_id' => $testStudent->id,
                    'key' => $key,
                    'value' => $value
                ]);
            }
        }

        Log::info("Memulai proses prediksi untuk siswa", ['student_id' => $testStudent->id]);
        $this->predictForStudent($testStudent, $k);

        // Ambil hasil prediksi manual
        $prediction = Prediction::where('test_student_id', $testStudent->id)->first();
        $neighbors = \App\Models\DistanceCalculation::with(['trainingStudent', 'weightCalculation'])
            ->where('test_student_id', $testStudent->id)
            ->orderBy('distance')
            ->limit($k)
            ->get()
            ->map(function($item) {
                return [
                    'nisn' => $item->trainingStudent->nisn ?? '-',
                    'nama' => $item->trainingStudent->name ?? '-',
                    'true_status' => $item->trainingStudent->true_status ?? '-',
                    'distance' => $item->distance,
                    'weight' => optional($item->weightCalculation)->weight,
                ];
            })->toArray();
        $ratios = \App\Models\WeightRatio::where('test_student_id', $testStudent->id)->get(['class', 'total_weight', 'weight_ratio']);
        $manualPrediction = [
            'nisn' => $testStudent->nisn,
            'name' => $testStudent->name,
            'status' => $prediction ? $prediction->predicted_status : '-',
            'neighbors' => $neighbors,
            'ratios' => $ratios
        ];
        return view('pages.prediksi', [
            'manualPrediction' => $manualPrediction,
            'activeInputMethod' => 'manual'
        ])->with('success', 'Prediksi berhasil dilakukan.');
    }

    // ==================== FUNGSI PENENTUAN STATUS KELULUSAN ====================
    private function tentukanStatusKelulusan($fitur)
    {
        Log::info("=== MULAI PENENTUAN STATUS KELULUSAN ===", ['fitur' => $fitur]);
        
        // Ambil semua aturan kelulusan dan urutkan berdasarkan priority
        $rules = GraduationRule::orderBy('priority')->get();
        Log::info("Aturan kelulusan yang ditemukan", ['rules_count' => $rules->count()]);

        foreach ($rules as $rule) {
            $attribute = $rule->attribute;
            $operator = $rule->operator;
            $value = $rule->value;
            $category = $rule->category;

            Log::info("Mengevaluasi aturan", [
                'attribute' => $attribute,
                'operator' => $operator,
                'value' => $value,
                'category' => $category
            ]);

            // Cek apakah atribut ada dalam fitur
            if (!isset($fitur[$attribute])) {
                Log::info("Atribut tidak ditemukan dalam fitur", ['attribute' => $attribute]);
                continue;
            }

            $fiturValue = $this->convertToNumeric($fitur[$attribute]);
            $ruleValue = floatval($value);

            Log::info("Nilai fitur vs aturan", [
                'fitur_value' => $fiturValue,
                'rule_value' => $ruleValue,
                'operator' => $operator
            ]);

            // Evaluasi aturan berdasarkan operator
            $isRuleMet = match($operator) {
                '=' => $fiturValue == $ruleValue,
                '>=' => $fiturValue >= $ruleValue,
                '<=' => $fiturValue <= $ruleValue,
                '>' => $fiturValue > $ruleValue,
                '<' => $fiturValue < $ruleValue,
                default => false
            };

            Log::info("Hasil evaluasi aturan", [
                'is_rule_met' => $isRuleMet,
                'category' => $category
            ]);

            if ($isRuleMet) {
                Log::info("=== ATURAN KELULUSAN DITEMUKAN ===", ['category' => $category]);
                return $category;
            }
        }

        // Jika tidak ada aturan yang terpenuhi, gunakan hasil Fuzzy KNN
        Log::info("=== TIDAK ADA ATURAN KELULUSAN YANG TERPENUHI ===");
        return null;
    }

    // ==================== FUNGSI UTAMA FUZZY KNN ====================
    private function predictForStudent($testStudent, $k = 5)
    {
        Log::info("=== MULAI PREDIKSI UNTUK SISWA ===", [
            'student_id' => $testStudent->id,
            'nisn' => $testStudent->nisn,
            'name' => $testStudent->name,
            'k_value' => $k
        ]);

        // 1. Persiapan Data
        Log::info("1. Persiapan Data - Mencari MinMax dari data training");
        $minMax = $this->getMinMaxPerFeatureFromTraining();
        if (empty($minMax)) {
            Log::error('MinMax data kosong, pastikan data latih sudah ada.');
            return;
        }
        Log::info("MinMax data berhasil diperoleh", ['minMax' => $minMax]);

        // 2. Normalisasi Data Testing
        Log::info("2. Normalisasi Data Testing");
        $testValues = $testStudent->studentValues->pluck('value', 'key');
        Log::info("Data testing sebelum normalisasi", ['testValues' => $testValues->toArray()]);
        
        $testValues = $this->processAndNormalizeData($testValues, $minMax);
        Log::info("Data testing setelah normalisasi", ['normalizedTestValues' => $testValues]);

        // 3. Ambil Data Training
        Log::info("3. Mengambil Data Training");
        $trainingStudents = Student::where('jenis_data', 'training')
            ->whereNotNull('true_status')
            ->where('id', '!=', $testStudent->id)
            ->with('studentValues')
            ->get();
        
        Log::info("Jumlah data training yang ditemukan", ['count' => $trainingStudents->count()]);

        // 4. Hitung Jarak ke Setiap Data Training
        Log::info("4. Menghitung Jarak ke Setiap Data Training");
        $distances = [];
        foreach ($trainingStudents as $train) {
            $trainValues = $train->studentValues->pluck('value', 'key');
            $trainValues = $this->processAndNormalizeData($trainValues, $minMax);

            $distance = $this->calculateFeatureWeightedDistance($testValues, $trainValues);
            $distances[] = [
                'student' => $train,
                'distance' => $distance
            ];
            
            Log::info("Jarak ke training student", [
                'training_id' => $train->id,
                'training_nisn' => $train->nisn,
                'training_name' => $train->name,
                'distance' => $distance,
                'true_status' => $train->true_status
            ]);
        }

        // 5. Ambil K Tetangga Terdekat
        Log::info("5. Mengambil K Tetangga Terdekat", ['k' => $k]);
        $neighbors = collect($distances)->sortBy('distance')->take($k);
        
        Log::info("K tetangga terdekat", [
            'neighbors' => $neighbors->map(function($item) {
                return [
                    'id' => $item['student']->id,
                    'nisn' => $item['student']->nisn,
                    'name' => $item['student']->name,
                    'distance' => $item['distance'],
                    'true_status' => $item['student']->true_status
                ];
            })->toArray()
        ]);

        // 6. Inisialisasi Bobot Kelas
        Log::info("6. Inisialisasi Bobot Kelas");
        $classWeights = [
            'lulus' => 0,
            'lulus bersyarat' => 0,
            'tidak lulus' => 0,
        ];
        Log::info("Bobot kelas awal", $classWeights);

        // 7. Hitung Bobot Fuzzy untuk Setiap Tetangga
        Log::info("7. Menghitung Bobot Fuzzy untuk Setiap Tetangga");
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
            Log::info("Perhitungan bobot fuzzy", [
                'training_id' => $train->id,
                'training_nisn' => $train->nisn,
                'distance' => $distance,
                'fuzzy_weight' => $weight,
                'true_status' => $train->true_status
            ]);

            // Simpan Perhitungan Bobot
            WeightCalculation::create([
                'distance_calculation_id' => $distanceCalc->id,
                'weight' => $weight
            ]);

            // Akumulasi Bobot untuk Setiap Kelas
            $status = $train->true_status;
            if ($status && isset($classWeights[$status])) {
                $classWeights[$status] += $weight;
                Log::info("Akumulasi bobot untuk kelas", [
                    'status' => $status,
                    'weight_added' => $weight,
                    'total_weight' => $classWeights[$status]
                ]);
            }
        }

        // 8. Hitung Total Bobot
        Log::info("8. Menghitung Total Bobot");
        $totalWeight = array_sum($classWeights);
        Log::info("Total bobot semua kelas", ['totalWeight' => $totalWeight]);

        // 9. Simpan Rasio Bobot untuk Setiap Kelas
        Log::info("9. Menyimpan Rasio Bobot untuk Setiap Kelas");
        WeightRatio::where('test_student_id', $testStudent->id)->delete();
        foreach ($classWeights as $status => $weight) {
            $weightRatio = $totalWeight > 0 ? $weight / $totalWeight : 0;
            WeightRatio::create([
                'test_student_id' => $testStudent->id,
                'class' => $status,
                'total_weight' => $weight,
                'weight_ratio' => $weightRatio,
            ]);
            
            Log::info("Rasio bobot untuk kelas", [
                'status' => $status,
                'total_weight' => $weight,
                'weight_ratio' => $weightRatio
            ]);
        }

        // 10. Tentukan Kelas dengan Bobot Tertinggi (Fuzzy KNN)
        Log::info("10. Menentukan Kelas dengan Bobot Tertinggi (Fuzzy KNN)");
        $fuzzyKnnStatus = collect($classWeights)->sortDesc()->keys()->first();
        Log::info("Hasil Fuzzy KNN", [
            'fuzzyKnnStatus' => $fuzzyKnnStatus,
            'classWeights' => $classWeights
        ]);

        // 11. Cek Aturan Kelulusan
        Log::info("11. Mengecek Aturan Kelulusan");
        $fitur = $testStudent->studentValues->pluck('value', 'key')->toArray();
        Log::info("Fitur untuk pengecekan aturan", ['fitur' => $fitur]);
        
        $ruleBasedStatus = $this->tentukanStatusKelulusan($fitur);
        Log::info("Hasil pengecekan aturan kelulusan", ['ruleBasedStatus' => $ruleBasedStatus]);

        // 12. Simpan Hasil Prediksi (Prioritaskan aturan kelulusan jika ada)
        $finalStatus = $ruleBasedStatus ?? $fuzzyKnnStatus;
        Log::info("12. Status Final Prediksi", [
            'ruleBasedStatus' => $ruleBasedStatus,
            'fuzzyKnnStatus' => $fuzzyKnnStatus,
            'finalStatus' => $finalStatus
        ]);

        Prediction::create([
            'test_student_id' => $testStudent->id,
            'predicted_status' => $finalStatus,
            'k_value' => $k
        ]);

        Log::info("=== SELESAI PREDIKSI UNTUK SISWA ===", [
            'student_id' => $testStudent->id,
            'nisn' => $testStudent->nisn,
            'final_prediction' => $finalStatus,
            'k_value' => $k
        ]);
    }

    // ==================== FUNGSI PENDUKUNG ====================
private function normalize($value, $min, $max)
{
        if ($value === null) {
            Log::info("Nilai null, return 0");
            return 0;
        }

    $value = floatval($value);
    $min = floatval($min);
    $max = floatval($max);

    if ($max - $min == 0) {
        Log::info("Min dan max sama, return 0 untuk menghindari pembagian nol", [
            'value' => $value,
            'min' => $min,
            'max' => $max
        ]);
        return 0; // Hindari pembagian dengan nol
    }

    // Normalisasi dan batasi hasil antara 0 dan 1
    $normalized = ($value - $min) / ($max - $min);
    $result = max(0, min(1, $normalized));
    
    Log::info("Normalisasi nilai", [
        'original_value' => $value,
        'min' => $min,
        'max' => $max,
        'normalized' => $normalized,
        'final_result' => $result
    ]);
    
    return $result;
}

private function processAndNormalizeData($studentValues, $minMax)
{
    Log::info("=== MULAI PROSES NORMALISASI DATA ===");
    Log::info("Data yang akan dinormalisasi", ['studentValues' => $studentValues->toArray()]);
    Log::info("MinMax yang digunakan", ['minMax' => $minMax]);
    
    $normalizedValues = [];

    foreach ($studentValues as $key => $value) {
        $val = $this->convertToNumeric($value);

        if (isset($minMax[$key])) {
            $min = $minMax[$key]['min'];
            $max = $minMax[$key]['max'];
            $normalizedValues[$key] = $this->normalize($val, $min, $max);
            
            Log::info("Normalisasi fitur", [
                'feature' => $key,
                'original_value' => $value,
                'numeric_value' => $val,
                'min' => $min,
                'max' => $max,
                'normalized_value' => $normalizedValues[$key]
            ]);
        } else {
                // Jika min/max tidak ditemukan, gunakan bobot default
            $normalizedValues[$key] = 0;
            Log::warning("Feature {$key} tidak memiliki min/max values, menggunakan 0");
        }
    }

    Log::info("=== SELESAI PROSES NORMALISASI DATA ===", ['normalizedValues' => $normalizedValues]);
    return $normalizedValues;
}

private function getMinMaxPerFeatureFromTraining()
{
    Log::info("=== MULAI MENCARI MINMAX DARI DATA TRAINING ===");
    
    // Ambil semua atribut dari data siswa uji
    $testStudent = Student::where('jenis_data', 'testing')
        ->latest()
        ->first();

    if (!$testStudent) {
        Log::error('Tidak ada data siswa uji yang ditemukan.');
        return [];
    }

    Log::info("Siswa uji yang digunakan untuk referensi", [
        'student_id' => $testStudent->id,
        'nisn' => $testStudent->nisn,
        'name' => $testStudent->name
    ]);

    $testAttributes = $testStudent->studentValues->pluck('key')->unique()->toArray();
    Log::info("Atribut yang akan dicari minmax", ['attributes' => $testAttributes]);
    
    $minMax = [];

    // Ambil data latih
    $dataLatih = Student::where('jenis_data', 'training')
        ->whereNotNull('true_status')
        ->with('studentValues')
        ->get();

    Log::info("Data latih yang ditemukan", [
        'training_count' => $dataLatih->count()
    ]);

    foreach ($testAttributes as $attribute) {
        Log::info("Mencari minmax untuk atribut", ['attribute' => $attribute]);
        
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
                $min = $values->min();
                $max = $values->max();
                $minMax[$attribute] = [
                    'min' => $min,
                    'max' => $max
                ];
                
                Log::info("Minmax dari data latih", [
                    'attribute' => $attribute,
                    'min' => $min,
                    'max' => $max,
                    'values_count' => $values->count()
                ]);
            } else {
                Log::warning("Tidak ada nilai untuk atribut dalam data latih", ['attribute' => $attribute]);
            }
        } else {
            // Jika tidak ada data latih, gunakan nilai default berdasarkan atribut
            Log::info("Tidak ada data latih, menggunakan nilai default untuk atribut", ['attribute' => $attribute]);
            
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
            
            Log::info("Nilai default yang digunakan", [
                'attribute' => $attribute,
                'minmax' => $minMax[$attribute]
            ]);
        }
    }

    Log::info("=== SELESAI MENCARI MINMAX DARI DATA TRAINING ===", ['minMax' => $minMax]);
    return $minMax;
}

    private function calculateFuzzyMembership($distance, $m = self::FUZZY_STRENGTH)
    {
        // Fungsi keanggotaan fuzzy menggunakan Euclidean distance
        // μ(x) = 1 / (1 + ε + d²)
        // Dimana d adalah jarak Euclidean dan ε adalah nilai kecil untuk menghindari pembagian nol
        $adjustedDistance = $distance + self::EPSILON;
        $membership = 1 / (1 + pow($adjustedDistance, 2));
        
        Log::info("Perhitungan Fuzzy Membership", [
            'distance' => $distance,
            'epsilon' => self::EPSILON,
            'adjusted_distance' => $adjustedDistance,
            'membership' => $membership
        ]);
        
        return $membership;
    }

    private function calculateFuzzyWeight($distance, $m = self::FUZZY_STRENGTH)
    {
        // Perhitungan bobot fuzzy menggunakan Euclidean distance
        // w = 1 / (1 + ε + d²)
        $weight = $this->calculateFuzzyMembership($distance, $m);
        
        Log::info("Perhitungan Fuzzy Weight", [
            'distance' => $distance,
            'fuzzy_weight' => $weight
        ]);
        
        return $weight;
    }

    private function calculateFeatureWeightedDistance($testValues, $trainValues)
    {
        Log::info("=== MULAI PERHITUNGAN JARAK BERBOBOT ===");
        Log::info("Data test", ['testValues' => $testValues]);
        Log::info("Data training", ['trainValues' => $trainValues]);
        
        $sum = 0;
        $totalWeight = 0;
        $featureDetails = [];

        foreach ($testValues as $key => $value) {
            if (isset($trainValues[$key])) {
                $testVal = $this->convertToNumeric($value);
                $trainVal = $this->convertToNumeric($trainValues[$key]);
                
                // Pembobotan fitur sesuai kaidah
                $weight = self::FEATURE_WEIGHTS[$key] ?? 0.1;
                $squaredDiff = pow($testVal - $trainVal, 2);
                $weightedDiff = $weight * $squaredDiff;
                
                $sum += $weightedDiff;
                $totalWeight += $weight;
                
                $featureDetails[] = [
                    'feature' => $key,
                    'test_value' => $testVal,
                    'train_value' => $trainVal,
                    'difference' => $testVal - $trainVal,
                    'squared_diff' => $squaredDiff,
                    'feature_weight' => $weight,
                    'weighted_diff' => $weightedDiff
                ];
                
                Log::info("Perhitungan fitur", [
                    'feature' => $key,
                    'test_value' => $testVal,
                    'train_value' => $trainVal,
                    'difference' => $testVal - $trainVal,
                    'squared_diff' => $squaredDiff,
                    'feature_weight' => $weight,
                    'weighted_diff' => $weightedDiff
                ]);
            }
        }

        // Normalisasi jarak dengan total bobot
        $finalDistance = $totalWeight > 0 ? sqrt($sum / $totalWeight) : 0;
        
        Log::info("=== HASIL PERHITUNGAN JARAK BERBOBOT ===", [
            'sum' => $sum,
            'total_weight' => $totalWeight,
            'final_distance' => $finalDistance,
            'feature_details' => $featureDetails
        ]);
        
        return $finalDistance;
    }

    private function convertToNumeric($value)
    {
        $originalValue = $value;
        
        $map = [
            'baik' => 1,
            'cukup' => 0.5,
            'kurang' => 0
        ];

        if (is_numeric($value)) {
            $result = floatval($value);
            Log::info("Konversi nilai numerik", [
                'original_value' => $originalValue,
                'converted_value' => $result
            ]);
            return $result;
        }

        $value = strtolower(trim($value));
        $result = $map[$value] ?? 0;
        
        Log::info("Konversi nilai non-numerik", [
            'original_value' => $originalValue,
            'cleaned_value' => $value,
            'converted_value' => $result,
            'mapping_used' => $map[$value] ?? 'default (0)'
        ]);
        
        return $result;
    }

    public function uploadExcelDanPrediksi(Request $request)
    {
        Log::info("=== MULAI PROSES UPLOAD EXCEL DAN PREDIKSI ===");
        
        try {
            if (!$request->hasFile('excel_file')) {
                Log::error("File tidak ditemukan pada request");
                return response()->json(['status' => 'error', 'message' => 'File tidak ditemukan pada request!']);
            }

            $file = $request->file('excel_file');
            $k = 5; // Fixed K value

            Log::info("File Excel yang diupload", [
                'filename' => $file->getClientOriginalName(),
                'size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
                'k_value' => $k
            ]);

            // Validate file extension
            $extension = $file->getClientOriginalExtension();
            if (!in_array($extension, ['xlsx', 'xls', 'csv'])) {
                Log::error("Format file tidak didukung", ['extension' => $extension]);
                return back()->with('error', 'Format file tidak didukung. Gunakan file Excel (.xlsx, .xls) atau CSV.');
            }

            Log::info("Memulai proses membaca file Excel");
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

            Log::info("Data yang dibaca dari Excel", [
                'total_rows' => count($rows),
                'first_row' => $rows[0] ?? null
            ]);

            if (count($rows) < 2) {
                Log::error("Data tidak cukup", ['row_count' => count($rows)]);
                return back()->with('error', 'Data tidak cukup (minimal header dan satu baris data).');
            }

            $headers = $rows[0];
            $dataRows = array_slice($rows, 1);
            $formattedRows = [];

            Log::info("Header yang ditemukan", ['headers' => $headers]);

            // Validate required headers
            $requiredHeaders = ['nama', 'nisn', 'semester_1', 'semester_2', 'semester_3', 'semester_4', 'semester_5', 'semester_6', 'usp', 'sikap', 'kerapian', 'kerajinan'];
            $missingHeaders = array_diff($requiredHeaders, array_map('strtolower', $headers));
            
            if (!empty($missingHeaders)) {
                Log::error("Header yang diperlukan tidak ditemukan", ['missing_headers' => $missingHeaders]);
                return back()->with('error', 'Format Excel tidak sesuai. Header yang diperlukan: ' . implode(', ', $missingHeaders));
            }

            Log::info("Memformat data dari Excel");
            foreach ($dataRows as $index => $dataRow) {
                $formatted = [];
                foreach ($headers as $i => $header) {
                    $formatted[strtolower(trim($header))] = trim($dataRow[$i] ?? '');
                }
                $formattedRows[] = $formatted;
                
                Log::info("Data baris " . ($index + 1), ['formatted_data' => $formatted]);
            }

            Log::info("Memulai transaksi database");
            DB::beginTransaction();
            $insertedStudents = [];
            $existingNISNs = [];

            foreach ($formattedRows as $index => $row) {
                $name = trim($row['nama'] ?? '');
                $nisn = trim($row['nisn'] ?? '');

                Log::info("Memproses baris " . ($index + 1), [
                    'name' => $name,
                    'nisn' => $nisn
                ]);

                if ($name === '' || $nisn === '') {
                    Log::warning("Baris " . ($index + 1) . " dilewati karena nama atau NISN kosong");
                    continue;
                }

                // Check for duplicate NISN
                if (in_array($nisn, $existingNISNs)) {
                    Log::error("NISN duplikat ditemukan", ['nisn' => $nisn]);
                    DB::rollback();
                    return back()->with('error', "NISN duplikat ditemukan: $nisn");
                }
                $existingNISNs[] = $nisn;

                // Check if NISN already exists as training data
                if (Student::where('nisn', $nisn)->where('jenis_data', 'training')->exists()) {
                    Log::error("NISN sudah terdaftar sebagai data latih", ['nisn' => $nisn]);
                    DB::rollback();
                    return back()->with('error', "NISN sudah terdaftar sebagai data latih: $nisn");
                }

                // Simpan siswa sebagai testing
                $student = Student::create([
                    'nisn' => $nisn,
                    'name' => $name,
                    'true_status' => null,
                    'jenis_data' => 'testing',
                ]);

                Log::info("Siswa berhasil dibuat", [
                    'student_id' => $student->id,
                    'nisn' => $student->nisn,
                    'name' => $student->name
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
                        
                        Log::info("Nilai siswa disimpan", [
                            'student_id' => $student->id,
                            'key' => trim($key),
                            'value' => trim($value)
                        ]);
                    }
                }

                $insertedStudents[] = $student;
            }

            if (empty($insertedStudents)) {
                Log::error("Tidak ada data valid yang dapat diproses");
                DB::rollback();
                return back()->with('error', 'Tidak ada data valid yang dapat diproses.');
            }

            Log::info("Commit transaksi database", ['inserted_count' => count($insertedStudents)]);
            DB::commit();

            // Jalankan prediksi untuk setiap siswa yang baru diinsert
            Log::info("Memulai prediksi untuk semua siswa yang diinsert");
            foreach ($insertedStudents as $index => $testStudent) {
                Log::info("Prediksi untuk siswa " . ($index + 1) . " dari " . count($insertedStudents), [
                    'student_id' => $testStudent->id,
                    'nisn' => $testStudent->nisn
                ]);
                $this->predictForStudent($testStudent, $k);
            }

            // Ambil hasil prediksi untuk semua siswa
            $excelPredictions = [];
            foreach ($insertedStudents as $student) {
                $prediction = Prediction::where('test_student_id', $student->id)->first();
                $neighbors = \App\Models\DistanceCalculation::with(['trainingStudent', 'weightCalculation'])
                    ->where('test_student_id', $student->id)
                    ->orderBy('distance')
                    ->limit($k)
                    ->get()
                    ->map(function($item) {
                        return [
                            'nisn' => $item->trainingStudent->nisn ?? '-',
                            'nama' => $item->trainingStudent->name ?? '-',
                            'true_status' => $item->trainingStudent->true_status ?? '-',
                            'distance' => $item->distance,
                            'weight' => optional($item->weightCalculation)->weight,
                        ];
                    })->toArray();
                $ratios = \App\Models\WeightRatio::where('test_student_id', $student->id)->get(['class', 'total_weight', 'weight_ratio']);
                $excelPredictions[] = [
                    'nisn' => $student->nisn,
                    'name' => $student->name,
                    'status' => $prediction ? $prediction->predicted_status : '-',
                    'neighbors' => $neighbors,
                    'ratios' => $ratios
                ];
            }
            return view('pages.prediksi', [
                'excelPredictions' => $excelPredictions,
                'activeInputMethod' => 'excel'
            ])->with('success', 'Data Excel berhasil diproses dan diprediksi.');

        } catch (\Exception $e) {
            Log::error("Error dalam upload Excel dan prediksi", [
                'error_message' => $e->getMessage(),
                'error_trace' => $e->getTraceAsString()
            ]);
            DB::rollback();
            Log::error('Upload & Prediksi Error: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
