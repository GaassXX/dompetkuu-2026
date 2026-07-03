@php
    $totalPemasukan = $this->getTotalPemasukan();
    $totalPengeluaran = $this->getTotalPengeluaran();
    $saldoBersih = $this->getSaldoBersih();
    $tren = $this->getTrenPengeluaran();
    $kategori = $this->getPengeluaranPerKategori();
    $ringkasan = $this->getRingkasanBulanan();
    $scopeOptions = $this->getScopeOptions();
@endphp

<x-filament-panels::page>
    @php
        $trenLabels = array_column($tren, 'bulan');
        $trenTotals = array_column($tren, 'total');
    @endphp

    <div class="space-y-5 report-page">

        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <p class="report-page-subtitle">Analisis keuanganmu</p>
            </div>
        </div>

        {{-- ===================== FILTERS ===================== --}}
        <div class="report-card report-toolbar-card">
            <div class="grid gap-4 lg:grid-cols-[repeat(3,minmax(0,1fr))_auto] lg:items-end">
                <div class="min-w-0">
                    <label class="report-filter-label">Periode</label>
                    <div class="relative report-select-wrap">
                        <select wire:model.live="periode" class="report-select">
                            @php
                                $__start = now()->subMonths(9)->startOfMonth();
                                $__end = now()->startOfMonth();
                            @endphp
                            @for ($__d = $__start->copy(); $__d->lte($__end); $__d->addMonth())
                                <option value="{{ $__d->format('Y-m') }}">{{ $__d->translatedFormat('F Y') }}</option>
                            @endfor
                        </select>
                    </div>
                </div>

                <div class="min-w-0">
                    <label class="report-filter-label">Data</label>
                    <div class="relative report-select-wrap">
                        <select wire:model.live="scope" class="report-select">
                            @foreach ($scopeOptions as $val => $label)
                                <option value="{{ $val }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="lg:justify-self-end text-xs report-muted pb-2.5 flex items-center gap-1.5">
                    <x-heroicon-o-clock class="w-3.5 h-3.5" />
                    Diperbarui {{ now()->translatedFormat('d M Y, H:i') }}
                </div>
            </div>
        </div>

        {{-- ===================== STAT CARDS ===================== --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="report-card report-stat-card" style="--accent:#10B981;">
                <p class="report-card-label">Total Pemasukan</p>
                <p class="report-stat-value" style="color:#10B981;">
                    Rp {{ number_format($totalPemasukan, 0, ',', '.') }}
                </p>
            </div>

            <div class="report-card report-stat-card" style="--accent:#EF4444;">
                <p class="report-card-label">Total Pengeluaran</p>
                <p class="report-stat-value" style="color:#EF4444;">
                    Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}
                </p>
            </div>

            <div class="report-card report-stat-card" style="--accent:{{ $saldoBersih >= 0 ? '#10B981' : '#EF4444' }};">
                <p class="report-card-label">Saldo Bersih</p>
                <p class="report-stat-value" style="color:{{ $saldoBersih >= 0 ? '#10B981' : '#EF4444' }};">
                    {{ $saldoBersih < 0 ? '-' : '' }}Rp {{ number_format(abs($saldoBersih), 0, ',', '.') }}
                </p>
            </div>
        </div>

        <div class="report-tren-kategori-grid">
            {{-- ===================== TREN PENGELUARAN ===================== --}}
            <div class="report-card">
                <div class="flex items-center justify-between mb-4 flex-wrap gap-2">
                    <h3 class="report-section-title">
                        <x-heroicon-o-chart-bar class="w-4 h-4 report-icon-muted" />
                        Tren Pengeluaran ({{ $this->durasiTren }} Bulan)
                    </h3>
                    <div class="relative report-select-wrap">
                        <select wire:model.live="durasiTren" class="report-select report-select--sm">
                            <option value="3">3 Bulan</option>
                            <option value="6">6 Bulan</option>
                            <option value="12">12 Bulan</option>
                        </select>
                    </div>
                </div>

                @if (count($tren) > 0 && array_sum($trenTotals) > 0)
                    <div class="report-chart-shell" data-tren-chart data-tren-labels='@json($trenLabels)'
                        data-tren-totals='@json($trenTotals)'>
                        <canvas id="trenChart"></canvas>
                    </div>
                @else
                    <div class="report-empty">
                        <x-heroicon-o-presentation-chart-line class="w-9 h-9 report-icon-muted mb-2" />
                        <p>Belum ada data pengeluaran</p>
                    </div>
                @endif
            </div>

            {{-- ===================== PENGELUARAN PER KATEGORI ===================== --}}
            <div class="report-card">
                <h3 class="report-section-title mb-4">
                    <x-heroicon-o-chart-pie class="w-4 h-4 report-icon-muted" />
                    Pengeluaran per Kategori
                </h3>

                @if (count($kategori) > 0)
                    @php
                        $totalKat = array_sum(array_column($kategori, 'total'));
                        $colors = [
                            '#EF4444',
                            '#F97316',
                            '#EAB308',
                            '#22C55E',
                            '#3B82F6',
                            '#8B5CF6',
                            '#EC4899',
                            '#6B7280',
                        ];
                    @endphp
                    <div class="space-y-4">
                        @foreach ($kategori as $i => $kat)
                            @php $pct = $totalKat > 0 ? round(($kat->total / $totalKat) * 100) : 0; @endphp
                            <div class="report-kategori-row">
                                <span class="report-kategori-dot"
                                    style="background:{{ $colors[$i % count($colors)] }};"></span>
                                <span class="report-kategori-label">{{ $kat->category }}</span>

                                <div class="report-kategori-bar-track">
                                    <div class="report-kategori-bar-fill"
                                        style="width:{{ $pct }}%;background:{{ $colors[$i % count($colors)] }};">
                                    </div>
                                </div>

                                <span class="report-kategori-pct">{{ $pct }}%</span>
                                <span class="report-kategori-amount">Rp
                                    {{ number_format($kat->total, 0, ',', '.') }}</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="report-empty">
                        <x-heroicon-o-inbox class="w-9 h-9 report-icon-muted mb-2" />
                        <p>Tidak ada pengeluaran</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- ===================== RINGKASAN BULANAN ===================== --}}
        <div class="report-card !p-0 overflow-hidden">
            <h3 class="report-section-title p-5 pb-0 mb-3">
                <x-heroicon-o-table-cells class="w-4 h-4 report-icon-muted" />
                Ringkasan Bulanan
            </h3>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="report-thead-row">
                            <th class="report-th text-left">Bulan</th>
                            <th class="report-th text-right">Pemasukan</th>
                            <th class="report-th text-right">Pengeluaran</th>
                            <th class="report-th text-right">Selisih</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($ringkasan as $r)
                            <tr class="report-tbody-row">
                                <td class="py-3 px-5 report-cell">{{ $r['bulan'] }}</td>
                                <td class="py-3 px-5 text-right tabular-nums" style="color:#10B981;">Rp
                                    {{ number_format($r['pemasukan'], 0, ',', '.') }}</td>
                                <td class="py-3 px-5 text-right tabular-nums" style="color:#EF4444;">Rp
                                    {{ number_format($r['pengeluaran'], 0, ',', '.') }}</td>
                                <td class="py-3 px-5 text-right font-medium tabular-nums"
                                    style="color:{{ $r['selisih'] >= 0 ? '#10B981' : '#EF4444' }};">
                                    {{ $r['selisih'] >= 0 ? '+' : '' }}Rp
                                    {{ number_format($r['selisih'], 0, ',', '.') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    {{-- ===================== STYLES ===================== --}}
    <style>
        .report-card {
            background: var(--color-background-primary, #fff);
            border-radius: 1rem;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.03);
            border: 1px solid var(--color-border-tertiary, rgba(0, 0, 0, 0.06));
            padding: 1.25rem;
        }

        .report-page-title {
            font-size: 1.8rem;
            line-height: 2.2rem;
            font-weight: 700;
            color: var(--color-text-primary, #111827);
            letter-spacing: -0.02em;
        }

        .report-page-subtitle {
            margin-top: 0.2rem;
            font-size: 0.875rem;
            color: var(--color-text-secondary, #6b7280);
        }

        .report-toolbar-card {
            padding-top: 1.5rem;
        }

        .report-stat-card {
            position: relative;
            padding-top: 1.5rem;
            overflow: hidden;
        }

        .report-stat-card::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: var(--accent, #6B7280);
        }

        .report-stat-value {
            font-size: 1.5rem;
            line-height: 2rem;
            font-weight: 700;
            margin-top: 0.375rem;
            font-variant-numeric: tabular-nums;
        }

        .report-card-label {
            font-size: 0.72rem;
            font-weight: 600;
            letter-spacing: 0.03em;
            text-transform: uppercase;
            color: var(--color-text-secondary, #9ca3af);
        }

        .report-filter-label {
            font-size: 0.72rem;
            font-weight: 500;
            color: var(--color-text-secondary, #9ca3af);
            margin-bottom: 0.375rem;
            display: block;
        }

        .report-section-title {
            display: flex;
            align-items: center;
            gap: 0.4rem;
            font-size: 0.8rem;
            font-weight: 600;
            letter-spacing: 0.02em;
            text-transform: uppercase;
            color: var(--color-text-primary, #4b5563);
        }

        .report-muted {
            color: var(--color-text-secondary, #9ca3af);
        }

        .report-icon-muted {
            color: var(--color-text-secondary, #9ca3af);
            opacity: 0.7;
        }

        .report-empty {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 2.5rem 0;
            color: var(--color-text-secondary, #9ca3af);
            font-size: 0.875rem;
        }

        .report-select {
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            font-size: 0.8125rem;
            padding: 0.5rem 2.25rem 0.5rem 0.75rem;
            border-radius: 0.5rem;
            border: 1px solid var(--color-border-tertiary, rgb(209 213 219));
            background: var(--color-background-primary, #fff);
            color: var(--color-text-primary, rgb(55 65 81));
            width: 100%;
            cursor: pointer;
            line-height: 1.25rem;
            background-image: none !important;
            background-repeat: no-repeat !important;
            background-position: right 0.75rem center !important;
        }

        .report-select:focus {
            outline: none;
            border-color: var(--color-primary-500, #f59e0b);
            box-shadow: 0 0 0 3px var(--color-primary-100, rgba(245, 158, 11, 0.15));
        }

        .report-select--sm {
            font-size: 0.75rem;
            padding: 0.4rem 1.75rem 0.4rem 0.65rem;
        }

        .report-select-wrap::after {
            content: none;
        }

        .report-select--sm {
            padding-right: 2rem;
        }

        .report-chart-shell {
            position: relative;
            height: 260px;
        }

        .report-tren-kategori-grid {
            display: grid;
            grid-template-columns: minmax(0, 1fr);
            gap: 1rem;
        }

        @media (min-width: 1280px) {
            .report-tren-kategori-grid {
                grid-template-columns: minmax(0, 1.35fr) minmax(0, 1fr);
            }
        }

        /* Kategori list rows */
        .report-kategori-row {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .report-kategori-dot {
            width: 0.6rem;
            height: 0.6rem;
            border-radius: 3px;
            flex-shrink: 0;
        }

        .report-kategori-label {
            font-size: 0.8125rem;
            font-weight: 500;
            color: var(--color-text-primary, #374151);
            width: 9rem;
            flex-shrink: 0;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .report-kategori-bar-track {
            flex: 1;
            height: 0.4rem;
            background: var(--color-background-secondary, #f3f4f6);
            border-radius: 999px;
            overflow: hidden;
        }

        .report-kategori-bar-fill {
            height: 100%;
            border-radius: 999px;
        }

        .report-kategori-pct {
            font-size: 0.75rem;
            font-weight: 600;
            color: var(--color-text-secondary, #9ca3af);
            width: 2.5rem;
            text-align: right;
            flex-shrink: 0;
        }

        .report-kategori-amount {
            font-size: 0.8125rem;
            font-weight: 600;
            color: var(--color-text-primary, #374151);
            width: 8rem;
            text-align: right;
            flex-shrink: 0;
            font-variant-numeric: tabular-nums;
        }

        /* Table */
        .report-thead-row {
            background: var(--color-background-secondary, #f9fafb);
        }

        .report-th {
            padding: 0.75rem 1.25rem;
            font-weight: 600;
            color: var(--color-text-secondary, #9ca3af);
            font-size: 0.6875rem;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .report-tbody-row {
            border-top: 1px solid var(--color-border-tertiary, rgba(0, 0, 0, 0.06));
            transition: background-color 0.15s;
        }

        .report-tbody-row:hover {
            background: var(--color-background-secondary, rgba(0, 0, 0, 0.015));
        }

        .report-cell {
            color: var(--color-text-primary, #374151);
        }

        @media (max-width: 640px) {
            .report-page-title {
                font-size: 1.5rem;
                line-height: 2rem;
            }

            .report-kategori-label {
                width: 6rem;
            }

            .report-kategori-amount {
                width: 6.5rem;
            }
        }
    </style>
</x-filament-panels::page>

@push('scripts')
    <script>
        document.addEventListener('livewire:navigated', initCharts);
        document.addEventListener('livewire:init', initCharts);
        document.addEventListener('laporan-refresh', scheduleChartRefresh);

        function initCharts() {
            if (typeof Chart === 'undefined') {
                var s = document.createElement('script');
                s.src = 'https://cdn.jsdelivr.net/npm/chart.js';
                s.onload = renderCharts;
                document.head.appendChild(s);
            } else {
                scheduleChartRefresh();
            }
        }

        function scheduleChartRefresh() {
            window.requestAnimationFrame(() => {
                setTimeout(renderCharts, 50);
            });
        }

        function rupiah(v) {
            return 'Rp' + Number(v).toLocaleString('id-ID');
        }

        function renderCharts() {
            renderTrenChart();
        }

        function renderTrenChart() {
            const canvas = document.getElementById('trenChart');
            if (!canvas) return;

            const ctx = canvas.getContext('2d');
            const holder = canvas.closest('[data-tren-chart]');
            const labels = holder ? JSON.parse(holder.dataset.trenLabels || '[]') : [];
            const totals = holder ? JSON.parse(holder.dataset.trenTotals || '[]') : [];

            if (window._trenChart) window._trenChart.destroy();

            window._trenChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Pengeluaran',
                        data: totals,
                        borderColor: '#EF4444',
                        backgroundColor: 'rgba(239,68,68,0.08)',
                        fill: true,
                        tension: 0.4,
                        borderWidth: 2.5,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        pointBackgroundColor: '#EF4444',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        intersect: false,
                        mode: 'index'
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: '#111827',
                            padding: 10,
                            cornerRadius: 8,
                            callbacks: {
                                label: (item) => rupiah(item.parsed.y)
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                display: false
                            }
                        },
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0,0,0,0.05)'
                            },
                            ticks: {
                                callback: (v) => rupiah(v)
                            }
                        }
                    }
                }
            });
        }
    </script>
@endpush
