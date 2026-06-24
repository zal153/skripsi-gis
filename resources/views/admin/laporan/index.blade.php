<x-app title="Laporan Pengguna">
    <main class="app-main">
        <!--begin::App Content Header-->
        <div class="app-content-header">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-6">
                        <h3 class="mb-0">Laporan Pengguna</h3>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-end">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Halaman Laporan</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <!--end::App Content Header-->
        <!--begin::App Content-->
        <div class="app-content">
            <div class="container-fluid">
                <div class="col-md-12">
                    <div class="card mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h3 class="card-title">Daftar Laporan Posyandu Baru</h3>
                            <div class="ms-auto d-flex gap-2 align-items-center">
                                <a href="{{ route('laporan.export-pdf') }}" class="btn btn-sm btn-danger">
                                    <i class="bi bi-file-earmark-pdf-fill me-1"></i> Cetak PDF
                                </a>
                                <span class="badge bg-secondary">{{ $laporans->count() }} Laporan</span>
                            </div>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            @if ($laporans->isEmpty())
                                <div class="text-center py-5">
                                    <i class="bi bi-chat-left-text text-gray-300 fs-1"></i>
                                    <p class="text-muted mt-2 mb-0">Belum ada laporan posyandu dari pengguna.</p>
                                </div>
                            @else
                                <div class="row">
                                    @foreach ($laporans as $item)
                                        <div class="col-12 mb-3">
                                            <div class="card border border-light-subtle shadow-sm">
                                                <div
                                                    class="card-header d-flex align-items-center justify-content-between bg-light">
                                                    <div>
                                                        <h5 class="card-title mb-0 text-primary fw-bold"
                                                            style="font-size: 15px;">
                                                            <i
                                                                class="bi bi-hospital-fill me-2"></i>{{ $item->nama_posyandu }}
                                                        </h5>
                                                        <small class="text-muted">Dilaporkan pada
                                                            {{ $item->created_at->format('d M Y, H:i') }}
                                                            ({{ $item->created_at->diffForHumans() }})</small>
                                                    </div>
                                                    <div>
                                                        <form action="{{ route('laporan.destroy', $item) }}"
                                                            method="POST" style="display:inline;"
                                                            id="delete-form-{{ $item->id }}">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="button"
                                                                class="btn btn-sm btn-danger delete-btn"
                                                                data-form-id="{{ $item->id }}">
                                                                <i class="bi bi-trash-fill me-1"></i> Hapus Laporan
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                                <div class="card-body">
                                                    <p class="mb-2 text-sm"><strong>Alamat:</strong> {{ $item->alamat }}
                                                    </p>
                                                    @if ($item->keterangan)
                                                        <p class="mb-3 text-muted text-sm"><strong>Keterangan:</strong>
                                                            {{ $item->keterangan }}</p>
                                                    @endif

                                                    <hr class="my-3 border-light-subtle">

                                                    <!-- Accordion / Balasan Section -->
                                                    <div class="bg-body-tertiary p-3 rounded-3"
                                                        style="background-color: #f8f9fa;">
                                                        <h6 class="fw-bold mb-3 d-flex align-items-center"
                                                            style="font-size: 13px;">
                                                            <i class="bi bi-reply-fill text-purple me-2"
                                                                style="color: #7c3aed;"></i>
                                                            Balasan ({{ $item->balasans->count() }})
                                                        </h6>

                                                        @if ($item->balasans->isEmpty())
                                                            <p class="text-muted small mb-3">Belum ada balasan untuk
                                                                laporan ini.</p>
                                                        @else
                                                            <div class="reply-list mb-3">
                                                                @foreach ($item->balasans as $balasan)
                                                                    <div
                                                                        class="reply-item p-2 mb-2 bg-white rounded border border-light-subtle shadow-xs">
                                                                        <div
                                                                            class="d-flex justify-content-between align-items-center mb-1">
                                                                            <span
                                                                                class="fw-bold small text-dark d-flex align-items-center"
                                                                                style="font-size: 11px;">
                                                                                <i
                                                                                    class="bi bi-person-badge-fill text-success me-1"></i>
                                                                                {{ $balasan->user ? $balasan->user->name : 'Admin' }}
                                                                                <span
                                                                                    class="badge bg-success-subtle text-success ms-2 small"
                                                                                    style="font-size: 9px; padding: 2px 6px;">Petugas</span>
                                                                            </span>
                                                                            <small class="text-muted"
                                                                                style="font-size: 10px;">{{ $balasan->created_at->diffForHumans() }}</small>
                                                                        </div>
                                                                        <p class="mb-0 text-secondary small"
                                                                            style="font-size: 12px; padding-left: 2px;">
                                                                            {{ $balasan->pesan }}</p>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        @endif

                                                        <!-- Reply Form -->
                                                        <form action="{{ route('laporan.reply', $item) }}"
                                                            method="POST" class="mt-3">
                                                            @csrf
                                                            <div class="input-group">
                                                                <input type="text" name="pesan" required
                                                                    placeholder="Tulis balasan admin di sini..."
                                                                    class="form-control form-control-sm">
                                                                <button type="reset"
                                                                    class="btn btn-sm btn-outline-secondary">Reset</button>
                                                                <button type="submit" class="btn btn-sm btn-primary">
                                                                    <i class="bi bi-send-fill me-1"></i> Kirim
                                                                </button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--end::App Content-->
    </main>

    <x-alert />
</x-app>
