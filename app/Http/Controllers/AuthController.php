<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLoginForm() {
        return view('auth.login');
    }
    public function login(Request $request) {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            ActivityLog::record('login', 'Pengguna Melakukan Login');

            // Redirect berdasarkan role
            if (Auth::user()->role == 'admin') {
                return redirect('/admin/dashboard');
            } elseif (Auth::user()->role == 'petugas') {
                return redirect('/petugas/dashboard');
            } else {
                return redirect('/peminjam/dashboard');
            }
        }

        return back()->withErrors(['email' => 'Login Gagal']);
    }
    public function logout(Request $request) {
        ActivityLog::record('logout', 'Pengguna Melakukan Logout');
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}
