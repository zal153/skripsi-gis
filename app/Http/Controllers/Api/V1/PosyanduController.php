<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\PosyanduResource;
use App\Models\Posyandu;
use Illuminate\Http\Request;

class PosyanduController extends Controller
{
    /**
     * Get a list of all Posyandu.
     * Optionally filter by search term.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        $posyandu = Posyandu::with('desa')
            ->search($search)
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Data Posyandu berhasil diambil',
            'data' => PosyanduResource::collection($posyandu),
        ]);
    }
}
