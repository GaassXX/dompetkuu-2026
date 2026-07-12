<div class="min-h-screen bg-gray-50 text-gray-900 pb-28">

    {{-- Header --}}
    <div class="px-4 pt-4 pb-2">
        <h1 class="font-bold text-gray-800 text-lg">Anggaran</h1>
        <p class="text-xs text-gray-400 mt-0.5">Kelola batas pengeluaran per kategori</p>
    </div>

    {{-- Filter --}}
    <div class="mx-4 bg-white border border-gray-100 rounded-xl p-3 shadow-sm">
        <div class="grid {{ $this->isParent() ? 'grid-cols-2 gap-3' : 'grid-cols-1' }}">
            <div>
                <label class="text-[11px] font-medium text-gray-400 mb-1 block">Periode</label>
                <select wire:model.live="periode" class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2 bg-white text-gray-700">
                    @foreach ($this->periodeOptions as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            @if ($this->isParent())
                <div>
                    <label class="text-[11px] font-medium text-gray-400 mb-1 block">Data</label>
                    <select wire:model.live="scope" class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2 bg-white text-gray-700">
                        @foreach ($this->scopeOptions as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            @endif
        </div>
    </div>

    @php
        $stats = $this->getOverviewStats();
        $cards = $this->getBudgetCards();
        $alerts = $this->getAlerts();
        $chart = $this->getChartData();
    @endphp

    {{-- Stat Cards --}}
    <div class="grid grid-cols-3 gap-2.5 mx-4 mt-3">
        <div class="bg-white border border-gray-100 rounded-xl p-3 shadow-sm">
            <p class="text-[10px] text-gray-400">Anggaran</p>
            <p class="text-xs font-bold text-emerald-600 truncate">Rp {{ number_format($stats['total_budget'], 0, ',', '.') }}</p>
        </div>
        <div class="bg-white border border-gray-100 rounded-xl p-3 shadow-sm">
            <p class="text-[10px] text-gray-400">Terpakai</p>
            <p class="text-xs font-bold {{ $stats['spent'] > $stats['total_budget'] ? 'text-red-500' : 'text-amber-600' }} truncate">Rp {{ number_format($stats['spent'], 0, ',', '.') }}</p>
        </div>
        <div class="bg-white border border-gray-100 rounded-xl p-3 shadow-sm">
            <p class="text-[10px] text-gray-400">Sisa</p>
            <p class="text-xs font-bold {{ $stats['remaining'] > 0 ? 'text-emerald-600' : 'text-red-500' }} truncate">Rp {{ number_format($stats['remaining'], 0, ',', '.') }}</p>
        </div>
    </div>

    {{-- Alerts --}}
    @if (count($alerts) > 0)
        <div class="mx-4 mt-3 space-y-1.5">
            @foreach ($alerts as $alert)
                <div class="flex items-start gap-2 {{ $alert['is_over'] ? 'bg-red-50' : 'bg-amber-50' }} rounded-xl px-3 py-2.5">
                    <x-heroicon-o-exclamation-triangle class="w-4 h-4 mt-0.5 {{ $alert['is_over'] ? 'text-red-500' : 'text-amber-500' }} shrink-0" />
                    <div>
                        <p class="text-[11px] font-medium {{ $alert['is_over'] ? 'text-red-700' : 'text-amber-700' }}">
                            {{ $alert['category'] }} {{ $alert['is_over'] ? 'Melebihi batas!' : 'Hampir mencapai batas' }}
                        </p>
                        <p class="text-[10px] {{ $alert['is_over'] ? 'text-red-500' : 'text-amber-600' }}">
                            Rp {{ number_format($alert['spent'], 0, ',', '.') }} / Rp {{ number_format($alert['limit'], 0, ',', '.') }} ({{ $alert['percentage'] == (int)$alert['percentage'] ? number_format($alert['percentage']) : number_format($alert['percentage'], 1) }}%)
                        </p>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    {{-- Budget List --}}
    <div class="mx-4 mt-5">
        @if (count($cards) > 0)
            <div class="space-y-2.5">
                @foreach ($cards as $item)
                    @php
                        $barColor = $item['percentage'] >= 100 ? 'bg-red-500' : ($item['percentage'] >= 80 ? 'bg-amber-400' : 'bg-emerald-400');
                    @endphp
                    <div class="bg-white border border-gray-100 rounded-xl p-3 shadow-sm">
                        <div class="flex items-start justify-between mb-1">
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-2">
                                    <p class="text-sm font-semibold text-gray-800 truncate">{{ $item['category'] }}</p>
                                    <span class="text-[10px] font-medium px-1.5 py-0.5 rounded-full
                                        {{ $item['percentage'] >= 100 ? 'bg-red-100 text-red-600' : ($item['percentage'] >= 80 ? 'bg-amber-100 text-amber-700' : 'bg-emerald-100 text-emerald-700') }}">
                                        {{ $item['percentage'] }}%
                                    </span>
                                </div>
                                @if ($this->isParent())
                                    <p class="text-[10px] text-gray-400">{{ $item['user_name'] }} • {{ $item['period'] }}</p>
                                @else
                                    <p class="text-[10px] text-gray-400">{{ $item['period'] }}</p>
                                @endif
                            </div>
                            @if ($this->canManageBudget($item['user_id']))
                                <div class="flex items-center gap-1 shrink-0">
                                    <button wire:click="openEditForm({{ $item['id'] }})" class="p-1 rounded hover:bg-gray-100">
                                        <x-heroicon-o-pencil-square class="w-4 h-4 text-gray-600" />
                                    </button>
                                    <button wire:click="deleteBudget({{ $item['id'] }})" wire:confirm="Hapus anggaran ini?" class="p-1 rounded hover:bg-red-50">
                                        <x-heroicon-o-trash class="w-4 h-4 text-red-400" />
                                    </button>
                                </div>
                            @endif
                        </div>

                        <div class="w-full bg-gray-100 rounded-full h-1.5 mb-1.5">
                            <div class="{{ $barColor }} h-1.5 rounded-full" style="width: {{ $item['percentage'] }}%"></div>
                        </div>

                        <div class="flex items-center justify-between text-[11px]">
                            <span class="text-gray-500">
                                <span class="{{ $item['is_over'] ? 'text-red-500 font-medium' : 'text-gray-700' }}">Rp {{ number_format($item['spent'], 0, ',', '.') }}</span>
                                / Rp {{ number_format($item['limit'], 0, ',', '.') }}
                            </span>
                            <span class="{{ $item['is_over'] ? 'text-red-500 font-medium' : 'text-emerald-600' }}">
                                @if ($item['is_over'])
                                    Over Rp {{ number_format(abs($item['spent'] - $item['limit']), 0, ',', '.') }}
                                @else
                                    Sisa Rp {{ number_format($item['remaining'], 0, ',', '.') }}
                                @endif
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="bg-white border border-gray-100 rounded-xl p-6 text-center shadow-sm">
                <div class="w-12 h-12 rounded-full bg-amber-100 flex items-center justify-center mx-auto mb-2">
                    <x-heroicon-o-wallet class="w-6 h-6 text-amber-600" />
                </div>
                <p class="text-xs font-medium text-gray-500">Belum ada anggaran</p>
                <p class="text-[10px] text-gray-400 mt-1">Buat anggaran untuk mengontrol pengeluaran</p>
                @if ($this->isParent() || (Auth::user()->is_independent ?? false))
                    <button wire:click="openAddForm" class="inline-flex items-center gap-1.5 text-xs font-semibold bg-amber-500 text-white px-3 py-1.5 rounded-full mt-3">
                        <x-heroicon-o-plus class="w-3.5 h-3.5" />
                        Buat Anggaran
                    </button>
                @endif
            </div>
        @endif
    </div>

    {{-- Chart --}}
    @if (count($chart['labels']) > 0)
        <div class="mx-4 mt-5 bg-white border border-gray-100 rounded-xl p-4 shadow-sm">
            <p class="text-sm font-semibold text-gray-800 mb-3">Anggaran vs Aktual</p>
            <div wire:ignore>
                <div style="height: 200px; position: relative;">
                    <canvas x-init="
                        new Chart($el, {
                            type: 'bar',
                            data: {
                                labels: @js($chart['labels']),
                                datasets: [
                                    {
                                        label: 'Anggaran',
                                        data: @js($chart['budget']),
                                        backgroundColor: 'rgba(16,185,129,0.7)',
                                        borderColor: '#10B981',
                                        borderWidth: 1.5,
                                        borderRadius: 4,
                                    },
                                    {
                                        label: 'Aktual',
                                        data: @js($chart['spent']),
                                        backgroundColor: 'rgba(239,68,68,0.7)',
                                        borderColor: '#EF4444',
                                        borderWidth: 1.5,
                                        borderRadius: 4,
                                    }
                                ]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                interaction: { intersect: false, mode: 'index' },
                                plugins: {
                                    legend: {
                                        display: true,
                                        position: 'top',
                                        labels: { usePointStyle: true, padding: 12, font: { size: 10 } }
                                    },
                                    tooltip: {
                                        backgroundColor: '#1E293B',
                                        padding: 8,
                                        cornerRadius: 6,
                                        callbacks: {
                                            label: (ctx) => ctx.dataset.label + ': Rp ' + Number(ctx.parsed.y).toLocaleString('id-ID')
                                        }
                                    }
                                },
                                scales: {
                                    x: {
                                        grid: { display: false },
                                        ticks: { font: { size: 8 } }
                                    },
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
        </div>
    @endif

    {{-- FAB Tambah --}}
    @if ($this->isParent() || (Auth::user()->is_independent ?? false))
        <div class="mx-4 mt-5">
            <button wire:click="openAddForm"
                class="w-full flex items-center justify-center gap-2 bg-amber-500 text-white text-sm font-semibold py-3 rounded-xl shadow-sm hover:bg-amber-600 transition">
                <x-heroicon-o-plus class="w-4 h-4" />
                Tambah Anggaran
            </button>
        </div>
    @endif

    {{-- Bottom Sheet Add/Edit --}}
    @if ($showForm)
        <div class="fixed inset-0 z-50 flex items-end md:items-center md:justify-center">
            <div class="absolute inset-0 bg-black/40" wire:click="closeForm"></div>
            <div class="relative bg-white w-full md:w-96 rounded-t-2xl md:rounded-2xl p-4 pb-6">
                <div class="flex items-center justify-between mb-4">
                    <p class="text-sm font-semibold text-gray-800">{{ $editBudgetId ? 'Edit Anggaran' : 'Tambah Anggaran' }}</p>
                    <button wire:click="showForm = false" class="p-1">
                        <x-heroicon-o-x-mark class="w-5 h-5 text-gray-400" />
                    </button>
                </div>

                <div class="space-y-3">
                    <div>
                        <label class="text-[11px] font-medium text-gray-400 mb-1 block">Kategori</label>
                        <select wire:model="formCategoryId"
                                class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2 bg-white text-gray-700">
                            <option value="">Pilih kategori</option>
                            @foreach ($expenseCategories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                        @error('formCategoryId') <p class="text-[11px] text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="text-[11px] font-medium text-gray-400 mb-1 block">Batas Limit (Rp)</label>
                        <input type="text" inputmode="numeric" placeholder="0"
                               x-data="{ display: '{{ $formLimit ? number_format((int)$formLimit, 0, ',', '.') : '' }}' }"
                               x-model="display"
                               x-on:input="
                                   let raw = $el.value.replace(/\./g, '').replace(/\D/g, '');
                                   display = raw.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                                   $wire.set('formLimit', raw);
                               "
                               class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2 bg-white">
                        @error('formLimit') <p class="text-[11px] text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="text-[11px] font-medium text-gray-400 mb-1 block">Periode</label>
                        <select wire:model="formPeriod"
                                class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2 bg-white text-gray-700">
                            <option value="monthly">Bulanan</option>
                            <option value="weekly">Mingguan</option>
                        </select>
                    </div>

                    <div class="flex gap-2 pt-2">
                        <button wire:click="closeForm"
                                class="flex-1 text-sm font-medium text-red-500 bg-red-50 rounded-xl py-2.5 hover:bg-red-100 transition">
                            Batal
                        </button>
                        <button wire:click="saveBudget"
                                class="flex-1 text-sm font-semibold text-gray-900 bg-gray-100 rounded-xl py-2.5 hover:bg-gray-200 transition">
                            Simpan
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <x-mobile.bottom-nav active="anggaran" />
</div>
