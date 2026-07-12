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
                tip: { show: false, x: 0, y: 0, label: '', amt: '', color: '' },
                activeIdx: -1,

                hitIndex(el, mx, my) {
                    const rect = el.getBoundingClientRect();
                    const cx = 75, cy = 75;
                    const x = mx - rect.left - cx;
                    const y = my - rect.top - cy;
                    const d = Math.sqrt(x*x + y*y);
                    if (d < 42 || d > 66) return -1;
                    let a = Math.atan2(y, x) * (180 / Math.PI);
                    if (a < 0) a += 360;
                    a = (a + 90) % 360;
                    const total = {{ $totalExpense }};
                    const slices = {{ Js::from($categoryData) }};
                    let cumPct = 0;
                    for (let i = 0; i < slices.length; i++) {
                        const pct = (slices[i].amount / total) * 100;
                        cumPct += pct;
                        if (a <= cumPct * 3.6) return i;
                    }
                    return slices.length - 1;
                },

                showTooltip(el, e) {
                    const idx = this.hitIndex(el, e.clientX, e.clientY);
                    if (idx < 0) {
                        this.activeIdx = -1;
                        this.tip.show = false;
                        return;
                    }
                    this.activeIdx = idx;
                    const s = {{ Js::from($categoryData) }}[idx];
                    this.tip = {
                        show: true,
                        x: e.offsetX + 12,
                        y: e.offsetY - 40,
                        label: s.name,
                        amt: 'Rp ' + Number(s.amount).toLocaleString('id-ID'),
                        color: s.color
                    };
                },

                hideTooltip() {
                    this.activeIdx = -1;
                    this.tip.show = false;
                }
             }">

            {{-- SVG Donut --}}
            <div style="position:relative;width:150px;height:150px;flex-shrink:0;">
                <svg width="150" height="150" viewBox="0 0 150 150"
                     @mousemove="showTooltip($el, $event)"
                     @mouseleave="hideTooltip()">
                    @php
                        $cx = 75; $cy = 75; $r = 54; $w = 24;
                        $circ = 2 * pi() * $r;
                        $off = 0;
                    @endphp
                    @foreach($categoryData as $i => $cat)
                        @php
                            $len = max(1, ($cat['amount'] / $totalExpense) * $circ);
                            $dashLen = $len;
                        @endphp
                        <circle cx="{{ $cx }}" cy="{{ $cy }}" r="{{ $r }}"
                                fill="none" stroke="{{ $cat['color'] }}" stroke-width="{{ $w }}"
                                stroke-dasharray="{{ $dashLen }} {{ $circ - $dashLen }}"
                                stroke-dashoffset="{{ -$off }}"
                                transform="rotate(-90 {{ $cx }} {{ $cy }})"
                                style="cursor:pointer;" />
                        @php $off += $len; @endphp
                    @endforeach
                    <circle cx="{{ $cx }}" cy="{{ $cy }}" r="{{ $r - $w / 2 }}" fill="white" />
                    <text x="{{ $cx }}" y="{{ $cy - 5 }}" text-anchor="middle" font-size="9" fill="#9ca3af" font-weight="700" letter-spacing="0.5">TOTAL</text>
                    <text x="{{ $cx }}" y="{{ $cy + 12 }}" text-anchor="middle" font-size="13" fill="#111827" font-weight="800" font-family="ui-monospace,monospace">Rp {{ number_format($totalExpense / 1000, 0, ',', '.') }}K</text>
                </svg>

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
                Detail
                <x-heroicon-o-arrow-right style="width:14px;height:14px;"/>
            </a>
        </div>
        @endif

    </x-filament::section>
</x-filament-widgets::widget>
