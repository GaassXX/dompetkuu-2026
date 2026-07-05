<?php

namespace App\Livewire\Mobile;

use App\Models\PanduanSection;
use Livewire\Component;

class Panduan extends Component
{
    public $sections;

    public function mount()
    {
        $this->sections = PanduanSection::where('is_active', true)
            ->orderBy('order')
            ->get();
    }

    public function render()
    {
        return view('livewire.mobile.panduan')
            ->layout('layouts.mobile', ['title' => 'Panduan']);
    }
}
