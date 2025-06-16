<?php

namespace App\Http\Controllers;

use App\Models\Ruangan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Perangkat; // Jika Anda menggunakan model Perangkat
use Illuminate\Support\Str; // CHANGED: Import Str for UUID generation
use App\Http\Resources\RuanganResource; // CHANGED: Import the API Resource

class RuanganController extends Controller
{
    public function index()
    {
        $firebaseUid = "CUQHiYo3yvhbsAFK8fi16atLmwB3";
        if (!$firebaseUid) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $ruangan = Ruangan::where('user_id', 'like', $firebaseUid)->get();

        // CHANGED: Use an API Resource for consistent output
        return RuanganResource::collection($ruangan);
    }

    public function store(Request $request)
    {
        $firebaseUid = "CUQHiYo3yvhbsAFK8fi16atLmwB3";
        if(!$firebaseUid) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // CHANGED: Validation now uses snake_case and Laravel's built-in validation
        $validated = $request->validate([
            'nama_ruangan' => 'required|string|max:255',
            'panjang_ruangan' => 'required|numeric|min:0',
            'lebar_ruangan' => 'required|numeric|min:0',
            'jenis_ruangan' => 'required|string|max:50',
            'uuid' => 'nullable|uuid',
        ]);

        // CHANGED: Automatically generate a UUID if one isn't provided
        $validated['uuid'] = $validated['uuid'] ?? Str::uuid()->toString();
        $validated['user_id'] = $firebaseUid;

        $ruangan = Ruangan::create($validated);

        // CHANGED: Use the API Resource for the response
        return new RuanganResource($ruangan);
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

        return new RuanganResource($ruangan);
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

        $validated = $request->validate([
            'nama_ruangan' => 'sometimes|required|string|max:255',
            'panjang_ruangan' => 'sometimes|required|numeric|min:0',
            'lebar_ruangan' => 'sometimes|required|numeric|min:0',
            'jenis_ruangan' => 'sometimes|required|string|max:50',
        ]);

        $ruangan->update($validated);

        return new RuanganResource($ruangan);
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
