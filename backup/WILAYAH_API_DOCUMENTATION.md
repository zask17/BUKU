# 🔌 Wilayah API Documentation

Complete API documentation untuk wilayah administrasi Indonesia dengan examples dan response types.

---

## 📡 Base URLs

- **Guest Routes**: `/wilayah`
- **Admin Routes**: `/admin/wilayah`
- **Visitor Routes**: `/visitor/wilayah`

---

## 🎯 Endpoints

### **1. Display Hierarki Wilayah - Axios Version**

**Endpoint:**
```
GET /wilayah/axios
GET /admin/wilayah/axios
GET /visitor/wilayah/axios
```

**Description:** Menampilkan form hierarchial dengan implementasi Axios untuk fetch data

**Response Type:** HTML (Blade View)

**View File:** `resources/views/wilayah/index_axios.blade.php`

**Features:**
- 4-level hierarchical selects (Provinsi → Kota → Kecamatan → Kelurahan)
- Real-time cascading selects
- Smart dependency management
- Result display section

**Example Usage:**
```html
<!-- Access from browser -->
http://localhost/wilayah/axios
http://localhost/admin/wilayah/axios
http://localhost/visitor/wilayah/axios
```

---

### **2. Display Hierarki Wilayah - AJAX jQuery Version**

**Endpoint:**
```
GET /wilayah/ajax
GET /admin/wilayah/ajax
GET /visitor/wilayah/ajax
```

**Description:** Menampilkan form hierarchial dengan implementasi jQuery AJAX

**Response Type:** HTML (Blade View)

**View File:** `resources/views/wilayah/index_ajax.blade.php`

**Features:**
- Same as Axios version
- Uses jQuery instead of Axios library
- `$(document).ready()` pattern

**Example Usage:**
```html
<!-- Access from browser -->
http://localhost/wilayah/ajax
http://localhost/admin/wilayah/ajax
http://localhost/visitor/wilayah/ajax
```

---

### **3. Get Kota (Regencies)**

**Endpoint:**
```
POST /wilayah/get-kota
POST /admin/wilayah/get-kota
POST /visitor/wilayah/get-kota
```

**Description:** Fetch data kota/kabupaten berdasarkan province_id

**HTTP Method:** POST

**Request Headers:**
```
Content-Type: application/json
X-CSRF-TOKEN: {csrf_token}
```

**Request Body:**
```json
{
  "id": "32",
  "_token": "csrf_token_value"
}
```

**Request Parameters:**
| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `id` | String | Yes | Kode provinsi (CHAR(2)) |
| `_token` | String | Yes | CSRF token dari Laravel |

**Success Response:**
```json
{
  "status": "success",
  "code": 200,
  "data": [
    {
      "id": "3201",
      "province_id": "32",
      "name": "Kota Semarang"
    },
    {
      "id": "3202",
      "province_id": "32",
      "name": "Kabupaten Semarang"
    }
  ]
}
```

**Response Fields:**
| Field | Type | Description |
|-------|------|-------------|
| `status` | String | Status response (success/error) |
| `code` | Number | HTTP status code |
| `data` | Array | Array of regencies |
| `data[].id` | String | Kode kota (CHAR(4)) |
| `data[].province_id` | String | Kode provinsi (CHAR(2)) |
| `data[].name` | String | Nama kota |

**Example Axios:**
```javascript
axios.post('/wilayah/get-kota', {
    id: '32',
    _token: document.querySelector('meta[name="csrf-token"]').content
})
.then(response => {
    console.log('Regencies:', response.data.data);
    // Populate kota select
    response.data.data.forEach(kota => {
        console.log(`${kota.id}: ${kota.name}`);
    });
})
.catch(error => {
    console.error('Error:', error);
});
```

**Example jQuery AJAX:**
```javascript
$.ajax({
    url: '/wilayah/get-kota',
    type: 'POST',
    data: {
        id: '32',
        _token: $('meta[name="csrf-token"]').attr('content')
    },
    dataType: 'json',
    success: function(response) {
        console.log('Regencies:', response.data);
        response.data.forEach(function(kota) {
            console.log(kota.id + ': ' + kota.name);
        });
    },
    error: function(error) {
        console.error('Error:', error);
    }
});
```

---

### **4. Get Kecamatan (Districts)**

**Endpoint:**
```
POST /wilayah/get-kecamatan
POST /admin/wilayah/get-kecamatan
POST /visitor/wilayah/get-kecamatan
```

**Description:** Fetch data kecamatan berdasarkan regency_id

**HTTP Method:** POST

**Request Body:**
```json
{
  "id": "3201",
  "_token": "csrf_token_value"
}
```

**Request Parameters:**
| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `id` | String | Yes | Kode kota (CHAR(4)) |
| `_token` | String | Yes | CSRF token dari Laravel |

**Success Response:**
```json
{
  "status": "success",
  "code": 200,
  "data": [
    {
      "id": "320101",
      "regency_id": "3201",
      "name": "Semarang Selatan"
    },
    {
      "id": "320102",
      "regency_id": "3201",
      "name": "Semarang Tengah"
    }
  ]
}
```

**Response Fields:**
| Field | Type | Description |
|-------|------|-------------|
| `status` | String | Status response (success/error) |
| `code` | Number | HTTP status code |
| `data` | Array | Array of districts |
| `data[].id` | String | Kode kecamatan (CHAR(6)) |
| `data[].regency_id` | String | Kode kota (CHAR(4)) |
| `data[].name` | String | Nama kecamatan |

**Example Axios:**
```javascript
axios.post('/wilayah/get-kecamatan', {
    id: '3201',
    _token: document.querySelector('meta[name="csrf-token"]').content
})
.then(response => {
    console.log('Districts:', response.data.data);
})
.catch(error => console.error('Error:', error));
```

---

### **5. Get Kelurahan (Villages)**

**Endpoint:**
```
POST /wilayah/get-kelurahan
POST /admin/wilayah/get-kelurahan
POST /visitor/wilayah/get-kelurahan
```

**Description:** Fetch data kelurahan berdasarkan district_id

**HTTP Method:** POST

**Request Body:**
```json
{
  "id": "320101",
  "_token": "csrf_token_value"
}
```

**Request Parameters:**
| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `id` | String | Yes | Kode kecamatan (CHAR(6)) |
| `_token` | String | Yes | CSRF token dari Laravel |

**Success Response:**
```json
{
  "status": "success",
  "code": 200,
  "data": [
    {
      "id": "3201011001",
      "district_id": "320101",
      "name": "Bongrejo"
    },
    {
      "id": "3201011002",
      "district_id": "320101",
      "name": "Rezeki"
    }
  ]
}
```

**Response Fields:**
| Field | Type | Description |
|-------|------|-------------|
| `status` | String | Status response (success/error) |
| `code` | Number | HTTP status code |
| `data` | Array | Array of villages |
| `data[].id` | String | Kode kelurahan (CHAR(10)) |
| `data[].district_id` | String | Kode kecamatan (CHAR(6)) |
| `data[].name` | String | Nama kelurahan |

**Example Axios:**
```javascript
axios.post('/wilayah/get-kelurahan', {
    id: '320101',
    _token: document.querySelector('meta[name="csrf-token"]').content
})
.then(response => {
    console.log('Villages:', response.data.data);
})
.catch(error => console.error('Error:', error));
```

---

## 🔑 CSRF Token

Semua POST request memerlukan CSRF token. Ada beberapa cara untuk mendapatkannya:

### **Method 1: Meta Tag**
```html
<!-- In blade template -->
<meta name="csrf-token" content="{{ csrf_token() }}">

<!-- In JavaScript -->
const token = document.querySelector('meta[name="csrf-token"]').content;
```

### **Method 2: Hidden Input**
```html
<!-- In blade template -->
{{ csrf_field() }}

<!-- In JavaScript -->
const token = document.querySelector('input[name="_token"]').value;
```

### **Method 3: Direct with axios**
```javascript
// axios automatically includes CSRF token from meta tag
import axios from 'axios';

// Axios will use meta[name="csrf-token"] automatically
axios.post('/wilayah/get-kota', { id: '32' });
```

---

## 🧪 Testing dengan Postman

### **1. Get Kota**
```
POST http://localhost/wilayah/get-kota
Headers:
  Content-Type: application/json
  X-CSRF-TOKEN: {{csrf_token}}

Body (raw JSON):
{
  "id": "32",
  "_token": "{{csrf_token}}"
}
```

### **2. Get Kecamatan**
```
POST http://localhost/wilayah/get-kecamatan
Headers:
  Content-Type: application/json
  X-CSRF-TOKEN: {{csrf_token}}

Body (raw JSON):
{
  "id": "3201",
  "_token": "{{csrf_token}}"
}
```

### **3. Get Kelurahan**
```
POST http://localhost/wilayah/get-kelurahan
Headers:
  Content-Type: application/json
  X-CSRF-TOKEN: {{csrf_token}}

Body (raw JSON):
{
  "id": "320101",
  "_token": "{{csrf_token}}"
}
```

---

## 📊 Data Seeding Status

Seeder sudah menyediakan sample data:

### **Provinsi**
- Total: 35 provinsi di Indonesia
- Sample: Jawa Tengah (32), Jakarta (31), Jawa Barat (32)

### **Kota (Regencies)**
- Total dalam seeder: 5 sample
- Sample: Kota Semarang (3201), Kabupaten Semarang (3202), dll

### **Kecamatan (Districts)**
- Total dalam seeder: 5 sample
- Sample: Semarang Selatan (320101), Semarang Tengah (320102), dll

### **Kelurahan (Villages)**
- Total dalam seeder: 5 sample
- Sample: Bongrejo (3201011001), Rezeki (3201011002), dll

---

## 📝 Response Status Codes

| Code | Status | Description |
|------|--------|-------------|
| 200 | Success | Request berhasil |
| 400 | Bad Request | Parameter tidak valid |
| 401 | Unauthorized | User tidak terautentikasi |
| 403 | Forbidden | User tidak memiliki akses |
| 404 | Not Found | Resource tidak ditemukan |
| 500 | Server Error | Error di server |

---

## 🛡️ Security Considerations

1. **CSRF Protection**: Semua POST request dilindungi dengan CSRF token
2. **Authentication**: Beberapa routes memerlukan authentication (admin, visitor)
3. **Authorization**: Middleware menentukan role yang bisa akses route
4. **Input Validation**: ID parameter harus sesuai format CHAR

---

## 💡 Common Use Cases

### **1. Dynamic Select Form**
```javascript
// Ketika user memilih provinsi
document.getElementById('provinsi').addEventListener('change', async function(e) {
    const provinsiId = e.target.value;
    
    if (!provinsiId) {
        document.getElementById('kota').innerHTML = '<option>-- Pilih Kota --</option>';
        return;
    }
    
    try {
        const response = await axios.post('/wilayah/get-kota', {
            id: provinsiId,
            _token: document.querySelector('meta[name="csrf-token"]').content
        });
        
        const kotaSelect = document.getElementById('kota');
        kotaSelect.innerHTML = '<option value="0">-- Pilih Kota --</option>';
        
        response.data.data.forEach(kota => {
            const option = document.createElement('option');
            option.value = kota.id;
            option.textContent = kota.name;
            kotaSelect.appendChild(option);
        });
        
        kotaSelect.disabled = false;
    } catch (error) {
        console.error('Error fetching kota:', error);
    }
});
```

### **2. Populate All Levels**
```javascript
async function populateAllLevels(provinsiId) {
    try {
        // Get Kota
        const kotaRes = await axios.post('/wilayah/get-kota', { 
            id: provinsiId,
            _token: csrf_token 
        });
        
        // Get Kecamatan
        const kecamatanRes = await axios.post('/wilayah/get-kecamatan', { 
            id: kotaRes.data.data[0].id,
            _token: csrf_token 
        });
        
        // Get Kelurahan
        const kelurahanRes = await axios.post('/wilayah/get-kelurahan', { 
            id: kecamatanRes.data.data[0].id,
            _token: csrf_token 
        });
        
        console.log({
            kota: kotaRes.data.data,
            kecamatan: kecamatanRes.data.data,
            kelurahan: kelurahanRes.data.data
        });
    } catch (error) {
        console.error('Error:', error);
    }
}
```

---

## 🚀 Deployment Notes

1. **Database Migration**: Run `php artisan migrate` sebelum deploy
2. **Database Seeding**: Run `php artisan db:seed` untuk populate data
3. **Cache Clear**: Run `php artisan cache:clear` setelah deploy
4. **CSRF Token**: Pastikan CSRF middleware aktif di `app/Http/Middleware/VerifyCsrfToken.php`

---

Generated: 2024
