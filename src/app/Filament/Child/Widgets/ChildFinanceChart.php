<?php

namespace App\Filament\Child\Widgets;

use App\Models\Expense;
use App\Models\Income;
use Filament\Widgets\ChartWidget;

class ChildFinanceChart extends ChartWidget
{
    protected static ?int    $sort            = 2;
    protected static ?string $maxHeight       = '300px';

    protected int|string|array $columnSpan = [
        'default' => 1,
        'md'      => 2,
        'lg'      => 4,
    ];
    protected static ?string $pollingInterval = null;
    protected static ?string $heading         = 'Arus Keuangan';

    public ?string $filter = '3'; // ✅ pakai $filter bawaan Filament, default 3 bulan

    protected function getFilters(): ?array
    {
        return [
            '1'  => '1 Bulan',
            '3'  => '3 Bulan',
            '6'  => '6 Bulan',
            '12' => '1 Tahun',
        ];
    }

    protected function getData(): array
    {
        $userId   = auth()->id();
        $duration = (int) ($this->filter ?? '3'); // ✅ baca $this->filter
        $months   = collect(range($duration - 1, 0))->map(fn($i) => now()->startOfMonth()->subMonths($i));
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
                    'borderColor'     => 'rgb(34, 197, 94)',
                    'backgroundColor' => 'rgba(34, 197, 94, 0.1)',
                    'fill'            => true,
                    'tension'         => 0.4,
                    'pointRadius'     => 4,
                    'pointBackgroundColor' => 'rgb(34, 197, 94)',
                    'borderWidth'     => 2,
                ],
                [
                    'label'           => 'Pengeluaran',
                    'data'            => $expenses,
                    'borderColor'     => 'rgb(239, 68, 68)',
                    'backgroundColor' => 'rgba(239, 68, 68, 0.1)',
                    'fill'            => true,
                    'tension'         => 0.4,
                    'pointRadius'     => 4,
                    'pointBackgroundColor' => 'rgb(239, 68, 68)',
                    'borderWidth'     => 2,
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
        return 'line';
    }
}
