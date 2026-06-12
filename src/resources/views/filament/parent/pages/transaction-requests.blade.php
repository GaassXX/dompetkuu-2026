@php $requests = $this->getRequests(); @endphp

<x-filament-panels::page>
    <div class="space-y-4">
        @if($requests->count() > 0)
        @foreach($requests as $req)
        @php
            $data = $req->old_data;
            $amount = $data['amount'] ?? 0;
            $desc   = $data['description'] ?? '-';
        @endphp
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
            <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;">
                <div>
                    <div style="display:flex;align-items:center;gap:8px;margin-bottom:6px;">
                        <span style="font-size:12px;font-weight:700;padding:2px 10px;background:#fef3c7;color:#d97706;border-radius:999px;">
                            🗑️ Hapus {{ ucfirst($req->model_type) }}
                        </span>
                        <span style="font-size:12px;color:#9ca3af;">{{ $req->created_at->diffForHumans() }}</span>
                    </div>
                    <p style="font-weight:600;color:#111827;font-size:15px;">{{ $req->child->name }}</p>
                    <p style="font-size:13px;color:#6b7280;margin-top:2px;">
                        {{ $desc }} · <span style="font-weight:600;color:#ef4444;">Rp {{ number_format($amount, 0, ',', '.') }}</span>
                    </p>
                </div>
                <div style="display:flex;gap:8px;">
                    <button
                        wire:click="approve({{ $req->id }})"
                        wire:confirm="Setujui permintaan hapus ini?"
                        style="padding:8px 16px;background:#dcfce7;color:#16a34a;border:none;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;">
                        ✓ Setujui
                    </button>
                    <button
                        wire:click="reject({{ $req->id }})"
                        wire:confirm="Tolak permintaan hapus ini?"
                        style="padding:8px 16px;background:#fee2e2;color:#ef4444;border:none;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;">
                        ✕ Tolak
                    </button>
                </div>
            </div>
        </div>
        @endforeach
        @else
        <div style="padding:60px 0;text-align:center;">
            <div style="width:64px;height:64px;background:#f3f4f6;border-radius:16px;display:flex;align-items:center;justify-content:center;margin:0 auto 12px;">
                <svg style="width:32px;height:32px;" fill="none" stroke="#9ca3af" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                </svg>
            </div>
            <p style="font-size:14px;font-weight:500;color:#6b7280;">Tidak ada permintaan pending</p>
        </div>
        @endif
    </div>
</x-filament-panels::page>
