<?php

namespace Database\Seeders;

use App\Models\PanduanSection;
use Illuminate\Database\Seeder;

class PanduanSectionSeeder extends Seeder
{
    public function run(): void
    {
        $sections = [
            [
                'title' => 'Mulai Menggunakan',
                'icon' => '🚀',
                'order' => 1,
                'content' => '<p class="mb-2">DompetKuu adalah aplikasi pencatatan keuangan keluarga yang dirancang agar kamu sadar akan setiap pengeluaran.</p>'
                    . '<p class="mb-2"><strong>Langkah-langkah:</strong></p>'
                    . '<ol class="list-decimal pl-5 space-y-1">'
                    . '<li>Daftar akun melalui halaman Registrasi</li>'
                    . '<li>Login dengan email dan password</li>'
                    . '<li>Setelah login, kamu akan diarahkan ke Dashboard</li>'
                    . '<li>Gunakan menu di bawah untuk mulai mencatat</li>'
                    . '</ol>',
            ],
            [
                'title' => 'Mencatat Transaksi',
                'icon' => '💰',
                'order' => 2,
                'content' => '<p class="mb-2">Catat setiap pemasukan dan pengeluaran agar arus keuangan terpantau.</p>'
                    . '<p class="mb-2"><strong>Cara mencatat transaksi:</strong></p>'
                    . '<ol class="list-decimal pl-5 space-y-1">'
                    . '<li>Tap tombol <strong>"+"</strong> di halaman utama</li>'
                    . '<li>Pilih jenis: <strong>Pemasukan</strong> atau <strong>Pengeluaran</strong></li>'
                    . '<li>Isi nominal, kategori, dan deskripsi</li>'
                    . '<li>Tap <strong>Simpan</strong></li>'
                    . '</ol>'
                    . '<p class="mt-2 text-amber-600 text-sm">💡 Tips: Catat segera setelah transaksi agar tidak lupa!</p>',
            ],
            [
                'title' => 'Mengatur Anggaran',
                'icon' => '📊',
                'order' => 3,
                'content' => '<p class="mb-2">Atur batas pengeluaran per kategori agar keuangan lebih terkontrol.</p>'
                    . '<p class="mb-2"><strong>Cara mengatur anggaran:</strong></p>'
                    . '<ol class="list-decimal pl-5 space-y-1">'
                    . '<li>Buka menu <strong>Anggaran</strong> dari navigasi bawah</li>'
                    . '<li>Tap <strong>Buat Anggaran</strong></li>'
                    . '<li>Pilih kategori dan tentukan batas nominal</li>'
                    . '<li>Pilih periode (mingguan/bulanan)</li>'
                    . '<li>Tap <strong>Simpan</strong></li>'
                    . '</ol>'
                    . '<p class="mt-2 text-amber-600 text-sm">💡 Kamu akan mendapat notifikasi jika anggaran mendekati batas!</p>',
            ],
            [
                'title' => 'Menabung',
                'icon' => '🏦',
                'order' => 4,
                'content' => '<p class="mb-2">Buat target tabungan untuk mencapai tujuan keuanganmu.</p>'
                    . '<p class="mb-2"><strong>Cara menabung:</strong></p>'
                    . '<ol class="list-decimal pl-5 space-y-1">'
                    . '<li>Buka menu <strong>Tabungan</strong> dari navigasi bawah</li>'
                    . '<li>Tap <strong>Buat Target Baru</strong></li>'
                    . '<li>Isi nama tabungan, target nominal, dan tanggal target</li>'
                    . '<li>Tap <strong>Simpan</strong></li>'
                    . '<li>Setor tabungan secara berkala dengan tap <strong>Setor</strong></li>'
                    . '</ol>'
                    . '<p class="mt-2 text-amber-600 text-sm">💡 Konsisten menyisihkan uang meski kecil, lama-lama jadi besar!</p>',
            ],
            [
                'title' => 'Kelola Keluarga',
                'icon' => '👨‍👩‍👧‍👦',
                'order' => 5,
                'content' => '<p class="mb-2">Fitur ini khusus untuk akun <strong>Orang Tua</strong>. Pantau dan setujui transaksi anak.</p>'
                    . '<p class="mb-2"><strong>Cara mengelola keluarga:</strong></p>'
                    . '<ol class="list-decimal pl-5 space-y-1">'
                    . '<li>Buka menu <strong>Keluarga</strong></li>'
                    . '<li>Tap <strong>Tambah Anggota</strong></li>'
                    . '<li>Masukkan email anak yang sudah terdaftar</li>'
                    . '<li>Anak akan terdaftar sebagai anggota keluarga</li>'
                    . '<li>Setiap transaksi anak perlu <strong>disetujui</strong> oleh Orang Tua</li>'
                    . '</ol>',
            ],
            [
                'title' => 'Pertanyaan Umum',
                'icon' => '❓',
                'order' => 6,
                'content' => '<div class="space-y-3">'
                    . '<div><p class="font-semibold">Apakah data saya aman?</p>'
                    . '<p class="text-sm text-gray-600">Ya, data kamu dilindungi dengan sistem keamanan berlapis. Kami tidak menghubungkan ke rekening bank manapun.</p></div>'
                    . '<div><p class="font-semibold">Berapa biaya menggunakan DompetKuu?</p>'
                    . '<p class="text-sm text-gray-600">DompetKuu gratis digunakan. Tidak ada biaya berlangganan.</p></div>'
                    . '<div><p class="font-semibold">Bisa digunakan di HP dan laptop?</p>'
                    . '<p class="text-sm text-gray-600">Ya, DompetKuu bisa diakses dari perangkat apa pun melalui browser.</p></div>'
                    . '<div><p class="font-semibold">Bagaimana jika lupa password?</p>'
                    . '<p class="text-sm text-gray-600">Klik "Lupa kata sandi" di halaman login, masukkan email, dan ikuti petunjuk reset.</p></div>'
                    . '</div>',
            ],
            [
                'title' => 'Hubungi Kami',
                'icon' => '📧',
                'order' => 7,
                'content' => '<p class="mb-2">Ada pertanyaan atau butuh bantuan? Hubungi kami melalui:</p>'
                    . '<div class="bg-amber-50 rounded-xl p-4 border border-amber-100 mt-3">'
                    . '<p class="font-semibold">📧 Email</p>'
                    . '<a href="mailto:infodompetkuu@gmail.com" class="text-amber-600 font-medium">infodompetkuu@gmail.com</a>'
                    . '<p class="text-sm text-gray-500 mt-1">Kami akan merespon dalam 1x24 jam</p>'
                    . '</div>',
            ],
        ];

        foreach ($sections as $section) {
            PanduanSection::create($section);
        }
    }
}
