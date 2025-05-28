<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ruangan;
use App\Models\User; // Import User model
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule; // Untuk validasi Enum jika Anda menggunakan Enum PHP

class RuanganController extends Controller
{
    /**
     * Display a listing of the resource.
     * GET /api/ruangan
     */
    public function index(Request $request)
    {
        $firebaseUid = $request->attributes->get('firebase_uid');
        // Ambil user_id (primary key dari tabel users) berdasarkan firebase_uid
        // Ini jika Anda menggunakan user_id (PK) sebagai foreign key di tabel ruangan
        // $user = User::where('firebase_uid', $firebaseUid)->firstOrFail();
        // $ruangan = Ruangan::where('user_id', $user->id)->orderBy('created_at', 'desc')->get();

        // Jika Anda menyimpan firebase_uid langsung di kolom user_id tabel ruangan:
        $ruangan = Ruangan::where('user_id', $firebaseUid)->orderBy('created_at', 'desc')->get();


        return response()->json($ruangan);
    }

    /**
     * Store a newly created resource in storage.
     * POST /api/ruangan
     */
    public function store(Request $request)
    {
        $firebaseUid = $request->attributes->get('firebase_uid');
        // $user = User::where('firebase_uid', $firebaseUid)->firstOrFail();

        $validator = Validator::make($request->all(), [
            'nama_ruangan' => 'required|string|max:255',
            'panjang_ruangan' => 'required|numeric|min:0',
            'lebar_ruangan' => 'required|numeric|min:0',
            'jenis_ruangan' => [
                'required',
                'string',
                // Jika Anda menggunakan Enum PHP: Rule::enum(\App\Enums\JenisRuanganEnum::class)
                // Atau validasi manual terhadap nilai enum string Anda
                Rule::in(['KamarTidur', 'RuangTamu', 'Dapur', 'KamarMandi', 'Lainnya']),
            ],
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $ruangan = Ruangan::create([
            // 'user_id' => $user->id, // Jika menggunakan PK user
            'user_id' => $firebaseUid, // Jika menyimpan firebase_uid langsung
            'nama_ruangan' => $request->nama_ruangan,
            'panjang_ruangan' => $request->panjang_ruangan,
            'lebar_ruangan' => $request->lebar_ruangan,
            'jenis_ruangan' => $request->jenis_ruangan,
        ]);

        return response()->json($ruangan, 201);
    }

    /**
     * Display the specified resource.
     * GET /api/ruangan/{ruangan}
     */
    public function show(Request $request, Ruangan $ruangan) // Route Model Binding
    {
        $firebaseUid = $request->attributes->get('firebase_uid');
        // $user = User::where('firebase_uid', $firebaseUid)->firstOrFail();

        // Pastikan ruangan ini milik pengguna yang terautentikasi
        // if ($ruangan->user_id !== $user->id) { // Jika menggunakan PK user
        if ($ruangan->user_id !== $firebaseUid) { // Jika menyimpan firebase_uid langsung
            return response()->json(['message' => 'Akses ditolak.'], 403);
        }

        return response()->json($ruangan);
    }

    /**
     * Update the specified resource in storage.
     * PUT /api/ruangan/{ruangan}
     */
    public function update(Request $request, Ruangan $ruangan)
    {
        $firebaseUid = $request->attributes->get('firebase_uid');
        // $user = User::where('firebase_uid', $firebaseUid)->firstOrFail();

        // if ($ruangan->user_id !== $user->id) { // Jika menggunakan PK user
        if ($ruangan->user_id !== $firebaseUid) { // Jika menyimpan firebase_uid langsung
            return response()->json(['message' => 'Akses ditolak.'], 403);
        }

        $validator = Validator::make($request->all(), [
            'nama_ruangan' => 'sometimes|required|string|max:255',
            'panjang_ruangan' => 'sometimes|required|numeric|min:0',
            'lebar_ruangan' => 'sometimes|required|numeric|min:0',
            'jenis_ruangan' => [
                'sometimes',
                'required',
                'string',
                Rule::in(['KamarTidur', 'RuangTamu', 'Dapur', 'KamarMandi', 'Lainnya']),
            ],
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $ruangan->update($request->only(['nama_ruangan', 'panjang_ruangan', 'lebar_ruangan', 'jenis_ruangan']));

        return response()->json($ruangan);
    }

    /**
     * Remove the specified resource from storage.
     * DELETE /api/ruangan/{ruangan}
     */
    public function destroy(Request $request, Ruangan $ruangan)
    {
        $firebaseUid = $request->attributes->get('firebase_uid');
        // $user = User::where('firebase_uid', $firebaseUid)->firstOrFail();

        // if ($ruangan->user_id !== $user->id) { // Jika menggunakan PK user
        if ($ruangan->user_id !== $firebaseUid) { // Jika menyimpan firebase_uid langsung
            return response()->json(['message' => 'Akses ditolak.'], 403);
        }

        $ruangan->delete();

        return response()->json(null, 204);
    }
}
