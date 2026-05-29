<?php

namespace App\Filament\Child\Widgets;

use App\Models\Expense;
use App\Models\Income;
use Filament\Widgets\ChartWidget;

class ChildFinanceChart extends ChartWidget
{
    protected static ?int    $sort            = 2;
    protected static ?string $maxHeight       = '300px';
    protected static ?string $pollingInterval = null;
    protected static ?string $heading         = 'Arus Keuangan';

    public ?string $filter = '3'; // ✅ pakai $filter bawaan Filament, default 3 bulan

    protected function getFilters(): ?array
    {
        return [
            '1'  => '1 Bulan',
            '3'  => '3 Bulan',
            '6'  => '6 Bulan',
            '12' => '12 Bulan',
        ];
    }

    protected function getData(): array
    {
        $userId   = auth()->id();
        $duration = (int) ($this->filter ?? '3'); // ✅ baca $this->filter
        $months   = collect(range($duration - 1, 0))->map(fn($i) => now()->subMonths($i));
        $labels   = $months->map(fn($m) => $m->translatedFormat('M Y'))->toArray();

        $incomes = $months->map(fn($m) =>
            (float) Income::where('user_id', $userId)
                ->whereMonth('date', $m->month)
                ->whereYear('date', $m->year)
                ->where('status', 'approved')
                ->sum('amount')
        )->toArray();

        $expenses = $months->map(fn($m) =>
            (float) Expense::where('user_id', $userId)
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

    protected function getType(): string
    {
        return 'bar';
    }
}
