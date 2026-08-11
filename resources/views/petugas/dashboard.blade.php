@extends('layouts.partial.app')

@section('title', 'BSPS Verval - Dashboard Petugas')
@section('title_header', 'Dashboard Petugas Lapangan')
@section('subtitle_header', 'Monitoring & Verifikasi Calon Penerima BSPS Wilayah Desa {{ Auth::user()->desa ?? "-" }}')

@push('styles')
<style>
    /* Hero Banner Petugas */
    .welcome-card {
        background: linear-gradient(135deg, #001835 0%, #002855 50%, #004080 100%);
        color: #ffffff;
        border-radius: 16px;
        padding: 24px 30px;
        margin-bottom: 24px;
        box-shadow: 0 10px 28px rgba(0, 40, 85, 0.22);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        border-left: 6px solid #ffb800;
        width: 100%;
        box-sizing: border-box;
        position: relative;
        overflow: hidden;
    }
    .welcome-card::after {
        content: '';
        position: absolute;
        right: -30px;
        top: -30px;
        width: 220px;
        height: 220px;
        background: radial-gradient(circle, rgba(255, 184, 0, 0.14) 0%, rgba(255, 184, 0, 0) 70%);
        border-radius: 50%;
        pointer-events: none;
    }
    .welcome-text { flex: 1; min-width: 260px; z-index: 1; }
    .welcome-text h2 { font-size: 22px; font-weight: 800; margin-bottom: 6px; color: #ffffff; letter-spacing: -0.2px; }
    .welcome-text p  { font-size: 13.5px; opacity: 0.92; margin: 0; color: rgba(255,255,255,0.9); line-height: 1.5; }
    .welcome-actions {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-shrink: 0;
        flex-wrap: wrap;
        z-index: 1;
    }
    @media (max-width: 1024px) {
        .welcome-card {
            flex-direction: column;
            align-items: flex-start;
            gap: 16px;
            padding: 20px 24px;
        }
        .welcome-actions {
            width: 100%;
            flex-wrap: wrap;
        }
    }

    /* Stats Grid Responsive 5-Column System */
    .stats-grid-petugas {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 16px;
        margin-bottom: 24px;
        width: 100%;
        box-sizing: border-box;
    }
    @media (max-width: 1280px) {
        .stats-grid-petugas {
            grid-template-columns: repeat(3, 1fr);
        }
    }
    @media (max-width: 840px) {
        .stats-grid-petugas {
            grid-template-columns: repeat(2, 1fr);
        }
    }
    @media (max-width: 520px) {
        .stats-grid-petugas {
            grid-template-columns: 1fr;
            gap: 12px;
        }
    }

    .stat-card-petugas {
        background: var(--bg-card);
        border-radius: 14px;
        padding: 18px 20px;
        box-shadow: var(--shadow-sm);
        border: 1px solid rgba(0, 40, 85, 0.08);
        display: flex;
        align-items: center;
        gap: 14px;
        text-decoration: none;
        color: inherit;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        min-width: 0;
        box-sizing: border-box;
        position: relative;
        overflow: hidden;
    }
    .stat-card-petugas:hover {
        transform: translateY(-4px);
        box-shadow: 0 10px 24px rgba(0, 40, 85, 0.14);
        border-color: var(--primary);
    }
    .stat-card-petugas .stat-icon {
        width: 48px; height: 48px;
        border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        font-size: 20px; flex-shrink: 0;
    }
    .stat-card-petugas .stat-icon.blue   { background: rgba(0,40,85,0.08);   color: var(--primary); }
    .stat-card-petugas .stat-icon.orange { background: rgba(255,184,0,0.15); color: #d69e00; }
    .stat-card-petugas .stat-icon.green  { background: rgba(34,197,94,0.14); color: #15803d; }
    .stat-card-petugas .stat-icon.cyan   { background: rgba(6,182,212,0.14);  color: #0891b2; }
    .stat-card-petugas .stat-icon.purple { background: rgba(142,68,173,0.12); color: #7e22ce; }
    
    .stat-card-petugas .stat-info { flex: 1; min-width: 0; }
    .stat-card-petugas .stat-value { font-size: 24px; font-weight: 800; line-height: 1.1; color: var(--primary-dark); }
    .stat-card-petugas .stat-label {
        font-size: 12px; color: var(--text-muted); font-weight: 600; margin-top: 4px; line-height: 1.35;
        white-space: normal; word-break: break-word;
    }

    /* Chart Grid Section */
    .petugas-charts-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 20px;
        margin-bottom: 24px;
        width: 100%;
        box-sizing: border-box;
    }
    .chart-box {
        background: var(--bg-card);
        border-radius: var(--radius);
        padding: 22px;
        box-shadow: var(--shadow-sm);
        border: 1px solid rgba(0,40,85,0.06);
        display: flex;
        flex-direction: column;
    }
    .chart-box-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 16px;
        border-bottom: 1px solid rgba(0,40,85,0.06);
        padding-bottom: 12px;
    }
    .chart-box-header h4 {
        margin: 0;
        font-size: 14px;
        font-weight: 800;
        color: var(--primary-dark);
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .chart-canvas-wrapper {
        position: relative;
        height: 200px;
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .chart-legend-custom {
        display: flex;
        justify-content: center;
        gap: 14px;
        margin-top: 14px;
        flex-wrap: wrap;
        font-size: 12px;
        font-weight: 700;
    }
    .legend-item { display: inline-flex; align-items: center; gap: 6px; }
    .legend-dot { width: 10px; height: 10px; border-radius: 50%; display: inline-block; }

    /* Filter Bar */
    .filter-section {
        background: var(--bg-card);
        border-radius: var(--radius);
        padding: 16px 20px;
        margin-bottom: 18px;
        box-shadow: var(--shadow-sm);
        border: 1px solid rgba(0,40,85,0.06);
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
    }
    .search-input-wrap { position: relative; flex: 1; min-width: 260px; }
    .search-input-wrap input {
        width: 100%; padding: 10px 14px 10px 38px;
        border-radius: var(--radius-sm);
        border: 1px solid rgba(0,40,85,0.14);
        background: var(--bg-body); color: var(--text-primary);
        font-size: 13.5px; outline: none; box-sizing: border-box;
    }
    .search-input-wrap input:focus { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(0,40,85,0.08); background: var(--bg-card); }
    .search-input-wrap i { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 14px; }

    /* Table Container Card */
    .table-container-card {
        background: var(--bg-card);
        border-radius: var(--radius);
        box-shadow: var(--shadow-sm);
        border: 1px solid rgba(0, 40, 85, 0.06);
        overflow: hidden;
        margin-bottom: 24px;
    }
    .table-header-bar {
        padding: 18px 24px;
        border-bottom: 1px solid rgba(0,40,85,0.06);
        display: flex; align-items: center; justify-content: space-between; gap: 12px;
        flex-wrap: wrap;
    }
    .table-header-bar h3 { font-size: 15px; font-weight: 800; color: var(--primary-dark); display: flex; align-items: center; gap: 8px; margin: 0; }

    .badge-status-survey {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 11.5px;
        font-weight: 700;
    }
    .badge-status-survey.belum { background: rgba(255, 184, 0, 0.15); color: #b88600; }
    .badge-status-survey.sudah { background: rgba(39, 174, 96, 0.12); color: var(--success); }

    .badge-gender {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 26px;
        height: 26px;
        border-radius: 6px;
        font-size: 11px;
        font-weight: 800;
    }
    .badge-gender.l { background: rgba(0, 40, 85, 0.10); color: var(--primary); }
    .badge-gender.p { background: rgba(212, 63, 120, 0.12); color: #d43f78; }

    .btn-act {
        padding: 7px 14px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 800;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s ease;
        cursor: pointer;
        border: none;
    }
    .btn-act.survey { background: var(--primary); color: #fff; box-shadow: 0 2px 6px rgba(0,40,85,0.15); }
    .btn-act.survey:hover { background: var(--primary-dark); color: #fff; transform: translateY(-1px); }

    /* Custom Pagination Styling */
    .pagination-custom-bar {
        padding: 16px 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
        border-top: 1px solid rgba(0, 40, 85, 0.08);
        background: var(--bg-card);
    }
    .pagination-info-text { font-size: 13px; color: var(--text-muted); font-weight: 500; }
    .pagination-info-text strong { color: var(--primary-dark); font-weight: 700; }
    .pagination-nav { display: inline-flex; align-items: center; gap: 6px; margin: 0; padding: 0; }
    .pg-link {
        display: inline-flex; align-items: center; justify-content: center;
        min-width: 36px; height: 36px; padding: 0 12px;
        border-radius: 8px; font-size: 13px; font-weight: 700;
        color: var(--text-primary); background: var(--bg-body);
        border: 1px solid rgba(0, 40, 85, 0.14); text-decoration: none;
        transition: all 0.2s ease;
    }
    .pg-link:hover { background: var(--primary); color: #fff; border-color: var(--primary); transform: translateY(-1px); }
    .pg-link.active { background: var(--primary); color: #fff; border-color: var(--primary); box-shadow: 0 2px 6px rgba(0, 40, 85, 0.25); }
    .pg-link.disabled { opacity: 0.4; cursor: not-allowed; pointer-events: none; }
    .pg-dots { display: inline-flex; align-items: center; justify-content: center; min-width: 30px; height: 36px; font-size: 14px; font-weight: 700; color: var(--text-muted); letter-spacing: 2px; }

    .table-petugas-wrapper {
        width: 100%;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    .table-container-card table {
        width: 100%;
        min-width: 880px;
        border-collapse: collapse;
        white-space: nowrap;
    }

    .table-container-card table th,
    .table-container-card table td {
        white-space: nowrap;
    }

    @media (max-width: 1100px) {
        .petugas-charts-grid { grid-template-columns: 1fr; }
    }

    @media (max-width: 768px) {
        .welcome-card {
            flex-direction: column;
            align-items: flex-start;
            gap: 16px;
            padding: 20px;
        }
        .welcome-actions {
            width: 100%;
        }
        .welcome-actions .btn {
            width: 100%;
            justify-content: center;
        }
        .table-header-bar {
            flex-direction: column;
            align-items: flex-start;
            gap: 8px;
        }
        .pagination-custom-bar {
            flex-direction: column;
            align-items: center;
            gap: 12px;
            text-align: center;
        }
    }

    @media (max-width: 576px) {
        .stat-card-petugas { padding: 14px 16px; gap: 12px; border-radius: 12px; }
        .stat-card-petugas .stat-icon { width: 42px; height: 42px; font-size: 18px; border-radius: 10px; }
        .stat-card-petugas .stat-value { font-size: 20px; }
        .stat-card-petugas .stat-label { font-size: 11.5px; }
        .chart-box { padding: 16px; }
        .chart-canvas-wrapper { height: 180px; }
        .filter-section { flex-direction: column; align-items: stretch; padding: 14px; gap: 10px; }
        .search-input-wrap { min-width: 100%; }
        .pupr-dropdown-wrapper, .pupr-dropdown-toggle { width: 100%; }
        .pupr-dropdown-toggle { justify-content: space-between; }
        .pagination-nav { flex-wrap: wrap; justify-content: center; }
    }
</style>
@endpush

@section('content')
    <!-- Navbar Component -->
    @include('layouts.navbar')

    <main class="dashboard-content">
        <!-- Breadcrumb -->
        <div class="breadcrumb" style="font-size:13px;color:var(--text-muted);margin-bottom:20px;display:flex;align-items:center;gap:8px;">
            <span><i class="fas fa-home"></i> Dashboard Petugas</span>
            <i class="fas fa-chevron-right" style="font-size:10px;"></i>
            <span>Desa {{ $user->desa ?: '-' }}</span>
        </div>

        <!-- Alert Success & Error Banners -->
        @if(session('success'))
            <div class="alert alert-success" style="background:#f0fdf4;border:1px solid #bbf7d0;color:#166534;border-radius:12px;padding:16px 20px;margin-bottom:20px;display:flex;align-items:center;gap:14px;box-shadow:0 4px 16px rgba(34,197,94,0.12);animation:fadeIn 0.4s ease;">
                <div style="width:36px;height:36px;border-radius:50%;background:rgba(34,197,94,0.18);color:#22c55e;display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0;">
                    <i class="fas fa-circle-check"></i>
                </div>
                <div style="font-weight:700;font-size:14px;line-height:1.4;">{{ session('success') }}</div>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger" style="background:#fef2f2;border:1px solid #fecaca;color:#991b1b;border-radius:12px;padding:16px 20px;margin-bottom:20px;display:flex;align-items:center;gap:14px;box-shadow:0 4px 16px rgba(239,68,68,0.12);animation:fadeIn 0.4s ease;">
                <div style="width:36px;height:36px;border-radius:50%;background:rgba(239,68,68,0.18);color:#ef4444;display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0;">
                    <i class="fas fa-triangle-exclamation"></i>
                </div>
                <div style="font-weight:700;font-size:14px;line-height:1.4;">{{ session('error') }}</div>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger" style="background:#fef2f2;border:1px solid #fecaca;color:#991b1b;border-radius:12px;padding:16px 20px;margin-bottom:20px;box-shadow:0 4px 16px rgba(239,68,68,0.12);animation:fadeIn 0.4s ease;">
                <div style="display:flex;align-items:center;gap:14px;margin-bottom:6px;">
                    <div style="width:36px;height:36px;border-radius:50%;background:rgba(239,68,68,0.18);color:#ef4444;display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0;">
                        <i class="fas fa-triangle-exclamation"></i>
                    </div>
                    <div style="font-weight:800;font-size:14px;">Gagal Menyimpan Usulan Baru:</div>
                </div>
                <ul style="margin:0 0 0 50px;padding:0;font-size:13px;font-weight:600;line-height:1.5;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Welcome Banner Petugas -->
        <div class="welcome-card">
            <div class="welcome-text">
                <h2>Selamat Datang, {{ $user->name }}!</h2>
                <p>
                    <i class="fas fa-location-dot" style="margin-right:4px;color:#ffb800;"></i> Wilayah Tugas: <strong>Desa {{ $user->desa ?: '-' }}</strong> &bull; Kecamatan <strong>{{ $user->kecamatan ?: '-' }}</strong>
                </p>
            </div>
            <div class="welcome-actions">
                <button type="button" class="btn" style="background:#22c55e;color:#fff;font-weight:800;padding:10.5px 18px;border-radius:8px;border:none;cursor:pointer;display:inline-flex;align-items:center;gap:8px;box-shadow:0 4px 14px rgba(34,197,94,0.35);" onclick="window.PuprModal.open('modalTambahUsulan')" title="Tambah Usulan Calon Penerima Baru di Desa {{ $user->desa ?: '-' }}">
                    <i class="fas fa-plus-circle"></i> Tambah Usulan
                </button>
                <a href="{{ route('verval-data.surat-pernyataan-kolektif', array_merge(['desa' => $user->desa], request()->all())) }}" target="_blank" class="btn" style="background:#ffb800;color:#002855;font-weight:800;padding:10.5px 18px;border-radius:8px;text-decoration:none;display:inline-flex;align-items:center;gap:8px;box-shadow:0 4px 12px rgba(255,184,0,0.3);" title="Cetak Surat Pernyataan secara kolektif untuk Desa {{ $user->desa ?: '-' }}">
                    <i class="fas fa-file-signature"></i> Cetak Kolektif
                </a>
                <a href="{{ url('/petugas/belum-survei') }}" class="btn" style="background:#ffffff;color:#002855;font-weight:800;padding:10.5px 18px;border-radius:8px;text-decoration:none;display:inline-flex;align-items:center;gap:8px;box-shadow:0 4px 14px rgba(0,0,0,0.18);border:none;">
                    <i class="fas fa-clipboard-question"></i> Belum Survei
                </a>
            </div>
        </div>

        <!-- 5 Stats Cards -->
        <div class="stats-grid-petugas">
            <div class="stat-card-petugas">
                <div class="stat-icon blue"><i class="fas fa-users-viewfinder"></i></div>
                <div class="stat-info">
                    <div class="stat-value">{{ number_format($stats['total_tugas'], 0, ',', '.') }}</div>
                    <div class="stat-label">Total Penerima Desa</div>
                </div>
            </div>
            <a href="{{ url('/petugas/belum-survei') }}" class="stat-card-petugas">
                <div class="stat-icon orange"><i class="fas fa-clipboard-question"></i></div>
                <div class="stat-info">
                    <div class="stat-value" style="color:#d69e00;">{{ number_format($stats['belum_survei'], 0, ',', '.') }}</div>
                    <div class="stat-label">Belum Di-survei</div>
                </div>
            </a>
            <a href="{{ url('/petugas/sudah-survei') }}" class="stat-card-petugas">
                <div class="stat-icon green"><i class="fas fa-clipboard-check"></i></div>
                <div class="stat-info">
                    <div class="stat-value" style="color:#15803d;">{{ number_format($stats['sudah_survei'], 0, ',', '.') }}</div>
                    <div class="stat-label">Sudah Selesai Survei</div>
                </div>
            </a>
            <div class="stat-card-petugas">
                <div class="stat-icon cyan"><i class="fas fa-user-plus"></i></div>
                <div class="stat-info">
                    <div class="stat-value" style="color:#0891b2;">{{ number_format($stats['usulan_baru'], 0, ',', '.') }}</div>
                    <div class="stat-label">Usulan Baru Lapangan</div>
                </div>
            </div>
            <div class="stat-card-petugas">
                <div class="stat-icon purple"><i class="fas fa-chart-pie"></i></div>
                <div class="stat-info">
                    <div class="stat-value" style="color:#7e22ce;">{{ $stats['persentase_selesai'] }}%</div>
                    <div class="stat-label">Persentase Selesai</div>
                </div>
            </div>
        </div>

        <!-- 3 Interactive Visual Charts Section -->
        <div class="petugas-charts-grid">
            {{-- Grafik 1: Progress Status Survei --}}
            <div class="chart-box">
                <div class="chart-box-header">
                    <h4><i class="fas fa-chart-pie" style="color:var(--primary);"></i> Progress Survei Desa</h4>
                    <span style="font-size:11.5px;font-weight:700;color:var(--text-muted);">{{ $stats['total_tugas'] }} Total</span>
                </div>
                <div class="chart-canvas-wrapper">
                    <canvas id="chartProgressSurvei"></canvas>
                </div>
                <div class="chart-legend-custom">
                    <span class="legend-item"><span class="legend-dot" style="background:#27ae60;"></span> Sudah Survei ({{ $stats['sudah_survei'] }})</span>
                    <span class="legend-item"><span class="legend-dot" style="background:#ffb800;"></span> Belum Survei ({{ $stats['belum_survei'] }})</span>
                </div>
            </div>

            {{-- Grafik 2: Komposisi Jenis Kelamin --}}
            <div class="chart-box">
                <div class="chart-box-header">
                    <h4><i class="fas fa-venus-mars" style="color:#8e44ad;"></i> Komposisi Jenis Kelamin</h4>
                    <span style="font-size:11.5px;font-weight:700;color:var(--text-muted);">L/P Penerima</span>
                </div>
                <div class="chart-canvas-wrapper">
                    <canvas id="chartGender"></canvas>
                </div>
                <div class="chart-legend-custom">
                    <span class="legend-item"><span class="legend-dot" style="background:#002855;"></span> Laki-laki ({{ $stats['laki_count'] }})</span>
                    <span class="legend-item"><span class="legend-dot" style="background:#d43f78;"></span> Perempuan ({{ $stats['perempuan_count'] }})</span>
                </div>
            </div>

            {{-- Grafik 3: Pengelompokan Desil --}}
            <div class="chart-box">
                <div class="chart-box-header">
                    <h4><i class="fas fa-layer-group" style="color:#d69e00;"></i> Pengelompokan Desil</h4>
                    <span style="font-size:11.5px;font-weight:700;color:var(--text-muted);">Prioritas Backlog</span>
                </div>
                <div class="chart-canvas-wrapper">
                    <canvas id="chartDesil"></canvas>
                </div>
                <div class="chart-legend-custom">
                    <span class="legend-item"><span class="legend-dot" style="background:#0078ff;"></span> Backlog 1 ({{ $stats['backlog1_count'] }})</span>
                    <span class="legend-item"><span class="legend-dot" style="background:#27ae60;"></span> Backlog 2 ({{ $stats['backlog2_count'] }})</span>
                </div>
            </div>
        </div>

        {{-- Filter & Search Bar --}}
        <form action="{{ route('petugas.dashboard') }}" method="GET" class="filter-section" id="filterFormPetugasDash">
            <div class="search-input-wrap">
                <i class="fas fa-search"></i>
                <input type="text" id="searchPetugasDash" name="search" value="{{ $search }}" placeholder="Cari nama calon penerima, NIK, KK, atau alamat..." />
            </div>

            <input type="hidden" name="status" id="hiddenStatusPetugas" value="{{ $statusFilter }}" />

            {{-- Custom Dropdown: Filter Status Survei --}}
            <div class="pupr-dropdown-wrapper" id="ddStatusPetugasWrapper">
                <button type="button" class="pupr-dropdown-toggle" onclick="window.PuprDropdown.toggle(document.getElementById('ddStatusPetugasWrapper'))">
                    <i class="fas fa-filter" style="font-size:12px;opacity:0.6;"></i>
                    <span class="selected-label">
                        @if($statusFilter === 'sudah') Sudah Selesai Survei
                        @elseif($statusFilter === 'belum') Belum Di-survei
                        @else Semua Status Survei
                        @endif
                    </span>
                    <i class="fas fa-chevron-down" style="font-size:10px;opacity:0.5;"></i>
                </button>
                <div class="pupr-dropdown-menu" style="min-width:210px;">
                    <div class="pupr-dropdown-item {{ $statusFilter === 'all' ? 'active' : '' }}"
                         onclick="selectDropdown('hiddenStatusPetugas', 'ddStatusPetugasWrapper', 'all', 'Semua Status Survei', 'filterFormPetugasDash')">
                        <i class="fas fa-th-list" style="font-size:12px;opacity:0.5;"></i> Semua Status Survei
                    </div>
                    <div class="dropdown-divider"></div>
                    <div class="pupr-dropdown-item {{ $statusFilter === 'belum' ? 'active' : '' }}"
                         onclick="selectDropdown('hiddenStatusPetugas', 'ddStatusPetugasWrapper', 'belum', 'Belum Di-survei', 'filterFormPetugasDash')">
                        <i class="fas fa-clock" style="font-size:11px;color:#d69e00;"></i> Belum Di-survei
                    </div>
                    <div class="pupr-dropdown-item {{ $statusFilter === 'sudah' ? 'active' : '' }}"
                         onclick="selectDropdown('hiddenStatusPetugas', 'ddStatusPetugasWrapper', 'sudah', 'Sudah Selesai Survei', 'filterFormPetugasDash')">
                        <i class="fas fa-check-circle" style="font-size:11px;color:var(--success);"></i> Sudah Selesai Survei
                    </div>
                </div>
            </div>

            {{-- Tombol Cetak Kolektif berdasarkan Filter / Search Aktif --}}
            <a href="{{ route('verval-data.surat-pernyataan-kolektif', array_merge(['desa' => $user->desa], request()->all())) }}" target="_blank" class="btn" style="padding:10px 16px;font-size:13px;font-weight:700;background:#ffb800;color:#002855;text-decoration:none;border-radius:var(--radius-sm);display:inline-flex;align-items:center;gap:6px;" title="Cetak Surat Pernyataan Kolektif Desa {{ $user->desa ?: '-' }}">
                <i class="fas fa-file-signature"></i> Cetak Kolektif Surat Pernyataan
            </a>
        </form>

        {{-- Tabel Data Calon Penerima Desa --}}
        <div class="table-container-card">
            <div class="table-header-bar">
                <h3><i class="fas fa-clipboard-list"></i> Daftar Calon Penerima BSPS — Desa {{ $user->desa ?: '-' }}</h3>
                <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
                    <button type="button" class="btn" style="padding:8px 14px;font-size:12.5px;font-weight:800;background:#22c55e;color:#fff;border:none;border-radius:var(--radius-sm);cursor:pointer;display:inline-flex;align-items:center;gap:6px;box-shadow:0 2px 8px rgba(34,197,94,0.3);" onclick="window.PuprModal.open('modalTambahUsulan')">
                        <i class="fas fa-user-plus"></i> + Tambah Usulan
                    </button>
                    <a href="{{ route('verval-data.surat-pernyataan-kolektif', array_merge(['desa' => $user->desa], request()->all())) }}" target="_blank" class="btn" style="padding:8px 14px;font-size:12.5px;font-weight:800;background:#ffb800;color:#002855;text-decoration:none;border-radius:var(--radius-sm);display:inline-flex;align-items:center;gap:6px;" title="Cetak Surat Pernyataan Kolektif per Desa">
                        <i class="fas fa-print"></i> Cetak Kolektif (Desa {{ $user->desa ?: '-' }})
                    </a>
                    <span style="font-size:12.5px;color:var(--text-muted);font-weight:600;">
                        Menampilkan {{ $vervals->firstItem() ?? 0 }} - {{ $vervals->lastItem() ?? 0 }} dari {{ number_format($vervals->total(), 0, ',', '.') }} data
                    </span>
                </div>
            </div>

            <div class="table-petugas-wrapper">
                <table class="table" style="width:100%;border-collapse:collapse;min-width:880px;">
                    <thead>
                        <tr style="background:var(--bg-body);border-bottom:1px solid rgba(0,40,85,0.08);text-align:left;font-size:12.5px;color:var(--text-muted);">
                            <th style="padding:14px 18px;width:50px;">No</th>
                            <th style="padding:14px 18px;min-width:180px;">Nama Calon Penerima</th>
                            <th style="padding:14px 18px;text-align:center;width:60px;">L/P</th>
                            <th style="padding:14px 18px;min-width:180px;">NIK &amp; No. KK</th>
                            <th style="padding:14px 18px;min-width:200px;">Alamat / Dusun</th>
                            <th style="padding:14px 18px;text-align:center;width:130px;">Status Survei</th>
                            <th style="padding:14px 18px;text-align:center;min-width:160px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($vervals as $index => $item)
                            <tr style="border-bottom:1px solid rgba(0,40,85,0.06);font-size:13px;">
                                <td style="padding:14px 18px;font-weight:700;color:var(--text-muted);">
                                    {{ $vervals->firstItem() + $index }}
                                </td>
                                <td style="padding:14px 18px;">
                                    <div style="font-weight:800;color:var(--primary-dark);">{{ $item->nama }}</div>
                                </td>
                                <td style="padding:14px 18px;text-align:center;">
                                    <span class="badge-gender {{ strtolower($item->jenis_kelamin) }}">
                                        {{ $item->jenis_kelamin ?: '-' }}
                                    </span>
                                </td>
                                <td style="padding:14px 18px;">
                                    <div style="font-family:monospace;font-weight:700;color:var(--text-primary);">NIK: {{ $item->no_ktp ?: '-' }}</div>
                                    <div style="font-family:monospace;font-size:12px;color:var(--text-muted);margin-top:2px;">KK: {{ $item->no_kk ?: '-' }}</div>
                                </td>
                                <td style="padding:14px 18px;color:var(--text-secondary);">
                                    {{ $item->alamat ?: '-' }}
                                </td>
                                <td style="padding:14px 18px;text-align:center;">
                                    @if($item->foto_sudut_depan)
                                        <span class="badge-status-survey sudah"><i class="fas fa-check-circle"></i> Sudah Survei</span>
                                    @else
                                        <span class="badge-status-survey belum"><i class="fas fa-clock"></i> Belum Survei</span>
                                    @endif
                                </td>
                                <td style="padding:14px 18px;text-align:center;">
                                    <div style="display:inline-flex;align-items:center;gap:6px;">
                                        <button type="button" class="btn-act survey btn-trigger-status-modal"
                                                data-id="{{ $item->id }}" data-nama="{{ e($item->nama) }}" data-nik="{{ e($item->no_ktp ?: '-') }}" data-alamat="{{ e($item->alamat ?: '-') }}" data-status="{{ e($item->status) }}" data-url="{{ url('/survey/' . $item->id) }}">
                                            <i class="fas fa-camera"></i> {{ $item->foto_sudut_depan ? 'Lihat / Edit' : 'Mulai Survei' }}
                                        </button>
                                        <a href="{{ route('verval-data.surat-pernyataan', $item->id) }}" target="_blank" class="btn-act" style="background:rgba(0,40,85,0.08);color:var(--primary-dark);padding:7px 10px;" title="Cetak Surat Pernyataan Satuan">
                                            <i class="fas fa-file-signature"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" style="text-align:center;padding:32px;color:var(--text-muted);">
                                    <i class="fas fa-clipboard-question" style="font-size:28px;display:block;margin-bottom:8px;opacity:0.4;"></i>
                                    Tidak ada data calon penerima yang sesuai kriteria saringan di Desa {{ $user->desa ?: '-' }}.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Custom Pagination Bar -->
            <div class="pagination-custom-bar">
                <div class="pagination-info-text">
                    Menampilkan <strong>{{ $vervals->firstItem() ?? 0 }}</strong> - <strong>{{ $vervals->lastItem() ?? 0 }}</strong> dari <strong>{{ number_format($vervals->total(), 0, ',', '.') }}</strong> penerima (Halaman <strong>{{ $vervals->currentPage() }}</strong> dari <strong>{{ $vervals->lastPage() }}</strong>)
                </div>

                @php
                    $current = $vervals->currentPage();
                    $last = $vervals->lastPage();
                    $delta = 2;
                    $left = $current - $delta;
                    $right = $current + $delta + 1;
                    $range = [];
                    $rangeWithDots = [];
                    $l = null;

                    for ($i = 1; $i <= $last; $i++) {
                        if ($i == 1 || $i == $last || ($i >= $left && $i < $right)) {
                            $range[] = $i;
                        }
                    }

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

                <div style="display:flex;align-items:center;flex-wrap:wrap;gap:8px;">
                    <div class="pagination-nav">
                        @if($vervals->onFirstPage())
                            <span class="pg-link disabled"><i class="fas fa-chevron-left"></i></span>
                        @else
                            <a href="{{ $vervals->previousPageUrl() }}" class="pg-link"><i class="fas fa-chevron-left"></i></a>
                        @endif

                        @foreach($rangeWithDots as $pageItem)
                            @if($pageItem === '...')
                                <span class="pg-dots">&hellip;</span>
                            @elseif($pageItem == $current)
                                <span class="pg-link active">{{ $pageItem }}</span>
                            @else
                                <a href="{{ $vervals->url($pageItem) }}" class="pg-link">{{ $pageItem }}</a>
                            @endif
                        @endforeach

                        @if($vervals->hasMorePages())
                            <a href="{{ $vervals->nextPageUrl() }}" class="pg-link"><i class="fas fa-chevron-right"></i></a>
                        @else
                            <span class="pg-link disabled"><i class="fas fa-chevron-right"></i></span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Modal GPS Wajib (Saat Petugas Mau Mulai Survei) -->
    <div class="modal-overlay" id="modalGpsRequired">
        <div class="modal-box" style="max-width: 440px;">
            <div class="modal-header" style="background: #fff3cd; border-bottom-color: #ffeeba;">
                <h3 style="color: #856404; display: flex; align-items: center; gap: 10px; font-size: 16px;">
                    <i class="fas fa-location-dot"></i> Akses GPS / Lokasi Wajib
                </h3>
                <button class="close-btn" type="button" onclick="window.PuprModal.close('modalGpsRequired')">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="modal-body" style="padding: 24px; text-align: center;">
                <div style="width: 60px; height: 60px; border-radius: 50%; background: rgba(220, 53, 69, 0.1); color: #dc3545; display: inline-flex; align-items: center; justify-content: center; font-size: 24px; margin: 0 auto 16px;">
                    <i class="fas fa-map-location-dot"></i>
                </div>
                <h4 style="font-size: 16px; font-weight: 800; color: var(--text-primary); margin-bottom: 8px;">
                    Izin Lokasi Belum Diaktifkan!
                </h4>
                <p style="font-size: 13.5px; color: var(--text-secondary); line-height: 1.5; margin-bottom: 0;">
                    Sebagai Petugas Lapangan, Anda <strong>wajib mengaktifkan izin GPS / Lokasi</strong> pada perangkat/browser Anda untuk memastikan koordinat geotagging rumah calon penerima tercatat secara akurat saat survei.
                </p>
            </div>

            <div class="modal-footer" style="padding: 16px 20px; background: var(--bg-body); border-top: 1px solid rgba(0, 40, 85, 0.06); display: flex; gap: 10px; justify-content: center;">
                <button type="button" class="btn btn-outline" style="padding:10px 18px;" onclick="window.PuprModal.close('modalGpsRequired')">
                    Batal
                </button>
                <button type="button" class="btn btn-primary" id="btnRetryGps" style="padding:10px 20px; font-weight:800;" onclick="retryLocationPermission()">
                    <i class="fas fa-location-crosshairs"></i> Izinkan Lokasi &amp; Lanjutkan
                </button>
            </div>
        </div>
    </div>

    <!-- Modal Verifikasi Status Keberadaan Wajib Sebelum Survei -->
    <div class="modal-overlay" id="modalStatusVerification">
        <div class="modal-box" style="max-width: 480px;">
            <div class="modal-header" style="background: var(--primary); color: #ffffff;">
                <h3 style="color: #ffffff; display: flex; align-items: center; gap: 10px; font-size: 15px; font-weight: 800; margin: 0;">
                    <i class="fas fa-user-check"></i> Update Status Keberadaan Petugas
                </h3>
                <button class="close-btn" type="button" onclick="window.PuprModal.close('modalStatusVerification')" style="color: #ffffff;">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="modal-body" style="padding: 20px 24px;">
                {{-- Recipient Info Box --}}
                <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 12px 16px; margin-bottom: 16px;">
                    <div style="font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px;">Calon Penerima</div>
                    <div id="statusModalNama" style="font-size: 15px; font-weight: 800; color: var(--primary-dark); margin-top: 2px;">-</div>
                    <div style="display: flex; gap: 12px; margin-top: 6px; font-size: 12px; color: #475569; flex-wrap: wrap;">
                        <span><i class="fas fa-id-card" style="color: var(--primary);"></i> NIK: <strong id="statusModalNik">-</strong></span>
                        <span><i class="fas fa-location-dot" style="color: var(--primary);"></i> Alamat: <strong id="statusModalAlamat">-</strong></span>
                    </div>
                </div>

                <label style="font-size: 13px; font-weight: 800; color: #1e293b; display: block; margin-bottom: 10px;">
                    Pilih Status Keberadaan Lapangan <span style="color: #dc2626;">*</span>
                </label>

                <div style="display: flex; flex-direction: column; gap: 10px;">
                    <label class="status-option-card opt-ditemukan" style="display: flex; align-items: center; gap: 12px; padding: 12px 16px; border: 1px solid #cbd5e1; border-radius: 10px; cursor: pointer; transition: all 0.2s;">
                        <input type="radio" name="modal_verval_status" value="ditemukan" checked onchange="onModalStatusChange('ditemukan')" style="accent-color: #16a34a; width: 18px; height: 18px;">
                        <div style="flex: 1;">
                            <div style="font-size: 13.5px; font-weight: 800; color: #15803d; display: flex; align-items: center; gap: 6px;">
                                <i class="fas fa-check-circle"></i> Ditemukan (Ada di Lokasi)
                            </div>
                            <div style="font-size: 11.5px; color: #475569; margin-top: 2px;">
                                Penerima berada di lokasi &amp; siap untuk disurvei fisik
                            </div>
                        </div>
                    </label>

                    <label class="status-option-card opt-meninggal" style="display: flex; align-items: center; gap: 12px; padding: 12px 16px; border: 1px solid #cbd5e1; border-radius: 10px; cursor: pointer; transition: all 0.2s;">
                        <input type="radio" name="modal_verval_status" value="meninggal" onchange="onModalStatusChange('meninggal')" style="accent-color: #dc2626; width: 18px; height: 18px;">
                        <div style="flex: 1;">
                            <div style="font-size: 13.5px; font-weight: 800; color: #b91c1c; display: flex; align-items: center; gap: 6px;">
                                <i class="fas fa-heart-crack"></i> Meninggal Dunia
                            </div>
                            <div style="font-size: 11.5px; color: #475569; margin-top: 2px;">
                                Penerima telah meninggal dunia
                            </div>
                        </div>
                    </label>

                    <label class="status-option-card opt-pindah" style="display: flex; align-items: center; gap: 12px; padding: 12px 16px; border: 1px solid #cbd5e1; border-radius: 10px; cursor: pointer; transition: all 0.2s;">
                        <input type="radio" name="modal_verval_status" value="pindah" onchange="onModalStatusChange('pindah')" style="accent-color: #d97706; width: 18px; height: 18px;">
                        <div style="flex: 1;">
                            <div style="font-size: 13.5px; font-weight: 800; color: #b45309; display: flex; align-items: center; gap: 6px;">
                                <i class="fas fa-house-circle-xmark"></i> Pindah Alamat
                            </div>
                            <div style="font-size: 11.5px; color: #475569; margin-top: 2px;">
                                Penerima telah pindah tempat tinggal
                            </div>
                        </div>
                    </label>

                    <label class="status-option-card opt-tidak-diketahui" style="display: flex; align-items: center; gap: 12px; padding: 12px 16px; border: 1px solid #cbd5e1; border-radius: 10px; cursor: pointer; transition: all 0.2s;">
                        <input type="radio" name="modal_verval_status" value="tidak diketahui" onchange="onModalStatusChange('tidak diketahui')" style="accent-color: #475569; width: 18px; height: 18px;">
                        <div style="flex: 1;">
                            <div style="font-size: 13.5px; font-weight: 800; color: #475569; display: flex; align-items: center; gap: 6px;">
                                <i class="fas fa-question-circle"></i> Tidak Diketahui
                            </div>
                            <div style="font-size: 11.5px; color: #475569; margin-top: 2px;">
                                Keberadaan penerima tidak ditemukan / tidak diketahui
                            </div>
                        </div>
                    </label>
                </div>
            </div>

            <div class="modal-footer" style="padding: 14px 20px; background: var(--bg-body); border-top: 1px solid rgba(0, 40, 85, 0.06); display: flex; gap: 10px; justify-content: flex-end;">
                <button type="button" class="btn btn-outline" style="padding: 9px 16px;" onclick="window.PuprModal.close('modalStatusVerification')">
                    Batal
                </button>
                <button type="button" class="btn btn-primary" id="btnSubmitStatusVerification" style="padding: 9px 20px; font-weight: 800; display: inline-flex; align-items: center; gap: 6px;" onclick="submitStatusVerification()">
                    <i class="fas fa-location-crosshairs"></i> Simpan &amp; Lanjutkan Survei
                </button>
            </div>
        </div>
    </div>

    <!-- Modal Tambah Usulan Calon Penerima BSPS (PUPR Style) -->
    <div class="modal-overlay" id="modalTambahUsulan">
        <div class="modal-box" style="max-width: 600px; border-radius: 14px; overflow: hidden; box-shadow: 0 20px 40px rgba(0, 40, 85, 0.25);">
            <div class="modal-header" style="background: linear-gradient(135deg, #002855 0%, #001835 100%); color: #fff; padding: 18px 22px; display: flex; align-items: center; justify-content: space-between;">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <div style="width: 40px; height: 40px; border-radius: 50%; background: rgba(34, 197, 94, 0.2); color: #22c55e; display: flex; align-items: center; justify-content: center; font-size: 18px; flex-shrink: 0;">
                        <i class="fas fa-user-plus"></i>
                    </div>
                    <div>
                        <h3 style="font-size: 16px; font-weight: 800; margin: 0; color: #fff;">Usulkan Calon Penerima Baru</h3>
                        <p style="font-size: 12px; color: rgba(255, 255, 255, 0.75); margin: 2px 0 0 0;">
                            Desa <strong>{{ $user->desa ?: '-' }}</strong> &bull; Kec. <strong>{{ $user->kecamatan ?: '-' }}</strong>
                        </p>
                    </div>
                </div>
                <button type="button" style="background: transparent; border: none; color: rgba(255,255,255,0.7); font-size: 22px; cursor: pointer; line-height: 1;" onclick="window.PuprModal.close('modalTambahUsulan')">&times;</button>
            </div>

            <form action="{{ route('petugas.usulkan-penerima') }}" method="POST" id="formTambahUsulan">
                @csrf
                <div class="modal-body" style="padding: 22px; max-height: 75vh; overflow-y: auto;">
                    {{-- Alert Info Desa --}}
                    <div style="background: rgba(34, 197, 94, 0.08); border: 1px solid rgba(34, 197, 94, 0.2); border-radius: 8px; padding: 12px 14px; margin-bottom: 18px; display: flex; align-items: center; justify-content: space-between;">
                        <span style="font-size: 12.5px; color: #15803d; font-weight: 600;">
                            <i class="fas fa-location-dot" style="margin-right: 4px;"></i> Wilayah Usulan: <strong>Desa {{ $user->desa ?: '-' }}</strong> (Kec. {{ $user->kecamatan ?: '-' }})
                        </span>
                        <span style="background: #22c55e; color: #fff; font-size: 10px; font-weight: 800; padding: 3px 8px; border-radius: 12px; text-transform: uppercase;">
                            Otomatis Terunci
                        </span>
                    </div>

                    {{-- Grid Input Form --}}
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
                        <div style="grid-column: span 2;">
                            <label style="font-size: 12px; font-weight: 700; color: #334155; display: block; margin-bottom: 4px;">
                                Nama Lengkap Calon Penerima <span style="color:#ef4444;">*</span>
                            </label>
                            <input type="text" name="nama" class="form-control" placeholder="Contoh: SAMAD" required style="width: 100%; padding: 10px 12px; border-radius: 8px; border: 1px solid #cbd5e1; font-size: 13px;">
                        </div>

                        <div>
                            <label style="font-size: 12px; font-weight: 700; color: #334155; display: block; margin-bottom: 4px;">
                                NIK / No. KTP (16 Digit) <span style="color:#ef4444;">*</span>
                            </label>
                            <input type="text" name="no_ktp" class="form-control" maxlength="16" minlength="16" placeholder="350917..." required style="width: 100%; padding: 10px 12px; border-radius: 8px; border: 1px solid #cbd5e1; font-size: 13px;">
                        </div>

                        <div>
                            <label style="font-size: 12px; font-weight: 700; color: #334155; display: block; margin-bottom: 4px;">
                                Nomor Kartu Keluarga (KK)
                            </label>
                            <input type="text" name="no_kk" class="form-control" maxlength="16" placeholder="350917..." style="width: 100%; padding: 10px 12px; border-radius: 8px; border: 1px solid #cbd5e1; font-size: 13px;">
                        </div>

                        <div>
                            <label style="font-size: 12px; font-weight: 700; color: #334155; display: block; margin-bottom: 4px;">
                                Jenis Kelamin
                            </label>
                            <select name="jenis_kelamin" class="form-control" style="width: 100%; padding: 10px 12px; border-radius: 8px; border: 1px solid #cbd5e1; font-size: 13px; background: #fff;">
                                <option value="L">Laki-laki</option>
                                <option value="P">Perempuan</option>
                            </select>
                        </div>

                        <div>
                            <label style="font-size: 12px; font-weight: 700; color: #334155; display: block; margin-bottom: 4px;">
                                Pengelompokan Desil / Status Usulan
                            </label>
                            <input type="hidden" name="pengelompokan_desil" value="Usulan Baru Lapangan">
                            <div style="width: 100%; padding: 10px 12px; border-radius: 8px; border: 1px solid #cbd5e1; font-size: 13px; background: #f8fafc; color: #15803d; font-weight: 800; display: flex; align-items: center; justify-content: space-between;">
                                <span><i class="fas fa-tag" style="margin-right:6px;color:#22c55e;"></i> Usulan Baru Lapangan</span>
                                <span style="font-size: 9.5px; background: #22c55e; color: #fff; padding: 2px 6px; border-radius: 10px; font-weight: 800; text-transform: uppercase;">Terkunci</span>
                            </div>
                        </div>

                        <div>
                            <label style="font-size: 12px; font-weight: 700; color: #334155; display: block; margin-bottom: 4px;">
                                Dusun / Dukuh
                            </label>
                            <input type="text" name="dusun" class="form-control" placeholder="Contoh: Krajan" style="width: 100%; padding: 10px 12px; border-radius: 8px; border: 1px solid #cbd5e1; font-size: 13px;">
                        </div>

                        <div style="display: flex; gap: 8px;">
                            <div style="flex: 1;">
                                <label style="font-size: 12px; font-weight: 700; color: #334155; display: block; margin-bottom: 4px;">
                                    RT
                                </label>
                                <input type="text" name="rt" class="form-control" maxlength="5" placeholder="001" style="width: 100%; padding: 10px 12px; border-radius: 8px; border: 1px solid #cbd5e1; font-size: 13px;">
                            </div>
                            <div style="flex: 1;">
                                <label style="font-size: 12px; font-weight: 700; color: #334155; display: block; margin-bottom: 4px;">
                                    RW
                                </label>
                                <input type="text" name="rw" class="form-control" maxlength="5" placeholder="002" style="width: 100%; padding: 10px 12px; border-radius: 8px; border: 1px solid #cbd5e1; font-size: 13px;">
                            </div>
                        </div>

                        <div style="grid-column: span 2;">
                            <label style="font-size: 12px; font-weight: 700; color: #334155; display: block; margin-bottom: 4px;">
                                Alamat Jalan / Rumah
                            </label>
                            <textarea name="alamat" class="form-control" rows="2" placeholder="Contoh: Jl. Mawar No. 12" style="width: 100%; padding: 10px 12px; border-radius: 8px; border: 1px solid #cbd5e1; font-size: 13px; font-family: inherit;"></textarea>
                        </div>
                    </div>
                </div>

                <div class="modal-footer" style="padding: 14px 22px; background: var(--bg-body); border-top: 1px solid rgba(0, 40, 85, 0.06); display: flex; gap: 10px; justify-content: flex-end; align-items: center; flex-wrap: wrap;">
                    <button type="button" class="btn btn-outline" style="padding: 9px 16px;" onclick="window.PuprModal.close('modalTambahUsulan')">
                        Batal
                    </button>
                    <button type="submit" class="btn btn-primary" style="padding: 9px 18px; font-weight: 800; background: #002855; color: #fff; display: inline-flex; align-items: center; gap: 6px;">
                        <i class="fas fa-save"></i> Simpan Usulan
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

<script id="allPenerimasDashData" type="application/json">
    {!! json_encode($allPenerimas ?? []) !!}
</script>

@push('scripts')
<!-- Local Chart.js -->
<script src="{{ asset('assets/js/chart.js') }}"></script>

<script>
    let pendingSurveyUrl = null;
    let currentVervalId = null;
    let currentTargetSurveyUrl = null;

    // Delegated click event untuk tombol trigger modal status
    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.btn-trigger-status-modal');
        if (btn) {
            e.preventDefault();
            const id = btn.getAttribute('data-id');
            const nama = btn.getAttribute('data-nama');
            const nik = btn.getAttribute('data-nik');
            const alamat = btn.getAttribute('data-alamat');
            const status = btn.getAttribute('data-status');
            const url = btn.getAttribute('data-url');

            openStatusVerificationModal(id, nama, nik, alamat, status, url);
        }
    });
    function openStatusVerificationModal(id, nama, nik, alamat, currentStatus, surveyUrl) {
        currentVervalId = id;
        currentTargetSurveyUrl = surveyUrl;

        const elNama = document.getElementById('statusModalNama');
        const elNik = document.getElementById('statusModalNik');
        const elAlamat = document.getElementById('statusModalAlamat');

        if (elNama) elNama.textContent = nama || '-';
        if (elNik) elNik.textContent = nik || '-';
        if (elAlamat) elAlamat.textContent = alamat || '-';

        const statusToSelect = (currentStatus && ['ditemukan', 'meninggal', 'pindah', 'tidak diketahui'].includes(currentStatus)) ? currentStatus : 'ditemukan';
        const radio = document.querySelector(`input[name="modal_verval_status"][value="${statusToSelect}"]`);
        if (radio) {
            radio.checked = true;
        }
        onModalStatusChange(statusToSelect);

        if (window.PuprModal) {
            window.PuprModal.open('modalStatusVerification');
        }
    }

    function onModalStatusChange(statusVal) {
        const btn = document.getElementById('btnSubmitStatusVerification');
        if (!btn) return;

        if (statusVal === 'ditemukan') {
            btn.className = 'btn btn-primary';
            btn.style.background = 'var(--primary)';
            btn.style.color = '#fff';
            btn.innerHTML = '<i class="fas fa-location-crosshairs"></i> Simpan &amp; Lanjutkan Survei';
        } else {
            btn.className = 'btn btn-warning';
            btn.style.background = '#d97706';
            btn.style.color = '#fff';
            btn.innerHTML = '<i class="fas fa-save"></i> Simpan Status Baru';
        }
    }

    function submitStatusVerification() {
        const selectedRadio = document.querySelector('input[name="modal_verval_status"]:checked');
        if (!selectedRadio || !currentVervalId) return;

        const newStatus = selectedRadio.value;

        if (newStatus === 'ditemukan') {
            if (window.PuprModal) window.PuprModal.close('modalStatusVerification');
            if (currentTargetSurveyUrl) {
                window.location.href = currentTargetSurveyUrl;
            }
            return;
        }

        // Jika status selain ditemukan (meninggal/pindah/tidak diketahui)
        if (!navigator.onLine) {
            if (window.PuprModal) window.PuprModal.close('modalStatusVerification');
            if (window.BspsOffline && window.BspsOffline.showPuprToast) {
                window.BspsOffline.showPuprToast(`Status dicatat: "${newStatus.toUpperCase()}" (Offline)`, 'success');
            }
            return;
        }

        if (window.PuprLoading) {
            window.PuprLoading.show('Memperbarui Status Penerima...');
        }

        fetch(`/data-verval/${currentVervalId}/status`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ status: newStatus })
        })
        .then(response => response.json())
        .then(data => {
            if (window.PuprLoading) window.PuprLoading.hide();
            if (window.PuprModal) window.PuprModal.close('modalStatusVerification');
            if (data.success) {
                if (window.BspsOffline && window.BspsOffline.showPuprToast) {
                    window.BspsOffline.showPuprToast(`Status berhasil diperbarui menjadi "${newStatus.toUpperCase()}"`, 'success');
                }
                setTimeout(() => { window.location.reload(); }, 800);
            } else {
                alert(data.message || 'Gagal memperbarui status.');
            }
        })
        .catch(err => {
            if (window.PuprLoading) window.PuprLoading.hide();
            if (window.PuprModal) window.PuprModal.close('modalStatusVerification');
        });
    }

    function startSurveyWithGps(targetUrl) {
        if (targetUrl) {
            window.location.href = targetUrl;
        }
    }

    /**
     * Helper: Pilih item di custom pupr-dropdown
     */
    function selectDropdown(hiddenInputId, wrapperId, value, label, formId) {
        const hidden = document.getElementById(hiddenInputId);
        if (hidden) hidden.value = value;

        const wrapper = document.getElementById(wrapperId);
        if (wrapper) {
            const lbl = wrapper.querySelector('.selected-label');
            if (lbl) lbl.textContent = label;
            wrapper.querySelectorAll('.pupr-dropdown-item').forEach(i => i.classList.remove('active'));
            wrapper.classList.remove('active');
        }

        // Jika sedang offline, saring tabel langsung di browser tanpa reload
        if (!navigator.onLine) {
            filterDashboardTableOffline();
            return;
        }

        if (formId) {
            const form = document.getElementById(formId);
            if (form) {
                if (window.PuprLoading) {
                    window.PuprLoading.show('Menyaring Data Petugas...');
                }
                form.submit();
            }
        }
    }

    let ALL_DATA_DASH = [];
    try {
        const raw = document.getElementById('allPenerimasDashData')?.textContent;
        if (raw) ALL_DATA_DASH = JSON.parse(raw);
    } catch (e) {
        console.error('Error parsing all penerimas dash:', e);
    }

    let ORIGINAL_ROWS_DASH = null;

    function escapeHtml(str) {
        if (!str) return '';
        return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }

    /**
     * Client-Side Search & Status Filter untuk Petugas Dashboard (Mencari di SELURUH data desa)
     */
    function filterDashboardTableOffline(showFeedback = false) {
        if (window.PuprLoading) window.PuprLoading.hide();
        const searchInput = document.getElementById('searchPetugasDash');
        const query = (searchInput?.value || '').toLowerCase().trim();
        const terms = query.split(/\s+/).filter(t => t.length > 0);
        const statusVal = (document.getElementById('hiddenStatusPetugas')?.value || 'all').toLowerCase().trim();
        const tbody = document.querySelector('.table-petugas-wrapper tbody');
        if (!tbody) return;

        if (ORIGINAL_ROWS_DASH === null) {
            ORIGINAL_ROWS_DASH = tbody.innerHTML;
        }

        if (terms.length === 0 && statusVal === 'all') {
            tbody.innerHTML = ORIGINAL_ROWS_DASH;
            if (showFeedback && window.BspsOffline && window.BspsOffline.showPuprToast) {
                window.BspsOffline.showPuprToast(`Menampilkan halaman awal (${ALL_DATA_DASH.length} total di desa)`, 'success');
            }
            return;
        }

        const matches = ALL_DATA_DASH.filter(item => {
            const fullText = `${item.nama || ''} ${item.no_ktp || ''} ${item.no_kk || ''} ${item.alamat || ''}`.toLowerCase();
            const matchSearch = terms.length === 0 || terms.every(term => fullText.includes(term));
            let matchStatus = true;
            if (statusVal === 'sudah') {
                matchStatus = Boolean(item.foto_sudut_depan);
            } else if (statusVal === 'belum') {
                matchStatus = !item.foto_sudut_depan;
            }
            return matchSearch && matchStatus;
        });

        if (matches.length === 0) {
            tbody.innerHTML = `<tr id="noSearchResultRow"><td colspan="7" style="text-align:center;padding:30px;color:var(--text-muted);font-weight:600;"><i class="fas fa-search" style="margin-right:6px;"></i> Tidak ada penerima yang cocok dengan kriteria pencarian dari seluruh ${ALL_DATA_DASH.length} data desa</td></tr>`;
        } else {
            let html = '';
            matches.forEach((item, idx) => {
                const genderClass = (item.jenis_kelamin || '').toLowerCase();
                const isSudah = Boolean(item.foto_sudut_depan);
                const statusBadge = isSudah
                    ? `<span class="badge-status-survey sudah"><i class="fas fa-check-circle"></i> Sudah Survei</span>`
                    : `<span class="badge-status-survey belum"><i class="fas fa-clock"></i> Belum Survei</span>`;

                html += `
                    <tr style="border-bottom:1px solid rgba(0,40,85,0.06);font-size:13px;">
                        <td style="padding:14px 18px;font-weight:700;color:var(--text-muted);">${idx + 1}</td>
                        <td style="padding:14px 18px;">
                            <div style="font-weight:800;color:var(--primary-dark);">${escapeHtml(item.nama)}</div>
                        </td>
                        <td style="padding:14px 18px;text-align:center;">
                            <span class="badge-gender ${genderClass}">${escapeHtml(item.jenis_kelamin || '-')}</span>
                        </td>
                        <td style="padding:14px 18px;">
                            <div style="font-family:monospace;font-weight:700;color:var(--text-primary);">NIK: ${escapeHtml(item.no_ktp || '-')}</div>
                            <div style="font-family:monospace;font-size:12px;color:var(--text-muted);margin-top:2px;">KK: ${escapeHtml(item.no_kk || '-')}</div>
                        </td>
                        <td style="padding:14px 18px;color:var(--text-secondary);">${escapeHtml(item.alamat || '-')}</td>
                        <td style="padding:14px 18px;text-align:center;">${statusBadge}</td>
                        <td style="padding:14px 18px;text-align:center;">
                            <div style="display:inline-flex;align-items:center;gap:6px;">
                                <button type="button" class="btn-act survey btn-trigger-status-modal"
                                        data-id="${item.id}" data-nama="${escapeHtml(item.nama)}" data-nik="${escapeHtml(item.no_ktp || '-')}" data-alamat="${escapeHtml(item.alamat || '-')}" data-status="${escapeHtml(item.status || 'belum_ditentukan')}" data-url="/survey/${item.id}">
                                    <i class="fas fa-camera"></i> ${isSudah ? 'Lihat / Edit' : 'Mulai Survei'}
                                </button>
                                <a href="/verval-data/surat-pernyataan/${item.id}" target="_blank" class="btn-act" style="background:rgba(0,40,85,0.08);color:var(--primary-dark);padding:7px 10px;" title="Cetak Surat Pernyataan Satuan">
                                    <i class="fas fa-file-signature"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                `;
            });
            tbody.innerHTML = html;
        }

        if (showFeedback && window.BspsOffline && window.BspsOffline.showPuprToast) {
            if (matches.length > 0) {
                window.BspsOffline.showPuprToast(`Ditemukan ${matches.length} dari seluruh ${ALL_DATA_DASH.length} penerima desa`, 'success');
            } else {
                window.BspsOffline.showPuprToast(`Tidak ditemukan penerima untuk pencarian ini`, 'warning');
            }
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const filterForm = document.getElementById('filterFormPetugasDash');
        if (filterForm) {
            filterForm.addEventListener('submit', function(e) {
                if (!navigator.onLine) {
                    e.preventDefault();
                    filterDashboardTableOffline(true);
                }
            });
        }

        const searchInput = document.getElementById('searchPetugasDash');
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                filterDashboardTableOffline(false);
            });
            searchInput.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') {
                    if (!navigator.onLine) {
                        e.preventDefault();
                        filterDashboardTableOffline(true);
                    }
                }
            });
        }
    });

    function startSurveyWithGps(targetUrl) {
        pendingSurveyUrl = targetUrl;

        if (window.PuprLoading) {
            window.PuprLoading.show('Sedang Memuat Lokasi GPS Satelit...');
        }

        if (!navigator.geolocation) {
            if (window.PuprLoading) window.PuprLoading.hide();
            alert("Perangkat atau browser Anda tidak mendukung fitur Geolocation/GPS.");
            return;
        }

        navigator.geolocation.getCurrentPosition(
            function(position) {
                if (window.PuprLoading) {
                    window.PuprLoading.show('Lokasi Terdeteksi, Membuka Form Survei...');
                }
                window.location.href = targetUrl;
            },
            function(error) {
                if (window.PuprLoading) {
                    window.PuprLoading.hide();
                }

                // [Bypass Offline] Jika perangkat offline & bukan penolakan izin (PERMISSION_DENIED == 1)
                if (error.code !== 1 && !navigator.onLine) {
                    console.warn('[Offline] GPS gagal namun diizinkan lanjut karena mode offline aktif.');
                    if (window.PuprModal) window.PuprModal.close('modalGpsRequired');
                    window.location.href = targetUrl;
                    return;
                }

                if (window.PuprModal) {
                    window.PuprModal.open('modalGpsRequired');
                } else {
                    alert("Silakan aktifkan lokasi (GPS) jika mau melakukan survei!");
                }
            },
            { enableHighAccuracy: true, timeout: 12000, maximumAge: 60000 }
        );
    }

    function retryLocationPermission() {
        if (window.PuprModal) window.PuprModal.close('modalGpsRequired');
        if (window.PuprLoading) window.PuprLoading.show('Sedang Mendeteksi Ulang Lokasi GPS...');

        if (!navigator.geolocation) {
            if (window.PuprLoading) window.PuprLoading.hide();
            alert("Perangkat tidak mendukung GPS.");
            return;
        }

        navigator.geolocation.getCurrentPosition(
            function(position) {
                if (window.PuprLoading) {
                    window.PuprLoading.show('Lokasi Berhasil Didapat, Membuka Form...');
                }
                if (pendingSurveyUrl) {
                    window.location.href = pendingSurveyUrl;
                }
            },
            function(error) {
                if (window.PuprLoading) {
                    window.PuprLoading.hide();
                }

                // [Bypass Offline] Jika perangkat offline & bukan penolakan izin (PERMISSION_DENIED == 1)
                if (error.code !== 1 && !navigator.onLine) {
                    console.warn('[Offline] GPS gagal namun diizinkan lanjut karena mode offline aktif.');
                    if (window.PuprModal) window.PuprModal.close('modalGpsRequired');
                    if (pendingSurveyUrl) window.location.href = pendingSurveyUrl;
                    return;
                }

                if (window.PuprModal) {
                    window.PuprModal.open('modalGpsRequired');
                }
            },
            { enableHighAccuracy: true, timeout: 12000, maximumAge: 60000 }
        );
    }

    // Inisialisasi Chart.js
    function initPetugasCharts() {
        if (typeof Chart === 'undefined') return;

        // 1. Chart Progress Survei (Donut)
        const ctxProgress = document.getElementById('chartProgressSurvei');
        if (ctxProgress) {
            new Chart(ctxProgress, {
                type: 'doughnut',
                data: {
                    labels: ['Sudah Survei', 'Belum Survei'],
                    datasets: [{
                        data: [{{ $stats['sudah_survei'] }}, {{ $stats['belum_survei'] }}],
                        backgroundColor: ['#27ae60', '#ffb800'],
                        borderWidth: 2,
                        borderColor: '#ffffff',
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function(ctx) {
                                    const total = {{ $stats['total_tugas'] }};
                                    const val = ctx.parsed;
                                    const pct = total > 0 ? ((val / total) * 100).toFixed(1) : 0;
                                    return ctx.label + ': ' + val + ' KK (' + pct + '%)';
                                }
                            }
                        }
                    },
                    cutout: '70%'
                }
            });
        }

        // 2. Chart Komposisi Jenis Kelamin (Donut)
        const ctxGender = document.getElementById('chartGender');
        if (ctxGender) {
            new Chart(ctxGender, {
                type: 'doughnut',
                data: {
                    labels: ['Laki-laki', 'Perempuan'],
                    datasets: [{
                        data: [{{ $stats['laki_count'] }}, {{ $stats['perempuan_count'] }}],
                        backgroundColor: ['#002855', '#d43f78'],
                        borderWidth: 2,
                        borderColor: '#ffffff',
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function(ctx) {
                                    const total = {{ $stats['total_tugas'] }};
                                    const val = ctx.parsed;
                                    const pct = total > 0 ? ((val / total) * 100).toFixed(1) : 0;
                                    return ctx.label + ': ' + val + ' Orang (' + pct + '%)';
                                }
                            }
                        }
                    },
                    cutout: '70%'
                }
            });
        }

        // 3. Chart Pengelompokan Desil (Bar)
        const ctxDesil = document.getElementById('chartDesil');
        if (ctxDesil) {
            new Chart(ctxDesil, {
                type: 'bar',
                data: {
                    labels: ['Backlog 1', 'Backlog 2'],
                    datasets: [{
                        label: 'Jumlah Calon Penerima',
                        data: [{{ $stats['backlog1_count'] }}, {{ $stats['backlog2_count'] }}],
                        backgroundColor: ['#0078ff', '#27ae60'],
                        borderRadius: 6,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        x: { grid: { display: false } },
                        y: {
                            beginAtZero: true,
                            grid: { color: 'rgba(0,40,85,0.06)' },
                            ticks: { stepSize: 5 }
                        }
                    }
                }
            });
        }
    }

    // Handling submit Form Tambah Usulan (Support Offline Mode PWA - Anti Dinosaur Page)
    document.getElementById('formTambahUsulan')?.addEventListener('submit', async function (e) {
        const isOffline = !navigator.onLine;

        const noKtpInput = this.querySelector('input[name="no_ktp"]');
        const noKtpVal = noKtpInput ? noKtpInput.value.trim() : '';

        if (noKtpVal.length !== 16 || isNaN(noKtpVal)) {
            e.preventDefault();
            alert('NIK (No. KTP) wajib berupa 16 digit angka!');
            return;
        }

        if (isOffline) {
            e.preventDefault();

            const formData = new FormData(this);
            const usulanData = {
                id: 'usulan_off_' + Date.now(),
                nama: formData.get('nama') || '',
                no_ktp: formData.get('no_ktp') || '',
                no_kk: formData.get('no_kk') || '',
                jenis_kelamin: formData.get('jenis_kelamin') || 'L',
                pengelompokan_desil: formData.get('pengelompokan_desil') || 'Usulan Baru Lapangan',
                dusun: formData.get('dusun') || '',
                rt: formData.get('rt') || '',
                rw: formData.get('rw') || '',
                alamat: formData.get('alamat') || '',
                saved_at: new Date().toISOString()
            };

            if (window.BspsOffline && window.BspsOffline.saveUsulanToIndexedDB) {
                const saved = await window.BspsOffline.saveUsulanToIndexedDB(usulanData);
                if (saved) {
                    if (window.BspsOffline.showPuprToast) {
                        window.BspsOffline.showPuprToast(`📲 Mode Offline: Usulan "${usulanData.nama}" tersimpan di memori HP! Data akan terunggah otomatis saat online.`, 'warning');
                    } else {
                        alert(`Mode Offline: Usulan "${usulanData.nama}" berhasil disimpan sementara di HP!`);
                    }
                    if (window.PuprModal) window.PuprModal.close('modalTambahUsulan');
                    this.reset();
                }
            } else {
                alert('Mode Offline: Data usulan berhasil disimpan sementara.');
            }
        }
    });

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initPetugasCharts);
    } else {
        initPetugasCharts();
    }
</script>
@endpush
