<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Laporan;
use App\Models\LaporanBalasan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use SweetAlert2\Laravel\Swal;

class LaporanController extends Controller
{
    /**
     * Display a listing of the reports.
     */
    public function index(): View
    {
        $laporans = Laporan::with(['balasans' => function ($query) {
            $query->orderBy('created_at', 'asc');
        }, 'balasans.user'])->latest()->get();

        return view('admin.laporan.index', compact('laporans'));
    }

    /**
     * Store a reply from the admin.
     */
    public function storeReply(Request $request, Laporan $laporan): RedirectResponse
    {
        $request->validate([
            'pesan' => 'required|string',
        ]);

        LaporanBalasan::create([
            'laporan_id' => $laporan->id,
            'user_id' => Auth::id(),
            'pesan' => $request->input('pesan'),
        ]);

        Swal::success([
            'title' => 'Berhasil!',
            'text' => 'Balasan berhasil dikirim',
        ]);

        return redirect()->back();
    }

    /**
     * Remove the specified report from storage.
     */
    public function destroy(Laporan $laporan): RedirectResponse
    {
        $laporan->delete();

        Swal::success([
            'title' => 'Berhasil!',
            'text' => 'Laporan berhasil dihapus',
        ]);

        return redirect()->route('laporan.index');
    }
}
