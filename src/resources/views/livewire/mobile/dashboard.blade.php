<div class="min-h-full bg-gray-50 text-gray-900 pb-28 md:pb-8">

    {{-- Header --}}
    <div class="flex items-center justify-between px-4 pt-4 pb-3 md:px-6 md:pt-6 md:pb-4 max-w-6xl mx-auto">
        <div class="flex items-center gap-2">
            <h1 class="font-bold text-gray-800 text-lg md:text-xl">Dashboard</h1>
            <span class="text-xs font-medium text-gray-500 bg-gray-100 px-2.5 py-1 rounded-full hidden md:inline-flex">
                {{ now()->translatedFormat('F Y') }}
            </span>
        </div>
        <div class="flex items-center gap-2">
            <span class="text-xs font-medium text-gray-500 bg-gray-100 px-2.5 py-1 rounded-full md:hidden">
                {{ now()->translatedFormat('F Y') }}
            </span>
            <a href="{{ route('mobile.add-transaction') }}"
               class="flex items-center gap-1.5 text-xs font-semibold bg-emerald-600 text-white px-3 py-1.5 rounded-full hover:bg-emerald-700 transition shadow-sm">
                <x-heroicon-o-plus class="w-3.5 h-3.5" />
                <span class="hidden md:inline">Tambah Transaksi</span>
            </a>
        </div>
    </div>

    {{-- 4 Summary Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-2.5 md:gap-3 px-4 md:px-6 max-w-6xl mx-auto">
        <div class="bg-white border border-gray-100 rounded-xl p-3 shadow-sm">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-full bg-emerald-100 flex items-center justify-center shrink-0">
                    <x-heroicon-o-arrow-trending-down class="w-4 h-4 text-emerald-600" />
                </div>
                <div class="min-w-0">
                    <p class="text-[10px] text-gray-400">Pemasukan</p>
                    <p class="text-sm font-bold text-emerald-600 truncate">Rp{{ number_format($pemasukanBulanIni, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white border border-gray-100 rounded-xl p-3 shadow-sm">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-full bg-red-100 flex items-center justify-center shrink-0">
                    <x-heroicon-o-arrow-trending-up class="w-4 h-4 text-red-500" />
                </div>
                <div class="min-w-0">
                    <p class="text-[10px] text-gray-400">Pengeluaran</p>
                    <p class="text-sm font-bold text-red-500 truncate">Rp{{ number_format($pengeluaranBulanIni, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white border border-gray-100 rounded-xl p-3 shadow-sm">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-full bg-blue-100 flex items-center justify-center shrink-0">
                    <x-heroicon-o-banknotes class="w-4 h-4 text-blue-600" />
                </div>
                <div class="min-w-0">
                    <p class="text-[10px] text-gray-400">Saldo</p>
                    <p class="text-sm font-bold text-blue-600 truncate">Rp{{ number_format($totalSaldo, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white border border-gray-100 rounded-xl p-3 shadow-sm">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-full bg-purple-100 flex items-center justify-center shrink-0">
                    <x-heroicon-o-squares-2x2 class="w-4 h-4 text-purple-600" />
                </div>
                <div class="min-w-0">
                    <p class="text-[10px] text-gray-400">Tabungan</p>
                    <p class="text-sm font-bold text-purple-600 truncate">Rp{{ number_format($tabunganBulanIni, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Middle Row: Arus Kas + Kategori --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 md:gap-4 px-4 md:px-6 mt-4 max-w-6xl mx-auto">

        {{-- Arus Kas Bulanan (Line Chart SVG) --}}
        <div class="bg-white border border-gray-100 rounded-xl p-3 shadow-sm">
            <div class="flex justify-between items-center mb-2">
                <p class="font-semibold text-sm text-gray-800">Arus Kas Bulanan</p>
                <span class="text-[10px] text-gray-400">7 hari terakhir</span>
            </div>
            @php
                $netValues = array_column($chart7Hari, 'net');
                $minVal = !empty($netValues) ? min($netValues) : 0;
                $maxVal = !empty($netValues) ? max($netValues) : 1;
                $range = $maxVal - $minVal ?: 1;
                $count = count($chart7Hari);
                $sw = 260; $sh = 110;
                $pl = 10; $pr = 10; $pt = 15; $pb = 22;
                $cw = $sw - $pl - $pr;
                $ch = $sh - $pt - $pb;
            @endphp
            @if ($count > 0)
                <div class="w-full" style="height:120px">
                    <svg viewBox="0 0 260 120" class="w-full h-full" preserveAspectRatio="xMidYMid meet">
                        <line x1="{{ $pl }}" y1="{{ $pt }}" x2="{{ $sw - $pr }}" y2="{{ $pt }}" stroke="#f3f4f6" stroke-width="1"/>
                        <line x1="{{ $pl }}" y1="{{ $pt + $ch * 0.33 }}" x2="{{ $sw - $pr }}" y2="{{ $pt + $ch * 0.33 }}" stroke="#f3f4f6" stroke-width="1"/>
                        <line x1="{{ $pl }}" y1="{{ $pt + $ch * 0.67 }}" x2="{{ $sw - $pr }}" y2="{{ $pt + $ch * 0.67 }}" stroke="#f3f4f6" stroke-width="1"/>
                        <line x1="{{ $pl }}" y1="{{ $pt + $ch }}" x2="{{ $sw - $pr }}" y2="{{ $pt + $ch }}" stroke="#f3f4f6" stroke-width="1"/>
                        @php
                            $points = [];
                            foreach ($chart7Hari as $i => $day) {
                                $x = $pl + ($i / max($count - 1, 1)) * $cw;
                                $y = $pt + $ch - (($day['net'] - $minVal) / $range) * $ch;
                                $points[] = ['x' => $x, 'y' => $y];
                            }
                            $firstX = $points[0]['x'];
                            $lastX = $points[$count - 1]['x'];
                            $bottomY = $pt + $ch;
                        @endphp
                        <path d="M{{ $firstX }},{{ $bottomY }}
                                 L@foreach ($points as $p){{ $p['x'] }},{{ $p['y'] }} L@endforeach
                                 {{ $lastX }},{{ $bottomY }} Z"
                              fill="url(#areaGradient)" opacity="0.25"/>
                        <polyline points="@foreach ($points as $p){{ $p['x'] }},{{ $p['y'] }} @endforeach"
                                   fill="none" stroke="#059669" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        @foreach ($points as $p)
                            <circle cx="{{ $p['x'] }}" cy="{{ $p['y'] }}" r="3" fill="#059669" stroke="white" stroke-width="2"/>
                        @endforeach
                        @foreach ($chart7Hari as $i => $day)
                            @php $x = $pl + ($i / max($count - 1, 1)) * $cw; @endphp
                            <text x="{{ $x }}" y="{{ $pt + $ch + 16 }}" text-anchor="middle" fill="#9ca3af" font-size="8">{{ $day['label'] }}</text>
                        @endforeach
                        <defs>
                            <linearGradient id="areaGradient" x1="0" y1="0" x2="0" y2="1">
                                <stop offset="0%" stop-color="#059669" stop-opacity="0.35"/>
                                <stop offset="100%" stop-color="#059669" stop-opacity="0.01"/>
                            </linearGradient>
                        </defs>
                    </svg>
                </div>
            @else
                <p class="text-xs text-gray-400 text-center py-8">Belum ada data transaksi</p>
            @endif
        </div>

        {{-- Pengeluaran per Kategori (Donut CSS) --}}
        <div class="bg-white border border-gray-100 rounded-xl p-3 shadow-sm">
            <p class="font-semibold text-sm text-gray-800 mb-2">Pengeluaran per Kategori</p>
            @if (count($pengeluaranPerKategori) > 0)
                @php
                    $totalKat = array_sum(array_column($pengeluaranPerKategori, 'amount'));
                    $angle = 0;
                    $stops = [];
                    foreach ($pengeluaranPerKategori as $cat) {
                        $pct = $totalKat > 0 ? ($cat['amount'] / $totalKat) * 100 : 0;
                        $stops[] = "{$cat['color']} {$angle}% " . ($angle + $pct) . "%";
                        $angle += $pct;
                    }
                @endphp
                <div class="flex items-center gap-3">
                    <div class="relative shrink-0">
                        <div class="w-24 h-24 rounded-full" style="background: conic-gradient({{ implode(', ', $stops) }})"></div>
                        <div class="absolute inset-2.5 bg-white rounded-full flex items-center justify-center">
                            <div class="text-center">
                                <p class="text-[9px] text-gray-400">Total</p>
                                <p class="text-xs font-bold text-gray-700">Rp{{ number_format($totalKat / 1000, 0) }}k</p>
                            </div>
                        </div>
                    </div>
                    <div class="flex-1 min-w-0 space-y-1.5">
                        @foreach ($pengeluaranPerKategori as $cat)
                            @php $pct = $totalKat > 0 ? round(($cat['amount'] / $totalKat) * 100) : 0; @endphp
                            <div class="flex items-center gap-1.5 text-[10px]">
                                <span class="w-2 h-2 rounded-full shrink-0" style="background:{{ $cat['color'] }}"></span>
                                <span class="text-gray-500 truncate max-w-[80px]">{{ $cat['label'] }}</span>
                                <span class="ml-auto font-semibold text-gray-700">{{ $pct }}%</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                <p class="text-xs text-gray-400 text-center py-8">Belum ada pengeluaran bulan ini</p>
            @endif
        </div>
    </div>

    {{-- Bottom Row: Transaksi Terbaru + Target Tabungan --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 md:gap-4 px-4 md:px-6 mt-3 md:mt-4 max-w-6xl mx-auto">

        {{-- Transaksi Terbaru --}}
        <div class="bg-white border border-gray-100 rounded-xl p-3 shadow-sm">
            <div class="flex justify-between items-center mb-2">
                <p class="font-semibold text-sm text-gray-800">Transaksi Terbaru</p>
                <a href="{{ route('mobile.history') }}" class="text-[10px] text-emerald-600 font-medium hover:text-emerald-700">Lihat Semua</a>
            </div>
            <div class="space-y-2">
                @forelse ($transaksiTerakhir as $trx)
                    <x-mobile.transaction-item :transaction="$trx" />
                @empty
                    <p class="text-xs text-gray-400 text-center py-4">Belum ada transaksi</p>
                @endforelse
            </div>
        </div>

        {{-- Target Tabungan --}}
        <div class="bg-white border border-gray-100 rounded-xl p-3 shadow-sm">
            <div class="flex justify-between items-center mb-2">
                <p class="font-semibold text-sm text-gray-800">Target Tabungan</p>
                <a href="#" class="text-[10px] text-emerald-600 font-medium hover:text-emerald-700">Lihat Semua</a>
            </div>
            @forelse ($tabunganList as $tab)
                <div class="mb-3 last:mb-0">
                    <div class="flex justify-between items-center mb-1">
                        <p class="text-xs font-medium text-gray-700 truncate">{{ $tab['name'] }}</p>
                        <span class="text-[10px] font-medium {{ $tab['progress'] >= 100 ? 'text-emerald-600' : 'text-gray-400' }}">
                            {{ $tab['progress'] >= 100 ? 'Selesai' : $tab['progress'] . '%' }}
                        </span>
                    </div>
                    <div class="w-full h-2 bg-gray-100 rounded-full overflow-hidden">
                        <div class="h-full rounded-full transition-all duration-300 {{ $tab['progress'] >= 100 ? 'bg-emerald-500' : 'bg-purple-500' }}"
                             style="width: {{ min($tab['progress'], 100) }}%">
                        </div>
                    </div>
                    <p class="text-[10px] text-gray-400 mt-0.5">
                        Rp{{ number_format($tab['current_amount'], 0, ',', '.') }}
                        <span class="text-gray-300">/</span>
                        Rp{{ number_format($tab['target_amount'], 0, ',', '.') }}
                    </p>
                </div>
            @empty
                <p class="text-xs text-gray-400 text-center py-4">Belum ada target tabungan</p>
            @endforelse
        </div>
    </div>

    <x-mobile.bottom-nav active="dashboard" />
</div>