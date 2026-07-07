@props(['transaction', 'showActions' => false])

@php
    $categoryIcons = [
        'Gaji'       => ['bg' => 'bg-blue-100',   'text' => 'text-blue-600',   'icon' => 'briefcase'],
        'Uang Saku'  => ['bg' => 'bg-emerald-100','text' => 'text-emerald-600','icon' => 'wallet'],
        'Hadiah'     => ['bg' => 'bg-pink-100',   'text' => 'text-pink-600',   'icon' => 'gift'],
        'Bonus'      => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-600', 'icon' => 'star'],
        'Investasi'  => ['bg' => 'bg-purple-100', 'text' => 'text-purple-600', 'icon' => 'chart-bar'],
        'Penjualan'  => ['bg' => 'bg-orange-100', 'text' => 'text-orange-600', 'icon' => 'shopping-cart'],
        'Makan & Minum' => ['bg' => 'bg-orange-100','text' => 'text-orange-600','icon' => 'cake'],
        'Transportasi'  => ['bg' => 'bg-sky-100',   'text' => 'text-sky-600',  'icon' => 'truck'],
        'Jajan'         => ['bg' => 'bg-rose-100',  'text' => 'text-rose-600', 'icon' => 'sparkles'],
        'Top Up Game'   => ['bg' => 'bg-indigo-100','text' => 'text-indigo-600','icon' => 'device-phone-mobile'],
        'Belanja'       => ['bg' => 'bg-teal-100',  'text' => 'text-teal-600', 'icon' => 'shopping-bag'],
        'Pendidikan'    => ['bg' => 'bg-amber-100', 'text' => 'text-amber-600','icon' => 'academic-cap'],
        'Kesehatan'     => ['bg' => 'bg-red-100',   'text' => 'text-red-500',  'icon' => 'heart'],
        'Hiburan'       => ['bg' => 'bg-violet-100','text' => 'text-violet-600','icon' => 'tv'],
        'Tagihan'       => ['bg' => 'bg-gray-100',  'text' => 'text-gray-600', 'icon' => 'document-text'],
        'Lainnya'       => ['bg' => 'bg-gray-100',  'text' => 'text-gray-600', 'icon' => 'ellipsis-horizontal-circle'],
    ];

    $catIcon = $categoryIcons[$transaction->category_name]
        ?? ['bg' => 'bg-gray-100', 'text' => 'text-gray-600', 'icon' => 'ellipsis-horizontal-circle'];
@endphp

<div class="flex items-center justify-between bg-white border border-gray-100 rounded-xl p-3 shadow-sm">
    <div class="flex items-center gap-3">
        <div class="w-9 h-9 rounded-full {{ $catIcon['bg'] }} flex items-center justify-center shrink-0">
            <x-dynamic-component :component="'heroicon-o-' . $catIcon['icon']" class="w-4 h-4 {{ $catIcon['text'] }}" />
        </div>
        <div>
            <p class="text-sm font-medium text-gray-800">{{ $transaction->category_name }}</p>
            <div class="flex items-center gap-1.5">
                @if ($transaction->source)
                    <span class="text-[9px] font-medium text-gray-400">{{ $transaction->source }}</span>
                @endif
                @php $d = \Carbon\Carbon::parse($transaction->date); @endphp
                <p class="text-xs text-gray-400">{{ $d->format('H:i') === '00:00' ? $d->translatedFormat('d M Y') : $d->translatedFormat('d M, H:i') }}</p>
            </div>
        </div>
    </div>
    <div class="flex items-center gap-2">
        <span class="text-sm font-semibold {{ $transaction->type === 'Pemasukan' ? 'text-emerald-600' : 'text-red-500' }}">
            {{ $transaction->type === 'Pemasukan' ? '+' : '-' }}Rp {{ number_format($transaction->amount, 0, ',', '.') }}
        </span>
        @if ($showActions)
            <div class="flex items-center gap-0.5">
                <button wire:click="viewTransaction({{ $transaction->original_id }}, '{{ $transaction->type }}')"
                        class="p-1.5 rounded hover:bg-gray-100">
                    <x-heroicon-o-eye class="w-4 h-4 text-gray-400" />
                </button>
                <a href="{{ route('mobile.add-transaction', ['edit' => $transaction->original_id, 'type' => $transaction->type]) }}"
                   class="p-1.5 rounded hover:bg-gray-100">
                    <x-heroicon-o-pencil-square class="w-4 h-4 text-gray-400" />
                </a>
                <button wire:click="deleteTransaction({{ $transaction->original_id }}, '{{ $transaction->type }}')"
                        wire:confirm="Hapus transaksi ini?"
                        class="p-1.5 rounded hover:bg-red-50">
                    <x-heroicon-o-trash class="w-4 h-4 text-red-400" />
                </button>
            </div>
        @endif
    </div>
</div>
