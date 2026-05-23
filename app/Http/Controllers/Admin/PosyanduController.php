<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StorePosyanduRequest;
use App\Http\Requests\Admin\UpdatePosyanduRequest;
use App\Models\Desa;
use App\Models\Posyandu;
use Illuminate\Http\Request;
use SweetAlert2\Laravel\Swal;

class PosyanduController extends Controller
{
    public function index()
    {
        $posyandu = Posyandu::with('desa')->orderBy('id', 'asc')->get();

        return view('admin.posyandu.index', compact('posyandu'));
    }

    public function create()
    {
        $desa = Desa::all();

        return view('admin.posyandu.create', compact('desa'));
    }

    public function store(StorePosyanduRequest $request)
    {
        try {
            Posyandu::create($request->validated());

            Swal::success([
                'title' => 'Berhasil!',
                'text' => 'Data posyandu berhasil ditambahkan',
            ]);

            return redirect()->route('posyandu.index');
        } catch (\Exception $e) {
            Swal::error([
                'title' => 'Gagal!',
                'text' => 'Terjadi kesalahan saat menyimpan data: '.$e->getMessage(),
            ]);

            return back()->withInput();
        }
    }

    public function show(Posyandu $posyandu)
    {
        return view('admin.posyandu.show', compact('posyandu'));
    }

    public function edit(Posyandu $posyandu)
    {
        $desa = Desa::all();

        return view('admin.posyandu.edit', compact('posyandu', 'desa'));
    }

    public function update(UpdatePosyanduRequest $request, Posyandu $posyandu)
    {
        try {
            $posyandu->update($request->validated());

            Swal::success([
                'title' => 'Berhasil!',
                'text' => 'Data posyandu berhasil diperbarui',
            ]);

            return redirect()->route('posyandu.index');
        } catch (\Exception $e) {
            Swal::error([
                'title' => 'Gagal!',
                'text' => 'Terjadi kesalahan saat memperbarui data: '.$e->getMessage(),
            ]);

            return back()->withInput();
        }
    }

    public function destroy(Posyandu $posyandu)
    {
        $posyandu->delete();

        Swal::success([
            'title' => 'Berhasil!',
            'text' => 'Data posyandu berhasil dihapus',
        ]);

        return redirect()->route('posyandu.index');
    }
}
