<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DompetKuu - Kelola Keuangan Keluarga</title>
    @php
        $manifest = json_decode(file_get_contents(public_path('build/manifest.json')), true);
        $appCss = $manifest['resources/css/app.css']['file'] ?? '';
        $appJs = $manifest['resources/js/app.js']['file'] ?? '';
    @endphp
    <link rel="stylesheet" href="/build/{{ $appCss }}">
    <script defer src="/build/{{ $appJs }}"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; scroll-behavior: smooth; }
        .text-gradient {
            background: linear-gradient(90deg, #0D9488 0%, #0F766E 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
    </style>
</head>
<body class="bg-[#FFFDF9] text-[#1E293B] antialiased overflow-x-hidden selection:bg-[#CCFBF1] selection:text-[#134E4A]">

    <!-- Navbar -->
    <nav class="fixed w-full z-50 transition-all duration-300 bg-white/80 backdrop-blur-md border-b border-[#E2E8F0]" id="navbar">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <div class="flex-shrink-0 flex items-center gap-2 cursor-pointer" onclick="window.scrollTo(0,0)">
                    <div class="w-10 h-10 bg-[#0D9488] rounded-xl flex items-center justify-center shadow-lg shadow-[#0D9488]/30">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 18v1a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h14a2 2 0 012 2v1h-9a2 2 0 00-2 2v8a2 2 0 002 2h9zm-9-2h10V8H12v8zm4-2.5a1.5 1.5 0 110-3 1.5 1.5 0 010 3z"></path>
                        </svg>
                    </div>
                    <span class="font-bold text-2xl tracking-tight text-[#1E293B]">Dompet<span class="text-[#0D9488]">Kuu</span></span>
                </div>

                <div class="hidden md:flex items-center space-x-8">
                    <a href="#home" class="text-[#475569] hover:text-[#0D9488] font-medium transition-colors">Home</a>
                    <a href="#fitur" class="text-[#475569] hover:text-[#0D9488] font-medium transition-colors">Fitur</a>
                </div>

                <div class="hidden md:flex items-center space-x-4">
                    <a href="{{ route('login') }}" class="text-[#475569] font-semibold hover:text-[#0F766E] transition-colors px-4 py-2">
                        Login
                    </a>
                    <a href="{{ route('auth.register') }}" class="bg-[#0D9488] hover:bg-[#0F766E] text-white px-6 py-2.5 rounded-full font-semibold transition-all shadow-md hover:shadow-lg hover:shadow-[#0D9488]/20 transform hover:-translate-y-0.5">
                        Sign Up
                    </a>
                </div>

                <div class="md:hidden flex items-center">
                    <button id="mobile-menu-btn" class="text-[#475569] hover:text-[#0D9488] focus:outline-none">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <div id="mobile-menu" class="hidden md:hidden bg-white border-b border-[#E2E8F0] shadow-xl absolute w-full">
            <div class="px-4 pt-2 pb-6 space-y-2">
                <a href="#home" class="block px-3 py-3 rounded-md text-base font-medium text-[#475569] hover:text-[#0D9488] hover:bg-[#F0FDFA]">Home</a>
                <a href="#fitur" class="block px-3 py-3 rounded-md text-base font-medium text-[#475569] hover:text-[#0D9488] hover:bg-[#F0FDFA]">Fitur</a>
                <div class="mt-4 pt-4 border-t border-[#E2E8F0] flex flex-col gap-3">
                    <a href="{{ route('login') }}" class="w-full text-center block px-4 py-3 rounded-xl text-base font-medium text-[#475569] bg-gray-50 border border-[#F1F5F9]">Login</a>
                    <a href="{{ route('auth.register') }}" class="w-full text-center block px-4 py-3 rounded-xl text-base font-medium text-white bg-[#0D9488] shadow-md">Sign Up</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="home" class="relative min-h-screen flex items-center overflow-hidden">
        <div class="absolute inset-0 -z-10 pointer-events-none">
            <div class="absolute top-1/4 -right-20 w-[500px] h-[500px] rounded-full bg-[#1E293B]/5 blur-3xl"></div>
            <div class="absolute bottom-1/4 -left-20 w-[400px] h-[400px] rounded-full bg-[#2DD4BF]/5 blur-3xl"></div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full pt-32 pb-20 lg:pt-40 lg:pb-28">
            <div class="lg:grid lg:grid-cols-12 lg:gap-16 items-center">
                <div class="lg:col-span-6 text-center lg:text-left mb-16 lg:mb-0">
                    <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-[#CCFBF1] text-[#0F766E] font-medium text-sm mb-8 border border-[#99F6E4]/60">
                        <span class="flex h-2 w-2 rounded-full bg-[#0D9488] animate-pulse"></span>
                        Aplikasi Pencatat Keuangan Pintar
                    </div>
                    <h1 class="text-5xl sm:text-6xl lg:text-7xl font-extrabold text-[#1E293B] leading-[1.1] mb-8 tracking-tight">
                        Kelola Keuangan<br>Keluarga Lebih <span class="text-gradient">Terstruktur</span>
                    </h1>
                    <p class="text-lg sm:text-xl text-[#475569] mb-10 max-w-xl mx-auto lg:mx-0 leading-relaxed">
                        DompetKuu membantu keluarga Anda mencatat, merencanakan, dan memantau arus keuangan dengan mudah dalam satu platform yang terintegrasi.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start">
                        <a href="{{ route('auth.register') }}" class="group bg-[#1E293B] hover:bg-[#334155] text-white px-8 py-4 rounded-full font-bold text-lg transition-all shadow-xl hover:shadow-2xl hover:shadow-[#1E293B]/20 transform hover:-translate-y-1 flex items-center justify-center gap-2">
                            Mulai Sekarang Gratis
                            <svg class="w-5 h-5 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        </a>
                    </div>
                </div>

                <div class="lg:col-span-6 relative">
                    <div class="relative rounded-3xl bg-white shadow-2xl p-6 md:p-8 border border-[#E2E8F0] transform rotate-2 hover:rotate-0 transition-all duration-700 hover:shadow-3xl">
                        <div class="absolute -top-8 -right-8 w-32 h-32 bg-[#CCFBF1] rounded-full blur-2xl opacity-50"></div>
                        <div class="absolute -bottom-8 -left-8 w-40 h-40 bg-[#CCFBF1] rounded-full blur-2xl opacity-50"></div>

                        <div class="bg-white rounded-2xl overflow-hidden border border-[#E2E8F0] shadow-sm relative">
                            <div class="bg-gradient-to-r from-[#F0FDFA] to-white px-6 py-4 border-b border-[#E2E8F0] flex justify-between items-center">
                                <div>
                                    <h3 class="font-bold text-[#1E293B]">Halo, Orang Tua</h3>
                                    <p class="text-xs text-[#64748B]">Dashboard Keuangan</p>
                                </div>
                                <div class="w-10 h-10 bg-[#CCFBF1] rounded-full flex items-center justify-center ring-2 ring-[#99F6E4]">
                                    <span class="text-[#0F766E] font-bold text-sm">OT</span>
                                </div>
                            </div>
                            <div class="p-6 space-y-5">
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="bg-gradient-to-br from-[#F0FDFA] to-white p-5 rounded-2xl border border-[#CCFBF1]/50 shadow-sm">
                                        <p class="text-xs text-[#64748B] mb-1.5 font-medium">Total Saldo</p>
                                        <p class="text-xl font-bold text-[#1E293B]">Rp 4.500.000</p>
                                        <div class="mt-2 w-full h-1.5 bg-[#CCFBF1] rounded-full overflow-hidden">
                                            <div class="w-3/4 h-full bg-[#0D9488] rounded-full"></div>
                                        </div>
                                    </div>
                                    <div class="bg-gradient-to-br from-red-50 to-white p-5 rounded-2xl border border-red-100/50 shadow-sm">
                                        <p class="text-xs text-[#64748B] mb-1.5 font-medium">Pengeluaran Anak</p>
                                        <p class="text-xl font-bold text-red-600">-Rp 150.000</p>
                                        <div class="mt-2 w-full h-1.5 bg-red-100 rounded-full overflow-hidden">
                                            <div class="w-1/3 h-full bg-red-400 rounded-full"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="bg-gradient-to-br from-gray-50 to-white rounded-2xl border border-[#E2E8F0] shadow-sm p-5">
                                    <h4 class="text-sm font-bold text-[#1E293B] mb-4 flex items-center gap-2">
                                        <span class="w-2 h-2 rounded-full bg-[#0D9488]"></span>
                                        Menunggu Persetujuan
                                    </h4>
                                    <div class="flex items-center justify-between p-3 bg-white rounded-xl border border-[#CCFBF1] shadow-sm">
                                        <div class="flex items-center gap-3">
                                            <div class="w-9 h-9 rounded-full bg-gradient-to-br from-[#99F6E4] to-[#5EEAD4] flex items-center justify-center text-sm font-bold text-[#115E59] shadow-sm">A</div>
                                            <div>
                                                <p class="text-sm font-semibold text-[#1E293B]">Beli Buku</p>
                                                <p class="text-xs text-[#64748B]">Anak &middot; Rp 50.000</p>
                                            </div>
                                        </div>
                                        <div class="flex gap-2">
                                            <div class="w-7 h-7 rounded-lg bg-[#F0FDFA] flex items-center justify-center text-[#0F766E] border border-[#99F6E4] cursor-pointer hover:bg-[#CCFBF1] transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                            </div>
                                            <div class="w-7 h-7 rounded-lg bg-red-50 flex items-center justify-center text-red-500 border border-red-200 cursor-pointer hover:bg-red-100 transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Mengapa Manual Section -->
    <section id="mengapa" class="py-24 lg:py-32 bg-[#FFFDF9] relative overflow-hidden">
        <div class="absolute inset-0 -z-10 pointer-events-none">
            <div class="absolute top-1/3 -right-20 w-[400px] h-[400px] rounded-full bg-[#1E293B]/5 blur-3xl"></div>
            <div class="absolute bottom-1/4 -left-20 w-[300px] h-[300px] rounded-full bg-[#2DD4BF]/5 blur-3xl"></div>
        </div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="text-center max-w-4xl mx-auto mb-16 lg:mb-20">
                <span class="inline-block px-4 py-1.5 rounded-full bg-[#CCFBF1] text-[#0F766E] font-semibold text-sm mb-4 border border-[#99F6E4]/60">Mengapa Manual Lebih Baik?</span>
                <h2 class="text-3xl md:text-4xl lg:text-5xl font-extrabold text-[#1E293B] mb-6 tracking-tight leading-tight">
                    Bukan karena otomatis itu salah,<br>
                    tapi karena <span class="text-gradient">sadar itu lebih kuat</span>
                </h2>
                <p class="text-lg text-[#475569] leading-relaxed max-w-3xl mx-auto">
                    Saat kamu mencatat sendiri, otak ikut terlibat dalam setiap transaksi. Riset menunjukkan bahwa pencatatan manual meningkatkan kesadaran finansial lebih efektif dibanding sinkronisasi otomatis — karena kamu benar-benar "merasakan" setiap pengeluaran, bukan sekadar melihatnya di notifikasi. DompetKuu mengubah kebiasaan itu menjadi gaya hidup yang terasa ringan.
                </p>
            </div>

            <div class="text-center mb-12">
                <h3 class="text-2xl md:text-3xl font-bold text-[#1E293B] tracking-tight">
                    Kemana perginya uangmu <span class="text-[#0D9488]">setiap akhir bulan</span>?
                </h3>
                <p class="text-[#64748B] mt-3 text-lg">
                    Bukan karena gajimu kurang. Tapi karena pengeluaran kecil yang "tidak terasa" menumpuk diam-diam.
                </p>
            </div>

            <div class="grid md:grid-cols-3 gap-6 lg:gap-8 max-w-5xl mx-auto mb-16 lg:mb-20">

                <div class="bg-white rounded-3xl p-8 border border-[#E2E8F0] shadow-sm hover:shadow-xl transition-all duration-300 hover:-translate-y-1 text-center">
                    <div class="w-16 h-16 bg-[#F0FDFA] rounded-2xl flex items-center justify-center mx-auto mb-5 ring-1 ring-[#99F6E4]/50">
                        <svg class="w-8 h-8 text-[#0F766E]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
                        </svg>
                    </div>
                    <h4 class="text-xl font-bold text-[#1E293B] mb-3">Pengeluaran "Tidak Terasa"</h4>
                    <p class="text-[#475569] leading-relaxed">
                        Kopi Rp16.000, parkir Rp3.000, snack Rp12.000 — tidak ada yang besar, tapi totalnya bisa mencapai Rp800.000 per bulan.
                    </p>
                </div>

                <div class="bg-white rounded-3xl p-8 border border-[#E2E8F0] shadow-sm hover:shadow-xl transition-all duration-300 hover:-translate-y-1 text-center">
                    <div class="w-16 h-16 bg-red-50 rounded-2xl flex items-center justify-center mx-auto mb-5 ring-1 ring-red-200/50">
                        <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                    </div>
                    <h4 class="text-xl font-bold text-[#1E293B] mb-3">Tagihan yang Terlupakan</h4>
                    <p class="text-[#475569] leading-relaxed">
                        Berlangganan auto-debit tanpa sadar, tagihan lewat jatuh tempo, biaya bulanan yang menumpuk tak terpantau.
                    </p>
                </div>

                <div class="bg-white rounded-3xl p-8 border border-[#E2E8F0] shadow-sm hover:shadow-xl transition-all duration-300 hover:-translate-y-1 text-center">
                    <div class="w-16 h-16 bg-blue-50 rounded-2xl flex items-center justify-center mx-auto mb-5 ring-1 ring-blue-200/50">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                    </div>
                    <h4 class="text-xl font-bold text-[#1E293B] mb-3">Target Tak Tercapai</h4>
                    <p class="text-[#475569] leading-relaxed">
                        Ingin nabung buat liburan atau DP rumah, tapi tidak pernah tahu berapa yang sudah terkumpul dan berapa yang bocor.
                    </p>
                </div>

            </div>

            <div class="max-w-4xl mx-auto bg-gradient-to-r from-[#F0FDFA] to-[#F0FDFA] rounded-3xl p-8 md:p-10 border border-[#CCFBF1]/60 shadow-sm">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 bg-[#CCFBF1] rounded-full flex items-center justify-center flex-shrink-0 mt-1">
                        <span class="text-[#0F766E] text-xl font-bold">!</span>
                    </div>
                    <div>
                        <p class="text-[#475569] text-lg leading-relaxed">
                            Bayangkan: di tanggal 14 kamu cek saldo, dan bingung. Gaji sudah masuk, tapi uang hampir habis. Kamu tidak ingat belanja apa yang <em>seharga</em> itu. Ini bukan soal boros — ini soal tidak pernah benar-benar melihat ke mana uang pergi.
                        </p>
                        <p class="text-[#1E293B] font-semibold mt-4 text-lg">
                            DompetKuu hadir untuk mengubah itu. <span class="text-[#0F766E]">Satu catatan kecil hari ini, selisih besar di akhir bulan.</span>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Fitur Section -->
    <section id="fitur" class="py-24 lg:py-32 bg-white relative">
        <div class="absolute inset-0 -z-10 pointer-events-none">
            <div class="absolute top-1/2 left-0 w-72 h-72 bg-[#1E293B]/5 rounded-full blur-3xl"></div>
            <div class="absolute bottom-0 right-0 w-96 h-96 bg-[#5EEAD4]/5 rounded-full blur-3xl"></div>
        </div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="text-center max-w-3xl mx-auto mb-16 lg:mb-20">
                <span class="inline-block px-4 py-1.5 rounded-full bg-[#CCFBF1] text-[#0F766E] font-semibold text-sm mb-4 border border-[#99F6E4]/60">Fitur Unggulan</span>
                <h2 class="text-3xl md:text-4xl lg:text-5xl font-extrabold text-[#1E293B] mb-6 tracking-tight">Kemudahan dalam Satu Genggaman</h2>
                <p class="text-lg text-[#475569] leading-relaxed">Sistem terintegrasi yang didesain khusus untuk memenuhi kebutuhan pencatatan keuangan modern bagi keluarga Anda.</p>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8">

                <div class="group relative bg-[#FFFDF9] rounded-3xl p-8 border border-[#E2E8F0] shadow-sm hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
                    <div class="absolute inset-0 rounded-3xl bg-gradient-to-br from-[#F0FDFA]/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    <div class="relative">
                        <div class="w-14 h-14 bg-blue-50 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 group-hover:bg-blue-100 transition-all duration-300 ring-1 ring-blue-200/50">
                            <svg class="w-7 h-7 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                        </div>
                        <h3 class="text-xl font-bold text-[#1E293B] mb-3">Pencatatan Mudah</h3>
                        <p class="text-[#475569] leading-relaxed">Catat pemasukan dan pengeluaran harian dengan cepat. Kategorisasi otomatis mempermudah pelacakan arus uang Anda.</p>
                    </div>
                </div>

                <div class="group relative bg-[#FFFDF9] rounded-3xl p-8 border border-[#E2E8F0] shadow-sm hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
                    <div class="absolute inset-0 rounded-3xl bg-gradient-to-br from-[#F0FDFA]/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    <div class="relative">
                        <div class="w-14 h-14 bg-[#1E293B]/5 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 group-hover:bg-[#1E293B]/10 transition-all duration-300 ring-1 ring-[#1E293B]/10">
                            <svg class="w-7 h-7 text-[#1E293B]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                        </div>
                        <h3 class="text-xl font-bold text-[#1E293B] mb-3">Pantau Aktivitas Anak</h3>
                        <p class="text-[#475569] leading-relaxed">Sistem persetujuan (approval) pengeluaran anak. Orang tua dapat memverifikasi transaksi sebelum masuk ke catatan.</p>
                    </div>
                </div>

                <div class="group relative bg-[#FFFDF9] rounded-3xl p-8 border border-[#E2E8F0] shadow-sm hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
                    <div class="absolute inset-0 rounded-3xl bg-gradient-to-br from-[#F0FDFA]/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    <div class="relative">
                        <div class="w-14 h-14 bg-[#F0FDFA] rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 group-hover:bg-[#CCFBF1] transition-all duration-300 ring-1 ring-[#99F6E4]/50">
                            <svg class="w-7 h-7 text-[#0F766E]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"></path></svg>
                        </div>
                        <h3 class="text-xl font-bold text-[#1E293B] mb-3">Manajemen Anggaran</h3>
                        <p class="text-[#475569] leading-relaxed">Atur batas anggaran (budget) mingguan atau bulanan per kategori untuk mengontrol pengeluaran agar tidak berlebih.</p>
                    </div>
                </div>

                <div class="group relative bg-[#FFFDF9] rounded-3xl p-8 border border-[#E2E8F0] shadow-sm hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
                    <div class="absolute inset-0 rounded-3xl bg-gradient-to-br from-[#F0FDFA]/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    <div class="relative">
                        <div class="w-14 h-14 bg-purple-50 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 group-hover:bg-purple-100 transition-all duration-300 ring-1 ring-purple-200/50">
                            <svg class="w-7 h-7 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                        </div>
                        <h3 class="text-xl font-bold text-[#1E293B] mb-3">Laporan & Statistik</h3>
                        <p class="text-[#475569] leading-relaxed">Dapatkan visualisasi grafik arus kas yang mudah dipahami. Analisa keuangan keluarga Anda hingga 6 bulan terakhir.</p>
                    </div>
                </div>

                <div class="group relative bg-[#FFFDF9] rounded-3xl p-8 border border-[#E2E8F0] shadow-sm hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
                    <div class="absolute inset-0 rounded-3xl bg-gradient-to-br from-[#F0FDFA]/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    <div class="relative">
                        <div class="w-14 h-14 bg-rose-50 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 group-hover:bg-rose-100 transition-all duration-300 ring-1 ring-rose-200/50">
                            <svg class="w-7 h-7 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                        </div>
                        <h3 class="text-xl font-bold text-[#1E293B] mb-3">Keamanan Data</h3>
                        <p class="text-[#475569] leading-relaxed">Hak akses tersistemasi (RBAC). Data pribadi tiap anggota terisolasi aman dengan sistem roles yang ketat.</p>
                    </div>
                </div>

                <div class="group relative bg-[#FFFDF9] rounded-3xl p-8 border border-[#E2E8F0] shadow-sm hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
                    <div class="absolute inset-0 rounded-3xl bg-gradient-to-br from-[#F0FDFA]/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    <div class="relative">
                        <div class="w-14 h-14 bg-teal-50 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 group-hover:bg-teal-100 transition-all duration-300 ring-1 ring-teal-200/50">
                            <svg class="w-7 h-7 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                        </div>
                        <h3 class="text-xl font-bold text-[#1E293B] mb-3">Multi Perangkat</h3>
                        <p class="text-[#475569] leading-relaxed">Akses dashboard dari laptop, PC, maupun smartphone. Tampilan akan beradaptasi sempurna dengan perangkat Anda.</p>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-24 relative overflow-hidden bg-[#1E293B]">
        <div class="absolute top-0 right-0 w-96 h-96 bg-[#0D9488] rounded-full blur-3xl opacity-10 transform translate-x-1/3 -translate-y-1/3"></div>
        <div class="absolute bottom-0 left-0 w-80 h-80 bg-[#0D9488] rounded-full blur-3xl opacity-10 transform -translate-x-1/3 translate-y-1/3"></div>
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center">
            <h2 class="text-4xl md:text-5xl font-extrabold text-white mb-6 tracking-tight">Siap Merapikan Keuangan Anda?</h2>
            <p class="text-xl text-[#CBD5E1] mb-12 max-w-2xl mx-auto leading-relaxed">Bergabunglah sekarang dan rasakan kemudahan mencatat serta merencanakan masa depan keuangan keluarga yang lebih baik.</p>
            <a href="{{ route('auth.register') }}" class="group inline-flex items-center gap-2 bg-[#0D9488] hover:bg-[#0F766E] text-white px-10 py-4 rounded-full font-bold text-lg transition-all shadow-xl hover:shadow-[#0D9488]/30 transform hover:-translate-y-1">
                Buat Akun Gratis
                <svg class="w-5 h-5 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
            </a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-white border-t border-[#E2E8F0] py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row justify-between items-center gap-6">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 bg-[#0D9488] rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 18v1a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h14a2 2 0 012 2v1h-9a2 2 0 00-2 2v8a2 2 0 002 2h9zm-9-2h10V8H12v8zm4-2.5a1.5 1.5 0 110-3 1.5 1.5 0 010 3z"></path>
                    </svg>
                </div>
                <span class="font-bold text-xl text-[#1E293B]">Dompet<span class="text-[#0D9488]">Kuu</span></span>
            </div>
            <div class="flex flex-col sm:flex-row items-center gap-2 sm:gap-6">
                <p class="text-[#64748B] text-sm text-center md:text-left">
                    &copy; 2026 DompetKuu. Semua hak dilindungi.
                </p>
                <div class="flex items-center gap-3 text-sm">
                    <a href="{{ route('privacy') }}" class="text-[#94A3B8] hover:text-[#0F766E] transition">Privasi</a>
                    <span class="text-[#CBD5E1]">&bull;</span>
                    <a href="{{ route('terms') }}" class="text-[#94A3B8] hover:text-[#0F766E] transition">Syarat & Ketentuan</a>
                </div>
            </div>
        </div>
    </footer>

    <script>
        const btn = document.getElementById('mobile-menu-btn');
        const menu = document.getElementById('mobile-menu');

        btn.addEventListener('click', () => {
            menu.classList.toggle('hidden');
        });

        const mobileLinks = menu.querySelectorAll('a');
        mobileLinks.forEach(link => {
            link.addEventListener('click', () => {
                menu.classList.add('hidden');
            });
        });

        const navbar = document.getElementById('navbar');
        window.addEventListener('scroll', () => {
            if (window.scrollY > 10) {
                navbar.classList.add('shadow-sm');
                navbar.classList.replace('bg-white/80', 'bg-white/95');
            } else {
                navbar.classList.remove('shadow-sm');
                navbar.classList.replace('bg-white/95', 'bg-white/80');
            }
        });
    </script>
</body>
</html>
