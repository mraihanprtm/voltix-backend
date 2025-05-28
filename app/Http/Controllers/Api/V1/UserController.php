<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'jenis_listrik' => ['sometimes', 'nullable', 'integer', Rule::in([450, 900, 1300, 2200, 3500, 5500, 6600])], // Tambahkan nullable jika boleh dikosongkan
            'foto_profil' => 'sometimes|nullable|string|url',
            'is_prabayar' => 'sometimes|boolean', // <-- TAMBAHKAN VALIDASI UNTUK is_prabayar
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validasi gagal.', 'errors' => $validator->errors()], 422);
        }

        $validatedData = $validator->validated();

        // Update field yang ada di request dan sudah tervalidasi
        if (array_key_exists('name', $validatedData)) { // Cek dengan array_key_exists untuk field opsional
            $user->name = $validatedData['name'];
        }
        if (array_key_exists('jenis_listrik', $validatedData)) {
            $user->jenis_listrik = $validatedData['jenis_listrik'];
        }
        if (array_key_exists('foto_profil', $validatedData)) {
            $user->foto_profil = $validatedData['foto_profil'];
        }
        if (array_key_exists('is_prabayar', $validatedData)) { // <-- TAMBAHKAN LOGIKA UPDATE
            $user->is_prabayar = $validatedData['is_prabayar'];
        }

        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Profil berhasil diperbarui.',
            'user' => $user
        ], 200);
    }
}