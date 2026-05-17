<?php

namespace App\Filament\Parent\Widgets;

use App\Models\Expense;
use App\Models\Income;
use App\Models\User;
use Filament\Widgets\Widget;

class ChildSummaryWidget extends Widget
{
    protected static ?int $sort   = 3;
    protected static string $view = 'filament.parent.widgets.child-summary-widget';

    public static function canView(): bool
    {
        return User::where('parent_id', auth()->id())->exists();
    }

    public array $children = [];

    public function mount(): void
    {
        $this->children = User::where('parent_id', auth()->id())
            ->get()
            ->map(function ($child) {
                $income = (float) Income::where('user_id', $child->id)
                    ->whereMonth('date', now()->month)
                    ->whereYear('date', now()->year)
                    ->where('status', 'approved')
                    ->sum('amount');

                $expense = (float) Expense::where('user_id', $child->id)
                    ->whereMonth('date', now()->month)
                    ->whereYear('date', now()->year)
                    ->where('status', 'approved')
                    ->sum('amount');

                $pending = Income::where('user_id', $child->id)->where('status', 'pending')->count()
                         + Expense::where('user_id', $child->id)->where('status', 'pending')->count();

                return [
                    'name'    => $child->name,
                    'initial' => strtoupper(substr($child->name, 0, 2)),
                    'income'  => $income,
                    'expense' => $expense,
                    'saldo'   => $income - $expense,
                    'pending' => $pending,
                ];
            })->toArray();
    }
}
