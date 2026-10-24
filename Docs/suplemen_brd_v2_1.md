# Suplemen & Revisi BRD DompetKuu v2.1 (Mobile Flutter + Web Admin)

Dokumen ini berisi poin-poin tambahan dan pembaruan resmi untuk disisipkan ke dalam **Business Requirement Document (BRD) DompetKuu v2**.

---

## 1. Pembaruan pada Bab 4.3 & 4.4 (Ruang Lingkup Fitur Mobile)

### Tambahan pada Bab 4.3 (Ruang Lingkup Masuk):
* **Pengelolaan Utang dan Piutang (Debt & Receivable):**
  * Pencatatan uang yang dipinjam dari orang lain (Utang) dan uang yang dipinjamkan ke orang lain (Piutang).
  * Pemantauan status pelunasan (*Unpaid* / *Paid*) dan tenggat jatuh tempo (*Due Date*).
  * Fitur pelunasan sekali klik (*Mark as Paid*) yang otomatis mengintegrasikan arus kas ke Pengeluaran (*Expense*) atau Pemasukan (*Income*).
  * Indikator visual peringatan utang/piutang yang lewat jatuh tempo (*Overdue*).
* **Bot Chat Quick Entry (Pencatatan Cepat Berbasis Bahasa Alami):**
  * Antarmuka chat sederhana di dalam aplikasi mobile untuk mencatat transaksi secara instan tanpa perlu masuk dan mengisi formulir panjang.
  * Parser kalimat cerdas yang mendeteksi nominal (termasuk format angka `10.000`, `10rb`, `10k`, `1jt`), tipe transaksi (pemasukan vs pengeluaran), serta sinonim kategori otomatis.

### Koreksi pada Bab 4.4 (Ruang Lingkup yang Tidak Termasuk):
* **Sebelumnya:** *"Integrasi Telegram dan WhatsApp (bot Telegram diganti bot chat di dalam aplikasi)"*
* **Koreksi/Penyempurnaan:** Integrasi bot pihak ketiga (seperti Telegram Bot) **dihilangkan sepenuhnya** dan digantikan oleh **fitur native Bot Chat Quick Entry langsung di dalam aplikasi mobile Flutter**.

---

## 2. Tambahan pada Bab 6: Persyaratan Fungsional (Functional Requirements)

### 6.8 Bot Chat Pencatatan Cepat (Quick Entry Bot)
Perbarui dan lengkapi ID kebutuhan untuk bot chat mobile:

| ID | Kebutuhan Fungsional | Keterangan & Aturan Bisnis |
|---|---|---|
| **FR-32** | Input Transaksi Berbasis Bahasa Alami | Pengguna dapat mengirim pesan teks singkat, contoh: *"Makan 10.000 Makan Siang"* atau *"Gajian 1.000.000 gaji bulan oktober"*. |
| **FR-33** | Ekstraksi Cerdas (Parser Engine) | Sistem secara otomatis mengekstrak: <br>• **Nominal**: Mengenali angka reguler maupun singkatan (`15k`, `15rb`, `1.5jt`).<br>• **Tipe Transaksi**: Kata kunci seperti `gaji`, `gajian`, `bonus`, `freelance` diidentifikasi sebagai **Pemasukan**; kata kunci lain default ke **Pengeluaran**.<br>• **Kategori**: Mencocokkan kamus sinonim (contoh: *bakso/kopi* $\rightarrow$ Makan & Minum, *bensin/gojek* $\rightarrow$ Transportasi, *listrik/wifi* $\rightarrow$ Tagihan).<br>• **Deskripsi**: Teks selain angka dibersihkan dan dijadikan catatan transaksi. |
| **FR-34** | Konfirmasi Instan & Status Transaksi | Bot memberikan respons balasan langsung berupa kartu konfirmasi transaksi yang berhasil dicatat (status otomatis `approved`). Jika teks tidak valid, bot memberikan panduan contoh format. |

### 6.10 Pengelolaan Utang dan Piutang (Debts & Receivables)

| ID | Kebutuhan Fungsional | Keterangan & Aturan Bisnis |
|---|---|---|
| **FR-35** | Pencatatan Utang & Piutang Baru | Pengguna dapat mencatat data utang (`type = debt`) atau piutang (`type = receivable`) dengan input: Nama Pihak terkait (`person_name`), Nominal (`amount`), Tanggal (`date`), Tenggat Jatuh Tempo (`due_date`, opsional), dan Keterangan (`description`). |
| **FR-36** | Pemantauan & Filter Daftar Utang/Piutang | Pengguna dapat melihat daftar utang dan piutang dengan filter status (*Belum Lunas* / *Lunas*) dan tipe (*Utang* / *Piutang*). Menampilkan ringkasan total utang belum dibayar dan total piutang belum diterima. |
| **FR-37** | Indikator Jatuh Tempo (*Due Date Alert*) | Memberikan penanda visual warna merah / badge peringatan pada item yang telah melewati tanggal jatuh tempo (`due_date < hari ini`). |
| **FR-38** | Pelunasan Terintegrasi (*Mark as Paid*) | Pengguna dapat menandai utang/piutang sebagai lunas: <br>• **Bayar Utang (`debt`)**: Otomatis dicatat sebagai Pengeluaran (*Expense*).<br>• **Terima Piutang (`receivable`)**: Otomatis dicatat sebagai Pemasukan (*Income*).<br>• Data yang sudah lunas berstatus *immutable* (tidak dapat diubah) demi audit trail yang valid. |
| **FR-39** | Perubahan & Penghapusan Data | Pengguna dapat mengedit atau menghapus data utang/piutang selama statusnya masih *Belum Lunas* (`unpaid`). |

---

## 3. Tambahan pada Bab 8.1 (Kondisi API Backend Aktual)

Perbarui tabel pemetaan API di Bab 8.1 BRD:

| Kelompok | Endpoint | Status Aktual | Catatan Integrasi Mobile |
|---|---|---|---|
| **Bot Chat Quick Entry** | `POST /api/chat-bot/parse-and-save` | **Sudah Dibuat (Siap Pakai)** | Endpoint menerima payload `{"message": "..."}`, memproses teks via `TransactionParserService`, menyimpan transaksi ke database, dan mengembalikan balasan bot beserta detail transaksi. |
| **Utang & Piutang** | `GET /api/debts`<br>`POST /api/debts`<br>`GET /api/debts/{id}`<br>`PUT /api/debts/{id}`<br>`DELETE /api/debts/{id}`<br>`POST /api/debts/{id}/pay` | **Sudah Ada (Siap Pakai)** | Endpoint lengkap di `DebtController.php`. Filter `type`, `status`, summary nominal unpaid, dan pelunasan otomatis. |
| **Tabungan (Saving)** | `GET/POST /api/savings`<br>`GET/PUT/DELETE /api/savings/{id}`<br>`GET/POST /api/savings/{id}/deposits` | **Sudah Ada (Siap Pakai)** | Sudah tersedia di `SavingController.php`. Siap dihubungkan ke UI Flutter. |
| **Transaksi** | `GET/POST/PUT/DELETE /api/transactions` | **Ada, Perlu Penyesuaian** | Sesuaikan validasi `update`/`destroy` agar akun Personal bisa mengedit transaksi berstatus `approved` miliknya. |

---

## 4. Rancangan Layar Aplikasi Mobile Flutter (Bab UI)

### Layar 1: Bot Chat Quick Entry Screen
* **Tampilan Chat Bubbles:**
  * Bubble pengguna: Menampilkan teks yang diketik pengguna (misal: *"Makan 10.000 Makan Siang"*).
  * Bubble bot: Membalas dengan kartu mini transaksi yang menampilkan icon centang hijau, nominal format Rupiah, kategori yang terdeteksi, dan tombol *"Lihat Detail"*.
* **Input Bar Bawah:**
  * Kolom teks dengan placeholder *"Contoh: Kopi 25k, Gaji 3jt..."* + tombol kirim.
  * Tombol pintasan cepat di atas input bar (misal chip saran: *"Bensin 20k"*, *"Makan 15k"*).

### Layar 2: Manajemen Utang & Piutang (Debts Screen)
* Header Card: Ringkasan Total Utang (Merah) vs Total Piutang (Hijau).
* Tab Toggle: Utang Saya vs Piutang Saya.
* Kartu List: Nama pihak, nominal, tanggal tempo (label merah jika lewat tempo), dan tombol **"Lunasi"**.
