<div class="min-h-screen bg-gray-50 pb-28">

    {{-- Header --}}
    <div class="bg-gradient-to-r from-amber-500 to-orange-500 px-5 pt-12 pb-6 -mt-2">
        <div class="flex items-start justify-between gap-2">
            <h1 class="text-xl font-extrabold text-white">🔔 Notifikasi</h1>
            <div class="flex items-center gap-2 shrink-0">
                @if ($unreadCount > 0)
                    <button wire:click="markAllAsRead"
                        class="text-xs font-semibold text-amber-100 bg-white/20 px-3 py-1.5 rounded-full hover:bg-white/30 transition">
                        Tandai Dibaca
                    </button>
                @endif
                @if ($notifications->total() > 0)
                    <button wire:click="deleteAll" wire:confirm="Hapus semua notifikasi?"
                        class="text-xs font-semibold text-red-300 bg-white/10 px-3 py-1.5 rounded-full hover:bg-white/20 transition">
                        Hapus Semua
                    </button>
                @endif
            </div>
        </div>
        <p class="text-amber-100 text-sm mt-1">
            @if ($unreadCount > 0)
                {{ $unreadCount }} notifikasi belum dibaca
            @else
                Tidak ada notifikasi baru
            @endif
        </p>
    </div>

    {{-- List --}}
    <div class="px-4 -mt-3 space-y-2">
        @forelse ($notifications as $notif)
            @php
                $data = $notif->data;
                $isUnread = is_null($notif->read_at);
            @endphp
            <div class="bg-white border border-gray-100 rounded-xl shadow-sm overflow-hidden {{ $isUnread ? 'ring-1 ring-amber-200' : '' }}">
                <div class="flex items-start gap-3 p-3.5" wire:click="markAsRead('{{ $notif->id }}')" style="cursor:pointer">
                    @php $icon = $data['icon'] ?? 'heroicon-o-bell'; @endphp
                    <x-dynamic-component :component="$icon" class="w-5 h-5 text-gray-500 shrink-0 mt-0.5" />
                    <div class="flex-1 min-w-0">
                        <div class="flex items-start justify-between gap-2">
                            <p class="text-sm font-semibold text-gray-800 {{ !$isUnread ? 'text-gray-500' : '' }}">
                                {{ $data['title'] ?? 'Notifikasi' }}
                            </p>
                            <span class="text-[10px] text-gray-400 shrink-0">{{ $notif->created_at->diffForHumans() }}</span>
                        </div>
                        <p class="text-xs text-gray-600 mt-0.5 {{ !$isUnread ? 'text-gray-400' : '' }}">
                            {{ $data['message'] ?? '' }}
                        </p>
                        @if (!empty($data['description']))
                            <p class="text-[11px] text-gray-400 mt-1">{{ $data['description'] }}</p>
                        @endif
                        @if ($isUnread)
                            <span class="inline-block mt-1.5 w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-16">
                <div class="w-16 h-16 rounded-full bg-gray-100 flex items-center justify-center mx-auto mb-3">
                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                </div>
                <p class="text-sm font-medium text-gray-500">Belum ada notifikasi</p>
                <p class="text-xs text-gray-400 mt-1">Aktivitas akun akan muncul di sini</p>
            </div>
        @endforelse

        @if ($notifications->hasPages())
            <div class="pt-2">
                {{ $notifications->links() }}
            </div>
        @endif
    </div>

    <x-mobile.bottom-nav active="dashboard" />
</div>
