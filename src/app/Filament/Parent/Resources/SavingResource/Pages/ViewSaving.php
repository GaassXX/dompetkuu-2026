<?php

namespace App\Filament\Parent\Resources\SavingResource\Pages;

use App\Filament\Parent\Resources\SavingResource;
use App\Models\SavingDeposit;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Infolist;

class ViewSaving extends ViewRecord
{
    protected static string $resource = SavingResource::class;

     public function getTitle(): string
    {
        return $this->record->name;
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('deposit')
                ->label('Tambah Setoran')
                ->color('success')
                ->icon('heroicon-o-plus-circle')
                ->form([
                    TextInput::make('amount')
                        ->label('Jumlah Setoran')
                        ->prefix('Rp')
                        ->numeric()
                        ->required(),
                    DatePicker::make('date')
                        ->label('Tanggal')
                        ->default(now())
                        ->required(),
                    Textarea::make('note')
                        ->label('Catatan')
                        ->nullable(),
                ])
                ->action(function (array $data) {
                    SavingDeposit::create([
                        'saving_id' => $this->record->id,
                        'user_id'   => auth()->id(),
                        'amount'    => $data['amount'],
                        'date'      => $data['date'],
                        'note'      => $data['note'] ?? null,
                    ]);
                    $this->record->increment('current_amount', $data['amount']);
                    if ((float)$this->record->fresh()->current_amount >= (float)$this->record->target_amount) {
                        $this->record->update(['status' => 'completed']);
                    }
                    $this->refreshFormData(['current_amount', 'status']);
                }),

                 \Filament\Actions\EditAction::make()
            ->label('Edit')
            ->color('warning'),

        // ✅ Tambah Delete
        \Filament\Actions\DeleteAction::make()
            ->label('Hapus')
            ->successRedirectUrl(SavingResource::getUrl('index')),
    
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Section::make('Detail Tabungan')
                ->schema([
                    TextEntry::make('name')->label('Nama Tabungan'),
                    TextEntry::make('category')->label('Kategori')->badge(),
                    TextEntry::make('target_amount')->label('Target')
                        ->formatStateUsing(fn($state) => 'Rp ' . number_format((float)$state, 0, ',', '.')),
                    TextEntry::make('current_amount')->label('Terkumpul')
                        ->formatStateUsing(fn($state) => 'Rp ' . number_format((float)$state, 0, ',', '.')),
                    TextEntry::make('progress')->label('Progress')
                        ->state(fn() => $this->record->getProgressPercentage() . '%')
                        ->badge()
                        ->color(fn() => $this->record->getProgressPercentage() >= 100 ? 'success' : 'warning'),
                    TextEntry::make('target_date')->label('Target Waktu')->date('d M Y'),
                    TextEntry::make('status')->label('Status')->badge()
                        ->formatStateUsing(fn($state) => match($state) {
                            'active' => 'Aktif', 'completed' => 'Selesai', 'cancelled' => 'Dibatalkan', default => $state
                        })
                        ->color(fn($state) => match($state) {
                            'active' => 'success', 'completed' => 'info', 'cancelled' => 'danger', default => 'gray'
                        }),
                    TextEntry::make('description')->label('Deskripsi')->columnSpanFull(),
                ])
                ->columns(3),

            Section::make('Riwayat Setoran')
                ->description('Data terbaru di atas')
                ->schema([
                    \Filament\Infolists\Components\RepeatableEntry::make('deposits')
                        ->label('')
                        ->schema([
                            TextEntry::make('date')->label('Tanggal')->date('d M Y'),
                            TextEntry::make('amount')->label('Jumlah')
                                ->formatStateUsing(fn($state) => '+Rp ' . number_format((float)$state, 0, ',', '.'))
                                ->color('success'),
                            TextEntry::make('note')->label('Catatan')->default('-'),
                        ])
                        ->columns(3),
                ]),
        ]);
    }
}
