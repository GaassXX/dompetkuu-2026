<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Debt;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class DebtController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Debt::where('user_id', $request->user()->id);

        if ($request->type) {
            $request->validate(['type' => 'in:debt,receivable']);
            $query->where('type', $request->type);
        }

        if ($request->status) {
            $request->validate(['status' => 'in:unpaid,paid']);
            $query->where('status', $request->status);
        }

        if ($request->date_from) {
            $query->where('date', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->where('date', '<=', $request->date_to);
        }

        $debts = $query->orderByRaw("CASE WHEN status = 'unpaid' THEN 0 ELSE 1 END")
            ->orderBy('due_date')
            ->orderByDesc('date')
            ->get();

        $summary = [
            'total_debt_unpaid' => (float) Debt::where('user_id', $request->user()->id)
                ->where('type', Debt::TYPE_DEBT)
                ->where('status', Debt::STATUS_UNPAID)
                ->sum('amount'),
            'total_receivable_unpaid' => (float) Debt::where('user_id', $request->user()->id)
                ->where('type', Debt::TYPE_RECEIVABLE)
                ->where('status', Debt::STATUS_UNPAID)
                ->sum('amount'),
        ];

        return response()->json([
            'success' => true,
            'data' => $debts,
            'summary' => $summary,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'type' => 'required|in:debt,receivable',
            'person_name' => 'required|string|max:255',
            'amount' => 'required|numeric|min:1',
            'date' => 'nullable|date',
            'due_date' => 'nullable|date|after_or_equal:date',
            'description' => 'nullable|string|max:500',
        ]);

        $debt = Debt::create([
            'user_id' => $request->user()->id,
            'type' => $validated['type'],
            'person_name' => $validated['person_name'],
            'amount' => $validated['amount'],
            'date' => $validated['date'] ?? now()->toDateString(),
            'due_date' => $validated['due_date'] ?? null,
            'description' => $validated['description'] ?? null,
            'status' => Debt::STATUS_UNPAID,
            'paid_at' => null,
        ]);

        return response()->json([
            'success' => true,
            'message' => $validated['type'] === 'debt'
                ? 'Utang berhasil dicatat'
                : 'Piutang berhasil dicatat',
            'data' => $debt,
        ], 201);
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $debt = Debt::where('user_id', $request->user()->id)->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $debt,
        ]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $debt = Debt::where('user_id', $request->user()->id)->findOrFail($id);

        if ($debt->isPaid()) {
            return response()->json([
                'success' => false,
                'message' => 'Data yang sudah lunas tidak bisa diubah',
            ], 422);
        }

        $validated = $request->validate([
            'type' => 'sometimes|in:debt,receivable',
            'person_name' => 'sometimes|string|max:255',
            'amount' => 'sometimes|numeric|min:1',
            'date' => 'sometimes|date',
            'due_date' => 'nullable|date',
            'description' => 'nullable|string|max:500',
        ]);

        $debt->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Data berhasil diperbarui',
            'data' => $debt->refresh(),
        ]);
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $debt = Debt::where('user_id', $request->user()->id)->findOrFail($id);

        if ($debt->isPaid()) {
            return response()->json([
                'success' => false,
                'message' => 'Data yang sudah lunas tidak bisa dihapus',
            ], 422);
        }

        $debt->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data berhasil dihapus',
        ]);
    }

    /**
     * Lunasi utang/piutang. Otomatis mencatat transaksi
     * (Expense untuk utang, Income untuk piutang).
     */
    public function pay(Request $request, int $id): JsonResponse
    {
        $debt = Debt::where('user_id', $request->user()->id)->findOrFail($id);

        try {
            $debt->markAsPaid();
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data ini sudah lunas',
            ], 422);
        }

        $isDebt = $debt->type === Debt::TYPE_DEBT;
        $pendingExpense = $isDebt
            && $debt->expenses()->where('status', 'pending')->exists();

        return response()->json([
            'success' => true,
            'message' => $isDebt
                ? ($pendingExpense
                    ? 'Pembayaran utang diajukan, menunggu persetujuan'
                    : 'Utang berhasil dilunasi dan tercatat sebagai pengeluaran')
                : 'Piutang berhasil dilunasi dan tercatat sebagai pemasukan',
            'data' => $debt,
        ]);
    }
}
