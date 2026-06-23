<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Support\RoleRedirector;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function show()
    {
        if (Auth::check()) {
            return RoleRedirector::to(Auth::user());
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'login'    => 'required',
            'password' => 'required',
        ]);

        // Tentukan apakah input berupa email atau nomor HP
        $loginField = filter_var($request->login, FILTER_VALIDATE_EMAIL)
            ? 'email'
            : 'phone'; // sesuaikan dengan nama kolom nomor HP di tabel users

        $credentials = [
            $loginField => $request->login,
            'password'  => $request->password,
        ];

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            return RoleRedirector::to(Auth::user());
        }

        return back()
            ->withErrors(['login' => 'Email/No. HP atau password salah.'])
            ->withInput($request->only('login'));
    }
}
