@php
    $formatRupiah = function ($number) {
        $number = (float) $number;
        if ($number >= 1000000000) {
            $val = $number / 1000000000;
            return 'Rp ' . ($val == floor($val) ? number_format($val, 0, ',', '.') : number_format($val, 1, ',', '.')) . ' M';
        } elseif ($number >= 1000000) {
            $val = $number / 1000000;
            return 'Rp ' . ($val == floor($val) ? number_format($val, 0, ',', '.') : number_format($val, 1, ',', '.')) . ' Jt';
        } elseif ($number >= 1000) {
            $val = $number / 1000;
            return 'Rp ' . ($val == floor($val) ? number_format($val, 0, ',', '.') : number_format($val, 1, ',', '.')) . ' Rb';
        }
        return 'Rp ' . number_format($number, 0, ',', '.');
    };

    $categoryIcons = [
        'Liburan'    => ['bg' => 'bg-blue-100',   'text' => 'text-blue-600',   'icon' => 'paper-airplane'],
        'Properti'   => ['bg' => 'bg-green-100',  'text' => 'text-green-600',  'icon' => 'home'],
        'Pribadi'    => ['bg' => 'bg-purple-100', 'text' => 'text-purple-600', 'icon' => 'user-circle'],
        'Pendidikan' => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-600', 'icon' => 'academic-cap'],
        'Kendaraan'  => ['bg' => 'bg-orange-100', 'text' => 'text-orange-600', 'icon' => 'truck'],
        'Kesehatan'  => ['bg' => 'bg-pink-100',   'text' => 'text-pink-600',   'icon' => 'heart'],
        'Elektronik' => ['bg' => 'bg-indigo-100', 'text' => 'text-indigo-600', 'icon' => 'device-phone-mobile'],
        'Lainnya'    => ['bg' => 'bg-gray-100',   'text' => 'text-gray-600',   'icon' => 'archive-box'],
    ];

    $periodLabels = ['7' => '7 Hari', '30' => '30 Hari', '90' => '3 Bulan', '180' => '6 Bulan', '365' => '1 Tahun', 'all' => 'Semua'];
@endphp

<div class="min-h-screen bg-gray-50 text-gray-900 pb-28">

    
    {{-- Header --}}
<div class="flex items-center justify-between px-4 pt-4 pb-2">
    <span class="font-bold text-gray-800">Tabungan</span>
    <a href="{{ route('mobile.tabungan.create') }}"
       class="flex items-center gap-1.5 text-xs font-semibold bg-amber-500 text-white px-3 py-1.5 rounded-full hover:bg-amber-600 transition shadow-sm">
        <x-heroicon-o-plus class="w-3.5 h-3.5" />
        Tambah
    </a>
</div>

    @if (session('success'))
        <div class="mx-4 mt-2 bg-amber-50 text-amber-700 text-xs font-medium px-3 py-2 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    {{-- Stats cards --}}
    <div class="grid grid-cols-2 gap-2.5 px-4 mt-3">
        <div class="bg-white border border-gray-100 rounded-xl p-3 shadow-sm">
            <p class="text-[10px] text-gray-400">Total Tabungan</p>
            <p class="text-sm font-bold text-gray-800 truncate">{{ $formatRupiah($totalSaved) }}</p>
            <p class="text-[10px] mt-0.5 {{ $isPositive ? 'text-emerald-600' : 'text-red-500' }}">
                {{ $isPositive ? '↑' : '↓' }} {{ $isPositive ? '+' : '' }}{{ $percentChange }}% bulan lalu
            </p>
        </div>
        <div class="bg-white border border-gray-100 rounded-xl p-3 shadow-sm">
            <p class="text-[10px] text-gray-400">Sisa Target</p>
            <p class="text-sm font-bold text-gray-800 truncate">{{ $formatRupiah($remaining) }}</p>
            <p class="text-[10px] text-gray-400 mt-0.5">{{ $activeSavings->count() }} target aktif</p>
        </div>
        <a href="{{ route('mobile.tabungan.completed') }}"
           class="bg-white border border-gray-100 rounded-xl p-3 shadow-sm col-span-2 block hover:bg-gray-50 transition">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-full bg-emerald-100 flex items-center justify-center shrink-0">
                    <x-heroicon-o-check-circle class="w-4.5 h-4.5 text-emerald-600" />
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-[10px] text-gray-400">Target Tercapai</p>
                    <p class="text-sm font-bold text-gray-800">{{ $completed }} Target Selesai</p>
                </div>
                <x-heroicon-o-chevron-right class="w-4 h-4 text-gray-300 shrink-0" />
            </div>
        </a>
    </div>

    {{-- Target Tabungan Aktif --}}
    <div class="mx-4 mt-5">
        <p class="text-[11px] text-gray-400 tracking-wide mb-2 font-medium">TARGET AKTIF</p>

        @if ($activeSavings->count() > 0)
            <div class="space-y-2.5">
                @foreach ($activeSavings as $saving)
                    @php
                        $progress = $saving->getProgressPercentage();
                        $barColor = $progress >= 100 ? 'bg-amber-400' : ($progress >= 60 ? 'bg-yellow-400' : 'bg-amber-500');
                        $catIcon  = $categoryIcons[$saving->category] ?? $categoryIcons['Lainnya'];
                    @endphp
                    <a href="{{ route('mobile.tabungan.show', $saving) }}"
                       class="block bg-white border border-gray-100 rounded-xl p-3 shadow-sm">
                        <div class="flex items-start justify-between mb-2">
                            <div class="flex items-center gap-2.5">
                                <div class="w-9 h-9 rounded-full {{ $catIcon['bg'] }} flex items-center justify-center shrink-0">
                                    <x-dynamic-component :component="'heroicon-o-' . $catIcon['icon']" class="w-4 h-4 {{ $catIcon['text'] }}" />
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-gray-800 truncate">{{ $saving->name }}</p>
                                    <p class="text-[10px] text-gray-400">{{ $saving->category }}</p>
                                </div>
                            </div>
                            <span class="text-[10px] font-semibold text-gray-500 shrink-0">{{ $progress }}%</span>
                        </div>

                        <div class="w-full bg-gray-100 rounded-full h-1.5 mb-2">
                            <div class="{{ $barColor }} h-1.5 rounded-full" style="width: {{ $progress }}%"></div>
                        </div>

                        <div class="flex items-center justify-between text-[11px]">
                            <span class="text-gray-400">{{ $formatRupiah($saving->current_amount) }} / {{ $formatRupiah($saving->target_amount) }}</span>
                            <span class="text-amber-600 font-semibold">Setor →</span>
                        </div>
                    </a>
                @endforeach
            </div>
        @else
            <div class="bg-white border border-gray-100 rounded-xl p-6 text-center shadow-sm">
                <div class="w-12 h-12 rounded-full bg-gray-100 flex items-center justify-center mx-auto mb-2">
                    <x-heroicon-o-archive-box class="w-6 h-6 text-gray-400" />
                </div>
                <p class="text-xs font-medium text-gray-500">Belum ada tabungan aktif</p>
                <a href="{{ route('mobile.tabungan.create') }}"
                   class="inline-flex items-center gap-1.5 text-xs font-semibold bg-amber-500 text-white px-3 py-1.5 rounded-full mt-3">
                    <x-heroicon-o-plus class="w-3.5 h-3.5" />
                    Buat Tabungan Pertama
                </a>
            </div>
        @endif
    </div>

    {{-- Riwayat Setoran --}}
    <div class="mx-4 mt-5">
        <div class="flex items-center justify-between mb-2" x-data="{ open: false }">
            <p class="text-[11px] text-gray-400 tracking-wide font-medium">RIWAYAT SETORAN</p>

            <div class="relative">
                <button @click="open = !open"
                    class="flex items-center gap-1 text-[11px] font-medium text-gray-500 bg-white border border-gray-200 px-2.5 py-1 rounded-full">
                    {{ $periodLabels[$period] ?? '30 Hari' }}
                    <x-heroicon-o-chevron-down class="w-3 h-3" />
                </button>

                <div x-show="open" @click.outside="open = false" x-transition
                     class="absolute right-0 top-full mt-1.5 bg-white border border-gray-100 rounded-lg shadow-lg min-w-[140px] z-20 overflow-hidden">
                    @foreach (['7' => '7 Hari Terakhir', '30' => '30 Hari Terakhir', '90' => '3 Bulan Terakhir', '180' => '6 Bulan Terakhir', '365' => '1 Tahun Terakhir', 'all' => 'Semua Data'] as $val => $label)
                        <button wire:click="setPeriod('{{ $val }}')" @click="open = false"
                            class="block w-full text-left px-3 py-2 text-[11px] {{ $period === $val ? 'bg-amber-50 text-amber-700 font-semibold' : 'text-gray-600' }}">
                            {{ $label }}
                        </button>
                    @endforeach
                </div>
            </div>
        </div>

        @if ($recentDeposits->count() > 0)
            <div class="space-y-2">
                @foreach ($recentDeposits as $deposit)
                    @php
                        $goalName = $deposit->savingGoal?->name ?? '-';
                        $goalCat  = $deposit->savingGoal?->category ?? 'Lainnya';
                        $catIcon  = $categoryIcons[$goalCat] ?? $categoryIcons['Lainnya'];
                    @endphp
                    <div class="flex items-center justify-between bg-white border border-gray-100 rounded-xl p-3 shadow-sm">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-full {{ $catIcon['bg'] }} flex items-center justify-center shrink-0">
                                <x-dynamic-component :component="'heroicon-o-' . $catIcon['icon']" class="w-4 h-4 {{ $catIcon['text'] }}" />
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-800">{{ $goalName }}</p>
                                <p class="text-xs text-gray-400">{{ $deposit->date->translatedFormat('d M Y') }}</p>
                            </div>
                        </div>
                        <span class="text-sm font-semibold text-emerald-600">+{{ $formatRupiah($deposit->amount) }}</span>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-xs text-gray-400 text-center py-10">Belum ada riwayat menabung</p>
        @endif
    </div>

    <x-mobile.bottom-nav active="tabungan" />
</div>
