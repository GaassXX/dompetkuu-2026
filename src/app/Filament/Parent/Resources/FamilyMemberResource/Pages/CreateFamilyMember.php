<?php

namespace App\Filament\Parent\Resources\FamilyMemberResource\Pages;

use App\Filament\Parent\Resources\FamilyMemberResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Role;

class CreateFamilyMember extends CreateRecord
{
    protected static string $resource = FamilyMemberResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    // ✅ Auto set parent_id dan role saat create
    protected function handleRecordCreation(array $data): Model
    {
        $data['parent_id'] = auth()->id();
        $data['role']      = 'child';

        $user = parent::handleRecordCreation($data);

        // Assign role via Spatie
        $childRole = Role::firstOrCreate(['name' => 'child', 'guard_name' => 'web']);
        $user->assignRole($childRole);

        return $user;
    }
}
