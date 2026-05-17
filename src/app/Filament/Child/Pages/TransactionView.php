<?php

namespace App\Filament\Child\Pages;

use App\Models\Expense;
use App\Models\Income;
use Filament\Forms\Components\Placeholder;
use Filament\Pages\Page;
use Filament\Tables\Actions\Action;
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
    protected static ?string $navigationLabel = 'Riwayat Transaksi';
    protected static ?string $title = 'Riwayat Transaksi Saya';
    protected static ?int $navigationSort = 3;
    protected static string $view = 'filament.child.pages.transaction-view';

    public function getTableRecordKey(Model $record): string
    {
        return (string) md5($record->type . $record->id . $record->date);
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
            ])
            ->actions([
                Action::make('view')
                    ->label('Detail')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->modalHeading('Detail Transaksi')
                    ->fillForm(fn($record) => [
                        'type'          => $record->type,
                        'category_name' => $record->category_name,
                        'amount'        => $record->amount,
                        'date'          => $record->date,
                        'status'        => $record->status,
                        'description'   => $record->description,
                    ])
                    ->form([
                        Placeholder::make('type')
                            ->label('Tipe')
                            ->content(fn($state) => $state),

                        Placeholder::make('category_name')
                            ->label('Kategori')
                            ->content(fn($state) => $state),

                        Placeholder::make('amount')
                            ->label('Jumlah')
                            ->content(fn($state) => 'Rp ' . number_format((float) $state, 0, ',', '.')),

                        Placeholder::make('date')
                            ->label('Tanggal')
                            ->content(fn($state) => \Carbon\Carbon::parse($state)->translatedFormat('d F Y')),

                        Placeholder::make('status')
                            ->label('Status')
                            ->content(fn($state) => match($state) {
                                'pending'  => 'Pending',
                                'approved' => 'Disetujui',
                                'rejected' => 'Ditolak',
                                default    => $state,
                            }),

                        Placeholder::make('description')
                            ->label('Keterangan')
                            ->content(fn($state) => $state ?? '-'),
                    ])
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Tutup'),
            ])
            ->defaultSort('date', 'desc');
    }

    protected function getTableQuery(): Builder
    {
        $userId = auth()->id();

        $incomes = DB::table('incomes')
            ->join('categories', 'incomes.category_id', '=', 'categories.id')
            ->where('incomes.user_id', $userId)
            ->select(
                'incomes.id',
                'categories.name as category_name',
                'incomes.amount',
                'incomes.date',
                'incomes.status',
                'incomes.description',
                DB::raw("'Pemasukan' as type")
            );

        $expenses = DB::table('expenses')
            ->join('categories', 'expenses.category_id', '=', 'categories.id')
            ->where('expenses.user_id', $userId)
            ->select(
                'expenses.id',
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
                'id',
                'category_name',
                'amount',
                'date',
                'status',
                'description',
                'type'
            );
    }
}
