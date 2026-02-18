<?php

namespace App\Http\Controllers\Visitor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardVisitorController extends Controller
{

    public function index()
    {
        //Jumlah kategori
        $jumlahKategori = DB::table('kategori')->count();

        //Jumlah buku
        $jumlahBuku = DB::table('buku')->count();

        //Jumlah peminjaman
        // $jumlahPeminjaman = DB::table('peminjaman')->count();

        return view('visitor.dashboard-visitor', compact('jumlahPengguna', 'jumlahKategori', 'jumlahBuku'));
    }

    public function kategori()
    {
        // Memanggil file kategori-visitor.blade.php
        return view('visitor.kategori-visitor', $this->getStats());
    }

    public function buku()
    {
        // Memanggil file buku-visitor.blade.php
        return view('visitor.buku-visitor', $this->getStats());
    }
}
