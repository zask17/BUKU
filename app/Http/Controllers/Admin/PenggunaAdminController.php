<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PenggunaAdminController extends Controller
{
    private function getStats()
    {
        return [
            'jumlahPengguna' => DB::table('users')->count(),
            'jumlahKategori' => DB::table('kategori')->count(),
            'jumlahBuku' => DB::table('buku')->count(),
        ];
    }


    // Fungsi untuk halaman Pengguna
    public function index()
    {
        $dataPengguna = DB::table('users')
            ->join('roles', 'users.idrole', '=', 'roles.idrole')
            ->select('users.*', 'roles.nama_role')
            ->orderBy('users.iduser', 'asc')
            ->get();

        $data = array_merge($this->getStats(), ['dataPengguna' => $dataPengguna]);

        return view('admin.pengguna-admin', $data);
    }
}
