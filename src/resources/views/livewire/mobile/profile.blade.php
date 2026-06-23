<div class="min-h-screen bg-gray-50 text-gray-900 pb-24">

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

    {{-- Card Profil --}}
    <div class="mx-4 mt-2 rounded-2xl bg-gradient-to-br from-emerald-500 to-emerald-700 p-5 flex flex-col items-center shadow-sm">
        <div class="w-16 h-16 rounded-full bg-white/20 flex items-center justify-center text-2xl font-bold text-white">
            {{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}
        </div>
        <p class="font-semibold mt-2 text-white">{{ $user->name ?? '-' }}</p>
        <p class="text-xs text-emerald-100">{{ $user->email ?? '-' }}</p>
    </div>

    {{-- Total Saldo / Tabungan --}}
    <div class="flex gap-3 mx-4 mt-3">
        <div class="flex-1 bg-white border border-gray-100 rounded-xl p-3 shadow-sm">
            <p class="text-xs text-gray-400">Total Saldo</p>
            <p class="font-semibold text-gray-800">Rp {{ number_format($totalSaldo, 0, ',', '.') }}</p>
        </div>
        <div class="flex-1 bg-white border border-gray-100 rounded-xl p-3 shadow-sm">
            <p class="text-xs text-gray-400">Tabungan</p>
            <p class="font-semibold text-gray-800">Rp -</p>
        </div>
    </div>

    {{-- Pengaturan Akun --}}
    <div class="mx-4 mt-5">
        <p class="text-xs text-gray-400 mb-2">PENGATURAN AKUN</p>
        <div class="bg-white border border-gray-100 rounded-xl divide-y divide-gray-100 shadow-sm">
            <a href="#" class="flex items-center justify-between px-4 py-3">
                <span class="flex items-center gap-3 text-sm text-gray-800">
                    <x-heroicon-o-chart-pie class="w-4 h-4 text-emerald-600" /> Anggaran
                </span>
                <x-heroicon-o-chevron-right class="w-4 h-4 text-gray-300" />
            </a>
            <a href="#" class="flex items-center justify-between px-4 py-3">
                <span class="flex items-center gap-3 text-sm text-gray-800">
                    <x-heroicon-o-user-group class="w-4 h-4 text-emerald-600" /> Anggota Keluarga
                </span>
                <x-heroicon-o-chevron-right class="w-4 h-4 text-gray-300" />
            </a>
            <a href="#" class="flex items-center justify-between px-4 py-3">
                <span class="flex items-center gap-3 text-sm text-gray-800">
                    <x-heroicon-o-shield-check class="w-4 h-4 text-emerald-600" /> Keamanan
                </span>
                <x-heroicon-o-chevron-right class="w-4 h-4 text-gray-300" />
            </a>
        </div>
    </div>

    {{-- Lainnya --}}
    <div class="mx-4 mt-5">
        <p class="text-xs text-gray-400 mb-2">LAINNYA</p>
        <div class="bg-white border border-gray-100 rounded-xl divide-y divide-gray-100 shadow-sm">
            <a href="#" class="flex items-center justify-between px-4 py-3">
                <span class="flex items-center gap-3 text-sm text-gray-800">
                    <x-heroicon-o-question-mark-circle class="w-4 h-4 text-gray-500" /> Pusat Bantuan
                </span>
                <x-heroicon-o-chevron-right class="w-4 h-4 text-gray-300" />
            </a>
            <button wire:click="logout" type="button" class="w-full flex items-center justify-between px-4 py-3 text-red-500">
                <span class="flex items-center gap-3 text-sm">
                    <x-heroicon-o-arrow-right-on-rectangle class="w-4 h-4" /> Keluar
                </span>
            </button>
        </div>
    </div>

    <p class="text-center text-[10px] text-gray-400 mt-6">Dompetkuu v2.4.0</p>

    <x-mobile.bottom-nav active="profile" />
</div>
