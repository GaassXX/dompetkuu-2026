<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\RoleRedirector;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    public function redirectToGoogle(Request $request): RedirectResponse
    {
        $action = $request->query('action', 'login');
        session(['google_action' => $action]);

        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback(): RedirectResponse
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Exception $e) {
            Log::error('Google login failed', ['error' => $e->getMessage()]);
            return redirect('/login')->with('error', 'Gagal terhubung ke Google. Silakan coba lagi.');
        }

        $action = session('google_action', 'login');
        session()->forget('google_action');

        $user = User::where('email', $googleUser->email)->first();

        if ($action === 'register' && !$user) {
            return redirect()->route('auth.register')->with([
                'google_name'  => $googleUser->name,
                'google_email' => $googleUser->email,
                'google_id'    => $googleUser->id,
            ]);
        }

        if (!$user) {
            return redirect('/login')
                ->with('error', 'Email Google ini tidak terdaftar di sistem. Daftar dulu lewat halaman register.');
        }

        if (!$user->is_active) {
            return redirect('/login')
                ->with('error', 'Akun Anda dinonaktifkan. Hubungi admin.');
        }

        $user->update([
            'google_id' => $googleUser->id,
        ]);

        Auth::login($user);

        return RoleRedirector::to($user);
    }
}
