<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BukuAdminController extends Controller
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
        $dataBuku = DB::table('buku')
            ->leftJoin('kategori', 'buku.idkategori', '=', 'kategori.idkategori')
            ->select('buku.*', 'kategori.nama_kategori')
            ->orderBy('buku.idbuku', 'asc')
            ->get();

        $dataKategori = DB::table('kategori')
            ->orderBy('idkategori', 'asc')
            ->get();

        $data = array_merge($this->getStats(), [
            'dataBuku'     => $dataBuku,
            'dataKategori' => $dataKategori,
        ]);

        return view('admin.buku-admin', $data);
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode'        => 'required|string|max:20',
            'judul'       => 'required|string|max:500',
            'pengarang'   => 'required|string|max:200',
            'idkategori'  => 'nullable|integer|exists:kategori,idkategori',
        ]);

        DB::table('buku')->insert([
            'kode'       => $request->kode,
            'judul'      => $request->judul,
            'pengarang'  => $request->pengarang,
            'idkategori' => $request->idkategori,
        ]);

        return redirect()->back()->with('success', 'Buku berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'kode'        => 'required|string|max:20',
            'judul'       => 'required|string|max:500',
            'pengarang'   => 'required|string|max:200',
            'idkategori'  => 'nullable|integer|exists:kategori,idkategori',
        ]);

        DB::table('buku')
            ->where('idbuku', $id)
            ->update([
                'kode'       => $request->kode,
                'judul'      => $request->judul,
                'pengarang'  => $request->pengarang,
                'idkategori' => $request->idkategori,
            ]);

        return redirect()->back()->with('success', 'Buku berhasil diperbarui!');
    }

    public function destroy($id)
    {
        DB::table('buku')
            ->where('idbuku', $id)
            ->delete();

        return redirect()->back()->with('success', 'Buku berhasil dihapus!');
    }
}
