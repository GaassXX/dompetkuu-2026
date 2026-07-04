<?php

namespace App\Livewire\Mobile;

use App\Models\Category;
use App\Models\Expense;
use App\Models\Income;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class AddTransaction extends Component
{
    public ?string $type = null; // 'Pemasukan' | 'Pengeluaran'
    public $category_id = '';
    public $amount = '';
    public $date;
    public $description = '';
    public $editId = null;

    public function mount(): void
    {
        $this->date = now()->format('Y-m-d\TH:i');

        if ($editId = request('edit')) {
            $type = request('type', 'Pemasukan');
            $model = $type === 'Pemasukan'
                ? Income::where('user_id', Auth::id())->findOrFail($editId)
                : Expense::where('user_id', Auth::id())->findOrFail($editId);

            $this->editId = $model->id;
            $this->type = $type;
            $this->category_id = (string) $model->category_id;
            $this->amount = (string) (int) $model->amount;
            $this->date = \Carbon\Carbon::parse($model->date)->format('Y-m-d\TH:i');
            $this->description = $model->description ?? '';
        }
    }

    public function getCategoriesProperty()
    {
        if (!$this->type) {
            return collect();
        }

        $dbType = $this->type === 'Pemasukan' ? 'income' : 'expense';

        return Category::query()
            ->where('type', $dbType)
            ->where(function ($q) {
                $q->where('is_global', true)
                  ->orWhere('created_by', Auth::id());
            })
            ->orderBy('name')
            ->get();
    }

    public function selectType(string $type): void
    {
        $this->type = $type;
        $this->category_id = '';
    }

    public function save()
    {
        $this->validate([
            'category_id' => 'required|exists:categories,id',
            'amount'      => 'required|numeric|min:1',
            'date'        => 'required|date',
            'description' => 'nullable|string|max:255',
        ]);

        $data = [
            'user_id'     => Auth::id(),
            'category_id' => $this->category_id,
            'amount'      => $this->amount,
            'description' => $this->description,
            'date'        => \Carbon\Carbon::parse($this->date)->format('Y-m-d H:i:s'),
            'status'      => 'approved',
        ];

        if ($this->editId) {
            $model = $this->type === 'Pemasukan'
                ? Income::where('user_id', Auth::id())->findOrFail($this->editId)
                : Expense::where('user_id', Auth::id())->findOrFail($this->editId);
            $model->update($data);
            session()->flash('success', 'Transaksi berhasil diperbarui.');
        } else {
            if ($this->type === 'Pemasukan') {
                Income::create($data);
            } else {
                Expense::create($data);
            }
            session()->flash('success', 'Transaksi berhasil dicatat.');
        }

        return redirect()->route('mobile.dashboard');
    }

    public function render()
    {
        return view('livewire.mobile.add-transaction', [
            'categories' => $this->categories,
        ])->layout('layouts.mobile', ['noNavbar' => true]);
    }
}
