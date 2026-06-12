@php
    $stats         = $this->getStats();
    $activeSavings = $stats['savings'];
    $totalSaved    = $stats['totalSaved'];
    $completed     = $stats['completed'];
    $remaining     = $stats['remaining'];
    $percentChange = $stats['percentChange'];
    $isPositive    = $stats['isPositive'];
    $recentDeposits = $this->getRecentDeposits();

    $categories = [
        'Liburan'    => ['icon' => '✈️', 'color' => 'bg-blue-50 text-blue-600 dark:bg-blue-900 dark:text-blue-200'],
        'Properti'   => ['icon' => '🏠', 'color' => 'bg-green-50 text-green-600 dark:bg-green-900 dark:text-green-200'],
        'Pribadi'    => ['icon' => '👤', 'color' => 'bg-purple-50 text-purple-600 dark:bg-purple-900 dark:text-purple-200'],
        'Pendidikan' => ['icon' => '📚', 'color' => 'bg-yellow-50 text-yellow-600 dark:bg-yellow-900 dark:text-yellow-200'],
        'Kendaraan'  => ['icon' => '🚗', 'color' => 'bg-orange-50 text-orange-600 dark:bg-orange-900 dark:text-orange-200'],
        'Kesehatan'  => ['icon' => '💊', 'color' => 'bg-pink-50 text-pink-600 dark:bg-pink-900 dark:text-pink-200'],
        'Elektronik' => ['icon' => '💻', 'color' => 'bg-indigo-50 text-indigo-600 dark:bg-indigo-900 dark:text-indigo-200'],
        'Lainnya'    => ['icon' => '📦', 'color' => 'bg-gray-50 text-gray-600 dark:bg-gray-700 dark:text-gray-200'],
    ];
@endphp

<x-filament-panels::page>
    <div class="space-y-6">

        {{-- STATS CARDS --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

            {{-- Total Tabungan --}}
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
                    <div class="w-12 h-12 bg-amber-50 dark:bg-amber-900 rounded-xl flex items-center justify-center text-2xl">
                        🐷
                    </div>
                </div>
            </div>

            {{-- Target Tercapai --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Target Tercapai</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">
                            {{ $completed }} Target
                        </p>
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Minggu ini</p>
                    </div>
                    <div class="w-12 h-12 bg-green-50 dark:bg-green-900 rounded-xl flex items-center justify-center text-2xl">
                        ✅
                    </div>
                </div>
            </div>

            {{-- Sisa Target --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Sisa Target</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">
                            Rp {{ number_format($remaining, 0, ',', '.') }}
                        </p>
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">
                            {{ $activeSavings->count() }} target tersisa
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-red-50 dark:bg-red-900 rounded-xl flex items-center justify-center text-2xl">
                        🚩
                    </div>
                </div>
            </div>
        </div>

        {{-- ACTIVE SAVINGS CARDS --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-base font-semibold text-gray-900 dark:text-white">Target Tabungan Aktif</h3>
                @if($activeSavings->count() > 3)
                <a href="{{ \App\Filament\Parent\Resources\SavingResource::getUrl('index') }}"
                   class="text-sm text-amber-500 hover:text-amber-600 font-medium">
                    Lihat Semua →
                </a>
                @endif
            </div>

            @if($activeSavings->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @foreach($activeSavings->take(3) as $saving)
                @php
                    $cat      = $categories[$saving->category] ?? $categories['Lainnya'];
                    $progress = $saving->getProgressPercentage();
                    $progressColor = $progress >= 100 ? 'bg-green-500'
                                   : ($progress >= 60  ? 'bg-yellow-400'
                                   : 'bg-yellow-500');
                @endphp
                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-2xl p-5 border border-gray-100 dark:border-gray-600 hover:shadow-md transition-all">

                    {{-- Icon + Badge --}}
                    <div class="flex items-start justify-between mb-4">
                        <div class="w-12 h-12 rounded-xl {{ $cat['color'] }} flex items-center justify-center text-2xl">
                            {{ $cat['icon'] }}
                        </div>
                        <span class="text-xs font-semibold tracking-wider text-gray-400 uppercase">
                            {{ $saving->category }}
                        </span>
                    </div>

                    {{-- Name & Target --}}
                    <h4 class="font-semibold text-gray-900 dark:text-white mb-1">{{ $saving->name }}</h4>
                    <p class="text-xs text-gray-400 mb-4">Target: Rp {{ number_format($saving->target_amount, 0, ',', '.') }}</p>

                    {{-- Progress --}}
                    <div class="space-y-1.5 mb-4">
                        <div class="flex justify-between text-xs">
                            <span class="text-gray-500">Progress: {{ $progress }}%</span>
                            <span class="font-medium text-gray-700 dark:text-gray-300">
                                Rp {{ number_format($saving->current_amount, 0, ',', '.') }}
                            </span>
                        </div>
                        <div class="w-full bg-gray-200 dark:bg-gray-500 rounded-full h-1.5">
                            <div class="{{ $progressColor }} h-1.5 rounded-full transition-all duration-500"
                                 style="width: {{ $progress }}%"></div>
                        </div>
                    </div>

                    {{-- Footer --}}
                    <div class="flex items-center justify-between pt-3 border-t border-gray-100 dark:border-gray-600">
                        <p class="text-xs text-gray-400">
                            Rp {{ number_format($saving->current_amount / 1000000, 1) }}M
                            / Rp {{ number_format($saving->target_amount / 1000000, 1) }}M
                        </p>
                        <a href="{{ \App\Filament\Parent\Resources\SavingResource::getUrl('view', ['record' => $saving]) }}"
                           class="inline-flex items-center gap-1 px-3 py-1.5 bg-white dark:bg-gray-600 border border-gray-200 dark:border-gray-500 hover:bg-amber-50 hover:border-amber-300 text-gray-600 dark:text-gray-300 rounded-lg text-xs font-medium transition-colors">
                            + Tambah
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="py-10 text-center">
                <p class="text-4xl mb-3">🐷</p>
                <p class="text-sm text-gray-500 dark:text-gray-400">Belum ada tabungan aktif</p>
                <a href="{{ \App\Filament\Parent\Resources\SavingResource::getUrl('create') }}"
                   class="inline-flex items-center gap-1 mt-3 px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white text-sm font-medium rounded-lg transition-colors">
                    + Buat Tabungan Pertama
                </a>
            </div>
            @endif
        </div>

        {{-- RIWAYAT TRANSAKSI --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-base font-semibold text-gray-900 dark:text-white">Riwayat Menabung</h3>
                <span class="text-xs text-gray-400 bg-gray-100 dark:bg-gray-700 px-2.5 py-1 rounded-full">
                    7 Hari Terakhir
                </span>
            </div>

            @if($recentDeposits->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-100 dark:border-gray-700">
                            <th class="text-left pb-3 text-xs text-gray-400 font-medium">Tujuan</th>
                            <th class="text-left pb-3 text-xs text-gray-400 font-medium">Tanggal</th>
                            <th class="text-left pb-3 text-xs text-gray-400 font-medium">Nominal</th>
                            <th class="text-left pb-3 text-xs text-gray-400 font-medium">Catatan</th>
                            <th class="text-right pb-3 text-xs text-gray-400 font-medium">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 dark:divide-gray-700/50">
                        @foreach($recentDeposits as $deposit)
                        @php
                            $depCat = $categories[$deposit->savingGoal->category ?? 'Lainnya'] ?? $categories['Lainnya'];
                        @endphp
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                            <td class="py-3.5 pr-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg {{ $depCat['color'] }} flex items-center justify-center text-base flex-shrink-0">
                                        {{ $depCat['icon'] }}
                                    </div>
                                    <span class="font-medium text-gray-900 dark:text-white text-sm">
                                        {{ $deposit->savingGoal->name ?? '-' }}
                                    </span>
                                </div>
                            </td>
                            <td class="py-3.5 pr-4 text-gray-500 dark:text-gray-400 text-xs">
                                {{ $deposit->date->format('d M Y') }}
                            </td>
                            <td class="py-3.5 pr-4 font-semibold text-green-600 dark:text-green-400 text-sm">
                                +Rp {{ number_format($deposit->amount, 0, ',', '.') }}
                            </td>
                            <td class="py-3.5 pr-4 text-gray-400 text-xs">
                                {{ $deposit->note ?? '-' }}
                            </td>
                            <td class="py-3.5 text-right">
                                <span class="inline-flex px-2.5 py-1 bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-300 text-xs font-semibold rounded-full">
                                    SUCCESS
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="py-10 text-center">
                <p class="text-4xl mb-3">📭</p>
                <p class="text-sm text-gray-500 dark:text-gray-400">Belum ada riwayat menabung</p>
                <p class="text-xs text-gray-400 mt-1">Mulai tambah setoran untuk melihat riwayat di sini</p>
            </div>
            @endif
        </div>

    </div>
</x-filament-panels::page>
