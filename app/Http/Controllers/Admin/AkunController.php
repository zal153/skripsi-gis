<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreAkunRequest;
use App\Models\User;
use SweetAlert2\Laravel\Swal;

class AkunController extends Controller
{
    public function index()
    {
        $akun = User::orderBy('id', 'asc')->get();

        return view('admin.akun.index', compact('akun'));
    }

    public function create()
    {
        return view('admin.akun.create');
    }

    public function store(StoreAkunRequest $request)
    {
        try {
            User::create([
                'name' => $request->validated('name'),
                'email' => $request->validated('email'),
                'password' => $request->validated('password'),
            ]);

            Swal::success([
                'title' => 'Berhasil!',
                'text' => 'Akun berhasil ditambahkan',
            ]);

            return redirect()->route('akun.index');
        } catch (\Exception $e) {
            Swal::error([
                'title' => 'Gagal!',
                'text' => 'Terjadi kesalahan saat menyimpan data: '.$e->getMessage(),
            ]);

            return back()->withInput();
        }
    }

    public function destroy(User $akun)
    {
        User::destroy($akun->id);

        Swal::success([
            'title' => 'Berhasil!',
            'text' => 'Akun berhasil dihapus',
        ]);

        return redirect()->route('akun.index');
    }
}
