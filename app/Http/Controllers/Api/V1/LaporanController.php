<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\LaporanResource;
use App\Models\Laporan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    /**
     * Get list of reports with their replies.
     */
    public function index(): JsonResponse
    {
        $laporans = Laporan::with(['balasans' => function ($query) {
            $query->orderBy('created_at', 'asc');
        }, 'balasans.user'])->latest()->get();

        return response()->json([
            'success' => true,
            'message' => 'Daftar laporan berhasil diambil',
            'data' => LaporanResource::collection($laporans),
        ]);
    }

    /**
     * Store a new report.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nama_posyandu' => 'required|string|max:255',
            'alamat' => 'required|string',
            'keterangan' => 'nullable|string',
        ]);

        $laporan = Laporan::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Laporan berhasil dikirim ke admin',
            'data' => new LaporanResource($laporan->load('balasans')),
        ], 201);
    }
}
