<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">Anggaran Saya</x-slot>

        @php $budgets = $this->getBudgets() @endphp

        @if($budgets->isEmpty())
            <p class="text-sm text-gray-400 text-center py-4">
                Belum ada anggaran yang diset orangtua
            </p>
        @else
            <div class="space-y-4">
                @foreach($budgets as $budget)
                <div class="p-4 rounded-xl bg-gray-50 dark:bg-gray-800 space-y-2">

                    {{-- Header --}}
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="font-semibold text-sm text-gray-800 dark:text-white">
                                {{ $budget['category'] }}
                            </p>
                            <p class="text-xs text-gray-400">{{ $budget['period'] }}</p>
                        </div>
                        <span @class([
                            'text-xs px-2 py-1 rounded-full font-semibold',
                            'bg-red-100 text-red-700'   => $budget['is_exceeded'],
                            'bg-green-100 text-green-700' => !$budget['is_exceeded'],
                        ])>
                            {{ $budget['is_exceeded'] ? 'Melebihi!' : $budget['percentage'] . '%' }}
                        </span>
                    </div>

                    {{-- Progress Bar --}}
                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                        <div
                            @class([
                                'h-2 rounded-full transition-all',
                                'bg-red-500'    => $budget['is_exceeded'],
                                'bg-yellow-500' => !$budget['is_exceeded'] && $budget['percentage'] >= 75,
                                'bg-green-500'  => !$budget['is_exceeded'] && $budget['percentage'] < 75,
                            ])
                            style="width: {{ $budget['percentage'] }}%"
                        ></div>
                    </div>

                    {{-- Detail --}}
                    <div class="flex justify-between text-xs text-gray-500">
                        <span>
                            Terpakai: <strong>Rp {{ number_format($budget['spent'], 0, ',', '.') }}</strong>
                        </span>
                        <span>
                            Sisa: <strong class="{{ $budget['is_exceeded'] ? 'text-red-600' : 'text-green-600' }}">
                                Rp {{ number_format($budget['remaining'], 0, ',', '.') }}
                            </strong>
                        </span>
                        <span>
                            Limit: <strong>Rp {{ number_format($budget['limit'], 0, ',', '.') }}</strong>
                        </span>
                    </div>

                </div>
                @endforeach
            </div>
        @endif

    </x-filament::section>
</x-filament-widgets::widget>
