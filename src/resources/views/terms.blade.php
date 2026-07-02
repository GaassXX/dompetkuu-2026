<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Syarat & Ketentuan - DompetKuu</title>
    @php
        $manifest = json_decode(file_get_contents(public_path('build/manifest.json')), true);
        $appCss = $manifest['resources/css/app.css']['file'] ?? '';
    @endphp
    <link rel="stylesheet" href="/build/{{ $appCss }}">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-[#FFFDF9] text-gray-800 antialiased">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 py-16 lg:py-24">
        <a href="{{ route('landing') }}" class="inline-flex items-center gap-2 text-amber-600 hover:text-amber-700 font-semibold mb-8 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16l-4-4m0 0l4-4m-4 4h18"/>
            </svg>
            Kembali ke Beranda
        </a>

        <div class="flex items-center gap-3 mb-8">
            <div class="w-12 h-12 bg-amber-500 rounded-2xl flex items-center justify-center shadow-lg shadow-amber-500/30">
                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <div>
                <h1 class="text-3xl lg:text-4xl font-extrabold text-gray-900 tracking-tight">Syarat & Ketentuan</h1>
                <p class="text-gray-500 mt-1">DompetKuu — Keuangan Keluarga</p>
            </div>
        </div>

        <div class="bg-white rounded-3xl p-8 md:p-10 border border-gray-100 shadow-sm">
            <p class="text-gray-500 text-sm mb-6">Terakhir diperbarui: Januari 2026</p>

            <div class="space-y-10">
                <div>
                    <h2 class="text-xl font-bold text-gray-900 mb-3">1. Penerimaan Ketentuan</h2>
                    <p class="text-gray-600 leading-relaxed">
                        Dengan mendaftar dan menggunakan aplikasi DompetKuu, Anda menyatakan telah membaca, memahami, dan menyetujui seluruh Syarat & Ketentuan ini. Jika Anda tidak setuju dengan salah satu bagian, mohon untuk tidak menggunakan layanan ini.
                    </p>
                </div>

                <div>
                    <h2 class="text-xl font-bold text-gray-900 mb-3">2. Deskripsi Layanan</h2>
                    <p class="text-gray-600 leading-relaxed">
                        DompetKuu adalah aplikasi pencatatan keuangan pribadi dan keluarga berbasis web. Aplikasi ini berfungsi sebagai alat bantu untuk mencatat pemasukan, pengeluaran, mengelola anggaran, dan memantau aktivitas keuangan anggota keluarga. <strong>DompetKuu bukanlah layanan konsultasi keuangan, perbankan, atau investasi.</strong> Segala keputusan finansial sepenuhnya merupakan tanggung jawab pengguna.
                    </p>
                </div>

                <div>
                    <h2 class="text-xl font-bold text-gray-900 mb-3">3. Tanggung Jawab Akun</h2>
                    <ul class="text-gray-600 space-y-2 leading-relaxed">
                        <li>Anda bertanggung jawab penuh atas keamanan kata sandi dan akun Anda.</li>
                        <li>Setiap akun berlaku untuk satu keluarga. Satu akun dapat memiliki beberapa anggota dengan peran (role) Orang Tua dan Anak.</li>
                        <li>Orang Tua bertanggung jawab atas aktivitas yang dilakukan oleh Anak dalam satu akun keluarga.</li>
                        <li>Anda tidak boleh menggunakan akun untuk mengakses data pengguna lain.</li>
                    </ul>
                </div>

                <div>
                    <h2 class="text-xl font-bold text-gray-900 mb-3">4. Penggunaan yang Diizinkan</h2>
                    <p class="text-gray-600 leading-relaxed mb-3">Anda setuju untuk menggunakan DompetKuu dengan cara yang wajar dan tidak:</p>
                    <ul class="text-gray-600 space-y-2 leading-relaxed">
                        <li>Memasukkan data palsu, menyesatkan, atau melanggar hukum</li>
                        <li>Mencoba merusak, meretas, atau mengganggu sistem keamanan aplikasi</li>
                        <li>Menggunakan bot, scraper, atau alat otomatis lainnya untuk mengakses atau mengambil data</li>
                        <li>Menyalahgunakan fitur untuk tujuan di luar yang dimaksudkan</li>
                    </ul>
                </div>

                <div>
                    <h2 class="text-xl font-bold text-gray-900 mb-3">5. Data & Privasi</h2>
                    <p class="text-gray-600 leading-relaxed">
                        Data yang Anda masukkan ke DompetKuu adalah milik Anda. Detail lebih lanjut tentang bagaimana data Anda dikelola dapat dilihat di halaman <a href="{{ route('privacy') }}" class="text-amber-600 hover:text-amber-700 underline">Kebijakan Privasi</a>.
                    </p>
                </div>

                <div>
                    <h2 class="text-xl font-bold text-gray-900 mb-3">6. Batasan Tanggung Jawab</h2>
                    <ul class="text-gray-600 space-y-2 leading-relaxed">
                        <li>DompetKuu menyediakan layanan "sebagaimana adanya" (as-is) tanpa jaminan mutlak bebas dari kesalahan teknis.</li>
                        <li>Kami tidak bertanggung jawab atas kerugian finansial, keputusan investasi, atau konsekuensi lain yang timbul dari penggunaan aplikasi ini.</li>
                        <li>Data backup dilakukan secara berkala, namun pengguna disarankan tetap menyimpan catatan keuangan penting di luar aplikasi.</li>
                    </ul>
                </div>

                <div>
                    <h2 class="text-xl font-bold text-gray-900 mb-3">7. Penghentian Akun</h2>
                    <ul class="text-gray-600 space-y-2 leading-relaxed">
                        <li>Anda dapat menghapus akun kapan saja melalui pengaturan akun. Seluruh data akan dihapus secara permanen.</li>
                        <li>Kami berhak menangguhkan atau menghentikan akses Anda jika ditemukan pelanggaran terhadap Syarat & Ketentuan ini.</li>
                    </ul>
                </div>

                <div>
                    <h2 class="text-xl font-bold text-gray-900 mb-3">8. Perubahan Ketentuan</h2>
                    <p class="text-gray-600 leading-relaxed">
                        Syarat & Ketentuan ini dapat diperbarui sewaktu-waktu. Pengguna akan diberitahu melalui email atau notifikasi dalam aplikasi jika terjadi perubahan signifikan. Penggunaan lanjutan setelah perubahan berarti Anda menyetujui ketentuan yang baru.
                    </p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
