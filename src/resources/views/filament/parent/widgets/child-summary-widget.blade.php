<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">Ringkasan per Anak</x-slot>

        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:12px;">
            @foreach($children as $child)
            <div style="background:var(--color-background-secondary);border:0.5px solid var(--color-border-tertiary);border-radius:12px;padding:14px;">

                {{-- Header --}}
                <div style="display:flex;align-items:center;gap:10px;margin-bottom:12px;">
                    <div style="width:38px;height:38px;border-radius:50%;background:#B5D4F4;display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:500;color:#0C447C;flex-shrink:0;">
                        {{ $child['initial'] }}
                    </div>
                    <div style="flex:1;min-width:0;">
                        <p style="font-size:13px;font-weight:500;color:var(--color-text-primary);margin:0;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                            {{ $child['name'] }}
                        </p>
                        @if($child['pending'] > 0)
                        <span style="font-size:10px;background:#FCEBEB;color:#A32D2D;padding:1px 7px;border-radius:99px;">
                            {{ $child['pending'] }} pending
                        </span>
                        @else
                        <span style="font-size:10px;background:#EAF3DE;color:#3B6D11;padding:1px 7px;border-radius:99px;">
                            Semua clear
                        </span>
                        @endif
                    </div>
                </div>

                {{-- Stats --}}
                <div style="display:flex;flex-direction:column;gap:5px;">
                    <div style="display:flex;justify-content:space-between;font-size:11px;">
                        <span style="color:var(--color-text-secondary);">Pemasukan</span>
                        <span style="color:#3B6D11;font-weight:500;">+Rp {{ number_format($child['income'], 0, ',', '.') }}</span>
                    </div>
                    <div style="display:flex;justify-content:space-between;font-size:11px;">
                        <span style="color:var(--color-text-secondary);">Pengeluaran</span>
                        <span style="color:#A32D2D;font-weight:500;">-Rp {{ number_format($child['expense'], 0, ',', '.') }}</span>
                    </div>
                    <div style="border-top:0.5px solid var(--color-border-tertiary);margin-top:4px;padding-top:5px;display:flex;justify-content:space-between;font-size:12px;">
                        <span style="color:var(--color-text-secondary);font-weight:500;">Saldo</span>
                        <span style="font-weight:500;color:{{ $child['saldo'] >= 0 ? '#3B6D11' : '#A32D2D' }}">
                            Rp {{ number_format($child['saldo'], 0, ',', '.') }}
                        </span>
                    </div>
                </div>

            </div>
            @endforeach
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
