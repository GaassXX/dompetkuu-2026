@props(['transaction'])

<div class="flex items-center justify-between bg-white border border-gray-100 rounded-xl p-3 shadow-sm">
    <div class="flex items-center gap-3">
        <div class="w-9 h-9 rounded-full flex items-center justify-center
            {{ $transaction->type === 'Pemasukan' ? 'bg-emerald-100 text-emerald-600' : 'bg-red-100 text-red-500' }}">
            <x-heroicon-o-{{ $transaction->type === 'Pemasukan' ? 'arrow-down' : 'arrow-up' }} class="w-4 h-4" />
        </div>
        <div>
            <p class="text-sm font-medium text-gray-800">{{ $transaction->category_name }}</p>
            <p class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($transaction->date)->translatedFormat('d M, H:i') }}</p>
        </div>
    </div>
    <span class="text-sm font-semibold {{ $transaction->type === 'Pemasukan' ? 'text-emerald-600' : 'text-red-500' }}">
        {{ $transaction->type === 'Pemasukan' ? '+' : '-' }}Rp {{ number_format($transaction->amount, 0, ',', '.') }}
    </span>
</div>
