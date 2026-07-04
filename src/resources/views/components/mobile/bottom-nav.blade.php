@props(['active' => 'dashboard'])

<div class="fixed bottom-0 left-0 right-0 max-w-md mx-auto bg-white border-t border-gray-100 px-2 py-2 shadow-[0_-2px_10px_rgba(0,0,0,0.05)] md:hidden">
    <div class="flex justify-around items-center">
        <a href="{{ route('mobile.dashboard') }}"
           class="flex flex-col items-center gap-1 text-[11px] px-3 py-1.5 rounded-xl {{ $active === 'dashboard' ? 'bg-amber-500 text-white' : 'text-gray-400' }}">
            <x-heroicon-o-squares-2x2 class="w-5 h-5" />
            Dashboard
        </a>
        <a href="{{ route('mobile.history') }}"
           class="flex flex-col items-center gap-1 text-[11px] px-3 py-1.5 rounded-xl {{ $active === 'history' ? 'bg-amber-500 text-white' : 'text-gray-400' }}">
            <x-heroicon-o-clock class="w-5 h-5" />
            Riwayat
        </a>
        <a href="{{ route('mobile.anggaran') }}"
           class="flex flex-col items-center gap-1 text-[11px] px-3 py-1.5 rounded-xl {{ $active === 'anggaran' ? 'bg-amber-500 text-white' : 'text-gray-400' }}">
            <x-heroicon-o-wallet class="w-5 h-5" />
            Anggaran
        </a>
        <a href="{{ route('mobile.profile') }}"
           class="flex flex-col items-center gap-1 text-[11px] px-3 py-1.5 rounded-xl {{ $active === 'profile' ? 'bg-amber-500 text-white' : 'text-gray-400' }}">
            <x-heroicon-o-user class="w-5 h-5" />
            Profile
        </a>
    </div>
</div>
