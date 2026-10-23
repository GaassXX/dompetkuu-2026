<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'account_type' => 'required|in:parent,child,independent',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Tentukan role user
        |--------------------------------------------------------------------------
        |
        | parent      -> parent
        | child       -> child
        | independent -> personal
        |
        */

        $role = match ($validated['account_type']) {
            'parent' => 'parent',
            'child' => 'child',
            'independent' => 'personal',
        };

        $isIndependent = $validated['account_type'] === 'independent';

        /*
        |--------------------------------------------------------------------------
        | Buat user
        |--------------------------------------------------------------------------
        */

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'role' => $role,
            'is_independent' => $isIndependent,
        ]);

        /*
        |--------------------------------------------------------------------------
        | Assign Spatie Role
        |--------------------------------------------------------------------------
        |
        | Penting supaya kolom users.role dan Spatie role selalu sama.
        |
        */

        $user->assignRole($role);

        /*
        |--------------------------------------------------------------------------
        | Buat API Token
        |--------------------------------------------------------------------------
        */

        $token = $user
            ->createToken('api-token')
            ->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Registrasi berhasil',
            'data' => [
                'user' => $user,
                'token' => $token,
            ],
        ], 201);
    }
}