<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>dompettkuu — Masuk</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Plus Jakarta Sans', sans-serif; }

        body {
            background: #FFFDF9;
        }

        .animate-float {
            animation: float 4s ease-in-out infinite;
        }
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }

        .animate-float-slow {
            animation: float 5s ease-in-out infinite;
            animation-delay: .5s;
        }

        .blob {
            position: absolute;
            border-radius: 9999px;
            filter: blur(0px);
        }
    </style>
</head>
<body class="min-h-screen">

    <div class="max-w-7xl mx-auto px-6 py-8 lg:py-10">

        {{-- Top bar --}}
        <div class="flex items-center justify-between mb-12 lg:mb-16">
            <div class="flex items-center gap-2">
                <div class="w-9 h-9 rounded-xl flex items-center justify-center"
                     style="background:linear-gradient(135deg,#0D9488,#0F766E);">
                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M21 18v1a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h14a2 2 0 012 2v1h-9a2 2 0 00-2 2v8a2 2 0 002 2h9zm-9-2h10V8H12v8zm4-2.5a1.5 1.5 0 110-3 1.5 1.5 0 010 3z"/>
                    </svg>
                </div>
                <span class="text-lg font-extrabold text-[#1E293B]">dompet<span class="text-[#0D9488]">tkuu</span></span>
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

        {{-- Main grid --}}
        <div class="grid lg:grid-cols-2 gap-10 items-center">

            {{-- LEFT: Form --}}
            <div class="relative z-10 max-w-md">

                <h1 class="text-3xl lg:text-[2.5rem] font-extrabold text-gray-900 leading-tight mb-3">
                    Selamat datang<br>kembali di
                    <span class="text-[#0D9488]">dompettkuu</span>
                </h1>
                <p class="text-gray-500 text-sm mb-8">
                    Masuk untuk mengelola keuanganmu dengan lebih mudah, cepat, dan aman.
                </p>

                {{-- Alert error --}}
                @if(session('error') || $errors->any())
                <div class="bg-red-50 border border-red-200 text-red-600 text-xs rounded-xl px-3.5 py-3 mb-5 flex items-center gap-2">
                    <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
                    </svg>
                    {{ session('error') ?? $errors->first() }}
                </div>
                @endif

                <form method="POST" action="{{ route('auth.login.post') }}" class="space-y-5">
                    @csrf

                    {{-- Email / Phone --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Email atau Nomor HP</label>
                        <div class="relative">
                            <input type="text" name="login" value="{{ old('login') }}"
                                placeholder="Masukkan email atau nomor HP"
                                class="w-full pl-4 pr-11 py-3.5 bg-white border border-gray-200 rounded-xl text-sm text-gray-700 placeholder-gray-300 focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-teal-400 transition {{ $errors->has('login') ? 'border-red-400' : '' }}"
                                required autofocus>
                            <div class="absolute inset-y-0 right-0 pr-3.5 flex items-center pointer-events-none">
                                <svg class="w-4 h-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </div>
                        </div>
                        @error('login')<p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>@enderror
                    </div>

                    {{-- Password --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Kata Sandi</label>
                        <div class="relative">
                            <input type="password" id="password" name="password"
                                placeholder="Masukkan kata sandi"
                                class="w-full pl-4 pr-11 py-3.5 bg-white border border-gray-200 rounded-xl text-sm text-gray-700 placeholder-gray-300 focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-teal-400 transition {{ $errors->has('password') ? 'border-red-400' : '' }}"
                                required>
                            <button type="button" onclick="togglePassword()" class="absolute inset-y-0 right-0 pr-3.5 flex items-center">
                                <svg id="eyeIcon" class="w-4 h-4 text-gray-300 hover:text-gray-500 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </button>
                        </div>
                        @error('password')<p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>@enderror
                    </div>

                    {{-- Remember & forgot --}}
                    <div class="flex items-center justify-between">
                        <label class="flex items-center gap-2 cursor-pointer select-none">
                            <input type="checkbox" name="remember" class="w-4 h-4 rounded border-gray-300 text-teal-500 focus:ring-teal-400">
                            <span class="text-xs text-gray-500 font-medium">Ingat saya</span>
                        </label>
                        <a href="{{ route('password.request') }}" class="text-xs text-teal-600 font-semibold hover:underline">
                            Lupa kata sandi?
                        </a>
                    </div>

                    {{-- Submit --}}
                    <button type="submit"
                        class="w-full text-white font-semibold py-3.5 rounded-xl transition duration-200 text-sm flex items-center justify-center gap-2 shadow-lg shadow-teal-600/20 hover:shadow-teal-600/30 hover:-translate-y-0.5"
                        style="background: linear-gradient(135deg, #0D9488, #0F766E);">
                        Masuk
                    </button>
                </form>

                {{-- Divider --}}
                <div class="relative my-6">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-200"></div>
                    </div>
                    <div class="relative flex justify-center text-xs">
                        <span class="bg-white px-3 text-gray-400 font-medium">atau masuk dengan</span>
                    </div>
                </div>

                {{-- Google --}}
                <a href="{{ route('auth.google') }}"
                   class="w-full flex items-center justify-center gap-3 bg-white border border-gray-200 text-gray-700 font-semibold py-3.5 rounded-xl hover:shadow-md hover:border-gray-300 transition text-sm shadow-sm">
                    <svg class="w-5 h-5" viewBox="0 0 24 24">
                        <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 01-2.2 3.32v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.1z"/>
                        <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                        <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                        <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                    </svg>
                    Masuk dengan Google
                </a>

                <p class="text-center text-gray-500 text-sm mt-6">
                    Belum punya akun?
                    <a href="{{ route('auth.register') }}" class="text-teal-600 font-bold hover:underline inline-flex items-center gap-1">
                        Daftar sekarang
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                        </svg>
                    </a>
                </p>

                {{-- Security note --}}
                <div class="mt-10 bg-teal-50/70 border border-teal-100 rounded-2xl px-4 py-3.5 flex items-center gap-3">
                    <div class="w-9 h-9 bg-teal-500 rounded-xl flex items-center justify-center flex-shrink-0">
                        <svg class="w-4.5 h-4.5 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm-1 14l-4-4 1.41-1.41L11 12.17l5.59-5.59L18 8l-7 7z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-gray-700">Keamanan datamu adalah prioritas kami.</p>
                        <p class="text-[11px] text-gray-400">Semua data dilindungi dengan sistem keamanan berlapis.</p>
                    </div>
                </div>
            </div>

            {{-- RIGHT: Illustration --}}
            <div class="hidden lg:flex relative h-[640px] items-center justify-center">

                {{-- background blobs --}}
                <div class="blob w-[420px] h-[420px]" style="background:radial-gradient(circle, rgba(13,148,136,0.12), transparent 70%); top:0; right:0;"></div>
                <div class="blob w-[320px] h-[320px]" style="background:radial-gradient(circle, rgba(13,148,136,0.10), transparent 70%); bottom:0; left:10%;"></div>

                {{-- phone mockup --}}
                <div class="relative z-10 animate-float">
                    <div class="w-[270px] rounded-[2.5rem] bg-gray-900 p-2.5 shadow-2xl">
                        <div class="bg-white rounded-[2rem] overflow-hidden">
                            {{-- status bar --}}
                            <div class="flex items-center justify-between px-5 pt-3 pb-1 text-[10px] font-semibold text-gray-700">
                                <span>9:41</span>
                                <div class="flex gap-1">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24"><path d="M1 9l2 2c4.97-4.97 13.03-4.97 18 0l2-2C16.93 2.93 7.08 2.93 1 9z"/></svg>
                                </div>
                            </div>

                            {{-- app header --}}
                            <div class="px-5 pt-2 pb-4">
                                <div class="flex items-center gap-1.5 mb-4">
                                    <div class="w-5 h-5 rounded-md flex items-center justify-center" style="background:linear-gradient(135deg,#0D9488,#0F766E);">
                                        <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M21 18v1a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h14a2 2 0 012 2v1h-9a2 2 0 00-2 2v8a2 2 0 002 2h9zm-9-2h10V8H12v8zm4-2.5a1.5 1.5 0 110-3 1.5 1.5 0 010 3z"/>
                                        </svg>
                                    </div>
                                    <span class="text-[11px] font-extrabold text-gray-800">dompettkuu</span>
                                </div>

                                <p class="text-[10px] text-gray-400 mb-1">Saldo Utama</p>
                                <p class="text-xl font-extrabold text-gray-900 mb-4">Rp 2.450.000</p>

                                {{-- quick actions --}}
                                <div class="grid grid-cols-4 gap-2 mb-5">
                                    @foreach([
                                        ['label' => 'Top Up', 'path' => 'M12 4v16m8-8H4'],
                                        ['label' => 'Transfer', 'path' => 'M7 16V4m0 0L3 8m4-4l4 4m6 4v8m0 0l4-4m-4 4l-4-4'],
                                        ['label' => 'Bayar', 'path' => 'M9 7h6m-6 4h6m-7 8h8a2 2 0 002-2V7a2 2 0 00-2-2H8a2 2 0 00-2 2v10a2 2 0 002 2z'],
                                        ['label' => 'Lainnya', 'path' => 'M4 6h16M4 12h16M4 18h16'],
                                    ] as $action)
                                    <div class="flex flex-col items-center gap-1">
                                        <div class="w-9 h-9 rounded-xl flex items-center justify-center" style="background:linear-gradient(135deg,#0D9488,#0F766E);">
                                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $action['path'] }}"/>
                                            </svg>
                                        </div>
                                        <span class="text-[8px] text-gray-500 font-medium">{{ $action['label'] }}</span>
                                    </div>
                                    @endforeach
                                </div>

                                {{-- transactions --}}
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-[10px] font-bold text-gray-700">Transaksi Terakhir</span>
                                    <span class="text-[9px] text-teal-600 font-semibold">Lihat semua</span>
                                </div>

                                <div class="space-y-2.5">
                                    @foreach([
                                        ['title' => 'Transfer ke Andi', 'date' => '24 Mei • 09:15', 'amount' => '- Rp 150.000', 'neg' => true],
                                        ['title' => 'Pembayaran Listrik', 'date' => '24 Mei • 11:50', 'amount' => '- Rp 200.000', 'neg' => true],
                                        ['title' => 'Top Up Saldo', 'date' => '24 Mei • 14:05', 'amount' => '+ Rp 500.000', 'neg' => false],
                                    ] as $t)
                                    <div class="flex items-center justify-between bg-gray-50 rounded-xl px-3 py-2">
                                        <div class="flex items-center gap-2">
                                            <div class="w-7 h-7 rounded-lg flex items-center justify-center {{ $t['neg'] ? 'bg-red-100' : 'bg-teal-100' }}">
                                                <svg class="w-3.5 h-3.5 {{ $t['neg'] ? 'text-red-500' : 'text-teal-600' }}" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M12 2L1 21h22L12 2z"/>
                                                </svg>
                                            </div>
                                            <div>
                                                <p class="text-[9px] font-bold text-gray-700 leading-tight">{{ $t['title'] }}</p>
                                                <p class="text-[8px] text-gray-400">{{ $t['date'] }}</p>
                                            </div>
                                        </div>
                                        <span class="text-[9px] font-bold {{ $t['neg'] ? 'text-red-500' : 'text-teal-600' }}">{{ $t['amount'] }}</span>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- wallet card --}}
                <div class="absolute bottom-10 right-4 z-20 animate-float-slow">
                    <div class="w-28 h-36 rounded-2xl shadow-xl flex flex-col items-center justify-center gap-1"
                         style="background:linear-gradient(160deg,#0F766E,#0D9488);">
                        <svg class="w-9 h-9 text-white/90" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M21 18v1a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h14a2 2 0 012 2v1h-9a2 2 0 00-2 2v8a2 2 0 002 2h9zm-9-2h10V8H12v8zm4-2.5a1.5 1.5 0 110-3 1.5 1.5 0 010 3z"/>
                        </svg>
                    </div>
                    <div class="w-9 h-9 rounded-full bg-amber-400 shadow-lg -mt-4 ml-2 border-4 border-white"></div>
                </div>

                {{-- floating leaf --}}
                <div class="absolute top-16 left-4 z-0 animate-float">
                    <svg class="w-16 h-16 text-teal-200" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M17 8C8 10 5.9 16.17 3.82 21.34l1.89.66.95-2.3c.48.17.98.3 1.34.3C19 20 22 3 22 3c-1 2-8 2.25-13 3.25S2 11.5 2 13.5s1.75 3.75 1.75 3.75C7 8 17 8 17 8z"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Footer --}}
        <div class="mt-14 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-gray-400 border-t border-gray-100 pt-6">
            <p>© {{ date('Y') }} dompettkuu. Semua hak dilindungi.</p>
            <div class="flex items-center gap-4">
                <a href="{{ Route::has('privacy') ? route('privacy') : '#' }}" class="hover:text-teal-600 transition">Privasi</a>
                <span class="text-gray-200">•</span>
                <a href="{{ Route::has('terms') ? route('terms') : '#' }}" class="hover:text-teal-600 transition">Syarat & Ketentuan</a>
            </div>
        </div>
    </div>

    <script>
        function togglePassword() {
            const input = document.getElementById('password');
            const isHidden = input.type === 'password';
            input.type = isHidden ? 'text' : 'password';
        }
    </script>
</body>
</html>
