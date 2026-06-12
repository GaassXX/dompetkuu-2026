<?php

namespace App\Filament\Child\Resources\IncomeResource\Pages;

use App\Filament\Child\Resources\IncomeResource;
use App\Models\Category;
use App\Models\Income;
use Filament\Actions\Action;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Carbon;

class ListIncomes extends Page
{
    protected static string $resource = IncomeResource::class;
    protected static string $view     = 'filament.child.resources.income-resource.pages.list-incomes';

    public string $filterCategory = 'all';
    public string $filterPeriod   = 'this_month';
    public int    $currentPage    = 1;

    public function getTitle(): string { return 'Pemasukan'; }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('create')
                ->label('Tambah Pemasukan')
                ->icon('heroicon-o-plus')
                ->color('warning')
                ->url(IncomeResource::getUrl('create')),
        ];
    }

    public function setCategory(string $category): void
    {
        $this->filterCategory = $category;
        $this->currentPage    = 1;
    }

    public function setPeriod(string $period): void
    {
        $this->filterPeriod = $period;
        $this->currentPage  = 1;
    }

    public function nextPage(int $lastPage): void
    {
        if ($this->currentPage < $lastPage) $this->currentPage++;
    }

    public function previousPage(): void
    {
        if ($this->currentPage > 1) $this->currentPage--;
    }

    public function gotoPage(int $page): void
    {
        $this->currentPage = $page;
    }

    private function getDateRange(): array
    {
        $now = Carbon::now();
        return match($this->filterPeriod) {
            'this_month' => [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()],
            'last_month' => [$now->copy()->subMonth()->startOfMonth(), $now->copy()->subMonth()->endOfMonth()],
            'this_year'  => [$now->copy()->startOfYear(), $now->copy()->endOfYear()],
            default      => [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()],
        };
    }

    public function getStats(): array
    {
        $userId               = auth()->id();
        [$startDate,$endDate] = $this->getDateRange();
        $now                  = Carbon::now();

        $totalThisMonth = Income::where('user_id', $userId)
            ->where('status', 'approved')
            ->whereBetween('date', [$startDate, $endDate])
            ->sum('amount');

        $lastStart      = $startDate->copy()->subMonth();
        $lastEnd        = $endDate->copy()->subMonth();
        $totalLastMonth = Income::where('user_id', $userId)
            ->where('status', 'approved')
            ->whereBetween('date', [$lastStart, $lastEnd])
            ->sum('amount');

        $percentChange = 0;
        $isHigher      = false;
        if ($totalLastMonth > 0) {
            $percentChange = round((($totalThisMonth - $totalLastMonth) / $totalLastMonth) * 100, 1);
            $isHigher      = $percentChange > 0;
        }

        // Rata-rata pemasukan (dari 30 transaksi terakhir)
        $last30 = Income::where('user_id', $userId)
            ->where('status', 'approved')
            ->orderByDesc('date')
            ->limit(30)
            ->sum('amount');
        $count30 = Income::where('user_id', $userId)
            ->where('status', 'approved')
            ->orderByDesc('date')
            ->limit(30)
            ->count();
        $avgIncome = $count30 > 0 ? round($last30 / $count30) : 0;

        // Tren mingguan (7 hari terakhir)
        $weeklyData = [];
        for ($i = 6; $i >= 0; $i--) {
            $day           = Carbon::now()->subDays($i);
            $weeklyData[]  = [
                'day'    => $day->format('D'),
                'amount' => Income::where('user_id', $userId)
                    ->where('status', 'approved')
                    ->whereDate('date', $day)
                    ->sum('amount'),
            ];
        }
        $maxWeekly = max(array_column($weeklyData, 'amount')) ?: 1;

        return compact(
            'totalThisMonth', 'totalLastMonth', 'percentChange', 'isHigher',
            'avgIncome', 'count30', 'weeklyData', 'maxWeekly'
        );
    }

    public function getIncomes()
    {
        $userId               = auth()->id();
        [$startDate,$endDate] = $this->getDateRange();

        $query = Income::where('user_id', $userId)
            ->with('category')
            ->whereBetween('date', [$startDate, $endDate])
            ->orderByDesc('date');

        if ($this->filterCategory !== 'all') {
            $query->where('category_id', $this->filterCategory);
        }

        return $query->paginate(10, ['*'], 'page', $this->currentPage);
    }

    public function getCategories()
    {
        return Category::where('type', 'income')->get();
    }
    public function deleteIncome(int $id): void
{
    $income = \App\Models\Income::where('id', $id)
        ->where('user_id', auth()->id())
        ->firstOrFail();

    if ($income->status === 'approved') {
        $parentId = auth()->user()->parent_id;

        if (!$parentId) {
            $income->delete();
            return;
        }

        $parent   = \App\Models\User::find($parentId);
        $userName = auth()->user()->name;
        $desc     = $income->description ?? optional($income->category)->name ?? '-';
        $amount   = number_format($income->amount, 0, ',', '.');

        // Simpan request (optional, skip kalau tabel belum ada)
        try {
            \App\Models\TransactionRequest::create([
                'child_id'   => auth()->id(),
                'parent_id'  => $parentId,
                'type'       => 'delete',
                'model_type' => 'income',
                'model_id'   => $income->id,
                'old_data'   => $income->toArray(),
                'status'     => 'pending',
            ]);
        } catch (\Exception $e) {
            // Lanjut meski tabel belum ada
        }

        // Kirim notifikasi ke parent
        \Illuminate\Support\Facades\DB::table('notifications')->insert([
            'id'              => \Illuminate\Support\Str::uuid()->toString(),
            'type'            => 'Filament\Notifications\DatabaseNotification',
            'notifiable_type' => 'App\Models\User',
            'notifiable_id'   => $parent->id,
            'data'            => json_encode([
                'title'   => '🗑️ Permintaan Hapus Pemasukan',
                'body'    => $userName . ' minta izin hapus: ' . $desc . ' — Rp ' . $amount,
                'color'   => 'warning',
                'actions' => [[
                    'name'   => 'review',
                    'label'  => 'Tinjau',
                    'url'    => '/parent/approval-queue',
                    'button' => true,
                ]],
            ]),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        \Filament\Notifications\Notification::make()
            ->title('Permintaan Terkirim')
            ->body('Permintaan hapus dikirim ke orang tua.')
            ->info()
            ->send();

        return;
    }

    $income->delete();

    \Filament\Notifications\Notification::make()
        ->title('Berhasil dihapus')
        ->success()
        ->send();
}
}
