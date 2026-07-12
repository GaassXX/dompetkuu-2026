@php
    $stats = $this->getOverviewStats();
    $budgets = $this->getBudgetCards();
    $scopeOptions = $this->getScopeOptions();
    $chartData = $this->getChartData();
@endphp

<x-filament-panels::page>
    <div class="space-y-5 report-page">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <p class="report-page-subtitle">Rencanakan pengeluaranmu</p>
            </div>
        </div>

        <div class="report-card report-toolbar-card">
            <div class="grid gap-4 md:grid-cols-[repeat(2,minmax(0,1fr))_auto] md:items-end">
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
                            @foreach ($scopeOptions as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
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

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="report-card report-stat-card" style="--accent:#22C55E;">
                <p class="report-card-label">Total Anggaran</p>
                <p class="report-stat-value" style="color:#22C55E;">Rp
                    {{ number_format($stats['total_budget'], 0, ',', '.') }}</p>
            </div>
            <div class="report-card report-stat-card" style="--accent:#EF4444;">
                <p class="report-card-label">Sudah Terpakai</p>
                <p class="report-stat-value" style="color:#EF4444;">Rp {{ number_format($stats['spent'], 0, ',', '.') }}
                </p>
            </div>
            <div class="report-card report-stat-card" style="--accent:#10B981;">
                <p class="report-card-label">Sisa Anggaran</p>
                <p class="report-stat-value" style="color:#10B981;">Rp
                    {{ number_format($stats['remaining'], 0, ',', '.') }}</p>
            </div>
        </div>

        <div class="report-tren-kategori-grid">
            <div class="report-card">
                <h3 class="report-section-title mb-4">
                    <x-heroicon-o-rectangle-stack class="w-4 h-4 report-icon-muted" />
                    Detail Anggaran
                </h3>

                @if (count($budgets) > 0)
                    <div class="space-y-4">
                        @foreach ($budgets as $budget)
                            <div class="report-budget-row">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="min-w-0 flex-1">
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <span class="font-semibold text-[#1F2937]">{{ $budget['category'] }}</span>
                                            <span
                                                class="text-[11px] px-2 py-0.5 rounded-full bg-[#FEF3C7] text-[#B45309] font-semibold">{{ $budget['percentage'] }}%</span>
                                        </div>
                                        <div class="text-xs text-[#6B7280] mt-1">{{ $budget['user_name'] }} ·
                                            {{ $budget['period'] }}</div>
                                    </div>
                                    <div class="flex items-center gap-2 flex-shrink-0">
                                        <div class="text-right text-xs text-[#6B7280] whitespace-nowrap">
                                            Rp {{ number_format($budget['spent'], 0, ',', '.') }} / Rp
                                            {{ number_format($budget['limit'], 0, ',', '.') }}
                                        </div>
                                        <div class="flex items-center gap-1.5">
                                            <a href="{{ \App\Filament\Parent\Resources\BudgetResource::getUrl('edit', ['record' => $budget['id']]) }}"
                                               class="p-2 rounded-xl bg-amber-100 text-amber-600 hover:bg-amber-200 transition shadow-sm">
                                                <x-heroicon-o-pencil class="w-4 h-4" />
                                            </a>
                                            <button wire:click="deleteBudget({{ $budget['id'] }})" wire:confirm="Yakin ingin menghapus anggaran ini?"
                                                    class="p-2 rounded-xl bg-red-100 text-red-500 hover:bg-red-200 transition shadow-sm">
                                                <x-heroicon-o-trash class="w-4 h-4" />
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="report-progress-track mt-3">
                                    <div class="report-progress-fill {{ $budget['is_over'] ? 'is-over' : '' }}"
                                        style="width:{{ $budget['percentage'] }}%"></div>
                                </div>

                                <div class="flex items-center justify-between text-xs mt-2">
                                    <span class="text-[#6B7280]">Sisa Rp
                                        {{ number_format($budget['remaining'], 0, ',', '.') }}</span>
                                    <span
                                        class="font-semibold {{ $budget['is_over'] ? ($budget['remaining'] > 0 ? 'text-red-500' : 'text-orange-500') : 'text-emerald-500' }}">
                                        @php
                                            $label = 'Terkendali';
                                            if ($budget['remaining'] <= 0 && $budget['spent'] > 0) $label = 'Habis';
                                            if ($budget['is_over'] && $budget['remaining'] < 0) $label = 'Melebihi batas!';
                                        @endphp
                                        {{ $label }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="report-empty">
                        <x-heroicon-o-inbox class="w-9 h-9 report-icon-muted mb-2" />
                        <p>Belum ada anggaran</p>
                    </div>
                @endif
            </div>

            <div class="report-card">
                <h3 class="report-section-title mb-4">
                    <x-heroicon-o-chart-bar class="w-4 h-4 report-icon-muted" />
                    Perbandingan Anggaran vs Aktual
                </h3>

                <div class="report-chart-shell">
                    <canvas id="budgetChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <span id="chart-data" class="hidden">@json($chartData)</span>

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
            padding-right: 2rem;
        }

        .report-select-wrap::after {
            content: none;
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
                grid-template-columns: minmax(0, 1.1fr) minmax(0, 1fr);
            }
        }

        .report-budget-row {
            padding: 1rem 0;
            border-bottom: 1px solid var(--color-border-tertiary, rgba(0, 0, 0, 0.06));
        }

        .report-budget-row:last-child {
            border-bottom: none;
        }

        .report-progress-track {
            width: 100%;
            height: 0.42rem;
            background: #e5efe9;
            border-radius: 999px;
            overflow: hidden;
        }

        .report-progress-fill {
            height: 100%;
            border-radius: 999px;
            background: linear-gradient(90deg, #34d399 0%, #10b981 100%);
        }

        .report-progress-fill.is-over {
            background: linear-gradient(90deg, #fb7185 0%, #ef4444 100%);
        }

        @media (max-width: 640px) {
            .report-page-title {
                font-size: 1.5rem;
                line-height: 2rem;
            }
        }
    </style>
</x-filament-panels::page>

@push('scripts')
    <script>
        document.addEventListener('livewire:navigated', initBudgetCharts);
        document.addEventListener('livewire:init', initBudgetCharts);
        document.addEventListener('anggaran-refresh', scheduleBudgetChartRefresh);

        function initBudgetCharts() {
            if (typeof Chart === 'undefined') {
                var s = document.createElement('script');
                s.src = 'https://cdn.jsdelivr.net/npm/chart.js';
                s.onload = renderBudgetCharts;
                document.head.appendChild(s);
            } else {
                scheduleBudgetChartRefresh();
            }
        }

        function scheduleBudgetChartRefresh() {
            window.requestAnimationFrame(() => {
                setTimeout(renderBudgetCharts, 50);
            });
        }

        function rupiah(v) {
            return 'Rp' + Number(v).toLocaleString('id-ID');
        }

        function renderBudgetCharts() {
            const canvas = document.getElementById('budgetChart');
            if (!canvas) return;

            const chartDataEl = document.getElementById('chart-data');
            if (!chartDataEl) return;
            const payload = JSON.parse(chartDataEl.textContent);

            const holder = canvas.closest('.report-chart-shell');
            const labels = payload.labels || [];
            const budgets = payload.budget || [];
            const spent = payload.spent || [];

            if (window._budgetChart) window._budgetChart.destroy();

            window._budgetChart = new Chart(canvas.getContext('2d'), {
                type: 'bar',
                data: {
                    labels,
                    datasets: [{
                            label: 'Anggaran',
                            data: budgets,
                            backgroundColor: 'rgba(99,102,241,0.3)',
                            borderColor: '#6366F1',
                            borderWidth: 1.5,
                            borderRadius: 8,
                        },
                        {
                            label: 'Aktual',
                            data: spent,
                            backgroundColor: 'rgba(251,113,133,0.35)',
                            borderColor: '#FB7185',
                            borderWidth: 1.5,
                            borderRadius: 8,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top'
                        },
                        tooltip: {
                            callbacks: {
                                label: (item) => `${item.dataset.label}: ${rupiah(item.parsed.y)}`,
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
                                color: 'rgba(0,0,0,0.06)'
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
