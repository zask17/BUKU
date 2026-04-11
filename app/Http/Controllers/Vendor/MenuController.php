<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\Vendor;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    // Menggunakan layout khusus vendor
    private function getLayout()
    {
        return 'layouts.vendor.main'; 
    }

    public function index()
    {
        // Menampilkan semua menu dengan informasi vendornya
        $menus = Menu::with('vendor')->orderBy('idmenu', 'asc')->get();
        $layout = $this->getLayout();
        return view('vendor.menu.index', compact('menus', 'layout'));
    }

    public function create()
    {
        $vendors = Vendor::all();
        $layout = $this->getLayout();
        return view('vendor.menu.create', compact('layout', 'vendors'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_menu' => 'required|string|max:255',
            'harga'     => 'required|integer|min:500',
            'idvendor'  => 'required|exists:vendor,idvendor',
            'gambar'    => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $data = $request->only('nama_menu', 'harga', 'idvendor');

        if ($request->hasFile('gambar')) {
            // Menyimpan gambar ke folder public/menu
            $path = $request->file('gambar')->store('menu', 'public');
            $data['path_gambar'] = $path;
        }

        Menu::create($data);

        return redirect()->route('vendor.menu.index')->with('success', 'Menu berhasil ditambahkan');
    }
}