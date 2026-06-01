<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MenuController extends Controller
{
    /**
     * Middleware untuk ensure vendor hanya akses menu mereka
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Get vendor yang login
     */
    private function getVendor()
    {
        return Auth::user()->vendor;
    }

    /**
     * Menampilkan menu milik vendor yang login
     */
    public function index()
    {
        $vendor = $this->getVendor();
        
        if (!$vendor) {
            return redirect()->route('vendor.dashboard')->with('error', 'Anda belum terdaftar sebagai vendor');
        }

        $menus = Menu::where('idvendor', $vendor->idvendor)
                    ->orderBy('idmenu', 'asc')
                    ->get();
        
        return view('vendor.menu.index', compact('menus', 'vendor'));
    }

    /**
     * Tampilkan form buat menu baru
     */
    public function create()
    {
        $vendor = $this->getVendor();
        
        if (!$vendor) {
            return redirect()->route('vendor.dashboard')->with('error', 'Anda belum terdaftar sebagai vendor');
        }

        return view('vendor.menu.create', compact('vendor'));
    }

    /**
     * Simpan menu baru
     */
    public function store(Request $request)
    {
        $vendor = $this->getVendor();
        
        if (!$vendor) {
            return redirect()->route('vendor.dashboard')->with('error', 'Anda belum terdaftar sebagai vendor');
        }

        $request->validate([
            'nama_menu' => 'required|string|max:255',
            'harga'     => 'required|integer|min:500',
            'gambar'    => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $data = $request->only('nama_menu', 'harga');
        $data['idvendor'] = $vendor->idvendor;

        if ($request->hasFile('gambar')) {
            $path = $request->file('gambar')->store('menu', 'public');
            $data['path_gambar'] = $path;
        }

        Menu::create($data);

        return redirect()->route('vendor.menu.index')->with('success', 'Menu berhasil ditambahkan');
    }

    /**
     * Edit menu
     */
    public function edit(Menu $menu)
    {
        $vendor = $this->getVendor();
        
        if (!$vendor || $menu->idvendor !== $vendor->idvendor) {
            return redirect()->route('vendor.menu.index')->with('error', 'Anda tidak memiliki akses ke menu ini');
        }

        return view('vendor.menu.edit', compact('menu', 'vendor'));
    }

    /**
     * Update menu
     */
    public function update(Request $request, Menu $menu)
    {
        $vendor = $this->getVendor();
        
        if (!$vendor || $menu->idvendor !== $vendor->idvendor) {
            return redirect()->route('vendor.menu.index')->with('error', 'Anda tidak memiliki akses ke menu ini');
        }

        $request->validate([
            'nama_menu' => 'required|string|max:255',
            'harga'     => 'required|integer|min:500',
            'gambar'    => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $data = $request->only('nama_menu', 'harga');

        if ($request->hasFile('gambar')) {
            $path = $request->file('gambar')->store('menu', 'public');
            $data['path_gambar'] = $path;
        }

        $menu->update($data);

        return redirect()->route('vendor.menu.index')->with('success', 'Menu berhasil diupdate');
    }

    /**
     * Hapus menu
     */
    public function destroy(Menu $menu)
    {
        $vendor = $this->getVendor();
        
        if (!$vendor || $menu->idvendor !== $vendor->idvendor) {
            return redirect()->route('vendor.menu.index')->with('error', 'Anda tidak memiliki akses ke menu ini');
        }

        $menu->delete();

        return redirect()->route('vendor.menu.index')->with('success', 'Menu berhasil dihapus');
    }
}