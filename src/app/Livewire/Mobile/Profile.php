<?php

namespace App\Livewire\Mobile;

use App\Models\Saving;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Component;

class Profile extends Component
{
    public function disconnectTelegram()
    {
        $user = Auth::user();

        do {
            $code = 'TG-' . Str::upper(Str::random(6));
        } while (User::where('telegram_pair_code', $code)->exists());

        $user->update([
            'telegram_chat_id' => null,
            'telegram_disconnected_at' => now(),
            'telegram_pair_code' => $code,
        ]);

        $this->dispatch('$refresh');
    }

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

        $totalTabungan = Saving::where('user_id', Auth::id())
            ->where('status', 'active')
            ->sum('current_amount');

        $totalTargetTabungan = Saving::where('user_id', Auth::id())
            ->where('status', 'active')
            ->sum('target_amount');

        return view('livewire.mobile.profile', [
            'user'                => $user,
            'totalSaldo'          => $totalSaldo,
            'totalTabungan'       => $totalTabungan,
            'totalTargetTabungan' => $totalTargetTabungan,
        ])->layout('layouts.mobile');
    }
}
