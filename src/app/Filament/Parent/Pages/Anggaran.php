<?php

namespace App\Filament\Parent\Pages;

use App\Filament\Parent\Resources\BudgetResource;
use App\Models\Budget;
use App\Models\Expense;
use App\Models\User;
use Filament\Actions\Action as HeaderAction;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class Anggaran extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-wallet';
    protected static ?string $navigationLabel = 'Anggaran';
    protected static ?string $title           = 'Anggaran';
    protected static ?int    $navigationSort  = 4;
    protected static string  $view            = 'filament.parent.pages.anggaran';

    public int $bulan = 0;
    public int $tahun = 0;
    public string $periode = '';
    public string $scope = 'family';

    private int $currentUserId = 0;

    public function mount(): void
    {
        $this->currentUserId = (int) (Auth::id() ?? 0);
        $this->setPeriode(now()->format('Y-m'));
    }

    public function updated(string $propertyName): void
    {
        if ($propertyName === 'periode') {
            $this->parsePeriode();
        }
        if (in_array($propertyName, ['periode', 'scope'], true)) {
            $this->dispatch('anggaran-refresh');
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

    private function getCurrentUserId(): int
    {
        return $this->currentUserId ?: (int) (Auth::id() ?? 0);
    }

    public function getScopeOptions(): array
    {
        $options = [
            'family' => 'Semua Keluarga',
            'me'     => 'Saya Sendiri',
        ];

        foreach (User::where('parent_id', $this->getCurrentUserId())->orderBy('name')->get() as $child) {
            $options['child_' . $child->id] = $child->name;
        }

        return $options;
    }

    public function getUserIds(): array
    {
        $parentId = $this->getCurrentUserId();

        return match (true) {
            $this->scope === 'family' => array_merge(
                [$parentId],
                User::where('parent_id', $parentId)->pluck('id')->toArray()
            ),
            $this->scope === 'me'     => [$parentId],
            str_starts_with($this->scope, 'child_') => [(int) str_replace('child_', '', $this->scope)],
            default                   => [$parentId],
        };
    }

    public function getBudgetCards(): array
    {
        $endOfMonth = Carbon::create($this->tahun, $this->bulan, 1)->endOfMonth();

        $budgets = Budget::whereIn('user_id', $this->getUserIds())
            ->where('created_at', '<=', $endOfMonth)
            ->with(['category', 'user'])
            ->orderByDesc('created_at')
            ->get();

        return $budgets->map(function (Budget $budget) {
            $spent = $this->getSpentAmountForBudget($budget);
            $limit = (float) $budget->limit_amount;
            $remaining = max(0, $limit - $spent);
            $percentage = $limit > 0 ? min(100, round(($spent / $limit) * 100)) : 0;

            return [
                'id'         => $budget->id,
                'user_name'  => $budget->user?->name ?? '-',
                'category'   => $budget->category?->name ?? '-',
                'period'     => $budget->period === 'weekly' ? 'Mingguan' : 'Bulanan',
                'limit'      => $limit,
                'spent'      => $spent,
                'remaining'  => $remaining,
                'percentage' => $percentage,
                'is_over'    => $spent >= $limit,
            ];
        })->toArray();
    }

    public function getOverviewStats(): array
    {
        $cards = collect($this->getBudgetCards());

        $totalBudget = (float) $cards->sum('limit');
        $totalSpent = (float) $cards->sum('spent');
        $remaining = max(0, $totalBudget - $totalSpent);

        return [
            'total_budget' => $totalBudget,
            'spent'        => $totalSpent,
            'remaining'    => $remaining,
        ];
    }

    public function getChartData(): array
    {
        $cards = collect($this->getBudgetCards());

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
        $budget = Budget::whereIn('user_id', $this->getUserIds())->findOrFail($id);
        $budget->delete();
        Notification::make()->success()->title('Anggaran berhasil dihapus')->send();
        $this->dispatch('anggaran-refresh');
    }

    private function getSpentAmountForBudget(Budget $budget): float
    {
        $start = Carbon::create($this->tahun, $this->bulan, 1)->startOfMonth();
        $end = $start->copy()->endOfMonth();

        $query = Expense::where('user_id', $budget->user_id)
            ->where('category_id', $budget->category_id)
            ->where('status', 'approved')
            ->whereBetween('date', [$start, $end]);

        return (float) $query->sum('amount');
    }

    protected function getHeaderActions(): array
    {
        return [
            HeaderAction::make('new_budget')
                ->label('Anggaran Baru')
                ->icon('heroicon-o-plus')
                ->color('primary')
                ->url(BudgetResource::getUrl('create')),
        ];
    }
}
