💰 DompetKuu

Sistem Informasi Manajemen Keuangan Keluarga Berbasis Web


Nama: Rizqi Bagas Wicaksono
NIM: 20240801187
Program Studi: Teknik Informatika · Fakultas Ilmu Komputer · Universitas Esa Unggul · 2026

⚠️ Catatan: Proyek ini masih dalam tahap pengembangan awal untuk keperluan UTS. Beberapa fitur direncanakan untuk ditambahkan pada pengembangan lanjutan (TA-2).


Rancangan Sistem Manajemen Keuangan Keluarga (3 Panel)
Sistem ini dirancang dengan arsitektur Multi-Role Access Control, di mana satu database melayani tiga antarmuka yang berbeda sesuai kebutuhan masing-masing pengguna dalam satu unit keluarga.

🎯 Konsep Dasar
Setiap panel bertindak sebagai gerbang masuk (gate) yang berbeda ke dalam sistem yang sama. Hak akses dibatasi agar data keuangan tetap aman, transparan, dan alur kerja antar anggota keluarga teratur.

👥 Peran dan Tanggung Jawab
PanelPenggunaFungsi UtamaAdminTim Pengelola SistemManajemen akun, kategori global, seluruh data keuangan, activity log, dan RBAC.ParentOrang Tua / Kepala KeluargaKelola keuangan pribadi, pantau & setujui transaksi anak, tetapkan anggaran per anak.ChildAnggota Keluarga / AnakCatat pemasukan & pengeluaran pribadi, pantau status approval dari Parent.

🔐 Hirarki Akses

Admin: Memiliki kontrol penuh atas seluruh sistem — user, kategori, transaksi semua anggota, dan activity log.
Parent: Operator keuangan keluarga — input transaksi pribadi (langsung approved) dan validasi transaksi anak.
Child: Kontributor data — input transaksi pribadi dengan status awal pending hingga disetujui Parent.


📋 Struktur Menu Per Panel
🔵 1. Panel Admin (/admin) — System Controller

Dashboard: Statistik keuangan agregat — total pemasukan, pengeluaran, saldo, dan jumlah user.
Grafik Pemasukan vs Pengeluaran: Bar chart perbandingan 6 bulan terakhir dari semua pengguna.
Kategori: Kelola kategori global (dapat dipakai semua user) dan kategori personal.
Pemasukan: Lihat & kelola seluruh data pemasukan semua user, ubah status approval.
Pengeluaran: Lihat & kelola seluruh data pengeluaran semua user, ubah status approval.
Anggaran: Tetapkan batas anggaran per user per kategori (periode mingguan/bulanan).
Pengguna: Tambah, edit, dan nonaktifkan akun; kelola role (Admin/Parent/Child).
Transaction View: Tampilan gabungan pemasukan & pengeluaran semua user dalam satu halaman (read-only).
Activity Log: Riwayat seluruh aktivitas CRUD pengguna di sistem (read-only).

🟢 2. Panel Parent (/parent) — Family Finance Manager

Dashboard: Statistik keuangan pribadi — pemasukan, pengeluaran, saldo diri sendiri, dan saldo gabungan keluarga.
Grafik Arus Keuangan Keluarga: Bar chart 6 bulan terakhir dengan filter: Seluruh Keluarga, Saya Sendiri, atau per nama anak.
Pemasukan: Kelola pemasukan pribadi (status: approved) & lihat pemasukan anak; dapat ubah status approval.
Pengeluaran: Kelola pengeluaran pribadi (status: approved) & lihat pengeluaran anak; dapat ubah status approval.
Anggaran: Tetapkan batas pengeluaran per kategori khusus untuk anak-anaknya.

🟠 3. Panel Child (/child) — Personal Finance Recorder

Pemasukan: Catat pemasukan pribadi; status awal pending, menunggu persetujuan dari Parent.
Pengeluaran: Catat pengeluaran pribadi; status awal pending, menunggu persetujuan dari Parent.


🔄 Alur Approval Workflow
Child input transaksi  →  Status: PENDING
        ↓
Parent melihat di panel Parent
        ↓
   ┌────┴────┐
APPROVED   REJECTED
   ↓           ↓
Masuk       Tidak masuk
statistik   statistik

StatusDibuat OlehMasuk Statistik?KeteranganpendingChild (default)
❌Menunggu persetujuan ParentapprovedAdmin/Parent (default) atau dari pending
✅Dihitung dalam total keuanganrejectedParent (menolak dari pending)
❌Transaksi tidak valid

🛠️ Panduan Implementasi (Teknis)
1. Struktur Database
TabelField UtamaRelasiusersid, name, email, password, role, parent_id, is_active, avatar_urlSelf-referential (parent_id → users.id)categoriesid, name, type (income/expense), is_global, created_byBelongsTo usersincomesid, user_id, category_id, amount, description, date, status, approved_byBelongsTo users, categoriesexpensesid, user_id, category_id, amount, description, date, status, approved_byBelongsTo users, categoriesbudgetsid, user_id, category_id, limit_amount, period (weekly/monthly)BelongsTo users, categories
2. Arsitektur Panel
app/
└── Filament/
    ├── Admin/          → AdminPanelProvider  (/admin)   tema biru
    ├── Parent/         → ParentPanelProvider (/parent)  tema hijau
    └── Child/          → ChildPanelProvider  (/child)   tema oranye
3. Alur Pengembangan

Auth & RBAC: Tiga Panel Provider terpisah dengan middleware autentikasi per role menggunakan Filament Shield.
Panel Admin: CRUD penuh + Transaction View (SQL UNION) + Activity Log (Filament Logger).
Panel Parent: CRUD transaksi pribadi + approval workflow untuk anak + budgeting.
Panel Child: Input pemasukan & pengeluaran dengan status pending secara default.


💡 Prinsip Keamanan

One Account, One Role: Setiap akun hanya memiliki satu role — menghindari kerancuan akses data.
Panel Separation: Setiap panel sepenuhnya terpisah (Provider, Resource, Widget, Middleware) — tidak ada tumpang tindih antar role.
Read-Only for Statistics: Hanya transaksi berstatus approved yang dihitung dalam statistik dan grafik.
Activity Logging: Seluruh aktivitas CRUD dicatat otomatis dan hanya bisa dilihat Admin.


⚙️ Cara Menjalankan
Prasyarat

Docker & Docker Compose terinstall
mkcert untuk SSL lokal

1. Clone Repository
bashgit clone https://github.com/GaassXX/dompetkuu-2026.git
cd dompetkuu-2026
2. Setup SSL
bashmkcert -install
cd nginx/ssl
mkcert dompetkuu.test
3. Tambahkan Domain ke Hosts
bash# Linux / macOS
echo "127.0.0.1 dompetkuu.test" | sudo tee -a /etc/hosts

# Windows — tambahkan baris berikut ke C:\Windows\System32\drivers\etc\hosts
# 127.0.0.1 dompetkuu.test
4. Salin File Environment
bashcp src/.env.example src/.env
Sesuaikan src/.env:
envAPP_NAME="DompetKuu"
APP_URL=https://dompetkuu.test
ASSET_URL=https://dompetkuu.test
DB_DATABASE=dompetkuu
5. Jalankan Docker
bashdocker compose up -d
Container akan otomatis menjalankan:

Install dependensi Composer
Migrasi database
php artisan project:init — migrate fresh + seed + generate RBAC permissions
Symlink storage

6. Buka Aplikasi
PanelURLWarna TemaAdminhttps://dompetkuu.test/admin🔵 BiruParenthttps://dompetkuu.test/parent🟢 HijauChildhttps://dompetkuu.test/child🟠 Oranye

📦 Plugin Filament V3 yang Digunakan
PackageFungsibezhansalleh/filament-shieldRBAC & manajemen permissionz3d0x/filament-loggerActivity log / audit trailhasnayeen/themesTema warna dinamis per panelawcodes/light-switchToggle dark/light modeawcodes/overlookWidget statistik overviewjoaopaulolndev/filament-edit-profileEdit profil dalam paneljoshembling/image-optimizerOptimasi avatar ke format WebPnjxqlus/filament-progressbarProgress bar loading animasidiogogpinto/filament-auth-ui-enhancerPeningkatan tampilan halaman login