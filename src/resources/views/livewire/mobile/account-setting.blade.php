<div class="min-h-screen bg-gray-50 text-gray-900 pb-24">

    {{-- Header --}}
    <div class="flex items-center gap-3 px-4 pt-4 pb-2">
        <a href="{{ route('mobile.profile') }}" class="w-8 h-8 rounded-full bg-white border border-gray-200 flex items-center justify-center">
            <x-heroicon-o-arrow-left class="w-4 h-4 text-gray-500" />
        </a>
        <span class="font-bold text-gray-800">Pengaturan Akun</span>
    </div>

    @if (session('success'))
        <div class="mx-4 mt-2 bg-emerald-50 text-emerald-700 text-xs font-medium px-3 py-2 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    <form wire:submit="save" class="mx-4 mt-3 space-y-4">

        {{-- Avatar --}}
        <div class="bg-white border border-gray-100 rounded-xl p-4 shadow-sm">
            <label class="text-xs font-medium text-gray-500 mb-3 block">Foto Profil</label>
            <div class="flex flex-col items-center gap-3">
                <div class="w-20 h-20 rounded-full bg-gray-100 flex items-center justify-center overflow-hidden">
                    @if ($avatar)
                        <img src="{{ $avatar->temporaryUrl() }}" class="w-full h-full object-cover">
                    @elseif (auth()->user()->avatar_url)
                        <img src="{{ asset('storage/' . auth()->user()->avatar_url) }}" class="w-full h-full object-cover">
                    @else
                        <span class="text-2xl font-bold text-gray-400">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                    @endif
                </div>
                <label class="text-xs font-medium text-amber-600 cursor-pointer">
                    <input type="file" wire:model="avatar" accept="image/*" class="hidden">
                    Ganti Foto
                </label>
                @error('avatar') <p class="text-[11px] text-red-500">{{ $message }}</p> @enderror
            </div>
        </div>

        {{-- Data Diri --}}
        <div class="bg-white border border-gray-100 rounded-xl p-4 shadow-sm space-y-4">
            <div>
                <label class="text-xs font-medium text-gray-500">Nama</label>
                <input type="text" wire:model="name"
                       class="mt-1 w-full text-sm rounded-lg border-gray-200 focus:border-amber-500 focus:ring-amber-500">
                @error('name') <p class="text-[11px] text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="text-xs font-medium text-gray-500">Email</label>
                <input type="email" wire:model="email" readonly disabled
                       class="mt-1 w-full text-sm rounded-lg border-gray-200 bg-gray-50 text-gray-400 cursor-not-allowed">
            </div>
        </div>

        {{-- Ganti Sandi --}}
        <div class="bg-white border border-gray-100 rounded-xl p-4 shadow-sm space-y-4">
            <p class="text-xs font-semibold text-gray-500">GANTI SANDI</p>
            <div>
                <label class="text-xs font-medium text-gray-500">Sandi Saat Ini</label>
                <input type="password" wire:model="current_password"
                       class="mt-1 w-full text-sm rounded-lg border-gray-200 focus:border-amber-500 focus:ring-amber-500">
                @error('current_password') <p class="text-[11px] text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="text-xs font-medium text-gray-500">Sandi Baru</label>
                <input type="password" wire:model="new_password"
                       class="mt-1 w-full text-sm rounded-lg border-gray-200 focus:border-amber-500 focus:ring-amber-500">
                @error('new_password') <p class="text-[11px] text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="text-xs font-medium text-gray-500">Konfirmasi Sandi Baru</label>
                <input type="password" wire:model="new_password_confirmation"
                       class="mt-1 w-full text-sm rounded-lg border-gray-200 focus:border-amber-500 focus:ring-amber-500">
            </div>
        </div>

        <button type="submit"
            class="w-full bg-amber-500 text-white text-sm font-semibold py-3 rounded-xl shadow-sm hover:bg-amber-600 transition">
            Simpan Perubahan
        </button>
    </form>

    <x-mobile.bottom-nav active="profile" />
</div>
