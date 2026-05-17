<?php

namespace App\Filament\Parent\Pages;

use App\Models\Expense;
use App\Models\Income;
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
        return (string) md5($record->type . $record->original_id . $record->date);
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
                    ->modalDescription(fn($record) => 'Setujui transaksi ' . $record->category_name . ' - Rp ' . number_format((float) $record->amount, 0, ',', '.') . '?')
                    ->action(function ($record) {
                        $this->processApproval($record, 'approved');
                    }),

                TableAction::make('reject')
                    ->label('Tolak')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn($record) => $record->status === 'pending')
                    ->requiresConfirmation()
                    ->modalHeading('Tolak Transaksi')
                    ->modalDescription(fn($record) => 'Tolak transaksi ' . $record->category_name . ' - Rp ' . number_format((float) $record->amount, 0, ',', '.') . '?')
                    ->action(function ($record) {
                        $this->processApproval($record, 'rejected');
                    }),
            ])
            ->defaultSort('date', 'desc');
    }

    private function processApproval($record, string $status): void
    {
        $model = $record->type === 'Pemasukan'
            ? Income::find($record->original_id)
            : Expense::find($record->original_id);

        if (!$model) {
            Notification::make()
                ->title('Data tidak ditemukan!')
                ->danger()
                ->send();
            return;
        }

        $model->update([
            'status'      => $status,
            'approved_by' => auth()->id(),
        ]);

        Notification::make()
            ->title($status === 'approved' ? '✅ Transaksi Disetujui!' : '❌ Transaksi Ditolak!')
            ->body($record->category_name . ' — Rp ' . number_format((float) $record->amount, 0, ',', '.'))
            ->color($status === 'approved' ? 'success' : 'danger')
            ->send();
    }

    protected function getTableQuery(): Builder
    {
        $parentId = auth()->id();
        $childIds = User::where('parent_id', $parentId)->pluck('id')->toArray();

        if (empty($childIds)) {
            return Income::query()->whereRaw('1 = 0');
        }

        $incomes = DB::table('incomes')
            ->join('users', 'incomes.user_id', '=', 'users.id')
            ->join('categories', 'incomes.category_id', '=', 'categories.id')
            ->whereIn('incomes.user_id', $childIds)
            ->select(
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
                'expenses.id as original_id',
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
                'original_id',
                'user_name',
                'category_name',
                'amount',
                'date',
                'status',
                'description',
                'type',
                DB::raw("CONCAT(type, '-', original_id) as id")
            );
    }
}
