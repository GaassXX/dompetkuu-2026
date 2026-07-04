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
                        <x-mobile.transaction-item :transaction="$tx" :showActions="true" />
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

    {{-- Detail Bottom Sheet --}}
    @if ($showDetail && $detailTransaction)
        @php $d = \Carbon\Carbon::parse($detailTransaction->date); @endphp
        <div class="fixed inset-0 z-50 flex items-end md:items-center md:justify-center">
            <div class="absolute inset-0 bg-black/40" wire:click="closeDetail"></div>
            <div class="relative bg-white w-full md:w-96 rounded-t-2xl md:rounded-2xl p-5 pb-6">
                <div class="flex items-center justify-between mb-4">
                    <p class="text-sm font-semibold text-gray-800">Detail Transaksi</p>
                    <button wire:click="closeDetail" class="p-1">
                        <x-heroicon-o-x-mark class="w-5 h-5 text-gray-400" />
                    </button>
                </div>

                <div class="flex flex-col items-center mb-4">
                    <p class="text-lg font-bold text-gray-800">{{ $detailTransaction->category_name }}</p>
                    <span class="text-xs font-medium px-2.5 py-0.5 rounded-full mt-1 {{ $detailTransaction->type === 'Pemasukan' ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-600' }}">
                        {{ $detailTransaction->type }}
                    </span>
                </div>

                <p class="text-2xl font-bold text-center {{ $detailTransaction->type === 'Pemasukan' ? 'text-emerald-600' : 'text-red-500' }}">
                    {{ $detailTransaction->type === 'Pemasukan' ? '+' : '-' }}Rp {{ number_format($detailTransaction->amount, 0, ',', '.') }}
                </p>
                <div class="mt-4 bg-gray-50 rounded-xl p-3 space-y-2 text-xs">
                    <div class="flex justify-between">
                        <span class="text-gray-400">Kategori</span>
                        <span class="text-gray-700 font-medium">{{ $detailTransaction->category_name }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-400">Tipe</span>
                        <span class="text-gray-700 font-medium">{{ $detailTransaction->type }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-400">Tanggal</span>
                        <span class="text-gray-700 font-medium">{{ $d->translatedFormat('d F Y, H:i') }}</span>
                    </div>
                    @if ($detailTransaction->description)
                    <div class="flex justify-between">
                        <span class="text-gray-400">Catatan</span>
                        <span class="text-gray-700 font-medium">{{ $detailTransaction->description }}</span>
                    </div>
                    @endif
                    <div class="flex justify-between">
                        <span class="text-gray-400">Status</span>
                        <span class="text-emerald-600 font-medium">Berhasil</span>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <x-mobile.bottom-nav active="history" />
</div>
