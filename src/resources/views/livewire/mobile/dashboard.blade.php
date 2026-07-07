<div class="min-h-full bg-gray-50 text-gray-900 pb-28 md:pb-8">

    {{-- 4 Summary Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-2.5 md:gap-3 px-4 md:px-6 max-w-6xl mx-auto">
        <div class="bg-white border border-gray-100 rounded-xl p-3 shadow-sm">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-full bg-emerald-100 flex items-center justify-center shrink-0">
                    <x-heroicon-o-arrow-trending-down class="w-4 h-4 text-emerald-600" />
                </div>
                <div class="min-w-0">
                    <p class="text-[11px] text-gray-400">Pemasukan</p>
                    <p class="font-semibold text-gray-800 text-sm truncate">{{ $formatRp($pemasukanBulanIni) }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white border border-gray-100 rounded-xl p-3 shadow-sm">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-full bg-red-100 flex items-center justify-center shrink-0">
                    <x-heroicon-o-arrow-trending-up class="w-4 h-4 text-red-500" />
                </div>
                <div class="min-w-0">
                    <p class="text-[11px] text-gray-400">Pengeluaran</p>
                    <p class="font-semibold text-gray-800 text-sm truncate">{{ $formatRp($pengeluaranBulanIni) }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white border border-gray-100 rounded-xl p-3 shadow-sm">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-full bg-blue-100 flex items-center justify-center shrink-0">
                    <x-heroicon-o-banknotes class="w-4 h-4 text-blue-600" />
                </div>
                <div class="min-w-0">
                    <p class="text-[11px] text-gray-400">Saldo</p>
                    <p class="font-semibold text-gray-800 text-sm truncate">{{ $formatRp($totalSaldo) }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white border border-gray-100 rounded-xl p-3 shadow-sm">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-full bg-purple-100 flex items-center justify-center shrink-0">
                    <x-heroicon-o-credit-card class="w-4 h-4 text-purple-600" />
                </div>
                <div class="min-w-0">
                    <p class="text-[11px] text-gray-400">Tabungan</p>
                    <p class="font-semibold text-gray-800 text-sm truncate">{{ $formatRp($tabunganBulanIni) }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- FAB Tambah Transaksi untuk mobile --}}
    <a href="{{ route('mobile.add-transaction') }}"
       class="md:hidden fixed right-5 bottom-24 z-40 w-12 h-12 rounded-full bg-emerald-600 text-white flex items-center justify-center shadow-lg shadow-emerald-600/30 hover:bg-emerald-700 transition">
        <x-heroicon-o-plus class="w-6 h-6" />
    </a>

    {{-- Middle Row --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-3 px-4 md:px-6 mt-3 max-w-6xl mx-auto">
        {{-- Arus Kas 6 Bulan --}}
        <div class="bg-white border border-gray-100 rounded-xl p-4 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <p class="text-sm font-semibold text-gray-800">ARUS KAS BULANAN</p>
                <span class="text-[10px] text-gray-400 font-medium">6 Bulan</span>
            </div>
            <div wire:ignore>
                <div style="height: 200px; position: relative;">
                    <canvas x-init="
                        console.log('chartDataForJS:', @js($chartDataForJS));
                        new Chart($el, {
                            type: 'line',
                            data: @js($chartDataForJS),
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                interaction: { intersect: false, mode: 'index' },
                                plugins: {
                                    legend: {
                                        display: true,
                                        position: 'top',
                                        labels: { usePointStyle: true, padding: 15, font: { size: 11 } }
                                    },
                                    tooltip: {
                                        backgroundColor: '#1E293B',
                                        padding: 10,
                                        cornerRadius: 8,
                                        callbacks: {
                                            label: (ctx) => ctx.dataset.label + ': Rp ' + Number(ctx.parsed.y).toLocaleString('id-ID')
                                        }
                                    }
                                },
                                scales: {
                                    y: {
                                        beginAtZero: true,
                                        ticks: {
                                            callback: (v) => v >= 1000000 ? 'Rp' + (v/1000000).toFixed(1) + 'jt' : 'Rp' + Number(v/1000).toFixed(0) + 'rb',
                                            font: { size: 9 }
                                        },
                                        grid: { color: '#F3F4F6' }
                                    },
                                    x: { grid: { display: false }, ticks: { font: { size: 9 } } }
                                }
                            }
                        });
                    "></canvas>
                </div>
            </div>
        </div>

        {{-- Pengeluaran per Kategori --}}
        <div class="bg-white border border-gray-100 rounded-xl p-4 shadow-sm">
            <p class="text-sm font-semibold text-gray-800 mb-3">Pengeluaran per Kategori</p>
            @if (count($pengeluaranPerKategori) > 0)
                @php
                    $palette  = ['#10B981','#F59E0B','#EF4444','#3B82F6','#8B5CF6','#EC4899'];
                    $labels   = array_column($pengeluaranPerKategori, 'label');
                    $amounts  = array_column($pengeluaranPerKategori, 'amount');
                    $colors   = array_map(fn($i) => $palette[$i % count($palette)], array_keys($pengeluaranPerKategori));
                @endphp
                <div class="flex items-center gap-4" wire:ignore>
                    <div class="w-20 h-20 shrink-0">
                        <canvas x-init="
                            new Chart($el, {
                                type: 'doughnut',
                                data: {
                                    labels: @js($labels),
                                    datasets: [{
                                        data: @js($amounts),
                                        backgroundColor: @js($colors),
                                        borderWidth: 0,
                                        hoverOffset: 4,
                                    }]
                                },
                                options: {
                                    cutout: '72%',
                                    plugins: {
                                        legend: { display: false },
                                        tooltip: {
                                            callbacks: {
                                                label: function(ctx) {
                                                    let total = ctx.dataset.data.reduce((a, b) => a + b, 0);
                                                    let pct = total > 0 ? ((ctx.parsed / total) * 100).toFixed(1) : 0;
                                                    return 'Rp ' + Number(ctx.parsed).toLocaleString('id-ID') + ' (' + pct + '%)';
                                                }
                                            }
                                        }
                                    },
                                    responsive: true,
                                    maintainAspectRatio: true,
                                }
                            });
                        "></canvas>
                    </div>
                    <div class="space-y-1.5 min-w-0 flex-1">
                        @foreach ($pengeluaranPerKategori as $i => $cat)
                            <div class="flex items-center gap-2 text-xs">
                                <span class="w-2 h-2 rounded-full shrink-0" style="background:{{ $colors[$i] }}"></span>
                                <span class="text-gray-600 truncate">{{ $cat['label'] }}</span>
                                <span class="ml-auto font-medium text-gray-800 shrink-0">{{ $formatRp($cat['amount']) }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                <p class="text-xs text-gray-400 text-center py-6">Belum ada pengeluaran bulan ini</p>
            @endif
        </div>
    </div>

    {{-- Bottom Row --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-3 px-4 md:px-6 mt-3 max-w-6xl mx-auto">
        {{-- Transaksi Terbaru --}}
        <div class="bg-white border border-gray-100 rounded-xl p-4 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <p class="text-sm font-semibold text-gray-800">Transaksi Terbaru</p>
                <a href="{{ route('mobile.history') }}" class="text-[11px] text-amber-600 font-medium">Lihat Semua</a>
            </div>
            @forelse ($transaksiTerakhir as $tx)
                <x-mobile.transaction-item :transaction="$tx" />
            @empty
                <p class="text-xs text-gray-400 text-center py-6">Belum ada transaksi</p>
            @endforelse
        </div>

        {{-- Target Tabungan --}}
        <div class="bg-white border border-gray-100 rounded-xl p-4 shadow-sm">
            <p class="text-sm font-semibold text-gray-800 mb-3">Target Tabungan</p>
            @forelse ($tabunganList as $tab)
                <div class="mb-3 last:mb-0">
                    <div class="flex justify-between text-xs mb-1">
                        <span class="font-medium text-gray-700">{{ $tab['name'] }}</span>
                        <span class="text-gray-400">{{ number_format($tab['progress'], 0) }}%</span>
                    </div>
                    <div class="w-full h-2 bg-gray-100 rounded-full overflow-hidden">
                        <div class="h-full rounded-full bg-gradient-to-r from-amber-400 to-orange-500 transition-all"
                             style="width: {{ min($tab['progress'], 100) }}%"></div>
                    </div>
                    <p class="text-[10px] text-gray-400 mt-1">
                        {{ $formatRp($tab['current_amount']) }} / {{ $formatRp($tab['target_amount']) }}
                    </p>
                </div>
            @empty
                <p class="text-xs text-gray-400 text-center py-6">Belum ada target tabungan</p>
            @endforelse
        </div>
    </div>

    <x-mobile.bottom-nav active="dashboard" />
</div>
