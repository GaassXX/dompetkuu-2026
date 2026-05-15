<?php

namespace App\Filament\Parent\Widgets;

use App\Models\Expense;
use App\Models\Income;
use App\Models\User;
use Filament\Widgets\ChartWidget;

class FamilyFinanceChart extends ChartWidget
{
    protected static ?string $heading = 'Arus Keuangan Keluarga (6 Bulan Terakhir)';
    protected static ?int $sort = 2;
    protected static ?string $maxHeight = '300px';
    protected static ?string $pollingInterval = null;

    public ?string $filter = 'family';

    protected function getFilters(): ?array
    {
        $filters = [
            'family' => 'Seluruh Keluarga',
            'me'     => 'Saya Sendiri',
        ];

        $children = User::where('parent_id', auth()->id())->get();
        foreach ($children as $child) {
            $filters['child_' . $child->id] = $child->name;
        }

        return $filters;
    }

    protected function getData(): array
    {
        return [
            'datasets' => [
                [
                    'label'           => 'Pemasukan',
                    'data'            => [100000, 200000, 150000, 300000, 250000, 800000],
                    'backgroundColor' => 'rgba(34, 197, 94, 0.8)',
                    'borderColor'     => 'rgb(34, 197, 94)',
                    'borderWidth'     => 0,
                    'borderRadius'    => 6,
                ],
                [
                    'label'           => 'Pengeluaran',
                    'data'            => [50000, 100000, 200000, 150000, 100000, 600000],
                    'backgroundColor' => 'rgba(239, 68, 68, 0.8)',
                    'borderColor'     => 'rgb(239, 68, 68)',
                    'borderWidth'     => 0,
                    'borderRadius'    => 6,
                ],
            ],
            'labels' => ['Dec 2025', 'Jan 2026', 'Feb 2026', 'Mar 2026', 'Apr 2026', 'May 2026'],
        ];
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                ],
            ],
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
