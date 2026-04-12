# 🛒 Setup & Verification Guide - Sistem Kantin + Midtrans

## ✅ Database Schema Verification

Pastikan database Anda memiliki struktur berikut. Jalankan SQL script di bawah jika belum ada:

```sql
-- ✓ Cek Tabel vendor
SELECT * FROM vendor;
-- Expected: 
-- idvendor | nama_vendor
-- 1        | Kantin Sehat
-- 2        | Warung Berkah

-- ✓ Cek Tabel menu
SELECT * FROM menu;
-- Expected columns: idmenu, nama_menu, harga, path_gambar, idvendor

-- ✓ Cek Tabel pesanan  
SELECT * FROM pesanan;
-- Expected columns: idpesanan, nama, timestamp, total, metode_bayar, status_bayar, snap_token, order_id_pg

-- ✓ Cek Tabel detail_pesanan
SELECT * FROM detail_pesanan;
-- Expected columns: iddetail_pesanan, idmenu, idpesanan, jumlah, harga, subtotal, timestamp, catatan
```

---

## 📋 Quick Setup Checklist

### 1. ✅ Database Configuration
- [ ] File `.env` sudah ada dengan konfigurasi database yang benar
- [ ] Koneksi database berhasil (test dengan `php artisan tinker`)

### 2. ✅ Environment Variables
```env
# Database
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=buku
DB_USERNAME=postgres
DB_PASSWORD=yourpassword

# Midtrans
MIDTRANS_SERVER_KEY=your_server_key
MIDTRANS_CLIENT_KEY=your_client_key
MIDTRANS_IS_PRODUCTION=false
```

### 3. ✅ Run Migrations & Seeders
```bash
# Create tables
php artisan migrate

# Seed sample data (opsional)
php artisan db:seed --class=KantinSeeder

# Clear cache
php artisan config:clear
php artisan route:clear
php artisan cache:clear
```

### 4. ✅ Verify Models
Pastikan model files sudah ada di `app/Models/`:
- [x] Vendor.php
- [x] Menu.php
- [x] Pesanan.php
- [x] DetailPesanan.php

### 5. ✅ Verify Controllers
Pastikan controller file sudah ada:
- [x] `app/Http/Controllers/Guest/KantinController.php`

### 6. ✅ Verify Routes
Pastikan routes sudah terdaftar di `routes/web.php`:
- [x] `GET /kantin` → KantinController@index
- [x] `POST /kantin/checkout` → KantinController@checkout
- [x] `GET /kantin/selesai` → KantinController@selesai
- [x] `GET /kantin/pending` → KantinController@pending
- [x] `GET /kantin/gagal` → KantinController@gagal
- [x] `POST /midtrans/callback` → KantinController@callback

### 7. ✅ Verify Views
Pastikan blade templates sudah ada di `resources/views/`:
- [x] `guest/order.blade.php` (Menu + Cart)
- [x] `kantin/selesai.blade.php` (Success page)
- [x] `kantin/pending.blade.php` (Pending page)
- [x] `kantin/gagal.blade.php` (Failed page)

---

## 🧪 Testing Procedure

### Step 1: Test ke Database
```bash
php artisan tinker
>>> App\Models\Vendor::with('menus')->get();
>>> App\Models\Menu::all();
```

Expected output:
- Vendor "Kantin Sehat" dengan 3 menu
- Vendor "Warung Berkah" dengan 2 menu

### Step 2: Test Routes
```bash
# Jalankan server
php artisan serve

# Buka di browser
http://localhost:8000/kantin
```

✅ Harus menampilkan:
- 2 vendor dengan nama
- 5 menu total dari kedua vendor
- Shopping cart sidebar
- "BAYAR SEKARANG" button

### Step 3: Add to Cart
```
1. Klik menu "Nasi Bakar" → "Tambah"
2. Lihat keranjang update
3. Total harus = 15000
4. Test "Hapus" item
```

### Step 4: Checkout Test
```
1. Tambah 1-2 items ke cart
2. Klik "BAYAR SEKARANG"
3. Harus muncul halaman Midtrans Snap
4. Check database:
   - Pesanan baru di tabel pesanan
   - Detail_pesanan records untuk setiap item
   - snap_token sudah tersimpan
```

### Step 5: Payment Callback Test
```
1. Di sandbox Midtrans, simulasi pembayaran
2. Tunggu webhook masuk
3. Check database:
   - status_bayar = 1 (jika sukses)
   - metode_bayar sudah terisi
4. Browser harus redirect ke /kantin/selesai
```

---

## 📊 Database Query Verification

### Cek Vendor & Menu
```sql
SELECT v.idvendor, v.nama_vendor, COUNT(m.idmenu) as jumlah_menu
FROM vendor v
LEFT JOIN menu m ON v.idvendor = m.idvendor
GROUP BY v.idvendor, v.nama_vendor;

-- Expected:
-- idvendor | nama_vendor    | jumlah_menu
-- 1        | Kantin Sehat   | 3
-- 2        | Warung Berkah  | 2
```

### Cek Menu dengan Vendor
```sql
SELECT m.idmenu, m.nama_menu, m.harga, v.nama_vendor
FROM menu m
JOIN vendor v ON m.idvendor = v.idvendor
ORDER BY m.idvendor, m.idmenu;

-- Expected 5 rows dengan detail menu lengkap
```

### Cek Pesanan yang Sudah Dibuat
```sql
SELECT p.idpesanan, p.nama, p.total, p.status_bayar, p.order_id_pg,
       COUNT(dp.iddetail_pesanan) as jumlah_item
FROM pesanan p
LEFT JOIN detail_pesanan dp ON p.idpesanan = dp.idpesanan
GROUP BY p.idpesanan
ORDER BY p.idpesanan DESC
LIMIT 5;
```

### Cek Detail Pesanan
```sql
SELECT dp.iddetail_pesanan, p.idpesanan, m.nama_menu, dp.jumlah, 
       dp.harga, dp.subtotal, dp.catatan
FROM detail_pesanan dp
JOIN pesanan p ON dp.idpesanan = p.idpesanan
JOIN menu m ON dp.idmenu = m.idmenu
WHERE p.idpesanan = 1  -- Ganti dengan idpesanan yang ada
ORDER BY dp.iddetail_pesanan;
```

---

## 🔍 Troubleshooting

### ❌ "Table not found: vendor"
**Solusi:**
```bash
php artisan migrate
php artisan db:seed --class=KantinSeeder
```

### ❌ Menu tidak muncul di halaman
**Cek:**
```bash
# Via Tinker
php artisan tinker
>>> App\Models\Vendor::with('menus')->get()
```

**Kemungkinan:**
- Vendor belum ada → seeder belum dijalankan
- Menu belum di-insert → jalankan seeder
- Relasi di model salah → check foreign key

### ❌ "Snap Token Error"
**Cek:**
```bash
php artisan config:clear
php artisan cache:clear
# Verifikasi MIDTRANS_SERVER_KEY & MIDTRANS_CLIENT_KEY di .env
```

### ❌ Callback tidak masuk
**Cek:**
1. `/midtrans/callback` ada di CSRF exception (VerifyCsrfToken.php)
2. URL callback sudah registrasi di Midtrans Dashboard
3. Laravel log: `storage/logs/laravel.log`

---

## 🛠️ Troubleshooting SQL

### Bersihkan Data Test (Jika Diperlukan)
```sql
-- Hapus semua pesanan & detail (CASCADE delete akan handle)
DELETE FROM pesanan;

-- Reset auto-increment (PostgreSQL)
TRUNCATE TABLE pesanan CASCADE;
ALTER SEQUENCE pesanan_idpesanan_seq RESTART WITH 1;

TRUNCATE TABLE detail_pesanan;
ALTER SEQUENCE detail_pesanan_iddetail_pesanan_seq RESTART WITH 1;
```

### Bersihkan Vendor & Menu Test
```sql
DELETE FROM menu;
DELETE FROM vendor;

-- Reset auto-increment
TRUNCATE TABLE menu CASCADE;
ALTER SEQUENCE menu_idmenu_seq RESTART WITH 1;

TRUNCATE TABLE vendor;
ALTER SEQUENCE vendor_idvendor_seq RESTART WITH 1;

-- Re-seed
-- INSERT INTO vendor (nama_vendor) VALUES (...);
-- INSERT INTO menu (...) VALUES (...);
```

---

## 📝 Final Checklist Before Production

- [ ] Database schema sesuai (`vendor`, `menu`, `pesanan`, `detail_pesanan`)
- [ ] Models sudah created dan relasi benar
- [ ] Controller implementasi checkbox
- [ ] Routes teregistrasi dengan benar
- [ ] Views menampilkan menu dari database
- [ ] Cart functionality working (add/remove items)
- [ ] CSRF exception untuk `/midtrans/callback`
- [ ] Midtrans credentials benar di .env
- [ ] Test checkout dan payment flow
- [ ] Test callback handler
- [ ] Logging configured untuk debug
- [ ] Error handling implemented
- [ ] Database transactions working

---

## 🚀 Deployment Checklist

```bash
# 1. Clear everything
php artisan config:clear
php artisan cache:clear
php artisan view:clear

# 2. Optimize for production
php artisan config:cache
php artisan route:cache

# 3. Set production mode
# Update .env: APP_ENV=production

# 4. Verify Midtrans production keys
# Update .env: MIDTRANS_IS_PRODUCTION=true

# 5. Enable SSL/HTTPS
```

---

## 📞 Support Resources

- **Midtrans Docs:** https://docs.midtrans.com
- **Laravel Eloquent:** https://laravel.com/docs/eloquent
- **PostgreSQL Docs:** https://www.postgresql.org/docs/

---

**Last Updated:** April 12, 2026  
**Status:** ✅ Ready for Testing & Production
