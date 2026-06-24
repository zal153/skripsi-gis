<x-app title="Edit Jalan">
    <main class="app-main">
        <div class="app-content-header">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-6">
                        <h3 class="mb-0">Edit Jalan</h3>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-end">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('jalan.index') }}">Jalan</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Edit Jalan</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <div class="app-content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card card-primary card-outline mb-4">
                            <div class="card-header">
                                <div class="card-title">Form Edit Jalan</div>
                            </div>
                            <form action="{{ route('jalan.update', $jalan->id) }}" method="POST" id="form-edit-jalan">
                                @csrf
                                @method('PUT')
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="titik_awal_id" class="form-label">Titik Awal <span
                                                class="text-danger">*</span></label>
                                        <select name="titik_awal_id" id="titik_awal_id"
                                            class="form-control @error('titik_awal_id') is-invalid @enderror" required>
                                            <option value="">-- Pilih Titik Awal --</option>
                                            @foreach ($titikJalan as $item)
                                                <option value="{{ $item->id }}"
                                                    {{ old('titik_awal_id', $jalan->titik_awal_id) == $item->id ? 'selected' : '' }}>
                                                    {{ $item->nama_titik }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('titik_awal_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="titik_akhir_id" class="form-label">Titik Akhir <span
                                                class="text-danger">*</span></label>
                                        <select name="titik_akhir_id" id="titik_akhir_id"
                                            class="form-control @error('titik_akhir_id') is-invalid @enderror" required>
                                            <option value="">-- Pilih Titik Akhir --</option>
                                            @foreach ($titikJalan as $item)
                                                <option value="{{ $item->id }}"
                                                    {{ old('titik_akhir_id', $jalan->titik_akhir_id) == $item->id ? 'selected' : '' }}>
                                                    {{ $item->nama_titik }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('titik_akhir_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="alert alert-info mb-0" role="alert">
                                        Jarak saat ini: {{ number_format($jalan->jarak, 3) }} km. Nilai akan dihitung
                                        ulang otomatis berdasarkan titik awal dan titik akhir.
                                    <div class="text-danger small mt-3 mb-0">* Wajib diisi</div>
                                </div>
                                <div class="card-footer">
                                    <button type="submit" class="btn btn-primary">Simpan</button>
                                    <button type="reset" class="btn btn-outline-secondary">Reset</button>
                                    <a href="{{ route('jalan.index') }}" class="btn btn-danger">Batal</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    @push('scripts')
        <script type="module">
            document.addEventListener('DOMContentLoaded', function() {
                const form = document.getElementById('form-edit-jalan');
                const titikAwal = document.getElementById('titik_awal_id');
                const titikAkhir = document.getElementById('titik_akhir_id');
                const editId = {{ $jalan->id }};
                // Ambil semua data selain id yang diedit saat ini
                const existingJalan = {!! json_encode(
                    \App\Models\Jalan::where('id', '!=', $jalan->id)->select('titik_awal_id', 'titik_akhir_id')->get(),
                ) !!};
                const titikData = {!! json_encode(\App\Models\TitikJalan::select('id', 'latitude', 'longitude')->get()->keyBy('id')) !!};

                function calculateHaversine(lat1, lon1, lat2, lon2) {
                    const R = 6371.0; // Radius bumi dalam kilometer
                    const dLat = (lat2 - lat1) * Math.PI / 180;
                    const dLon = (lon2 - lon1) * Math.PI / 180;
                    const a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                        Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                        Math.sin(dLon / 2) * Math.sin(dLon / 2);
                    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
                    return R * c;
                }

                form.addEventListener('submit', function(e) {
                    const awalVal = parseInt(titikAwal.value);
                    const akhirVal = parseInt(titikAkhir.value);

                    if (!awalVal || !akhirVal) return;

                    // Validasi Jarak Haversine
                    const maxDistance = 5; // Batas maksimal dalam kilometer
                    const tAwal = titikData[awalVal];
                    const tAkhir = titikData[akhirVal];

                    if (tAwal && tAkhir) {
                        const distance = calculateHaversine(
                            parseFloat(tAwal.latitude),
                            parseFloat(tAwal.longitude),
                            parseFloat(tAkhir.latitude),
                            parseFloat(tAkhir.longitude)
                        );

                        if (distance > maxDistance) {
                            e.preventDefault();
                            Swal.fire({
                                icon: 'error',
                                title: 'Jarak Terlalu Jauh!',
                                text: `Jarak antar titik dalah ${distance.toFixed(2)} km. Batas maksimal yang diizinkan adalah ${maxDistance} km.`,
                                confirmButtonColor: '#d33',
                            });
                            return;
                        }
                    }

                    // Cek duplikat pakai JS
                    const isDuplicate = existingJalan.some(jalan =>
                        jalan.titik_awal_id === awalVal && jalan.titik_akhir_id === akhirVal
                    );

                    if (isDuplicate) {
                        e.preventDefault();
                        Swal.fire({
                            icon: 'error',
                            title: 'Validasi Gagal!',
                            text: 'Rute jalan dari titik tersebut ke titik akhir sudah ada.',
                            confirmButtonColor: '#d33',
                        });
                    }
                });
            });
        </script>
    @endpush
</x-app>
