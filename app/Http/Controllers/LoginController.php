<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function index()
    {
        return view('login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => ['required'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            if ($user->status === 'Inactive') {
                Auth::logout();
                return back()->with('error', 'Anda sudah nonaktif!');
            }

            $request->session()->regenerate();

            // Redirect berdasarkan Role
            if ($user->role === 'Admin') {
                return redirect()->intended('/admin/dashboard');
            } elseif ($user->role === 'PGA') {
                return redirect()->intended('/pga/dashboard');
            } elseif ($user->role === 'Satpam') {
                return redirect()->intended('/satpam/dashboard');
            }

            return redirect('/');
        }

        return back()->with('error', 'Username atau Password salah!');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}