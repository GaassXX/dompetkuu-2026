<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Expense;
use App\Models\Income;
use App\Models\Transaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $userId = $request->user()->id;

        $incomes = Income::where('user_id', $userId)
            ->with('category');

        $expenses = Expense::where('user_id', $userId)
            ->with('category');

        if ($request->type === 'income') {
            $expenses->whereRaw('1 = 0');
        } elseif ($request->type === 'expense') {
            $incomes->whereRaw('1 = 0');
        }

        if ($request->status) {
            $incomes->where('status', $request->status);
            $expenses->where('status', $request->status);
        }

        if ($request->date_from) {
            $incomes->where('date', '>=', $request->date_from);
            $expenses->where('date', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $incomes->where('date', '<=', $request->date_to);
            $expenses->where('date', '<=', $request->date_to);
        }

        $incomes  = $incomes->latest('date')->get();
        $expenses = $expenses->latest('date')->get();

        $mapped = collect();

        foreach ($incomes as $i) {
            $mapped->push([
                'id'          => 'inc-' . $i->id,
                'type'        => 'income',
                'amount'      => (float) $i->amount,
                'description' => $i->description,
                'category'    => $i->category?->name ?? '-',
                'category_id' => $i->category_id,
                'date'        => $i->date->format('Y-m-d'),
                'status'      => $i->status,
                'created_at'  => $i->created_at,
            ]);
        }

        foreach ($expenses as $e) {
            $mapped->push([
                'id'          => 'exp-' . $e->id,
                'type'        => 'expense',
                'amount'      => (float) $e->amount,
                'description' => $e->description,
                'category'    => $e->category?->name ?? '-',
                'category_id' => $e->category_id,
                'date'        => $e->date->format('Y-m-d'),
                'status'      => $e->status,
                'created_at'  => $e->created_at,
            ]);
        }

        $sorted = $mapped->sortByDesc('date')->values();

        return response()->json([
            'success' => true,
            'data'    => $sorted,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'type'        => 'required|in:income,expense',
            'amount'      => 'required|numeric|min:1',
            'description' => 'required|string|max:500',
            'category_id' => 'required|exists:categories,id',
            'date'        => 'nullable|date',
        ]);

        $user = $request->user();

        if ($validated['type'] === 'income') {
            $transaction = Income::create([
                'user_id'     => $user->id,
                'category_id' => $validated['category_id'],
                'amount'      => $validated['amount'],
                'description' => $validated['description'],
                'date'        => $validated['date'] ?? now(),
                'status'      => 'approved',
            ]);
        } else {
            $status = $user->parent_id ? 'pending' : 'approved';

            $transaction = Expense::create([
                'user_id'     => $user->id,
                'category_id' => $validated['category_id'],
                'amount'      => $validated['amount'],
                'description' => $validated['description'],
                'date'        => $validated['date'] ?? now(),
                'status'      => $status,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => $validated['type'] === 'income'
                ? 'Pemasukan berhasil dicatat'
                : ($status === 'pending'
                    ? 'Pengeluaran diajukan, menunggu persetujuan'
                    : 'Pengeluaran berhasil dicatat'),
            'data'    => $transaction->load('category'),
        ], 201);
    }

    public function show(Request $request, string $id): JsonResponse
    {
        $userId = $request->user()->id;

        if (str_starts_with($id, 'inc-')) {
            $model = Income::where('user_id', $userId)->with('category')->findOrFail(substr($id, 4));
            $type  = 'income';
        } elseif (str_starts_with($id, 'exp-')) {
            $model = Expense::where('user_id', $userId)->with('category')->findOrFail(substr($id, 4));
            $type  = 'expense';
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Format ID tidak valid',
            ], 400);
        }

        return response()->json([
            'success' => true,
            'data'    => array_merge($model->toArray(), ['type' => $type]),
        ]);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $user = $request->user();

        if (str_starts_with($id, 'inc-')) {
            $modelId    = substr($id, 4);
            $model      = Income::where('user_id', $user->id)->findOrFail($modelId);
            $modelClass = Income::class;
        } elseif (str_starts_with($id, 'exp-')) {
            $modelId    = substr($id, 4);
            $model      = Expense::where('user_id', $user->id)->findOrFail($modelId);
            $modelClass = Expense::class;
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Format ID tidak valid',
            ], 400);
        }

        if ($model->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Hanya transaksi dengan status pending yang bisa diubah',
            ], 422);
        }

        $validated = $request->validate([
            'amount'      => 'sometimes|numeric|min:1',
            'description' => 'sometimes|string|max:500',
            'category_id' => 'sometimes|exists:categories,id',
            'date'        => 'sometimes|date',
        ]);

        // Jika user adalah Parent, bisa approve/reject
        if ($request->status && $user->hasRole('parent') && $modelClass === Expense::class) {
            $validated['status'] = $request->status === 'approved' ? 'approved' : 'rejected';
            $validated['approved_by'] = $user->id;
        }

        $model->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Transaksi berhasil diperbarui',
            'data'    => $model->load('category'),
        ]);
    }

    public function destroy(Request $request, string $id): JsonResponse
    {
        $userId = $request->user()->id;

        if (str_starts_with($id, 'inc-')) {
            $model = Income::where('user_id', $userId)->findOrFail(substr($id, 4));
        } elseif (str_starts_with($id, 'exp-')) {
            $model = Expense::where('user_id', $userId)->findOrFail(substr($id, 4));
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Format ID tidak valid',
            ], 400);
        }

        if ($model->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Hanya transaksi pending yang bisa dihapus',
            ], 422);
        }

        $model->delete();

        return response()->json([
            'success' => true,
            'message' => 'Transaksi berhasil dihapus',
        ]);
    }
}
