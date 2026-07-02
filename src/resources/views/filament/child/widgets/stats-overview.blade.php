<x-filament-widgets::widget>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach($cards as $card)
            <div class="relative bg-white rounded-xl border border-[#E2E8F0] shadow-sm hover:shadow-md transition-all duration-200">
                <div class="absolute top-0 left-0 right-0 h-1 rounded-t-xl {{ $card['accent'] }}"></div>
                <div class="p-4">
                    <div class="flex items-start justify-between mb-2">
                        <span class="text-xs font-semibold text-[#64748B] uppercase tracking-wider">{{ $card['label'] }}</span>
                        <div class="w-8 h-8 rounded-lg bg-[#F8FAFC] flex items-center justify-center flex-shrink-0 ml-2" style="color: {{ $card['color'] }};">
                            <x-dynamic-component :component="$card['icon']" class="w-4 h-4" />
                        </div>
                    </div>
                    <p class="text-xl font-bold text-[#1E293B] tracking-tight">{{ $card['value'] }}</p>
                </div>
            </div>
        @endforeach
    </div>
</x-filament-widgets::widget>
