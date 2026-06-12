<x-filament-widgets::widget class="fi-wi-chart">
    <x-filament::section>

        <x-slot name="heading">
            <div style="display:flex;align-items:center;justify-content:space-between;width:100%;flex-wrap:wrap;gap:8px;">
                <p style="font-size:14px;font-weight:600;margin:0;">Arus Keuangan Keluarga</p>
                <div style="display:flex;gap:8px;align-items:center;">
                    <select wire:model.live="durationFilter"
                        style="font-size:12px;padding:5px 10px;border-radius:8px;border:1px solid var(--color-border-tertiary);background:var(--color-background-primary);color:var(--color-text-primary);cursor:pointer;">
                        <option value="1">1 Bulan</option>
                        <option value="3">3 Bulan</option>
                        <option value="6">6 Bulan</option>
                        <option value="12">12 Bulan</option>
                    </select>
                    <select wire:model.live="filter"
                        style="font-size:12px;padding:5px 10px;border-radius:8px;border:1px solid var(--color-border-tertiary);background:var(--color-background-primary);color:var(--color-text-primary);cursor:pointer;">
                        <option value="family">Seluruh Keluarga</option>
                        <option value="me">Saya Sendiri</option>
                        @foreach(\App\Models\User::where('parent_id', auth()->id())->get() as $child)
                            <option value="child_{{ $child->id }}">{{ $child->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </x-slot>

        <div
            wire:ignore
            x-data="{
                chart: null,
                handler: null,
                chartOptions: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: true, position: 'bottom' } },
                    scales: { y: { beginAtZero: true } }
                },
                init() {
                    this.boot({{ Js::from($this->getData()) }});
                    this.handler = (e) => {
                        const raw = Array.isArray(e.detail) ? e.detail[0] : e.detail;
                        if (!raw?.datasets) return;
                        const canvas = this.$refs.canvas;
                        if (!canvas) return;
                        const existing = Chart.getChart(canvas);
                        if (existing) existing.destroy();
                        this.chart = new Chart(canvas.getContext('2d'), { type: 'line', data: raw, options: this.chartOptions });
                    };
                    window.addEventListener('familyChartUpdate', this.handler);
                },
                destroy() {
                    window.removeEventListener('familyChartUpdate', this.handler);
                },
                boot(data) {
                    if (typeof Chart !== 'undefined') {
                        this.draw(data);
                    } else {
                        const s = document.createElement('script');
                        s.src = 'https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js';
                        s.onload = () => this.draw(data);
                        document.head.appendChild(s);
                    }
                },
                draw(data) {
                    const canvas = this.$refs.canvas;
                    if (!canvas) return;
                    const existing = Chart.getChart(canvas);
                    if (existing) existing.destroy();
                    this.chart = new Chart(canvas.getContext('2d'), { type: 'line', data: data, options: this.chartOptions });
                }
            }"
        >
            <div style="height:280px;position:relative;">
                <canvas x-ref="canvas"></canvas>
            </div>
        </div>

    </x-filament::section>
</x-filament-widgets::widget>
