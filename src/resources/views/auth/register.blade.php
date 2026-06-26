<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>dompettkuu — Daftar</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Plus Jakarta Sans', sans-serif; }
        body { background: #f7f9f8; }
        .animate-float { animation: float 4s ease-in-out infinite; }
        @keyframes float { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-8px)} }
        .animate-float-slow { animation: float 5s ease-in-out infinite; animation-delay:.6s; }
        .blob { position:absolute; border-radius:9999px; }
    </style>
</head>
<body class="min-h-screen">
    @php
        $google_name  = $google_name ?? session('google_name');
        $google_email = $google_email ?? session('google_email');
        $google_id    = $google_id ?? session('google_id');
    @endphp
    <div class="max-w-7xl mx-auto px-6 py-8 lg:py-10">

        {{-- Top bar --}}
        <div class="flex items-center justify-between mb-8">
            <div class="flex items-center gap-2">
                <div class="w-9 h-9 rounded-xl flex items-center justify-center" style="background:linear-gradient(135deg,#059669,#10b981);">
                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M21 18v1a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h14a2 2 0 012 2v1h-9a2 2 0 00-2 2v8a2 2 0 002 2h9zm-9-2h10V8H12v8zm4-2.5a1.5 1.5 0 110-3 1.5 1.5 0 010 3z"/>
                    </svg>
                </div>
                <span class="text-lg font-extrabold text-gray-800">dompet<span class="text-emerald-600">tkuu</span></span>
            </div>
            <a href="{{ Route::has('help') ? route('help') : '#' }}"
               class="flex items-center gap-2 border border-gray-200 bg-white rounded-full px-4 py-2 text-xs font-semibold text-gray-600 shadow-sm hover:shadow transition">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 18v-6a9 9 0 0118 0v6"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 19a2 2 0 01-2 2h-1a2 2 0 01-2-2v-3a2 2 0 012-2h3v5zM3 19a2 2 0 002 2h1a2 2 0 002-2v-3a2 2 0 00-2-2H3v5z"/>
                </svg>
                Butuh bantuan?
            </a>
        </div>

        <div class="grid lg:grid-cols-2 gap-10 items-start">

            {{-- LEFT: form --}}
            <div class="relative z-10 max-w-lg">

                <h1 class="text-2xl lg:text-[1.85rem] font-extrabold text-gray-900 leading-snug mb-2">
                    Buat akun baru di <span class="text-emerald-600">dompettkuu</span>
                </h1>
                <p class="text-gray-500 text-sm mb-6">
                    Daftar sekarang dan nikmati berbagai kemudahan transaksi dalam satu aplikasi finansial yang cerdas.
                </p>

                @if($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-600 text-xs rounded-xl px-3.5 py-3 mb-5 flex items-center gap-2">
                    <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
                    </svg>
                    {{ $errors->first() }}
                </div>
                @endif

                <form method="POST" action="{{ route('auth.register.post') }}">
                    @csrf

                    @if($google_id)
                    <input type="hidden" name="google_id" value="{{ $google_id }}">
                    @endif

                    {{-- Daftar sebagai --}}
                    <label class="block text-sm font-semibold text-gray-700 mb-2.5">Daftar Sebagai</label>
                    <div class="grid grid-cols-2 gap-3 mb-5">

                        <label class="cursor-pointer block">
                            <input type="radio" name="account_type" value="parent" class="sr-only peer"
                                {{ old('account_type', 'parent') === 'parent' ? 'checked' : '' }}>
                            <div class="relative flex items-center gap-3 border-2 border-gray-200 peer-checked:border-emerald-500 peer-checked:bg-emerald-50/60 rounded-2xl p-3 transition hover:border-emerald-300">
                                <div class="w-5 h-5 rounded-full bg-emerald-500 flex items-center justify-center absolute top-2 right-2 check-icon hidden">
                                    <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                                </div>
                                <div class="w-11 h-11 rounded-full flex items-center justify-center flex-shrink-0" style="background:linear-gradient(135deg,#a7f3d0,#6ee7b7);">
                                    <svg class="w-5.5 h-5.5 text-emerald-700" fill="currentColor" viewBox="0 0 24 24" style="width:22px;height:22px;">
                                        <path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/>
                                    </svg>
                                </div>
                                <div class="text-left">
                                    <p class="text-sm font-bold text-gray-800 leading-tight">Orangtua</p>
                                    <p class="text-[11px] text-gray-400 leading-tight">Kelola keuangan keluarga</p>
                                </div>
                            </div>
                        </label>

                        <label class="cursor-pointer block">
                            <input type="radio" name="account_type" value="independent" class="sr-only peer"
                                {{ old('account_type') === 'independent' ? 'checked' : '' }}>
                            <div class="relative flex items-center gap-3 border-2 border-gray-200 peer-checked:border-emerald-500 peer-checked:bg-emerald-50/60 rounded-2xl p-3 transition hover:border-emerald-300">
                                <div class="w-5 h-5 rounded-full bg-emerald-500 flex items-center justify-center absolute top-2 right-2 check-icon hidden">
                                    <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                                </div>
                                <div class="w-11 h-11 rounded-full flex items-center justify-center flex-shrink-0" style="background:linear-gradient(135deg,#bae6fd,#7dd3fc);">
                                    <svg class="text-sky-700" fill="currentColor" viewBox="0 0 24 24" style="width:22px;height:22px;">
                                        <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                                    </svg>
                                </div>
                                <div class="text-left">
                                    <p class="text-sm font-bold text-gray-800 leading-tight">Pribadi</p>
                                    <p class="text-[11px] text-gray-400 leading-tight">Transaksi harian praktis</p>
                                </div>
                            </div>
                        </label>
                    </div>
                    @error('account_type')<p class="text-red-500 text-xs -mt-3 mb-4">{{ $message }}</p>@enderror

                    {{-- Nama --}}
                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Nama Lengkap</label>
                        <input type="text" name="name" value="{{ old('name', $google_name ?? '') }}"
                            placeholder="Masukkan nama lengkap"
                            class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl text-sm placeholder-gray-300 focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400 transition {{ $errors->has('name') ? 'border-red-400' : '' }}"
                            required autofocus {{ $google_name ? 'readonly' : '' }}>
                        @error('name')<p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>@enderror
                    </div>

                    {{-- Email --}}
                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Email</label>
                        <input type="email" name="email" value="{{ old('email', $google_email ?? '') }}"
                            placeholder="nama@email.com"
                            class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl text-sm placeholder-gray-300 focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400 transition {{ $errors->has('email') ? 'border-red-400' : '' }}"
                            required {{ $google_email ? 'readonly' : '' }}>
                        @if($google_email)
                        <p class="text-emerald-600 text-xs mt-1 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
                            Terhubung dengan Google
                        </p>
                        @endif
                        @error('email')<p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>@enderror
                    </div>

                    {{-- Password (sembunyi kalau register via Google) --}}
                    @unless($google_id)
                    <div class="grid grid-cols-2 gap-3 mb-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Kata Sandi</label>
                            <div class="relative">
                                <input type="password" id="password" name="password" placeholder="Min 8 karakter"
                                    class="w-full px-4 py-3 pr-9 bg-white border border-gray-200 rounded-xl text-sm placeholder-gray-300 focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400 transition {{ $errors->has('password') ? 'border-red-400' : '' }}"
                                    required>
                                <button type="button" onclick="togglePass('password')" class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                    <svg class="w-4 h-4 text-gray-300 hover:text-gray-500 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </button>
                            </div>
                            @error('password')<p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Konfirmasi Kata Sandi</label>
                            <div class="relative">
                                <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Ulangi sandi"
                                    class="w-full px-4 py-3 pr-9 bg-white border border-gray-200 rounded-xl text-sm placeholder-gray-300 focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400 transition"
                                    required>
                                <button type="button" onclick="togglePass('password_confirmation')" class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                    <svg class="w-4 h-4 text-gray-300 hover:text-gray-500 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </button>
                            </div>
                            @error('password_confirmation')<p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>@enderror
                        </div>
                    </div>
                    @endunless

                    {{-- Submit --}}
                    <button type="submit"
                        class="w-full text-white font-semibold py-3.5 rounded-xl transition duration-200 text-sm flex items-center justify-center gap-2 shadow-lg shadow-emerald-600/20 hover:shadow-emerald-600/30 hover:-translate-y-0.5"
                        style="background: linear-gradient(135deg, #059669, #10b981);">
                        Daftar Sekarang
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h11m5-4v8a2 2 0 01-2 2h-5"/>
                        </svg>
                    </button>
                </form>

                {{-- Divider --}}
                <div class="relative my-6">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-200"></div>
                    </div>
                    <div class="relative flex justify-center text-xs">
                        <span class="bg-white px-3 text-gray-400 font-medium">atau daftar dengan</span>
                    </div>
                </div>

                {{-- Google --}}
                <a href="{{ route('auth.google', ['action' => 'register']) }}"
                   class="w-full flex items-center justify-center gap-3 bg-white border border-gray-200 text-gray-700 font-semibold py-3.5 rounded-xl hover:shadow-md hover:border-gray-300 transition text-sm shadow-sm">
                    <svg class="w-5 h-5" viewBox="0 0 24 24">
                        <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 01-2.2 3.32v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.1z"/>
                        <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                        <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                        <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                    </svg>
                    Daftar dengan Google
                </a>

                <div class="mt-5 bg-gray-50 border border-gray-100 rounded-2xl px-4 py-3.5 flex items-center justify-between">
                    <span class="text-xs text-gray-500">
                        Sudah punya akun?
                        <a href="{{ route('auth.login') }}" class="text-emerald-600 font-bold hover:underline">Masuk di sini</a>
                    </span>
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                    </svg>
                </div>
            </div>

            {{-- RIGHT: illustration --}}
            <div class="hidden lg:flex relative h-[440px] items-center justify-center mt-2">
                <div class="blob w-[360px] h-[360px]" style="background:radial-gradient(circle, rgba(16,185,129,0.10), transparent 70%); top:-10%; right:0;"></div>

                <div class="relative z-10 w-full max-w-md rounded-3xl overflow-hidden shadow-xl" style="background: linear-gradient(160deg,#f1f5f3,#e9f3ef);">
                    <div class="relative h-[300px] flex items-center justify-center px-8">

                        <div class="absolute top-5 right-5 bg-white rounded-2xl shadow-lg px-3.5 py-2.5 flex items-center gap-2 animate-float z-20">
                            <div class="w-7 h-7 bg-amber-100 rounded-full flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4 text-amber-500" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4z"/>
                                </svg>
                            </div>
                            <span class="text-[11px] font-bold text-gray-700">Cashback Hingga 50%</span>
                        </div>

                        <div class="absolute right-12 top-1/2 -translate-y-2 flex flex-col-reverse items-center z-10">
                            @for ($i = 0; $i < 5; $i++)
                            <div class="w-14 h-3.5 rounded-full -mb-1.5 border-2 border-amber-300" style="background: linear-gradient(180deg,#fde68a,#f59e0b);"></div>
                            @endfor
                        </div>

                        <div class="absolute right-6 bottom-6 w-24 h-16 rounded-xl shadow-md bg-white border border-gray-100 rotate-6 z-0"></div>

                        <div class="relative z-10 animate-float-slow">
                            <div class="w-28 h-36 rounded-2xl shadow-2xl flex flex-col items-center justify-center gap-2 relative" style="background: linear-gradient(160deg,#065f46,#059669);">
                                <svg class="w-9 h-9 text-white/90" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M21 18v1a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h14a2 2 0 012 2v1h-9a2 2 0 00-2 2v8a2 2 0 002 2h9zm-9-2h10V8H12v8zm4-2.5a1.5 1.5 0 110-3 1.5 1.5 0 010 3z"/>
                                </svg>
                                <div class="absolute top-1/2 -right-1.5 w-3 h-6 bg-amber-400 rounded-sm -translate-y-1/2 shadow"></div>
                            </div>
                        </div>
                    </div>

                    <div class="px-8 pb-6 -mt-4 relative z-20">
                        <div class="bg-white rounded-2xl shadow-lg px-3.5 py-2.5 flex items-center gap-2.5 w-fit">
                            <div class="w-8 h-8 bg-emerald-100 rounded-xl flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4 text-emerald-600" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm-1 14l-4-4 1.41-1.41L11 12.17l5.59-5.59L18 8l-7 7z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold text-gray-700 leading-tight">AMAN &amp; TERPERCAYA</p>
                                <p class="text-[10px] text-gray-400 leading-tight">Lisensi BI Resmi</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Footer --}}
        <div class="mt-12 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-gray-400 border-t border-gray-100 pt-6">
            <p>© {{ date('Y') }} dompettkuu. Semua hak dilindungi.</p>
            <div class="flex items-center gap-4">
                <a href="{{ Route::has('privacy') ? route('privacy') : '#' }}" class="hover:text-emerald-600 transition">Privasi</a>
                <span class="text-gray-200">•</span>
                <a href="{{ Route::has('terms') ? route('terms') : '#' }}" class="hover:text-emerald-600 transition">Syarat & Ketentuan</a>
            </div>
        </div>
    </div>

    <script>
        function togglePass(id) {
            const input = document.getElementById(id);
            input.type = input.type === 'password' ? 'text' : 'password';
        }
        function syncChecks() {
            document.querySelectorAll('input[name="account_type"]').forEach(function (radio) {
                const icon = radio.closest('label').querySelector('.check-icon');
                icon.classList.toggle('hidden', !radio.checked);
            });
        }
        document.querySelectorAll('input[name="account_type"]').forEach(r => r.addEventListener('change', syncChecks));
        syncChecks();
    </script>
</body>
</html>
