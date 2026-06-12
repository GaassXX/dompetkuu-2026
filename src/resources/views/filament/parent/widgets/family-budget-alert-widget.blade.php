<x-filament-widgets::widget>
    <div style="min-height:280px;display:flex;flex-direction:column;justify-content:flex-start;overflow-y:auto;">
    <div style="display:flex;flex-direction:column;gap:8px;">
        @foreach($alerts as $alert)
        <div style="
            background:{{ $alert['is_over'] ? '#FCEBEB' : '#FAEEDA' }};
            border:0.5px solid {{ $alert['is_over'] ? '#F7C1C1' : '#FAC775' }};
            border-radius:12px;padding:12px 14px;
            display:flex;align-items:center;gap:12px;">

            <div style="width:34px;height:34px;border-radius:50%;flex-shrink:0;
                background:{{ $alert['is_over'] ? '#F7C1C1' : '#FAC775' }};
                display:flex;align-items:center;justify-content:center;">
                @if($alert['is_over'])
                    <x-heroicon-o-exclamation-triangle style="width:16px;height:16px;color:#A32D2D;"/>
                @else
                    <x-heroicon-o-bell-alert style="width:16px;height:16px;color:#633806;"/>
                @endif
            </div>

            <div style="flex:1;min-width:0;">
                <p style="font-size:13px;font-weight:500;margin:0;color:{{ $alert['is_over'] ? '#501313' : '#412402' }}">
                    {{ $alert['is_over'] ? 'Budget melebihi limit!' : 'Budget hampir habis!' }}
                    — {{ $alert['child_name'] }} / {{ $alert['category'] }}
                </p>
                <p style="font-size:11px;margin:2px 0 4px;color:{{ $alert['is_over'] ? '#791F1F' : '#633806' }}">
                    Rp {{ number_format($alert['spent'], 0, ',', '.') }}
                    dari Rp {{ number_format($alert['limit'], 0, ',', '.') }}
                </p>
                <div style="height:5px;border-radius:99px;background:rgba(0,0,0,0.1);overflow:hidden;">
                    <div style="height:100%;border-radius:99px;width:{{ min($alert['percentage'], 100) }}%;
                        background:{{ $alert['is_over'] ? '#E24B4A' : '#EF9F27' }};"></div>
                </div>
            </div>

            <span style="font-size:12px;font-weight:500;white-space:nowrap;
                color:{{ $alert['is_over'] ? '#A32D2D' : '#854F0B' }}">
                {{ number_format($alert['percentage'], 1) }}%
            </span>
        </div>
        @endforeach
    </div>
    </div>
</x-filament-widgets::widget>
