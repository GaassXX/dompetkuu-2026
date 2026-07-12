<?php

namespace App\Filament\Child\Pages;

use App\Models\Budget;
use App\Models\Expense;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

class BudgetView extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-wallet';
    protected static ?string $navigationLabel = 'Anggaran Saya';
    protected static ?string $title           = 'Anggaran Saya';
    protected static ?int    $navigationSort  = 4;
    protected static string  $view            = 'filament.child.pages.budget-view';

    public int $bulan = 0;
    public int $tahun = 0;
    public string $periode = '';

    public function mount(): void
    {
        $this->setPeriode(now()->format('Y-m'));
    }

    public function updated(string $propertyName): void
    {
        if ($propertyName === 'periode') {
            $this->parsePeriode();
        }
        if ($propertyName === 'periode') {
            $this->dispatch('child-budget-refresh');
        }
    }

    private function setPeriode(string $value): void
    {
        $this->periode = $value;
        $this->parsePeriode();
    }

    private function parsePeriode(): void
    {
        $parts = explode('-', $this->periode);
        $this->tahun = (int) ($parts[0] ?? now()->year);
        $this->bulan = (int) ($parts[1] ?? now()->month);
    }

    public function getAllBudgets(): array
    {
        $userId = Auth::id();
        $bulan = $this->bulan ?: now()->month;
        $tahun = $this->tahun ?: now()->year;
        $endOfMonth = Carbon::create($tahun, $bulan, 1)->endOfMonth();

        return Budget::where('user_id', $userId)
            ->where('created_at', '<=', $endOfMonth)
            ->with('category')
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($budget) use ($userId, $bulan, $tahun) {
                $start = Carbon::create($tahun, $bulan, 1)->startOfMonth();
                $end = $start->copy()->endOfMonth();

                $spent = Expense::where('user_id', $userId)
                    ->where('category_id', $budget->category_id)
                    ->where('status', 'approved')
                    ->whereBetween('date', [$start, $end])
                    ->sum('amount');

                $percentage = $budget->limit_amount > 0
                    ? min(100, round(($spent / $budget->limit_amount) * 100))
                    : 0;

                return [
                    'id'          => $budget->id,
                    'category'    => $budget->category?->name ?? '-',
                    'period'      => $budget->period === 'weekly' ? 'Mingguan' : 'Bulanan',
                    'limit'       => (float) $budget->limit_amount,
                    'spent'       => (float) $spent,
                    'remaining'   => max(0, (float) $budget->limit_amount - (float) $spent),
                    'percentage'  => $percentage,
                    'is_exceeded' => (float) $spent >= (float) $budget->limit_amount,
                ];
            })->toArray();
    }

    public function getOverviewStats(): array
    {
        $cards = collect($this->getAllBudgets());

        $totalBudget = (float) $cards->sum('limit');
        $totalSpent = (float) $cards->sum('spent');

        return [
            'total_budget' => $totalBudget,
            'spent'        => $totalSpent,
            'remaining'    => max(0, $totalBudget - $totalSpent),
        ];
    }

    public function getChartData(): array
    {
        $cards = collect($this->getAllBudgets());

        $grouped = $cards->groupBy('category')->map(function ($items, $category) {
            return [
                'category' => $category,
                'budget'   => (float) $items->sum('limit'),
                'spent'    => (float) $items->sum('spent'),
            ];
        })->values();

        return [
            'labels' => $grouped->pluck('category')->toArray(),
            'budget' => $grouped->pluck('budget')->toArray(),
            'spent'  => $grouped->pluck('spent')->toArray(),
        ];
    }

    public function deleteBudget(int $id): void
    {
        $budget = Budget::where('user_id', Auth::id())->findOrFail($id);
        $budget->delete();
        Notification::make()->success()->title('Anggaran berhasil dihapus')->send();
        $this->dispatch('child-budget-refresh');
    }

    public function canManageBudget(): bool
    {
        return (bool) Auth::user()?->is_independent;
    }
}
