<?php

namespace App\Livewire\Mobile;

use App\Models\Budget;
use App\Models\Category;
use App\Models\Expense;
use App\Models\User;
use App\Services\BudgetAlertService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Anggaran extends Component
{
    public string $periode = '';
    public int $bulan = 0;
    public int $tahun = 0;
    public string $scope = 'family';

    public bool $showForm = false;
    public ?int $editBudgetId = null;
    public ?int $formCategoryId = null;
    public string $formLimit = '';
    public string $formPeriod = 'monthly';

    private int $currentUserId = 0;

    public function mount(): void
    {
        $this->currentUserId = (int) (Auth::id() ?? 0);
        $this->setPeriode(now()->format('Y-m'));
    }

    private function getCurrentUserId(): int
    {
        return $this->currentUserId ?: (int) (Auth::id() ?? 0);
    }

    public function isParent(): bool
    {
        return (bool) Auth::user()?->hasRole('parent');
    }

    public function updated(string $propertyName): void
    {
        if ($propertyName === 'periode') {
            $this->parsePeriode();
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

    public function getPeriodeOptionsProperty(): array
    {
        $start = now()->subMonths(9)->startOfMonth();
        $end = now()->startOfMonth();
        $options = [];
        for ($d = $start->copy(); $d->lte($end); $d->addMonth()) {
            $options[$d->format('Y-m')] = $d->translatedFormat('F Y');
        }
        return $options;
    }

    public function getUserIds(): array
    {
        if (!$this->isParent()) {
            return [$this->getCurrentUserId()];
        }

        $parentId = $this->getCurrentUserId();

        return match (true) {
            $this->scope === 'family' => array_merge(
                [$parentId],
                User::where('parent_id', $parentId)->pluck('id')->toArray()
            ),
            $this->scope === 'me' => [$parentId],
            str_starts_with($this->scope, 'child_') => [(int) str_replace('child_', '', $this->scope)],
            default => [$parentId],
        };
    }

    public function getScopeOptionsProperty(): array
    {
        $options = [
            'family' => 'Semua Keluarga',
            'me' => 'Saya Sendiri',
        ];

        foreach (User::where('parent_id', $this->getCurrentUserId())->orderBy('name')->get() as $child) {
            $options['child_' . $child->id] = $child->name;
        }

        return $options;
    }

    public function getBudgetCards(): array
    {
        $userIds = $this->getUserIds();

        $budgets = Budget::whereIn('user_id', $userIds)
            ->with(['category', 'user'])
            ->orderByDesc('created_at')
            ->get();

        return $budgets->map(function (Budget $budget) {
            $start = Carbon::create($this->tahun, $this->bulan, 1)->startOfMonth();
            $end = $start->copy()->endOfMonth();

            $spent = (float) Expense::where('user_id', $budget->user_id)
                ->where('category_id', $budget->category_id)
                ->where('status', 'approved')
                ->whereBetween('date', [$start, $end])
                ->sum('amount');

            $limit = (float) $budget->limit_amount;
            $remaining = max(0, $limit - $spent);
            $percentage = $limit > 0 ? min(100, round(($spent / $limit) * 100)) : 0;

            return [
                'id' => $budget->id,
                'user_id' => $budget->user_id,
                'user_name' => $budget->user?->name ?? '-',
                'category' => $budget->category?->name ?? '-',
                'period' => $budget->period === 'weekly' ? 'Mingguan' : 'Bulanan',
                'limit' => $limit,
                'spent' => $spent,
                'remaining' => $remaining,
                'percentage' => $percentage,
                'is_over' => $spent > $limit,
            ];
        })->toArray();
    }

    public function getOverviewStats(): array
    {
        $cards = collect($this->getBudgetCards());

        return [
            'total_budget' => (float) $cards->sum('limit'),
            'spent' => (float) $cards->sum('spent'),
            'remaining' => max(0, (float) $cards->sum('limit') - (float) $cards->sum('spent')),
        ];
    }

    public function getChartData(): array
    {
        $cards = collect($this->getBudgetCards());

        $grouped = $cards->groupBy('category')->map(function ($items) {
            return [
                'budget' => (float) $items->sum('limit'),
                'spent' => (float) $items->sum('spent'),
            ];
        });

        return [
            'labels' => $grouped->keys()->toArray(),
            'budget' => $grouped->pluck('budget')->toArray(),
            'spent' => $grouped->pluck('spent')->toArray(),
        ];
    }

    public function getAlerts(): array
    {
        $alerts = [];
        foreach ($this->getUserIds() as $uid) {
            $alerts = array_merge($alerts, BudgetAlertService::getAlerts($uid, 80));
        }
        return $alerts;
    }

    public function getUserBudgetIds(): array
    {
        if ($this->isParent() && $this->scope === 'family') {
            return array_merge(
                [$this->getCurrentUserId()],
                User::where('parent_id', $this->getCurrentUserId())->pluck('id')->toArray()
            );
        }
        return $this->getUserIds();
    }

    public function canManageBudget(int $budgetUserId): bool
    {
        $user = Auth::user();
        if ($user->hasRole('parent')) return true;
        if ($user->is_independent) return $user->id === $budgetUserId;
        return false;
    }

    public function openAddForm(): void
    {
        $this->resetForm();
        $this->editBudgetId = null;
        $this->showForm = true;
    }

    public function openEditForm(int $id): void
    {
        $budget = Budget::findOrFail($id);
        if (!$this->canManageBudget($budget->user_id)) return;

        $this->editBudgetId = $budget->id;
        $this->formCategoryId = $budget->category_id;
        $this->formLimit = (string) (int) $budget->limit_amount;
        $this->formPeriod = $budget->period;
        $this->showForm = true;
    }

    public function saveBudget(): void
    {
        $this->validate([
            'formCategoryId' => 'required|exists:categories,id',
            'formLimit' => 'required|numeric|min:1',
            'formPeriod' => 'required|in:monthly,weekly',
        ]);

        $data = [
            'category_id' => $this->formCategoryId,
            'limit_amount' => (float) str_replace('.', '', $this->formLimit),
            'period' => $this->formPeriod,
        ];

        if ($this->editBudgetId) {
            $budget = Budget::findOrFail($this->editBudgetId);
            if (!$this->canManageBudget($budget->user_id)) return;
            $budget->update($data);
        } else {
            $data['user_id'] = $this->getCurrentUserId();
            Budget::create($data);
        }

        $this->showForm = false;
        $this->resetForm();
    }

    public function deleteBudget(int $id): void
    {
        $budget = Budget::findOrFail($id);
        if (!$this->canManageBudget($budget->user_id)) return;
        $budget->delete();
    }

    public function closeForm(): void
    {
        $this->showForm = false;
        $this->resetForm();
    }

    private function resetForm(): void
    {
        $this->formCategoryId = null;
        $this->formLimit = '';
        $this->formPeriod = 'monthly';
        $this->editBudgetId = null;
    }

    public function render()
    {
        $expenseCategories = Category::where('type', 'expense')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('livewire.mobile.anggaran', [
            'expenseCategories' => $expenseCategories,
        ])->layout('layouts.mobile');
    }
}
