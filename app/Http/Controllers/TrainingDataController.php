<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\StudentValue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TrainingDataController extends Controller
{
    // Tampilkan semua data latih
    public function index()
    {
        $students = Student::where('jenis_data', 'training')->with('studentValues')->get();
        return view('pages.data-latih', compact('students'));
    }

    // Tampilkan form edit data latih
    public function edit($id)
    {
        $student = Student::where('jenis_data', 'training')->with('studentValues')->findOrFail($id);
        return view('pages.edit-data-latih', compact('student'));
    }

    // Update data latih
    public function update(Request $request, $id)
    {
        $student = Student::where('jenis_data', 'training')->findOrFail($id);
        $student->update($request->only(['nisn', 'name', 'tahun_angkatan', 'true_status']));

        // Update nilai/atribut
        $values = $request->input('values', []);
        foreach ($values as $key => $value) {
            StudentValue::updateOrCreate(
                ['student_id' => $student->id, 'key' => $key],
                ['value' => $value]
            );
        }
        return redirect()->route('training.index')->with('success', 'Data latih berhasil diupdate.');
    }

    // Hapus data latih
    public function destroy($id)
    {
        $student = Student::where('jenis_data', 'training')->findOrFail($id);
        DB::transaction(function () use ($student) {
            $student->studentValues()->delete();
            $student->delete();
        });
        return redirect()->route('training.index')->with('success', 'Data latih berhasil dihapus.');
    }
} 