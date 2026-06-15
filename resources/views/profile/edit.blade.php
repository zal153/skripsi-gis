<x-app title="Profil Saya">
    <main class="app-main">
        <!--begin::App Content Header-->
        <div class="app-content-header">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-6">
                        <h3 class="mb-0">Profil Saya</h3>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-end">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Profil Saya</li>
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
                    <!-- Profile Information Form -->
                    <div class="col-md-6 mb-4">
                        <div class="card card-primary card-outline h-100 shadow-sm">
                            <div class="card-header">
                                <h3 class="card-title mb-0">Informasi Profil</h3>
                            </div>
                            <div class="card-body">
                                @include('profile.partials.update-profile-information-form')
                            </div>
                        </div>
                    </div>

                    <!-- Update Password Form -->
                    <div class="col-md-6 mb-4">
                        <div class="card card-warning card-outline h-100 shadow-sm">
                            <div class="card-header">
                                <h3 class="card-title mb-0">Ubah Password</h3>
                            </div>
                            <div class="card-body">
                                @include('profile.partials.update-password-form')
                            </div>
                        </div>
                    </div>

                    <!-- Delete Account Form -->
                    <div class="col-md-12 mb-4">
                        <div class="card card-danger card-outline shadow-sm">
                            <div class="card-header">
                                <h3 class="card-title mb-0">Hapus Akun</h3>
                            </div>
                            <div class="card-body">
                                @include('profile.partials.delete-user-form')
                            </div>
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
                if (input && icon) {
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
            }
        </script>
    @endpush
</x-app>
