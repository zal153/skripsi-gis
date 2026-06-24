<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreAkunRequest;
use App\Http\Requests\Admin\UpdateAkunRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;
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

    public function edit(Request $request, User $akun): View
    {
        abort_unless($request->user()->is($akun), 403);

        return view('admin.akun.edit', compact('akun'));
    }

    public function update(UpdateAkunRequest $request, User $akun)
    {
        try {
            $data = [
                'name' => $request->validated('name'),
                'email' => $request->validated('email'),
            ];

            if ($request->filled('password')) {
                $data['password'] = $request->validated('password');
            }

            $akun->update($data);

            Swal::success([
                'title' => 'Berhasil!',
                'text' => 'Akun berhasil diperbarui',
            ]);

            return redirect()->route('akun.index');
        } catch (\Exception $e) {
            Swal::error([
                'title' => 'Gagal!',
                'text' => 'Terjadi kesalahan saat memperbarui data: '.$e->getMessage(),
            ]);

            return back()->withInput();
        }
    }
}
