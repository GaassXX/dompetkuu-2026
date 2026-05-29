<?php

namespace App\Filament\Admin\Pages;

use App\Models\Transaction;
use Carbon\Carbon;
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

    protected static ?string $navigationIcon  = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Rekap Gabungan';
    protected static ?string $title           = 'Rekap Transaksi';
    protected static ?int    $navigationSort  = 5;
    protected static string  $view            = 'filament.admin.pages.transaction-view';

    public function getTableRecordKey(Model $record): string
    {
        return (string) $record->id;
    }

    public function resolveTableRecord(string $key): ?Model
    {
        $prefix     = substr($key, 0, 1);
        $originalId = substr($key, 2);
        $type       = $prefix === 'I' ? 'Pemasukan' : 'Pengeluaran';

        $incomes = DB::table('incomes')
            ->join('users', 'incomes.user_id', '=', 'users.id')
            ->join('categories', 'incomes.category_id', '=', 'categories.id')
            ->leftJoin('users as approver', 'incomes.approved_by', '=', 'approver.id')
            ->select(
                DB::raw("CONCAT('I-', incomes.id) as id"),
                'incomes.id as original_id',
                'users.name as user_name',
                'categories.name as category_name',
                'incomes.amount',
                'incomes.date',
                'incomes.status',
                'incomes.description',
                'incomes.created_at',
                DB::raw("approver.name as approved_by_name"),
                DB::raw("'Pemasukan' as type")
            );

        $expenses = DB::table('expenses')
            ->join('users', 'expenses.user_id', '=', 'users.id')
            ->join('categories', 'expenses.category_id', '=', 'categories.id')
            ->leftJoin('users as approver', 'expenses.approved_by', '=', 'approver.id')
            ->select(
                DB::raw("CONCAT('E-', expenses.id) as id"),
                'expenses.id as original_id',
                'users.name as user_name',
                'categories.name as category_name',
                'expenses.amount',
                'expenses.date',
                'expenses.status',
                'expenses.description',
                'expenses.created_at',
                DB::raw("approver.name as approved_by_name"),
                DB::raw("'Pengeluaran' as type")
            );

        return Transaction::fromSub($incomes->unionAll($expenses), 'transactions')
            ->select('id', 'original_id', 'user_name', 'category_name', 'amount', 'date', 'status', 'description', 'type', 'created_at', 'approved_by_name')
            ->where('type', $type)
            ->where('original_id', $originalId)
            ->first();
    }

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getTableQuery())
            ->columns([
                TextColumn::make('type')
                    ->label('Tipe')
                    ->badge()
                    ->color(fn($state) => match ($state) {
                        'Pemasukan'   => 'success',
                        'Pengeluaran' => 'danger',
                        default       => 'gray',
                    }),

                TextColumn::make('user_name')
                    ->label('User')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('category_name')
                    ->label('Kategori')
                    ->searchable()
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
                    ->color(fn($state) => match ($state) {
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
                    ])
                    ->placeholder('Semua Tipe'),

                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'pending'  => 'Pending',
                        'approved' => 'Disetujui',
                        'rejected' => 'Ditolak',
                    ])
                    ->placeholder('Semua Status'),
            ])
            ->actions([
                Action::make('view')
                    ->label('Detail')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->modalHeading('Detail Transaksi')
                    ->fillForm(fn($record) => [
                        'type'             => $record->type,
                        'user_name'        => $record->user_name,
                        'category_name'    => $record->category_name,
                        'amount'           => $record->amount,
                        'date'             => $record->date,
                        'created_at'       => $record->created_at,
                        'status'           => $record->status,
                        'approved_by_name' => $record->approved_by_name,
                        'description'      => $record->description,
                    ])
                    ->form([
                        Placeholder::make('type')->label('Tipe')->content(fn($state) => $state),
                        Placeholder::make('user_name')->label('Nama User')->content(fn($state) => $state),
                        Placeholder::make('category_name')->label('Kategori')->content(fn($state) => $state),
                        Placeholder::make('amount')->label('Jumlah')->content(fn($state) => 'Rp ' . number_format((float) $state, 0, ',', '.')),
                        Placeholder::make('date')->label('Tanggal Transaksi')->content(fn($state) => Carbon::parse($state)->translatedFormat('d F Y')),
                        Placeholder::make('created_at')->label('Dibuat Pada')->content(fn($state) => $state ? Carbon::parse($state)->translatedFormat('d F Y, H:i') . ' WIB' : '-'),
                        Placeholder::make('status')->label('Status')->content(fn($state) => match ($state) { 'pending' => '🟡 Pending', 'approved' => '✅ Disetujui', 'rejected' => '❌ Ditolak', default => $state }),
                        Placeholder::make('approved_by_name')->label('Disetujui Oleh')->content(fn($state) => $state ?? '-'),
                        Placeholder::make('description')->label('Keterangan')->content(fn($state) => $state ?? '-'),
                    ])
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Tutup'),
            ])
            ->defaultSort('date', 'desc');
    }

    protected function getTableQuery(): Builder
    {
        return Transaction::query()
            ->select('id', 'original_id', 'user_name', 'category_name', 'amount', 'date', 'status', 'description', 'type', 'created_at', 'approved_by_name');
    }
}
