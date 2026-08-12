@extends('layouts.partial.app')

@section('title', 'BSPS Verval - Laporan Rekap Hasil & Capaian Indikator')
@section('title_header', 'Laporan & Rekapitulasi BSPS')
@section('subtitle_header', 'Rekapitulasi Hasil Verifikasi Sesuai / Tidak Sesuai per Desa & Kecamatan, Capaian Indikator RTLH, dan Lampiran Foto Lapangan')

@push('styles')
<style>
    /* Grid Stat Cards PUPR Theme */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
        margin-bottom: 24px;
    }
    .stat-card {
        background: var(--bg-card, #ffffff);
        border-radius: var(--radius-sm, 10px);
        padding: 18px 20px;
        box-shadow: 0 2px 10px rgba(0, 40, 85, 0.05);
        border: 1px solid rgba(0, 40, 85, 0.08);
        display: flex;
        align-items: center;
        gap: 14px;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 18px rgba(0, 40, 85, 0.1);
    }
    .stat-card .icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        flex-shrink: 0;
    }
    .stat-card .icon.blue { background: rgba(0, 40, 85, 0.10); color: #002855; }
    .stat-card .icon.green { background: rgba(39, 174, 96, 0.12); color: #27ae60; }
    .stat-card .icon.orange { background: rgba(255, 184, 0, 0.15); color: #d69e00; }
    .stat-card .icon.red { background: rgba(231, 76, 60, 0.12); color: #e74c3c; }
    .stat-card .info .value { font-size: 26px; font-weight: 800; color: #002855; line-height: 1.1; }
    .stat-card .info .label { font-size: 12px; color: #64748b; font-weight: 600; margin-top: 3px; }

    /* Navigasi Tab Rekapitulasi */
    .laporan-tabs {
        display: flex;
        gap: 8px;
        margin-bottom: 20px;
        background: #ffffff;
        padding: 8px;
        border-radius: 12px;
        border: 1px solid rgba(0, 40, 85, 0.08);
        box-shadow: 0 2px 8px rgba(0, 40, 85, 0.04);
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }
    .laporan-tabs::-webkit-scrollbar { display: none; }
    .laporan-tab-item {
        padding: 11px 20px;
        border-radius: 8px;
        font-size: 13.5px;
        font-weight: 600;
        color: #475569;
        text-decoration: none;
        white-space: nowrap;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    .laporan-tab-item:hover { background: rgba(0, 40, 85, 0.05); color: #002855; }
    .laporan-tab-item.active { background: #002855; color: #ffffff; font-weight: 700; box-shadow: 0 3px 10px rgba(0,40,85,0.2); }

    /* Filter Form PUPR Layout */
    .filter-card {
        background: #ffffff;
        border-radius: 12px;
        padding: 16px 20px;
        border: 1px solid rgba(0, 40, 85, 0.08);
        margin-bottom: 24px;
        box-shadow: 0 2px 8px rgba(0, 40, 85, 0.04);
    }
    .filter-grid {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        align-items: center;
    }
    .filter-item {
        flex: 1;
        min-width: 170px;
    }
    .filter-item input, .filter-item select {
        width: 100%;
        padding: 9px 14px;
        border-radius: 8px;
        border: 1px solid #cbd5e1;
        font-size: 13px;
        color: #0f172a;
        background-color: #f8fafc;
        transition: border-color 0.2s;
    }
    .filter-item input:focus, .filter-item select:focus {
        border-color: #002855;
        outline: none;
        background-color: #fff;
    }

    /* Table Container Styling */
    .table-card {
        background: #ffffff;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0, 40, 85, 0.05);
        border: 1px solid rgba(0, 40, 85, 0.08);
        overflow: hidden;
    }
    .table-header {
        padding: 18px 24px;
        border-bottom: 1px solid rgba(0, 40, 85, 0.08);
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
    }
    .table-header h3 { font-size: 16px; font-weight: 700; color: #002855; margin: 0; }

    .table-wrapper {
        overflow-x: auto;
        width: 100%;
    }
    table.pupr-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
    }
    table.pupr-table thead {
        background: #f8fafc;
    }
    table.pupr-table th {
        padding: 12px 16px;
        text-align: left;
        font-weight: 700;
        font-size: 11.5px;
        text-transform: uppercase;
        color: #475569;
        border-bottom: 1px solid #e2e8f0;
        white-space: nowrap;
    }
    table.pupr-table td {
        padding: 12px 16px;
        border-bottom: 1px solid #e2e8f0;
        vertical-align: middle;
    }
    table.pupr-table tr:hover {
        background-color: rgba(0, 40, 85, 0.02);
    }

    /* Progress & Badges */
    .badge-status {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 11.5px;
        font-weight: 700;
    }
    .badge-status.success { background: rgba(39, 174, 96, 0.12); color: #27ae60; }
    .badge-status.danger { background: rgba(231, 76, 60, 0.12); color: #e74c3c; }
    .badge-status.warning { background: rgba(255, 184, 0, 0.18); color: #b78100; }
    .badge-status.info { background: rgba(0, 40, 85, 0.1); color: #002855; }

    .progress-bar-mini {
        width: 90px;
        height: 6px;
        background: #e2e8f0;
        border-radius: 4px;
        overflow: hidden;
        display: inline-block;
        vertical-align: middle;
        margin-right: 6px;
    }
    .progress-bar-fill {
        height: 100%;
        background: #27ae60;
        border-radius: 4px;
    }

    /* Galeri Grid Cards */
    .galeri-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 20px;
        padding: 20px;
    }
    .galeri-card {
        background: #ffffff;
        border-radius: 12px;
        border: 1px solid rgba(0, 40, 85, 0.1);
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .galeri-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 22px rgba(0, 40, 85, 0.12);
    }
    .galeri-card-header {
        padding: 14px 16px;
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
    }
    .galeri-card-body {
        padding: 14px 16px;
    }
    .photo-thumbs-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 8px;
        margin-top: 10px;
    }
    .photo-thumb-item {
        position: relative;
        aspect-ratio: 4/3;
        border-radius: 6px;
        overflow: hidden;
        border: 1px solid #cbd5e1;
        cursor: pointer;
        background: #f1f5f9;
    }
    .photo-thumb-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.2s ease;
    }
    .photo-thumb-item:hover img {
        transform: scale(1.1);
    }
    .photo-thumb-label {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        background: rgba(0, 40, 85, 0.75);
        color: #fff;
        font-size: 8.5px;
        padding: 2px 4px;
        text-align: center;
        font-weight: 600;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* Modal Lightbox Popup */
    .lightbox-modal {
        display: none;
        position: fixed;
        top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(0, 24, 53, 0.88);
        backdrop-filter: blur(5px);
        z-index: 9999;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }
    .lightbox-modal.active {
        display: flex;
    }
    .lightbox-content {
        max-width: 900px;
        width: 100%;
        background: #ffffff;
        border-radius: 14px;
        overflow: hidden;
        box-shadow: 0 10px 40px rgba(0,0,0,0.3);
        display: flex;
        flex-direction: column;
        max-height: 90vh;
    }
    .lightbox-header {
        padding: 14px 20px;
        background: #002855;
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .lightbox-body {
        padding: 20px;
        overflow-y: auto;
        text-align: center;
        background: #0f172a;
    }
    .lightbox-body img {
        max-height: 60vh;
        max-width: 100%;
        border-radius: 8px;
        object-fit: contain;
    }

    /* Custom Pagination Styling */
    .pagination-custom-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 16px 24px;
        background: #ffffff;
        border-top: 1px solid #e2e8f0;
        flex-wrap: wrap;
        gap: 14px;
    }
    .pagination-info-text {
        font-size: 13px;
        color: #64748b;
        font-weight: 500;
    }
    .pagination-info-text strong {
        color: #002855;
        font-weight: 700;
    }
    .pagination-nav {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        list-style: none;
        padding: 0;
        margin: 0;
    }
    .page-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 34px;
        height: 34px;
        padding: 0 10px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 600;
        color: #475569;
        background: #f8fafc;
        border: 1px solid #cbd5e1;
        text-decoration: none;
        transition: all 0.15s ease;
    }
    .page-btn:hover:not(.disabled):not(.active) {
        background: #002855;
        color: #ffffff;
        border-color: #002855;
    }
    .page-btn.active {
        background: #002855;
        color: #ffffff;
        border-color: #002855;
        font-weight: 800;
        box-shadow: 0 2px 6px rgba(0, 40, 85, 0.25);
    }
    .page-btn.disabled {
        opacity: 0.45;
        cursor: not-allowed;
        background: #f1f5f9;
        border-color: #e2e8f0;
        color: #94a3b8;
    }
    .page-dots {
        padding: 0 6px;
        color: #94a3b8;
        font-weight: 700;
    }

    /* Button Export Excel PUPR Theme */
    .btn-export-excel {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: linear-gradient(135deg, #107c41, #15803d);
        color: #ffffff !important;
        padding: 9px 18px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 700;
        text-decoration: none !important;
        box-shadow: 0 3px 10px rgba(16, 124, 65, 0.28);
        border: 1px solid rgba(255, 255, 255, 0.15);
        transition: all 0.2s ease;
    }
    .btn-export-excel:hover {
        background: linear-gradient(135deg, #0e6b37, #166534);
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(16, 124, 65, 0.38);
        color: #ffffff !important;
    }
    .btn-export-excel i {
        font-size: 15px;
    }

    /* Option Cards inside Export Modal */
    .export-option-card {
        display: block;
        padding: 14px 16px;
        border: 2px solid #e2e8f0;
        border-radius: 10px;
        background: #ffffff;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .export-option-card:hover {
        border-color: #cbd5e1;
        background: #f8fafc;
    }
    .export-option-card.active {
        border-color: #107c41;
        background: rgba(16, 124, 65, 0.04);
        box-shadow: 0 2px 8px rgba(16, 124, 65, 0.12);
    }

    /* Modal Export Excel Fix Overflow */
    #modalExportExcel .modal-box {
        overflow: visible !important;
    }
    #modalExportExcel .modal-body {
        overflow: visible !important;
    }
    #modalExportExcel .pupr-dropdown-menu {
    /* Button Export PDF & Excel PUPR Theme */
    .btn-export-pdf {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: linear-gradient(135deg, #dc2626, #b91c1c);
        color: #ffffff !important;
        padding: 9px 18px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 700;
        text-decoration: none !important;
        box-shadow: 0 3px 10px rgba(220, 38, 38, 0.28);
        border: 1px solid rgba(255, 255, 255, 0.15);
        transition: all 0.2s ease;
    }
    .btn-export-pdf:hover {
        background: linear-gradient(135deg, #b91c1c, #991b1b);
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(220, 38, 38, 0.38);
        color: #ffffff !important;
    }
    .btn-export-pdf i { font-size: 15px; }

    .btn-pdf-mini {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        background: rgba(220, 38, 38, 0.1);
        color: #dc2626 !important;
        border: 1px solid rgba(220, 38, 38, 0.25);
        padding: 4px 8px;
        border-radius: 6px;
        font-size: 11px;
        font-weight: 700;
        text-decoration: none !important;
        transition: all 0.15s ease;
        white-space: nowrap;
    }
    .btn-pdf-mini:hover {
        background: #dc2626;
        color: #ffffff !important;
        border-color: #dc2626;
        transform: translateY(-1px);
        box-shadow: 0 2px 6px rgba(220, 38, 38, 0.25);
    }

    /* Modal Export Excel & PDF Fix Overflow */
    #modalExportExcel .modal-box, #modalPdfDesa .modal-box {
        overflow: visible !important;
    }
    #modalExportExcel .modal-body, #modalPdfDesa .modal-body {
        overflow: visible !important;
    }
    #modalExportExcel .pupr-dropdown-menu, #modalPdfDesa .pupr-dropdown-menu {
        z-index: 1050 !important;
    }

    /* Responsive adjustments */
    @media (max-width: 1024px) {
        .stats-grid { grid-template-columns: repeat(2, 1fr); }
    }
    @media (max-width: 640px) {
        .stats-grid { grid-template-columns: 1fr; }
        .filter-grid { flex-direction: column; }
        .pagination-custom-bar { flex-direction: column; align-items: center; text-align: center; }
    }
</style>
@endpush

@section('content')
    @include('layouts.navbar')

    <main class="dashboard-content">
        <!-- Breadcrumb -->
        <div class="breadcrumb" style="font-size:13px;color:#64748b;margin-bottom:18px;display:flex;align-items:center;gap:8px;">
            <a href="{{ url('/') }}" style="color:#002855;text-decoration:none;font-weight:600;"><i class="fas fa-home"></i> Home</a>
            <i class="fas fa-chevron-right" style="font-size:10px;"></i>
            <span>Laporan &amp; Rekapitulasi</span>
        </div>

        <!-- 4 Stat Counters Dinamis dari Real Data -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="icon blue"><i class="fas fa-users-viewfinder"></i></div>
                <div class="info">
                    <div class="value">{{ number_format($stats['total_penerima']) }}</div>
                    <div class="label">Total Calon Penerima</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="icon green"><i class="fas fa-clipboard-check"></i></div>
                <div class="info">
                    <div class="value">{{ number_format($stats['sudah_survei']) }}</div>
                    <div class="label">Sudah Disurvei Lapangan</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="icon green"><i class="fas fa-circle-check"></i></div>
                <div class="info">
                    <div class="value">{{ number_format($stats['total_layak']) }}</div>
                    <div class="label">Hasil Sesuai (Layak)</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="icon red"><i class="fas fa-circle-xmark"></i></div>
                <div class="info">
                    <div class="value">{{ number_format($stats['total_tidak_layak']) }}</div>
                    <div class="label">Hasil Tidak Sesuai</div>
                </div>
            </div>
        </div>

        <!-- Navigasi Tab Rekapitulasi -->
        <div class="laporan-tabs">
            <a href="{{ route('laporan', array_merge(request()->query(), ['tab' => 'rekap', 'page' => 1])) }}" class="laporan-tab-item {{ $tab === 'rekap' ? 'active' : '' }}">
                <i class="fas fa-chart-pie"></i> Rekap Sesuai vs Tidak Sesuai (Per Desa)
            </a>
            <a href="{{ route('laporan', array_merge(request()->query(), ['tab' => 'indikator', 'page' => 1])) }}" class="laporan-tab-item {{ $tab === 'indikator' ? 'active' : '' }}">
                <i class="fas fa-sliders"></i> Capaian 6 Indikator RTLH
            </a>
            <a href="{{ route('laporan', array_merge(request()->query(), ['tab' => 'galeri', 'page' => 1])) }}" class="laporan-tab-item {{ $tab === 'galeri' ? 'active' : '' }}">
                <i class="fas fa-images"></i> Galeri Foto Lapangan
            </a>
            <a href="{{ route('laporan', array_merge(request()->query(), ['tab' => 'detail', 'page' => 1])) }}" class="laporan-tab-item {{ $tab === 'detail' ? 'active' : '' }}">
                <i class="fas fa-table-list"></i> Detail Data Penerima
            </a>
        </div>

        <!-- Filter & Search Section -->
        <div class="filter-card">
            <form action="{{ route('laporan') }}" method="GET" id="formFilterLaporan" class="filter-grid">
                <input type="hidden" name="tab" value="{{ $tab }}" />

                <div class="filter-item" style="flex:2;min-width:220px;">
                    <div style="position:relative;">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, NIK, KK, desa, kecamatan..." />
                        <i class="fas fa-search" style="position:absolute;right:12px;top:50%;transform:translateY(-50%);color:#94a3b8;"></i>
                    </div>
                </div>

                {{-- Filter Kecamatan dengan Custom PUPR Dropdown --}}
                @if(!auth()->check() || !auth()->user()->isAdminKecamatan())
                @php
                    $selectedKec = request('kecamatan', 'all');
                @endphp
                <div class="filter-item">
                    <input type="hidden" name="kecamatan" id="filterKecamatan" value="{{ $selectedKec }}" />
                    <div class="pupr-dropdown-wrapper" id="ddKecamatanWrapper" style="width:100%;">
                        <button type="button" class="pupr-dropdown-toggle" style="width:100%;" onclick="window.PuprDropdown.toggle(document.getElementById('ddKecamatanWrapper'))">
                            <span style="display:flex;align-items:center;gap:6px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                                <i class="fas fa-building-flag" style="font-size:12px;opacity:0.6;"></i>
                                <span class="selected-label">
                                    {{ $selectedKec && $selectedKec !== 'all' ? 'Kec. ' . ucwords(strtolower($selectedKec)) : '-- Semua Kecamatan --' }}
                                </span>
                            </span>
                            <i class="fas fa-chevron-down" style="font-size:10px;opacity:0.5;"></i>
                        </button>
                        <div class="pupr-dropdown-menu" style="min-width:220px;max-height:300px;overflow-y:auto;width:100%;">
                            <div class="pupr-dropdown-item {{ (!request('kecamatan') || request('kecamatan') === 'all') ? 'active' : '' }}"
                                 onclick="selectDropdown('filterKecamatan', 'ddKecamatanWrapper', 'all', '-- Semua Kecamatan --', 'formFilterLaporan')">
                                <i class="fas fa-layer-group" style="font-size:11px;opacity:0.5;"></i> -- Semua Kecamatan --
                            </div>
                            @foreach($listKecamatan as $kec)
                            <div class="pupr-dropdown-item {{ request('kecamatan') === $kec ? 'active' : '' }}"
                                 onclick="selectDropdown('filterKecamatan', 'ddKecamatanWrapper', '{{ $kec }}', 'Kec. {{ ucwords(strtolower($kec)) }}', 'formFilterLaporan')">
                                <i class="fas fa-map-pin" style="font-size:11px;opacity:0.5;"></i> Kec. {{ ucwords(strtolower($kec)) }}
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif

                {{-- Filter Status dengan Custom PUPR Dropdown --}}
                @php
                    $statusLabels = [
                        'all' => '-- Semua Status --',
                        'layak' => 'Hasil Sesuai (Layak)',
                        'tidak_layak' => 'Hasil Tidak Sesuai',
                        'sudah' => 'Sudah Survei',
                        'belum' => 'Belum Survei',
                    ];
                    $currentStatusKey = request('status', 'all');
                @endphp
                <div class="filter-item">
                    <input type="hidden" name="status" id="filterStatus" value="{{ $currentStatusKey }}" />
                    <div class="pupr-dropdown-wrapper" id="ddStatusWrapper" style="width:100%;">
                        <button type="button" class="pupr-dropdown-toggle" style="width:100%;" onclick="window.PuprDropdown.toggle(document.getElementById('ddStatusWrapper'))">
                            <span style="display:flex;align-items:center;gap:6px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                                <i class="fas fa-filter" style="font-size:12px;opacity:0.6;"></i>
                                <span class="selected-label">
                                    {{ $statusLabels[$currentStatusKey] ?? '-- Semua Status --' }}
                                </span>
                            </span>
                            <i class="fas fa-chevron-down" style="font-size:10px;opacity:0.5;"></i>
                        </button>
                        <div class="pupr-dropdown-menu" style="min-width:200px;width:100%;">
                            <div class="pupr-dropdown-item {{ $currentStatusKey === 'all' ? 'active' : '' }}"
                                 onclick="selectDropdown('filterStatus', 'ddStatusWrapper', 'all', '-- Semua Status --', 'formFilterLaporan')">
                                <i class="fas fa-list" style="font-size:11px;opacity:0.5;"></i> -- Semua Status --
                            </div>
                            <div class="pupr-dropdown-item {{ $currentStatusKey === 'layak' ? 'active' : '' }}"
                                 onclick="selectDropdown('filterStatus', 'ddStatusWrapper', 'layak', 'Hasil Sesuai (Layak)', 'formFilterLaporan')">
                                <i class="fas fa-circle-check" style="font-size:11px;color:#27ae60;"></i> Hasil Sesuai (Layak)
                            </div>
                            <div class="pupr-dropdown-item {{ $currentStatusKey === 'tidak_layak' ? 'active' : '' }}"
                                 onclick="selectDropdown('filterStatus', 'ddStatusWrapper', 'tidak_layak', 'Hasil Tidak Sesuai', 'formFilterLaporan')">
                                <i class="fas fa-circle-xmark" style="font-size:11px;color:#e74c3c;"></i> Hasil Tidak Sesuai
                            </div>
                            <div class="pupr-dropdown-item {{ $currentStatusKey === 'sudah' ? 'active' : '' }}"
                                 onclick="selectDropdown('filterStatus', 'ddStatusWrapper', 'sudah', 'Sudah Survei', 'formFilterLaporan')">
                                <i class="fas fa-clipboard-check" style="font-size:11px;color:#002855;"></i> Sudah Survei
                            </div>
                            <div class="pupr-dropdown-item {{ $currentStatusKey === 'belum' ? 'active' : '' }}"
                                 onclick="selectDropdown('filterStatus', 'ddStatusWrapper', 'belum', 'Belum Survei', 'formFilterLaporan')">
                                <i class="fas fa-clock" style="font-size:11px;color:#d69e00;"></i> Belum Survei
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Pilihan Per Halaman dengan Custom PUPR Dropdown --}}
                @php
                    $perPageLabels = [
                        '15' => '15 Baris',
                        '25' => '25 Baris',
                        '50' => '50 Baris',
                        '100' => '100 Baris',
                        'all' => 'Semua Baris',
                    ];
                    $currentPerPage = request('per_page', '15');
                @endphp
                <div class="filter-item" style="max-width:140px;">
                    <input type="hidden" name="per_page" id="filterPerPage" value="{{ $currentPerPage }}" />
                    <div class="pupr-dropdown-wrapper" id="ddPerPageWrapper" style="width:100%;">
                        <button type="button" class="pupr-dropdown-toggle" style="width:100%;" onclick="window.PuprDropdown.toggle(document.getElementById('ddPerPageWrapper'))">
                            <span style="display:flex;align-items:center;gap:6px;">
                                <i class="fas fa-bars-staggered" style="font-size:12px;opacity:0.6;"></i>
                                <span class="selected-label">
                                    {{ $perPageLabels[$currentPerPage] ?? '15 Baris' }}
                                </span>
                            </span>
                            <i class="fas fa-chevron-down" style="font-size:10px;opacity:0.5;"></i>
                        </button>
                        <div class="pupr-dropdown-menu" style="min-width:150px;width:100%;">
                            @foreach($perPageLabels as $k => $label)
                            <div class="pupr-dropdown-item {{ $currentPerPage == $k ? 'active' : '' }}"
                                 onclick="selectDropdown('filterPerPage', 'ddPerPageWrapper', '{{ $k }}', '{{ $label }}', 'formFilterLaporan')">
                                <i class="fas fa-list-ol" style="font-size:11px;opacity:0.5;"></i> {{ $label }}
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div style="display:flex;gap:8px;">
                    <button type="submit" class="btn btn-primary" style="padding:9px 16px;border-radius:8px;font-weight:700;"><i class="fas fa-filter"></i> Filter</button>
                    <a href="{{ route('laporan', ['tab' => $tab]) }}" class="btn btn-outline" style="padding:9px 14px;border-radius:8px;"><i class="fas fa-redo"></i> Reset</a>
                </div>
            </form>
        </div>

        <!-- Card Container Content -->
        <div class="table-card">
            <div class="table-header">
                <h3>
                    @if($tab === 'rekap')
                        <i class="fas fa-chart-pie" style="color:#002855;margin-right:8px;"></i>Rekapitulasi Hasil Sesuai / Tidak Sesuai per Desa &amp; Kecamatan
                    @elseif($tab === 'indikator')
                        <i class="fas fa-sliders" style="color:#27ae60;margin-right:8px;"></i>Rekapitulasi Capaian 6 Indikator RTLH per Desa &amp; Kecamatan
                    @elseif($tab === 'galeri')
                        <i class="fas fa-images" style="color:#d69e00;margin-right:8px;"></i>Galeri &amp; Dokumen Lampiran Foto Lapangan BSPS
                    @elseif($tab === 'detail')
                        <i class="fas fa-table-list" style="color:#8e44ad;margin-right:8px;"></i>Daftar Detail Hasil Verifikasi &amp; Validasi Penerima
                    @endif
                </h3>
                <div style="display:flex;align-items:center;gap:8px;">
                    @if(auth()->check() && auth()->user()->isAdmin())
                <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
                    <button type="button" class="btn-export-pdf" onclick="window.PuprModal.open('modalPdfDesa')" title="Cetak Laporan PDF Resmi per Desa (Kertas F4)" style="cursor:pointer;border:none;">
                        <i class="fas fa-file-pdf"></i>
                        <span>Cetak PDF Desa (F4)</span>
                    </button>
                    <button type="button" class="btn-export-excel" onclick="window.PuprModal.open('modalExportExcel')" title="Pilih opsi dan download data ke format Microsoft Excel" style="cursor:pointer;border:none;">
                        <i class="fas fa-file-excel"></i>
                        <span>Export Excel (.XLS)</span>
                    </button>
                    @endif
                </div>
            </div>

            @if($tab === 'rekap')
                {{-- TAB 1: REKAP SESUAI VS TIDAK SESUAI PER DESA & KECAMATAN --}}
                <div class="table-wrapper">
                    <table class="pupr-table">
                        <thead>
                            <tr>
                                <th style="width:40px;text-align:center;">No</th>
                                <th>Kecamatan</th>
                                <th>Desa / Kelurahan</th>
                                <th style="text-align:center;">Total Target</th>
                                <th style="text-align:center;">Sudah Survei</th>
                                <th style="text-align:center;">Belum Survei</th>
                                <th style="text-align:center;">Hasil Sesuai (Layak)</th>
                                <th style="text-align:center;">Hasil Tidak Sesuai</th>
                                <th style="text-align:center;min-width:130px;">% Progres Survei</th>
                                <th style="text-align:center;min-width:140px;">% Kesesuaian Hasil</th>
                                <th style="width:65px;text-align:center;">Laporan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($rekapDesaKecamatan as $index => $row)
                                @php
                                    $belumSurvei = max(0, $row->total_penerima - $row->total_sudah_survei);
                                    $pctSurvei = $row->total_penerima > 0 ? round(($row->total_sudah_survei / $row->total_penerima) * 100, 1) : 0;
                                    $pctKesesuaian = $row->total_sudah_survei > 0 ? round(($row->total_layak / $row->total_sudah_survei) * 100, 1) : 0;
                                @endphp
                                <tr>
                                    <td style="text-align:center;">{{ $rekapDesaKecamatan->firstItem() + $index }}</td>
                                    <td><strong style="color:#002855;">{{ $row->kecamatan }}</strong></td>
                                    <td><strong style="color:#0f172a;">{{ $row->desa_kelurahan }}</strong></td>
                                    <td style="text-align:center;font-weight:700;">{{ number_format($row->total_penerima) }}</td>
                                    <td style="text-align:center;"><span class="badge-status info">{{ number_format($row->total_sudah_survei) }}</span></td>
                                    <td style="text-align:center;"><span class="badge-status warning">{{ number_format($belumSurvei) }}</span></td>
                                    <td style="text-align:center;"><span class="badge-status success"><i class="fas fa-check"></i> {{ number_format($row->total_layak) }}</span></td>
                                    <td style="text-align:center;"><span class="badge-status danger"><i class="fas fa-xmark"></i> {{ number_format($row->total_tidak_layak) }}</span></td>
                                    <td style="text-align:center;">
                                        <div class="progress-bar-mini">
                                            <div class="progress-bar-fill" style="width: {{ min(100, $pctSurvei) }}%;"></div>
                                        </div>
                                        <strong style="color:#002855;font-size:12px;">{{ $pctSurvei }}%</strong>
                                    </td>
                                    <td style="text-align:center;">
                                        <div class="progress-bar-mini">
                                            <div class="progress-bar-fill" style="width: {{ min(100, $pctKesesuaian) }}%;background:#27ae60;"></div>
                                        </div>
                                        <strong style="color:#27ae60;font-size:12px;">{{ $pctKesesuaian }}%</strong>
                                    </td>
                                    <td style="text-align:center;">
                                        <a href="{{ route('laporan.pdf_desa', ['kecamatan' => $row->kecamatan, 'desa' => $row->desa_kelurahan]) }}" target="_blank" class="btn-pdf-mini" title="Cetak PDF Resmi F4 Desa {{ $row->desa_kelurahan }}">
                                            <i class="fas fa-file-pdf"></i> PDF
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="11" style="text-align:center;padding:40px;color:#94a3b8;">
                                        <i class="fas fa-inbox" style="font-size:32px;display:block;margin-bottom:10px;opacity:0.4;"></i>
                                        Belum ada data rekapitulasi desa &amp; kecamatan.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        @if($rekapDesaKecamatan->total() > 0)
                        <tfoot style="background:#f8fafc;font-weight:800;border-top:2px solid #cbd5e1;">
                            <tr>
                                <td colspan="3" style="text-align:right;padding:14px;color:#002855;">TOTAL KESELURUHAN ({{ $rekapDesaKecamatan->total() }} DESA):</td>
                                <td style="text-align:center;color:#002855;">{{ number_format($stats['total_penerima']) }}</td>
                                <td style="text-align:center;color:#002855;">{{ number_format($stats['sudah_survei']) }}</td>
                                <td style="text-align:center;color:#b78100;">{{ number_format($stats['belum_survei']) }}</td>
                                <td style="text-align:center;color:#27ae60;">{{ number_format($stats['total_layak']) }}</td>
                                <td style="text-align:center;color:#e74c3c;">{{ number_format($stats['total_tidak_layak']) }}</td>
                                <td style="text-align:center;color:#002855;">
                                    {{ $stats['total_penerima'] > 0 ? round(($stats['sudah_survei'] / $stats['total_penerima']) * 100, 1) : 0 }}%
                                </td>
                                <td style="text-align:center;color:#27ae60;">
                                    {{ $stats['sudah_survei'] > 0 ? round(($stats['total_layak'] / $stats['sudah_survei']) * 100, 1) : 0 }}%
                                </td>
                            </tr>
                        </tfoot>
                        @endif
                    </table>
                </div>

                {{-- Pagination Bar Tab 1 --}}
                @if($rekapDesaKecamatan->hasPages() || $rekapDesaKecamatan->total() > 0)
                <div class="pagination-custom-bar">
                    <div class="pagination-info-text">
                        Menampilkan <strong>{{ $rekapDesaKecamatan->firstItem() ?? 0 }}</strong> -
                        <strong>{{ $rekapDesaKecamatan->lastItem() ?? 0 }}</strong> dari
                        <strong>{{ number_format($rekapDesaKecamatan->total(), 0, ',', '.') }}</strong> desa/kelurahan
                        @if($rekapDesaKecamatan->lastPage() > 1)
                            (Halaman <strong>{{ $rekapDesaKecamatan->currentPage() }}</strong> dari <strong>{{ $rekapDesaKecamatan->lastPage() }}</strong>)
                        @endif
                    </div>

                    @if($rekapDesaKecamatan->lastPage() > 1)
                        @php
                            $current = $rekapDesaKecamatan->currentPage();
                            $last = $rekapDesaKecamatan->lastPage();
                            $delta = 2;
                            $left = $current - $delta;
                            $right = $current + $delta + 1;
                            $range = [];
                            for ($i = 1; $i <= $last; $i++) {
                                if ($i == 1 || $i == $last || ($i >= $left && $i < $right)) {
                                    $range[] = $i;
                                }
                            }
                            $rangeWithDots = [];
                            $l = null;
                            foreach ($range as $i) {
                                if ($l) {
                                    if ($i - $l === 2) {
                                        $rangeWithDots[] = $l + 1;
                                    } elseif ($i - $l !== 1) {
                                        $rangeWithDots[] = '...';
                                    }
                                }
                                $rangeWithDots[] = $i;
                                $l = $i;
                            }
                        @endphp
                        <ul class="pagination-nav">
                            @if($rekapDesaKecamatan->onFirstPage())
                                <li><span class="page-btn disabled"><i class="fas fa-chevron-left"></i></span></li>
                            @else
                                <li><a href="{{ $rekapDesaKecamatan->previousPageUrl() }}" class="page-btn"><i class="fas fa-chevron-left"></i></a></li>
                            @endif

                            @foreach($rangeWithDots as $page)
                                @if($page === '...')
                                    <li><span class="page-dots">...</span></li>
                                @elseif($page == $current)
                                    <li><span class="page-btn active">{{ $page }}</span></li>
                                @else
                                    <li><a href="{{ $rekapDesaKecamatan->url($page) }}" class="page-btn">{{ $page }}</a></li>
                                @endif
                            @endforeach

                            @if($rekapDesaKecamatan->hasMorePages())
                                <li><a href="{{ $rekapDesaKecamatan->nextPageUrl() }}" class="page-btn"><i class="fas fa-chevron-right"></i></a></li>
                            @else
                                <li><span class="page-btn disabled"><i class="fas fa-chevron-right"></i></span></li>
                            @endif
                        </ul>
                    @endif
                </div>
                @endif

            @elseif($tab === 'indikator')
                {{-- TAB 2: CAPAIAN 6 INDIKATOR RTLH PER DESA & KECAMATAN --}}
                <div class="table-wrapper">
                    <table class="pupr-table">
                        <thead>
                            <tr>
                                <th style="width:40px;text-align:center;">No</th>
                                <th>Kecamatan</th>
                                <th>Desa / Kelurahan</th>
                                <th style="text-align:center;">Sudah Survei</th>
                                <th style="text-align:center;">1. Atap Rusak</th>
                                <th style="text-align:center;">2. Dinding Rusak</th>
                                <th style="text-align:center;">3. Lantai Tanah</th>
                                <th style="text-align:center;">4. Pondasi Rusak</th>
                                <th style="text-align:center;">5. Struktur Rusak</th>
                                <th style="text-align:center;">6. Penghasilan &lt; UMK</th>
                                <th style="width:65px;text-align:center;">Laporan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($rekapIndikator as $index => $row)
                                <tr>
                                    <td style="text-align:center;">{{ $rekapIndikator->firstItem() + $index }}</td>
                                    <td><strong style="color:#002855;">{{ $row->kecamatan }}</strong></td>
                                    <td><strong style="color:#0f172a;">{{ $row->desa_kelurahan }}</strong></td>
                                    <td style="text-align:center;font-weight:700;">{{ number_format($row->total_sudah_survei) }}</td>
                                    <td style="text-align:center;"><span class="badge-status danger">{{ number_format($row->atap_rtlh) }}</span></td>
                                    <td style="text-align:center;"><span class="badge-status danger">{{ number_format($row->dinding_rtlh) }}</span></td>
                                    <td style="text-align:center;"><span class="badge-status danger">{{ number_format($row->lantai_rtlh) }}</span></td>
                                    <td style="text-align:center;"><span class="badge-status danger">{{ number_format($row->pondasi_rtlh) }}</span></td>
                                    <td style="text-align:center;"><span class="badge-status danger">{{ number_format($row->struktur_rtlh) }}</span></td>
                                    <td style="text-align:center;"><span class="badge-status warning">{{ number_format($row->penghasilan_rtlh) }}</span></td>
                                    <td style="text-align:center;">
                                        <a href="{{ route('laporan.pdf_desa', ['kecamatan' => $row->kecamatan, 'desa' => $row->desa_kelurahan]) }}" target="_blank" class="btn-pdf-mini" title="Cetak PDF Resmi F4 Desa {{ $row->desa_kelurahan }}">
                                            <i class="fas fa-file-pdf"></i> PDF
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" style="text-align:center;padding:40px;color:#94a3b8;">
                                    <td colspan="11" style="text-align:center;padding:40px;color:#94a3b8;">
                                        <i class="fas fa-inbox" style="font-size:32px;display:block;margin-bottom:10px;opacity:0.4;"></i>
                                        Belum ada data indikator RTLH.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        @if($rekapIndikator->total() > 0 && isset($indTotals))
                        <tfoot style="background:#f8fafc;font-weight:800;border-top:2px solid #cbd5e1;">
                            <tr>
                                <td colspan="3" style="text-align:right;padding:14px;color:#002855;">TOTAL KESELURUHAN ({{ $rekapIndikator->total() }} DESA):</td>
                                <td style="text-align:center;color:#002855;">{{ number_format($indTotals->total_sudah_survei ?? 0) }}</td>
                                <td style="text-align:center;color:#e74c3c;">{{ number_format($indTotals->atap_rtlh ?? 0) }}</td>
                                <td style="text-align:center;color:#e74c3c;">{{ number_format($indTotals->dinding_rtlh ?? 0) }}</td>
                                <td style="text-align:center;color:#e74c3c;">{{ number_format($indTotals->lantai_rtlh ?? 0) }}</td>
                                <td style="text-align:center;color:#e74c3c;">{{ number_format($indTotals->pondasi_rtlh ?? 0) }}</td>
                                <td style="text-align:center;color:#e74c3c;">{{ number_format($indTotals->struktur_rtlh ?? 0) }}</td>
                                <td style="text-align:center;color:#b78100;">{{ number_format($indTotals->penghasilan_rtlh ?? 0) }}</td>
                            </tr>
                        </tfoot>
                        @endif
                    </table>
                </div>

                {{-- Pagination Bar Tab 2 --}}
                @if($rekapIndikator->hasPages() || $rekapIndikator->total() > 0)
                <div class="pagination-custom-bar">
                    <div class="pagination-info-text">
                        Menampilkan <strong>{{ $rekapIndikator->firstItem() ?? 0 }}</strong> -
                        <strong>{{ $rekapIndikator->lastItem() ?? 0 }}</strong> dari
                        <strong>{{ number_format($rekapIndikator->total(), 0, ',', '.') }}</strong> desa/kelurahan
                        @if($rekapIndikator->lastPage() > 1)
                            (Halaman <strong>{{ $rekapIndikator->currentPage() }}</strong> dari <strong>{{ $rekapIndikator->lastPage() }}</strong>)
                        @endif
                    </div>

                    @if($rekapIndikator->lastPage() > 1)
                        @php
                            $current = $rekapIndikator->currentPage();
                            $last = $rekapIndikator->lastPage();
                            $delta = 2;
                            $left = $current - $delta;
                            $right = $current + $delta + 1;
                            $range = [];
                            for ($i = 1; $i <= $last; $i++) {
                                if ($i == 1 || $i == $last || ($i >= $left && $i < $right)) {
                                    $range[] = $i;
                                }
                            }
                            $rangeWithDots = [];
                            $l = null;
                            foreach ($range as $i) {
                                if ($l) {
                                    if ($i - $l === 2) {
                                        $rangeWithDots[] = $l + 1;
                                    } elseif ($i - $l !== 1) {
                                        $rangeWithDots[] = '...';
                                    }
                                }
                                $rangeWithDots[] = $i;
                                $l = $i;
                            }
                        @endphp
                        <ul class="pagination-nav">
                            @if($rekapIndikator->onFirstPage())
                                <li><span class="page-btn disabled"><i class="fas fa-chevron-left"></i></span></li>
                            @else
                                <li><a href="{{ $rekapIndikator->previousPageUrl() }}" class="page-btn"><i class="fas fa-chevron-left"></i></a></li>
                            @endif

                            @foreach($rangeWithDots as $page)
                                @if($page === '...')
                                    <li><span class="page-dots">...</span></li>
                                @elseif($page == $current)
                                    <li><span class="page-btn active">{{ $page }}</span></li>
                                @else
                                    <li><a href="{{ $rekapIndikator->url($page) }}" class="page-btn">{{ $page }}</a></li>
                                @endif
                            @endforeach

                            @if($rekapIndikator->hasMorePages())
                                <li><a href="{{ $rekapIndikator->nextPageUrl() }}" class="page-btn"><i class="fas fa-chevron-right"></i></a></li>
                            @else
                                <li><span class="page-btn disabled"><i class="fas fa-chevron-right"></i></span></li>
                            @endif
                        </ul>
                    @endif
                </div>
                @endif

            @elseif($tab === 'galeri')
                {{-- TAB 3: GALERI & LAMPIRAN FOTO LAPANGAN --}}
                <div class="galeri-grid">
                    @forelse($penerimaList as $p)
                        <div class="galeri-card">
                            <div class="galeri-card-header">
                                <div>
                                    <strong style="font-size:14.5px;color:#002855;display:block;">{{ $p->nama }}</strong>
                                    <span style="font-size:11.5px;color:#64748b;"><i class="fas fa-location-dot" style="color:#e74c3c;"></i> {{ $p->desa_kelurahan }}, Kec. {{ $p->kecamatan }}</span>
                                </div>
                                <div>
                                    @if($p->status_kelayakan === 'Layak Diusulkan')
                                        <span class="badge-status success"><i class="fas fa-check"></i> Sesuai</span>
                                    @elseif($p->status_kelayakan === 'Tidak Layak Diusulkan')
                                        <span class="badge-status danger"><i class="fas fa-times"></i> Tidak Sesuai</span>
                                    @else
                                        <span class="badge-status warning">Belum Survei</span>
                                    @endif
                                </div>
                            </div>
                            <div class="galeri-card-body">
                                <div style="font-size:11.5px;color:#475569;margin-bottom:6px;">
                                    <strong>NIK:</strong> <span style="font-family:monospace;">{{ $p->no_ktp ?: '-' }}</span> &bull; <strong>Alamat:</strong> {{ $p->alamat ?: '-' }}
                                </div>

                                <div class="photo-thumbs-grid">
                                    {{-- Tampak Depan --}}
                                    <div class="photo-thumb-item" onclick="openLightbox('{{ asset($p->foto_sudut_depan ?: 'logo.jpg') }}', '{{ $p->nama }} - Tampak Depan')">
                                        <img src="{{ asset($p->foto_sudut_depan ?: 'logo.jpg') }}" alt="Depan" />
                                        <span class="photo-thumb-label">Depan</span>
                                    </div>
                                    {{-- Dalam / Interior --}}
                                    <div class="photo-thumb-item" onclick="openLightbox('{{ asset($p->foto_bagian_dalam ?: 'logo.jpg') }}', '{{ $p->nama }} - Interior')">
                                        <img src="{{ asset($p->foto_bagian_dalam ?: 'logo.jpg') }}" alt="Dalam" />
                                        <span class="photo-thumb-label">Dalam</span>
                                    </div>
                                    {{-- KTP --}}
                                    <div class="photo-thumb-item" onclick="openLightbox('{{ asset($p->ktp ?: 'logo.jpg') }}', '{{ $p->nama }} - KTP')">
                                        <img src="{{ asset($p->ktp ?: 'logo.jpg') }}" alt="KTP" />
                                        <span class="photo-thumb-label">KTP</span>
                                    </div>
                                    {{-- KK --}}
                                    <div class="photo-thumb-item" onclick="openLightbox('{{ asset($p->kk ?: 'logo.jpg') }}', '{{ $p->nama }} - Kartu Keluarga')">
                                        <img src="{{ asset($p->kk ?: 'logo.jpg') }}" alt="KK" />
                                        <span class="photo-thumb-label">KK</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div style="grid-column:1/-1;text-align:center;padding:40px;color:#94a3b8;">
                            <i class="fas fa-images" style="font-size:36px;margin-bottom:10px;opacity:0.4;"></i>
                            Tidak ada data galeri foto penerima.
                        </div>
                    @endforelse
                </div>

                {{-- Pagination Bar Tab 3 --}}
                @if($penerimaList->hasPages() || $penerimaList->total() > 0)
                <div class="pagination-custom-bar">
                    <div class="pagination-info-text">
                        Menampilkan <strong>{{ $penerimaList->firstItem() ?? 0 }}</strong> -
                        <strong>{{ $penerimaList->lastItem() ?? 0 }}</strong> dari
                        <strong>{{ number_format($penerimaList->total(), 0, ',', '.') }}</strong> penerima
                        @if($penerimaList->lastPage() > 1)
                            (Halaman <strong>{{ $penerimaList->currentPage() }}</strong> dari <strong>{{ $penerimaList->lastPage() }}</strong>)
                        @endif
                    </div>

                    @if($penerimaList->lastPage() > 1)
                        @php
                            $current = $penerimaList->currentPage();
                            $last = $penerimaList->lastPage();
                            $delta = 2;
                            $left = $current - $delta;
                            $right = $current + $delta + 1;
                            $range = [];
                            for ($i = 1; $i <= $last; $i++) {
                                if ($i == 1 || $i == $last || ($i >= $left && $i < $right)) {
                                    $range[] = $i;
                                }
                            }
                            $rangeWithDots = [];
                            $l = null;
                            foreach ($range as $i) {
                                if ($l) {
                                    if ($i - $l === 2) {
                                        $rangeWithDots[] = $l + 1;
                                    } elseif ($i - $l !== 1) {
                                        $rangeWithDots[] = '...';
                                    }
                                }
                                $rangeWithDots[] = $i;
                                $l = $i;
                            }
                        @endphp
                        <ul class="pagination-nav">
                            @if($penerimaList->onFirstPage())
                                <li><span class="page-btn disabled"><i class="fas fa-chevron-left"></i></span></li>
                            @else
                                <li><a href="{{ $penerimaList->previousPageUrl() }}" class="page-btn"><i class="fas fa-chevron-left"></i></a></li>
                            @endif

                            @foreach($rangeWithDots as $page)
                                @if($page === '...')
                                    <li><span class="page-dots">...</span></li>
                                @elseif($page == $current)
                                    <li><span class="page-btn active">{{ $page }}</span></li>
                                @else
                                    <li><a href="{{ $penerimaList->url($page) }}" class="page-btn">{{ $page }}</a></li>
                                @endif
                            @endforeach

                            @if($penerimaList->hasMorePages())
                                <li><a href="{{ $penerimaList->nextPageUrl() }}" class="page-btn"><i class="fas fa-chevron-right"></i></a></li>
                            @else
                                <li><span class="page-btn disabled"><i class="fas fa-chevron-right"></i></span></li>
                            @endif
                        </ul>
                    @endif
                </div>
                @endif

            @elseif($tab === 'detail')
                {{-- TAB 4: DETAIL DATA PENERIMA --}}
                <div class="table-wrapper">
                    <table class="pupr-table">
                        <thead>
                            <tr>
                                <th style="width:40px;text-align:center;">No</th>
                                <th>Nama Calon Penerima</th>
                                <th>NIK / KK</th>
                                <th>Kecamatan &amp; Desa</th>
                                <th style="text-align:center;">Status Kelayakan</th>
                                <th>Indikator RTLH Terpenuhi</th>
                                <th style="text-align:center;">Foto</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($penerimaList as $index => $p)
                                <tr>
                                    <td style="text-align:center;">{{ $penerimaList->firstItem() + $index }}</td>
                                    <td>
                                        <strong style="color:#002855;font-size:14px;display:block;">{{ $p->nama }}</strong>
                                        <span style="font-size:11.5px;color:#64748b;">{{ $p->alamat ?: '-' }}</span>
                                    </td>
                                    <td>
                                        <div style="font-family:monospace;font-size:12px;">KTP: {{ $p->no_ktp ?: '-' }}</div>
                                        <div style="font-family:monospace;font-size:11px;color:#64748b;">KK: {{ $p->no_kk ?: '-' }}</div>
                                    </td>
                                    <td>
                                        <div><strong style="color:#0f172a;">{{ $p->desa_kelurahan }}</strong></div>
                                        <div style="font-size:11.5px;color:#64748b;">Kec. {{ $p->kecamatan }}</div>
                                    </td>
                                    <td style="text-align:center;">
                                        @if($p->status_kelayakan === 'Layak Diusulkan')
                                            <span class="badge-status success"><i class="fas fa-check-circle"></i> Layak (Sesuai)</span>
                                        @elseif($p->status_kelayakan === 'Tidak Layak Diusulkan')
                                            <span class="badge-status danger"><i class="fas fa-times-circle"></i> Tidak Layak</span>
                                        @else
                                            <span class="badge-status warning"><i class="fas fa-clock"></i> Belum Survei</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div style="display:flex;gap:4px;flex-wrap:wrap;">
                                            @if($p->indikator_atap === 'tidak_ada') <span class="badge-status danger" style="font-size:10px;padding:2px 6px;">Atap</span> @endif
                                            @if($p->indikator_dinding === 'tidak_ada') <span class="badge-status danger" style="font-size:10px;padding:2px 6px;">Dinding</span> @endif
                                            @if($p->indikator_lantai === 'tidak_ada') <span class="badge-status danger" style="font-size:10px;padding:2px 6px;">Lantai</span> @endif
                                            @if($p->indikator_pondasi === 'tidak_ada') <span class="badge-status danger" style="font-size:10px;padding:2px 6px;">Pondasi</span> @endif
                                            @if($p->indikator_struktur === 'tidak_ada') <span class="badge-status danger" style="font-size:10px;padding:2px 6px;">Struktur</span> @endif
                                            @if($p->indikator_penghasilan === 'ada') <span class="badge-status warning" style="font-size:10px;padding:2px 6px;">Penghasilan</span> @endif
                                        </div>
                                    </td>
                                    <td style="text-align:center;">
                                        @if($p->foto_sudut_depan)
                                            <button type="button" class="btn btn-outline" style="padding:4px 10px;font-size:11px;" onclick="openLightbox('{{ asset($p->foto_sudut_depan) }}', '{{ $p->nama }} - Tampak Depan')">
                                                <i class="fas fa-camera"></i> Foto
                                            </button>
                                        @else
                                            <span style="font-size:11px;color:#94a3b8;">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" style="text-align:center;padding:40px;color:#94a3b8;">
                                        <i class="fas fa-inbox" style="font-size:32px;display:block;margin-bottom:10px;opacity:0.4;"></i>
                                        Belum ada data detail penerima.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination Bar Tab 4 --}}
                @if($penerimaList->hasPages() || $penerimaList->total() > 0)
                <div class="pagination-custom-bar">
                    <div class="pagination-info-text">
                        Menampilkan <strong>{{ $penerimaList->firstItem() ?? 0 }}</strong> -
                        <strong>{{ $penerimaList->lastItem() ?? 0 }}</strong> dari
                        <strong>{{ number_format($penerimaList->total(), 0, ',', '.') }}</strong> penerima
                        @if($penerimaList->lastPage() > 1)
                            (Halaman <strong>{{ $penerimaList->currentPage() }}</strong> dari <strong>{{ $penerimaList->lastPage() }}</strong>)
                        @endif
                    </div>

                    @if($penerimaList->lastPage() > 1)
                        @php
                            $current = $penerimaList->currentPage();
                            $last = $penerimaList->lastPage();
                            $delta = 2;
                            $left = $current - $delta;
                            $right = $current + $delta + 1;
                            $range = [];
                            for ($i = 1; $i <= $last; $i++) {
                                if ($i == 1 || $i == $last || ($i >= $left && $i < $right)) {
                                    $range[] = $i;
                                }
                            }
                            $rangeWithDots = [];
                            $l = null;
                            foreach ($range as $i) {
                                if ($l) {
                                    if ($i - $l === 2) {
                                        $rangeWithDots[] = $l + 1;
                                    } elseif ($i - $l !== 1) {
                                        $rangeWithDots[] = '...';
                                    }
                                }
                                $rangeWithDots[] = $i;
                                $l = $i;
                            }
                        @endphp
                        <ul class="pagination-nav">
                            @if($penerimaList->onFirstPage())
                                <li><span class="page-btn disabled"><i class="fas fa-chevron-left"></i></span></li>
                            @else
                                <li><a href="{{ $penerimaList->previousPageUrl() }}" class="page-btn"><i class="fas fa-chevron-left"></i></a></li>
                            @endif

                            @foreach($rangeWithDots as $page)
                                @if($page === '...')
                                    <li><span class="page-dots">...</span></li>
                                @elseif($page == $current)
                                    <li><span class="page-btn active">{{ $page }}</span></li>
                                @else
                                    <li><a href="{{ $penerimaList->url($page) }}" class="page-btn">{{ $page }}</a></li>
                                @endif
                            @endforeach

                            @if($penerimaList->hasMorePages())
                                <li><a href="{{ $penerimaList->nextPageUrl() }}" class="page-btn"><i class="fas fa-chevron-right"></i></a></li>
                            @else
                                <li><span class="page-btn disabled"><i class="fas fa-chevron-right"></i></span></li>
                            @endif
                        </ul>
                    @endif
                </div>
                @endif
            @endif
        </div>
    </main>

    <!-- Modal Export Excel (.XLS) Pilihan Lingkup -->
    <div class="modal-overlay" id="modalExportExcel">
        <div class="modal-box" style="max-width: 560px; overflow: visible !important;">
            <div class="modal-header" style="border-top-left-radius: 12px; border-top-right-radius: 12px;">
                <h3>
                    <i class="fas fa-file-excel" style="color: #107c41;"></i>
                    <span>Export Rekapitulasi BSPS (.XLS)</span>
                </h3>
                <button type="button" class="close-btn" onclick="window.PuprModal.close('modalExportExcel')">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <form action="{{ route('laporan.export') }}" method="GET" target="_blank" id="formExportExcelModal" data-no-loading="true" style="overflow: visible !important;">
                <div class="modal-body" style="padding: 22px 24px; overflow: visible !important; min-height: 200px;">
                    @if(auth()->check() && auth()->user()->isPetugas())
                        {{-- PETUGAS DESA --}}
                        <input type="hidden" name="export_scope" value="desa" />
                        <input type="hidden" name="kecamatan" value="{{ auth()->user()->kecamatan }}" />
                        <input type="hidden" name="desa" value="{{ auth()->user()->desa }}" />

                        <div style="padding: 16px; border-radius: 10px; background: rgba(0, 40, 85, 0.04); border: 1px solid rgba(0, 40, 85, 0.12);">
                            <strong style="font-size: 14px; color: #002855; display: block; margin-bottom: 6px;">
                                <i class="fas fa-user-check" style="color: #27ae60; margin-right: 6px;"></i>
                                Export Data Verval Petugas Lapangan
                            </strong>
                            <p style="font-size: 12.5px; color: #64748b; margin: 0; line-height: 1.5;">
                                File Excel akan mencakup seluruh rekapitulasi indikator dan foto verifikasi calon penerima BSPS di 
                                <strong>Desa {{ ucwords(strtolower(auth()->user()->desa)) }}</strong>, 
                                <strong>Kecamatan {{ ucwords(strtolower(auth()->user()->kecamatan)) }}</strong>.
                            </p>
                        </div>
                    @elseif(auth()->check() && auth()->user()->isAdminKecamatan())
                        {{-- ADMIN KECAMATAN --}}
                        <input type="hidden" name="export_scope" value="kecamatan" />
                        <input type="hidden" name="kecamatan" value="{{ auth()->user()->kecamatan }}" />

                        <p style="font-size: 13px; color: #64748b; margin-top: 0; margin-bottom: 14px; line-height: 1.5;">
                            Unduh rekapitulasi data BSPS untuk wilayah <strong>Kecamatan {{ ucwords(strtolower(auth()->user()->kecamatan)) }}</strong>:
                        </p>

                        <div style="margin-bottom: 14px;">
                            <label style="font-size: 12px; font-weight: 700; color: #002855; display: block; margin-bottom: 6px;">
                                Pilih Desa (Opsional):
                            </label>
                            <select name="desa" class="form-control" style="width:100%;padding:9px 12px;border-radius:8px;border:1px solid #cbd5e1;font-size:13px;">
                                <option value="all">-- Semua Desa di Kecamatan {{ ucwords(strtolower(auth()->user()->kecamatan)) }} --</option>
                                @foreach($listDesa as $d)
                                <option value="{{ $d }}">Desa {{ ucwords(strtolower($d)) }}</option>
                                @endforeach
                            </select>
                        </div>
                    @else
                        {{-- SUPER ADMIN / ADMIN KABUPATEN --}}
                        <p style="font-size: 13px; color: #64748b; margin-top: 0; margin-bottom: 18px; line-height: 1.5;">
                            Silakan pilih lingkup wilayah rekapitulasi data BSPS yang ingin Anda unduh ke format Microsoft Excel:
                        </p>

                        <!-- Pilihan Lingkup Export -->
                        <div style="display: flex; flex-direction: column; gap: 12px;">
                            <!-- Pilihan 1: Export Keseluruhan Kabupaten (Semua Desa) -->
                            <div class="export-option-card active" id="optCardAll" onclick="setExportScope('all')">
                                <div style="display: flex; align-items: flex-start; gap: 12px;">
                                    <input type="radio" name="export_scope" value="all" id="scopeAll" checked style="margin-top: 3px; cursor: pointer;" />
                                    <div>
                                        <strong style="font-size: 13.5px; color: #002855; display: block;">
                                            <i class="fas fa-globe-asia" style="color: #002855; margin-right: 6px;"></i>Export Seluruh Kabupaten (Semua Desa)
                                        </strong>
                                        <span style="font-size: 12px; color: #64748b; margin-top: 3px; display: block; line-height: 1.4;">
                                            Mendownload data rekapitulasi lengkap dari seluruh desa/kelurahan di 31 kecamatan se-Kabupaten Jember.
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Pilihan 2: Export Per Kecamatan Tertentu -->
                            <div class="export-option-card" id="optCardKecamatan" onclick="setExportScope('kecamatan')">
                                <div style="display: flex; align-items: flex-start; gap: 12px;">
                                    <input type="radio" name="export_scope" value="kecamatan" id="scopeKecamatan" style="margin-top: 3px; cursor: pointer;" />
                                    <div style="width: 100%;">
                                        <strong style="font-size: 13.5px; color: #002855; display: block;">
                                            <i class="fas fa-building-flag" style="color: #107c41; margin-right: 6px;"></i>Export Per Kecamatan Tertentu
                                        </strong>
                                        <span style="font-size: 12px; color: #64748b; margin-top: 3px; display: block; line-height: 1.4;">
                                            Hanya mendownload seluruh desa/kelurahan dan calon penerima di dalam satu kecamatan yang dipilih.
                                        </span>
                                        
                                        <!-- Dropdown Pilihan Kecamatan di dalam Modal Menggunakan PUPR Theme -->
                                        <div id="wrapperPilihKecamatanModal" style="margin-top: 12px; display: none;" onclick="event.stopPropagation();">
                                            <label style="font-size: 12px; font-weight: 700; color: #002855; display: block; margin-bottom: 6px;">
                                                Pilih Kecamatan:
                                            </label>
                <div class="modal-body" style="padding: 22px 24px; overflow: visible !important; min-height: 240px;">
                    <p style="font-size: 13px; color: #64748b; margin-top: 0; margin-bottom: 18px; line-height: 1.5;">
                        Silakan pilih lingkup wilayah rekapitulasi data BSPS yang ingin Anda unduh ke format Microsoft Excel:
                    </p>

                    <!-- Pilihan Lingkup Export -->
                    <div style="display: flex; flex-direction: column; gap: 12px;">
                        <!-- Pilihan 1: Export Keseluruhan Kabupaten (Semua Desa) -->
                        <div class="export-option-card active" id="optCardAll" onclick="setExportScope('all')">
                            <div style="display: flex; align-items: flex-start; gap: 12px;">
                                <input type="radio" name="export_scope" value="all" id="scopeAll" checked style="margin-top: 3px; cursor: pointer;" />
                                <div>
                                    <strong style="font-size: 13.5px; color: #002855; display: block;">
                                        <i class="fas fa-globe-asia" style="color: #002855; margin-right: 6px;"></i>Export Seluruh Kabupaten (Semua Desa)
                                    </strong>
                                    <span style="font-size: 12px; color: #64748b; margin-top: 3px; display: block; line-height: 1.4;">
                                        Mendownload data rekapitulasi lengkap dari seluruh desa/kelurahan di 31 kecamatan se-Kabupaten Jember.
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Pilihan 2: Export Per Kecamatan Tertentu -->
                        <div class="export-option-card" id="optCardKecamatan" onclick="setExportScope('kecamatan')">
                            <div style="display: flex; align-items: flex-start; gap: 12px;">
                                <input type="radio" name="export_scope" value="kecamatan" id="scopeKecamatan" style="margin-top: 3px; cursor: pointer;" />
                                <div style="width: 100%;">
                                    <strong style="font-size: 13.5px; color: #002855; display: block;">
                                        <i class="fas fa-building-flag" style="color: #107c41; margin-right: 6px;"></i>Export Per Kecamatan Tertentu
                                    </strong>
                                    <span style="font-size: 12px; color: #64748b; margin-top: 3px; display: block; line-height: 1.4;">
                                        Hanya mendownload seluruh desa/kelurahan dan calon penerima di dalam satu kecamatan yang dipilih.
                                    </span>
                                    
                                    <!-- Dropdown Pilihan Kecamatan di dalam Modal Menggunakan PUPR Theme -->
                                    <div id="wrapperPilihKecamatanModal" style="margin-top: 12px; display: none;" onclick="event.stopPropagation();">
                                        <label style="font-size: 12px; font-weight: 700; color: #002855; display: block; margin-bottom: 6px;">
                                            Pilih Kecamatan:
                                        </label>
                                        @if(auth()->check() && auth()->user()->isAdminKecamatan())
                                            <input type="hidden" name="kecamatan" value="{{ auth()->user()->kecamatan }}" />
                                            <div style="padding: 9px 14px; border-radius: 8px; background: #f1f5f9; border: 1px solid #cbd5e1; font-weight: 700; font-size: 13px; color: #002855; display: flex; align-items: center; gap: 8px;">
                                                <i class="fas fa-building-flag" style="color: #002855;"></i>
                                                <span>Kec. {{ ucwords(strtolower(auth()->user()->kecamatan)) }}</span>
                                            </div>
                                        @else
                                            @php
                                                $firstKec = $listKecamatan->first() ?? '';
                                            @endphp
                                            <input type="hidden" name="kecamatan" id="modalInputKecamatan" value="{{ $firstKec }}" disabled />
                                            <div class="pupr-dropdown-wrapper" id="ddModalKecamatanWrapper" style="width: 100%;">
                                                <button type="button" class="pupr-dropdown-toggle" style="width: 100%;" onclick="event.stopPropagation(); window.PuprDropdown.toggle(document.getElementById('ddModalKecamatanWrapper')); setTimeout(() => { const s = document.getElementById('inputSearchKecamatanModal'); if(s) s.focus(); }, 100);">
                                                <button type="button" class="pupr-dropdown-toggle" style="width: 100%;" onclick="event.stopPropagation(); window.PuprDropdown.toggle(document.getElementById('ddModalKecamatanWrapper'))">
                                                    <span style="display:flex;align-items:center;gap:6px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                                                        <i class="fas fa-building-flag" style="font-size:12px;opacity:0.6;"></i>
                                                        <span class="selected-label" id="modalSelectedKecLabel">
                                                            Kec. {{ ucwords(strtolower($firstKec)) }}
                                                        </span>
                                                    </span>
                                                    <i class="fas fa-chevron-down" style="font-size:10px;opacity:0.5;"></i>
                                                </button>
                                                <div class="pupr-dropdown-menu" style="min-width: 220px; max-height: 230px; overflow-y: auto; width: 100%; z-index: 1050; box-shadow: 0 12px 30px rgba(0, 40, 85, 0.22); padding: 6px;">
                                                    <div style="padding: 4px 6px 8px 6px; position: sticky; top: 0; background: #fff; z-index: 10;" onclick="event.stopPropagation();">
                                                        <div style="position: relative;">
                                                            <i class="fas fa-search" style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%); font-size: 11px; color: #94a3b8;"></i>
                                                            <input type="text" id="inputSearchKecamatanModal" placeholder="Cari nama kecamatan..." onkeyup="filterModalKecamatanList(this.value)" style="width: 100%; padding: 6px 10px 6px 28px; border-radius: 6px; border: 1px solid #cbd5e1; font-size: 12px; outline: none; box-sizing: border-box;" />
                                                        </div>
                                                    </div>
                                                    <div id="containerModalKecItems">
                                                        @foreach($listKecamatan as $kec)
                                                        <div class="pupr-dropdown-item {{ $firstKec === $kec ? 'active' : '' }}"
                                                             data-kec="{{ strtolower($kec) }}"
                                                             onclick="event.stopPropagation(); selectModalKecamatan('{{ $kec }}', 'Kec. {{ ucwords(strtolower($kec)) }}', this)">
                                                            <i class="fas fa-map-pin" style="font-size:11px;opacity:0.5;"></i> Kec. {{ ucwords(strtolower($kec)) }}
                                                        </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                                <div class="pupr-dropdown-menu" style="min-width: 220px; max-height: 190px; overflow-y: auto; width: 100%; z-index: 1050; box-shadow: 0 12px 30px rgba(0, 40, 85, 0.22);">
                                                    @foreach($listKecamatan as $kec)
                                                    <div class="pupr-dropdown-item {{ $firstKec === $kec ? 'active' : '' }}"
                                                         onclick="event.stopPropagation(); selectModalKecamatan('{{ $kec }}', 'Kec. {{ ucwords(strtolower($kec)) }}', this)">
                                                        <i class="fas fa-map-pin" style="font-size:11px;opacity:0.5;"></i> Kec. {{ ucwords(strtolower($kec)) }}
                                                    </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                    </div>
                </div>

                <div class="modal-footer" style="padding: 14px 24px; border-top: 1px solid #e2e8f0; display: flex; justify-content: flex-end; gap: 10px; background: #f8fafc; border-bottom-left-radius: 12px; border-bottom-right-radius: 12px; position: relative; z-index: 2;">
                    <button type="button" class="btn btn-outline" onclick="window.PuprModal.close('modalExportExcel')" style="padding: 8px 16px; border-radius: 8px; font-weight: 600; font-size: 13px;">
                        Batal
                    </button>
                    <button type="submit" class="btn-export-excel" onclick="setTimeout(() => { window.PuprModal.close('modalExportExcel'); if (window.PuprLoading) window.PuprLoading.hide(); }, 300)" style="padding: 8px 18px; border-radius: 8px; font-weight: 700; cursor: pointer; border: none; font-size: 13px;">
                        <i class="fas fa-download"></i>
                        <span>Download File Excel</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Cetak PDF per Desa (Kertas F4) -->
    <div class="modal-overlay" id="modalPdfDesa">
        <div class="modal-box" style="max-width: 520px; overflow: visible !important;">
            <div class="modal-header" style="border-top-left-radius: 12px; border-top-right-radius: 12px;">
                <h3>
                    <i class="fas fa-file-pdf" style="color: #dc2626;"></i>
                    <span>Cetak Laporan PDF Resmi Desa (F4)</span>
                </h3>
                <button type="button" class="close-btn" onclick="window.PuprModal.close('modalPdfDesa')">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <form action="{{ route('laporan.pdf_desa') }}" method="GET" target="_blank" id="formPdfDesaModal" data-no-loading="true" style="overflow: visible !important;">
                <div class="modal-body" style="padding: 22px 24px; overflow: visible !important;">
                    <div style="background: #fef2f2; border: 1px solid #fecaca; border-radius: 8px; padding: 12px 14px; margin-bottom: 18px; display: flex; gap: 10px; align-items: flex-start;">
                        <i class="fas fa-info-circle" style="color: #dc2626; font-size: 16px; margin-top: 2px;"></i>
                        <div style="font-size: 12px; color: #7f1d1d; line-height: 1.4;">
                            Dokumen resmi format <strong>Kertas F4 (Folio)</strong> lengkap dengan <strong>Kop Surat Pemkab Jember / Dinas PUPR Cipta Karya</strong>, tabel rekapitulasi formal, dan lembar tanda tangan 2 pihak.
                        </div>
                    </div>

                    @php
                        $firstKecPdf = (auth()->check() && auth()->user()->isAdminKecamatan()) ? auth()->user()->kecamatan : (request('kecamatan') && request('kecamatan') !== 'all' ? request('kecamatan') : ($listKecamatan->first() ?? ''));
                        $initialDesaList = isset($allDesaByKecamatan[$firstKecPdf]) ? $allDesaByKecamatan[$firstKecPdf] : $listDesa;
                        $firstDesaPdf = $initialDesaList->first() ?? '';
                    @endphp

                    {{-- Pilihan Kecamatan Custom Dropdown --}}
                    @if(!auth()->check() || !auth()->user()->isAdminKecamatan())
                    <div class="form-group" style="margin-bottom: 16px;">
                        <label style="font-size: 12px; font-weight: 700; color: #002855; display: block; margin-bottom: 6px;">
                            Pilih Kecamatan:
                        </label>
                        <input type="hidden" name="kecamatan" id="pdfInputKecamatan" value="{{ $firstKecPdf }}" />
                        <div class="pupr-dropdown-wrapper" id="ddPdfKecamatanWrapper" style="width: 100%;">
                            <button type="button" class="pupr-dropdown-toggle" style="width: 100%;" onclick="event.stopPropagation(); window.PuprDropdown.toggle(document.getElementById('ddPdfKecamatanWrapper'))">
                                <span style="display:flex;align-items:center;gap:6px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                                    <i class="fas fa-building-flag" style="font-size:12px;opacity:0.6;"></i>
                                    <span class="selected-label" id="pdfSelectedKecLabel">
                                        Kec. {{ ucwords(strtolower($firstKecPdf)) }}
                                    </span>
                                </span>
                                <i class="fas fa-chevron-down" style="font-size:10px;opacity:0.5;"></i>
                            </button>
                            <div class="pupr-dropdown-menu" style="min-width: 220px; max-height: 190px; overflow-y: auto; width: 100%; z-index: 1060; box-shadow: 0 12px 30px rgba(0, 40, 85, 0.22);">
                                @foreach($listKecamatan as $kec)
                                <div class="pupr-dropdown-item {{ $firstKecPdf === $kec ? 'active' : '' }}"
                                     onclick="event.stopPropagation(); selectPdfModalKecamatan('{{ $kec }}', 'Kec. {{ ucwords(strtolower($kec)) }}', this)">
                                    <i class="fas fa-map-pin" style="font-size:11px;opacity:0.5;"></i> Kec. {{ ucwords(strtolower($kec)) }}
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @else
                        <input type="hidden" name="kecamatan" value="{{ auth()->user()->kecamatan }}" id="pdfInputKecamatan" />
                        <div class="form-group" style="margin-bottom: 16px;">
                            <label style="font-size: 12px; font-weight: 700; color: #002855; display: block; margin-bottom: 6px;">
                                Wilayah Kecamatan:
                            </label>
                            <div style="padding: 9px 14px; border-radius: 8px; background: #f1f5f9; border: 1px solid #cbd5e1; font-weight: 700; font-size: 13px; color: #002855; display: flex; align-items: center; gap: 8px;">
                                <i class="fas fa-building-flag" style="color: #002855;"></i>
                                <span>Kec. {{ ucwords(strtolower(auth()->user()->kecamatan)) }}</span>
                            </div>
                        </div>
                    @endif

                    {{-- Pilihan Desa Custom Dropdown --}}
                    <div class="form-group" style="margin-bottom: 16px;">
                        <label style="font-size: 12px; font-weight: 700; color: #002855; display: block; margin-bottom: 6px;">
                            Pilih Desa / Kelurahan:
                        </label>
                        <input type="hidden" name="desa" id="pdfInputDesa" value="{{ $firstDesaPdf }}" />
                        <div class="pupr-dropdown-wrapper" id="ddPdfDesaWrapper" style="width: 100%;">
                            <button type="button" class="pupr-dropdown-toggle" style="width: 100%;" onclick="event.stopPropagation(); window.PuprDropdown.toggle(document.getElementById('ddPdfDesaWrapper'))">
                                <span style="display:flex;align-items:center;gap:6px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                                    <i class="fas fa-house-chimney" style="font-size:12px;opacity:0.6;"></i>
                                    <span class="selected-label" id="pdfSelectedDesaLabel">
                                        Desa {{ ucwords(strtolower($firstDesaPdf)) }}
                                    </span>
                                </span>
                                <i class="fas fa-chevron-down" style="font-size:10px;opacity:0.5;"></i>
                            </button>
                            <div class="pupr-dropdown-menu" id="pdfDesaDropdownMenu" style="min-width: 220px; max-height: 190px; overflow-y: auto; width: 100%; z-index: 1055; box-shadow: 0 12px 30px rgba(0, 40, 85, 0.22);">
                                @foreach($initialDesaList as $d)
                                <div class="pupr-dropdown-item {{ $firstDesaPdf === $d ? 'active' : '' }}"
                                     onclick="event.stopPropagation(); selectPdfModalDesa('{{ $d }}', 'Desa {{ ucwords(strtolower($d)) }}', this)">
                                    <i class="fas fa-map-marker-alt" style="font-size:11px;opacity:0.5;"></i> Desa {{ ucwords(strtolower($d)) }}
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    {{-- Opsi Aksi Cetak / Download --}}
                    <div class="form-group" style="margin-bottom: 6px;">
                        <label style="font-size: 12px; font-weight: 700; color: #002855; display: block; margin-bottom: 6px;">
                            Tindakan Dokumen:
                        </label>
                        <div style="display: flex; gap: 12px;">
                            <label style="display: flex; align-items: center; gap: 6px; font-size: 12.5px; cursor: pointer; color: #002855; font-weight: 600;">
                                <input type="radio" name="mode" value="stream" checked style="cursor: pointer;" />
                                Buka di Tab Baru (Print Langsung)
                            </label>
                            <label style="display: flex; align-items: center; gap: 6px; font-size: 12.5px; cursor: pointer; color: #002855; font-weight: 600;">
                                <input type="radio" name="mode" value="download" style="cursor: pointer;" />
                                Download File PDF (.PDF)
                            </label>
                        </div>
                    </div>
                </div>

                <div class="modal-footer" style="padding: 14px 24px; border-top: 1px solid #e2e8f0; display: flex; justify-content: flex-end; gap: 10px; background: #f8fafc; border-bottom-left-radius: 12px; border-bottom-right-radius: 12px;">
                    <button type="button" class="btn btn-outline" onclick="window.PuprModal.close('modalPdfDesa')" style="padding: 8px 16px; border-radius: 8px; font-weight: 600; font-size: 13px;">
                        Batal
                    </button>
                    <button type="submit" class="btn-export-pdf" onclick="setTimeout(() => { window.PuprModal.close('modalPdfDesa'); if (window.PuprLoading) window.PuprLoading.hide(); }, 300)" style="padding: 8px 18px; border-radius: 8px; font-weight: 700; cursor: pointer; border: none; font-size: 13px;">
                        <i class="fas fa-file-pdf"></i>
                        <span>Cetak PDF Resmi F4</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Lightbox Viewer Foto -->
    <div class="lightbox-modal" id="lightboxModal">
        <div class="lightbox-content">
            <div class="lightbox-header">
                <strong id="lightboxTitle">Pratinjau Foto Lapangan</strong>
                <button type="button" onclick="closeLightbox()" style="background:transparent;border:none;color:#fff;font-size:18px;cursor:pointer;"><i class="fas fa-times"></i></button>
            </div>
            <div class="lightbox-body">
                <img id="lightboxImg" src="" alt="Full Preview" />
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    // Data Pemetaan Desa per Kecamatan untuk Modal PDF
    const mapDesaPerKecamatan = @json($allDesaByKecamatan ?? []);

    function selectPdfModalKecamatan(kec, label, el) {
        const inputKec = document.getElementById('pdfInputKecamatan');
        const labelEl = document.getElementById('pdfSelectedKecLabel');
        const wrapper = document.getElementById('ddPdfKecamatanWrapper');

        if (inputKec) inputKec.value = kec;
        if (labelEl) labelEl.textContent = label;
        if (wrapper) {
            wrapper.querySelectorAll('.pupr-dropdown-item').forEach(i => i.classList.remove('active'));
            if (el) el.classList.add('active');
            wrapper.classList.remove('active');
        }

        // Update list desa di dropdown desa secara dinamis
        updatePdfDesaDropdown(kec);
    }

    function updatePdfDesaDropdown(selectedKec) {
        const menuEl = document.getElementById('pdfDesaDropdownMenu');
        const inputDesa = document.getElementById('pdfInputDesa');
        const labelDesa = document.getElementById('pdfSelectedDesaLabel');
        if (!menuEl) return;

        menuEl.innerHTML = '';
        const listDesa = mapDesaPerKecamatan[selectedKec] || [];

        if (listDesa.length > 0) {
            const firstDesa = listDesa[0];
            const firstDesaLabel = 'Desa ' + firstDesa.toLowerCase().replace(/\b\w/g, s => s.toUpperCase());

            if (inputDesa) inputDesa.value = firstDesa;
            if (labelDesa) labelDesa.textContent = firstDesaLabel;

            listDesa.forEach((d, idx) => {
                const itemLabel = 'Desa ' + d.toLowerCase().replace(/\b\w/g, s => s.toUpperCase());
                const item = document.createElement('div');
                item.className = 'pupr-dropdown-item' + (idx === 0 ? ' active' : '');
                item.innerHTML = '<i class="fas fa-map-marker-alt" style="font-size:11px;opacity:0.5;"></i> ' + itemLabel;
                item.onclick = function(e) {
                    e.stopPropagation();
                    selectPdfModalDesa(d, itemLabel, item);
                };
                menuEl.appendChild(item);
            });
        } else {
            if (inputDesa) inputDesa.value = '';
            if (labelDesa) labelDesa.textContent = 'Semua Desa';
        }
    }

    function selectPdfModalDesa(desa, label, el) {
        const inputDesa = document.getElementById('pdfInputDesa');
        const labelEl = document.getElementById('pdfSelectedDesaLabel');
        const wrapper = document.getElementById('ddPdfDesaWrapper');

        if (inputDesa) inputDesa.value = desa;
        if (labelEl) labelEl.textContent = label;
        if (wrapper) {
            wrapper.querySelectorAll('.pupr-dropdown-item').forEach(i => i.classList.remove('active'));
            if (el) el.classList.add('active');
            wrapper.classList.remove('active');
        }
    }

    function setExportScope(scope) {
        const optAll = document.getElementById('optCardAll');
        const optKec = document.getElementById('optCardKecamatan');
        const radioAll = document.getElementById('scopeAll');
        const radioKec = document.getElementById('scopeKecamatan');
        const wrapperKec = document.getElementById('wrapperPilihKecamatanModal');
        const inputKec = document.getElementById('modalInputKecamatan');

        if (scope === 'all') {
            optAll.classList.add('active');
            optKec.classList.remove('active');
            radioAll.checked = true;
            radioKec.checked = false;
            if (wrapperKec) wrapperKec.style.display = 'none';
            if (inputKec) inputKec.disabled = true;
        } else {
            optAll.classList.remove('active');
            optKec.classList.add('active');
            radioAll.checked = false;
            radioKec.checked = true;
            if (wrapperKec) wrapperKec.style.display = 'block';
            if (inputKec) inputKec.disabled = false;
        }
    }

    function filterModalKecamatanList(query) {
        const term = query.toLowerCase().trim();
        const items = document.querySelectorAll('#containerModalKecItems .pupr-dropdown-item');
        items.forEach(item => {
            const text = (item.getAttribute('data-kec') || item.textContent).toLowerCase();
            if (text.includes(term)) {
                item.style.display = 'flex';
            } else {
                item.style.display = 'none';
            }
        });
    }

    function selectModalKecamatan(val, label, el) {
        const inputKec = document.getElementById('modalInputKecamatan');
        const labelEl = document.getElementById('modalSelectedKecLabel');
        const wrapper = document.getElementById('ddModalKecamatanWrapper');

        if (inputKec) inputKec.value = val;
        if (labelEl) labelEl.textContent = label;
        if (wrapper) {
            wrapper.querySelectorAll('.pupr-dropdown-item').forEach(i => i.classList.remove('active'));
            if (el) el.classList.add('active');
            wrapper.classList.remove('active');
        }
    }

    function openLightbox(src, title) {
        document.getElementById('lightboxImg').src = src;
        document.getElementById('lightboxTitle').innerText = title || 'Pratinjau Foto Lapangan';
        document.getElementById('lightboxModal').classList.add('active');
    }

    function closeLightbox() {
        document.getElementById('lightboxModal').classList.remove('active');
    }

    document.getElementById('lightboxModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeLightbox();
        }
    });
</script>
@endpush
