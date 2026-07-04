<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Livewire\Livewire;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Support\RoleRedirector;

/*
|--------------------------------------------------------------------------
| Livewire Asset Handling
|--------------------------------------------------------------------------
*/

Livewire::setUpdateRoute(function ($handle) {
    return Route::post(
        config('app.asset_prefix') . '/livewire/update',
        $handle
    );
});

Livewire::setScriptRoute(function ($handle) {
    return Route::get(
        config('app.asset_prefix') . '/livewire/livewire.js',
        $handle
    );
});

/*
|--------------------------------------------------------------------------
| Home
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    if (Auth::check()) {
        return RoleRedirector::to(Auth::user());
    }

    return view('landing');
})->name('landing');

/*
|--------------------------------------------------------------------------
| Authentication
|--------------------------------------------------------------------------
*/

// GET /login
Route::get('/login', [LoginController::class, 'show'])
    ->name('login');

// Alias supaya route('auth.login') tetap jalan
Route::get('/auth-login', function () {
    return redirect()->route('login');
})->name('auth.login');

// POST /login
Route::post('/login', [LoginController::class, 'login'])
    ->name('login.post');

// Alias supaya route('auth.login.post') tetap jalan
Route::post('/auth-login', [LoginController::class, 'login'])
    ->name('auth.login.post');

Route::post('/logout', function () {
    Auth::logout();

    request()->session()->invalidate();
    request()->session()->regenerateToken();

    return redirect('/login');
})->name('logout');

/*
|--------------------------------------------------------------------------
| Register
|--------------------------------------------------------------------------
*/

Route::get('/register', [RegisterController::class, 'show'])
    ->name('auth.register');

Route::post('/register', [RegisterController::class, 'register'])
    ->name('auth.register.post');

/*
|--------------------------------------------------------------------------
| Other Pages
|--------------------------------------------------------------------------
*/

Route::get('/forgot-password', [ForgotPasswordController::class, 'show'])
    ->name('password.request');

Route::post('/forgot-password', [ForgotPasswordController::class, 'send'])
    ->name('password.email');

// Reset Password
Route::get('/reset-password/{token}', [App\Http\Controllers\Auth\ResetPasswordController::class, 'show'])
    ->name('password.reset');
Route::post('/reset-password', [App\Http\Controllers\Auth\ResetPasswordController::class, 'reset'])
    ->name('password.update');

// Google OAuth
Route::get('/auth/google', [App\Http\Controllers\Auth\SocialAuthController::class, 'redirectToGoogle'])
    ->name('auth.google');
Route::get('/auth/google/callback', [App\Http\Controllers\Auth\SocialAuthController::class, 'handleGoogleCallback']);

Route::view('/privacy', 'privacy')->name('privacy');
Route::view('/terms', 'terms')->name('terms');

/*
|--------------------------------------------------------------------------
| Personal Account Alias
|--------------------------------------------------------------------------
| Akun pribadi (is_independent) tetap memakai panel Child di belakang,
| hanya path awal yang berbeda secara kosmetik.
*/

Route::get('/personal/{any?}', function ($any = null) {
    return redirect('/child/' . $any);
})->where('any', '.*');


Route::middleware(['auth', \App\Http\Middleware\RedirectIfDesktop::class])->prefix('mobile')->name('mobile.')->group(function () {
    Route::get('/dashboard', \App\Livewire\Mobile\Dashboard::class)->name('dashboard');
    Route::get('/add-transaction', \App\Livewire\Mobile\AddTransaction::class)->name('add-transaction');
    Route::get('/history', \App\Livewire\Mobile\TransactionHistory::class)->name('history');
    Route::get('/profile', \App\Livewire\Mobile\Profile::class)->name('profile');
    Route::get('/account', \App\Livewire\Mobile\AccountSetting::class)->name('account');
    Route::get('/keluarga', \App\Livewire\Mobile\Keluarga::class)->name('keluarga');
    Route::get('/anggaran', \App\Livewire\Mobile\Anggaran::class)->name('anggaran');
    Route::get('/laporan', \App\Livewire\Mobile\Laporan::class)->name('laporan');
    Route::get('/tabungan', \App\Livewire\Mobile\Tabungan::class)->name('tabungan');
    Route::get('/tabungan/create', \App\Livewire\Mobile\TabunganCreate::class)->name('tabungan.create');
    Route::get('/tabungan/completed', \App\Livewire\Mobile\TabunganComplete::class)->name('tabungan.completed');
    Route::get('/tabungan/{saving}', \App\Livewire\Mobile\TabunganDetail::class)->name('tabungan.show');
});

Route::get('/test-device', function () {
    return session('is_mobile_device') ? 'MOBILE TERDETEKSI' : 'DESKTOP TERDETEKSI';
});
