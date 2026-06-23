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
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name'         => 'required|string|max:255',
            'email'        => 'required|email|unique:users,email',
            'password'     => 'required|min:8|confirmed',
            'account_type' => 'required|in:parent,independent',
        ]);

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
            'password'       => Hash::make($request->password),
            'role'           => $roleLabel,
            'is_independent' => $isIndependent,
            'parent_id'      => null,
            'is_active'      => true,
        ]);

        $user->assignRole($spatieRole);
        Auth::login($user);

        return RoleRedirector::to($user);
    }
}
