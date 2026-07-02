@php
    $route = request()->route()->getName();
    $items = [
        'dashboard' => ['label' => 'Dashboard',     'icon' => 'squares-2x2', 'route' => 'mobile.dashboard'],
        'history'   => ['label' => 'Riwayat',       'icon' => 'clock',       'route' => 'mobile.history'],
        'ai-bot'    => ['label' => 'AI Bot',        'icon' => 'cpu-chip',    'route' => 'mobile.ai-bot'],
        'profile'   => ['label' => 'Profil',        'icon' => 'user',        'route' => 'mobile.profile'],
    ];
    $active = collect($items)->search(fn ($i) => $route === $i['route']) ?: 'dashboard';
@endphp

<div class="hidden md:flex md:flex-col md:fixed md:inset-y-0 md:w-64 bg-white border-r border-gray-200 z-30">
    {{-- Logo --}}
    <div class="flex items-center gap-2.5 px-5 py-5 border-b border-gray-100">
        <div class="w-9 h-9 rounded-xl bg-emerald-600 flex items-center justify-center shrink-0 shadow-sm">
            <x-dynamic-component :component="'heroicon-o-banknotes'" class="w-5 h-5 text-white" />
        </div>
        <span class="font-bold text-gray-800 text-lg tracking-tight">Dompetkuu</span>
    </div>

    {{-- Nav Items --}}
    <nav class="flex-1 px-3 py-5 space-y-1 overflow-y-auto">
        @foreach ($items as $key => $item)
            <a href="{{ route($item['route']) }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors duration-150
                      {{ $active === $key ? 'bg-emerald-50 text-emerald-700' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-700' }}">
                <x-dynamic-component :component="'heroicon-o-' . $item['icon']" class="w-5 h-5 shrink-0" />
                {{ $item['label'] }}
            </a>
        @endforeach
    </nav>

    {{-- User Footer --}}
    @auth
    <div class="px-3 py-4 border-t border-gray-100">
        <div class="flex items-center gap-3 px-3 py-2">
            <div class="w-9 h-9 rounded-full bg-emerald-100 flex items-center justify-center shrink-0">
                <span class="text-sm font-semibold text-emerald-700">
                    {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                </span>
            </div>
            <div class="min-w-0 flex-1">
                <p class="text-sm font-medium text-gray-800 truncate">{{ auth()->user()->name }}</p>
                <p class="text-xs text-gray-400 truncate">
                    @if(auth()->user()->is_independent)
                        Akun Pribadi
                    @elseif(auth()->user()->hasRole('parent'))
                        Orang Tua
                    @elseif(auth()->user()->hasRole('child'))
                        Anggota Keluarga
                    @else
                        Admin
                    @endif
                </p>
            </div>
        </div>
    </div>
    @endauth
</div>