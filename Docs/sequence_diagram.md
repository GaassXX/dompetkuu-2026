# Dokumentasi Sequence Diagram DompetKuu v2.1
**Sistem**: DompetKuu (Mobile Flutter Personal + Laravel REST API)  
**Versi**: 2.1 (Oktober 2026)

Sequence Diagram berikut menggambarkan interaksi objek secara kronologis antar layer:
`Flutter UI` $\rightarrow$ `HTTP Client (Dio)` $\rightarrow$ `Laravel API Controller` $\rightarrow$ `Service / Eloquent Model` $\rightarrow$ `Database (MySQL)`.

---

## 1. Sequence Diagram: Catat Cepat via Bot Chat
Menggambarkan interaksi saat pengguna mengirim kalimat transaksi alami (misal: *"Makan 15.000 bakso"*) hingga transaksi tersimpan dan bot membalas.

```mermaid
sequenceDiagram
    autonumber
    actor User as Pengguna
    participant UI as Flutter View (ChatScreen)
    participant Client as ApiClient (Dio)
    participant Controller as ChatBotController
    participant Parser as TransactionParserService
    participant Model as Expense / Income Model
    participant DB as Database (MySQL)

    User->>UI: Ketik kalimat ("Makan 15.000 bakso") & Kirim
    UI->>Client: post('/api/chat-bot/parse-and-save', {message})
    Note over Client: Attach Bearer Token Sanctum
    Client->>Controller: HTTP POST Request

    Controller->>Parser: parse(text, userId)
    Note over Parser: 1. Regex nominal (15.000)<br/>2. Deteksi tipe (expense)<br/>3. Sinonim kategori ('bakso' -> Makan & Minum)<br/>4. Bersihkan deskripsi

    alt Format Teks Valid
        Parser-->>Controller: Return Array Parsed Data
        Controller->>Parser: save(parsed, userId)
        Parser->>Model: create([user_id, amount, category_id, status: 'approved'])
        Model->>DB: INSERT INTO expenses (...)
        DB-->>Model: Success (Model Instance)
        Model-->>Parser: Return Transaction Model
        Parser-->>Controller: Return Saved Transaction
        Controller-->>Client: 200 OK (JSON Data + Bot Reply Message)
        Client-->>UI: Response Success
        UI-->>User: Tampilkan Bubble Balasan Bot Sukses & Mini Card
    else Format Tidak Dikenali
        Parser-->>Controller: Return null
        Controller-->>Client: 422 Unprocessable Entity (Saran format)
        Client-->>UI: Response Error
        UI-->>User: Tampilkan Bubble Bot Saran Bantuan
    end
```

---

## 2. Sequence Diagram: Pelunasan Utang / Piutang Otomatis
Menggambarkan interaksi ketika pengguna menekan tombol "Lunasi" pada data utang/piutang, memicu transaksi pelunasan dan otomatisasi arus kas.

```mermaid
sequenceDiagram
    autonumber
    actor User as Pengguna
    participant UI as Flutter View (DebtDetailScreen)
    participant Client as ApiClient (Dio)
    participant Controller as DebtController
    participant Debt as Debt Model
    participant Cashflow as Expense / Income Model
    participant DB as Database (MySQL)

    User->>UI: Klik Tombol "Lunasi" & Konfirmasi
    UI->>Client: post('/api/debts/{id}/pay')
    Client->>Controller: HTTP POST Request

    Controller->>Debt: findOrFail(id)
    Debt->>DB: SELECT * FROM debts WHERE id = ?
    DB-->>Debt: Debt Instance

    Controller->>Debt: markAsPaid()
    Note over Debt: Mulai Database Transaction (DB::transaction)

    alt Tipe adalah Utang (type = 'debt')
        Debt->>Cashflow: Expense::create([amount, category: 'Lainnya', desc: 'Bayar utang...'])
        Cashflow->>DB: INSERT INTO expenses (...)
    else Tipe adalah Piutang (type = 'receivable')
        Debt->>Cashflow: Income::create([amount, category: 'Lainnya', desc: 'Terima piutang...'])
        Cashflow->>DB: INSERT INTO incomes (...)
    end

    Debt->>DB: UPDATE debts SET status = 'paid', paid_at = NOW()
    Note over Debt: Commit Transaction

    Debt-->>Controller: Return Refreshed Debt Model
    Controller-->>Client: 200 OK (Status Berhasil Dilunasi)
    Client-->>UI: Response Success
    UI-->>User: Update Status Menjadi "LUNAS" & Refresh Saldo
```

---

## 3. Sequence Diagram: Autentikasi Login Pengguna Mobile
Menggambarkan proses pertukaran kredensial login dengan token otentikasi Sanctum serta penyimpanan lokal di Android.

```mermaid
sequenceDiagram
    autonumber
    actor User as Pengguna
    participant UI as Flutter View (LoginScreen)
    participant Client as ApiClient (Dio)
    participant Controller as LoginController
    participant UserMod as User Model
    participant DB as Database (MySQL)
    participant SecureStorage as flutter_secure_storage

    User->>UI: Input Email & Password, Tekan "Masuk"
    UI->>Client: post('/api/auth/login', {email, password, device_name})
    Client->>Controller: HTTP POST Request

    Controller->>UserMod: where('email', $email)->first()
    UserMod->>DB: SELECT * FROM users WHERE email = ?
    DB-->>UserMod: User Record

    alt Kredensial Valid & Akun Aktif
        Note over Controller: Hash::check(password, user->password) == true
        Controller->>UserMod: createToken(device_name)
        UserMod->>DB: INSERT INTO personal_access_tokens (...)
        DB-->>UserMod: Token Created
        Controller-->>Client: 200 OK {token: "1|abc...", user: {...}}
        Client->>SecureStorage: write(key: 'token', value: '1|abc...')
        SecureStorage-->>Client: Saved
        Client-->>UI: Login Berhasil
        UI-->>User: Pindah ke Layar Utama (DashboardScreen)
    else Kredensial Salah
        Controller-->>Client: 401 Unauthorized / 422 Error
        Client-->>UI: Response Gagal
        UI-->>User: Tampilkan SnackBar "Email atau password salah"
    end
```

---

## 4. Sequence Diagram: Setor Tabungan (Savings Deposit)
Menggambarkan interaksi saat pengguna menambahkan setoran ke target tabungan hingga perhitungan progres target.

```mermaid
sequenceDiagram
    autonumber
    actor User as Pengguna
    participant UI as Flutter View (SavingDetailScreen)
    participant Client as ApiClient (Dio)
    participant Controller as SavingController
    participant Saving as Saving Model
    participant Deposit as SavingDeposit Model
    participant DB as Database (MySQL)

    User->>UI: Input Nominal Setoran & Tekan "Simpan Setoran"
    UI->>Client: post('/api/savings/{id}/deposits', {amount, note})
    Client->>Controller: HTTP POST Request

    Controller->>Saving: findOrFail(id)
    Saving->>DB: SELECT * FROM savings WHERE id = ?
    DB-->>Saving: Saving Record

    Controller->>Deposit: create([saving_id, amount, note, date: now()])
    Deposit->>DB: INSERT INTO saving_deposits (...)

    Controller->>Saving: increment('current_amount', amount)
    Saving->>DB: UPDATE savings SET current_amount = current_amount + ?

    Note over Controller: Cek apakah current_amount >= target_amount
    opt Jika Target Tercapai
        Controller->>Saving: update(['status' => 'completed'])
        Saving->>DB: UPDATE savings SET status = 'completed'
    end

    Controller-->>Client: 200 OK (Updated Saving & Deposit Data)
    Client-->>UI: Response Success
    UI-->>User: Tampilkan Dialog "Setoran Berhasil" & Animasi Progress Bar
```
