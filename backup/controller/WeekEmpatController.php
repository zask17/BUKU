<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class WeekEmpatController extends Controller
{
    public function index()
    {
        return view('admin.week4.index');
    }

    public function submit(Request $request)
    {
        $data = $request->post('name');

        return response()->json([
            'status' => 'success',
            'code' => 200,
            'message' => 'Data berhasil dikirim!',
            'data' => [
                'name' => $data
            ]
        ]);
    }
}
