@php
    $formatRp = fn ($n) => 'Rp ' . number_format((float) $n, 0, ',', '.');
@endphp

<div class="min-h-screen bg-gray-50 pb-28">

    {{-- Header --}}
    <div class="bg-gradient-to-r from-amber-500 to-orange-500 px-5 pt-12 pb-6 -mt-2">
        <h1 class="text-xl font-extrabold text-white">✅ Persetujuan</h1>
        <p class="text-amber-100 text-sm mt-1">
            {{ $pendingCount }} transaksi menunggu persetujuan
        </p>
    </div>

    {{-- Filter --}}
    <div class="flex gap-2 px-4 -mt-3 mb-3">
        <button wire:click="$set('filter', 'all')"
            class="text-xs font-semibold px-3 py-1.5 rounded-full transition {{ $filter === 'all' ? 'bg-amber-500 text-white' : 'bg-white text-gray-500 border border-gray-200' }}">
            Semua
        </button>
        <button wire:click="$set('filter', 'income')"
            class="text-xs font-semibold px-3 py-1.5 rounded-full transition {{ $filter === 'income' ? 'bg-emerald-500 text-white' : 'bg-white text-gray-500 border border-gray-200' }}">
            Pemasukan
        </button>
        <button wire:click="$set('filter', 'expense')"
            class="text-xs font-semibold px-3 py-1.5 rounded-full transition {{ $filter === 'expense' ? 'bg-red-500 text-white' : 'bg-white text-gray-500 border border-gray-200' }}">
            Pengeluaran
        </button>
    </div>

    {{-- List --}}
    <div class="px-4 space-y-2.5">
        @forelse ($transactions as $tx)
            <div class="bg-white border border-gray-100 rounded-xl p-3.5 shadow-sm">
                <div class="flex items-start justify-between mb-2">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-full {{ $tx['type'] === 'Pemasukan' ? 'bg-emerald-100' : 'bg-red-100' }} flex items-center justify-center shrink-0">
                            <x-dynamic-component :component="'heroicon-o-' . ($tx['type'] === 'Pemasukan' ? 'arrow-down' : 'arrow-up')"
                                class="w-4 h-4 {{ $tx['type'] === 'Pemasukan' ? 'text-emerald-600' : 'text-red-500' }}" />
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-800">{{ $tx['description'] ?: $tx['category'] }}</p>
                            <p class="text-[11px] text-gray-400">
                                <span class="font-medium text-gray-500">{{ $tx['child_name'] }}</span>
                                &middot; {{ $tx['category'] }}
                                &middot; {{ \Carbon\Carbon::parse($tx['date'])->translatedFormat('d M') }}
                            </p>
                        </div>
                    </div>
                    <span class="text-sm font-bold {{ $tx['type'] === 'Pemasukan' ? 'text-emerald-600' : 'text-red-500' }} shrink-0">
                        {{ $tx['type'] === 'Pemasukan' ? '+' : '-' }}{{ $formatRp($tx['amount']) }}
                    </span>
                </div>
                <div class="flex gap-2">
                    <button wire:click="approve('{{ $tx['id'] }}')" wire:confirm="Setujui transaksi ini?"
                        class="flex-1 flex items-center justify-center gap-1.5 text-xs font-semibold bg-emerald-500 text-white py-2 rounded-xl hover:bg-emerald-600 transition">
                        <x-heroicon-o-check class="w-3.5 h-3.5" />
                        Setujui
                    </button>
                    <button wire:click="reject('{{ $tx['id'] }}')" wire:confirm="Tolak transaksi ini?"
                        class="flex-1 flex items-center justify-center gap-1.5 text-xs font-semibold bg-white text-red-500 border border-red-200 py-2 rounded-xl hover:bg-red-50 transition">
                        <x-heroicon-o-x-mark class="w-3.5 h-3.5" />
                        Tolak
                    </button>
                </div>
            </div>
        @empty
            <div class="text-center py-16">
                <div class="w-16 h-16 rounded-full bg-emerald-100 flex items-center justify-center mx-auto mb-3">
                    <x-heroicon-o-check-circle class="w-8 h-8 text-emerald-500" />
                </div>
                <p class="text-sm font-medium text-gray-500">Tidak ada transaksi menunggu</p>
                <p class="text-xs text-gray-400 mt-1">Semua transasi anak sudah diproses</p>
            </div>
        @endforelse
    </div>

    <x-mobile.bottom-nav active="dashboard" />
</div>
