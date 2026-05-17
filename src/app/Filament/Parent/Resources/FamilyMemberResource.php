<?php

namespace App\Filament\Parent\Resources;

use App\Filament\Parent\Resources\FamilyMemberResource\Pages;
use App\Models\User;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class FamilyMemberResource extends Resource
{
    protected static ?string $model            = User::class;
    protected static ?string $navigationIcon   = 'heroicon-o-user-group';
    protected static ?string $navigationLabel  = 'Anggota Keluarga';
    protected static ?string $modelLabel       = 'Anggota';
    protected static ?string $pluralModelLabel = 'Anggota Keluarga';
    protected static ?int    $navigationSort   = 6;
    protected static bool    $shouldRegisterNavigation = true;

    public static function canAccess(): bool
    {
        return auth()->user()?->hasRole('parent') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->hasRole('parent') ?? false;
    }

    public static function canEdit(Model $record): bool
    {
        return $record->parent_id === auth()->id();
    }

    public static function canDelete(Model $record): bool
    {
        return $record->parent_id === auth()->id();
    }

    public static function canView(Model $record): bool
    {
        return $record->parent_id === auth()->id();
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('parent_id', auth()->id());
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Informasi Akun')->schema([
                TextInput::make('name')
                    ->label('Nama Lengkap')
                    ->required()
                    ->maxLength(255),

                TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->required()
                    ->unique(User::class, 'email', ignoreRecord: true)
                    ->maxLength(255),

                TextInput::make('password')
                    ->label('Password')
                    ->password()
                    ->revealable()
                    ->minLength(8)
                    ->dehydrateStateUsing(fn($state) => !empty($state) ? Hash::make($state) : null)
                    ->dehydrated(fn($state) => !empty($state))
                    ->required(fn(string $operation) => $operation === 'create')
                    ->helperText('Kosongkan jika tidak ingin mengubah password'),

                Toggle::make('is_active')
                    ->label('Akun Aktif')
                    ->default(true),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable()
                    ->description(fn($record) => $record->email),

                IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean()
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle'),

                TextColumn::make('pemasukan_bulan_ini')
                    ->label('Pemasukan Bulan Ini')
                    ->getStateUsing(fn($record) =>
                        'Rp ' . number_format(
                            $record->incomes()
                                ->whereMonth('date', now()->month)
                                ->whereYear('date', now()->year)
                                ->where('status', 'approved')
                                ->sum('amount'),
                            0, ',', '.'
                        )
                    )
                    ->color('success'),

                TextColumn::make('pengeluaran_bulan_ini')
                    ->label('Pengeluaran Bulan Ini')
                    ->getStateUsing(fn($record) =>
                        'Rp ' . number_format(
                            $record->expenses()
                                ->whereMonth('date', now()->month)
                                ->whereYear('date', now()->year)
                                ->where('status', 'approved')
                                ->sum('amount'),
                            0, ',', '.'
                        )
                    )
                    ->color('danger'),

                TextColumn::make('pending_count')
                    ->label('Pending')
                    ->getStateUsing(fn($record) =>
                        $record->incomes()->where('status', 'pending')->count() +
                        $record->expenses()->where('status', 'pending')->count()
                    )
                    ->badge()
                    ->color(fn($state) => $state > 0 ? 'warning' : 'success'),

                TextColumn::make('created_at')
                    ->label('Bergabung')
                    ->date('d M Y')
                    ->sortable(),
            ])
            ->actions([
                EditAction::make()
                    ->label('Edit'),

                Action::make('toggle_active')
                    ->label(fn($record) => $record->is_active ? 'Nonaktifkan' : 'Aktifkan')
                    ->icon(fn($record) => $record->is_active ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle')
                    ->color(fn($record) => $record->is_active ? 'warning' : 'success')
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $record->update(['is_active' => !$record->is_active]);
                        Notification::make()
                            ->title($record->is_active ? 'Akun diaktifkan' : 'Akun dinonaktifkan')
                            ->success()
                            ->send();
                    }),

                DeleteAction::make()
                    ->label('Hapus')
                    ->requiresConfirmation(),
            ])
            ->emptyStateHeading('Belum ada anggota keluarga')
            ->emptyStateDescription('Tambahkan akun anak untuk mulai memantau keuangan keluarga.')
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
