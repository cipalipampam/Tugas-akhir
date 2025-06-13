<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GraduationRule;
use Illuminate\Support\Facades\Validator;
use App\Http\Middleware\CheckRole;

class KebijakanController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', CheckRole::class.':superadministrator']);
    }

    public function index()
    {
        $rules = GraduationRule::orderBy('priority')->get();
        return view('pages.kebijakan.index', compact('rules'));
    }

    public function create()
    {
        return view('pages.kebijakan.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'attribute' => 'required|string',
            'operator' => 'required|in:<,<=,=,>=,>',
            'value' => 'required|numeric',
            'category' => 'required|in:lulus,lulus bersyarat,tidak lulus',
            'priority' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Additional validation for value ranges
        $value = floatval($request->value);
        $attribute = $request->attribute;

        if (in_array($attribute, ['sikap', 'kerajinan', 'kerapian'])) {
            if ($value < 0 || $value > 1) {
                return redirect()->back()
                    ->withErrors(['value' => 'Nilai untuk ' . $attribute . ' harus antara 0 dan 1'])
                    ->withInput();
            }
        } elseif (in_array($attribute, ['rata_rata', 'usp'])) {
            if ($value < 0 || $value > 100) {
                return redirect()->back()
                    ->withErrors(['value' => 'Nilai untuk ' . $attribute . ' harus antara 0 dan 100'])
                    ->withInput();
            }
        }

        try {
            GraduationRule::create($request->only([
                'attribute', 'operator', 'value', 'category', 'priority'
            ]));

            return redirect()->route('kebijakan.index')
                ->with('success', 'Aturan berhasil ditambahkan');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat menambahkan aturan: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            $rule = GraduationRule::findOrFail($id);
            $rule->delete();

            return response()->json([
                'success' => true,
                'message' => 'Aturan berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus aturan: ' . $e->getMessage()
            ], 500);
        }
    }
}
