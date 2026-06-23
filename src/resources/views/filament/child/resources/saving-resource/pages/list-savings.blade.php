@php
    $stats          = $this->getStats();
    $activeSavings  = $stats['activeSavings'];
    $totalSaved     = $stats['totalSaved'];
    $completed      = $stats['completed'];
    $remaining      = $stats['remaining'];
    $percentChange  = $stats['percentChange'];
    $isPositive     = $stats['isPositive'];
    $recentDeposits = $this->getRecentDeposits();

    // Helper format angka Indonesia
    $formatRupiah = function($number) {
    $number = (float) $number;
    if ($number >= 1000000000) {
        $val = $number / 1000000000;
        return 'Rp ' . ($val == floor($val) ? number_format($val, 0, ',', '.') : number_format($val, 1, ',', '.')) . ' M';
    } elseif ($number >= 1000000) {
        $val = $number / 1000000;
        return 'Rp ' . ($val == floor($val) ? number_format($val, 0, ',', '.') : number_format($val, 1, ',', '.')) . ' Jt';
    } elseif ($number >= 1000) {
        $val = $number / 1000;
        return 'Rp ' . ($val == floor($val) ? number_format($val, 0, ',', '.') : number_format($val, 1, ',', '.')) . ' Rb';
    }
    return 'Rp ' . number_format($number, 0, ',', '.') ;
    };

    $categoryIcons = [
        'Liburan'    => ['bg' => 'bg-blue-50',   'color' => 'text-blue-500',   'svg' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 12 3.269 3.125A59.769 59.769 0 0 1 21.485 12 59.768 59.768 0 0 1 3.27 20.875L5.999 12Zm0 0h7.5"/>'],
        'Properti'   => ['bg' => 'bg-green-50',  'color' => 'text-green-500',  'svg' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25"/>'],
        'Pribadi'    => ['bg' => 'bg-purple-50', 'color' => 'text-purple-500', 'svg' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z"/>'],
        'Pendidikan' => ['bg' => 'bg-yellow-50', 'color' => 'text-yellow-500', 'svg' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 3.741-3.342M6.75 15a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Zm0 0v-3.675A55.378 55.378 0 0 1 12 8.443m-7.007 11.55A5.981 5.981 0 0 0 6.75 15.75v-1.5"/>'],
        'Kendaraan'  => ['bg' => 'bg-orange-50', 'color' => 'text-orange-500', 'svg' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 0 1-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 0 0-3.213-9.193 2.056 2.056 0 0 0-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 0 0-10.026 0 1.106 1.106 0 0 0-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12"/>'],
        'Kesehatan'  => ['bg' => 'bg-pink-50',   'color' => 'text-pink-500',   'svg' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z"/>'],
        'Elektronik' => ['bg' => 'bg-indigo-50', 'color' => 'text-indigo-500', 'svg' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17.25v1.007a3 3 0 0 1-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0 1 15 18.257V17.25m6-12V15a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 15V5.25m18 0A2.25 2.25 0 0 0 18.75 3H5.25A2.25 2.25 0 0 0 3 5.25m18 0H3"/>'],
        'Lainnya'    => ['bg' => 'bg-gray-100',  'color' => 'text-gray-500',   'svg' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.75 9.776c.112-.017.227-.026.344-.026h15.812c.117 0 .232.009.344.026m-16.5 0a2.25 2.25 0 0 0-1.883 2.542l.857 6a2.25 2.25 0 0 0 2.227 1.932H19.05a2.25 2.25 0 0 0 2.227-1.932l.857-6a2.25 2.25 0 0 0-1.883-2.542m-16.5 0V6A2.25 2.25 0 0 1 6 3.75h3.879a1.5 1.5 0 0 1 1.06.44l2.122 2.12a1.5 1.5 0 0 0 1.06.44H18A2.25 2.25 0 0 1 20.25 9v.776"/>'],
    ];
@endphp

<x-filament-panels::page>
    <div class="space-y-6">

        {{-- STATS CARDS --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Total Tabungan</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">
                            Rp {{ number_format($totalSaved, 0, ',', '.') }}
                        </p>
                        <p class="text-xs mt-1 {{ $isPositive ? 'text-green-600' : 'text-red-500' }}">
                            {{ $isPositive ? '↑' : '↓' }} {{ $isPositive ? '+' : '' }}{{ $percentChange }}% dari bulan lalu
                        </p>
                    </div>
                    <div style="width:48px;height:48px;background:#fffbeb;border-radius:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <svg style="width:24px;height:24px;color:#f59e0b;" fill="none" stroke="#f59e0b" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.172-.879-1.172-2.303 0-3.182.53-.4 1.21-.62 1.894-.62M12 4v1m0 14v1M6 12H4m16 0h-2"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Target Tercapai</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $completed }} Target</p>
                        <p class="text-xs text-gray-400 mt-1">Minggu ini</p>
                    </div>
                    <div style="width:48px;height:48px;background:#f0fdf4;border-radius:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <svg style="width:24px;height:24px;" fill="none" stroke="#22c55e" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Sisa Target</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">
                            Rp {{ number_format($remaining, 0, ',', '.') }}
                        </p>
                        <p class="text-xs text-gray-400 mt-1">{{ $activeSavings->count() }} target tersisa</p>
                    </div>
                    <div style="width:48px;height:48px;background:#fff1f2;border-radius:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <svg style="width:24px;height:24px;" fill="none" stroke="#f87171" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3v1.5M3 21v-6m0 0 2.77-.693a9 9 0 0 1 6.208.682l.108.054a9 9 0 0 0 6.086.71l3.114-.732a48.524 48.524 0 0 1-.005-10.499l-3.11.732a9 9 0 0 1-6.085-.711l-.108-.054a9 9 0 0 0-6.208-.682L3 4.5M3 15V4.5"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        
        {{-- ACTIVE SAVINGS CARDS --}}
<div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
    <div class="flex items-center justify-between mb-6">
        <h3 class="text-base font-semibold text-gray-900 dark:text-white">Target Tabungan Aktif</h3>
        <a href="{{ \App\Filament\Child\Resources\SavingResource::getUrl('all') }}"
           class="text-sm text-amber-500 hover:text-amber-600 font-medium">Lihat Semua →</a>
    </div>

            @if($activeSavings->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @foreach($activeSavings->take(3) as $saving)
                @php
                    $progress      = $saving->getProgressPercentage();
                    $progressHex   = $progress >= 100 ? '#22c55e' : ($progress >= 60 ? '#facc15' : '#f59e0b');
                    $catIcon       = $categoryIcons[$saving->category] ?? $categoryIcons['Lainnya'];

                    $iconBgColors  = [
                        'bg-blue-50'   => '#eff6ff',
                        'bg-green-50'  => '#f0fdf4',
                        'bg-purple-50' => '#faf5ff',
                        'bg-yellow-50' => '#fefce8',
                        'bg-orange-50' => '#fff7ed',
                        'bg-pink-50'   => '#fdf2f8',
                        'bg-indigo-50' => '#eef2ff',
                        'bg-gray-100'  => '#f3f4f6',
                    ];
                    $iconStrokeColors = [
                        'text-blue-500'   => '#3b82f6',
                        'text-green-500'  => '#22c55e',
                        'text-purple-500' => '#a855f7',
                        'text-yellow-500' => '#eab308',
                        'text-orange-500' => '#f97316',
                        'text-pink-500'   => '#ec4899',
                        'text-indigo-500' => '#6366f1',
                        'text-gray-500'   => '#6b7280',
                    ];
                    $iconBg     = $iconBgColors[$catIcon['bg']] ?? '#f3f4f6';
                    $iconStroke = $iconStrokeColors[$catIcon['color']] ?? '#6b7280';
                @endphp

                <div style="background:#fff;border:1px solid #f3f4f6;border-radius:16px;padding:20px;" class="hover:shadow-md transition-all dark:bg-gray-700/50 dark:border-gray-600">

                    {{-- Icon + Badge --}}
                    <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:16px;">
                        <div style="width:52px;height:52px;background:{{ $iconBg }};border-radius:14px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <svg style="width:26px;height:26px;" fill="none" stroke="{{ $iconStroke }}" viewBox="0 0 24 24">
                                {!! $catIcon['svg'] !!}
                            </svg>
                        </div>
                        <span style="font-size:11px;font-weight:700;letter-spacing:0.08em;color:#9ca3af;text-transform:uppercase;margin-top:4px;">
                            {{ $saving->category }}
                        </span>
                    </div>

                    {{-- Name & Target --}}
                    <p style="font-weight:700;font-size:15px;color:#111827;margin-bottom:4px;" class="dark:text-white">
                        {{ $saving->name }}
                    </p>
                    <p style="font-size:12px;color:#9ca3af;margin-bottom:16px;">
                        Target: Rp {{ number_format($saving->target_amount, 0, ',', '.') }}
                    </p>

                    {{-- Progress --}}
                    <div style="margin-bottom:16px;">
                        <div style="display:flex;justify-content:space-between;font-size:12px;margin-bottom:6px;">
                            <span style="color:#6b7280;">Progress: {{ $progress }}%</span>
                            <span style="font-weight:600;color:#374151;" class="dark:text-gray-300">
                                Rp {{ number_format($saving->current_amount, 0, ',', '.') }}
                            </span>
                        </div>
                        <div style="width:100%;background:#f3f4f6;border-radius:999px;height:8px;">
                            <div style="width:{{ $progress }}%;background:{{ $progressHex }};height:8px;border-radius:999px;transition:width 0.5s;"></div>
                        </div>
                    </div>

                    {{-- Footer --}}
                    <div style="display:flex;align-items:center;justify-content:space-between;padding-top:12px;border-top:1px solid #f3f4f6;" class="dark:border-gray-600">
                        <p style="font-size:12px;color:#9ca3af;">
                            Terkumpul: {{ $formatRupiah($saving->current_amount) }} / {{ $formatRupiah($saving->target_amount) }}
                        </p>
                        <a href="{{ \App\Filament\Child\Resources\SavingResource::getUrl('view', ['record' => $saving]) }}"
                           style="display:inline-flex;align-items:center;gap:4px;padding:6px 12px;background:#fff;border:1px solid #e5e7eb;border-radius:8px;font-size:12px;font-weight:500;color:#374151;text-decoration:none;transition:all 0.2s;"
                           onmouseover="this.style.background='#fffbeb';this.style.borderColor='#fcd34d';this.style.color='#d97706';"
                           onmouseout="this.style.background='#fff';this.style.borderColor='#e5e7eb';this.style.color='#374151';">
                            <svg style="width:12px;height:12px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Tambah
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div style="padding:40px 0;text-align:center;">
                <div style="width:64px;height:64px;background:#f3f4f6;border-radius:16px;display:flex;align-items:center;justify-content:center;margin:0 auto 12px;">
                    <svg style="width:32px;height:32px;" fill="none" stroke="#9ca3af" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z"/>
                    </svg>
                </div>
                <p style="font-size:14px;font-weight:500;color:#6b7280;">Belum ada tabungan aktif</p>
                <p style="font-size:12px;color:#9ca3af;margin-top:4px;margin-bottom:16px;">Buat tabungan pertama Anda sekarang</p>
                <a href="{{ \App\Filament\Child\Resources\SavingResource::getUrl('create') }}"
                   style="display:inline-flex;align-items:center;gap:6px;padding:8px 16px;background:#f59e0b;color:#fff;font-size:14px;font-weight:500;border-radius:8px;text-decoration:none;">
                    <svg style="width:16px;height:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Buat Tabungan Pertama
                </a>
            </div>
            @endif
        </div>

        {{-- RIWAYAT TRANSAKSI --}}
<div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
    @php
        $periodLabels = ['7'=>'7 Hari','30'=>'30 Hari','90'=>'3 Bulan','180'=>'6 Bulan','365'=>'1 Tahun','all'=>'Semua'];
    @endphp
    <div class="flex items-center justify-between mb-6">
        <h3 class="text-base font-semibold text-gray-900 dark:text-white">Riwayat Menabung</h3>

        <div style="position:relative;" x-data="{ open: false }">
            <button @click="open = !open"
                style="display:inline-flex;align-items:center;gap:6px;padding:6px 12px;background:#fff;border:1px solid #e5e7eb;border-radius:8px;font-size:12px;font-weight:500;color:#6b7280;cursor:pointer;">
                <svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/>
                </svg>
                {{ $periodLabels[$period] ?? '7 Hari' }}
                <svg style="width:12px;height:12px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>

            <div x-show="open" @click.outside="open = false" x-transition
                style="position:absolute;right:0;top:calc(100% + 6px);background:#fff;border:1px solid #e5e7eb;border-radius:10px;box-shadow:0 4px 16px rgba(0,0,0,0.08);min-width:160px;z-index:50;overflow:hidden;">
                @foreach(['7'=>'7 Hari Terakhir','30'=>'30 Hari Terakhir','90'=>'3 Bulan Terakhir','180'=>'6 Bulan Terakhir','365'=>'1 Tahun Terakhir','all'=>'Semua Data'] as $val => $label)
                <button
                    wire:click="setPeriod('{{ $val }}')"
                    @click="open = false"
                    style="display:block;width:100%;text-align:left;padding:9px 14px;font-size:13px;border:none;cursor:pointer;background:{{ $period === $val ? '#fef3c7' : 'transparent' }};color:{{ $period === $val ? '#d97706' : '#374151' }};font-weight:{{ $period === $val ? '600' : '400' }};"
                    onmouseover="this.style.background='#f9fafb'"
                    onmouseout="this.style.background='{{ $period === $val ? '#fef3c7' : 'transparent' }}'"
                >{{ $label }}</button>
                @endforeach
            </div>
        </div>
    </div>

    @if($recentDeposits->count() > 0)
    <div class="overflow-x-auto">
        <table style="width:100%;border-collapse:collapse;font-size:14px;">
            <thead>
                <tr style="border-bottom:1px solid #f3f4f6;">
                    <th style="text-align:left;padding-bottom:12px;font-size:12px;color:#9ca3af;font-weight:500;">Tujuan</th>
                    <th style="text-align:left;padding-bottom:12px;font-size:12px;color:#9ca3af;font-weight:500;">Tanggal</th>
                    <th style="text-align:left;padding-bottom:12px;font-size:12px;color:#9ca3af;font-weight:500;">Nominal</th>
                    <th style="text-align:left;padding-bottom:12px;font-size:12px;color:#9ca3af;font-weight:500;">Catatan</th>
                    <th style="text-align:right;padding-bottom:12px;font-size:12px;color:#9ca3af;font-weight:500;">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($recentDeposits as $deposit)
                @php
                    $goalName    = $deposit->savingGoal?->name ?? $deposit->saving?->name ?? '-';
                    $goalCat     = $deposit->savingGoal?->category ?? $deposit->saving?->category ?? 'Lainnya';
                    $depCatIcon  = $categoryIcons[$goalCat] ?? $categoryIcons['Lainnya'];
                    $depIconBgColors = [
                        'bg-blue-50'   => '#eff6ff', 'bg-green-50'  => '#f0fdf4',
                        'bg-purple-50' => '#faf5ff', 'bg-yellow-50' => '#fefce8',
                        'bg-orange-50' => '#fff7ed', 'bg-pink-50'   => '#fdf2f8',
                        'bg-indigo-50' => '#eef2ff', 'bg-gray-100'  => '#f3f4f6',
                    ];
                    $depIconStrokeColors = [
                        'text-blue-500'   => '#3b82f6', 'text-green-500'  => '#22c55e',
                        'text-purple-500' => '#a855f7', 'text-yellow-500' => '#eab308',
                        'text-orange-500' => '#f97316', 'text-pink-500'   => '#ec4899',
                        'text-indigo-500' => '#6366f1', 'text-gray-500'   => '#6b7280',
                    ];
                    $depIconBg     = $depIconBgColors[$depCatIcon['bg']] ?? '#f3f4f6';
                    $depIconStroke = $depIconStrokeColors[$depCatIcon['color']] ?? '#6b7280';
                @endphp
                <tr style="border-bottom:1px solid #f9fafb;" onmouseover="this.style.background='#f9fafb'" onmouseout="this.style.background='transparent'">
                    <td style="padding:14px 16px 14px 0;">
                        <div style="display:flex;align-items:center;gap:10px;">
                            <div style="width:36px;height:36px;background:{{ $depIconBg }};border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                <svg style="width:16px;height:16px;" fill="none" stroke="{{ $depIconStroke }}" viewBox="0 0 24 24">
                                    {!! $depCatIcon['svg'] !!}
                                </svg>
                            </div>
                            <span style="font-weight:500;color:#111827;">{{ $goalName }}</span>
                        </div>
                    </td>
                    <td style="padding:14px 16px 14px 0;font-size:12px;color:#6b7280;white-space:nowrap;">
                        {{ $deposit->date->format('d M Y') }}
                    </td>
                    <td style="padding:14px 16px 14px 0;font-weight:600;color:#16a34a;white-space:nowrap;">
                        +Rp {{ number_format($deposit->amount, 0, ',', '.') }}
                    </td>
                    <td style="padding:14px 16px 14px 0;font-size:12px;color:#9ca3af;">
                        {{ $deposit->note ?? '-' }}
                    </td>
                    <td style="padding:14px 0;text-align:right;">
                        <span style="display:inline-flex;padding:4px 10px;background:#dcfce7;color:#16a34a;font-size:11px;font-weight:700;border-radius:999px;">
                            SUCCESS
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if($recentDeposits->count() === 0)
    {{-- handled below --}}
    @endif

    @else
    <div style="padding:40px 0;text-align:center;">
        <div style="width:64px;height:64px;background:#f3f4f6;border-radius:16px;display:flex;align-items:center;justify-content:center;margin:0 auto 12px;">
            <svg style="width:32px;height:32px;" fill="none" stroke="#9ca3af" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25ZM6.75 12h.008v.008H6.75V12Zm0 3h.008v.008H6.75V15Zm0 3h.008v.008H6.75V18Z"/>
            </svg>
        </div>
        <p style="font-size:14px;font-weight:500;color:#6b7280;">Belum ada riwayat menabung</p>
        <p style="font-size:12px;color:#9ca3af;margin-top:4px;">Mulai tambah setoran untuk melihat riwayat di sini</p>
    </div>
    @endif
</div>

    </div>
</x-filament-panels::page>
