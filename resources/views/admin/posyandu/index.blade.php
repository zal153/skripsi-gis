<x-app title="Posyandu">
    <main class="app-main">
        <!--begin::App Content Header-->
        <div class="app-content-header">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-6">
                        <h3 class="mb-0">Posyandu</h3>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-end">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Halaman Posyandu</li>
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
                            <h3 class="card-title">Data Posyandu</h3>
                            <a href="{{ route('posyandu.create') }}" class="btn btn-sm btn-primary ms-auto">Tambah
                                Data</a>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <div class="table-responsive">
                                <x-table id="posyanduTable" :headers="[
                                    'No',
                                    'Desa',
                                    'Nama Posyandu',
                                    'Alamat',
                                    'Latitude',
                                    'Longitude',
                                    'Status',
                                    'Keterangan',
                                    'Aksi',
                                ]">
                                    @foreach ($posyandu as $item)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $item->desa->nama_desa }}</td>
                                            <td>{{ $item->nama_posyandu }}</td>
                                            <td>{{ $item->alamat }}</td>
                                            <td>{{ $item->latitude }}</td>
                                            <td>{{ $item->longitude }}</td>
                                            <td>
                                                @if ($item->status === 'aktif')
                                                    <span class="badge text-bg-success">Aktif</span>
                                                @else
                                                    <span class="badge text-bg-danger">Tidak Aktif</span>
                                                @endif
                                            </td>
                                            <td>{{ $item->keterangan ?? '-' }}</td>
                                            <td class="text-center">
                                                <a href="{{ route('posyandu.edit', $item) }}"
                                                    class="btn btn-sm btn-primary" title="Edit">
                                                    <i class="bi bi-pencil-square"></i>
                                                </a>
                                                <form action="{{ route('posyandu.destroy', $item) }}" method="POST"
                                                    style="display:inline;" id="delete-form-{{ $item->id }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="btn btn-sm btn-danger delete-btn"
                                                        data-form-id="{{ $item->id }}" title="Delete">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </x-table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--end::App Content-->
    </main>

</x-app>
