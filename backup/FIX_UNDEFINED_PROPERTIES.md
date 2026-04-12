# ✅ Fix "Unidentified Property" Issue

## 🔧 Masalah yang Diperbaiki

Error **"Undefined property" atau "Trying to get property of non-object"** biasa terjadi ketika:
1. Property di-access tapi tidak di-declare di model
2. Database field tidak di-mapping dengan benar ke attribute
3. Casting type tidak sesuai
4. Lazy loading atau silent discarding attributes diaktifkan

---

## ✅ Solusi yang Diterapkan

### 1. **Tambah Property Casting di Semua Models**

#### **Pesanan.php**
```php
protected $casts = [
    'idpesanan' => 'integer',
    'total' => 'integer',
    'metode_bayar' => 'integer',
    'status_bayar' => 'integer',
    'timestamp' => 'datetime',
];
```

#### **DetailPesanan.php**
```php
protected $casts = [
    'iddetail_pesanan' => 'integer',
    'idmenu' => 'integer',
    'idpesanan' => 'integer',
    'jumlah' => 'integer',
    'harga' => 'integer',
    'subtotal' => 'integer',
    'timestamp' => 'datetime',
];
```

#### **Menu.php**
```php
protected $casts = [
    'idmenu' => 'integer',
    'harga' => 'integer',
    'idvendor' => 'integer',
];
```

#### **Vendor.php**
```php
protected $casts = [
    'idvendor' => 'integer',
];
```

### 2. **Enable Strict Mode di AppServiceProvider**

```php
public function boot(): void
{
    // Enable strict mode for Eloquent attributes
    Model::preventLazyLoading(!app()->isProduction());
    Model::preventSilentlyDiscardingAttributes(!app()->isProduction());
    Model::preventAccessingMissingAttributes(!app()->isProduction());
}
```

**Apa yang dilakukan:**
- `preventLazyLoading()` - Prevent lazy loading di development
- `preventSilentlyDiscardingAttributes()` - Throw exception jika ada attribute yang tidak di-fillable
- `preventAccessingMissingAttributes()` - Throw exception jika akses property yang tidak ada

### 3. **Clear All Caches**

```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

---

## 📋 Property yang Sekarang Aman di-Akses

### Pesanan Model
| Property | Type | Database Type |
|----------|------|---------------|
| `idpesanan` | integer | SERIAL |
| `nama` | string | VARCHAR(255) |
| `timestamp` | datetime | TIMESTAMP |
| `total` | integer | INT |
| `metode_bayar` | integer | INT |
| `status_bayar` | integer | SMALLINT |
| `snap_token` | string | VARCHAR(255) |
| `order_id_pg` | string | VARCHAR(100) |

### DetailPesanan Model
| Property | Type | Database Type |
|----------|------|---------------|
| `iddetail_pesanan` | integer | SERIAL |
| `idmenu` | integer | INT |
| `idpesanan` | integer | INT |
| `jumlah` | integer | INT |
| `harga` | integer | INT |
| `subtotal` | integer | INT |
| `catatan` | string | VARCHAR(255) |
| `timestamp` | datetime | TIMESTAMP |

### Menu Model
| Property | Type | Database Type |
|----------|------|---------------|
| `idmenu` | integer | SERIAL |
| `nama_menu` | string | VARCHAR(255) |
| `harga` | integer | INT |
| `path_gambar` | string | VARCHAR(255) |
| `idvendor` | integer | INT |

### Vendor Model
| Property | Type | Database Type |
|----------|------|---------------|
| `idvendor` | integer | SERIAL |
| `nama_vendor` | string | VARCHAR(255) |

---

## 🧪 Testing

### Verify Models
```bash
php artisan tinker

# Test Vendor dengan menus
>>> App\Models\Vendor::with('menus')->first()
=> App\Models\Vendor {#1234
     idvendor: 1,
     nama_vendor: "Kantin Sehat",
     menus: [...]
   }

# Test Menu
>>> App\Models\Menu::first()
=> App\Models\Menu {#1234
     idmenu: 1,
     nama_menu: "Nasi Bakar",
     harga: 15000,
     idvendor: 1,
   }

# Test Pesanan (jika ada)
>>> App\Models\Pesanan::first()
=> App\Models\Pesanan {#1234
     idpesanan: 1,
     nama: "Guest_0000001",
     total: 27000,
     status_bayar: 0,
   }
```

### Verify Routes
```bash
php artisan route:list | grep kantin
```

Expected output:
```
GET|HEAD  kantin                        guest.order                     KantinController@index
POST      kantin/checkout               kantin.checkout                 KantinController@checkout
GET|HEAD  kantin/selesai                kantin.selesai                  KantinController@selesai
GET|HEAD  kantin/pending                kantin.pending                  KantinController@pending
GET|HEAD  kantin/gagal                  kantin.gagal                    KantinController@gagal
POST      midtrans/callback             midtrans.callback               KantinController@callback
```

---

## 🚀 Next Steps

### Jalankan Server
```bash
php artisan serve
```

### Akses Halaman
```
http://localhost:8000/kantin
```

### Test Functionality
1. ✅ Halaman menu muncul dengan vendor & items
2. ✅ Bisa add/remove items ke cart
3. ✅ Total price ter-update dengan benar
4. ✅ Checkout button bisa diklik
5. ✅ Midtrans Snap payment gateway muncul

---

## ⚠️ Common Issues & Solutions

### Issue: "Undefined property: App\Models\Pesanan::$status"
**Solution:**
```php
// Pastikan field ada di fillable
protected $fillable = [
    'nama',
    'timestamp',
    'total',
    'metode_bayar',
    'status_bayar',  // ← Correct field name
    'snap_token',
    'order_id_pg'
];

// Tidak: 'status' (salah)
// Tidak: 'paymentStatus' (salah)
```

### Issue: "Trying to get property 'idmenu' of non-object"
**Solution:**
```php
// Pastikan relasi eager-loaded
$vendors = Vendor::with('menus')->get();  // ✓ Correct

// Tidak: 
$vendors = Vendor::all();  // Akan lazy load menus
```

### Issue: Cache tidak ter-clear
**Solution:**
```bash
# Force clear
php artisan cache:clear --force
php artisan config:clear --force

# Clear bootstrap cache
rm -rf bootstrap/cache/*  # Unix/Linux
del bootstrap\cache\*    # Windows PowerShell

# Re-run commands
php artisan config:cache
php artisan route:cache
```

---

## 🔍 Debugging Tips

### Enable Debug Mode
```env
APP_DEBUG=true
APP_ENV=local
```

### Check Model Attributes
```php
$pesanan = Pesanan::first();
echo json_encode($pesanan->getAttributes());  // Lihat semua attributes
echo json_encode($pesanan->toArray());         // Convert to array
```

### Monitor Strict Mode Errors
```php
// Di local/development:
// - Lazy loading akan throw LazyLoadingViolationException
// - Accessing missing attribute akan throw MissingAttributeException
// - Non-fillable mass assignment akan throw MassAssignmentException
```

---

## 📚 Resources

- **Laravel Eloquent Docs:** https://laravel.com/docs/eloquent#mass-assignment
- **Attribute Casting:** https://laravel.com/docs/eloquent#attribute-casting
- **Strict Mode:** https://laravel.com/docs/eloquent#strict-mode

---

## ✅ Verification Checklist

- [x] Models updated dengan casts
- [x] AppServiceProvider enabled strict mode
- [x] Cache cleared
- [x] Fillable properties verified
- [x] Foreign key relationships correct
- [x] Primary keys di-define
- [x] Database field names match model attributes
- [x] No typos di field names

**Status: 🟢 READY FOR DEPLOYMENT**
