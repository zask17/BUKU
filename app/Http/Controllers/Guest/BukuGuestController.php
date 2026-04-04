<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Models\Kategori;
use App\Models\Buku;

class BukuGuestController extends Controller
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
        $dataBuku = DB::table('buku')
            ->leftJoin('kategori', 'buku.idkategori', '=', 'kategori.idkategori')
            ->select('buku.*', 'kategori.nama_kategori')
            ->orderBy('buku.idbuku', 'asc')
            ->get();

        $data = array_merge($this->getStats(), ['dataBuku' => $dataBuku]);

        return view('guest.buku-guest', $data);
    }
}