<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Livewire\Livewire;
use Illuminate\Support\Facades\Response;
use App\Http\Controllers\Auth\LoginController;

/* NOTE: Do Not Remove
/ Livewire asset handling if using sub folder in domain
*/

Livewire::setUpdateRoute(function ($handle) {
    return Route::post(config('app.asset_prefix') . '/livewire/update', $handle);
});

Livewire::setScriptRoute(function ($handle) {
    return Route::get(config('app.asset_prefix') . '/livewire/livewire.js', $handle);
});
/*
/ END
*/

Route::get('/', function () {
    // Kalau sudah login → redirect ke panel sesuai role
    if (Auth::check()) {
        $user = Auth::user();
        return match(true) {
            $user->hasRole('super_admin'), $user->hasRole('admin') => redirect('/admin'),
            $user->hasRole('parent') => redirect('/parent'),
            $user->hasRole('child')  => redirect('/child'),
            default                  => redirect('/login'),
        };
    }
    // Belum login → ke login page
    return redirect('/login');
});

// ===== Auth =====
Route::get('/login', [LoginController::class, 'show'])->name('auth.login');
Route::post('/login', [LoginController::class, 'login'])->name('auth.login.post');
Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/login');
})->name('logout');
