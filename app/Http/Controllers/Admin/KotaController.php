<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KotaController extends Controller
{
    private function getLayout()
    {
        return (Auth::user()->idrole == 1) ? 'layouts.admin.main' : 'layouts.visitor.main';
    }

    public function index()
    {
        $layout = $this->getLayout(); // Panggil fungsi getLayout
        
        // Kirim variabel $layout ke view agar @extends($layout) bisa bekerja
        return view('admin.kota.index', compact('layout'));
    }
}