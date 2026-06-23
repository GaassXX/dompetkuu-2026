<div class="min-h-screen bg-gray-50 text-gray-900 pb-28">

    {{-- Header --}}
    <div class="flex items-center justify-between px-4 pt-4 pb-3">
        <div class="flex items-center gap-2">
            <div class="w-7 h-7 rounded-lg bg-emerald-600 flex items-center justify-center shrink-0">
                <x-heroicon-o-banknotes class="w-4 h-4 text-white" />
            </div>
            <span class="font-bold text-gray-800">Dompetkuu</span>
        </div>
        <x-heroicon-o-bell class="w-5 h-5 text-gray-400" />
    </div>

    {{-- Card Saldo --}}
    <div class="mx-4 rounded-2xl bg-gradient-to-br from-emerald-500 to-emerald-600 p-4">
        <p class="text-xs text-emerald-50 font-medium">TOTAL SALDO</p>
        <p class="text-2xl font-bold mt-1 text-white">Rp {{ number_format($totalSaldo, 0, ',', '.') }}</p>
        <div class="inline-flex items-center gap-1 mt-2 bg-white/20 text-white text-[11px] font-medium px-2 py-1 rounded-full">
            <x-heroicon-o-arrow-trending-up class="w-3 h-3" />
            Bulan {{ now()->translatedFormat('F Y') }}
        </div>
    </div>

    {{-- Pemasukan / Pengeluaran --}}
    <div class="flex gap-3 mx-4 mt-3">
        <div class="flex-1 bg-white border border-gray-100 rounded-xl p-3 flex items-center gap-2 shadow-sm">
            <div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center shrink-0">
                <x-heroicon-o-arrow-down class="w-4 h-4 text-emerald-600" />
            </div>
            <div>
                <p class="text-[11px] text-gray-400">Pemasukan</p>
                <p class="text-emerald-600 font-semibold text-sm">Rp {{ number_format($pemasukanBulanIni / 1000, 1) }}k</p>
            </div>
        </div>
        <div class="flex-1 bg-white border border-gray-100 rounded-xl p-3 flex items-center gap-2 shadow-sm">
            <div class="w-8 h-8 rounded-full bg-red-100 flex items-center justify-center shrink-0">
                <x-heroicon-o-arrow-up class="w-4 h-4 text-red-500" />
            </div>
            <div>
                <p class="text-[11px] text-gray-400">Pengeluaran</p>
                <p class="text-red-500 font-semibold text-sm">Rp {{ number_format($pengeluaranBulanIni / 1000, 1) }}k</p>
            </div>
        </div>
    </div>

    {{-- Arus Keuangan --}}
    <div class="mx-4 mt-4">
        <div class="flex justify-between items-center mb-2">
            <p class="font-semibold text-sm text-gray-800">Arus Keuangan</p>
            <span class="text-xs text-gray-400">7 Hari Terakhir</span>
        </div>
        <div class="bg-white border border-gray-100 rounded-xl p-3 flex items-end gap-2 h-28 shadow-sm">
    @php $max = max(array_column($chart7Hari, 'total')) ?: 1; @endphp
    @foreach ($chart7Hari as $day)
        <div class="flex-1 flex flex-col items-center justify-end h-full">
            <div class="w-full rounded-t {{ $day['net'] < 0 ? 'bg-red-300/70' : 'bg-emerald-300/70' }}"
                 style="height: {{ $day['total'] > 0 ? max(10, ($day['total'] / $max) * 100) : 4 }}%">
            </div>
            <span class="text-[10px] mt-1 {{ $day['net'] < 0 ? 'text-red-400' : 'text-gray-400' }}">{{ strtoupper($day['label']) }}</span>
        </div>
    @endforeach
</div>
    </div>

    {{-- Transaksi Terakhir --}}
    <div class="mx-4 mt-4">
        <div class="flex justify-between items-center mb-2">
            <p class="font-semibold text-sm text-gray-800">Transaksi Terakhir</p>
            <a href="{{ route('mobile.history') }}" class="text-xs text-emerald-600 font-medium">Lihat Semua</a>
        </div>

        <div class="space-y-2">
            @forelse ($transaksiTerakhir as $trx)
                <x-mobile.transaction-item :transaction="$trx" />
            @empty
                <p class="text-xs text-gray-400 text-center py-4">Belum ada transaksi</p>
            @endforelse
        </div>
    </div>

    {{-- Tanya AI Keuangan --}}
    <div class="mx-4 mt-4">
        <div class="bg-gray-900 rounded-2xl p-4 relative overflow-hidden">
            <p class="text-white text-sm font-semibold">Tanya AI Keuangan</p>
            <p class="text-gray-400 text-xs mt-1">"Berapa sisa budget makan minggu ini?"</p>
            <div class="flex gap-2 mt-3">
                <span class="text-[11px] bg-gray-800 text-gray-300 px-2.5 py-1 rounded-full">Cek Saldo</span>
                <span class="text-[11px] bg-gray-800 text-gray-300 px-2.5 py-1 rounded-full">Atur Budget</span>
            </div>
            <a href="{{ route('mobile.ai-bot') }}" class="absolute bottom-3 right-3 w-9 h-9 bg-emerald-500 rounded-full flex items-center justify-center">
                <x-heroicon-o-plus class="w-5 h-5 text-white" />
            </a>
        </div>
    </div>

    <x-mobile.bottom-nav active="dashboard" />
</div>
