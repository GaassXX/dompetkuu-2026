<?php

namespace App\Livewire\Mobile;

use App\Models\Saving;
use App\Models\SavingDeposit;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class TabunganDetail extends Component
{
    public Saving $saving;

    public bool $showDepositForm = false;
    public $amount = '';
    public ?string $date = null;
    public string $note = '';

    public function mount(Saving $saving): void
    {
        // Pastikan hanya pemilik yang bisa akses (bukan tabungan orang lain)
        abort_unless($saving->user_id === Auth::id(), 403);

        $this->saving = $saving;
        $this->date   = now()->format('Y-m-d');
    }

    public function toggleDepositForm(): void
    {
        $this->showDepositForm = ! $this->showDepositForm;
    }

    public function setor()
    {
        $this->validate([
            'amount' => 'required|numeric|min:1',
            'date'   => 'required|date',
            'note'   => 'nullable|string|max:255',
        ]);

        SavingDeposit::create([
            'saving_id' => $this->saving->id,
            'user_id'   => Auth::id(),
            'amount'    => $this->amount,
            'date'      => $this->date,
            'note'      => $this->note ?: null,
        ]);

        $this->saving->increment('current_amount', $this->amount);

        if ((float) $this->saving->fresh()->current_amount >= (float) $this->saving->target_amount) {
            $this->saving->update(['status' => 'completed']);
        }

        $this->saving->refresh();
        $this->reset(['amount', 'note', 'showDepositForm']);
        $this->date = now()->format('Y-m-d');

        session()->flash('success', 'Setoran berhasil dicatat.');
    }

    public function render()
    {
        $deposits = $this->saving->deposits()->orderByDesc('date')->get();

        return view('livewire.mobile.tabungan-detail', [
            'deposits' => $deposits,
            'progress' => $this->saving->getProgressPercentage(),
        ])->layout('layouts.mobile');
    }
}
