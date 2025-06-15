<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StudentValue;
use App\Models\Prediction;
use Illuminate\Support\Facades\DB;
use App\Models\Student;

class VisualisasiController extends Controller
{
    public function index(Request $request)
    {
        // Get filter parameters
        $statusFilter = $request->input('status', 'all');
        $semesterFilter = $request->input('semester', [1, 2, 3, 4, 5, 6]);
        $tahunAngkatanFilter = $request->input('tahun_angkatan', 'all');
        
        if (!is_array($semesterFilter)) {
            $semesterFilter = [$semesterFilter];
        }
        
        // Get available tahun angkatan for filter dropdown
        $availableTahunAngkatan = Student::select('tahun_angkatan')
            ->whereNotNull('tahun_angkatan')
            ->distinct()
            ->pluck('tahun_angkatan')
            ->sort()
            ->values();
        
        // Prediction distribution data
        $statusCounts = $this->getPredictionDistribution($statusFilter, $tahunAngkatanFilter);
        
        // Academic vs Non-Academic data
        $acadVsNonAcadData = $this->getAcademicVsNonAcademicData($statusFilter, $tahunAngkatanFilter);
        
        // Semester trend data
        $semesterTrendData = $this->getSemesterTrendData($statusFilter, $semesterFilter, $tahunAngkatanFilter);
        
        // Correlation heatmap data
        $correlationData = $this->getCorrelationData($statusFilter, $tahunAngkatanFilter);
        
        return view('pages.visualisasi-data', compact(
            'statusCounts',
            'acadVsNonAcadData',
            'semesterTrendData',
            'correlationData',
            'statusFilter',
            'semesterFilter',
            'tahunAngkatanFilter',
            'availableTahunAngkatan'
        ));
    }
    
    private function getPredictionDistribution($statusFilter, $tahunAngkatanFilter)
    {
        $query = Prediction::select('predicted_status', DB::raw('count(*) as total'))
            ->join('student', 'predictions.test_student_id', '=', 'student.id')
            ->whereNotNull('predicted_status');
            
        if ($statusFilter !== 'all') {
            $query->where('predicted_status', $statusFilter);
        }
        
        if ($tahunAngkatanFilter !== 'all') {
            $query->where('student.tahun_angkatan', $tahunAngkatanFilter);
        }
            
        $results = $query->groupBy('predicted_status')
            ->pluck('total', 'predicted_status')
            ->toArray();
            
        return [
            'lulus' => $results['lulus'] ?? 0,
            'lulus_bersyarat' => $results['lulus bersyarat'] ?? 0,
            'tidak_lulus' => $results['tidak lulus'] ?? 0
        ];
    }
    
    private function getAcademicVsNonAcademicData($statusFilter, $tahunAngkatanFilter)
    {
        $query = StudentValue::select('key', DB::raw('AVG(CAST(value AS DECIMAL(10,2))) as average'))
            ->join('student', 'student_values.student_id', '=', 'student.id')
            ->where('value', '!=', '');
            
        if ($tahunAngkatanFilter !== 'all') {
            $query->where('student.tahun_angkatan', $tahunAngkatanFilter);
        }
        
        if ($statusFilter !== 'all') {
            $query->join('predictions', 'student.id', '=', 'predictions.test_student_id')
                ->where('predictions.predicted_status', $statusFilter);
        }
        
        $results = $query->groupBy('key')
            ->pluck('average', 'key')
            ->toArray();
            
        return [
            'academic' => [
                'Rata-Rata Semester 1' => $results['semester_1'] ?? 0,
                'Rata-Rata Semester 2' => $results['semester_2'] ?? 0,
                'Rata-Rata Semester 3' => $results['semester_3'] ?? 0,
                'Rata-Rata Semester 4' => $results['semester_4'] ?? 0,
                'Rata-Rata Semester 5' => $results['semester_5'] ?? 0,
                'Rata-Rata Semester 6' => $results['semester_6'] ?? 0,
                'USP' => $results['usp'] ?? 0
            ],
            'non_academic' => [
                'sikap' => $results['sikap'] ?? 0,
                'kerajinan' => $results['kerajinan'] ?? 0,
                'kerapian' => $results['kerapian'] ?? 0
            ]
        ];
    }
    
    private function getSemesterTrendData($statusFilter, $semesterFilter, $tahunAngkatanFilter)
    {
        $semesterKeys = [];
        foreach ($semesterFilter as $semester) {
            $semesterKeys[] = "semester_$semester";
        }
        
        if (empty($semesterKeys)) {
            return [];
        }
        
        $statusCategories = ['lulus', 'lulus bersyarat', 'tidak lulus'];
        $trendData = [];
        
        foreach ($statusCategories as $status) {
            if ($statusFilter !== 'all' && $statusFilter !== $status) {
                continue;
            }
            
            $studentIds = $this->getStudentIdsByStatus($status, $tahunAngkatanFilter);
            
            $averages = [];
            foreach ($semesterKeys as $key) {
                $query = StudentValue::where('key', $key)
                    ->where('value', '!=', '');
                    
                if (!empty($studentIds)) {
                    $query->whereIn('student_id', $studentIds);
                }
                    
                $averages[] = $query->avg(DB::raw('CAST(value AS DECIMAL(10,2))')) ?? 0;
            }
            
            $trendData[$status] = $averages;
        }
        
        return [
            'labels' => array_map(function($semester) { 
                return "Semester $semester"; 
            }, $semesterFilter),
            'datasets' => $trendData
        ];
    }
    
    private function getCorrelationData($statusFilter, $tahunAngkatanFilter)
    {
        $query = StudentValue::select('key', 'value')
            ->join('student', 'student_values.student_id', '=', 'student.id')
            ->where('value', '!=', '');
            
        if ($tahunAngkatanFilter !== 'all') {
            $query->where('student.tahun_angkatan', $tahunAngkatanFilter);
        }
        
        if ($statusFilter !== 'all') {
            $query->join('predictions', 'student.id', '=', 'predictions.test_student_id')
                ->where('predictions.predicted_status', $statusFilter);
        }
        
        $data = $query->get()
            ->groupBy('key')
            ->map(function($values) {
                return $values->pluck('value')->map(function($value) {
                    return floatval($value);
                })->toArray();
            });
            
        $labels = array_keys($data->toArray());
        $correlationMatrix = [];
        
        foreach ($labels as $label1) {
            $correlationMatrix[$label1] = [];
            foreach ($labels as $label2) {
                $correlationMatrix[$label1][$label2] = $this->calculateCorrelation(
                    $data[$label1] ?? [],
                    $data[$label2] ?? []
                );
            }
        }
        
        return [
            'labels' => $labels,
            'data' => $correlationMatrix
        ];
    }
    
    private function getStudentIdsByStatus($status, $tahunAngkatanFilter)
    {
        $query = Prediction::select('test_student_id')
            ->join('student', 'predictions.test_student_id', '=', 'student.id')
            ->where('predicted_status', $status);
            
        if ($tahunAngkatanFilter !== 'all') {
            $query->where('student.tahun_angkatan', $tahunAngkatanFilter);
        }
        
        return $query->pluck('test_student_id')->toArray();
    }
 
    private function calculateCorrelation($x, $y)
    {
        // Ensure arrays are of equal length by taking the minimum length
        $n = min(count($x), count($y));
        
        if ($n <= 1) return 0; // Cannot calculate correlation with less than 2 points
        
        // Take only the first n elements from each array
        $x = array_slice($x, 0, $n);
        $y = array_slice($y, 0, $n);
        
        // Calculate means
        $meanX = array_sum($x) / $n;
        $meanY = array_sum($y) / $n;
        
        // Calculate correlation coefficient
        $numerator = 0;
        $denominatorX = 0;
        $denominatorY = 0;
        
        for ($i = 0; $i < $n; $i++) {
            $xDiff = $x[$i] - $meanX;
            $yDiff = $y[$i] - $meanY;
            
            $numerator += $xDiff * $yDiff;
            $denominatorX += $xDiff * $xDiff;
            $denominatorY += $yDiff * $yDiff;
        }
        
        $denominator = sqrt($denominatorX * $denominatorY);
        
        return $denominator == 0 ? 0 : $numerator / $denominator;
    }
}
