<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\Target;

class StatusController extends Controller
{
    /**
     * @OA
     * Get(
     *     path="/api/status/{id}",
     *     summary="Get the status of a target by ID",
     *     tags={"Status"},
     *     security={{"bearerAuth":{}}},
     *     @OA
     *     Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Target ID",
     *         @OA
     *     Schema(type="integer")
     *     ),
     *     @OA
     *     Response(
     *         response=200,
     *         description="Target status"
     *     ),
     *     @OA
     *     Response(response=404, description="Not found")
     * )
     */
    public function show($id)
    {
        $target = Target::with('checks')->findOrFail($id);

        // Assuming 'checks' relation is ordered by latest
        $latestCheck = $target->checks->first();

        return response()->json([
            'target' => $target,
            'latest_check' => $latestCheck,
        ]);
    }
}
