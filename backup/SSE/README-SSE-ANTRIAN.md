# 🚦 Sistem Antrian Real-Time dengan SSE

> **Dokumentasi teknis implementasi Server-Sent Events (SSE) untuk sistem antrian poli.**

---

## 📋 Daftar Isi

1. [Arsitektur Sistem](#1-arsitektur-sistem)
2. [Komponen & File](#2-komponen--file)
3. [Cara Kerja SSE](#3-cara-kerja-sse)
4. [Endpoint API](#4-endpoint-api)
5. [Halaman & Role Pengguna](#5-halaman--role-pengguna)
6. [Mekanisme Cache](#6-mekanisme-cache)
7. [Alur Data Lengkap](#7-alur-data-lengkap)
8. [Panduan Menjalankan](#8-panduan-menjalankan)
9. [Troubleshooting](#9-troubleshooting)

---

## 1. Arsitektur Sistem

```
                           ┌─────────────────────┐
                           │   Database (SQL)     │
                           │   tabel: antrian     │
                           └──────────┬──────────┘
                                      │
                           ┌──────────▼──────────┐
                           │  AntrianController   │
                           │  (Laravel Backend)   │
                           └──┬───────┬──────────┘
                              │       │
                ┌─────────────┤       ├──────────────────┐
                │             │       │                  │
       ┌────────▼────┐  ┌────▼───┐   │        ┌─────────▼──────────┐
       │ Guest Page  │  │ Admin  │   │        │ Papan Display      │
       │ (daftar     │  │ Panel  │   │        │ (SSE EventSource)  │
       │  antrian)   │  │ (SSE   │   │        │ real-time + suara  │
       │              │  │  Cli- │   │        └────────────────────┘
       └──────┬───────┘  │ ent)  │   │
              │          └───┬────┘   │
       ┌──────▼───────┐      │        │
       │ Tiket Pribadi│      │        │
       │ (tab baru)   │      │        │
       └──────────────┘      │        │
                    ┌────────▼────────▼──────┐
                    │   Cache (Laravel)      │
                    │   key: 'antrian_state' │
                    │   TTL: 12 jam          │
                    └────────────────────────┘
```

### Alur Singkat

| Langkah | Aksi | Keterangan |
|---------|------|------------|
| 1 | Guest daftar → POST `/antrian/daftar` | Simpan ke DB |
| 2 | Server panggil `updateAntrianCache()` | DB → Cache |
| 3 | Cache diperbarui | SSE deteksi perubahan (via MD5 hash) |
| 4 | SSE kirim `queue-update` ke Admin & Papan | Real-time |
| 5 | Guest → tab baru `/antrian/tiket/{id}` | Tiket pribadi |
| 6 | Admin POST `/admin/antrian/panggil` | Aksi operator |
| 7 | Ulang step 2-4 | Sinkron real-time |

---

## 2. Komponen & File

### 2.1 Backend

| File | Fungsi |
|------|--------|
| `app/Http/Controllers/AntrianController.php` | Controller utama — semua logika antrian |
| `app/Models/Antrian.php` | Model Eloquent dengan SoftDeletes |

### 2.2 Views (Blade)

| File | Fungsi | Teknologi Data |
|------|--------|---------------|
| `resources/views/antrian/guest.blade.php` | Form daftar antrian | Axios POST |
| `resources/views/antrian/tiket.blade.php` | Tiket pribadi (tab baru) | Direct render |
| `resources/views/antrian/admin.blade.php` | Panel operator | **SSE** + fallback polling |
| `resources/views/antrian/papan.blade.php` | Display publik | **SSE** + audio + speech |

### 2.3 Asset & Routes

| File | Fungsi |
|------|--------|
| `public/audio/dingdong.mp3` | Suara notifikasi panggilan (ting-tong) |
| `routes/web.php` | Registrasi semua route antrian |

---

## 3. Cara Kerja SSE

### 3.1 Apa Itu SSE?

**Server-Sent Events (SSE)** adalah teknologi di mana server mengirim data ke client melalui koneksi HTTP persisten **satu arah** (server → client).

### 3.2 Implementasi Server (PHP/Laravel)

**Endpoint:** `GET /sse/antrian` → method `stream()` di `AntrianController`

```php
public function streamAntrian()
{
    set_time_limit(0);                          // No timeout
    @apache_setenv('no-gzip', 1);               // Matikan kompresi
    @ini_set('zlib.output_compression', 0);     // Matikan buffering

    return response()->stream(function () {
        $lastHash = '';

        // Bersihkan buffer & lepas session lock
        while (ob_get_level() > 0) @ob_end_clean();
        if (session_id()) session_write_close();

        // Isi cache awal
        $this->updateAntrianCache();

        while (true) {
            // Baca dari CACHE (bukan DB langsung)
            $state = Cache::get('antrian_state');

            // Deteksi perubahan via hash
            $currentHash = md5(json_encode($state));

            if ($currentHash !== $lastHash) {
                echo "event: queue-update\n";
                echo "data: " . json_encode($state) . "\n\n";
                $lastHash = $currentHash;
            }

            // Keep-alive
            echo ": keep-alive\n\n";

            if (connection_aborted()) break;

            @ob_flush();
            @flush();
            sleep(1);
        }
    }, 200, [
        'Content-Type'      => 'text/event-stream',
        'Cache-Control'     => 'no-cache',
        'X-Accel-Buffering' => 'no',      // Krusial untuk Nginx!
    ]);
}
```

### 3.3 Format Event SSE

```
event: queue-update
data: {
  "daftar_tunggu": [
    { "idantrian": 1, "nomor": "UMUM-01", "nama": "Budi", "nama_poli": "Umum", "kode_poli": "UMUM" },
    { "idantrian": 2, "nomor": "GIGI-01", "nama": "Siti", "nama_poli": "Gigi", "kode_poli": "GIGI" }
  ],
  "sedang_dipanggil": {
    "idantrian": 3, "nomor": "UMUM-02", "nama": "Ahmad", "nama_poli": "Umum", "kode_poli": "UMUM"
  },
  "terlewat": [
    { "idantrian": 4, "nomor": "GIGI-02", "nama": "Rini", "nama_poli": "Gigi", "kode_poli": "GIGI" }
  ],
  "hari_lain": [
    { "idantrian": 10, "nomor": "UMUM-01", "nama": "Dewi", "nama_poli": "Umum", "status": "selesai", "tanggal_antrian": "30-05-2026" }
  ]
}

: keep-alive
```

### 3.4 Implementasi Client (JavaScript)

#### Admin & Papan menggunakan EventSource API:

```javascript
// Membuka koneksi SSE
const source = new EventSource('/sse/antrian');

// Event listener untuk queue-update
source.addEventListener('queue-update', function(e) {
    const data = JSON.parse(e.data);
    renderData(data);
});

// Error handling (browser auto-reconnect)
source.onerror = function(err) {
    console.error('Koneksi SSE terputus', err);
    // Browser akan reconnect otomatis setelah beberapa detik
};

// Menutup koneksi
// source.close();
```

### 3.5 Perbedaan SSE vs Polling

| Aspek | SSE (EventSource) | Polling (setInterval + fetch) |
|-------|-------------------|-------------------------------|
| Koneksi | Persisten | Putus-nyambung tiap request |
| Latency | Real-time (<1 detik) | Tergantung interval (2 detik) |
| Beban Server | 1 koneksi per client (berat) | Request tiap 2 detik (ringan) |
| Arah | Server → Client (push) | Client → Server (pull) |
| Kompatibilitas | Butuh Apache/Nginx multi-thread | Bisa di server single-thread |
| Auto-reconnect | ✅ Bawaan browser | ❌ Harus manual (sudah otomatis) |
| Digunakan di | **Papan Display** (real-time + suara) | **Admin Panel** (stabil & ringan) |

### 3.6 Kenapa Admin Pakai Polling, Bukan SSE?

Di lingkungan development dengan `php artisan serve` (single-threaded di Windows):

1. **SSE blokir server** — Satu koneksi SSE akan memblokir semua request lain (CSS, JS, data, dsb)
2. **Polling lebih stabil** — Request pendek (2 detik) tidak memblokir server
3. **Cache sebagai sumber data** — Admin tetap membaca dari cache, bukan DB langsung
4. **Delay minimal** — 2 detik sudah cukup responsif untuk operator

> **Catatan:** Papan display tetap menggunakan SSE karena membutuhkan:
> - Update real-time seketika (nomor panggilan)
> - Suara dingdong + speech synthesis
> - Tidak ada interaksi (read-only), jadi tidak masalah jika request lain lambat

---

## 4. Endpoint API

### 4.1 Publik (Tanpa Auth)

| Method | Route | Nama Route | Fungsi |
|--------|-------|-----------|--------|
| GET | `/antrian` | `antrian.guest` | Halaman pendaftaran |
| POST | `/antrian/daftar` | `antrian.guest.daftar` | Daftar antrian baru |
| GET | `/antrian/tiket/{id}` | `antrian.tiket` | Tiket pribadi (tab baru) |
| GET | `/antrian/papan` | `antrian.papan` | Display publik |
| GET | `/antrian/sse/stream` | `antrian.stream` | SSE stream (alias 1) |
| GET | `/sse/antrian` | `sse.antrian` | SSE stream (alias 2) |

### 4.2 Admin (Auth + Role Admin)

| Method | Route | Nama Route | Fungsi |
|--------|-------|-----------|--------|
| GET | `/admin/antrian` | `admin.antrian.index` | Panel admin |
| GET | `/admin/antrian/data` | `admin.antrian.data` | Data dari cache (fallback polling) |
| POST | `/admin/antrian/panggil` | `admin.antrian.panggil` | Panggil antrian |
| POST | `/admin/antrian/lewatkan` | `admin.antrian.lewatkan` | Lewatkan antrian |
| POST | `/admin/antrian/panggil_terlewat` | `admin.antrian.panggil_terlewat` | Panggil ulang terlewat |

---

## 5. Halaman & Role Pengguna

### 5.1 Guest — Pendaftaran & Tiket

**Halaman:** `/antrian`
**Role:** Tidak perlu login

**Fitur:**
- Form input nama + pilih poli
- Submit → simpan ke DB
- Response: JSON berisi `tiket_url`
- **Tab baru** terbuka ke `/antrian/tiket/{id}`

**Halaman Tiket (`/antrian/tiket/{id}`):**
- Badge nama poli
- Nomor antrian (besar)
- Nama pasien
- Waktu daftar
- Tombol **Cetak Tiket** (print)
- Tombol **Tutup** (close tab)

### 5.2 Admin Operator — Panel Manajemen

**Halaman:** `/admin/antrian`
**Role:** Login + Role Admin (idrole = 1)
**Metode data:** **Polling cache** tiap 2 detik (bukan SSE)

**Fitur:**

| Area | Fungsi |
|------|--------|
| **Sedang Dipanggil** | Menampilkan nomor, nama, poli yang aktif |
| **Panggil Berikutnya** | Tombol untuk memanggil antrian berikutnya (sesuai filter poli) |
| **Lewatkan** | Melewatkan antrian yang sedang dipanggil |
| **Daftar Tunggu** | List pasien menunggu, klik untuk panggil spesifik |
| **Terlewat** | List pasien terlewat, double-klik untuk panggil ulang |
| **Riwayat Hari Lain** | Tabel log antrian dari hari sebelumnya |
| **Filter Poli** | Dropdown filter berdasarkan poli |

**Polling data dari cache:**
```javascript
function ambilDataAntrian() {
    axios.get('/admin/antrian/data')  // ← dari Cache::get('antrian_state')
        .then(res => renderData(res.data));
}

// Panggil langsung + ulang tiap 2 detik
ambilDataAntrian();
setInterval(ambilDataAntrian, 2000);
```

**Aksi via HTTP POST (axios):**
```javascript
// Panggil berikutnya (dengan filter poli)
axios.post('/admin/antrian/panggil', { kode_poli: 'UMUM' })
    .then(() => ambilDataAntrian());  // Refresh langsung

// Panggil spesifik
axios.post('/admin/antrian/panggil', { idantrian: 5 })
    .then(() => ambilDataAntrian());

// Lewatkan
axios.post('/admin/antrian/lewatkan', { idantrian: 3 })
    .then(() => ambilDataAntrian());

// Panggil ulang terlewat
axios.post('/admin/antrian/panggil_terlewat', { idantrian: 7 })
    .then(() => ambilDataAntrian());
```

> 💡 Setiap aksi langsung memanggil `ambilDataAntrian()` untuk memperbarui UI tanpa menunggu interval 2 detik.

### 5.3 Papan Display — Tampilan Publik

**Halaman:** `/antrian/papan`
**Role:** Tidak perlu login (publik)

**Fitur:**

| Area | Tampilan |
|------|----------|
| **Sedang Dipanggil** | Nomor jumbo, nama, poli — latar gradasi biru |
| **4 Antrian Berikutnya** | Grid 2×2 card |
| **Jam Digital** | Tanggal + waktu real-time |
| **Suara Panggilan** | Dingdong → Speech Synthesis |

**Suara Panggilan Otomatis:**
```javascript
function bunyiSuaraPanggilan(nomor, nama, poli) {
    // 1. Hentikan speech sebelumnya
    window.speechSynthesis.cancel();

    // 2. Mainkan dingdong
    audioDingdong.currentTime = 0;
    audioDingdong.play();

    // 3. Setelah dingdong, ucapkan teks
    audioDingdong.onended = function() {
        const ucapan = new SpeechSynthesisUtterance(
            `Nomor antrian ${nomor}. Atas nama ${nama}. Silakan menuju ke ${poli}.`
        );
        ucapan.lang = 'id-ID';
        ucapan.rate = 0.85;
        window.speechSynthesis.speak(ucapan);
    };
}
```

---

## 6. Mekanisme Cache

### 6.1 Tujuan Cache

1. **Mengurangi query DB** — Data tidak perlu diambil dari database setiap detik
2. **Konsistensi** — Semua client (admin, papan) melihat data yang sama
3. **Performa** — Cache file/redis lebih cepat daripada query DB

### 6.2 Kapan Cache Diperbarui?

| Pemicu | Method | Keterangan |
|--------|--------|-----------|
| Pasien daftar baru | `guestDaftar()` | ✅ Setelah INSERT |
| Admin panggil berikutnya | `adminPanggil()` | ✅ Setelah UPDATE |
| Admin panggil spesifik | `adminPanggil()` | ✅ Setelah UPDATE |
| Admin lewatkan | `adminLewatkan()` | ✅ Setelah UPDATE |
| Admin panggil ulang terlewat | `adminPanggilTerlewat()` | ✅ Setelah UPDATE |
| SSE stream loop | `streamAntrian()` | ✅ Di awal loop |

### 6.3 Struktur Data Cache

```php
$state = [
    'daftar_tunggu'    => Collection of {idantrian, nomor, nama, nama_poli, kode_poli, ...},
    'sedang_dipanggil'  => Object {idantrian, nomor, nama, nama_poli, kode_poli, ...} | null,
    'terlewat'         => Collection of {idantrian, nomor, nama, nama_poli, kode_poli, ...},
    'hari_lain'        => Collection of {idantrian, nomor, nama, nama_poli, status, tanggal_antrian, ...}
];

Cache::put('antrian_state', $state, now()->addHours(12));
```

### 6.4 MD5 Hash untuk Deteksi Perubahan

SSE stream menggunakan MD5 hash untuk mengirim data **hanya jika ada perubahan**:

```php
$currentHash = md5(json_encode($state));

if ($currentHash !== $lastHash) {
    // Kirim event hanya jika hash berbeda
    echo "event: queue-update\n";
    echo "data: " . json_encode($state) . "\n\n";
    $lastHash = $currentHash;
}
```

---

## 7. Alur Data Lengkap

### 7.1 Guest Mendaftar

```
Guest                  Server                    Database          Cache
  │                      │                         │                │
  │── POST /daftar ──────►│                         │                │
  │  (nama + idpoli)     │── INSERT ───────────────►│                │
  │                      │◄── OK ──────────────────│                │
  │                      │── updateAntrianCache() ──│──► SELECT ────►│
  │                      │◄─────────────────────────│◄── result ────│
  │                      │── Cache::put(state) ────────────────────►│
  │◄── JSON ─────────────│                         │                │
  │  {tiket_url, nomor,  │                         │                │
  │   idantrian, ...}    │                         │                │
  │                      │                         │                │
  │── Tab Baru ──────────►│                         │                │
  │   GET /tiket/{id}    │── SELECT ───────────────►│                │
  │◄── HTML Tiket ───────│◄── data ────────────────│                │
```

### 7.2 Admin Memanggil Antrian

```
Admin                  Server                   Database        Cache
  │                      │                        │              │
  │── SSE connected ─────►│                        │              │
  │   /sse/antrian       │                        │              │
  │                      │                        │              │
  │── POST /panggil ─────►│                        │              │
  │  {idantrian: 5}      │── UPDATE status ──────►│              │
  │                      │   (dipanggil sebelumnya → selesai)    │
  │                      │   (idantrian 5 → dipanggil)           │
  │                      │── updateAntrianCache()─►── SELECT ───►│
  │                      │── Cache::put(state) ────────────────►│
  │◄── JSON sukses ──────│                        │              │
  │                      │                        │              │
  │   === SSE mendeteksi perubahan hash ===       │              │
  │◄── SSE: queue-update ─────────────────────────◄── Cache::get │
  │  (data terbaru)      │                        │              │
```

### 7.3 Papan Display Menerima Update

```
Papan                  SSE Server                Cache
  │                      │                        │
  │── EventSource ──────►│                        │
  │   /sse/antrian       │                        │
  │                      │── Cache::get() ───────►│
  │◄── queue-update ─────│◄── state ──────────────│
  │  (data awal)         │                        │
  │                      │                        │
  │   (setiap 1 detik)   │── Cache::get() ───────►│
  │                      │  (hash sama → skip)    │
  │                      │                        │
  │   (Admin panggil)    │── Cache::get() ───────►│
  │◄── queue-update ─────│  (hash beda → kirim!)  │
  │  (nomor baru)        │                        │
  │                      │                        │
  │── Audio dingdong 🔊  │                        │
  │── Speech: "Nomor..." │                        │
```

---

## 8. Panduan Menjalankan

### 8.1 Prasyarat

- PHP 8.1+
- Laravel 10+
- Database (PostgreSQL/MySQL)
- Web server: **Apache** atau **Nginx** (untuk multi-koneksi)
- Cache driver: `file` (default) atau `redis`

### 8.2 Konfigurasi `.env`

```env
APP_URL=http://localhost  # atau http://BUKU.test
CACHE_DRIVER=file          # Untuk development
# CACHE_DRIVER=redis      # Untuk production
```

### 8.3 Menjalankan dengan Apache (Rekomendasi)

> Karena SSE butuh koneksi persisten, **Apache atau Nginx** diperlukan untuk menangani banyak koneksi bersamaan.

**Laragon:**
1. Buka Laragon → **Start All**
2. Akses: `http://BUKU.test` atau `http://localhost/BUKU/public`

**XAMPP/WAMP:**
1. Jalankan Apache + MySQL
2. Letakkan project di `htdocs/`
3. Akses: `http://localhost/BUKU/public`

### 8.4 Menjalankan dengan `php artisan serve` (Terbatas)

⚠️ `php artisan serve` bersifat **single-threaded** di Windows.
Hanya bisa handle **1 koneksi SSE + beberapa request pendek** secara bersamaan.

**Cara:** Buka 2 terminal (jika multi-instance):
```bash
# Terminal 1: Main app
php artisan serve --port=8000

# Terminal 2: SSE stream saja (ubah konfigurasi jika perlu)
```

### 8.5 Buka Halaman

| Halaman | URL |
|---------|-----|
| Guest daftar | `http://BUKU.test/antrian` |
| Admin panel | `http://BUKU.test/admin/antrian` (login dulu) |
| Papan display | `http://BUKU.test/antrian/papan` |
| SSE stream | `http://BUKU.test/sse/antrian` |
| Tiket contoh | `http://BUKU.test/antrian/tiket/1` |

### 8.6 Testing SSE dengan curl

```bash
curl -N http://localhost:8000/sse/antrian

# Output:
# event: queue-update
# data: {"daftar_tunggu":[],"sedang_dipanggil":null,"terlewat":[],"hari_lain":[]}
#
# : keep-alive
#
```

---

## 9. Troubleshooting

### 9.1 SSE Tidak Mengirim Data

| Gejala | Penyebab | Solusi |
|--------|----------|--------|
| Koneksi terbuka tapi kosong | Buffering Nginx | Tambah `X-Accel-Buffering: no` ✅ (sudah) |
| Data terpotong | Kompresi GZip | Matikan dengan `no-gzip` ✅ (sudah) |
| Request lain lambat | Session lock | Panggil `session_write_close()` ✅ (sudah) |
| Koneksi timeout | PHP time limit | `set_time_limit(0)` ✅ (sudah) |

### 9.2 Admin Tidak Melihat Data

| Gejala | Solusi |
|--------|--------|
| Admin buka tapi data kosong | Buka SSE stream di tab baru, cek respons |
| Data tidak berubah setelah aksi | Pastikan `updateAntrianCache()` dipanggil |
| Cache usang | Hapus cache: `php artisan cache:clear` |
| Browser tidak support SSE | Fallback otomatis ke polling 3 detik ✅ |

### 9.3 Suara Tidak Terdengar di Papan

| Penyebab | Solusi |
|----------|--------|
| Autoplay diblokir browser | Klik halaman sekali sebelum interaksi |
| File dingdong.mp3 tidak ada | Cek `public/audio/dingdong.mp3` |
| SpeechSynthesis tidak support | Gunakan Chrome/Edge versi terbaru |
| Suara tumpuk | Sudah ada `cancel()` sebelum play ✅ |

### 9.4 Cache Tidak Konsisten

```bash
# Hapus semua cache
php artisan cache:clear

# Cek isi cache (jika pakai file cache)
cat storage/framework/cache/data/*
```

### 9.5 Debugging SSE Stream

```bash
# Test koneksi SSE
curl -N http://localhost:8000/sse/antrian

# Test endpoint data cache
curl http://localhost:8000/admin/antrian/data

# Cek route
php artisan route:list --path=antrian
php artisan route:list --path=sse

# Bersihkan cache
php artisan cache:clear
php artisan config:clear
```

---

## Lampiran: Cuplikan Kode Penting

### Controller — Method Utama

```php
// Cache updater
private function updateAntrianCache()
{
    $state = [
        'daftar_tunggu'    => DB::table('antrian')->join('poli', ...)->where('status', 'menunggu')->get(),
        'sedang_dipanggil' => DB::table('antrian')->join('poli', ...)->where('status', 'dipanggil')->first(),
        'terlewat'         => DB::table('antrian')->join('poli', ...)->where('status', 'terlewat')->get(),
        'hari_lain'        => DB::table('antrian')->join('poli', ...)->whereDate('created_at', '<', today())->get(),
    ];
    Cache::put('antrian_state', $state, now()->addHours(12));
}

// SSE stream engine
public function streamAntrian() { ... }

// Data cache untuk fallback polling
public function adminGetData() {
    return response()->json(Cache::get('antrian_state'));
}
```

### Admin — Polling Cache + Aksi POST

```javascript
// Polling data dari cache
function ambilDataAntrian() {
    axios.get('/admin/antrian/data')
        .then(res => renderData(res.data))
        .catch(err => console.error(err));
}

// Mulai polling
ambilDataAntrian();
setInterval(ambilDataAntrian, 2000);

// Aksi POST + refresh langsung
function panggilUrutanBerikutnya() {
    axios.post('/admin/antrian/panggil', { kode_poli: filterPoli })
        .then(() => ambilDataAntrian())  // Refresh segera!
        .catch(err => alert(err.response?.data?.message));
}
```

### Papan — SSE + Dingdong + Speech

```javascript
const audioDingdong = new Audio('/audio/dingdong.mp3');

function bunyiSuaraPanggilan(nomor, nama, poli) {
    window.speechSynthesis.cancel();
    audioDingdong.currentTime = 0;
    audioDingdong.play().catch(() => {});

    audioDingdong.onended = () => {
        const u = new SpeechSynthesisUtterance(
            `Nomor antrian ${nomor}. Atas nama ${nama}. Silakan menuju ke ${poli}.`
        );
        u.lang = 'id-ID'; u.rate = 0.85;
        window.speechSynthesis.speak(u);
    };
}
```

### Guest — Daftar + Buka Tab Baru

```javascript
axios.post('/antrian/daftar', { nama, idpoli })
    .then(res => {
        if (res.data.success) {
            window.open(res.data.tiket_url, '_blank');
            alert('✅ Pendaftaran berhasil! Nomor: ' + res.data.nomor);
        }
    });
```

---

> **Dibuat:** 1 Juni 2026  
> **Aplikasi:** BUKU — Sistem Antrian Poli  
> **Teknologi:** Laravel 12 + SSE + Cache + Web Speech API
