<?php

namespace App\Filament\Parent\Pages;

use App\Models\Expense;
use App\Models\Income;
use App\Models\Transaction;
use App\Models\User;
use Filament\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class TransactionView extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Rekap Transaksi';
    protected static ?string $title = 'Rekap Transaksi Keluarga';
    protected static ?int $navigationSort = 4;
    protected static string $view = 'filament.parent.pages.transaction-view';

    public function getTableRecordKey(Model $record): string
    {
        return $record->type . '-' . $record->id . '-' . $record->date;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getTableQuery())
            ->columns([
                TextColumn::make('type')
                    ->label('Tipe')
                    ->badge()
                    ->color(fn($state) => match($state) {
                        'Pemasukan'   => 'success',
                        'Pengeluaran' => 'danger',
                        default       => 'gray',
                    }),

                TextColumn::make('user_name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('category_name')
                    ->label('Kategori')
                    ->sortable(),

                TextColumn::make('amount')
                    ->label('Jumlah')
                    ->formatStateUsing(fn($state) => 'Rp ' . number_format((float) $state, 0, ',', '.'))
                    ->sortable(),

                TextColumn::make('date')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn($state) => match($state) {
                        'pending'  => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                        default    => 'gray',
                    }),

                TextColumn::make('description')
                    ->label('Keterangan')
                    ->limit(30),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label('Tipe')
                    ->options([
                        'Pemasukan'   => 'Pemasukan',
                        'Pengeluaran' => 'Pengeluaran',
                    ]),

                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'pending'  => 'Pending',
                        'approved' => 'Disetujui',
                        'rejected' => 'Ditolak',
                    ]),

                SelectFilter::make('user_name')
                    ->label('Anggota')
                    ->options(function () {
                        $parentId = auth()->id();
                        $options  = [auth()->user()->name => auth()->user()->name . ' (Saya)'];
                        $children = User::where('parent_id', $parentId)->get();
                        foreach ($children as $child) {
                            $options[$child->name] = $child->name;
                        }
                        return $options;
                    }),
            ])
            ->defaultSort('date', 'desc');
    }

    protected function getTableQuery(): Builder
    {
        $parentId = auth()->id();
        $childIds = User::where('parent_id', $parentId)->pluck('id')->toArray();
        $allIds   = array_merge([$parentId], $childIds);

        $incomes = DB::table('incomes')
            ->join('users', 'incomes.user_id', '=', 'users.id')
            ->join('categories', 'incomes.category_id', '=', 'categories.id')
            ->whereIn('incomes.user_id', $allIds)
            ->select(
                'incomes.id',
                'users.name as user_name',
                'categories.name as category_name',
                'incomes.amount',
                'incomes.date',
                'incomes.status',
                'incomes.description',
                DB::raw("'Pemasukan' as type")
            );

        $expenses = DB::table('expenses')
            ->join('users', 'expenses.user_id', '=', 'users.id')
            ->join('categories', 'expenses.category_id', '=', 'categories.id')
            ->whereIn('expenses.user_id', $allIds)
            ->select(
                'expenses.id',
                'users.name as user_name',
                'categories.name as category_name',
                'expenses.amount',
                'expenses.date',
                'expenses.status',
                'expenses.description',
                DB::raw("'Pengeluaran' as type")
            );

        $union = $incomes->unionAll($expenses);

        return Income::query()
            ->fromSub($union, 'transactions')
            ->select(
                DB::raw("CONCAT(type, '-', id, '-', date) as record_key"),
                'user_name',
                'category_name',
                'amount',
                'date',
                'status',
                'description',
                'type'
            );
    }
}
