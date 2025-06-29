<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Evaluations;
use App\Models\Student;
use Illuminate\Support\Facades\Log;

class PerformaController extends Controller
{
    private const STATUS_MAP = [
        'lulus' => 'lulus',
        'lulus bersyarat' => 'lulus_bersyarat',
        'tidak lulus' => 'tidak_lulus'
    ];

    public function index()
    {
        Log::info('[PerformaController] Memanggil index()');
        $latestEvaluation = Evaluations::latest()->first();
        $evaluationHistory = Evaluations::orderBy('created_at', 'desc')->get();
        return view('pages.performa', compact('latestEvaluation', 'evaluationHistory'));
    }

    public function evaluate(Request $request)
    {
        Log::info('[PerformaController] Memanggil evaluate()', ['training_percentage' => $request->training_percentage]);
        $request->validate([
            'training_percentage' => 'required|integer|min:10|max:90'
        ]);

        try {
            Log::info('[PerformaController] Mulai proses evaluasi');
            $students = Student::whereNotNull('true_status')->get();
            $students = $students->all();

            // Konsistensi shuffle
            mt_srand($request->training_percentage);
            shuffle($students);
            $students = collect($students);

            $splitIndex = (int) (count($students) * ($request->training_percentage / 100));
            $trainingData = $students->slice(0, $splitIndex);
            $testingData = $students->slice($splitIndex);

            Log::info('[PerformaController] Jumlah data latih & uji', [
                'training_count' => count($trainingData),
                'testing_count' => count($testingData)
            ]);

            // Ambil min-max untuk normalisasi HANYA dari data latih
            $minMax = $this->calculateMinMax($trainingData);
            Log::info('[PerformaController] MinMax training', $minMax);

            $classes = ['lulus', 'lulus_bersyarat', 'tidak_lulus'];
            $confusionMatrix = [];
            foreach ($classes as $actual) {
                foreach ($classes as $predicted) {
                    $confusionMatrix[$actual][$predicted] = 0;
                }
            }

            $total = 0;
            $correctPredictions = 0;
            $observedClasses = [];

            foreach ($testingData as $testStudent) {
                $neighbors = $this->getKNearestNeighbors($testStudent, $trainingData, 5, $minMax);
                $predictedStatus = $this->predictStatus($neighbors);

                $trueStatusKey = strtolower(trim($testStudent->true_status));
                if (!array_key_exists($trueStatusKey, self::STATUS_MAP)) {
                    Log::warning("Unknown true status '{$testStudent->true_status}' for student ID {$testStudent->id}");
                    continue;
                }

                $actualStatus = self::STATUS_MAP[$trueStatusKey];

                if (!isset($confusionMatrix[$actualStatus][$predictedStatus])) {
                    Log::warning("Unexpected predicted status '{$predictedStatus}'");
                    continue;
                }

                $confusionMatrix[$actualStatus][$predictedStatus]++;
                $observedClasses[$actualStatus] = true;
                $total++;

                if ($predictedStatus === $actualStatus) {
                    $correctPredictions++;
                }
            }

            $accuracy = ($total > 0) ? ($correctPredictions / $total) * 100 : 0;
            Log::info('[PerformaController] Akurasi evaluasi', ['accuracy' => $accuracy, 'total' => $total, 'benar' => $correctPredictions]);

            $classMetrics = [];
            $allClasses = array_values(self::STATUS_MAP);

            foreach ($allClasses as $class) {
                $tp = $confusionMatrix[$class][$class];
                $fp = array_sum(array_column($confusionMatrix, $class)) - $tp;
                $fn = array_sum($confusionMatrix[$class]) - $tp;

                $precision = ($tp + $fp) > 0 ? ($tp / ($tp + $fp)) * 100 : 0;
                $recall = ($tp + $fn) > 0 ? ($tp / ($tp + $fn)) * 100 : 0;
                $f1Score = ($precision + $recall) > 0 ? (2 * $precision * $recall) / ($precision + $recall) : 0;

                $classMetrics[$class] = [
                    'precision' => $precision,
                    'recall' => $recall,
                    'f1_score' => $f1Score
                ];
            }

            $macroPrecision = count($allClasses) > 0 ? array_sum(array_column($classMetrics, 'precision')) / count($allClasses) : 0;
            $macroRecall = count($allClasses) > 0 ? array_sum(array_column($classMetrics, 'recall')) / count($allClasses) : 0;
            $macroF1 = count($allClasses) > 0 ? array_sum(array_column($classMetrics, 'f1_score')) / count($allClasses) : 0;

            Log::info('[PerformaController] Macro metrics', [
                'precision' => $macroPrecision,
                'recall' => $macroRecall,
                'f1_score' => $macroF1
            ]);
            Log::info('[PerformaController] Confusion matrix', $confusionMatrix);

            Evaluations::create([
                'training_percentage' => $request->training_percentage,
                'k_value' => 5,
                'accuracy' => $accuracy,
                'error_rate' => 100 - $accuracy,
                'precision' => $macroPrecision,
                'recall' => $macroRecall,
                'f1_score' => $macroF1,
                'confusion_matrix' => $confusionMatrix,
                'training_data_count' => count($trainingData),
                'test_data_count' => $total
            ]);

            Log::info('[PerformaController] Evaluasi selesai & data disimpan');
            return redirect()->route('performa')->with('success', 'Evaluasi berhasil dilakukan');
        } catch (\Exception $e) {
            Log::error('[PerformaController] Evaluation error: ' . $e->getMessage());
            return redirect()->route('performa')->with('error', 'Terjadi kesalahan saat melakukan evaluasi');
        }
    }

    private function getKNearestNeighbors($testStudent, $trainingData, $k, $minMax)
    {
        Log::info('[PerformaController] Memanggil getKNearestNeighbors()', [
            'test_student_id' => $testStudent->id,
            'k' => $k
        ]);
        $distances = [];

        foreach ($trainingData as $trainStudent) {
            $distance = $this->calculateDistance($testStudent, $trainStudent, $minMax);
            $distances[] = [
                'student' => $trainStudent,
                'distance' => $distance
            ];
        }

        usort($distances, fn($a, $b) => $a['distance'] <=> $b['distance']);

        return array_slice($distances, 0, $k);
    }

    private function calculateDistance($student1, $student2, $minMax)
    {
        Log::info('[PerformaController] Memanggil calculateDistance()', [
            'student1_id' => $student1->id, 'student2_id' => $student2->id]);
        $features = ['avg_semester_score', 'usp_score', 'kerapian', 'kerajinan'];
        $sumSquaredDiff = 0;

        foreach ($features as $feature) {
            $min = $minMax[$feature]['min'];
            $max = $minMax[$feature]['max'];

            $value1 = $this->getFeatureValue($student1, $feature);
            $value2 = $this->getFeatureValue($student2, $feature);

            if (is_null($value1) || is_null($value2)) continue;

            $v1 = $this->normalize($value1, $min, $max);
            $v2 = $this->normalize($value2, $min, $max);

            $sumSquaredDiff += pow($v1 - $v2, 2);
        }

        return sqrt($sumSquaredDiff);
    }

    private function normalize($value, $min, $max)
    {
        Log::info('[PerformaController] Memanggil normalize()', ['value' => $value, 'min' => $min, 'max' => $max]);
        return ($max - $min) == 0 ? 0 : ($value - $min) / ($max - $min);
    }

    private function calculateMinMax($data)
    {
        Log::info('[PerformaController] Memanggil calculateMinMax()');
        $features = ['avg_semester_score', 'usp_score', 'kerapian', 'kerajinan'];
        $minMax = [];

        foreach ($features as $feature) {
            $values = $data->map(function($student) use ($feature) {
                return $this->getFeatureValue($student, $feature);
            })->filter(fn($v) => !is_null($v))->all();

            if (count($values) === 0) {
                $minMax[$feature] = ['min' => 0, 'max' => 1];
            } else {
                $minMax[$feature] = [
                    'min' => min($values),
                    'max' => max($values)
                ];
            }
        }

        return $minMax;
    }

    private function predictStatus($neighbors)
    {
        Log::info('[PerformaController] Memanggil predictStatus()', ['neighbor_count' => count($neighbors)]);
        $classWeights = [
            'lulus' => 0,
            'lulus_bersyarat' => 0,
            'tidak_lulus' => 0
        ];

        foreach ($neighbors as $neighbor) {
            $trueStatusKey = strtolower(trim($neighbor['student']->true_status));
            if (!array_key_exists($trueStatusKey, self::STATUS_MAP)) {
                continue;
            }

            $status = self::STATUS_MAP[$trueStatusKey];
            $weight = 1 / ($neighbor['distance'] + 0.001); // Avoid division by zero
            $classWeights[$status] += $weight;
        }

        return array_search(max($classWeights), $classWeights);
    }

    // Helper untuk mengambil nilai fitur dari student_values
    private function getFeatureValue($student, $key)
    {
        // Untuk avg_semester_score, hitung rata-rata semester_1 sampai semester_6
        if ($key === 'avg_semester_score') {
            $semesterKeys = ['semester_1', 'semester_2', 'semester_3', 'semester_4', 'semester_5', 'semester_6'];
            $values = collect($semesterKeys)->map(function($k) use ($student) {
                return (float) optional($student->studentValues->firstWhere('key', $k))->value;
            })->filter(function($v) { return !is_null($v) && $v !== 0; });
            return $values->count() > 0 ? $values->avg() : null;
        }
        // Untuk usp_score, key-nya 'usp'
        if ($key === 'usp_score') {
            return (float) optional($student->studentValues->firstWhere('key', 'usp'))->value;
        }
        // Untuk kerapian dan kerajinan
        if (in_array($key, ['kerapian', 'kerajinan'])) {
            return (float) optional($student->studentValues->firstWhere('key', $key))->value;
        }
        return null;
    }
}