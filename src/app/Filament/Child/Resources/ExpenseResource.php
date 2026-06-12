<?php

namespace App\Filament\Child\Resources;

use App\Filament\Child\Resources\ExpenseResource\Pages;
use App\Models\Budget;
use App\Models\Category;
use App\Models\Expense;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ExpenseResource extends Resource
{
    protected static ?string $model                      = Expense::class;
    protected static ?string $navigationIcon             = 'heroicon-o-arrow-trending-down';
    protected static ?string $navigationLabel            = 'Pengeluaran';
    protected static ?string $modelLabel                 = 'Pengeluaran';
    protected static ?string $pluralModelLabel           = 'Pemasukan';
    protected static ?int    $navigationSort             = 2;
    protected static bool    $shouldCheckPolicyExistence = false;

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
                    ->preload()
                    ->live()
                    ->afterStateUpdated(function (Get $get, Set $set) {
                        $set('_budget_check', now()->timestamp);
                    }),

                Forms\Components\TextInput::make('amount')
                    ->label('Jumlah')
                    ->numeric()
                    ->prefix('Rp')
                    ->required()
                    ->live(debounce: 500)
                    ->afterStateUpdated(function (Get $get, Set $set) {
                        $set('_budget_check', now()->timestamp);
                    }),

                Forms\Components\Hidden::make('_budget_check'),

                Forms\Components\DatePicker::make('date')
                    ->label('Tanggal')
                    ->default(now())
                    ->required(),

                Forms\Components\Select::make('status')
                    ->label('Status')
                    ->options([
                        'pending'  => 'Pending',
                        'approved' => 'Disetujui',
                        'rejected' => 'Ditolak',
                    ])
                    ->default('pending')
                    ->required(),

                Forms\Components\Textarea::make('description')
                    ->label('Keterangan')
                    ->nullable()
                    ->columnSpanFull(),

                Forms\Components\Placeholder::make('budget_warning')
                    ->label('')
                    ->content(function (Get $get) {
                        $categoryId    = $get('category_id');
                        $amount        = (float) ($get('amount') ?? 0);
                        $isIndependent = auth()->user()->is_independent;

                        if (!$categoryId || $amount <= 0) {
                            return null;
                        }

                        $userId = auth()->id();
                        $budget = Budget::where('user_id', $userId)
                            ->where('category_id', $categoryId)
                            ->first();

                        if (!$budget) {
                            return null;
                        }

                        $query = Expense::where('user_id', $userId)
                            ->where('category_id', $categoryId)
                            ->where('status', 'approved');

                        if ($budget->period === 'monthly') {
                            $query->whereMonth('date', now()->month)
                                  ->whereYear('date', now()->year);
                        } else {
                            $query->whereBetween('date', [
                                now()->startOfWeek(),
                                now()->endOfWeek(),
                            ]);
                        }

                        $spent     = (float) $query->sum('amount');
                        $limit     = (float) $budget->limit_amount;
                        $afterThis = $spent + $amount;
                        $pct       = $limit > 0 ? ($afterThis / $limit) * 100 : 0;
                        $period    = $budget->period === 'monthly' ? 'bulan ini' : 'minggu ini';

                        if ($afterThis > $limit) {
                            $over = number_format($afterThis - $limit, 0, ',', '.');

                            $overMsg = $isIndependent
                                ? 'Pengeluaran ini melebihi batas anggaran sebesar Rp ' . $over . '. Pertimbangkan untuk menyesuaikan anggaran Anda.'
                                : 'Melebihi Rp ' . $over . ' — kamu masih bisa submit, tapi perlu persetujuan orang tua.';

                            return new \Illuminate\Support\HtmlString("
                                <div style='background:#FCEBEB;border:1px solid #F7C1C1;border-radius:8px;padding:10px 14px;'>
                                    <p style='color:#A32D2D;font-weight:500;font-size:13px;margin:0;'>
                                        🚨 Pengeluaran ini akan melebihi budget {$period}!
                                    </p>
                                    <p style='color:#791F1F;font-size:12px;margin:4px 0 0;'>
                                        Terpakai: Rp " . number_format($spent, 0, ',', '.') . " + Rp " . number_format($amount, 0, ',', '.') . " = Rp " . number_format($afterThis, 0, ',', '.') . "
                                        dari limit Rp " . number_format($limit, 0, ',', '.') . " (" . number_format($pct, 1) . "%)
                                    </p>
                                    <p style='color:#791F1F;font-size:12px;margin:2px 0 0;'>
                                        {$overMsg}
                                    </p>
                                </div>
                            ");
                        }

                        if ($pct >= 80) {
                            $remaining = number_format($limit - $spent, 0, ',', '.');
                            return new \Illuminate\Support\HtmlString("
                                <div style='background:#FAEEDA;border:1px solid #FAC775;border-radius:8px;padding:10px 14px;'>
                                    <p style='color:#633806;font-weight:500;font-size:13px;margin:0;'>
                                        ⚠️ Budget {$period} hampir habis!
                                    </p>
                                    <p style='color:#854F0B;font-size:12px;margin:4px 0 0;'>
                                        Setelah transaksi ini: " . number_format($pct, 1) . "% dari budget terpakai.
                                        Sisa budget: Rp {$remaining}.
                                    </p>
                                </div>
                            ");
                        }

                        return null;
                    })
                    ->columnSpanFull(),

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

                Tables\Columns\TextColumn::make('amount')
                    ->label('Jumlah')
                    ->formatStateUsing(fn($state) => 'Rp ' . number_format((float) $state, 0, ',', '.'))
                    ->sortable(),

                Tables\Columns\TextColumn::make('date')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn($state) => match ($state) {
                        'pending'  => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                        default    => 'gray',
                    }),

                Tables\Columns\TextColumn::make('description')
                    ->label('Keterangan')
                    ->limit(30),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending'  => 'Pending',
                        'approved' => 'Disetujui',
                        'rejected' => 'Ditolak',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->visible(fn($record) => $record->status === 'pending' || is_null($record->parent_id)),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn($record) => $record->status === 'pending' || is_null($record->parent_id)),
            ])
            ->bulkActions([]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('user_id', auth()->id())
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc');
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
        // Izinkan edit jika berstatus pending ATAU jika itu akun mandiri (parent_id kosong)
        return auth()->user()->hasRole('child') && ($record->status === 'pending' || is_null($record->parent_id));
    }

    public static function canDelete($record): bool
    {
        // Izinkan hapus jika berstatus pending ATAU jika itu akun mandiri (parent_id kosong)
        return auth()->user()->hasRole('child') && ($record->status === 'pending' || is_null($record->parent_id));
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListExpenses::route('/'),
            'create' => Pages\CreateExpense::route('/create'),
            'edit'   => Pages\EditExpense::route('/{record}/edit'),
        ];
    }
    
}
