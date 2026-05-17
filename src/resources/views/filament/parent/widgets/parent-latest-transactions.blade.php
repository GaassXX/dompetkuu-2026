<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">Transaksi Terbaru</x-slot>

        <div style="display:flex;flex-direction:column;gap:6px;">
            @forelse($transactions as $tx)
            <div style="display:flex;align-items:center;justify-content:space-between;padding:8px 10px;background:var(--color-background-secondary);border-radius:10px;">

                <div style="display:flex;align-items:center;gap:10px;">
                    <div style="width:32px;height:32px;border-radius:50%;flex-shrink:0;display:flex;align-items:center;justify-content:center;
                        background:{{ $tx['type'] === 'Pemasukan' ? '#EAF3DE' : '#FCEBEB' }}">
                        @if($tx['type'] === 'Pemasukan')
                            <x-heroicon-o-arrow-up style="width:14px;height:14px;color:#3B6D11;"/>
                        @else
                            <x-heroicon-o-arrow-down style="width:14px;height:14px;color:#A32D2D;"/>
                        @endif
                    </div>
                    <div>
                        <p style="font-size:13px;font-weight:500;color:var(--color-text-primary);margin:0;">
                            {{ $tx['category'] }}
                        </p>
                        <p style="font-size:11px;color:var(--color-text-secondary);margin:0;">
                            {{ $tx['date_fmt'] }}
                        </p>
                    </div>
                </div>

                <div style="text-align:right;">
                    <p style="font-size:13px;font-weight:500;margin:0;
                        color:{{ $tx['type'] === 'Pemasukan' ? '#3B6D11' : '#A32D2D' }}">
                        {{ $tx['type'] === 'Pemasukan' ? '+' : '-' }}Rp {{ number_format($tx['amount'], 0, ',', '.') }}
                    </p>
                    <span style="font-size:10px;padding:2px 8px;border-radius:99px;
                        background:{{ match($tx['status']) { 'approved' => '#EAF3DE', 'pending' => '#FAEEDA', 'rejected' => '#FCEBEB', default => '#F1EFE8' } }};
                        color:{{ match($tx['status']) { 'approved' => '#27500A', 'pending' => '#633806', 'rejected' => '#791F1F', default => '#5F5E5A' } }}">
                        {{ match($tx['status']) {
                            'approved' => 'Disetujui',
                            'pending'  => 'Pending',
                            'rejected' => 'Ditolak',
                            default    => $tx['status'],
                        } }}
                    </span>
                </div>

            </div>
            @empty
            <p style="font-size:13px;color:var(--color-text-secondary);text-align:center;padding:16px 0;">
                Belum ada transaksi
            </p>
            @endforelse
        </div>

    </x-filament::section>
</x-filament-widgets::widget>
