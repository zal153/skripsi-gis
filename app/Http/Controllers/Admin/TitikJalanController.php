<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreTitikJalanRequest;
use App\Http\Requests\Admin\UpdateTitikJalanRequest;
use App\Models\TitikJalan;
use Illuminate\Http\Request;
use SweetAlert2\Laravel\Swal;

class TitikJalanController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $sortBy = $request->input('sort_by', 'id');
        $sortDir = $request->input('sort_dir', 'desc');
        $perPage = $request->input('per_page', 50); // Using 50 as default for TitikJalan
        $sortDir = in_array(strtolower($sortDir), ['asc', 'desc']) ? $sortDir : 'desc';

        $titikJalan = TitikJalan::search($search)
            ->sort($sortBy, $sortDir)
            ->paginate($perPage)
            ->withQueryString();

        return view('admin.titik-jalan.index', compact('titikJalan', 'search', 'sortBy', 'sortDir', 'perPage'));
    }

    public function create()
    {
        return view('admin.titik-jalan.create');
    }

    public function store(StoreTitikJalanRequest $request)
    {
        try {
            TitikJalan::create($request->validated());

            Swal::success([
                'title' => 'Berhasil!',
                'text' => 'Data berhasil ditambahkan',
            ]);

            return redirect()->route('titik-jalan.index');
        } catch (\Exception $e) {
            Swal::error([
                'title' => 'Gagal!',
                'text' => 'Terjadi kesalahan saat menyimpan data: '.$e->getMessage(),
            ]);

            return back()->withInput();
        }
    }

    public function show(TitikJalan $titikJalan)
    {
        return view('admin.titik-jalan.show', compact('titikJalan'));
    }

    public function edit(TitikJalan $titikJalan)
    {
        return view('admin.titik-jalan.edit', compact('titikJalan'));
    }

    public function update(UpdateTitikJalanRequest $request, TitikJalan $titikJalan)
    {
        try {
            $titikJalan->update($request->validated());

            Swal::success([
                'title' => 'Berhasil!',
                'text' => 'Data berhasil diperbarui',
            ]);

            return redirect()->route('titik-jalan.index');
        } catch (\Exception $e) {
            Swal::error([
                'title' => 'Gagal!',
                'text' => 'Terjadi kesalahan saat memperbarui data: '.$e->getMessage(),
            ]);

            return back()->withInput();
        }
    }

    public function destroy(TitikJalan $titikJalan)
    {
        $titikJalan->delete();

        Swal::success([
            'title' => 'Berhasil!',
            'text' => 'Data berhasil dihapus',
        ]);

        return redirect()->route('titik-jalan.index');
    }
}
