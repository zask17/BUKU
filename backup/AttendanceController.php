<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\NfcCard;
use App\Models\Attendance;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function scanNfc(Request $request)
    {
        $request->validate([
            'serial_number' => 'required|string',
        ]);

        // 1. Cari tahu apakah Serial Number NFC terdaftar di DB
        $nfcCard = NfcCard::with('student.user')->where('serial_number', $request->serial_number)->first();

        if (!$nfcCard) {
            return response()->json([
                'success' => false,
                'message' => 'Kartu NFC tidak dikenali atau belum terdaftar!'
            ], 404);
        }

        $student = $nfcCard->student;

        // 2. Cek apakah mahasiswa sudah absen dalam jangka waktu tertentu (misal hari ini) untuk menghindari double scan
        $alreadyCheckedIn = Attendance::where('idstudent', $student->idstudent)
            ->whereDate('scan_time', Carbon::today())
            ->exists();

        if ($alreadyCheckedIn) {
            return response()->json([
                'success' => false,
                'message' => 'Mahasiswa bernama ' . $student->user->nama_user . ' sudah melakukan absensi hari ini.'
            ], 400);
        }

        // 3. Simpan data absensi ke database
        $attendance = Attendance::create([
            'idstudent' => $student->idstudent,
            'scan_time' => Carbon::now(),
            'status' => 'hadir'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Absensi berhasil dicatat!',
            'data' => [
                'nama' => $student->user->nama_user,
                'nim' => $student->nim,
                'prodi' => $student->prodi,
                'waktu' => $attendance->scan_time->toDateTimeString()
            ]
        ], 200);
    }
}