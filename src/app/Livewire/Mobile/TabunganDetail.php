<?php

namespace App\Livewire\Mobile;

use App\Models\Saving;
use App\Models\SavingDeposit;
use App\Notifications\SavingGoalReached;
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
            'amount' => 'required|integer|min:1',
            'date'   => 'required|date',
            'note'   => 'nullable|string|max:255',
        ]);

        $amount = (int) $this->amount;

        SavingDeposit::create([
            'saving_id' => $this->saving->id,
            'user_id'   => Auth::id(),
            'amount'    => $amount,
            'date'      => $this->date,
            'note'      => $this->note ?: null,
        ]);

        $this->saving->update([
            'current_amount' => $this->saving->current_amount + $amount,
        ]);

        $this->saving->refresh();

        if ((float) $this->saving->current_amount >= (float) $this->saving->target_amount) {
            $this->saving->update(['status' => 'completed']);
            Auth::user()->notify(new SavingGoalReached(
                savingName: $this->saving->name,
                targetAmount: $this->saving->target_amount,
            ));
        }

        $this->reset(['amount', 'note', 'showDepositForm']);
        $this->date = now()->format('Y-m-d');

        session()->flash('success', 'Setoran berhasil dicatat.');
    }

    public function delete()
    {
        $this->saving->deposits()->delete();
        $this->saving->delete();

        return redirect()->route('mobile.tabungan');
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
