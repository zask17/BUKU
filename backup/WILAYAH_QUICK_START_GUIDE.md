# 🚀 Wilayah Implementation - Quick Start Guide

Step-by-step guide untuk memulai menggunakan sistem wilayah administrasi Indonesia yang baru.

---

## ⚡ 5 Menit Setup

### **Step 1: Run Migration**
```bash
cd c:\laragon\www\BUKU
php artisan migrate
```

**Output yang diharapkan:**
```
Migrating: 2024_01_01_000003_create_wilayah_tables
Migrated:  2024_01_01_000003_create_wilayah_tables (###ms)
```

---

### **Step 2: Run Seeder**
```bash
php artisan db:seed --class=WilayahSeeder
```

**Output yang diharapkan:**
```
Database seeding completed successfully.
```

---

### **Step 3: Buka Browser dan Test**

#### **Guest Access (No Login Required)**
```
http://localhost/wilayah/axios
http://localhost/wilayah/ajax
```

#### **Admin Access (Login Required)**
1. Login as admin user
2. Visit: `http://localhost/admin/wilayah/axios`

#### **Visitor Access (Login Required)**
1. Login as visitor user
2. Visit: `http://localhost/visitor/wilayah/axios`

---

### **Step 4: Verify Database Population**

Open terminal dan check data:
```bash
# Check provinces
php artisan tinker
>>> DB::table('reg_provinces')->count();
# Should output: 35

# Check regencies (kota)
>>> DB::table('reg_regencies')->count();
# Should output: 5

# Check districts (kecamatan)
>>> DB::table('reg_districts')->count();
# Should output: 5

# Check villages (kelurahan)
>>> DB::table('reg_villages')->count();
# Should output: 5

# Exit tinker
>>> exit
```

---

## 🧪 Testing Workflows

### **Test Case 1: Guest Axios Form**

1. Open: `http://localhost/wilayah/axios`
2. **Expected Results:**
   - ✓ Page loads successfully
   - ✓ Provinsi select shows 35 provinces
   - ✓ Other selects are disabled (grayed out)
   - ✓ Placeholders show "-- Pilih [Provinsi] --" format

3. **Action: Select Provinsi (Jawa Tengah)**
   - Choose "Jawa Tengah (32)" from Provinsi dropdown
   - **Expected Results:**
     - ✓ Kota select becomes enabled
     - ✓ Kota select populates with sample cities
     - ✓ Kecamatan and Kelurahan remain disabled

4. **Action: Select Kota (Semarang)**
   - Choose "Kota Semarang" from Kota dropdown
   - **Expected Results:**
     - ✓ Kecamatan select becomes enabled
     - ✓ Kecamatan select populates
     - ✓ Kelurahan remains disabled

5. **Action: Select Kecamatan (Semarang Selatan)**
   - Choose "Semarang Selatan" from Kecamatan dropdown
   - **Expected Results:**
     - ✓ Kelurahan select becomes enabled
     - ✓ Kelurahan select populates
     - ✓ Result section displays all selections

6. **Action: Select Kelurahan (Bongrejo)**
   - Choose "Bongrejo" from Kelurahan dropdown
   - **Expected Results:**
     - ✓ Result section shows: "Provinsi: ..., Kota: ..., Kecamatan: ..., Kelurahan: ..."

7. **Action: Change Provinsi**
   - Select different province
   - **Expected Results:**
     - ✓ Kota populated with new province's cities
     - ✓ Kecamatan cleared/disabled (smart reset)
     - ✓ Kelurahan cleared/disabled
     - ✓ Result section clears

---

### **Test Case 2: Guest AJAX Form**

1. Open: `http://localhost/wilayah/ajax`
2. Repeat same steps as Test Case 1
3. **Key Difference:**
   - Uses jQuery AJAX instead of Axios for backend calls
   - All user interactions should work identically

---

### **Test Case 3: Admin Access**

**Prerequisites:** Must be logged in as admin (idrole=1)

1. Login to admin panel with admin credentials
2. Navigate to: `http://localhost/admin/wilayah/axios`
3. **Expected Results:**
   - ✓ Page loads with admin layout
   - ✓ All functionality works same as guest version
   - ✓ Admin header/navigation visible

---

### **Test Case 4: Visitor Access**

**Prerequisites:** Must be logged in as visitor (idrole=2)

1. Login to visitor panel with visitor credentials
2. Navigate to: `http://localhost/visitor/wilayah/axios`
3. **Expected Results:**
   - ✓ Page loads with visitor layout
   - ✓ All functionality works same as guest version
   - ✓ Visitor header/navigation visible

---

### **Test Case 5: Dashboard Hover Effects**

#### **Guest Dashboard**
1. Open: `http://localhost/welcome` (or home page)
2. **Hover over stat cards:**
   - ✓ Cards move up (translateY effect)
   - ✓ Cards scale slightly larger (scale effect)
   - ✓ Cards show enhanced shadow
3. **Click on stat card:**
   - Buku card → Should navigate to `/buku`
   - Kategori card → Should navigate to `/kategori`

#### **Admin Dashboard**
1. Login as admin
2. Open: `/admin/dashboard` (or admin home page)
3. **Hover Effects:**
   - ✓ Same animation effects as guest
4. **Navigation:**
   - Total Pengguna → `/admin/pengguna`
   - Total Kategori → `/admin/kategori.index`
   - Total Buku → `/admin/buku.index`

#### **Visitor Dashboard**
1. Login as visitor
2. Open: `/visitor/dashboard` (or visitor home page)
3. Same behavior as admin dashboard

---

## 🔍 API Testing with Postman

### **Setup Postman Collection**

#### **Request 1: Get Provinces**
```
GET http://localhost/wilayah/axios
Headers: (none needed for GET)
Response: HTML page with all 35 provinces in select
```

#### **Request 2: Get Kota**
```
POST http://localhost/wilayah/get-kota

Headers:
  Content-Type: application/json
  X-CSRF-TOKEN: {{csrf_token}}

Body (raw):
{
  "id": "32",
  "_token": "{{csrf_token}}"
}

Expected Response:
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
    },
    ...
  ]
}
```

#### **Request 3: Get Kecamatan**
```
POST http://localhost/wilayah/get-kecamatan

Body (raw):
{
  "id": "3201",
  "_token": "{{csrf_token}}"
}

Expected: Array of 5+ kecamatan objects
```

#### **Request 4: Get Kelurahan**
```
POST http://localhost/wilayah/get-kelurahan

Body (raw):
{
  "id": "320101",
  "_token": "{{csrf_token}}"
}

Expected: Array of 5+ kelurahan objects
```

---

## 🐛 Troubleshooting Common Issues

### **Issue: "Migration already exists"**
```
Error: SQLSTATE[42P07]: Duplicate table
```

**Solution:**
```bash
# Option 1: Run specific migration
php artisan migrate --path=database/migrations/2024_01_01_000003_create_wilayah_tables.php

# Option 2: Refresh all (WARNING: will delete all data)
php artisan migrate:refresh
php artisan db:seed

# Option 3: Rollback and remigrate
php artisan migrate:rollback
php artisan migrate
```

---

### **Issue: "CSRF token mismatch"**
```
Error: SQLSTATE[08S01]: Communication link failure
```

**Solution:**
1. Ensure CSRF token is included in requests:
   ```javascript
   const token = document.querySelector('meta[name="csrf-token"]').content;
   ```

2. Include in POST data:
   ```javascript
   axios.post('/wilayah/get-kota', {
       id: '32',
       _token: token
   })
   ```

3. Clear cache:
   ```bash
   php artisan cache:clear
   php artisan config:clear
   ```

---

### **Issue: "No data showing in selects"**
```
Selects are empty despite page loading
```

**Solution:**
1. Verify seeder ran:
   ```bash
   php artisan db:seed --class=WilayahSeeder
   ```

2. Check database has data:
   ```bash
   php artisan tinker
   >>> DB::table('reg_provinces')->get();
   ```

3. Verify controller methods are returning data:
   - Open browser console (F12)
   - Check Network tab for `/wilayah/get-kota` requests
   - Should show 200 response with JSON data

---

### **Issue: "401 Unauthorized" on admin/visitor routes**
```
Error: User not authenticated or lacks permission
```

**Solution:**
1. Verify you're logged in:
   ```
   /login → provide credentials
   ```

2. Verify user role:
   ```bash
   php artisan tinker
   >>> Auth::user()->idrole;  # Should be 1 for admin, 2 for visitor
   ```

3. Check middleware in `routes/web.php`:
   - Admin routes should be in `admin` middleware group
   - Visitor routes should be in `visitor` middleware group

---

### **Issue: "View [wilayah.index_axios] not found"**
```
Error: View file doesn't exist or wrong namespace
```

**Solution:**
1. Verify view files exist:
   ```
   resources/views/wilayah/index_axios.blade.php
   resources/views/wilayah/index_ajax.blade.php
   resources/views/admin/wilayah/index_axios.blade.php
   resources/views/admin/wilayah/index_ajax.blade.php
   resources/views/visitor/wilayah/index_axios.blade.php
   resources/views/visitor/wilayah/index_ajax.blade.php
   ```

2. Check routes are pointing to correct controllers:
   ```bash
   php artisan route:list | grep wilayah
   ```

3. Verify controller methods exist and return correct view

---

### **Issue: "Call to member on null" error**
```
Error: Trying to access property on null object
```

**Solution:**
1. Usually in `getLayout()` without Auth check
2. Verify controller has:
   ```php
   if (!Auth::check()) {
       return 'guest';
   }
   ```

3. Clear compiled views:
   ```bash
   php artisan view:clear
   ```

---

### **Issue: Status cards not showing hover effects**
```
Buttons don't animate on hover
```

**Solution:**
1. Check CSS is properly included in view
2. Verify `.stat-card-link` class exists:
   ```bash
   grep -n "stat-card-link" resources/views/**/*.blade.php
   ```

3. Check browser console for CSS errors
4. Clear browser cache (Ctrl+Shift+Delete)

---

## 📊 Database Schema Quick Reference

### **reg_provinces**
- Primary Key: `id` (CHAR(2)) - Province code
- Data: 35 Indonesian provinces
- Example: `11=Aceh`, `32=Jawa Tengah`

### **reg_regencies**
- Primary Key: `id` (CHAR(4))
- Foreign Key: `province_id` → `reg_provinces(id)`
- Example: `3201=Kota Semarang` (under province 32)

### **reg_districts**
- Primary Key: `id` (CHAR(6))
- Foreign Key: `regency_id` → `reg_regencies(id)`
- Example: `320101=Semarang Selatan` (under regency 3201)

### **reg_villages**
- Primary Key: `id` (CHAR(10))
- Foreign Key: `district_id` → `reg_districts(id)`
- Example: `3201011001=Bongrejo` (under district 320101)

---

## 💡 Tips & Tricks

### **Dump All Data Structure**
```bash
php artisan tinker

# Get full hierarchy
>>> $prov = Provinsi::with('kota.kecamatan.kelurahan')->find('32');
>>> $prov->kota;
>>> $prov->kota[0]->kecamatan;
>>> $prov->kota[0]->kecamatan[0]->kelurahan;
```

### **Add Custom Data to Seeder**
Edit `database/seeders/WilayahSeeder.php`:
```php
DB::table('reg_provinces')->insert([
    ['id' => '99', 'name' => 'Custom Province']
]);
```

Then run:
```bash
php artisan migrate:refresh --seed
```

### **Create Test User with Specific Role**
```bash
php artisan tinker
>>> User::create(['email' => 'admin@test.com', 'password' => bcrypt('password'), 'idrole' => 1]);
>>> User::create(['email' => 'visitor@test.com', 'password' => bcrypt('password'), 'idrole' => 2]);
```

---

## ✅ Success Indicators

When everything is working correctly, you should see:

### **Response Checklist**
- ✓ `/wilayah/axios` loads with 35 provinces
- ✓ Selecting province populates kota dropdown
- ✓ Selecting kota populates kecamatan dropdown
- ✓ Selecting kecamatan populates kelurahan dropdown
- ✓ Result section shows all 4 selections
- ✓ Changing parent level resets children
- ✓ `/wilayah/ajax` works identically with jQuery
- ✓ Admin and visitor routes work after login
- ✓ Dashboard cards have hover effects
- ✓ Dashboard card links navigate correctly

---

## 📞 Need Help?

### **Check These Files First:**
1. `WILAYAH_DATABASE_SETUP.md` - Database schema explanation
2. `WILAYAH_API_DOCUMENTATION.md` - API endpoint details
3. `WILAYAH_IMPLEMENTATION_CHECKLIST.md` - Verification checklist

### **Common Documentation Locations:**
- Models: `app/Models/{Provinsi,Kota,Kecamatan,Kelurahan}.php`
- Controller: `app/Http/Controllers/WilayahController.php`
- Routes: `routes/web.php` (lines 51-59 for guest, admin/visitor groups)
- Views: `resources/views/{wilayah,admin/wilayah,visitor/wilayah}/`
- Database: `database/migrations/2024_01_01_000003_create_wilayah_tables.php`

---

**Ready to start? Run these 2 commands:**
```bash
php artisan migrate
php artisan db:seed
```

**Then open:** `http://localhost/wilayah/axios`

---

Generated: 2024
