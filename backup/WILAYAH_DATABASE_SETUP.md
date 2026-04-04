# 📍 Setup Database Wilayah Administrasi Indonesia

Dokumentasi lengkap untuk setup dan menggunakan database wilayah administrasi Indonesia (Provinsi, Kota, Kecamatan, Kelurahan).

---

## 📋 Struktur Database

### **1. Tabel `reg_provinces` (Level 1: Provinsi)**
```sql
CREATE TABLE "reg_provinces" (
  id CHAR(2) PRIMARY KEY,
  name VARCHAR(255) NOT NULL
);
```
- **Primary Key**: `id` (CHAR(2)) - Kode provinsi (11, 12, 13, dst)
- **Columns**: `name` (nama provinsi)

---

### **2. Tabel `reg_regencies` (Level 2: Kota/Kabupaten)**
```sql
CREATE TABLE "reg_regencies" (
  id CHAR(4) PRIMARY KEY,
  province_id CHAR(2) NOT NULL,
  name VARCHAR(255) NOT NULL,
  CONSTRAINT fk_province FOREIGN KEY (province_id) REFERENCES reg_provinces(id)
);
```
- **Primary Key**: `id` (CHAR(4))
- **Foreign Key**: `province_id` → `reg_provinces.id`
- **Columns**: `name` (nama kota/kabupaten)

---

### **3. Tabel `reg_districts` (Level 3: Kecamatan)**
```sql
CREATE TABLE "reg_districts" (
  id CHAR(6) PRIMARY KEY,
  regency_id CHAR(4) NOT NULL,
  name VARCHAR(255) NOT NULL,
  CONSTRAINT fk_regency FOREIGN KEY (regency_id) REFERENCES reg_regencies(id)
);
```
- **Primary Key**: `id` (CHAR(6))
- **Foreign Key**: `regency_id` → `reg_regencies.id`
- **Columns**: `name` (nama kecamatan)

---

### **4. Tabel `reg_villages` (Level 4: Kelurahan)**
```sql
CREATE TABLE "reg_villages" (
  id CHAR(10) PRIMARY KEY,
  district_id CHAR(6) NOT NULL,
  name VARCHAR(255) NOT NULL,
  CONSTRAINT fk_district FOREIGN KEY (district_id) REFERENCES reg_districts(id)
);
```
- **Primary Key**: `id` (CHAR(10))
- **Foreign Key**: `district_id` → `reg_districts.id`
- **Columns**: `name` (nama kelurahan)

---

## 🚀 Cara Setup Database

### **Step 1: Jalankan Migration**
```bash
php artisan migrate
```

Perintah ini akan membuat semua tabel yang diperlukan sesuai dengan file migration:
- `database/migrations/2024_01_01_000003_create_wilayah_tables.php`

---

### **Step 2: Jalankan Seeder**
```bash
php artisan db:seed
```

Atau jalankan hanya WilayahSeeder:
```bash
php artisan db:seed --class=WilayahSeeder
```

Seeder akan mengisi data contoh:
- **35 Provinsi** di Indonesia
- **5 Kota/Kabupaten** contoh (Jawa Tengah)
- **5 Kecamatan** contoh (Semarang)
- **5 Kelurahan** contoh (Semarang Selatan)

---

## 📦 Models & Relationships

### **Model: Provinsi** (`App\Models\Provinsi`)
```php
protected $table = 'reg_provinces';
protected $keyType = 'string';

// Relasi
public function kota() {
    return $this->hasMany(Kota::class, 'province_id', 'id');
}
```

**Usage:**
```php
$provinsi = Provinsi::find('32'); // Jawa Tengah
$kota = $provinsi->kota; // Semua kota di Jawa Tengah
```

---

### **Model: Kota** (`App\Models\Kota`)
```php
protected $table = 'reg_regencies';
protected $keyType = 'string';

// Relasi
public function provinsi() {
    return $this->belongsTo(Provinsi::class, 'province_id', 'id');
}

public function kecamatan() {
    return $this->hasMany(Kecamatan::class, 'regency_id', 'id');
}
```

**Usage:**
```php
$kota = Kota::find('3201'); // Semarang
$provinsi = $kota->provinsi; // Provinsi Jawa Tengah
$kecamatan = $kota->kecamatan; // Semua kecamatan di Semarang
```

---

### **Model: Kecamatan** (`App\Models\Kecamatan`)
```php
protected $table = 'reg_districts';
protected $keyType = 'string';

// Relasi
public function kota() {
    return $this->belongsTo(Kota::class, 'regency_id', 'id');
}

public function kelurahan() {
    return $this->hasMany(Kelurahan::class, 'district_id', 'id');
}
```

**Usage:**
```php
$kecamatan = Kecamatan::find('320101'); // Semarang Selatan
$kota = $kecamatan->kota; // Kota Semarang
$kelurahan = $kecamatan->kelurahan; // Semua kelurahan di Semarang Selatan
```

---

### **Model: Kelurahan** (`App\Models\Kelurahan`)
```php
protected $table = 'reg_villages';
protected $keyType = 'string';

// Relasi
public function kecamatan() {
    return $this->belongsTo(Kecamatan::class, 'district_id', 'id');
}
```

**Usage:**
```php
$kelurahan = Kelurahan::find('3201011001'); // Bongrejo
$kecamatan = $kelurahan->kecamatan; // Semarang Selatan
```

---

## 🔄 Relasi Antar Model

```
Provinsi (1)
    ↓ hasMany
Kota (Many) — belongsTo → Provinsi
    ↓ hasMany
Kecamatan (Many) — belongsTo → Kota
    ↓ hasMany
Kelurahan (Many) — belongsTo → Kecamatan
```

---

## 💾 Controller Implementation

Di `WilayahController`, methods sudah menggunakan Eloquent Models:

```php
// Fetch Kota berdasarkan Provinsi
public function getKota(Request $request) {
    $data = Kota::where('province_id', $request->id)
                ->orderBy('name', 'asc')
                ->get();
    
    return response()->json([
        'status' => 'success',
        'code'   => 200,
        'data'   => $data
    ]);
}

// Fetch Kecamatan berdasarkan Kota
public function getKecamatan(Request $request) {
    $data = Kecamatan::where('regency_id', $request->id)
                     ->orderBy('name', 'asc')
                     ->get();
    
    return response()->json([
        'status' => 'success',
        'code'   => 200,
        'data'   => $data
    ]);
}

// Fetch Kelurahan berdasarkan Kecamatan
public function getKelurahan(Request $request) {
    $data = Kelurahan::where('district_id', $request->id)
                     ->orderBy('name', 'asc')
                     ->get();
    
    return response()->json([
        'status' => 'success',
        'code'   => 200,
        'data'   => $data
    ]);
}
```

---

## 🧪 Testing Endpoints

### **Axios Version** (Recommended)
```
GET  /wilayah/axios
POST /wilayah/get-kota
POST /wilayah/get-kecamatan
POST /wilayah/get-kelurahan
```

### **AJAX jQuery Version**
```
GET  /wilayah/ajax
POST /wilayah/get-kota
POST /wilayah/get-kecamatan
POST /wilayah/get-kelurahan
```

---

## 📝 Example Requests

### **1. Ambil Kota berdasarkan Provinsi**
```javascript
// Axios
axios.post('/wilayah/get-kota', {
    id: '32',  // Jawa Tengah
    _token: csrf_token
})
.then(res => {
    console.log(res.data.data); // Array of kota
});

// jQuery AJAX
$.ajax({
    url: '/wilayah/get-kota',
    type: 'POST',
    data: {
        id: '32',
        _token: csrf_token
    },
    success: function(res) {
        console.log(res.data); // Array of kota
    }
});
```

### **2. Ambil Kecamatan berdasarkan Kota**
```javascript
axios.post('/wilayah/get-kecamatan', {
    id: '3201',  // Semarang
    _token: csrf_token
})
.then(res => {
    console.log(res.data.data); // Array of kecamatan
});
```

### **3. Ambil Kelurahan berdasarkan Kecamatan**
```javascript
axios.post('/wilayah/get-kelurahan', {
    id: '320101',  // Semarang Selatan
    _token: csrf_token
})
.then(res => {
    console.log(res.data.data); // Array of kelurahan
});
```

---

## ✅ Checklist Implementasi

- ✅ Models dengan relasi Eloquent
- ✅ Migration untuk membuat tabel
- ✅ Seeder untuk data contoh
- ✅ Controller methods dengan Eloquent
- ✅ Routes untuk GET dan POST
- ✅ View dengan Axios version
- ✅ View dengan AJAX jQuery version
- ✅ Dokumentasi lengkap

---

## 📚 File-file Terkait

| File | Deskripsi |
|------|-----------|
| `app/Models/Provinsi.php` | Model Provinsi dengan relasi |
| `app/Models/Kota.php` | Model Kota dengan relasi |
| `app/Models/Kecamatan.php` | Model Kecamatan dengan relasi |
| `app/Models/Kelurahan.php` | Model Kelurahan dengan relasi |
| `database/migrations/2024_01_01_000003_create_wilayah_tables.php` | Migration untuk wilayah |
| `database/seeders/WilayahSeeder.php` | Seeder untuk data wilayah |
| `database/seeders/DatabaseSeeder.php` | Main seeder yang memanggil WilayahSeeder |
| `app/Http/Controllers/WilayahController.php` | Controller untuk wilayah |
| `resources/views/wilayah/index_axios.blade.php` | View Axios version |
| `resources/views/wilayah/index_ajax.blade.php` | View AJAX jQuery version |

---

## 🎯 Notes

1. **Primary Key CHAR**: Semua primary key menggunakan CHAR karena sistem kode wilayah Indonesia
2. **Foreign Keys**: Digunakan cascade untuk menjaga integritas data
3. **Timestamps**: Disabled (`$timestamps = false`) karena tabel wilayah statis
4. **Data Lengkap**: Seeder hanya contoh, untuk data lengkap perlu import dari sumber resmi

---

Generated: 2024
