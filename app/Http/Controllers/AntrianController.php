<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class AntrianController extends Controller
{
    // =========================================================================
    // REFRESH DATA CACHE
    // =========================================================================
    private function updateAntrianCache()
    {
        $hariIni = date('Y-m-d');

        // 1. DAFTAR TUNGGU (Tetap sama)
        $daftar_tunggu = DB::table('antrian')
            ->join('poli', 'antrian.idpoli', '=', 'poli.idpoli')
            ->where('antrian.status', 'menunggu')
            ->whereDate('antrian.created_at', $hariIni)
            ->whereNull('antrian.deleted_at')
            ->select('antrian.*', 'poli.nama_poli', 'poli.kode_poli')
            ->orderBy('antrian.idantrian', 'asc')->get();

        // 2. SEDANG DIPANGGIL (Tetap sama)
        $sedang_dipanggil = DB::table('antrian')
            ->join('poli', 'antrian.idpoli', '=', 'poli.idpoli')
            ->where('antrian.status', 'dipanggil')
            ->whereDate('antrian.created_at', $hariIni)
            ->whereNull('antrian.deleted_at')
            ->select('antrian.*', 'poli.nama_poli', 'poli.kode_poli')
            ->orderBy('antrian.waktu_panggil', 'desc')->first();

        // 3. TERLEWAT HARI INI (Tetap sama)
        $terlewat = DB::table('antrian')
            ->join('poli', 'antrian.idpoli', '=', 'poli.idpoli')
            ->where('antrian.status', 'terlewat')
            ->whereDate('antrian.created_at', $hariIni)
            ->whereNull('antrian.deleted_at')
            ->select('antrian.*', 'poli.nama_poli', 'poli.kode_poli')
            ->orderBy('antrian.idantrian', 'desc')->get();

        // 4. DATA HARI LAIN (Perbaikan casting tipe data untuk PostgreSQL)
        $hari_lain = DB::table('antrian')
            ->join('poli', 'antrian.idpoli', '=', 'poli.idpoli')
            ->whereDate('antrian.created_at', '<', $hariIni)
            ->whereNull('antrian.deleted_at')
            ->select('antrian.*', 'poli.nama_poli', 'poli.kode_poli', DB::raw("TO_CHAR(antrian.created_at::timestamp, 'DD-MM-YYYY') as tanggal_antrian"))
            ->orderBy('antrian.created_at', 'desc')
            ->orderBy('antrian.idantrian', 'asc')->get();

        $state = [
            'daftar_tunggu'    => $daftar_tunggu,
            'sedang_dipanggil' => $sedang_dipanggil,
            'terlewat'         => $terlewat,
            'hari_lain'        => $hari_lain
        ];

        Cache::put('antrian_state', $state, now()->addHours(12));
    }

    // =========================================================================
    // 1. REGISTRASI PASIEN (GUEST)
    // =========================================================================
    public function guestIndex()
    {
        $daftarPoli = DB::table('poli')->whereNull('deleted_at')->get();
        return view('antrian.guest', compact('daftarPoli'));
    }

    public function guestDaftar(Request $request)
    {
        $request->validate([
            'nama'   => 'required|string|max:150',
            'idpoli' => 'required|integer|exists:poli,idpoli'
        ]);

        $hariIni = date('Y-m-d');

        // Menggunakan Database Transaction untuk mengunci tabel sementara saat menghitung urutan
        $dataBaru = DB::transaction(function () use ($request, $hariIni) {

            // 1. Ambil kode poli untuk prefix nomor antrian
            $poli = DB::table('poli')->where('idpoli', $request->idpoli)->first();

            // 2. Hitung jumlah antrian poli tersebut yang mendaftar HARI INI
            $jumlahHariIni = DB::table('antrian')
                ->where('idpoli', $request->idpoli)
                ->whereDate('created_at', $hariIni)
                ->whereNull('deleted_at')
                ->count();

            $urutanBaru = $jumlahHariIni + 1;

            // 3. Format nomor antrian (Contoh: UMUM-01, GIGI-02)
            // LPAD di PHP menggunakan str_pad
            $nomorAntrian = $poli->kode_poli . '-' . str_pad($urutanBaru, 2, '0', STR_PAD_LEFT);

            // 4. Masukkan data ke dalam database
            $id = DB::table('antrian')->insertGetId([
                'nama'          => $request->nama,
                'idpoli'        => $request->idpoli,
                'status'        => 'menunggu',
                'nomor'         => $nomorAntrian,
                'created_at'    => now(),
                'updated_at'    => now()
            ], 'idantrian');

            return [
                'idantrian' => $id,
                'nomor'     => $nomorAntrian,
                'nama_poli' => $poli->nama_poli
            ];
        });

        // Segarkan data cache secara real-time agar admin & papan menerima pembaruan barunya
        $this->updateAntrianCache();

        return response()->json([
            'success'    => true,
            'idantrian'  => $dataBaru['idantrian'],
            'nomor'      => $dataBaru['nomor'],
            'nama'       => $request->nama,
            'nama_poli'  => $dataBaru['nama_poli'],
            'tiket_url'  => route('antrian.tiket', $dataBaru['idantrian'])
        ]);
    }
    // public function guestDaftar(Request $request)
    // {
    //     $request->validate([
    //         'nama'   => 'required|string|max:150',
    //         'idpoli' => 'required|integer|exists:poli,idpoli'
    //     ]);

    //     $idBaru = DB::table('antrian')->insertGetId([
    //         'nama'   => $request->nama,
    //         'idpoli' => $request->idpoli,
    //         'status' => 'menunggu'
    //     ], 'idantrian');

    //     $this->updateAntrianCache();

    //     $dataAntrian = DB::table('antrian')
    //         ->join('poli', 'antrian.idpoli', '=', 'poli.idpoli')
    //         ->where('antrian.idantrian', $idBaru)
    //         ->select('antrian.*', 'poli.nama_poli')
    //         ->first();

    //     return response()->json([
    //         'success'   => true,
    //         'idantrian' => $idBaru,
    //         'nomor'     => $dataAntrian->nomor,
    //         'nama'      => $dataAntrian->nama,
    //         'nama_poli' => $dataAntrian->nama_poli
    //     ]);
    // }

    // =========================================================================
    // 2. PANEL MANAGEMENT CONTROL OPERATOR LOKET
    // =========================================================================
    public function adminIndex()
    {
        $daftarPoli = DB::table('poli')->whereNull('deleted_at')->get();
        return view('antrian.admin', compact('daftarPoli'));
    }

    public function adminPanggil(Request $request)
    {
        $hariIni = date('Y-m-d');

        if ($request->has('idantrian') && $request->idantrian != null) {
            DB::table('antrian')
                ->where('status', 'dipanggil')
                ->whereDate('created_at', $hariIni)
                ->update(['status' => 'selesai', 'waktu_selesai' => now(), 'updated_at' => now()]);

            DB::table('antrian')
                ->where('idantrian', $request->idantrian)
                ->update(['status' => 'dipanggil', 'waktu_panggil' => now(), 'updated_at' => now()]);

            $this->updateAntrianCache();
            return response()->json(['success' => true, 'message' => 'Berhasil memanggil pasien terpilih.']);
        }

        $query = DB::table('antrian')
            ->where('status', 'menunggu')
            ->whereDate('created_at', $hariIni)
            ->whereNull('deleted_at');

        if ($request->filled('kode_poli')) {
            $query->whereIn('idpoli', function ($q) use ($request) {
                $q->select('idpoli')->from('poli')->where('kode_poli', $request->kode_poli);
            });
        }

        $berikutnya = $query->orderBy('idantrian', 'asc')->first();

        if (!$berikutnya) {
            return response()->json(['success' => false, 'message' => 'Antrian tunggu hari ini sudah kosong.'], 404);
        }

        DB::table('antrian')
            ->where('status', 'dipanggil')
            ->whereDate('created_at', $hariIni)
            ->update(['status' => 'selesai', 'waktu_selesai' => now(), 'updated_at' => now()]);

        DB::table('antrian')
            ->where('idantrian', $berikutnya->idantrian)
            ->update(['status' => 'dipanggil', 'waktu_panggil' => now(), 'updated_at' => now()]);

        $this->updateAntrianCache();
        return response()->json(['success' => true, 'message' => 'Memanggil antrian berikutnya.']);
    }

    public function adminLewatkan(Request $request)
    {
        $request->validate(['idantrian' => 'required|integer']);

        DB::table('antrian')->where('idantrian', $request->idantrian)->update([
            'status'     => 'terlewat',
            'updated_at' => now()
        ]);

        $this->updateAntrianCache();
        return response()->json(['success' => true, 'message' => 'Pasien berhasil dilewatkan.']);
    }

    public function adminPanggilTerlewat(Request $request)
    {
        $request->validate(['idantrian' => 'required|integer']);
        $hariIni = date('Y-m-d');

        DB::table('antrian')
            ->where('status', 'dipanggil')
            ->whereDate('created_at', $hariIni)
            ->update(['status' => 'selesai', 'waktu_selesai' => now(), 'updated_at' => now()]);

        DB::table('antrian')->where('idantrian', $request->idantrian)->update([
            'status'        => 'dipanggil',
            'waktu_panggil' => now(),
            'updated_at'    => now()
        ]);

        $this->updateAntrianCache();
        return response()->json(['success' => true, 'message' => 'Memanggil ulang pasien terlewat.']);
    }

    // =========================================================================
    // 3. CACHE DATA ENDPOINT UNTUK ADMIN (POLLING)
    // =========================================================================
    public function adminGetData()
    {
        $state = Cache::get('antrian_state');

        if (!$state) {
            $this->updateAntrianCache();
            $state = Cache::get('antrian_state');
        }

        return response()->json($state);
    }

    // =========================================================================
    // 3. HALAMAN TIKET PRIBADI GUEST (Tab baru setelah daftar)
    // =========================================================================
    public function tiket($id)
    {
        $antrian = DB::table('antrian')
            ->join('poli', 'antrian.idpoli', '=', 'poli.idpoli')
            ->where('antrian.idantrian', $id)
            ->select('antrian.*', 'poli.nama_poli', 'poli.kode_poli')
            ->first();

        if (!$antrian) {
            abort(404, 'Antrian tidak ditemukan');
        }

        return view('antrian.tiket', compact('antrian'));
    }

    public function papanIndex()
    {
        return view('antrian.papan');
    }

    // =========================================================================
    // 4. SSE STREAM ENGINE SINKRONISASI DATA CACHE
    // =========================================================================
    public function streamAntrian()
    {
        set_time_limit(0);

        if (function_exists('apache_setenv')) {
            @apache_setenv('no-gzip', 1);
        }
        @ini_set('zlib.output_compression', 0);
        @ini_set('implicit_flush', 1);

        return response()->stream(function () {
            $lastHash = '';

            while (ob_get_level() > 0) {
                @ob_end_clean();
            }

            if (session_id()) {
                session_write_close();
            }

            $this->updateAntrianCache();

            while (true) {
                $state = Cache::get('antrian_state', [
                    'daftar_tunggu'    => [],
                    'sedang_dipanggil' => null,
                    'terlewat'         => [],
                    'hari_lain'        => []
                ]);

                $currentHash = md5(json_encode($state));

                if ($currentHash !== $lastHash) {
                    echo "event: queue-update\n";
                    echo "data: " . json_encode($state) . "\n\n";
                    $lastHash = $currentHash;
                }

                echo ": keep-alive\n\n";

                if (connection_aborted()) {
                    break;
                }

                @ob_flush();
                @flush();
                sleep(1); // Interval cek data setiap 1 detik
            }
        }, 200, [
            'Content-Type'      => 'text/event-stream',
            'Cache-Control'     => 'no-cache, no-store, must-revalidate',
            'Connection'        => 'keep-alive',
            'X-Accel-Buffering' => 'no', // Menghindari buffering pada Nginx
        ]);
    }

    public function stream(?Request $request = null)
    {
        return $this->streamAntrian();
    }
}
