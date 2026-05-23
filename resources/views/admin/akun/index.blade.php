<x-app title="Akun">
    <main class="app-main">
        <!--begin::App Content Header-->
        <div class="app-content-header">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-6">
                        <h3 class="mb-0">Akun</h3>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-end">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Halaman Akun</li>
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
                        <div class="card-header d-flex flex-column flex-md-row justify-content-between align-items-center">
                            <h3 class="card-title mb-3 mb-md-0">Data Akun</h3>
                            
                            <div class="d-flex w-100 w-md-auto align-items-center">
                                <form action="{{ route('akun.index') }}" method="GET" class="d-flex me-2 w-100" id="search-form">
                                    <input type="hidden" name="sort_by" value="{{ request('sort_by', 'id') }}">
                                    <input type="hidden" name="sort_dir" value="{{ request('sort_dir', 'desc') }}">
                                    <input type="hidden" name="per_page" value="{{ request('per_page', 10) }}">
                                    
                                    <div class="input-group input-group-sm">
                                        <input type="text" name="search" class="form-control" placeholder="Cari akun..."
                                            value="{{ request('search') }}">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="bi bi-search"></i>
                                        </button>
                                        @if(request('search'))
                                            <a href="{{ route('akun.index', ['per_page' => request('per_page', 10)]) }}" class="btn btn-secondary">
                                                <i class="bi bi-x-circle"></i>
                                            </a>
                                        @endif
                                    </div>
                                </form>
                                <a href="{{ route('akun.create') }}" class="btn btn-sm btn-primary text-nowrap">Tambah Data</a>
                            </div>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover align-middle" id="akunTable">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="text-center" width="5%">No.</th>
                                            <th width="35%">
                                                <a href="{{ route('akun.index', ['sort_by' => 'name', 'sort_dir' => request('sort_by') == 'name' && request('sort_dir') == 'asc' ? 'desc' : 'asc', 'search' => request('search'), 'per_page' => request('per_page')]) }}" class="text-decoration-none text-dark d-flex justify-content-between align-items-center">
                                                    Nama
                                                    @if(request('sort_by') == 'name')
                                                        <i class="bi bi-sort-{{ request('sort_dir') == 'asc' ? 'up' : 'down' }}"></i>
                                                    @else
                                                        <i class="bi bi-arrow-down-up text-muted opacity-50"></i>
                                                    @endif
                                                </a>
                                            </th>
                                            <th width="40%">
                                                <a href="{{ route('akun.index', ['sort_by' => 'email', 'sort_dir' => request('sort_by') == 'email' && request('sort_dir') == 'asc' ? 'desc' : 'asc', 'search' => request('search'), 'per_page' => request('per_page')]) }}" class="text-decoration-none text-dark d-flex justify-content-between align-items-center">
                                                    Email
                                                    @if(request('sort_by') == 'email')
                                                        <i class="bi bi-sort-{{ request('sort_dir') == 'asc' ? 'up' : 'down' }}"></i>
                                                    @else
                                                        <i class="bi bi-arrow-down-up text-muted opacity-50"></i>
                                                    @endif
                                                </a>
                                            </th>
                                            <th class="text-center" width="20%">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                @foreach ($akun as $item)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $item->name }}</td>
                                        <td>{{ $item->email }}</td>
                                        <td>
                                            <a href="{{ route('akun.edit', $item) }}" class="btn btn-sm btn-primary"><i
                                                    class="bi bi-pencil-square"></i></a>
                                            <form action="{{ route('akun.destroy', $item) }}" method="POST"
                                                style="display:inline;" id="delete-form-{{ $item->id }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-sm btn-danger delete-btn"
                                                    data-form-id="{{ $item->id }}"><i
                                                        class="bi bi-trash"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        
                        <!-- Pagination -->
                        @if($akun->hasPages())
                        <div class="card-footer clearfix d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <span class="me-2 text-muted">Tampilkan</span>
                                <form action="{{ route('akun.index') }}" method="GET" id="perPageForm">
                                    <input type="hidden" name="search" value="{{ request('search') }}">
                                    <input type="hidden" name="sort_by" value="{{ request('sort_by') }}">
                                    <input type="hidden" name="sort_dir" value="{{ request('sort_dir') }}">
                                    <select name="per_page" class="form-select form-select-sm" onchange="document.getElementById('perPageForm').submit()">
                                        <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10</option>
                                        <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                                        <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                                        <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                                    </select>
                                </form>
                                <span class="ms-2 text-muted">data</span>
                            </div>
                            
                            {{ $akun->links('pagination::bootstrap-5') }}
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <!--end::App Content-->
    </main>
</x-app>
