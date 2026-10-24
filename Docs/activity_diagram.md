# Dokumentasi Activity Diagram DompetKuu v2.1
**Sistem**: DompetKuu (Mobile Flutter Personal + Web Admin Filament)  
**Versi**: 2.1 (Oktober 2026)

Activity diagram berikut menggambarkan alur kerja langkah demi langkah, percabangan keputusan (*decision*), serta pembagian peran (*swimlane*) antara **Pengguna (Mobile Flutter)**, **Backend API (Laravel)**, dan **Database**.

---

## 1. Activity Diagram: Catat Transaksi Cepat via Bot Chat (Quick Entry Bot)
Menggambarkan alur pengguna saat mengetik transaksi menggunakan bahasa sehari-hari (contoh: *"Makan 10.000 Makan Siang"*).

```mermaid
flowchart TD
    startNode([Mulai]) --> A1[Buka Layar Bot Chat di Flutter]
    A1 --> A2[Ketik Pesan Transaksi Alami]
    A2 --> A3[Tekan Tombol Kirim]

    A3 --> B1[Kirim HTTP POST /api/chat-bot/parse-and-save]
    B1 --> B2[TransactionParserService Membaca Teks]

    B2 --> C1{Apakah Format Angka & Teks Valid?}
    
    C1 -- Tidak --> C2[Kembalikan Error 422 + Saran Format]
    C2 --> A4[Tampilkan Balasan Bot: 'Format Tidak Dikenali']
    A4 --> A2

    C1 -- Ya --> B3[Ekstraksi Nominal & Tipe: Income / Expense]
    B3 --> B4[Deteksi Kategori Otomatis via Kamus Sinonim]
    B4 --> B5[Bersihkan Teks Menjadi Deskripsi]
    B5 --> DB1[(Simpan ke Database Incomes/Expenses dengan Status Approved)]
    
    DB1 --> B6[Kirim Response Sukses JSON + Data Transaksi]
    B6 --> A5[Tampilkan Bubble Balasan Bot Sukses + Kartu Transaksi]
    A5 --> endNode([Selesai])
```

---

## 2. Activity Diagram: Pengelolaan & Pelunasan Utang/Piutang
Menggambarkan alur pencatatan utang/piutang serta fitur pelunasan sekali klik (*mark as paid*) yang otomatis memicu pencatatan arus kas.

```mermaid
flowchart TD
    startNode([Mulai]) --> A1[Buka Menu Utang & Piutang di Mobile]
    A1 --> A2{Pilih Tindakan}

    %% Cabang 1: Tambah Baru
    A2 -- Catat Baru --> B1[Pilih Tipe: Utang atau Piutang]
    B1 --> B2[Isi Nama, Nominal, Tanggal & Jatuh Tempo]
    B2 --> B3[Tekan Simpan]
    B3 --> API1[POST /api/debts]
    API1 --> DB1[(Simpan Data ke Tabel Debts Status: Unpaid)]
    DB1 --> C1[Tampilkan Item di Daftar Utang/Piutang]
    C1 --> endNode([Selesai])

    %% Cabang 2: Lunasi
    A2 -- Lunasi Utang/Piutang --> D1[Pilih Item Belum Lunas & Tekan 'Lunasi']
    D1 --> D2[Konfirmasi Dialog Pelunasan]
    D2 --> API2[POST /api/debts/:id/pay]
    
    API2 --> COND1{Tipe Transaksi?}
    
    COND1 -- Utang (Debt) --> EXP1[Sistem Otomatis Buat Catatan Pengeluaran / Expense]
    EXP1 --> DB2[(Update Status Debt: Paid & Simpan Expense)]
    
    COND1 -- Piutang (Receivable) --> INC1[Sistem Otomatis Buat Catatan Pemasukan / Income]
    INC1 --> DB3[(Update Status Debt: Paid & Simpan Income)]

    DB2 --> E1[Kirim Response Sukses + Ringkasan Update]
    DB3 --> E1
    E1 --> E2[Perbarui Tampilan Saldo & Label Status Jadi Lunas]
    E2 --> endNode
```

---

## 3. Activity Diagram: Transaksi Manual & Peringatan Anggaran (Budget Alert)
Menggambarkan pencatatan pengeluaran biasa disertai pengecekan batas anggaran (*budget limit*).

```mermaid
flowchart TD
    startNode([Mulai]) --> A1[Buka Form Tambah Pengeluaran di Flutter]
    A1 --> A2[Isi Nominal, Pilih Kategori & Tanggal]
    A2 --> A3[Tekan Simpan Transaksi]
    
    A3 --> API1[POST /api/transactions]
    API1 --> DB1[(Simpan Pengeluaran Status: Approved)]
    
    DB1 --> B1[Cek Batas Anggaran Kategori Tersebut]
    B1 --> B2{Total Pengeluaran >= 80% Budget?}
    
    B2 -- Ya --> B3[Buat Notifikasi Peringatan Anggaran Hampir Habis]
    B3 --> B4[Kirim Notifikasi Mobile & Simpan ke DB Notifications]
    B4 --> C1[Tampilkan SnackBar Sukses + Peringatan Budget]
    
    B2 -- Tidak --> C2[Tampilkan SnackBar Sukses Transaksi Disimpan]
    
    C1 --> endNode([Selesai])
    C2 --> endNode
```

---

## 4. Activity Diagram: Pengelolaan Tabungan & Setoran Deposit
Menggambarkan alur menabung dengan target dan penambahan setoran secara bertahap.

```mermaid
flowchart TD
    startNode([Mulai]) --> A1[Buka Menu Tabungan di Flutter]
    A1 --> A2{Pilih Aksi Tabungan}

    %% Buat Tabungan
    A2 -- Buat Target Baru --> B1[Input Nama Tabungan, Target Nominal & Tanggal]
    B1 --> API1[POST /api/savings]
    API1 --> DB1[(Simpan ke Tabel Savings: current_amount = 0)]
    DB1 --> B2[Tampilkan Kartu Tabungan Baru]
    B2 --> endNode([Selesai])

    %% Setor Tabungan
    A2 -- Setor Uang Tabungan --> C1[Pilih Tabungan & Tekan 'Tambah Setoran']
    C1 --> C2[Input Nominal Setoran & Catatan]
    C2 --> API2[POST /api/savings/:id/deposits]
    API2 --> DB2[(Catat ke SavingDeposits & Tambahkan current_amount)]
    
    DB2 --> D1{current_amount >= target_amount?}
    D1 -- Ya --> D2[Ubah Status Tabungan Menjadi 'Completed']
    D1 -- Tidak --> D3[Pertahankan Status 'Active']
    
    D2 --> E1[Kembalikan Response + Persentase Progres 100%]
    D3 --> E1
    E1 --> E2[Perbarui Progress Bar Tabungan di UI Flutter]
    E2 --> endNode
```

---

## 5. Activity Diagram: Autentikasi Pengguna Mobile (Email & Google Sign-In)
Menggambarkan alur login pengguna mobile hingga memperoleh token otentikasi Sanctum.

```mermaid
flowchart TD
    startNode([Mulai]) --> A1[Buka Aplikasi DompetKuu Mobile]
    A1 --> A2{Pilih Metode Masuk}

    %% Email & Password
    A2 -- Email & Password --> B1[Masukkan Email & Password]
    B1 --> B2[Kirim POST /api/auth/login]
    B2 --> C1{Kredensial Sesuai & Akun Aktif?}
    C1 -- Tidak --> C2[Tampilkan Pesan Error Login]
    C2 --> B1
    C1 -- Ya --> C3[Terbitkan Personal Access Token Sanctum]

    %% Google Sign In
    A2 -- Google Sign-In --> D1[Tekan Tombol 'Masuk dengan Google']
    D1 --> D2[Autentikasi SDK Google di Android]
    D2 --> D3[Peroleh Google ID Token]
    D3 --> D4[Kirim POST /api/auth/google-sign-in]
    D4 --> D5[Verifikasi Token ke Server Google]
    D5 --> D6{Akun Sudah Terdaftar?}
    D6 -- Belum --> D7[Otomatis Buat Akun Baru Role: Personal]
    D6 -- Sudah --> D8[Ambil Data Akun Terdaftar]
    D7 --> C3
    D8 --> C3

    C3 --> E1[Kembalikan Token Sanctum + Data Profil User]
    E1 --> E2[Simpan Token Aman di flutter_secure_storage]
    E2 --> E3[Arahkan Pengguna Masuk ke Dashboard Utama]
    E3 --> endNode([Selesai])
```
