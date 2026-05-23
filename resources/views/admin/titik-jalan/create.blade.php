<x-app title="Tambah Titik Jalan">
    <main class="app-main">
        <div class="app-content-header">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-6">
                        <h3 class="mb-0">Tambah Titik Jalan</h3>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-end">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('titik-jalan.index') }}">Titik Jalan</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Tambah Titik Jalan</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <div class="app-content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card card-primary card-outline mb-4">
                            <div class="card-header">
                                <div class="card-title">Form Tambah Titik Jalan</div>
                            </div>
                            <form action="{{ route('titik-jalan.store') }}" method="POST">
                                @csrf
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="nama_titik" class="form-label">Nama Titik</label>
                                        <input type="text" name="nama_titik" id="nama_titik"
                                            class="form-control @error('nama_titik') is-invalid @enderror"
                                            value="{{ old('nama_titik') }}" placeholder="Masukkan nama titik" required>
                                        @error('nama_titik')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="latitude" class="form-label">Latitude</label>
                                        <input type="number" step="0.00000001" name="latitude" id="latitude"
                                            class="form-control @error('latitude') is-invalid @enderror"
                                            value="{{ old('latitude') }}" placeholder="Masukkan latitude" required>
                                        @error('latitude')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="longitude" class="form-label">Longitude</label>
                                        <input type="number" step="0.00000001" name="longitude" id="longitude"
                                            class="form-control @error('longitude') is-invalid @enderror"
                                            value="{{ old('longitude') }}" placeholder="Masukkan longitude" required>
                                        @error('longitude')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                </div>
                                <div class="card-footer">
                                    <button type="submit" class="btn btn-primary">Simpan</button>
                                    <a href="{{ route('titik-jalan.index') }}" class="btn btn-danger">Batal</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</x-app>
