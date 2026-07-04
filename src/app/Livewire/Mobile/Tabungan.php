<?php

namespace App\Livewire\Mobile;

use App\Models\Saving;
use App\Models\SavingDeposit;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Tabungan extends Component
{
    // Filter periode untuk riwayat setoran
    public string $period = '30';

    public function setPeriod(string $period): void
    {
        $this->period = $period;
    }

    /**
     * Data tabungan HANYA milik user yang sedang login.
     * Sama seperti scoping di Filament Child\SavingResource — tidak ada
     * agregasi data anak, walaupun user-nya adalah parent.
     */
    protected function ownSavingIds()
    {
        return Saving::where('user_id', Auth::id())->pluck('id');
    }

    public function getStats(): array
    {
        $userId = Auth::id();

        $activeSavings = Saving::where('user_id', $userId)->where('status', 'active')->get();
        $totalSaved    = $activeSavings->sum('current_amount');
        $totalTarget   = $activeSavings->sum('target_amount');
        $completed     = Saving::where('user_id', $userId)->where('status', 'completed')->count();
        $remaining     = max(0, $totalTarget - $totalSaved);

        $savingIds = $this->ownSavingIds();
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
        $query = SavingDeposit::whereIn('saving_id', $this->ownSavingIds())
            ->with('savingGoal')
            ->orderByDesc('date');

        if ($this->period !== 'all') {
            $query->where('date', '>=', Carbon::now()->subDays((int) $this->period));
        }

        return $query->limit(50)->get();
    }

    public function render()
    {
        $stats = $this->getStats();

        return view('livewire.mobile.tabungan', [
            'activeSavings'  => $stats['activeSavings'],
            'totalSaved'     => $stats['totalSaved'],
            'totalTarget'    => $stats['totalTarget'],
            'completed'      => $stats['completed'],
            'remaining'      => $stats['remaining'],
            'percentChange'  => $stats['percentChange'],
            'isPositive'     => $stats['isPositive'],
            'recentDeposits' => $this->getRecentDeposits(),
        ])->layout('layouts.mobile');
    }
}
