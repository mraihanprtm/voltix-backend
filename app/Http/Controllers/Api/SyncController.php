<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Perangkat;
use App\Models\Ruangan;
use App\Models\Lampu;
use Carbon\Carbon;

class SyncController extends Controller
{
    /**
     * Handles the /sync endpoint to send data changes to the client.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function syncData(Request $request)
    {
        // 1. Validate the incoming request
        $validated = $request->validate([
            'lastSyncTimestamp' => 'required|numeric', // Expected in milliseconds
            'deviceId' => 'required|string',         // deviceId for logging or future use
        ]);

        // Convert lastSyncTimestamp from milliseconds (client) to seconds for Carbon
        $lastSyncTimestampSeconds = $validated['lastSyncTimestamp'] / 1000;
        $lastSyncCarbon = Carbon::createFromTimestamp($lastSyncTimestampSeconds);

        // Get the authenticated user's ID
        // $userId = Auth::id();
        $userId = "CUQHiYo3yvhbsAFK8fi16atLmwB3";

        // Ensure user is authenticated
        if (!$userId) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthenticated.',
                'data' => null
            ], 401);
        }

        // 2. Fetch data modified after the lastSyncTimestamp for the authenticated user
        // withTrashed() is used to include items that might have been soft-deleted after the last sync.
        // The 'isDeleted' attribute on the models will inform the client.
        // Eloquent models have $casts to ensure IDs are integers for Kotlin.

        $perangkats = Perangkat::withTrashed()
            ->where('user_id', $userId)
            ->where('updated_at', '>', $lastSyncCarbon)
            ->get();

        $ruangans = Ruangan::withTrashed()
            ->where('user_id', $userId)
            ->where('updated_at', '>', $lastSyncCarbon)
            ->get();

        // Lampus are fetched if they were updated and belong to one of the user's perangkats.
        $lampus = Lampu::withTrashed()
            ->whereHas('perangkat', function ($query) use ($userId) {
                $query->where('user_id', $userId); // Ensures the parent perangkat belongs to the user
            })
            ->where('updated_at', '>', $lastSyncCarbon)
            ->get();

        // Fetch RuanganPerangkatCrossRef (many-to-many relationships)
        // This fetches relationship entries from the pivot table that were themselves updated.
        $ruanganPerangkatCrossRefs = DB::table('ruangan_perangkat')
            ->join('ruangan', 'ruangan_perangkat.ruangan_id', '=', 'ruangan.id')
            ->join('perangkat', 'ruangan_perangkat.perangkat_id', '=', 'perangkat.id')
            ->where('ruangan.user_id', $userId) // Scope by user through ruangan
            ->where('perangkat.user_id', $userId) // Scope by user through perangkat
            ->where('ruangan_perangkat.updated_at', '>', $lastSyncCarbon)
            ->select(
                'ruangan_perangkat.ruangan_id as ruanganId',
                'ruangan_perangkat.perangkat_id as perangkatId',
                'ruangan_perangkat.waktu_nyala as waktuNyala',
                'ruangan_perangkat.waktu_mati as waktuMati'
            )
            ->get()
            ->map(function ($item) { // Ensure IDs are integers as per Kotlin data class
                return [
                    'ruanganId' => (int)$item->ruanganId,
                    'perangkatId' => (int)$item->perangkatId,
                    'waktuNyala' => $item->waktuNyala,
                    'waktuMati' => $item->waktuMati
                ];
            });

        // Current server time in milliseconds for the new sync timestamp
        $newSyncTimestamp = Carbon::now()->getTimestamp() * 1000;

        // 3. Construct the SyncResponse data structure
        $syncResponseData = [
            'lastSyncTimestamp' => $newSyncTimestamp,
            'perangkat' => $perangkats,
            'ruangan' => $ruangans,
            'lampu' => $lampus,
            'ruanganPerangkat' => $ruanganPerangkatCrossRefs,
        ];

        // Return the successful response
        return response()->json([
            'status' => 'success',
            'data' => $syncResponseData,
            'message' => 'Data synced successfully.'
        ]);
    }

    // You would implement pushChanges here later
    // public function pushChanges(Request $request) { ... }
}
