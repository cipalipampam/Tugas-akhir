<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\StudentValue;
use App\Models\DistanceCalculation;
use App\Models\WeightCalculation;
use App\Models\WeightRatio;
use App\Models\Prediction;
use Illuminate\Support\Facades\Log;

class PrediksiController extends Controller
{
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
                'predicted_status' => optional($data->trainingStudent->prediction)->predicted_status ?? '-' // atau info lain jika perlu
            ];
        });

        return view('pages.prediksi', compact('testStudent', 'results'));
    }

    public function processAndPredict(Request $request)
    {
        $data = $request->input('data');
        $k = $request->input('k_value'); // Tambahan ambil nilai K

        if (!$data || !is_array($data)) {
            return back()->with('error', 'Input data tidak valid.');
        }

        $testStudent = Student::create([
            'nisn' => 'test_' . time(),
            'name' => 'Siswa Uji Coba',
            'jenis_data' => 'testing',
            'true_status' => null
        ]);

        foreach ($data as $key => $value) {
            StudentValue::create([
                'student_id' => $testStudent->id,
                'key' => $key,
                'value' => $value
            ]);
        }

        $this->predictForStudent($testStudent, $k); // Kirim nilai K

        return redirect()->route('prediction.result', ['id' => $testStudent->id])
            ->with('success', 'Prediksi berhasil dilakukan.');
    }

    // Fungsi untuk normalisasi nilai
    private function normalize($value, $min, $max)
    {
        // Pastikan bahwa nilai-nilai ini adalah tipe numerik (float atau int)
        $value = floatval($value);
        $min = floatval($min);
        $max = floatval($max);

        // Cek jika $min dan $max bukan 0 untuk menghindari pembagian dengan 0
        if ($max - $min == 0) {
            return 0;  // Hindari pembagian dengan nol
        }

        return ($value - $min) / ($max - $min);
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
                // Jika tidak ditemukan min/max, nilai tetap 0
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

            $weight = $distance > 0 ? 1 / ($distance * $distance) : 999999;

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
            'cukup baik' => 2,
            'kurang baik' => 1
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

}
