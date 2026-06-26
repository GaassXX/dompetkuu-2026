<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $user = $request->user()->load('roles');

        return response()->json([
            'success' => true,
            'data'    => $user,
        ]);
    }
}
