<?php

namespace App\Livewire\Mobile;

use App\Models\Saving;
use App\Models\Transaction;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Dashboard extends Component
{
    public float $totalSaldo = 0;
    public float $pemasukanBulanIni = 0;
    public float $pengeluaranBulanIni = 0;
    public float $tabunganBulanIni = 0;
    public array $chart7Hari = [];
    public array $pengeluaranPerKategori = [];
    public array $tabunganList = [];
    public $transaksiTerakhir = [];

    public function mount(): void
    {
        $this->loadSummary();
        $this->loadChart();
        $this->loadPengeluaranPerKategori();
        $this->loadTabungan();
        $this->loadTransaksiTerakhir();
    }

    protected function loadSummary(): void
    {
        $userId = Auth::id();
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        $rows = Transaction::query()
            ->whereRaw('user_id = ?', [$userId])
            ->where('status', 'approved')
            ->get();

        $this->totalSaldo = $rows->where('type', 'Pemasukan')->sum('amount')
            - $rows->where('type', 'Pengeluaran')->sum('amount');

        $bulanIni = $rows->filter(
            fn ($t) => Carbon::parse($t->date)->between($startOfMonth, $endOfMonth)
        );

        $this->pemasukanBulanIni = $bulanIni->where('type', 'Pemasukan')->sum('amount');
        $this->pengeluaranBulanIni = $bulanIni->where('type', 'Pengeluaran')->sum('amount');
    }

    protected function loadChart(): void
    {
        $userId = Auth::id();
        $start = Carbon::now()->subDays(6)->startOfDay();

        $rows = Transaction::query()
            ->whereRaw('user_id = ?', [$userId])
            ->where('status', 'approved')
            ->where('date', '>=', $start->toDateString())
            ->get()
            ->groupBy(fn ($t) => Carbon::parse($t->date)->format('Y-m-d'));

        $hariIndo = ['Sun' => 'Min', 'Mon' => 'Sen', 'Tue' => 'Sel', 'Wed' => 'Rab', 'Thu' => 'Kam', 'Fri' => 'Jum', 'Sat' => 'Sab'];

        $result = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $key = $date->format('Y-m-d');
            $dayRows = $rows->get($key, collect());

            $pemasukan = $dayRows->where('type', 'Pemasukan')->sum('amount');
            $pengeluaran = $dayRows->where('type', 'Pengeluaran')->sum('amount');

            $result[] = [
                'label'      => $hariIndo[$date->format('D')] ?? $date->format('D'),
                'pemasukan'  => (float) $pemasukan,
                'pengeluaran' => (float) $pengeluaran,
                'net'        => $pemasukan - $pengeluaran,
                'total'      => $pemasukan + $pengeluaran,
            ];
        }

        $this->chart7Hari = $result;
    }

    protected function loadPengeluaranPerKategori(): void
    {
        $userId = Auth::id();
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        $rows = Transaction::query()
            ->whereRaw('user_id = ?', [$userId])
            ->where('type', 'Pengeluaran')
            ->where('status', 'approved')
            ->whereBetween('date', [$startOfMonth->toDateString(), $endOfMonth->toDateString()])
            ->get();

        $grouped = $rows->groupBy('category_name')
            ->map(fn ($items) => $items->sum('amount'))
            ->sortDesc()
            ->take(6);

        $palette = ['#ef4444', '#f97316', '#eab308', '#22c55e', '#3b82f6', '#8b5cf6'];

        $i = 0;
        $this->pengeluaranPerKategori = $grouped->map(function ($amount, $label) use ($palette, &$i) {
            return [
                'label'  => $label,
                'amount' => (float) $amount,
                'color'  => $palette[$i++ % count($palette)],
            ];
        })->values()->toArray();
    }

    protected function loadTabungan(): void
    {
        $userId = Auth::id();

        $savings = Saving::query()
            ->where('user_id', $userId)
            ->where('status', 'active')
            ->orderBy('target_date')
            ->limit(3)
            ->get();

        $this->tabunganList = $savings->map(fn ($s) => [
            'id'             => $s->id,
            'name'           => $s->name,
            'target_amount'  => (float) $s->target_amount,
            'current_amount' => (float) $s->current_amount,
            'progress'       => $s->getProgressPercentage(),
            'remaining'      => $s->getRemainingAmount(),
        ])->toArray();

        $this->tabunganBulanIni = (float) Saving::query()
            ->where('user_id', $userId)
            ->where('status', 'active')
            ->sum('current_amount');
    }

    protected function loadTransaksiTerakhir(): void
    {
        $userId = Auth::id();

        $this->transaksiTerakhir = Transaction::query()
            ->whereRaw('user_id = ?', [$userId])
            ->where('status', 'approved')
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->limit(3)
            ->get();
    }

    public function render()
    {
        return view('livewire.mobile.dashboard')
            ->layout('layouts.mobile');
    }
}
