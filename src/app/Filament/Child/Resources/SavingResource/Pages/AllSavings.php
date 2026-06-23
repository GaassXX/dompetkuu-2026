<?php

namespace App\Filament\Child\Resources\SavingResource\Pages;

use App\Filament\Child\Resources\SavingResource;
use App\Models\Saving;
use Filament\Actions\Action;
use Filament\Resources\Pages\Page;

class AllSavings extends Page
{
    protected static string $resource = SavingResource::class;
    protected static string $view     = 'filament.child.resources.saving-resource.pages.all-savings';

    public string $activeTab = 'active';

    public function getTitle(): string { return 'Semua Tabungan'; }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('create')
                ->label('Tambah Tabungan')
                ->icon('heroicon-o-plus')
                ->color('warning')
                ->url(SavingResource::getUrl('create')),
        ];
    }

    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
    }

    public function getSavings()
    {
        $query = Saving::where('user_id', auth()->id())
            ->orderByDesc('created_at');

        return match($this->activeTab) {
            'active'    => $query->where('status', 'active')->get(),
            'completed' => $query->where('status', 'completed')->get(),
            default     => $query->get(),
        };
    }

    public function getCounts(): array
    {
        return [
            'all'       => Saving::where('user_id', auth()->id())->count(),
            'active'    => Saving::where('user_id', auth()->id())->where('status', 'active')->count(),
            'completed' => Saving::where('user_id', auth()->id())->where('status', 'completed')->count(),
        ];
    }
}
