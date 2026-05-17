<x-filament-panels::page>
    <div class="space-y-4">
        {{ $this->table }}
    </div>

    {{-- Modal Detail --}}
    @if($showModal && $selectedTransaction)
    <div
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/50"
        wire:click.self="$set('showModal', false)"
    >
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl w-full max-w-md p-6 space-y-4">

            <div class="flex items-center justify-between">
                <h2 class="text-lg font-bold text-gray-800 dark:text-white">Detail Transaksi</h2>
                <button wire:click="$set('showModal', false)" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <p class="text-xs text-gray-400">Tipe</p>
                    <span @class([
                        'px-2 py-1 rounded-full text-xs font-semibold',
                        'bg-green-100 text-green-700' => $selectedTransaction['type'] === 'Pemasukan',
                        'bg-red-100 text-red-700'     => $selectedTransaction['type'] === 'Pengeluaran',
                    ])>{{ $selectedTransaction['type'] }}</span>
                </div>

                <div>
                    <p class="text-xs text-gray-400">Nama</p>
                    <p class="font-semibold text-sm">{{ $selectedTransaction['user_name'] }}</p>
                </div>

                <div>
                    <p class="text-xs text-gray-400">Kategori</p>
                    <p class="font-semibold text-sm">{{ $selectedTransaction['category_name'] }}</p>
                </div>

                <div>
                    <p class="text-xs text-gray-400">Jumlah</p>
                    <p class="font-semibold text-sm text-green-600">
                        Rp {{ number_format((float) $selectedTransaction['amount'], 0, ',', '.') }}
                    </p>
                </div>

                <div>
                    <p class="text-xs text-gray-400">Tanggal</p>
                    <p class="font-semibold text-sm">
                        {{ \Carbon\Carbon::parse($selectedTransaction['date'])->translatedFormat('d F Y') }}
                    </p>
                </div>

                <div>
                    <p class="text-xs text-gray-400">Status</p>
                    <span @class([
                        'px-2 py-1 rounded-lg text-xs font-semibold',
                        'bg-yellow-100 text-yellow-700' => $selectedTransaction['status'] === 'pending',
                        'bg-green-100 text-green-700'   => $selectedTransaction['status'] === 'approved',
                        'bg-red-100 text-red-700'       => $selectedTransaction['status'] === 'rejected',
                    ])>
                        {{ match($selectedTransaction['status']) {
                            'pending'  => 'Pending',
                            'approved' => 'Disetujui',
                            'rejected' => 'Ditolak',
                            default    => $selectedTransaction['status'],
                        } }}
                    </span>
                </div>
            </div>

            <div>
                <p class="text-xs text-gray-400">Keterangan</p>
                <p class="text-sm">{{ $selectedTransaction['description'] ?? '-' }}</p>
            </div>

            <button
                wire:click="$set('showModal', false)"
                class="w-full mt-2 py-2 rounded-xl bg-gray-100 dark:bg-gray-700 text-sm font-medium hover:bg-gray-200"
            >
                Tutup
            </button>
        </div>
    </div>
    @endif
</x-filament-panels::page>
