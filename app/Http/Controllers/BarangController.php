<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Barang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class BarangController extends Controller
{
    private function getLayout()
    {
        return (Auth::user()->idrole == 1) ? 'layouts.admin.main' : 'layouts.visitor.main';
    }

    public function index()
    {
        $barangs = Barang::orderBy('id_barang', 'asc')->get();
        $layout = $this->getLayout();
        return view('barang.index', compact('barangs', 'layout'));
    }

    public function create()
    {
        $layout = $this->getLayout();
        return view('barang.create', compact('layout'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama'  => 'required|string|max:50',
            'harga' => 'required|integer|min:1000',
        ]);

        Barang::create($request->only('nama', 'harga'));

        return redirect()->route('barang.index')
            ->with('success', 'Barang berhasil ditambahkan');
    }

    public function show($id)
    {
        // Opsional: halaman detail barang
        $barang = Barang::findOrFail($id);
        return view('barang.show', compact('barang'));
    }

    public function edit($id)
    {
        $barang = Barang::findOrFail($id);
        $layout = $this->getLayout();
        return view('barang.edit', compact('barang', 'layout'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama'  => 'required|string|max:50',
            'harga' => 'required|integer',
        ]);

        $barang = Barang::findOrFail($id);
        $barang->update($request->only('nama', 'harga'));

        return redirect()->route('barang.index')->with('success', 'Data diperbarui');
    }

    public function destroy($id)
    {
        Barang::findOrFail($id)->delete();
        return redirect()->route('barang.index')->with('success', 'Data dihapus');
    }


    public function cetakPdf(Request $request)
    {
        // Validasi input
        $request->validate([
            'selected_items' => 'required|array',
            'startX'         => 'required|integer|min:1|max:5',
            'startY'         => 'required|integer|min:1|max:8',
        ], [
            'selected_items.required' => 'Pilih minimal satu barang untuk dicetak!'
        ]);

        $barangs = Barang::whereIn('id_barang', $request->selected_items)
            ->orderBy('id_barang', 'asc')
            ->get();

        // Menghitung start_index untuk grid 5x8
        $start_index = (($request->startY - 1) * 5) + ($request->startX - 1);

        $pdf = Pdf::loadView('barang.pdf', compact(
            'barangs',
            'start_index'
        ))->setPaper('a5', 'landscape');

        return $pdf->stream('tag-harga-tnj108.pdf');
    }
}
