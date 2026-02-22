<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KategoriAdminController extends Controller
{
    private function getStats()
    {
        return [
            'jumlahPengguna' => DB::table('users')->count(),
            'jumlahKategori' => DB::table('kategori')->count(),
            'jumlahBuku'     => DB::table('buku')->count(),
        ];
    }

    public function index()
    {
        $dataKategori = DB::table('kategori')
            ->orderBy('idkategori', 'asc')
            ->get();

        $data = array_merge($this->getStats(), ['dataKategori' => $dataKategori]);

        return view('admin.kategori-admin', $data);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_kategori' => 'required|string|max:100',
        ]);

        DB::table('kategori')->insert([
            'nama_kategori' => $request->nama_kategori,
        ]);

        return redirect()->back()->with('success', 'Kategori berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_kategori' => 'required|string|max:100',
        ]);

        DB::table('kategori')
            ->where('idkategori', $id)
            ->update(['nama_kategori' => $request->nama_kategori]);

        return redirect()->back()->with('success', 'Kategori berhasil diperbarui!');
    }

    public function destroy($id)
    {
        DB::table('kategori')
            ->where('idkategori', $id)
            ->delete();

        return redirect()->back()->with('success', 'Kategori berhasil dihapus!');
    }
}