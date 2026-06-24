<x-app title="Tambah Desa">
    <main class="app-main">
        <!--begin::App Content Header-->
        <div class="app-content-header">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-6">
                        <h3 class="mb-0">Tambah Desa</h3>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-end">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('desa.index') }}">Desa</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Tambah Desa</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <!--end::App Content Header-->
        <!--begin::App Content-->
        <div class="app-content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card card-primary card-outline mb-4">
                            <div class="card-header">
                                <div class="card-title">Form Tambah Desa</div>
                            </div>
                            <form action="{{ route('desa.store') }}" method="POST">
                                @csrf
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="nama_desa" class="form-label">Nama Desa <span
                                                class="text-danger">*</span></label>
                                        <input type="text" name="nama_desa" id="nama_desa"
                                            class="form-control @error('nama_desa') is-invalid @enderror"
                                            value="{{ old('nama_desa') }}" placeholder="Masukkan nama desa" required>
                                        @error('nama_desa')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    <div class="text-danger small mb-3">* Wajib diisi</div>
                                </div>
                                <div class="card-footer">
                                    <button type="submit" class="btn btn-primary">Simpan</button>
                                    <button type="reset" class="btn btn-outline-secondary">Reset</button>
                                    <a href="{{ route('desa.index') }}" class="btn btn-danger">Batal</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--end::App Content-->
    </main>
</x-app>
