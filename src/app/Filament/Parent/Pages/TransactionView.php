<?php

namespace App\Filament\Parent\Pages;

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
    protected static ?string $title           = 'Riwayat Transaksi Keluarga';
    protected static ?int    $navigationSort  = 4;
    protected static string  $view            = 'filament.parent.pages.transaction-view';

    // Filter properties
    public string $search       = '';
    public string $filterType   = 'all';
    public string $filterStatus = 'all';
    public string $dateFrom     = '';
    public string $dateTo       = '';
    public int    $currentPage  = 1;

    // Property penampung data modal detail transaksi
    public ?object $selectedTransaction = null;

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

        $query = DB::table(DB::raw("({$baseQuery->toSql()}) as transactions"))
            ->mergeBindings($baseQuery);

        // Menerapkan sisa filter status di query luar
        if ($this->filterStatus !== 'all') {
            $query->where('status', $this->filterStatus);
        }

        // Urutkan data setelah semua filter masuk resmi bersatu
        $query->orderByDesc('date')
              ->orderByDesc('created_at');

        // --- PAGINASI MANUAL ---
        $perPage = 10;
        $total = $query->count();

        $items = $query->offset(($this->currentPage - 1) * $perPage)
            ->limit($perPage)
            ->get();

        return new \Illuminate\Pagination\LengthAwarePaginator(
            $items,
            $total,
            $perPage,
            $this->currentPage,
            ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath()]
        );
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

    protected function getHeaderActions(): array
    {
        $childOptions = ['family' => 'Seluruh Keluarga', 'me' => 'Data Saya Sendiri'];
        User::where('parent_id', auth()->id())->get()
            ->each(fn($c) => $childOptions['child_' . $c->id] = 'Anak: ' . $c->name);

        return [
            HeaderAction::make('export')
                ->label('Export Laporan')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->form([
                    Select::make('format')
                        ->label('Format')
                        ->options(['pdf' => 'PDF', 'excel' => 'Excel'])
                        ->default('pdf')->required(),
                    Select::make('scope')
                        ->label('Data')
                        ->options($childOptions)
                        ->default('family')->required(),
                    Select::make('month')
                        ->label('Bulan')
                        ->options(collect(range(1, 12))->mapWithKeys(fn($m) =>
                            [$m => Carbon::create()->month($m)->translatedFormat('F')]
                        ))
                        ->default(now()->month)->required(),
                    Select::make('year')
                        ->label('Tahun')
                        ->options(collect(range(now()->year - 2, now()->year))->mapWithKeys(fn($y) => [$y => $y]))
                        ->default(now()->year)->required(),
                    Select::make('type')
                        ->label('Tipe Transaksi')
                        ->options(['all' => 'Semua', 'Pemasukan' => 'Pemasukan', 'Pengeluaran' => 'Pengeluaran'])
                        ->default('all')->required(),
                ])
                ->action(function (array $data) {
                    $parentId = auth()->id();
                    $userIds  = match(true) {
                        $data['scope'] === 'me'                               => [$parentId],
                        $data['scope'] === 'family'                           => array_merge([$parentId], User::where('parent_id', $parentId)->pluck('id')->toArray()),
                        str_starts_with($data['scope'], 'child_') => [(int) str_replace('child_', '', $data['scope'])],
                        default                                   => [$parentId],
                    };

                    $incomes = DB::table('incomes')
                        ->join('users', 'incomes.user_id', '=', 'users.id')
                        ->join('categories', 'incomes.category_id', '=', 'categories.id')
                        ->whereIn('incomes.user_id', $userIds)
                        ->whereMonth('incomes.date', $data['month'])
                        ->whereYear('incomes.date', $data['year'])
                        ->select('users.name as user_name', 'categories.name as category_name', 'incomes.amount', 'incomes.date', 'incomes.status', 'incomes.description', DB::raw("'Pemasukan' as type"));

                    $expenses = DB::table('expenses')
                        ->join('users', 'expenses.user_id', '=', 'users.id')
                        ->join('categories', 'expenses.category_id', '=', 'categories.id')
                        ->whereIn('expenses.user_id', $userIds)
                        ->whereMonth('expenses.date', $data['month'])
                        ->whereYear('expenses.date', $data['year'])
                        ->select('users.name as user_name', 'categories.name as category_name', 'expenses.amount', 'expenses.date', 'expenses.status', 'expenses.description', DB::raw("'Pengeluaran' as type"));

                    $transactions = $data['type'] !== 'all'
                        ? ($data['type'] === 'Pemasukan' ? collect($incomes->get()) : collect($expenses->get()))
                        : collect($incomes->get())->merge(collect($expenses->get()))->sortByDesc('date')->values();

                    $monthName    = Carbon::create()->month($data['month'])->translatedFormat('F');
                    $period       = "$monthName {$data['year']}";
                    $title        = "Laporan Transaksi Keluarga — $period";
                    $fileName     = "laporan-keluarga-{$data['month']}-{$data['year']}";
                    $totalIncome  = $transactions->where('type', 'Pemasukan')->sum('amount');
                    $totalExpense = $transactions->where('type', 'Pengeluaran')->sum('amount');

                    if ($data['format'] === 'excel') {
                        return Excel::download(new TransactionExport($transactions, $title), "$fileName.xlsx");
                    }

                    $pdf = Pdf::loadView('exports.transactions-pdf', [
                        'transactions' => $transactions, 'title' => $title, 'period' => $period,
                        'userName' => auth()->user()->name, 'totalIncome' => $totalIncome,
                        'totalExpense' => $totalExpense, 'showUserName' => true,
                    ])->setPaper('a4', 'landscape');

                    return response()->streamDownload(fn() => print($pdf->output()), "$fileName.pdf");
                }),
        ];
    }
}
