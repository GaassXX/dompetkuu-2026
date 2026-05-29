<?php

namespace App\Filament\Parent\Pages;

use App\Models\Expense;
use App\Models\Income;
use App\Models\Transaction;
use App\Models\User;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Tables\Actions\Action as TableAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ApprovalQueue extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon  = 'heroicon-o-clipboard-document-check';
    protected static ?string $navigationLabel = 'Persetujuan';
    protected static ?string $title           = 'Antrian Persetujuan';
    protected static ?int    $navigationSort  = 3;
    protected static string  $view            = 'filament.parent.pages.approval-queue';

    public function getTableRecordKey(Model $record): string
    {
        return (string) $record->id;
    }

    public function resolveTableRecord(string $key): ?Model
    {
        $prefix     = substr($key, 0, 1);
        $originalId = substr($key, 2);
        $type       = $prefix === 'I' ? 'Pemasukan' : 'Pengeluaran';

        $parentId = auth()->id();
        $childIds = User::where('parent_id', $parentId)->pluck('id')->toArray();
        if (empty($childIds)) return null;

        $incomes = DB::table('incomes')
            ->join('users', 'incomes.user_id', '=', 'users.id')
            ->join('categories', 'incomes.category_id', '=', 'categories.id')
            ->whereIn('incomes.user_id', $childIds)
            ->select(
                DB::raw("CONCAT('I-', incomes.id) as id"),
                'incomes.id as original_id',
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
            ->whereIn('expenses.user_id', $childIds)
            ->select(
                DB::raw("CONCAT('E-', expenses.id) as id"),
                'expenses.id as original_id',
                'users.name as user_name',
                'categories.name as category_name',
                'expenses.amount',
                'expenses.date',
                'expenses.status',
                'expenses.description',
                DB::raw("'Pengeluaran' as type")
            );

        return Transaction::fromSub($incomes->unionAll($expenses), 'transactions')
            ->select('id', 'original_id', 'user_name', 'category_name', 'amount', 'date', 'status', 'description', 'type')
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
                    ->label('Anak')
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
                TableAction::make('approve')
                    ->label('Setujui')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn($record) => $record->status === 'pending')
                    ->requiresConfirmation()
                    ->modalHeading('Setujui Transaksi')
                    ->modalDescription('Apakah Anda yakin ingin menyetujui transaksi ini?')
                    ->action(function ($record) {
                        $model = $record->type === 'Pemasukan'
                            ? Income::find($record->original_id)
                            : Expense::find($record->original_id);

                        if ($model) {
                            $model->update([
                                'status'      => 'approved',
                                'approved_by' => auth()->id(),
                            ]);

                            if ($record->type === 'Pengeluaran') {
                                \App\Services\BudgetAlertService::checkAndNotify($model->user_id, 90);
                            }

                            Notification::make()
                                ->title(' Transaksi Disetujui!')
                                ->body($record->category_name . ' — Rp ' . number_format((float) $record->amount, 0, ',', '.'))
                                ->success()
                                ->send();
                        }
                    }),

                TableAction::make('reject')
                    ->label('Tolak')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn($record) => $record->status === 'pending')
                    ->requiresConfirmation()
                    ->modalHeading('Tolak Transaksi')
                    ->modalDescription('Apakah Anda yakin ingin menolak transaksi ini?')
                    ->action(function ($record) {
                        $model = $record->type === 'Pemasukan'
                            ? Income::find($record->original_id)
                            : Expense::find($record->original_id);

                        if ($model) {
                            $model->update([
                                'status'      => 'rejected',
                                'approved_by' => auth()->id(),
                            ]);

                            Notification::make()
                                ->title(' Transaksi Ditolak!')
                                ->body($record->category_name . ' — Rp ' . number_format((float) $record->amount, 0, ',', '.'))
                                ->danger()
                                ->send();
                        }
                    }),
            ])
            ->defaultSort('date', 'desc');
    }

    protected function getTableQuery(): Builder
    {
        $parentId = auth()->id();
        $childIds = User::where('parent_id', $parentId)->pluck('id')->toArray();

        if (empty($childIds)) {
            return Transaction::query()->whereRaw('1 = 0');
        }

        $incomes = DB::table('incomes')
            ->join('users', 'incomes.user_id', '=', 'users.id')
            ->join('categories', 'incomes.category_id', '=', 'categories.id')
            ->whereIn('incomes.user_id', $childIds)
            ->select(
                DB::raw("CONCAT('I-', incomes.id) as id"),
                'incomes.id as original_id',
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
            ->whereIn('expenses.user_id', $childIds)
            ->select(
                DB::raw("CONCAT('E-', expenses.id) as id"),
                'expenses.id as original_id',
                'users.name as user_name',
                'categories.name as category_name',
                'expenses.amount',
                'expenses.date',
                'expenses.status',
                'expenses.description',
                DB::raw("'Pengeluaran' as type")
            );

        return Transaction::fromSub($incomes->unionAll($expenses), 'transactions')
            ->select('id', 'original_id', 'user_name', 'category_name', 'amount', 'date', 'status', 'description', 'type');
    }
}
