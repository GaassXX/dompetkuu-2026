<?php

namespace App\Filament\Parent\Resources;

use App\Filament\Parent\Resources\FamilyMemberResource\Pages;
use App\Models\User;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Hash;

class FamilyMemberResource extends Resource
{
    protected static ?string $model           = User::class;
    protected static ?string $slug            = 'family-members';
    protected static ?string $navigationIcon  = 'heroicon-o-user-group';
    protected static ?string $navigationLabel = 'Anggota Keluarga';
    protected static ?string $modelLabel      = 'Anggota';
    protected static ?string $pluralModelLabel = 'Anggota Keluarga';
     protected static ?string $navigationGroup = 'Kelola';
    protected static ?int    $navigationSort  = 5;

    // ✅ Izinkan akses hanya untuk parent
    public static function canAccess(): bool
    {
        return auth()->user()?->hasRole('parent') ?? false;
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->hasRole('parent') ?? false;
    }

    // ✅ Hanya tampilkan anak milik parent yang login
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('parent_id', auth()->id());
    }

    public static function canCreate(): bool
    {
    return auth()->user()?->hasRole('parent') ?? false;
    }

    public static function canEdit($record): bool
    {
    return auth()->user()?->hasRole('parent') ?? false;
    }

    public static function canDelete($record): bool
    {
    return auth()->user()?->hasRole('parent') ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Nama Lengkap')
                    ->required()
                    ->maxLength(255),

                TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->required()
                    ->unique(User::class, 'email', ignoreRecord: true),

                TextInput::make('password')
                    ->label('Password')
                    ->password()
                    ->dehydrateStateUsing(fn($state) => Hash::make($state))
                    ->dehydrated(fn($state) => filled($state))
                    ->required(fn(string $operation) => $operation === 'create')
                    ->minLength(8)
                    ->helperText(fn(string $operation) => $operation === 'edit' ? 'Kosongkan jika tidak ingin mengubah password' : null),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('email')
                    ->label('Email')
                    ->searchable(),

                TextColumn::make('is_active')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn($state) => $state ? 'Aktif' : 'Nonaktif')
                    ->color(fn($state) => $state ? 'success' : 'danger'),

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->date('d M Y')
                    ->sortable(),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->emptyStateHeading('Belum ada anggota keluarga')
            ->emptyStateDescription('Tambah akun anak untuk mulai memantau keuangan mereka')
            ->emptyStateIcon('heroicon-o-user-group');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListFamilyMembers::route('/'),
            'create' => Pages\CreateFamilyMember::route('/create'),
            'edit'   => Pages\EditFamilyMember::route('/{record}/edit'),
        ];
    }
}
