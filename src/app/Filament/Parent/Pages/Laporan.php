<?php

namespace App\Filament\Parent\Pages;

use App\Exports\TransactionExport;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Actions\Action as HeaderAction;
use Filament\Forms\Components\Select;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class Laporan extends Page
{
    protected static ?string $navigationIcon  = 'heroicon-o-document-chart-bar';
    protected static ?string $navigationLabel = 'Laporan';
    protected static ?int    $navigationSort  = 5;
    protected static string  $view            = 'filament.parent.pages.laporan';

    public int    $bulan       = 0;
    public int    $tahun       = 0;
    public string $periode     = '';
    public string $scope       = 'family';
    public int    $durasiTren  = 6;
    private array $reportStatuses = ['approved', 'pending'];
    private int $currentUserId = 0;

    public function mount(): void
    {
        $this->currentUserId = (int) (Auth::id() ?? 0);
        $this->setPeriode(now()->format('Y-m'));
    }

    private function getCurrentUserId(): int
    {
        return $this->currentUserId ?: (int) (Auth::id() ?? 0);
    }

    public function updated(string $propertyName): void
    {
        if ($propertyName === 'periode') {
            $this->parsePeriode();
        }
        if (in_array($propertyName, ['periode', 'scope', 'durasiTren'], true)) {
            $this->dispatch('laporan-refresh');
        }
    }

    private function setPeriode(string $value): void
    {
        $this->periode = $value;
        $this->parsePeriode();
    }

    private function parsePeriode(): void
    {
        $parts = explode('-', $this->periode);
        $this->tahun = (int) ($parts[0] ?? now()->year);
        $this->bulan = (int) ($parts[1] ?? now()->month);
    }

    public function getUserIds(): array
    {
        $parentId = $this->getCurrentUserId();

        return match (true) {
            $this->scope === 'family' => array_merge(
                [$parentId],
                User::where('parent_id', $parentId)->pluck('id')->toArray()
            ),
            $this->scope === 'me'     => [$parentId],
            str_starts_with($this->scope, 'child_') => [(int) str_replace('child_', '', $this->scope)],
            default                   => [$parentId],
        };
    }

    public function getTotalPemasukan(): float
    {
        return (float) DB::table('incomes')
            ->whereIn('user_id', $this->getUserIds())
            ->whereMonth('date', $this->bulan)
            ->whereYear('date', $this->tahun)
            ->whereIn('status', $this->reportStatuses)
            ->sum('amount');
    }

    public function getTotalPengeluaran(): float
    {
        return (float) DB::table('expenses')
            ->whereIn('user_id', $this->getUserIds())
            ->whereMonth('date', $this->bulan)
            ->whereYear('date', $this->tahun)
            ->whereIn('status', $this->reportStatuses)
            ->sum('amount');
    }

    public function getSaldoBersih(): float
    {
        return $this->getTotalPemasukan() - $this->getTotalPengeluaran();
    }

    public function getTrenPengeluaran(): array
    {
        $userIds = $this->getUserIds();
        $data = [];

        for ($i = $this->durasiTren - 1; $i >= 0; $i--) {
            $date = now()->startOfMonth()->subMonths($i);
            $total = (float) DB::table('expenses')
                ->whereIn('user_id', $userIds)
                ->whereMonth('date', $date->month)
                ->whereYear('date', $date->year)
                ->whereIn('status', $this->reportStatuses)
                ->sum('amount');

            $data[] = [
                'bulan' => $date->translatedFormat('M Y'),
                'total' => $total,
            ];
        }

        return $data;
    }

    public function getPengeluaranPerKategori(): array
    {
        $userIds = $this->getUserIds();

        $categories = DB::table('expenses')
            ->join('categories', 'expenses.category_id', '=', 'categories.id')
            ->whereIn('expenses.user_id', $userIds)
            ->whereMonth('expenses.date', $this->bulan)
            ->whereYear('expenses.date', $this->tahun)
            ->whereIn('expenses.status', $this->reportStatuses)
            ->groupBy('categories.name')
            ->select('categories.name as category', DB::raw('SUM(expenses.amount) as total'))
            ->orderByDesc('total')
            ->get()
            ->toArray();

        return $categories;
    }

    public function getRingkasanBulanan(): array
    {
        $userIds = $this->getUserIds();
        $data = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = now()->startOfMonth()->subMonths($i);
            $bulanNama = $date->translatedFormat('F Y');

            $pemasukan = (float) DB::table('incomes')
                ->whereIn('user_id', $userIds)
                ->whereMonth('date', $date->month)
                ->whereYear('date', $date->year)
                ->whereIn('status', $this->reportStatuses)
                ->sum('amount');

            $pengeluaran = (float) DB::table('expenses')
                ->whereIn('user_id', $userIds)
                ->whereMonth('date', $date->month)
                ->whereYear('date', $date->year)
                ->whereIn('status', $this->reportStatuses)
                ->sum('amount');

            $data[] = [
                'bulan'       => $bulanNama,
                'pemasukan'   => $pemasukan,
                'pengeluaran' => $pengeluaran,
                'selisih'     => $pemasukan - $pengeluaran,
            ];
        }

        return $data;
    }

    public function getScopeOptions(): array
    {
        $options = [
            'family' => 'Semua Keluarga',
            'me'     => 'Saya Sendiri',
        ];

        foreach (User::where('parent_id', $this->getCurrentUserId())->orderBy('name')->get() as $child) {
            $options['child_' . $child->id] = $child->name;
        }

        return $options;
    }

    public function getTotalTrenPengeluaran(): float
    {
        return array_sum(array_column($this->getTrenPengeluaran(), 'total'));
    }

    protected function getHeaderActions(): array
    {
        $childOptions = $this->getScopeOptions();

        return [
            HeaderAction::make('export_pdf')
                ->label('Export PDF')
                ->color('danger')
                ->icon('heroicon-o-document-arrow-down')
                ->form($this->getExportFormFields($childOptions))
                ->action(fn (array $data) => $this->doExport($data, 'pdf')),

            HeaderAction::make('export_excel')
                ->label('Export Excel')
                ->color('success')
                ->icon('heroicon-o-table-cells')
                ->form($this->getExportFormFields($childOptions))
                ->action(fn (array $data) => $this->doExport($data, 'excel')),
        ];
    }

    private function getExportFormFields(array $scopeOptions): array
    {
        return [
            Select::make('scope')
                ->label('Data')
                ->options($scopeOptions)
                ->default($this->scope)
                ->required(),
            Select::make('month')
                ->label('Bulan')
                ->options(collect(range(1, 12))->mapWithKeys(fn($m) =>
                    [$m => Carbon::create()->month($m)->translatedFormat('F')]
                ))
                ->default($this->bulan)
                ->required(),
            Select::make('year')
                ->label('Tahun')
                ->options(collect(range(now()->year - 2, now()->year + 1))->mapWithKeys(fn($y) => [$y => $y]))
                ->default($this->tahun)
                ->required(),
            Select::make('type')
                ->label('Tipe Transaksi')
                ->options(['all' => 'Semua', 'Pemasukan' => 'Pemasukan', 'Pengeluaran' => 'Pengeluaran'])
                ->default('all')
                ->required(),
        ];
    }

    private function doExport(array $data, string $format): mixed
    {
        $parentId = Auth::id();
        $parentId = $this->getCurrentUserId();
        $userIds  = match (true) {
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
        $title        = "Laporan Keuangan Keluarga — $period";
        $fileName     = "laporan-{$data['month']}-{$data['year']}";
        $totalIncome  = $transactions->where('type', 'Pemasukan')->sum('amount');
        $totalExpense = $transactions->where('type', 'Pengeluaran')->sum('amount');

        if ($format === 'excel') {
            return Excel::download(new TransactionExport($transactions, $title), "$fileName.xlsx");
        }

        $pdf = Pdf::loadView('exports.transactions-pdf', [
            'transactions' => $transactions, 'title' => $title, 'period' => $period,
            'userName' => Auth::user()?->name ?? '', 'totalIncome' => $totalIncome,
            'totalExpense' => $totalExpense, 'showUserName' => true,
        ])->setPaper('a4', 'landscape');

        return response()->streamDownload(fn() => print($pdf->output()), "$fileName.pdf");
    }
}
