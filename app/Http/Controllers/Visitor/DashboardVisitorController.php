<?php

namespace App\Http\Controllers\Visitor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardVisitorController extends Controller
{

    public function index()
    {
        //Jumlah pengguna
        $jumlahPengguna = DB::table('users')->count();

        //Jumlah kategori
        $jumlahKategori = DB::table('kategori')->count();

        //Jumlah buku
        $jumlahBuku = DB::table('buku')->count();

        //Jumlah peminjaman
        // $jumlahPeminjaman = DB::table('peminjaman')->count();

        return view('visitor.dashboard-visitor', compact('jumlahPengguna', 'jumlahKategori', 'jumlahBuku'));
    }
}
