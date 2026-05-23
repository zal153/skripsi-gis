<x-app title="Dashboard">
    <main class="app-main">
        <!--begin::App Content Header-->
        <div class="app-content-header">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-6">
                        <h3 class="mb-0">Dashboard</h3>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-end">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
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
                    <!--begin::Desa-->
                    <div class="col-lg-3 col-6">
                        <div class="small-box text-bg-info">
                            <div class="inner">
                                <h3>{{ $jumlahDesa }}</h3>
                                <p>Desa</p>
                            </div>
                            <i class="small-box-icon bi bi-geo-alt-fill"></i>
                            <a href="{{ route('desa.index') }}"
                                class="small-box-footer link-dark link-underline-opacity-0 link-underline-opacity-50-hover">
                                Selengkapnya <i class="bi bi-link-45deg"></i>
                            </a>
                        </div>
                    </div>
                    <!--end::Desa-->
                    <!--begin::Posyandu-->
                    <div class="col-lg-3 col-6">
                        <div class="small-box text-bg-success">
                            <div class="inner">
                                <h3>{{ $jumlahPosyandu }}</h3>
                                <p>Posyandu</p>
                            </div>
                            <i class="small-box-icon bi bi-house-heart-fill"></i>
                            <a href="{{ route('posyandu.index') }}"
                                class="small-box-footer link-light link-underline-opacity-0 link-underline-opacity-50-hover">
                                Selengkapnya <i class="bi bi-link-45deg"></i>
                            </a>
                        </div>
                    </div>
                    <!--end::Posyandu-->
                    <!--begin::Data Jalan-->
                    <div class="col-lg-3 col-6">
                        <div class="small-box text-bg-primary">
                            <div class="inner">
                                <h3>{{ $jumlahJalan }}</h3>
                                <p>Data Jalan</p>
                            </div>
                            <i class="small-box-icon bi bi-signpost-split-fill"></i>
                            <a href="{{ route('jalan.index') }}"
                                class="small-box-footer link-light link-underline-opacity-0 link-underline-opacity-50-hover">
                                Selengkapnya <i class="bi bi-link-45deg"></i>
                            </a>
                        </div>
                    </div>
                    <!--end::Data Jalan-->
                    <!--begin::Titik Jalan-->
                    <div class="col-lg-3 col-6">
                        <div class="small-box text-bg-warning">
                            <div class="inner">
                                <h3>{{ $jumlahTitik }}</h3>
                                <p>Titik Jalan</p>
                            </div>
                            <i class="small-box-icon bi bi-pin-map"></i>
                            <a href="{{ route('titik-jalan.index') }}"
                                class="small-box-footer link-dark link-underline-opacity-0 link-underline-opacity-50-hover">
                                Selengkapnya <i class="bi bi-link-45deg"></i>
                            </a>
                        </div>
                    </div>
                    <!--end::Titik Jalan-->
                </div>
            </div>
        </div>
        <!--end::App Content-->
    </main>
</x-app>
