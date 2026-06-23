@php
    $stats      = $this->getStats();
    $expenses   = $this->getExpenses();
    $categories = $this->getCategories();

    $periodLabels = [
        'this_month' => 'Bulan Ini',
        'last_month' => 'Bulan Lalu',
        'this_year'  => 'Tahun Ini',
    ];
@endphp

<x-filament-panels::page>
    <div class="space-y-6" x-data="{ showDeleteModal: false, expenseIdToDelete: null }">

        {{-- STATS CARDS --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            {{-- Total Pengeluaran --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                <div style="display:flex;align-items:center;gap:8px;margin-bottom:8px;">
                    <div style="width:32px;height:32px;background:#fff7ed;border-radius:8px;display:flex;align-items:center;justify-content:center;">
                        <svg style="width:16px;height:16px;" fill="none" stroke="#f97316" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75"/>
                        </svg>
                    </div>
                    <p style="font-size:11px;font-weight:700;letter-spacing:0.08em;color:#9ca3af;text-transform:uppercase;">Total Pengeluaran Bulan Ini</p>
                </div>
                <p style="font-size:24px;font-weight:700;color:{{ $stats['isHigher'] ? '#ef4444' : '#111827' }};">
                    Rp {{ number_format($stats['totalThisMonth'], 0, ',', '.') }}
                </p>
                <p style="font-size:12px;margin-top:4px;color:{{ $stats['isHigher'] ? '#ef4444' : '#22c55e' }};">
                    {{ $stats['isHigher'] ? '↑' : '↓' }}
                    {{ $stats['isHigher'] ? '+' : '' }}{{ $stats['percentChange'] }}% dari bulan lalu
                </p>
            </div>

            {{-- Kategori Terboros --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                <div style="display:flex;align-items:center;gap:8px;margin-bottom:8px;">
                    <div style="width:32px;height:32px;background:#fef3c7;border-radius:8px;display:flex;align-items:center;justify-content:center;">
                        <svg style="width:16px;height:16px;" fill="none" stroke="#f59e0b" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"/>
                        </svg>
                    </div>
                    <p style="font-size:11px;font-weight:700;letter-spacing:0.08em;color:#9ca3af;text-transform:uppercase;">Kategori Terboros</p>
                </div>
                <p style="font-size:20px;font-weight:700;color:#111827;">{{ $stats['topCategoryName'] }}</p>
                <p style="font-size:12px;color:#6b7280;margin-top:4px;">
                    Mencakup {{ $stats['topCategoryPct'] }}% dari total pengeluaran
                </p>
            </div>

            {{-- Sisa Anggaran --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                <div style="display:flex;align-items:center;gap:8px;margin-bottom:8px;">
                    <div style="width:32px;height:32px;background:{{ $stats['isOverBudget'] ? '#fee2e2' : '#dcfce7' }};border-radius:8px;display:flex;align-items:center;justify-content:center;">
                        <svg style="width:16px;height:16px;" fill="none" stroke="{{ $stats['isOverBudget'] ? '#ef4444' : '#22c55e' }}" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 12a2.25 2.25 0 0 0-2.25-2.25H15a3 3 0 1 1-6 0H5.25A2.25 2.25 0 0 1 3 7.5V15a2.25 2.25 0 0 0 2.25 2.25h13.5A2.25 2.25 0 0 0 21 15v-3Z"/>
                        </svg>
                    </div>
                    <p style="font-size:11px;font-weight:700;letter-spacing:0.08em;color:#9ca3af;text-transform:uppercase;">Sisa Anggaran</p>
                </div>
                @if($stats['totalBudget'] > 0)
                    <p style="font-size:24px;font-weight:700;color:{{ $stats['isOverBudget'] ? '#ef4444' : '#111827' }};">
                        {{ $stats['isOverBudget'] ? '- ' : '' }}Rp {{ number_format($stats['sisaAnggaran'], 0, ',', '.') }}
                    </p>
                    <div style="margin-top:8px;">
                        <div style="display:flex;justify-content:space-between;font-size:11px;color:#9ca3af;margin-bottom:4px;">
                            <span>{{ $stats['budgetPct'] }}% terpakai</span>
                            <span>Rp {{ number_format($stats['totalBudget'], 0, ',', '.') }}</span>
                        </div>
                        <div style="width:100%;background:#f3f4f6;border-radius:999px;height:6px;">
                            <div style="width:{{ min(100, $stats['budgetPct']) }}%;background:{{ $stats['isOverBudget'] ? '#ef4444' : '#22c55e' }};height:6px;border-radius:999px;"></div>
                        </div>
                    </div>
                @else
                    <p style="font-size:14px;color:#9ca3af;margin-top:8px;">Belum ada anggaran bulan ini</p>
                @endif
            </div>
        </div>

        {{-- TABLE CARD --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
            {{-- Filter Bar --}}
            <div style="display:flex;align-items:center;gap:8px;margin-bottom:20px;">
                {{-- Category Filter Dropdown --}}
                @php
                    $selectedCatName = $filterCategory === 'all'
                    ? 'Semua Kategori'
                    : ($categories->firstWhere('id', $filterCategory)?->name ?? 'Semua Kategori');
                @endphp
                <div style="position:relative;" x-data="{ open: false }">
                    <button @click="open = !open"
                        style="display:inline-flex;align-items:center;gap:6px;padding:6px 14px;background:#fff;border:1px solid {{ $filterCategory !== 'all' ? '#f59e0b' : '#e5e7eb' }};border-radius:8px;font-size:12px;font-weight:500;color:{{ $filterCategory !== 'all' ? '#d97706' : '#6b7280' }};cursor:pointer;min-width:160px;justify-content:space-between;">
                        <div style="display:flex;align-items:center;gap:6px;">
                            <svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/>
                            </svg>
                            {{ $selectedCatName }}
                        </div>
                        <svg style="width:12px;height:12px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    <div x-show="open" @click.outside="open = false" x-transition
                        style="position:absolute;left:0;top:calc(100% + 6px);background:#fff;border:1px solid #e5e7eb;border-radius:10px;box-shadow:0 4px 16px rgba(0,0,0,0.08);min-width:180px;z-index:50;overflow:hidden;max-height:300px;overflow-y:auto;">
                        <button wire:click="setCategory('all')" @click="open = false"
                            style="display:block;width:100%;text-align:left;padding:9px 14px;font-size:13px;border:none;cursor:pointer;background:{{ $filterCategory === 'all' ? '#fef3c7' : 'transparent' }};color:{{ $filterCategory === 'all' ? '#d97706' : '#374151' }};font-weight:{{ $filterCategory === 'all' ? '600' : '400' }};"
                            onmouseover="this.style.background='#f9fafb'"
                            onmouseout="this.style.background='{{ $filterCategory === 'all' ? '#fef3c7' : 'transparent' }}'">
                            Semua Kategori
                        </button>

                        @foreach($categories as $cat)
                        <button wire:click="setCategory('{{ $cat->id }}')" @click="open = false"
                            style="display:block;width:100%;text-align:left;padding:9px 14px;font-size:13px;border:none;cursor:pointer;background:{{ $filterCategory == $cat->id ? '#fef3c7' : 'transparent' }};color:{{ $filterCategory == $cat->id ? '#d97706' : '#374151' }};font-weight:{{ $filterCategory == $cat->id ? '600' : '400' }};"
                            onmouseover="this.style.background='#f9fafb'"
                            onmouseout="this.style.background='{{ $filterCategory == $cat->id ? '#fef3c7' : 'transparent' }}'">
                            {{ $cat->name }}
                        </button>
                        @endforeach
                    </div>
                </div>

                {{-- Period Dropdown --}}
                <div style="position:relative;" x-data="{ open: false }">
                    <button @click="open = !open"
                        style="display:inline-flex;align-items:center;gap:6px;padding:6px 12px;background:#fff;border:1px solid #e5e7eb;border-radius:8px;font-size:12px;font-weight:500;color:#6b7280;cursor:pointer;">
                        <svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5"/>
                        </svg>
                        {{ $periodLabels[$filterPeriod] ?? 'Bulan Ini' }}
                        <svg style="width:12px;height:12px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div x-show="open" @click.outside="open = false" x-transition
                        style="position:absolute;right:0;top:calc(100% + 6px);background:#fff;border:1px solid #e5e7eb;border-radius:10px;box-shadow:0 4px 16px rgba(0,0,0,0.08);min-width:150px;z-index:50;overflow:hidden;">
                        @foreach($periodLabels as $val => $label)
                        <button wire:click="setPeriod('{{ $val }}')" @click="open = false"
                            style="display:block;width:100%;text-align:left;padding:9px 14px;font-size:13px;border:none;cursor:pointer;background:{{ $filterPeriod === $val ? '#fef3c7' : 'transparent' }};color:{{ $filterPeriod === $val ? '#d97706' : '#374151' }};font-weight:{{ $filterPeriod === $val ? '600' : '400' }};"
                            onmouseover="this.style.background='#f9fafb'"
                            onmouseout="this.style.background='{{ $filterPeriod === $val ? '#fef3c7' : 'transparent' }}'">
                            {{ $label }}
                        </button>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Table Rendering --}}
            @if($expenses->count() > 0)
            <table style="width:100%;border-collapse:collapse;font-size:14px;">
                <thead>
                    <tr style="border-bottom:2px solid #f3f4f6;">
                        <th style="text-align:left;padding:0 16px 12px 0;font-size:12px;color:#9ca3af;font-weight:500;">Keperluan</th>
                        <th style="text-align:left;padding:0 16px 12px 0;font-size:12px;color:#9ca3af;font-weight:500;">Kategori</th>
                        <th style="text-align:left;padding:0 16px 12px 0;font-size:12px;color:#9ca3af;font-weight:500;">Tanggal</th>
                        <th style="text-align:right;padding:0 32px 12px 0;font-size:12px;color:#9ca3af;font-weight:500;">Nominal</th>
                        <th style="text-align:left;padding:0 32px 12px 0;font-size:12px;color:#9ca3af;font-weight:500;">Status</th>
                        <th style="text-align:right;padding:0 0 12px 0;font-size:12px;color:#9ca3af;font-weight:500;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($expenses as $expense)
                    <tr style="border-bottom:1px solid #f9fafb;" onmouseover="this.style.background='#f9fafb'" onmouseout="this.style.background='transparent'">
                        <td style="padding:14px 16px 14px 0;">
                            <p style="font-weight:500;color:#111827;font-size:14px;">{{ $expense->description ?? '-' }}</p>
                            <p style="font-size:12px;color:#9ca3af;margin-top:2px;">{{ $expense->category?->name }}</p>
                        </td>
                        <td style="padding:14px 16px 14px 0;">
                            <span style="display:inline-flex;padding:3px 10px;background:#f3f4f6;color:#374151;font-size:12px;font-weight:500;border-radius:999px;">
                                {{ $expense->category?->name ?? '-' }}
                            </span>
                        </td>
                        <td style="padding:14px 16px 14px 0;font-size:13px;color:#6b7280;white-space:nowrap;">
                            {{ \Carbon\Carbon::parse($expense->date)->format('d M Y') }}
                        </td>
                        <td style="padding:14px 32px 14px 0;text-align:right;font-weight:600;color:#ef4444;white-space:nowrap;">
                            - Rp {{ number_format($expense->amount, 0, ',', '.') }}
                        </td>
                        <td style="padding:14px 32px 14px 0;text-align:left;vertical-align:middle;">
                            @php
                                $sc = match($expense->status){
                                    'approved' => ['bg' => '#dcfce7', 'color' => '#16a34a', 'label' => 'Disetujui'],
                                    'pending'  => ['bg' => '#fef3c7', 'color' => '#d97706', 'label' => 'Pending'],
                                    'rejected' => ['bg' => '#fee2e2', 'color' => '#ef4444', 'label' => 'Ditolak'],
                                    default    => ['bg' => '#f3f4f6', 'color' => '#6b7280', 'label' => ucfirst($expense->status)],
                                };
                            @endphp
                            <span style="display:inline-flex;padding:3px 10px;background:{{ $sc['bg'] }};color:{{ $sc['color'] }};font-size:12px;font-weight:600;border-radius:999px;white-space:nowrap;">
                                {{ $sc['label'] }}
                            </span>
                        </td>
                        <td style="padding:14px 0; text-align: right; white-space: nowrap;">
                            <div style="display:inline-flex; gap:6px; align-items:center; justify-content:flex-end;">
                                {{-- Edit Button --}}
                                <a href="{{ \App\Filament\Child\Resources\ExpenseResource::getUrl('edit', ['record' => $expense]) }}"
                                   title="Ubah"
                                   style="display:inline-flex;padding:6px;background:#f9fafb;border:1px solid #e5e7eb;border-radius:6px;color:#6b7280;text-decoration:none;"
                                   onmouseover="this.style.background='#fef3c7';this.style.borderColor='#fcd34d';"
                                   onmouseout="this.style.background='#f9fafb';this.style.borderColor='#e5e7eb';">
                                    <svg style="width:14px;height:14px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125"/>
                                    </svg>
                                </a>

                                {{-- Delete Button --}}
                                <button @click="showDeleteModal = true; expenseIdToDelete = '{{ $expense->id }}'"
                                        title="Hapus"
                                        style="display:inline-flex;padding:6px;background:#f9fafb;border:1px solid #e5e7eb;border-radius:6px;color:#ef4444;cursor:pointer;"
                                        onmouseover="this.style.background='#fee2e2';this.style.borderColor='#fca5a5';"
                                        onmouseout="this.style.background='#f9fafb';this.style.borderColor='#e5e7eb';">
                                    <svg style="width:14px;height:14px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            {{-- Pagination --}}
            <div style="display:flex;align-items:center;justify-content:space-between;margin-top:16px;padding-top:16px;border-top:1px solid #f3f4f6;">
                <p style="font-size:12px;color:#9ca3af;">
                    Menampilkan {{ $expenses->firstItem() }}-{{ $expenses->lastItem() }} dari {{ $expenses->total() }} pengeluaran
                </p>
                <div style="display:flex;gap:4px;">
                    @if($expenses->onFirstPage())
                    <span style="padding:6px 10px;background:#f3f4f6;border-radius:6px;font-size:12px;color:#9ca3af;">‹</span>
                    @else
                    <button wire:click="previousPage" style="padding:6px 10px;background:#fff;border:1px solid #e5e7eb;border-radius:6px;font-size:12px;color:#374151;cursor:pointer;">‹</button>
                    @endif

                    @foreach($expenses->getUrlRange(1, $expenses->lastPage()) as $page => $url)
                    <button wire:click="gotoPage({{ $page }})"
                        style="padding:6px 10px;border-radius:6px;font-size:12px;cursor:pointer;border:1px solid {{ $expenses->currentPage() === $page ? '#f59e0b' : '#e5e7eb' }};background:{{ $expenses->currentPage() === $page ? '#fef3c7' : '#fff' }};color:{{ $expenses->currentPage() === $page ? '#d97706' : '#374151' }};">
                        {{ $page }}
                    </button>
                    @endforeach

                    @if($expenses->hasMorePages())
                    <button wire:click="nextPage" style="padding:6px 10px;background:#fff;border:1px solid #e5e7eb;border-radius:6px;font-size:12px;color:#374151;cursor:pointer;">›</button>
                    @else
                    <span style="padding:6px 10px;background:#f3f4f6;border-radius:6px;font-size:12px;color:#9ca3af;">›</span>
                    @endif
                </div>
            </div>
            @else
            <div style="padding:60px 0;text-align:center;">
                <p style="font-size:14px;color:#6b7280;">Belum ada pengeluaran</p>
            </div>
            @endif
        </div>

        {{-- MODAL OVERLAY CONFIRMATION (FIXED VIEWPORT) --}}
        <div x-show="showDeleteModal"
             class="fixed inset-0 z-[99999] flex items-center justify-center overflow-y-auto"
             style="display: none;"
             x-cloak>

            <div x-show="showDeleteModal"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-gray-950/50 backdrop-blur-sm transition-opacity"
                 @click="showDeleteModal = false">
            </div>

            <div x-show="showDeleteModal"
                 x-transition:enter="transition ease-out duration-300 transform"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-200 transform"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 style="background: #ffffff; border-radius: 16px; padding: 28px; max-width: 380px; width: 100%; position: relative; z-index: 100; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04); margin: 16px; border: 1px solid #f3f4f6; text-align: center;">

                <button @click="showDeleteModal = false"
                        style="position: absolute; top: 16px; right: 16px; background: transparent; border: none; color: #9ca3af; cursor: pointer; padding: 4px;"
                        onmouseover="this.style.color='#4b5563'" onmouseout="this.style.color='#9ca3af'">
                    <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>

                <div style="width: 56px; height: 56px; background-color: #fef2f2; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px;">
                    <svg style="width: 24px; height: 24px; color: #ef4444;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </div>

                <h3 style="font-size: 18px; font-weight: 700; color: #111827; margin: 0 0 4px 0;">
                    Delete Pengeluaran
                </h3>

                <p style="font-size: 14px; font-weight: 500; color: #6b7280; margin: 0 0 24px 0;">
                    Apakah Anda yakin ingin melakukan ini?
                </p>

                <div style="display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 12px; width: 100%;">
                    <button @click="showDeleteModal = false"
                            style="padding: 10px 16px; background-color: #ffffff; border: 1px solid #e5e7eb; border-radius: 10px; font-size: 14px; font-weight: 600; color: #374151; cursor: pointer; transition: background 0.2s;"
                            onmouseover="this.style.backgroundColor='#f9fafb'" onmouseout="this.style.backgroundColor='#ffffff'">
                        Cancel
                    </button>

                    <button @click="$wire.deleteExpense(expenseIdToDelete); showDeleteModal = false"
                            style="padding: 10px 16px; background-color: #ef4444; border: none; border-radius: 10px; font-size: 14px; font-weight: 600; color: #ffffff; cursor: pointer; transition: background 0.2s;"
                            onmouseover="this.style.backgroundColor='#dc2626'" onmouseout="this.style.backgroundColor='#ef4444'">
                        Confirm
                    </button>
                </div>

            </div>
        </div>

    </div>
</x-filament-panels::page>
