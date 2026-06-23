{{-- ai-chat-bot.blade.php --}}
<div class="min-h-screen bg-gray-50 text-gray-900 pb-28 flex flex-col">

    {{-- Header --}}
    <div class="flex items-center justify-between px-4 pt-4 pb-2">
        <div class="flex items-center gap-2">
            <div class="w-7 h-7 rounded-lg bg-emerald-600 flex items-center justify-center shrink-0">
                <x-heroicon-o-banknotes class="w-4 h-4 text-white" />
            </div>
            <span class="font-bold text-gray-800">Dompetkuu</span>
        </div>
        <div class="flex items-center gap-3">
            <x-heroicon-o-bell class="w-5 h-5 text-gray-400" />
            <x-heroicon-o-user-circle class="w-5 h-5 text-gray-400" />
        </div>
    </div>

    {{-- Intro Card --}}
    @if (count($chatHistory) === 0)
        <div class="mx-4 mt-2 bg-emerald-50 border border-emerald-100 rounded-2xl p-4 flex items-start gap-3">
            <div class="w-9 h-9 rounded-full bg-emerald-600 flex items-center justify-center shrink-0">
                <x-heroicon-o-sparkles class="w-4 h-4 text-white" />
            </div>
            <div>
                <p class="font-semibold text-sm text-gray-800">Asisten AI Dompetkuu</p>
                <p class="text-xs text-gray-500 mt-0.5">Siap membantu mencatat pengeluaran Anda dalam sekejap.</p>
            </div>
        </div>
    @endif

    {{-- Chat messages --}}
    <div class="flex-1 mx-4 mt-3 space-y-3 overflow-y-auto">
        @foreach ($chatHistory as $chat)
            <div class="flex items-end gap-2 {{ $chat['from'] === 'user' ? 'justify-end' : 'justify-start' }}">
                @if ($chat['from'] !== 'user')
                    <div class="w-6 h-6 rounded-full bg-emerald-600 flex items-center justify-center shrink-0">
                        <x-heroicon-o-sparkles class="w-3 h-3 text-white" />
                    </div>
                @endif
                <div class="max-w-[75%] rounded-2xl px-3 py-2 text-sm
                    {{ $chat['from'] === 'user' ? 'bg-emerald-600 text-white' : 'bg-white border border-gray-100 text-gray-700 shadow-sm' }}">
                    {{ $chat['text'] }}
                </div>
            </div>
        @endforeach
    </div>

    {{-- Quick actions --}}
    <div class="flex gap-2 mx-4 mt-3 overflow-x-auto">
        <button type="button" wire:click="quickAction('laporan')" class="shrink-0 flex items-center gap-1.5 bg-white border border-gray-200 text-gray-600 text-xs font-medium px-3 py-1.5 rounded-full">
            <x-heroicon-o-document-chart-bar class="w-3.5 h-3.5" />
            Laporan Mingguan
        </button>
        <button type="button" wire:click="quickAction('transaksi')" class="shrink-0 flex items-center gap-1.5 bg-white border border-gray-200 text-gray-600 text-xs font-medium px-3 py-1.5 rounded-full">
            <x-heroicon-o-plus-circle class="w-3.5 h-3.5" />
            Tambah Transaksi
        </button>
    </div>

    {{-- Input --}}
    <form wire:submit.prevent="sendMessage" class="flex items-center gap-2 mx-4 mt-3 mb-4">
        <input type="text" wire:model="message" placeholder="Ketik pesan di sini..."
               class="flex-1 bg-white border border-gray-200 rounded-full px-4 py-2.5 text-sm outline-none focus:border-emerald-400">
        <button type="submit" class="bg-emerald-600 rounded-full p-2.5 shrink-0">
            <x-heroicon-o-paper-airplane class="w-5 h-5 text-white" />
        </button>
    </form>

    <x-mobile.bottom-nav active="ai-bot" />
</div>
