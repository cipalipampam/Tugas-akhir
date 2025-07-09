<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use Illuminate\Support\Facades\Response;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;

class ExportController extends Controller
{
    public function index()
    {
        try {
            $exportHistory = $this->getExportHistory();
            // Get unique years from students table
            $tahunAngkatan = Student::select('tahun_angkatan')
                ->distinct()
                ->orderBy('tahun_angkatan', 'desc')
                ->pluck('tahun_angkatan');
            
            return view('pages.export', compact('exportHistory', 'tahunAngkatan'));
        } catch (Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    
    public function export(Request $request)
    {
        try {
            // Validate the request
            $request->validate([
                'fileFormat' => 'required|in:pdf,csv',
                'dataType' => 'required|in:all,passed,failed,conditional_passed,prediction',
                'title' => 'nullable|string|max:255',
                // 'schoolYear' => 'nullable|string|max:20',
                'tahunAngkatan' => 'nullable|string|max:4',
            ]);
            
            // Get data based on the type
            $data = $this->getDataByType($request->dataType, $request->tahunAngkatan);
            
            if ($data->isEmpty()) {
                // Jika request AJAX, balas JSON error
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json(['error' => 'Data tidak ditemukan untuk konfigurasi yang dipilih.'], 422);
                }
                return back()->with('error', 'Tidak ada data yang dapat diekspor');
            }
            
            // Get included columns
            $includedColumns = $this->getIncludedColumns($request);
            
            if (empty($includedColumns)) {
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json(['error' => 'Pilih minimal satu kolom untuk diekspor'], 422);
                }
                return back()->with('error', 'Pilih minimal satu kolom untuk diekspor');
            }
            
            // Export based on format
            switch ($request->fileFormat) {
                case 'pdf':
                    return $this->exportPdf($data, $includedColumns, $request->title, $request->tahunAngkatan);
                case 'csv':
                    return $this->exportCsv($data, $includedColumns, $request->title, $request->tahunAngkatan);
                default:
                    if ($request->ajax() || $request->wantsJson()) {
                        return response()->json(['error' => 'Format tidak valid'], 422);
                    }
                    return back()->with('error', 'Format tidak valid');
            }
        } catch (Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['error' => 'Terjadi kesalahan saat mengekspor data: ' . $e->getMessage()], 500);
            }
            return back()->with('error', 'Terjadi kesalahan saat mengekspor data: ' . $e->getMessage());
        }
    }
    
    private function getDataByType($type, $tahunAngkatan = null)
    {
        try {
            $query = Student::with(['studentValues', 'prediction'])
                ->where('jenis_data', 'testing');
            
            // Apply year filter if specified
            if (!empty($tahunAngkatan)) {
                $query->where('tahun_angkatan', $tahunAngkatan);
            }
            
            switch ($type) {
                case 'all':
                    return $query->get();
                case 'passed':
                    // Hanya testing dengan predicted_status lulus
                    return $query->whereHas('prediction', function($q) {
                        $q->where('predicted_status', 'lulus');
                    })->get();
                case 'conditional_passed':
                    // Hanya testing dengan predicted_status lulus bersyarat
                    return $query->whereHas('prediction', function($q) {
                        $q->where('predicted_status', 'lulus bersyarat');
                    })->get();
                case 'failed':
                    // Hanya testing dengan predicted_status tidak lulus
                    return $query->whereHas('prediction', function($q) {
                        $q->where('predicted_status', 'tidak lulus');
                    })->get();
                case 'prediction':
                    // Semua testing yang punya prediksi
                    return $query->whereHas('prediction')->get();
                default:
                    return collect([]);
            }
        } catch (Exception $e) {
            throw new Exception('Gagal mengambil data: ' . $e->getMessage());
        }
    }
    
    private function getIncludedColumns(Request $request)
    {
        // Ambil array columns[] dari request
        $columns = $request->input('columns', []);
        return $columns;
    }
    
    private function exportPdf($data, $columns, $title, $tahunAngkatan)
    {
        try {
            $formattedData = $this->formatDataForExport($data, $columns);
            
            $pdf = PDF::loadView('exports.pdf', [
                'data' => $formattedData,
                'columns' => $columns,
                'title' => $title ?: 'Laporan Data Siswa',
                'tahunAngkatan' => $tahunAngkatan ?: date('Y')
            ]);
            
            // Set PDF to landscape orientation
            $pdf->setPaper('a4', 'landscape');
            
            $this->saveExportHistory('pdf', $title);
            
            return $pdf->download(($title ?: 'Laporan_Data_Siswa') . '.pdf');
        } catch (Exception $e) {
            throw new Exception('Gagal mengekspor PDF: ' . $e->getMessage());
        }
    }
    
    private function exportCsv($data, $columns, $title, $schoolYear)
    {
        try {
            $formattedData = $this->formatDataForExport($data, $columns);
            
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            
            // Add headers
            $col = 1;
            foreach ($columns as $column) {
                $sheet->setCellValueByColumnAndRow($col, 1, ucwords(str_replace('_', ' ', $column)));
                $col++;
            }
            
            // Add data
            $row = 2;
            foreach ($formattedData as $item) {
                $col = 1;
                foreach ($columns as $column) {
                    $sheet->setCellValueByColumnAndRow($col, $row, $item[$column] ?? '');
                    $col++;
                }
                $row++;
            }
            
            // Create writer
            $writer = new Csv($spreadsheet);
            $filename = ($title ?: 'Laporan_Data_Siswa') . '.csv';
            $filepath = storage_path('app/public/' . $filename);
            
            // Save file
            $writer->save($filepath);
            
            $this->saveExportHistory('csv', $title);
            
            return Response::download($filepath, $filename, [
                'Content-Type' => 'text/csv',
            ])->deleteFileAfterSend(true);
        } catch (Exception $e) {
            throw new Exception('Gagal mengekspor CSV: ' . $e->getMessage());
        }
    }
    
    private function formatDataForExport($students, $columns)
    {
        try {
            $formattedData = [];
            foreach ($students as $student) {
                $item = [];
                foreach ($columns as $col) {
                    if ($col === 'nisn') {
                        $item['nisn'] = $student->nisn;
                    } elseif ($col === 'name') {
                        $item['name'] = $student->name;
                    } elseif ($col === 'status') {
                        $item['status'] = $student->prediction->predicted_status ?? 'Belum ada status';
                    } else {
                        // Cek di studentValues
                        $value = null;
                        if ($student->studentValues) {
                            foreach ($student->studentValues as $sv) {
                                if ($sv->key === $col) {
                                    $value = $sv->value;
                                    break;
                                }
                            }
                        }
                        $item[$col] = $value;
                    }
                }
                $formattedData[] = $item;
            }
            return $formattedData;
        } catch (Exception $e) {
            throw new Exception('Gagal memformat data: ' . $e->getMessage());
        }
    }
    
    private function saveExportHistory($format, $title)
    {
        // Implementation for saving export history
        return true;
    }
    
    private function getExportHistory()
    {
        // Mock data for export history
        return [
            [
                'date' => '20 Jun 2023',
                'time' => '14:30',
                'filename' => 'Laporan_Kelulusan_2023',
                'format' => 'Excel',
                'size' => '256 KB',
            ],
            [
                'date' => '15 Jun 2023',
                'time' => '09:15',
                'filename' => 'Data_Siswa_Lulus_2023',
                'format' => 'PDF',
                'size' => '512 KB',
            ],
            [
                'date' => '10 Jun 2023',
                'time' => '11:45',
                'filename' => 'Hasil_Prediksi_Batch_1',
                'format' => 'CSV',
                'size' => '128 KB',
            ],
        ];
    }
}
