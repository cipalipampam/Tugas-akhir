<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StudentValue;
use App\Models\Prediction;
use Illuminate\Support\Facades\DB;

class VisualisasiController extends Controller
{
    public function index(Request $request)
    {
        // Get filter parameters
        $statusFilter = $request->input('status', 'all');
        $semesterFilter = $request->input('semester', [1, 2, 3, 4, 5, 6]);
        
        if (!is_array($semesterFilter)) {
            $semesterFilter = [$semesterFilter];
        }
        
        // Prediction distribution data
        $statusCounts = $this->getPredictionDistribution($statusFilter);
        
        // Academic vs Non-Academic data
        $acadVsNonAcadData = $this->getAcademicVsNonAcademicData($statusFilter);
        
        // Semester trend data
        $semesterTrendData = $this->getSemesterTrendData($statusFilter, $semesterFilter);
        
        // Correlation heatmap data
        $correlationData = $this->getCorrelationData($statusFilter);
        
        return view('pages.visualisasi-data', compact(
            'statusCounts',
            'acadVsNonAcadData',
            'semesterTrendData',
            'correlationData',
            'statusFilter',
            'semesterFilter'
        ));
    }
    
    private function getPredictionDistribution($statusFilter)
    {
        $query = Prediction::select('predicted_status', DB::raw('count(*) as total'))
            ->whereNotNull('predicted_status');
            
        if ($statusFilter !== 'all') {
            $query->where('predicted_status', $statusFilter);
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
    
    private function getAcademicVsNonAcademicData($statusFilter)
    {
        // Get student IDs based on filter
        $studentIds = $this->getFilteredStudentIds($statusFilter);
        
        // Academic data (Semester averages and USP)
        $academicKeys = ['Rata-Rata Semester 1', 'Rata-Rata Semester 2', 'Rata-Rata Semester 3', 
                         'Rata-Rata Semester 4', 'Rata-Rata Semester 5', 'Rata-Rata Semester 6', 'usp'];
                         
        $academicAvgs = [];
        
        foreach ($academicKeys as $key) {
            $query = StudentValue::where('key', $key)
                ->where('value', '!=', '');
                
            if (!empty($studentIds)) {
                $query->whereIn('student_id', $studentIds);
            }
                
            $academicAvgs[$key] = $query->avg(DB::raw('CAST(value AS DECIMAL(10,2))')) ?? 0;
        }
        
        // Non-academic data (Sikap, Kerapian, Kerajinan)
        $nonAcademicKeys = ['sikap', 'kerapian', 'kerajinan'];
        $nonAcademicAvgs = [];
        
        foreach ($nonAcademicKeys as $key) {
            $query = StudentValue::where('key', $key);
            
            if (!empty($studentIds)) {
                $query->whereIn('student_id', $studentIds);
            }
            
            $values = $query->get()->map(function($item) {
                return $this->convertAttitudeToNumeric($item->value);
            });
            
            $nonAcademicAvgs[$key] = $values->avg() ?? 0;
        }
        
        return [
            'academic' => $academicAvgs,
            'non_academic' => $nonAcademicAvgs
        ];
    }

    private function getSemesterTrendData($statusFilter, $semesterFilter)
    {
        $semesterKeys = [];
        foreach ($semesterFilter as $semester) {
            $semesterKeys[] = "Rata-Rata Semester $semester";
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
            
            $studentIds = $this->getStudentIdsByStatus($status);
            
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
 
    private function getCorrelationData($statusFilter)
    {
        $studentIds = $this->getFilteredStudentIds($statusFilter);
        
        $attributes = [
            'Rata-Rata Semester 1', 'Rata-Rata Semester 2', 'Rata-Rata Semester 3',
            'Rata-Rata Semester 4', 'Rata-Rata Semester 5', 'Rata-Rata Semester 6',
            'usp', 'sikap', 'kerapian', 'kerajinan'
        ];
        
        $displayNames = [
            'Rata-Rata Semester 1' => 'Sem 1', 
            'Rata-Rata Semester 2' => 'Sem 2', 
            'Rata-Rata Semester 3' => 'Sem 3',
            'Rata-Rata Semester 4' => 'Sem 4', 
            'Rata-Rata Semester 5' => 'Sem 5', 
            'Rata-Rata Semester 6' => 'Sem 6',
            'usp' => 'USP', 
            'sikap' => 'Sikap', 
            'kerapian' => 'Kerapian', 
            'kerajinan' => 'Kerajinan'
        ];
        
        $correlationMatrix = [];
        
        // Initialize matrix with zeros
        foreach ($attributes as $attr1) {
            foreach ($attributes as $attr2) {
                $correlationMatrix[$displayNames[$attr1]][$displayNames[$attr2]] = 0;
            }
        }
        
        // Get all students' data
        $studentValues = StudentValue::whereIn('key', $attributes);
        
        if (!empty($studentIds)) {
            $studentValues->whereIn('student_id', $studentIds);
        }
        
        $studentValues = $studentValues->get()->groupBy('student_id');
        
        // Process student data for correlation calculation
        $attributeValues = [];
        foreach ($attributes as $attr) {
            $attributeValues[$attr] = [];
        }
        
        foreach ($studentValues as $studentId => $values) {
            $studentData = [];
            
            foreach ($values as $value) {
                if ($value->key === 'sikap' || $value->key === 'kerapian' || $value->key === 'kerajinan') {
                    $studentData[$value->key] = $this->convertAttitudeToNumeric($value->value);
                } else {
                    $studentData[$value->key] = floatval($value->value);
                }
            }
            
            foreach ($attributes as $attr) {
                if (isset($studentData[$attr])) {
                    $attributeValues[$attr][] = $studentData[$attr];
                }
            }
        }
        
        // Calculate correlation
        foreach ($attributes as $attr1) {
            foreach ($attributes as $attr2) {
                if (count($attributeValues[$attr1]) > 0 && count($attributeValues[$attr2]) > 0) {
                    $correlation = $this->calculateCorrelation(
                        $attributeValues[$attr1], 
                        $attributeValues[$attr2]
                    );
                    
                    $correlationMatrix[$displayNames[$attr1]][$displayNames[$attr2]] = round($correlation, 2);
                }
            }
        }
        
        return [
            'labels' => array_values(array_intersect_key($displayNames, array_flip($attributes))),
            'data' => $correlationMatrix
        ];
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
 
    private function getFilteredStudentIds($statusFilter)
    {
        if ($statusFilter === 'all') {
            return [];
        }
        
        return Prediction::where('predicted_status', $statusFilter)
            ->pluck('test_student_id')
            ->toArray();
    }
  
    private function getStudentIdsByStatus($status)
    {
        return Prediction::where('predicted_status', $status)
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
