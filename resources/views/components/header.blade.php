@props(['title' => 'Dashboard'])

<nav class="app-header navbar navbar-expand bg-body">
    <!--begin::Container-->
    <div class="container-fluid">
        <!--begin::Start Navbar Links-->
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button">
                    <i class="bi bi-list"></i>
                </a>
            </li>
            <li class="nav-item d-none d-md-block">
                <span class="nav-link fw-bold">{{ $title ?? 'Dashboard' }}</span>
            </li>
        </ul>
        <!--end::Start Navbar Links-->
        <!--begin::End Navbar Links-->
        <ul class="navbar-nav ms-auto">
            <!-- Theme Toggle Button -->
            <li class="nav-item me-2">
                <button class="nav-link btn btn-link border-0 px-2" id="themeToggleBtn" title="Ubah Tema"
                    style="padding-top: 8px;">
                    <i class="bi bi-moon-fill" id="themeToggleIcon" style="font-size: 1.1rem;"></i>
                </button>
            </li>
            <!--begin::User Menu Dropdown-->
            <li class="nav-item dropdown user-menu">
                <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                    <img src="{{ asset('assets/img/user2-160x160.jpg') }}" class="user-image rounded-circle shadow"
                        alt="User Image" />
                </a>
                <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-3" style="min-width: 160px; padding: 6px 0;">
                    <li>
                        <a href="{{ route('profile.edit') }}" class="dropdown-item py-2 d-flex align-items-center gap-2 text-dark">
                            <i class="bi bi-person-fill text-muted"></i>
                            <span>Profile</span>
                        </a>
                    </li>
                    <li>
                        <hr class="dropdown-divider my-1 border-light-subtle">
                    </li>
                    <li>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                        <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                            class="dropdown-item py-2 d-flex align-items-center gap-2 text-danger">
                            <i class="bi bi-box-arrow-right text-danger"></i>
                            <span>Logout</span>
                        </a>
                    </li>
                </ul>
            </li>
            <!--end::User Menu Dropdown-->
        </ul>
        <!--end::End Navbar Links-->
    </div>
    <!--end::Container-->
</nav>
