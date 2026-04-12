# 📋 Dokumentasi Sistem Kantin - Cart & Midtrans Payment

## 🎯 Overview
Sistem kantin yang sudah dilengkapi dengan fitur:
- ✅ Menu management per vendor
- ✅ Shopping cart dengan note/catatan
- ✅ Middleware CSRF exception untuk Midtrans callback
- ✅ Integrasi Midtrans Snap Payment Gateway
- ✅ Status tracking pembayaran (pending, lunas, gagal)
- ✅ Halaman status pembayaran terpisah

---

## 📁 Struktur File

### Controllers
```
app/Http/Controllers/Guest/
├── KantinController.php      ← Controller utama untuk kantin
├── PaymentController.php     ← Controller untuk callback pembayaran
└── OrderController.php       ← Controller alternatif (backup)
```

### Views
```
resources/views/
├── guest/
│   ├── order.blade.php       ← Halaman menu + cart
│   ├── kategori-guest.blade.php
│   └── buku-guest.blade.php
└── kantin/
    ├── selesai.blade.php     ← Halaman sukses pembayaran
    ├── pending.blade.php     ← Halaman menunggu pembayaran
    └── gagal.blade.php       ← Halaman pembayaran gagal
```

### Models
```
app/Models/
├── Pesanan.php           ← Header pesanan
├── DetailPesanan.php     ← Detail item pesanan
├── Menu.php
├── Vendor.php
└── ...
```

### Routes
```
routes/
└── web.php
    ├── kantin.index      → GET /kantin               (Halaman menu)
    ├── kantin.checkout   → POST /kantin/checkout     (Process checkout)
    ├── kantin.selesai    → GET /kantin/selesai       (Sukses)
    ├── kantin.pending    → GET /kantin/pending       (Pending)
    ├── kantin.gagal      → GET /kantin/gagal         (Gagal)
    └── midtrans.callback → POST /midtrans/callback   (Webhook)
```

---

## 🔧 Konfigurasi

### 1. Environment Variables (.env)
```env
# Midtrans Configuration
MIDTRANS_SERVER_KEY=your_server_key_here
MIDTRANS_CLIENT_KEY=your_client_key_here
MIDTRANS_IS_PRODUCTION=false  # Gunakan 'false' untuk sandbox
```

### 2. Config File (config/midtrans.php)
```php
return [
    'server_key' => env('MIDTRANS_SERVER_KEY'),
    'client_key' => env('MIDTRANS_CLIENT_KEY'),
    'is_production' => env('MIDTRANS_IS_PRODUCTION', false),
    'is_sanitized' => true,
    'is_3ds' => true,
];
```

### 3. CSRF Exception (app/Http/Middleware/VerifyCsrfToken.php)
```php
protected $except = [
    'midtrans/callback',  // ← Webhook dari Midtrans tidak perlu CSRF
];
```

---

## 📊 Database Structure

### Tabel: pesanan
```sql
CREATE TABLE pesanan (
    idpesanan BIGINT PRIMARY KEY AUTO_INCREMENT,
    nama VARCHAR(50),                    -- Guest_0000001
    timestamp DATETIME,
    total BIGINT,                         -- Total pembayaran
    metode_bayar INT DEFAULT 0,          -- 1=VA, 2=CC, 3=GoPay, 4=QRIS
    status_bayar INT DEFAULT 0,          -- 0=Pending, 1=Lunas, 2=Gagal
    snap_token VARCHAR(255),             -- Token dari Midtrans
    order_id_pg VARCHAR(50) UNIQUE       -- Order ID untuk Midtrans
);
```

### Tabel: detail_pesanan
```sql
CREATE TABLE detail_pesanan (
    iddetail_pesanan BIGINT PRIMARY KEY AUTO_INCREMENT,
    idmenu BIGINT,
    idpesanan BIGINT,
    jumlah INT,
    harga BIGINT,
    subtotal BIGINT,
    catatan TEXT NULL,     -- Contoh: "Pedas", "Tidak pake cabai"
    timestamp DATETIME,
    FOREIGN KEY (idmenu) REFERENCES menu(idmenu),
    FOREIGN KEY (idpesanan) REFERENCES pesanan(idpesanan)
);
```

---

## 🔄 Flow Pesanan

### 1. User Melihat Menu
```
GET /kantin 
  ↓
KantinController@index
  ↓
views/guest/order.blade.php
  ↓
Tampilkan Vendors & Menus
```

### 2. User Menambah Ke Cart
```
JavaScript: tambahItem()
  ↓
Simpan ke array keranjang (client-side)
  ↓
Render ulang tampilan cart
```

### 3. User Checkout
```
POST /kantin/checkout
  ↓
KantinController@checkout
  ├─ Generate Nama Guest
  ├─ Buat Pesanan di Database
  ├─ Buat Detail Pesanan Per Item
  ├─ Konfigurasi Midtrans
  └─ Generate Snap Token
  ↓
Return snap_token ke AJAX
  ↓
JavaScript: snap.pay() → Halaman Midtrans
```

### 4. User Membayar di Midtrans
```
Midtrans Payment Gateway
  ├─ Transfer Bank
  ├─ E-Wallet (GoPay, ShopeePay)
  ├─ Credit Card
  └─ QR Code (QRIS)
```

### 5. Midtrans Callback
```
POST /midtrans/callback (Webhook)
  ↓
KantinController@callback
  ├─ Validasi Signature
  ├─ Cari Order di Database
  ├─ Update Status Pembayaran
  │  ├─ settlement → status_bayar = 1 (✓ Lunas)
  │  ├─ pending   → status_bayar = 0 (🕐 Pending)
  │  └─ deny/expire/cancel → status_bayar = 2 (✗ Gagal)
  └─ Return OK
```

### 6. Redirect Ke Halaman Status
```
Berhasil  → GET /kantin/selesai  → views/kantin/selesai.blade.php ✓
Pending   → GET /kantin/pending  → views/kantin/pending.blade.php 🕐
Gagal     → GET /kantin/gagal    → views/kantin/gagal.blade.php   ✗
```

---

## 💳 Payment Methods Didukung

> Diatur di `KantinController@checkout` dalam `$params['enabled_payments']`

```javascript
'enabled_payments' => [
    'credit_card',        // Kartu Kredit/Debit
    'gopay',              // GoPay
    'shopeepay',          // ShopeePay
    'permata_va',         // Virtual Account Permata
    'bca_va',             // Virtual Account BCA
    'bni_va',             // Virtual Account BNI
    'bri_va',             // Virtual Account BRI
    'other_va',           // Virtual Account lainnya
],
```

---

## 🧪 Testing

### 1. Setup Sandbox Midtrans
```
1. Daftar di https://dashboard.sandbox.midtrans.com
2. Ambil Server Key & Client Key
3. Masukkan ke file .env
4. Set MIDTRANS_IS_PRODUCTION=false
```

### 2. Test Flow
```
1. Buka http://localhost:8000/kantin
2. Tambahkan menu ke cart (minimal 1 item)
3. Klik "BAYAR SEKARANG"
4. Gunakan test credentials di dashboard Midtrans
5. Verifikasi status di database tabel pesanan
```

### 3. Test Credentials (Sandbox)
```
Kartu Credit Test:
  No: 4111 1111 1111 1111
  Exp: 12/25
  CVV: 123

GoPay / E-Wallet:
  Phone: Sesuai akun sandbox Midtrans
```

---

## 📝 Method Maps (mapPaymentType)

```
bank_transfer  → 1 (Transfer Bank)
credit_card    → 2 (Kartu Kredit)
gopay          → 3 (GoPay)
qris/other_va  → 4 (QRIS/Virtual Account)
default        → 0 (Unknown)
```

---

## ⚙️ Troubleshooting

### ❌ "Snap Token Error" / "Signature Verification Failed"
**Solusi:**
- Pastikan MIDTRANS_SERVER_KEY & MIDTRANS_CLIENT_KEY benar di .env
- Clear config cache: `php artisan config:clear`
- Periksa VerifyCsrfToken middleware sudah exclude 'midtrans/callback'

### ❌ Callback Tidak Masuk
**Solusi:**
- Pastikan /midtrans/callback di CSRF except
- Debug: Tambahkan logging di callback method
- Cek Midtrans Dashboard → Settings → Configuration untuk URL callback

### ❌ Cart Data Tidak Terkirim
**Solusi:**
- Pastikan format data cart (JSON) sesuai dengan backend validation
- Debug di browser console: `console.log(keranjang)`
- Periksa AJAX error response

### ❌ Pesanan Tidak Tersimpan di Database
**Solusi:**
- Pastikan Pesanan & DetailPesanan model fillable benar
- Check table schema sesuai dengan migration
- Lihat Laravel error log: `storage/logs/laravel.log`

---

## 🔐 Security Notes

1. **CSRF Exception:** Hanya `/midtrans/callback` yang di-except
2. **Signature Verification:** Semua callback dari Midtrans di-verify dengan hash SHA512
3. **Input Validation:** Semua request di-validate di controller
4. **HTTPS:** Gunakan HTTPS di production untuk Midtrans

---

## 📞 Support Channels

- **Midtrans Docs:** https://docs.midtrans.com
- **Midtrans Support:** support@midtrans.com
- **Sandbox URL:** https://app.sandbox.midtrans.com

---

## 🚀 Next Steps

Anda bisa menambahkan:
- [ ] Admin Dashboard untuk melihat pesanan masuk
- [ ] Notifikasi real-time ke user (WA Bot, Email)
- [ ] Print struk pembayaran/pesanan
- [ ] Estimasi waktu ready pesanan
- [ ] Review & rating per menu
- [ ] Promo code / discount system
- [ ] Recurring order / subscription

---

**Last Updated:** April 2026
**Version:** 1.0
