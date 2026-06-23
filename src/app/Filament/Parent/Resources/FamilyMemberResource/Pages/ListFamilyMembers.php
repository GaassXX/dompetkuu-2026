<?php

namespace App\Filament\Parent\Resources\FamilyMemberResource\Pages;

use App\Filament\Parent\Resources\FamilyMemberResource;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;

class ListFamilyMembers extends Page
{
    protected static string $resource = FamilyMemberResource::class;

    protected static string $view = 'filament.parent.resources.family-member-resource.pages.list-family-members';

    public function getTitle(): string
    {
        return 'Anggota Keluarga';
    }

    public function getMembers()
    {
        return User::where('parent_id', auth()->id())
            ->orderByDesc('created_at')
            ->get();
    }

    public function getStats(): array
    {
        $total = User::where('parent_id', auth()->id())->count();
        $active = User::where('parent_id', auth()->id())->where('is_active', true)->count();

        return compact('total', 'active');
    }

    public function toggleActive(int $id): void
    {
        $member = User::where('parent_id', auth()->id())->findOrFail($id);
        $member->update(['is_active' => !$member->is_active]);

        Notification::make()
            ->title($member->is_active ? 'Akun diaktifkan' : 'Akun dinonaktifkan')
            ->success()
            ->send();
    }

    public function deleteMember(int $id): void
    {
        $member = User::where('parent_id', auth()->id())->findOrFail($id);
        $member->delete();

        Notification::make()
            ->title('Anggota berhasil dihapus')
            ->success()
            ->send();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('create')
                ->label('New Anggota')
                ->icon('heroicon-o-plus')
                ->color('warning')
                ->url(FamilyMemberResource::getUrl('create')),
        ];
    }
}
