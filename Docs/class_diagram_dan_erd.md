# Dokumentasi Class Diagram & ERD DompetKuu v2.1
**Sistem**: DompetKuu (Mobile Flutter Personal + Laravel REST API)  
**Versi**: 2.1 (Oktober 2026)

Dokumen ini memuat arsitektur struktur kode perangkat lunak (**Class Diagram**) dan rancangan struktur basis data relasional (**Entity Relationship Diagram / ERD**).

---

## 1. Class Diagram (Struktur Kelas Backend & Mobile)

Diagram kelas berikut menampilkan Controller API, Service Layer, dan Eloquent Model beserta atribut (*attributes*) dan fungsi (*methods*), serta relasi asosiasi dan dependensinya.

```mermaid
classDiagram
    direction TB

    %% Controller Layer
    class ChatBotController {
        +parseAndSave(Request request, TransactionParserService parser) JsonResponse
    }

    class DebtController {
        +index(Request request) JsonResponse
        +store(Request request) JsonResponse
        +show(Request request, int id) JsonResponse
        +update(Request request, int id) JsonResponse
        +destroy(Request request, int id) JsonResponse
        +pay(Request request, int id) JsonResponse
    }

    class TransactionController {
        +index(Request request) JsonResponse
        +store(Request request) JsonResponse
        +show(Request request, int id) JsonResponse
        +update(Request request, int id) JsonResponse
        +destroy(Request request, int id) JsonResponse
    }

    class SavingController {
        +index(Request request) JsonResponse
        +store(Request request) JsonResponse
        +show(Request request, int id) JsonResponse
        +update(Request request, int id) JsonResponse
        +destroy(Request request, int id) JsonResponse
        +deposits(Request request, int id) JsonResponse
        +deposit(Request request, int id) JsonResponse
    }

    class AuthController {
        +login(Request request) JsonResponse
        +register(Request request) JsonResponse
        +logout(Request request) JsonResponse
        +me(Request request) JsonResponse
    }

    %% Service Layer
    class TransactionParserService {
        #array synonyms
        +parse(string text, int userId) array
        +detectCategory(string text, string type, int userId) Category
        +save(array parsed, int userId) Model
    }

    %% Model Layer
    class User {
        +int id
        +string name
        +string email
        +string password
        +string role
        +bool is_active
        +incomes() HasMany
        +expenses() HasMany
        +debts() HasMany
        +savings() HasMany
        +budgets() HasMany
    }

    class Category {
        +int id
        +string name
        +string type
        +bool is_global
        +int created_by
        +incomes() HasMany
        +expenses() HasMany
    }

    class Expense {
        +int id
        +int user_id
        +int category_id
        +int debt_id
        +decimal amount
        +string description
        +datetime date
        +string status
        +string source
        +user() BelongsTo
        +category() BelongsTo
        +debt() BelongsTo
    }

    class Income {
        +int id
        +int user_id
        +int category_id
        +int debt_id
        +decimal amount
        +string description
        +datetime date
        +string status
        +string source
        +user() BelongsTo
        +category() BelongsTo
        +debt() BelongsTo
    }

    class Debt {
        +int id
        +int user_id
        +string type
        +string person_name
        +decimal amount
        +date date
        +date due_date
        +string description
        +string status
        +datetime paid_at
        +user() BelongsTo
        +expenses() HasMany
        +incomes() HasMany
        +isPaid() bool
        +isOverdue() bool
        +markAsPaid() Debt
    }

    class Saving {
        +int id
        +int user_id
        +string name
        +decimal target_amount
        +decimal current_amount
        +date target_date
        +string status
        +user() BelongsTo
        +deposits() HasMany
    }

    class SavingDeposit {
        +int id
        +int saving_id
        +decimal amount
        +date date
        +string note
        +saving() BelongsTo
    }

    class Budget {
        +int id
        +int user_id
        +int category_id
        +decimal limit_amount
        +string period
        +user() BelongsTo
        +category() BelongsTo
    }

    %% Relasi Controller -> Service & Model
    ChatBotController ..> TransactionParserService : uses
    TransactionParserService ..> Category : searches
    TransactionParserService ..> Expense : creates
    TransactionParserService ..> Income : creates

    DebtController ..> Debt : manages
    TransactionController ..> Expense : manages
    TransactionController ..> Income : manages
    SavingController ..> Saving : manages
    SavingController ..> SavingDeposit : manages
    AuthController ..> User : authenticates

    %% Relasi Antar Model
    User "1" --> "*" Income : has
    User "1" --> "*" Expense : has
    User "1" --> "*" Debt : has
    User "1" --> "*" Saving : has
    User "1" --> "*" Budget : has

    Category "1" --> "*" Income : categorized
    Category "1" --> "*" Expense : categorized
    Category "1" --> "*" Budget : sets limit

    Debt "1" --> "*" Expense : generates on pay
    Debt "1" --> "*" Income : generates on pay
    Saving "1" --> "*" SavingDeposit : accumulates
```

---

## 2. Entity Relationship Diagram (ERD)

Diagram ERD berikut memodelkan skema basis data relasional MariaDB/MySQL yang digunakan oleh DompetKuu v2.1.

```mermaid
erDiagram
    USERS ||--o{ INCOMES : "records"
    USERS ||--o{ EXPENSES : "records"
    USERS ||--o{ DEBTS : "tracks"
    USERS ||--o{ SAVINGS : "creates"
    USERS ||--o{ BUDGETS : "defines"
    USERS ||--o{ CATEGORIES : "customizes"

    CATEGORIES ||--o{ INCOMES : "classifies"
    CATEGORIES ||--o{ EXPENSES : "classifies"
    CATEGORIES ||--o{ BUDGETS : "limits"

    DEBTS ||--o{ EXPENSES : "pays (debt)"
    DEBTS ||--o{ INCOMES : "receives (receivable)"

    SAVINGS ||--o{ SAVING_DEPOSITS : "contains"

    USERS {
        bigint id PK
        string name
        string email UK
        string password
        enum role "admin,personal,parent,child"
        bigint parent_id FK "nullable"
        boolean is_active
        string google_id "nullable"
        timestamp created_at
        timestamp updated_at
    }

    CATEGORIES {
        bigint id PK
        string name
        enum type "income,expense"
        boolean is_global
        bigint created_by FK "nullable"
        boolean is_active
        timestamp created_at
        timestamp updated_at
    }

    INCOMES {
        bigint id PK
        bigint user_id FK
        bigint category_id FK "nullable"
        bigint debt_id FK "nullable"
        decimal amount "15,2"
        string description
        datetime date
        enum status "pending,approved,rejected"
        string source "web,api,bot"
        timestamp created_at
        timestamp updated_at
    }

    EXPENSES {
        bigint id PK
        bigint user_id FK
        bigint category_id FK "nullable"
        bigint debt_id FK "nullable"
        decimal amount "15,2"
        string description
        datetime date
        enum status "pending,approved,rejected"
        string source "web,api,bot"
        timestamp created_at
        timestamp updated_at
    }

    DEBTS {
        bigint id PK
        bigint user_id FK
        enum type "debt,receivable"
        string person_name
        decimal amount "15,2"
        date date
        date due_date "nullable"
        text description "nullable"
        enum status "unpaid,paid"
        datetime paid_at "nullable"
        timestamp created_at
        timestamp updated_at
    }

    SAVINGS {
        bigint id PK
        bigint user_id FK
        string name
        decimal target_amount "15,2"
        decimal current_amount "15,2"
        date target_date "nullable"
        enum status "active,completed"
        timestamp created_at
        timestamp updated_at
    }

    SAVING_DEPOSITS {
        bigint id PK
        bigint saving_id FK
        bigint user_id FK "nullable"
        decimal amount "15,2"
        date date
        string note "nullable"
        timestamp created_at
        timestamp updated_at
    }

    BUDGETS {
        bigint id PK
        bigint user_id FK
        bigint category_id FK
        decimal limit_amount "15,2"
        enum period "weekly,monthly"
        timestamp created_at
        timestamp updated_at
    }
```

---

## 3. Penjelasan Desain Arsitektur & Relasi

1. **Relasi Arus Kas Otomatis Utang (`debt_id` di `incomes` & `expenses`):**
   - Kolom `debt_id` pada tabel `incomes` dan `expenses` berperan sebagai foreign key opsional (*nullable*). Saat utang dilunasi, baris transaksi yang tercipta langsung tertaut ke ID utang terkait, memudahkan pelacakan asal-usul pengeluaran/pemasukan.
2. **Kategori Global vs Personal:**
   - Kategori dengan `is_global = true` dapat digunakan oleh seluruh pengguna aplikasi mobile. Pengguna juga dapat membuat kategori khusus pribadi (`created_by = user_id`).
3. **Penyederhanaan Role Mobile (Personal):**
   - Transaksi yang dimasukkan dari aplikasi mobile Flutter otomatis memiliki `status = 'approved'` dan `source = 'api'` atau `'bot'`, sehingga langsung masuk ke kalkulasi saldo tanpa memerlukan alur persetujuan.
