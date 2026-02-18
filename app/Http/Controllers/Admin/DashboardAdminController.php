<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardAdminController extends Controller
{
    public function index()
    {
        //Jumlah pengunjung
        $jumlahPengunjung = DB::table('users')->where('idrole', 2)->count();

        //Jumlah kategori
        $jumlahKategori = DB::table('kategori')->count();

        //Jumlah buku
        $jumlahBuku = DB::table('buku')->count();

        //Jumlah peminjaman
        // $jumlahPeminjaman = DB::table('peminjaman')->count();

        return view('admin.dashboard-admin', compact('jumlahPengunjung', 'jumlahKategori', 'jumlahBuku'));
    }

    // Fungsi untuk halaman Kategori
    public function kategori()
    {
        // Tetap kirim data statistik agar card di atas tidak error
        $jumlahPengunjung = DB::table('users')->where('idrole', 2)->count();
        $jumlahKategori = DB::table('kategori')->count();
        $jumlahBuku = DB::table('buku')->count();

        return view('admin.kategori-admin', compact('jumlahPengunjung', 'jumlahKategori', 'jumlahBuku'));
    }

    // Fungsi untuk halaman Buku
    public function buku()
    {
        $jumlahPengunjung = DB::table('users')->where('idrole', 2)->count();
        $jumlahKategori = DB::table('kategori')->count();
        $jumlahBuku = DB::table('buku')->count();

        return view('admin.buku-admin', compact('jumlahPengunjung', 'jumlahKategori', 'jumlahBuku'));
    }
}
