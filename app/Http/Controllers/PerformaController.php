<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Evaluations;
use App\Models\Student;
use Illuminate\Support\Facades\Log;

class PerformaController extends Controller
{
    public function index()
    {
        $latestEvaluation = Evaluations::latest()->first();
        $evaluationHistory = Evaluations::orderBy('created_at', 'desc')->get();

        return view('pages.performa', compact('latestEvaluation', 'evaluationHistory'));
    }

    public function evaluate(Request $request)
    {
        $request->validate([
            'training_percentage' => 'required|integer|min:10|max:90',
            'k_value' => 'required|integer|min:1'
        ]);

        try {
            $students = Student::whereNotNull('true_status')->get();

            $students = $students->shuffle();
            $splitIndex = (int) (count($students) * ($request->training_percentage / 100));
            $trainingData = $students->slice(0, $splitIndex);
            $testingData = $students->slice($splitIndex);

            $truePositive = 0;
            $trueNegative = 0;
            $falsePositive = 0;
            $falseNegative = 0;

            foreach ($testingData as $testStudent) {
                $neighbors = $this->getKNearestNeighbors($testStudent, $trainingData, $request->k_value);
                $predictedStatus = $this->predictStatus($neighbors);
                $actualStatus = $this->statusToBinary($testStudent->true_status);

                Log::info("Predicted: $predictedStatus, Actual: $actualStatus");

                if ($predictedStatus == 1 && $actualStatus == 1) {
                    $truePositive++;
                } elseif ($predictedStatus == 0 && $actualStatus == 0) {
                    $trueNegative++;
                } elseif ($predictedStatus == 1 && $actualStatus == 0) {
                    $falsePositive++;
                } else {
                    $falseNegative++;
                }
            }

            $total = count($testingData);
            $accuracy = (($truePositive + $trueNegative) / $total) * 100;
            $errorRate = (($falsePositive + $falseNegative) / $total) * 100;

            $evaluation = Evaluations::create([
                'training_percentage' => $request->training_percentage,
                'k_value' => $request->k_value,
                'accuracy' => $accuracy,
                'error_rate' => $errorRate,
                'confusion_matrix' => [
                    'true_positive' => $truePositive,
                    'true_negative' => $trueNegative,
                    'false_positive' => $falsePositive,
                    'false_negative' => $falseNegative
                ]
            ]);

            return redirect()->route('performa')->with('success', 'Evaluasi berhasil dilakukan');
        } catch (\Exception $e) {
            Log::error('Evaluation error: ' . $e->getMessage());
            return redirect()->route('performa')->with('error', 'Terjadi kesalahan saat melakukan evaluasi');
        }
    }

    private function statusToBinary($status)
    {
        $normalized = strtolower(trim($status));
        switch ($normalized) {
            case 'lulus':
                return 2;
            case 'lulus bersyarat':
                return 1;
            case 'tidak lulus':
                return 0;
            default:
                return 0;
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

        // Sort by distance
        usort($distances, function ($a, $b) {
            return $a['distance'] <=> $b['distance'];
        });

        // Return K nearest neighbors
        return array_slice($distances, 0, $k);
    }

    private function calculateDistance($student1, $student2)
    {
        $features = ['avg_semester_score', 'usp_score', 'kerapian', 'kerajinan']; // Sesuaikan dengan kolom real
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
        $positiveWeight = 0;
        $negativeWeight = 0;

        foreach ($neighbors as $neighbor) {
            $status = $this->statusToBinary($neighbor['student']->true_status);
            $weight = 1 / ($neighbor['distance'] + 0.001); // Hindari div 0

            if ($status == 1) {
                $positiveWeight += $weight;
            } else {
                $negativeWeight += $weight;
            }
        }

        return $positiveWeight > $negativeWeight ? 1 : 0;
    }
}
