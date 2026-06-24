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
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- EmailJS SDK -->
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/@emailjs/browser@4/dist/email.min.js"></script>
    <script type="text/javascript">
        (function() {
            emailjs.init({
                publicKey: "YLRHffHV-8qeE8wm3",
            });
        })();
    </script>
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

                <form id="forgotPasswordForm" method="POST" action="{{ route('password.email') }}">
                    @csrf

                    <!-- Email Address -->
                    <div class="mb-4">
                        <label for="email" class="form-label fw-semibold text-xs text-gray-700"
                            style="font-size: 13px;">Alamat Email Anda</label>
                        <div class="input-group">
                            <input type="email" name="email" id="email"
                                class="form-control"
                                placeholder="Masukkan email terdaftar..." value="{{ old('email') }}" required
                                autofocus>
                            <div class="input-group-text bg-white border-start-0">
                                <span class="bi bi-envelope text-muted"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold">
                                <i class="bi bi-envelope-fill me-1"></i> Kirim Link Reset Password
                            </button>
                        </div>
                        <div class="col-6">
                            <button type="reset" class="btn btn-outline-secondary w-100 py-2 fw-semibold">
                                Reset
                            </button>
                        </div>
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

    <script>
        document.getElementById('forgotPasswordForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const form = this;
            const submitBtn = form.querySelector('button[type="submit"]');
            const emailInput = document.getElementById('email');
            const email = emailInput.value.trim();

            if (!email) return;

            // Show loading state
            const originalBtnText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Memproses...';

            // 1. Request reset token & URL from Laravel via AJAX
            fetch("{{ route('password.email') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "Accept": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({ email: email })
            })
            .then(async response => {
                const contentType = response.headers.get("content-type");
                let isJson = contentType && contentType.indexOf("application/json") !== -1;
                
                let data;
                if (isJson) {
                    data = await response.json();
                } else {
                    const text = await response.text();
                    throw new Error(`Server Error (${response.status}): ${text.substring(0, 100)}...`);
                }

                if (!response.ok) {
                    throw new Error(data.message || 'Terjadi kesalahan pada server.');
                }
                return data;
            })
            .then(data => {
                // 2. Tautan reset berhasil didapatkan, sekarang kirim email lewat EmailJS
                const serviceID = "service_3hgb4eu";
                const templateID = "template_keh8x3p";

                // Gunakan template parameters yang disesuaikan agar cocok dengan template bawaan/default EmailJS
                const templateParams = {
                    to_name: data.name,
                    from_name: "Posyandu Locator Support",
                    message: `Silakan klik tautan berikut untuk mengatur ulang kata sandi Anda:\n\n${data.reset_url}\n\nTautan ini akan kedaluwarsa dalam 60 menit.`,
                    reply_to: "noreply@posyandu-locator.com"
                };

                return emailjs.send(serviceID, templateID, templateParams);
            })
            .then(response => {
                // 3. EmailJS berhasil mengirimkan email
                Swal.fire({
                    icon: 'success',
                    title: 'Email Terkirim!',
                    text: 'Tautan reset password berhasil dikirim ke email Anda. Silakan periksa kotak masuk/spam email Anda.',
                    confirmButtonColor: '#0d6efd',
                    confirmButtonText: 'OK'
                }).then(() => {
                    // Redirect to login page
                    window.location.href = "{{ route('login') }}";
                });
            })
            .catch(error => {
                // Handle error
                console.error("Forgot password error:", error);
                
                let errorMsg = 'Gagal mengirim email reset password. Pastikan email terdaftar dan koneksi internet stabil.';
                
                if (error && typeof error === 'object') {
                    if (error.text) {
                        // EmailJS error response
                        errorMsg = `EmailJS Error: ${error.text} (Status: ${error.status})`;
                    } else if (error.message) {
                        // Standard JS/Fetch error
                        errorMsg = error.message;
                    }
                } else if (typeof error === 'string') {
                    errorMsg = error;
                }

                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Mengirim!',
                    text: errorMsg,
                    confirmButtonColor: '#0d6efd',
                    confirmButtonText: 'Coba Lagi'
                });
            })
            .finally(() => {
                // Reset button state
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnText;
            });
        });
    </script>
</body>

</html>
