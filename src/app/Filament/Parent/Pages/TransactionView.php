<?php

namespace App\Filament\Parent\Pages;

use App\Models\Income;
use App\Models\User;
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
    protected static ?string $navigationLabel = 'Riwayat Transaksi';
    protected static ?string $title           = 'Riwayat Transaksi Keluarga';
    protected static ?int    $navigationSort  = 4;
    protected static string  $view            = 'filament.parent.pages.transaction-view';

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
                        Placeholder::make('type')
                            ->label('Tipe')
                            ->content(fn($state) => $state),

                        Placeholder::make('user_name')
                            ->label('Nama Anggota')
                            ->content(fn($state) => $state),

                        Placeholder::make('category_name')
                            ->label('Kategori')
                            ->content(fn($state) => $state),

                        Placeholder::make('amount')
                            ->label('Jumlah')
                            ->content(fn($state) => 'Rp ' . number_format((float) $state, 0, ',', '.')),

                        Placeholder::make('date')
                            ->label('Tanggal Transaksi')
                            ->content(fn($state) => \Carbon\Carbon::parse($state)->translatedFormat('d F Y')),

                        Placeholder::make('created_at')
                            ->label('Dibuat Pada')
                            ->content(fn($state) => $state
                                ? \Carbon\Carbon::parse($state)->translatedFormat('d F Y, H:i') . ' WIB'
                                : '-'
                            ),

                        Placeholder::make('status')
                            ->label('Status')
                            ->content(fn($state) => match ($state) {
                                'pending'  => '🟡 Pending',
                                'approved' => '✅ Disetujui',
                                'rejected' => '❌ Ditolak',
                                default    => $state,
                            }),

                        Placeholder::make('approved_by_name')
                            ->label('Disetujui Oleh')
                            ->content(fn($state) => $state ?? '-'),

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
        $parentId = auth()->id();
        $childIds = User::where('parent_id', $parentId)->pluck('id')->toArray();
        $allIds   = array_merge([$parentId], $childIds);

        $incomes = DB::table('incomes')
            ->join('users', 'incomes.user_id', '=', 'users.id')
            ->join('categories', 'incomes.category_id', '=', 'categories.id')
            ->leftJoin('users as approver', 'incomes.approved_by', '=', 'approver.id')
            ->whereIn('incomes.user_id', $allIds)
            ->select(
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
            ->whereIn('expenses.user_id', $allIds)
            ->select(
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
                'created_at',
                'approved_by_name',
                DB::raw("CONCAT(type, '-', original_id) as id")
            );
    }
}
