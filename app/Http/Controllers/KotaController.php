<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class KotaController extends Controller
{

    private function getLayout()
    {
        return (Auth::user()->idrole == 1) ? 'layouts.admin.main' : 'layouts.visitor.main';
    }

    public function index()
    {
        return view('admin.kota.index');
    }
}
