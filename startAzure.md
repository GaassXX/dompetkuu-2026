# Menyalakan DompetKuu di Azure VPS

## 1. Start VM

- Buka Azure Portal
- Pilih Virtual Machine `DompetKuu`
- Klik **Start**
- Tunggu status menjadi **Running**

---

## 2. Login ke VPS

Buka PowerShell:

```powershell
ssh -i "C:\Users\USER\Downloads\DompetKuu_key.pem" dompetkuu@dompetkuu.my.id
```

Atau jika domain bermasalah:

```powershell
ssh -i "C:\Users\USER\Downloads\DompetKuu_key.pem" dompetkuu@70.153.136.228
```

---

## 3. Masuk ke project

```bash
cd ~/dompetkuu-2026
```

---

## 4. Cek container

```bash
docker ps
```

Jika tampil:

- dompetkuu_nginx
- dompetkuu_php
- dompetkuu_db

✅ Website sudah berjalan.

Tidak perlu menjalankan apa pun lagi.

---

## 5. Jika container belum jalan

Jalankan:

```bash
docker compose up -d
```

Lalu cek lagi:

```bash
docker ps
```

---

## 6. Jika ada update dari GitHub

```bash
cd ~/dompetkuu-2026

git pull origin main

cd src

composer install --no-dev --optimize-autoloader

npm install

npm run build

php artisan optimize:clear

php artisan migrate --force

cd ..

docker compose up -d --build
```

---

## 7. Cek website

```
https://dompetkuu.my.id
```

---

# Mematikan VPS

1. Logout SSH

```bash
exit
```

2. Azure Portal

```
Virtual Machine
↓
Stop
```

Jangan menggunakan Delete.

---

# Catatan

Jika restart policy sudah diset:

```
restart: unless-stopped
```

atau

```bash
docker update --restart unless-stopped dompetkuu_db
docker update --restart unless-stopped dompetkuu_php
docker update --restart unless-stopped dompetkuu_nginx
```

Maka setelah VM di-Start biasanya cukup:

- Start VM
- SSH
- `docker ps`

Kalau ketiga container sudah muncul, aplikasi langsung bisa diakses tanpa menjalankan `docker compose up -d`.
