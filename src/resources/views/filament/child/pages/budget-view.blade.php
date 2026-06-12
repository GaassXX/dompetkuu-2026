<x-filament-panels::page>
    <div class="space-y-4">
        @php
            $budgets = $this->getAllBudgets();
        @endphp

        @if($budgets->isEmpty())
            <div class="text-center py-12">
                <p class="text-gray-400 text-sm">Belum ada anggaran yang diset orangtua</p>
            </div>
        @else
            <div class="grid grid-cols-1 gap-4">
                @foreach($budgets as $budget)
                <div class="p-4 rounded-xl bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 shadow-sm space-y-2">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="font-semibold text-sm text-gray-800 dark:text-white">{{ $budget['category'] }}</p>
                            <p class="text-xs text-gray-400">{{ $budget['period'] }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-semibold text-gray-700 dark:text-gray-200">
                                Sisa Rp {{ number_format($budget['remaining'], 0, ',', '.') }}
                            </p>
                            <p class="text-xs text-gray-400">/ Rp {{ number_format($budget['limit'], 0, ',', '.') }}</p>
                        </div>
                    </div>

                    <div class="w-full bg-gray-100 dark:bg-gray-700 rounded-full h-2.5">
                        <div
                            @class([
                                'h-2.5 rounded-full transition-all duration-300',
                                'bg-red-500'     => $budget['is_exceeded'],
                                'bg-yellow-400'  => !$budget['is_exceeded'] && $budget['percentage'] >= 75,
                                'bg-primary-500' => !$budget['is_exceeded'] && $budget['percentage'] < 75,
                            ])
                            style="width: {{ $budget['percentage'] }}%"
                        ></div>
                    </div>

                    <div class="flex justify-between text-xs text-gray-400">
                        <span>Terpakai: Rp {{ number_format($budget['spent'], 0, ',', '.') }}</span>
                        <span @class([
                            'font-semibold',
                            'text-red-500'   => $budget['is_exceeded'],
                            'text-green-500' => !$budget['is_exceeded'],
                        ])>
                            {{ $budget['is_exceeded'] ? 'Melebihi batas!' : $budget['percentage'] . '%' }}
                        </span>
                    </div>
                </div>
                @endforeach
            </div>
        @endif
    </div>
</x-filament-panels::page>
