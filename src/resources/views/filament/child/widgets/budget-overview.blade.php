<x-filament-widgets::widget>
    <x-filament::section class="rounded-2xl border border-gray-100 dark:border-gray-700/60 shadow-sm bg-white dark:bg-gray-800">

        <x-slot name="heading">
            <div class="flex items-center justify-between w-full">
                <span class="text-sm font-bold text-gray-900 dark:text-white tracking-tight">Anggaran Saya</span>
                <a href="{{ route('filament.child.pages.budget-view') }}"
                   class="text-xs font-medium text-[var(--color-primary-500)] hover:text-[var(--color-primary-600)] transition-colors">
                    Lihat semua →
                </a>
            </div>
        </x-slot>

        <div class="min-h-[200px] flex flex-col justify-start overflow-y-auto pt-2">
            @php $budgets = $this->getBudgets() @endphp

            @if($budgets->isEmpty())
                <div class="flex flex-col items-center justify-center py-12 gap-2">
                    <p class="text-sm text-gray-400 dark:text-gray-500">Belum ada anggaran yang diset</p>
                </div>
            @else
                <div class="space-y-4">
                    @foreach($budgets as $budget)
                    <div class="space-y-2">
                        <div class="flex items-center justify-between">
                            <div>
                                <span class="text-sm font-bold text-gray-800 dark:text-gray-100">
                                    {{ $budget['category'] }}
                                </span>
                            </div>
                            <div class="text-right flex items-baseline gap-1">
                                <span class="text-sm font-bold text-gray-800 dark:text-gray-200">
                                    Rp {{ number_format($budget['remaining'], 0, ',', '.') }}
                                </span>
                                <span class="text-xs text-gray-400">
                                    / Rp {{ number_format($budget['limit'], 0, ',', '.') }}
                                </span>
                            </div>
                        </div>

                        {{-- Progress Bar Rapi --}}
                        <div class="w-full bg-gray-100 dark:bg-gray-700 rounded-full h-2">
                            <div
                                @class([
                                    'h-2 rounded-full transition-all duration-500',
                                    'bg-red-500 shadow-sm shadow-red-100' => $budget['is_exceeded'],
                                    'bg-amber-400' => !$budget['is_exceeded'] && $budget['percentage'] >= 75,
                                    'bg-emerald-500' => !$budget['is_exceeded'] && $budget['percentage'] < 75,
                                ])
                                style="width: {{ min($budget['percentage'], 100) }}%"
                            ></div>
                        </div>

                        <div class="flex justify-between text-xs text-gray-400">
                            <span>Terpakai: Rp {{ number_format($budget['spent'], 0, ',', '.') }}</span>
                            <span @class([
                                'font-semibold text-xs',
                                'text-red-500' => $budget['is_exceeded'],
                                'text-emerald-500' => !$budget['is_exceeded'],
                            ])>
                                {{ $budget['is_exceeded'] ? 'Melebihi anggaran! (' . number_format($budget['spent'] - $budget['limit'], 0, ',', '.') . ')' : $budget['percentage'] . '%' }}
                            </span>
                        </div>
                    </div>

                    @if(!$loop->last)
                        <hr class="border-gray-100/70 dark:border-gray-700/50 my-2">
                    @endif
                    @endforeach
                </div>
            @endif
        </div>

    </x-filament::section>
</x-filament-widgets::widget>
