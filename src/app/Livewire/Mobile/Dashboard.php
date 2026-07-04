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
    public array $chartDataForJS = [];
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
        $months = collect(range(5, 0))
            ->map(fn($i) => Carbon::now()->startOfMonth()->subMonths($i));

        $startDate = $months->first()->copy()->startOfMonth()->toDateString();

        $rows = Transaction::query()
            ->where('user_id', $userId)
            ->where('status', 'approved')
            ->where('date', '>=', $startDate)
            ->get();

        $labels = $months->map(fn($m) => $m->translatedFormat('M Y'));

        // PENTING: $m adalah Carbon (mutable). Jangan panggil ->startOfMonth()
        // dan ->endOfMonth() pada objek $m yang sama dalam satu ekspresi,
        // karena keduanya akan merujuk ke reference yang sama dan sama-sama
        // berakhir sebagai "akhir bulan" setelah endOfMonth() dipanggil.
        // Selalu clone ($m->copy()) dulu sebelum dimutasi.
        $incomes = $months->map(function ($m) use ($rows) {
            $start = $m->copy()->startOfMonth();
            $end   = $m->copy()->endOfMonth();

            return (float) $rows->filter(fn($t) =>
                Carbon::parse($t->date)->between($start, $end)
            )->where('type', 'Pemasukan')->sum('amount');
        })->values()->toArray();

        $expenses = $months->map(function ($m) use ($rows) {
            $start = $m->copy()->startOfMonth();
            $end   = $m->copy()->endOfMonth();

            return (float) $rows->filter(fn($t) =>
                Carbon::parse($t->date)->between($start, $end)
            )->where('type', 'Pengeluaran')->sum('amount');
        })->values()->toArray();

        $this->chartDataForJS = [
            'labels' => $labels->toArray(),
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
        ];
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
            ->orderByDesc('created_at')
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
