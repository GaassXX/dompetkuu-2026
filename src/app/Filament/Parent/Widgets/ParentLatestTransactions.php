<?php

namespace App\Filament\Parent\Widgets;

use App\Models\Expense;
use App\Models\Income;
use Filament\Widgets\Widget;

class ParentLatestTransactions extends Widget
{
    protected static ?int $sort = 5;
    protected static ?string $maxHeight = '300px';
    protected static string $view = 'filament.parent.widgets.parent-latest-transactions';

    public function getColumnSpan(): int | string | array
    {
        return 5;
    }

    public array $transactions = [];

    public function mount(): void
    {
        $userId = auth()->id();

        // 1. Ambil data dengan 'latest()' yang benar agar database mengurutkan dari yang terbaru
        $incomes = Income::where('user_id', $userId)
            ->with('category')
            ->latest() // Menggunakan created_at secara default jika date kosong
            ->take(10) // Ambil lebih banyak sebelum di-merge agar tidak terbuang
            ->get()
            ->map(fn($item) => [
                'type'     => 'Pemasukan',
                'category' => $item->category->name ?? '-',
                'amount'   => (float) $item->amount,
                'date'     => $item->date, // Simpan objek Carbon/string untuk perbandingan
                'created_at' => $item->created_at, // Tambahkan created_at untuk sorting presisi
                'date_fmt' => $item->date?->format('d M Y') ?? '-',
                'status'   => $item->status,
            ]);

        $expenses = Expense::where('user_id', $userId)
            ->with('category')
            ->latest()
            ->take(10)
            ->get()
            ->map(fn($item) => [
                'type'     => 'Pengeluaran',
                'category' => $item->category->name ?? '-',
                'amount'   => (float) $item->amount,
                'date'     => $item->date,
                'created_at' => $item->created_at,
                'date_fmt' => $item->date?->format('d M Y') ?? '-',
                'status'   => $item->status,
            ]);

        // 2. Gabungkan koleksi
        $merged = $incomes->concat($expenses);

        // 3. Sortir berdasarkan tanggal, jika tanggal sama, sortir berdasarkan waktu input (created_at)
        $sorted = $merged->sort(function ($a, $b) {
            $dateCompare = $b['date'] <=> $a['date'];
            if ($dateCompare === 0) {
                return $b['created_at'] <=> $a['created_at'];
            }
            return $dateCompare;
        });

        // 4. Ambil 5 data paling atas setelah diurutkan
        $this->transactions = $sorted->take(5)->toArray();
    }
}
