<?php

use App\Services\DijkstraService;

test('route api returns route search time in milliseconds', function () {
    $this->mock(DijkstraService::class, function ($mock) {
        $mock->shouldReceive('findKShortestPaths')
            ->once()
            ->with(-8.105, 113.74, -8.102, 113.735, 3, Mockery::any())
            ->andReturnUsing(function ($startLat, $startLng, $endLat, $endLng, $K, &$telemetry) {
                $telemetry = [
                    'graph_nodes_count' => 10,
                    'graph_edges_count' => 15,
                    'dijkstra_runs' => 1,
                    'dijkstra_visited' => 5,
                    'padding' => 0.025,
                    'start_snap_distance' => 0.05,
                    'end_snap_distance' => 0.02,
                ];

                return [
                    [
                        'path' => [
                            ['lat' => -8.105, 'lng' => 113.74],
                            ['lat' => -8.102, 'lng' => 113.735],
                        ],
                        'distance' => 0.557,
                    ],
                ];
            });
    });

    $response = $this->getJson('/api/route?startLat=-8.105&startLng=113.74&endLat=-8.102&endLng=113.735&k=3');

    $response->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonCount(1, 'routes')
        ->assertJsonStructure([
            'success',
            'routes',
            'search_time_ms',
            'telemetry',
        ]);

    expect($response->json('search_time_ms'))->toBeNumeric();
});
