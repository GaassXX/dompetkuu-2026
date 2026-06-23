<x-filament-panels::page>
    <div x-data="{ showPassword: false }">
        <div style="background:white;border:1px solid #e5e7eb;border-radius:12px;padding:32px;box-shadow:0 1px 3px rgba(0,0,0,0.05);">
            <form wire:submit="save">

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:24px;margin-bottom:24px;">
                    <div>
                        <label style="display:block;font-size:14px;font-weight:500;color:#374151;margin-bottom:8px;">Nama Lengkap <span style="color:#ef4444;">*</span></label>
                        <input wire:model="name" type="text" placeholder="Masukkan nama lengkap"
                            style="width:100%;padding:10px 16px;border:1px solid #d1d5db;border-radius:8px;font-size:14px;outline:none;box-sizing:border-box;" />
                        @error('name') <p style="font-size:12px;color:#ef4444;margin-top:4px;">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label style="display:block;font-size:14px;font-weight:500;color:#374151;margin-bottom:8px;">Email <span style="color:#ef4444;">*</span></label>
                        <input wire:model="email" type="email" placeholder="Masukkan alamat email"
                            style="width:100%;padding:10px 16px;border:1px solid #d1d5db;border-radius:8px;font-size:14px;outline:none;box-sizing:border-box;" />
                        @error('email') <p style="font-size:12px;color:#ef4444;margin-top:4px;">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:24px;margin-bottom:24px;">
                    <div>
                        <label style="display:block;font-size:14px;font-weight:500;color:#374151;margin-bottom:8px;">Password <span style="color:#ef4444;">*</span></label>
                        <div style="position:relative;">
                            <input wire:model="password" :type="showPassword ? 'text' : 'password'" placeholder="Masukkan password"
                                style="width:100%;padding:10px 48px 10px 16px;border:1px solid #d1d5db;border-radius:8px;font-size:14px;outline:none;box-sizing:border-box;" />
                            <button type="button" @click="showPassword = !showPassword"
                                style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:#9ca3af;">
                                <svg x-show="!showPassword" xmlns="http://www.w3.org/2000/svg" style="width:20px;height:20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                <svg x-show="showPassword" xmlns="http://www.w3.org/2000/svg" style="width:20px;height:20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                            </button>
                        </div>
                        @error('password') <p style="font-size:12px;color:#ef4444;margin-top:4px;">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div style="background:#eff6ff;border:1px solid #bfdbfe;border-radius:8px;padding:16px;display:flex;align-items:flex-start;gap:12px;margin-bottom:24px;">
                    <div style="flex-shrink:0;width:32px;height:32px;background:#dbeafe;border-radius:50%;display:flex;align-items:center;justify-content:center;">
                        <svg xmlns="http://www.w3.org/2000/svg" style="width:18px;height:18px;" fill="none" viewBox="0 0 24 24" stroke="#3b82f6"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <p style="font-size:14px;font-weight:600;color:#1e40af;margin:0;">Informasi Akun</p>
                        <p style="font-size:14px;color:#3b82f6;margin:4px 0 0;">Anggota keluarga yang didaftarkan akan dapat mengakses panel anak untuk mengelola keuangan mereka sendiri.</p>
                    </div>
                </div>

                <div style="display:flex;align-items:center;justify-content:flex-end;gap:12px;padding-top:16px;border-top:1px solid #f3f4f6;">
                    <button type="button" wire:click="cancel"
                        style="padding:10px 20px;background:white;border:1px solid #d1d5db;color:#6b7280;font-size:14px;font-weight:500;border-radius:8px;cursor:pointer;">
                        Cancel
                    </button>
                    <button type="button" wire:click="saveAndCreateAnother"
                        style="padding:10px 20px;background:white;border:1px solid #d1d5db;color:#6b7280;font-size:14px;font-weight:500;border-radius:8px;cursor:pointer;">
                        Create &amp; create another
                    </button>
                    <button type="submit"
                        style="padding:10px 24px;background:#d97706;border:none;color:white;font-size:14px;font-weight:600;border-radius:8px;cursor:pointer;display:flex;align-items:center;gap:8px;">
                        <span>Create</span>
                        <svg xmlns="http://www.w3.org/2000/svg" style="width:16px;height:16px;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                    </button>
                </div>

            </form>
        </div>
    </div>
</x-filament-panels::page>
