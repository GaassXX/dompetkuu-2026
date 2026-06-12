<?php

namespace App\Filament\Child\Resources;

use App\Filament\Child\Resources\BudgetResource\Pages;
use App\Models\Budget;
use App\Models\Category;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class BudgetResource extends Resource
{
    protected static ?string $model               = Budget::class;
    protected static ?string $navigationIcon = 'heroicon-o-wallet';
    protected static ?string $navigationLabel     = 'Anggaran Saya';
    protected static ?string $modelLabel          = 'Anggaran';
    protected static ?string $pluralModelLabel    = 'Anggaran Saya';
    protected static ?int    $navigationSort      = 4;
    protected static bool    $shouldCheckPolicyExistence = false;


    // ✅ Hanya tampil untuk user is_independent
    public static function canAccess(): bool
    {
        $user = auth()->user();
        return $user->hasRole('child') && $user->is_independent;
    }

    public static function canCreate(): bool
    {
        $user = auth()->user();
        return $user->hasRole('child') && $user->is_independent;
    }

    public static function canEdit($record): bool
    {
        return auth()->user()->is_independent;
    }

    public static function canDelete($record): bool
    {
        return auth()->user()->is_independent;
    }

    // ✅ Hanya tampilkan budget milik user sendiri
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('user_id', auth()->id());
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make()->schema([

                Forms\Components\Hidden::make('user_id')
                    ->default(fn() => auth()->id()),

                Forms\Components\Select::make('category_id')
                    ->label('Kategori')
                    ->options(fn() => Category::where('type', 'expense')
                        ->whereNotNull('name')
                        ->pluck('name', 'id')
                        ->toArray()
                    )
                    ->required()
                    ->searchable()
                    ->preload(),

                Forms\Components\TextInput::make('limit_amount')
                    ->label('Batas Anggaran')
                    ->numeric()
                    ->prefix('Rp')
                    ->required(),

                Forms\Components\Select::make('period')
                    ->label('Periode')
                    ->options([
                        'weekly'  => 'Mingguan',
                        'monthly' => 'Bulanan',
                    ])
                    ->default('monthly')
                    ->required(),

            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('category.name')
                    ->label('Kategori')
                    ->sortable(),

                Tables\Columns\TextColumn::make('limit_amount')
                    ->label('Batas Anggaran')
                    ->formatStateUsing(fn($state) => 'Rp ' . number_format((float) $state, 0, ',', '.'))
                    ->sortable(),

                Tables\Columns\TextColumn::make('period')
                    ->label('Periode')
                    ->badge()
                    ->color(fn($state) => match($state) {
                        'weekly'  => 'info',
                        'monthly' => 'primary',
                        default   => 'gray',
                    })
                    ->formatStateUsing(fn($state) => $state === 'weekly' ? 'Mingguan' : 'Bulanan'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->date('d M Y')
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->emptyStateHeading('Belum ada anggaran')
            ->emptyStateDescription('Buat anggaran untuk mengontrol pengeluaran Anda')
            ->emptyStateIcon('heroicon-o-banknotes');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListBudgets::route('/'),
            'create' => Pages\CreateBudget::route('/create'),
            'edit'   => Pages\EditBudget::route('/{record}/edit'),
        ];
    }
}
