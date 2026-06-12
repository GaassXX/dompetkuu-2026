<?php

namespace App\Filament\Child\Widgets;

use App\Models\Expense;
use App\Models\Income;
use Filament\Widgets\Widget;

class LatestTransactions extends Widget
{
    protected static ?int $sort = 3;
    protected static ?string $maxHeight = '300px';
    protected static string $view = 'filament.child.widgets.latest-transactions';

    // ✅ Expose sebagai property publik, bukan method Collection
    public array $transactions = [];

    public function mount(): void
{
    $userId = auth()->id();

    $incomes = Income::where('user_id', $userId)
        ->with('category')
        ->latest('created_at') // Ambil yang paling baru dibuat
        ->take(5)
        ->get()
        ->map(fn($item) => [
            'type'       => 'Pemasukan',
            'category'   => $item->category->name ?? '-',
            'amount'     => (float) $item->amount,
            'date'       => $item->date?->format('Y-m-d') ?? '',
            'created_at' => $item->created_at, // Tambahkan ini untuk sorting
            'status'     => $item->status,
        ])->toArray();

    $expenses = Expense::where('user_id', $userId)
        ->with('category')
        ->latest('created_at') // Ambil yang paling baru dibuat
        ->take(5)
        ->get()
        ->map(fn($item) => [
            'type'       => 'Pengeluaran',
            'category'   => $item->category->name ?? '-',
            'amount'     => (float) $item->amount,
            'date'       => $item->date?->format('Y-m-d') ?? '',
            'created_at' => $item->created_at, // Tambahkan ini untuk sorting
            'status'     => $item->status,
        ])->toArray();

    $merged = array_merge($incomes, $expenses);

    // Sort berdasarkan created_at secara DESC (terbaru paling atas)
    usort($merged, fn($a, $b) => $b['created_at'] <=> $a['created_at']);

    $this->transactions = array_slice($merged, 0, 5);
    }
}
