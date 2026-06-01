@props(['title' => 'Dashboard'])

<!doctype html>
<html lang="id">

<head>
    <script>
        // Set theme based on preference or system
        (function() {
            const getPreferredTheme = () => {
                const storedTheme = localStorage.getItem('theme');
                if (storedTheme) {
                    return storedTheme;
                }
                return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
            };

            const setTheme = theme => {
                document.documentElement.setAttribute('data-bs-theme', theme);
            };

            // Set initial theme immediately to avoid layout flash
            setTheme(getPreferredTheme());
        })();
    </script>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>{{ $title }} | Posyandu Locator</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes" />
    <meta name="color-scheme" content="light dark" />
    <meta name="theme-color" content="#007bff" media="(prefers-color-scheme: light)" />
    <meta name="theme-color" content="#1a1a1a" media="(prefers-color-scheme: dark)" />
    <meta name="description" content="Posyandu Locator - Sistem Informasi Geografis Posyandu" />
    <!--begin::Fonts-->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.0.12/index.css"
        integrity="sha256-tXJfXfp6Ewt1ilPzLDtQnJV4hclT9XuaZUKyUvmyr+Q=" crossorigin="anonymous" />
    <!--end::Fonts-->
    <!--begin::Third Party Plugin(OverlayScrollbars)-->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.11.0/styles/overlayscrollbars.min.css"
        crossorigin="anonymous" />
    <!--end::Third Party Plugin(OverlayScrollbars)-->
    <!--begin::Third Party Plugin(Bootstrap Icons)-->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css"
        crossorigin="anonymous" />
    <!--end::Third Party Plugin(Bootstrap Icons)-->
    <!--begin::Required Plugin(AdminLTE)-->
    <link rel="stylesheet" href="{{ asset('assets/css/adminlte.css') }}" />
    <!--end::Required Plugin(AdminLTE)-->

    <link rel="icon" type="image/png" href="{{ asset('assets/img/logo.png') }}" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')

    @include('sweetalert2::index')
</head>

<body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
    <!--begin::App Wrapper-->
    <div class="app-wrapper">
        <x-header :title="$title" />
        <x-sidebar />
        {{ $slot }}
        <x-footer />
    </div>
    <!--end::App Wrapper-->

    <!-- Alert Component -->
    <x-alert />

    <!--begin::Required Scripts-->
    <script src="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.11.0/browser/overlayscrollbars.browser.es6.min.js"
        crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.min.js" crossorigin="anonymous"></script>
    <script src="{{ asset('assets/js/adminlte.js') }}"></script>
    <!--end::Required Scripts-->

    <!--begin::OverlayScrollbars Configure-->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarWrapper = document.querySelector('.sidebar-wrapper');
            if (sidebarWrapper && typeof OverlayScrollbarsGlobal !== 'undefined') {
                OverlayScrollbarsGlobal.OverlayScrollbars(sidebarWrapper, {
                    scrollbars: {
                        theme: 'os-theme-light',
                        autoHide: 'leave',
                        clickScroll: true,
                    },
                });
            }
        });
    </script>
    <!--end::OverlayScrollbars Configure-->

    <!-- Theme Toggle Handler -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const themeToggleBtn = document.getElementById('themeToggleBtn');
            const themeToggleIcon = document.getElementById('themeToggleIcon');

            if (!themeToggleBtn) return;

            const getTheme = () => {
                return document.documentElement.getAttribute('data-bs-theme') || 'light';
            };

            const updateToggleIcon = (theme) => {
                if (theme === 'dark') {
                    themeToggleIcon.className = 'bi bi-sun-fill text-warning';
                } else {
                    themeToggleIcon.className = 'bi bi-moon-fill';
                }
            };

            // Set initial icon
            updateToggleIcon(getTheme());

            // Handle button click
            themeToggleBtn.addEventListener('click', () => {
                const currentTheme = getTheme();
                const newTheme = currentTheme === 'dark' ? 'light' : 'dark';

                document.documentElement.setAttribute('data-bs-theme', newTheme);
                localStorage.setItem('theme', newTheme);
                updateToggleIcon(newTheme);
            });

            // Listen for system changes if no manual preference is set
            window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
                if (!localStorage.getItem('theme')) {
                    const systemTheme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' :
                        'light';
                    document.documentElement.setAttribute('data-bs-theme', systemTheme);
                    updateToggleIcon(systemTheme);
                }
            });
        });
    </script>

    @stack('scripts')
</body>

</html>
