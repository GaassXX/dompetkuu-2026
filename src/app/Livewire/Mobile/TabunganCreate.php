<?php

namespace App\Livewire\Mobile;

use App\Models\Saving;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class TabunganCreate extends Component
{
    public $editId = null;
    public string $name = '';
    public string $category = 'Lainnya';
    public $target_amount = '';
    public $current_amount = 0;
    public ?string $target_date = null;
    public string $description = '';

    public array $categories = [
        'Liburan'    => '✈️ Liburan',
        'Properti'   => '🏠 Properti',
        'Pribadi'    => '👤 Pribadi',
        'Pendidikan' => '📚 Pendidikan',
        'Kendaraan'  => '🚗 Kendaraan',
        'Kesehatan'  => '💊 Kesehatan',
        'Elektronik' => '💻 Elektronik',
        'Lainnya'    => '📦 Lainnya',
    ];

    public function mount()
    {
        if ($editId = request('edit')) {
            $saving = Saving::where('user_id', Auth::id())->findOrFail($editId);
            $this->editId = $saving->id;
            $this->name = $saving->name;
            $this->category = $saving->category;
            $this->target_amount = (string) (int) $saving->target_amount;
            $this->current_amount = $saving->current_amount;
            $this->target_date = $saving->target_date?->format('Y-m-d');
            $this->description = $saving->description ?? '';
        }
    }

    public function save()
    {
        $this->validate([
            'name'           => 'required|string|max:255',
            'category'       => 'required|string',
            'target_amount'  => 'required|integer|min:1',
            'current_amount' => 'nullable|numeric|min:0',
            'target_date'    => 'nullable|date',
            'description'    => 'nullable|string',
        ]);

        $data = [
            'user_id'        => Auth::id(),
            'name'           => $this->name,
            'category'       => $this->category,
            'target_amount'  => $this->target_amount,
            'current_amount' => $this->current_amount ?: 0,
            'target_date'    => $this->target_date,
            'description'    => $this->description,
            'status'         => 'active',
        ];

        if ($this->editId) {
            $saving = Saving::where('user_id', Auth::id())->findOrFail($this->editId);
            $saving->update($data);
            session()->flash('success', 'Tabungan berhasil diperbarui.');
            return redirect()->route('mobile.tabungan.show', $saving);
        }

        Saving::create($data);
        session()->flash('success', 'Tabungan baru berhasil dibuat.');
        return redirect()->route('mobile.tabungan');
    }

    public function render()
    {
        return view('livewire.mobile.tabungan-create')->layout('layouts.mobile');
    }
}
