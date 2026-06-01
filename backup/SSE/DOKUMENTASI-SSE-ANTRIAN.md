# Dokumentasi Sistem Antrian SSE (Server-Sent Events)

## 📋 Daftar Isi

1. [Arsitektur Sistem](#1-arsitektur-sistem)
2. [Komponen Sistem](#2-komponen-sistem)
3. [Cara Kerja SSE Stream](#3-cara-kerja-sse-stream)
4. [Mekanisme Cache](#4-mekanisme-cache)
5. [Admin Panel — SSE Real-time dari Cache](#5-admin-panel--sse-real-time-dari-cache)
6. [Papan Display — Real-time SSE + Suara](#6-papan-display--real-time-sse--suara)
7. [Guest — Registrasi + Tiket Tab Baru](#7-guest--registrasi--tiket-tab-baru)
8. [Endpoint API](#8-endpoint-api)
9. [Alur Data Lengkap](#9-alur-data-lengkap)
10. [Cara Menjalankan](#10-cara-menjalankan)
11. [Pemecahan Masalah](#11-pemecahan-masalah)

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
              ┌─────────────┤       ├──────────────┐
              │             │       │              │
     ┌────────▼────┐  ┌────▼───┐   │    ┌─────────▼──────┐
     │ Guest Page  │  │ Admin  │   │    │ Papan Display  │
     │ (daftar     │  │ Panel  │   │    │ (SSE Client)   │
     │  antrian)   │  │ (SSE   │   │    │ real-time       │
     │              │  │  Cli- │   │    │ + suara         │
     └──────┬───────┘  │ ent)  │   │    └────────────────┘
            │          └───┬────┘   │
     ┌──────▼───────┐      │        │
     │ Tiket Pribadi │      │        │
     │ (tab baru)    │      │        │
     └──────────────┘      │        │
                  ┌────────▼────────▼──────┐
                  │   Cache (Laravel)      │
                  │   key: antrian_state   │
                  └────────────────────────┘
```

### Penjelasan Alur:

1. **Guest** mendaftar antrian → POST `/antrian/daftar`
2. **Controller** menyimpan ke database, lalu **memperbarui cache** (`updateAntrianCache()`)
3. **Guest** mendapat `tiket_url` → **tab baru** terbuka ke `/antrian/tiket/{id}`
4. **Admin panel** & **Papan display** menggunakan **SSE** (EventSource) → GET `/sse/antrian`
5. SSE stream membaca dari **Cache** (bukan query DB langsung)
6. Setiap aksi admin (panggil/lewatkan) → POST → simpan DB → perbarui cache → SSE otomatis kirim data baru ke semua client

---

## 2. Komponen Sistem

### 2.1 Backend — AntrianController

**File:** `app/Http/Controllers/AntrianController.php`

| Method | Fungsi |
|--------|--------|
| `guestIndex()` | Menampilkan halaman pendaftaran antrian |
| `guestDaftar()` | Proses pendaftaran pasien baru |
| `adminIndex()` | Menampilkan panel admin operator |
| `adminGetData()` | **Mengembalikan data antrian dari cache** (untuk polling admin) |
| `adminPanggil()` | Memanggil antrian berikutnya atau spesifik |
| `adminLewatkan()` | Melewatkan antrian aktif |
| `adminPanggilTerlewat()` | Memanggil ulang antrian yang terlewat |
| `papanIndex()` | Menampilkan halaman papan display |
| `streamAntrian()` | **SSE Stream Engine** — mengirim data real-time ke papan |
| `updateAntrianCache()` | **Private** — memperbarui data cache setelah setiap perubahan |

### 2.2 Model — Antrian

**File:** `app/Models/Antrian.php`

- Table: `antrian`
- Primary Key: `idantrian` (auto-increment)
- Soft Deletes: ya (`deleted_at`)
- Relasi: belongsTo `Poli`

### 2.3 Views

| View | File | Fungsi |
|------|------|--------|
| Guest | `resources/views/antrian/guest.blade.php` | Form pendaftaran pasien |
| Admin | `resources/views/antrian/admin.blade.php` | Panel operator dengan **cache polling** |
| Papan | `resources/views/antrian/papan.blade.php` | Display publik dengan **SSE real-time** |

---

## 3. Cara Kerja SSE Stream

### 3.1 Apa itu SSE?

Server-Sent Events (SSE) adalah teknologi web di mana server dapat **mengirim data ke client secara push** melalui koneksi HTTP persisten. Berbeda dengan WebSocket, SSE hanya **satu arah** (server → client) dan menggunakan protokol HTTP biasa.

### 3.2 Implementasi SSE di AntrianController

```php
public function streamAntrian()
{
    set_time_limit(0); // Tidak ada batas waktu eksekusi
    
    // Matikan kompresi dan buffering agar data langsung terkirim
    @apache_setenv('no-gzip', 1);
    @ini_set('zlib.output_compression', 0);
    @ini_set('implicit_flush', 1);

    return response()->stream(function () {
        $lastHash = '';
        
        // Bersihkan buffer output
        while (ob_get_level() > 0) { @ob_end_clean(); }
        
        // Lepas session lock agar request lain tidak terblokir
        if (session_id()) { session_write_close(); }

        // Update cache sebelum loop dimulai
        $this->updateAntrianCache();

        while (true) {
            // Ambil data dari CACHE (bukan query langsung ke DB)
            $state = Cache::get('antrian_state', [...]);
            
            // Hash untuk deteksi perubahan
            $currentHash = md5(json_encode($state));
            
            // Kirim event hanya jika data berubah
            if ($currentHash !== $lastHash) {
                echo "event: queue-update\n";
                echo "data: " . json_encode($state) . "\n\n";
                $lastHash = $currentHash;
            }
            
            // Keep-alive agar koneksi tidak terputus
            echo ": keep-alive\n\n";
            
            if (connection_aborted()) break;
            
            @ob_flush(); @flush();
            sleep(1); // Cek perubahan setiap 1 detik
        }
    }, 200, [
        'Content-Type' => 'text/event-stream',
        'Cache-Control' => 'no-cache',
        'X-Accel-Buffering' => 'no', // Penting untuk Nginx!
    ]);
}
```

### 3.3 Format Event SSE

```
event: queue-update
data: {
  "daftar_tunggu": [...],
  "sedang_dipanggil": {...} | null,
  "terlewat": [...],
  "hari_lain": [...]
}

: keep-alive
```

### 3.4 Client-side (Papan Display)

```javascript
const source = new EventSource("/antrian/sse/stream");

source.addEventListener('queue-update', function(e) {
    const data = JSON.parse(e.data);
    // Render data ke UI...
});

source.onerror = function() {
    // Exponential backoff reconnect
    source.close();
    setTimeout(initSSE, delay);
    delay = Math.min(delay * 2, 30000);
};
```

### 3.5 Exponential Backoff

Papan display memiliki mekanisme reconnect cerdas:
- Mulai dengan delay 1 detik
- Setiap gagal, delay dikalikan 2 (1s → 2s → 4s → 8s → ...)
- Maksimal delay 30 detik
- Reset ke 1 detik setelah berhasil menerima data

---

## 4. Mekanisme Cache

### 4.1 Tujuan Cache

1. **Mengurangi beban database** — Data tidak perlu di-query setiap kali admin polling
2. **Konsistensi data** — Admin dan papan display melihat data yang sama
3. **Administrator tidak terkoneksi terus-menerus** — Admin menggunakan polling sederhana

### 4.2 Data yang Di-cache

```php
$state = [
    'daftar_tunggu'    => $daftar_tunggu,    // Collection
    'sedang_dipanggil' => $sedang_dipanggil,  // Object atau null
    'terlewat'         => $terlewat,          // Collection
    'hari_lain'        => $hari_lain          // Collection
];

Cache::put('antrian_state', $state, now()->addHours(12));
```

### 4.3 Kapan Cache Diperbarui?

Cache diperbarui setiap kali ada perubahan data:

| Aksi | Method | Cache Update |
|------|--------|-------------|
| Pasien daftar baru | `guestDaftar()` | ✅ Ya |
| Admin panggil berikutnya | `adminPanggil()` | ✅ Ya |
| Admin panggil spesifik | `adminPanggil()` | ✅ Ya |
| Admin lewatkan pasien | `adminLewatkan()` | ✅ Ya |
| Admin panggil ulang terlewat | `adminPanggilTerlewat()` | ✅ Ya |
| SSE stream loop | `streamAntrian()` | ✅ Ya (pertama kali) |

### 4.4 Admin Polling vs SSE

| Aspek | Admin (Polling) | Papan (SSE) |
|-------|----------------|-------------|
| Koneksi | Setiap 3 detik (putus-nyambung) | Persisten (selalu terhubung) |
| Sumber data | Cache (via endpoint) | Cache (via stream) |
| Beban server | Rendah | Sedang |
| Real-time | Mendekati real-time (< 3 detik) | Real-time (< 1 detik) |
| Cocok untuk | Panel operator | Display publik |

### 4.5 Diagram Alur Cache

```
                    ┌───────────────────────┐
                    │   Database (antrian)   │
                    └───────────┬───────────┘
                                │
                    ┌───────────▼───────────┐
                    │  updateAntrianCache()  │
                    │  (setelah setiap aksi) │
                    └───────────┬───────────┘
                                │
                    ┌───────────▼───────────┐
                    │  Cache::put()          │
                    │  key: 'antrian_state'  │
                    │  TTL: 12 jam           │
                    └──┬────────────────┬───┘
                       │                │
              ┌────────▼──────┐  ┌──────▼─────────┐
              │ Admin Polling  │  │  SSE Stream     │
              │ GET /admin/    │  │  GET /antrian/  │
              │ antrian/data   │  │  sse/stream     │
              └───────────────┘  └─────────────────┘
```

---

## 5. Admin Panel — SSE Real-time dari Cache

### 5.1 Admin Menggunakan SSE (EventSource)

Admin panel menggunakan **SSE** (koneksi persisten) untuk menerima data real-time, sesuai panduan sistem antrian. Data yang diterima berasal dari **Cache** (bukan query DB langsung), sehingga performa tetap optimal.

### 5.2 Cara Kerja Admin SSE

```javascript
// Membuka koneksi SSE
const source = new EventSource("/sse/antrian");

// Menerima event queue-update
source.addEventListener('queue-update', function(e) {
    const data = JSON.parse(e.data);
    renderData(data);
});

// Fallback: polling jika browser tidak support SSE
if (!window.EventSource) {
    setInterval(() => {
        fetch('/admin/antrian/data').then(res => res.json()).then(renderData);
    }, 3000);
}
```

### 5.3 Aksi Operator via HTTP POST

SSE bersifat **one-way** (server → client). Untuk aksi seperti panggil/lewatkan, admin tetap menggunakan **HTTP POST via axios**:

```javascript
function panggilUrutanBerikutnya() {
    axios.post("/admin/antrian/panggil", { kode_poli: filterPoli })
        .catch(err => alert(err.response?.data?.message));
}
```

Setelah aksi POST dijalankan, server akan:
1. Update database
2. Panggil `updateAntrianCache()` → perbarui cache
3. SSE stream otomatis mendeteksi perubahan hash → kirim data baru ke semua client

### 5.4 Filter Poli

Admin dapat memfilter antrian berdasarkan poli. Filter diterapkan **client-side**:

```javascript
let daftarTungguFiltered = data.daftar_tunggu;
if (filterPoli !== "") {
    daftarTungguFiltered = daftarTungguFiltered.filter(
        item => item.kode_poli === filterPoli
    );
}
```

---

## 6. Papan Display — Real-time SSE + Suara

### 6.1 Mengapa Papan Menggunakan SSE?

Papan display membutuhkan **real-time updates** karena:
1. Menampilkan antrian yang sedang dipanggil (perubahan seketika)
2. Suara panggilan otomatis (dingdong + Web Speech API)
3. Display publik harus responsif tanpa delay

### 6.2 Fitur Papan Display

| Fitur | Teknologi |
|-------|-----------|
| Nomor antrian dipanggil (besar) | SSE + DOM manipulation |
| Nama pasien dipanggil | SSE + DOM manipulation |
| 4 antrian berikutnya | SSE + DOM manipulation |
| Suara dingdong notifikasi | Audio MP3 (`dingdong.mp3`) |
| Suara panggilan otomatis | Web Speech API (id-ID) |
| Jam digital real-time | setInterval() |
| Reconnect otomatis | Exponential backoff |

### 6.3 Suara Panggilan (Dingdong + Speech)

Sesuai panduan, suara panggilan terdiri dari 2 tahap:
1. **Audio dingdong.mp3** — notifikasi "ting tong"
2. **Web Speech API** — menyebutkan nomor, nama, dan poli

```javascript
function bunyiSuaraPanggilan(nomor, nama, poli) {
    // Batalkan speech sebelumnya
    window.speechSynthesis.cancel();

    // 1. Mainkan suara dingdong
    audioDingdong.currentTime = 0;
    audioDingdong.play();

    // 2. Setelah audio selesai, ucapkan teks
    audioDingdong.onended = function() {
        const kalimat = `Nomor antrian ${nomor}. Atas nama ${nama}. Silakan menuju ke ${poli}.`;
        const utterance = new SpeechSynthesisUtterance(kalimat);
        utterance.lang = 'id-ID';
        utterance.rate = 0.85;
        utterance.pitch = 1.0;
        window.speechSynthesis.speak(utterance);
    };
}
```

### 6.4 Anti-redundansi Suara

Mekanisme `lastCalledId` mencegah suara diputar berulang untuk pasien yang sama:

```javascript
if (lastCalledId !== active.idantrian) {
    lastCalledId = active.idantrian;
    bunyiSuaraPanggilan(...);
}
```

---

## 7. Guest — Registrasi + Tiket Tab Baru

### 7.1 Alur Guest

Sesuai panduan studi kasus:
> "Tab baru terbuka di browser guest menampilkan nomor antrian dan nama secara personal (pengganti kertas dicetak)."

**Alur lengkap:**
1. Guest membuka halaman `/antrian`
2. Mengisi nama dan memilih poli
3. Submit form → POST `/antrian/daftar`
4. Server simpan DB + update cache → return JSON dengan `tiket_url`
5. Browser membuka **tab baru** ke `/antrian/tiket/{id}`
6. Tiket pribadi tampil dengan nomor, nama, poli, dan tombol cetak

### 7.2 Halaman Tiket (`/antrian/tiket/{id}`)

Halaman tiket menampilkan:
- Nama poli dengan badge hijau
- Nomor antrian ukuran besar
- Nama pasien
- Waktu pendaftaran
- Tombol **Cetak Tiket** (mencetak halaman)
- Tombol **Tutup** (menutup tab)

### 7.3 Kode Guest JavaScript

```javascript
.then(res => {
    if (res.data.success) {
        // ✅ BUKA TAB BARU dengan tiket pribadi
        if (res.data.tiket_url) {
            window.open(res.data.tiket_url, '_blank');
        }
        alert('✅ Pendaftaran berhasil! Nomor: ' + res.data.nomor);
    }
})
```

## 8. Endpoint API

### 8.1 Endpoint Publik (Tanpa Auth)

| Method | Route | Fungsi |
|--------|-------|--------|
| GET | `/antrian` | Halaman pendaftaran guest |
| POST | `/antrian/daftar` | Daftar antrian baru |
| GET | `/antrian/tiket/{id}` | **Halaman tiket pribadi (tab baru)** |
| GET | `/antrian/papan` | Halaman papan display |
| GET | `/antrian/sse/stream` | SSE stream (alias 1) |
| GET | `/sse/antrian` | SSE stream (alias 2, sesuai panduan) |

### 8.2 Endpoint Admin (Auth + Role Admin)

| Method | Route | Fungsi |
|--------|-------|--------|
| GET | `/admin/antrian` | Halaman panel admin |
| GET | `/admin/antrian/data` | **Data antrian dari cache** (fallback polling) |
| POST | `/admin/antrian/panggil` | Panggil antrian berikutnya/spesifik |
| POST | `/admin/antrian/lewatkan` | Lewatkan antrian aktif |
| POST | `/admin/antrian/panggil_terlewat` | Panggil ulang antrian terlewat |

---

## 8. Alur Data Lengkap

### 8.1 Skenario: Pasien Mendaftar

```
Guest                  Server                    Database          Cache
  │                      │                         │                │
  │── POST /daftar ──────►│                         │                │
  │                      │── INSERT ───────────────►│                │
  │                      │◄── OK ──────────────────│                │
  │                      │── updateAntrianCache() ──│──► SELECT ────►│
  │                      │◄─────────────────────────│◄── result ────│
  │                      │── Cache::put(state) ────────────────────►│
  │◄── JSON {tiket_url} ─│                         │                │
  │                      │                         │                │
  │── Tab Baru ──────────►│                         │                │
  │   GET /tiket/{id}    │── SELECT FROM DB ───────►│                │
  │◄── HTML Tiket ───────│◄── result ──────────────│                │
```

### 8.2 Skenario: Admin Memanggil Antrian

```
Admin Panel            Server                  Database        Cache
    │                    │                        │              │
    │── POST /panggil ──►│                        │              │
    │                    │── UPDATE status ──────►│              │
    │                    │── updateAntrianCache()─►── SELECT ───►│
    │                    │── Cache::put(state) ────────────────►│
    │◄── JSON sukses ────│                        │              │
    │                    │                        │              │
    │   (SSE otomatis deteksi perubahan)          │              │
    │◄── SSE: queue-update ───────────────────────◄── Cache::get │
```

### 8.3 Skenario: Papan Display Menerima Update

```
Papan Display          SSE Server              Cache
    │                    │                       │
    │── EventSource ────►│                       │
    │                    │── Cache::get(state) ──►│
    │◄── queue-update ───│◄──────────────────────│
    │                    │                       │
    │   (setiap 1 detik cek hash)               │
    │                    │── Cache::get(state) ──►│
    │                    │  (hash sama, skip)     │
    │                    │                       │
    │   (Admin panggil pasien)                   │
    │                    │── Cache::get(state) ──►│
    │◄── queue-update ───│  (hash beda, kirim!)  │
    │   (nomor baru + suara)                     │
```

---

## 9. Cara Menjalankan

### 9.1 Prasyarat

- PHP 8.0+
- Laravel 8+
- Database (PostgreSQL/MySQL)
- Cache driver (file/redis/memcached) — default Laravel menggunakan `file`

### 9.2 Konfigurasi Cache

Pastikan `.env` memiliki konfigurasi cache:

```env
CACHE_DRIVER=file
# atau untuk production:
# CACHE_DRIVER=redis
# REDIS_HOST=127.0.0.1
# REDIS_PASSWORD=null
# REDIS_PORT=6379
```

### 9.3 Database Migration

```sql
CREATE TABLE antrian (
    idantrian     SERIAL PRIMARY KEY,
    nomor         VARCHAR(20),
    nama          VARCHAR(150) NOT NULL,
    idpoli        INTEGER NOT NULL REFERENCES poli(idpoli),
    status        VARCHAR(20) DEFAULT 'menunggu',
    waktu_panggil TIMESTAMP,
    waktu_selesai TIMESTAMP,
    created_at    TIMESTAMP DEFAULT NOW(),
    updated_at    TIMESTAMP,
    deleted_at    TIMESTAMP
);

CREATE INDEX idx_antrian_status_date 
    ON antrian(status, created_at) 
    WHERE deleted_at IS NULL;
```

### 9.4 Menjalankan Aplikasi

```bash
# Jalankan aplikasi Laravel
php artisan serve

# Akses halaman:
# Guest:       http://localhost:8000/antrian
# Admin:       http://localhost:8000/admin/antrian (login required)
# Papan:       http://localhost:8000/antrian/papan
# SSE Stream:  http://localhost:8000/antrian/sse/stream
```

### 9.5 Testing SSE Stream

Gunakan `curl` untuk test SSE:

```bash
curl -N http://localhost:8000/antrian/sse/stream

# Output:
# event: queue-update
# data: {"daftar_tunggu":[...],"sedang_dipanggil":null,...}
# 
# : keep-alive
# 
# : keep-alive
```

---

## 10. Pemecahan Masalah

### 10.1 SSE Tidak Mengirim Data

**Gejala:** Koneksi SSE terbuka tapi tidak menerima data.

**Penyebab & Solusi:**

| Penyebab | Solusi |
|----------|--------|
| Buffering server | Tambahkan header `X-Accel-Buffering: no` untuk Nginx |
| Kompresi GZip | Matikan dengan `@apache_setenv('no-gzip', 1)` |
| Session lock | Panggil `session_write_close()` di awal stream |
| PHP time limit | `set_time_limit(0)` |
| Firewall/Proxy | Pastikan proxy tidak mem-buffer response |

### 10.2 Admin Tidak Melihat Data Terbaru

**Gejala:** Admin melakukan panggilan tapi data tidak berubah.

**Penyebab & Solusi:**

1. **Cache tidak diperbarui** — Pastikan `updateAntrianCache()` dipanggil setelah setiap aksi
2. **Cache driver file** — Pada shared hosting, cache file mungkin lambat. Gunakan Redis
3. **Interval polling** — Admin polling setiap 3 detik. Tunggu atau refresh halaman

### 10.3 Suara Panggilan Tidak Terdengar

**Gejala:** Papan display menampilkan nomor tapi tidak ada suara.

**Penyebab & Solusi:**

| Penyebab | Solusi |
|----------|--------|
| Browser tidak mendukung Web Speech API | Gunakan Chrome/Edge (terbaru) |
| Suara diblokir autoplay | Izinkan autoplay di browser |
| SpeechSynthesis sedang sibuk | `window.speechSynthesis.cancel()` sudah dihandle |
| Volume browser mute | Periksa volume sistem dan browser |

### 10.4 Koneksi SSE Sering Terputus

**Gejala:** Papan display sering menampilkan status "menghubungkan...".

**Solusi:**
- SSE sudah memiliki **exponential backoff** (delay 1s → 2s → 4s → ... → 30s)
- Cek kestabilan jaringan
- Cek konfigurasi timeout server (Nginx `proxy_read_timeout`, Apache `TimeOut`)

### 10.5 Cache Tidak Konsisten

**Gejala:** Admin dan papan menampilkan data berbeda.

**Solusi:**
- `updateAntrianCache()` menggunakan query yang sama untuk admin dan papan
- Cache disimpan dengan key `antrian_state` yang sama
- Periksa apakah semua method memanggil `updateAntrianCache()` setelah perubahan data

---

## 11. Ringkasan Teknis

### File yang Terlibat

```
app/Http/Controllers/AntrianController.php    → Controller utama
app/Models/Antrian.php                        → Model Eloquent
routes/web.php                                → Definisi route
resources/views/antrian/guest.blade.php       → Halaman daftar
resources/views/antrian/tiket.blade.php       → Halaman tiket pribadi (tab baru)
resources/views/antrian/admin.blade.php       → Panel admin (SSE + fallback polling)
resources/views/antrian/papan.blade.php       → Display publik (SSE + dingdong + speech)
public/audio/dingdong.mp3                     → Suara notifikasi panggilan
```

### Teknologi yang Digunakan

| Teknologi | Penggunaan |
|-----------|-----------|
| **Laravel Cache** | Menyimpan state antrian agar tidak perlu query DB terus-menerus |
| **Server-Sent Events** | Push data real-time ke admin & papan display |
| **Axios** | HTTP client untuk aksi operator (POST panggil/lewatkan) |
| **Audio MP3** | Suara dingdong notifikasi sebelum panggilan suara |
| **Web Speech API** | Suara panggilan otomatis (bahasa Indonesia) |
| **Exponential Backoff** | Reconnect cerdas saat koneksi SSE terputus |
| **MD5 Hash** | Deteksi perubahan data sebelum kirim event SSE |

---

*Dokumentasi ini dibuat pada: 1 Juni 2026*
*Sistem Antrian SSE — BUKU App*
