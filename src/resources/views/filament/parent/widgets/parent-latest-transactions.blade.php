<x-filament-widgets::widget>
    <x-filament::section>

        {{-- Custom Header: Warna tombol "Lihat semua" diubah menjadi hijau konsisten --}}
    <div style="display: flex; align-items: center; justify-content: space-between; width: 100%; border-bottom: 1px solid var(--color-border-tertiary); padding-bottom: 12px; margin-bottom: 12px; gap: 8px;">

    {{-- Container untuk Judul dan Sub-judul --}}
    <div style="display: flex; flex-direction: column; overflow: hidden;">
        <span style="font-size: 14px; font-weight: 600; color: var(--color-text-primary);">
            Transaksi Terbaru
        </span>

        <span style="font-size: 11px; color: var(--color-text-secondary); margin-top: 2px;">
            Aktivitas hari ini
        </span>
    </div>

        <a href="{{ route('filament.parent.pages.transaction-view') }}"
            style="font-size: 12px; color: #22c55e; text-decoration: none; font-weight: 600; white-space: nowrap; flex-shrink: 0;">
            Lihat semua →
        </a>
    </div>

        <div style="min-height:280px;display:flex;flex-direction:column;justify-content:flex-start;overflow-y:auto;">
        {{-- Container List Data --}}
        <div style="display: flex; flex-direction: column; gap: 8px; width: 100%;">
            @forelse($transactions as $tx)
            {{-- Row Item: Menggunakan min-width: 0 agar flex child bisa mengecil di area sempit --}}
            <div style="display: flex; align-items: center; justify-content: space-between; padding: 10px 12px; background: var(--color-background-secondary); border-radius: 12px; border: 1px solid var(--color-border-tertiary); gap: 12px; min-width: 0; width: 100%;">

                {{-- Sisi Kiri: Icon Bulat + Informasi Teks (flex-grow dinamis) --}}
                <div style="display: flex; align-items: center; gap: 10px; min-width: 0; flex: 1;">
                    <div style="width: 36px; height: 36px; border-radius: 50%; flex-shrink: 0; display: flex; align-items: center; justify-content: center; background: {{ $tx['type'] === 'Pemasukan' ? '#DCFCE7' : '#FEE2E2' }};">
                        @if($tx['type'] === 'Pemasukan')
                            <x-heroicon-o-arrow-trending-up style="width: 16px; height: 16px; color: #16A34A; flex-shrink: 0;"/>
                        @else
                            <x-heroicon-o-arrow-trending-down style="width: 16px; height: 16px; color: #DC2626; flex-shrink: 0;"/>
                        @endif
                    </div>

                    {{-- Judul Kategori & Jenis: Diproteksi agar tidak mendorong layout --}}
                    <div style="min-width: 0; flex: 1;">
                        <p style="font-size: 13px; font-weight: 600; color: var(--color-text-primary); margin: 0; line-height: 1.4; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                            {{ $tx['category'] }}
                        </p>
                        <p style="font-size: 11px; color: var(--color-text-secondary); margin: 0; line-height: 1.4; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                            {{ $tx['type'] }} · {{ $tx['date_fmt'] }}
                        </p>
                    </div>
                </div>

                {{-- Sisi Kanan: Nominal Duit + Badge Status (flex-shrink: 0 dikunci agar tidak gepeng) --}}
                <div style="display: flex; flex-direction: column; align-items: flex-end; gap: 4px; flex-shrink: 0; text-align: right;">
                    <p style="font-size: 13px; font-weight: 700; margin: 0; color: {{ $tx['type'] === 'Pemasukan' ? '#16A34A' : '#DC2626' }}; white-space: nowrap;">
                        {{ $tx['type'] === 'Pemasukan' ? '+' : '-' }}Rp {{ number_format($tx['amount'], 0, ',', '.') }}
                    </p>
                    <span style="font-size: 10px; padding: 2px 8px; border-radius: 99px; font-weight: 500; white-space: nowrap; background: {{ match($tx['status']) { 'approved' => '#DCFCE7', 'pending' => '#FEF9C3', 'rejected' => '#FEE2E2', default => '#F3F4F6' } }}; color: {{ match($tx['status']) { 'approved' => '#15803D', 'pending' => '#A16207', 'rejected' => '#B91C1C', default => '#6B7280' } }};">
                        {{ match($tx['status']) { 'approved' => '✓ Disetujui', 'pending' => '⏳ Pending', 'rejected' => '✕ Ditolak', default => $tx['status'] } }}
                    </span>
                </div>

            </div>
            @empty
            <div style="display: flex; flex-direction: column; align-items: center; padding: 32px 0; gap: 8px; width: 100%;">
                <x-heroicon-o-inbox style="width: 40px; height: 40px; color: var(--color-text-secondary); opacity: 0.4;"/>
                <p style="font-size: 13px; color: var(--color-text-secondary); margin: 0;">Belum ada transaksi</p>
                <a href="{{ route('filament.parent.resources.expenses.create') }}"
                   style="font-size: 12px; color: #22c55e; text-decoration: none; font-weight: 500;">
                    + Tambah Transaksi
                </a>
            </div>
            @endforelse
        </div>
        </div>

    </x-filament::section>
</x-filament-widgets::widget>
