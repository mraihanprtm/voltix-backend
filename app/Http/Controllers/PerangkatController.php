<?php

namespace App\Http\Controllers;

use App\Models\Perangkat;
use Illuminate\Http\Request;
use App\Models\Lampu; // Jika Anda menggunakan model Lampu
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule; // Untuk validasi enum jika diperlukan
use App\Models\Ruangan; // Jika Anda menggunakan model Ruangan
use Carbon\Carbon;
use App\Http\Resources\PerangkatResource;
use Illuminate\Support\Str;

class PerangkatController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $firebaseUid = "CUQHiYo3yvhbsAFK8fi16atLmwB3";
        if (!$firebaseUid) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $perangkat = Perangkat::where('user_id', 'like', $firebaseUid)->get();

        // CHANGED: Use an API Resource for consistent output
        return PerangkatResource::collection($perangkat);
    }

    public function store(Request $request)
    {
        $firebaseUid = "CUQHiYo3yvhbsAFK8fi16atLmwB3";
        if (!$firebaseUid) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // CHANGED: Validation now uses snake_case for consistency
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'jumlah' => 'required|integer|min:1',
            'daya' => 'required|integer|min:0',
            'jenis' => 'required|string|max:50',
            'uuid' => 'nullable|uuid', // UUID is optional, we can generate it
        ]);

        // CHANGED: Automatically generate a UUID if one isn't provided
        $validated['uuid'] = $validated['uuid'] ?? Str::uuid()->toString();
        $validated['user_id'] = $firebaseUid;

        $perangkat = Perangkat::create($validated);

        // This logic remains, but could be refactored into its own LampuController later
        if ($validated['jenis'] === 'Lampu') {
            $lampuValidated = $request->validate([
                'lumen' => 'required|integer|min:0',
                'jenis_lampu' => ['required', 'string', Rule::in(['Neon', 'LED'])],
                'lampu_uuid' => 'nullable|uuid' // Expect a UUID for the lamp too
            ]);

            Lampu::create([
                'perangkat_id' => $perangkat->id,
                'jenis' => $lampuValidated['jenis_lampu'],
                'lumen' => $lampuValidated['lumen'],
                'uuid' => $lampuValidated['lampu_uuid'] ?? Str::uuid()->toString(),
            ]);
        }

        // CHANGED: Use the API Resource for the response
        return new PerangkatResource($perangkat->load('lampuDetail'));
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
        return new PerangkatResource($perangkat->load('lampuDetail'));
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
        $validated = $request->validate([
            'nama' => 'sometimes|required|string|max:255',
            'jumlah' => 'sometimes|required|integer|min:1',
            'daya' => 'sometimes|required|integer|min:0',
            'jenis' => 'sometimes|required|string|max:50',
        ]);

        $perangkat->update($validated);

        if ($perangkat->jenis === 'Lampu' && $request->hasAny(['lumen', 'jenis_lampu'])) {
            $lampuValidated = $request->validate([
                 'lumen' => 'sometimes|required|integer|min:0',
                 'jenis_lampu' => ['sometimes','required', 'string', Rule::in(['Neon', 'LED'])],
            ]);
            if ($perangkat->lampuDetail) {
                $perangkat->lampuDetail->update($lampuValidated);
            }
        }

        // CHANGED: Use the API Resource for the response
        return new PerangkatResource($perangkat->load('lampuDetail'));
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
            'updated_at' => Carbon::now(),
        ]);

        return response()->json(['message' => 'Perangkat attached to Ruangan successfully']);
    }
}
