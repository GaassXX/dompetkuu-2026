<div class="min-h-screen bg-gray-50 pb-28">

    {{-- Header --}}
    <div class="bg-gradient-to-r from-amber-500 to-orange-500 px-5 pt-12 pb-6 -mt-2">
        <div class="flex items-center gap-3">
            <a href="{{ route('mobile.profile') }}" class="text-white/80 hover:text-white transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <h1 class="text-xl font-extrabold text-white">Keamanan</h1>
        </div>
    </div>

    <div class="px-4 -mt-3 space-y-4">

        {{-- Ubah Password --}}
        <div class="bg-white border border-gray-100 rounded-2xl overflow-hidden shadow-sm">
            <div class="px-4 pt-4 pb-2">
                <p class="font-semibold text-gray-800 flex items-center gap-2">
                    <x-heroicon-o-lock-closed class="w-4 h-4 text-amber-600" />
                    Ubah Password
                </p>
            </div>

            @if (session('success'))
                <div class="mx-4 mb-3 bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs rounded-xl px-3.5 py-3 flex items-center gap-2">
                    <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                    </svg>
                    {{ session('success') }}
                </div>
            @endif

            <form wire:submit="updatePassword" class="px-4 pb-4 space-y-3">
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Password Saat Ini</label>
                    <input type="password" wire:model="current_password"
                        class="w-full px-3.5 py-2.5 bg-white border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-amber-400 focus:border-amber-400 transition @error('current_password') border-red-400 @enderror"
                        placeholder="Masukkan password lama">
                    @error('current_password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Password Baru</label>
                    <input type="password" wire:model="new_password"
                        class="w-full px-3.5 py-2.5 bg-white border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-amber-400 focus:border-amber-400 transition @error('new_password') border-red-400 @enderror"
                        placeholder="Minimal 8 karakter">
                    @error('new_password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Konfirmasi Password Baru</label>
                    <input type="password" wire:model="new_password_confirmation"
                        class="w-full px-3.5 py-2.5 bg-white border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-amber-400 focus:border-amber-400 transition"
                        placeholder="Ulangi password baru">
                </div>
                <button type="submit"
                    class="w-full text-white font-semibold py-2.5 rounded-xl transition text-sm shadow-lg shadow-amber-600/20 hover:shadow-amber-600/30"
                    style="background: linear-gradient(135deg, #F59E0B, #EA580C);">
                    Simpan Password
                </button>
            </form>
        </div>

        {{-- Info Akun --}}
        <div class="bg-white border border-gray-100 rounded-2xl shadow-sm">
            <div class="px-4 pt-4 pb-1">
                <p class="font-semibold text-gray-800 flex items-center gap-2">
                    <x-heroicon-o-information-circle class="w-4 h-4 text-amber-600" />
                    Info Akun
                </p>
            </div>
            <div class="px-4 pb-4 space-y-2.5 text-sm">
                <div class="flex items-center justify-between">
                    <span class="text-gray-500">Email</span>
                    <span class="font-medium text-gray-800">{{ $user->email }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-gray-500">Role</span>
                    <span class="font-medium text-gray-800 capitalize">
                        @if($user->hasRole('parent')) Orang Tua
                        @elseif($user->is_independent) Personal
                        @elseif($user->hasRole('child')) Anak
                        @else {{ $user->role ?? '-' }}
                        @endif
                    </span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-gray-500">Bergabung</span>
                    <span class="font-medium text-gray-800">{{ $user->created_at->format('d M Y') }}</span>
                </div>
            </div>
        </div>

        {{-- Tips Keamanan --}}
        <div class="bg-white border border-gray-100 rounded-2xl shadow-sm">
            <div class="px-4 pt-4 pb-1">
                <p class="font-semibold text-gray-800 flex items-center gap-2">
                    <x-heroicon-o-shield-check class="w-4 h-4 text-amber-600" />
                    Tips Keamanan
                </p>
            </div>
            <div class="px-4 pb-4 space-y-2 text-sm">
                <div class="flex items-start gap-2.5">
                    <span class="text-amber-500 mt-0.5">🔐</span>
                    <p class="text-gray-600">Gunakan password yang kuat — minimal 8 karakter dengan kombinasi huruf, angka, dan simbol.</p>
                </div>
                <div class="flex items-start gap-2.5">
                    <span class="text-amber-500 mt-0.5">🔐</span>
                    <p class="text-gray-600">Jangan bagikan password ke siapapun, termasuk yang mengaku dari tim DompetKuu.</p>
                </div>
                <div class="flex items-start gap-2.5">
                    <span class="text-amber-500 mt-0.5">🔐</span>
                    <p class="text-gray-600">Rutin ganti password setiap 3 bulan untuk menjaga keamanan akun.</p>
                </div>
                <div class="flex items-start gap-2.5">
                    <span class="text-amber-500 mt-0.5">🔐</span>
                    <p class="text-gray-600">Pastikan email yang terdaftar selalu aktif untuk menerima notifikasi penting.</p>
                </div>
            </div>
        </div>

    </div>

    <x-mobile.bottom-nav active="profile" />
</div>
