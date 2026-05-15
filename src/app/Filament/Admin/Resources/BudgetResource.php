<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\BudgetResource\Pages;
use App\Models\Budget;
use App\Models\Category;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class BudgetResource extends Resource
{
    protected static ?string $model = Budget::class;
    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationLabel = 'Anggaran';
    protected static ?string $modelLabel = 'Anggaran';
    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make()->schema([
                Forms\Components\Select::make('user_id')
                    ->label('User')
                    ->options(
                        User::where('role', 'child')->whereNotNull('name')->pluck('name', 'id')
                        ->toArray()
                    )
                    ->required()
                    ->searchable(),

                Forms\Components\Select::make('category_id')
                    ->label('Kategori')
                    ->options(
                        Category::where('type', 'expense')
                        ->whereNotNull('name')
                        ->pluck('name', 'id')
                        ->toArray()
                    )
                    ->required()
                    ->searchable(),

                Forms\Components\TextInput::make('limit_amount')
                    ->label('Batas Anggaran')
                    ->numeric()
                    ->prefix('Rp')
                    ->required(),

                Forms\Components\Select::make('period')
                    ->label('Periode')
                    ->options([
                        'weekly' => 'Mingguan',
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
                Tables\Columns\TextColumn::make('user.name')
                    ->label('User')
                    ->sortable()
                    ->searchable(),

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
                        'weekly' => 'info',
                        'monthly' => 'primary',
                    })
                    ->formatStateUsing(fn($state) => $state === 'weekly' ? 'Mingguan' : 'Bulanan'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('period')
                    ->options([
                        'weekly' => 'Mingguan',
                        'monthly' => 'Bulanan',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBudgets::route('/'),
            'create' => Pages\CreateBudget::route('/create'),
            'edit' => Pages\EditBudget::route('/{record}/edit'),
        ];
    }
}
