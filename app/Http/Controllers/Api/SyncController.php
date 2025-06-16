<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\RuanganResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Perangkat;
use App\Models\Ruangan;
use App\Models\Lampu;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Throwable; // Import Throwable for catching exceptions
use App\Http\Resources\LampuResource;
use App\Http\Resources\PerangkatResource;
use Illuminate\Support\Arr;

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
            'deviceId' => 'required|string',           // deviceId for logging or future use
        ]);

        // Convert lastSyncTimestamp from milliseconds (client) to seconds for Carbon
        $lastSyncTimestampSeconds = $validated['lastSyncTimestamp'] / 1000;
        $lastSyncCarbon = Carbon::createFromTimestamp($lastSyncTimestampSeconds);

        // 1. Dapatkan seluruh objek User yang sedang login.
        $user = Auth::user();

        // 2. Deklarasikan variabel untuk menampung firebase_uid.
        $firebaseUid = null;

        // 3. (Penting) Periksa apakah pengguna benar-benar ada sebelum mengakses propertinya.
        if ($user) {
            // 4. Akses properti 'firebase_uid' dari objek user.
            $firebaseUid = $user->firebase_uid;

            // Sekarang Anda bisa menggunakan $firebaseUid di sisa fungsi Anda.
            Log::debug('Firebase UID retrieved:', ['uid' => $firebaseUid]);
        }

        // 5. Periksa jika $firebaseUid masih null (pengguna tidak terautentikasi).
        if (!$firebaseUid) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        // 2. Fetch data modified after the lastSyncTimestamp for the authenticated user
        $perangkats = Perangkat::withTrashed()
            ->where('user_id', $firebaseUid)
            ->where('updated_at', '>', $lastSyncCarbon)
            ->get();

        $ruangans = Ruangan::withTrashed()
            ->where('user_id', $firebaseUid)
            ->where('updated_at', '>', $lastSyncCarbon)
            ->get();

        $lampus = Lampu::withTrashed()
            ->whereHas('perangkat', function ($query) use ($firebaseUid) {
                $query->where('user_id', $firebaseUid);
            })
            ->where('updated_at', '>', $lastSyncCarbon)
            ->get();

        $ruanganPerangkatCrossRefs = DB::table('ruangan_perangkat')
            ->join('ruangan', 'ruangan_perangkat.ruangan_id', '=', 'ruangan.id')
            ->join('perangkat', 'ruangan_perangkat.perangkat_id', '=', 'perangkat.id')
            ->where('ruangan.user_id', $firebaseUid)
            ->where('perangkat.user_id', $firebaseUid)
            ->where('ruangan_perangkat.updated_at', '>', $lastSyncCarbon)
            ->select(
                'ruangan_perangkat.ruangan_id as ruanganId',
                'ruangan_perangkat.perangkat_id as perangkatId',
                'ruangan_perangkat.waktu_nyala as waktuNyala',
                'ruangan_perangkat.waktu_mati as waktuMati'
                // You might also want to select the UUIDs here for the client
            )
            ->get()
            ->map(function ($item) {
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
            // CHANGED: Wrap collections with their respective resources for consistent output
            'perangkat' => PerangkatResource::collection($perangkats),
            'ruangan' => RuanganResource::collection($ruangans),
            'lampu' => LampuResource::collection($lampus),
            'ruanganPerangkat' => $ruanganPerangkatCrossRefs,
        ];

        // Return the successful response
        return response()->json([
            'status' => 'success',
            'data' => $syncResponseData,
            'message' => 'Data synced successfully.'
        ]);
    }

    /**
     * Handles the /push-changes endpoint to receive data changes from the client.
     * This version uses UUIDs as the primary key for synchronization to prevent ID conflicts.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function pushChanges(Request $request)
    {
        Log::debug('Push Request:', $request->all());
        // For production, use Auth::id(). For testing, you can use a hardcoded ID.
        $firebaseUid = Auth::user();
        // $firebaseUid = "CUQHiYo3yvhbsAFK8fi16atLmwB3";
        if (!$firebaseUid) {
            // Use this for local testing if you are not sending an auth token
            return response()->json(['status' => 'error', 'message' => 'Unauthenticated.'], 401);
        }

        // Validate the incoming data, now requiring UUIDs for new/updated records.
        $validated = $request->validate([
            'perangkat' => 'present|array',
            'ruangan' => 'present|array',
            'lampu' => 'present|array',
            'ruanganPerangkat' => 'present|array',
            'deletedIds' => 'present|array',
        ]);

        try {
            $perangkatData = $request->input('perangkat', []);
            $ruanganData = $request->input('ruangan', []);
            $lampuData = $request->input('lampu', []);
            $deletedIds = $request->input('deletedIds', []);
            $ruanganPerangkatData = $request->input('ruanganPerangkat', []);

            $result = DB::transaction(function () use ($perangkatData, $ruanganData, $lampuData, $deletedIds, $firebaseUid, $ruanganPerangkatData) {
                // --- DELETIONS ---
                // Deletions can still use the internal integer ID.
                if (!empty($deletedIds['lampuIds'])) {
                    // Note: A more secure version would check ownership before deleting.
                    Lampu::whereIn('id', $deletedIds['lampuIds'])->delete();
                }
                if (!empty($deletedIds['ruanganIds'])) {
                    Ruangan::where('user_id', $firebaseUid)->whereIn('id', $deletedIds['ruanganIds'])->delete();
                }
                if (!empty($deletedIds['perangkatIds'])) {
                    Perangkat::where('user_id', $firebaseUid)->whereIn('id', $deletedIds['perangkatIds'])->delete();
                }

                // === INSERTS / UPDATES (ORDER IS CRITICAL) ===

                // STEP 1: Process Parent tables first (Perangkat and Ruangan)
                foreach ($perangkatData as $item) {
                    // Use a defensive 'firstOrNew' approach
                    $perangkat = Perangkat::firstOrNew(
                        ['uuid' => $item['uuid'] ?? null, 'user_id' => $firebaseUid]
                    );

                    // Now, only fill the properties that actually exist in the request data.
                    if (isset($item['nama'])) { $perangkat->nama = $item['nama']; }
                    if (isset($item['jumlah'])) { $perangkat->jumlah = $item['jumlah']; }
                    if (isset($item['daya'])) { $perangkat->daya = $item['daya']; }
                    if (isset($item['jenis'])) { $perangkat->jenis = $item['jenis']; }
                    if (isset($item['lastModified'])) {
                        $perangkat->updated_at = Carbon::createFromTimestampMs($item['lastModified']);
                    }

                    $perangkat->save();
                }

                // STEP 2: Process Ruangan
                foreach ($ruanganData as $item) {
                    $ruangan = Ruangan::firstOrNew(
                        ['uuid' => $item['uuid'] ?? null, 'user_id' => $firebaseUid]
                    );
                    if (isset($item['nama_ruangan'])) { $ruangan->nama_ruangan = $item['nama_ruangan']; }
                    if (isset($item['panjang_ruangan'])) { $ruangan->panjang_ruangan = $item['panjang_ruangan']; }
                    if (isset($item['lebar_ruangan'])) { $ruangan->lebar_ruangan = $item['lebar_ruangan']; }
                    if (isset($item['jenis_ruangan'])) { $ruangan->jenis_ruangan = $item['jenis_ruangan']; }
                    if (isset($item['lastModified'])) {
                        $ruangan->updated_at = Carbon::createFromTimestampMs($item['lastModified']);
                    }
                    $ruangan->save();
                }

                // CHANGED: STEP 2 - Build COMPREHENSIVE UUID-to-ID maps
                // Gather all relevant UUIDs from all parts of the payload.
                $perangkatUuidToIdMap = Perangkat::where('user_id', $firebaseUid)->pluck('id', 'uuid');
                $ruanganUuidToIdMap = Ruangan::where('user_id', $firebaseUid)->pluck('id', 'uuid');

                // STEP 3: Process Child table (Lampu)
                foreach ($lampuData as $item) {
                    $perangkatServerId = isset($item['perangkat_uuid']) ? $perangkatUuidToIdMap->get($item['perangkat_uuid']) : null;
                    if ($perangkatServerId) {
                        $lampu = Lampu::firstOrNew(['uuid' => $item['uuid'] ?? null]);
                        $lampu->perangkat_id = $perangkatServerId;
                        if (isset($item['jenis'])) { $lampu->jenis = $item['jenis']; }
                        if (isset($item['lumen'])) { $lampu->lumen = $item['lumen']; }
                        if (isset($item['lastModified'])) {
                            $lampu->updated_at = Carbon::createFromTimestampMs($item['lastModified']);
                        }
                        $lampu->save();
                    }
                }

                // STEP 4: Process Pivot table
                 Log::debug('--- Processing RuanganPerangkat ---');
                foreach ($ruanganPerangkatData as $item) {
                    Log::debug('Processing pivot item:', $item);

                    $ruanganServerId = null;
                    $perangkatServerId = null;

                    // Logic to find Ruangan ID
                    if (!empty($item['ruangan_uuid']) && $ruanganId = $ruanganUuidToIdMap->get($item['ruangan_uuid'])) {
                        $ruanganServerId = $ruanganId;
                        Log::debug("Found Ruangan ID {$ruanganServerId} via UUID '{$item['ruangan_uuid']}'.");
                    } elseif (!empty($item['ruanganId'])) {
                        $ruanganServerId = $item['ruanganId'];
                        Log::debug("Using fallback Ruangan ID {$ruanganServerId} from client payload.");
                    }

                    // Logic to find Perangkat ID
                    if (!empty($item['perangkat_uuid']) && $perangkatId = $perangkatUuidToIdMap->get($item['perangkat_uuid'])) {
                        $perangkatServerId = $perangkatId;
                        Log::debug("Found Perangkat ID {$perangkatServerId} via UUID '{$item['perangkat_uuid']}'.");
                    } elseif (!empty($item['perangkatId'])) {
                        $perangkatServerId = $item['perangkatId'];
                        Log::debug("Using fallback Perangkat ID {$perangkatServerId} from client payload.");
                    }

                    // Final check and insert
                    if ($ruanganServerId && $perangkatServerId) {
                        Log::debug("Link VALID. Inserting/updating link between Ruangan ID {$ruanganServerId} and Perangkat ID {$perangkatServerId}.");
                        DB::table('ruangan_perangkat')->updateOrInsert(
                            ['ruangan_id' => $ruanganServerId, 'perangkat_id' => $perangkatServerId],
                            [
                                'waktu_nyala' => $item['waktuNyala'] ?? null,
                                'waktu_mati' => $item['waktuMati'] ?? null,
                                'updated_at' => Carbon::now(),
                            ]
                        );
                    } else {
                        Log::warning("Link SKIPPED. Could not determine a valid server ID for the link.", ['item' => $item, 'foundRuanganId' => $ruanganServerId, 'foundPerangkatId' => $perangkatServerId]);
                    }
                }

                return true;
            });
        } catch (Throwable $e) {
            // Log the full error for easier debugging
            Log::error('Push changes failed inside transaction: ' . $e->getMessage(), ['exception' => $e]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to push changes.',
                'error' => $e->getMessage()
            ], 500);
        }

        if ($result) {
             return response()->json(['status' => 'success', 'message' => 'Changes pushed successfully.']);
        }

        // This part is reached if the transaction fails for a non-exception reason.
        return response()->json(['status' => 'error', 'message' => 'Transaction failed to commit.'], 500);
    }
}
