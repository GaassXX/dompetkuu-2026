@php
    $stats        = $this->getStats();
    $transactions = $this->getTransactions();
    $counts       = $this->getCounts();

    $statusLabels = [
        'all'      => 'Semua',
        'pending'  => 'Pending',
        'approved' => 'Disetujui',
        'rejected' => 'Ditolak',
    ];
@endphp

<x-filament-panels::page>

    {{-- ===== INJECT STYLES ===== --}}
    <style>
        .aq-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1rem;
        }
        @media (max-width: 1024px) {
            .aq-grid { grid-template-columns: repeat(2, 1fr); }
        }
        @media (max-width: 640px) {
            .aq-grid { grid-template-columns: repeat(1, 1fr); }
        }

        .aq-card {
            background: #fff;
            border-radius: 0.75rem;
            border: 1px solid #f3f4f6;
            padding: 1.25rem;
            box-shadow: 0 1px 3px 0 rgb(0 0 0 / .05);
        }
        .dark .aq-card {
            background: rgb(31 41 55);
            border-color: rgb(55 65 81);
        }

        .aq-icon-wrap {
            width: 2.5rem;
            height: 2.5rem;
            border-radius: 0.625rem;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .aq-card-label {
            font-size: 0.7rem;
            font-weight: 500;
            color: #9ca3af;
            margin-top: 0.75rem;
            margin-bottom: 0.2rem;
            letter-spacing: 0.01em;
        }
        .dark .aq-card-label { color: #9ca3af; }

        .aq-card-value-lg {
            font-size: 2rem;
            font-weight: 700;
            color: #111827;
            line-height: 1.1;
        }
        .dark .aq-card-value-lg { color: #f9fafb; }

        .aq-card-value-md {
            font-size: 1.2rem;
            font-weight: 700;
            color: #111827;
            line-height: 1.2;
        }
        .dark .aq-card-value-md { color: #f9fafb; }

        /* TABLE CARD */
        .aq-table-card {
            background: #fff;
            border-radius: 0.75rem;
            border: 1px solid #f3f4f6;
            box-shadow: 0 1px 3px 0 rgb(0 0 0 / .05);
            overflow: hidden;
            margin-top: 1.5rem;
        }
        .dark .aq-table-card {
            background: rgb(31 41 55);
            border-color: rgb(55 65 81);
        }

        .aq-table-header {
            padding: 1rem 1.5rem;
            border-bottom: 1px solid #f3f4f6;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            flex-wrap: wrap;
        }
        .dark .aq-table-header { border-color: rgb(55 65 81); }

        .aq-results-badge {
            font-size: 0.7rem;
            font-weight: 600;
            background: #f3f4f6;
            color: #6b7280;
            padding: 0.15rem 0.6rem;
            border-radius: 9999px;
        }
        .dark .aq-results-badge { background: rgb(55 65 81); color: #d1d5db; }

        /* Custom select */
        .aq-select-wrap {
            position: relative;
            display: flex;
            align-items: center;
            gap: 0.4rem;
        }
        .aq-select-label {
            font-size: 0.7rem;
            font-weight: 500;
            color: #9ca3af;
            white-space: nowrap;
        }
        .aq-select {
            appearance: none;
            -webkit-appearance: none;
            font-size: 0.75rem;
            font-weight: 600;
            border-radius: 0.5rem;
            border: 1px solid #e5e7eb;
            background: #fff;
            color: #374151;
            padding: 0.4rem 2rem 0.4rem 0.75rem;
            cursor: pointer;
            box-shadow: 0 1px 2px 0 rgb(0 0 0 / .04);
            transition: border-color .15s;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%239ca3af' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M19 9l-7 7-7-7'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 0.6rem center;
        }
        .aq-select:focus { outline: none; border-color: #f59e0b; box-shadow: 0 0 0 2px rgb(245 158 11 / .15); }
        .dark .aq-select { background-color: rgb(55 65 81); border-color: rgb(75 85 99); color: #e5e7eb; }

        /* Tabs */
        .aq-tabs {
            display: flex;
            gap: 0;
            border-bottom: 1px solid #f3f4f6;
            padding: 0 1.5rem;
            background: #fff;
            overflow-x: auto;
        }
        .dark .aq-tabs { background: rgb(31 41 55); border-color: rgb(55 65 81); }

        .aq-tab {
            position: relative;
            padding: 0.875rem 0.75rem;
            font-size: 0.8125rem;
            font-weight: 500;
            white-space: nowrap;
            cursor: pointer;
            background: none;
            border: none;
            display: flex;
            align-items: center;
            gap: 0.375rem;
            color: #6b7280;
            transition: color .15s;
        }
        .aq-tab:hover { color: #374151; }
        .dark .aq-tab { color: #9ca3af; }
        .dark .aq-tab:hover { color: #e5e7eb; }

        .aq-tab.active { color: #d97706; }
        .dark .aq-tab.active { color: #fbbf24; }

        .aq-tab.active::after {
            content: '';
            position: absolute;
            bottom: 0; left: 0; right: 0;
            height: 2px;
            background: #f59e0b;
            border-radius: 2px 2px 0 0;
        }

        .aq-tab-badge {
            font-size: 0.65rem;
            font-weight: 700;
            padding: 0.1rem 0.45rem;
            border-radius: 9999px;
            background: #f3f4f6;
            color: #6b7280;
        }
        .aq-tab.active .aq-tab-badge {
            background: #fef3c7;
            color: #b45309;
        }
        .dark .aq-tab-badge { background: rgb(55 65 81); color: #9ca3af; }
        .dark .aq-tab.active .aq-tab-badge { background: rgb(120 53 15 / .4); color: #fcd34d; }

        /* Table */
        .aq-table { width: 100%; border-collapse: collapse; font-size: 0.8125rem; }
        .aq-th {
            padding: 0.75rem 1.5rem;
            font-size: 0.65rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: #9ca3af;
            background: #f9fafb;
            text-align: left;
            white-space: nowrap;
        }
        .dark .aq-th { background: rgb(17 24 39 / .5); color: #6b7280; }

        .aq-td {
            padding: 0.875rem 1.5rem;
            border-top: 1px solid #f9fafb;
            white-space: nowrap;
        }
        .dark .aq-td { border-color: rgb(55 65 81 / .5); }

        .aq-tr:hover .aq-td { background: #fafafa; }
        .dark .aq-tr:hover .aq-td { background: rgb(55 65 81 / .25); }

        /* Type badge */
        .aq-type-income { background: #ecfdf5; color: #16a34a; border-radius: 9999px; padding: 0.2rem 0.6rem; font-size: 0.7rem; font-weight: 600; }
        .aq-type-expense { background: #fff1f2; color: #ef4444; border-radius: 9999px; padding: 0.2rem 0.6rem; font-size: 0.7rem; font-weight: 600; }
        .dark .aq-type-income { background: rgb(6 78 59 / .3); color: #6ee7b7; }
        .dark .aq-type-expense { background: rgb(136 19 55 / .3); color: #fca5a5; }

        /* Avatar */
        .aq-avatar {
            width: 2rem; height: 2rem;
            border-radius: 9999px;
            background: linear-gradient(135deg, #e5e7eb, #d1d5db);
            display: flex; align-items: center; justify-content: center;
            font-size: 0.65rem; font-weight: 700; color: #6b7280;
            flex-shrink: 0; text-transform: uppercase;
        }
        .dark .aq-avatar { background: linear-gradient(135deg, rgb(55 65 81), rgb(75 85 99)); color: #d1d5db; }

        /* Status badge */
        .aq-status {
            display: inline-flex; padding: 0.15rem 0.5rem;
            font-size: 0.65rem; font-weight: 700;
            border-radius: 0.375rem; text-transform: uppercase; letter-spacing: 0.05em;
        }
        .aq-status-approved { background: #ecfdf5; color: #16a34a; }
        .aq-status-pending  { background: #fffbeb; color: #d97706; }
        .aq-status-rejected { background: #fff1f2; color: #ef4444; }
        .dark .aq-status-approved { background: rgb(6 78 59 / .3); color: #6ee7b7; }
        .dark .aq-status-pending  { background: rgb(120 53 15 / .3); color: #fcd34d; }
        .dark .aq-status-rejected { background: rgb(136 19 55 / .3); color: #fca5a5; }

        /* Action buttons */
        .aq-btn {
            display: inline-flex; align-items: center; gap: 0.35rem;
            padding: 0.35rem 0.75rem;
            font-size: 0.7rem; font-weight: 600;
            border-radius: 0.5rem;
            border: 1px solid #e5e7eb;
            background: #fff;
            color: #374151;
            cursor: pointer;
            box-shadow: 0 1px 2px rgb(0 0 0 / .04);
            transition: all .15s;
        }
        .aq-btn:hover-approve, .aq-btn-approve:hover { background: #ecfdf5; border-color: #a7f3d0; color: #065f46; }
        .aq-btn-reject:hover  { background: #fff1f2; border-color: #fecdd3; color: #9f1239; }
        .dark .aq-btn { background: rgb(31 41 55); border-color: rgb(75 85 99); color: #d1d5db; }

        /* Pagination */
        .aq-pagination {
            padding: 0.875rem 1.5rem;
            border-top: 1px solid #f3f4f6;
            display: flex; align-items: center;
            justify-content: space-between;
            flex-wrap: wrap; gap: 0.5rem;
            background: #fff;
        }
        .dark .aq-pagination { border-color: rgb(55 65 81); background: rgb(31 41 55); }

        .aq-page-info { font-size: 0.7rem; color: #9ca3af; }

        .aq-page-btn {
            width: 2rem; height: 2rem;
            display: inline-flex; align-items: center; justify-content: center;
            border-radius: 0.5rem;
            font-size: 0.75rem; font-weight: 600;
            border: 1px solid #e5e7eb;
            background: #fff; color: #374151;
            cursor: pointer;
            transition: all .15s;
            box-shadow: 0 1px 2px rgb(0 0 0 / .04);
        }
        .aq-page-btn:hover { background: #f9fafb; }
        .aq-page-btn.active { background: #f59e0b; border-color: #f59e0b; color: #fff; box-shadow: 0 2px 8px rgb(245 158 11 / .3); }
        .aq-page-btn.disabled { background: #f9fafb; color: #d1d5db; cursor: default; pointer-events: none; }
        .dark .aq-page-btn { background: rgb(31 41 55); border-color: rgb(75 85 99); color: #d1d5db; }
        .dark .aq-page-btn:hover { background: rgb(55 65 81); }
        .dark .aq-page-btn.disabled { background: rgb(55 65 81); color: rgb(75 85 99); }

        /* Empty state */
        .aq-empty {
            padding: 4rem 1.5rem;
            text-align: center;
        }
        .aq-empty-icon {
            width: 3rem; height: 3rem;
            background: #f3f4f6;
            border-radius: 0.75rem;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 0.75rem;
        }
        .dark .aq-empty-icon { background: rgb(55 65 81); }
    </style>

    {{-- ===== STATS GRID ===== --}}
    <div class="aq-grid">

        {{-- Total Antrian --}}
        <div class="aq-card">
            <div style="display:flex;align-items:flex-start;justify-content:space-between;">
                <div class="aq-icon-wrap" style="background:#fffbeb;">
                    <svg width="20" height="20" fill="none" stroke="#d97706" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15"/>
                    </svg>
                </div>
                @if($stats['total'] > 0)
                    <span style="font-size:.65rem;font-weight:700;background:#fef3c7;color:#b45309;padding:.15rem .5rem;border-radius:9999px;text-transform:uppercase;letter-spacing:.05em;">Baru</span>
                @endif
            </div>
            <p class="aq-card-label">Total Antrian</p>
            <p class="aq-card-value-lg">{{ $stats['total'] }}</p>
        </div>

        {{-- Pemasukan Tertunda --}}
        <div class="aq-card">
            <div class="aq-icon-wrap" style="background:#ecfdf5;">
                <svg width="20" height="20" fill="none" stroke="#059669" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12"/>
                </svg>
            </div>
            <p class="aq-card-label">Pemasukan Tertunda</p>
            <p class="aq-card-value-md">Rp {{ number_format($stats['pendingIncome'], 0, ',', '.') }}</p>
        </div>

        {{-- Pengeluaran Tertunda --}}
        <div class="aq-card">
            <div class="aq-icon-wrap" style="background:#fff1f2;">
                <svg width="20" height="20" fill="none" stroke="#e11d48" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z"/>
                </svg>
            </div>
            <p class="aq-card-label">Pengeluaran Tertunda</p>
            <p class="aq-card-value-md">Rp {{ number_format($stats['pendingExpense'], 0, ',', '.') }}</p>
        </div>

        {{-- Permintaan Aktif --}}
        <div class="aq-card">
            <div class="aq-icon-wrap" style="background:#eff6ff;">
                <svg width="20" height="20" fill="none" stroke="#2563eb" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z"/>
                </svg>
            </div>
            <p class="aq-card-label">Permintaan Aktif</p>
            <p class="aq-card-value-lg">{{ $stats['activeChildren'] }} <span style="font-size:1rem;font-weight:500;color:#9ca3af;">Orang</span></p>
        </div>

    </div>

    {{-- ===== TABLE CARD ===== --}}
    <div class="aq-table-card">

        {{-- Header --}}
        <div class="aq-table-header">
            <div style="display:flex;align-items:center;gap:0.625rem;">
                <h3 style="font-size:.9375rem;font-weight:600;color:#111827;" class="dark:text-white">Daftar Permintaan</h3>
                <span class="aq-results-badge">{{ $transactions->total() }} RESULTS</span>
            </div>
            <div class="aq-select-wrap">
                <span class="aq-select-label">Tipe:</span>
                <select class="aq-select" wire:change="setType($event.target.value)">
                    <option value="all"         {{ $filterType === 'all'         ? 'selected' : '' }}>Semua Tipe</option>
                    <option value="Pemasukan"   {{ $filterType === 'Pemasukan'   ? 'selected' : '' }}>Pemasukan</option>
                    <option value="Pengeluaran" {{ $filterType === 'Pengeluaran' ? 'selected' : '' }}>Pengeluaran</option>
                </select>
            </div>
        </div>

        {{-- Status Tabs --}}
        <div class="aq-tabs">
            @foreach($statusLabels as $val => $label)
                <button
                    class="aq-tab {{ $filterStatus === $val ? 'active' : '' }}"
                    wire:click="setStatus('{{ $val }}')"
                >
                    {{ $label }}
                    <span class="aq-tab-badge">{{ $counts[$val] ?? 0 }}</span>
                </button>
            @endforeach
        </div>

        {{-- Table --}}
        <div style="overflow-x:auto;">
            @if($transactions->count() > 0)
                <table class="aq-table">
                    <thead>
                        <tr>
                            <th class="aq-th">Tipe</th>
                            <th class="aq-th">Anggota Keluarga</th>
                            <th class="aq-th">Kategori</th>
                            <th class="aq-th" style="text-align:right;">Jumlah</th>
                            <th class="aq-th">Tanggal</th>
                            <th class="aq-th">Status</th>
                            <th class="aq-th" style="text-align:right;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($transactions as $trx)
                            @php
                                $isIncome = $trx->type === 'Pemasukan';
                                $statusClass = match($trx->status) {
                                    'approved' => 'aq-status-approved',
                                    'pending'  => 'aq-status-pending',
                                    'rejected' => 'aq-status-rejected',
                                    default    => '',
                                };
                                $statusLabel = match($trx->status) {
                                    'approved' => 'Disetujui',
                                    'pending'  => 'Pending',
                                    'rejected' => 'Ditolak',
                                    default    => $trx->status,
                                };
                            @endphp
                            <tr class="aq-tr">
                                <td class="aq-td">
                                    <span class="{{ $isIncome ? 'aq-type-income' : 'aq-type-expense' }}">
                                        {{ $trx->type }}
                                    </span>
                                </td>
                                <td class="aq-td">
                                    <div style="display:flex;align-items:center;gap:.75rem;">
                                        <div class="aq-avatar">{{ strtoupper(substr($trx->user_name, 0, 2)) }}</div>
                                        <div>
                                            <p style="font-weight:500;color:#111827;font-size:.8125rem;" class="dark:text-white">{{ $trx->user_name }}</p>
                                            <p style="font-size:.7rem;color:#9ca3af;max-width:160px;overflow:hidden;text-overflow:ellipsis;">{{ $trx->description ?? '-' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="aq-td" style="color:#4b5563;">{{ $trx->category_name }}</td>
                                <td class="aq-td" style="text-align:right;font-weight:600;color:#111827;" class="dark:text-white">
                                    Rp {{ number_format($trx->amount, 0, ',', '.') }}
                                </td>
                                <td class="aq-td" style="font-size:.75rem;color:#9ca3af;">
                                    {{ \Carbon\Carbon::parse($trx->date)->translatedFormat('d M Y') }}
                                </td>
                                <td class="aq-td">
                                    <span class="aq-status {{ $statusClass }}">{{ $statusLabel }}</span>
                                </td>
                                <td class="aq-td" style="text-align:right;">
                                    @if($trx->status === 'pending')
                                        <div style="display:flex;gap:.5rem;justify-content:flex-end;">
                                            <button
                                                wire:click="approve('{{ $trx->id }}')"
                                                wire:confirm="Setujui transaksi ini?"
                                                class="aq-btn aq-btn-approve"
                                                onmouseover="this.style.background='#ecfdf5';this.style.borderColor='#a7f3d0';this.style.color='#065f46';"
                                                onmouseout="this.style.background='';this.style.borderColor='';this.style.color='';"
                                            >
                                                <svg width="14" height="14" fill="none" stroke="#059669" stroke-width="2.5" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                                                </svg>
                                                Setujui
                                            </button>
                                            <button
                                                wire:click="reject('{{ $trx->id }}')"
                                                wire:confirm="Tolak transaksi ini?"
                                                class="aq-btn aq-btn-reject"
                                                onmouseover="this.style.background='#fff1f2';this.style.borderColor='#fecdd3';this.style.color='#9f1239';"
                                                onmouseout="this.style.background='';this.style.borderColor='';this.style.color='';"
                                            >
                                                <svg width="14" height="14" fill="none" stroke="#e11d48" stroke-width="2.5" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 9l6 6m0-6-6 6m13-3a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                                                </svg>
                                                Tolak
                                            </button>
                                        </div>
                                    @else
                                        <span style="color:#d1d5db;">—</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="aq-empty">
                    <div class="aq-empty-icon">
                        <svg width="24" height="24" fill="none" stroke="#9ca3af" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                        </svg>
                    </div>
                    <p style="font-size:.875rem;font-weight:500;color:#9ca3af;">Tidak ada transaksi {{ $statusLabels[$filterStatus] ?? '' }}</p>
                </div>
            @endif
        </div>

        {{-- Pagination --}}
        @if($transactions->count() > 0)
            <div class="aq-pagination">
                <p class="aq-page-info">
                    Menampilkan {{ $transactions->firstItem() }}–{{ $transactions->lastItem() }} dari {{ $transactions->total() }} hasil
                </p>
                <div style="display:flex;gap:.25rem;align-items:center;">
                    @if($transactions->onFirstPage())
                        <span class="aq-page-btn disabled">‹</span>
                    @else
                        <button wire:click="previousPage" class="aq-page-btn">‹</button>
                    @endif

                    @foreach(range(1, $transactions->lastPage()) as $page)
                        <button
                            wire:click="gotoPage({{ $page }})"
                            class="aq-page-btn {{ $currentPage === $page ? 'active' : '' }}"
                        >{{ $page }}</button>
                    @endforeach

                    @if($transactions->hasMorePages())
                        <button wire:click="nextPage" class="aq-page-btn">›</button>
                    @else
                        <span class="aq-page-btn disabled">›</span>
                    @endif
                </div>
            </div>
        @endif

    </div>

</x-filament-panels::page>
