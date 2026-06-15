<!doctype html>
<html lang="id">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Login Admin | Posyandu Locator</title>
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
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body class="login-page bg-body-secondary">
    <div class="login-box">
        <div class="login-logo">
            <b>Login</b>
        </div>
        <!-- /.login-logo -->

        <div class="card">
            <div class="card-body login-card-body">
                <!-- Session Status -->
                @if (session('status'))
                    <div class="alert alert-success mb-3">{{ session('status') }}</div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <!-- Email -->
                    <div class="mb-3">
                        <label for="email" class="form-label">Masukkan Email</label>
                        <div class="input-group">
                            <input type="email" name="email" id="email" class="form-control" placeholder="Email"
                                value="{{ old('email') }}" required autofocus>
                            <div class="input-group-text">
                                <span class="bi bi-envelope"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Password -->
                    <div class="mb-3">
                        <label for="password" class="form-label">Masukkan Password</label>
                        <div class="input-group">
                            <input type="password" name="password" id="password" class="form-control"
                                placeholder="Password" required>
                            <button type="button" class="input-group-text" id="togglePassword"
                                style="cursor: pointer;">
                                <span class="bi bi-eye"></span>
                            </button>
                        </div>
                    </div>

                    <!-- Forgot Password Link -->
                    <div class="d-flex justify-content-end mb-3">
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="text-sm text-decoration-none"
                                style="font-size: 13px;">Lupa Password?</a>
                        @endif
                    </div>

                    <!-- Login & Reset Buttons -->
                    <div class="row g-2">
                        <div class="col-6">
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">Login</button>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="d-grid">
                                <button type="reset" class="btn btn-outline-secondary">Reset</button>
                            </div>
                        </div>
                    </div>
                </form>

                <!-- Back to Home -->
                <div class="mt-3 text-center">
                    <a href="/" class="text-decoration-none text-secondary" style="font-size: 13px;">
                        <i class="bi bi-arrow-left me-1"></i>Kembali ke Halaman Utama
                    </a>
                </div>
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
    <script>
        document.getElementById('togglePassword').addEventListener('click', function() {
            const passwordInput = document.getElementById('password');
            const icon = this.querySelector('.bi');
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.remove('bi-eye');
                icon.classList.add('bi-eye-slash');
            } else {
                passwordInput.type = 'password';
                icon.classList.remove('bi-eye-slash');
                icon.classList.add('bi-eye');
            }
        });

        // Reset login attempts counter when clicking form reset
        const resetBtn = document.querySelector('button[type="reset"]');
        if (resetBtn) {
            resetBtn.addEventListener('click', function() {
                localStorage.removeItem('login_attempts');
            });
        }
    </script>

    @if ($errors->any())
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Increment login attempts
                let attempts = parseInt(localStorage.getItem('login_attempts') || '0');
                attempts++;
                localStorage.setItem('login_attempts', attempts);

                const forgotPasswordUrl = "{{ route('password.request') }}";

                if (attempts === 5) {
                    Swal.fire({
                        icon: 'question',
                        title: 'Apakah Anda lupa password?',
                        text: 'Anda telah salah memasukkan password sebanyak 5 kali.',
                        showCancelButton: true,
                        confirmButtonColor: '#0d6efd',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Ya',
                        cancelButtonText: 'Tidak'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = forgotPasswordUrl;
                        }
                    });
                } else if (attempts >= 6) {
                    // Redirect immediately to forgot password
                    window.location.href = forgotPasswordUrl;
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Login Gagal!',
                        text: 'Email atau password yang Anda masukkan salah. (Percobaan ' + attempts + '/5)',
                        confirmButtonColor: '#0d6efd',
                        confirmButtonText: 'Coba Lagi'
                    });
                }
            });
        </script>
    @else
        <script>
            // Reset counter on fresh page load without errors
            localStorage.removeItem('login_attempts');
        </script>
    @endif
    <!--end::Required Scripts-->
</body>

</html>
