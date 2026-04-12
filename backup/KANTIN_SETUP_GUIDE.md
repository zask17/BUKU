# ✅ Sistem Kantin dengan Cart & Midtrans - Setup Lengkap

## 📌 Apa yang Sudah Selesai

### 1. ✅ Controller (KantinController.php)
- [x] Method `index()` - Tampilkan menu dari semua vendor
- [x] Method `checkout()` - Process order dan generate Midtrans Snap Token
- [x] Method `callback()` - Handle Midtrans webhook untuk konfirmasi pembayaran
- [x] Method `selesai()` - Halaman sukses pembayaran
- [x] Method `pending()` - Halaman menunggu pembayaran
- [x] Method `gagal()` - Halaman pembayaran gagal
- [x] Method `mapPaymentType()` - Map metode pembayaran ke integer

### 2. ✅ Views (Blade Templates)
- [x] `resources/views/guest/order.blade.php`
  - Menu listing dari semua vendor
  - Shopping cart dengan quantity & notes
  - Total price calculator
  - AJAX checkout dengan Midtrans Snap
  
- [x] `resources/views/kantin/selesai.blade.php`
  - Notifikasi pembayaran sukses
  - Button "Pesan Lagi" & "Beranda"
  - Estimasi waktu ready 10-15 menit

- [x] `resources/views/kantin/pending.blade.php`
  - Notifikasi menunggu pembayaran
  - Warning timeout 1 jam
  - Button untuk kembali atau retry

- [x] `resources/views/kantin/gagal.blade.php`
  - Notifikasi pembayaran gagal
  - Daftar kemungkinan penyebab
  - Button "Coba Lagi"

### 3. ✅ Routes (web.php)
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

### 4. ✅ Security & Config
- [x] CSRF Exception untuk `/midtrans/callback`
- [x] Signature verification SHA512 pada callback
- [x] Input validation di checkout
- [x] Midtrans config dengan environment variables

### 5. ✅ Database Models
- [x] `Pesanan` model dengan proper timestamps & relations
- [x] `DetailPesanan` model untuk detail item
- [x] `Vendor` model dengan hasMany menus
- [x] `Menu` model dengan belongsTo vendor

### 6. ✅ Payment Features
- [x] Support Bank Transfer (VA BCA, BNI, BRI, Permata)
- [x] Support E-Wallet (GoPay, ShopeePay)
- [x] Support Credit Card
- [x] Support QRIS
- [x] Transaction status tracking (0=Pending, 1=Lunas, 2=Gagal)

---

## 🚀 Quick Start Guide

### Step 1: Configure Midtrans
Buka file `.env` dan tambahkan:
```env
MIDTRANS_SERVER_KEY=your_server_key_from_midtrans
MIDTRANS_CLIENT_KEY=your_client_key_from_midtrans
MIDTRANS_IS_PRODUCTION=false
```

### Step 2: Clear Cache
```bash
php artisan config:clear
php artisan cache:clear
```

### Step 3: Test di Browser
```
1. Akses: http://localhost:8000/kantin
2. Klik menu untuk menambah ke cart
3. Edit "Catatan" jika ada (contoh: "Pedas", "Tanpa gula")
4. Klik "BAYAR SEKARANG"
5. Selesaikan pembayaran di halaman Midtrans
```

### Step 4: Verify Database
Periksa tabel `pesanan` dan `detail_pesanan` untuk memastikan data tersimpan.

---

## 📋 Feature Checklist

### Frontend (User Experience)
- [x] Menu display dengan vendor grouping
- [x] Shopping cart dengan add/remove items
- [x] Notes per item (Catatan pesanan)
- [x] Real-time total calculator
- [x] Disable checkout jika cart kosong
- [x] Loading state saat processing
- [x] Error handling & user feedback

### Backend (Business Logic)
- [x] Auto-generate guest name (Guest_0000001)
- [x] Order ID untuk referensi (KANTIN-xxxxxxxx)
- [x] Save order header (Pesanan)
- [x] Save order items (DetailPesanan)
- [x] Generate Midtrans Snap Token
- [x] Handle payment success/failure/pending
- [x] Map payment method to database

### Integration
- [x] Midtrans Snap Payment Gateway
- [x] Webhook callback handling
- [x] Signature verification
- [x] HTTP Status handling

### Status Pages
- [x] Success page (selesai)
- [x] Pending page (menunggu konfirmasi)
- [x] Failed page (gagal)

---

## 🔍 API Endpoints

### Public Endpoints
```
GET  /kantin                      → Show menu
POST /kantin/checkout             → Create order & get snap token
GET  /kantin/selesai              → Show success page
GET  /kantin/pending              → Show pending page
GET  /kantin/gagal                → Show failed page
POST /midtrans/callback           → Webhook (no CSRF needed)
```

---

## 📊 Database Flow

### Saat User Checkout
```
1. POST /kantin/checkout
   ├─ Generate nama guest (Guest_XXXXXXX)
   ├─ Create pesanan row
   ├─ Loop cart items → create detail_pesanan rows
   ├─ Configure Midtrans
   └─ Return snap_token

2. Client-side:
   ├─ window.snap.pay(snap_token)
   └─ Show Midtrans Payment UI

3. User bayar di Midtrans

4. Midtrans POST /midtrans/callback
   ├─ Verify signature
   ├─ Find pesanan by order_id
   └─ Update status_bayar (0→1 jika sukses, 0→2 jika gagal)

5. JavaScript callback:
   ├─ onSuccess → redirect /kantin/selesai
   ├─ onPending → redirect /kantin/pending
   └─ onError   → redirect /kantin/gagal
```

---

## 🧪 Test Credentials (Sandbox)

### Credit Card
```
Number: 4111 1111 1111 1111
Expiry: 12/25
CVV:    123
```

### GoPay (Use Midtrans App)
```
Available in Midtrans sandbox dashboard
```

### Virtual Account
Pilih salah satu VA dari Midtrans UI

---

## ⚠️ Important Notes

1. **Timezone:** Pastikan timezone di `config/app.php` sesuai (Asia/Jakarta)
2. **Timestamps:** Field `timestamp` di pesanan & detail_pesanan tidak auto-set oleh Laravel, diset manual via `now()` di controller
3. **CSRF:** Route `/midtrans/callback` sudah di-except dari CSRF verification
4. **Validation:** Input data dari cart di-validate sebelum query database
5. **Transaction:** Menggunakan DB::beginTransaction() untuk atomicity

---

## 🐛 Debugging Tips

### Check Pesanan Created
```bash
# Di terminal
php artisan tinker
>>> App\Models\Pesanan::latest()->first();

# Atau query langsung
SELECT * FROM pesanan ORDER BY idpesanan DESC LIMIT 1;
```

### Check Callback Log
Tambahkan logging di KantinController@callback:
```php
\Log::info('Midtrans Callback', $request->all());
```

Lihat log di: `storage/logs/laravel.log`

---

## 📞 Need Help?

### Common Issues & Solutions

**Issue:** "Snap Token Error"
- [ ] Verify MIDTRANS_SERVER_KEY & MIDTRANS_CLIENT_KEY
- [ ] Run `php artisan config:clear`
- [ ] Check if Midtrans dependency installed: `composer show | grep midtrans`

**Issue:** Callback tidak masuk
- [ ] Check `/midtrans/callback` in CSRF except list
- [ ] Verify URL di Midtrans Dashboard Settings
- [ ] Check Laravel logs

**Issue:** Cart data tidak terkirim
- [ ] Debug: `console.log(keranjang)` di browser
- [ ] Check AJAX payload format
- [ ] Verify form validation di controller

---

**Version:** 1.0  
**Date:** April 12, 2026  
**Status:** ✅ Prod-Ready
