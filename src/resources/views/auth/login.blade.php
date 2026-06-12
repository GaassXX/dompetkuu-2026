<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DompetKu — Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Montserrat', sans-serif; }
        .animate-float {
            animation: float 3s ease-in-out infinite;
        }
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-5px); }
        }
        body {
            background: linear-gradient(135deg, #e8f5f0 0%, #f0f7ff 50%, #e8f5f0 100%);
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4 relative overflow-hidden">

    {{-- Background blobs --}}
    <div class="fixed top-0 right-0 w-72 h-72 rounded-full pointer-events-none"
         style="background:radial-gradient(circle, rgba(134,239,172,0.4), transparent 70%);transform:translate(20%,-20%);"></div>
    <div class="fixed bottom-0 left-0 w-64 h-64 rounded-full pointer-events-none"
         style="background:radial-gradient(circle, rgba(134,239,172,0.3), transparent 70%);transform:translate(-20%,20%);"></div>

    {{-- Card --}}
    <div class="w-full max-w-4xl bg-white rounded-2xl shadow-xl overflow-hidden flex relative z-10">

        {{-- Left Panel --}}
        <div class="hidden lg:flex lg:w-[42%] p-8 flex-col justify-between relative overflow-hidden"
             style="background: linear-gradient(160deg, #052e16 0%, #064e3b 45%, #065f46 75%, #166534 100%);">

            {{-- Decorative blobs --}}
            <div class="absolute top-1/3 right-0 w-48 h-48 rounded-full pointer-events-none"
                 style="background:radial-gradient(circle, rgba(52,211,153,0.15), transparent 70%);transform:translateX(30%);"></div>
            <div class="absolute bottom-1/4 left-0 w-40 h-40 rounded-full pointer-events-none"
                 style="background:radial-gradient(circle, rgba(16,185,129,0.1), transparent 70%);"></div>

            <div class="relative z-10">
                {{-- Logo --}}
                <div class="flex items-center gap-3 mb-8">
                    <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center shadow-lg">
                        <svg class="w-5 h-5 text-emerald-600" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M21 18v1a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h14a2 2 0 012 2v1h-9a2 2 0 00-2 2v8a2 2 0 002 2h9zm-9-2h10V8H12v8zm4-2.5a1.5 1.5 0 110-3 1.5 1.5 0 010 3z"/>
                        </svg>
                    </div>
                    <span class="text-white text-lg font-bold">DompetKu</span>
                </div>

                {{-- Badge --}}
                <div class="inline-flex items-center gap-2 mb-5">
                    <div class="w-5 h-5 rounded-full bg-emerald-500/30 flex items-center justify-center">
                        <svg class="w-3 h-3 text-emerald-400" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                        </svg>
                    </div>
                    <span class="text-emerald-400 text-xs font-bold tracking-widest uppercase">Personal Finance</span>
                </div>

                {{-- Headline --}}
                <h1 class="text-white text-3xl font-extrabold leading-tight mb-3">
                    Kendali Penuh<br>Atas Keuangan<br>
                    <span class="text-emerald-400">Anda & Keluarga</span>
                </h1>
                <p class="text-white/60 text-xs leading-relaxed mb-6">
                    Catat pemasukan, kelola pengeluaran, dan pantau anggaran keluarga — semua dalam satu tempat.
                </p>

                {{-- Stats --}}
                <div class="grid grid-cols-2 gap-2 mb-6">
                    <div class="bg-white/10 border border-white/10 rounded-xl p-3">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 bg-emerald-500/30 rounded-lg flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4 text-emerald-400" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-white font-bold text-sm leading-none">100%</p>
                                <p class="text-white/50 text-xs mt-0.5">Data Aman</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white/10 border border-white/10 rounded-xl p-3">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 bg-emerald-500/30 rounded-lg flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4 text-emerald-400" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M20 4H4c-1.11 0-2 .89-2 2v12c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V6c0-1.11-.89-2-2-2zm0 14H4v-6h16v6zm0-10H4V6h16v2z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-white font-bold text-sm leading-none">Gratis</p>
                                <p class="text-white/50 text-xs mt-0.5">Selamanya</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Features --}}
                <div class="space-y-2">
                    @foreach([
                        'Pantau keuangan seluruh anggota keluarga',
                        'Set anggaran & notifikasi otomatis',
                        'Laporan & grafik keuangan bulanan',
                        'Persetujuan transaksi anak oleh orang tua',
                    ] as $f)
                    <div class="flex items-center gap-2">
                        <div class="w-4 h-4 bg-emerald-500 rounded-full flex items-center justify-center flex-shrink-0">
                            <svg class="w-2.5 h-2.5 text-white" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/>
                            </svg>
                        </div>
                        <p class="text-white/75 text-xs">{{ $f }}</p>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="flex items-center gap-2 relative z-10 mt-6">
                <svg class="w-3 h-3 text-white/30" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4z"/>
                </svg>
                <p class="text-white/30 text-xs">© {{ date('Y') }} DompetKu. All rights reserved.</p>
            </div>
        </div>

        {{-- Right Panel --}}
        <div class="w-full lg:w-[58%] p-6 lg:p-10 flex flex-col justify-center bg-white relative">

            <div class="absolute top-0 right-0 w-32 h-32 rounded-full pointer-events-none"
                 style="background:radial-gradient(circle, rgba(167,243,208,0.3), transparent 70%);transform:translate(30%,-30%);"></div>

            <div class="max-w-sm mx-auto w-full">

                {{-- Icon --}}
                <div class="w-16 h-16 bg-emerald-50 rounded-2xl flex items-center justify-center mx-auto mb-5 animate-float shadow-sm">
                    <svg class="w-8 h-8 text-emerald-600" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M21 18v1a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h14a2 2 0 012 2v1h-9a2 2 0 00-2 2v8a2 2 0 002 2h9zm-9-2h10V8H12v8zm4-2.5a1.5 1.5 0 110-3 1.5 1.5 0 010 3z"/>
                    </svg>
                </div>

                <h2 class="text-xl font-bold text-gray-800 text-center mb-1">Selamat Datang Kembali!</h2>
                <p class="text-gray-400 text-xs text-center mb-6">Masuk untuk melanjutkan ke dashboard Anda</p>

                {{-- Error --}}
                @if(session('error') || $errors->any())
                <div class="bg-red-50 border border-red-200 text-red-600 text-xs rounded-xl px-3 py-2.5 mb-4 flex items-center gap-2">
                    <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
                    </svg>
                    {{ session('error') ?? $errors->first() }}
                </div>
                @endif

                <form method="POST" action="{{ route('auth.login.post') }}">
                    @csrf

                    {{-- Email --}}
                    <div class="mb-3">
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5">Email</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="w-4 h-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <input type="email" name="email" placeholder="nama@gmail.com"
                                value="{{ old('email') }}"
                                class="w-full pl-9 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-400 transition {{ $errors->has('email') ? 'border-red-400' : '' }}"
                                required>
                        </div>
                        @error('email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    {{-- Password --}}
                    <div class="mb-4">
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5">Password</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="w-4 h-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                            </div>
                            <input type="password" id="password" name="password"
                                placeholder="Masukkan password"
                                class="w-full pl-9 pr-10 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-400 transition"
                                required>
                            <button type="button" onclick="togglePassword()" class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                <svg class="w-4 h-4 text-gray-300 hover:text-gray-500 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </button>
                        </div>
                        @error('password')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    {{-- Remember & Forgot --}}
                    <div class="flex items-center justify-between mb-5">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="remember" class="w-3.5 h-3.5 rounded border-gray-300 text-emerald-500 focus:ring-emerald-400">
                            <span class="text-xs text-gray-500">Ingat saya</span>
                        </label>
                        <a href="{{ route('password.request') }}" class="text-xs text-emerald-600 font-semibold hover:underline">
                            Lupa password?
                        </a>
                    </div>

                    {{-- Submit --}}
                    <button type="submit"
                        class="w-full text-white font-semibold py-3 rounded-xl transition duration-200 text-sm flex items-center justify-center gap-2 hover:opacity-90"
                        style="background: linear-gradient(135deg, #16a34a, #059669);">
                        Masuk ke Dashboard
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                        </svg>
                    </button>
                </form>

                {{-- Divider --}}
                <div class="flex items-center gap-3 my-4">
                    <div class="flex-1 h-px bg-gray-100"></div>
                    <span class="text-gray-300 text-xs">atau</span>
                    <div class="flex-1 h-px bg-gray-100"></div>
                </div>

                {{-- Register Link --}}
                <a href="{{ route('auth.register') }}"
                   class="w-full flex items-center justify-center gap-2 border border-gray-200 hover:border-emerald-300 hover:bg-emerald-50 text-gray-600 font-semibold py-2.5 rounded-xl transition duration-200 text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                    </svg>
                    Buat Akun Baru
                </a>

                <p class="text-center text-gray-300 text-xs mt-4 flex items-center justify-center gap-1">
                    <svg class="w-3 h-3 text-emerald-400" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4z"/>
                    </svg>
                    Dengan masuk, Anda menyetujui
                    <a href="{{ route('privacy') }}" class="text-emerald-500 hover:underline">kebijakan privasi</a> kami
                </p>
            </div>
        </div>
    </div>

    <script>
        function togglePassword() {
            const input = document.getElementById('password');
            input.type = input.type === 'password' ? 'text' : 'password';
        }
    </script>
</body>
</html>
