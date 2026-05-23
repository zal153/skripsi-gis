<?php

namespace App\Http\Controllers;

use App\Services\DijkstraService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RouteController extends Controller
{
    public function calculate(Request $request, DijkstraService $dijkstraService): JsonResponse
    {
        $request->validate([
            'startLat' => ['required', 'numeric'],
            'startLng' => ['required', 'numeric'],
            'endLat' => ['required', 'numeric'],
            'endLng' => ['required', 'numeric'],
            'k' => ['nullable', 'integer', 'min:1', 'max:5'],
        ]);

        $k = $request->input('k', 3); // Default to 3 routes total (1 main, 2 alternatives)

        $startedAt = hrtime(true);
        $telemetry = [];

        $routes = $dijkstraService->findKShortestPaths(
            (float) $request->input('startLat'),
            (float) $request->input('startLng'),
            (float) $request->input('endLat'),
            (float) $request->input('endLng'),
            $k,
            $telemetry
        );

        $searchTimeMs = round((hrtime(true) - $startedAt) / 1_000_000, 3);

        if (empty($routes)) {
            return response()->json([
                'success' => false,
                'message' => 'Rute tidak ditemukan.',
                'routes' => [],
                'search_time_ms' => $searchTimeMs,
                'telemetry' => $telemetry,
            ], 404);
        }

        return response()->json([
            'success' => true,
            'routes' => $routes,
            'search_time_ms' => $searchTimeMs,
            'telemetry' => $telemetry,
        ]);
    }
}
