<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Category::where(function ($q) {
            $q->where('is_global', true)
              ->orWhere('created_by', auth()->id());
        });

        if ($request->type) {
            $query->where('type', $request->type);
        }

        return response()->json([
            'success' => true,
            'data'    => $query->where('is_active', true)->get(),
        ]);
    }
}
