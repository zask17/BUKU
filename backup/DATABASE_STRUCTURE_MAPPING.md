# 🔄 Database Structure Mapping

## Schema Alignment Check

### ✅ Tabel: vendor
```
Database Schema:
  idvendor        SERIAL NOT NULL (PRIMARY KEY)
  nama_vendor     VARCHAR(255) NOT NULL

Blade View Usage:
  $vendor->idvendor         ✓
  $vendor->nama_vendor      ✓
  
Model (Vendor.php):
  protected $primaryKey = 'idvendor'   ✓
  protected $table = 'vendor'          ✓
```

### ✅ Tabel: menu
```
Database Schema:
  idmenu          SERIAL NOT NULL (PRIMARY KEY)
  nama_menu       VARCHAR(255) NOT NULL
  harga           INT NOT NULL
  path_gambar     VARCHAR(255) NULLABLE
  idvendor        INT NOT NULL (FOREIGN KEY)

Blade View Usage:
  $menu->idmenu             ✓
  $menu->nama_menu          ✓
  $menu->harga              ✓
  $menu->path_gambar        ✓ (opsional)
  
JavaScript Usage:
  item['idmenu']            ✓
  item['nama']              ✓ (dari tambahItem() function)
  item['harga']             ✓

Model (Menu.php):
  protected $primaryKey = 'idmenu'     ✓
  protected $table = 'menu'            ✓
  protected $fillable = ['nama_menu', 'harga', 'path_gambar', 'idvendor']  ✓
```

### ✅ Tabel: pesanan
```
Database Schema:
  idpesanan       SERIAL NOT NULL (PRIMARY KEY)
  nama            VARCHAR(255) NOT NULL
  timestamp       TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
  total           INT NOT NULL
  metode_bayar    INT NULLABLE
  status_bayar    SMALLINT DEFAULT 0 (0=Pending, 1=Lunas, 2=Gagal)
  snap_token      VARCHAR(255) NULLABLE
  order_id_pg     VARCHAR(100) UNIQUE NULLABLE

Controller Usage:
  $pesanan = Pesanan::create([
    'nama'        => $guestName              ✓
    'timestamp'   => now()                   ✓
    'total'       => $request->total_bayar   ✓
    'status_bayar' => 0                      ✓
    'order_id_pg' => $orderId                ✓
  ])
  
  $pesanan->update(['snap_token' => $snapToken]);              ✓
  $pesanan->update(['status_bayar' => 1, 'metode_bayar' => 1]); ✓

Model (Pesanan.php):
  protected $primaryKey = 'idpesanan'       ✓
  protected $table = 'pesanan'              ✓
  public $timestamps = false                ✓ (manual timestamp)
  protected $fillable = [
    'nama', 'timestamp', 'total', 'metode_bayar', 
    'status_bayar', 'snap_token', 'order_id_pg'
  ]  ✓
```

### ✅ Tabel: detail_pesanan
```
Database Schema:
  iddetail_pesanan SERIAL NOT NULL (PRIMARY KEY)
  idmenu           INT NOT NULL (FOREIGN KEY → menu)
  idpesanan        INT NOT NULL (FOREIGN KEY → pesanan)
  jumlah           INT NOT NULL
  harga            INT NOT NULL (harga satuan saat dipesan)
  subtotal         INT NOT NULL
  timestamp        TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
  catatan          VARCHAR(255) NULLABLE

Controller Usage:
  DetailPesanan::create([
    'idpesanan'   => $pesanan->idpesanan    ✓
    'idmenu'      => $item['idmenu']         ✓
    'jumlah'      => $item['jumlah']         ✓
    'harga'       => $item['harga']          ✓
    'subtotal'    => $item['subtotal']       ✓
    'catatan'     => $item['catatan'] ?? null ✓
    'timestamp'   => now()                   ✓
  ])

JavaScript Usage (from cart):
  item = {
    idmenu: id          ✓
    nama: nama          ✓
    harga: harga        ✓
    jumlah: 1           ✓
    subtotal: harga     ✓
    catatan: catatan    ✓
  }

Model (DetailPesanan.php):
  protected $primaryKey = 'iddetail_pesanan'  ✓
  protected $table = 'detail_pesanan'         ✓
  public $timestamps = false                  ✓ (manual timestamp)
  protected $fillable = [
    'idmenu', 'idpesanan', 'jumlah', 'harga', 
    'subtotal', 'catatan', 'timestamp'
  ]  ✓
```

---

## 🔗 Relasi Mapping

### Vendor → Menu (One-to-Many)
```
Database:
  menu.idvendor FK → vendor.idvendor

Model (Vendor.php):
  public function menus() {
    return $this->hasMany(Menu::class, 'idvendor', 'idvendor');
  }  ✓

Model (Menu.php):
  public function vendor() {
    return $this->belongsTo(Vendor::class, 'idvendor', 'idvendor');
  }  ✓

Blade View Usage:
  @foreach($vendors as $vendor)
    @foreach($vendor->menus as $menu)
      // Display menu
    @endforeach
  @endforeach  ✓
```

### Pesanan → DetailPesanan (One-to-Many)
```
Database:
  detail_pesanan.idpesanan FK → pesanan.idpesanan

Model (Pesanan.php):
  public function details() {
    return $this->hasMany(DetailPesanan::class, 'idpesanan', 'idpesanan');
  }  ✓

Model (DetailPesanan.php):
  public function pesanan() {
    return $this->belongsTo(Pesanan::class, 'idpesanan', 'idpesanan');
  }  ✓
```

### DetailPesanan → Menu (Many-to-One)
```
Database:
  detail_pesanan.idmenu FK → menu.idmenu

Model (DetailPesanan.php):
  public function menu() {
    return $this->belongsTo(Menu::class, 'idmenu', 'idmenu');
  }  ✓
```

---

## 📊 Data Flow Verification

### 1. Display Menu
```
Flow:
  1. GET /kantin
  2. KantinController@index
  3. $vendors = Vendor::with('menus')->get()
     ✓ Query akan eager-load menus dari database
  4. Pass ke view('guest.order', compact('vendors'))
  5. View loop: @foreach($vendors as $vendor) @foreach($vendor->menus as $menu)
     ✓ Semua column yang digunakan ada di database
  
Database Tables Used:
  ✓ vendor
  ✓ menu
```

### 2. Add to Cart (JavaScript Only)
```
Data Structure:
  keranjang = [
    {
      idmenu: 1 (dari $menu->idmenu) ✓
      nama: 'Nasi Bakar' (dari $menu->nama_menu) ✓
      harga: 15000 (dari $menu->harga) ✓
      jumlah: 1 ✓
      subtotal: 15000 ✓
      catatan: 'Pedas' (dari input) ✓
    }
  ]
```

### 3. Checkout (Insert to Database)
```
1. POST /kantin/checkout
2. Validate cart data ✓
3. Generate nama: 'Guest_XXXXXXX' ✓
4. Generate orderId: 'KANTIN-xxxxxxxx' ✓
5. Create pesanan row:
   ✓ idpesanan (auto-increment)
   ✓ nama (Guest_XXXXXXX)
   ✓ timestamp (now())
   ✓ total (dari request)
   ✓ status_bayar (0)
   ✓ order_id_pg (unique)
6. For each item in cart:
   ✓ Create detail_pesanan row
   ✓ All fields populated
7. Get Midtrans Snap Token ✓
8. Update pesanan.snap_token ✓
9. Return snap_token to frontend ✓
```

### 4. Payment Callback (Webhook)
```
1. POST /midtrans/callback
2. Verify Signature ✓
3. Find pesanan by order_id_pg ✓
4. Update pesanan:
   - status_bayar (0→1 if success, 0→2 if failed)
   - metode_bayar (mapped value)
5. JavaScript redirect to status page ✓
```

---

## ✅ Column Type Compatibility

| Table | Column | Expected | Actual | Status |
|-------|--------|----------|--------|--------|
| vendor | idvendor | SERIAL (auto-inc) | ✓ | ✓ |
| vendor | nama_vendor | VARCHAR(255) | ✓ | ✓ |
| menu | idmenu | SERIAL (auto-inc) | ✓ | ✓ |
| menu | nama_menu | VARCHAR(255) | ✓ | ✓ |
| menu | harga | INT | ✓ | ✓ |
| menu | path_gambar | VARCHAR(255) NULL | ✓ | ✓ |
| menu | idvendor | INT FK | ✓ | ✓ |
| pesanan | idpesanan | SERIAL (auto-inc) | ✓ | ✓ |
| pesanan | nama | VARCHAR(255) | ✓ | ✓ |
| pesanan | timestamp | TIMESTAMP | ✓ | ✓ |
| pesanan | total | INT | ✓ | ✓ |
| pesanan | metode_bayar | INT NULL | ✓ | ✓ |
| pesanan | status_bayar | SMALLINT | ✓ | ✓ |
| pesanan | snap_token | VARCHAR(255) NULL | ✓ | ✓ |
| pesanan | order_id_pg | VARCHAR(100) UNIQUE NULL | ✓ | ✓ |
| detail_pesanan | iddetail_pesanan | SERIAL (auto-inc) | ✓ | ✓ |
| detail_pesanan | idmenu | INT FK | ✓ | ✓ |
| detail_pesanan | idpesanan | INT FK | ✓ | ✓ |
| detail_pesanan | jumlah | INT | ✓ | ✓ |
| detail_pesanan | harga | INT | ✓ | ✓ |
| detail_pesanan | subtotal | INT | ✓ | ✓ |
| detail_pesanan | timestamp | TIMESTAMP | ✓ | ✓ |
| detail_pesanan | catatan | VARCHAR(255) NULL | ✓ | ✓ |

---

## 🔐 Foreign Key Constraints

✓ vendor.idvendor
  ↓ menu.idvendor (ON DELETE CASCADE)
  
✓ menu.idmenu
  ↓ detail_pesanan.idmenu
  
✓ pesanan.idpesanan
  ↓ detail_pesanan.idpesanan (ON DELETE CASCADE)

All constraints implemented correctly in Migration & Database

---

## 📌 Summary

- **Vendors:** 2 (Kantin Sehat, Warung Berkah)
- **Menus:** 5 total (3 dari Kantin Sehat, 2 dari Warung Berkah)
- **Database Schema:** ✅ Fully aligned
- **Models:** ✅ Correctly configured
- **Controllers:** ✅ Using correct column names
- **Views:** ✅ Displaying correct attributes
- **Foreign Keys:** ✅ All constraints in place
- **Migrations:** ✅ Can be run to setup from scratch
- **Seeders:** ✅ Can populate sample data

**Status:** 🟢 READY FOR DEPLOYMENT
