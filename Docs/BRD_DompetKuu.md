# BUSINESS REQUIREMENT DOCUMENT (BRD)
## DOMPETKUU
### Sistem Informasi Manajemen Keuangan Keluarga Berbasis Web

---

## 1. Ringkasan Eksekutif

Dokumen Business Requirement Document (BRD) ini menjelaskan kebutuhan bisnis dan kebutuhan sistem untuk pengembangan aplikasi **DompetKuu**, yaitu platform berbasis web yang digunakan untuk membantu proses pencatatan dan pengelolaan keuangan keluarga secara digital. Sistem ini dirancang untuk memudahkan setiap anggota keluarga dalam mencatat pemasukan dan pengeluaran, memantau anggaran belanja, serta memperoleh informasi terkait kondisi keuangan keluarga secara real-time.

Selain membantu anak dalam mengelola keuangan pribadi, sistem juga mendukung orang tua dalam memverifikasi transaksi anak, mengatur anggaran per kategori, serta memantau aktivitas keuangan keluarga secara lebih terstruktur. Dengan adanya sistem ini, pencatatan keuangan yang sebelumnya dilakukan secara manual atau tersebar di berbagai catatan dapat dilakukan dalam satu platform yang terintegrasi sehingga meningkatkan transparansi dan efisiensi pengelolaan keuangan keluarga.

---

## 2. Latar Belakang Dan Justifikasi Bisnis

### 2.1 Konteks

DompetKuu merupakan aplikasi manajemen keuangan keluarga yang melayani kebutuhan keluarga dalam mencatat pemasukan, pengeluaran, dan tabungan secara digital. Saat ini pencatatan keuangan keluarga masih dilakukan secara manual melalui buku catatan atau aplikasi catatan sederhana sehingga orang tua sulit memantau pengeluaran anak dan anak tidak memiliki batasan anggaran yang jelas.

### 2.2 Permasalahan

- Data pemasukan dan pengeluaran keluarga tersebar pada banyak catatan manual atau aplikasi terpisah.
- Sulit melakukan rekapitulasi keuangan keluarga secara cepat dan akurat.
- Orang tua tidak memiliki kontrol terhadap pengeluaran anak secara real-time.
- Anak tidak memiliki panduan anggaran (budget) yang jelas dalam membelanjakan uang.
- Tidak tersedia media yang terpusat untuk memantau kondisi keuangan keluarga.
- Proses persetujuan pengeluaran anak masih dilakukan secara lisan tanpa dokumentasi.

### 2.3 Solusi yang Diusulkan

Membangun sistem berbasis web untuk mendukung proses pencatatan dan pengelolaan keuangan keluarga secara terintegrasi dengan fitur:
- Registrasi dan login anggota keluarga.
- Pencatatan pemasukan (income) secara digital.
- Pencatatan pengeluaran (expense) dengan sistem persetujuan orang tua (khusus anak).
- Pengelolaan anggaran (budget) per kategori oleh orang tua untuk anak.
- Monitoring status transaksi (pending/approved/rejected).
- Dashboard analitik keuangan keluarga.
- Kelola tabungan (saving) dengan target.

Manfaat yang diharapkan dari sistem ini antara lain:
- Mempermudah pencatatan keuangan keluarga.
- Mengurangi risiko kesalahan pencatatan transaksi.
- Meningkatkan kontrol orang tua terhadap pengeluaran anak.
- Membantu anak belajar mengelola keuangan dengan batasan anggaran.
- Meningkatkan efisiensi pengelolaan keuangan keluarga.

---

## 3. Tujuan Bisnis

- Menyediakan platform pencatatan keuangan keluarga yang terstruktur dan mudah digunakan.
- Membantu pengelolaan data pemasukan, pengeluaran, dan tabungan secara terpusat.
- Mempermudah orang tua dalam memantau dan menyetujui transaksi anak.
- Mendukung efisiensi pengelolaan keuangan keluarga melalui sistem anggaran dan alert.
- Membantu anggota keluarga dalam mencapai target tabungan.

---

## 4. Ruang Lingkup

### 4.1 Dashboard Anak (Child)

- Registrasi akun anggota keluarga (anak).
- Login dan logout.
- Mencatat pemasukan pribadi (auto-approved).
- Mencatat pengeluaran pribadi (pending approval orang tua).
- Melihat status transaksi (pending/approved/rejected).
- Melihat sisa anggaran per kategori.
- Mengelola tabungan pribadi.
- Melihat riwayat transaksi.

### 4.2 Dashboard Orang Tua (Parent)

- Manajemen data anggota keluarga (anak).
- Mencatat pemasukan pribadi (auto-approved).
- Mencatat pengeluaran pribadi (auto-approved).
- Verifikasi pengeluaran anak (approve/reject).
- Pengelolaan anggaran per kategori untuk anak.
- Monitoring keuangan keluarga secara keseluruhan.
- Monitoring keuangan masing-masing anak.

### 4.3 Dashboard Admin

- Manajemen data pengguna (aktif/nonaktifkan).
- Manajemen kategori transaksi global.
- Monitoring activity log sistem (audit trail).

### 4.4 Pengelolaan Transaksi

- Pencatatan pemasukan dengan kategori.
- Pencatatan pengeluaran dengan kategori.
- Sistem persetujuan (approval workflow) untuk pengeluaran anak.
- Penyimpanan data dan riwayat transaksi.

### 4.5 Pelaporan Dasar

- Ringkasan jumlah pemasukan dan pengeluaran.
- Riwayat transaksi per periode.
- Ringkasan saldo keuangan.
- Grafik perbandingan pemasukan dan pengeluaran 6 bulan.

### 4.6 Ruang Lingkup yang Tidak Termasuk

- Integrasi dengan rekening bank atau e-wallet secara otomatis.
- Aplikasi mobile Android atau iOS native.
- Sistem akuntansi dan pembukuan lengkap (neraca, laba rugi).
- Multi-mata uang asing.
- Integrasi Telegram Bot.

---

## 5. Stakeholders dan Pengguna

| Stakeholder            | Peran dan Tanggung Jawab |
|------------------------|-------------------------|
| Anak (Child)           | Mencatat pemasukan dan pengeluaran pribadi, mengelola tabungan, memantau anggaran. |
| Orang Tua (Parent)     | Mencatat keuangan pribadi, memverifikasi pengeluaran anak, mengatur anggaran anak, memantau keuangan keluarga. |
| Admin                  | Mengelola data pengguna dan kategori transaksi global, memonitor activity log sistem (audit trail). |

---

## 6. Persyaratan Fungsional

### 6.1 Website Pengguna (Child / Parent)

- Registrasi akun pengguna.
- Login dan logout pengguna.
- Mencatat pemasukan dengan kategori dan jumlah.
- Mencatat pengeluaran dengan kategori dan jumlah.
- Melihat status transaksi (pending/approved/rejected) — khusus Child.
- Melihat riwayat transaksi.
- Melihat dashboard statistik keuangan pribadi.
- Melihat sisa anggaran per kategori.
- Mengelola tabungan dan target.

### 6.2 Manajemen Transaksi

- Pencatatan pemasukan dengan kategori.
- Pencatatan pengeluaran dengan kategori.
- Status transaksi meliputi:
  - Pending (menunggu verifikasi) — khusus pengeluaran Child.
  - Approved (disetujui) — otomatis untuk Parent.
  - Rejected (ditolak) — khusus pengeluaran Child.
- Riwayat perubahan status transaksi.

### 6.3 Approval Workflow

- Orang tua menerima notifikasi pengeluaran anak yang pending.
- Orang tua dapat menyetujui (approve) pengeluaran anak.
- Orang tua dapat menolak (reject) pengeluaran anak.
- Hanya transaksi approved yang masuk ke dalam perhitungan statistik.
- Anak menerima notifikasi status transaksi.

### 6.4 Manajemen Anggaran

- Orang tua dapat membuat anggaran per kategori untuk anak.
- Penentuan periode anggaran (mingguan/bulanan).
- Anak dapat melihat sisa anggaran di dashboard.
- Anak tidak dapat membuat, mengedit, atau menghapus anggaran sendiri.

### 6.5 Dashboard Admin

- Manajemen data pengguna (aktif/nonaktifkan, atur roles).
- Manajemen kategori transaksi global.
- Activity log sistem (audit trail seluruh aktivitas CRUD).

### 6.6 Pelaporan

Data Transaksi:
- Riwayat transaksi pengguna.
- Riwayat transaksi per kategori.
- Rekap jumlah pemasukan dan pengeluaran.

Data Keuangan:
- Ringkasan saldo keuangan.
- Riwayat transaksi berdasarkan periode tertentu.
- Grafik perbandingan pemasukan dan pengeluaran 6 bulan.

---

## 7. Persyaratan Non-Fungsional (Kualitatif)

- **Keamanan Data:** data pengguna, transaksi, dan keuangan hanya dapat diakses oleh pengguna yang memiliki hak akses sesuai perannya (RBAC dengan Filament Shield).
- **Reliabilitas:** sistem dapat menyimpan data transaksi dengan baik sehingga dapat diakses kembali ketika dibutuhkan.
- **Kemudahan Penggunaan:** tampilan sistem dibuat sederhana dengan dashboard yang informatif agar mudah digunakan oleh seluruh anggota keluarga dari berbagai usia.
- **Kinerja Sistem:** sistem mampu menampilkan dashboard, grafik, dan data transaksi dengan waktu akses yang wajar.
- **Pemeliharaan:** data kategori, pengaturan sistem, dan status transaksi dapat diperbarui oleh admin tanpa mengubah kode program.
- **Kompatibilitas:** sistem dapat diakses melalui browser modern pada perangkat komputer maupun smartphone.

---

## 8. Arsitektur Tingkat Tinggi

- Back-end: Laravel
- Panel/Admin UI: Filament 3 (3 panel: Admin, Parent, Child)
- Database: MariaDB / MySQL
- Front-end: Blade, Livewire, Tailwind CSS, Chart.js
- Web Server: Nginx
- Containerization: Docker
- Version Control: Git dan GitHub
- URL Lokal: https://dompetkuu.test
- Storage: penyimpanan file avatar pada storage aplikasi

---

## 9. Model Data (Ringkas)

### 9.1 Data Pengguna dan Akses

- **users:** id, name, email, password, role (admin/parent/child), parent_id, is_active, avatar_url, created_at, updated_at

### 9.2 Data Kategori dan Transaksi

- **categories:** id, name, type (income/expense), is_global, created_by, created_at, updated_at
- **incomes:** id, user_id, category_id, amount, description, date, status (pending/approved/rejected), approved_by, created_at, updated_at
- **expenses:** id, user_id, category_id, amount, description, date, status (pending/approved/rejected), approved_by, created_at, updated_at

### 9.3 Data Anggaran dan Tabungan

- **budgets:** id, user_id, category_id, limit_amount, period (weekly/monthly), created_at, updated_at
- **savings:** id, user_id, name, target_amount, current_amount, target_date, status (active/completed/cancelled), created_at, updated_at

---

## 10. Alur Proses Bisnis (Ringkas)

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
11. Seluruh anggota keluarga dapat memantau dashboard keuangan masing-masing.

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
- RBAC: Filament Shield
- Activity Log: Filament Logger

---

## 12. Asumsi

- Setiap anggota keluarga memiliki akses internet untuk menggunakan aplikasi.
- Satu akun hanya memiliki satu peran (tidak bisa menjadi parent dan child sekaligus).
- Semua transaksi dicatat dalam mata uang Rupiah (Rp).
- Proses persetujuan transaksi dilakukan oleh orang tua setelah anak mencatat pengeluaran.
- Orang tua bertindak sebagai pengelola utama keuangan keluarga.
- Sistem digunakan untuk mendukung pencatatan keuangan keluarga dan tidak menggantikan proses pengambilan keputusan keuangan secara langsung.

---

## 13. Risiko & Mitigasi

| Risiko | Mitigasi |
|--------|----------|
| Anak mencatat pengeluaran dengan nominal yang tidak wajar | Orang tua melakukan verifikasi sebelum menyetujui dan dapat menolak pengeluaran yang tidak wajar. |
| Anak mencatat pengeluaran dengan kategori yang salah | Anak dapat mengajukan permintaan edit kepada orang tua untuk mengubah kategori transaksi. |
| Orang tua lupa memverifikasi pengeluaran anak | Sistem menampilkan notifikasi di dashboard dan database notifications. |
| Terjadi kehilangan data transaksi | Data disimpan pada database dan dilakukan backup secara berkala (tersedia mekanisme di Docker). |
| Anak mencoba mencatat pengeluaran melebihi anggaran | Sistem memberikan alert budget warning saat anggaran mendekati batas. |
| Akun anak digunakan oleh orang yang tidak bertanggung jawab | Sistem menyediakan fitur aktivasi/deaktivasi akun oleh admin/parent. |

---

## 14. Kriteria Penerimaan

- Pengguna dapat melakukan registrasi dan login ke dalam sistem sesuai perannya (admin, parent, child).
- Anak dapat mencatat pemasukan dan pengeluaran ke dalam sistem.
- Orang tua menerima notifikasi pengeluaran anak yang perlu diverifikasi.
- Orang tua dapat menyetujui atau menolak pengeluaran anak.
- Sistem dapat menampilkan status transaksi (pending/approved/rejected).
- Anak dapat memantau sisa anggaran per kategori.
- Data transaksi tersimpan pada database dan dapat ditampilkan kembali ketika diperlukan.
- Sistem dapat menampilkan dashboard statistik keuangan (pemasukan, pengeluaran, saldo).
- Sistem dapat menampilkan grafik perbandingan pemasukan dan pengeluaran.
- Admin dapat mengelola pengguna dan kategori melalui dashboard admin.
- Admin dapat melihat activity log sistem.

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
     | - Lihat        |        |   transaksi       |        |   (pending)    |
     |   activity log |        |   pribadi         |        | - Lihat status |
     | - Lihat        |        | - Approve/Reject  |        | - Lihat sisa   |
     |   dashboard    |        |   transaksi anak  |        |   budget       |
     |                |        | - Set budget anak |        | - Kelola       |
     |                |        | - Monitor         |        |   tabungan     |
     |                |        |   keuangan        |        |                |
     |                |        |   keluarga        |        |                |
     +----------------+        +-------------------+        +---------------+
```
