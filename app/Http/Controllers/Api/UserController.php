<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User; // Pastikan model User Anda sudah ada dan sesuai
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Contract\Auth as FirebaseAuth; // Jika menggunakan kreait/laravel-firebase
use Illuminate\Validation\Rule; // Untuk validasi unik dengan kondisi

class UserController extends Controller
{
    protected $firebaseAuth;

    // Inject FirebaseAuth jika menggunakan kreait/laravel-firebase
    public function __construct(FirebaseAuth $firebaseAuth)
    {
        $this->firebaseAuth = $firebaseAuth;
    }

    /**
     * Sinkronkan pengguna Firebase dengan database lokal atau kembalikan data pengguna saat ini.
     * Endpoint ini akan dipanggil setelah login/registrasi di sisi klien.
     * Idealnya dipanggil dengan metode POST jika ada data yang mungkin dikirim dari klien (misal jenis_listrik awal).
     * Atau GET /api/me jika hanya untuk mengambil data user yang sudah login.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function syncOrGetCurrentUser(Request $request)
    {
        // firebase_uid sudah diambil dan diverifikasi oleh middleware VerifyFirebaseToken
        $firebaseUid = $request->attributes->get('firebase_uid');

        if (!$firebaseUid) {
            return response()->json(['message' => 'Token Firebase tidak valid atau UID tidak ditemukan.'], 401);
        }

        try {
            // Dapatkan detail user terbaru dari Firebase
            $firebaseUserInfo = $this->firebaseAuth->getUser($firebaseUid);
            $email = $firebaseUserInfo->email;
            $name = $firebaseUserInfo->displayName ?? 'Voltix User';
            $photoUrl = $firebaseUserInfo->photoUrl;
            $emailVerified = $firebaseUserInfo->emailVerified;

            // Data tambahan yang mungkin dikirim dari klien saat sinkronisasi pertama kali
            $jenisListrik = $request->input('jenis_listrik'); // Ambil dari request jika ada

            // Cari atau buat pengguna di database lokal Anda
            // Jika user_id (PK) di tabel users adalah auto-increment, dan firebase_uid adalah kolom terpisah
            $user = User::updateOrCreate(
                ['firebase_uid' => $firebaseUid], // Kondisi untuk mencari
                [                                   // Nilai untuk di-create atau di-update
                    'name' => $name,
                    'email' => $email,
                    'email_verified_at' => $emailVerified ? now() : null,
                    'foto_profil' => $photoUrl,
                    // Hanya update jenis_listrik jika dikirim DAN belum ada, atau jika memang ingin diupdate
                    // Jika jenis_listrik hanya diisi sekali saat onboarding/awal, logika ini perlu disesuaikan.
                ]
            );

            // Jika jenis_listrik dikirim dan user baru dibuat atau jenis_listrik sebelumnya null
            if ($request->filled('jenis_listrik') && ($user->wasRecentlyCreated || is_null($user->jenis_listrik))) {
                $user->jenis_listrik = $jenisListrik;
                $user->save();
            }
            
            // Muat ulang data user untuk memastikan semua perubahan terambil
            $user->refresh();

            return response()->json([
                'message' => 'User synchronized successfully.',
                'user' => $user // Kembalikan data user dari database Anda
            ], 200);

        } catch (\Kreait\Firebase\Exception\FirebaseException $e) {
            Log::error('Firebase error during user sync: ' . $e->getMessage(), ['firebase_uid' => $firebaseUid]);
            return response()->json(['message' => 'Gagal mendapatkan detail pengguna dari Firebase.', 'error' => $e->getMessage()], 500);
        } catch (\Exception $e) {
            Log::error('General error during user sync: ' . $e->getMessage(), ['firebase_uid' => $firebaseUid]);
            return response()->json(['message' => 'Terjadi kesalahan pada server.', 'error' => $e->getMessage()], 500);
        }
    }
}
