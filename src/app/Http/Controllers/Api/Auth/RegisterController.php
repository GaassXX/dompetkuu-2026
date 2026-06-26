<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RegisterController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'email'         => 'required|email|unique:users,email',
            'password'      => 'required|min:8|confirmed',
            'account_type'  => 'required|in:parent,child,independent',
        ]);

        $role       = $validated['account_type'] === 'parent' ? 'parent' : 'child';
        $isIndependent = $validated['account_type'] === 'independent';

        $user = User::create([
            'name'            => $validated['name'],
            'email'           => $validated['email'],
            'password'        => bcrypt($validated['password']),
            'role'            => $validated['account_type'] === 'parent' ? 'parent' : ($isIndependent ? 'personal' : 'child'),
            'is_independent'  => $isIndependent,
        ]);

        $user->assignRole($role);

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Registrasi berhasil',
            'data'    => [
                'user'  => $user,
                'token' => $token,
            ],
        ], 201);
    }
}
