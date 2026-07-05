@props(['active' => 'dashboard'])

@php
$user = auth()->user();
$isParent = $user && $user->hasRole('parent');
$panel = $isParent ? 'parent' : 'child';

$menuItems = $isParent ? [
    ['label' => 'Laporan',          'icon' => 'document-chart-bar',      'url' => route('mobile.laporan')],
    ['label' => 'Tabungan',         'icon' => 'banknotes',              'url' => route('mobile.tabungan')],
    ['label' => 'Anggaran',         'icon' => 'wallet',                 'url' => route('mobile.anggaran')],
    ['label' => 'Anggota Keluarga', 'icon' => 'user-group',             'url' => route('mobile.keluarga')],
] : [
    ['label' => 'Laporan',          'icon' => 'document-chart-bar',      'url' => route('mobile.laporan')],
    ['label' => 'Tabungan',         'icon' => 'banknotes',              'url' => route('mobile.tabungan')],
    ['label' => 'Anggaran',         'icon' => 'wallet',                 'url' => route('mobile.anggaran')],
];
@endphp

<div class="flex items-center justify-between px-4 pt-4 pb-2" x-data="{ open: false }">
    <div class="flex items-center gap-2">
        <div class="w-7 h-7 rounded-lg flex items-center justify-center shrink-0"
             style="background:linear-gradient(135deg,#F59E0B,#EA580C);">
            <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 24 24">
                <path d="M21 18v1a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h14a2 2 0 012 2v1h-9a2 2 0 00-2 2v8a2 2 0 002 2h9zm-9-2h10V8H12v8zm4-2.5a1.5 1.5 0 110-3 1.5 1.5 0 010 3z"/>
            </svg>
        </div>
        <span class="font-bold text-gray-800 text-sm tracking-tight">Dompet<span class="text-amber-500">Kuu</span></span>
    </div>
    <div class="flex items-center gap-1">
        <x-heroicon-o-bell class="w-5 h-5 text-gray-400" />
        <div class="relative">
            <button @click="open = !open" class="p-1.5 rounded-lg hover:bg-gray-100 transition-colors">
                <x-heroicon-o-ellipsis-vertical class="w-5 h-5 text-gray-500" />
            </button>
            <div x-show="open"
                 x-on:click.away="open = false"
                 x-transition:enter="transition ease-out duration-100"
                 x-transition:enter-start="transform opacity-0 scale-95"
                 x-transition:enter-end="transform opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-75"
                 x-transition:leave-start="transform opacity-100 scale-100"
                 x-transition:leave-end="transform opacity-0 scale-95"
                 class="absolute right-0 top-full mt-2 w-52 bg-white border border-gray-100 rounded-xl shadow-lg z-50 py-1.5"
                 style="display: none;">
                <p class="px-4 pt-1 pb-1 text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Menu</p>
                <a href="{{ route('mobile.dashboard') }}"
                   class="flex items-center gap-3 px-4 py-2.5 text-sm {{ $active === 'dashboard' ? 'text-amber-600 font-medium' : 'text-gray-700' }} hover:bg-amber-50 hover:text-amber-700 transition-colors">
                    <x-heroicon-o-squares-2x2 class="w-4 h-4 text-gray-400 shrink-0" />
                    Dashboard
                </a>
                <a href="{{ route('mobile.history') }}"
                   class="flex items-center gap-3 px-4 py-2.5 text-sm {{ $active === 'history' ? 'text-amber-600 font-medium' : 'text-gray-700' }} hover:bg-amber-50 hover:text-amber-700 transition-colors">
                    <x-heroicon-o-clock class="w-4 h-4 text-gray-400 shrink-0" />
                    Riwayat Transaksi
                </a>
                @foreach ($menuItems as $item)
                    <a href="{{ $item['url'] }}"
                       class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-amber-50 hover:text-amber-700 transition-colors">
                        <x-dynamic-component :component="'heroicon-o-' . $item['icon']" class="w-4 h-4 text-gray-400 shrink-0" />
                        {{ $item['label'] }}
                    </a>
                @endforeach
                <a href="{{ route('mobile.panduan') }}"
                   class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-amber-50 hover:text-amber-700 transition-colors">
                    <x-heroicon-o-book-open class="w-4 h-4 text-gray-400 shrink-0" />
                    Panduan
                </a>
                <div class="border-t border-gray-100 my-1"></div>
                <p class="px-4 pt-1 pb-1 text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Akun</p>
                <a href="{{ route('mobile.profile') }}"
                   class="flex items-center gap-3 px-4 py-2.5 text-sm {{ $active === 'profile' ? 'text-amber-600 font-medium' : 'text-gray-700' }} hover:bg-amber-50 hover:text-amber-700 transition-colors">
                    <x-heroicon-o-user class="w-4 h-4 text-gray-400 shrink-0" />
                    Profil
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-red-500 hover:bg-red-50 transition-colors">
                        <x-heroicon-o-arrow-right-on-rectangle class="w-4 h-4 text-gray-400 shrink-0" />
                        Keluar
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
