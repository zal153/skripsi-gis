<x-app title="Edit Akun">
    <main class="app-main">
        <!--begin::App Content Header-->
        <div class="app-content-header">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-6">
                        <h3 class="mb-0">Edit Akun</h3>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-end">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('akun.index') }}">Akun</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Edit Akun</li>
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
                                <div class="card-title">Form Edit Akun</div>
                            </div>
                            <form action="{{ route('akun.update', $akun->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="card-body">
                                    <!-- Nama -->
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Nama</label>
                                        <input type="text" name="name" id="name"
                                            class="form-control @error('name') is-invalid @enderror"
                                            value="{{ old('name', $akun->name) }}" placeholder="Masukkan nama" required>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Email -->
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email</label>
                                        <input type="email" name="email" id="email"
                                            class="form-control @error('email') is-invalid @enderror"
                                            value="{{ old('email', $akun->email) }}" placeholder="Masukkan email"
                                            required>
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Password Awal -->
                                    <div class="mb-3">
                                        <label for="current_password" class="form-label">Password Awal</label>
                                        <div class="input-group">
                                            <input type="password" name="current_password" id="current_password"
                                                class="form-control @error('current_password') is-invalid @enderror"
                                                placeholder="Masukkan password awal (saat ini)">
                                            <button class="btn btn-outline-secondary" type="button" onclick="togglePasswordVisibility('current_password', this)">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                        </div>
                                        @error('current_password')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Password Baru -->
                                    <div class="mb-3">
                                        <label for="password" class="form-label">Password Baru</label>
                                        <div class="input-group">
                                            <input type="password" name="password" id="password"
                                                class="form-control @error('password') is-invalid @enderror"
                                                placeholder="Masukkan password baru">
                                            <button class="btn btn-outline-secondary" type="button" onclick="togglePasswordVisibility('password', this)">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                        </div>
                                        <small class="form-text text-muted">Minimal 8 karakter. Kosongkan jika password tidak diubah.</small>
                                        @error('password')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Konfirmasi Password Baru -->
                                    <div class="mb-3">
                                        <label for="password_confirmation" class="form-label">Konfirmasi Password Baru</label>
                                        <div class="input-group">
                                            <input type="password" name="password_confirmation" id="password_confirmation"
                                                class="form-control" placeholder="Ulangi password baru">
                                            <button class="btn btn-outline-secondary" type="button" onclick="togglePasswordVisibility('password_confirmation', this)">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <button type="submit" class="btn btn-primary">Simpan</button>
                                    <a href="{{ route('akun.index') }}" class="btn btn-danger">Batal</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--end::App Content-->
    </main>

    @push('scripts')
        <script>
            function togglePasswordVisibility(inputId, button) {
                const input = document.getElementById(inputId);
                const icon = button.querySelector('i');
                if (input.type === 'password') {
                    input.type = 'text';
                    icon.classList.remove('bi-eye');
                    icon.classList.add('bi-eye-slash');
                } else {
                    input.type = 'password';
                    icon.classList.remove('bi-eye-slash');
                    icon.classList.add('bi-eye');
                }
            }
        </script>
    @endpush
</x-app>
