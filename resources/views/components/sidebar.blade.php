<aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">
    <!--begin::Sidebar Brand-->
    <div class="sidebar-brand">
        <!--begin::Brand Link-->
        <a href="{{ route('admin.dashboard') }}" class="brand-link">
            <!--begin::Brand Image-->
            <img src="{{ asset('assets/img/logo.png') }}" alt="Posyandu Locator Logo"
                class="brand-image opacity-75 shadow" />
            <!--end::Brand Image-->
            <!--begin::Brand Text-->
            <span class="brand-text fw-light">Posyandu Locator</span>
            <!--end::Brand Text-->
        </a>
        <!--end::Brand Link-->
    </div>
    <!--end::Sidebar Brand-->
    <!--begin::Sidebar Wrapper-->
    <div class="sidebar-wrapper">
        <nav class="mt-2">
            <!--begin::Sidebar Menu-->
            <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="navigation"
                aria-label="Main navigation" data-accordion="false">
                <li class="nav-item">
                    <a href="{{ route('admin.dashboard') }}"
                        class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <i class="nav-icon bi bi-grid-fill"></i>
                        <p>Dashboard</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('posyandu.index') }}"
                        class="nav-link {{ request()->routeIs('posyandu.index') ? 'active' : '' }}">
                        <i class="nav-icon bi bi-house-heart-fill"></i>
                        <p>Posyandu</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('desa.index') }}"
                        class="nav-link {{ request()->routeIs('desa.index') ? 'active' : '' }}">
                        <i class="nav-icon bi bi-geo-alt-fill"></i>
                        <p>Desa</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('jalan.index') }}"
                        class="nav-link {{ request()->routeIs('jalan.index') ? 'active' : '' }}">
                        <i class="nav-icon bi bi-signpost-split-fill"></i>
                        <p>Data Jalan</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('titik-jalan.index') }}"
                        class="nav-link {{ request()->routeIs('titik-jalan.index') ? 'active' : '' }}">
                        <i class="nav-icon bi bi-pin-map-fill"></i>
                        <p>Titik Jalan</p>
                    </a>
                </li>
                {{-- <li class="nav-item">
                    <a href="{{ route('admin.mae-test') }}"
                        class="nav-link {{ request()->routeIs('admin.mae-test') ? 'active' : '' }}">
                        <i class="nav-icon bi bi-calculator-fill"></i>
                        <p>Pengujian MAE</p>
                    </a>
                </li> --}}
                <li class="nav-item">
                    <a href="{{ route('akun.index') }}"
                        class="nav-link {{ request()->routeIs('admin.akun') ? 'active' : '' }}">
                        <i class="nav-icon bi bi-person-fill"></i>
                        <p>Akun</p>
                    </a>
                </li>
            </ul>
            <!--end::Sidebar Menu-->
        </nav>
    </div>
    <!--end::Sidebar Wrapper-->
</aside>
