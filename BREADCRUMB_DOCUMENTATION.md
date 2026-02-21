# Dokumentasi Breadcrumb Struktur

## Overview
Setiap halaman di aplikasi ini memiliki breadcrumb yang menunjukkan lokasi user dalam navigasi aplikasi. Breadcrumb diatur per role (Admin dan Visitor).

---

## ROLE: ADMIN

### 1. Dashboard Admin
- **View**: `resources/views/admin/dashboard-admin.blade.php`
- **Route**: `admin.dashboard`
- **Title**: Dashboard Admin
- **Breadcrumb**: `Dashboard > Dashboard (aktif)`
```blade
@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
@endsection
```

### 2. Manajemen Buku
- **View**: `resources/views/admin/buku-admin.blade.php`
- **Route**: `admin.buku`
- **Title**: Manajemen Buku
- **Breadcrumb**: `Dashboard > Buku (aktif)`
```blade
@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">Buku</li>
@endsection
```

### 3. Manajemen Kategori
- **View**: `resources/views/admin/kategori-admin.blade.php`
- **Route**: `admin.kategori`
- **Title**: Manajemen Kategori
- **Breadcrumb**: `Dashboard > Kategori (aktif)`
```blade
@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">Kategori</li>
@endsection
```

### 4. Manajemen Pengguna
- **View**: `resources/views/admin/pengguna-admin.blade.php`
- **Route**: `admin.pengguna`
- **Title**: Manajemen Pengguna
- **Breadcrumb**: `Dashboard > Pengguna (aktif)`
```blade
@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">Pengguna</li>
@endsection
```

---

## ROLE: VISITOR

### 1. Dashboard Visitor
- **View**: `resources/views/visitor/dashboard-visitor.blade.php`
- **Route**: `visitor.dashboard`
- **Title**: Dashboard Pengunjung
- **Breadcrumb**: `Dashboard > Dashboard (aktif)`
```blade
@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
@endsection
```

### 2. Daftar Buku
- **View**: `resources/views/visitor/buku-visitor.blade.php`
- **Route**: `visitor.buku`
- **Title**: Daftar Buku
- **Breadcrumb**: `Dashboard > Buku (aktif)`
```blade
@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">Buku</li>
@endsection
```

### 3. Daftar Kategori
- **View**: `resources/views/visitor/kategori-visitor.blade.php`
- **Route**: `visitor.kategori`
- **Title**: Daftar Kategori
- **Breadcrumb**: `Dashboard > Kategori (aktif)`
```blade
@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">Kategori</li>
@endsection
```

---

## Implementasi Breadcrumb

### Standard Breadcrumb Structure (Admin & Visitor)
Setiap halaman menggunakan struktur yang sama:

```blade
@extends('layouts.{role}.main')

@section('title-page', 'Nama Halaman')

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">Nama Item</li>
@endsection

@section('content')
    <!-- Konten halaman -->
@endsection
```

### Layout Integration
**Admin Layout** (`resources/views/layouts/admin/main.blade.php`):
```blade
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
        @yield('breadcrumb')
    </ol>
</nav>
```

**Visitor Layout** (`resources/views/layouts/visitor/main.blade.php`):
```blade
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('visitor.dashboard') }}">Dashboard</a></li>
        @yield('breadcrumb')
    </ol>
</nav>
```

---

## Pengembangan Lebih Lanjut

### Untuk Menambah Halaman Detail/Sub-page
Jika Anda menambahkan halaman detail (misal: Detail Buku), gunakan breadcrumb berlapis:

```blade
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.buku') }}">Buku</a></li>
    <li class="breadcrumb-item active" aria-current="page">Detail Buku</li>
@endsection
```

### Menggunakan Component Breadcrumb (Opsional)
Jika ingin lebih maintainable, bisa menggunakan component yang sudah disediakan di `resources/views/components/breadcrumb.blade.php`:

```blade
@include('components.breadcrumb', [
    'items' => [
        ['url' => route('admin.dashboard'), 'label' => 'Dashboard'],
        ['url' => route('admin.buku'), 'label' => 'Buku'],
        ['label' => 'Detail Buku', 'active' => true]
    ]
])
```

---

## CSS Classes
- `.breadcrumb` - Container untuk breadcrumb
- `.breadcrumb-item` - Item dalam breadcrumb
- `.breadcrumb-item.active` - Item aktif (tidak bisa di-click)

## Aksesibilitas
- Setiap breadcrumb menggunakan `<nav aria-label="breadcrumb">` untuk screen readers
- Item aktif menggunakan `aria-current="page"` untuk menunjukkan page aktif saat ini
