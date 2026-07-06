<?php

namespace App\Livewire\Mobile;

use App\Models\Expense;
use App\Models\Income;
use App\Models\Transaction;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class TransactionHistory extends Component
{
    public string $filter = 'semua';
    public string $periode = '';
    public int $bulan = 0;
    public int $tahun = 0;
    public bool $showDetail = false;
    public $detailTransaction = null;

    public function mount(): void
    {
        $this->setPeriode(now()->format('Y-m'));
    }

    public function setFilter(string $filter): void
    {
        $this->filter = $filter;
    }

    public function updated(string $propertyName): void
    {
        if ($propertyName === 'periode') {
            $this->parsePeriode();
        }
    }

    private function setPeriode(string $value): void
    {
        $this->periode = $value;
        $this->parsePeriode();
    }

    private function parsePeriode(): void
    {
        $parts = explode('-', $this->periode);
        $this->tahun = (int) ($parts[0] ?? now()->year);
        $this->bulan = (int) ($parts[1] ?? now()->month);
    }

    public function closeDetail(): void
    {
        $this->showDetail = false;
        $this->detailTransaction = null;
    }

    public function viewTransaction(int $id, string $type): void
    {
        $this->detailTransaction = Transaction::whereRaw('user_id = ?', [Auth::id()])
            ->where('original_id', $id)
            ->where('type', $type)
            ->first();
        $this->showDetail = true;
    }

    public function deleteTransaction(int $id, string $type): void
    {
        if ($type === 'Pemasukan') {
            Income::where('user_id', Auth::id())->where('id', $id)->delete();
        } else {
            Expense::where('user_id', Auth::id())->where('id', $id)->delete();
        }
    }

    public function getPeriodeOptionsProperty(): array
    {
        $start = now()->startOfYear();
        $end = now()->startOfMonth();
        $options = [];
        for ($d = $start->copy(); $d->lte($end); $d->addMonth()) {
            $options[$d->format('Y-m')] = $d->translatedFormat('F Y');
        }
        return $options;
    }

    public function render()
    {
        $userId = Auth::id();

        $query = Transaction::query()
            ->whereRaw('user_id = ?', [$userId])
            ->whereMonth('date', $this->bulan)
            ->whereYear('date', $this->tahun)
            ->orderByDesc('created_at')
            ->orderByDesc('id');

        if (!Auth::user()->parent_id) {
            $query->where('status', 'approved');
        }

        if ($this->filter === 'pengeluaran') {
            $query->where('type', 'Pengeluaran');
        } elseif ($this->filter === 'pemasukan') {
            $query->where('type', 'Pemasukan');
        }

        $transactions = $query->get();

        $grouped = $transactions->groupBy(function ($t) {
            $date = Carbon::parse($t->date);

            if ($date->isToday()) return 'HARI INI, ' . $date->translatedFormat('d F Y');
            if ($date->isYesterday()) return 'KEMARIN, ' . $date->translatedFormat('d F Y');

            return $date->translatedFormat('d F Y');
        });

        return view('livewire.mobile.transaction-history', [
            'grouped' => $grouped,
        ])->layout('layouts.mobile');
    }
}
