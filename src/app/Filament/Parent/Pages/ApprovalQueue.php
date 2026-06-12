<?php

namespace App\Filament\Parent\Pages;

use App\Models\Expense;
use App\Models\Income;
use App\Models\Transaction;
use App\Models\User;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;

class ApprovalQueue extends Page
{
    protected static ?string $navigationIcon  = 'heroicon-o-clipboard-document-check';
    protected static ?string $navigationLabel = 'Persetujuan';
    protected static ?string $title           = 'Antrian Persetujuan';
    protected static ?int    $navigationSort  = 3;
    protected static string  $view            = 'filament.parent.pages.approval-queue';

    public string $filterStatus = 'pending';
    public string $filterType   = 'all';
    public int    $currentPage  = 1;

    public function setStatus(string $status): void
    {
        $this->filterStatus = $status;
        $this->currentPage  = 1;
    }

    public function setType(string $type): void
    {
        $this->filterType  = $type;
        $this->currentPage = 1;
    }

    public function gotoPage(int $page): void    { $this->currentPage = $page; }
    public function nextPage(int $last): void    { if ($this->currentPage < $last) $this->currentPage++; }
    public function previousPage(): void         { if ($this->currentPage > 1) $this->currentPage--; }

    private function getChildIds(): array
    {
        return User::where('parent_id', auth()->id())->pluck('id')->toArray();
    }

    public function getStats(): array
    {
        $childIds = $this->getChildIds();
        if (empty($childIds)) return [
            'total' => 0, 'pendingIncome' => 0,
            'pendingExpense' => 0, 'activeChildren' => 0,
        ];

        $total          = Income::whereIn('user_id', $childIds)->where('status', 'pending')->count()
                        + Expense::whereIn('user_id', $childIds)->where('status', 'pending')->count();
        $pendingIncome  = Income::whereIn('user_id', $childIds)->where('status', 'pending')->sum('amount');
        $pendingExpense = Expense::whereIn('user_id', $childIds)->where('status', 'pending')->sum('amount');
        $activeChildren = User::where('parent_id', auth()->id())
            ->whereHas('incomes', fn($q) => $q->where('status', 'pending'))
            ->orWhereHas('expenses', fn($q) => $q->where('status', 'pending'))
            ->where('parent_id', auth()->id())
            ->count();

        return compact('total', 'pendingIncome', 'pendingExpense', 'activeChildren');
    }

    public function getTransactions()
    {
        $childIds = $this->getChildIds();
        if (empty($childIds)) return collect();

        // 1. Ambil Query Dasar Incomes
        $incomes = DB::table('incomes')
            ->join('users', 'incomes.user_id', '=', 'users.id')
            ->join('categories', 'incomes.category_id', '=', 'categories.id')
            ->whereIn('incomes.user_id', $childIds)
            ->select([
                DB::raw("CONCAT('I-', incomes.id) as id"),
                'incomes.id as original_id',
                'users.id as user_id',
                'users.name as user_name',
                'categories.name as category_name',
                'incomes.amount',
                'incomes.date',
                'incomes.status',
                'incomes.description',
                'incomes.created_at',
                DB::raw("'Pemasukan' as type")
            ]);

        // 2. Ambil Query Dasar Expenses (Urutan select wajib sejajar)
        $expenses = DB::table('expenses')
            ->join('users', 'expenses.user_id', '=', 'users.id')
            ->join('categories', 'expenses.category_id', '=', 'categories.id')
            ->whereIn('expenses.user_id', $childIds)
            ->select([
                DB::raw("CONCAT('E-', expenses.id) as id"),
                'expenses.id as original_id',
                'users.id as user_id',
                'users.name as user_name',
                'categories.name as category_name',
                'expenses.amount',
                'expenses.date',
                'expenses.status',
                'expenses.description',
                'expenses.created_at',
                DB::raw("'Pengeluaran' as type")
            ]);

        // 3. Gabungkan Menggunakan fromSub Agar Binding Parameter Tidak Tertukar/Acak-Acakan
        if ($this->filterType === 'Pemasukan') {
            $baseQuery = $incomes;
        } elseif ($this->filterType === 'Pengeluaran') {
            $baseQuery = $expenses;
        } else {
            $baseQuery = $incomes->unionAll($expenses);
        }

        // Bungkus query ke dalam subquery bersih
        $query = DB::query()->fromSub($baseQuery, 'transactions')
            ->orderByDesc('date')
            ->orderByDesc('created_at');

        // 4. Jalankan Filter Global Pada Subquery Wrapper
        if ($this->filterStatus !== 'all') {
            $query->where('status', $this->filterStatus);
        }

        return $query->paginate(10, ['*'], 'page', $this->currentPage);
    }

    public function getCounts(): array
    {
        $childIds = $this->getChildIds();
        if (empty($childIds)) return ['all' => 0, 'pending' => 0, 'approved' => 0, 'rejected' => 0];

        $incomes  = DB::table('incomes')->whereIn('user_id', $childIds);
        $expenses = DB::table('expenses')->whereIn('user_id', $childIds);

        return [
            'all'      => $incomes->count() + $expenses->count(),
            'pending'  => $incomes->clone()->where('status', 'pending')->count()  + $expenses->clone()->where('status', 'pending')->count(),
            'approved' => $incomes->clone()->where('status', 'approved')->count() + $expenses->clone()->where('status', 'approved')->count(),
            'rejected' => $incomes->clone()->where('status', 'rejected')->count() + $expenses->clone()->where('status', 'rejected')->count(),
        ];
    }

    public function approve(string $id): void
    {
        $prefix     = substr($id, 0, 1);
        $originalId = substr($id, 2);

        $model = $prefix === 'I'
            ? Income::find($originalId)
            : Expense::find($originalId);

        if ($model) {
            $model->update(['status' => 'approved', 'approved_by' => auth()->id()]);

            if ($prefix === 'E') {
                \App\Services\BudgetAlertService::checkAndNotify($model->user_id, 90);
            }

            Notification::make()->title(' Transaksi Disetujui!')->success()->send();
        }
    }

    public function reject(string $id): void
    {
        $prefix     = substr($id, 0, 1);
        $originalId = substr($id, 2);

        $model = $prefix === 'I'
            ? Income::find($originalId)
            : Expense::find($originalId);

        if ($model) {
            $model->update(['status' => 'rejected', 'approved_by' => auth()->id()]);
            Notification::make()->title(' Transaksi Ditolak!')->danger()->send();
        }
    }
}
