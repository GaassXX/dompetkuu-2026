<x-filament-widgets::widget>
    <x-filament::section>

        <x-slot name="heading">
            <div style="display:flex;align-items:center;justify-content:space-between;width:100%;">
                {{-- Container untuk Judul dan Sub-judul --}}
                <div style="display:flex;flex-direction:column;">
                    <span style="font-size:14px;font-weight:600;">Transaksi Terbaru</span>
                    <span style="font-size:11px;color:var(--color-text-secondary);font-weight:400;margin-top:2px;">
                        Aktivitas hari ini
                    </span>
                </div>

                <a href="{{ route('filament.child.pages.transaction-view') }}"
                   style="font-size:12px;color:var(--color-primary-500);text-decoration:none;font-weight:400;">
                    Lihat semua →
                </a>
            </div>
        </x-slot>

        <div style="min-height:200px;display:flex;flex-direction:column;justify-content:flex-start;overflow-y:auto;">
        <div style="display:flex;flex-direction:column;gap:8px;">
            @forelse($transactions as $tx)
            <div style="display:flex;align-items:center;justify-content:space-between;
                        padding:10px 12px;
                        background:var(--color-background-secondary);
                        border-radius:12px;
                        border:1px solid var(--color-border-tertiary);">

                <div style="display:flex;align-items:center;gap:12px;">
                    <div style="width:36px;height:36px;border-radius:50%;flex-shrink:0;
                                display:flex;align-items:center;justify-content:center;
                                background:{{ $tx['type'] === 'Pemasukan' ? '#DCFCE7' : '#FEE2E2' }}">
                        @if($tx['type'] === 'Pemasukan')
                            <x-heroicon-o-arrow-trending-up style="width:16px;height:16px;color:#16A34A;"/>
                        @else
                            <x-heroicon-o-arrow-trending-down style="width:16px;height:16px;color:#DC2626;"/>
                        @endif
                    </div>
                    <div>
                        <p style="font-size:13px;font-weight:600;color:var(--color-text-primary);margin:0;line-height:1.4;">
                            {{ $tx['category'] }}
                        </p>
                        <p style="font-size:11px;color:var(--color-text-secondary);margin:0;line-height:1.4;">
                            {{ $tx['type'] }} · {{ \Carbon\Carbon::parse($tx['date'])->translatedFormat('d F Y') }}
                        </p>
                    </div>
                </div>

                <div style="display:flex;flex-direction:column;align-items:flex-end;gap:4px;">
                    <p style="font-size:13px;font-weight:700;margin:0;
                               color:{{ $tx['type'] === 'Pemasukan' ? '#16A34A' : '#DC2626' }}">
                        {{ $tx['type'] === 'Pemasukan' ? '+' : '-' }}Rp {{ number_format($tx['amount'], 0, ',', '.') }}
                    </p>
                    <span style="font-size:10px;padding:2px 10px;border-radius:99px;font-weight:500;
                        background:{{ match($tx['status']) {
                            'approved' => '#DCFCE7',
                            'pending'  => '#FEF9C3',
                            'rejected' => '#FEE2E2',
                            default    => '#F3F4F6'
                        } }};
                        color:{{ match($tx['status']) {
                            'approved' => '#15803D',
                            'pending'  => '#A16207',
                            'rejected' => '#B91C1C',
                            default    => '#6B7280'
                        } }}">
                        {{ match($tx['status']) {
                            'approved' => '✓ Disetujui',
                            'pending'  => '⏳ Pending',
                            'rejected' => '✕ Ditolak',
                            default    => $tx['status'],
                        } }}
                    </span>
                </div>

            </div>
            @empty
            <div style="display:flex;flex-direction:column;align-items:center;padding:32px 0;gap:8px;">
                <x-heroicon-o-inbox style="width:40px;height:40px;color:var(--color-text-secondary);opacity:0.4;"/>
                <p style="font-size:13px;color:var(--color-text-secondary);margin:0;">Belum ada transaksi</p>
                <a href="{{ route('filament.child.resources.expenses.create') }}"
                   style="font-size:12px;color:var(--color-primary-500);text-decoration:none;font-weight:500;">
                    + Tambah Transaksi
                </a>
            </div>
            @endforelse
        </div>
        </div>

    </x-filament::section>
</x-filament-widgets::widget>
