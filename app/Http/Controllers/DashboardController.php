<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\StudentValue;
use App\Models\Prediction;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(){
        // Data siswa training
        $trainingCount = Student::where('jenis_data', 'training')->count();
        $trainingStatusCounts = Student::select('true_status', DB::raw('count(*) as total'))
            ->where('jenis_data', 'training')
            ->whereNotNull('true_status')
            ->groupBy('true_status')
            ->pluck('total', 'true_status')
            ->toArray();
        $trainingStats = [
            'count' => $trainingCount,
            'lulus' => $trainingStatusCounts['lulus'] ?? 0,
            'lulus_bersyarat' => $trainingStatusCounts['lulus bersyarat'] ?? 0,
            'tidak_lulus' => $trainingStatusCounts['tidak lulus'] ?? 0,
        ];

        // Data siswa testing
        $testingCount = Student::where('jenis_data', 'testing')->count();
        $testingStatusCounts = Prediction::select('predicted_status', DB::raw('count(*) as total'))
            ->whereNotNull('predicted_status')
            ->whereHas('student', function($q) {
                $q->where('jenis_data', 'testing');
            })
            ->groupBy('predicted_status')
            ->pluck('total', 'predicted_status')
            ->toArray();
        $testingStats = [
            'count' => $testingCount,
            'lulus' => $testingStatusCounts['lulus'] ?? 0,
            'lulus_bersyarat' => $testingStatusCounts['lulus bersyarat'] ?? 0,
            'tidak_lulus' => $testingStatusCounts['tidak lulus'] ?? 0,
        ];
        
        // Get semester trend data
        $semesterTrendData = $this->getSemesterTrendData();
        
        // Calculate average values per semester (hanya untuk siswa testing)
        $testingStudentIds = Student::where('jenis_data', 'testing')->pluck('id')->toArray();
        $semesterAverages = [];
        for ($i = 1; $i <= 6; $i++) {
            $semesterAverages["Semester $i"] = StudentValue::where('key', "semester_$i")
                ->where('value', '!=', '')
                ->whereIn('student_id', $testingStudentIds)
                ->avg(DB::raw('CAST(value AS DECIMAL(10,2))')) ?? 0;
        }
        
        // Round all values to 2 decimal places for better display
        foreach ($semesterAverages as $key => $value) {
            $semesterAverages[$key] = round($value, 2);
        }
        
        return view('dashboard.index', compact(
            'trainingStats',
            'testingStats',
            'semesterTrendData',
            'semesterAverages'
        ));
    }
    
    private function getSemesterTrendData(){
        $statusCategories = ['lulus', 'lulus bersyarat', 'tidak lulus'];
        $trendData = [];
        
        foreach ($statusCategories as $status) {
            $studentIds = $this->getStudentIdsByStatus($status);
            
            $averages = [];
            for ($i = 1; $i <= 6; $i++) {
                $query = StudentValue::where('key', "semester_$i")
                    ->where('value', '!=', '');
                    
                if (!empty($studentIds)) {
                    $query->whereIn('student_id', $studentIds);
                }
                    
                $averages[] = $query->avg(DB::raw('CAST(value AS DECIMAL(10,2))')) ?? 0;
            }
            
            $trendData[$status] = $averages;
        }
        
        return [
            'labels' => ['Semester 1', 'Semester 2', 'Semester 3', 'Semester 4', 'Semester 5', 'Semester 6'],
            'datasets' => $trendData
        ];
    }
    
    private function getStudentIdsByStatus($status){
        // Ambil hanya student_id yang jenis_data-nya 'testing'
        return Prediction::where('predicted_status', $status)
            ->whereHas('student', function($query) {
                $query->where('jenis_data', 'testing');
            })
            ->pluck('test_student_id')
            ->toArray();
    }
    
    private function convertAttitudeToNumeric($value)
    {
        $value = strtolower(trim($value));
        $map = [
            'baik' => 3,
            'cukup baik' => 2,
            'kurang baik' => 1,
            '' => 0
        ];
        
        return $map[$value] ?? 0;
    }
}
