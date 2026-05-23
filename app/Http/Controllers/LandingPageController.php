<?php

namespace App\Http\Controllers;

use App\Models\Posyandu;
use Illuminate\View\View;

class LandingPageController extends Controller
{
    public function __invoke(): View
    {
        $posyanduData = Posyandu::query()
            ->select(['id', 'nama_posyandu', 'alamat', 'latitude', 'longitude'])
            ->orderBy('nama_posyandu')
            ->get()
            ->map(function (Posyandu $posyandu): array {
                return [
                    'id' => $posyandu->id,
                    'nama' => $posyandu->nama_posyandu,
                    'alamat' => $posyandu->alamat,
                    'lat' => (float) $posyandu->latitude,
                    'lng' => (float) $posyandu->longitude,
                ];
            })
            ->values();

        return view('landing-page', [
            'posyanduData' => $posyanduData,
        ]);
    }
}
