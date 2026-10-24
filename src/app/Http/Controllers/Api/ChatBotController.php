<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\TransactionParserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ChatBotController extends Controller
{
    public function parseAndSave(Request $request, TransactionParserService $parser): JsonResponse
    {
        $request->validate([
            'message' => 'required|string|max:500',
        ]);

        $text = trim($request->input('message'));
        $userId = $request->user()->id;

        $parsed = $parser->parse($text, $userId);

        if (! $parsed) {
            return response()->json([
                'success' => false,
                'message' => 'Format tidak dikenali. Contoh: "Makan 15.000 bakso" atau "Gaji 2.500.000 bulanan"',
            ], 422);
        }

        $transaction = $parser->save($parsed, $userId);
        $transaction->load('category');

        $typeLabel = $parsed['type'] === 'income' ? 'Pemasukan' : 'Pengeluaran';
        $formattedAmount = 'Rp' . number_format($parsed['amount'], 0, ',', '.');
        $categoryName = $transaction->category?->name ?? 'Lainnya';

        return response()->json([
            'success' => true,
            'message' => "Siap! {$typeLabel} {$formattedAmount} berhasil dicatat ({$categoryName}).",
            'data' => [
                'id' => $transaction->id,
                'type' => $parsed['type'],
                'amount' => $parsed['amount'],
                'formatted_amount' => $formattedAmount,
                'category' => $categoryName,
                'description' => $transaction->description,
                'date' => $transaction->date?->format('Y-m-d') ?? now()->toDateString(),
            ],
        ]);
    }
}
