<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreAkunRequest;
use App\Models\User;
use Illuminate\Http\Request;
use SweetAlert2\Laravel\Swal;

class AkunController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $sortBy = $request->input('sort_by', 'id');
        $sortDir = $request->input('sort_dir', 'desc');
        $perPage = $request->input('per_page', 10);

        $akun = User::search($search)
            ->sort($sortBy, $sortDir)
            ->paginate($perPage)
            ->withQueryString();

        return view('admin.akun.index', compact('akun', 'search', 'sortBy', 'sortDir', 'perPage'));
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
        $akun->delete();

        Swal::success([
            'title' => 'Berhasil!',
            'text' => 'Akun berhasil dihapus',
        ]);

        return redirect()->route('akun.index');
    }
}
