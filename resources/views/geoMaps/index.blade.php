@extends('layouts.partial.app')

@section('title', 'BSPS Verval - Geo Maps')
@section('title_header', 'Geo Maps Sebaran RTLH')
@section('subtitle_header', 'Peta Geotagging Persebaran Titik Survei Lapangan & Calon Penerima Bantuan BSPS')

@push('styles')
    <!-- Leaflet CSS & Plugins -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/leaflet.fullscreen@2.0.0/Control.FullScreen.css" />

    <style>
        /* ============================================================
           PAGE STYLES: GEO MAPS (GOOGLE MAPS PURE SYSTEM)
           ============================================================ */
        .breadcrumb {
            font-size: 13px;
            color: var(--text-muted);
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .breadcrumb a { color: var(--primary); text-decoration: none; font-weight: 500; }
        .breadcrumb a:hover { color: var(--secondary); }

        /* Filter Map Bar */
        .map-filter-bar {
            background: var(--bg-card);
            border-radius: var(--radius);
            padding: 16px 24px;
            box-shadow: var(--shadow-sm);
            border: 1px solid rgba(0, 40, 85, 0.06);
            margin-bottom: 20px;
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
        }
        .map-filter-bar .filter-left { display: flex; flex-wrap: wrap; align-items: center; gap: 14px; }
        .map-filter-bar .filter-item { display: flex; align-items: center; gap: 8px; }
        .map-filter-bar .filter-item label { font-size: 13px; font-weight: 600; color: var(--text-secondary); }
        .map-filter-bar .filter-item select,
        .map-filter-bar .filter-item input {
            padding: 8px 14px;
            border-radius: var(--radius-sm);
            border: 1px solid rgba(0, 40, 85, 0.12);
            font-family: inherit;
            font-size: 13px;
            background: var(--bg-body);
            color: var(--text-primary);
            outline: none;
            transition: var(--transition);
        }
        .map-filter-bar .filter-item select:focus,
        .map-filter-bar .filter-item input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(0, 40, 85, 0.08);
            background: var(--bg-card);
        }
        .map-filter-bar .filter-right { display: flex; gap: 10px; }
        .map-filter-bar .btn {
            padding: 8px 18px;
            border-radius: var(--radius-sm);
            border: none;
            font-family: inherit;
            font-weight: 600;
            font-size: 13px;
            cursor: pointer;
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .map-filter-bar .btn-primary { background: var(--primary); color: #fff; }
        .map-filter-bar .btn-outline { background: transparent; color: var(--text-secondary); border: 1px solid rgba(0, 40, 85, 0.12); }

        /* Map Container Layout */
        .map-container-layout {
            display: grid;
            grid-template-columns: 1fr 340px;
            gap: 20px;
            margin-bottom: 28px;
        }

        .map-wrapper-card {
            background: var(--bg-card);
            border-radius: var(--radius);
            box-shadow: var(--shadow-sm);
            border: 1px solid rgba(0, 40, 85, 0.06);
            overflow: hidden;
            position: relative;
        }

        .map-header {
            padding: 14px 20px;
            border-bottom: 1px solid rgba(0, 40, 85, 0.06);
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: var(--bg-card);
        }
        .map-header h3 { font-size: 15px; font-weight: 700; color: var(--text-primary); }

        .layer-toggle { display: flex; background: var(--bg-body); padding: 3px; border-radius: var(--radius-sm); gap: 2px; }
        .layer-toggle .layer-btn {
            padding: 6px 14px;
            border-radius: 6px;
            border: none;
            background: transparent;
            font-family: inherit;
            font-size: 12px;
            font-weight: 600;
            color: var(--text-secondary);
            cursor: pointer;
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .layer-toggle .layer-btn.active { background: var(--primary); color: #fff; }

        #map { width: 100%; height: 580px; z-index: 1; }

        /* Google Maps Night Mode CSS Filter for Leaflet Tile Pane */
        html[data-theme="dark"] #map .leaflet-tile-pane,
        #map.google-night-mode .leaflet-tile-pane {
            filter: brightness(0.65) invert(1) hue-rotate(180deg) saturate(1.3) contrast(1.1);
        }

        html[data-theme="dark"] #map .leaflet-marker-pane,
        html[data-theme="dark"] #map .leaflet-popup-pane,
        html[data-theme="dark"] #map .leaflet-control-container,
        #map.google-night-mode .leaflet-marker-pane,
        #map.google-night-mode .leaflet-popup-pane,
        #map.google-night-mode .leaflet-control-container {
            filter: none !important;
        }

        /* Sidebar Marker Info */
        .map-sidebar { display: flex; flex-direction: column; gap: 16px; }
        .map-sidebar .card { background: var(--bg-card); border-radius: var(--radius); border: 1px solid rgba(0, 40, 85, 0.06); padding: 18px 20px; box-shadow: var(--shadow-sm); }
        .map-sidebar .card-header { padding-bottom: 12px; margin-bottom: 12px; border-bottom: 1px solid rgba(0, 40, 85, 0.06); display: flex; justify-content: space-between; align-items: center; }
        .map-sidebar .card-header h3 { font-size: 15px; font-weight: 700; color: var(--text-primary); }

        .marker-list { max-height: 480px; overflow-y: auto; display: flex; flex-direction: column; gap: 8px; }
        .marker-item { padding: 10px 12px; border-radius: var(--radius-sm); border: 1px solid rgba(0, 40, 85, 0.06); cursor: pointer; transition: var(--transition); display: flex; align-items: center; gap: 10px; }
        .marker-item:hover { background: rgba(0, 40, 85, 0.04); border-color: var(--primary); }
        .marker-item .marker-dot { width: 12px; height: 12px; border-radius: 50%; flex-shrink: 0; }
        .marker-item .marker-dot.green { background: var(--success); }
        .marker-item .marker-dot.orange { background: var(--warning); }
        .marker-item .marker-dot.blue { background: var(--primary); }
        .marker-item .marker-dot.purple { background: var(--purple); }
        .marker-item .marker-dot.red { background: var(--danger); }

        .marker-item .marker-info { flex: 1; }
        .marker-item .marker-info .name { font-size: 13px; font-weight: 600; color: var(--text-primary); }
        .marker-item .marker-info .location { font-size: 11px; color: var(--text-muted); }

        /* Responsive Geo Maps Layout */
        @media (max-width: 1024px) {
            .map-container-layout { grid-template-columns: 1fr; }
            .map-filter-bar { padding: 16px; flex-direction: column; align-items: stretch; gap: 12px; }
            .map-filter-bar .filter-left { flex-direction: column; width: 100%; gap: 10px; }
            .map-filter-bar .filter-item { width: 100%; }
            .map-filter-bar .filter-item .pupr-dropdown-wrapper,
            .map-filter-bar .filter-item .pupr-dropdown-toggle { width: 100%; justify-content: space-between; }
            .map-filter-bar .filter-right { width: 100%; }
            .map-filter-bar .filter-right .btn { flex: 1; justify-content: center; }
            .table-card .table-header { flex-direction: column; align-items: stretch; gap: 12px; }
            .table-card .table-header > div { max-width: 100% !important; }
        }

        @media (max-width: 768px) {
            .map-header { flex-direction: column; align-items: stretch; gap: 10px; }
            .map-header h3 { font-size: 14px; line-height: 1.4; }
            .layer-toggle { flex-wrap: wrap; width: 100%; }
            .layer-toggle .layer-btn { flex: 1; justify-content: center; min-width: 110px; }
            #map { height: 420px !important; }
            .table-wrapper {
                overflow-x: auto !important;
                overflow-y: hidden !important;
                -webkit-overflow-scrolling: touch !important;
                touch-action: pan-x pan-y !important;
                overscroll-behavior-x: contain !important;
                transform: translateZ(0);
                width: 100% !important;
                display: block !important;
            }
            .table-card table {
                width: 100% !important;
                min-width: 950px !important;
                border-collapse: collapse;
                font-size: 13.5px;
                white-space: nowrap !important;
            }
            .table-card table tr,
            .table-card table th,
            .table-card table td {
                transition: none !important;
                white-space: nowrap !important;
            }
        }

        @media (max-width: 480px) {
            .dashboard-content { padding: 12px; }
            #map { height: 360px !important; }
            .layer-toggle .layer-btn { padding: 6px 8px; font-size: 11px; }
        }
    </style>
@endpush

@section('content')
    <!-- Navbar Component per Halaman -->
    @include('layouts.navbar')

    <main class="dashboard-content">
        <!-- Breadcrumb -->
        <div class="breadcrumb">
            <a href="{{ url('/') }}"><i class="fas fa-home"></i> Home</a>
            <i class="fas fa-chevron-right" style="font-size:10px;"></i>
            <span>Geo Maps</span>
        </div>

        <!-- Filter Map Bar Custom Dropdown -->
        <div class="map-filter-bar">
            <div class="filter-left">
                <div class="filter-item">
                    <div class="pupr-dropdown-wrapper">
                        <button type="button" class="btn btn-outline pupr-dropdown-toggle" data-toggle="pupr-dropdown">
                            <span class="selected-label">Semua Kegiatan</span>
                            <i class="fas fa-chevron-down" style="font-size:10px;margin-left:8px;"></i>
                        </button>
                        <div class="pupr-dropdown-menu" id="dropdownKategoriMenu">
                            <div class="pupr-dropdown-item active" data-value="all">Semua Kegiatan</div>
                            <div class="pupr-dropdown-item" data-value="Jalan">Perbaikan Jalan</div>
                            <div class="pupr-dropdown-item" data-value="Drainase">Drainase & Irigasi</div>
                            <div class="pupr-dropdown-item" data-value="Jembatan">Jembatan</div>
                            <div class="pupr-dropdown-item" data-value="Sungai">Normalisasi Sungai</div>
                        </div>
                    </div>
                </div>
                <div class="filter-item">
                    <div class="pupr-dropdown-wrapper">
                        <button type="button" class="btn btn-outline pupr-dropdown-toggle" data-toggle="pupr-dropdown">
                            <span class="selected-label">Semua Status</span>
                            <i class="fas fa-chevron-down" style="font-size:10px;margin-left:8px;"></i>
                        </button>
                        <div class="pupr-dropdown-menu" id="dropdownStatusMenu">
                            <div class="pupr-dropdown-item active" data-value="all">Semua Status</div>
                            <div class="pupr-dropdown-item" data-value="petugas_aktif"><i class="fas fa-user-shield" style="color:#00d2d3;margin-right:6px;"></i> Petugas Aktif</div>
                            <div class="pupr-dropdown-item" data-value="selesai">Selesai</div>
                            <div class="pupr-dropdown-item" data-value="proses">Dalam Proses</div>
                            <div class="pupr-dropdown-item" data-value="menunggu">Menunggu</div>
                            <div class="pupr-dropdown-item" data-value="survei">Survei</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="filter-right">
                <button class="btn btn-outline" id="resetMapFilterBtn"><i class="fas fa-redo"></i> Reset</button>
                <button class="btn btn-primary" id="applyMapFilterBtn"><i class="fas fa-search"></i> Terapkan Filter</button>
            </div>
        </div>

        <!-- Map Wrapper Full Width -->
        <div class="map-wrapper-card" style="margin-bottom:24px;">
            <div class="map-header">
                <h3><i class="fab fa-google" style="color:var(--secondary);margin-right:8px;"></i>Google Maps - Persebaran Titik Survei Lapangan BSPS Verval</h3>
                <div class="layer-toggle">
                    <button class="layer-btn active" data-layer="google_street"><i class="fab fa-google"></i> Google Jalan</button>
                    <button class="layer-btn" data-layer="google_hybrid"><i class="fas fa-globe-asia"></i> Google Satelit</button>
                    <button class="layer-btn" data-layer="google_terrain"><i class="fas fa-mountain"></i> Google Medan</button>
                    <button class="layer-btn" data-layer="dark"><i class="fas fa-moon"></i> Google Malam</button>
                </div>
            </div>
            <div id="map" style="height: 520px;"></div>
        </div>

        <!-- Tabel Daftar Titik Lokasi Survei -->
        <div class="table-card">
            <div class="table-header" style="display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap;padding:16px 20px;">
                <h3 style="margin:0;font-size:15px;font-weight:800;color:var(--primary-dark);display:flex;align-items:center;gap:8px;">
                    <i class="fas fa-map-location-dot" style="color:var(--secondary);"></i> Daftar Titik Lokasi (<span id="markerCount" style="color:var(--primary);">0 titik</span>)
                </h3>
                <div style="max-width:340px;width:100%;">
                    <div class="pupr-search-group" style="margin:0;">
                        <input type="text" id="searchLocation" placeholder="Cari nama lokasi, kegiatan, atau petugas..." class="pupr-search-input" />
                        <button type="button" class="pupr-search-btn"><i class="fas fa-search"></i></button>
                    </div>
                </div>
            </div>

            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th style="width:50px;">No</th>
                            <th style="min-width:200px;">Nama Calon Penerima</th>
                            <th style="min-width:160px;">NIK / No. KK</th>
                            <th style="min-width:160px;">Lokasi / Desa</th>
                            <th style="min-width:160px;">Petugas Survei</th>
                            <th style="min-width:130px;">Koordinat GPS</th>
                            <th style="min-width:130px;">Status Kelayakan</th>
                            <th style="width:120px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="markerTableBody">
                        <!-- Diisi secara dinamis oleh JavaScript -->
                    </tbody>
                </table>
            </div>
        </div>
    </main>
@endsection

@push('scripts')
    <!-- Leaflet JS & Fullscreen -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/leaflet.fullscreen@2.0.0/Control.FullScreen.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const markersData = @json($markers);

            const defaultCenter = markersData.length > 0 && markersData[0].lat && markersData[0].lng
                ? [markersData[0].lat, markersData[0].lng]
                : [-8.1724, 113.6983];

            const map = L.map('map', { center: defaultCenter, zoom: 13, zoomControl: false });
            L.control.zoom({ position: 'topright' }).addTo(map);
            L.control.fullscreen({ position: 'topright' }).addTo(map);

            // Google Maps Tile Layers Collection (All 100% Authentic Google Maps)
            const tileLayers = {
                google_street: L.tileLayer('https://mt1.google.com/vt/lyrs=m&x={x}&y={y}&z={z}', { maxZoom: 20, attribution: '&copy; Google Maps' }),
                google_hybrid: L.tileLayer('https://mt1.google.com/vt/lyrs=y&x={x}&y={y}&z={z}', { maxZoom: 20, attribution: '&copy; Google Maps' }),
                google_terrain: L.tileLayer('https://mt1.google.com/vt/lyrs=p&x={x}&y={y}&z={z}', { maxZoom: 20, attribution: '&copy; Google Maps' }),
                dark: L.tileLayer('https://mt1.google.com/vt/lyrs=m&x={x}&y={y}&z={z}', { maxZoom: 20, attribution: '&copy; Google Maps' })
            };

            const mapEl = document.getElementById('map');
            function updateGoogleNightMode(layerName) {
                const currentTheme = document.documentElement.getAttribute('data-theme') || 'pupr';
                if (layerName === 'dark' || currentTheme === 'dark') {
                    mapEl.classList.add('google-night-mode');
                } else {
                    mapEl.classList.remove('google-night-mode');
                }
            }

            // Detect current theme mode for initial map layer
            const initialTheme = document.documentElement.getAttribute('data-theme') || 'pupr';
            let currentLayer = initialTheme === 'dark' ? 'dark' : 'google_street';

            // Add active Google Maps layer to map
            tileLayers[currentLayer].addTo(map);
            updateGoogleNightMode(currentLayer);

            // Update UI Layer Buttons active state
            function setLayerActiveUI(layerName) {
                document.querySelectorAll('.layer-btn').forEach(btn => {
                    if (btn.dataset.layer === layerName) {
                        btn.classList.add('active');
                    } else {
                        btn.classList.remove('active');
                    }
                });
            }
            setLayerActiveUI(currentLayer);

            // Layer button click handler
            document.querySelectorAll('.layer-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const layer = this.dataset.layer;
                    if (tileLayers[layer] && layer !== currentLayer) {
                        map.removeLayer(tileLayers[currentLayer]);
                        tileLayers[layer].addTo(map);
                        currentLayer = layer;
                        setLayerActiveUI(layer);
                        updateGoogleNightMode(layer);
                    }
                });
            });

            // Listen to Theme Changes dynamically
            const themeObserver = new MutationObserver(function(mutations) {
                mutations.forEach(function(mutation) {
                    if (mutation.type === 'attributes' && mutation.attributeName === 'data-theme') {
                        const newTheme = document.documentElement.getAttribute('data-theme');
                        const targetLayer = newTheme === 'dark' ? 'dark' : 'google_street';
                        if (targetLayer !== currentLayer && tileLayers[targetLayer]) {
                            map.removeLayer(tileLayers[currentLayer]);
                            tileLayers[targetLayer].addTo(map);
                            currentLayer = targetLayer;
                            setLayerActiveUI(targetLayer);
                        }
                        updateGoogleNightMode(currentLayer);
                    }
                });
            });
            themeObserver.observe(document.documentElement, { attributes: true });

            function getCssVar(varName, defaultVal) {
                return getComputedStyle(document.documentElement).getPropertyValue(varName).trim() || defaultVal;
            }

            function createMarkerIcon(color, type) {
                const primaryColor = getCssVar('--primary', '#002855');
                const secondaryColor = getCssVar('--secondary', '#FFB800');
                const colors = { blue: primaryColor, green: '#27ae60', orange: secondaryColor, red: '#e74c3c', purple: '#8e44ad', cyan: '#00d2d3' };
                const bgColor = colors[color] || primaryColor;

                const iconInner = type === 'petugas'
                    ? '<i class="fas fa-user-shield" style="transform:rotate(45deg);color:#fff;font-size:10px;"></i>'
                    : '<i class="fas fa-location-dot" style="transform:rotate(45deg);color:#fff;font-size:10px;"></i>';

                return L.divIcon({
                    className: 'custom-marker-pin',
                    html: `<div style="background:${bgColor};width:28px;height:28px;border-radius:50% 50% 50% 0;transform:rotate(-45deg);border:2.5px solid #fff;box-shadow:0 4px 12px rgba(0,0,0,0.35);display:flex;align-items:center;justify-content:center;">
                                ${iconInner}
                            </div>`,
                    iconSize: [28, 28],
                    iconAnchor: [14, 28]
                });
            }

            const markerGroup = L.layerGroup().addTo(map);
            function renderMarkers(data) {
                markerGroup.clearLayers();
                if (!data || data.length === 0) {
                    renderMarkerList([]);
                    return;
                }

                const bounds = [];
                data.forEach(item => {
                    if (!item.lat || !item.lng) return;
                    bounds.push([item.lat, item.lng]);

                    const icon = createMarkerIcon(item.color, item.type);
                    const imgHtml = item.foto
                        ? `<a href="${item.foto}" target="_blank" style="display:block;margin:6px 0;">
                               <img src="${item.foto}" style="width:100%;height:90px;object-fit:cover;border-radius:6px;cursor:pointer;" title="Klik untuk lihat foto asli" />
                           </a>`
                        : '';

                    const popupContent = `
                        <div style="font-family:Inter,sans-serif;padding:4px;max-width:240px;">
                            <strong style="font-size:13.5px;color:#002855;display:block;margin-bottom:4px;">${item.name}</strong>
                            ${imgHtml}
                            <div style="font-size:11.5px;color:#334155;margin-top:4px;display:flex;align-items:center;gap:5px;">
                                <i class="fas fa-id-card" style="color:#002855;width:14px;"></i>
                                <span>NIK: <strong>${item.nik}</strong></span>
                            </div>
                            <div style="font-size:11.5px;color:#475569;margin-top:2px;display:flex;align-items:center;gap:5px;">
                                <i class="fas fa-users" style="color:#64748b;width:14px;"></i>
                                <span>No. KK: ${item.no_kk}</span>
                            </div>
                            <div style="font-size:11.5px;color:#475569;margin-top:2px;display:flex;align-items:center;gap:5px;">
                                <i class="fas fa-location-dot" style="color:#002855;width:14px;"></i>
                                <span>${item.location}</span>
                            </div>
                            <div style="font-size:11.5px;color:#475569;margin-top:2px;display:flex;align-items:center;gap:5px;">
                                <i class="fas fa-user-hard-hat" style="color:#d69e00;width:14px;"></i>
                                <span>Petugas: <strong style="color:#002855;">${item.petugas || '-'}</strong></span>
                            </div>
                            <div style="font-size:11px;color:#64748b;margin-top:2px;display:flex;align-items:center;gap:5px;">
                                <i class="fas fa-layer-group" style="width:14px;"></i>
                                <span>${item.desil || '-'}</span>
                            </div>
                            <div style="font-size:11px;color:#64748b;margin-top:2px;display:flex;align-items:center;gap:5px;">
                                <i class="fas fa-clock" style="width:14px;"></i>
                                <span>${item.tanggal}</span>
                            </div>
                            <div style="margin-top:8px;">
                                <span style="font-size:11px;font-weight:700;padding:3px 10px;border-radius:12px;background:${item.color === 'green' ? 'rgba(39,174,96,0.15)' : (item.color === 'orange' ? 'rgba(255,184,0,0.15)' : 'rgba(142,68,173,0.12)')};color:${item.color === 'green' ? '#15803d' : (item.color === 'orange' ? '#b45309' : '#7e22ce')};">
                                    ${item.statusLabel}
                                </span>
                            </div>
                        </div>
                    `;

                    const marker = L.marker([item.lat, item.lng], { icon }).bindPopup(popupContent);
                    markerGroup.addLayer(marker);
                });

                if (bounds.length > 0) {
                    map.fitBounds(bounds, { padding: [40, 40], maxZoom: 15 });
                }

                renderMarkerList(data);
            }

            function renderMarkerList(data) {
                const tbody = document.getElementById('markerTableBody');
                const countSpan = document.getElementById('markerCount');
                if (!tbody) return;

                // Hanya tampilkan kegiatan survei pada tabel (lokasi petugas hanya tampil di Peta)
                const surveyData = (data || []).filter(item => item.type !== 'petugas');

                if (countSpan) countSpan.textContent = `${surveyData.length} titik`;

                if (surveyData.length === 0) {
                    tbody.innerHTML = `
                        <tr>
                            <td colspan="7" style="text-align:center;padding:32px;color:var(--text-muted);">
                                <i class="fas fa-map-pin" style="font-size:32px;display:block;margin-bottom:8px;opacity:0.5;"></i>
                                Tidak ada data titik lokasi survei yang ditemukan.
                            </td>
                        </tr>
                    `;
                    return;
                }

                let html = '';
                surveyData.forEach((item, index) => {
                    const colorMap = { green: '#15803d', orange: '#b45309', purple: '#7e22ce', blue: '#002855' };
                    const bgMap   = { green: 'rgba(39,174,96,0.12)', orange: 'rgba(255,184,0,0.15)', purple: 'rgba(142,68,173,0.12)', blue: 'rgba(0,40,85,0.08)' };

                    html += `
                        <tr>
                            <td>${index + 1}</td>
                            <td>
                                <strong style="color:var(--primary-dark);font-size:13px;display:block;">${item.name}</strong>
                                <span style="font-size:11px;color:var(--text-muted);"><i class="fas fa-clock"></i> ${item.tanggal}</span>
                            </td>
                            <td>
                                <div style="font-family:monospace;font-size:12px;"><strong>NIK:</strong> ${item.nik}</div>
                                <div style="font-family:monospace;font-size:11px;color:var(--text-muted);">KK: ${item.no_kk}</div>
                            </td>
                            <td>
                                <div style="font-size:12.5px;"><i class="fas fa-location-dot" style="color:var(--primary);font-size:11px;"></i> ${item.location}</div>
                                <div style="font-size:11px;color:var(--text-muted);margin-top:2px;">${item.alamat || '-'}</div>
                            </td>
                            <td>
                                <div style="font-weight:700;font-size:12.5px;"><i class="fas fa-user-hard-hat" style="font-size:11px;color:#d69e00;"></i> ${item.petugas || '-'}</div>
                            </td>
                            <td>
                                <span style="font-family:monospace;font-size:11.5px;background:rgba(0,40,85,0.06);padding:3px 8px;border-radius:4px;color:var(--primary-dark);">
                                    ${item.lat.toFixed(5)}, ${item.lng.toFixed(5)}
                                </span>
                            </td>
                            <td>
                                <span style="font-size:11px;font-weight:700;padding:3px 10px;border-radius:12px;background:${bgMap[item.color] || 'rgba(0,40,85,0.08)'};color:${colorMap[item.color] || '#002855'};">
                                    ${item.statusLabel}
                                </span>
                            </td>
                            <td>
                                <button type="button" class="btn btn-primary" style="padding:6px 12px;font-size:11px;border-radius:4px;font-weight:700;border:none;cursor:pointer;display:inline-flex;align-items:center;gap:4px;" onclick="focusOnMapMarker(${item.lat}, ${item.lng})">
                                    <i class="fas fa-crosshairs"></i> Lihat
                                </button>
                            </td>
                        </tr>
                    `;
                });
                tbody.innerHTML = html;
            }

            window.focusOnMapMarker = function(lat, lng) {
                map.flyTo([lat, lng], 17, { duration: 1.2 });
                window.scrollTo({ top: 120, behavior: 'smooth' });
            };

            const searchInput = document.getElementById('searchLocation');
            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    const query = this.value.toLowerCase().trim();
                    let filtered = markersData.filter(item =>
                        item.name.toLowerCase().includes(query) ||
                        item.location.toLowerCase().includes(query) ||
                        (item.alamat && item.alamat.toLowerCase().includes(query)) ||
                        (item.petugas && item.petugas.toLowerCase().includes(query))
                    );
                    renderMarkers(filtered);
                });
            }

            let selectedKategori = 'all';
            let selectedStatus = 'all';

            function filterMapMarkers() {
                let filtered = markersData;
                if (selectedKategori !== 'all') {
                    filtered = filtered.filter(item => item.name.toLowerCase().includes(selectedKategori.toLowerCase()));
                }
                if (selectedStatus !== 'all') {
                    filtered = filtered.filter(item => item.status === selectedStatus);
                }
                renderMarkers(filtered);
            }

            document.querySelectorAll('#dropdownKategoriMenu .pupr-dropdown-item').forEach(item => {
                item.addEventListener('click', function() {
                    selectedKategori = this.dataset.value;
                    filterMapMarkers();
                });
            });

            document.querySelectorAll('#dropdownStatusMenu .pupr-dropdown-item').forEach(item => {
                item.addEventListener('click', function() {
                    selectedStatus = this.dataset.value;
                    filterMapMarkers();
                });
            });

            const resetMapBtn = document.getElementById('resetMapFilterBtn');
            if (resetMapBtn) {
                resetMapBtn.addEventListener('click', function() {
                    selectedKategori = 'all';
                    selectedStatus = 'all';

                    const katMenu = document.getElementById('dropdownKategoriMenu');
                    if (katMenu) {
                        const wrapper = katMenu.closest('.pupr-dropdown-wrapper');
                        wrapper.querySelector('.selected-label').textContent = 'Semua Kegiatan';
                        wrapper.querySelectorAll('.pupr-dropdown-item').forEach(i => i.classList.remove('active'));
                        wrapper.querySelector('[data-value="all"]').classList.add('active');
                    }

                    const statMenu = document.getElementById('dropdownStatusMenu');
                    if (statMenu) {
                        const wrapper = statMenu.closest('.pupr-dropdown-wrapper');
                        wrapper.querySelector('.selected-label').textContent = 'Semua Status';
                        wrapper.querySelectorAll('.pupr-dropdown-item').forEach(i => i.classList.remove('active'));
                        wrapper.querySelector('[data-value="all"]').classList.add('active');
                    }

                    if (searchInput) searchInput.value = '';
                    renderMarkers(markersData);
                });
            }

            renderMarkers(markersData);
            setTimeout(() => map.invalidateSize(), 300);
        });
    </script>
@endpush
