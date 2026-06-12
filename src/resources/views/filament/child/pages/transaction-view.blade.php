@php
    $transactions = $this->getTransactions();
@endphp

<x-filament-panels::page>
    <div class="space-y-6">

        {{-- TABLE --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">

            {{-- Filter Bar --}}
            <div style="display:flex;align-items:center;gap:8px;margin-bottom:20px;flex-wrap:wrap;">

                {{-- Search --}}
                <div style="position:relative;flex:1;min-width:200px;">
                    <svg style="position:absolute;left:10px;top:50%;transform:translateY(-50%);width:14px;height:14px;color:#9ca3af;" fill="none" stroke="#9ca3af" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/>
                    </svg>
                    <input wire:model.live.debounce.300ms="search"
                        type="text" placeholder="Cari deskripsi, kategori, nama..."
                        style="width:100%;padding:7px 12px 7px 32px;border:1px solid #e5e7eb;border-radius:8px;font-size:12px;color:#374151;outline:none;">
                </div>

                {{-- Date From --}}
                <input wire:model.live="dateFrom" type="date"
                    style="padding:7px 10px;border:1px solid #e5e7eb;border-radius:8px;font-size:12px;color:#374151;cursor:pointer;">

                {{-- Date To --}}
                <input wire:model.live="dateTo" type="date"
                    style="padding:7px 10px;border:1px solid #e5e7eb;border-radius:8px;font-size:12px;color:#374151;cursor:pointer;">

                {{-- Jenis Transaksi --}}
                <select wire:model.live="filterType"
                    style="padding:7px 12px;border:1px solid #e5e7eb;border-radius:8px;font-size:12px;color:#374151;cursor:pointer;background:#fff;">
                    <option value="all">Semua Jenis</option>
                    <option value="Pemasukan">Pemasukan</option>
                    <option value="Pengeluaran">Pengeluaran</option>
                </select>

                {{-- Status --}}
                <select wire:model.live="filterStatus"
                    style="padding:7px 12px;border:1px solid #e5e7eb;border-radius:8px;font-size:12px;color:#374151;cursor:pointer;background:#fff;">
                    <option value="all">Semua Status</option>
                    <option value="approved">Disetujui</option>
                    <option value="pending">Pending</option>
                    <option value="rejected">Ditolak</option>
                </select>

                {{-- Reset --}}
                @if($search || $filterType !== 'all' || $filterStatus !== 'all' || $dateFrom || $dateTo)
                <button wire:click="resetFilters"
                    style="padding:7px 12px;background:#fee2e2;border:none;border-radius:8px;font-size:12px;color:#ef4444;cursor:pointer;font-weight:500;">
                    Reset
                </button>
                @endif
            </div>

            {{-- Table --}}
            @if($transactions->count() > 0)
            <table style="width:100%;border-collapse:collapse;font-size:14px;">
                <thead>
                    <tr style="border-bottom:2px solid #f3f4f6;">
                        <th style="text-align:left;padding-bottom:12px;font-size:12px;color:#9ca3af;font-weight:500;text-transform:uppercase;letter-spacing:0.05em;">Jenis</th>
                        <th style="text-align:left;padding-bottom:12px;font-size:12px;color:#9ca3af;font-weight:500;text-transform:uppercase;letter-spacing:0.05em;">Deskripsi</th>
                        <th style="text-align:left;padding-bottom:12px;font-size:12px;color:#9ca3af;font-weight:500;text-transform:uppercase;letter-spacing:0.05em;">Tanggal</th>
                        <th style="text-align:right;padding-bottom:12px;font-size:12px;color:#9ca3af;font-weight:500;text-transform:uppercase;letter-spacing:0.05em;">Nominal</th>
                        <th style="text-align:left;padding-bottom:12px;padding-left:16px;font-size:12px;color:#9ca3af;font-weight:500;text-transform:uppercase;letter-spacing:0.05em;">Status</th>
                        <th style="text-align:right;padding-bottom:12px;font-size:12px;color:#9ca3af;font-weight:500;"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($transactions as $trx)
                    @php
                        $isIncome = $trx->type === 'Pemasukan';
                        $sc = match($trx->status) {
                            'approved' => ['bg' => '#dcfce7', 'color' => '#16a34a', 'label' => 'Berhasil'],
                            'pending'  => ['bg' => '#fef3c7', 'color' => '#d97706', 'label' => 'Pending'],
                            'rejected' => ['bg' => '#fee2e2', 'color' => '#ef4444', 'label' => 'Ditolak'],
                            default    => ['bg' => '#f3f4f6', 'color' => '#6b7280', 'label' => $trx->status],
                        };
                    @endphp
                    <tr style="border-bottom:1px solid #f9fafb;" onmouseover="this.style.background='#f9fafb'" onmouseout="this.style.background='transparent'">

                        {{-- Jenis --}}
                        <td style="padding:14px 16px 14px 0;">
                            <div style="display:flex;align-items:center;gap:8px;">
                                <div style="width:36px;height:36px;background:{{ $isIncome ? '#f0fdf4' : '#fff7ed' }};border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                    <svg style="width:16px;height:16px;" fill="none" stroke="{{ $isIncome ? '#22c55e' : '#f97316' }}" viewBox="0 0 24 24">
                                        @if($isIncome)
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.5 10.5 12 3m0 0 7.5 7.5M12 3v18"/>
                                        @else
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.5 13.5 12 21m0 0-7.5-7.5M12 21V3"/>
                                        @endif
                                    </svg>
                                </div>
                                <span style="font-size:12px;font-weight:600;color:{{ $isIncome ? '#16a34a' : '#ef4444' }};">
                                    {{ $trx->type }}
                                </span>
                            </div>
                        </td>

                        {{-- Deskripsi --}}
                        <td style="padding:14px 16px 14px 0;">
                            <p style="font-weight:500;color:#111827;font-size:14px;">{{ $trx->description ?? '-' }}</p>
                            <p style="font-size:11px;color:#9ca3af;margin-top:2px;">
                                {{ $trx->category_name }} · {{ $trx->user_name }}
                            </p>
                        </td>

                        {{-- Tanggal --}}
                        <td style="padding:14px 16px 14px 0;font-size:13px;color:#6b7280;white-space:nowrap;">
                            {{ \Carbon\Carbon::parse($trx->date)->format('d M Y') }}
                            <p style="font-size:11px;color:#9ca3af;margin-top:1px;">
                                {{ \Carbon\Carbon::parse($trx->created_at)->format('H:i') }} WIB
                            </p>
                        </td>

                        {{-- Nominal --}}
                        <td style="padding:14px 16px 14px 0;text-align:right;font-weight:700;font-size:15px;white-space:nowrap;color:{{ $isIncome ? '#16a34a' : '#ef4444' }};">
                            {{ $isIncome ? '+' : '-' }} Rp {{ number_format($trx->amount, 0, ',', '.') }}
                        </td>

                        {{-- Status --}}
                        <td style="padding:14px 0 14px 16px;">
                            <span style="display:inline-flex;padding:3px 10px;background:{{ $sc['bg'] }};color:{{ $sc['color'] }};font-size:12px;font-weight:600;border-radius:999px;">
                                {{ $sc['label'] }}
                            </span>
                        </td>

                        {{-- Aksi --}}
                        <td style="padding:14px 0;text-align:right;">
                            <button
                                wire:click="showTransactionDetail('{{ $trx->id }}')"
                                style="display:inline-flex;padding:6px;background:#f9fafb;border:1px solid #e5e7eb;border-radius:6px;color:#6b7280;cursor:pointer;"
                                onmouseover="this.style.background='#eff6ff';this.style.borderColor='#bfdbfe';"
                                onmouseout="this.style.background='#f9fafb';this.style.borderColor='#e5e7eb';">
                                <svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                                </svg>
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            {{-- Pagination --}}
            <div style="display:flex;align-items:center;justify-content:space-between;margin-top:16px;padding-top:16px;border-top:1px solid #f3f4f6;">
                <p style="font-size:12px;color:#9ca3af;">
                    Menampilkan {{ $transactions->firstItem() }}-{{ $transactions->lastItem() }} dari {{ $transactions->total() }} transaksi
                </p>
                <div style="display:flex;gap:4px;">
                    @if($transactions->onFirstPage())
                    <span style="padding:6px 10px;background:#f3f4f6;border-radius:6px;font-size:12px;color:#9ca3af;">‹</span>
                    @else
                    <button wire:click="previousPage" style="padding:6px 10px;background:#fff;border:1px solid #e5e7eb;border-radius:6px;font-size:12px;color:#374151;cursor:pointer;">‹</button>
                    @endif

                    @foreach(range(1, $transactions->lastPage()) as $page)
                    <button wire:click="gotoPage({{ $page }})"
                        style="padding:6px 10px;border-radius:6px;font-size:12px;cursor:pointer;border:1px solid {{ $currentPage === $page ? '#f59e0b' : '#e5e7eb' }};background:{{ $currentPage === $page ? '#fef3c7' : '#fff' }};color:{{ $currentPage === $page ? '#d97706' : '#374151' }};">
                        {{ $page }}
                    </button>
                    @endforeach

                    @if($transactions->hasMorePages())
                    <button wire:click="nextPage({{ $transactions->lastPage() }})" style="padding:6px 10px;background:#fff;border:1px solid #e5e7eb;border-radius:6px;font-size:12px;color:#374151;cursor:pointer;">›</button>
                    @else
                    <span style="padding:6px 10px;background:#f3f4f6;border-radius:6px;font-size:12px;color:#9ca3af;">›</span>
                    @endif
                </div>
            </div>

            @else
            <div style="padding:60px 0;text-align:center;">
                <div style="width:64px;height:64px;background:#f3f4f6;border-radius:16px;display:flex;align-items:center;justify-content:center;margin:0 auto 12px;">
                    <svg style="width:32px;height:32px;" fill="none" stroke="#9ca3af" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 12c0-1.232-.046-2.453-.138-3.662a4.006 4.006 0 0 0-3.7-3.7 48.678 48.678 0 0 0-7.324 0 4.006 4.006 0 0 0-3.7 3.7c-.017.22-.032.441-.046.662M19.5 12l3-3m-3 3-3-3m-12 3c0 1.232.046 2.453.138 3.662a4.006 4.006 0 0 0 3.7 3.7 48.656 48.656 0 0 0 7.324 0 4.006 4.006 0 0 0 3.7-3.7c.017-.22.032-.441.046-.662M4.5 12l3 3m-3-3-3 3"/>
                    </svg>
                </div>
                <p style="font-size:14px;font-weight:500;color:#6b7280;">Tidak ada transaksi ditemukan</p>
                <p style="font-size:12px;color:#9ca3af;margin-top:4px;">Coba ubah filter pencarian</p>
            </div>
            @endif
        </div>
    </div>

    {{-- MODAL DETAIL TRANSAKSI (FILAMENT COMPONENT STYLE) --}}
    @if($selectedTransaction)
    <div style="position:fixed;inset:0;z-index:50;display:flex;align-items:center;justify-content:center;padding:16px;">
        <div wire:click="closeTransactionDetail" style="position:absolute;inset:0;background:#00000090;backdrop-filter:blur(4px);"></div>

        <div style="position:relative;background:#ffffff;width:100%;max-width:500px;border-radius:16px;box-shadow:0 20px 25px -5px rgb(0 0 0 / 0.1);overflow:hidden;animation: scaleUp 0.2s ease-out;">
            <div style="padding:16px 20px;border-bottom:1px solid #f3f4f6;display:flex;justify-content:between;align-items:center;">
                <h3 style="font-size:16px;font-weight:700;color:#111827;">Detail Transaksi</h3>
                <button wire:click="closeTransactionDetail" style="background:none;border:none;color:#9ca3af;cursor:pointer;font-size:20px;">&times;</button>
            </div>

            <div style="padding:20px;display:flex;flex-direction:column;gap:14px;">
                <div style="text-align:center;padding:12px 0;border-bottom:1px dashed #e5e7eb;">
                    <p style="font-size:12px;color:#6b7280;text-transform:uppercase;letter-spacing:0.05em;">Nominal Transaksi</p>
                    <h2 style="font-size:26px;font-weight:800;margin-top:4px;color:{{ $selectedTransaction->type === 'Pemasukan' ? '#16a34a' : '#ef4444' }};">
                        {{ $selectedTransaction->type === 'Pemasukan' ? '+' : '-' }} Rp {{ number_format($selectedTransaction->amount, 0, ',', '.') }}
                    </h2>
                </div>

                <div style="display:flex;justify-content:space-between;font-size:13px;">
                    <span style="color:#6b7280;">Jenis</span>
                    <span style="font-weight:600;color:{{ $selectedTransaction->type === 'Pemasukan' ? '#16a34a' : '#ef4444' }};">{{ $selectedTransaction->type }}</span>
                </div>

                <div style="display:flex;justify-content:space-between;font-size:13px;">
                    <span style="color:#6b7280;">Deskripsi</span>
                    <span style="font-weight:600;color:#111827;text-align:right;">{{ $selectedTransaction->description ?? '-' }}</span>
                </div>

                <div style="display:flex;justify-content:space-between;font-size:13px;">
                    <span style="color:#6b7280;">Kategori</span>
                    <span style="font-weight:600;color:#111827;">{{ $selectedTransaction->category_name }}</span>
                </div>

                <div style="display:flex;justify-content:space-between;font-size:13px;">
                    <span style="color:#6b7280;">Oleh Anggota</span>
                    <span style="font-weight:600;color:#111827;">{{ $selectedTransaction->user_name }}</span>
                </div>

                <div style="display:flex;justify-content:space-between;font-size:13px;">
                    <span style="color:#6b7280;">Tanggal Transaksi</span>
                    <span style="font-weight:600;color:#111827;">{{ \Carbon\Carbon::parse($selectedTransaction->date)->format('d M Y') }}</span>
                </div>

                <div style="display:flex;justify-content:space-between;font-size:13px;">
                    <span style="color:#6b7280;">Waktu Input</span>
                    <span style="font-weight:600;color:#111827;">{{ \Carbon\Carbon::parse($selectedTransaction->created_at)->format('H:i') }} WIB</span>
                </div>

                <div style="display:flex;justify-content:space-between;align-items:center;font-size:13px;margin-top:4px;">
                    <span style="color:#6b7280;">Status</span>
                    @php
                        $modalStatus = match($selectedTransaction->status) {
                            'approved' => ['bg' => '#dcfce7', 'color' => '#16a34a', 'label' => 'Berhasil'],
                            'pending'  => ['bg' => '#fef3c7', 'color' => '#d97706', 'label' => 'Pending'],
                            'rejected' => ['bg' => '#fee2e2', 'color' => '#ef4444', 'label' => 'Ditolak'],
                            default    => ['bg' => '#f3f4f6', 'color' => '#6b7280', 'label' => $selectedTransaction->status],
                        };
                    @endphp
                    <span style="padding:4px 12px;background:{{ $modalStatus['bg'] }};color:{{ $modalStatus['color'] }};font-weight:600;border-radius:999px;">
                        {{ $modalStatus['label'] }}
                    </span>
                </div>
            </div>

            <div style="padding:12px 20px;background:#f9fafb;border-top:1px solid #f3f4f6;display:flex;justify-content:flex-end;">
                <button wire:click="closeTransactionDetail" style="padding:8px 16px;background:#ffffff;border:1px solid #e5e7eb;border-radius:8px;font-size:13px;color:#374151;cursor:pointer;font-weight:500;">
                    Tutup
                </button>
            </div>
        </div>
    </div>
    @endif
</x-filament-panels::page>
