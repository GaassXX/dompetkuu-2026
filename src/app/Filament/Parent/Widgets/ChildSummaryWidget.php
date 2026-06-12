<?php

namespace App\Filament\Parent\Widgets;

use Filament\Widgets\Widget;
use App\Models\Budget;
use App\Models\Expense;
use App\Models\Income;
use App\Models\User;
use Carbon\Carbon;

class ChildSummaryWidget extends Widget
{
    protected static ?int $sort = 4;
    protected static ?string $maxHeight = '300px'; // Selaraskan tinggi widget
    protected static string $view = 'filament.parent.widgets.child-summary-widget';

    public function getColumnSpan(): int | string | array
    {
        return 7;
    }

    public array $children = [];
    public array $topBudgets = [];

    public function mount(): void
    {
        $userId = auth()->id();
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        // ====================================================================
        // KODE FILTER AKURAT: HANYA AMBIL ANAK YANG BERELASI DENGAN ORANG TUA INI
        // ====================================================================
        $childUsers = User::where('parent_id', $userId)
            ->orderBy('name', 'asc')
            ->get();

        $this->children = $childUsers->map(function ($child) use ($startOfMonth, $endOfMonth) {
            // Hitung total transaksi pemasukan anak bulan ini yang disetujui (Approved)
            $incomeSum = Income::where('user_id', $child->id)
                ->whereBetween('date', [$startOfMonth, $endOfMonth])
                ->where('status', 'approved')
                ->sum('amount');

            // Hitung total pengeluaran anak bulan ini yang disetujui (Approved)
            $expenseSum = Expense::where('user_id', $child->id)
                ->whereBetween('date', [$startOfMonth, $endOfMonth])
                ->where('status', 'approved')
                ->sum('amount');

            // Hitung jumlah transaksi pending milik anak
            $pendingCount = Expense::where('user_id', $child->id)->where('status', 'pending')->count() +
                            Income::where('user_id', $child->id)->where('status', 'pending')->count();

            // FIX: Hitung saldo bersih secara realtime (Pemasukan - Pengeluaran) bulan berjalan
            $calculatedSaldo = (float) ($incomeSum - $expenseSum);

            // Membuat inisial nama anak (contoh: "Anak Account 1" -> "AA")
            $words = explode(' ', trim($child->name));
            $initial = 'AN';
            if (count($words) >= 2) {
                $initial = strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
            } elseif (isset($words[0])) {
                $initial = strtoupper(substr($words[0], 0, 2));
            }

            return [
                'initial' => $initial,
                'name'    => $child->name,
                'pending' => $pendingCount,
                'income'  => (float) $incomeSum,
                'expense' => (float) $expenseSum,
                'saldo'   => $calculatedSaldo, // Menggunakan kalkulasi realtime agar ter-update otomatis
            ];
        })->toArray();


        // ====================================================================
        // KODE PROGRESS ANGGARAN ANAK BULAN INI
        // ====================================================================
        // Mengambil batas anggaran berdasarkan user_id anak-anak yang terikat dengan orang tua ini
        $childIds = $childUsers->pluck('id')->toArray();

        $budgets = Budget::whereIn('user_id', $childIds)
            ->with(['category', 'user'])
            ->take(3)
            ->get();

        $this->topBudgets = $budgets->map(function ($budget) {
            $used = $budget->getSpentAmount();
            $percent = $budget->getUsagePercentage();

            return [
                'category_name' => $budget->category->name ?? 'Umum',
                'child_name'    => $budget->user->name ?? 'Anak',
                'used'          => (float) $used,
                'limit'         => (float) $budget->limit_amount,
                'percent'       => (float) $percent,
            ];
        })->toArray();
    }
}
