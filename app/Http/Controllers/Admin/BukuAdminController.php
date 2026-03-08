<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BukuAdminController extends Controller
{
    // private function getStats()
    // {
    //     return [
    //         'jumlahPengguna' => DB::table('users')->count(),
    //         'jumlahKategori' => DB::table('kategori')->count(),
    //         'jumlahBuku'     => DB::table('buku')->count(),
    // //     ];
    // // }

    // public function index()
    // {
    //     $dataBuku = DB::table('buku')
    //         ->leftJoin('kategori', 'buku.idkategori', '=', 'kategori.idkategori')
    //         ->select('buku.*', 'kategori.nama_kategori')
    //         ->orderBy('buku.idbuku', 'asc')
    //         ->get();

    //     $dataKategori = DB::table('kategori')
    //         ->orderBy('idkategori', 'asc')
    //         ->get();

    //     $data = array_merge($this->getStats(), [
    //         'dataBuku'     => $dataBuku,
    //         'dataKategori' => $dataKategori,
    //     ]);

    //     return view('admin.buku.index', compact('dataBuku'));
    // }

    public function index()
    {
        $dataBuku = DB::table('buku')
            ->leftJoin('kategori', 'buku.idkategori', '=', 'kategori.idkategori')
            ->select('buku.*', 'kategori.nama_kategori')
            ->orderBy('buku.idbuku', 'asc')
            ->get();

        return view('admin.buku.index', compact('dataBuku'));
    }

    public function create()
    {
        $dataKategori = DB::table('kategori')->orderBy('nama_kategori', 'asc')->get();
        return view('admin.buku.create', compact('dataKategori'));
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

        return redirect()->route('admin.buku.index')->with('success', 'Buku berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $buku = DB::table('buku')->where('idbuku', $id)->first();
        if (!$buku) {
            return redirect()->route('admin.buku.index')->with('error', 'Data tidak ditemukan.');
        }

        $dataKategori = DB::table('kategori')->orderBy('nama_kategori', 'asc')->get();
        return view('admin.buku.edit', compact('buku', 'dataKategori'));
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

        return redirect()->route('admin.buku.index')->with('success', 'Buku berhasil diperbarui!');
    }

    public function destroy($id)
    {
        DB::table('buku')->where('idbuku', $id)->delete();
        return redirect()->route('admin.buku.index')->with('success', 'Buku berhasil dihapus!');
    }
}
