<?php

namespace App\Livewire\Mobile;

use App\Models\Transaction;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class TransactionHistory extends Component
{
    public string $filter = 'semua'; // semua | pengeluaran | pemasukan

    public function setFilter(string $filter): void
    {
        $this->filter = $filter;
    }

    public function render()
    {
        $userId = Auth::id();

        $query = Transaction::query()
            ->whereRaw('user_id = ?', [$userId])
            ->where('status', 'approved')
            ->orderByDesc('date')
            ->orderByDesc('id');

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
