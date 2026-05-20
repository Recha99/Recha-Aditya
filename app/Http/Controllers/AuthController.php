<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

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

    public function showRegisterForm() {
        return view('auth.register');
    }

    public function register(Request $request) {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'peminjam', // Otomatis sebagai peminjam
        ]);

        ActivityLog::record('register', 'Pengguna baru mendaftar: ' . $user->name);

        Auth::login($user);

        return redirect('/login')->with('success', 'Registrasi berhasil. silakan login untuk melanjutkan.');
    }
}
