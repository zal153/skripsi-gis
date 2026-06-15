<x-app title="Edit Posyandu">
    <main class="app-main">
        <!--begin::App Content Header-->
        <div class="app-content-header">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-6">
                        <h3 class="mb-0">Edit Posyandu</h3>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-end">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('posyandu.index') }}">Posyandu</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Edit Posyandu</li>
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
                                <div class="card-title">Form Edit Posyandu</div>
                            </div>
                            <form action="{{ route('posyandu.update', $posyandu->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="card-body">
                                    <!-- Desa -->
                                    <div class="mb-3">
                                        <label for="desa_id" class="form-label">Desa <span
                                                class="text-danger">*</span></label>
                                        <select name="desa_id" id="desa_id"
                                            class="form-control @error('desa_id') is-invalid @enderror" required>
                                            <option value="">-- Pilih Desa --</option>
                                            @foreach ($desa as $item)
                                                <option value="{{ $item->id }}"
                                                    {{ old('desa_id', $posyandu->desa_id) == $item->id ? 'selected' : '' }}>
                                                    {{ $item->nama_desa }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('desa_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Nama Posyandu -->
                                    <div class="mb-3">
                                        <label for="nama_posyandu" class="form-label">Nama Posyandu <span
                                                class="text-danger">*</span></label>
                                        <input type="text" name="nama_posyandu" id="nama_posyandu"
                                            class="form-control @error('nama_posyandu') is-invalid @enderror"
                                            value="{{ old('nama_posyandu', $posyandu->nama_posyandu) }}"
                                            placeholder="Masukkan nama posyandu" required>
                                        @error('nama_posyandu')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Alamat -->
                                    <div class="mb-3">
                                        <label for="alamat" class="form-label">Alamat <span
                                                class="text-danger">*</span></label>
                                        <textarea name="alamat" id="alamat" class="form-control @error('alamat') is-invalid @enderror"
                                            placeholder="Masukkan alamat posyandu" rows="3" required>{{ old('alamat', $posyandu->alamat) }}</textarea>
                                        @error('alamat')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Latitude -->
                                    <div class="mb-3">
                                        <label for="latitude" class="form-label">Latitude <span
                                                class="text-danger">*</span></label>
                                        <input type="number" name="latitude" id="latitude" step="0.00000001"
                                            class="form-control @error('latitude') is-invalid @enderror"
                                            value="{{ old('latitude', $posyandu->latitude) }}"
                                            placeholder="Masukkan latitude (contoh: -8.12071)" required>
                                        @error('latitude')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Longitude -->
                                    <div class="mb-3">
                                        <label for="longitude" class="form-label">Longitude <span
                                                class="text-danger">*</span></label>
                                        <input type="number" name="longitude" id="longitude" step="0.00000001"
                                            class="form-control @error('longitude') is-invalid @enderror"
                                            value="{{ old('longitude', $posyandu->longitude) }}"
                                            placeholder="Masukkan longitude (contoh: 113.72991)" required>
                                        @error('longitude')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Status -->
                                    <div class="mb-3">
                                        <label for="status" class="form-label">Status <span
                                                class="text-danger">*</span></label>
                                        <select name="status" id="status"
                                            class="form-control @error('status') is-invalid @enderror" required>
                                            <option value="">-- Pilih Status --</option>
                                            <option value="aktif"
                                                {{ old('status', $posyandu->status) == 'aktif' ? 'selected' : '' }}>
                                                Aktif
                                            </option>
                                            <option value="non-aktif"
                                                {{ old('status', $posyandu->status) == 'non-aktif' ? 'selected' : '' }}>
                                                Non-Aktif
                                            </option>
                                        </select>
                                        @error('status')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Keterangan -->
                                    <div class="mb-3">
                                        <label for="keterangan" class="form-label">Keterangan</label>
                                        <textarea name="keterangan" id="keterangan" class="form-control @error('keterangan') is-invalid @enderror"
                                            placeholder="Masukkan keterangan (opsional)" rows="3">{{ old('keterangan', $posyandu->keterangan) }}</textarea>
                                        @error('keterangan')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <button type="submit" class="btn btn-primary">Simpan</button>
                                    <a href="{{ route('posyandu.index') }}" class="btn btn-danger">Batal</a>
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
