<x-filament-widgets::widget class="fi-wi-chart">
    <x-filament::section>

        <x-slot name="heading">
            <div style="display:flex;align-items:center;justify-content:space-between;width:100%;flex-wrap:wrap;gap:8px;">
                <span style="font-size:14px;font-weight:500;">Arus Keuangan Keluarga</span>
                <div style="display:flex;gap:8px;align-items:center;">

                    <select
                        wire:model.live="durationFilter"
                        style="font-size:12px;padding:4px 10px;border-radius:8px;border:0.5px solid var(--color-border-tertiary);background:var(--color-background-secondary);color:var(--color-text-primary);">
                        <option value="1">1 Bulan</option>
                        <option value="3">3 Bulan</option>
                        <option value="6">6 Bulan</option>
                        <option value="12">12 Bulan</option>
                    </select>

                    <select
                        wire:model.live="filter"
                        style="font-size:12px;padding:4px 10px;border-radius:8px;border:0.5px solid var(--color-border-tertiary);background:var(--color-background-secondary);color:var(--color-text-primary);">
                        <option value="family">Seluruh Keluarga</option>
                        <option value="me">Saya Sendiri</option>
                        @foreach(\App\Models\User::where('parent_id', auth()->id())->get() as $child)
                        <option value="child_{{ $child->id }}">{{ $child->name }}</option>
                        @endforeach
                    </select>

                </div>
            </div>
        </x-slot>

        {{-- ✅ Load Chart.js via CDN --}}
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

        <div wire:ignore id="chart-wrapper-{{ $this->filter }}-{{ $this->durationFilter }}">
            <canvas id="familyFinanceChart" style="max-height:300px;"></canvas>
        </div>

        <script>
            (function() {
                const ctx = document.getElementById('familyFinanceChart');
                if (!ctx) return;

                // Destroy existing chart jika ada
                if (window._familyChart) {
                    window._familyChart.destroy();
                }

                window._familyChart = new Chart(ctx.getContext('2d'), {
                    type: 'bar',
                    data: @json($this->getData()),
                    options: {
                        responsive: true,
                        plugins: { legend: { display: true } },
                        scales: { y: { beginAtZero: true } }
                    }
                });
            })();
        </script>

        {{-- ✅ Re-render chart saat Livewire update --}}
        <script>
            document.addEventListener('livewire:updated', function () {
                const ctx = document.getElementById('familyFinanceChart');
                if (!ctx) return;

                if (window._familyChart) {
                    window._familyChart.destroy();
                }

                window._familyChart = new Chart(ctx.getContext('2d'), {
                    type: 'bar',
                    data: @json($this->getData()),
                    options: {
                        responsive: true,
                        plugins: { legend: { display: true } },
                        scales: { y: { beginAtZero: true } }
                    }
                });
            });
        </script>

    </x-filament::section>
</x-filament-widgets::widget>
