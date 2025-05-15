<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;
use App\Models\StudentValue;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Log;

class InputDataController extends Controller
{
public function index()
{
    return view('pages.input-data');
}
    public function preview(Request $request)
    {
        try {
            if (!$request->hasFile('excel_file')) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'File tidak ditemukan pada request!',
                ]);
            }

            $file = $request->file('excel_file'); // disamakan dengan name di form
            $spreadsheet = IOFactory::load($file->getPathname());
            $worksheet = $spreadsheet->getActiveSheet();

            $rows = [];
            foreach ($worksheet->getRowIterator() as $index => $row) {
                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(false);

                $cells = [];
                foreach ($cellIterator as $cell) {
                    $cells[] = $cell->getFormattedValue(); // lebih aman dari getValue()
                }

                // Skip baris kosong
                if (array_filter($cells)) {
                    $rows[] = $cells;
                }
            }

            if (count($rows) < 2) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Data tidak cukup (minimal 2 baris: header dan 1 data)',
                ]);
            }

            $headers = $rows[0];
            $dataRows = array_slice($rows, 1);

            $formattedRows = [];
            foreach ($dataRows as $dataRow) {
                $formattedRow = [];
                foreach ($headers as $key => $header) {
                    $formattedRow[$header] = $dataRow[$key] ?? '';
                }
                $formattedRows[] = $formattedRow;
            }

            return response()->json([
                'status' => 'success',
                'headers' => $headers,
                'rows' => $formattedRows
            ]);

        } catch (\Exception $e) {
            Log::error('Excel Preview Error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal memproses file Excel: ' . $e->getMessage(),
            ]);
        }
    }

    public function simpanData(Request $request)
    {
        $data = $request->input('data');

        if (empty($data)) {
            return response()->json(['status' => 'error', 'message' => 'Data tidak boleh kosong.']);
        }

        DB::beginTransaction();
        try {
            foreach ($data as $row) {
                // Ambil dan normalisasi nama dan NISN
                $name = trim($row['nama'] ?? '');
                $nisn = trim($row['nisn'] ?? '');
                $trueStatus = trim($row['status'] ?? '');

                // Tentukan jenis data secara otomatis
                $jenisData = $trueStatus !== '' ? 'training' : 'testing';

                // Lewati jika NISN atau nama kosong
                if ($name === '' || $nisn === '') {
                    continue;
                }

                // Lewati jika siswa sudah ada
                if (Student::where('nisn', $nisn)->exists()) {
                    continue;
                }

                // Simpan siswa baru
                $student = Student::create([
                    'nisn' => $nisn,
                    'name' => $name,
                    'true_status' => $trueStatus !== '' ? $trueStatus : null,
                    'jenis_data' => $jenisData,
                ]);

                // Simpan atribut nilai
                foreach ($row as $key => $value) {
                    if (in_array($key, ['nama', 'nisn', 'jenis_data', 'status'])) {
                        continue;
                    }

                    if (trim($key) !== '' && $value !== null) {
                        StudentValue::create([
                            'student_id' => $student->id,
                            'key' => trim($key),
                            'value' => trim($value),
                        ]);
                    }
                }
            }

            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Data berhasil disimpan.']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => 'error', 'message' => 'Gagal menyimpan data: ' . $e->getMessage()]);
        }
    }
    
}
