<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
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

        $isParent = $request->account_type === 'parent';

        $user = User::create([
            'name'           => $request->name,
            'email'          => $request->email,
            'password'       => Hash::make($request->password),
            'role'           => $isParent ? 'parent' : 'child',
            'is_independent' => !$isParent,
            'parent_id'      => null,
            'is_active'      => true,
        ]);

        $user->assignRole($isParent ? 'parent' : 'child');

        Auth::login($user);

        return match(true) {
            $user->hasRole('parent') => redirect('/parent'),
            default                  => redirect('/child'),
        };
    }
}
