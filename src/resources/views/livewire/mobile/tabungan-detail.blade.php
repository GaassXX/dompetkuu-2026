@php
    $formatRp = fn ($n) => 'Rp ' . number_format((float) $n, 0, ',', '.');
    $barColor = $progress >= 100 ? 'bg-amber-400' : ($progress >= 60 ? 'bg-yellow-400' : 'bg-amber-500');

    $statusLabel = match ($saving->status) {
        'active'    => 'Aktif',
        'completed' => 'Selesai',
        'cancelled' => 'Dibatalkan',
        default     => $saving->status,
    };
    $statusColor = match ($saving->status) {
        'active'    => 'bg-emerald-100 text-emerald-700',
        'completed' => 'bg-blue-100 text-blue-700',
        'cancelled' => 'bg-red-100 text-red-700',
        default     => 'bg-gray-100 text-gray-600',
    };
@endphp

<div class="min-h-screen bg-gray-50 text-gray-900 pb-28">

    {{-- Header --}}
    <div class="flex items-center justify-between px-4 pt-4 pb-2" x-data="{ menu: false }">
        <div class="flex items-center gap-3">
            <a href="{{ route('mobile.tabungan') }}" class="w-8 h-8 rounded-full bg-white border border-gray-200 flex items-center justify-center">
                <x-heroicon-o-arrow-left class="w-4 h-4 text-gray-500" />
            </a>
            <span class="font-bold text-gray-800 truncate">{{ $saving->name }}</span>
        </div>

        <div class="relative">
            <button @click="menu = !menu" class="w-8 h-8 rounded-full bg-white border border-gray-200 flex items-center justify-center hover:bg-gray-50 transition">
                <x-heroicon-o-ellipsis-vertical class="w-4 h-4 text-gray-500" />
            </button>

            <div x-show="menu" @click.away="menu = false"
                 x-transition:enter="transition ease-out duration-100"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-75"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 class="absolute right-0 top-full mt-1 w-44 bg-white border border-gray-100 rounded-xl shadow-lg z-50 py-1.5"
                 style="display: none;">
                <a href="{{ route('mobile.tabungan.create', ['edit' => $saving->id]) }}"
                   class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-amber-50 transition-colors">
                    <x-heroicon-o-pencil class="w-4 h-4 text-gray-400" />
                    Edit Tabungan
                </a>
                <button wire:click="delete" wire:confirm="Hapus tabungan? Semua setoran juga akan dihapus."
                    class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-red-500 hover:bg-red-50 transition-colors">
                    <x-heroicon-o-trash class="w-4 h-4 text-gray-400" />
                    Hapus Tabungan
                </button>
            </div>
        </div>
    </div>

    @if (session('success'))
        <div class="mx-4 mt-2 bg-amber-50 text-amber-700 text-xs font-medium px-3 py-2 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    {{-- Ringkasan --}}
    <div class="mx-4 mt-3 bg-white border border-gray-100 rounded-xl p-4 shadow-sm">
        <div class="flex items-center justify-between mb-3">
            <span class="text-[11px] font-semibold uppercase tracking-wide text-gray-400">{{ $saving->category }}</span>
            <span class="text-[10px] font-semibold px-2 py-1 rounded-full {{ $statusColor }}">{{ $statusLabel }}</span>
        </div>

        <p class="text-xs text-gray-400">Terkumpul</p>
        <p class="text-xl font-bold text-gray-800">{{ $formatRp($saving->current_amount) }}</p>
        <p class="text-xs text-gray-400 mb-2">dari target {{ $formatRp($saving->target_amount) }}</p>

        <div class="w-full bg-gray-100 rounded-full h-2 mb-1.5">
            <div class="{{ $barColor }} h-2 rounded-full" style="width: {{ $progress }}%"></div>
        </div>
        <div class="flex justify-between text-[11px] text-gray-400">
            <span>{{ $progress }}% tercapai</span>
            @if ($saving->target_date)
                <span>Target: {{ $saving->target_date->translatedFormat('d M Y') }}</span>
            @endif
        </div>

        @if ($saving->description)
            <p class="text-xs text-gray-500 mt-3 pt-3 border-t border-gray-100">{{ $saving->description }}</p>
        @endif
    </div>

    {{-- Tombol setor --}}
    @if ($saving->status === 'active')
        <div class="mx-4 mt-3">
            <button wire:click="toggleDepositForm"
                class="w-full flex items-center justify-center gap-2 bg-amber-500 text-white text-sm font-semibold py-2.5 rounded-xl shadow-sm hover:bg-amber-600 transition">
                <x-heroicon-o-plus-circle class="w-4 h-4" />
                Tambah Setoran
            </button>
        </div>

        @if ($showDepositForm)
            <form wire:submit="setor" class="mx-4 mt-2.5 bg-white border border-gray-100 rounded-xl p-4 shadow-sm space-y-3">
                <div>
                    <label class="text-xs font-medium text-gray-500">Jumlah Setoran</label>
                    <div class="mt-1 flex items-center rounded-lg border border-gray-200 focus-within:border-amber-500 focus-within:ring-1 focus-within:ring-amber-500">
                        <span class="pl-3 text-sm text-gray-400">Rp</span>
                        <input type="text" inputmode="numeric" placeholder="0"
                               x-data="{ display: '{{ $amount ? number_format((int)$amount, 0, ',', '.') : '' }}' }"
                               x-model="display"
                               x-on:input="
                                   let raw = $el.value.replace(/\./g, '').replace(/\D/g, '');
                                   display = raw.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                                   $wire.set('amount', raw);
                               "
                               class="w-full text-sm border-0 focus:ring-0 bg-transparent">
                     </div>
                     @error('amount') <p class="text-[11px] text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="text-xs font-medium text-gray-500">Tanggal</label>
                    <input type="date" wire:model="date"
                           class="mt-1 w-full text-sm rounded-lg border-gray-200 focus:border-amber-500 focus:ring-amber-500">
                </div>
                <div>
                    <label class="text-xs font-medium text-gray-500">Catatan (Opsional)</label>
                    <input type="text" wire:model="note" placeholder="Catatan..."
                           class="mt-1 w-full text-sm rounded-lg border-gray-200 focus:border-amber-500 focus:ring-amber-500">
                </div>
                <button type="submit"
                    class="w-full bg-amber-500 text-white text-sm font-semibold py-2.5 rounded-xl">
                    Simpan Setoran
                </button>
            </form>
        @endif
    @endif

    {{-- Riwayat setoran goal ini --}}
    <div class="mx-4 mt-5">
        <p class="text-[11px] text-gray-400 tracking-wide mb-2 font-medium">RIWAYAT SETORAN</p>

        @if ($deposits->count() > 0)
            <div class="space-y-2">
                @foreach ($deposits as $deposit)
                    <div class="flex items-center justify-between bg-white border border-gray-100 rounded-xl p-3 shadow-sm">
                        <div>
                            <p class="text-xs text-gray-400">{{ $deposit->date->translatedFormat('d M Y') }}</p>
                            @if ($deposit->note)
                                <p class="text-xs text-gray-500 mt-0.5">{{ $deposit->note }}</p>
                            @endif
                        </div>
                        <span class="text-sm font-semibold text-emerald-600">+{{ $formatRp($deposit->amount) }}</span>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-xs text-gray-400 text-center py-10">Belum ada setoran</p>
        @endif
    </div>

    <x-mobile.bottom-nav active="tabungan" />
</div>
