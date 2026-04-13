<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Kategori;
use App\Models\Buku;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class HomeController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */

    public function welcome()
    {
        // 1. Ambil Total Data
        $totalBuku = Buku::count();
        $totalKategori = Kategori::count();
        // Ambil vendor pertama yang ada di database
        $kantin = DB::table('vendor')->first();

        $menu = DB::table('menu')
            ->join('vendor', 'menu.idvendor', '=', 'vendor.idvendor')
            ->select('menu.*', 'vendor.nama_vendor')
            ->get();

        // Ambil semua vendor untuk keperluan filter di halaman depan
        $vendors = DB::table('vendor')->get();

        // 2. Ambil Koleksi Buku Terbaru (Ambil 4 buku terakhir berdasarkan idbuku)
        $bukuTerbaru = Buku::with('kategori')->orderBy('idbuku', 'desc')->take(4)->get();

        // 3. Statistik Kategori untuk Chart (Doughnut)
        // Menghitung jumlah buku per kategori
        $kategoriStats = Kategori::withCount('buku')
            ->get()
            ->map(function ($k) {
                return [
                    'nama' => $k->nama_kategori,
                    'total' => $k->buku_count
                ];
            });

        // 4. Data Pertumbuhan Koleksi per Kategori (untuk line chart)
        // Menggunakan jumlah buku per kategori sebagai pertumbuhan
        $pertumbuhanData = Kategori::withCount('buku')
            ->orderBy('idkategori')
            ->get()
            ->map(function ($k) {
                return [
                    'nama' => $k->nama_kategori,
                    'total' => $k->buku_count
                ];
            });

        return view('welcome', compact('totalBuku', 'totalKategori', 'bukuTerbaru', 'kategoriStats', 'pertumbuhanData', 'menu', 'vendors', 'kantin'));
    }

    public function index()
    {
        return view('home');
    }
}
