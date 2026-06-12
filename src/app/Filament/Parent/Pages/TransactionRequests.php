<?php

namespace App\Filament\Parent\Pages;

use App\Models\Expense;
use App\Models\Income;
use App\Models\TransactionRequest;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class TransactionRequests extends Page
{
    protected static ?string $navigationIcon  = 'heroicon-o-inbox-arrow-down';
    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $navigationLabel = 'Permintaan Hapus';
    protected static ?string $title           = 'Permintaan Hapus Transaksi';
    protected static ?int    $navigationSort  = 5;
    protected static string  $view            = 'filament.parent.pages.transaction-requests';

    public function getRequests()
    {
        return TransactionRequest::where('parent_id', auth()->id())
            ->where('status', 'pending')
            ->with('child')
            ->orderByDesc('created_at')
            ->get();
    }

    public function approve(int $id): void
    {
        $req = TransactionRequest::findOrFail($id);

        if ($req->type === 'delete') {
            $model = $req->model_type === 'income'
                ? Income::find($req->model_id)
                : Expense::find($req->model_id);

            $model?->delete();
        }

        $req->update(['status' => 'approved']);

        // Notif ke child
        \Filament\Notifications\Notification::make()
            ->title('✅ Permintaan Disetujui')
            ->body('Orang tua menyetujui permintaan hapus transaksi Anda.')
            ->success()
            ->sendToDatabase($req->child);

        Notification::make()->title('Disetujui')->success()->send();
    }

    public function reject(int $id): void
    {
        $req = TransactionRequest::findOrFail($id);
        $req->update(['status' => 'rejected']);

        \Filament\Notifications\Notification::make()
            ->title('❌ Permintaan Ditolak')
            ->body('Orang tua menolak permintaan hapus transaksi Anda.')
            ->danger()
            ->sendToDatabase($req->child);

        Notification::make()->title('Ditolak')->danger()->send();
    }
}
