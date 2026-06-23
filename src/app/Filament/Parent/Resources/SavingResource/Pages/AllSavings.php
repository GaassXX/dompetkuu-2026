<?php

namespace App\Filament\Parent\Resources\SavingResource\Pages;

use App\Filament\Parent\Resources\SavingResource;
use App\Models\Saving;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Resources\Pages\Page;

class AllSavings extends Page
{
    protected static string $resource = SavingResource::class;
    protected static string $view     = 'filament.parent.resources.saving-resource.pages.all-savings';

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
    $parentId = auth()->id();
    $childIds = User::where('parent_id', $parentId)->pluck('id')->toArray();
    $allIds   = array_merge([$parentId], $childIds);

    $query = Saving::whereIn('user_id', $allIds)
        ->with('user')
        ->orderByDesc('created_at');

    return match($this->activeTab) {
        'active'    => $query->where('status', 'active')->get(),
        'completed' => $query->where('status', 'completed')->get(),
        default     => $query->get(),
    };
}

public function getCounts(): array
{
    $parentId = auth()->id();
    $childIds = User::where('parent_id', $parentId)->pluck('id')->toArray();
    $allIds   = array_merge([$parentId], $childIds);

    return [
        'all'       => Saving::whereIn('user_id', $allIds)->count(),
        'active'    => Saving::whereIn('user_id', $allIds)->where('status', 'active')->count(),
        'completed' => Saving::whereIn('user_id', $allIds)->where('status', 'completed')->count(),
    ];
}
}
