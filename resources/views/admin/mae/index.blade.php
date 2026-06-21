<x-app title="Pengujian Model Spasial">
    <main class="app-main">
        <!--begin::App Content Header-->
        <div class="app-content-header">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-6">
                        <h3 class="mb-0">Pengujian & Evaluasi Model Spasial</h3>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-end">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Pengujian Model</li>
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
                    <!-- Table Card -->
                    <div class="col-lg-9 col-md-12">
                        <div class="card mb-4 shadow-sm border-0">
                            <div class="card-header p-0 border-bottom-0 bg-light">
                                <div class="d-flex justify-content-between align-items-center pr-3">
                                    <ul class="nav nav-tabs card-header-tabs m-0 border-bottom-0" id="testingTabs" role="tablist">
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link active fw-bold px-4 py-3 border-0 rounded-0" id="haversine-tab" data-bs-toggle="tab" data-bs-target="#haversine-pane" type="button" role="tab" aria-controls="haversine-pane" aria-selected="true" style="border-bottom: 3px solid transparent !important;">
                                                <i class="bi bi-geo-alt-fill text-purple-600 me-2"></i>Akurasi Jarak (MAE - Haversine)
                                            </button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link fw-bold px-4 py-3 border-0 rounded-0" id="dijkstra-tab" data-bs-toggle="tab" data-bs-target="#dijkstra-pane" type="button" role="tab" aria-controls="dijkstra-pane" aria-selected="false" style="border-bottom: 3px solid transparent !important;">
                                                <i class="bi bi-bezier2 text-primary me-2"></i>Efektivitas Rute (Path Deviation - Dijkstra)
                                            </button>
                                        </li>
                                    </ul>
                                    <span class="badge bg-success me-3"><i class="bi bi-pin-fill me-1"></i>Data Tetap (20 Titik Uji)</span>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="tab-content" id="testingTabContent">
                                    <!-- Haversine Tab -->
                                    <div class="tab-pane fade show active" id="haversine-pane" role="tabpanel" aria-labelledby="haversine-tab">
                                        <div class="alert alert-info border-0 shadow-3xs py-2 px-3 mb-3 d-flex align-items-center" style="font-size: 13px;">
                                            <i class="bi bi-info-circle-fill text-info fs-5 me-2"></i>
                                            <div>
                                                Bandingkan jarak garis lurus dari **Haversine (Sistem)** dengan hasil pengukuran garis lurus Google Maps (klik kanan peta -> **"Ukur Jarak"**).
                                            </div>
                                        </div>

                                        <div class="table-responsive">
                                            <x-table id="haversineTable" :headers="[
                                                'No',
                                                'Titik Awal (A)',
                                                'Posyandu Tujuan (B)',
                                                'Lat/Lng A',
                                                'Lat/Lng B',
                                                'Sistem Haversine (km)',
                                                'Google Maps Lurus (km)',
                                                'Selisih Absolut (km)',
                                                'Ukur',
                                            ]">
                                                @foreach ($testCases as $case)
                                                    <tr data-index="{{ $loop->index }}" data-system-dist="{{ $case['dist_haversine'] }}">
                                                        <td>{{ $loop->iteration }}</td>
                                                        <td class="fw-semibold">{{ $case['start_name'] }}</td>
                                                        <td class="text-purple-700 fw-semibold">{{ $case['end_name'] }}</td>
                                                        <td><span class="badge bg-body-secondary text-secondary border font-monospace">{{ $case['start_lat'] }}, {{ $case['start_lng'] }}</span></td>
                                                        <td><span class="badge bg-body-secondary text-secondary border font-monospace">{{ $case['end_lat'] }}, {{ $case['end_lng'] }}</span></td>
                                                        <td class="fw-bold">{{ number_format($case['dist_haversine'], 4) }}</td>
                                                        <td>
                                                            <input type="number" step="0.0001" min="0"
                                                                class="form-control form-control-sm google-maps-dist haversine-input"
                                                                style="width: 105px; font-weight: 700; border-color: #d1d5db;"
                                                                data-key="mae_haversine_{{ $seed }}_{{ $loop->index }}"
                                                                placeholder="Ketik jarak..." />
                                                        </td>
                                                        <td class="abs-error fw-extrabold text-danger font-monospace">-</td>
                                                        <td>
                                                            <a href="https://www.google.com/maps/dir/?api=1&origin={{ $case['start_lat'] }},{{ $case['start_lng'] }}&destination={{ $case['end_lat'] }},{{ $case['end_lng'] }}"
                                                                target="_blank" class="btn btn-xs btn-outline-primary"
                                                                title="Ukur Jarak Lurus (Klik Kanan peta -> Ukur Jarak)">
                                                                <i class="bi bi-geo-alt-fill"></i> Buka Map
                                                            </a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </x-table>
                                        </div>
                                    </div>

                                    <!-- Dijkstra Tab -->
                                    <div class="tab-pane fade" id="dijkstra-pane" role="tabpanel" aria-labelledby="dijkstra-tab">
                                        <div class="alert alert-info border-0 shadow-3xs py-2 px-3 mb-3 d-flex align-items-center" style="font-size: 13px;">
                                            <i class="bi bi-info-circle-fill text-info fs-5 me-2"></i>
                                            <div>
                                                Bandingkan panjang rute jalan raya dari **Dijkstra (Sistem)** dengan rute berkendara asli di **Google Maps** (Travel mode: Driving).
                                            </div>
                                        </div>

                                        <div class="table-responsive">
                                            <x-table id="dijkstraTable" :headers="[
                                                'No',
                                                'Titik Awal (A)',
                                                'Posyandu Tujuan (B)',
                                                'Lat/Lng A',
                                                'Lat/Lng B',
                                                'Rute Dijkstra (km)',
                                                'Google Maps Rute (km)',
                                                'Path Deviation (km)',
                                                'Ukur',
                                            ]">
                                                @foreach ($testCases as $case)
                                                    <tr data-index="{{ $loop->index }}" data-system-dist="{{ $case['dist_dijkstra'] }}">
                                                        <td>{{ $loop->iteration }}</td>
                                                        <td class="fw-semibold">{{ $case['start_name'] }}</td>
                                                        <td class="text-purple-700 fw-semibold">{{ $case['end_name'] }}</td>
                                                        <td><span class="badge bg-body-secondary text-secondary border font-monospace">{{ $case['start_lat'] }}, {{ $case['start_lng'] }}</span></td>
                                                        <td><span class="badge bg-body-secondary text-secondary border font-monospace">{{ $case['end_lat'] }}, {{ $case['end_lng'] }}</span></td>
                                                        <td class="fw-bold">{{ number_format($case['dist_dijkstra'], 4) }}</td>
                                                        <td>
                                                            <input type="number" step="0.0001" min="0"
                                                                class="form-control form-control-sm google-maps-dist dijkstra-input"
                                                                style="width: 105px; font-weight: 700; border-color: #d1d5db;"
                                                                data-key="path_deviation_dijkstra_{{ $seed }}_{{ $loop->index }}"
                                                                placeholder="Ketik rute..." />
                                                        </td>
                                                        <td class="abs-error fw-extrabold text-danger font-monospace">-</td>
                                                        <td>
                                                            <a href="https://www.google.com/maps/dir/?api=1&origin={{ $case['start_lat'] }},{{ $case['start_lng'] }}&destination={{ $case['end_lat'] }},{{ $case['end_lng'] }}&travelmode=driving"
                                                                target="_blank" class="btn btn-xs btn-outline-primary"
                                                                title="Ukur Rute Jalan Raya di Google Maps">
                                                                <i class="bi bi-geo-alt-fill"></i> Buka Map
                                                            </a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </x-table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Calculator Sidebar Card -->
                    <div class="col-lg-3 col-md-12">
                        <!-- Result Box -->
                        <div class="card mb-4 text-white shadow-sm border-0 bg-gradient-purple"
                            id="resultCard"
                            style="background: linear-gradient(135deg, #7c3aed 0%, #5b21b6 100%); transition: background 0.3s ease, transform 0.2s;">
                            <div class="card-body text-center py-4">
                                <h6 class="text-uppercase tracking-wider text-purple-200 small fw-bold mb-2" id="resultLabel">Hasil Akhir MAE</h6>
                                <div class="display-4 fw-extrabold font-monospace mb-2" id="maeDisplay">0.0000</div>
                                <p class="small text-purple-100 mb-0" id="maeDescription">Silakan masukkan data jarak untuk mulai menghitung.</p>
                            </div>
                        </div>

                        <!-- Actions Box -->
                        <div class="card mb-4 shadow-sm border-0">
                            <div class="card-header py-3">
                                <h3 class="card-title font-bold m-0"><i
                                        class="bi bi-gear-fill text-secondary me-2"></i>Kontrol Pengujian</h3>
                            </div>
                            <div class="card-body d-flex flex-column gap-2">

                                <button type="button" class="btn btn-success btn-sm w-full py-2" id="btnCopyMarkdown">
                                    <i class="bi bi-clipboard-check-fill me-2"></i>Salin Tabel (Markdown)
                                </button>

                                <button type="button" class="btn btn-outline-success btn-sm w-full py-2"
                                    id="btnExportExcel">
                                    <i class="bi bi-file-earmark-excel-fill me-2"></i>Ekspor ke Excel (.xlsx)
                                </button>

                                <button type="button" class="btn btn-outline-danger btn-sm w-full py-2"
                                    id="btnClearCache">
                                    <i class="bi bi-trash-fill me-2"></i>Reset Input Halaman
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--end::App Content-->
    </main>

    <!-- Hidden Textarea for copying Markdown -->
    <textarea id="markdownCopyArea" class="visually-hidden"></textarea>

    <style>
        .nav-tabs .nav-link {
            border: none;
            color: #6b7280;
            background-color: transparent;
            transition: all 0.2s ease;
        }
        .nav-tabs .nav-link:hover {
            color: #374151;
            background-color: #f3f4f6;
        }
        .nav-tabs .nav-link.active#haversine-tab {
            border-bottom: 3px solid #7c3aed !important;
            color: #7c3aed !important;
            background-color: #ffffff;
        }
        .nav-tabs .nav-link.active#dijkstra-tab {
            border-bottom: 3px solid #0d6efd !important;
            color: #0d6efd !important;
            background-color: #ffffff;
        }
    </style>

    @push('scripts')
        <!-- SheetJS Library for Excel Export -->
        <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const testCases = @json($testCases);
                const seed = "{{ $seed }}";
                const copyBtn = document.getElementById('btnCopyMarkdown');
                const excelBtn = document.getElementById('btnExportExcel');
                const clearBtn = document.getElementById('btnClearCache');
                const maeDisplay = document.getElementById('maeDisplay');
                const maeDescription = document.getElementById('maeDescription');
                const copyArea = document.getElementById('markdownCopyArea');
                const resultCard = document.getElementById('resultCard');
                const resultLabel = document.getElementById('resultLabel');

                let activeTab = 'haversine';

                // Listen to tab changes
                document.getElementById('haversine-tab').addEventListener('shown.bs.tab', function () {
                    activeTab = 'haversine';
                    resultLabel.textContent = 'Hasil Akhir MAE';
                    resultCard.className = 'card mb-4 text-white shadow-sm border-0 bg-gradient-purple';
                    resultCard.style.background = 'linear-gradient(135deg, #7c3aed 0%, #5b21b6 100%)';
                    calculateTesting();
                });

                document.getElementById('dijkstra-tab').addEventListener('shown.bs.tab', function () {
                    activeTab = 'dijkstra';
                    resultLabel.textContent = 'Rata-Rata Path Deviation';
                    resultCard.className = 'card mb-4 text-white shadow-sm border-0 bg-primary bg-gradient';
                    resultCard.style.background = 'linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%)';
                    calculateTesting();
                });

                // Function to load values from localStorage and calculate testing results
                function calculateTesting() {
                    let totalAbsError = 0;
                    let validCount = 0;

                    testCases.forEach((caseData, index) => {
                        const cacheKey = activeTab === 'haversine' 
                            ? `mae_haversine_${seed}_${index}` 
                            : `path_deviation_dijkstra_${seed}_${index}`;
                        
                        const cachedVal = localStorage.getItem(cacheKey);
                        const selector = activeTab === 'haversine' ? '.haversine-input' : '.dijkstra-input';
                        const input = document.querySelector(`${selector}[data-key="${cacheKey}"]`);
                        
                        const row = input ? input.closest('tr') : null;
                        const absErrorTd = row ? row.querySelector('.abs-error') : null;

                        if (input) {
                            if (cachedVal !== null && input.value !== cachedVal) {
                                input.value = cachedVal;
                            }
                        }

                        const googleMapsDist = cachedVal !== null ? parseFloat(cachedVal) : NaN;
                        const systemDist = activeTab === 'haversine' ? caseData.dist_haversine : caseData.dist_dijkstra;

                        if (!isNaN(googleMapsDist) && googleMapsDist >= 0) {
                            const error = Math.abs(systemDist - googleMapsDist);
                            if (absErrorTd) {
                                absErrorTd.textContent = error.toFixed(4) + ' km';
                                absErrorTd.className = 'abs-error fw-extrabold text-success font-monospace';
                            }
                            totalAbsError += error;
                            validCount++;
                        } else {
                            if (absErrorTd) {
                                absErrorTd.textContent = '-';
                                absErrorTd.className = 'abs-error fw-extrabold text-danger font-monospace';
                            }
                        }
                    });

                    if (validCount > 0) {
                        const avg = totalAbsError / validCount;
                        maeDisplay.textContent = avg.toFixed(4);
                        if (validCount === testCases.length) {
                            maeDescription.textContent = `Dihitung berdasarkan seluruh ${validCount} titik uji.`;
                        } else {
                            maeDescription.textContent =
                                `Dihitung berdasarkan ${validCount} dari ${testCases.length} titik uji.`;
                        }
                    } else {
                        maeDisplay.textContent = '0.0000';
                        maeDescription.textContent = 'Silakan masukkan data jarak untuk mulai menghitung.';
                    }

                    generateMarkdown();
                }

                // Generate Markdown for clipboard
                function generateMarkdown() {
                    let md = "";
                    if (activeTab === 'haversine') {
                        md = "| No | Titik Awal (A) | Posyandu Tujuan (B) | Lat A, Lng A | Lat B, Lng B | Jarak Haversine (km) | Jarak Google Maps Lurus (km) | Selisih Absolut (km) |\n";
                        md += "|---|---|---|---|---|---|---|---|\n";

                        testCases.forEach((caseData, index) => {
                            const cacheKey = `mae_haversine_${seed}_${index}`;
                            const cachedVal = localStorage.getItem(cacheKey);
                            const distGmaps = cachedVal !== null && cachedVal !== '' ? parseFloat(cachedVal).toFixed(4) : '';

                            let absErr = '';
                            if (distGmaps !== '') {
                                absErr = Math.abs(caseData.dist_haversine - parseFloat(cachedVal)).toFixed(4);
                            }

                            md +=
                                `| ${caseData.no} | ${caseData.start_name} | ${caseData.end_name} | ${caseData.start_lat}, ${caseData.start_lng} | ${caseData.end_lat}, ${caseData.end_lng} | ${caseData.dist_haversine.toFixed(4)} | ${distGmaps} | ${absErr} |\n`;
                        });

                        const validCount = testCases.filter((_, idx) => localStorage.getItem(`mae_haversine_${seed}_${idx}`) !== null).length;
                        if (validCount > 0) {
                            const mae = parseFloat(maeDisplay.textContent);
                            md += `\n**Rata-rata Mean Absolute Error (MAE): ${mae.toFixed(4)} km**\n`;
                        }
                    } else {
                        md = "| No | Titik Awal (A) | Posyandu Tujuan (B) | Lat A, Lng A | Lat B, Lng B | Rute Dijkstra (km) | Google Maps Driving (km) | Path Deviation (km) |\n";
                        md += "|---|---|---|---|---|---|---|---|\n";

                        testCases.forEach((caseData, index) => {
                            const cacheKey = `path_deviation_dijkstra_${seed}_${index}`;
                            const cachedVal = localStorage.getItem(cacheKey);
                            const distGmaps = cachedVal !== null && cachedVal !== '' ? parseFloat(cachedVal).toFixed(4) : '';

                            let absErr = '';
                            if (distGmaps !== '') {
                                absErr = Math.abs(caseData.dist_dijkstra - parseFloat(cachedVal)).toFixed(4);
                            }

                            md +=
                                `| ${caseData.no} | ${caseData.start_name} | ${caseData.end_name} | ${caseData.start_lat}, ${caseData.start_lng} | ${caseData.end_lat}, ${caseData.end_lng} | ${caseData.dist_dijkstra.toFixed(4)} | ${distGmaps} | ${absErr} |\n`;
                        });

                        const validCount = testCases.filter((_, idx) => localStorage.getItem(`path_deviation_dijkstra_${seed}_${idx}`) !== null).length;
                        if (validCount > 0) {
                            const pd = parseFloat(maeDisplay.textContent);
                            md += `\n**Rata-rata Path Deviation (PD): ${pd.toFixed(4)} km**\n`;
                        }
                    }

                    copyArea.value = md;
                }

                // Event delegation for input events (handles SimpleDataTable dynamically generated DOM)
                document.addEventListener('input', function(e) {
                    if (e.target.classList.contains('google-maps-dist')) {
                        const input = e.target;
                        const cacheKey = input.getAttribute('data-key');
                        if (input.value !== '') {
                            localStorage.setItem(cacheKey, input.value);
                        } else {
                            localStorage.removeItem(cacheKey);
                        }
                        calculateTesting();
                    }
                });

                // Sync values and re-run on Table events (SimpleDataTable redraws)
                const tables = ['haversineTable', 'dijkstraTable'];
                tables.forEach(tableId => {
                    const table = document.getElementById(tableId);
                    if (table) {
                        table.addEventListener('click', () => setTimeout(calculateTesting, 50));
                        table.addEventListener('keyup', () => setTimeout(calculateTesting, 50));
                    }
                });

                // Periodically run sync to capture any other UI draw events (pagination/search)
                setInterval(calculateTesting, 800);

                // Initial run
                calculateTesting();

                // Copy to Clipboard
                copyBtn.addEventListener('click', function() {
                    copyArea.select();
                    document.execCommand('copy');

                    const label = activeTab === 'haversine' ? 'MAE (Haversine)' : 'Path Deviation (Dijkstra)';

                    if (window.Swal) {
                        window.Swal.fire({
                            title: 'Tabel Disalin!',
                            text: `Format tabel Markdown ${label} telah disalin ke clipboard untuk dokumen skripsi Anda.`,
                            icon: 'success',
                            confirmButtonText: 'Selesai',
                            confirmButtonColor: activeTab === 'haversine' ? '#7c3aed' : '#0d6efd'
                        });
                    } else {
                        alert('Tabel berhasil disalin ke clipboard!');
                    }
                });

                // Excel Export
                excelBtn.addEventListener('click', function() {
                    const data = [];
                    
                    if (activeTab === 'haversine') {
                        data.push([
                            "No",
                            "Titik Awal (A)",
                            "Posyandu Tujuan (B)",
                            "Lat A",
                            "Lng A",
                            "Lat B",
                            "Lng B",
                            "Jarak Haversine (km)",
                            "Jarak Google Maps Lurus (km)",
                            "Selisih Absolut (km)"
                        ]);

                        testCases.forEach((caseData, index) => {
                            const cacheKey = `mae_haversine_${seed}_${index}`;
                            const cachedVal = localStorage.getItem(cacheKey);
                            const distGmaps = cachedVal !== null && cachedVal !== '' ? parseFloat(cachedVal) : null;

                            let absErr = null;
                            if (distGmaps !== null) {
                                absErr = Math.abs(caseData.dist_haversine - distGmaps);
                            }

                            data.push([
                                caseData.no,
                                caseData.start_name,
                                caseData.end_name,
                                caseData.start_lat,
                                caseData.start_lng,
                                caseData.end_lat,
                                caseData.end_lng,
                                caseData.dist_haversine,
                                distGmaps,
                                absErr
                            ]);
                        });

                        data.push([]);

                        const validCount = testCases.filter((_, idx) => localStorage.getItem(`mae_haversine_${seed}_${idx}`) !== null).length;
                        if (validCount > 0) {
                            const mae = parseFloat(maeDisplay.textContent);
                            data.push(["", "", "", "", "", "", "", "", "Rata-rata MAE:", mae]);
                        }
                    } else {
                        data.push([
                            "No",
                            "Titik Awal (A)",
                            "Posyandu Tujuan (B)",
                            "Lat A",
                            "Lng A",
                            "Lat B",
                            "Lng B",
                            "Rute Dijkstra (km)",
                            "Google Maps Driving (km)",
                            "Path Deviation (km)"
                        ]);

                        testCases.forEach((caseData, index) => {
                            const cacheKey = `path_deviation_dijkstra_${seed}_${index}`;
                            const cachedVal = localStorage.getItem(cacheKey);
                            const distGmaps = cachedVal !== null && cachedVal !== '' ? parseFloat(cachedVal) : null;

                            let absErr = null;
                            if (distGmaps !== null) {
                                absErr = Math.abs(caseData.dist_dijkstra - distGmaps);
                            }

                            data.push([
                                caseData.no,
                                caseData.start_name,
                                caseData.end_name,
                                caseData.start_lat,
                                caseData.start_lng,
                                caseData.end_lat,
                                caseData.end_lng,
                                caseData.dist_dijkstra,
                                distGmaps,
                                absErr
                            ]);
                        });

                        data.push([]);

                        const validCount = testCases.filter((_, idx) => localStorage.getItem(`path_deviation_dijkstra_${seed}_${idx}`) !== null).length;
                        if (validCount > 0) {
                            const pd = parseFloat(maeDisplay.textContent);
                            data.push(["", "", "", "", "", "", "", "", "Rata-rata Path Deviation:", pd]);
                        }
                    }

                    // Create worksheet
                    const ws = XLSX.utils.aoa_to_sheet(data);

                    // Create workbook
                    const wb = XLSX.utils.book_new();
                    XLSX.utils.book_append_sheet(wb, ws, activeTab === 'haversine' ? "Pengujian MAE" : "Path Deviation");

                    // Save workbook
                    const fileName = activeTab === 'haversine' 
                        ? `Pengujian_MAE_Seed_${seed}.xlsx` 
                        : `Pengujian_PathDeviation_Seed_${seed}.xlsx`;
                    XLSX.writeFile(wb, fileName);

                    if (window.Swal) {
                        window.Swal.fire({
                            title: 'Ekspor Berhasil!',
                            text: 'File Excel telah berhasil diunduh.',
                            icon: 'success',
                            confirmButtonText: 'Selesai',
                            confirmButtonColor: '#10b981'
                        });
                    }
                });

                // Clear input cache
                clearBtn.addEventListener('click', function() {
                    const label = activeTab === 'haversine' ? 'MAE (Haversine)' : 'Path Deviation (Dijkstra)';
                    
                    const performReset = () => {
                        testCases.forEach((_, index) => {
                            const cacheKey = activeTab === 'haversine' 
                                ? `mae_haversine_${seed}_${index}` 
                                : `path_deviation_dijkstra_${seed}_${index}`;
                            localStorage.removeItem(cacheKey);
                        });
                        
                        document.querySelectorAll(activeTab === 'haversine' ? '.haversine-input' : '.dijkstra-input').forEach(input => {
                            input.value = '';
                        });
                        
                        calculateTesting();
                    };

                    if (window.Swal) {
                        window.Swal.fire({
                            title: 'Apakah Anda yakin?',
                            text: `Semua data input ${label} untuk seed ini akan dihapus!`,
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#d33',
                            cancelButtonColor: '#3085d6',
                            confirmButtonText: 'Ya, Reset!',
                            cancelButtonText: 'Batal'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                performReset();
                                window.Swal.fire(
                                    'Direset!',
                                    'Input telah dibersihkan.',
                                    'success'
                                );
                            }
                        });
                    } else {
                        if (confirm(`Apakah Anda yakin ingin mereset input ${label}?`)) {
                            performReset();
                        }
                    }
                });
            });
        </script>
    @endpush
</x-app>
