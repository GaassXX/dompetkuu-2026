<?php

namespace App\Livewire\Mobile;

use App\Models\Saving;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class TabunganComplete extends Component
{
    public function render()
    {
        $completedSavings = Saving::where('user_id', Auth::id())
            ->where('status', 'completed')
            ->orderByDesc('updated_at')
            ->get();

        return view('livewire.mobile.tabungan-complete', [
            'completedSavings' => $completedSavings,
            'totalCompleted'   => $completedSavings->count(),
            'totalTargetSum'   => $completedSavings->sum('target_amount'),
            'totalSavedSum'    => $completedSavings->sum('current_amount'),
        ])->layout('layouts.mobile');
    }
}
