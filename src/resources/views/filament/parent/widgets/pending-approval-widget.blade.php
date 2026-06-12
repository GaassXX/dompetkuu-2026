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
                        {{ count($pendingItems) }} item perlu ditinjau
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
            <div style="background:rgba(255,255,255,0.6);border-radius:8px;padding:10px 12px;display:flex;align-items:center;justify-content:space-between;gap:8px;flex-wrap:wrap;">
                <div style="display:flex;align-items:center;gap:8px;">

                    {{-- Icon --}}
                    <div style="width:28px;height:28px;border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;
                        background:{{ str_contains($item['type'], 'Hapus') ? '#fee2e2' : ($item['type'] === 'Pemasukan' ? '#EAF3DE' : '#FCEBEB') }}">
                        @if(str_contains($item['type'], 'Hapus'))
                            <x-heroicon-o-trash style="width:13px;height:13px;color:#ef4444;"/>
                        @elseif($item['type'] === 'Pemasukan')
                            <x-heroicon-o-arrow-up style="width:13px;height:13px;color:#3B6D11;"/>
                        @else
                            <x-heroicon-o-arrow-down style="width:13px;height:13px;color:#A32D2D;"/>
                        @endif
                    </div>

                    <div>
                        <div style="display:flex;align-items:center;gap:6px;">
                            <p style="font-size:12px;font-weight:500;color:#412402;margin:0;">
                                {{ $item['name'] }} — {{ $item['category'] }}
                            </p>
                            @if(str_contains($item['type'], 'Hapus'))
                            <span style="font-size:10px;font-weight:700;padding:1px 6px;background:#fef3c7;color:#d97706;border-radius:999px;">
                                {{ $item['type'] }}
                            </span>
                            @endif
                        </div>
                        <p style="font-size:10px;color:#854F0B;margin:0;">
                            {{ $item['date'] }} ·
                            <span style="font-weight:600;color:{{ str_contains($item['type'], 'Pemasukan') ? '#3B6D11' : '#A32D2D' }}">
                                Rp {{ number_format($item['amount'], 0, ',', '.') }}
                            </span>
                        </p>
                    </div>
                </div>

                {{-- Tombol approve/reject hanya untuk delete request --}}
                @if($item['request'] ?? false)
                <div style="display:flex;gap:6px;">
                    <button
                        wire:click="approveRequest({{ $item['id'] }})"
                        wire:confirm="Setujui permintaan hapus ini?"
                        style="padding:5px 10px;background:#dcfce7;color:#16a34a;border:1px solid #86efac;border-radius:6px;font-size:11px;font-weight:600;cursor:pointer;">
                        ✓ Setujui
                    </button>
                    <button
                        wire:click="rejectRequest({{ $item['id'] }})"
                        wire:confirm="Tolak permintaan ini?"
                        style="padding:5px 10px;background:#fee2e2;color:#ef4444;border:1px solid #fca5a5;border-radius:6px;font-size:11px;font-weight:600;cursor:pointer;">
                        ✕ Tolak
                    </button>
                </div>
                @else
                <p style="font-size:12px;font-weight:500;margin:0;color:{{ $item['type'] === 'Pemasukan' ? '#3B6D11' : '#A32D2D' }}">
                    {{ $item['type'] === 'Pemasukan' ? '+' : '-' }}Rp {{ number_format($item['amount'], 0, ',', '.') }}
                </p>
                @endif
            </div>
            @endforeach
        </div>
    </div>
</x-filament-widgets::widget>
