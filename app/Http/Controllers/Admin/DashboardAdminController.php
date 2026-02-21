<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Kategori;
use App\Models\Buku;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class DashboardAdminController extends Controller
{
    private function getStats()
    {
        return [
            'jumlahPengguna' => DB::table('users')->count(),
            'jumlahKategori' => DB::table('kategori')->count(),
            'jumlahBuku' => DB::table('buku')->count(),
        ];
    }

    public function index()
    {
        return view('admin.dashboard-admin', $this->getStats());
    }

    // Fungsi untuk halaman Pengguna
    public function pengguna()
    {
        $dataPengguna = DB::table('users')
            ->join('roles', 'users.idrole', '=', 'roles.idrole')
            ->select('users.*', 'roles.nama_role')
            ->orderBy('users.iduser', 'asc')
            ->get();

        $data = array_merge($this->getStats(), ['dataPengguna' => $dataPengguna]);

        return view('admin.pengguna-admin', $data);
    }


    // Fungsi untuk halaman Kategori
    public function kategori()
    {
        // Mengambil data kategori untuk ditampilkan di tabel
        $dataKategori = DB::table('kategori')->orderBy('idkategori', 'asc')->get();

        // Menggabungkan statistik dengan data kategori
        $data = array_merge($this->getStats(), ['dataKategori' => $dataKategori]);

        return view('admin.kategori-admin', $data);
    }

    // --- CRUD KATEGORI ---
    public function kategoriStore(Request $request)
    {
        DB::table('kategori')->insert(['nama_kategori' => $request->nama_kategori]);
        return redirect()->back()->with('success', 'Kategori ditambahkan!');
    }

    public function kategoriUpdate(Request $request, $id)
    {
        DB::table('kategori')->where('idkategori', $id)->update(['nama_kategori' => $request->nama_kategori]);
        return redirect()->back()->with('success', 'Kategori diperbarui!');
    }

    public function kategoriDestroy($id)
    {
        DB::table('kategori')->where('idkategori', $id)->delete();
        return redirect()->back()->with('success', 'Kategori dihapus!');
    }

    // Fungsi untuk halaman Buku
    public function buku()
    {
        // 1. Ambil data buku dan kategorinya menggunakan Join
        $dataBuku = DB::table('buku')
            ->leftJoin('kategori', 'buku.idkategori', '=', 'kategori.idkategori')
            ->select('buku.*', 'kategori.nama_kategori')
            ->orderBy('buku.idbuku', 'asc')
            ->get();

        // 2. Ambil data kategori
        $dataKategori = DB::table('kategori')->orderBy('idkategori', 'asc')->get();

        // 3. Menggabungkan statistik dengan data buku DAN data kategori
        $data = array_merge($this->getStats(), [
            'dataBuku' => $dataBuku,
            'dataKategori' => $dataKategori
        ]);

        return view('admin.buku-admin', $data);
    }

    // --- CRUD BUKU ---
    public function bukuStore(Request $request)
    {
        DB::table('buku')->insert([
            'kode' => $request->kode,
            'judul' => $request->judul,
            'pengarang' => $request->pengarang,
            'idkategori' => $request->idkategori
        ]);
        return redirect()->back()->with('success', 'Buku ditambahkan!');
    }

    public function bukuUpdate(Request $request, $id)
    {
        DB::table('buku')->where('idbuku', $id)->update([
            'kode' => $request->kode,
            'judul' => $request->judul,
            'pengarang' => $request->pengarang,
            'idkategori' => $request->idkategori
        ]);
        return redirect()->back()->with('success', 'Buku diperbarui!');
    }

    public function bukuDestroy($id)
    {
        DB::table('buku')->where('idbuku', $id)->delete();
        return redirect()->back()->with('success', 'Buku dihapus!');
    }
}
