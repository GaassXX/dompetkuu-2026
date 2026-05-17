<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">Transaksi Terbaru</x-slot>

        <div class="space-y-3">
            @forelse($this->getTransactions() as $transaction)
            <div class="flex items-center justify-between p-3 rounded-xl bg-gray-50 dark:bg-gray-800">
                <div class="flex items-center gap-3">
                    <div @class([
                        'w-9 h-9 rounded-full flex items-center justify-center',
                        'bg-green-100' => $transaction['type'] === 'Pemasukan',
                        'bg-red-100'   => $transaction['type'] === 'Pengeluaran',
                    ])>
                        @if($transaction['type'] === 'Pemasukan')
                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
                            </svg>
                        @else
                            <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                            </svg>
                        @endif
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-800 dark:text-white">{{ $transaction['category'] }}</p>
                        <p class="text-xs text-gray-400">
                            {{ \Carbon\Carbon::parse($transaction['date'])->translatedFormat('d F Y') }}
                        </p>
                    </div>
                </div>
                <div class="text-right">
                    <p @class([
                        'text-sm font-bold',
                        'text-green-600' => $transaction['type'] === 'Pemasukan',
                        'text-red-600'   => $transaction['type'] === 'Pengeluaran',
                    ])>
                        {{ $transaction['type'] === 'Pemasukan' ? '+' : '-' }}
                        Rp {{ number_format((float) $transaction['amount'], 0, ',', '.') }}
                    </p>
                    <span @class([
                        'text-xs px-2 py-0.5 rounded-full',
                        'bg-yellow-100 text-yellow-700' => $transaction['status'] === 'pending',
                        'bg-green-100 text-green-700'   => $transaction['status'] === 'approved',
                        'bg-red-100 text-red-700'       => $transaction['status'] === 'rejected',
                    ])>
                        {{ match($transaction['status']) {
                            'pending'  => 'Pending',
                            'approved' => 'Disetujui',
                            'rejected' => 'Ditolak',
                            default    => $transaction['status'],
                        } }}
                    </span>
                </div>
            </div>
            @empty
            <p class="text-sm text-gray-400 text-center py-4">Belum ada transaksi</p>
            @endforelse
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
