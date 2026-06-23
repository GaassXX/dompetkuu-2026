<div class="min-h-screen bg-gray-50 text-gray-900 pb-28">

    {{-- Header --}}
    <div class="flex items-center justify-between px-4 pt-4 pb-2">
        <div class="flex items-center gap-2">
            <div class="w-7 h-7 rounded-lg bg-emerald-600 flex items-center justify-center shrink-0">
                <x-heroicon-o-banknotes class="w-4 h-4 text-white" />
            </div>
            <span class="font-bold text-gray-800">Dompetkuu</span>
        </div>
        <x-heroicon-o-bell class="w-5 h-5 text-gray-400" />
    </div>

    {{-- Tabs Filter --}}
    <div class="flex gap-2 mx-4 mt-2">
        <button wire:click="setFilter('semua')"
            class="px-4 py-1.5 rounded-full text-xs font-medium flex items-center gap-1
            {{ $filter === 'semua' ? 'bg-emerald-600 text-white' : 'bg-white border border-gray-200 text-gray-500' }}">
            <x-heroicon-o-bars-3 class="w-3.5 h-3.5" />
            Semua
        </button>
        <button wire:click="setFilter('pengeluaran')"
            class="px-4 py-1.5 rounded-full text-xs font-medium flex items-center gap-1
            {{ $filter === 'pengeluaran' ? 'bg-emerald-600 text-white' : 'bg-white border border-gray-200 text-gray-500' }}">
            <x-heroicon-o-arrow-up class="w-3.5 h-3.5" />
            Pengeluaran
        </button>
        <button wire:click="setFilter('pemasukan')"
            class="px-4 py-1.5 rounded-full text-xs font-medium flex items-center gap-1
            {{ $filter === 'pemasukan' ? 'bg-emerald-600 text-white' : 'bg-white border border-gray-200 text-gray-500' }}">
            <x-heroicon-o-arrow-down class="w-3.5 h-3.5" />
            Pemasukan
        </button>
    </div>

    {{-- List Grouped by Date --}}
    <div class="mx-4 mt-4 space-y-5">
        @forelse ($grouped as $label => $items)
            <div>
                <p class="text-[11px] text-gray-400 tracking-wide mb-2 font-medium">{{ strtoupper($label) }}</p>
                <div class="space-y-2">
                    @foreach ($items as $trx)
                        <x-mobile.transaction-item :transaction="$trx" />
                    @endforeach
                </div>
            </div>
        @empty
            <p class="text-xs text-gray-400 text-center py-10">Belum ada transaksi</p>
        @endforelse
    </div>

    {{-- Tombol tambah transaksi --}}
    <a href="{{ route('mobile.add-transaction') }}" class="fixed bottom-24 right-4 w-12 h-12 bg-emerald-600 rounded-full flex items-center justify-center shadow-lg shadow-emerald-200 z-10">
        <x-heroicon-o-plus class="w-6 h-6 text-white" />
    </a>

    <x-mobile.bottom-nav active="history" />
</div>
