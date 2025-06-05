<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\Target;
use Illuminate\Support\Facades\Auth;

class TargetController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/targets",
     *     summary="Create a new target",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","url"},
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="url", type="string")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Target created"),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'url' => 'required|url',
            'check_frequency' => 'required|integer|min:1',
        ]);

        $target = Target::create([
            'user_id' => Auth::id(),
            'name' => $validated['name'],
            'url' => $validated['url'],
            'check_frequency' => $validated['check_frequency'],
            'is_active' => true,
        ]);

        return response()->json($target, 201);
    }

    /**
     * @OA\Get(
     *     path="/api/targets",
     *     summary="Get all targets",
     *     tags={"Targets"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="List of targets"
     *     )
     * )
     */
    public function index(Request $request)
    {
        $targets = Target::all();
        $now = now();
        $result = $targets->map(function ($target) use ($now) {
            // Latest check
            $latestCheck = $target->checks()->latest('created_at')->first();
            // Last 24h checks
            $last24hChecks = $target->checks()
                ->where('created_at', '>=', $now->copy()->subHours(24))
                ->orderBy('created_at')
                ->get(['is_success', 'latency_ms', 'created_at']);
            return [
                'id' => $target->id,
                'name' => $target->name,
                'url' => $target->url,
                'ssl_expiry_days' => $target->ssl_expiry_days,
                'domain_expiry_days' => $target->domain_expiry_days,
                'latest_check' => $latestCheck ? [
                    'is_success' => $latestCheck->is_success,
                    'latency' => $latestCheck->latency_ms,
                    'created_at' => $latestCheck->created_at,
                ] : null,
                'last_24h_checks' => $last24hChecks->map(function($c) {
                    return [
                        'is_success' => $c->is_success,
                        'latency' => $c->latency_ms,
                        'created_at' => $c->created_at,
                    ];
                }),
            ];
        });
        return response()->json($result);
    }
}
