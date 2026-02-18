<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardAdminController extends Controller
{
    /**
     * Fungsi pembantu untuk mengambil statistik dasar.
     * PERBAIKAN: Menambahkan fungsi yang sebelumnya hilang.
     */
    private function getStats()
    {
        return [
            'jumlahPengunjung' => DB::table('users')->where('idrole', 2)->count(),
            'jumlahKategori' => DB::table('kategori')->count(),
            'jumlahBuku' => DB::table('buku')->count(),
        ];
    }

    public function index()
    {
        return view('admin.dashboard-admin', $this->getStats());
    }

    // Fungsi untuk halaman Kategori
    public function kategori()
    {
        // Mengambil data kategori untuk ditampilkan di tabel
        $dataKategori = DB::table('kategori')->orderBy('nama_kategori', 'asc')->get();

        // Menggabungkan statistik dengan data kategori
        // PERBAIKAN: Mengarahkan ke view admin, bukan visitor
        $data = array_merge($this->getStats(), ['dataKategori' => $dataKategori]);

        return view('admin.kategori-admin', $data);
    }

    // Fungsi untuk halaman Buku
    public function buku()
    {
        // Mengambil data buku beserta nama kategorinya menggunakan Join
        $dataBuku = DB::table('buku')
            ->leftJoin('kategori', 'buku.idkategori', '=', 'kategori.idkategori')
            ->select('buku.*', 'kategori.nama_kategori')
            ->get();

        // Menggabungkan statistik dengan data buku
        $data = array_merge($this->getStats(), ['dataBuku' => $dataBuku]);

        return view('admin.buku-admin', $data);
    }
}