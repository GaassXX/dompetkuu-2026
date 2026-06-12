<?php

namespace App\Filament\Child\Resources\SavingResource\Pages;

use App\Filament\Child\Resources\SavingResource;
use App\Models\Saving;
use App\Models\SavingDeposit;
use Filament\Actions\Action;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Carbon;

class ListSavings extends Page
{
    protected static string $resource = SavingResource::class;
    protected static string $view     = 'filament.child.resources.saving-resource.pages.list-savings';

    public string $period = '30';

    public function getTitle(): string { return 'Tabungan'; }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('create')
                ->label('Tambah Tabungan')
                ->icon('heroicon-o-plus')
                ->color('warning')
                ->url(SavingResource::getUrl('create')),
        ];
    }

    public function getStats(): array
    {
        $userId    = auth()->id();
        $savingIds = Saving::where('user_id', $userId)->pluck('id');

        $activeSavings = Saving::where('user_id', $userId)->where('status', 'active')->get();
        $totalSaved    = $activeSavings->sum('current_amount');
        $totalTarget   = $activeSavings->sum('target_amount');
        $completed     = Saving::where('user_id', $userId)->where('status', 'completed')->count();
        $remaining     = max(0, $totalTarget - $totalSaved);

        $now       = Carbon::now();
        $lastMonth = $now->copy()->subMonth();

        $depositsThisMonth = SavingDeposit::whereIn('saving_id', $savingIds)
            ->whereMonth('date', $now->month)->whereYear('date', $now->year)->sum('amount');

        $depositsLastMonth = SavingDeposit::whereIn('saving_id', $savingIds)
            ->whereMonth('date', $lastMonth->month)->whereYear('date', $lastMonth->year)->sum('amount');

        $percentChange = 0;
        $isPositive    = true;
        if ($depositsLastMonth > 0) {
            $percentChange = round((($depositsThisMonth - $depositsLastMonth) / $depositsLastMonth) * 100, 1);
            $isPositive    = $percentChange >= 0;
        } elseif ($depositsThisMonth > 0) {
            $percentChange = 100;
        }

        return compact('activeSavings', 'totalSaved', 'totalTarget', 'completed', 'remaining', 'percentChange', 'isPositive');
    }

    public function getRecentDeposits()
    {
        $savingIds = Saving::where('user_id', auth()->id())->pluck('id');

        $query = SavingDeposit::whereIn('saving_id', $savingIds)
            ->with(['savingGoal'])
            ->orderByDesc('date');

        if ($this->period !== 'all') {
            $days = (int) $this->period;
            $query->where('date', '>=', Carbon::now()->subDays($days));
        }

        return $query->limit(50)->get();
    }

    public function setPeriod(string $period): void
    {
        $this->period = $period;
    }
}
