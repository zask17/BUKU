<?php

namespace App\Http\Controllers\Visitor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardVisitorController extends Controller
{
    // Fungsi pembantu untuk mengambil statistik data
    private function getStats()
    {
        return [
            'jumlahKategori'   => DB::table('kategori')->count(),
            'jumlahBuku'       => DB::table('buku')->count(),
            // 'jumlahPengunjung' => DB::table('users')->where('role_id', 2)->count(), // Menghitung role visitor
        ];
    }

    public function index()
    {
        // Menggunakan getStats agar data konsisten di semua halaman
        return view('visitor.dashboard-visitor', $this->getStats());
    }

    public function kategori()
    {
        // Mengambil data kategori untuk ditampilkan di tabel
        $allKategori = DB::table('kategori')->get();

        // Menggabungkan statistik dashboard dengan data kategori
        $data = array_merge($this->getStats(), ['allKategori' => $allKategori]);

        return view('visitor.kategori-visitor', $data);
    }


    public function buku()
    {
        // Mengambil data buku dengan join kategori
        $dataBuku = DB::table('buku')
            ->leftJoin('kategori', 'buku.idkategori', '=', 'kategori.idkategori')
            ->select('buku.*', 'kategori.nama_kategori')
            ->get();

        $data = array_merge($this->getStats(), ['dataBuku' => $dataBuku]);

        return view('visitor.buku-visitor', $data);
    }
}
