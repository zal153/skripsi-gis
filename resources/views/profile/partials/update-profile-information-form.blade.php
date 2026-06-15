<section>
    <header class="mb-3">
        <p class="text-sm text-muted">
            {{ __("Perbarui informasi profil akun dan alamat email Anda.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}">
        @csrf
        @method('patch')

        <!-- Name -->
        <div class="mb-3">
            <label for="name" class="form-label">Nama</label>
            <input type="text" name="name" id="name"
                class="form-control @error('name') is-invalid @enderror"
                value="{{ old('name', $user->name) }}" placeholder="Masukkan nama" required autofocus autocomplete="name">
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Email -->
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" name="email" id="email"
                class="form-control @error('email') is-invalid @enderror"
                value="{{ old('email', $user->email) }}" placeholder="Masukkan email" required autocomplete="username">
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && !$user->hasVerifiedEmail())
                <div class="mt-2">
                    <p class="text-sm text-warning d-flex align-items-center gap-2">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                        {{ __('Alamat email Anda belum diverifikasi.') }}
                    </p>
                    <button form="send-verification"
                        class="btn btn-sm btn-outline-secondary">
                        {{ __('Klik di sini untuk mengirim ulang email verifikasi.') }}
                    </button>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 text-success small fw-semibold">
                            {{ __('Tautan verifikasi baru telah dikirim ke alamat email Anda.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="d-flex align-items-center gap-2 mt-4">
            <button type="submit" class="btn btn-primary">Simpan</button>
        </div>
    </form>
</section>
