<x-app title="Desa">
    <main class="app-main">
        <!--begin::App Content Header-->
        <div class="app-content-header">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-6">
                        <h3 class="mb-0">Desa</h3>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-end">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Halaman Desa</li>
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
                            <h3 class="card-title">Data Desa</h3>
                            <div class="ms-auto d-flex gap-2">
                                <a href="{{ route('desa.create') }}" class="btn btn-sm btn-primary">Tambah Data</a>
                            </div>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <div class="table-responsive">
                                <x-table id="desaTable" :headers="[
                                    'No',
                                    'Nama Desa',
                                    'Aksi'
                                ]">
                                    @foreach ($desa as $item)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $item->nama_desa }}</td>
                                            <td class="text-center">
                                                <a href="{{ route('desa.edit', $item) }}"
                                                    class="btn btn-sm btn-primary"><i
                                                        class="bi bi-pencil-square"></i></a>
                                                <form action="{{ route('desa.destroy', $item) }}" method="POST"
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
