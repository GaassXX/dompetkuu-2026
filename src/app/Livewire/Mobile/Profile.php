<?php

namespace App\Livewire\Mobile;

use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Profile extends Component
{
    public function logout()
    {
        Auth::logout();

        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect('/login');
    }

    public function render()
    {
        $user = Auth::user();

        $rows = Transaction::query()
            ->whereRaw('user_id = ?', [Auth::id()])
            ->where('status', 'approved')
            ->get();

        $totalSaldo = $rows->where('type', 'Pemasukan')->sum('amount')
            - $rows->where('type', 'Pengeluaran')->sum('amount');

        return view('livewire.mobile.profile', [
            'user'       => $user,
            'totalSaldo' => $totalSaldo,
        ])->layout('layouts.mobile');
    }
}
