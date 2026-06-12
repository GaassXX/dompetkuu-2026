<?php

namespace App\Filament\Child\Resources;

use App\Filament\Child\Resources\SavingResource\Pages;
use App\Models\Saving;
use App\Models\SavingDeposit;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class SavingResource extends Resource
{
    protected static ?string $model            = Saving::class;
    protected static ?string $slug             = 'saving-resource';
    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationLabel  = 'Tabungan';
    protected static ?string $modelLabel       = 'Tabungan';
    protected static ?string $pluralModelLabel = 'Tabungan';
    protected static ?int    $navigationSort   = 3;
    protected static bool    $shouldCheckPolicyExistence = false;

    // ✅ Child hanya lihat tabungan milik sendiri
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('user_id', auth()->id());
    }

    public static function canCreate(): bool   { return true; }
    public static function canViewAny(): bool  { return true; }
    public static function canView($record): bool   { return true; }
    public static function canEdit($record): bool   { return true; }
    public static function canDelete($record): bool { return true; }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make()
                ->description('Siapkan rencana keuangan Anda untuk masa depan yang lebih terencana.')
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->label('Nama Tabungan')
                        ->placeholder('e.g. Beli Sepeda Baru')
                        ->helperText('Gunakan nama yang memotivasi Anda.')
                        ->required()
                        ->maxLength(255),

                    Forms\Components\Select::make('category')
                        ->label('Kategori')
                        ->options([
                            'Liburan'    => '✈️ Liburan',
                            'Properti'   => '🏠 Properti',
                            'Pribadi'    => '👤 Pribadi',
                            'Pendidikan' => '📚 Pendidikan',
                            'Kendaraan'  => '🚗 Kendaraan',
                            'Kesehatan'  => '💊 Kesehatan',
                            'Elektronik' => '💻 Elektronik',
                            'Lainnya'    => '📦 Lainnya',
                        ])
                        ->native(false),

                    Forms\Components\TextInput::make('target_amount')
                        ->label('Target Jumlah')
                        ->prefix('Rp')
                        ->numeric()
                        ->required(),

                    Forms\Components\DatePicker::make('target_date')
                        ->label('Target Waktu')
                        ->native(false),


                    Forms\Components\TextInput::make('current_amount')
                        ->label('Setoran Awal (Opsional)')
                        ->prefix('Rp')
                        ->numeric()
                        ->default(0)
                        ->helperText('Modal awal untuk memulai tabungan ini.'),

                    Forms\Components\Textarea::make('description')
                        ->label('Deskripsi')
                        ->placeholder('Tuliskan catatan atau detail lainnya...')
                        ->columnSpanFull(),
                ])
                ->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Tabungan')
                    ->searchable()->sortable(),

                Tables\Columns\TextColumn::make('category')
                    ->label('Kategori')
                    ->badge()->color('primary'),

                Tables\Columns\TextColumn::make('current_amount')
                    ->label('Terkumpul')
                    ->formatStateUsing(fn($state) => 'Rp ' . number_format((float)$state, 0, ',', '.')),

                Tables\Columns\TextColumn::make('target_amount')
                    ->label('Target')
                    ->formatStateUsing(fn($state) => 'Rp ' . number_format((float)$state, 0, ',', '.')),

                Tables\Columns\TextColumn::make('progress')
                    ->label('Progress')
                    ->state(fn(Saving $record) => $record->getProgressPercentage() . '%')
                    ->badge()
                    ->color(fn(Saving $record) => $record->getProgressPercentage() >= 100 ? 'success' : ($record->getProgressPercentage() >= 50 ? 'warning' : 'danger')),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn($state) => match($state) {
                        'active'    => 'success',
                        'completed' => 'info',
                        'cancelled' => 'danger',
                        default     => 'gray',
                    })
                    ->formatStateUsing(fn($state) => match($state) {
                        'active'    => 'Aktif',
                        'completed' => 'Selesai',
                        'cancelled' => 'Dibatalkan',
                        default     => $state,
                    }),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                Tables\Actions\Action::make('deposit')
                    ->label('+ Setor')
                    ->color('success')
                    ->icon('heroicon-o-plus-circle')
                    ->form([
                        Forms\Components\TextInput::make('amount')
                            ->label('Jumlah Setoran')
                            ->prefix('Rp')
                            ->numeric()
                            ->required(),
                        Forms\Components\DatePicker::make('date')
                            ->label('Tanggal')
                            ->default(now())
                            ->required(),
                        Forms\Components\Textarea::make('note')
                            ->label('Catatan')
                            ->nullable(),
                    ])
                    ->action(function (Saving $record, array $data) {
                        SavingDeposit::create([
                            'saving_id' => $record->id,
                            'user_id'   => auth()->id(),
                            'amount'    => $data['amount'],
                            'date'      => $data['date'],
                            'note'      => $data['note'] ?? null,
                        ]);
                        $record->increment('current_amount', $data['amount']);
                        if ((float)$record->fresh()->current_amount >= (float)$record->target_amount) {
                            $record->update(['status' => 'completed']);
                        }
                    }),

                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListSavings::route('/'),
            'all'    => Pages\AllSavings::route('/all'),
            'create' => Pages\CreateSaving::route('/create'),
            'edit'   => Pages\EditSaving::route('/{record}/edit'),
            'view'   => Pages\ViewSaving::route('/{record}'),
        ];
    }
}
