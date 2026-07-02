<x-filament-widgets::widget>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach($cards as $card)
            <div class="rounded-2xl border border-gray-100 dark:border-gray-700/60 shadow-sm bg-white dark:bg-gray-800 p-4">

                {{-- Icon lingkaran --}}
                <div class="w-11 h-11 rounded-full flex items-center justify-center mb-3"
                     style="background: {{ $card['bg'] }};">
                    <x-dynamic-component
                        :component="$card['icon']"
                        style="width:20px;height:20px;color:{{ $card['iconColor'] }};"
                    />
                </div>

                {{-- Label --}}
                <p class="text-xs font-semibold tracking-wide text-gray-400 uppercase mb-1">
                    {{ $card['label'] }}
                </p>

                {{-- Nominal --}}
                <p class="text-xl font-bold mb-2" style="color: {{ $card['amountColor'] }};">
                    Rp {{ number_format($card['amount'], 0, ',', '.') }}
                </p>

                {{-- Sub label + badge trend --}}
                <div class="flex items-center justify-between">
                    <span class="text-xs text-gray-400">{{ $card['sub'] }}</span>

                    @if($card['trend'] !== 0)
                        <span
                            class="text-xs font-semibold px-2 py-0.5 rounded-full"
                            style="
                                background: {{ $card['trend'] >= 0 ? '#DCFCE7' : '#FEE2E2' }};
                                color: {{ $card['trend'] >= 0 ? '#16A34A' : '#DC2626' }};
                            "
                        >
                            {{ $card['trend'] >= 0 ? '↑' : '↓' }} {{ abs($card['trend']) }}%
                        </span>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</x-filament-widgets::widget>
