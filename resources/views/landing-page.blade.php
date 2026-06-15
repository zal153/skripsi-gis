<x-landing.layout title="Posyandu Locator">
    <div class="flex flex-col md:flex-row h-screen w-screen overflow-hidden">
        <!-- Sidebar on the Left -->
        <x-landing.sidebar />

        <!-- Map area on the Right -->
        <div class="flex-grow h-full relative">
            <x-landing.map />

            <!-- Map Layer Switcher -->
            <div class="map-layer-switcher" id="layerSwitcher">
                <button class="layer-btn active" data-layer="satelit" onclick="switchMapLayer('satelit')">
                    <i class="bi bi-globe-americas"></i>
                    <span>Satelit</span>
                </button>
                <button class="layer-btn" data-layer="peta" onclick="switchMapLayer('peta')">
                    <i class="bi bi-map-fill"></i>
                    <span>Peta</span>
                </button>
            </div>
        </div>
    </div>

    <x-landing.feedback-modal />

    @push('scripts')
        <script>
            // ─── Data dari Database (Jember, Kecamatan Arjasa) ──────────────────────
            const DEFAULT_CENTER = [-8.1050, 113.7400];
            const posyanduData = @js($posyanduData ?? []);

            // ─── Init Map ─────────────────────────────────────────────────────────────
            let map = null;
            let mapLayers = {};
            let currentLayerKey = 'satelit';

            // ─── Markers & State ──────────────────────────────────────────────────────
            let userMarker = null;
            let userCircle = null;
            let posyanduMarkers = [];
            let routeLayers = [];
            let userLatLng = null;
            let posyanduIcon = null;
            let userIcon = null;
            let destinationMarker = null;
            let currentResults = [];

            let currentTransportMode = 'motorcycle';
            let activePosyanduId = null;
            let currentRouteDest = null;
            let radiusTimeout = null;

            function initializeMap() {
                posyanduIcon = L.divIcon({
                    className: '',
                    html: `<div style="width:14px;height:14px;background:#7c3aed;border:3px solid #fff;border-radius:50%;box-shadow:0 2px 8px rgba(0,0,0,0.3)"></div>`,
                    iconSize: [14, 14],
                    iconAnchor: [7, 7]
                });

                userIcon = L.divIcon({
                    className: '',
                    html: `<div style="position:relative;width:24px;height:24px">
                   <div style="position:absolute;inset:0;background:#7c3aed;opacity:0.25;border-radius:50%;animation:pulse-ring 1.5s ease-out infinite"></div>
                   <div style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);width:14px;height:14px;background:#7c3aed;border:3px solid #fff;border-radius:50%;box-shadow:0 2px 10px rgba(124,58,237,0.5)"></div>
                 </div>`,
                    iconSize: [24, 24],
                    iconAnchor: [12, 12]
                });

                map = L.map('map', {
                    zoomControl: false
                }).setView(DEFAULT_CENTER, 13);

                // Setup Tile Layers
                mapLayers.satelit = L.tileLayer(
                    'https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
                        attribution: 'Tiles © Esri — Source: Esri, Maxar, Earthstar Geographics',
                        maxNativeZoom: 18,
                        maxZoom: 20
                    });

                mapLayers.peta = L.tileLayer(
                    'https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
                        attribution: '© OpenStreetMap contributors © CARTO',
                        subdomains: 'abcd',
                        maxZoom: 20
                    }
                );

                // Default layer
                mapLayers.satelit.addTo(map);

                L.control.zoom({
                    position: 'bottomright'
                }).addTo(map);

                // Create markers but DO NOT add to map yet (hide by default)
                posyanduData.forEach(p => {
                    const m = L.marker([p.lat, p.lng], {
                            icon: posyanduIcon
                        })
                        .bindPopup(
                            `<div class="p-1" style="font-family:'Plus Jakarta Sans',sans-serif;">
                                <b class="text-gray-900 text-sm">${p.nama}</b><br>
                                <span class="text-gray-500 text-xs">${p.alamat}</span>
                            </div>`
                        );

                    m.on('click', () => {
                        selectPosyandu(p.id, p.lat, p.lng, p.nama);
                    });

                    posyanduMarkers.push({
                        data: p,
                        marker: m,
                        visible: false
                    });
                });
            }

            function switchMapLayer(layerKey) {
                if (!mapLayers[layerKey]) return;

                map.removeLayer(mapLayers[currentLayerKey]);
                mapLayers[layerKey].addTo(map);
                currentLayerKey = layerKey;

                document.querySelectorAll('#layerSwitcher .layer-btn').forEach(btn => {
                    if (btn.getAttribute('data-layer') === layerKey) {
                        btn.classList.add('active');
                    } else {
                        btn.classList.remove('active');
                    }
                });
            }

            // ─── Haversine ────────────────────────────────────────────────────────────
            function haversine(lat1, lng1, lat2, lng2) {
                const R = 6371;
                const dLat = (lat2 - lat1) * Math.PI / 180;
                const dLng = (lng2 - lng1) * Math.PI / 180;
                const a = Math.sin(dLat / 2) ** 2 + Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) * Math.sin(
                    dLng / 2) ** 2;
                return R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
            }

            // ─── Debounced Radius Change ──────────────────────────────────────────────
            function triggerRadiusChange() {
                if (!userLatLng) return;
                clearTimeout(radiusTimeout);
                radiusTimeout = setTimeout(() => {
                    processSearch(userLatLng);
                }, 400);
            }

            // ─── Cari Terdekat ────────────────────────────────────────────────────────
            function cariTerdekat() {
                showLoading(true);

                setTimeout(() => {
                    // Menggunakan koordinat acak Kecamatan Arjasa untuk simulasi lokasi pengguna (Demo)
                    const centerLat = -8.1050;
                    const centerLng = 113.7400;

                    const randomLat = centerLat + ((Math.random() - 0.5) * 0.04);
                    const randomLng = centerLng + ((Math.random() - 0.5) * 0.04);

                    userLatLng = [randomLat, randomLng];
                    processSearch(userLatLng, true, false);
                }, 1500); // Jeda 1.5 detik untuk simulasi pengambilan GPS yang realistis saat sidang
            }

            function processSearch(latlng, isFallback = false, shouldShowRoute = false) {
                const radius = parseInt(document.getElementById('radiusSlider').value);

                if (userMarker) map.removeLayer(userMarker);
                if (userCircle) map.removeLayer(userCircle);

                userMarker = L.marker(latlng, {
                        icon: userIcon
                    }).addTo(map)
                    .bindPopup(isFallback ? "📍 Lokasi Perkiraan" : "📍 Lokasi Anda").openPopup();

                userCircle = L.circle(latlng, {
                    radius: radius * 1000,
                    color: '#7c3aed',
                    fillColor: '#7c3aed',
                    fillOpacity: 0.04,
                    weight: 1.5,
                    dashArray: '6,4'
                }).addTo(map);

                map.fitBounds(userCircle.getBounds(), {
                    padding: [40, 40]
                });

                const results = posyanduData
                    .map(p => ({
                        ...p,
                        dist: haversine(latlng[0], latlng[1], p.lat, p.lng)
                    }))
                    .filter(p => p.dist <= radius)
                    .sort((a, b) => a.dist - b.dist);

                currentResults = results;
                routeLayers.forEach(l => map.removeLayer(l));
                routeLayers = [];

                if (shouldShowRoute && results.length > 0) {
                    getRouteToThis(latlng, [results[0].lat, results[0].lng], results[0].id);
                } else {
                    document.getElementById('transportSelectorContainer').classList.add('hidden');
                    document.getElementById('telemetryCard').classList.add('hidden');
                    if (destinationMarker) {
                        map.removeLayer(destinationMarker);
                        destinationMarker = null;
                    }
                    updatePosyanduMarkersOnMap();
                }

                renderResults(results, latlng);
                showLoading(false);
            }

            // ─── Offset Route Coords ──────────────────────────────────────────────────
            function offsetRouteCoords(coords, routeIndex) {
                if (routeIndex === 0) {
                    return coords;
                }

                const offsetDistance = routeIndex * 0.000035;

                return coords.map((coord, index) => {
                    if (index === 0 || index === coords.length - 1) {
                        return coord;
                    }

                    const previousCoord = coords[Math.max(index - 1, 0)];
                    const nextCoord = coords[Math.min(index + 1, coords.length - 1)];
                    const deltaLat = nextCoord[0] - previousCoord[0];
                    const deltaLng = nextCoord[1] - previousCoord[1];
                    const length = Math.sqrt((deltaLat * deltaLat) + (deltaLng * deltaLng));

                    if (length === 0) {
                        return coord;
                    }

                    return [
                        coord[0] + ((-deltaLng / length) * offsetDistance),
                        coord[1] + ((deltaLat / length) * offsetDistance),
                    ];
                });
            }

            // ─── Calculate Duration ───────────────────────────────────────────────────
            function calculateDuration(distanceKm, mode) {
                let speed = 40; // Motorcycle
                if (mode === 'car') speed = 30;
                if (mode === 'walking') speed = 5;

                const timeHours = distanceKm / speed;
                const timeMinutes = Math.round(timeHours * 60);

                if (timeMinutes < 1) {
                    return "1 Menit";
                } else if (timeMinutes < 60) {
                    return `${timeMinutes} Menit`;
                } else {
                    const hours = Math.floor(timeMinutes / 60);
                    const mins = timeMinutes % 60;
                    return mins > 0 ? `${hours} Jam ${mins} Menit` : `${hours} Jam`;
                }
            }

            // ─── Fetch Route ──────────────────────────────────────────────────────────
            async function fetchRoute(from, to, posyanduId) {
                try {
                    const response = await fetch(
                        `/api/route?startLat=${from[0]}&startLng=${from[1]}&endLat=${to[0]}&endLng=${to[1]}&k=3`);
                    const result = await response.json();

                    if (!result.success || !result.routes || result.routes.length === 0) {
                        console.warn('Rute tidak ditemukan dari data jalan database. Menggunakan rute fallback.');
                        drawFallbackStraightLine(from, to);
                        return;
                    }

                    // Display Telemetry Card
                    if (result.telemetry) {
                        const tel = result.telemetry;
                        document.getElementById('telVisited').textContent = Number(tel.dijkstra_visited).toLocaleString(
                            'id-ID');
                        document.getElementById('telRuns').textContent = Number(tel.dijkstra_runs).toLocaleString('id-ID');
                        document.getElementById('telScale').textContent =
                            `${tel.graph_nodes_count} / ${tel.graph_edges_count}`;

                        const startSnapM = (tel.start_snap_distance * 1000).toFixed(0);
                        const endSnapM = (tel.end_snap_distance * 1000).toFixed(0);
                        document.getElementById('telSnapping').textContent = `${startSnapM}m / ${endSnapM}m`;

                        document.getElementById('telTime').textContent = `${Number(result.search_time_ms).toFixed(2)} ms`;

                        document.getElementById('telemetryCard').classList.remove('hidden');
                        document.getElementById('transportSelectorContainer').classList.remove('hidden');
                    }

                    const searchTimeMs = Number(result.search_time_ms ?? 0).toFixed(3);
                    const routeStyles = [{
                            color: '#7c3aed', // Purple
                            weight: 6,
                            opacity: 0.95,
                            dashArray: null
                        },
                        {
                            color: '#3b82f6', // Blue
                            weight: 5,
                            opacity: 0.85,
                            dashArray: '8, 6'
                        },
                        {
                            color: '#10b981', // Emerald
                            weight: 5,
                            opacity: 0.85,
                            dashArray: '3, 6'
                        }
                    ];

                    // Filter out highly similar alternative routes (overlap >= 85%)
                    const filteredRoutes = [];
                    if (result.routes && result.routes.length > 0) {
                        filteredRoutes.push(result.routes[0]); // Always keep the main route

                        for (let k = 1; k < result.routes.length; k++) {
                            const altRoute = result.routes[k];
                            const altKeys = altRoute.path.map(p => `${Number(p.lat).toFixed(5)},${Number(p.lng).toFixed(5)}`);
                            let isDuplicate = false;

                            for (const acceptedRoute of filteredRoutes) {
                                const acceptedKeys = new Set(acceptedRoute.path.map(p => `${Number(p.lat).toFixed(5)},${Number(p.lng).toFixed(5)}`));
                                let sharedCount = 0;
                                altKeys.forEach(key => {
                                    if (acceptedKeys.has(key)) {
                                        sharedCount++;
                                    }
                                });

                                const overlapRatio = sharedCount / Math.max(1, altKeys.length);
                                if (overlapRatio >= 0.50) {
                                    isDuplicate = true;
                                    break;
                                }
                            }

                            if (!isDuplicate) {
                                filteredRoutes.push(altRoute);
                            }
                        }
                    }

                    [...filteredRoutes].reverse().forEach((routeData, drawIndex) => {
                        const routeIndex = filteredRoutes.length - 1 - drawIndex;
                        const isMainRoute = routeIndex === 0;
                        const style = routeStyles[routeIndex] ?? routeStyles[routeStyles.length - 1];
                        const graphCoords = routeData.path.map(titik => [titik.lat, titik.lng]);
                        const coords = offsetRouteCoords([from, ...graphCoords, to], routeIndex);

                        // 1. Snapping Start (Dashed line from origin to first road node)
                        if (coords.length >= 3) {
                            const snapStart = L.polyline([coords[0], coords[1]], {
                                color: style.color,
                                weight: 4,
                                opacity: 0.8,
                                lineCap: 'round',
                                lineJoin: 'round',
                                dashArray: '4, 6'
                            }).addTo(map);
                            routeLayers.push(snapStart);
                        }

                        // 2. Snapping End (Dashed line from last road node to destination)
                        if (coords.length >= 3) {
                            const snapEnd = L.polyline([coords[coords.length - 2], coords[coords.length - 1]], {
                                color: style.color,
                                weight: 4,
                                opacity: 0.8,
                                lineCap: 'round',
                                lineJoin: 'round',
                                dashArray: '4, 6'
                            }).addTo(map);
                            routeLayers.push(snapEnd);
                        }

                        // 3. Main Road Route (Solid for main, dashed for alternative routes)
                        const roadCoords = coords.slice(1, coords.length - 1);
                        if (roadCoords.length > 0) {
                            const line = L.polyline(roadCoords, {
                                color: style.color,
                                weight: style.weight,
                                opacity: style.opacity,
                                lineCap: 'round',
                                lineJoin: 'round',
                                dashArray: isMainRoute ? null : style.dashArray
                            }).addTo(map);

                            line.routeDistance = routeData.distance;
                            line.routeIndex = routeIndex;
                            line.isMainRoute = isMainRoute;
                            line.searchTimeMs = searchTimeMs;

                            const duration = calculateDuration(routeData.distance, currentTransportMode);
                            line.bindPopup(`
                                <div class="p-1" style="font-family:'Plus Jakarta Sans',sans-serif;">
                                    <p class="text-xs text-gray-700 mb-1"><i class="bi bi-geo-alt"></i> Jarak: <b>${routeData.distance.toFixed(2)} km</b></p>
                                    <p class="text-xs text-purple-600 font-semibold mb-0"><i class="bi bi-clock"></i> Durasi: <b>${duration}</b></p>
                                </div>
                            `);

                            routeLayers.push(line);
                        }
                    });

                    // Update active card's duration text
                    if (posyanduId) {
                        const mainDistance = filteredRoutes[0].distance;
                        const badge = document.getElementById(`dist-badge-${posyanduId}`);
                        if (badge) {
                            const durationText = calculateDuration(mainDistance, currentTransportMode);
                            badge.innerHTML =
                                `${mainDistance.toFixed(2)} km <span class="text-gray-300 mx-1">|</span> <span class="duration-text text-purple-700 font-bold">${durationText}</span>`;
                        }
                    }
                } catch (error) {
                    console.error('Error fetching route:', error);
                    drawFallbackStraightLine(from, to);
                }
            }

            function drawFallbackStraightLine(from, to) {
                const distance = haversine(from[0], from[1], to[0], to[1]);
                const duration = calculateDuration(distance, currentTransportMode);
                const coords = [from, to];

                const line = L.polyline(coords, {
                    color: '#7c3aed',
                    weight: 6,
                    opacity: 0.95,
                    lineCap: 'round',
                    lineJoin: 'round'
                }).addTo(map);

                line.routeDistance = distance;
                line.routeIndex = 0;
                line.isMainRoute = true;
                line.searchTimeMs = "0.00";

                line.bindPopup(`
                    <div class="p-1" style="font-family:'Plus Jakarta Sans',sans-serif;">
                        <p class="text-xs text-gray-700 mb-1"><i class="bi bi-geo-alt"></i> Jarak: <b>${distance.toFixed(2)} km</b></p>
                        <p class="text-xs text-purple-600 font-semibold mb-0"><i class="bi bi-clock"></i> Durasi: <b>${duration}</b></p>
                    </div>
                `);

                routeLayers.push(line);

                if (activePosyanduId) {
                    const badge = document.getElementById(`dist-badge-${activePosyanduId}`);
                    if (badge) {
                        badge.innerHTML =
                            `${distance.toFixed(2)} km <span class="text-gray-300 mx-1">|</span> <span class="duration-text text-purple-700 font-bold">${duration}</span>`;
                    }
                }
            }

            // ─── Get Route To This ────────────────────────────────────────────────────
            function getRouteToThis(from, to, posyanduId) {
                activePosyanduId = posyanduId;
                currentRouteDest = to;

                // Re-render to focus layout on selected card (Figma style)
                renderResults(currentResults, userLatLng);

                if (destinationMarker) {
                    map.removeLayer(destinationMarker);
                    destinationMarker = null;
                }

                const destIcon = L.divIcon({
                    className: '',
                    html: `<div class="active-destination-marker" style="margin-top: -12px; margin-left: -12px;">
                        <div class="destination-pin">
                            <div class="destination-icon-inner">
                                <i class="bi bi-hospital-fill"></i>
                            </div>
                        </div>
                    </div>`,
                    iconSize: [32, 32],
                    iconAnchor: [16, 32]
                });

                destinationMarker = L.marker(to, {
                    icon: destIcon
                }).addTo(map);

                const activePosyandu = posyanduData.find(p => p.id === posyanduId);
                if (activePosyandu) {
                    destinationMarker.bindPopup(`
                        <div class="p-1" style="font-family:'Plus Jakarta Sans',sans-serif;">
                            <b class="text-gray-900 text-sm">${activePosyandu.nama}</b><br>
                            <span class="text-gray-500 text-xs">${activePosyandu.alamat}</span>
                        </div>
                    `);
                }

                updatePosyanduMarkersOnMap();

                routeLayers.forEach(l => map.removeLayer(l));
                routeLayers = [];
                fetchRoute(from, to, posyanduId);
            }

            // ─── Select Posyandu ──────────────────────────────────────────────────────
            function selectPosyandu(posyanduId, lat, lng, nama) {
                if (!userLatLng) return;

                getRouteToThis(userLatLng, [lat, lng], posyanduId);

                map.flyTo([lat, lng], 16, {
                    duration: 1.2
                });

                setTimeout(() => {
                    if (destinationMarker) {
                        destinationMarker.openPopup();
                    }
                }, 1200);
            }

            // ─── Update Posyandu Markers On Map ──────────────────────────────────────
            function updatePosyanduMarkersOnMap() {
                posyanduMarkers.forEach(item => {
                    map.removeLayer(item.marker);
                });
                currentResults.forEach(p => {
                    if (p.id !== activePosyanduId) {
                        const pm = posyanduMarkers.find(item => item.data.id === p.id);
                        if (pm) {
                            pm.marker.addTo(map);
                        }
                    }
                });
            }

            // ─── Update Route Popups ──────────────────────────────────────────────────
            function updateRoutePopups() {
                routeLayers.forEach(line => {
                    const duration = calculateDuration(line.routeDistance, currentTransportMode);
                    line.setPopupContent(`
                        <div class="p-1" style="font-family:'Plus Jakarta Sans',sans-serif;">
                            <p class="text-xs text-gray-700 mb-1"><i class="bi bi-geo-alt"></i> Jarak: <b>${line.routeDistance.toFixed(2)} km</b></p>
                            <p class="text-xs text-purple-600 font-semibold mb-0"><i class="bi bi-clock"></i> Durasi: <b>${duration}</b></p>
                        </div>
                    `);
                });
            }

            // ─── Select Transport Mode ────────────────────────────────────────────────
            function selectTransportMode(mode) {
                currentTransportMode = mode;

                document.querySelectorAll('.transport-btn').forEach(btn => {
                    btn.classList.remove('active');
                });

                const btnMotor = document.getElementById('btnModeMotor');
                const btnMobil = document.getElementById('btnModeMobil');
                if (mode === 'motorcycle' && btnMotor) btnMotor.classList.add('active');
                if (mode === 'car' && btnMobil) btnMobil.classList.add('active');

                document.querySelectorAll('.result-card').forEach(card => {
                    const dist = parseFloat(card.getAttribute('data-distance'));
                    const posyanduId = parseInt(card.getAttribute('data-posyandu-id'));
                    const durationSpan = card.querySelector('.duration-text');
                    if (durationSpan) {
                        if (card.classList.contains('active') && routeLayers.length > 0) {
                            const mainLine = routeLayers.find(l => l.isMainRoute);
                            if (mainLine) {
                                durationSpan.textContent = calculateDuration(mainLine.routeDistance,
                                    currentTransportMode);
                                return;
                            }
                        }
                        durationSpan.textContent = calculateDuration(dist, currentTransportMode);
                    }
                });

                updateRoutePopups();
            }

            // ─── Render Results ───────────────────────────────────────────────────────
            function renderResults(results, userLL) {
                const container = document.getElementById('resultContainer');
                const emptyState = document.getElementById('emptyState');

                container.querySelectorAll('.result-card').forEach(el => el.remove());

                // Remove any existing back button
                const existingBack = container.querySelector('.back-to-list-container');
                if (existingBack) existingBack.remove();

                if (results.length === 0) {
                    emptyState.style.display = 'flex';
                    emptyState.innerHTML =
                        `
                        <div class="relative mb-2">
                            <div class="w-16 h-16 rounded-2xl bg-purple-50 border border-purple-100 flex items-center justify-center">
                                <i class="bi bi-geo text-2xl text-purple-300"></i>
                            </div>
                        </div>
                        <p class="text-sm font-semibold text-gray-700">Tidak Ditemukan</p>
                        <p class="text-xs text-gray-400 max-w-[200px] leading-relaxed">Coba perbesar radius pencarian Anda</p>`;
                    return;
                }

                emptyState.style.display = 'none';

                // If a specific posyandu is active (route screen), show only that card and a back button
                let itemsToRender = results;
                if (activePosyanduId !== null) {
                    const backDiv = document.createElement('div');
                    backDiv.className = 'back-to-list-container mb-1';
                    backDiv.innerHTML = `
                        <button onclick="backToList()" class="text-xs font-semibold text-gray-500 hover:text-gray-800 flex items-center gap-1.5 transition-colors">
                            <i class="bi bi-arrow-left"></i> Kembali ke Daftar
                        </button>`;
                    container.appendChild(backDiv);

                    itemsToRender = results.filter(p => p.id === activePosyanduId);
                }

                itemsToRender.forEach((p, i) => {
                    const card = document.createElement('div');
                    card.className = 'result-card slide-in';
                    card.style.animationDelay = `${i * 60}ms`;
                    card.setAttribute('data-distance', p.dist);
                    card.setAttribute('data-posyandu-id', p.id);

                    if (activePosyanduId === p.id) {
                        card.classList.add('active');
                    }

                    const duration = calculateDuration(p.dist, currentTransportMode);

                    card.onclick = () => selectPosyandu(p.id, p.lat, p.lng, p.nama);

                    card.innerHTML = `
                        <div class="result-info flex-1 min-w-0">
                            <p class="font-bold text-gray-800 text-sm truncate">${p.nama}</p>
                            <p class="text-xs text-gray-400 mt-0.5 leading-relaxed truncate" style="max-width: 220px;">${p.alamat}</p>
                            <span class="badge-distance inline-block mt-2" id="dist-badge-${p.id}">
                                ${p.dist.toFixed(1)} km <span class="text-gray-300 mx-1">|</span> <span class="duration-text">${duration}</span>
                            </span>
                        </div>
                        <div class="result-action-btn" title="Rute Aktif">
                            <i class="bi bi-arrow-up-right"></i>
                        </div>`;
                    container.appendChild(card);
                });
            }

            // ─── Back to List View ────────────────────────────────────────────────────
            function backToList() {
                activePosyanduId = null;
                currentRouteDest = null;

                // Hide transport & telemetry
                document.getElementById('transportSelectorContainer').classList.add('hidden');
                document.getElementById('telemetryCard').classList.add('hidden');

                // Remove active destination marker
                if (destinationMarker) {
                    map.removeLayer(destinationMarker);
                    destinationMarker = null;
                }

                // Clear route layers
                routeLayers.forEach(l => map.removeLayer(l));
                routeLayers = [];

                // Show all markers in radius
                updatePosyanduMarkersOnMap();

                // Re-render results (will show all)
                renderResults(currentResults, userLatLng);

                // Zoom to fit search circle
                if (userCircle) {
                    map.fitBounds(userCircle.getBounds(), {
                        padding: [40, 40]
                    });
                }
            }

            function flyToMarker(lat, lng, nama) {
                map.flyTo([lat, lng], 16, {
                    duration: 1.2
                });
                const pm = posyanduMarkers.find(p => p.data.lat === lat && p.data.lng === lng);
                if (pm) setTimeout(() => pm.marker.openPopup(), 1200);
            }

            // ─── Reset ────────────────────────────────────────────────────────────────
            function resetMap() {
                if (userMarker) {
                    map.removeLayer(userMarker);
                    userMarker = null;
                }
                if (userCircle) {
                    map.removeLayer(userCircle);
                    userCircle = null;
                }
                if (destinationMarker) {
                    map.removeLayer(destinationMarker);
                    destinationMarker = null;
                }
                routeLayers.forEach(l => map.removeLayer(l));
                routeLayers = [];
                userLatLng = null;
                activePosyanduId = null;
                currentRouteDest = null;
                currentResults = [];

                // Hide transport & telemetry
                document.getElementById('transportSelectorContainer').classList.add('hidden');
                document.getElementById('telemetryCard').classList.add('hidden');

                // Remove result markers from map
                posyanduMarkers.forEach(item => {
                    map.removeLayer(item.marker);
                });

                const container = document.getElementById('resultContainer');
                container.querySelectorAll('.result-card').forEach(el => el.remove());

                const emptyState = document.getElementById('emptyState');
                emptyState.style.display = 'flex';
                emptyState.innerHTML =
                    `
                    <div class="relative mb-2">
                        <div class="w-16 h-16 rounded-2xl bg-purple-50 border border-purple-100 flex items-center justify-center shadow-inner">
                            <i class="bi bi-geo text-2xl text-purple-300"></i>
                        </div>
                    </div>
                    <p class="text-sm font-semibold text-gray-700">Hasil Belum Ditemukan</p>
                    <p class="text-xs text-gray-400 max-w-[200px] leading-relaxed">Klik "Cari Terdekat?" untuk menemukan posyandu di sekitar Anda</p>`;

                map.setView(DEFAULT_CENTER, 13, {
                    animate: true,
                    duration: 1
                });
            }

            // ─── Loading ──────────────────────────────────────────────────────────────
            function showLoading(show) {
                const el = document.getElementById('loadingState');
                const btn = document.getElementById('btnCari');
                el.style.display = show ? 'flex' : 'none';
                btn.disabled = show;
                btn.style.opacity = show ? '0.6' : '1';
            }

            // ─── Modal & Reports Tab ──────────────────────────────────────────────────
            let currentModalTab = 'form';

            function openModal() {
                document.getElementById('modalOverlay').classList.add('active');
                updateReportCountBadge();
            }

            function closeModal() {
                document.getElementById('modalOverlay').classList.remove('active');
                setTimeout(() => {
                    resetReportModalState();
                    switchModalTab('form');
                }, 200);
            }

            document.getElementById('modalOverlay').addEventListener('click', function(e) {
                if (e.target === this) closeModal();
            });

            function switchModalTab(tab) {
                currentModalTab = tab;
                const tabFormBtn = document.getElementById('tabFormBtn');
                const tabListBtn = document.getElementById('tabListBtn');
                const tabFormContainer = document.getElementById('tabFormContainer');
                const tabListContainer = document.getElementById('tabListContainer');

                if (tab === 'form') {
                    tabFormBtn.className = "flex-1 pb-2 text-center text-xs font-bold text-purple-600 border-b-2 border-purple-500 focus:outline-none transition-all";
                    tabListBtn.className = "flex-1 pb-2 text-center text-xs font-bold text-gray-400 border-b-2 border-transparent hover:text-gray-600 focus:outline-none transition-all";
                    
                    tabFormContainer.classList.remove('hidden');
                    tabListContainer.classList.add('hidden');
                } else {
                    tabFormBtn.className = "flex-1 pb-2 text-center text-xs font-bold text-gray-400 border-b-2 border-transparent hover:text-gray-600 focus:outline-none transition-all";
                    tabListBtn.className = "flex-1 pb-2 text-center text-xs font-bold text-purple-600 border-b-2 border-purple-500 focus:outline-none transition-all";
                    
                    tabFormContainer.classList.add('hidden');
                    tabListContainer.classList.remove('hidden');
                    
                    fetchReports();
                }
            }

            async function updateReportCountBadge() {
                try {
                    const response = await fetch('/api/v1/laporan');
                    const json = await response.json();
                    if (json.success) {
                        const badge = document.getElementById('reportCountBadge');
                        if (badge) badge.textContent = json.data.length;
                    }
                } catch (error) {
                    console.error('Gagal memperbarui jumlah laporan:', error);
                }
            }

            async function fetchReports() {
                const loading = document.getElementById('listLoadingState');
                const empty = document.getElementById('listEmptyState');
                const content = document.getElementById('reportsListContent');

                loading.classList.remove('hidden');
                empty.classList.add('hidden');
                content.innerHTML = '';

                try {
                    const response = await fetch('/api/v1/laporan');
                    const json = await response.json();
                    
                    loading.classList.add('hidden');
                    
                    if (!json.success || json.data.length === 0) {
                        empty.classList.remove('hidden');
                        const badge = document.getElementById('reportCountBadge');
                        if (badge) badge.textContent = '0';
                        return;
                    }

                    const badge = document.getElementById('reportCountBadge');
                    if (badge) badge.textContent = json.data.length;

                    json.data.forEach(report => {
                        const reportItem = document.createElement('div');
                        reportItem.className = "border-b border-gray-100/60 pb-3 mb-3 last:border-0 last:pb-0";

                        let repliesHtml = '';
                        if (report.balasans && report.balasans.length > 0) {
                            repliesHtml = `<div class="ml-8 mt-2.5 flex flex-col gap-2">`;
                            report.balasans.forEach(reply => {
                                repliesHtml += `
                                    <div class="flex items-start gap-2 bg-gray-50/70 p-2 rounded-xl border border-gray-100/50">
                                        <div class="w-6 h-6 rounded-full bg-emerald-50 border border-emerald-100 flex items-center justify-center flex-shrink-0">
                                            <i class="bi bi-patch-check-fill text-emerald-500 text-[10px]"></i>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center justify-between gap-2">
                                                <span class="font-bold text-emerald-700 text-[10px] flex items-center gap-0.5">
                                                    ${reply.admin_name}
                                                    <span class="bg-emerald-100 text-emerald-700 rounded px-1 text-[8px] font-extrabold uppercase scale-90">Petugas</span>
                                                </span>
                                                <span class="text-gray-400 text-[9px] flex-shrink-0">${reply.time_ago}</span>
                                            </div>
                                            <p class="text-[11px] text-gray-600 leading-normal mt-0.5">${reply.pesan}</p>
                                        </div>
                                    </div>
                                `;
                            });
                            repliesHtml += `</div>`;
                        }

                        const noteHtml = report.keterangan 
                            ? `<p class="text-[11px] text-gray-400 leading-normal mt-0.5 italic">"${report.keterangan}"</p>` 
                            : '';

                        reportItem.innerHTML = `
                            <div class="flex items-start gap-2.5">
                                <div class="w-8 h-8 rounded-full bg-purple-50 flex items-center justify-center flex-shrink-0 border border-purple-100">
                                    <i class="bi bi-person text-purple-500 text-sm"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between gap-2">
                                        <span class="font-bold text-gray-900 text-xs truncate">Masyarakat Jember</span>
                                        <span class="text-gray-400 text-[9px] flex-shrink-0">${report.time_ago}</span>
                                    </div>
                                    <p class="text-xs font-semibold text-purple-600 mt-0.5">${report.nama_posyandu}</p>
                                    <p class="text-[11px] text-gray-500 leading-normal mt-0.5 font-medium">Alamat: ${report.alamat}</p>
                                    ${noteHtml}
                                </div>
                            </div>
                            ${repliesHtml}
                        `;
                        content.appendChild(reportItem);
                    });

                } catch (error) {
                    loading.classList.add('hidden');
                    empty.classList.remove('hidden');
                    console.error('Gagal mengambil laporan:', error);
                }
            }

            async function submitReport(event) {
                event.preventDefault();
                const form = document.getElementById('reportForm');
                const nama = document.getElementById('report_nama').value;
                const alamat = document.getElementById('report_alamat').value;
                const keterangan = document.getElementById('report_keterangan').value;
                const token = form.querySelector('input[name="_token"]').value;

                try {
                    const response = await fetch('/api/v1/laporan', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': token,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            nama_posyandu: nama,
                            alamat: alamat,
                            keterangan: keterangan
                        })
                    });

                    const json = await response.json();

                    if (json.success) {
                        document.getElementById('reportFormContent').classList.add('hidden');
                        document.getElementById('reportSuccessContainer').classList.remove('hidden');
                        updateReportCountBadge();
                    } else {
                        alert('Gagal mengirim laporan. Silakan coba lagi.');
                    }
                } catch (error) {
                    console.error('Error submitting report:', error);
                    alert('Terjadi kesalahan koneksi. Silakan coba lagi.');
                }
            }

            function resetReportModal() {
                document.getElementById('reportForm').reset();
                resetReportModalState();
                closeModal();
            }

            function resetReportModalState() {
                document.getElementById('reportFormContent').classList.remove('hidden');
                document.getElementById('reportSuccessContainer').classList.add('hidden');
            }

            function bootMapWhenReady() {
                if (typeof window.L === 'undefined') {
                    console.error('Leaflet belum termuat. Pastikan Vite asset sudah jalan.');
                    return;
                }

                initializeMap();
            }

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', bootMapWhenReady);
            } else {
                bootMapWhenReady();
            }
        </script>
    @endpush
</x-landing.layout>
