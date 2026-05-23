<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreDesaRequest;
use App\Http\Requests\Admin\UpdateDesaRequest;
use App\Models\Desa;
use Illuminate\Http\Request;
use SweetAlert2\Laravel\Swal;

class DesaController extends Controller
{
    public function index()
    {
        $desa = Desa::orderBy('id', 'asc')->get();

        return view('admin.desa.index', compact('desa'));
    }

    public function create()
    {
        return view('admin.desa.create');
    }

    public function store(StoreDesaRequest $request)
    {
        try {
            Desa::create($request->validated());

            Swal::success([
                'title' => 'Berhasil!',
                'text' => 'Data desa berhasil ditambahkan',
            ]);

            return redirect()->route('desa.index');
        } catch (\Exception $e) {
            Swal::error([
                'title' => 'Gagal!',
                'text' => 'Terjadi kesalahan saat menyimpan data: '.$e->getMessage(),
            ]);

            return back()->withInput();
        }
    }

    public function edit(Desa $desa)
    {
        $desa = Desa::findOrFail($desa->id);

        return view('admin.desa.edit', compact('desa'));
    }

    public function update(UpdateDesaRequest $request, Desa $desa)
    {
        try {
            $desa->update($request->validated());

            Swal::success([
                'title' => 'Berhasil!',
                'text' => 'Data desa berhasil diperbarui',
            ]);

            return redirect()->route('desa.index');
        } catch (\Exception $e) {
            Swal::error([
                'title' => 'Gagal!',
                'text' => 'Terjadi kesalahan saat memperbarui data: '.$e->getMessage(),
            ]);

            return back()->withInput();
        }
    }

    public function destroy(Desa $desa)
    {
        $desa->delete();

        Swal::success([
            'title' => 'Berhasil!',
            'text' => 'Data desa berhasil dihapus',
        ]);

        return redirect()->route('desa.index');
    }
}
