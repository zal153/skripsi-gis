<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
// use Illuminate\Http\Request;
use App\Models\Desa;
use App\Models\Jalan;
use App\Models\Posyandu;
use App\Models\TitikJalan;

class DashboardController extends Controller
{
    public function index()
    {
        $jumlahDesa = Desa::count();
        $jumlahPosyandu = Posyandu::count();
        $jumlahJalan = Jalan::count();
        $jumlahTitik = TitikJalan::count();

        return view('admin.dashboard.index', compact('jumlahDesa', 'jumlahPosyandu', 'jumlahJalan', 'jumlahTitik'));
    }
}
