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
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h3 class="card-title">Data Akun</h3>
                            <a href="{{ route('akun.create') }}" class="btn btn-sm btn-primary ms-auto">Tambah Data</a>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <div class="table-responsive">
                                <x-table id="akunTable" :headers="['No.', 'ID', 'Nama', 'Email', 'Aksi']">
                                    @foreach ($akun as $item)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $item->id }}</td>
                                            <td>{{ $item->name }}</td>
                                            <td>{{ $item->email }}</td>
                                            <td class="text-center">
                                                @if (auth()->id() === $item->id)
                                                    <a href="{{ route('akun.edit', $item) }}" class="btn btn-sm btn-primary"
                                                        aria-label="Edit akun saya"><i class="bi bi-pencil-square"></i></a>
                                                @else
                                                    <button type="button" class="btn btn-sm btn-info text-white show-detail-btn"
                                                        data-id="{{ $item->id }}"
                                                        data-name="{{ $item->name }}"
                                                        data-email="{{ $item->email }}"
                                                        aria-label="Tampilkan detail akun">
                                                        <i class="bi bi-eye-fill"></i>
                                                    </button>
                                                @endif
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

    @push('scripts')
        <script type="module">
            document.addEventListener('DOMContentLoaded', function() {
                document.body.addEventListener('click', function(e) {
                    const btn = e.target.closest('.show-detail-btn');
                    if (btn) {
                        const id = btn.getAttribute('data-id');
                        const name = btn.getAttribute('data-name');
                        const email = btn.getAttribute('data-email');
                        
                        Swal.fire({
                            title: 'Detail Akun',
                            html: `
                                <div class="text-start fs-6 p-2">
                                    <div class="mb-2"><strong>ID:</strong> ${id}</div>
                                    <div class="mb-2"><strong>Nama:</strong> ${name}</div>
                                    <div class="mb-2"><strong>Email:</strong> ${email}</div>
                                </div>
                            `,
                            icon: 'info',
                            confirmButtonText: 'Tutup',
                            confirmButtonColor: '#0d6efd',
                        });
                    }
                });
            });
        </script>
    @endpush
</x-app>
