<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Saving;
use App\Models\SavingDeposit;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SavingController extends Controller
{
    /**
     * Menampilkan semua tabungan milik user yang sedang login.
     */
    public function index(Request $request): JsonResponse
    {
        $savings = Saving::where('user_id', $request->user()->id)
            ->latest()
            ->get()
            ->map(fn (Saving $saving) => $this->formatSaving($saving));

        return response()->json([
            'success' => true,
            'data' => $savings,
        ]);
    }

    /**
     * Membuat tabungan baru.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'nullable|string|max:255',
            'target_amount' => 'required|numeric|min:1',
            'target_date' => 'nullable|date',
            'description' => 'nullable|string',
        ]);

        $saving = Saving::create([
            'user_id' => $request->user()->id,
            'name' => $validated['name'],
            'category' => $validated['category'] ?? null,
            'target_amount' => $validated['target_amount'],
            'current_amount' => 0,
            'target_date' => $validated['target_date'] ?? null,
            'description' => $validated['description'] ?? null,
            'status' => 'active',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Tabungan berhasil dibuat',
            'data' => $this->formatSaving($saving),
        ], 201);
    }

    /**
     * Menampilkan detail tabungan milik user.
     */
    public function show(Request $request, int $id): JsonResponse
    {
        $saving = Saving::where('user_id', $request->user()->id)
            ->with([
                'deposits' => fn ($query) => $query->latest('date')->latest('id'),
            ])
            ->find($id);

        if (!$saving) {
            return response()->json([
                'success' => false,
                'message' => 'Tabungan tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                ...$this->formatSaving($saving),
                'deposits' => $saving->deposits->map(
                    fn (SavingDeposit $deposit) => [
                        'id' => $deposit->id,
                        'amount' => (float) $deposit->amount,
                        'date' => $deposit->date?->format('Y-m-d'),
                        'note' => $deposit->note,
                        'created_at' => $deposit->created_at,
                    ]
                )->values(),
            ],
        ]);
    }

    /**
     * Mengubah data tabungan.
     */
    public function update(
        Request $request,
        int $id
    ): JsonResponse {
        $saving = Saving::where('user_id', $request->user()->id)
            ->find($id);

        if (!$saving) {
            return response()->json([
                'success' => false,
                'message' => 'Tabungan tidak ditemukan',
            ], 404);
        }

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'category' => 'nullable|string|max:255',
            'target_amount' => 'sometimes|required|numeric|min:1',
            'target_date' => 'nullable|date',
            'description' => 'nullable|string',
            'status' => 'sometimes|in:active,completed,cancelled',
        ]);

        $saving->update($validated);

        // Pastikan status completed jika target sudah tercapai.
        if (
            (float) $saving->current_amount >=
            (float) $saving->target_amount
        ) {
            $saving->update([
                'status' => 'completed',
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Tabungan berhasil diperbarui',
            'data' => $this->formatSaving($saving->fresh()),
        ]);
    }

    /**
     * Menghapus tabungan.
     */
    public function destroy(
        Request $request,
        int $id
    ): JsonResponse {
        $saving = Saving::where('user_id', $request->user()->id)
            ->find($id);

        if (!$saving) {
            return response()->json([
                'success' => false,
                'message' => 'Tabungan tidak ditemukan',
            ], 404);
        }

        $saving->delete();

        return response()->json([
            'success' => true,
            'message' => 'Tabungan berhasil dihapus',
        ]);
    }

    /**
     * Menampilkan riwayat setoran.
     */
    public function deposits(
        Request $request,
        int $id
    ): JsonResponse {
        $saving = Saving::where('user_id', $request->user()->id)
            ->find($id);

        if (!$saving) {
            return response()->json([
                'success' => false,
                'message' => 'Tabungan tidak ditemukan',
            ], 404);
        }

        $deposits = $saving->deposits()
            ->latest('date')
            ->latest('id')
            ->get()
            ->map(
                fn (SavingDeposit $deposit) => [
                    'id' => $deposit->id,
                    'amount' => (float) $deposit->amount,
                    'date' => $deposit->date?->format('Y-m-d'),
                    'note' => $deposit->note,
                    'created_at' => $deposit->created_at,
                ]
            );

        return response()->json([
            'success' => true,
            'data' => $deposits,
        ]);
    }

    /**
     * Menambahkan setoran ke tabungan.
     */
    public function deposit(
        Request $request,
        int $id
    ): JsonResponse {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:1',
            'date' => 'required|date',
            'note' => 'nullable|string',
        ]);

        $saving = Saving::where('user_id', $request->user()->id)
            ->find($id);

        if (!$saving) {
            return response()->json([
                'success' => false,
                'message' => 'Tabungan tidak ditemukan',
            ], 404);
        }

        if ($saving->status !== 'active') {
            return response()->json([
                'success' => false,
                'message' => 'Tabungan sudah tidak aktif',
            ], 422);
        }

        $deposit = DB::transaction(function () use (
            $request,
            $validated,
            $saving
        ) {
            $deposit = SavingDeposit::create([
                'saving_id' => $saving->id,
                'user_id' => $request->user()->id,
                'amount' => $validated['amount'],
                'date' => $validated['date'],
                'note' => $validated['note'] ?? null,
            ]);

            $saving->increment(
                'current_amount',
                $validated['amount']
            );

            $saving->refresh();

            if (
                (float) $saving->current_amount >=
                (float) $saving->target_amount
            ) {
                $saving->update([
                    'status' => 'completed',
                ]);
            }

            return $deposit;
        });

        return response()->json([
            'success' => true,
            'message' => 'Setoran berhasil ditambahkan',
            'data' => [
                'deposit' => [
                    'id' => $deposit->id,
                    'amount' => (float) $deposit->amount,
                    'date' => $deposit->date?->format('Y-m-d'),
                    'note' => $deposit->note,
                    'created_at' => $deposit->created_at,
                ],
                'saving' => $this->formatSaving($saving->fresh()),
            ],
        ], 201);
    }

    /**
     * Format response tabungan.
     */
    private function formatSaving(Saving $saving): array
    {
        return [
            'id' => $saving->id,
            'name' => $saving->name,
            'category' => $saving->category,
            'target_amount' => (float) $saving->target_amount,
            'current_amount' => (float) $saving->current_amount,
            'remaining_amount' => $saving->getRemainingAmount(),
            'progress' => $saving->getProgressPercentage(),
            'target_date' => $saving->target_date?->format('Y-m-d'),
            'description' => $saving->description,
            'status' => $saving->status,
            'created_at' => $saving->created_at,
            'updated_at' => $saving->updated_at,
        ];
    }
}
