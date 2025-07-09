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
        $statusCounts = $this->getPredictionDistribution($statusFilter, $tahunAngkatanFilter, $semesterFilter);
        
        // Academic vs Non-Academic data
        $acadVsNonAcadData = $this->getAcademicVsNonAcademicData($statusFilter, $tahunAngkatanFilter, $semesterFilter);
        
        // Semester trend data
        $semesterTrendData = $this->getSemesterTrendData($statusFilter, $semesterFilter, $tahunAngkatanFilter);
        
        // Hapus pemanggilan getCorrelationData dan variabel correlationData
        // Tambahkan pembuatan data histogram
        $histogramData = [
            'labels' => [],
            'datasets' => []
        ];
        $studentTableData = [];
        $studentQuery = Student::query()->where('jenis_data', 'testing');
        if ($tahunAngkatanFilter !== 'all') {
            $studentQuery->where('tahun_angkatan', $tahunAngkatanFilter);
        }
        // Filter hanya siswa yang punya nilai di semester yang dipilih
        if (!empty($semesterFilter)) {
            $semesterKeys = array_map(fn($s) => "semester_$s", $semesterFilter);
            $studentQuery->whereExists(function($q) use ($semesterKeys) {
                $q->select(DB::raw(1))
                    ->from('student_values')
                    ->whereColumn('student_values.student_id', 'student.id')
                    ->whereIn('student_values.key', $semesterKeys)
                    ->where('student_values.value', '!=', '');
            });
        }
        $students = $studentQuery->get();
        $studentIds = $students->pluck('id');
        // Ambil prediksi status
        $predictions = Prediction::whereIn('test_student_id', $studentIds)
            ->pluck('predicted_status', 'test_student_id');
        // Ambil semua nilai student_values sekaligus
        $studentValues = StudentValue::whereIn('student_id', $studentIds)
            ->whereIn('key', array_merge(
                array_map(fn($s) => "semester_$s", range(1,6)),
                ['usp','sikap','kerapian','kerajinan']
            ))
            ->get()
            ->groupBy('student_id');
        foreach ($students as $student) {
            $values = $studentValues[$student->id] ?? collect();
            $row = [
                'nama' => $student->name,
                'nisn' => $student->nisn,
                'tahun_angkatan' => $student->tahun_angkatan,
            ];
            // Nilai semester 1-6
            for ($i=1; $i<=6; $i++) {
                $row["semester_$i"] = optional($values->firstWhere('key', "semester_$i"))->value;
            }
            // USP, sikap, kerapian, kerajinan
            $row['usp'] = optional($values->firstWhere('key', 'usp'))->value;
            $row['sikap'] = optional($values->firstWhere('key', 'sikap'))->value;
            $row['kerapian'] = optional($values->firstWhere('key', 'kerapian'))->value;
            $row['kerajinan'] = optional($values->firstWhere('key', 'kerajinan'))->value;
            // Status prediksi
            $row['status_prediksi'] = $predictions[$student->id] ?? '-';
            // Filter status jika dipilih
            if ($statusFilter === 'all' || strtolower($row['status_prediksi']) === strtolower($statusFilter)) {
                $studentTableData[] = $row;
            }
        }
        
        // Hapus pemanggilan getCorrelationData dan variabel correlationData
        // Tambahkan pembuatan data histogram
        $histogramData = [
            'labels' => [],
            'datasets' => []
        ];
        $studentCount = count($studentTableData);
        if ($studentCount > 0) {
            // Ambil nilai USP, rata-rata semester, sikap, kerapian, kerajinan
            $usp = [];
            $rataSemester = [];
            $sikap = [];
            $kerapian = [];
            $kerajinan = [];
            foreach ($studentTableData as $row) {
                if (is_numeric($row['usp'])) $usp[] = floatval($row['usp']);
                $semesterVals = [];
                for ($i=1; $i<=6; $i++) {
                    if (is_numeric($row["semester_$i"])) $semesterVals[] = floatval($row["semester_$i"]);
                }
                if (count($semesterVals)) $rataSemester[] = array_sum($semesterVals)/count($semesterVals);
                if ($row['sikap'] !== null && $row['sikap'] !== '') $sikap[] = $row['sikap'];
                if ($row['kerapian'] !== null && $row['kerapian'] !== '') $kerapian[] = $row['kerapian'];
                if ($row['kerajinan'] !== null && $row['kerajinan'] !== '') $kerajinan[] = $row['kerajinan'];
            }
            // Helper untuk binning
            $makeHistogram = function($data, $binCount, $min, $max) {
                $bins = array_fill(0, $binCount, 0);
                $binLabels = [];
                $step = ($max - $min) / $binCount;
                for ($i=0; $i<$binCount; $i++) {
                    $binLabels[] = round($min + $i*$step, 1) . ' - ' . round($min + ($i+1)*$step, 1);
                }
                foreach ($data as $val) {
                    $idx = (int) floor(($val - $min) / ($max - $min) * $binCount);
                    if ($idx < 0) $idx = 0;
                    if ($idx >= $binCount) $idx = $binCount-1;
                    $bins[$idx]++;
                }
                return [$binLabels, $bins];
            };
            // USP
            if (count($usp)) {
                list($labels, $counts) = $makeHistogram($usp, 10, 0, 100);
                $histogramData['labels'] = $labels;
                $histogramData['datasets'][] = [
                    'label' => 'USP',
                    'data' => $counts,
                    'backgroundColor' => 'rgba(54, 162, 235, 0.6)'
                ];
            }
            // Rata-rata semester
            if (count($rataSemester)) {
                list($labels, $counts) = $makeHistogram($rataSemester, 10, 0, 100);
                if (empty($histogramData['labels'])) $histogramData['labels'] = $labels;
                $histogramData['datasets'][] = [
                    'label' => 'Rata-rata Semester',
                    'data' => $counts,
                    'backgroundColor' => 'rgba(255, 99, 132, 0.6)'
                ];
            }
            // Non-akademik (sikap, kerapian, kerajinan) - 3 bin: kurang baik, cukup baik, baik
            $nonAkademikMap = ['kurang baik'=>0, 'cukup baik'=>1, 'baik'=>2];
            $nonAkademikLabels = ['Kurang Baik', 'Cukup Baik', 'Baik'];
            foreach ([['sikap',$sikap,'rgba(255, 206, 86, 0.7)'],['kerapian',$kerapian,'rgba(75, 192, 192, 0.7)'],['kerajinan',$kerajinan,'rgba(153, 102, 255, 0.7)']] as [$label,$data,$color]) {
                if (count($data)) {
                    $bins = [0,0,0];
                    foreach ($data as $v) {
                        $idx = $nonAkademikMap[strtolower($v)] ?? null;
                        if ($idx !== null) $bins[$idx]++;
                    }
                    $histogramData['datasets'][] = [
                        'label' => ucfirst($label),
                        'data' => $bins,
                        'backgroundColor' => $color
                    ];
                    if (empty($histogramData['labels'])) $histogramData['labels'] = $nonAkademikLabels;
                }
            }
        }
        
        return view('pages.visualisasi-data', compact(
            'statusCounts',
            'acadVsNonAcadData',
            'semesterTrendData',
            'statusFilter',
            'semesterFilter',
            'tahunAngkatanFilter',
            'availableTahunAngkatan',
            'studentTableData',
            'histogramData',
        ));
    }
    
    private function getPredictionDistribution($statusFilter, $tahunAngkatanFilter, $semesterFilter)
    {
        $semesterKeys = array_map(fn($s) => "semester_$s", $semesterFilter);
        $query = Prediction::select('predicted_status', DB::raw('count(*) as total'))
            ->join('student', 'predictions.test_student_id', '=', 'student.id')
            ->whereNotNull('predicted_status')
            ->where('student.jenis_data', 'testing');
            
        if ($statusFilter !== 'all') {
            $query->where('predicted_status', $statusFilter);
        }
        
        if ($tahunAngkatanFilter !== 'all') {
            $query->where('student.tahun_angkatan', $tahunAngkatanFilter);
        }
        // Filter hanya siswa yang punya nilai di semester yang dipilih
        $query->whereExists(function($q) use ($semesterKeys) {
            $q->select(DB::raw(1))
                ->from('student_values')
                ->whereColumn('student_values.student_id', 'student.id')
                ->whereIn('student_values.key', $semesterKeys)
                ->where('student_values.value', '!=', '');
        });
        
        $results = $query->groupBy('predicted_status')
            ->pluck('total', 'predicted_status')
            ->toArray();
        
        return [
            'lulus' => $results['lulus'] ?? 0,
            'lulus_bersyarat' => $results['lulus bersyarat'] ?? 0,
            'tidak_lulus' => $results['tidak lulus'] ?? 0
        ];
    }
    
    private function getAcademicVsNonAcademicData($statusFilter, $tahunAngkatanFilter, $semesterFilter)
    {
        $semesterKeys = array_map(fn($s) => "semester_$s", $semesterFilter);
        // Query untuk nilai akademik
        $academicQuery = StudentValue::select('key', DB::raw('AVG(CAST(value AS DECIMAL(10,2))) as average'))
            ->join('student', 'student_values.student_id', '=', 'student.id')
            ->where('value', '!=', '')
            ->where('student.jenis_data', 'testing')
            ->whereIn('key', array_merge($semesterKeys, ['usp']));
            
        // Query untuk nilai non-akademik
        $nonAcademicQuery = StudentValue::select('key', 'value')
            ->join('student', 'student_values.student_id', '=', 'student.id')
            ->where('value', '!=', '')
            ->where('student.jenis_data', 'testing')
            ->whereIn('key', ['sikap', 'kerajinan', 'kerapian']);
            
        if ($tahunAngkatanFilter !== 'all') {
            $academicQuery->where('student.tahun_angkatan', $tahunAngkatanFilter);
            $nonAcademicQuery->where('student.tahun_angkatan', $tahunAngkatanFilter);
        }
                
        if ($statusFilter !== 'all') {
            $academicQuery->join('predictions', 'student.id', '=', 'predictions.test_student_id')
                ->where('predictions.predicted_status', $statusFilter);
            $nonAcademicQuery->join('predictions', 'student.id', '=', 'predictions.test_student_id')
                ->where('predictions.predicted_status', $statusFilter);
        }
            
        $academicResults = $academicQuery->groupBy('key')
            ->pluck('average', 'key')
            ->toArray();
            
        // Proses nilai non-akademik
        $nonAcademicResults = $nonAcademicQuery->get()
            ->groupBy('key')
            ->map(function($values) {
                $numericValues = $values->map(function($value) {
                    return $this->convertAttitudeToNumeric($value->value);
                });
                return $numericValues->avg();
            })
            ->toArray();
        
        // Hanya semester yang dipilih
        $academic = [];
        foreach ($semesterKeys as $key) {
            $label = 'Rata-Rata ' . ucfirst(str_replace('_', ' ', $key));
            $academic[$label] = $academicResults[$key] ?? 0;
        }
        $academic['USP'] = $academicResults['usp'] ?? 0;
        
        return [
            'academic' => $academic,
            'non_academic' => [
                'sikap' => $nonAcademicResults['sikap'] ?? 0,
                'kerajinan' => $nonAcademicResults['kerajinan'] ?? 0,
                'kerapian' => $nonAcademicResults['kerapian'] ?? 0
            ]
        ];
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

    private function getSemesterTrendData($statusFilter, $semesterFilter, $tahunAngkatanFilter)
    {
        // Jika status filter adalah 'all', tampilkan semua status
        $statusCategories = $statusFilter === 'all' 
            ? ['lulus', 'lulus bersyarat', 'tidak lulus']
            : [$statusFilter];
            
        $trendData = [];
        
        foreach ($statusCategories as $status) {
            $studentIds = $this->getStudentIdsByStatus($status, $tahunAngkatanFilter);
            
            if (empty($studentIds)) {
                continue; // Skip jika tidak ada data untuk status ini
            }
            
            $averages = [];
            foreach ($semesterFilter as $i) {
                $query = StudentValue::where('key', "semester_$i")
                    ->where('value', '!=', '')
                    ->whereIn('student_id', $studentIds)
                    ->join('student', 'student_values.student_id', '=', 'student.id')
                    ->where('student.jenis_data', 'testing');
                    
                $average = $query->avg(DB::raw('CAST(value AS DECIMAL(10,2))')) ?? 0;
                // Pastikan nilai dalam rentang 0-100
                $average = max(0, min(100, $average));
                $averages[] = round($average, 2);
            }
            
            // Hanya tambahkan ke trendData jika ada nilai yang tidak 0
            if (array_sum($averages) > 0) {
                $trendData[$status] = $averages;
            }
        }
        $labels = array_map(fn($i) => 'Semester ' . $i, $semesterFilter);
        return [
            'labels' => $labels,
            'datasets' => $trendData
        ];
    }
 
    private function getCorrelationData($statusFilter, $tahunAngkatanFilter, $semesterFilter)
    {
        $semesterKeys = array_map(fn($s) => "semester_$s", $semesterFilter);
        $allKeys = array_merge($semesterKeys, ['usp', 'sikap', 'kerajinan', 'kerapian']);
        $query = StudentValue::select('key', 'value')
            ->join('student', 'student_values.student_id', '=', 'student.id')
            ->where('value', '!=', '')
            ->where('student.jenis_data', 'testing')
            ->whereIn('key', $allKeys);
            
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
            ->where('predicted_status', $status)
            ->where('student.jenis_data', 'testing');
            
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
