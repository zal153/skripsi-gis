<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreJalanRequest;
use App\Http\Requests\Admin\UpdateJalanRequest;
use App\Models\Jalan;
use App\Models\TitikJalan;
use App\Services\HaversineService;
use Illuminate\Http\Request;
use SweetAlert2\Laravel\Swal;

class JalanController extends Controller
{
    public function __construct(public HaversineService $haversineService) {}

    public function index(Request $request)
    {
        $search = $request->input('search');
        $sortBy = $request->input('sort_by', 'id');
        $sortDir = $request->input('sort_dir', 'desc');
        $perPage = $request->input('per_page', 50); // Using 50 as default for Jalan
        $sortDir = in_array(strtolower($sortDir), ['asc', 'desc']) ? $sortDir : 'desc';

        $jalan = Jalan::with('titikAwal', 'titikAkhir')
            ->search($search)
            ->sort($sortBy, $sortDir)
            ->paginate($perPage)
            ->withQueryString();

        return view('admin.jalan.index', compact('jalan', 'search', 'sortBy', 'sortDir', 'perPage'));
    }

    public function create()
    {
        $titikJalan = TitikJalan::limit(1000)->get();

        return view('admin.jalan.create', compact('titikJalan'));
    }

    public function store(StoreJalanRequest $request)
    {
        try {
            $payload = $request->validated();

            // Validasi duplikasi jalan
            $isDuplicate = Jalan::where('titik_awal_id', $payload['titik_awal_id'])
                ->where('titik_akhir_id', $payload['titik_akhir_id'])
                ->exists();

            if ($isDuplicate) {
                throw new \Exception('Rute jalan dari titik tersebut ke titik akhir sudah ada.');
            }

            $payload['jarak'] = $this->calculateDistanceInKilometers(
                (int) $payload['titik_awal_id'],
                (int) $payload['titik_akhir_id'],
            );

            $batasMaksimalKm = 5; // Atur jarak maksimal dalam Kilometer di sini
            if ($payload['jarak'] > $batasMaksimalKm) {
                throw new \Exception("Jarak terlalu jauh ({$payload['jarak']} km). Batas maksimal jarak yang dizinkan adalah {$batasMaksimalKm} km.");
            }

            Jalan::create($payload);

            Swal::success([
                'title' => 'Berhasil!',
                'text' => 'Data jalan berhasil ditambahkan',
            ]);

            return redirect()->route('jalan.index');
        } catch (\Exception $e) {
            Swal::error([
                'title' => 'Gagal!',
                'text' => 'Terjadi kesalahan saat menyimpan data: '.$e->getMessage(),
            ]);

            return back()->withInput();
        }
    }

    public function show(Jalan $jalan)
    {
        return view('admin.jalan.show', compact('jalan'));
    }

    public function edit(Jalan $jalan)
    {
        $titikJalan = TitikJalan::limit(1000)->get();
        if (! $titikJalan->contains($jalan->titik_awal_id)) {
            $titikJalan->push($jalan->titikAwal);
        }
        if (! $titikJalan->contains($jalan->titik_akhir_id)) {
            $titikJalan->push($jalan->titikAkhir);
        }

        return view('admin.jalan.edit', compact('jalan', 'titikJalan'));
    }

    public function update(UpdateJalanRequest $request, Jalan $jalan)
    {
        try {
            $payload = $request->validated();

            // Validasi duplikasi jalan (selain relasi jalan yang sedang diedit)
            $isDuplicate = Jalan::where('titik_awal_id', $payload['titik_awal_id'])
                ->where('titik_akhir_id', $payload['titik_akhir_id'])
                ->where('id', '!=', $jalan->id)
                ->exists();

            if ($isDuplicate) {
                throw new \Exception('Rute jalan dari titik tersebut ke titik akhir sudah ada.');
            }

            $payload['jarak'] = $this->calculateDistanceInKilometers(
                (int) $payload['titik_awal_id'],
                (int) $payload['titik_akhir_id'],
            );

            $batasMaksimalKm = 15;
            if ($payload['jarak'] > $batasMaksimalKm) {
                throw new \Exception("Jarak terlalu jauh ({$payload['jarak']} km). Batas maksimal jarak yang dizinkan adalah {$batasMaksimalKm} km.");
            }

            $jalan->update($payload);

            Swal::success([
                'title' => 'Berhasil!',
                'text' => 'Data jalan berhasil diperbarui',
            ]);

            return redirect()->route('jalan.index');
        } catch (\Exception $e) {
            Swal::error([
                'title' => 'Gagal!',
                'text' => 'Terjadi kesalahan saat memperbarui data: '.$e->getMessage(),
            ]);

            return back()->withInput();
        }
    }

    public function destroy(Jalan $jalan)
    {
        $jalan->delete();

        Swal::success([
            'title' => 'Berhasil!',
            'text' => 'Data jalan berhasil dihapus',
        ]);

        return redirect()->route('jalan.index');
    }

    private function calculateDistanceInKilometers(int $titikAwalId, int $titikAkhirId): float
    {
        $titikAwal = TitikJalan::query()->findOrFail($titikAwalId);
        $titikAkhir = TitikJalan::query()->findOrFail($titikAkhirId);

        return round(
            $this->haversineService->distanceInKilometers(
                (float) $titikAwal->latitude,
                (float) $titikAwal->longitude,
                (float) $titikAkhir->latitude,
                (float) $titikAkhir->longitude,
            ),
            3,
        );
    }
}
