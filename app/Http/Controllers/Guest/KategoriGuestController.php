<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Models\Kategori;
use App\Models\Buku;

class KategoriGuestController extends Controller
{
    private function getStats()
    {
        return [
            'jumlahKategori' => DB::table('kategori')->count(),
            'jumlahBuku'     => DB::table('buku')->count(),
        ];
    }

    public function index()
    {
        $dataKategori = DB::table('kategori')
            ->orderBy('nama_kategori', 'asc')
            ->get();

        return view('guest.kategori-guest', compact('dataKategori'));
    }
}
