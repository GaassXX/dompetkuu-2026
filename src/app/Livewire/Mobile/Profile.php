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

        $formatRp = function ($number) {
            $number = (float) $number;
            if ($number >= 1000000000) {
                $val = $number / 1000000000;
                return 'Rp ' . ($val == floor($val) ? number_format($val, 0, ',', '.') : number_format($val, 1, ',', '.')) . ' M';
            } elseif ($number >= 100000000) {
                $val = $number / 1000000;
                return 'Rp ' . ($val == floor($val) ? number_format($val, 0, ',', '.') : number_format($val, 1, ',', '.')) . ' Jt';
            }
            return 'Rp ' . number_format($number, 0, ',', '.');
        };

        return view('livewire.mobile.profile', [
            'user'                => $user,
            'totalSaldo'          => $totalSaldo,
            'totalTabungan'       => $totalTabungan,
            'totalTargetTabungan' => $totalTargetTabungan,
            'formatRp'            => $formatRp,
        ])->layout('layouts.mobile');
    }
}
