<x-filament-widgets::widget>
    <div style="min-height:280px;display:flex;flex-direction:column;justify-content:flex-start;overflow-y:auto;">
    <div style="display:flex;flex-direction:column;gap:8px;">
        @foreach($alerts as $alert)
            @php
                $isExact = !$alert['is_over'] && $alert['percentage'] >= 100;
            @endphp
        <div style="
            background:{{ $alert['is_over'] ? '#FCEBEB' : ($isExact ? '#FFF1E0' : '#FAEEDA') }};
            border:0.5px solid {{ $alert['is_over'] ? '#F7C1C1' : ($isExact ? '#FABD75' : '#FAC775') }};
            border-radius:12px;padding:12px 14px;
            display:flex;align-items:center;gap:12px;position:relative;">

            <button
                wire:click="dismissAlert({{ $alert['budget_id'] }}, '{{ $alert['period_raw'] }}', {{ $alert['child_id'] }})"
                title="Tandai sudah dibaca"
                style="position:absolute;top:6px;right:8px;background:none;border:none;cursor:pointer;padding:2px;"
            >
                <svg style="width:14px;height:14px;{{ $alert['is_over'] ? 'color:#E24B4A' : ($isExact ? 'color:#E07C00' : 'color:#EF9F27') }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>

            <div style="width:34px;height:34px;border-radius:50%;flex-shrink:0;
                background:{{ $alert['is_over'] ? '#F7C1C1' : ($isExact ? '#FABD75' : '#FAC775') }};
                display:flex;align-items:center;justify-content:center;">
                <x-heroicon-o-exclamation-triangle style="width:16px;height:16px;color:{{ $alert['is_over'] ? '#A32D2D' : ($isExact ? '#9A5A00' : '#633806') }};"/>
            </div>

            <div style="flex:1;min-width:0;">
                <p style="font-size:13px;font-weight:500;margin:0;color:{{ $alert['is_over'] ? '#501313' : ($isExact ? '#4A3000' : '#412402') }}">
                    @if($alert['is_over'])
                        Budget melebihi limit!
                    @elseif($isExact)
                        Budget habis!
                    @else
                        Budget hampir habis!
                    @endif
                    — {{ $alert['child_name'] }} / {{ $alert['category'] }}
                </p>
                <p style="font-size:11px;margin:2px 0 4px;color:{{ $alert['is_over'] ? '#791F1F' : ($isExact ? '#7A4F00' : '#633806') }}">
                    Rp {{ number_format($alert['spent'], 0, ',', '.') }}
                    dari Rp {{ number_format($alert['limit'], 0, ',', '.') }}
                </p>
                <div style="height:5px;border-radius:99px;background:rgba(0,0,0,0.1);overflow:hidden;">
                    <div style="height:100%;border-radius:99px;width:{{ min($alert['percentage'], 100) }}%;
                        background:{{ $alert['is_over'] ? '#E24B4A' : ($isExact ? '#E07C00' : '#EF9F27') }};"></div>
                </div>
            </div>

            <span style="font-size:12px;font-weight:500;white-space:nowrap;
                color:{{ $alert['is_over'] ? '#A32D2D' : ($isExact ? '#9A5A00' : '#854F0B') }}">
                {{ $alert['percentage'] == (int)$alert['percentage'] ? number_format($alert['percentage']) : number_format($alert['percentage'], 1) }}%
            </span>
        </div>
        @endforeach
    </div>
    </div>
</x-filament-widgets::widget>
