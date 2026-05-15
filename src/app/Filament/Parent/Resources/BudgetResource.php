<?php

namespace App\Filament\Parent\Resources;

use App\Filament\Parent\Resources\BudgetResource\Pages;
use App\Models\Budget;
use App\Models\Category;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class BudgetResource extends Resource
{
    protected static ?string $model = Budget::class;
    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationLabel = 'Anggaran';
    protected static ?string $modelLabel = 'Anggaran';
    protected static ?int $navigationSort = 3;
    protected static bool $shouldCheckPolicyExistence = false;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make()->schema([
                Forms\Components\Select::make('user_id')
                    ->label('Anak')
                    ->options(fn() => User::where('parent_id', auth()->id())
                        ->whereNotNull('name')
                        ->pluck('name', 'id')
                        ->toArray()
                    )
                    ->required()
                    ->preload(),

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
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Anak')
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
                        'weekly'  => 'info',
                        'monthly' => 'primary',
                        default   => 'gray',
                    })
                    ->formatStateUsing(fn($state) => $state === 'weekly' ? 'Mingguan' : 'Bulanan'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('period')
                    ->options([
                        'weekly'  => 'Mingguan',
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

    public static function getEloquentQuery(): Builder
    {
        $childIds = User::where('parent_id', auth()->id())
            ->pluck('id')
            ->toArray();

        return parent::getEloquentQuery()
            ->whereIn('user_id', $childIds);
    }

    public static function canAccess(): bool
    {
        return auth()->user()->role === 'parent';
    }

    public static function canCreate(): bool
{
    return auth()->user()->hasRole('parent');
}

public static function canEdit($record): bool
{
    return auth()->user()->hasRole('parent');
}

public static function canDelete($record): bool
{
    return auth()->user()->hasRole('parent');
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
