<x-app title="Jalan">
    <main class="app-main">
        <!--begin::App Content Header-->
        <div class="app-content-header">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-6">
                        <h3 class="mb-0">Jalan</h3>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-end">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Halaman Jalan</li>
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
                            <h3 class="card-title">Data Jalan</h3>
                            <a href="{{ route('jalan.create') }}" class="btn btn-sm btn-primary ms-auto">Tambah Data</a>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <form action="{{ route('jalan.index') }}" method="GET" class="mb-3 d-flex justify-content-between align-items-center">
                                <!-- Entries Per Page -->
                                <div class="d-flex align-items-center">
                                    <select name="per_page" class="form-select form-select-sm w-auto me-2" onchange="this.form.submit()">
                                        <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
                                        <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                                        <option value="50" {{ request('per_page', 50) == 50 ? 'selected' : '' }}>50</option>
                                        <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                                    </select>
                                    <span class="text-muted small">entries per page</span>
                                </div>

                                <!-- Search Box -->
                                <div class="input-group" style="max-width: 250px;">
                                    <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari nama titik..." value="{{ request('search') }}">
                                    @if(request('sort_by')) <input type="hidden" name="sort_by" value="{{ request('sort_by') }}"> @endif
                                    @if(request('sort_dir')) <input type="hidden" name="sort_dir" value="{{ request('sort_dir') }}"> @endif
                                    <button class="btn btn-outline-secondary btn-sm" type="submit"><i class="bi bi-search"></i></button>
                                </div>
                            </form>

                            @php
                                $sortHelper = function($column) {
                                    $currentSortBy = request('sort_by', 'id');
                                    $currentSortDir = request('sort_dir', 'desc');
                                    $newDir = ($currentSortBy === $column && $currentSortDir === 'asc') ? 'desc' : 'asc';
                                    $icon = 'bi-chevron-expand';
                                    
                                    if ($currentSortBy === $column) {
                                        $icon = $currentSortDir === 'asc' ? 'bi-chevron-up' : 'bi-chevron-down';
                                    }
                                    
                                    $url = request()->fullUrlWithQuery(['sort_by' => $column, 'sort_dir' => $newDir]);
                                    return '<a href="' . $url . '" class="text-dark text-decoration-none d-flex justify-content-between align-items-center">' . 
                                           '<span>' . ucfirst(str_replace('_', ' ', $column)) . '</span>' .
                                           '<i class="bi ' . $icon . ' ms-1" style="font-size: 0.8em;"></i></a>';
                                };
                            @endphp

                            <div class="table-responsive">
                                <table class="table table-bordered table-striped align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>{!! $sortHelper('id') !!}</th>
                                            <th>{!! $sortHelper('titik_awal') !!}</th>
                                            <th>{!! $sortHelper('titik_akhir') !!}</th>
                                            <th>{!! $sortHelper('jarak') !!}</th>
                                            <th class="text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                @forelse ($jalan as $item)
                                    <tr>
                                        <td>{{ $jalan->firstItem() + $loop->index }}</td>
                                        <td>{{ $item->titikAwal->nama_titik ?? '-' }}</td>
                                        <td>{{ $item->titikAkhir->nama_titik ?? '-' }}</td>
                                        <td>{{ $item->jarak }} km</td>
                                        <td class="text-center">
                                            <a href="{{ route('jalan.edit', $item) }}" class="btn btn-sm btn-primary"><i
                                                    class="bi bi-pencil-square"></i></a>
                                            <form action="{{ route('jalan.destroy', $item) }}" method="POST"
                                                style="display:inline;" id="delete-form-{{ $item->id }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-sm btn-danger delete-btn"
                                                    data-form-id="{{ $item->id }}"><i
                                                        class="bi bi-trash"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-3">Tidak ada data ditemukan</td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                            </div>
                            
                            <div class="mt-3">
                                {{ $jalan->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--end::App Content-->
    </main>
</x-app>
