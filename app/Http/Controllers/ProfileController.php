<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function verify()
    {
        return view('sessions.password.verify');
    }

    public function reset()
    {
        return view('sessions.password.reset');
    }

    public function create()
    {
        return view('pages.profile');
    }

    public function index()
    {
        return view('pages.laravel-examples.user-management');
    }

    public function inputData()
    {
        return view('pages.input-data');
    }

    public function profile()
    {
        return view('pages.profile');
    }

    public function staticSignIn()
    {
        return view('pages.static-sign-in');
    }

    public function staticSignUp()
    {
        return view('pages.static-sign-up');
    }

    public function update()
    {
        $user = request()->user();
        $attributes = request()->validate([
            'email' => 'required|email|unique:users,email,' . $user->id,
            'name' => 'required',
            'phone' => 'required|max:10',
            'about' => 'required:max:150',
            'location' => 'required'
        ]);

        auth()->user()->update($attributes);
        return back()->withStatus('Profile successfully updated.');
    }
}
