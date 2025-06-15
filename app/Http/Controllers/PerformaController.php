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
        $latestEvaluation = Evaluations::latest()->first();
        $evaluationHistory = Evaluations::orderBy('created_at', 'desc')->get();

        return view('pages.performa', compact('latestEvaluation', 'evaluationHistory'));
    }

    public function evaluate(Request $request)
    {
        $request->validate([
            'training_percentage' => 'required|integer|min:10|max:90'
        ]);

        try {
            $students = Student::whereNotNull('true_status')->get();
           
            srand($request->training_percentage); // Seed disesuaikan dari input persen training (biar tetap konsisten tapi beda seed tiap setting)
            $students = $students->all();
            shuffle($students);
            $students = collect($students);

            $splitIndex = (int) (count($students) * ($request->training_percentage / 100));
            $trainingData = $students->slice(0, $splitIndex);
            $testingData = $students->slice($splitIndex);

            $classes = ['lulus', 'lulus_bersyarat', 'tidak_lulus'];
            $confusionMatrix = [];
            foreach ($classes as $actual) {
                foreach ($classes as $predicted) {
                    $confusionMatrix[$actual][$predicted] = 0;
                }
            }

            $total = 0;
            $correctPredictions = 0;
            $observedClasses = []; // untuk menyimpan kelas yang muncul di data uji

            foreach ($testingData as $testStudent) {
                $neighbors = $this->getKNearestNeighbors($testStudent, $trainingData, 5);
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

            $classMetrics = [];
            $activeClasses = array_keys($observedClasses); // hanya kelas yang muncul

            foreach ($activeClasses as $class) {
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

            // Macro average hanya dari kelas aktif
            $macroPrecision = array_sum(array_column($classMetrics, 'precision')) / count($activeClasses);
            $macroRecall = array_sum(array_column($classMetrics, 'recall')) / count($activeClasses);
            $macroF1 = array_sum(array_column($classMetrics, 'f1_score')) / count($activeClasses);

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

            return redirect()->route('performa')->with('success', 'Evaluasi berhasil dilakukan');
        } catch (\Exception $e) {
            Log::error('Evaluation error: ' . $e->getMessage());
            return redirect()->route('performa')->with('error', 'Terjadi kesalahan saat melakukan evaluasi');
        }
    }


    private function getKNearestNeighbors($testStudent, $trainingData, $k)
    {
        $distances = [];

        foreach ($trainingData as $trainStudent) {
            $distance = $this->calculateDistance($testStudent, $trainStudent);
            $distances[] = [
                'student' => $trainStudent,
                'distance' => $distance
            ];
        }

        usort($distances, fn($a, $b) => $a['distance'] <=> $b['distance']);

        return array_slice($distances, 0, $k);
    }

    private function calculateDistance($student1, $student2)
    {
        $features = ['avg_semester_score', 'usp_score', 'kerapian', 'kerajinan'];
        $sumSquaredDiff = 0;

        foreach ($features as $feature) {
            $value1 = (float) $student1->$feature;
            $value2 = (float) $student2->$feature;
            $sumSquaredDiff += pow($value1 - $value2, 2);
        }

        return sqrt($sumSquaredDiff);
    }

    private function predictStatus($neighbors)
    {
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
}

