<?php

namespace App\Filament\Parent\Pages;

use App\Filament\Parent\Resources\SavingResource;
use App\Models\Saving;
use App\Models\SavingDeposit;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Pages\Page;
use Illuminate\Support\Carbon;

class SavingsOverview extends Page
{
    protected static ?string $navigationIcon  = 'heroicon-o-banknotes';
    protected static ?string $navigationLabel = 'Tabungan';
    protected static ?string $title           = 'Tabungan';
    protected static string  $view            = 'filament.parent.pages.savings-overview';

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

    private function getAllIds(): array
    {
        $parentId = auth()->id();
        $childIds = User::where('parent_id', $parentId)->pluck('id')->toArray();
        return array_merge([$parentId], $childIds);
    }

    public function getStats(): array
{
    $allIds      = $this->getAllIds();
    $savings     = Saving::whereIn('user_id', $allIds)->where('status', 'active')->get();
    $totalSaved  = $savings->sum('current_amount');
    $totalTarget = $savings->sum('target_amount');
    $completed   = Saving::whereIn('user_id', $allIds)->where('status', 'completed')->count();
    $remaining   = max(0, $totalTarget - $totalSaved);

    $now       = Carbon::now();
    $lastMonth = $now->copy()->subMonth();

    // ✅ Pakai whereIn saving_id, bukan whereHas
    $savingIds = Saving::whereIn('user_id', $allIds)->pluck('id');

    $depositsThisMonth = SavingDeposit::whereIn('saving_id', $savingIds)
        ->whereMonth('date', $now->month)
        ->whereYear('date', $now->year)
        ->sum('amount');

    $depositsLastMonth = SavingDeposit::whereIn('saving_id', $savingIds)
        ->whereMonth('date', $lastMonth->month)
        ->whereYear('date', $lastMonth->year)
        ->sum('amount');

    $percentChange = 0;
    $isPositive    = true;

    if ($depositsLastMonth > 0) {
        $percentChange = round((($depositsThisMonth - $depositsLastMonth) / $depositsLastMonth) * 100, 1);
        $isPositive    = $percentChange >= 0;
    } elseif ($depositsThisMonth > 0) {
        $percentChange = 100;
        $isPositive    = true;
    }

    return compact(
        'savings', 'totalSaved', 'totalTarget',
        'completed', 'remaining', 'percentChange', 'isPositive'
    );
}

public function getRecentDeposits()
{
    $allIds    = $this->getAllIds();
    $savingIds = Saving::whereIn('user_id', $allIds)->pluck('id');

    // ✅ Pakai whereIn saving_id
    return SavingDeposit::whereIn('saving_id', $savingIds)
        ->with('savingGoal')
        ->orderByDesc('date')
        ->limit(10)
        ->get();
    }
}
