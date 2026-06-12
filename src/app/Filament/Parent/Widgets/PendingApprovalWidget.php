<?php

namespace App\Filament\Parent\Widgets;

use App\Models\Expense;
use App\Models\Income;
use App\Models\TransactionRequest;
use App\Models\User;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PendingApprovalWidget extends Widget
{
    protected static ?int $sort = 0;
    protected static string $view = 'filament.parent.widgets.pending-approval-widget';

    public function getColumnSpan(): int|string|array
    {
        return 'full';
    }

    public static function canView(): bool
    {
        $parentId = auth()->id();
        $childIds = User::where('parent_id', $parentId)->pluck('id')->toArray();

        $pendingIncome   = !empty($childIds) ? Income::whereIn('user_id', $childIds)->where('status', 'pending')->count() : 0;
        $pendingExpense  = !empty($childIds) ? Expense::whereIn('user_id', $childIds)->where('status', 'pending')->count() : 0;
        $pendingRequests = TransactionRequest::where('parent_id', $parentId)->where('status', 'pending')->count();

        return ($pendingIncome + $pendingExpense + $pendingRequests) > 0;
    }

    public array $pendingItems = [];

    public function mount(): void
    {
        $this->loadData();
    }

    private function loadData(): void
    {
        $childIds = User::where('parent_id', auth()->id())->pluck('id')->toArray();

        $incomes = !empty($childIds) ? Income::whereIn('user_id', $childIds)
            ->where('status', 'pending')
            ->with(['user', 'category'])
            ->latest('date')->take(5)->get()
            ->map(fn($item) => [
                'id'       => $item->id,
                'type'     => 'Pemasukan',
                'request'  => false,
                'name'     => $item->user->name,
                'category' => $item->category->name,
                'amount'   => (float) $item->amount,
                'date'     => $item->date?->format('d M Y'),
            ])->toArray() : [];

        $expenses = !empty($childIds) ? Expense::whereIn('user_id', $childIds)
            ->where('status', 'pending')
            ->with(['user', 'category'])
            ->latest('date')->take(5)->get()
            ->map(fn($item) => [
                'id'       => $item->id,
                'type'     => 'Pengeluaran',
                'request'  => false,
                'name'     => $item->user->name,
                'category' => $item->category->name,
                'amount'   => (float) $item->amount,
                'date'     => $item->date?->format('d M Y'),
            ])->toArray() : [];

        // ✅ Tambah delete requests
        $deleteRequests = TransactionRequest::where('parent_id', auth()->id())
            ->where('status', 'pending')
            ->with('child')
            ->orderByDesc('created_at')->get()
            ->map(fn($req) => [
                'id'       => $req->id,
                'type'     => '🗑️ Hapus ' . ucfirst($req->model_type),
                'request'  => true,
                'name'     => $req->child->name,
                'category' => $req->old_data['description'] ?? '-',
                'amount'   => (float) ($req->old_data['amount'] ?? 0),
                'date'     => $req->created_at->format('d M Y'),
            ])->toArray();

        $merged = array_merge($incomes, $expenses, $deleteRequests);
        usort($merged, fn($a, $b) => strcmp($b['date'], $a['date']));
        $this->pendingItems = array_slice($merged, 0, 10);
    }

    public function approveRequest(int $id): void
    {
        $req = TransactionRequest::findOrFail($id);

        $model = $req->model_type === 'income'
            ? Income::find($req->model_id)
            : Expense::find($req->model_id);
        $model?->delete();

        $req->update(['status' => 'approved']);

        DB::table('notifications')->insert([
            'id'              => Str::uuid()->toString(),
            'type'            => 'Filament\Notifications\DatabaseNotification',
            'notifiable_type' => 'App\Models\User',
            'notifiable_id'   => $req->child_id,
            'data'            => json_encode([
                'title' => '✅ Permintaan Disetujui',
                'body'  => 'Orang tua menyetujui permintaan hapus transaksi Anda.',
                'color' => 'success',
            ]),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        \Filament\Notifications\Notification::make()
            ->title('Permintaan disetujui')->success()->send();

        $this->loadData();
    }

    public function rejectRequest(int $id): void
    {
        $req = TransactionRequest::findOrFail($id);
        $req->update(['status' => 'rejected']);

        DB::table('notifications')->insert([
            'id'              => Str::uuid()->toString(),
            'type'            => 'Filament\Notifications\DatabaseNotification',
            'notifiable_type' => 'App\Models\User',
            'notifiable_id'   => $req->child_id,
            'data'            => json_encode([
                'title' => '❌ Permintaan Ditolak',
                'body'  => 'Orang tua menolak permintaan hapus transaksi Anda.',
                'color' => 'danger',
            ]),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        \Filament\Notifications\Notification::make()
            ->title('Permintaan ditolak')->danger()->send();

        $this->loadData();
    }
}
