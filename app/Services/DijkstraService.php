<?php

namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class DijkstraService
{
    private int $runsCount = 0;

    private int $visitedCount = 0;

    public function __construct(public HaversineService $haversine) {}

    /**
     * @return array<int, array{path: array<int, array{lat: float, lng: float}>, distance: float}>
     */
    public function findKShortestPaths(float $startLat, float $startLng, float $endLat, float $endLng, int $K = 3, ?array &$telemetry = null): array
    {
        $this->runsCount = 0;
        $this->visitedCount = 0;

        $paddings = [0.025, 0.05, 0.08];

        foreach ($paddings as $padding) {
            $minLat = min($startLat, $endLat) - $padding;
            $maxLat = max($startLat, $endLat) + $padding;
            $minLng = min($startLng, $endLng) - $padding;
            $maxLng = max($startLng, $endLng) + $padding;

            [$allTitik, $allJalan] = $this->getRoutingData($minLat, $maxLat, $minLng, $maxLng);

            if ($allTitik->isEmpty() || $allJalan->isEmpty()) {
                continue;
            }

            $graph = [];
            $titikMap = [];
            foreach ($allTitik as $titik) {
                $graph[$titik->id] = [];
                $titikMap[$titik->id] = [
                    'lat' => (float) $titik->latitude,
                    'lng' => (float) $titik->longitude,
                ];
            }

            foreach ($allJalan as $jalan) {
                if (isset($graph[$jalan->titik_awal_id]) && isset($graph[$jalan->titik_akhir_id])) {
                    $graph[$jalan->titik_awal_id][] = [
                        'to' => $jalan->titik_akhir_id,
                        'weight' => (float) $jalan->jarak,
                    ];

                    // Add reverse edge to make graph bidirectional like in JS
                    $graph[$jalan->titik_akhir_id][] = [
                        'to' => $jalan->titik_awal_id,
                        'weight' => (float) $jalan->jarak,
                    ];
                }
            }

            $startSnapDistance = 0.0;
            $endSnapDistance = 0.0;
            $startId = $this->findNearestTitikId($allTitik, $startLat, $startLng, $graph, $startSnapDistance);
            $endId = $this->findNearestTitikId($allTitik, $endLat, $endLng, $graph, $endSnapDistance);

            if ($startId === null || $endId === null) {
                continue;
            }

            // Find the 1st shortest path
            $firstPath = $this->runDijkstra($graph, $startId, $endId);
            if ($firstPath === null) {
                continue;
            }

            // Implementation of Yen's Algorithm, which uses Dijkstra
            $A = []; // Array to store the top $K shortest paths
            $B = []; // Priority queue/array for potential paths

            $A[0] = $firstPath;

            for ($k = 1; $k < $K; $k++) {
                $prevPathNodes = $A[$k - 1]['nodes'];

                for ($i = 0; $i < count($prevPathNodes) - 1; $i++) {
                    $spurNodeId = $prevPathNodes[$i];
                    $rootPathNodes = array_slice($prevPathNodes, 0, $i + 1);

                    $removedEdges = [];
                    foreach ($A as $p) {
                        $pNodes = $p['nodes'];
                        if (count($pNodes) > $i && array_slice($pNodes, 0, $i + 1) === $rootPathNodes) {
                            $removedEdges[] = [
                                'from' => $pNodes[$i],
                                'to' => $pNodes[$i + 1],
                            ];
                        }
                    }

                    $removedNodes = [];
                    foreach ($rootPathNodes as $rootPathNode) {
                        if ($rootPathNode !== $spurNodeId) {
                            $removedNodes[] = $rootPathNode;
                        }
                    }

                    $spurPath = $this->runDijkstra($graph, $spurNodeId, $endId, $removedEdges, $removedNodes);

                    if ($spurPath !== null) {
                        $totalPathNodes = array_merge(array_slice($rootPathNodes, 0, count($rootPathNodes) - 1), $spurPath['nodes']);

                        // Calculate total cost
                        $totalCost = 0;
                        for ($j = 0; $j < count($totalPathNodes) - 1; $j++) {
                            $from = $totalPathNodes[$j];
                            $to = $totalPathNodes[$j + 1];
                            foreach ($graph[$from] as $edge) {
                                if ($edge['to'] === $to) {
                                    $totalCost += $edge['weight'];
                                    break;
                                }
                            }
                        }

                        $pathInB = false;
                        foreach ($B as $potentialPath) {
                            if ($potentialPath['nodes'] === $totalPathNodes) {
                                $pathInB = true;
                                break;
                            }
                        }

                        if (! $pathInB) {
                            $B[] = [
                                'nodes' => $totalPathNodes,
                                'cost' => $totalCost,
                            ];
                        }
                    }
                }

                if (empty($B)) {
                    break;
                }

                usort($B, function ($a, $b) {
                    return $a['cost'] <=> $b['cost'];
                });

                $A[$k] = array_shift($B);
            }

            // Prepare the final output mapping node IDs to coordinates and filter unreasonable routes
            $result = [];
            $shortestDistance = null;
            $maxDeviationRatio = 1.3; // Maximum 30% longer than the main route

            foreach ($A as $pathData) {
                if ($shortestDistance === null) {
                    $shortestDistance = $pathData['cost'];
                }

                // Filter out alternative routes that are excessively far (like Google Maps does)
                if ($pathData['cost'] <= ($shortestDistance * $maxDeviationRatio)) {
                    $coords = [];
                    foreach ($pathData['nodes'] as $nodeId) {
                        $coords[] = $titikMap[$nodeId];
                    }

                    $result[] = [
                        'path' => $coords,
                        'distance' => $pathData['cost'],
                    ];
                }
            }

            if (is_array($telemetry)) {
                $telemetry = [
                    'graph_nodes_count' => count($allTitik),
                    'graph_edges_count' => count($allJalan),
                    'dijkstra_runs' => $this->runsCount,
                    'dijkstra_visited' => $this->visitedCount,
                    'padding' => $padding,
                    'start_snap_distance' => $startSnapDistance,
                    'end_snap_distance' => $endSnapDistance,
                ];
            }

            return $result;
        }

        if (is_array($telemetry)) {
            $telemetry = [
                'graph_nodes_count' => 0,
                'graph_edges_count' => 0,
                'dijkstra_runs' => $this->runsCount,
                'dijkstra_visited' => $this->visitedCount,
                'padding' => 0.0,
                'start_snap_distance' => 0.0,
                'end_snap_distance' => 0.0,
            ];
        }

        return [];
    }

    private function runDijkstra(array $graph, int $startId, int $endId, array $removedEdges = [], array $removedNodes = []): ?array
    {
        $this->runsCount++;
        $distances = [];
        $previous = [];
        $visited = []; // Only track visited, don't pre-fill 50k unvisited

        foreach (array_keys($graph) as $titikId) {
            $distances[$titikId] = INF;
            $previous[$titikId] = null;
        }

        $distances[$startId] = 0;

        // Use a min-priority queue (SplPriorityQueue is a max-heap, so we insert negative priorities)
        $queue = new \SplPriorityQueue;
        $queue->insert($startId, 0);

        while (! $queue->isEmpty()) {
            $currentId = $queue->extract();

            if ($currentId === $endId) {
                break;
            }

            // Skip if we already found a shorter path before this popped
            if (isset($visited[$currentId])) {
                continue;
            }
            $visited[$currentId] = true;
            $this->visitedCount++;

            $currentDistance = $distances[$currentId];

            foreach ($graph[$currentId] as $edge) {
                $neighborId = $edge['to'];

                if (in_array($neighborId, $removedNodes)) {
                    continue;
                }

                $isEdgeRemoved = false;
                foreach ($removedEdges as $removedEdge) {
                    if ($removedEdge['from'] === $currentId && $removedEdge['to'] === $neighborId) {
                        $isEdgeRemoved = true;
                        break;
                    }
                }

                if ($isEdgeRemoved) {
                    continue;
                }

                $alt = $currentDistance + $edge['weight'];
                if ($alt < $distances[$neighborId]) {
                    $distances[$neighborId] = $alt;
                    $previous[$neighborId] = $currentId;

                    // Insert with negative distance so smaller distances are popped first
                    $queue->insert($neighborId, -$alt);
                }
            }
        }

        if ($distances[$endId] === INF) {
            return null;
        }

        $pathNodes = [];
        $current = $endId;

        while ($current !== null) {
            array_unshift($pathNodes, $current);
            $current = $previous[$current];
        }

        return [
            'nodes' => $pathNodes,
            'cost' => $distances[$endId],
        ];
    }

    // fallback for the previous logic
    public function findRoute(float $startLat, float $startLng, float $endLat, float $endLng): ?array
    {
        $routes = $this->findKShortestPaths($startLat, $startLng, $endLat, $endLng, 1);
        if (! empty($routes)) {
            return $routes[0]['path'];
        }

        return null;
    }

    /**
     * @return array{0: Collection, 1: Collection}
     */
    private function getRoutingData(float $minLat = -90, float $maxLat = 90, float $minLng = -180, float $maxLng = 180): array
    {
        $osmTitik = DB::table('titik_jalan')
            ->select('id', 'latitude', 'longitude')
            ->where('source', 'osm')
            ->whereBetween('latitude', [$minLat, $maxLat])
            ->whereBetween('longitude', [$minLng, $maxLng])
            ->get();

        if ($osmTitik->count() >= 2) {
            $osmJalan = DB::table('jalan')
                ->select('titik_awal_id', 'titik_akhir_id', 'jarak')
                ->where('source', 'osm')
                ->whereIn('titik_awal_id', function ($query) use ($minLat, $maxLat, $minLng, $maxLng) {
                    $query->select('id')
                        ->from('titik_jalan')
                        ->where('source', 'osm')
                        ->whereBetween('latitude', [$minLat, $maxLat])
                        ->whereBetween('longitude', [$minLng, $maxLng]);
                })
                ->whereIn('titik_akhir_id', function ($query) use ($minLat, $maxLat, $minLng, $maxLng) {
                    $query->select('id')
                        ->from('titik_jalan')
                        ->where('source', 'osm')
                        ->whereBetween('latitude', [$minLat, $maxLat])
                        ->whereBetween('longitude', [$minLng, $maxLng]);
                })
                ->get();

            return [$osmTitik, $osmJalan];
        }

        return [DB::table('titik_jalan')->get(), DB::table('jalan')->get()];
    }

    private function isNodeConnectedToMainNetwork(array $graph, int $nodeId, int $minSize = 30): bool
    {
        if (empty($graph[$nodeId])) {
            return false;
        }

        $visited = [];
        $queue = [$nodeId];
        $visited[$nodeId] = true;
        $count = 0;

        while (! empty($queue)) {
            $current = array_shift($queue);
            $count++;
            if ($count >= $minSize) {
                return true;
            }

            foreach ($graph[$current] as $edge) {
                $neighbor = $edge['to'];
                if (! isset($visited[$neighbor])) {
                    $visited[$neighbor] = true;
                    $queue[] = $neighbor;
                }
            }
        }

        return false;
    }

    private function findNearestTitikId(Collection $allTitik, float $lat, float $lng, array $graph, ?float &$snapDistance = null): ?int
    {
        $candidates = [];

        foreach ($allTitik as $titik) {
            $distance = $this->haversine->distanceInKilometers(
                $lat,
                $lng,
                (float) $titik->latitude,
                (float) $titik->longitude
            );

            $candidates[] = [
                'id' => (int) $titik->id,
                'distance' => $distance,
            ];
        }

        usort($candidates, function ($a, $b) {
            return $a['distance'] <=> $b['distance'];
        });

        foreach ($candidates as $candidate) {
            if ($this->isNodeConnectedToMainNetwork($graph, $candidate['id'])) {
                $snapDistance = (float) $candidate['distance'];

                return $candidate['id'];
            }
        }

        if (! empty($candidates)) {
            $snapDistance = (float) $candidates[0]['distance'];

            return $candidates[0]['id'];
        }

        $snapDistance = 0.0;

        return null;
    }
}
