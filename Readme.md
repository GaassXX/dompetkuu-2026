# 💰 DompetKuu
## Sistem Informasi Manajemen Keuangan Keluarga Berbasis Web

**Mata Kuliah:** Pemrograman Web — Proyek Akhir  
**Nama:** Rizqi Bagas Wicaksono  
**NIM:** 20240801187  
**Program Studi:** Teknik Informatika  
**Fakultas:** Ilmu Komputer  
**Universitas:** Esa Unggul  
**Tahun Akademik:** 2025/2026

---

## 📋 Daftar Isi

- [Fitur Utama](#fitur-utama)
- [Tech Stack](#tech-stack)
- [Role & Panel](#role--panel)
- [Struktur Menu Per Panel](#struktur-menu-per-panel)
- [Alur Approval Workflow](#alur-approval-workflow)
- [Struktur Database](#struktur-database)
- [Cara Menjalankan](#cara-menjalankan)
- [Plugin Filament](#plugin-filament)
- [Prinsip Keamanan](#prinsip-keamanan)

---

## ✨ Fitur Utama

| No | Fitur | Deskripsi |
|----|-------|-----------|
| 1 | **3 Panel Multi-Role** | Admin, Parent, dan Child dalam satu sistem terintegrasi |
| 2 | **Personal Mode** | Pengguna individu tanpa proses approval (otomatis approved) |
| 3 | **Manajemen Pemasukan & Pengeluaran** | Catat transaksi dengan kategori, nominal, tanggal, dan deskripsi |
| 4 | **Approval Workflow** | Parent menyetujui/menolak transaksi pengeluaran Child |
| 5 | **Manajemen Anggaran (Budget)** | Atur batas pengeluaran per kategori, periode mingguan/bulanan |
| 6 | **Tabungan dengan Target** | Buat target tabungan, catat riwayat setoran, pantau progress |
| 7 | **Laporan Keuangan** | Ringkasan pemasukan/pengeluaran/saldo, filter periode |
| 8 | **Export PDF & Excel** | Unduh laporan transaksi dalam format PDF dan Excel |
| 9 | **Google OAuth Login** | Masuk menggunakan akun Google |
| 10 | **Transaction Requests** | Child minta edit/hapus transaksi yang sudah approved ke Parent |
| 11 | **Database Notification** | Notifikasi real-time untuk approval dan sistem |
| 12 | **Mobile-Responsive UI** | Tampilan khusus perangkat mobile (Livewire components) |
| 13 | **Activity Log & Audit Trail** | Admin memantau seluruh aktivitas CRUD pengguna |
| 14 | **REST API** | Endpoint API menggunakan Laravel Sanctum untuk integrasi |
| 15 | **Role-Based Access Control** | Hak akses per panel menggunakan Filament Shield |

---

## 🧱 Tech Stack

| Layer | Teknologi |
|-------|-----------|
| **Backend** | Laravel 12 |
| **Admin Panel** | Filament v3 (3 Panel terpisah) |
| **Frontend** | Blade Template, Livewire 3, Tailwind CSS, Chart.js |
| **Database** | MariaDB 10.11 |
| **Web Server** | Nginx |
| **Container** | Docker & Docker Compose |
| **Version Control** | Git & GitHub |
| **Authentication** | Laravel Auth (email/password) + Google OAuth (Laravel Socialite) |
| **Authorization / RBAC** | Filament Shield (Spatie Permission) |
| **Activity Log** | Spatie Activity Log |
| **Export PDF** | barryvdh/laravel-dompdf |
| **Export Excel** | maatwebsite/laravel-excel |
| **Notification** | Laravel Database Notification |
| **API** | Laravel Sanctum |
| **Development** | VS Code, WSL Ubuntu |

---

## 👥 Role & Panel

| Role | Panel ID | Path | Warna Tema | Fungsi Utama |
|------|----------|------|-------------|--------------|
| **Admin** | `admin` | `/admin` | Biru | Kelola pengguna, kategori, activity log, RBAC |
| **Parent** | `parent` | `/parent` | Amber/Kuning | Kelola keuangan keluarga, approval Child, anggaran |
| **Child** | `child` | `/child` | Amber/Kuning | Catat transaksi pribadi, lihat status approval |
| **Personal** | `child` | `/child` | Amber/Kuning | Sama seperti Child tetapi tanpa approval (auto-approved) |

> **Personal** secara teknis menggunakan panel Child yang sama, namun karena `parent_id` bernilai NULL, transaksinya otomatis approved tanpa perlu approval dari Parent.

---

## 📋 Struktur Menu Per Panel

### 🔵 Panel Admin (`/admin`) — System Controller

| Menu | Fungsi |
|------|--------|
| **Dashboard** | Statistik keuangan agregat — total pemasukan, pengeluaran, saldo, jumlah user |
| **Grafik Pemasukan vs Pengeluaran** | Bar chart perbandingan 6 bulan terakhir dari semua pengguna |
| **Kategori** | Kelola kategori global (dipakai semua user) dan kategori personal |
| **Pemasukan** | Lihat & kelola seluruh pemasukan semua user, ubah status approval |
| **Pengeluaran** | Lihat & kelola seluruh pengeluaran semua user, ubah status approval |
| **Anggaran** | Tetapkan batas anggaran per user per kategori (mingguan/bulanan) |
| **Pengguna** | Tambah, edit, nonaktifkan akun; kelola role (Admin/Parent/Child) |
| **Transaction View** | Tampilan gabungan pemasukan & pengeluaran semua user (read-only) |
| **Activity Log** | Riwayat seluruh aktivitas CRUD pengguna di sistem (read-only) |

### 🟢 Panel Parent (`/parent`) — Family Finance Manager

| Menu | Fungsi |
|------|--------|
| **Dashboard** | Statistik keuangan pribadi & keluarga — saldo, pending approval, grafik |
| **Anggota Keluarga** | Kelola akun Child (tambah, edit, aktif/nonaktifkan) |
| **Pemasukan** | Catat pemasukan pribadi (auto-approved) & lihat pemasukan Child |
| **Pengeluaran** | Catat pengeluaran pribadi (auto-approved) & lihat pengeluaran Child |
| **Anggaran (Budget)** | Atur batas pengeluaran per kategori untuk Child |
| **Tabungan (Saving)** | Kelola tabungan pribadi dengan target |
| **Approval Queue** | Setujui/tolak transaksi pengeluaran Child yang pending |
| **Transaction Requests** | Setujui/tolak permintaan edit/hapus transaksi dari Child |
| **Laporan** | Ringkasan keuangan + export PDF/Excel |
| **Notifikasi** | Notifikasi approval dan sistem |

### 🟠 Panel Child (`/child`) — Personal Finance Recorder

| Menu | Fungsi |
|------|--------|
| **Dashboard** | Statistik keuangan pribadi — saldo, sisa anggaran, status transaksi |
| **Pemasukan** | Catat pemasukan pribadi (auto-approved) |
| **Pengeluaran** | Catat pengeluaran pribadi (status: PENDING, menunggu approval Parent) |
| **Tabungan (Saving)** | Kelola tabungan pribadi dengan target |
| **Riwayat Transaksi** | Lihat status transaksi (Pending/Approved/Rejected) |
| **Laporan** | Ringkasan transaksi pribadi |
| **Notifikasi** | Notifikasi hasil approval dari Parent |

> **Personal Mode**: Jika akun tidak memiliki Parent (parent_id = NULL), semua transaksi otomatis approved — tidak ada proses approval.

---

## 🔄 Alur Approval Workflow

```
Child input transaksi pengeluaran
              │
              ▼
         Status: PENDING
              │
              ▼
    Parent lihat di Approval Queue
              │
        ┌─────┴─────┐
        ▼           ▼
    APPROVED     REJECTED
        │           │
        ▼           ▼
   Masuk ke     Tidak masuk
   statistik    statistik
   & saldo      & saldo
```

| Status | Dibuat Oleh | Masuk Statistik? | Keterangan |
|--------|-------------|------------------|------------|
| **Pending** | Child (default) | ❌ | Menunggu persetujuan Parent |
| **Approved** | Admin/Parent (default) atau dari pending | ✅ | Dihitung dalam total keuangan |
| **Rejected** | Parent (menolak dari pending) | ❌ | Transaksi tidak valid |

---

## 🗄️ Struktur Database

| Tabel | Field Utama | Relasi |
|-------|-------------|--------|
| `users` | id, name, email, password, role, parent_id, is_active, avatar_url, google_id | Self-referential (parent_id → users.id) |
| `categories` | id, name, type (income/expense), is_global, created_by | BelongsTo users |
| `incomes` | id, user_id, category_id, amount, description, date, status, approved_by | BelongsTo users, categories |
| `expenses` | id, user_id, category_id, amount, description, date, status, approved_by | BelongsTo users, categories |
| `budgets` | id, user_id, category_id, limit_amount, period (weekly/monthly) | BelongsTo users, categories |
| `savings` | id, user_id, name, target_amount, current_amount, target_date, status | BelongsTo users |
| `saving_deposits` | id, saving_id, user_id, amount, date, note | BelongsTo savings, users |
| `transaction_requests` | id, child_id, parent_id, type (edit/delete), model_type, status | BelongsTo users |
| `notifications` | id, type, notifiable_type, notifiable_id, data, read_at | Polymorphic |
| `panduan_sections` | id, title, content, order, is_active | — |
| `activity_log` | id, log_name, subject_type, subject_id, causer_type, causer_id, properties | Polymorphic |

---

## 🛠️ Cara Menjalankan

### Prasyarat

- Docker & Docker Compose terinstall
- mkcert untuk SSL lokal

### 1. Clone Repository

```bash
git clone https://github.com/GaassXX/dompetkuu-2026.git
cd dompetkuu-2026
```

### 2. Setup SSL Lokal

```bash
mkcert -install
cd nginx/ssl
mkcert dompetkuu.test
```

### 3. Tambahkan Domain ke Hosts

```bash
# Linux / macOS
echo "127.0.0.1 dompetkuu.test" | sudo tee -a /etc/hosts

# Windows — tambahkan ke C:\Windows\System32\drivers\etc\hosts
# 127.0.0.1 dompetkuu.test
```

### 4. Salin File Environment

```bash
cp src/.env.example src/.env
```

Sesuaikan `src/.env`:

```
APP_NAME="DompetKuu"
APP_URL=https://dompetkuu.test
ASSET_URL=https://dompetkuu.test
DB_DATABASE=dompetkuu
```

### 5. Jalankan Docker

```bash
docker compose up -d
```

Container akan otomatis menjalankan:
- Install dependensi Composer
- Migrasi database
- `php artisan project:init` — migrate fresh + seed + generate RBAC permissions
- Symlink storage

### 6. Buka Aplikasi

| Panel | URL | Warna Tema |
|-------|-----|------------|
| **Admin** | `https://dompetkuu.test/admin` | 🔵 Biru |
| **Parent** | `https://dompetkuu.test/parent` | 🟢 Hijau |
| **Child** | `https://dompetkuu.test/child` | 🟠 Oranye |

**Akun Demo:**

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@admin.com | password |
| Orang Tua | orangtua@admin.com | password |
| Anak | anak1@admin.com | password |

---

## 📦 Plugin Filament V3

| Package | Fungsi |
|---------|--------|
| `bezhansalleh/filament-shield` | RBAC & manajemen permission |
| `z3d0x/filament-logger` | Activity log / audit trail |
| `hasnayeen/themes` | Tema warna dinamis per panel |
| `awcodes/light-switch` | Toggle dark/light mode |
| `awcodes/overlook` | Widget statistik overview |
| `joaopaulolndev/filament-edit-profile` | Edit profil dalam panel |
| `joshembling/image-optimizer` | Optimasi avatar ke format WebP |
| `njxqlus/filament-progressbar` | Progress bar loading animasi |
| `diogogpinto/filament-auth-ui-enhancer` | Peningkatan tampilan halaman login |

---

## 🔐 Prinsip Keamanan

- **One Account, One Role**: Setiap akun hanya memiliki satu role — menghindari kerancuan akses data
- **Panel Separation**: Setiap panel sepenuhnya terpisah (Provider, Resource, Widget, Middleware) — tidak ada tumpang tindih antar role
- **Read-Only for Statistics**: Hanya transaksi berstatus `approved` yang dihitung dalam statistik dan grafik
- **Activity Logging**: Seluruh aktivitas CRUD dicatat otomatis dan hanya bisa dilihat Admin
- **Role-Based Access Control**: Setiap resource dan action dilindungi oleh permission melalui Filament Shield
- **Session Security**: Cookie session menggunakan flag secure, http-only, dan same-site untuk mencegah CSRF & XSS

---

> Dibangun dengan Laravel 12 + Filament v3 untuk Proyek Akhir Mata Kuliah Pemrograman Web — Universitas Esa Unggul
