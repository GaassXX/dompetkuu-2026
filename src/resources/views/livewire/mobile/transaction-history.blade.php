<div class="min-h-screen bg-gray-50 text-gray-900 pb-28">

    {{-- Tabs Filter --}}
    <div class="flex gap-2 mx-4 mt-2">
        <button wire:click="setFilter('semua')"
            class="px-4 py-1.5 rounded-full text-xs font-medium flex items-center gap-1
            {{ $filter === 'semua' ? 'bg-amber-500 text-white' : 'bg-white border border-gray-200 text-gray-500' }}">
            <x-heroicon-o-bars-3 class="w-3.5 h-3.5" />
            Semua
        </button>
        <button wire:click="setFilter('pengeluaran')"
            class="px-4 py-1.5 rounded-full text-xs font-medium flex items-center gap-1
            {{ $filter === 'pengeluaran' ? 'bg-amber-500 text-white' : 'bg-white border border-gray-200 text-gray-500' }}">
            <x-heroicon-o-arrow-trending-up class="w-3.5 h-3.5" />
            Pengeluaran
        </button>
        <button wire:click="setFilter('pemasukan')"
            class="px-4 py-1.5 rounded-full text-xs font-medium flex items-center gap-1
            {{ $filter === 'pemasukan' ? 'bg-amber-500 text-white' : 'bg-white border border-gray-200 text-gray-500' }}">
            <x-heroicon-o-arrow-trending-down class="w-3.5 h-3.5" />
            Pemasukan
        </button>
    </div>

    {{-- Filter Bulan --}}
    <div class="mx-4 mt-3">
        <select wire:model.live="periode"
                class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2 bg-white text-gray-700">
            @foreach ($this->periodeOptions as $value => $label)
                <option value="{{ $value }}">{{ $label }}</option>
            @endforeach
        </select>
    </div>

    {{-- Transaction List --}}
    <div class="mx-4 mt-3 space-y-4">
        @forelse ($grouped as $label => $transactions)
            <div>
                <p class="text-[10px] font-semibold text-gray-400 tracking-wider mb-2">{{ $label }}</p>
                <div class="space-y-1.5">
                    @foreach ($transactions as $tx)
                        <x-mobile.transaction-item :transaction="$tx" />
                    @endforeach
                </div>
            </div>
        @empty
            <p class="text-center text-xs text-gray-400 py-10">Belum ada transaksi</p>
        @endforelse
    </div>

    {{-- FAB Tambah Transaksi --}}
    <a href="{{ route('mobile.add-transaction') }}"
       class="fixed right-5 bottom-24 z-40 w-12 h-12 rounded-full bg-emerald-600 text-white flex items-center justify-center shadow-lg shadow-emerald-600/30 hover:bg-emerald-700 transition">
        <x-heroicon-o-plus class="w-6 h-6" />
    </a>

    <x-mobile.bottom-nav active="history" />
</div>
