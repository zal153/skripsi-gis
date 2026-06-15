<!doctype html>
<html lang="id">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Lupa Password | Posyandu Locator</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!--begin::Fonts-->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.0.12/index.css"
        integrity="sha256-tXJfXfp6Ewt1ilPzLDtQnJV4hclT9XuaZUKyUvmyr+Q=" crossorigin="anonymous" />
    <!--end::Fonts-->
    <!--begin::Third Party Plugin(Bootstrap Icons)-->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css"
        crossorigin="anonymous" />
    <!--end::Third Party Plugin(Bootstrap Icons)-->
    <!--begin::Required Plugin(AdminLTE)-->
    <link rel="stylesheet" href="{{ asset('assets/css/adminlte.css') }}" />
    <!--end::Required Plugin(AdminLTE)-->
</head>

<body class="login-page bg-body-secondary">
    <div class="login-box" style="width: 400px; max-width: 90vw;">
        <div class="login-logo mb-3">
            <a href="/" class="text-decoration-none text-dark">
                <b>Posyandu</b> Locator
            </a>
        </div>
        <!-- /.login-logo -->

        <div class="card card-outline card-primary shadow-sm rounded-4">
            <div class="card-body login-card-body p-4">
                <h4 class="fw-bold text-dark text-center mb-3">Lupa Kata Sandi?</h4>
                <p class="text-muted text-sm text-center mb-4" style="font-size: 13px; line-height: 1.6;">
                    Jangan khawatir! Masukkan alamat email Anda yang terdaftar, dan kami akan mengirimkan link untuk
                    mereset kata sandi Anda secara aman.
                </p>

                <!-- Session Status -->
                @if (session('status'))
                    <div class="alert alert-success mb-3 small" role="alert">
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('password.email') }}">
                    @csrf

                    <!-- Email Address -->
                    <div class="mb-4">
                        <label for="email" class="form-label fw-semibold text-xs text-gray-700"
                            style="font-size: 13px;">Alamat Email Anda <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="email" name="email" id="email"
                                class="form-control @error('email') is-invalid @enderror"
                                placeholder="Masukkan email terdaftar..." value="{{ old('email') }}" required
                                autofocus>
                            <div class="input-group-text bg-white border-start-0">
                                <span class="bi bi-envelope text-muted"></span>
                            </div>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-grid gap-2 mb-3">
                        <button type="submit" class="btn btn-primary py-2 fw-semibold">
                            <i class="bi bi-envelope-fill me-1"></i> Kirim Link Reset Password
                        </button>
                    </div>

                    <div class="text-center mt-3">
                        <a href="{{ route('login') }}" class="text-decoration-none text-sm fw-semibold text-primary"
                            style="font-size: 13px;">
                            <i class="bi bi-arrow-left me-1"></i> Kembali ke Halaman Login
                        </a>
                    </div>
                </form>
            </div>
            <!-- /.login-card-body -->
        </div>
    </div>
    <!-- /.login-box -->

    <!--begin::Required Scripts-->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.min.js" crossorigin="anonymous"></script>
    <script src="{{ asset('assets/js/adminlte.js') }}"></script>
</body>

</html>
