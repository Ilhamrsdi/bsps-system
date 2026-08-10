@extends('layouts.partial.app')

@section('title', 'BSPS Verval - Dashboard Petugas')
@section('title_header', 'Dashboard Petugas Lapangan')
@section('subtitle_header', 'Monitoring & Verifikasi Calon Penerima BSPS Wilayah Desa {{ Auth::user()->desa ?? "-" }}')

@push('styles')
<style>
    /* Hero Banner Petugas */
    .welcome-card {
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
        color: #ffffff;
        border-radius: var(--radius);
        padding: 24px 28px;
        margin-bottom: 24px;
        box-shadow: 0 10px 24px rgba(0, 40, 85, 0.18);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        border-left: 6px solid var(--secondary);
    }
    .welcome-text h2 { font-size: 20px; font-weight: 800; margin-bottom: 4px; color:#fff; }
    .welcome-text p  { font-size: 13.5px; opacity: 0.9; margin: 0; color:rgba(255,255,255,0.9); }

    /* Stats Grid */
    .stats-grid-petugas {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
        margin-bottom: 24px;
    }
    .stat-card-petugas {
        background: var(--bg-card);
        border-radius: var(--radius);
        padding: 20px;
        box-shadow: var(--shadow-sm);
        border: 1px solid rgba(0, 40, 85, 0.06);
        display: flex;
        align-items: center;
        gap: 16px;
        text-decoration: none;
        color: inherit;
        transition: var(--transition);
    }
    .stat-card-petugas:hover { transform: translateY(-3px); box-shadow: var(--shadow-md); }
    .stat-card-petugas .stat-icon {
        width: 48px; height: 48px;
        border-radius: var(--radius-sm);
        display: flex; align-items: center; justify-content: center;
        font-size: 20px; flex-shrink: 0;
    }
    .stat-card-petugas .stat-icon.blue   { background: rgba(0,40,85,0.10);   color: var(--primary); }
    .stat-card-petugas .stat-icon.orange { background: rgba(255,184,0,0.15); color: #d69e00; }
    .stat-card-petugas .stat-icon.green  { background: rgba(39,174,96,0.12); color: var(--success); }
    .stat-card-petugas .stat-icon.purple { background: rgba(142,68,173,0.12); color: #8e44ad; }
    .stat-card-petugas .stat-info { flex: 1; }
    .stat-card-petugas .stat-value { font-size: 24px; font-weight: 800; line-height: 1.1; color: var(--primary-dark); }
    .stat-card-petugas .stat-label { font-size: 12px; color: var(--text-muted); font-weight: 600; margin-top: 3px; }

    /* Chart Grid Section */
    .petugas-charts-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 20px;
        margin-bottom: 24px;
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

    /* Table Container */
    .table-container-card {
        background: var(--bg-card);
        border-radius: var(--radius);
        box-shadow: var(--shadow-sm);
        border: 1px solid rgba(0, 40, 85, 0.06);
        overflow: hidden;
    }
    .table-header-bar {
        padding: 18px 24px;
        border-bottom: 1px solid rgba(0,40,85,0.06);
        display: flex; align-items: center; justify-content: space-between; gap: 12px;
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

    /* Jump to Page Form */
    .jump-page-form { display: inline-flex; align-items: center; gap: 6px; margin-left: 12px; }
    .jump-page-input {
        width: 54px; height: 36px; text-align: center;
        border-radius: 8px; border: 1px solid rgba(0, 40, 85, 0.16);
        background: var(--bg-body); color: var(--text-primary); font-size: 13px; font-weight: 700; outline: none;
    }
    .jump-page-btn {
        height: 36px; padding: 0 12px; border-radius: 8px;
        background: var(--primary); color: #fff; border: none; font-size: 12px; font-weight: 700; cursor: pointer;
    }

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

    @media (max-width: 992px) {
        .stats-grid-petugas { grid-template-columns: repeat(2, 1fr); gap: 12px; }
        .petugas-charts-grid { grid-template-columns: 1fr; }
    }

    @media (max-width: 768px) {
        .welcome-card {
            flex-direction: column;
            align-items: flex-start;
            gap: 16px;
            padding: 20px;
        }
        .welcome-card .btn {
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
        .stats-grid-petugas { grid-template-columns: 1fr; gap: 10px; }
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

        <!-- Welcome Banner Petugas -->
        <div class="welcome-card">
            <div class="welcome-text">
                <h2>Selamat Datang, {{ $user->name }}!</h2>
                <p>
                    Wilayah Tugas: <strong>Desa {{ $user->desa ?: '-' }}</strong> &bull; 
                    Kecamatan <strong>{{ $user->kecamatan ?: '-' }}</strong>
                </p>
            </div>
            <div>
                <a href="{{ url('/petugas/belum-survei') }}" class="btn" style="background:#ffb800;color:#002855;font-weight:800;padding:10px 18px;border-radius:var(--radius-sm);text-decoration:none;display:inline-flex;align-items:center;gap:6px;">
                    <i class="fas fa-clipboard-question"></i> Lihat Tugas Belum Survei
                </a>
            </div>
        </div>

        <!-- 4 Stats Cards -->
        <div class="stats-grid-petugas">
            <div class="stat-card-petugas">
                <div class="stat-icon blue"><i class="fas fa-users-viewfinder"></i></div>
                <div class="stat-info">
                    <div class="stat-value">{{ number_format($stats['total_tugas'], 0, ',', '.') }}</div>
                    <div class="stat-label">Total Calon Penerima Desa Ini</div>
                </div>
            </div>
            <a href="{{ url('/petugas/belum-survei') }}" class="stat-card-petugas">
                <div class="stat-icon orange"><i class="fas fa-clipboard-question"></i></div>
                <div class="stat-info">
                    <div class="stat-value" style="color:#d69e00;">{{ number_format($stats['belum_survei'], 0, ',', '.') }}</div>
                    <div class="stat-label">Belum Di-survei (Perlu Survei)</div>
                </div>
            </a>
            <a href="{{ url('/petugas/sudah-survei') }}" class="stat-card-petugas">
                <div class="stat-icon green"><i class="fas fa-clipboard-check"></i></div>
                <div class="stat-info">
                    <div class="stat-value" style="color:var(--success);">{{ number_format($stats['sudah_survei'], 0, ',', '.') }}</div>
                    <div class="stat-label">Sudah Selesai Survei</div>
                </div>
            </a>
            <div class="stat-card-petugas">
                <div class="stat-icon purple"><i class="fas fa-chart-pie"></i></div>
                <div class="stat-info">
                    <div class="stat-value" style="color:#8e44ad;">{{ $stats['persentase_selesai'] }}%</div>
                    <div class="stat-label">Tingkat Penyelesaian</div>
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
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama calon penerima, NIK, KK, atau alamat..." />
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
        </form>

        {{-- Tabel Data Calon Penerima Desa --}}
        <div class="table-container-card">
            <div class="table-header-bar">
                <h3><i class="fas fa-clipboard-list"></i> Daftar Calon Penerima BSPS — Desa {{ $user->desa ?: '-' }}</h3>
                <span style="font-size:12.5px;color:var(--text-muted);font-weight:600;">
                    Menampilkan {{ $vervals->firstItem() ?? 0 }} - {{ $vervals->lastItem() ?? 0 }} dari {{ number_format($vervals->total(), 0, ',', '.') }} data
                </span>
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
                            <th style="padding:14px 18px;text-align:center;min-width:140px;">Aksi</th>
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
                                    <button type="button" class="btn-act survey" onclick="startSurveyWithGps('{{ url('/survey/' . $item->id) }}')">
                                        <i class="fas fa-camera"></i> {{ $item->foto_sudut_depan ? 'Lihat / Edit' : 'Mulai Survei' }}
                                    </button>
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
@endsection

@push('scripts')
<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    let pendingSurveyUrl = null;

    /**
     * Intercept Mulai Survei — Loading Overlay & Wajibkan GPS Geolocation Aktif
     */
    function startSurveyWithGps(targetUrl) {
        pendingSurveyUrl = targetUrl;

        // 1. Tampilkan Reusable Loading Overlay
        if (window.PuprLoading) {
            window.PuprLoading.show('Sedang Memuat Lokasi GPS...');
        }

        if (!navigator.geolocation) {
            if (window.PuprLoading) window.PuprLoading.hide();
            alert("Perangkat atau browser Anda tidak mendukung fitur Geolocation/GPS.");
            return;
        }

        // 2. Request Geolocation Position
        navigator.geolocation.getCurrentPosition(
            function(position) {
                // GPS Berhasil Didapat -> Update status loading & pindah ke form survei
                if (window.PuprLoading) {
                    window.PuprLoading.show('Lokasi Terdeteksi, Membuka Form Survei...');
                }
                window.location.href = targetUrl;
            },
            function(error) {
                // GPS Gagal / Ditolak / Dimatikan -> Tutup loading & buka modal peringatan GPS
                if (window.PuprLoading) {
                    window.PuprLoading.hide();
                }
                if (window.PuprModal) {
                    window.PuprModal.open('modalGpsRequired');
                } else {
                    alert("Silakan aktifkan lokasi jika mau melakukan survei!");
                }
            },
            {
                enableHighAccuracy: true,
                timeout: 8000,
                maximumAge: 0
            }
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
                if (window.PuprModal) {
                    window.PuprModal.open('modalGpsRequired');
                }
            },
            { enableHighAccuracy: true, timeout: 8000 }
        );
    }

    document.addEventListener('DOMContentLoaded', function () {
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
    });
</script>
@endpush
