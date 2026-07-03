<?php

namespace App\Filament\Child\Pages;

use App\Exports\TransactionExport;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Actions\Action as HeaderAction;
use Filament\Forms\Components\Select;
use Filament\Pages\Page;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class TransactionView extends Page
{
    protected static ?string $navigationIcon  = 'heroicon-o-arrow-path';
    protected static ?string $navigationLabel = 'Riwayat Transaksi';
    protected static ?string $title           = 'Riwayat Transaksi';
    protected static ?int    $navigationSort  = 5;
    protected static string  $view            = 'filament.child.pages.transaction-view';

    // Filter properties
    public string $search       = '';
    public string $filterType   = 'all';
    public string $filterStatus = 'all';
    public string $dateFrom     = '';
    public string $dateTo       = '';
    public int    $currentPage  = 1;

    // Property penampung data modal detail transaksi
    public ?object $selectedTransaction = null;

    public function mount(): void
    {
        // Baca query string ?filterType=Pengeluaran dari link widget dashboard
        if (request()->has('filterType')) {
            $type = request()->query('filterType');
            if (in_array($type, ['Pemasukan', 'Pengeluaran', 'all'], true)) {
                $this->filterType = $type;
            }
        }
    }

    public function updatedSearch(): void       { $this->currentPage = 1; }
    public function updatedFilterType(): void   { $this->currentPage = 1; }
    public function updatedFilterStatus(): void { $this->currentPage = 1; }
    public function updatedDateFrom(): void     { $this->currentPage = 1; }
    public function updatedDateTo(): void       { $this->currentPage = 1; }

    public function gotoPage(int $page): void    { $this->currentPage = $page; }
    public function nextPage(int $last): void    { if ($this->currentPage < $last) $this->currentPage++; }
    public function previousPage(): void         { if ($this->currentPage > 1) $this->currentPage--; }

    public function resetFilters(): void
    {
        $this->search       = '';
        $this->filterType   = 'all';
        $this->filterStatus = 'all';
        $this->dateFrom     = '';
        $this->dateTo       = '';
        $this->currentPage  = 1;
        $this->selectedTransaction = null;
    }

    /**
     * Membuka modal detail transaksi berdasarkan ID gabungan (I-xxx / E-xxx)
     */
    public function showTransactionDetail(string $id): void
    {
        // Mengambil data item langsung dari Paginator halaman saat ini agar menghemat query
        $this->selectedTransaction = collect($this->getTransactions()->items())
            ->firstWhere('id', $id);
    }

    /**
     * Menutup kembali modal detail transaksi
     */
    public function closeTransactionDetail(): void
    {
        $this->selectedTransaction = null;
    }

    private function getAllIds(): array
    {
        $parentId = auth()->id();
        $childIds = User::where('parent_id', $parentId)->pluck('id')->toArray();
        return array_merge([$parentId], $childIds);
    }

    public function getTransactions()
    {
        $allIds = $this->getAllIds();
        $searchTerm = $this->search ? '%' . $this->search . '%' : null;

        // --- PARSING TANGGAL AMAN DI AWAL ---
        $dateFromFormatted = null;
        if (!empty($this->dateFrom) && trim($this->dateFrom) !== '') {
            $dateFromFormatted = str_contains($this->dateFrom, '/')
                ? Carbon::createFromFormat('d/m/Y', $this->dateFrom)->startOfDay()->toDateTimeString()
                : Carbon::parse($this->dateFrom)->startOfDay()->toDateTimeString();
        }

        $dateToFormatted = null;
        if (!empty($this->dateTo) && trim($this->dateTo) !== '') {
            $dateToFormatted = str_contains($this->dateTo, '/')
                ? Carbon::createFromFormat('d/m/Y', $this->dateTo)->endOfDay()->toDateTimeString()
                : Carbon::parse($this->dateTo)->endOfDay()->toDateTimeString();
        }

        // 1. QUERY PEMASUKAN (INCOMES)
        $incomes = DB::table('incomes')
            ->join('users', 'incomes.user_id', '=', 'users.id')
            ->join('categories', 'incomes.category_id', '=', 'categories.id')
            ->leftJoin('users as approver', 'incomes.approved_by', '=', 'approver.id')
            ->whereIn('incomes.user_id', $allIds)
            ->select(
                DB::raw("CONCAT('I-', incomes.id) as id"),
                'users.name as user_name',
                'categories.name as category_name',
                'incomes.amount',
                'incomes.date',
                'incomes.status',
                'incomes.description',
                'incomes.created_at',
                'approver.name as approved_by_name',
                DB::raw("'Pemasukan' as type")
            );

        // Filter search langsung di tabel incomes
        if ($searchTerm) {
            $incomes->where(function($q) use ($searchTerm) {
                $q->where('incomes.description', 'like', $searchTerm)
                  ->orWhere('categories.name', 'like', $searchTerm)
                  ->orWhere('users.name', 'like', $searchTerm)
                  ->orWhere(DB::raw("'Pemasukan'"), 'like', $searchTerm);
            });
        }

        // Filter tanggal langsung di tabel incomes
        if ($dateFromFormatted) {
            $incomes->where('incomes.date', '>=', $dateFromFormatted);
        }
        if ($dateToFormatted) {
            $incomes->where('incomes.date', '<=', $dateToFormatted);
        }

        // 2. QUERY PENGELUARAN (EXPENSES)
        $expenses = DB::table('expenses')
            ->join('users', 'expenses.user_id', '=', 'users.id')
            ->join('categories', 'expenses.category_id', '=', 'categories.id')
            ->leftJoin('users as approver', 'expenses.approved_by', '=', 'approver.id')
            ->whereIn('expenses.user_id', $allIds)
            ->select(
                DB::raw("CONCAT('E-', expenses.id) as id"),
                'users.name as user_name',
                'categories.name as category_name',
                'expenses.amount',
                'expenses.date',
                'expenses.status',
                'expenses.description',
                'expenses.created_at',
                'approver.name as approved_by_name',
                DB::raw("'Pengeluaran' as type")
            );

        // Filter search langsung di tabel expenses
        if ($searchTerm) {
            $expenses->where(function($q) use ($searchTerm) {
                $q->where('expenses.description', 'like', $searchTerm)
                  ->orWhere('categories.name', 'like', $searchTerm)
                  ->orWhere('users.name', 'like', $searchTerm)
                  ->orWhere(DB::raw("'Pengeluaran'"), 'like', $searchTerm);
            });
        }

        // Filter tanggal langsung di tabel expenses
        if ($dateFromFormatted) {
            $expenses->where('expenses.date', '>=', $dateFromFormatted);
        }
        if ($dateToFormatted) {
            $expenses->where('expenses.date', '<=', $dateToFormatted);
        }

        // 3. PENGGABUNGAN (UNION) DAN FILTER GLOBAL
        if ($this->filterType === 'Pemasukan') {
            $baseQuery = $incomes;
        } elseif ($this->filterType === 'Pengeluaran') {
            $baseQuery = $expenses;
        } else {
            $baseQuery = $incomes->unionAll($expenses);
        }

        $query = DB::query()->fromSub($baseQuery, 'transactions');

        // Menerapkan sisa filter status di query luar
        if ($this->filterStatus !== 'all') {
            $query->where('status', $this->filterStatus);
        }

        // Urutkan data setelah semua filter masuk resmi bersatu
        $query->orderByDesc('date')
              ->orderByDesc('created_at');

        // --- PAGINASI ---
        return $query->paginate(perPage: 10, pageName: 'page', page: $this->currentPage);
    }

    public function getStats(): array
    {
        $allIds = $this->getAllIds();
        $now    = Carbon::now();

        $totalIncome = DB::table('incomes')
            ->whereIn('user_id', $allIds)
            ->where('status', 'approved')
            ->whereMonth('date', $now->month)
            ->whereYear('date', $now->year)
            ->sum('amount');

        $totalExpense = DB::table('expenses')
            ->whereIn('user_id', $allIds)
            ->where('status', 'approved')
            ->whereMonth('date', $now->month)
            ->whereYear('date', $now->year)
            ->sum('amount');

        $totalPending = DB::table('incomes')
            ->whereIn('user_id', $allIds)
            ->where('status', 'pending')
            ->count()
            + DB::table('expenses')
            ->whereIn('user_id', $allIds)
            ->where('status', 'pending')
            ->count();

        return compact('totalIncome', 'totalExpense', 'totalPending');
    }
}
