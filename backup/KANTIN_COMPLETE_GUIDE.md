# 🛒 Panduan Lengkap: Sistem Kantin dengan Cart & Midtrans

## ✅ Fitur yang Sudah Diimplementasikan

### **1. Menu Listing**
- ✅ Tampil menu dari semua vendor
- ✅ Grouped by vendor
- ✅ Harga terintegrasi dari database
- ✅ Gambar placeholder (opsional)

### **2. Shopping Cart**
- ✅ Add item ke keranjang
- ✅ Remove item dari keranjang
- ✅ Field catatan per item (Pedas, tanpa gula, dll)
- ✅ Auto-merge item dengan catatan sama
- ✅ Real-time total calculator
- ✅ Sticky cart sidebar

### **3. Checkout Process**
- ✅ Validasi cart tidak kosong
- ✅ Generate unique order ID
- ✅ Generate unique guest name
- ✅ Save header pesanan (Pesanan table)
- ✅ Save detail pesanan (DetailPesanan table)
- ✅ Integration dengan Midtrans Snap

### **4. Payment Gateway (Midtrans)**
- ✅ Generate Snap Token
- ✅ Show payment UI
- ✅ Support: Bank Transfer, E-Wallet, Credit Card, QRIS
- ✅ Handle success/pending/error callback
- ✅ Webhook signature verification

### **5. Order Status Pages**
- ✅ Success page (Pembayaran sukses)
- ✅ Pending page (Menunggu konfirmasi)
- ✅ Failed page (Pembayaran gagal)

---

## 🔧 Tech Stack

| Komponen | Technology |
|----------|-----------|
| **Backend** | Laravel 11 |
| **Database** | PostgreSQL |
| **Frontend** | Blade Template, jQuery, Bootstrap |
| **Payment Gateway** | Midtrans Snap API |
| **Real-time Calculation** | JavaScript |
| **Cart Management** | Client-side Array (localStorage optional) |

---

## 📂 File Structure

```
app/
├── Http/Controllers/Guest/
│   └── KantinController.php          ← Main controller
├── Models/
│   ├── Vendor.php                    ← Vendor model (has many menus)
│   ├── Menu.php                      ← Menu model (belongs to vendor)
│   ├── Pesanan.php                   ← Order header
│   └── DetailPesanan.php             ← Order detail/items

resources/views/
├── guest/
│   └── order.blade.php               ← Menu + Cart UI
└── kantin/
    ├── selesai.blade.php             ← Success page
    ├── pending.blade.php             ← Pending page
    └── gagal.blade.php               ← Failed page

database/
├── migrations/
│   └── 2024_04_12_000000_create_kantin_tables.php
└── seeders/
    └── KantinSeeder.php

routes/
└── web.php                           ← Routes configuration

config/
└── midtrans.php                      ← Midtrans configuration
```

---

## 🚀 Setup & Testing Guide

### Step 1: Environment Configuration

```env
# File: .env

# Database
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=buku
DB_USERNAME=postgres
DB_PASSWORD=yourpassword

# Midtrans (dari https://dashboard.sandbox.midtrans.com)
MIDTRANS_SERVER_KEY=your_server_key_here
MIDTRANS_CLIENT_KEY=your_client_key_here
MIDTRANS_IS_PRODUCTION=false
```

### Step 2: Database Setup

```bash
# Jalankan migrations untuk buat tables
php artisan migrate

# Seed sample data (vendor & menu)
php artisan db:seed --class=KantinSeeder

# Clear cache
php artisan config:clear
php artisan cache:clear
```

### Step 3: Verify Database

```bash
# Check vendors
php artisan tinker
>>> App\Models\Vendor::with('menus')->get()

# Output:
# [
#   {"idvendor": 1, "nama_vendor": "Kantin Sehat", "menus": [...]},
#   {"idvendor": 2, "nama_vendor": "Warung Berkah", "menus": [...]}
# ]
```

### Step 4: Start Application

```bash
php artisan serve
```

### Step 5: Test di Browser

```
http://localhost:8000/kantin
```

---

## 📋 User Journey / Flow

### **1. Halaman Menu (http://localhost:8000/kantin)**

**UI Elements:**
```
┌─────────────────────────────────────┬──────────────────┐
│   DAFTAR MENU KANTIN                │   🛒 KERANJANG   │
├─────────────────────────────────────┤                  │
│ 🏪 KANTIN SEHAT                     │ Item 1: Rp 15k   │
│  □ Nasi Bakar           Rp 15.000   │ Item 2: Rp 12k   │
│    Catatan: [input]                 │ ─────────────────│
│    [+ Tambah]                       │ Total: Rp 27.000 │
│                                     │                  │
│  □ Ayam Geprek          Rp 12.000   │ [BAYAR SEKARANG] │
│    Catatan: [input]                 │                  │
│    [+ Tambah]                       └──────────────────┘
│
│ 🏪 WARUNG BERKAH
│  □ Soto Ayam            Rp 10.000
│    Catatan: [input]
│    [+ Tambah]
└─────────────────────────────────────┘
```

### **2. Add Item to Cart**

**JavaScript Action:**
```javascript
tambahItem(idmenu, nama, harga)
  ↓
Cek jika item + catatan sama → jumlah++
  ↓
Render ulang cart
  ↓
Update total
  ↓
Enable "BAYAR SEKARANG" button
```

**Data Structure:**
```javascript
keranjang = [
  {
    idmenu: 1,
    nama: "Nasi Bakar",
    harga: 15000,
    jumlah: 1,
    subtotal: 15000,
    catatan: "Pedas"
  },
  {
    idmenu: 2,
    nama: "Ayam Geprek",
    harga: 12000,
    jumlah: 1,
    subtotal: 12000,
    catatan: null
  }
]
// Total: 27000
```

### **3. Checkout Process**

**Click "BAYAR SEKARANG":**
```
prosesCheckout()
  ↓
POST /kantin/checkout
  ├─ total_bayar: 27000
  └─ cart: [...]
  ↓
KantinController@checkout
  ├─ Generate Nama: "Guest_0000001"
  ├─ Generate Order ID: "KANTIN-abc123def"
  ├─ Create pesanan row
  ├─ Create detail_pesanan rows (2 items)
  └─ Generate Midtrans Snap Token
  ↓
Response JSON: { snap_token: "xxx..." }
  ↓
window.snap.pay(snap_token)
```

### **4. Midtrans Payment Page**

**User memilih metode pembayaran:**
- 💳 Credit Card
- 🏦 Bank Transfer (VA BCA, BNI, BRI, Permata)
- 📱 E-Wallet (GoPay, ShopeePay)
- 🚀 QR Code (QRIS)

### **5. Payment Confirmation (Webhook)**

**Midtrans POST /midtrans/callback:**
```
KantinController@callback
  ├─ Verify Signature (SHA512)
  ├─ Find pesanan by order_id
  ├─ Update status_bayar:
  │  ├─ settlement/capture → 1 (Lunas) ✓
  │  ├─ pending → 0 (Menunggu)
  │  └─ deny/cancel/expire → 2 (Gagal) ✗
  └─ Save payment method
```

### **6. Redirect to Status Page**

**JavaScript Callback:**
```javascript
window.snap.pay(snap_token, {
  onSuccess: function() {
    // Pembayaran sukses
    window.location.href = "/kantin/selesai"
  },
  onPending: function() {
    // Pembayaran pending
    window.location.href = "/kantin/pending"
  },
  onError: function() {
    // Pembayaran gagal
    window.location.href = "/kantin/gagal"
  }
});
```

---

## 🧪 Testing Scenarios

### **Scenario 1: Berhasil Bayar**

1. Buka http://localhost:8000/kantin
2. Tambah "Nasi Bakar" → Catatan: "Pedas"
3. Tambah "Ayam Geprek" → Tanpa catatan
4. Klik "BAYAR SEKARANG"
5. Halaman Midtrans muncul
6. Pilih "Transfer Bank" → "Virtual Account BCA"
7. Scan/copy virtual account
8. Bayar di bank/e-banking
9. Redirect ke http://localhost:8000/kantin/selesai
10. Cek database: `pesanan.status_bayar = 1`

### **Scenario 2: Pembayaran Pending**

1. Ulangi langkah 1-5
2. Jangan bayar
3. Tunggu timeout
4. Redirect ke http://localhost:8000/kantin/pending
5. Cek database: `pesanan.status_bayar = 0`

### **Scenario 3: Pembayaran Gagal**

1. Ulangi langkah 1-5
2. Klik "X" untuk close
3. Transaksi akan expire dalam 1 jam
4. Redirect ke http://localhost:8000/kantin/gagal
5. Cek database: `pesanan.status_bayar = 2`

---

## 🔍 Database Queries untuk Testing

### Lihat Vendor & Menu
```sql
SELECT v.idvendor, v.nama_vendor, COUNT(m.idmenu) as jumlah_menu
FROM vendor v
LEFT JOIN menu m ON v.idvendor = m.idvendor
GROUP BY v.idvendor, v.nama_vendor;
```

### Lihat Pesanan yang Baru Dibuat
```sql
SELECT p.idpesanan, p.nama, p.total, p.status_bayar, p.order_id_pg,
       COUNT(dp.iddetail_pesanan) as jumlah_item
FROM pesanan p
LEFT JOIN detail_pesanan dp ON p.idpesanan = dp.idpesanan
GROUP BY p.idpesanan
ORDER BY p.idpesanan DESC
LIMIT 5;
```

### Lihat Detail Pesanan
```sql
SELECT dp.iddetail_pesanan, m.nama_menu, dp.jumlah, dp.harga, 
       dp.subtotal, dp.catatan
FROM detail_pesanan dp
JOIN menu m ON dp.idmenu = m.idmenu
WHERE dp.idpesanan = 1  -- Ganti dengan idpesanan yang mau lihat
ORDER BY dp.iddetail_pesanan;
```

---

## ⚙️ Configuration Files

### **config/midtrans.php**
```php
return [
    'server_key' => env('MIDTRANS_SERVER_KEY'),
    'client_key' => env('MIDTRANS_CLIENT_KEY'),
    'is_production' => env('MIDTRANS_IS_PRODUCTION', false),
    'is_sanitized' => true,
    'is_3ds' => true,
];
```

### **routes/web.php - Kantin Routes**
```php
Route::prefix('kantin')->name('kantin.')->group(function () {
    Route::get('/',         [KantinController::class, 'index'])->name('index');
    Route::post('/checkout',[KantinController::class, 'checkout'])->name('checkout');
    Route::get('/selesai',  [KantinController::class, 'selesai'])->name('selesai');
    Route::get('/pending',  [KantinController::class, 'pending'])->name('pending');
    Route::get('/gagal',    [KantinController::class, 'gagal'])->name('gagal');
});

Route::post('/midtrans/callback', [KantinController::class, 'callback'])
    ->name('midtrans.callback');
```

---

## 🛡️ Security Features

✅ **CSRF Protection**: Route `/midtrans/callback` di-exclude dari CSRF  
✅ **Signature Verification**: Callback dari Midtrans di-verify dengan SHA512 hash  
✅ **Input Validation**: Request data di-validate  
✅ **Transaction DB**: Menggunakan `DB::beginTransaction()` untuk atomicity  
✅ **Error Handling**: Try-catch dengan rollback jika gagal  

---

## 📱 Responsive Design

- ✅ Desktop: 2-column layout (menu + cart)
- ✅ Tablet: Responsive grid
- ✅ Mobile: Single column, sticky cart

---

## 🐛 Troubleshooting

### ❌ Menu tidak muncul
**Solusi:**
```bash
php artisan db:seed --class=KantinSeeder
```

### ❌ "Snap Token Error"
**Solusi:**
```bash
php artisan config:clear
# Verifikasi MIDTRANS_SERVER_KEY & MIDTRANS_CLIENT_KEY di .env
```

### ❌ Callback webhook tidak masuk
**Solusi:**
1. Cek `/midtrans/callback` di CSRF except list
2. Cek Midtrans Dashboard → Settings → URL Callback
3. Lihat Laravel log: `storage/logs/laravel.log`

### ❌ Total tidak update di cart
**Solusi:**
```javascript
// Check di browser console:
console.log(keranjang);  // Lihat data di cart
console.log(grandTotal); // Lihat total
```

---

## 📚 Key Functions

### **JavaScript Functions (order.blade.php)**

```javascript
// Tambah item ke keranjang
tambahItem(id, nama, harga)

// Render ulang cart display
renderKeranjang()

// Hapus item dari keranjang
hapusItem(index)

// Submit checkout ke backend
prosesCheckout()
```

### **Controller Methods (KantinController.php)**

```php
// GET /kantin - Tampil menu
index()

// POST /kantin/checkout - Process order & generate snap token
checkout(Request $request)

// POST /midtrans/callback - Handle webhook dari Midtrans
callback(Request $request)

// GET /kantin/selesai - Success page
selesai()

// GET /kantin/pending - Pending page
pending()

// GET /kantin/gagal - Failed page
gagal()

// Private: Map payment type to integer
mapPaymentType(string $type): int
```

---

## 🎯 Next Features (Optional)

- [ ] Add product images
- [ ] Search/filter menu
- [ ] Order history
- [ ] Estimate order ready time
- [ ] SMS/WhatsApp notification
- [ ] Rate & review menu
- [ ] Promo code & discount
- [ ] Admin dashboard

---

## ✅ Verification Checklist

- [x] Database migrated & seeded
- [x] Models configured dengan relationships
- [x] Controller implemented lengkap
- [x] Routes registered
- [x] Views created
- [x] Midtrans configured
- [x] Cart functionality working
- [x] Checkout flow implemented
- [x] Payment pages created
- [x] CSRF exception added
- [x] Error handling implemented
- [x] Cache cleared

**Status: 🟢 READY FOR PRODUCTION**

---

## 📞 Support

- **Midtrans Documentation:** https://docs.midtrans.com
- **Laravel Documentation:** https://laravel.com/docs
- **Sandbox Testing:** https://app.sandbox.midtrans.com

---

**Last Updated:** April 12, 2026  
**Version:** 1.0  
**Status:** ✅ Production Ready
