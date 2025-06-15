<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserManagementController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', \App\Http\Middleware\CheckRole::class.':superadministrator']);
    }

    public function index()
    {
        $users = User::all();
        return view('pages.user-management.index', compact('users'));
    }

    public function create()
    {
        return view('pages.user-management.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'role' => ['required', 'in:admin,superadministrator'],
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        return redirect()->route('user-management.index')
            ->with('success', 'User berhasil ditambahkan');
    }

    public function edit(User $user)
    {
        return view('pages.user-management.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'role' => ['required', 'in:admin,superadministrator'],
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
        ]);

        if ($request->filled('password')) {
            $request->validate([
                'password' => ['confirmed', Password::defaults()],
            ]);

            $user->update([
                'password' => Hash::make($request->password),
            ]);
        }

        return redirect()->route('user-management.index')
            ->with('success', 'User berhasil diperbarui');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->route('user-management.index')
                ->with('error', 'Tidak dapat menghapus akun sendiri');
        }

        $user->delete();

        return redirect()->route('user-management.index')
            ->with('success', 'User berhasil dihapus');
    }
}
