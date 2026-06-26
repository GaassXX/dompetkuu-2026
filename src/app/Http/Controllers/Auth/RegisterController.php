<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\RoleRedirector;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function show()
    {
        return view('auth.register', [
            'google_name'  => session('google_name'),
            'google_email' => session('google_email'),
            'google_id'    => session('google_id'),
        ]);
    }

    public function register(Request $request)
    {
        session()->forget(['google_name', 'google_email', 'google_id']);
        $rules = [
            'name'         => 'required|string|max:255',
            'email'        => 'required|email|unique:users,email',
            'account_type' => 'required|in:parent,independent',
        ];

        $isFromGoogle = $request->filled('google_id');

        if ($isFromGoogle) {
            $rules['google_id'] = 'required|string|unique:users,google_id';
        } else {
            $rules['password'] = 'required|min:8|confirmed';
        }

        $request->validate($rules);

        $isParent      = $request->account_type === 'parent';
        $isIndependent = $request->account_type === 'independent';

        // Kolom 'role' di tabel users: untuk label/tampilan & query ringan.
        // Akun pribadi diberi label 'personal' agar tidak disebut "anak".
        $roleLabel = match(true) {
            $isParent      => 'parent',
            $isIndependent => 'personal',
            default        => 'child',
        };

        // Spatie Permission role: TETAP 'parent' atau 'child' saja.
        // Akun pribadi tetap diberi role Spatie 'child' supaya semua
        // hasRole('child') check (akses panel /child, policy resource, dll)
        // tetap berfungsi tanpa perlu diubah di puluhan tempat.
        $spatieRole = $isParent ? 'parent' : 'child';

        $user = User::create([
            'name'           => $request->name,
            'email'          => $request->email,
            'password'       => $isFromGoogle ? Hash::make(uniqid()) : Hash::make($request->password),
            'role'           => $roleLabel,
            'is_independent' => $isIndependent,
            'parent_id'      => null,
            'is_active'      => true,
            'google_id'      => $isFromGoogle ? $request->google_id : null,
        ]);

        $user->assignRole($spatieRole);
        Auth::login($user);

        return RoleRedirector::to($user);
    }
}
