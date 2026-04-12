# ⚡ Quick Start - Kantin Cart + Midtrans

## 🚀 30-Second Setup

```bash
# 1. Setup database
php artisan migrate
php artisan db:seed --class=KantinSeeder

# 2. Setup Midtrans credentials di .env
MIDTRANS_SERVER_KEY=your_key
MIDTRANS_CLIENT_KEY=your_key
MIDTRANS_IS_PRODUCTION=false

# 3. Clear cache
php artisan config:clear && php artisan cache:clear

# 4. Run server
php artisan serve
```

## 🔗 URLs

| URL | Purpose |
|-----|---------|
| `http://localhost:8000/kantin` | Menu listing + Shopping cart |
| `http://localhost:8000/kantin/selesai` | Success page |
| `http://localhost:8000/kantin/pending` | Pending page |
| `http://localhost:8000/kantin/gagal` | Failed page |

## 📊 Database Tables

```
vendor (idvendor, nama_vendor)
  ↓ 1-to-many
menu (idmenu, nama_menu, harga, idvendor)
  ↓ 1-to-many
detail_pesanan (iddetail_pesanan, idmenu, jumlah, harga, subtotal, catatan)

pesanan (idpesanan, nama, total, status_bayar, snap_token, order_id_pg)
  ↑ 1-to-many
detail_pesanan
```

## 🎯 User Flow

```
1. Visit /kantin 
   ↓
2. Select menu items → Add to cart
   ↓
3. Set catatan (optional)
   ↓
4. Click "BAYAR SEKARANG"
   ↓
5. Choose payment method in Midtrans UI
   ↓
6. Complete payment
   ↓
7. Redirect to status page (selesai/pending/gagal)
```

## 📋 Test Cart Functionality

### Add Item
```javascript
// Click "Tambah" button di menu
tambahItem(1, 'Nasi Bakar', 15000)
// Cart di-update: 1 item
// Total: Rp 15.000
```

### Add Duplicate Item
```javascript
// Click "Tambah" di item yang sudah ada
tambahItem(1, 'Nasi Bakar', 15000)
// Jumlah++: now 2 items
// Total: Rp 30.000
```

### Add with Note
```javascript
// Input "Pedas" di catatan
// Click "Tambah"
tambahItem(1, 'Nasi Bakar', 15000)
// Cart: 1 item dengan catatan "Pedas"
// Catatan auto-clear
```

### Remove Item
```javascript
// Click "Hapus" di cart
hapusItem(0)  // Remove first item
// Cart updated
```

## 🛠️ Key Files

| File | Purpose |
|------|---------|
| `app/Http/Controllers/Guest/KantinController.php` | Logic |
| `resources/views/guest/order.blade.php` | Menu + Cart UI |
| `resources/views/kantin/*.blade.php` | Status pages |
| `app/Models/Pesanan.php` | Order header model |
| `app/Models/DetailPesanan.php` | Order items model |
| `config/midtrans.php` | Midtrans config |
| `database/seeders/KantinSeeder.php` | Sample data |

## 💳 Midtrans Test Credentials

### Credit Card (Sandbox)
```
No: 4111 1111 1111 1111
Exp: 12/25
CVV: 123
```

### Virtual Account
```
Choose any VA from Midtrans UI
```

## 🔧 Common Commands

```bash
# Check routes
php artisan route:list | grep kantin

# Check vendors & menus
php artisan tinker
>>> App\Models\Vendor::with('menus')->get()

# Check pesanan
>>> App\Models\Pesanan::latest()->first()

# Clear cache if needed
php artisan config:clear
php artisan cache:clear
```

## ✅ Features Checklist

- [x] Menu display per vendor
- [x] Add/remove cart items
- [x] Notes per item
- [x] Real-time total calculator
- [x] Checkout dengan Midtrans
- [x] Payment method options
- [x] Success/pending/failed pages
- [x] Webhook callback handling
- [x] Database persistence

## 🎨 UI Components

### Cart Item Display
```
┌─ Item Name (Bold)
├─ Note: "Pedas" (if exists)
├─ 2 x Rp 15.000
└─ Subtotal: Rp 30.000 | [Hapus]
```

### Total Section
```
┌────────────────────────────┐
│ Total: Rp 42.000           │
│ [BAYAR SEKARANG] (enabled) │
└────────────────────────────┘
```

## 🚨 Error Handling

| Error | Solution |
|-------|----------|
| Menu tidak muncul | `php artisan db:seed --class=KantinSeeder` |
| Snap Token error | Check `.env` env variables |
| Callback tidak masuk | Check /midtrans/callback di CSRF except |
| Cart not updating | Check browser console for JS errors |

## 📱 Status Pages

### Success (selesai.blade.php)
```
✓ Pembayaran Berhasil
Pesanan Anda telah diterima dan sedang diproses
Estimasi: 10-15 menit
[Pesan Lagi] [Beranda]
```

### Pending (pending.blade.php)
```
⏳ Pembayaran Tertunda
Silakan selesaikan pembayaran
Timeout: 1 jam
[Kembali] [Beranda]
```

### Failed (gagal.blade.php)
```
✗ Pembayaran Gagal
Kemungkinan: Saldo tidak cukup, Ditolak bank, Expired
[Coba Lagi] [Beranda]
```

## 🔐 Security

- ✅ CSRF exception: `/midtrans/callback`
- ✅ Signature verification: SHA512
- ✅ DB transaction: Atomicity guaranteed
- ✅ Input validation: All data validated

---

**Status: 🟢 READY TO USE**

Buka browser → `http://localhost:8000/kantin` → Mulai pesan! 🎉
