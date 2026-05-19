<?php

namespace App\Http\Controllers;

use App\Models\Antrian;
use App\Models\Poli;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class AntrianController extends Controller
{
    // ===================== GUEST =====================
    public function guestIndex()
    {
        $polis = Poli::whereNull('deleted_at')->get();
        return view('antrian.guest', compact('polis'));
    }

    public function guestDaftar(Request $request)
    {
        $request->validate([
            'nama'   => 'required|string|max:150',
            'idpoli' => 'required|exists:poli,idpoli'
        ]);

        $antrian = Antrian::create([
            'nama'   => $request->nama,
            'idpoli' => $request->idpoli,
            'status' => 'waiting'
        ]);

        // Refresh agar trigger nomor urut harian dari database terbaca
        $antrian->refresh();

        $successData = [
            'nama'  => $antrian->nama,
            'nomor' => $antrian->nomor,           
            'poli'  => $antrian->poli->nama_poli ?? 'Poli Umum'
        ];

        // Memicu update SSE agar Admin & Papan langsung melihat penambahan antrian baru
        Cache::put('antrian_trigger_update', true, now()->addMinutes(10));

        return redirect()->back()->with('success_antrian', $successData);
    }

    // ===================== ADMIN =====================
    public function adminIndex()
    {
        return view('antrian.admin');
    }

    /**
     * Memanggil pasien berikutnya yang berstatus 'waiting' urut dari nomor terkecil hari ini
     */
    public function panggilNext()
    {
        // Mengambil antrian pertama yang masih menunggu hari ini
        $antrian = Antrian::where('tanggal', today())
            ->where('status', 'waiting')
            ->orderBy('nomor_harian', 'asc') // Dipastikan urut dari nomor terkecil
            ->first();

        if (!$antrian) {
            return response()->json([
                'status' => 'empty', 
                'message' => 'Antrian hari ini kosong atau semua sudah dipanggil!'
            ]);
        }

        // Update status antrian menjadi sedang dipanggil (calling)
        $antrian->update([
            'status'        => 'calling',
            'waktu_panggil' => now()
        ]);

        // Simpan data antrian yang sedang aktif dipanggil ke dalam Cache
        Cache::put('antrian_sekarang', $antrian->fresh(), now()->addMinutes(10));
        
        // Picu pembaruan stream SSE untuk semua client (Admin & Papan Antrian)
        Cache::put('antrian_trigger_update', true, now()->addMinutes(10));

        return response()->json(['status' => 'success', 'data' => $antrian]);
    }

    /**
     * Mengubah status antrian yang saat ini dipanggil menjadi 'skipped' (terlewat)
     */
    public function lewatkanAntrian()
    {
        $antrianSekarang = Cache::get('antrian_sekarang');

        if (!$antrianSekarang) {
            return response()->json(['status' => 'error', 'message' => 'Tidak ada antrian yang sedang dipanggil']);
        }

        // Ambil ID antrian baik berupa objek maupun array cache
        $idAntrian = $antrianSekarang['idantrian'] ?? $antrianSekarang->idantrian ?? null;

        if ($idAntrian) {
            // Update status di database menjadi 'skipped'
            Antrian::where('idantrian', $idAntrian)->update([
                'status' => 'skipped'
            ]);
        }

        // Bersihkan cache antrian aktif karena statusnya sudah dilewatkan
        Cache::forget('antrian_sekarang');
        
        // Picu pembaruan data pada tampilan web via SSE
        Cache::put('antrian_trigger_update', true, now()->addMinutes(10));

        return response()->json(['status' => 'success']);
    }

    /**
     * Memanggil ulang pasien yang sempat terlewat berdasarkan nomor urutnya
     */
    public function panggilTerlewat(Request $request)
    {
        $request->validate(['nomor' => 'required|integer']);

        // Cari antrian hari ini yang statusnya terlewat (skipped) berdasarkan nomornya
        $antrian = Antrian::where('tanggal', today())
            ->where('nomor', $request->nomor)
            ->where('status', 'skipped')
            ->first();

        if (!$antrian) {
            return response()->json(['status' => 'error', 'message' => 'Antrian terlewat tidak ditemukan']);
        }

        // Kembalikan statusnya menjadi dipanggil (calling) kembali
        $antrian->update([
            'status'        => 'calling',
            'waktu_panggil' => now()
        ]);

        // Perbarui state cache untuk antrian yang sedang aktif dipanggil
        Cache::put('antrian_sekarang', $antrian->fresh(), now()->addMinutes(10));
        
        // Sinyalkan update data ke SSE Stream
        Cache::put('antrian_trigger_update', true, now()->addMinutes(10));

        return response()->json(['status' => 'success']);
    }

    // ===================== PAPAN =====================
    public function papanIndex()
    {
        return view('antrian.papan');
    }

    // ===================== SSE STREAM =====================
    public function stream()
    {
        set_time_limit(0);

        return response()->stream(function () {
            while (true) {
                // Hapus penanda trigger jika ada, agar loop berikutnya tetap berjalan efisien
                if (Cache::get('antrian_trigger_update')) {
                    Cache::forget('antrian_trigger_update');
                }

                $antrianSekarang = Cache::get('antrian_sekarang');

                // Ambil daftar antrian aktif (waiting) hari ini secara urut
                $antrianList = Antrian::where('tanggal', today())
                    ->where('status', 'waiting')
                    ->orderBy('nomor_harian', 'asc')
                    ->get(['idantrian', 'nomor', 'nama', 'waktu_masuk as waktu']);

                // Ambil daftar antrian terlewat (skipped) hari ini secara urut
                $antrianTerlewat = Antrian::where('tanggal', today())
                    ->where('status', 'skipped')
                    ->orderBy('nomor_harian', 'asc')
                    ->get(['idantrian', 'nomor', 'nama']);

                $data = [
                    'antrian_list'     => $antrianList,
                    'antrian_terlewat' => $antrianTerlewat,
                    'antrian_sekarang' => $antrianSekarang,
                ];

                echo "event: queue-update" . PHP_EOL;
                echo "data: " . json_encode($data) . PHP_EOL . PHP_EOL;

                ob_flush();
                flush();

                if (connection_aborted()) break;

                sleep(1); // Melakukan pembaruan aliran data setiap 1 detik
            }
        }, 200, [
            'Content-Type'      => 'text/event-stream',
            'Cache-Control'     => 'no-cache',
            'X-Accel-Buffering' => 'no', // Sangat krusial untuk mencegah buffering jika menggunakan web server Nginx
        ]);
    }
}