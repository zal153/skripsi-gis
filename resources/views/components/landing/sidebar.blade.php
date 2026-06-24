<!-- SIDEBAR -->
<aside class="sidebar">
    <!-- Mobile Pull Handle -->
    <div class="md:hidden flex justify-center py-1.5 cursor-pointer -mt-2 mb-2" id="mobileDragHandle" onclick="toggleMobileSidebar()">
        <div class="w-12 h-1.5 rounded-full bg-gray-200 hover:bg-gray-300 transition-colors"></div>
    </div>

    <!-- Logo -->
    <div class="flex items-center gap-3 mb-1">
        <div
            class="w-10 h-10 rounded-xl bg-white border border-gray-100 flex items-center justify-center shadow-sm overflow-hidden">
            <img src="{{ asset('assets/img/logo.png') }}" class="w-full h-full object-cover" alt="Logo" />
        </div>
        <div class="flex flex-col">
            <span class="font-bold text-gray-900 text-base leading-tight tracking-tight">Posyandu Locator</span>
            <span class="text-gray-400 text-2xs" style="font-size: 10px;">Kabupaten Jember</span>
        </div>
    </div>

    <hr class="border-gray-100/60" />

    <!-- Actions Area -->
    <div class="flex flex-col gap-2">
        <!-- Cari Terdekat Button (Navy Gradient) -->
        <button class="btn-primary w-full" id="btnCari" onclick="cariTerdekat()">
            <i class="bi bi-search text-sm"></i>
            Cari Terdekat?
        </button>

        <!-- Reset Button (Red Gradient) -->
        <button class="btn-danger w-full" onclick="resetMap()">
            <i class="bi bi-arrow-counterclockwise text-sm"></i>
            Reset
        </button>
    </div>

    <!-- Radius filter -->
    <div class="bg-gray-50 border border-gray-100 rounded-xl p-3">
        <div class="flex justify-between items-center mb-2">
            <label class="text-xs font-semibold text-gray-500 block">Radius Pencarian</label>
        </div>
        <div class="flex items-center gap-3">
            <input type="range" id="radiusSlider" min="1" max="20" value="5"
                class="flex-1 accent-gray-900 cursor-pointer h-1 bg-gray-200 rounded-lg appearance-none"
                oninput="document.getElementById('radiusVal').textContent=this.value; triggerRadiusChange();">
            <span class="text-xs font-extrabold text-gray-800 w-12 text-right"><span id="radiusVal">5</span> km</span>
        </div>
    </div>

    <!-- Transport Mode Selector (Shown only when route is computed) -->
    <div class="hidden flex-col gap-1.5" id="transportSelectorContainer">
        <label class="text-xs font-semibold text-gray-500 block">Mode Perjalanan</label>
        <div class="transport-selector">
            <button class="transport-btn" id="btnModeMobil" onclick="selectTransportMode('car')">
                <i class="bi bi-car-front-fill"></i>
                <span>Mobil</span>
            </button>
            <button class="transport-btn active" id="btnModeMotor" onclick="selectTransportMode('motorcycle')">
                <i class="bi bi-scooter"></i>
                <span>Motor</span>
            </button>
        </div>
    </div>

    <!-- Telemetry Card (Shown only when route is computed) -->
    <div class="telemetry-card hidden border border-gray-100 rounded-xl bg-gray-50/50 p-3" id="telemetryCard">
        <details class="group">
            <summary class="flex items-center justify-between cursor-pointer list-none">
                <div class="flex items-center gap-2">
                    <i class="bi bi-cpu-fill text-gray-500 text-sm"></i>
                    <span class="font-bold text-gray-700 text-xs tracking-wide">DETAIL TELEMETRI</span>
                </div>
                <span class="transition group-open:rotate-180">
                    <i class="bi bi-chevron-down text-gray-400 text-xs"></i>
                </span>
            </summary>
            <div class="grid grid-cols-2 gap-2 text-2xs mt-3 pt-3 border-t border-gray-100">
                <div class="bg-white p-2 rounded-lg border border-gray-100">
                    <span class="text-gray-400 block" style="font-size: 9px;">Visited Nodes</span>
                    <span class="font-bold text-gray-800" id="telVisited">-</span>
                </div>
                <div class="bg-white p-2 rounded-lg border border-gray-100">
                    <span class="text-gray-400 block" style="font-size: 9px;">Dijkstra Runs</span>
                    <span class="font-bold text-gray-800" id="telRuns">-</span>
                </div>
                <div class="bg-white p-2 rounded-lg border border-gray-100">
                    <span class="text-gray-400 block" style="font-size: 9px;">Graph Scale</span>
                    <span class="font-bold text-gray-800" id="telScale">-</span>
                </div>
                <div class="bg-white p-2 rounded-lg border border-gray-100">
                    <span class="text-gray-400 block" style="font-size: 9px;">Snapping Dist</span>
                    <span class="font-bold text-gray-800" id="telSnapping">-</span>
                </div>
                <div
                    class="bg-white p-2 rounded-lg border border-gray-100 col-span-2 flex justify-between items-center">
                    <span class="text-gray-400" style="font-size: 9px;">Backend Latency</span>
                    <span class="font-bold text-gray-800" id="telTime">-</span>
                </div>
            </div>
        </details>
    </div>

    <!-- Results -->
    <div class="flex-1 overflow-y-auto flex flex-col gap-3 pr-1" id="resultContainer">
        <div class="empty-state" id="emptyState">
            <div class="relative mb-2">
                <div
                    class="w-16 h-16 rounded-2xl bg-purple-50 border border-purple-100 flex items-center justify-center shadow-inner">
                    <i class="bi bi-geo text-2xl text-purple-300"></i>
                </div>
            </div>
            <p class="text-sm font-semibold text-gray-700">Hasil Belum Ditemukan</p>
            <p class="text-xs text-gray-400 max-w-[200px] leading-relaxed">Klik "Cari Terdekat?" untuk menemukan
                posyandu di sekitar Anda</p>
        </div>
    </div>

    <!-- Loading -->
    <div id="loadingState"
        class="hidden items-center justify-center gap-2 py-3 bg-purple-50/50 rounded-xl border border-purple-100/30">
        <div class="loading-dots">
            <span></span><span></span><span></span>
        </div>
        <span class="text-xs font-semibold text-purple-600">Mencari lokasi terdekat...</span>
    </div>

    <hr class="border-gray-100/60" />

    <div class="flex gap-2 w-full mt-auto">
        <!-- Pelaporan Button -->
        <button class="feedback-btn flex-1 !py-2.5 flex items-center justify-center gap-1.5" onclick="openModal()">
            <i class="bi bi-exclamation-triangle-fill text-amber-500"></i>
            <span>Pelaporan</span>
        </button>

        <!-- Login Button -->
        <a href="{{ route('login') }}" class="feedback-btn flex-1 !py-2.5 no-underline text-center justify-center flex items-center gap-1.5" style="text-decoration: none;">
            <i class="bi bi-box-arrow-in-right text-purple-500"></i>
            <span>Login</span>
        </a>
    </div>
</aside>
