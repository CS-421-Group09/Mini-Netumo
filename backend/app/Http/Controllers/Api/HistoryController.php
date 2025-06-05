<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\Target;

/**
 * @OA\Get(
 *     path="/api/history/{id}",
 *     summary="Get the check history for a target by ID",
 *     tags={"History"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Target ID",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Target history"
 *     ),
 *     @OA\Response(response=404, description="Not found")
 * )
 */
class HistoryController extends Controller
{
    public function show($id)
    {
        $target = Target::with(['checks' => function($q) {
            $q->orderBy('created_at', 'desc')->limit(100);
        }])->findOrFail($id);

        return response()->json([
            'target' => $target,
            'history' => $target->checks,
        ]);
    }
}
