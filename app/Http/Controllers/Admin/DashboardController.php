<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\DijkstraService;
use App\Services\HaversineService;
use Illuminate\Http\Request;
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

    public function maeTest(Request $request, HaversineService $haversine, DijkstraService $dijkstra)
    {
        $startPoints = [
            ['name' => 'Balai Desa Candijati', 'lat' => -8.100350, 'lng' => 113.755120],
            ['name' => 'Desa Darsono (Dusun Padasan)', 'lat' => -8.092901, 'lng' => 113.712533],
            ['name' => 'Desa Biting (Dusun Krajan)', 'lat' => -8.115592, 'lng' => 113.763222],
            ['name' => 'Desa Kemuning Lor (Dusun Rayap)', 'lat' => -8.092080, 'lng' => 113.694550],
            ['name' => 'Desa Kamal (Dusun Krajan)', 'lat' => -8.103427, 'lng' => 113.750880],
            ['name' => 'Desa Arjasa (Manggis 7)', 'lat' => -8.101200, 'lng' => 113.723100],
            ['name' => 'Puskesmas Arjasa', 'lat' => -8.09572, 'lng' => 113.75551],
            ['name' => 'Stasiun Arjasa', 'lat' => -8.12946, 'lng' => 113.74277],
            ['name' => 'Polsek Arjasa', 'lat' => -8.12244, 'lng' => 113.74605],
            ['name' => 'Kantor Kecamatan Arjasa', 'lat' => -8.11712, 'lng' => 113.74892],
        ];

        $seed = $request->input('seed', 12345);
        mt_srand($seed);

        $posyandus = DB::table('posyandu')->get();
        $testCases = [];

        if ($posyandus->isNotEmpty()) {
            foreach ($startPoints as $i => $start) {
                // Pick a pseudo-random posyandu using seeded random
                $index = mt_rand(0, $posyandus->count() - 1);
                $p = $posyandus[$index];

                // 1. Calculate Haversine (straight-line) distance
                $distHaversine = $haversine->distanceInKilometers($start['lat'], $start['lng'], (float) $p->latitude, (float) $p->longitude);

                // 2. Calculate Dijkstra (road network) distance
                $routes = $dijkstra->findKShortestPaths($start['lat'], $start['lng'], (float) $p->latitude, (float) $p->longitude, 1);
                $distDijkstra = ! empty($routes) ? $routes[0]['distance'] : $distHaversine;

                $testCases[] = [
                    'no' => $i + 1,
                    'start_name' => $start['name'],
                    'start_lat' => $start['lat'],
                    'start_lng' => $start['lng'],
                    'end_name' => $p->nama_posyandu,
                    'end_lat' => (float) $p->latitude,
                    'end_lng' => (float) $p->longitude,
                    'dist_haversine' => round($distHaversine, 4),
                    'dist_dijkstra' => round($distDijkstra, 4),
                ];
            }
        }

        $nextSeed = mt_rand(1, 99999);

        return view('admin.mae.index', compact('testCases', 'nextSeed', 'seed'));
    }
}
