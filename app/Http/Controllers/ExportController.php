<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use Illuminate\Support\Facades\Response;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;

class ExportController extends Controller
{
    public function index()
    {
        try {
            $exportHistory = $this->getExportHistory();
            return view('pages.export', compact('exportHistory'));
        } catch (Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    
    public function export(Request $request)
    {
        try {
            // Validate the request
            $request->validate([
                'fileFormat' => 'required|in:pdf,excel,csv,print',
                'dataType' => 'required|in:all,passed,failed,prediction',
                'title' => 'nullable|string|max:255',
                'schoolYear' => 'nullable|string|max:20',
            ]);
            
            // Get data based on the type
            $data = $this->getDataByType($request->dataType);
            
            if ($data->isEmpty()) {
                return back()->with('error', 'Tidak ada data yang dapat diekspor');
            }
            
            // Get included columns
            $includedColumns = $this->getIncludedColumns($request);
            
            if (empty($includedColumns)) {
                return back()->with('error', 'Pilih minimal satu kolom untuk diekspor');
            }
            
            // Export based on format
            switch ($request->fileFormat) {
                case 'pdf':
                    return $this->exportPdf($data, $includedColumns, $request->title, $request->schoolYear);
                case 'excel':
                    return $this->exportExcel($data, $includedColumns, $request->title, $request->schoolYear);
                case 'csv':
                    return $this->exportCsv($data, $includedColumns, $request->title, $request->schoolYear);
                case 'print':
                    return $this->exportPrint($data, $includedColumns, $request->title, $request->schoolYear);
                default:
                    return back()->with('error', 'Format tidak valid');
            }
        } catch (Exception $e) {
            return back()->with('error', 'Terjadi kesalahan saat mengekspor data: ' . $e->getMessage());
        }
    }
    
    private function getDataByType($type)
    {
        try {
            switch ($type) {
                case 'all':
                    return Student::with('studentValues')->get();
                case 'passed':
                    return Student::with('studentValues')
                        ->where('true_status', 'lulus')
                        ->orWhereHas('predictions', function ($query) {
                            $query->where('predicted_status', 'lulus');
                        })
                        ->get();
                case 'failed':
                    return Student::with('studentValues')
                        ->where('true_status', 'tidak lulus')
                        ->orWhereHas('predictions', function ($query) {
                            $query->where('predicted_status', 'tidak lulus');
                        })
                        ->get();
                case 'prediction':
                    return Student::with(['studentValues', 'predictions' => function ($query) {
                        $query->latest();
                    }])
                        ->whereHas('predictions')
                        ->get()
                        ->map(function ($student) {
                            $student->predicted_status = $student->predictions->first()->predicted_status ?? null;
                            return $student;
                        });
                default:
                    return collect([]);
            }
        } catch (Exception $e) {
            throw new Exception('Gagal mengambil data: ' . $e->getMessage());
        }
    }
    
    private function getIncludedColumns(Request $request)
    {
        $columns = [];
        
        if ($request->has('includeNISN')) {
            $columns[] = 'nisn';
        }
        
        if ($request->has('includeName')) {
            $columns[] = 'name';
        }
        
        if ($request->has('includeGrades')) {
            $columns = array_merge($columns, [
                'Rata-Rata Semester 1',
                'Rata-Rata Semester 2',
                'Rata-Rata Semester 3',
                'Rata-Rata Semester 4',
                'Rata-Rata Semester 5',
                'Rata-Rata Semester 6',
                'usp'
            ]);
        }
        
        if ($request->has('includeNonAcademic')) {
            $columns = array_merge($columns, [
                'sikap',
                'kerapian',
                'kerajinan'
            ]);
        }
        
        if ($request->has('includeStatus')) {
            $columns[] = 'status';
        }
        
        return $columns;
    }
    
    private function exportPdf($data, $columns, $title, $schoolYear)
    {
        try {
            $formattedData = $this->formatDataForExport($data, $columns);
            
            $pdf = PDF::loadView('exports.pdf', [
                'data' => $formattedData,
                'columns' => $columns,
                'title' => $title ?: 'Laporan Data Siswa',
                'schoolYear' => $schoolYear ?: date('Y')
            ]);
            
            $this->saveExportHistory('pdf', $title);
            
            return $pdf->download(($title ?: 'Laporan_Data_Siswa') . '.pdf');
        } catch (Exception $e) {
            throw new Exception('Gagal mengekspor PDF: ' . $e->getMessage());
        }
    }
    
    private function exportExcel($data, $columns, $title, $schoolYear)
    {
        try {
            $formattedData = $this->formatDataForExport($data, $columns);
            
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            
            // Add title and school year
            $sheet->mergeCells('A1:' . $sheet->getHighestColumn() . '1');
            $sheet->setCellValue('A1', $title ?: 'Laporan Data Siswa');
            $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
            $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            
            $sheet->mergeCells('A2:' . $sheet->getHighestColumn() . '2');
            $sheet->setCellValue('A2', 'Tahun Ajaran: ' . ($schoolYear ?: date('Y')));
            $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            
            // Add headers
            $col = 1;
            foreach ($columns as $column) {
                $sheet->setCellValueByColumnAndRow($col, 3, ucwords(str_replace('_', ' ', $column)));
                $sheet->getStyleByColumnAndRow($col, 3)->getFont()->setBold(true);
                $col++;
            }
            
            // Add data
            $row = 4;
            foreach ($formattedData as $item) {
                $col = 1;
                foreach ($columns as $column) {
                    $sheet->setCellValueByColumnAndRow($col, $row, $item[$column] ?? '');
                    $col++;
                }
                $row++;
            }
            
            // Auto-size columns
            foreach (range('A', $sheet->getHighestColumn()) as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }
            
            // Add borders
            $lastRow = $row - 1;
            $lastCol = $sheet->getHighestColumn();
            $sheet->getStyle('A3:' . $lastCol . $lastRow)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            
            // Create writer
            $writer = new Xlsx($spreadsheet);
            $filename = ($title ?: 'Laporan_Data_Siswa') . '.xlsx';
            $filepath = storage_path('app/public/' . $filename);
            
            // Save file
            $writer->save($filepath);
            
            $this->saveExportHistory('excel', $title);
            
            return Response::download($filepath, $filename, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ])->deleteFileAfterSend(true);
        } catch (Exception $e) {
            throw new Exception('Gagal mengekspor Excel: ' . $e->getMessage());
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
    
    private function exportPrint($data, $columns, $title, $schoolYear)
    {
        try {
            $formattedData = $this->formatDataForExport($data, $columns);
            
            // Add print-specific data
            $printData = [
                'data' => $formattedData,
                'columns' => $columns,
                'title' => $title ?: 'Laporan Data Siswa',
                'schoolYear' => $schoolYear ?: date('Y'),
                'printDate' => date('d/m/Y H:i:s'),
                'totalRecords' => count($formattedData)
            ];
            
            // Save export history
            $this->saveExportHistory('print', $title);
            
            return view('exports.print', $printData);
        } catch (Exception $e) {
            throw new Exception('Gagal mengekspor Print: ' . $e->getMessage());
        }
    }
    
    private function formatDataForExport($students, $columns)
    {
        try {
            $formattedData = [];
            
            foreach ($students as $student) {
                $item = [
                    'nisn' => $student->nisn,
                    'name' => $student->name,
                    'status' => $student->true_status ?? ($student->predicted_status ?? 'Belum ada status')
                ];
                
                // Add student values
                if ($student->studentValues) {
                    foreach ($student->studentValues as $value) {
                        $item[$value->key] = $value->value;
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
