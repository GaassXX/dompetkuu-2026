<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            <div style="display:flex;align-items:center;justify-content:space-between;width:100%;flex-wrap:wrap;gap:8px;">
                <div>
                    <p style="font-size:14px;font-weight:600;margin:0;">Pengeluaran Terbesar</p>
                    <p style="font-size:11px;color:var(--color-text-secondary);margin:0;">Kategori {{ $currentMonth }}</p>
                </div>
                <div style="display:flex;align-items:center;gap:8px;">
                    <select wire:model.live="monthFilter"
                        style="font-size:12px;padding:5px 10px;border-radius:8px;border:1px solid var(--color-border-tertiary);background:var(--color-background-primary);color:var(--color-text-primary);cursor:pointer;">
                        @foreach($this->getMonthOptions() as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    @if($totalExpense > 0)
                    <span style="font-size:12px;font-weight:600;padding:3px 10px;border-radius:99px;background:#FEE2E2;color:#DC2626;white-space:nowrap;">
                        Rp {{ number_format($totalExpense, 0, ',', '.') }}
                    </span>
                    @endif
                </div>
            </div>
        </x-slot>

        <div style="min-height:280px;display:flex;flex-direction:column;justify-content:center;">
        @if(empty($categoryData))
            <div style="text-align:center;padding:32px 0;">
                <x-heroicon-o-chart-pie style="width:40px;height:40px;color:var(--color-text-secondary);opacity:0.3;margin:0 auto 8px;"/>
                <p style="font-size:13px;color:var(--color-text-secondary);">Belum ada pengeluaran bulan ini</p>
            </div>
        @else
        <div style="display:flex;gap:20px;align-items:center;"
             x-data="{
                slices: [],
                tip: { show: false, x: 0, y: 0, label: '', amt: '', color: '' },

                drawAll(activeIdx) {
                    const el  = document.getElementById('donut-{{ md5($currentMonth) }}');
                    if (!el) return;
                    const ctx = el.getContext('2d');
                    const dpr = window.devicePixelRatio || 1;
                    const S   = 150;
                    ctx.clearRect(0, 0, S * dpr, S * dpr);
                    const cx = S/2, cy = S/2, baseR = 54, baseLW = 24;
                    const gap = this.slices.length > 1 ? 0.04 : 0;
                    this.slices.forEach((s, i) => {
                        const active = i === activeIdx;
                        const r  = (active ? baseR + 4 : baseR) * dpr;
                        const lw = (active ? baseLW + 4 : baseLW) * dpr;
                        ctx.beginPath();
                        ctx.arc(cx*dpr, cy*dpr, r, s.s + gap/2, s.e - gap/2);
                        ctx.strokeStyle = s.color;
                        ctx.lineWidth   = lw;
                        ctx.lineCap     = gap === 0 ? 'butt' : 'round';
                        ctx.stroke();
                    });
                },

                hitTest(mx, my, rect) {
                    const cx = 75, cy = 75, r = 54, lw = 24;
                    const x = mx - rect.left - cx;
                    const y = my - rect.top  - cy;
                    const d = Math.sqrt(x*x + y*y);
                    if (d < r - lw/2 || d > r + lw/2) return -1;
                    let a = Math.atan2(y, x);
                    if (a < -Math.PI/2) a += 2*Math.PI;
                    for (let i = 0; i < this.slices.length; i++) {
                        let s = this.slices[i].s, e = this.slices[i].e;
                        if (s < -Math.PI/2) { s += 2*Math.PI; e += 2*Math.PI; }
                        if (a >= s && a <= e) return i;
                    }
                    return -1;
                },

                init() {
                    this.$nextTick(() => {
                        const data  = {{ Js::from($categoryData) }};
                        const total = {{ $totalExpense }};
                        const el    = document.getElementById('donut-{{ md5($currentMonth) }}');
                        if (!el) return;
                        const dpr = window.devicePixelRatio || 1;
                        el.width  = 150 * dpr;
                        el.height = 150 * dpr;

                        let start = -Math.PI / 2;
                        this.slices = data.map(d => {
                            const slice = (d.amount / total) * 2 * Math.PI;
                            const obj = { s: start, e: start + slice, color: d.color, name: d.name, amount: d.amount };
                            start += slice;
                            return obj;
                        });

                        this.drawAll(-1);

                        el.addEventListener('mousemove', (e) => {
                            const rect = el.getBoundingClientRect();
                            const idx  = this.hitTest(e.clientX, e.clientY, rect);
                            if (idx >= 0) {
                                const s = this.slices[idx];
                                this.tip = {
                                    show: true,
                                    x: e.clientX - rect.left + 12,
                                    y: e.clientY - rect.top  - 40,
                                    label: s.name,
                                    amt: 'Rp ' + s.amount.toLocaleString('id'),
                                    color: s.color
                                };
                                this.drawAll(idx);
                            } else {
                                this.tip.show = false;
                                this.drawAll(-1);
                            }
                        });

                        el.addEventListener('mouseleave', () => {
                            this.tip.show = false;
                            this.drawAll(-1);
                        });
                    });
                }
             }">

            {{-- Donut Canvas --}}
            <div style="position:relative;width:150px;height:150px;flex-shrink:0;">
                <canvas id="donut-{{ md5($currentMonth) }}"
                        style="width:150px;height:150px;display:block;cursor:pointer;"></canvas>

                {{-- Tooltip --}}
                <template x-if="tip.show">
                    <div :style="'position:absolute;left:'+tip.x+'px;top:'+tip.y+'px;pointer-events:none;z-index:50;'">
                        <div style="background:#111827;color:white;border-radius:8px;padding:7px 11px;white-space:nowrap;box-shadow:0 4px 12px rgba(0,0,0,0.25);">
                            <div style="display:flex;align-items:center;gap:5px;margin-bottom:2px;">
                                <div :style="'width:8px;height:8px;border-radius:2px;background:'+tip.color"></div>
                                <span style="font-size:11px;font-weight:600;" x-text="tip.label"></span>
                            </div>
                            <span style="font-size:12px;font-weight:700;font-family:ui-monospace,monospace;" x-text="tip.amt"></span>
                        </div>
                    </div>
                </template>

                {{-- Center label --}}
                <div style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);text-align:center;pointer-events:none;">
                    <p style="font-size:9px;font-weight:700;color:var(--color-text-secondary);margin:0;text-transform:uppercase;letter-spacing:0.5px;">TOTAL</p>
                    <p style="font-size:13px;font-weight:800;color:var(--color-text-primary);margin:0;line-height:1.3;">
                        Rp {{ number_format($totalExpense/1000, 0, ',', '.') }}K
                    </p>
                </div>
            </div>

            {{-- Legend + Mini Bar --}}
            <div style="flex:1;display:flex;flex-direction:column;gap:8px;">
                @foreach($categoryData as $cat)
                <div>
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:3px;">
                        <div style="display:flex;align-items:center;gap:6px;">
                            <div style="width:10px;height:10px;border-radius:3px;background:{{ $cat['color'] }};flex-shrink:0;"></div>
                            <span style="font-size:12px;font-weight:500;color:var(--color-text-primary);">{{ $cat['name'] }}</span>
                        </div>
                        <div style="display:flex;align-items:center;gap:5px;">
                            <span style="font-size:11px;font-weight:600;color:var(--color-text-primary);font-family:ui-monospace,monospace;">
                                Rp {{ number_format($cat['amount'], 0, ',', '.') }}
                            </span>
                            <span style="font-size:10px;font-weight:600;color:var(--color-text-secondary);min-width:26px;text-align:right;">{{ $cat['pct'] }}%</span>
                        </div>
                    </div>
                    <div style="height:4px;background:var(--color-background-secondary,#f3f4f6);border-radius:99px;overflow:hidden;">
                        <div style="height:100%;width:{{ $cat['pct'] }}%;background:{{ $cat['color'] }};border-radius:99px;"></div>
                    </div>
                </div>
                @endforeach
            </div>

        </div>
        @endif
        </div>

        @if(!empty($categoryData))
        <div style="margin-top:14px;padding-top:12px;border-top:1px solid var(--color-border-tertiary);text-align:center;">
            <a href="{{ url('/parent/transactions?type=expense&month=' . urlencode($currentMonth)) }}"
               style="font-size:12px;font-weight:600;color:var(--color-primary-600);text-decoration:none;display:inline-flex;align-items:center;gap:4px;">
                Lihat semua kategori
                <x-heroicon-o-arrow-right style="width:14px;height:14px;"/>
            </a>
        </div>
        @endif

    </x-filament::section>
</x-filament-widgets::widget>
