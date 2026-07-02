<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Privasi - DompetKuu</title>
    @php
        $manifest = json_decode(file_get_contents(public_path('build/manifest.json')), true);
        $appCss = $manifest['resources/css/app.css']['file'] ?? '';
    @endphp
    <link rel="stylesheet" href="/build/{{ $appCss }}">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .prose h2 { margin-top: 2rem; margin-bottom: 0.75rem; }
        .prose p { line-height: 1.8; }
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
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
            </div>
            <div>
                <h1 class="text-3xl lg:text-4xl font-extrabold text-gray-900 tracking-tight">Kebijakan Privasi</h1>
                <p class="text-gray-500 mt-1">DompetKuu — Keuangan Keluarga</p>
            </div>
        </div>

        <div class="bg-white rounded-3xl p-8 md:p-10 border border-gray-100 shadow-sm prose prose-gray max-w-none">
            <p class="text-gray-500 text-sm">Terakhir diperbarui: Januari 2026</p>

            <h2 class="text-xl font-bold text-gray-900">1. Pendahuluan</h2>
            <p class="text-gray-600">
                DompetKuu berkomitmen untuk melindungi privasi dan keamanan data pribadi Anda. Kebijakan Privasi ini menjelaskan bagaimana kami mengumpulkan, menggunakan, menyimpan, dan melindungi informasi Anda saat menggunakan aplikasi DompetKuu.
            </p>

            <h2 class="text-xl font-bold text-gray-900">2. Data yang Dikumpulkan</h2>
            <p class="text-gray-600">Kami mengumpulkan data berikut saat Anda mendaftar dan menggunakan DompetKuu:</p>
            <ul class="text-gray-600 space-y-1">
                <li>Nama lengkap dan alamat email — untuk pembuatan dan pengelolaan akun</li>
                <li>Kata sandi yang dienkripsi (bcrypt) — tidak pernah disimpan dalam bentuk teks asli</li>
                <li>Catatan transaksi keuangan — jumlah, kategori, tanggal, dan deskripsi yang Anda masukkan secara manual</li>
                <li>Profil keluarga — nama anggota keluarga dan relasi (Orang Tua / Anak)</li>
                <li>Data sesi login — untuk menjaga akses tetap aman</li>
            </ul>

            <h2 class="text-xl font-bold text-gray-900">3. Cara Data Digunakan</h2>
            <p class="text-gray-600">Data Anda digunakan semata-mata untuk:</p>
            <ul class="text-gray-600 space-y-1">
                <li>Menampilkan dashboard, laporan, dan grafik keuangan Anda</li>
                <li>Menjalankan fitur persetujuan (approval) transaksi anak oleh orang tua</li>
                <li>Mengirim notifikasi terkait aktivitas akun (bukan iklan)</li>
                <li>Meningkatkan kualitas layanan berdasarkan pola penggunaan</li>
            </ul>
            <p class="text-gray-600 font-medium">Kami tidak akan pernah menjual, menyewakan, atau membagikan data Anda kepada pihak ketiga untuk tujuan komersial.</p>

            <h2 class="text-xl font-bold text-gray-900">4. Penyimpanan & Keamanan</h2>
            <ul class="text-gray-600 space-y-1">
                <li>Data disimpan di server dengan proteksi firewall dan akses terbatas</li>
                <li>Kata sandi dienkripsi menggunakan bcrypt — tidak ada yang bisa melihat password asli Anda</li>
                <li>Sistem RBAC (Role-Based Access Control) memastikan setiap anggota hanya bisa mengakses data sesuai perannya</li>
                <li>Tidak ada akses dari pihak ketiga ke database pengguna</li>
            </ul>

            <h2 class="text-xl font-bold text-gray-900">5. Cookie</h2>
            <p class="text-gray-600">
                DompetKuu hanya menggunakan cookie teknis yang diperlukan untuk menjaga sesi login Anda tetap aktif. Kami tidak menggunakan cookie untuk pelacakan iklan atau profiling.
            </p>

            <h2 class="text-xl font-bold text-gray-900">6. Hak Pengguna</h2>
            <p class="text-gray-600">Anda berhak untuk:</p>
            <ul class="text-gray-600 space-y-1">
                <li>Mengakses semua data yang kami simpan tentang Anda</li>
                <li>Memperbaiki data yang tidak akurat</li>
                <li>Menghapus akun dan seluruh data Anda kapan saja</li>
                <li>Mengekspor data transaksi Anda dalam format CSV atau PDF</li>
            </ul>

            <h2 class="text-xl font-bold text-gray-900">7. Kontak</h2>
            <p class="text-gray-600">
                Jika ada pertanyaan tentang kebijakan privasi ini, silakan hubungi pengembang melalui halaman GitHub proyek ini.
            </p>
        </div>
    </div>
</body>
</html>
