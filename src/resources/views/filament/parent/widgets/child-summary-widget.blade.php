<x-filament-widgets::widget>
    <x-filament::section>
        {{-- Slot Heading Utama --}}
        <x-slot name="heading">
            <div style="display: flex; justify-content: space-between; align-items: center; width: 100%;">
                <div style="display: flex; flex-direction: column;">
                    <span style="font-size: 16px; font-weight: 700; color: var(--color-text-primary);">Ringkasan Anggota Keluarga</span>
                    <span style="font-size: 12px; font-weight: 400; color: #8898aa; margin-top: 2px;">Saldo &amp; aktivitas bulan ini</span>
                </div>
                <a href="/parent/budgets" style="font-size: 12px; color: #22c55e; text-decoration: none; font-weight: 600;">
                    Lihat Semua →
                </a>
            </div>
        </x-slot>

        <div style="min-height:280px;display:flex;flex-direction:column;justify-content:flex-start;overflow-y:auto;">
        {{-- Urutan Blok Vertikal Ke Bawah --}}
        <div style="display: flex; flex-direction: column; gap: 20px; width: 100%; margin-top: 10px;">

            {{-- LIST DATA ANAK (MENURUN KE BAWAH SESUAI MOCKUP) --}}
            <div style="display: flex; flex-direction: column; gap: 12px; width: 100%;">
                @forelse($children as $index => $child)
                    @php
                        // Menentukan warna background avatar anak secara bergantian agar estetik
                        $bgColors = ['#2563eb', '#ea580c', '#16a34a', '#db2777'];
                        $currentBg = $bgColors[$index % count($bgColors)];
                    @endphp

                    <div style="display: flex; align-items: center; justify-content: space-between; background: var(--color-background-secondary); border: 1px solid var(--color-border-tertiary); border-radius: 14px; padding: 14px 18px; width: 100%;">
                        {{-- Sisi Kiri: Avatar & Info Akun --}}
                        <div style="display: flex; align-items: center; gap: 14px; min-width: 0; flex: 1;">
                            <div style="width: 42px; height: 42px; border-radius: 12px; background: {{ $currentBg }}; display: flex; align-items: center; justify-content: center; font-size: 14px; font-weight: 600; color: #ffffff; flex-shrink: 0;">
                                {{ $child['initial'] }}
                            </div>
                            <div style="min-width: 0;">
                                <p style="font-size: 14px; font-weight: 600; color: var(--color-text-primary); margin: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                    {{ $child['name'] }}
                                </p>
                                <div style="display: flex; align-items: center; gap: 6px; margin-top: 4px; flex-wrap: wrap;">
                                    <span style="font-size: 11px; color: #8898aa;">Anggota · Anak</span>
                                    <span style="font-size: 11px; background: #e8f5e9; color: #2e7d32; padding: 1px 8px; border-radius: 6px; font-weight: 500;">
                                        +Rp {{ number_format($child['income'], 0, ',', '.') }}
                                    </span>
                                    <span style="font-size: 11px; background: #ffebee; color: #c62828; padding: 1px 8px; border-radius: 6px; font-weight: 500;">
                                        -Rp {{ number_format($child['expense'], 0, ',', '.') }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        {{-- Sisi Kanan: Tampilan Saldo Berjalan Akun Anak --}}
                        <div style="text-align: right; flex-shrink: 0; margin-left: 12px;">
                            <p style="font-size: 15px; font-weight: 700; color: var(--color-text-primary); margin: 0;">
                                Rp {{ number_format($child['saldo'], 0, ',', '.') }}
                            </p>
                            <span style="font-size: 11px; color: #8898aa;">Saldo</span>
                        </div>
                    </div>
                @empty
                    <div style="text-align: center; padding: 20px; border: 1px dashed var(--color-border-tertiary); border-radius: 14px;">
                        <p style="font-size: 13px; color: var(--color-text-secondary); margin: 0;">
                            Belum ada akun anak yang ditautkan ke akun orang tua Anda.
                        </p>
                    </div>
                @endforelse
            </div>

            {{-- PEMBATAS GARIS HALUS --}}
            <div style="border-top: 1px dashed var(--color-border-tertiary); width: 100%; margin: 4px 0;"></div>

            {{-- LIST PROGRESS ANGGARAN --}}
            <div style="width: 100%;">
                <div style="margin-bottom: 14px;">
                    <span style="font-size: 13px; font-weight: 700; color: var(--color-text-primary); text-transform: uppercase; letter-spacing: 0.5px;">
                        Progress Anggaran Bulan Ini
                    </span>
                </div>

                <div style="display: flex; flex-direction: column; gap: 14px; width: 100%;">
                    @forelse($topBudgets as $budget)
                        @php
                            $percent = $budget['percent'] ?? 0;
                            $barColor = '#22c55e'; // Hijau aman
                            if ($percent >= 100) {
                                $barColor = '#ef4444'; // Merah over-limit
                            } elseif ($percent >= 80) {
                                $barColor = '#eab308'; // Kuning warning
                            }
                        @endphp

                        <div style="width: 100%;">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px; gap: 8px;">
                                <div style="display: flex; align-items: center; gap: 6px; min-width: 0;">
                                    <span style="font-size: 13px; font-weight: 500; color: var(--color-text-primary); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                        {{ $budget['category_name'] }}
                                    </span>
                                    <span style="font-size: 10px; padding: 1px 6px; background: var(--color-border-tertiary); color: var(--color-text-secondary); border-radius: 6px; white-space: nowrap;">
                                        {{ $budget['child_name'] }}
                                    </span>
                                </div>
                                <span style="font-size: 12px; color: var(--color-text-secondary); white-space: nowrap;">
                                    <strong style="color: var(--color-text-primary);">Rp {{ number_format($budget['used'], 0, ',', '.') }}</strong>
                                    / Rp {{ number_format($budget['limit'], 0, ',', '.') }}
                                </span>
                            </div>

                            <div style="width: 100%; height: 6px; background: var(--color-border-tertiary); border-radius: 99px; overflow: hidden;">
                                <div style="width: {{ min($percent, 100) }}%; height: 100%; background: {{ $barColor }}; border-radius: 99px; transition: width 0.4s ease;"></div>
                            </div>
                        </div>
                    @empty
                        <p style="font-size: 12px; color: var(--color-text-secondary); text-align: center; margin: 0; padding: 4px 0;">
                            Belum ada batas anggaran yang diatur untuk anak-anak bulan ini.
                        </p>
                    @endforelse
                </div>
            </div>

        </div>
        </div>

    </x-filament::section>
</x-filament-widgets::widget>
