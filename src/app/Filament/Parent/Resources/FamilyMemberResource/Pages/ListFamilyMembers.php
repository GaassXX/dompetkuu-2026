<?php

namespace App\Filament\Parent\Resources\FamilyMemberResource\Pages;

use App\Filament\Parent\Resources\FamilyMemberResource;
use Filament\Resources\Pages\ListRecords;

class ListFamilyMembers extends ListRecords
{
    protected static string $resource = FamilyMemberResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\CreateAction::make()
                ->label('Tambah Anak'),
        ];
    }
}
