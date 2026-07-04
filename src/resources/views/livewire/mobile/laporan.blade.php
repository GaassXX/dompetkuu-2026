@php
    $totalPemasukan   = $this->getTotalPemasukan();
    $totalPengeluaran = $this->getTotalPengeluaran();
    $saldoBersih      = $this->getSaldoBersih();
    $tren             = $this->getTrenPengeluaran();
    $kategori         = $this->getPengeluaranPerKategori();
    $ringkasan        = $this->getRingkasanBulanan();
    $trenLabels       = array_column($tren, 'bulan');
    $trenTotals       = array_column($tren, 'total');
    $totalKat         = array_sum(array_column($kategori, 'total'));
    $palette          = ['#EF4444', '#F97316', '#EAB308', '#22C55E', '#3B82F6', '#8B5CF6', '#EC4899', '#6B7280'];
    $isParent         = $this->isParent();
@endphp

<div class="min-h-full bg-gray-50 text-gray-900 pb-28 md:pb-8">

    {{-- Header --}}
    <div class="px-4 md:px-6 pt-4 max-w-6xl mx-auto">
        <h1 class="font-bold text-gray-800 text-lg">Laporan</h1>
        <p class="text-xs text-gray-400 mt-0.5">Analisis keuanganmu</p>
    </div>

    {{-- Export buttons --}}
    <div class="grid grid-cols-2 gap-2.5 px-4 md:px-6 mt-3 max-w-6xl mx-auto">
        <button wire:click="openExportSheet('pdf')"
                class="flex items-center justify-center gap-1.5 text-xs font-semibold text-white bg-red-500 hover:bg-red-600 transition rounded-xl py-2.5">
            <x-heroicon-o-document-arrow-down class="w-4 h-4" />
            Export PDF
        </button>
        <button wire:click="openExportSheet('excel')"
                class="flex items-center justify-center gap-1.5 text-xs font-semibold text-white bg-amber-500 hover:bg-amber-600 transition rounded-xl py-2.5">
            <x-heroicon-o-table-cells class="w-4 h-4" />
            Export Excel
        </button>
    </div>

    {{-- Filter Periode (+ Data kalau parent) --}}
    <div class="bg-white border border-gray-100 rounded-xl p-3 mx-4 md:mx-6 mt-3 shadow-sm max-w-6xl md:mx-auto">
        <div class="grid {{ $isParent ? 'grid-cols-2 gap-3' : 'grid-cols-1' }}">
            <div>
                <label class="text-[11px] font-medium text-gray-400 mb-1 block">Periode</label>
                <select wire:model.live="periode"
                        class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2 bg-white text-gray-700">
                    @foreach ($this->periodeOptions as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            @if ($isParent)
                <div>
                    <label class="text-[11px] font-medium text-gray-400 mb-1 block">Data</label>
                    <select wire:model.live="scope"
                            class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2 bg-white text-gray-700">
                        @foreach ($this->scopeOptions as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            @endif
        </div>
        <p class="text-[10px] text-gray-400 mt-2 flex items-center gap-1">
            <x-heroicon-o-clock class="w-3 h-3" />
            Diperbarui {{ now()->translatedFormat('d M Y, H:i') }}
        </p>
    </div>

    {{-- Stat cards --}}
    <div class="grid grid-cols-1 gap-2.5 px-4 md:px-6 mt-3 max-w-6xl mx-auto">
        <div class="bg-white border-t-4 border border-gray-100 rounded-xl p-3 shadow-sm" style="border-top-color:#10B981;">
            <p class="text-[11px] font-semibold uppercase tracking-wide text-gray-400">Total Pemasukan</p>
            <p class="text-xl font-bold mt-1" style="color:#10B981;">Rp {{ number_format($totalPemasukan, 0, ',', '.') }}</p>
        </div>
        <div class="bg-white border-t-4 border border-gray-100 rounded-xl p-3 shadow-sm" style="border-top-color:#EF4444;">
            <p class="text-[11px] font-semibold uppercase tracking-wide text-gray-400">Total Pengeluaran</p>
            <p class="text-xl font-bold mt-1" style="color:#EF4444;">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</p>
        </div>
        <div class="bg-white border-t-4 border border-gray-100 rounded-xl p-3 shadow-sm"
             style="border-top-color:{{ $saldoBersih >= 0 ? '#10B981' : '#EF4444' }};">
            <p class="text-[11px] font-semibold uppercase tracking-wide text-gray-400">Saldo Bersih</p>
            <p class="text-xl font-bold mt-1" style="color:{{ $saldoBersih >= 0 ? '#10B981' : '#EF4444' }};">
                {{ $saldoBersih < 0 ? '-' : '' }}Rp {{ number_format(abs($saldoBersih), 0, ',', '.') }}
            </p>
        </div>
    </div>

    {{-- Tren Pengeluaran --}}
    <div class="bg-white border border-gray-100 rounded-xl p-3 mx-4 md:mx-6 mt-3 shadow-sm max-w-6xl md:mx-auto">
        <div class="flex items-center justify-between mb-2 flex-wrap gap-2">
            <p class="text-sm font-semibold text-gray-800">Tren Pengeluaran ({{ $durasiTren }} Bulan)</p>
            <select wire:model.live="durasiTren"
                    class="text-[11px] border border-gray-200 rounded-lg px-2 py-1 bg-white text-gray-600">
                <option value="3">3 Bulan</option>
                <option value="6">6 Bulan</option>
                <option value="12">12 Bulan</option>
            </select>
        </div>

        @if (count($tren) > 0 && array_sum($trenTotals) > 0)
            <div wire:ignore wire:key="tren-{{ $durasiTren }}-{{ $periode }}-{{ $scope }}">
                <div style="height: 200px; position: relative;">
                    <canvas x-init="
                        new Chart($el, {
                            type: 'line',
                            data: {
                                labels: @js($trenLabels),
                                datasets: [{
                                    label: 'Pengeluaran',
                                    data: @js($trenTotals),
                                    borderColor: '#EF4444',
                                    backgroundColor: 'rgba(239,68,68,0.08)',
                                    fill: true,
                                    tension: 0.4,
                                    borderWidth: 2.5,
                                    pointRadius: 4,
                                    pointBackgroundColor: '#EF4444',
                                    pointBorderColor: '#fff',
                                    pointBorderWidth: 2,
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                interaction: { intersect: false, mode: 'index' },
                                plugins: {
                                    legend: { display: false },
                                    tooltip: {
                                        backgroundColor: '#111827',
                                        padding: 10,
                                        cornerRadius: 8,
                                        callbacks: {
                                            label: (item) => 'Rp' + Number(item.parsed.y).toLocaleString('id-ID')
                                        }
                                    }
                                },
                                scales: {
                                    x: { grid: { display: false }, ticks: { font: { size: 9 } } },
                                    y: {
                                        beginAtZero: true,
                                        grid: { color: '#F3F4F6' },
                                        ticks: {
                                            font: { size: 9 },
                                            callback: (v) => v >= 1000000 ? 'Rp' + (v/1000000).toFixed(1) + 'jt' : 'Rp' + Number(v/1000).toFixed(0) + 'rb'
                                        }
                                    }
                                }
                            }
                        });
                    "></canvas>
                </div>
            </div>
        @else
            <p class="text-xs text-gray-400 text-center py-8">Belum ada data pengeluaran</p>
        @endif
    </div>

    {{-- Pengeluaran per Kategori --}}
    <div class="bg-white border border-gray-100 rounded-xl p-3 mx-4 md:mx-6 mt-3 shadow-sm max-w-6xl md:mx-auto">
        <p class="text-sm font-semibold text-gray-800 mb-3">Pengeluaran per Kategori</p>
        @if (count($kategori) > 0)
            <div class="space-y-3">
                @foreach ($kategori as $i => $kat)
                    @php $pct = $totalKat > 0 ? round(($kat->total / $totalKat) * 100) : 0; @endphp
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <span class="w-2 h-2 rounded-full shrink-0" style="background:{{ $palette[$i % count($palette)] }}"></span>
                            <span class="text-xs font-medium text-gray-700 truncate flex-1">{{ $kat->category }}</span>
                            <span class="text-xs font-semibold text-gray-800">Rp {{ number_format($kat->total, 0, ',', '.') }}</span>
                        </div>
                        <div class="w-full h-1.5 bg-gray-100 rounded-full overflow-hidden ml-4">
                            <div class="h-full rounded-full" style="width:{{ $pct }}%;background:{{ $palette[$i % count($palette)] }};"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-xs text-gray-400 text-center py-6">Tidak ada pengeluaran</p>
        @endif
    </div>

    {{-- Ringkasan Bulanan --}}
    <div class="bg-white border border-gray-100 rounded-xl p-3 mx-4 md:mx-6 mt-3 shadow-sm max-w-6xl md:mx-auto">
        <p class="text-sm font-semibold text-gray-800 mb-3">Ringkasan Bulanan</p>
        <div class="space-y-2.5">
            @foreach ($ringkasan as $r)
                <div class="flex items-center justify-between border-b border-gray-50 last:border-0 pb-2.5 last:pb-0">
                    <span class="text-xs font-medium text-gray-700">{{ $r['bulan'] }}</span>
                    <div class="text-right">
                        <p class="text-[10px] text-gray-400">
                            <span style="color:#10B981;">+Rp {{ number_format($r['pemasukan'], 0, ',', '.') }}</span>
                            &nbsp;/&nbsp;
                            <span style="color:#EF4444;">-Rp {{ number_format($r['pengeluaran'], 0, ',', '.') }}</span>
                        </p>
                        <p class="text-xs font-semibold" style="color:{{ $r['selisih'] >= 0 ? '#10B981' : '#EF4444' }};">
                            {{ $r['selisih'] >= 0 ? '+' : '' }}Rp {{ number_format($r['selisih'], 0, ',', '.') }}
                        </p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Export Bottom Sheet --}}
    @if ($showExportSheet)
        <div class="fixed inset-0 z-50 flex items-end md:items-center md:justify-center">
            <div class="absolute inset-0 bg-black/40" wire:click="closeExportSheet"></div>
            <div class="relative bg-white w-full md:w-96 rounded-t-2xl md:rounded-2xl p-4 pb-6">
                <p class="text-sm font-semibold text-gray-800 mb-3">
                    Export {{ $exportFormat === 'pdf' ? 'PDF' : 'Excel' }}
                </p>

                @if ($isParent)
                    <label class="text-[11px] font-medium text-gray-400 mb-1 block">Data</label>
                    <select wire:model="exportScope" class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2 mb-3">
                        @foreach ($this->scopeOptions as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                @endif

                <label class="text-[11px] font-medium text-gray-400 mb-1 block">Bulan</label>
                <select wire:model="exportMonth" class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2 mb-3">
                    @foreach (range(1, 12) as $m)
                        <option value="{{ $m }}">{{ \Illuminate\Support\Carbon::create()->month($m)->translatedFormat('F') }}</option>
                    @endforeach
                </select>

                <label class="text-[11px] font-medium text-gray-400 mb-1 block">Tahun</label>
                <select wire:model="exportYear" class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2 mb-3">
                    @foreach (range(now()->year - 2, now()->year + 1) as $y)
                        <option value="{{ $y }}">{{ $y }}</option>
                    @endforeach
                </select>

                <label class="text-[11px] font-medium text-gray-400 mb-1 block">Tipe Transaksi</label>
                <select wire:model="exportType" class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2 mb-4">
                    <option value="all">Semua</option>
                    <option value="Pemasukan">Pemasukan</option>
                    <option value="Pengeluaran">Pengeluaran</option>
                </select>

                <div class="flex gap-2">
                    <button wire:click="closeExportSheet"
                            class="flex-1 text-sm font-medium text-gray-600 bg-gray-100 rounded-xl py-2.5">
                        Batal
                    </button>
                    <button wire:click="confirmExport"
                            class="flex-1 text-sm font-semibold text-white rounded-xl py-2.5 {{ $exportFormat === 'pdf' ? 'bg-red-500' : 'bg-amber-500' }}">
                        Download
                    </button>
                </div>
            </div>
        </div>
    @endif

    <x-mobile.bottom-nav active="laporan" />
</div>
