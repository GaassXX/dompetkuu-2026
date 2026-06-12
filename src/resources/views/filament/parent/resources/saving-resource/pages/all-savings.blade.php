@php
    $savings  = $this->getSavings();
    $counts   = $this->getCounts();

    $categoryIcons = [
        'Liburan'    => ['bg' => '#eff6ff', 'stroke' => '#3b82f6', 'svg' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 12 3.269 3.125A59.769 59.769 0 0 1 21.485 12 59.768 59.768 0 0 1 3.27 20.875L5.999 12Zm0 0h7.5"/>'],
        'Properti'   => ['bg' => '#f0fdf4', 'stroke' => '#22c55e', 'svg' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25"/>'],
        'Pribadi'    => ['bg' => '#faf5ff', 'stroke' => '#a855f7', 'svg' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z"/>'],
        'Pendidikan' => ['bg' => '#fefce8', 'stroke' => '#eab308', 'svg' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 3.741-3.342M6.75 15a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Zm0 0v-3.675A55.378 55.378 0 0 1 12 8.443m-7.007 11.55A5.981 5.981 0 0 0 6.75 15.75v-1.5"/>'],
        'Kendaraan'  => ['bg' => '#fff7ed', 'stroke' => '#f97316', 'svg' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 0 1-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 0 0-3.213-9.193 2.056 2.056 0 0 0-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 0 0-10.026 0 1.106 1.106 0 0 0-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12"/>'],
        'Kesehatan'  => ['bg' => '#fdf2f8', 'stroke' => '#ec4899', 'svg' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z"/>'],
        'Elektronik' => ['bg' => '#eef2ff', 'stroke' => '#6366f1', 'svg' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17.25v1.007a3 3 0 0 1-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0 1 15 18.257V17.25m6-12V15a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 15V5.25m18 0A2.25 2.25 0 0 0 18.75 3H5.25A2.25 2.25 0 0 0 3 5.25m18 0H3"/>'],
        'Lainnya'    => ['bg' => '#f3f4f6', 'stroke' => '#6b7280', 'svg' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.75 9.776c.112-.017.227-.026.344-.026h15.812c.117 0 .232.009.344.026m-16.5 0a2.25 2.25 0 0 0-1.883 2.542l.857 6a2.25 2.25 0 0 0 2.227 1.932H19.05a2.25 2.25 0 0 0 2.227-1.932l.857-6a2.25 2.25 0 0 0-1.883-2.542m-16.5 0V6A2.25 2.25 0 0 1 6 3.75h3.879a1.5 1.5 0 0 1 1.06.44l2.122 2.12a1.5 1.5 0 0 0 1.06.44H18A2.25 2.25 0 0 1 20.25 9v.776"/>'],
    ];
@endphp

<x-filament-panels::page>
    <div class="space-y-6">

        {{-- TABS --}}
        <div style="display:flex;gap:8px;border-bottom:2px solid #f3f4f6;padding-bottom:0;">
            @foreach(['all' => 'Semua', 'active' => 'Aktif', 'completed' => 'Selesai'] as $tab => $label)
            <button
                wire:click="setTab('{{ $tab }}')"
                style="
                    padding:10px 20px;
                    font-size:14px;
                    font-weight:{{ $activeTab === $tab ? '600' : '400' }};
                    color:{{ $activeTab === $tab ? '#d97706' : '#6b7280' }};
                    border:none;background:transparent;cursor:pointer;
                    border-bottom:{{ $activeTab === $tab ? '2px solid #d97706' : '2px solid transparent' }};
                    margin-bottom:-2px;
                    transition:all 0.15s;
                "
            >
                {{ $label }}
                <span style="
                    display:inline-flex;align-items:center;justify-content:center;
                    width:20px;height:20px;border-radius:999px;font-size:11px;margin-left:6px;
                    background:{{ $activeTab === $tab ? '#fef3c7' : '#f3f4f6' }};
                    color:{{ $activeTab === $tab ? '#d97706' : '#9ca3af' }};
                ">{{ $counts[$tab] }}</span>
            </button>
            @endforeach
        </div>

        {{-- SAVINGS GRID --}}
        @if($savings->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @foreach($savings as $saving)
            @php
                $catIcon       = $categoryIcons[$saving->category] ?? $categoryIcons['Lainnya'];
                $progress      = $saving->getProgressPercentage();
                $progressColor = $progress >= 100 ? '#22c55e' : ($progress >= 60 ? '#facc15' : '#f59e0b');
                $isCompleted   = $saving->status === 'completed';
            @endphp
            <div style="background:#fff;border:1px solid #f3f4f6;border-radius:16px;padding:20px;position:relative;" class="hover:shadow-md transition-all">

                {{-- Completed badge --}}
                @if($isCompleted)
                <div style="position:absolute;top:12px;right:12px;background:#dcfce7;color:#16a34a;font-size:11px;font-weight:700;padding:3px 10px;border-radius:999px;">
                    ✓ Selesai
                </div>
                @endif
{{-- Icon + Category --}}
<div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:16px;">
    <div style="width:52px;height:52px;background:{{ $catIcon['bg'] }};border-radius:14px;display:flex;align-items:center;justify-content:center;">
        <svg style="width:26px;height:26px;" fill="none" stroke="{{ $catIcon['stroke'] }}" viewBox="0 0 24 24">
            {!! $catIcon['svg'] !!}
        </svg>
    </div>
    <div style="text-align:right;">
        <span style="font-size:11px;font-weight:700;letter-spacing:0.08em;color:#9ca3af;text-transform:uppercase;display:block;">
            {{ $saving->category }}
        </span>
        {{-- ✅ Badge pemilik --}}
        @php
            $isOwner  = $saving->user_id === auth()->id();
            $badgeBg  = $isOwner ? '#fef3c7' : '#eff6ff';
            $badgeClr = $isOwner ? '#d97706' : '#3b82f6';
            $badgeTxt = $isOwner ? ' Orang Tua' : ' ' . $saving->user->name;
        @endphp
        <span style="font-size:10px;font-weight:600;padding:2px 8px;border-radius:999px;background:{{ $badgeBg }};color:{{ $badgeClr }};margin-top:4px;display:inline-block;">
            {{ $badgeTxt }}
        </span>
    </div>
</div>

                {{-- Owner (parent panel) --}}
                <div style="font-size:11px;color:#9ca3af;margin-bottom:4px;">
                    {{ $saving->user->name }}
                </div>

                {{-- Name & Target --}}
                <p style="font-weight:700;font-size:15px;color:#111827;margin-bottom:4px;">{{ $saving->name }}</p>
                <p style="font-size:12px;color:#9ca3af;margin-bottom:16px;">
                    Target: Rp {{ number_format($saving->target_amount, 0, ',', '.') }}
                </p>

                {{-- Progress --}}
                <div style="margin-bottom:16px;">
                    <div style="display:flex;justify-content:space-between;font-size:12px;margin-bottom:6px;">
                        <span style="color:#6b7280;">Progress: {{ $progress }}%</span>
                        <span style="font-weight:600;color:#374151;">
                            Rp {{ number_format($saving->current_amount, 0, ',', '.') }}
                        </span>
                    </div>
                    <div style="width:100%;background:#f3f4f6;border-radius:999px;height:8px;">
                        <div style="width:{{ $progress }}%;background:{{ $progressColor }};height:8px;border-radius:999px;"></div>
                    </div>
                </div>

                {{-- Footer --}}
                <div style="display:flex;align-items:center;justify-content:space-between;padding-top:12px;border-top:1px solid #f3f4f6;">
                    @if($saving->target_date)
                    <p style="font-size:11px;color:#9ca3af;">
                        📅 {{ $saving->target_date->format('d M Y') }}
                    </p>
                    @else
                    <p style="font-size:11px;color:#9ca3af;">Terkumpul: Rp {{ number_format($saving->current_amount, 0, ',', '.') }}</p>
                    @endif

                    <a href="{{ \App\Filament\Parent\Resources\SavingResource::getUrl('view', ['record' => $saving]) }}"
                       style="display:inline-flex;align-items:center;gap:4px;padding:6px 12px;background:#f9fafb;border:1px solid #e5e7eb;border-radius:8px;font-size:12px;font-weight:500;color:#374151;text-decoration:none;"
                       onmouseover="this.style.background='#fffbeb';this.style.borderColor='#fcd34d';"
                       onmouseout="this.style.background='#f9fafb';this.style.borderColor='#e5e7eb';">
                        Detail →
                    </a>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div style="padding:60px 0;text-align:center;">
            <div style="width:64px;height:64px;background:#f3f4f6;border-radius:16px;display:flex;align-items:center;justify-content:center;margin:0 auto 12px;">
                <svg style="width:32px;height:32px;" fill="none" stroke="#9ca3af" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z"/>
                </svg>
            </div>
            <p style="font-size:14px;font-weight:500;color:#6b7280;">Belum ada tabungan</p>
        </div>
        @endif

    </div>
</x-filament-panels::page>
