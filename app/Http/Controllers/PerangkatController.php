<?php

namespace App\Http\Controllers;

use App\Models\Perangkat;
use Illuminate\Http\Request;
use App\Models\Lampu; // Jika Anda menggunakan model Lampu
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule; // Untuk validasi enum jika diperlukan
use App\Models\Ruangan; // Jika Anda menggunakan model Ruangan

class PerangkatController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Ambil Firebase UID dari user yang sedang login
        // $firebaseUid = auth()->user()->firebase_uid ?? null;
        $firebaseUid = "CUQHiYo3yvhbsAFK8fi16atLmwB3"; // Contoh Firebase UID, ganti dengan yang sesuai

        // Error handling jika tidak ada Firebase UID
        if (!$firebaseUid) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Ambil semua perangkat milik user
        $perangkat = Perangkat::where('user_id', $firebaseUid)->get();

        return response()->json($perangkat);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Ambil Firebase UID dari user yang sedang login
        // $firebaseUid = auth()->user()->firebase_uid ?? null;
        $firebaseUid = "CUQHiYo3yvhbsAFK8fi16atLmwB3";

        // Error handling jika tidak ada Firebase UID
        if (!$firebaseUid) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Validasi input
        $request->validate([
            'nama' => 'required|string|max:255',
            'jumlah' => 'required|integer|min:1',
            'daya' => 'required|integer|min:0',
            'jenis' => 'required|string|max:50', // Misalnya, jika jenis perangkat adalah string
        ]);

        // Buat perangkat baru
        $perangkat = Perangkat::create([
            'user_id' => $firebaseUid,
            'nama' => $request->nama,
            'jumlah' => $request->jumlah,
            'daya' => $request->daya,
            'jenis' => $request->jenis, // Misalnya, jika jenis perangkat adalah string
        ]);

        // if jenis perangkat adalah lampu, buat relasi dengan tabel lampu
        if ($request->jenis === 'Lampu') {
            // Validasi tambahan untuk lampu
            $request->validate([
                'lumen' => 'required|integer|min:0',
                'jenisLampu' => [
                    'required',
                    'string',
                    Rule::in(['Neon', 'LED']), // Misalnya, jika jenis lampu adalah enum
                ],
            ]);

            // Buat detail lampu
            $lampu = Lampu::create([
                'perangkat_id' => $perangkat->id,
                'jenis' => $request->jenisLampu, // Misalnya, jika jenis lampu adalah string
                'lumen' => $request->lumen,
            ]);
        }

        // Jika Anda ingin mengembalikan detail lampu bersama perangkat
        $perangkat->load('lampuDetail'); // Jika Anda memiliki relasi 'lampu' di model Perangkat
        // Kembalikan perangkat yang baru dibuat    
        return response()->json($perangkat, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Perangkat $perangkat)
    {
        // Pastikan user yang sedang login adalah pemilik perangkat
        // if ($perangkat->user_id !== auth()->user()->firebase_uid) {
        //     return response()->json(['error' => 'Unauthorized'], 403);
        // }

        // Ambil detail perangkat, termasuk relasi dengan lampu jika ada
        $perangkat->load('lampuDetail'); // Jika Anda memiliki relasi 'lampu' di model Perangkat

        return response()->json($perangkat);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Perangkat $perangkat)
    {
        // Pastikan user yang sedang login adalah pemilik perangkat
        // if ($perangkat->user_id !== auth()->user()->firebase_uid) {
        //     return response()->json(['error' => 'Unauthorized'], 403);
        // }

        // Validasi input
        $request->validate([
            'nama' => 'sometimes|required|string|max:255',
            'jumlah' => 'sometimes|required|integer|min:1',
            'daya' => 'sometimes|required|integer|min:0',
            'jenis' => 'sometimes|required|string|max:50', // Misalnya, jika jenis perangkat adalah string
        ]);

        // Update perangkat
        $perangkat->update($request->only(['nama', 'jumlah', 'daya', 'jenis']));

        // Jika jenis perangkat adalah lampu, update relasi dengan tabel lampu
        if ($perangkat->jenis === 'Lampu') {
            $request->validate([
                'lumen' => 'sometimes|required|integer|min:0',
                'jenisLampu' => [
                    'sometimes',
                    'required',
                    'string',
                    Rule::in(['Neon', 'LED']), // Misalnya, jika jenis lampu adalah enum
                ],
            ]);

            // Update detail lampu jika ada
            $lampu = $perangkat->lampu; // Ambil relasi lampu jika ada
            if ($lampu) {
                $lampu->update([
                    'jenis' => $request->jenisLampu,
                    'lumen' => $request->lumen,
                ]);
            }
        }

        return response()->json($perangkat);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Perangkat $perangkat)
    {
        // Pastikan user yang sedang login adalah pemilik perangkat
        // if ($perangkat->user_id !== auth()->user()->firebase_uid) {
        //     return response()->json(['error' => 'Unauthorized'], 403);
        // }

        // Hapus perangkat
        $perangkat->delete();

        // Jika ada relasi dengan lampu, hapus juga
        if ($perangkat->lampu) {
            $perangkat->lampu->delete();
        }

        return response()->json(['message' => 'Perangkat deleted successfully']);
    }

    public function getPerangkatByRuangan($ruanganId)
    {
        // Ambil perangkat yang terkait dengan ruangan tertentu
        $perangkat = Perangkat::whereHas('ruangan', function ($query) use ($ruanganId) {
            $query->where('ruangan_id', $ruanganId);
        })->get();
        return response()->json($perangkat);
    }

    // tambahkan fungsi untuk menyimpan perangkat ke ruangan
    public function attachPerangkatToRuangan(Request $request, Perangkat $perangkat)
    {
        // Validasi input
        $request->validate([
            'ruangan_id' => 'required|exists:ruangan,id', // Pastikan ruangan_id valid
            'waktu_nyala' => 'nullable|date_format:H:i', // Format waktu nyala
            'waktu_mati' => 'nullable|date_format:H:i', // Format waktu mati
        ]);

        // Attach perangkat ke ruangan dengan waktu nyala dan mati jika ada
        $perangkat->ruangan()->attach($request->ruangan_id, [
            'waktu_nyala' => $request->waktu_nyala,
            'waktu_mati' => $request->waktu_mati,
        ]);

        return response()->json(['message' => 'Perangkat attached to Ruangan successfully']);
    }
}
