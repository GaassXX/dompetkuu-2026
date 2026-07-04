@php
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
@endphp

<div class="min-h-screen bg-gray-50 text-gray-900 pb-28">

    {{-- Header --}}
    <div class="flex items-center gap-3 px-4 pt-4 pb-2">
        <a href="{{ route('mobile.tabungan') }}" class="w-8 h-8 rounded-full bg-white border border-gray-200 flex items-center justify-center">
            <x-heroicon-o-arrow-left class="w-4 h-4 text-gray-500" />
        </a>
        <span class="font-bold text-gray-800">Target Tercapai</span>
        <span class="ml-auto text-xs font-semibold text-emerald-600 bg-emerald-50 px-2.5 py-1 rounded-full">
            {{ $totalCompleted }} Selesai
        </span>
    </div>

    {{-- Summary --}}
    <div class="mx-4 mt-3 bg-white border border-gray-100 rounded-xl p-4 shadow-sm">
        <div class="grid grid-cols-2 gap-3">
            <div>
                <p class="text-[10px] text-gray-400">Total Terkumpul</p>
                <p class="text-sm font-bold text-gray-800">Rp {{ number_format($totalSavedSum, 0, ',', '.') }}</p>
            </div>
            <div>
                <p class="text-[10px] text-gray-400">Total Target</p>
                <p class="text-sm font-bold text-gray-800">Rp {{ number_format($totalTargetSum, 0, ',', '.') }}</p>
            </div>
        </div>
    </div>

    {{-- Completed list --}}
    <div class="mx-4 mt-5">
        <p class="text-[11px] text-gray-400 tracking-wide mb-2 font-medium">TARGET SELESAI</p>

        @if ($completedSavings->count() > 0)
            <div class="space-y-2.5">
                @foreach ($completedSavings as $saving)
                    @php
                        $catIcon = $categoryIcons[$saving->category] ?? $categoryIcons['Lainnya'];
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
                            <span class="text-[10px] font-semibold bg-emerald-100 text-emerald-700 px-2 py-0.5 rounded-full shrink-0">Selesai</span>
                        </div>

                        <div class="w-full bg-gray-100 rounded-full h-1.5 mb-2">
                            <div class="bg-emerald-400 h-1.5 rounded-full" style="width: 100%"></div>
                        </div>

                        <div class="flex items-center justify-between text-[11px]">
                            <span class="text-gray-400">
                                Rp {{ number_format((float) $saving->current_amount, 0, ',', '.') }}
                                / Rp {{ number_format((float) $saving->target_amount, 0, ',', '.') }}
                            </span>
                            @if ($saving->target_date)
                                <span class="text-gray-400">{{ $saving->target_date->translatedFormat('d M Y') }}</span>
                            @endif
                        </div>
                    </a>
                @endforeach
            </div>
        @else
            <div class="bg-white border border-gray-100 rounded-xl p-6 text-center shadow-sm">
                <div class="w-12 h-12 rounded-full bg-emerald-100 flex items-center justify-center mx-auto mb-2">
                    <x-heroicon-o-check-circle class="w-6 h-6 text-emerald-500" />
                </div>
                <p class="text-xs font-medium text-gray-500">Belum ada target tercapai</p>
                <p class="text-[10px] text-gray-400 mt-1">Ayo terus menabung!</p>
            </div>
        @endif
    </div>

    <x-mobile.bottom-nav active="tabungan" />
</div>
