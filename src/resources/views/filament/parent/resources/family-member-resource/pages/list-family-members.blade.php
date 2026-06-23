@php
    $members = $this->getMembers();
    $stats   = $this->getStats();
@endphp

<x-filament-panels::page>
    <div class="space-y-6">

        {{-- STATS CARDS --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Total Anggota</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">
                            {{ $stats['total'] }} <span class="text-sm font-normal text-gray-400">Orang</span>
                        </p>
                    </div>
                    <div style="width:48px;height:48px;background:#fffbeb;border-radius:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <svg style="width:24px;height:24px;" fill="none" stroke="#f59e0b" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Anggota Aktif</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">
                            {{ $stats['active'] }} <span class="text-sm font-normal text-gray-400">Orang</span>
                        </p>
                    </div>
                    <div style="width:48px;height:48px;background:#f0fdf4;border-radius:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <svg style="width:24px;height:24px;" fill="none" stroke="#22c55e" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        {{-- TABLE --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
            @if($members->count() > 0)
            <div class="overflow-x-auto">
                <table style="width:100%;border-collapse:collapse;font-size:14px;">
                    <thead>
                        <tr style="border-bottom:1px solid #f3f4f6;background:#f9fafb;">
                            <th style="text-align:left;padding:14px 20px;font-size:12px;color:#9ca3af;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;">Nama</th>
                            <th style="text-align:left;padding:14px 20px;font-size:12px;color:#9ca3af;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;">Email</th>
                            <th style="text-align:center;padding:14px 20px;font-size:12px;color:#9ca3af;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;">Status</th>
                            <th style="text-align:left;padding:14px 20px;font-size:12px;color:#9ca3af;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;">Dibuat</th>
                            <th style="text-align:right;padding:14px 20px;font-size:12px;color:#9ca3af;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($members as $member)
                        <tr style="border-bottom:1px solid #f9fafb;" onmouseover="this.style.background='#f9fafb'" onmouseout="this.style.background='transparent'">
                            <td style="padding:16px 20px;">
                                <div style="display:flex;align-items:center;gap:12px;">
                                    <div style="width:36px;height:36px;border-radius:999px;background:#fffbeb;color:#d97706;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;flex-shrink:0;">
                                        {{ strtoupper(substr($member->name, 0, 2)) }}
                                    </div>
                                    <span style="font-weight:500;color:#111827;">{{ $member->name }}</span>
                                </div>
                            </td>
                            <td style="padding:16px 20px;font-size:13px;color:#6b7280;">{{ $member->email }}</td>
                            <td style="padding:16px 20px;text-align:center;">
                                <button
                                    wire:click="toggleActive({{ $member->id }})"
                                    wire:confirm="{{ $member->is_active ? 'Nonaktifkan akun ' . $member->name . '?' : 'Aktifkan akun ' . $member->name . '?' }}"
                                    style="display:inline-flex;align-items:center;gap:6px;padding:4px 12px;border-radius:999px;font-size:11px;font-weight:700;border:none;cursor:pointer;{{ $member->is_active ? 'background:#dcfce7;color:#16a34a;' : 'background:#fef2f2;color:#dc2626;' }}"
                                    title="{{ $member->is_active ? 'Klik untuk nonaktifkan' : 'Klik untuk aktifkan' }}"
                                >
                                    @if($member->is_active)
                                        <svg style="width:10px;height:10px;" fill="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/></svg>
                                        Aktif
                                    @else
                                        <svg style="width:10px;height:10px;" fill="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/></svg>
                                        Nonaktif
                                    @endif
                                </button>
                            </td>
                            <td style="padding:16px 20px;font-size:13px;color:#6b7280;">{{ $member->created_at->format('d M Y') }}</td>
                            <td style="padding:16px 20px;text-align:right;white-space:nowrap;">
                                <div style="display:inline-flex;gap:8px;align-items:center;">
                                    <a href="{{ \App\Filament\Parent\Resources\FamilyMemberResource::getUrl('edit', ['record' => $member]) }}"
                                       style="display:inline-flex;align-items:center;gap:4px;padding:6px 10px;border-radius:6px;font-size:12px;font-weight:500;color:#d97706;text-decoration:none;background:#fffbeb;border:1px solid #fde68a;"
                                       onmouseover="this.style.background='#fef3c7'"
                                       onmouseout="this.style.background='#fffbeb'">
                                        <svg style="width:14px;height:14px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/>
                                        </svg>
                                        Edit
                                    </a>
                                    <a href="#"
                                       onclick="event.preventDefault(); if(confirm('Hapus {{ $member->name }}?')) { $wire.deleteMember({{ $member->id }}); }"
                                       style="display:inline-flex;align-items:center;gap:4px;padding:6px 10px;border-radius:6px;font-size:12px;font-weight:500;color:#dc2626;text-decoration:none;background:#fef2f2;border:1px solid #fecaca;"
                                       onmouseover="this.style.background='#fee2e2'"
                                       onmouseout="this.style.background='#fef2f2'">
                                        <svg style="width:14px;height:14px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/>
                                        </svg>
                                        Delete
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div style="padding:60px 0;text-align:center;">
                <div style="width:64px;height:64px;background:#f3f4f6;border-radius:16px;display:flex;align-items:center;justify-content:center;margin:0 auto 12px;">
                    <svg style="width:32px;height:32px;" fill="none" stroke="#9ca3af" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/>
                    </svg>
                </div>
                <p style="font-size:14px;font-weight:500;color:#6b7280;">Belum ada anggota keluarga</p>
                <p style="font-size:12px;color:#9ca3af;margin-top:4px;">Tambah akun anak untuk mulai memantau keuangan mereka</p>
            </div>
            @endif
        </div>

    </div>
</x-filament-panels::page>
