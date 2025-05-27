<?php

namespace App\Http\Controllers;

use App\Models\Ruangan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Perangkat; // Jika Anda menggunakan model Perangkat

class RuanganController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // $firebaseUid = auth()->user()->firebase_uid; // Ambil Firebase UID dari user yang sedang login
        $firebaseUid = "CUQHiYo3yvhbsAFK8fi16atLmwB3";
        // error handling jika tidak ada Firebase UID
        if (!$firebaseUid) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $ruangan = Ruangan::where('user_id', $firebaseUid)->get(); // Ambil semua ruangan milik user
        return response()->json($ruangan);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //get Firebase UID dari user yang sedang login
        // $firebaseUid = auth()->user()->firebase_uid;
        $firebaseUid = "CUQHiYo3yvhbsAFK8fi16atLmwB3"; // Contoh Firebase UID, ganti dengan yang sesuai
        
        if(!$firebaseUid) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $validator = Validator::make($request->all(), [
            'nama_ruangan' => 'required|string|max:255',
            'panjang_ruangan' => 'required|numeric|min:0',
            'lebar_ruangan' => 'required|numeric|min:0',
            'jenis_ruangan' => 'required|string|max:50', // Misalnya, jika jenis ruangan adalah string
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $ruangan = Ruangan::create([
            'user_id' => $firebaseUid,
            'nama_ruangan' => $request->nama_ruangan,
            'panjang_ruangan' => $request->panjang_ruangan,
            'lebar_ruangan' => $request->lebar_ruangan,
            'jenis_ruangan' => $request->jenis_ruangan, // Misalnya, jika jenis ruangan adalah string
        ]);
        return response()->json($ruangan, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Ruangan $ruangan)
    {
        // Pastikan user yang sedang login adalah pemilik ruangan
        if ($ruangan->user_id !== auth()->user()->firebase_uid) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return response()->json($ruangan);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Ruangan $ruangan)
    {
        // Pastikan user yang sedang login adalah pemilik ruangan
        if ($ruangan->user_id !== auth()->user()->firebase_uid) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validator = Validator::make($request->all(), [
            'nama_ruangan' => 'sometimes|required|string|max:255',
            'panjang_ruangan' => 'sometimes|required|numeric|min:0',
            'lebar_ruangan' => 'sometimes|required|numeric|min:0',
            'jenis_ruangan' => 'sometimes|required|string|max:50', // Misalnya, jika jenis ruangan adalah string
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $ruangan->update($request->only(['nama_ruangan', 'panjang_ruangan', 'lebar_ruangan', 'jenis_ruangan']));
        return response()->json($ruangan);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Ruangan $ruangan)
    {
        // // Pastikan user yang sedang login adalah pemilik ruangan
        // if ($ruangan->user_id !== auth()->user()->firebase_uid) {
        //     return response()->json(['error' => 'Unauthorized'], 403);
        // }

        $ruangan->delete();
        return response()->json(['message' => 'Ruangan deleted successfully']);
    }

    public function getPerangkatByRuangan(Ruangan $ruangan)
    {
        // Pastikan user yang sedang login adalah pemilik ruangan
        // if ($ruangan->user_id !== auth()->user()->firebase_uid) {
        //     return response()->json(['error' => 'Unauthorized'], 403);
        // }

        // Ambil semua perangkat yang terkait dengan ruangan ini
        $perangkat = $ruangan->perangkat; // Menggunakan relasi yang sudah didefinisikan di model Ruangan

        return response()->json($perangkat);
    }
}
