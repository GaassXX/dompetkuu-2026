<x-filament-widgets::widget>
    <div style="background:#FAEEDA;border:0.5px solid #FAC775;border-radius:12px;padding:14px 16px;">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;">
            <div style="display:flex;align-items:center;gap:8px;">
                <div style="width:32px;height:32px;border-radius:50%;background:#FAC775;display:flex;align-items:center;justify-content:center;">
                    <x-heroicon-o-clock style="width:16px;height:16px;color:#633806;"/>
                </div>
                <div>
                    <p style="font-size:13px;font-weight:500;color:#412402;margin:0;">
                        Menunggu Persetujuan
                    </p>
                    <p style="font-size:11px;color:#854F0B;margin:0;">
                        {{ count($pendingItems) }} transaksi perlu ditinjau
                    </p>
                </div>
            </div>
            <a href="{{ route('filament.parent.pages.approval-queue') }}"
               style="font-size:12px;font-weight:500;color:#854F0B;text-decoration:none;background:#FAC775;padding:5px 12px;border-radius:99px;">
                Lihat semua →
            </a>
        </div>

        <div style="display:flex;flex-direction:column;gap:6px;">
            @foreach($pendingItems as $item)
            <div style="background:rgba(255,255,255,0.6);border-radius:8px;padding:8px 12px;display:flex;align-items:center;justify-content:space-between;">
                <div style="display:flex;align-items:center;gap:8px;">
                    <div style="width:28px;height:28px;border-radius:50%;display:flex;align-items:center;justify-content:center;
                        background:{{ $item['type'] === 'Pemasukan' ? '#EAF3DE' : '#FCEBEB' }}">
                        @if($item['type'] === 'Pemasukan')
                            <x-heroicon-o-arrow-up style="width:13px;height:13px;color:#3B6D11;"/>
                        @else
                            <x-heroicon-o-arrow-down style="width:13px;height:13px;color:#A32D2D;"/>
                        @endif
                    </div>
                    <div>
                        <p style="font-size:12px;font-weight:500;color:#412402;margin:0;">
                            {{ $item['name'] }} — {{ $item['category'] }}
                        </p>
                        <p style="font-size:10px;color:#854F0B;margin:0;">{{ $item['date'] }}</p>
                    </div>
                </div>
                <p style="font-size:12px;font-weight:500;margin:0;color:{{ $item['type'] === 'Pemasukan' ? '#3B6D11' : '#A32D2D' }}">
                    {{ $item['type'] === 'Pemasukan' ? '+' : '-' }}Rp {{ number_format($item['amount'], 0, ',', '.') }}
                </p>
            </div>
            @endforeach
        </div>
    </div>
</x-filament-widgets::widget>
