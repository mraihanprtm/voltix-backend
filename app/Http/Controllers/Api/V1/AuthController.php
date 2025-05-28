<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Kreait\Firebase\Contract\Auth as FirebaseAuthContract;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException; // Pastikan ini di-import

class AuthController extends Controller
{
    protected FirebaseAuthContract $firebaseAuth;

    public function __construct(FirebaseAuthContract $firebaseAuth)
    {
        $this->firebaseAuth = $firebaseAuth;
    }

    public function handleFirebaseLoginOrRegister(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'firebase_id_token' => 'required|string',
            'name' => 'sometimes|nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validation failed.', 'errors' => $validator->errors()], 422);
        }

        $firebaseToken = $request->input('firebase_id_token');
        $providedNameOnRegister = $request->input('name');

        try {
            $verifiedIdToken = $this->firebaseAuth->verifyIdToken($firebaseToken, true); // check LBER
            
            $firebaseUid = $verifiedIdToken->claims()->get('sub');
            $email = $verifiedIdToken->claims()->get('email');
            $firebaseProvidedName = $verifiedIdToken->claims()->get('name');
            $photoUrl = $verifiedIdToken->claims()->get('picture');
            $emailVerified = $verifiedIdToken->claims()->get('email_verified', false);

            $finalName = $providedNameOnRegister ?: ($firebaseProvidedName ?: explode('@', $email)[0]);

            $user = User::updateOrCreate(
                ['firebase_uid' => $firebaseUid],
                [ // Data yang diupdate jika user sudah ada, atau untuk user baru
                    'email' => $email,
                    'name' => $finalName, // $finalName dari form Android atau profil Firebase
                    'foto_profil' => $photoUrl,
                    'email_verified_at' => $emailVerified ? now() : null,
                ]
            );

            // Hanya set default untuk jenis_listrik dan is_prabayar JIKA USER BARU DIBUAT
            if ($user->wasRecentlyCreated) {
                $user->jenis_listrik = $user->jenis_listrik ?? 2200; // Default jika belum ada
                $user->is_prabayar = $user->is_prabayar ?? false;   // Default jika belum ada
                $user->save();
            }

            $apiToken = $user->createToken('voltix-api-token-' . $user->firebase_uid)->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'User processed successfully.',
                'api_token' => $apiToken,
                'user' => $user,
            ], 200);

        } catch (ValidationException $e) { // Tangani ValidationException secara spesifik
            return response()->json(['success' => false, 'message' => 'Validation error.', 'errors' => $e->errors()], 422);
        } catch (\Kreait\Firebase\Exception\Auth\FailedToVerifyToken $e) {
            Log::error('Firebase Token Verification Failed: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Invalid Firebase token. Please re-authenticate.'], 401);
        } catch (\Throwable $e) { // Tangkap Throwable untuk error yang lebih luas
            Log::error('Error in Firebase Login/Register: ' . $e->getMessage() . ' Stack: ' . $e->getTraceAsString());
            return response()->json(['success' => false, 'message' => 'An internal server error occurred: '. $e->getMessage()], 500);
        }
    }
}