<?php

namespace App\Filament\Parent\Widgets;

use App\Models\Expense;
use App\Models\Income;
use App\Models\User;
use Filament\Widgets\ChartWidget;

class FamilyFinanceChart extends ChartWidget
{
    protected static string $view = 'filament.parent.widgets.family-finance-chart';
    protected static ?string $heading         = ' '; // ✅ Kosongkan heading default
    protected static ?int    $sort            = 2;
    protected static ?string $maxHeight       = '300px';
    protected static ?string $pollingInterval = null;

    public ?string $filter         = 'family'; // anggota
    public string  $durationFilter = '6';      // durasi

    // ✅ Tidak pakai getFilters() bawaan — kita handle sendiri di heading
    protected function getFilters(): ?array
    {
        return null;
    }

    // ✅ Heading custom dengan 2 select terpisah
    public function getHeading(): string
    {
        return 'Arus Keuangan Keluarga';
    }

    protected function getExtraAttributes(): array
    {
        return ['wire:key' => 'family-finance-chart-' . $this->filter . '-' . $this->durationFilter];
    }

    public function updatedFilter(): void {}
    public function updatedDurationFilter(): void {}

    protected function getData(): array
    {
        $duration = (int) $this->durationFilter;
        $months   = collect(range($duration - 1, 0))->map(fn($i) => now()->subMonths($i));
        $labels   = $months->map(fn($m) => $m->translatedFormat('M Y'))->toArray();
        $userIds  = $this->getUserIds();

        $incomes = $months->map(fn($m) =>
            (float) Income::whereIn('user_id', $userIds)
                ->whereMonth('date', $m->month)
                ->whereYear('date', $m->year)
                ->where('status', 'approved')
                ->sum('amount')
        )->toArray();

        $expenses = $months->map(fn($m) =>
            (float) Expense::whereIn('user_id', $userIds)
                ->whereMonth('date', $m->month)
                ->whereYear('date', $m->year)
                ->where('status', 'approved')
                ->sum('amount')
        )->toArray();

        return [
            'datasets' => [
                [
                    'label'           => 'Pemasukan',
                    'data'            => $incomes,
                    'backgroundColor' => 'rgba(34, 197, 94, 0.8)',
                    'borderColor'     => 'rgb(34, 197, 94)',
                    'borderWidth'     => 0,
                    'borderRadius'    => 6,
                ],
                [
                    'label'           => 'Pengeluaran',
                    'data'            => $expenses,
                    'backgroundColor' => 'rgba(239, 68, 68, 0.8)',
                    'borderColor'     => 'rgb(239, 68, 68)',
                    'borderWidth'     => 0,
                    'borderRadius'    => 6,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => ['legend' => ['display' => true]],
            'scales'  => ['y' => ['beginAtZero' => true]],
        ];
    }

    private function getUserIds(): array
    {
        $parentId = auth()->id();
        $childIds = User::where('parent_id', $parentId)->pluck('id')->toArray();

        return match(true) {
            $this->filter === 'family'                => array_merge([$parentId], $childIds),
            $this->filter === 'me'                    => [$parentId],
            str_starts_with($this->filter, 'child_') => [(int) str_replace('child_', '', $this->filter)],
            default                                   => array_merge([$parentId], $childIds),
        };
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
