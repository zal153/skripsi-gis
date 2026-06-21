<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\DijkstraService;
use App\Services\HaversineService;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $jumlahDesa = DB::table('desa')->count();
        $jumlahPosyandu = DB::table('posyandu')->count();
        $jumlahJalan = DB::table('jalan')->count();
        $jumlahTitik = DB::table('titik_jalan')->count();

        return view('admin.dashboard.index', compact('jumlahDesa', 'jumlahPosyandu', 'jumlahJalan', 'jumlahTitik'));
    }

    public function maeTest(HaversineService $haversine, DijkstraService $dijkstra)
    {
        // Pasangan titik awal dan posyandu tujuan yang sudah ditentukan (fixed)
        $fixedPairs = [
            ['start_name' => 'Balai Desa Candijati', 'start_lat' => -8.10035, 'start_lng' => 113.75512, 'end_name' => 'Manggis 21', 'end_lat' => -8.092901, 'end_lng' => 113.712533],
            ['start_name' => 'Desa Darsono (Dusun Padasan)', 'start_lat' => -8.092901, 'start_lng' => 113.712533, 'end_name' => 'Manggis 1', 'end_lat' => -8.102094, 'end_lng' => 113.735351],
            ['start_name' => 'Desa Biting (Dusun Krajan)', 'start_lat' => -8.115592, 'start_lng' => 113.763222, 'end_name' => 'Manggis 13', 'end_lat' => -8.113960, 'end_lng' => 113.713130],
            ['start_name' => 'Desa Kemuning Lor (Dusun Rayap)', 'start_lat' => -8.092080, 'start_lng' => 113.694550, 'end_name' => 'Manggis 31', 'end_lat' => -8.115592, 'end_lng' => 113.763222],
            ['start_name' => 'Desa Kamal (Dusun Krajan)', 'start_lat' => -8.103427, 'start_lng' => 113.750880, 'end_name' => 'Manggis 8', 'end_lat' => -8.098968, 'end_lng' => 113.722526],
            ['start_name' => 'Desa Arjasa (Manggis 7)', 'start_lat' => -8.101200, 'start_lng' => 113.723100, 'end_name' => 'Manggis 32', 'end_lat' => -8.118717, 'end_lng' => 113.759173],
            ['start_name' => 'Puskesmas Arjasa', 'start_lat' => -8.095720, 'start_lng' => 113.755510, 'end_name' => 'Manggis 24', 'end_lat' => -8.083521, 'end_lng' => 113.706507],
            ['start_name' => 'Stasiun Arjasa', 'start_lat' => -8.129460, 'start_lng' => 113.742770, 'end_name' => 'Manggis 34', 'end_lat' => -8.113103, 'end_lng' => 113.769647],
            ['start_name' => 'Polsek Arjasa', 'start_lat' => -8.122440, 'start_lng' => 113.746050, 'end_name' => 'Manggis 22', 'end_lat' => -8.089094, 'end_lng' => 113.710685],
            ['start_name' => 'Kantor Kecamatan Arjasa', 'start_lat' => -8.117120, 'start_lng' => 113.748920, 'end_name' => 'Manggis 8', 'end_lat' => -8.098968, 'end_lng' => 113.722526],
            ['start_name' => 'WP4J+CMM Kamal', 'start_lat' => -8.093912, 'start_lng' => 113.731681, 'end_name' => 'Manggis 3', 'end_lat' => -8.119150, 'end_lng' => 113.743119],
            ['start_name' => 'VQW5+G4Q Candijati', 'start_lat' => -8.103653, 'start_lng' => 113.757856, 'end_name' => 'Manggis 40', 'end_lat' => -8.094318, 'end_lng' => 113.732622],
            ['start_name' => 'WQ67+CWV Candijati', 'start_lat' => -8.088896, 'start_lng' => 113.764801, 'end_name' => 'Manggis 42', 'end_lat' => -8.085130, 'end_lng' => 113.696240],
            ['start_name' => 'WP83+MPR Darsono', 'start_lat' => -8.083274, 'start_lng' => 113.704259, 'end_name' => 'Manggis 16', 'end_lat' => -8.080920, 'end_lng' => 113.693110],
            ['start_name' => 'WM6V+V9X Kemuning Lor', 'start_lat' => -8.087774, 'start_lng' => 113.693472, 'end_name' => 'Manggis 27', 'end_lat' => -8.107950, 'end_lng' => 113.755230],
            ['start_name' => 'VPHJ+47Q Kemuning Lor', 'start_lat' => -8.122151, 'start_lng' => 113.730746, 'end_name' => 'Manggis 28', 'end_lat' => -8.104712, 'end_lng' => 113.762145],
            ['start_name' => 'VPQQ+C5M Arjasa', 'start_lat' => -8.111410, 'start_lng' => 113.737910, 'end_name' => 'Manggis 29', 'end_lat' => -8.099765, 'end_lng' => 113.765966],
            ['start_name' => 'VQ35+V75 Candijati', 'start_lat' => -8.095365, 'start_lng' => 113.758224, 'end_name' => 'Manggis 16', 'end_lat' => -8.080920, 'end_lng' => 113.693110],
            ['start_name' => 'VQJ6+766 Biting', 'start_lat' => -8.119336, 'start_lng' => 113.760517, 'end_name' => 'Manggis 20', 'end_lat' => -8.113934, 'end_lng' => 113.727758],
            ['start_name' => 'VPJX+QHC Arjasa', 'start_lat' => -8.118055, 'start_lng' => 113.748890, 'end_name' => 'Manggis 38', 'end_lat' => -8.092991, 'end_lng' => 113.751143],
        ];

        $testCases = [];

        foreach ($fixedPairs as $i => $pair) {
            // 1. Calculate Haversine (straight-line) distance
            $distHaversine = $haversine->distanceInKilometers(
                $pair['start_lat'], $pair['start_lng'],
                $pair['end_lat'], $pair['end_lng']
            );

            // 2. Calculate Dijkstra (road network) distance
            $routes = $dijkstra->findKShortestPaths(
                $pair['start_lat'], $pair['start_lng'],
                $pair['end_lat'], $pair['end_lng'],
                1
            );
            $distDijkstra = ! empty($routes) ? $routes[0]['distance'] : $distHaversine;

            $testCases[] = [
                'no' => $i + 1,
                'start_name' => $pair['start_name'],
                'start_lat' => $pair['start_lat'],
                'start_lng' => $pair['start_lng'],
                'end_name' => $pair['end_name'],
                'end_lat' => $pair['end_lat'],
                'end_lng' => $pair['end_lng'],
                'dist_haversine' => round($distHaversine, 4),
                'dist_dijkstra' => round($distDijkstra, 4),
            ];
        }

        $seed = 'fixed';

        return view('admin.mae.index', compact('testCases', 'seed'));
    }
}
