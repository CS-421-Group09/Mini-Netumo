<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Controller;
use App\Models\Alert;
use Illuminate\Http\Request;

/**
 * @OA\Get(
 *     path="/api/alerts",
 *     summary="Get all alerts for the authenticated user",
 *     tags={"Alerts"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="List of alerts"
 *     ),
 *     @OA\Response(response=401, description="Unauthorized")
 * )
 */
class AlertController extends Controller
{
    public function index(Request $request)
    {
        // Make sure the user is authenticated
        $user = $request->user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Get all target IDs for this user
        $targetIds = $user->targets()->pluck('id');

        // Get all alerts for those targets
        $alerts = \App\Models\Alert::whereIn('target_id', $targetIds)->get();

        // If not, and you want all alerts:
        // $alerts = Alert::all();

        return response()->json($alerts);
    }
}
