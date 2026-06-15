<section>
    <header class="mb-3">
        <p class="text-sm text-muted">
            {{ __('Pastikan akun Anda menggunakan password yang panjang dan acak untuk menjaga keamanan.') }}
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}">
        @csrf
        @method('put')

        <!-- Current Password -->
        <div class="mb-3">
            <label for="update_password_current_password" class="form-label">Password Saat Ini <span
                    class="text-danger">*</span></label>
            <div class="input-group">
                <input type="password" name="current_password" id="update_password_current_password"
                    class="form-control @error('current_password', 'updatePassword') is-invalid @enderror"
                    placeholder="Masukkan password saat ini" required autocomplete="current-password">
                <button class="btn btn-outline-secondary" type="button"
                    onclick="togglePasswordVisibility('update_password_current_password', this)">
                    <i class="bi bi-eye"></i>
                </button>
            </div>
            @error('current_password', 'updatePassword')
                <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
        </div>

        <!-- New Password -->
        <div class="mb-3">
            <label for="update_password_password" class="form-label">Password Baru <span
                    class="text-danger">*</span></label>
            <div class="input-group">
                <input type="password" name="password" id="update_password_password"
                    class="form-control @error('password', 'updatePassword') is-invalid @enderror"
                    placeholder="Masukkan password baru" required autocomplete="new-password">
                <button class="btn btn-outline-secondary" type="button"
                    onclick="togglePasswordVisibility('update_password_password', this)">
                    <i class="bi bi-eye"></i>
                </button>
            </div>
            <small class="form-text text-muted">Minimal 8 karakter.</small>
            @error('password', 'updatePassword')
                <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
        </div>

        <!-- Confirm Password -->
        <div class="mb-3">
            <label for="update_password_password_confirmation" class="form-label">Konfirmasi Password Baru <span
                    class="text-danger">*</span></label>
            <div class="input-group">
                <input type="password" name="password_confirmation" id="update_password_password_confirmation"
                    class="form-control @error('password_confirmation', 'updatePassword') is-invalid @enderror"
                    placeholder="Ulangi password baru" required autocomplete="new-password">
                <button class="btn btn-outline-secondary" type="button"
                    onclick="togglePasswordVisibility('update_password_password_confirmation', this)">
                    <i class="bi bi-eye"></i>
                </button>
            </div>
            @error('password_confirmation', 'updatePassword')
                <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div class="d-flex align-items-center gap-2 mt-4">
            <button type="submit" class="btn btn-primary">Simpan</button>
        </div>
    </form>
</section>
