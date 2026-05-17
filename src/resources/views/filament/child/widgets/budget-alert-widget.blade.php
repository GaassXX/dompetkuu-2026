<x-filament-widgets::widget>
    <div class="space-y-3">
        @foreach($alerts as $alert)
            <div @class([
                'flex items-center gap-4 p-4 rounded-xl border',
                'bg-red-50 border-red-300 dark:bg-red-950 dark:border-red-700'   => $alert['is_over'],
                'bg-yellow-50 border-yellow-300 dark:bg-yellow-950 dark:border-yellow-700' => !$alert['is_over'],
            ])>
                {{-- Icon --}}
                <div @class([
                    'flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center',
                    'bg-red-100 dark:bg-red-900'    => $alert['is_over'],
                    'bg-yellow-100 dark:bg-yellow-900' => !$alert['is_over'],
                ])>
                    @if($alert['is_over'])
                        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                        </svg>
                    @else
                        <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                        </svg>
                    @endif
                </div>

                {{-- Info --}}
                <div class="flex-1 min-w-0">
                    <p @class([
                        'text-sm font-bold',
                        'text-red-700 dark:text-red-400'       => $alert['is_over'],
                        'text-yellow-700 dark:text-yellow-400' => !$alert['is_over'],
                    ])>
                        {{ $alert['is_over'] ? '🚨 Budget Melebihi Limit!' : '⚠️ Budget Hampir Habis!' }}
                        — {{ $alert['category'] }} ({{ $alert['period'] }})
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                        Terpakai: <strong>Rp {{ number_format($alert['spent'], 0, ',', '.') }}</strong>
                        dari <strong>Rp {{ number_format($alert['limit'], 0, ',', '.') }}</strong>
                    </p>

                    {{-- Progress Bar --}}
                    <div class="mt-2 w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                        <div
                            @class([
                                'h-2 rounded-full transition-all',
                                'bg-red-500'    => $alert['is_over'],
                                'bg-yellow-500' => !$alert['is_over'],
                            ])
                            style="width: {{ min($alert['percentage'], 100) }}%"
                        ></div>
                    </div>
                    <p class="text-xs mt-1 font-semibold {{ $alert['is_over'] ? 'text-red-600' : 'text-yellow-600' }}">
                        {{ number_format($alert['percentage'], 1) }}% terpakai
                    </p>
                </div>
            </div>
        @endforeach
    </div>
</x-filament-widgets::widget>
