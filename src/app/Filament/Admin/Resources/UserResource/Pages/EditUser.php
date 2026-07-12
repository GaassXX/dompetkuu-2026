<?php

namespace App\Filament\Admin\Resources\UserResource\Pages;

use App\Filament\Admin\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (isset($data['roles'])) {
            $role = $data['roles'][0] ?? 'child';
            $data['role'] = match (true) {
                in_array($role, ['super_admin', 'admin']) => 'admin',
                $role === 'parent' => 'parent',
                $role === 'child' && ($data['is_independent'] ?? false) => 'personal',
                default => 'child',
            };
        }
        return $data;
    }
}
