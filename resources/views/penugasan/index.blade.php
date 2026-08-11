@extends('layouts.partial.app')

@section('title', 'BSPS Verval - Penugasan Petugas Lapangan')
@section('title_header', 'Penugasan Petugas Verval')
@section('subtitle_header', 'Alokasi & Penugasan Petugas Lapangan per Desa untuk Verifikasi Calon Penerima Bantuan')

@push('styles')
<style>
    /* Stats Grid */
    .stats-penugasan {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
        margin-bottom: 24px;
    }

    .stat-item {
        background: var(--bg-card);
        border-radius: var(--radius-sm);
        padding: 18px 20px;
        box-shadow: var(--shadow-sm);
        border: 1px solid rgba(0, 40, 85, 0.06);
        display: flex;
        align-items: center;
        gap: 14px;
        transition: var(--transition);
    }

    .stat-item:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
    }

    .stat-item .icon {
        width: 46px;
        height: 46px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        flex-shrink: 0;
    }

    .stat-item .icon.blue   { background: rgba(0, 40, 85, 0.10); color: var(--primary); }
    .stat-item .icon.green  { background: rgba(39, 174, 96, 0.12); color: var(--success); }
    .stat-item .icon.orange { background: rgba(255, 184, 0, 0.15); color: #d69e00; }
    .stat-item .icon.purple { background: rgba(142, 68, 173, 0.12); color: var(--purple, #8e44ad); }

    .stat-item .info .value {
        font-size: 24px;
        font-weight: 800;
        line-height: 1.2;
        color: var(--primary-dark);
    }

    .stat-item .info .label {
        font-size: 12px;
        color: var(--text-muted);
        font-weight: 600;
        margin-top: 2px;
    }

    /* Filter Section */
    .filter-section {
        background: var(--bg-card);
        border-radius: var(--radius);
        padding: 16px 20px;
        box-shadow: var(--shadow-sm);
        border: 1px solid rgba(0, 40, 85, 0.06);
        margin-bottom: 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
    }

    .filter-left {
        display: flex;
        align-items: center;
        gap: 12px;
        flex: 1;
        flex-wrap: wrap;
    }

    .search-input-wrap {
        position: relative;
        min-width: 260px;
        flex: 1;
    }

    .search-input-wrap input {
        width: 100%;
        padding: 10px 14px 10px 38px;
        border-radius: var(--radius-sm);
        border: 1px solid rgba(0, 40, 85, 0.14);
        background: var(--bg-body);
        color: var(--text-primary);
        font-size: 13.5px;
        outline: none;
        transition: all 0.2s ease;
        box-sizing: border-box;
    }

    .search-input-wrap input:focus {
        border-color: var(--primary);
        background: var(--bg-card);
        box-shadow: 0 0 0 3px rgba(0, 40, 85, 0.08);
    }

    .search-input-wrap i {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--text-muted);
        font-size: 14px;
    }

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
        border-bottom: 1px solid rgba(0, 40, 85, 0.06);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
    }

    .table-header-bar h3 {
        font-size: 16px;
        font-weight: 800;
        color: var(--primary);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    /* Petugas Badge / Pill */
    .petugas-pill {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 5px 12px;
        border-radius: 20px;
        background: rgba(0, 40, 85, 0.06);
        border: 1px solid rgba(0, 40, 85, 0.12);
        font-size: 12.5px;
        font-weight: 700;
        color: var(--primary-dark);
    }

    .petugas-pill .avatar-mini {
        width: 24px;
        height: 24px;
        border-radius: 50%;
        background: var(--primary);
        color: #fff;
        font-size: 11px;
        font-weight: 800;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .badge-unassigned {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 10px;
        border-radius: 20px;
        background: rgba(231, 76, 60, 0.10);
        color: #e74c3c;
        font-size: 11.5px;
        font-weight: 700;
    }

    .badge-gender {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 28px;
        height: 28px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 800;
    }
    .badge-gender.l { background: rgba(0, 40, 85, 0.10); color: var(--primary); border: 1px solid rgba(0, 40, 85, 0.18); }
    .badge-gender.p { background: rgba(212, 63, 120, 0.12); color: #d43f78; border: 1px solid rgba(212, 63, 120, 0.22); }

    .btn-act {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        border: none;
        cursor: pointer;
        text-decoration: none;
        transition: all 0.2s ease;
    }
    .btn-act.survey { background: rgba(39, 174, 96, 0.12); color: var(--success); }
    .btn-act.survey:hover { background: var(--success); color: #fff; }

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

    .pagination-info-text {
        font-size: 13px;
        color: var(--text-muted);
        font-weight: 500;
    }

    .pagination-info-text strong {
        color: var(--primary-dark);
        font-weight: 700;
    }

    .pagination-nav {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        margin: 0;
        padding: 0;
    }

    .pg-link {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 36px;
        height: 36px;
        padding: 0 12px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 700;
        color: var(--text-primary);
        background: var(--bg-body);
        border: 1px solid rgba(0, 40, 85, 0.14);
        text-decoration: none;
        transition: all 0.2s ease;
    }

    .pg-link:hover {
        background: var(--primary);
        color: #ffffff;
        border-color: var(--primary);
        transform: translateY(-1px);
        box-shadow: var(--shadow-sm);
    }

    .pg-link.active {
        background: var(--primary);
        color: #ffffff;
        border-color: var(--primary);
        box-shadow: 0 2px 6px rgba(0, 40, 85, 0.25);
    }

    .pg-link.disabled {
        opacity: 0.4;
        cursor: not-allowed;
        pointer-events: none;
        background: var(--bg-body);
        color: var(--text-muted);
        border-color: rgba(0, 40, 85, 0.08);
    }

    .pg-dots {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 30px;
        height: 36px;
        font-size: 14px;
        font-weight: 700;
        color: var(--text-muted);
        letter-spacing: 2px;
    }

    /* Jump to Page Form */
    .jump-page-form {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        margin-left: 12px;
    }

    .jump-page-input {
        width: 54px;
        height: 36px;
        text-align: center;
        border-radius: 8px;
        border: 1px solid rgba(0, 40, 85, 0.16);
        background: var(--bg-body);
        color: var(--text-primary);
        font-size: 13px;
        font-weight: 700;
        outline: none;
        transition: all 0.2s ease;
    }

    .jump-page-input:focus {
        border-color: var(--primary);
        background: var(--bg-card);
        box-shadow: 0 0 0 3px rgba(0, 40, 85, 0.08);
    }

    .jump-page-btn {
        height: 36px;
        padding: 0 12px;
        border-radius: 8px;
        background: var(--primary);
        color: #fff;
        border: none;
        font-size: 12px;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .jump-page-btn:hover {
        background: var(--primary-dark);
        transform: translateY(-1px);
    }

    .table-penugasan-wrapper {
        width: 100%;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    .table-container-card table {
        width: 100%;
        min-width: 980px;
        border-collapse: collapse;
        white-space: nowrap;
    }

    .table-container-card table th,
    .table-container-card table td {
        white-space: nowrap;
    }

    @media (max-width: 992px) {
        .stats-penugasan { grid-template-columns: repeat(2, 1fr); gap: 12px; }
    }

    @media (max-width: 768px) {
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
        .stats-penugasan { grid-template-columns: 1fr; gap: 10px; }
        .stat-item { padding: 14px 16px; gap: 12px; border-radius: 12px; }
        .stat-item .icon { width: 42px; height: 42px; font-size: 18px; border-radius: 10px; }
        .stat-item .info .value { font-size: 20px; }
        .stat-item .info .label { font-size: 11.5px; }
        .filter-section { flex-direction: column; align-items: stretch; padding: 14px; gap: 10px; }
        .filter-left { flex-direction: column; align-items: stretch; gap: 10px; }
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
            <a href="{{ url('/') }}" style="color:var(--primary);text-decoration:none;font-weight:500;"><i class="fas fa-home"></i> Home</a>
            <i class="fas fa-chevron-right" style="font-size:10px;"></i>
            <span>Penugasan Petugas Verval</span>
        </div>

        <!-- 4 Top Stat Summary Cards -->
        <div class="stats-penugasan">
            <div class="stat-item">
                <div class="icon blue"><i class="fas fa-users-viewfinder"></i></div>
                <div class="info">
                    <div class="value">{{ number_format($stats['total'], 0, ',', '.') }}</div>
                    <div class="label">Total Calon Penerima</div>
                </div>
            </div>
            <div class="stat-item">
                <div class="icon green"><i class="fas fa-user-check"></i></div>
                <div class="info">
                    <div class="value">{{ number_format($stats['ditugaskan'], 0, ',', '.') }}</div>
                    <div class="label">Penerima Ditugaskan</div>
                </div>
            </div>
            <div class="stat-item">
                <div class="icon orange"><i class="fas fa-user-shield"></i></div>
                <div class="info">
                    <div class="value">{{ $stats['total_petugas'] }}</div>
                    <div class="label">Petugas Desa Terdaftar</div>
                </div>
            </div>
            <div class="stat-item">
                <div class="icon purple"><i class="fas fa-building-columns"></i></div>
                <div class="info">
                    <div class="value">{{ $stats['desa'] }}</div>
                    <div class="label">Desa / Kelurahan</div>
                </div>
            </div>
        </div>

        <!-- Filter & Search Bar Form -->
        <form action="{{ url('/penugasan') }}" method="GET" class="filter-section" id="filterFormPenugasan">
            <div class="filter-left">
                <div class="search-input-wrap">
                    <i class="fas fa-search"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama calon penerima, NIK, alamat, desa, atau nama petugas..." />
                </div>

                {{-- Hidden inputs --}}
                <input type="hidden" name="kecamatan" id="hiddenKecamatanPenugasan" value="{{ request('kecamatan', 'all') }}" />
                <input type="hidden" name="petugas_id" id="hiddenPetugasPenugasan" value="{{ request('petugas_id', 'all') }}" />

                {{-- Custom Dropdown: Kecamatan --}}
                <div class="pupr-dropdown-wrapper" id="ddKecPenugasanWrapper">
                    <button type="button" class="pupr-dropdown-toggle" id="ddKecPenugasanBtn" onclick="window.PuprDropdown.toggle(document.getElementById('ddKecPenugasanWrapper'))">
                        <i class="fas fa-map-marker-alt" style="font-size:12px;opacity:0.6;"></i>
                        <span class="selected-label">
                            {{ request('kecamatan') && request('kecamatan') !== 'all' ? 'Kec. '.request('kecamatan') : 'Semua Kecamatan' }}
                        </span>
                        <i class="fas fa-chevron-down" style="font-size:10px;opacity:0.5;"></i>
                    </button>
                    <div class="pupr-dropdown-menu" style="min-width:210px;max-height:300px;overflow-y:auto;">
                        <div class="pupr-dropdown-item {{ request('kecamatan', 'all') === 'all' ? 'active' : '' }}"
                             onclick="selectDropdown('hiddenKecamatanPenugasan', 'ddKecPenugasanWrapper', 'all', 'Semua Kecamatan', 'filterFormPenugasan')">
                            <i class="fas fa-th-list" style="font-size:12px;opacity:0.5;"></i> Semua Kecamatan
                        </div>
                        <div class="dropdown-divider"></div>
                        @foreach($listKecamatan as $kec)
                        <div class="pupr-dropdown-item {{ request('kecamatan') === $kec ? 'active' : '' }}"
                             onclick="selectDropdown('hiddenKecamatanPenugasan', 'ddKecPenugasanWrapper', '{{ $kec }}', 'Kec. {{ $kec }}', 'filterFormPenugasan')">
                            <i class="fas fa-map-pin" style="font-size:11px;opacity:0.4;"></i> Kec. {{ $kec }}
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- Custom Dropdown: Filter Petugas Desa --}}
                <div class="pupr-dropdown-wrapper" id="ddPetugasPenugasanWrapper">
                    <button type="button" class="pupr-dropdown-toggle" onclick="window.PuprDropdown.toggle(document.getElementById('ddPetugasPenugasanWrapper'))">
                        <i class="fas fa-user-shield" style="font-size:12px;opacity:0.6;"></i>
                        <span class="selected-label">
                            @php
                                $selectedPetugas = $listPetugas->firstWhere('id', request('petugas_id'));
                            @endphp
                            {{ $selectedPetugas ? $selectedPetugas->name : 'Semua Petugas Desa' }}
                        </span>
                        <i class="fas fa-chevron-down" style="font-size:10px;opacity:0.5;"></i>
                    </button>
                    <div class="pupr-dropdown-menu" style="min-width:260px;max-height:300px;overflow-y:auto;">
                        <div class="pupr-dropdown-item {{ request('petugas_id', 'all') === 'all' ? 'active' : '' }}"
                             onclick="selectDropdown('hiddenPetugasPenugasan', 'ddPetugasPenugasanWrapper', 'all', 'Semua Petugas Desa', 'filterFormPenugasan')">
                            <i class="fas fa-users" style="font-size:12px;opacity:0.5;"></i> Semua Petugas Desa
                        </div>
                        <div class="dropdown-divider"></div>
                        @foreach($listPetugas as $p)
                        <div class="pupr-dropdown-item {{ request('petugas_id') == $p->id ? 'active' : '' }}"
                             onclick="selectDropdown('hiddenPetugasPenugasan', 'ddPetugasPenugasanWrapper', '{{ $p->id }}', '{{ $p->name }}', 'filterFormPenugasan')">
                            <i class="fas fa-user-check" style="font-size:11px;color:var(--primary);"></i> {{ $p->name }}
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div style="display:flex;gap:10px;">
                <a href="{{ url('/penugasan') }}" class="btn btn-outline" style="padding:10px 16px;font-size:13px;text-decoration:none;border-radius:var(--radius-sm);">
                    <i class="fas fa-redo"></i> Reset
                </a>
            </div>
        </form>

        <!-- Main Data Table Penugasan -->
        <div class="table-container-card">
            <div class="table-header-bar">
                <h3><i class="fas fa-tasks"></i> Data Calon Penerima &amp; Petugas Penanggung Jawab Desa</h3>
                <span style="font-size:12.5px;color:var(--text-muted);font-weight:600;">
                    Menampilkan {{ $vervals->firstItem() ?? 0 }} - {{ $vervals->lastItem() ?? 0 }} dari {{ number_format($vervals->total(), 0, ',', '.') }} data
                </span>
            </div>

            <div class="table-penugasan-wrapper">
                <table class="table" style="width:100%;border-collapse:collapse;min-width:980px;">
                    <thead>
                        <tr style="background:var(--bg-body);border-bottom:1px solid rgba(0,40,85,0.08);text-align:left;font-size:12.5px;color:var(--text-muted);">
                            <th style="padding:14px 18px;width:50px;">No</th>
                            <th style="padding:14px 18px;min-width:200px;">Nama Calon Penerima</th>
                            <th style="padding:14px 18px;text-align:center;width:60px;">L/P</th>
                            <th style="padding:14px 18px;min-width:180px;">NIK &amp; No. KK</th>
                            <th style="padding:14px 18px;min-width:200px;">Alamat &amp; Dusun</th>
                            <th style="padding:14px 18px;min-width:160px;">Desa / Kelurahan</th>
                            <th style="padding:14px 18px;min-width:140px;">Kecamatan</th>
                            <th style="padding:14px 18px;min-width:260px;">Petugas Verval Ditugaskan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($vervals as $index => $item)
                            <tr style="border-bottom:1px solid rgba(0,40,85,0.06);font-size:13px;transition:all 0.15s ease;">
                                <td style="padding:14px 18px;font-weight:700;color:var(--text-muted);">
                                    {{ $vervals->firstItem() + $index }}
                                </td>
                                <td style="padding:14px 18px;">
                                    <div style="font-weight:800;color:var(--primary-dark);">
                                        {{ $item->nama }}
                                    </div>
                                </td>
                                <td style="padding:14px 18px;text-align:center;">
                                    <span class="badge-gender {{ strtolower($item->jenis_kelamin) }}">
                                        {{ $item->jenis_kelamin == 'L' ? 'L' : ($item->jenis_kelamin == 'P' ? 'P' : ($item->jenis_kelamin ?: '-')) }}
                                    </span>
                                </td>
                                <td style="padding:14px 18px;">
                                    <div style="font-family:monospace;font-weight:700;color:var(--text-primary);letter-spacing:0.3px;">
                                        <span style="font-size:11px;color:var(--text-muted);font-weight:600;margin-right:4px;">NIK:</span>{{ $item->no_ktp ?: '-' }}
                                    </div>
                                    <div style="font-family:monospace;font-weight:600;color:var(--text-muted);font-size:12px;margin-top:3px;letter-spacing:0.3px;">
                                        <span style="font-size:11px;color:var(--text-muted);font-weight:600;margin-right:4px;">KK:</span>{{ $item->no_kk ?: '-' }}
                                    </div>
                                </td>
                                <td style="padding:14px 18px;color:var(--text-secondary);">
                                    {{ $item->alamat ?: '-' }}
                                </td>
                                <td style="padding:14px 18px;font-weight:700;color:var(--primary-dark);">
                                    {{ $item->desa_kelurahan ?: '-' }}
                                </td>
                                <td style="padding:14px 18px;">
                                    <span style="font-weight:700;color:var(--primary);">Kec. {{ $item->kecamatan }}</span>
                                </td>
                                <td style="padding:14px 18px;">
                                    @if($item->petugas)
                                        <div class="petugas-pill">
                                            <span class="avatar-mini">{{ strtoupper(substr($item->petugas->name, 0, 1)) }}</span>
                                            <div>
                                                <div style="font-size:12.5px;color:var(--primary-dark);font-weight:800;">{{ $item->petugas->name }}</div>
                                                <div style="font-size:11px;color:var(--text-muted);font-weight:500;">{{ $item->petugas->email }}</div>
                                                <div style="font-size:10.5px;color:#27ae60;font-weight:700;font-family:monospace;margin-top:2px;">
                                                    <i class="fas fa-key" style="font-size:9px;"></i> Pass: {{ $item->petugas->plain_password ?: 'password123' }}
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <span class="badge-unassigned">
                                            <i class="fas fa-user-slash"></i> Belum Ada Petugas
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" style="text-align:center;padding:32px;color:var(--text-muted);">
                                    <i class="fas fa-clipboard-question" style="font-size:28px;display:block;margin-bottom:8px;opacity:0.4;"></i>
                                    Tidak ada data penugasan calon penerima yang sesuai dengan kriteria saringan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination Bar Custom -->
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
                        {{-- Tombol Previous --}}
                        @if($vervals->onFirstPage())
                            <span class="pg-link disabled"><i class="fas fa-chevron-left"></i></span>
                        @else
                            <a href="{{ $vervals->previousPageUrl() }}" class="pg-link"><i class="fas fa-chevron-left"></i></a>
                        @endif

                        {{-- Halaman 1 sampai N dengan Ellipsis pintar --}}
                        @foreach($rangeWithDots as $pageItem)
                            @if($pageItem === '...')
                                <span class="pg-dots">&hellip;</span>
                            @elseif($pageItem == $current)
                                <span class="pg-link active">{{ $pageItem }}</span>
                            @else
                                <a href="{{ $vervals->url($pageItem) }}" class="pg-link">{{ $pageItem }}</a>
                            @endif
                        @endforeach

                        {{-- Tombol Next --}}
                        @if($vervals->hasMorePages())
                            <a href="{{ $vervals->nextPageUrl() }}" class="pg-link"><i class="fas fa-chevron-right"></i></a>
                        @else
                            <span class="pg-link disabled"><i class="fas fa-chevron-right"></i></span>
                        @endif
                    </div>

                    <!-- Jump To Page Form -->
                    <form action="{{ url('/penugasan') }}" method="GET" class="jump-page-form">
                        @if(request('search')) <input type="hidden" name="search" value="{{ request('search') }}"> @endif
                        @if(request('kecamatan') && request('kecamatan') != 'all') <input type="hidden" name="kecamatan" value="{{ request('kecamatan') }}"> @endif
                        @if(request('petugas_id') && request('petugas_id') != 'all') <input type="hidden" name="petugas_id" value="{{ request('petugas_id') }}"> @endif
                        <span style="font-size:12px;color:var(--text-muted);font-weight:600;">Lompat:</span>
                        <input type="number" name="page" min="1" max="{{ $last }}" value="{{ $current }}" class="jump-page-input" title="Masukkan nomor halaman" />
                        <button type="submit" class="jump-page-btn" title="Buka Halaman">Go</button>
                    </form>
                </div>
            </div>
        </div>
    </main>
@endsection
