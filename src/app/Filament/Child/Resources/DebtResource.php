<?php

namespace App\Filament\Child\Resources;

use App\Filament\Child\Resources\DebtResource\Pages;
use App\Models\Debt;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\ValidationException;

class DebtResource extends Resource
{
    protected static ?string $model = Debt::class;
    protected static ?string $slug = 'debts';
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationLabel = 'Utang & Piutang';
    protected static ?string $modelLabel = 'Utang & Piutang';
    protected static ?string $pluralModelLabel = 'Utang & Piutang';
    protected static ?int $navigationSort = 4;
    protected static bool $shouldCheckPolicyExistence = false;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('user_id', auth()->id())
            ->orderByRaw("CASE WHEN status = 'unpaid' THEN 0 ELSE 1 END")
            ->orderBy('due_date')
            ->orderByDesc('date');
    }

    public static function canAccess(): bool
    {
        return auth()->user()->hasRole('child');
    }

    public static function canCreate(): bool
    {
        return auth()->user()->hasRole('child');
    }

    public static function canEdit($record): bool
    {
        return auth()->user()->hasRole('child') && $record->status === Debt::STATUS_UNPAID;
    }

    public static function canDelete($record): bool
    {
        return auth()->user()->hasRole('child') && $record->status === Debt::STATUS_UNPAID;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Utang & Piutang')
                ->description('Catat kewajiban (utang) dan tagihan (piutang). Pelunasan otomatis tercatat sebagai transaksi.')
                ->schema([
                    Forms\Components\Hidden::make('user_id')
                        ->default(fn () => auth()->id()),

                    Forms\Components\Select::make('type')
                        ->label('Jenis')
                        ->options([
                            Debt::TYPE_DEBT => '🔴 Utang (saya berutang ke orang lain)',
                            Debt::TYPE_RECEIVABLE => '🟢 Piutang (orang lain berutang ke saya)',
                        ])
                        ->required()
                        ->native(false),

                    Forms\Components\TextInput::make('person_name')
                        ->label('Nama Pihak')
                        ->placeholder('e.g. Budi')
                        ->required()
                        ->maxLength(255),

                    Forms\Components\TextInput::make('amount')
                        ->label('Nominal')
                        ->prefix('Rp')
                        ->required()
                        ->numeric()
                        ->minValue(1)
                        ->maxValue(999999999999999999)
                        ->extraInputAttributes(['data-field' => 'amount', 'inputmode' => 'numeric'])
                        ->mutateStateForValidationUsing(fn ($state) => (int) str_replace(['.', ','], '', $state ?? '0')),

                    Forms\Components\DatePicker::make('date')
                        ->label('Tanggal Catat')
                        ->default(now())
                        ->required()
                        ->native(false),

                    Forms\Components\DatePicker::make('due_date')
                        ->label('Jatuh Tempo (Opsional)')
                        ->native(false),

                    Forms\Components\Textarea::make('description')
                        ->label('Catatan')
                        ->placeholder('e.g. Pinjam untuk kebutuhan kuliah')
                        ->nullable()
                        ->maxLength(500)
                        ->columnSpanFull(),
                ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('type')
                    ->label('Jenis')
                    ->badge()
                    ->color(fn ($state) => $state === Debt::TYPE_DEBT ? 'danger' : 'success')
                    ->formatStateUsing(fn ($state) => $state === Debt::TYPE_DEBT ? 'Utang' : 'Piutang'),

                Tables\Columns\TextColumn::make('person_name')
                    ->label('Nama Pihak')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('amount')
                    ->label('Nominal')
                    ->formatStateUsing(fn ($state) => 'Rp ' . number_format((float) $state, 0, ',', '.'))
                    ->sortable(),

                Tables\Columns\TextColumn::make('date')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('due_date')
                    ->label('Jatuh Tempo')
                    ->date('d M Y')
                    ->sortable()
                    ->color(fn (Debt $record) => $record->isOverdue() ? 'danger' : null)
                    ->weight(fn (Debt $record) => $record->isOverdue() ? 'bold' : null),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn ($state) => $state === Debt::STATUS_PAID ? 'success' : 'warning')
                    ->formatStateUsing(fn ($state) => $state === Debt::STATUS_PAID ? 'Lunas' : 'Belum Lunas'),

                Tables\Columns\TextColumn::make('paid_at')
                    ->label('Tanggal Lunas')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label('Jenis')
                    ->options([
                        Debt::TYPE_DEBT => 'Utang',
                        Debt::TYPE_RECEIVABLE => 'Piutang',
                    ]),
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        Debt::STATUS_UNPAID => 'Belum Lunas',
                        Debt::STATUS_PAID => 'Lunas',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('pay')
                    ->label('Lunasi')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Lunasi Utang/Piutang?')
                    ->modalDescription(fn (Debt $record) => $record->type === Debt::TYPE_DEBT
                        ? 'Pelunasan akan otomatis tercatat sebagai PENGELUARAN.'
                        : 'Pelunasan akan otomatis tercatat sebagai PEMASUKAN.')
                    ->visible(fn (Debt $record) => $record->status === Debt::STATUS_UNPAID)
                    ->action(function (Debt $record) {
                        try {
                            $record->markAsPaid();
                            Notification::make()
                                ->title('Berhasil dilunasi')
                                ->body('Pelunasan otomatis tercatat sebagai transaksi.')
                                ->success()
                                ->send();
                        } catch (ValidationException $e) {
                            Notification::make()
                                ->title('Gagal')
                                ->body('Data ini sudah lunas.')
                                ->danger()
                                ->send();
                        }
                    }),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDebts::route('/'),
            'create' => Pages\CreateDebt::route('/create'),
            'edit' => Pages\EditDebt::route('/{record}/edit'),
        ];
    }
}
