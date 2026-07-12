# BUSINESS REQUIREMENT DOCUMENT (BRD)
## DOMPETKUU
### Sistem Informasi Manajemen Keuangan Keluarga dan Pribadi Berbasis Web

---

## 1. Ringkasan Eksekutif

Dokumen Business Requirement Document (BRD) ini menjelaskan kebutuhan bisnis dan kebutuhan sistem untuk pengembangan aplikasi **DompetKuu**, yaitu platform berbasis web yang digunakan untuk membantu proses pencatatan dan pengelolaan keuangan keluarga maupun pribadi secara digital. Sistem ini dirancang untuk memudahkan setiap anggota keluarga dalam mencatat pemasukan dan pengeluaran, memantau anggaran belanja, serta memperoleh informasi terkait kondisi keuangan secara real-time.

Selain membantu anak dalam mengelola keuangan pribadi di bawah pengawasan orang tua, sistem juga mendukung pengguna individu (personal) yang ingin mencatat keuangan pribadi secara mandiri. Orang tua dapat memverifikasi transaksi anak, mengatur anggaran per kategori, serta memantau aktivitas keuangan keluarga secara lebih terstruktur. Dengan adanya sistem ini, pencatatan keuangan yang sebelumnya dilakukan secara manual atau tersebar di berbagai catatan dapat dilakukan dalam satu platform yang terintegrasi sehingga meningkatkan transparansi dan efisiensi pengelolaan keuangan.

---

## 2. Latar Belakang Dan Justifikasi Bisnis

### 2.1 Konteks

DompetKuu merupakan aplikasi manajemen keuangan yang melayani kebutuhan keluarga dan individu dalam mencatat pemasukan, pengeluaran, dan tabungan secara digital. Saat ini pencatatan keuangan masih dilakukan secara manual melalui buku catatan atau aplikasi catatan sederhana sehingga orang tua sulit memantau pengeluaran anak, anak tidak memiliki batasan anggaran yang jelas, dan individu tidak memiliki platform terpusat untuk mengelola keuangan pribadi.

### 2.2 Permasalahan

- Data pemasukan dan pengeluaran tersebar pada banyak catatan manual atau aplikasi terpisah.
- Sulit melakukan rekapitulasi keuangan secara cepat dan akurat.
- Orang tua tidak memiliki kontrol terhadap pengeluaran anak secara real-time.
- Anak tidak memiliki panduan anggaran (budget) yang jelas dalam membelanjakan uang.
- Tidak tersedia media yang terpusat untuk memantau kondisi keuangan keluarga.
- Proses persetujuan pengeluaran anak masih dilakukan secara lisan tanpa dokumentasi.
- Individu tidak memiliki platform khusus untuk mencatat keuangan pribadi secara mandiri.

### 2.3 Solusi yang Diusulkan

Membangun sistem berbasis web untuk mendukung proses pencatatan dan pengelolaan keuangan secara terintegrasi dengan fitur:
- Registrasi dan login pengguna (termasuk Google OAuth).
- Pencatatan pemasukan (income) secara digital dengan sumber transaksi.
- Pencatatan pengeluaran (expense) dengan sistem persetujuan orang tua (khusus anak).
- Pengelolaan anggaran (budget) per kategori oleh orang tua untuk anak, atau mandiri untuk personal.
- Monitoring status transaksi (pending/approved/rejected).
- Dashboard analitik keuangan.
- Kelola tabungan (saving) dengan target dan riwayat deposit.
- Request edit/hapus transaksi oleh anak ke orang tua (transaction request).
- Notifikasi real-time dalam aplikasi.
- Halaman mobile web yang responsif untuk akses smartphone.
- REST API untuk integrasi pihak ketiga.
- Landing page, kebijakan privasi, dan syarat & ketentuan.
- Ekspor laporan ke PDF dan Excel.

Manfaat yang diharapkan dari sistem ini antara lain:
- Mempermudah pencatatan keuangan.
- Mengurangi risiko kesalahan pencatatan transaksi.
- Meningkatkan kontrol orang tua terhadap pengeluaran anak.
- Membantu anak belajar mengelola keuangan dengan batasan anggaran.
- Menyediakan platform mandiri bagi individu dalam mengelola keuangan pribadi.
- Meningkatkan efisiensi pengelolaan keuangan keluarga.

---

## 3. Tujuan Bisnis

- Menyediakan platform pencatatan keuangan yang terstruktur dan mudah digunakan.
- Membantu pengelolaan data pemasukan, pengeluaran, dan tabungan secara terpusat.
- Mempermudah orang tua dalam memantau dan menyetujui transaksi anak.
- Mendukung efisiensi pengelolaan keuangan melalui sistem anggaran dan alert.
- Membantu pengguna dalam mencapai target tabungan.
- Menyediakan akses mobile web yang responsif.
- Menyediakan API untuk integrasi dan pengembangan lanjutan.

---

## 4. Ruang Lingkup

### 4.1 Dashboard Personal

- Registrasi akun pengguna mandiri.
- Login dan logout (termasuk Google OAuth).
- Mencatat pemasukan pribadi (auto-approved).
- Mencatat pengeluaran pribadi (auto-approved).
- Mengelola anggaran per kategori secara mandiri.
- Mengelola tabungan pribadi dengan riwayat deposit.
- Melihat riwayat transaksi.
- Melihat dashboard statistik keuangan pribadi.
- Reset password via kode 6-digit email.
- Akses penuh melalui mobile web dan desktop.

### 4.2 Dashboard Anak (Child)

- Registrasi akun anggota keluarga (anak) oleh orang tua.
- Login dan logout.
- Mencatat pemasukan pribadi (auto-approved).
- Mencatat pengeluaran pribadi (pending approval orang tua).
- Melihat status transaksi (pending/approved/rejected).
- Melihat sisa anggaran per kategori.
- Mengelola tabungan pribadi dengan riwayat deposit.
- Melihat riwayat transaksi.
- Mengajukan request edit/hapus transaksi ke orang tua.
- Menerima notifikasi persetujuan.

### 4.3 Dashboard Orang Tua (Parent)

- Manajemen data anggota keluarga (anak).
- Mencatat pemasukan pribadi (auto-approved).
- Mencatat pengeluaran pribadi (auto-approved).
- Verifikasi pengeluaran anak (approve/reject).
- Verifikasi request edit/hapus transaksi anak.
- Pengelolaan anggaran per kategori untuk anak.
- Monitoring keuangan keluarga secara keseluruhan.
- Monitoring keuangan masing-masing anak.
- Ekspor laporan keuangan ke PDF dan Excel.
- Melihat notifikasi dan approval queue.

### 4.4 Dashboard Admin

- Manajemen data pengguna (aktif/nonaktifkan, atur roles).
- Manajemen kategori transaksi global (termasuk aktivasi/deaktivasi kategori).
- Manajemen panduan/FAQ (panduan sections).
- Monitoring activity log sistem (audit trail).

### 4.5 Pengelolaan Transaksi

- Pencatatan pemasukan dengan kategori dan sumber transaksi (Cash, Transfer Bank, E-Wallet, Lainnya).
- Pencatatan pengeluaran dengan kategori dan sumber transaksi.
- Sistem persetujuan (approval workflow) untuk pengeluaran anak.
- Status transaksi: pending, approved, rejected.
- Riwayat perubahan status transaksi.
- Request edit/hapus transaksi oleh anak dengan approval orang tua.
- Penyimpanan data dan riwayat transaksi.

### 4.6 Pelaporan Dasar

- Ringkasan jumlah pemasukan dan pengeluaran.
- Riwayat transaksi per periode.
- Ringkasan saldo keuangan.
- Grafik perbandingan pemasukan dan pengeluaran (1, 3, 6, 12 bulan).
- Ekspor laporan ke PDF.
- Ekspor laporan ke Excel.

### 4.7 Halaman Publik

- Landing page informasi aplikasi dengan FAQ.
- Halaman kebijakan privasi.
- Halaman syarat dan ketentuan.
- Halaman panduan penggunaan.

### 4.8 API

- REST API untuk autentikasi (login, register, logout, profile).
- REST API untuk transaksi (CRUD).
- REST API untuk kategori.
- REST API untuk dashboard (stats, chart).

### 4.9 Ruang Lingkup yang Tidak Termasuk

- Integrasi dengan rekening bank atau e-wallet secara otomatis.
- Aplikasi mobile Android atau iOS native (aplikasi berbasis web yang responsif).
- Sistem akuntansi dan pembukuan lengkap (neraca, laba rugi).
- Multi-mata uang asing.
- Integrasi Telegram Bot.

---

## 5. Stakeholders dan Pengguna

| Stakeholder                | Peran dan Tanggung Jawab |
|----------------------------|-------------------------|
| Personal                   | Mencatat pemasukan dan pengeluaran pribadi, mengelola tabungan, mengelola anggaran mandiri, melihat laporan keuangan pribadi. |
| Anak (Child)               | Mencatat pemasukan dan pengeluaran pribadi, mengelola tabungan, memantau anggaran, mengajukan request edit/hapus transaksi. |
| Orang Tua (Parent)         | Mencatat keuangan pribadi, memverifikasi pengeluaran anak, memverifikasi request transaksi anak, mengatur anggaran anak, memantau keuangan keluarga, mengekspor laporan. |
| Admin                      | Mengelola data pengguna dan kategori transaksi global, mengelola panduan/FAQ, memonitor activity log sistem (audit trail). |

---

## 6. Persyaratan Fungsional

### 6.1 Website Pengguna (Personal / Child / Parent)

- Registrasi akun pengguna (termasuk Google OAuth).
- Login dan logout pengguna.
- Mencatat pemasukan dengan kategori, jumlah, dan sumber transaksi.
- Mencatat pengeluaran dengan kategori, jumlah, dan sumber transaksi.
- Melihat status transaksi (pending/approved/rejected) — khusus Child.
- Melihat riwayat transaksi.
- Melihat dashboard statistik keuangan pribadi.
- Melihat sisa anggaran per kategori.
- Mengelola tabungan dan target dengan riwayat deposit.
- Reset password menggunakan kode verifikasi 6-digit via email.

### 6.2 Manajemen Transaksi

- Pencatatan pemasukan dengan kategori dan sumber transaksi.
- Pencatatan pengeluaran dengan kategori dan sumber transaksi.
- Status transaksi meliputi:
  - Pending (menunggu verifikasi) — khusus pengeluaran Child.
  - Approved (disetujui) — otomatis untuk Parent dan Personal.
  - Rejected (ditolak) — khusus pengeluaran Child.
- Riwayat perubahan status transaksi.
- Sumber transaksi: Cash, Transfer Bank, E-Wallet, Lainnya.

### 6.3 Approval Workflow

- Orang tua menerima notifikasi pengeluaran anak yang pending.
- Orang tua dapat menyetujui (approve) pengeluaran anak.
- Orang tua dapat menolak (reject) pengeluaran anak.
- Orang tua menerima notifikasi request edit/hapus transaksi dari anak.
- Orang tua dapat menyetujui atau menolak request edit/hapus transaksi anak.
- Hanya transaksi approved yang masuk ke dalam perhitungan statistik.
- Anak (atau personal) menerima notifikasi status transaksi.

### 6.4 Manajemen Anggaran

- Orang tua dapat membuat anggaran per kategori untuk anak.
- Pengguna personal dapat membuat anggaran per kategori secara mandiri.
- Penentuan periode anggaran (mingguan/bulanan).
- Anak dapat melihat sisa anggaran di dashboard.
- Anak tidak dapat membuat, mengedit, atau menghapus anggaran sendiri (kecuali personal).

### 6.5 Dashboard Admin

- Manajemen data pengguna (aktif/nonaktifkan, atur roles).
- Manajemen kategori transaksi global (termasuk aktivasi/deaktivasi).
- Manajemen panduan/FAQ (panduan sections).
- Activity log sistem (audit trail seluruh aktivitas CRUD).

### 6.6 Pelaporan

Data Transaksi:
- Riwayat transaksi pengguna.
- Riwayat transaksi per kategori.
- Rekap jumlah pemasukan dan pengeluaran.
- Ekspor ke PDF.
- Ekspor ke Excel.

Data Keuangan:
- Ringkasan saldo keuangan.
- Riwayat transaksi berdasarkan periode tertentu.
- Grafik perbandingan pemasukan dan pengeluaran (opsi 1, 3, 6, 12 bulan).

### 6.7 Notifikasi Dalam Aplikasi

- Notifikasi transaksi pending untuk orang tua.
- Notifikasi transaksi diproses (approved/rejected) untuk anak.
- Notifikasi transaksi berhasil dicatat.
- Notifikasi budget mendekati batas.
- Notifikasi target tabungan tercapai.
- Notifikasi perubahan password.

### 6.8 Mobile Web

- Tampilan yang dioptimalkan untuk perangkat smartphone.
- Navigasi berbasis bottom navigation.
- Halaman mobile: dashboard, tambah transaksi, riwayat, profil, pengaturan akun, keluarga, anggaran, laporan, tabungan, panduan, keamanan, persetujuan, notifikasi.
- Deteksi perangkat dan redirect otomatis (mobile vs desktop).

### 6.9 API

- Autentikasi via Laravel Sanctum (token-based).
- Endpoint login, register, logout, profile.
- Endpoint CRUD transaksi.
- Endpoint daftar kategori.
- Endpoint dashboard stats dan chart.

---

## 7. Persyaratan Non-Fungsional (Kualitatif)

- **Keamanan Data:** data pengguna, transaksi, dan keuangan hanya dapat diakses oleh pengguna yang memiliki hak akses sesuai perannya (RBAC dengan Filament Shield). Sesi terenkripsi.
- **Reliabilitas:** sistem dapat menyimpan data transaksi dengan baik sehingga dapat diakses kembali ketika dibutuhkan.
- **Kemudahan Penggunaan:** tampilan sistem dibuat sederhana dengan dashboard yang informatif agar mudah digunakan oleh seluruh anggota keluarga dari berbagai usia. Tersedia versi mobile web yang responsif.
- **Kinerja Sistem:** sistem mampu menampilkan dashboard, grafik, dan data transaksi dengan waktu akses yang wajar.
- **Pemeliharaan:** data kategori, pengaturan sistem, dan status transaksi dapat diperbarui oleh admin tanpa mengubah kode program.
- **Kompatibilitas:** sistem dapat diakses melalui browser modern pada perangkat komputer maupun smartphone.
- **Personalisasi:** pengguna dapat memilih tema tampilan (theme) sesuai preferensi.
- **Audit Trail:** seluruh aktivitas CRUD tercatat dalam activity log.

---

## 8. Arsitektur Tingkat Tinggi

- Back-end: Laravel
- Panel/Admin UI: Filament 3 (4 panel: Admin, Parent, Child, Personal)
- Database: MariaDB / MySQL
- Front-end: Blade, Livewire, Tailwind CSS, Chart.js
- Web Server: Nginx
- Containerization: Docker
- Version Control: Git dan GitHub
- URL Lokal: https://dompetkuu.test
- Storage: penyimpanan file avatar pada storage aplikasi
- API Auth: Laravel Sanctum (token-based)
- Social Login: Google OAuth via Laravel Socialite
- Device Detection: Jenssegers Agent
- Queue: Database queue

---

## 9. Model Data (Ringkas)

### 9.1 Data Pengguna dan Akses

- **users:** id, name, email, password, role (admin/parent/child/personal), parent_id, is_active, is_independent, avatar_url, google_id, telegram_chat_id, telegram_pair_code, telegram_disconnected_at, theme, theme_color, created_at, updated_at
- **password_reset_tokens:** email, token, created_at
- **personal_access_tokens:** id, tokenable_type, tokenable_id, name, token, abilities, last_used_at, expires_at, created_at, updated_at
- **notifications:** id, type, notifiable_type, notifiable_id, data, read_at, created_at, updated_at
- **password_reset_codes:** id, email, code (6-digit), is_used, expired_at, created_at, updated_at

### 9.2 Data Kategori dan Transaksi

- **categories:** id, name, type (income/expense), is_global, is_active, created_by, created_at, updated_at
- **incomes:** id, user_id, category_id, amount, source (Cash/Transfer Bank/E-Wallet/Lainnya), description, date, status (pending/approved/rejected), approved_by, created_at, updated_at
- **expenses:** id, user_id, category_id, amount, source (Cash/Transfer Bank/E-Wallet/Lainnya), description, date, status (pending/approved/rejected), approved_by, created_at, updated_at
- **transaction_requests:** id, child_id, parent_id, type (delete/edit), model_type (income/expense), model_id, old_data (JSON), new_data (JSON), status (pending/approved/rejected), reason, reject_reason, created_at, updated_at

### 9.3 Data Anggaran dan Tabungan

- **budgets:** id, user_id, category_id, limit_amount, period (weekly/monthly), created_at, updated_at
- **savings:** id, user_id, name, category (Liburan/Properti/Pribadi/Pendidikan/Kendaraan/Kesehatan/Elektronik/Lainnya), target_amount, current_amount, target_date, description, status (active/completed/cancelled), created_at, updated_at
- **saving_deposits:** id, saving_id, user_id, amount, date, note, created_at, updated_at

### 9.4 Data Konten dan Lainnya

- **panduan_sections:** id, title, content, icon, order, is_active, created_at, updated_at
- **activity_log:** id, log_name, description, subject_type, subject_id, causer_type, causer_id, properties, event, batch_uuid, created_at, updated_at

### 9.5 View

- **transactions_view:** View yang menggabungkan data incomes dan expenses melalui UNION untuk kemudahan query laporan.

---

## 10. Alur Proses Bisnis (Ringkas)

### 10.1 Alur Keluarga (Parent & Child)

1. Orang tua melakukan registrasi akun sebagai parent.
2. Orang tua menambahkan anggota keluarga (anak) ke dalam sistem.
3. Anak melakukan login ke dalam sistem.
4. Anak mencatat pemasukan (uang saku, hadiah, dll) — otomatis disetujui.
5. Anak mencatat pengeluaran (jajan, beli buku, dll) — status: PENDING.
6. Orang tua menerima notifikasi pengeluaran anak yang perlu diverifikasi.
7. Orang tua menyetujui (approve) atau menolak (reject) pengeluaran anak.
8. Anak menerima notifikasi status transaksi.
9. Hanya transaksi approved yang masuk ke dalam perhitungan statistik dan saldo.
10. Orang tua dapat mengatur batas anggaran per kategori untuk anak.
11. Anak dapat mengajukan request edit/hapus transaksi kepada orang tua.
12. Orang tua menyetujui atau menolak request tersebut.
13. Seluruh anggota keluarga dapat memantau dashboard keuangan masing-masing.

### 10.2 Alur Personal

1. Pengguna melakukan registrasi akun sebagai personal (mandiri).
2. Pengguna dapat login via email/password atau Google OAuth.
3. Pengguna mencatat pemasukan dan pengeluaran — otomatis disetujui.
4. Pengguna dapat mengelola anggaran per kategori secara mandiri.
5. Pengguna dapat mengelola tabungan dengan target dan riwayat deposit.
6. Pengguna dapat melihat laporan dan mengekspor ke PDF/Excel.
7. Pengguna dapat melihat dashboard statistik keuangan pribadi.

---

## 11. Teknologi

- Back-end: Laravel
- Panel/Admin UI: Filament 3
- Database: MariaDB / MySQL
- Front-end: Blade, Livewire, Tailwind CSS, Chart.js
- Web Server: Nginx
- Containerization: Docker
- Version Control: Git dan GitHub
- Development Environment: WSL, Docker, dan Visual Studio Code
- RBAC: Filament Shield (Spatie Laravel Permission)
- Activity Log: Filament Logger (z3d0x/filament-logger)
- API Auth: Laravel Sanctum
- Social Login: Laravel Socialite (Google OAuth)
- PDF Export: barryvdh/laravel-dompdf
- Excel Export: maatwebsite/laravel-excel
- Device Detection: jenssegers/agent
- Theme: hasnayeen/themes
- Dark/Light Mode: awcodes/light-switch
- Profile Editing: joaopaulolndev/filament-edit-profile
- Progress Bar: njxqlus/filament-progressbar
- Auth UI Enhancer: diogogpinto/filament-auth-ui-enhancer
- Image Optimization: joshembling/image-optimizer
- Slim Scrollbar: aymanalhattami/filament-slim-scrollbar
- Stats Overview: awcodes/overlook

---

## 12. Asumsi

- Setiap anggota keluarga memiliki akses internet untuk menggunakan aplikasi.
- Satu akun hanya memiliki satu peran (tidak bisa menjadi parent dan child sekaligus).
- Akun personal bersifat mandiri dan tidak terikat dengan struktur keluarga.
- Semua transaksi dicatat dalam mata uang Rupiah (Rp).
- Proses persetujuan transaksi dilakukan oleh orang tua setelah anak mencatat pengeluaran.
- Orang tua bertindak sebagai pengelola utama keuangan keluarga.
- Anak tidak dapat mengelola anggaran sendiri kecuali memiliki akun personal.
- Sistem digunakan untuk mendukung pencatatan keuangan dan tidak menggantikan proses pengambilan keputusan keuangan secara langsung.

---

## 13. Risiko & Mitigasi

| Risiko | Mitigasi |
|--------|----------|
| Anak mencatat pengeluaran dengan nominal yang tidak wajar | Orang tua melakukan verifikasi sebelum menyetujui dan dapat menolak pengeluaran yang tidak wajar. |
| Anak mencatat pengeluaran dengan kategori yang salah | Anak dapat mengajukan permintaan edit kepada orang tua untuk mengubah kategori transaksi melalui fitur transaction request. |
| Orang tua lupa memverifikasi pengeluaran anak | Sistem menampilkan notifikasi di dashboard dan database notifications. |
| Terjadi kehilangan data transaksi | Data disimpan pada database dan dilakukan backup secara berkala (tersedia mekanisme di Docker). |
| Anak mencoba mencatat pengeluaran melebihi anggaran | Sistem memberikan alert budget warning saat anggaran mendekati batas. |
| Akun anak digunakan oleh orang yang tidak bertanggung jawab | Sistem menyediakan fitur aktivasi/deaktivasi akun oleh admin/parent. |
| Pengguna lupa password | Sistem menyediakan fitur reset password via kode verifikasi 6-digit yang dikirim ke email. |

---

## 14. Kriteria Penerimaan

- Pengguna dapat melakukan registrasi dan login ke dalam sistem sesuai perannya (admin, parent, child, personal).
- Pengguna dapat login menggunakan Google OAuth.
- Anak dan personal dapat mencatat pemasukan dan pengeluaran ke dalam sistem.
- Sumber transaksi (Cash, Transfer Bank, E-Wallet, Lainnya) tersedia saat pencatatan.
- Orang tua menerima notifikasi pengeluaran anak yang perlu diverifikasi.
- Orang tua dapat menyetujui atau menolak pengeluaran anak.
- Orang tua dapat menyetujui atau menolak request edit/hapus transaksi anak.
- Sistem dapat menampilkan status transaksi (pending/approved/rejected).
- Anak dapat memantau sisa anggaran per kategori.
- Personal dapat mengelola anggaran per kategori secara mandiri.
- Data transaksi tersimpan pada database dan dapat ditampilkan kembali ketika diperlukan.
- Sistem dapat menampilkan dashboard statistik keuangan (pemasukan, pengeluaran, saldo).
- Sistem dapat menampilkan grafik perbandingan pemasukan dan pengeluaran dengan filter periode.
- Admin dapat mengelola pengguna dan kategori melalui dashboard admin.
- Admin dapat mengelola panduan/FAQ.
- Admin dapat melihat activity log sistem.
- Pengguna dapat mengekspor laporan ke PDF dan Excel.
- Sistem dapat diakses melalui perangkat mobile dengan tampilan yang responsif.
- API dapat diakses dengan token autentikasi Sanctum.

---

## 15. Diagram Use Case

```text
                                 +-----------------------------------+
                                 |      Sistem DompetKuu             |
                                 +-----------------------------------+
                                          |
               +--------------------------+--------------------------+
               |                          |                          |
       +-------+-------+        +--------+--------+        +-------+-------+
      |     Admin      |        |     Parent       |        |     Child      |
      +-------+-------+        +--------+---------+        +-------+-------+
              |                          |                          |
      +-------+-------+        +--------+---------+        +-------+-------+
      | - Kelola       |        | - Kelola          |        | - Catat        |
      |   pengguna     |        |   anggota         |        |   pemasukan    |
      | - Kelola       |        |   keluarga        |        | - Catat        |
      |   kategori     |        | - Catat           |        |   pengeluaran  |
      | - Kelola       |        |   transaksi       |        |   (pending)    |
      |   panduan/FAQ  |        |   pribadi         |        | - Lihat status |
      | - Lihat        |        | - Approve/Reject  |        | - Lihat sisa   |
      |   activity log |        |   transaksi anak  |        |   budget       |
      | - Lihat        |        | - Approve/Reject  |        | - Kelola       |
      |   dashboard    |        |   request anak    |        |   tabungan     |
      |                |        | - Set budget anak |        | - Request edit |
      |                |        | - Monitor         |        |   /hapus txn   |
      |                |        |   keuangan        |        |                |
      |                |        |   keluarga        |        |                |
      |                |        | - Export PDF/Excel|        |                |
      +----------------+        +-------------------+        +---------------+

                           +-------------------+
                           |    Personal       |
                           +-------------------+
                           | - Registrasi      |
                           |   mandiri         |
                           | - Login (email/   |
                           |   Google OAuth)   |
                           | - Catat pemasukan |
                           |   & pengeluaran   |
                           |   (auto-approved) |
                           | - Kelola budget   |
                           |   mandiri         |
                           | - Kelola tabungan |
                           | - Lihat laporan   |
                           | - Export PDF/Excel|
                           | - Reset password  |
                           |   via kode 6-digit|
                           +-------------------+
```
