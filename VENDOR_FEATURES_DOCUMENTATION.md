# 📋 DOKUMENTASI FITUR VENDOR - KANTIN SYSTEM

## 🎯 Ringkasan Fitur

Vendor dapat:
1. **Menambah Master Menu** - Create, Read, Update, Delete menu
2. **Melihat Pesanan Lunas** - View pesanan dengan status pembayaran "Lunas" (status = 1)

---

## 🔧 Database Changes

### Migration Baru
File: `database/migrations/2026_04_12_000001_add_iduser_to_vendor.php`

```sql
ALTER TABLE vendor ADD COLUMN iduser BIGINT UNSIGNED NULLABLE;
ALTER TABLE vendor ADD CONSTRAINT fk_vendor_user 
    FOREIGN KEY (iduser) REFERENCES users(iduser) ON DELETE SET NULL;
```

**Tujuan:** Menghubungkan Vendor dengan User yang manage-nya

---

## 📊 Model Relationships

### Vendor Model
```php
// app/Models/Vendor.php
- belongsTo(User::class) // User yang manage vendor ini
- hasMany(Menu::class)   // Menu-menu dari vendor
```

### User Model
```php
// app/Models/User.php
- hasOne(Vendor::class)  // Vendor yang manage user ini
```

### Menu Model
```php
// app/Models/Menu.php
- belongsTo(Vendor::class)
- hasMany(DetailPesanan::class) // Items yang dipesan dari menu ini
```

### DetailPesanan Model
```php
// app/Models/DetailPesanan.php
- belongsTo(Pesanan::class)
- belongsTo(Menu::class)
```

### Pesanan Model
```php
// app/Models/Pesanan.php
- hasMany(DetailPesanan::class)
- getStatusNama() // Helper method untuk nama status
```

---

## 🛣️ Routes

### Vendor Routes Group
```php
Route::group(['prefix' => 'vendor', 'middleware' => ['auth', 'role:3']], function () {
    // Dashboard
    Route::get('/dashboard', [DashboardVendorController::class, 'index'])->name('vendor.dashboard');
    
    // Menu CRUD
    Route::resource('menu', MenuController::class);
    // GET    /vendor/menu              -> MenuController@index
    // GET    /vendor/menu/create       -> MenuController@create
    // POST   /vendor/menu              -> MenuController@store
    // GET    /vendor/menu/{menu}       -> MenuController@show (optional)
    // GET    /vendor/menu/{menu}/edit  -> MenuController@edit
    // PUT    /vendor/menu/{menu}       -> MenuController@update
    // DELETE /vendor/menu/{menu}       -> MenuController@destroy
});
```

---

## 🎮 Controllers

### 1. DashboardVendorController

**Location:** `app/Http/Controllers/Vendor/DashboardVendorController.php`

**Methods:**

```php
public function index()
```
- **Purpose:** Menampilkan dashboard vendor dengan statistik dan pesanan lunas
- **Auth:** `auth`, Role 3 (Vendor)
- **Returns:** View dengan:
  - `$vendor` - Data vendor dari user yang login
  - `$pesananLunas` - Pesanan dengan status_bayar = 1 (grouped by idpesanan)
  - `$stats` - Array statistik:
    - `total_menu` - Jumlah menu
    - `total_pesanan_lunas` - Jumlah pesanan yang lunas
    - `total_pendapatan` - Total revenue dari pesanan lunas
    - `pesanan_hari_ini` - Pesanan lunas hari ini

**Logic:**
```php
// Get menu IDs dari vendor login
$menuIds = Auth::user()->vendor->menus()->pluck('idmenu')->toArray();

// Get detail pesanan lunas dari menu vendor (grouped)
DetailPesanan::whereIn('idmenu', $menuIds)
    ->with(['pesanan', 'menu'])
    ->whereHas('pesanan', fn($q) => $q->where('status_bayar', 1))
    ->orderBy('timestamp', 'desc')
    ->get()
    ->groupBy('idpesanan');
```

### 2. MenuController

**Location:** `app/Http/Controllers/Vendor/MenuController.php`

**Methods:**

#### `index()`
- **Purpose:** List semua menu milik vendor
- **Validation:** Vendor harus terdaftar
- **Query Filter:** `Menu::where('idvendor', $vendor->idvendor)`

#### `create()`
- **Purpose:** Tampilkan form tambah menu
- **Validation:** Vendor harus terdaftar

#### `store(Request $request)` ✨ NEW
- **Purpose:** Simpan menu baru
- **Validation:**
  - `nama_menu` - required|string|max:255
  - `harga` - required|integer|min:500
  - `gambar` - nullable|image|mimes:jpeg,png,jpg|max:2048
- **Logic:**
  - Auto set `idvendor` dari vendor yang login
  - Upload gambar ke folder `storage/app/public/menu`
  - Save ke database

#### `edit(Menu $menu)` ✨ NEW
- **Purpose:** Tampilkan form edit menu
- **Authorization:** Check vendor ownership
  - Vendor hanya bisa edit menu-nya sendiri
  - Jika bukan punya dia, redirect dengan error

#### `update(Request $request, Menu $menu)` ✨ NEW
- **Purpose:** Update menu
- **Authorization:** Cek vendor ownership
- **Validation:** Sama seperti store
- **Logic:** Update data + upload gambar jika ada

#### `destroy(Menu $menu)` ✨ NEW
- **Purpose:** Hapus menu
- **Authorization:** Cek vendor ownership
- **Logic:** Delete menu

---

## 📁 Views

### 1. `resources/views/vendor/menu/index.blade.php` ✨ NEW

**Purpose:** List menu vendor

**Features:**
- Tombol "Tambah Menu" untuk create
- Tabel dengan:
  - Nomor urut
  - Nama menu
  - Harga (formatted dengan Rp)
  - Thumbnail gambar (50x50px)
  - Tombol edit dan delete per row
- Alert success/error messages
- Empty state dengan link ke create

### 2. `resources/views/vendor/menu/create.blade.php` ✨ NEW

**Purpose:** Form tambah menu baru

**Fields:**
- Nama Menu (text, required)
- Harga (number, min 500, required)
- Gambar (file upload, optional)
- Preview gambar saat dipilih
- Info vendor yang active

**Features:**
- Image preview dengan JavaScript
- Validasi Bootstrap
- Tombol Simpan dan Batal
- Custom file input styling

### 3. `resources/views/vendor/menu/edit.blade.php` ✨ NEW

**Purpose:** Form edit menu existing

**Fields:** Sama seperti create

**Features:**
- Tampilkan gambar saat ini (jika ada)
- Preview gambar baru saat pilih
- Info vendor readonly
- Tombol Simpan Perubahan dan Batal

### 4. `resources/views/vendor/dashboard-vendor.blade.php` (UPDATED) 🔄

**Purpose:** Dashboard vendor dengan statistik dan pesanan

**Statistics Cards:**
1. Total Menu
2. Pesanan (Lunas) - count
3. Pesanan Hari Ini - count
4. Total Pendapatan - sum dari subtotal

**Pesanan Lunas Table:**
- Order ID
- Tanggal pesanan
- Item Pesanan (list dengan qty dan catatan)
- Total pesanan
- Metode pembayaran

**Action Buttons:**
- Kelola Menu → ke index menu
- Tambah Menu Baru → ke create menu

---

## 🔐 Security & Authorization

### Vendor Menu Access Control

**Middleware:** `auth`, `role:3`

**Controller Checks:**
```php
private function getVendor() {
    return Auth::user()->vendor;
}

// Check di setiap method
if (!$vendor) {
    return redirect()->with('error', 'Anda belum terdaftar sebagai vendor');
}

// Check ownership untuk edit/update/delete
if ($menu->idvendor !== $vendor->idvendor) {
    return redirect()->with('error', 'Anda tidak memiliki akses ke menu ini');
}
```

---

## 📈 Data Flow

### Tambah Menu
```
Vendor (User role:3)
    ↓
GET /vendor/menu/create (form)
    ↓
POST /vendor/menu (store)
    ↓
Menu::create([
    'nama_menu' => ...,
    'harga' => ...,
    'path_gambar' => ...,
    'idvendor' => Auth::user()->vendor->idvendor  ← Auto dari user
])
    ↓
Redirect ke index dengan success message
```

### Lihat Pesanan Lunas
```
Vendor (User role:3)
    ↓
GET /vendor/dashboard
    ↓
DashboardVendorController@index
    ↓
Query DetailPesanan
    - Join dengan pesanan (status_bayar = 1)
    - Filter by vendor's menus
    - Group by idpesanan
    - With pesanan & menu data
    ↓
Display di dashboard-vendor.blade.php
    - Statistik cards
    - Pesanan table dengan detail items
```

---

## 💡 Usage Examples

### Untuk Vendor

#### 1. Menambah Menu
```
1. Login dengan akun vendor (role: 3)
2. Buka "Vendor" → "Kelola Menu"
3. Klik "Tambah Menu"
4. Isi:
   - Nama Menu: "Nasi Bakar"
   - Harga: 15000
   - Gambar: (upload optional)
5. Klik "Simpan Menu"
```

#### 2. Melihat Pesanan Lunas
```
1. Login dengan akun vendor
2. Buka "Vendor" → "Dashboard"
3. Lihat statistik di cards
4. Scroll ke bawah, lihat tabel "Pesanan dengan Status Lunas"
5. Lihat detail:
   - Order ID
   - Tanggal & jam pesanan
   - Item yang dipesan (qty & catatan)
   - Total nominal
   - Metode pembayaran
```

---

## 🐛 Troubleshooting

### Error: "Anda belum terdaftar sebagai vendor"
**Penyebab:** User tidak memiliki vendor record
**Solusi:** 
1. Admin perlu create vendor record via database: 
   ```sql
   INSERT INTO vendor (nama_vendor, iduser) VALUES ('Nama Vendor', userId);
   ```
2. Atau buat endpoint admin untuk create vendor

### Menu tidak muncul di list
**Penyebab:** Menu belum ter-assign ke vendor user
**Solusi:** 
1. Check database: `SELECT * FROM menu WHERE idvendor = ?`
2. Ensure menu's idvendor sesuai dengan vendor user

### Pesanan tidak muncul di dashboard
**Penyebab:** 
1. Pesanan belum lunas (status_bayar ≠ 1)
2. Pesanan dari menu vendor lain
**Solusi:**
1. Check Midtrans callback sudah update status_bayar
2. Check menu ownership (detail_pesanan.idmenu → menu.idvendor)

---

## 📝 Next Steps (Optional Features)

1. **Edit Pesanan Status** - Vendor bisa update status pesanan (pending/siap/selesai)
2. **Print Invoice** - Vendor bisa print pesanan
3. **Laporan Penjualan** - Export penjualan per tanggal/minggu/bulan
4. **Notifikasi Pesanan** - Alert saat ada pesanan baru
5. **Rating Produk** - Customer bisa rate menu

---

## 📚 File Summary

| File | Status | Purpose |
|------|--------|---------|
| `app/Models/Vendor.php` | ✅ Updated | Add user relationship |
| `app/Models/User.php` | ✅ Updated | Add vendor relationship |
| `app/Models/Menu.php` | ✅ Updated | Add detailPesanan relationship |
| `app/Models/Pesanan.php` | ✅ Updated | Add details relationship & helper |
| `app/Models/DetailPesanan.php` | ✅ Exists | No change needed |
| `app/Http/Controllers/Vendor/MenuController.php` | ✅ Updated | Add CRUD logic |
| `app/Http/Controllers/Vendor/DashboardVendorController.php` | ✅ Updated | Add vendor-specific queries |
| `resources/views/vendor/menu/index.blade.php` | ✨ New | Menu list view |
| `resources/views/vendor/menu/create.blade.php` | ✨ New | Menu create form |
| `resources/views/vendor/menu/edit.blade.php` | ✨ New | Menu edit form |
| `resources/views/vendor/dashboard-vendor.blade.php` | 🔄 Updated | Add pesanan lunas table |
| `database/migrations/2026_04_12_000001_add_iduser_to_vendor.php` | ✨ New | Add iduser to vendor |
| `routes/web.php` | ✅ Exists | Sudah ada vendor route group |

---

## ✅ Checklist Implementasi

- [x] Create migration untuk add iduser ke vendor
- [x] Update Vendor model dengan relationships
- [x] Update User model dengan vendor relationship
- [x] Update Menu model dengan detailPesanan relationship
- [x] Update Pesanan model dengan details relationship
- [x] Update MenuController dengan vendor-specific logic
- [x] Update DashboardVendorController dengan vendor queries
- [x] Create menu index view
- [x] Create menu create form view
- [x] Create menu edit form view
- [x] Update dashboard view dengan pesanan table
- [x] Add authorization checks di controller

---

## 🚀 Installation Steps

```bash
# 1. Run migration
php artisan migrate

# 2. Clear cache
php artisan config:clear && php artisan cache:clear

# 3. Test dengan vendor user (role: 3)
# - Login ke /login
# - Buka /vendor/dashboard
# - Test tambah/edit/delete menu
# - Lihat pesanan lunas di dashboard
```

---

**Last Updated:** April 12, 2026  
**Version:** 1.0 - Initial Release  
**Status:** ✅ Production Ready
