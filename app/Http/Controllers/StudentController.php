<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Student;
use App\Models\NfcCard;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon; // Digunakan untuk memanipulasi zona waktu runtime

class StudentController extends Controller
{
    const STUDENT_ROLE_ID = 6;

    public function index()
    {
        $students = Student::with(['user', 'nfcCard'])->get();
        return view('admin.student.student', compact('students'));
    }

    public function create()
    {
        return view('admin.student.create-student');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'email'         => 'required|string|email|max:255|unique:users,email',
            'password'      => 'required|string|min:8|confirmed',
            'NIM'           => 'required|string|max:20|unique:students,nim',
            'fakultas'      => 'required|string|max:255',
            'prodi'         => 'required|string|max:255',
            'serial_number' => 'required|string|unique:nfc_cards,serial_number',
        ]);

        try {
            DB::beginTransaction();

            $nowWib = Carbon::now('Asia/Jakarta');

            $user = User::create([
                'nama_user'  => $validated['name'],
                'email'      => $validated['email'],
                'password'   => Hash::make($validated['password']),
                'idrole'     => self::STUDENT_ROLE_ID, 
                'role_id'    => self::STUDENT_ROLE_ID, 
                'created_at' => $nowWib,
                'updated_at' => $nowWib,
            ]);

            $student = Student::create([
                'iduser'     => $user->iduser,
                'nim'        => $validated['NIM'],
                'fakultas'   => $validated['fakultas'],
                'prodi'      => $validated['prodi'],
                'created_at' => $nowWib,
                'updated_at' => $nowWib,
            ]);

            NfcCard::create([
                'idstudent'     => $student->idstudent,
                'serial_number' => $validated['serial_number'],
                'created_at'    => $nowWib,
                'updated_at'    => $nowWib,
            ]);

            DB::commit();

            session()->flash('success', 'Student created successfully!');

            return response()->json([
                'success'  => true,
                'redirect' => route('admin.student.index'),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create student: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan data ke database server: ' . $e->getMessage()
            ], 500);
        }
    }

    public function edit($id)
    {
        $student = Student::with(['user', 'nfcCard'])->findOrFail($id);
        return view('admin.student.edit-student', compact('student'));
    }

    public function update(Request $request, $id)
    {
        $student = Student::findOrFail($id);

        $validated = $request->validate([
            'NIM'      => 'required|string|max:20|unique:students,nim,' . $student->idstudent . ',idstudent',
            'fakultas' => 'required|string|max:255',
            'prodi'    => 'required|string|max:255',
        ]);

        $student->update([
            'nim'        => $validated['NIM'],
            'fakultas'   => $validated['fakultas'],
            'prodi'      => $validated['prodi'],
            'updated_at' => Carbon::now('Asia/Jakarta'),
        ]);

        session()->flash('success', 'Student updated successfully!');

        return response()->json([
            'success'  => true,
            'redirect' => route('admin.student.index'),
        ]);
    }

    public function delete($id)
    {
        try {
            DB::beginTransaction();

            $student = Student::findOrFail($id);
            $userId  = $student->iduser;

            $student->delete();
            User::where('iduser', $userId)->delete();

            DB::commit();

            return redirect()->route('admin.student.index')->with('success', 'Student deleted successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete student: ' . $e->getMessage());

            return redirect()->route('admin.student.index')->with('error', 'Failed to delete student. Please try again.');
        }
    }
}