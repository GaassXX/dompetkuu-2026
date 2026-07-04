<div class="min-h-screen bg-gray-50 text-gray-900 pb-24" x-data="{ showInfo: false }">

    {{-- Card Profil --}}
    <a href="{{ route('mobile.account') }}"
       class="mx-4 mt-2 rounded-2xl bg-gradient-to-br from-amber-500 to-orange-600 p-5 flex flex-col items-center shadow-sm block hover:opacity-90 transition">
        <div class="w-16 h-16 rounded-full bg-white/20 flex items-center justify-center text-2xl font-bold text-white overflow-hidden">
            @if ($user->avatar_url)
                <img src="{{ asset('storage/' . $user->avatar_url) }}" class="w-full h-full object-cover">
            @else
                {{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}
            @endif
        </div>
        <p class="font-semibold mt-2 text-white">{{ $user->name ?? '-' }}</p>
        <p class="text-xs text-amber-100">{{ $user->email ?? '-' }}</p>
        <span class="mt-2 text-[10px] font-medium text-white/70 flex items-center gap-1">
            <x-heroicon-o-pencil-square class="w-3 h-3" /> Kelola Akun
        </span>
    </a>

    {{-- Total Saldo / Tabungan --}}
    <div class="flex gap-3 mx-4 mt-3">
        <div class="flex-1 bg-white border border-gray-100 rounded-xl p-3 shadow-sm">
            <p class="text-xs text-gray-400">Total Saldo</p>
            <p class="font-semibold text-gray-800">Rp {{ number_format($totalSaldo, 0, ',', '.') }}</p>
        </div>
        <a href="{{ route('mobile.tabungan') }}" class="flex-1 bg-white border border-gray-100 rounded-xl p-3 shadow-sm">
            <p class="text-xs text-gray-400">Tabungan</p>
            <p class="font-semibold text-gray-800">Rp {{ number_format($totalTabungan, 0, ',', '.') }}</p>
            <p class="text-[10px] text-gray-400 mt-0.5">Dari Rp {{ number_format($totalTargetTabungan, 0, ',', '.') }}</p>
        </a>
    </div>

    {{-- Koneksi Telegram --}}
    <div class="mx-4 mt-5">
        <p class="text-xs text-gray-400 mb-2">KONEKSI TELEGRAM</p>
        <div class="bg-white border border-gray-100 rounded-xl divide-y divide-gray-100 shadow-sm">
            {{-- Status --}}
            <div class="flex items-center justify-between px-4 py-3">
                <span class="text-sm text-gray-800">Status</span>
                @if ($user->telegram_chat_id)
                    <span class="inline-flex items-center gap-1.5 text-sm font-semibold text-emerald-600">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span> Terhubung
                    </span>
                @elseif ($user->telegram_disconnected_at)
                    <span class="inline-flex items-center gap-1.5 text-sm font-semibold text-amber-600">
                        <span class="w-2 h-2 rounded-full bg-amber-500"></span> Tidak Aktif
                    </span>
                @else
                    <span class="inline-flex items-center gap-1.5 text-sm font-semibold text-red-500">
                        <span class="w-2 h-2 rounded-full bg-red-500"></span> Belum Terhubung
                    </span>
                @endif
            </div>

            {{-- Kode Pairing --}}
            <div class="flex items-center justify-between px-4 py-3">
                <span class="text-sm text-gray-800">Kode Pairing</span>
                <code class="text-sm font-bold tracking-wider px-3 py-1 rounded-lg bg-gray-100 text-gray-800">
                    {{ $user->telegram_pair_code }}
                </code>
            </div>

            {{-- Cara Menghubungkan --}}
            <div class="px-4 py-3">
                <p class="text-xs text-gray-400 mb-2">Cara Menghubungkan:</p>
                <ol class="text-xs text-gray-500 space-y-1.5 list-decimal list-inside">
                    <li>Buka Telegram, cari bot <strong class="text-gray-700">@dompettkuu_bot</strong></li>
                    <li>Tekan <strong class="text-gray-700">/Start</strong> lalu kirim: <code class="px-1 py-0.5 rounded bg-gray-100 text-gray-700">/connect {{ $user->telegram_pair_code }}</code></li>
                    <li>Setelah berhasil, status akan berubah jadi <strong class="text-emerald-600">Terhubung</strong></li>
                    <li>Kirim transaksi contoh: <code class="px-1 py-0.5 rounded bg-gray-100 text-gray-700">masuk 500000 gaji bulan mei</code></li>
                </ol>
            </div>

            {{-- Tombol Putuskan --}}
            @if ($user->telegram_chat_id)
                <div class="px-4 py-3">
                    <button
                        wire:click="disconnectTelegram"
                        wire:confirm="Putuskan koneksi Telegram? Bot tidak akan bisa mencatat transaksi otomatis."
                        type="button"
                        class="w-full flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl border border-red-200 text-red-500 text-sm font-medium hover:bg-red-50 transition-colors"
                    >
                        <x-heroicon-o-link-slash class="w-4 h-4" />
                        Putuskan Koneksi Telegram
                    </button>
                </div>
            @endif
        </div>
    </div>

    {{-- Pengaturan Akun --}}
    <div class="mx-4 mt-5">
        <p class="text-xs text-gray-400 mb-2">PENGATURAN AKUN</p>
        <div class="bg-white border border-gray-100 rounded-xl divide-y divide-gray-100 shadow-sm">
            <a href="{{ route('mobile.anggaran') }}" class="flex items-center justify-between px-4 py-3">
                <span class="flex items-center gap-3 text-sm text-gray-800">
                    <x-heroicon-o-chart-pie class="w-4 h-4 text-amber-600" /> Anggaran
                </span>
                <x-heroicon-o-chevron-right class="w-4 h-4 text-gray-300" />
            </a>
            @if (auth()->user()->hasRole('parent'))
                <a href="{{ route('mobile.keluarga') }}" class="flex items-center justify-between px-4 py-3">
                    <span class="flex items-center gap-3 text-sm text-gray-800">
                        <x-heroicon-o-user-group class="w-4 h-4 text-amber-600" /> Anggota Keluarga
                    </span>
                    <x-heroicon-o-chevron-right class="w-4 h-4 text-gray-300" />
                </a>
            @else
                <button type="button" @click="showInfo = true" class="w-full flex items-center justify-between px-4 py-3">
                    <span class="flex items-center gap-3 text-sm text-gray-800">
                        <x-heroicon-o-user-group class="w-4 h-4 text-amber-600" /> Anggota Keluarga
                    </span>
                    <x-heroicon-o-chevron-right class="w-4 h-4 text-gray-300" />
                </button>
            @endif
            <a href="#" class="flex items-center justify-between px-4 py-3">
                <span class="flex items-center gap-3 text-sm text-gray-800">
                    <x-heroicon-o-shield-check class="w-4 h-4 text-amber-600" /> Keamanan
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

    {{-- Modal Info Anggota Keluarga --}}
    <div x-show="showInfo" x-cloak class="fixed inset-0 z-50 flex items-center justify-center px-4">
        <div class="absolute inset-0 bg-black/40" @click="showInfo = false"></div>
        <div class="relative bg-white rounded-2xl p-5 max-w-sm w-full shadow-lg">
            <div class="flex items-start gap-3">
                <div class="w-9 h-9 rounded-full bg-amber-100 flex items-center justify-center shrink-0">
                    <x-heroicon-o-information-circle class="w-5 h-5 text-amber-600" />
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-gray-800">Anggota Keluarga</p>
                    <p class="text-xs text-gray-500 mt-1 leading-relaxed">
                        Fitur ini hanya tersedia untuk akun dengan tipe <strong class="text-gray-700">Orang Tua</strong>.
                    </p>
                    <p class="text-xs text-gray-500 mt-1 leading-relaxed">
                        Akun Anda saat ini adalah <strong class="text-gray-700">Personal</strong> — untuk pencatatan mandiri tanpa anggota keluarga.
                    </p>
                </div>
            </div>
            <button @click="showInfo = false"
                class="mt-4 w-full text-sm font-semibold text-white bg-amber-500 rounded-xl py-2.5 hover:bg-amber-600 transition">
                Tutup
            </button>
        </div>
    </div>

    <p class="text-center text-[10px] text-gray-400 mt-6">Dompetkuu v1.0.0</p>

    <x-mobile.bottom-nav active="profile" />
</div>
