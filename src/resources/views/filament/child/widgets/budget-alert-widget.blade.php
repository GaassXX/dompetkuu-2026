<x-filament-widgets::widget>
    <div class="space-y-3">
        @foreach($alerts as $alert)
            @php
                $isExact = !$alert['is_over'] && $alert['percentage'] >= 100;
                $isWarning = !$alert['is_over'] && !$isExact;
            @endphp
            <div @class([
                'flex items-start gap-4 py-5 px-4 rounded-xl border shadow-sm relative',
                'bg-red-50 border-red-300 dark:bg-red-950 dark:border-red-700'       => $alert['is_over'],
                'bg-orange-50 border-orange-300 dark:bg-orange-950 dark:border-orange-700' => $isExact,
                'bg-yellow-50 border-yellow-300 dark:bg-yellow-950 dark:border-yellow-700' => $isWarning,
            ])>
                <button
                    wire:click="dismissAlert({{ $alert['budget_id'] }}, '{{ $alert['period_raw'] }}')"
                    title="Tandai sudah dibaca"
                    class="absolute top-2 right-2 p-1 rounded-full hover:bg-black/10 dark:hover:bg-white/10 transition"
                >
                    <svg class="w-4 h-4 {{ $alert['is_over'] ? 'text-red-400' : ($isExact ? 'text-orange-400' : 'text-yellow-400') }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>

                {{-- Icon --}}
                <div @class([
                    'flex-shrink-0 w-11 h-11 rounded-full flex items-center justify-center mt-0.5',
                    'bg-red-100 dark:bg-red-900'       => $alert['is_over'],
                    'bg-orange-100 dark:bg-orange-900' => $isExact,
                    'bg-yellow-100 dark:bg-yellow-900' => $isWarning,
                ])>
                    <svg class="w-6 h-6 {{ $alert['is_over'] ? 'text-red-600' : ($isExact ? 'text-orange-600' : 'text-yellow-600') }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                    </svg>
                </div>

                {{-- Info --}}
                <div class="flex-1 min-w-0">
                    <p @class([
                        'text-sm font-bold',
                        'text-red-700 dark:text-red-400'       => $alert['is_over'],
                        'text-orange-700 dark:text-orange-400' => $isExact,
                        'text-yellow-700 dark:text-yellow-400' => $isWarning,
                    ])>
                        @if($alert['is_over'])
                            🚨 Budget Melebihi Limit!
                        @elseif($isExact)
                            Budget Habis!
                        @else
                            ⚠️ Budget Hampir Habis!
                        @endif
                        — {{ $alert['category'] }} ({{ $alert['period'] }})
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                        Terpakai: <strong>Rp {{ number_format($alert['spent'], 0, ',', '.') }}</strong>
                        dari <strong>Rp {{ number_format($alert['limit'], 0, ',', '.') }}</strong>
                    </p>

                    {{-- Progress Bar --}}
                    <div class="mt-3 w-full bg-gray-200 dark:bg-gray-700 rounded-full h-3">
                        <div
                            @class([
                                'h-3 rounded-full transition-all',
                                'bg-red-500'    => $alert['is_over'],
                                'bg-orange-500' => $isExact,
                                'bg-yellow-500' => $isWarning,
                            ])
                            style="width: {{ min($alert['percentage'], 100) }}%"
                        ></div>
                    </div>
                    <p class="text-xs mt-1.5 font-semibold {{ $alert['is_over'] ? 'text-red-600' : ($isExact ? 'text-orange-600' : 'text-yellow-600') }}">
                        {{ $alert['percentage'] == (int)$alert['percentage'] ? number_format($alert['percentage']) : number_format($alert['percentage'], 1) }}% terpakai
                    </p>
                </div>
            </div>
        @endforeach
    </div>
</x-filament-widgets::widget>
