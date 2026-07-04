<div class="min-h-screen bg-gray-50 text-gray-900 pb-28">

    {{-- Header --}}
    <div class="flex items-center gap-3 px-4 pt-4 pb-2">
        <a href="{{ route('mobile.profile') }}" class="w-8 h-8 rounded-full bg-white border border-gray-200 flex items-center justify-center">
            <x-heroicon-o-arrow-left class="w-4 h-4 text-gray-500" />
        </a>
        <span class="font-bold text-gray-800">Anggota Keluarga</span>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 gap-2.5 mx-4 mt-3">
        <div class="bg-white border border-gray-100 rounded-xl p-3 shadow-sm">
            <p class="text-[10px] text-gray-400">Total Anggota</p>
            <p class="text-sm font-bold text-gray-800">{{ $stats['total'] }}</p>
        </div>
        <div class="bg-white border border-gray-100 rounded-xl p-3 shadow-sm">
            <p class="text-[10px] text-gray-400">Anggota Aktif</p>
            <p class="text-sm font-bold text-emerald-600">{{ $stats['active'] }}</p>
        </div>
    </div>

    {{-- Member List --}}
    <div class="mx-4 mt-5">
        @if ($members->count() > 0)
            <div class="space-y-2">
                @foreach ($members as $member)
                    <div class="bg-white border border-gray-100 rounded-xl p-3 shadow-sm">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3 min-w-0 flex-1">
                                <div class="w-10 h-10 rounded-full bg-amber-100 flex items-center justify-center shrink-0">
                                    <span class="text-sm font-semibold text-amber-700">{{ strtoupper(substr($member->name, 0, 2)) }}</span>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm font-medium text-gray-800 truncate">{{ $member->name }}</p>
                                    <p class="text-xs text-gray-400 truncate">{{ $member->email }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2 shrink-0">
                                <button wire:click="toggleActive({{ $member->id }})" wire:confirm="{{ $member->is_active ? 'Nonaktifkan' : 'Aktifkan' }} anggota ini?"
                                    class="text-[10px] font-semibold px-2 py-1 rounded-full {{ $member->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-500' }}">
                                    {{ $member->is_active ? 'Aktif' : 'Nonaktif' }}
                                </button>
                                <button wire:click="openEditForm({{ $member->id }})" class="p-1.5 rounded hover:bg-gray-100">
                                    <x-heroicon-o-pencil-square class="w-4 h-4 text-gray-500" />
                                </button>
                                <button wire:click="deleteMember({{ $member->id }})" wire:confirm="Hapus anggota ini?" class="p-1.5 rounded hover:bg-red-50">
                                    <x-heroicon-o-trash class="w-4 h-4 text-red-400" />
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="bg-white border border-gray-100 rounded-xl p-6 text-center shadow-sm">
                <div class="w-12 h-12 rounded-full bg-amber-100 flex items-center justify-center mx-auto mb-2">
                    <x-heroicon-o-user-group class="w-6 h-6 text-amber-600" />
                </div>
                <p class="text-xs font-medium text-gray-500">Belum ada anggota keluarga</p>
                <p class="text-[10px] text-gray-400 mt-1">Tambahkan anggota untuk mengelola keuangan bersama</p>
            </div>
        @endif
    </div>

    {{-- Tombol Tambah --}}
    <div class="mx-4 mt-5">
        <button wire:click="openAddForm"
            class="w-full flex items-center justify-center gap-2 bg-amber-500 text-white text-sm font-semibold py-3 rounded-xl shadow-sm hover:bg-amber-600 transition">
            <x-heroicon-o-plus class="w-4 h-4" />
            Tambah Anggota
        </button>
    </div>

    {{-- Bottom Sheet Add/Edit --}}
    @if ($showForm)
        <div class="fixed inset-0 z-50 flex items-end md:items-center md:justify-center">
            <div class="absolute inset-0 bg-black/40" wire:click="closeForm"></div>
            <div class="relative bg-white w-full md:w-96 rounded-t-2xl md:rounded-2xl p-4 pb-6">
                <div class="flex items-center justify-between mb-4">
                    <p class="text-sm font-semibold text-gray-800">{{ $editMemberId ? 'Edit Anggota' : 'Tambah Anggota' }}</p>
                    <button wire:click="closeForm" class="p-1">
                        <x-heroicon-o-x-mark class="w-5 h-5 text-gray-400" />
                    </button>
                </div>

                <div class="space-y-3">
                    <div>
                        <label class="text-[11px] font-medium text-gray-400 mb-1 block">Nama Lengkap</label>
                        <input type="text" wire:model="formName" placeholder="Nama anak"
                               class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2 bg-white focus:border-amber-500 focus:ring-amber-500">
                        @error('formName') <p class="text-[11px] text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="text-[11px] font-medium text-gray-400 mb-1 block">Email</label>
                        <input type="email" wire:model="formEmail" placeholder="email@contoh.com"
                               class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2 bg-white focus:border-amber-500 focus:ring-amber-500">
                        @error('formEmail') <p class="text-[11px] text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="text-[11px] font-medium text-gray-400 mb-1 block">{{ $editMemberId ? 'Sandi Baru (kosongkan jika tidak diubah)' : 'Sandi' }}</label>
                        <input type="password" wire:model="formPassword" placeholder="Minimal 8 karakter"
                               class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2 bg-white focus:border-amber-500 focus:ring-amber-500">
                        @error('formPassword') <p class="text-[11px] text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="text-xs text-gray-400 bg-gray-50 rounded-lg px-3 py-2 leading-relaxed">
                        Anggota yang didaftarkan akan mendapatkan akses panel anak untuk mencatat keuangan mereka sendiri.
                    </div>

                    <div class="flex gap-2 pt-2">
                        <button wire:click="closeForm"
                                class="flex-1 text-sm font-medium text-red-500 bg-red-50 rounded-xl py-2.5 hover:bg-red-100 transition">
                            Batal
                        </button>
                        <button wire:click="save"
                                class="flex-1 text-sm font-semibold text-gray-900 bg-gray-100 rounded-xl py-2.5 hover:bg-gray-200 transition">
                            {{ $editMemberId ? 'Simpan' : 'Tambah' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <x-mobile.bottom-nav active="profile" />
</div>
