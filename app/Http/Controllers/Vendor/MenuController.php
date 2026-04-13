<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MenuController extends Controller
{
    private function getVendor()
    {
        // Mengambil data vendor yang terhubung dengan user yang sedang login
        return Auth::user()->vendor;
    }

    public function index()
    {
        $vendor = $this->getVendor();
        if (!$vendor) return redirect()->back()->with('error', 'Data Vendor tidak ditemukan.');

        // Filter: Hanya ambil menu milik vendor ini
        $menus = Menu::where('idvendor', $vendor->idvendor)->orderBy('idmenu', 'asc')->get();
        
        return view('vendor.menu.index', compact('menus', 'vendor'));
    }

    public function create()
    {
        $vendor = $this->getVendor();
        return view('vendor.menu.create', compact('vendor'));
    }

    public function store(Request $request)
    {
        $vendor = $this->getVendor();
        $request->validate([
            'nama_menu' => 'required|string|max:255',
            'harga'     => 'required|integer|min:500',
            'gambar'    => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $data = $request->only('nama_menu', 'harga');
        $data['idvendor'] = $vendor->idvendor;

        if ($request->hasFile('gambar')) {
            $data['path_gambar'] = $request->file('gambar')->store('menu', 'public');
        }

        Menu::create($data);
        return redirect()->route('vendor.menu.index')->with('success', 'Menu berhasil ditambahkan.');
    }

    public function edit(Menu $menu)
    {
        $vendor = $this->getVendor();
        // Keamanan: Cek apakah menu ini benar milik vendor yang login
        if ($menu->idvendor !== $vendor->idvendor) abort(403, 'Akses Ditolak');

        return view('vendor.menu.edit', compact('menu', 'vendor'));
    }

    public function update(Request $request, Menu $menu)
    {
        $vendor = $this->getVendor();
        if ($menu->idvendor !== $vendor->idvendor) abort(403);

        $request->validate([
            'nama_menu' => 'required|string|max:255',
            'harga'     => 'required|integer|min:500',
            'gambar'    => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $data = $request->only('nama_menu', 'harga');

        if ($request->hasFile('gambar')) {
            if ($menu->path_gambar) Storage::disk('public')->delete($menu->path_gambar);
            $data['path_gambar'] = $request->file('gambar')->store('menu', 'public');
        }

        $menu->update($data);
        return redirect()->route('vendor.menu.index')->with('success', 'Menu berhasil diperbarui.');
    }

    public function destroy(Menu $menu)
    {
        $vendor = $this->getVendor();
        if ($menu->idvendor !== $vendor->idvendor) abort(403);

        if ($menu->path_gambar) Storage::disk('public')->delete($menu->path_gambar);
        $menu->delete();

        return redirect()->route('vendor.menu.index')->with('success', 'Menu berhasil dihapus.');
    }
}