<?php

namespace App\Livewire\Mobile;

use App\Exports\TransactionExport;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Maatwebsite\Excel\Facades\Excel;

class Laporan extends Component
{
    public string $periode    = '';
    public int    $bulan      = 0;
    public int    $tahun      = 0;
    public int    $durasiTren = 6;

    // Hanya dipakai kalau user adalah parent
    public string $scope = 'family';

    // Form export (menggantikan modal Filament Action form)
    public bool   $showExportSheet = false;
    public string $exportFormat    = 'pdf'; // 'pdf' | 'excel'
    public int    $exportMonth     = 0;
    public int    $exportYear      = 0;
    public string $exportType      = 'all';
    public string $exportScope     = 'family';

    private array $reportStatuses = ['approved', 'pending'];
    private int   $currentUserId  = 0;

    public function mount(): void
    {
        $this->currentUserId = (int) (Auth::id() ?? 0);
        $this->setPeriode(now()->format('Y-m'));
    }

    private function getCurrentUserId(): int
    {
        return $this->currentUserId ?: (int) (Auth::id() ?? 0);
    }

    public function isParent(): bool
    {
        return (bool) Auth::user()?->hasRole('parent');
    }

    public function updated(string $propertyName): void
    {
        if ($propertyName === 'periode') {
            $this->parsePeriode();
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

    /**
     * Opsi dropdown Periode: 10 bulan terakhir s/d bulan ini.
     */
    public function getPeriodeOptionsProperty(): array
    {
        $start = now()->subMonths(9)->startOfMonth();
        $end   = now()->startOfMonth();

        $options = [];
        for ($d = $start->copy(); $d->lte($end); $d->addMonth()) {
            $options[$d->format('Y-m')] = $d->translatedFormat('F Y');
        }

        return $options;
    }

    /**
     * Untuk parent: 'family' | 'me' | 'child_{id}'.
     * Untuk child/personal: selalu hanya dirinya sendiri.
     */
    public function getUserIds(): array
    {
        if (! $this->isParent()) {
            return [$this->getCurrentUserId()];
        }

        $parentId = $this->getCurrentUserId();

        return match (true) {
            $this->scope === 'family' => array_merge(
                [$parentId],
                User::where('parent_id', $parentId)->pluck('id')->toArray()
            ),
            $this->scope === 'me' => [$parentId],
            str_starts_with($this->scope, 'child_') => [(int) str_replace('child_', '', $this->scope)],
            default => [$parentId],
        };
    }

    public function getScopeOptionsProperty(): array
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
        $data    = [];

        for ($i = $this->durasiTren - 1; $i >= 0; $i--) {
            $date  = now()->startOfMonth()->subMonths($i);
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
        return DB::table('expenses')
            ->join('categories', 'expenses.category_id', '=', 'categories.id')
            ->whereIn('expenses.user_id', $this->getUserIds())
            ->whereMonth('expenses.date', $this->bulan)
            ->whereYear('expenses.date', $this->tahun)
            ->whereIn('expenses.status', $this->reportStatuses)
            ->groupBy('categories.name')
            ->select('categories.name as category', DB::raw('SUM(expenses.amount) as total'))
            ->orderByDesc('total')
            ->get()
            ->toArray();
    }

    public function getRingkasanBulanan(): array
    {
        $userIds = $this->getUserIds();
        $data    = [];

        for ($i = 5; $i >= 0; $i--) {
            $date      = now()->startOfMonth()->subMonths($i);
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

    /**
     * Buka bottom-sheet export, isi default dari filter yang lagi aktif.
     */
    public function openExportSheet(string $format): void
    {
        $this->exportFormat = $format;
        $this->exportMonth  = $this->bulan;
        $this->exportYear   = $this->tahun;
        $this->exportType   = 'all';
        $this->exportScope  = $this->isParent() ? $this->scope : 'me';
        $this->showExportSheet = true;
    }

    public function closeExportSheet(): void
    {
        $this->showExportSheet = false;
    }

    public function confirmExport(): mixed
    {
        return $this->doExport([
            'month' => $this->exportMonth,
            'year'  => $this->exportYear,
            'type'  => $this->exportType,
            'scope' => $this->isParent() ? $this->exportScope : 'me',
        ], $this->exportFormat);
    }

    private function resolveUserIdsForScope(string $scope): array
    {
        $parentId = $this->getCurrentUserId();

        if (! $this->isParent()) {
            return [$parentId];
        }

        return match (true) {
            $scope === 'me'     => [$parentId],
            $scope === 'family' => array_merge([$parentId], User::where('parent_id', $parentId)->pluck('id')->toArray()),
            str_starts_with($scope, 'child_') => [(int) str_replace('child_', '', $scope)],
            default => [$parentId],
        };
    }

    private function doExport(array $data, string $format): mixed
    {
        $userIds  = $this->resolveUserIdsForScope($data['scope']);
        $isParent = $this->isParent();

        $incomes = DB::table('incomes')
            ->join('users', 'incomes.user_id', '=', 'users.id')
            ->join('categories', 'incomes.category_id', '=', 'categories.id')
            ->whereIn('incomes.user_id', $userIds)
            ->whereMonth('incomes.date', $data['month'])
            ->whereYear('incomes.date', $data['year'])
            ->whereIn('incomes.status', $this->reportStatuses)
            ->select('users.name as user_name', 'categories.name as category_name', 'incomes.amount', 'incomes.date', 'incomes.status', 'incomes.description', DB::raw("'Pemasukan' as type"));

        $expenses = DB::table('expenses')
            ->join('users', 'expenses.user_id', '=', 'users.id')
            ->join('categories', 'expenses.category_id', '=', 'categories.id')
            ->whereIn('expenses.user_id', $userIds)
            ->whereMonth('expenses.date', $data['month'])
            ->whereYear('expenses.date', $data['year'])
            ->whereIn('expenses.status', $this->reportStatuses)
            ->select('users.name as user_name', 'categories.name as category_name', 'expenses.amount', 'expenses.date', 'expenses.status', 'expenses.description', DB::raw("'Pengeluaran' as type"));

        $transactions = $data['type'] !== 'all'
            ? ($data['type'] === 'Pemasukan' ? collect($incomes->get()) : collect($expenses->get()))
            : collect($incomes->get())->merge(collect($expenses->get()))->sortByDesc('date')->values();

        $monthName    = Carbon::create()->month($data['month'])->translatedFormat('F');
        $period       = "$monthName {$data['year']}";
        $title        = $isParent ? "Laporan Keuangan Keluarga — $period" : "Laporan Keuangan Saya — $period";
        $fileName     = $isParent ? "laporan-{$data['month']}-{$data['year']}" : "laporan-saya-{$data['month']}-{$data['year']}";
        $totalIncome  = $transactions->where('type', 'Pemasukan')->sum('amount');
        $totalExpense = $transactions->where('type', 'Pengeluaran')->sum('amount');

        $this->showExportSheet = false;

        if ($format === 'excel') {
            return Excel::download(new TransactionExport($transactions, $title), "$fileName.xlsx");
        }

        $pdf = Pdf::loadView('exports.transactions-pdf', [
            'transactions'  => $transactions,
            'title'         => $title,
            'period'        => $period,
            'userName'      => Auth::user()?->name ?? '',
            'totalIncome'   => $totalIncome,
            'totalExpense'  => $totalExpense,
            'showUserName'  => $isParent,
        ])->setPaper('a4', 'landscape');

        return response()->streamDownload(fn () => print($pdf->output()), "$fileName.pdf");
    }

    public function render()
    {
        return view('livewire.mobile.laporan')
            ->layout('layouts.mobile');
    }
}
