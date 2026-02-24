<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

class PdfController extends Controller
{
    private function getLayout() {
        return (Auth::user()->idrole == 1) ? 'layouts.admin.main' : 'layouts.visitor.main';
    }

    public function index() {
        return view('pdf.index', ['layout' => $this->getLayout()]);
    }

    // --- SERTIFIKAT ---
    public function sertifikatForm() {
        return view('pdf.form-sertifikat', ['layout' => $this->getLayout()]);
    }

    public function sertifikatPreview(Request $request) {
        $data = $request->all();
        $data['penyelenggara'] = "Universitas Airlangga"; // Default
        
        $pdf = Pdf::loadView('pdf.sertifikat', $data)->setPaper('a4', 'landscape');
        
        if ($request->has('download')) {
            return $pdf->download('Sertifikat_'.$request->nama.'.pdf');
        }
        return $pdf->stream();
    }

    // --- UNDANGAN ---
    public function undanganForm() {
        return view('pdf.form-undangan', ['layout' => $this->getLayout()]);
    }

    public function undanganPreview(Request $request) {
        $data = $request->all();
        $pdf = Pdf::loadView('pdf.undangan', $data)->setPaper('a4', 'portrait');
        
        if ($request->has('download')) {
            return $pdf->download('Undangan_Kegiatan.pdf');
        }
        return $pdf->stream();
    }
}