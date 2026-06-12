@auth
<div style="padding:12px 16px;border-top:1px solid var(--color-border-tertiary);margin-top:auto;">
    <a href="{{ \App\Filament\Pages\Auth\EditProfile::getUrl() }}"
       style="display:flex;align-items:center;gap:10px;text-decoration:none;padding:6px 8px;border-radius:10px;transition:background 0.2s;"
       onmouseover="this.style.background='var(--color-background-secondary)'"
       onmouseout="this.style.background='transparent'">
        <div style="width:36px;height:36px;border-radius:50%;background:var(--color-primary-100);
                    display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <span style="font-size:13px;font-weight:600;color:var(--color-primary-600);">
                {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
            </span>
        </div>
        <div style="flex:1;min-width:0;">
            <p style="font-size:13px;font-weight:600;color:var(--color-text-primary);margin:0;
                      white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                {{ auth()->user()->name }}
            </p>
            <p style="font-size:11px;margin:0;color:var(--color-text-secondary);">
                 Orang Tua
            </p>
        </div>
        <svg style="width:14px;height:14px;color:var(--color-text-secondary);flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
    </a>
</div>
@endauth
