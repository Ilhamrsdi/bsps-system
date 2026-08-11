@extends('layouts.partial.app')

@section('title', 'BSPS Verval - Data Verifikasi & Validasi')
@section('title_header', 'Data Verval BSPS')
@section('subtitle_header', 'Database Calon Penerima Bantuan Stimulan Perumahan Swadaya (BSPS) Kabupaten Jember')

@push('styles')
    <style>
        /* Stats Grid */
        .stats-verval {
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

        .stat-item .icon.blue {
            background: rgba(0, 40, 85, 0.10);
            color: var(--primary);
        }

        .stat-item .icon.green {
            background: rgba(39, 174, 96, 0.12);
            color: var(--success);
        }

        .stat-item .icon.orange {
            background: rgba(255, 184, 0, 0.15);
            color: #d69e00;
        }

        .stat-item .icon.purple {
            background: rgba(142, 68, 173, 0.12);
            color: var(--purple, #8e44ad);
        }

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

        .badge-desil {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
            background: rgba(0, 40, 85, 0.08);
            color: var(--primary);
        }

        .badge-desil.backlog-1 {
            background: rgba(39, 174, 96, 0.12);
            color: var(--success);
        }

    .btn-act.view { background: rgba(0, 40, 85, 0.08); color: var(--primary); }
    .btn-act.view:hover { background: var(--primary); color: #fff; }
    .btn-act.print { background: rgba(255, 184, 0, 0.18); color: #b88600; }
    .btn-act.print:hover { background: #ffb800; color: #002855; }
    .btn-act.map  { background: rgba(39, 174, 96, 0.12); color: var(--success); }
    .btn-act.map:hover { background: var(--success); color: #fff; }

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

        .badge-gender.l {
            background: rgba(0, 40, 85, 0.10);
            color: var(--primary);
            border: 1px solid rgba(0, 40, 85, 0.18);
        }

        .badge-gender.p {
            background: rgba(212, 63, 120, 0.12);
            color: #d43f78;
            border: 1px solid rgba(212, 63, 120, 0.22);
        }

        .action-btn-group {
            display: flex;
            align-items: center;
            gap: 6px;
        }

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

        .btn-act.view {
            background: rgba(0, 40, 85, 0.08);
            color: var(--primary);
        }

        .btn-act.view:hover {
            background: var(--primary);
            color: #fff;
        }

        .btn-act.map {
            background: rgba(39, 174, 96, 0.12);
            color: var(--success);
        }

        .btn-act.map:hover {
            background: var(--success);
            color: #fff;
        }

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

        .table-verval-wrapper {
            width: 100%;
            overflow-x: auto;
            overflow-y: visible;
            -webkit-overflow-scrolling: touch;
        }

        /* Pastikan td kolom status tidak clip dropdown menu */
        .table-verval-wrapper td.td-status-cell {
            overflow: visible;
            position: relative;
        }

        /* Dropdown di dalam tabel — munculkan ke atas agar tidak mepet bawah */
        .table-verval-wrapper .pupr-dropdown-wrapper {
            position: static;
        }

        .table-verval-wrapper .pupr-dropdown-menu {
            position: fixed;
            z-index: 9999;
        }

        .table-container-card table {
            width: 100%;
            min-width: 1050px;
            border-collapse: collapse;
            white-space: nowrap;
        }

        .table-container-card table th,
        .table-container-card table td {
            white-space: nowrap;
        }

        @media (max-width: 992px) {
            .stats-verval {
                grid-template-columns: repeat(2, 1fr);
                gap: 12px;
            }
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
            .stats-verval {
                grid-template-columns: 1fr;
                gap: 10px;
            }

            .stat-item {
                padding: 14px 16px;
                gap: 12px;
                border-radius: 12px;
            }

            .stat-item .icon {
                width: 42px;
                height: 42px;
                font-size: 18px;
                border-radius: 10px;
            }

            .stat-item .info .value {
                font-size: 20px;
            }

            .stat-item .info .label {
                font-size: 11.5px;
            }

            .filter-section {
                flex-direction: column;
                align-items: stretch;
                padding: 14px;
                gap: 10px;
            }

            .filter-left {
                flex-direction: column;
                align-items: stretch;
                gap: 10px;
            }

            .search-input-wrap {
                min-width: 100%;
            }

            .pupr-dropdown-wrapper,
            .pupr-dropdown-toggle {
                width: 100%;
            }

            .pupr-dropdown-toggle {
                justify-content: space-between;
            }

            .pagination-nav {
                flex-wrap: wrap;
                justify-content: center;
            }
        }
    </style>
@endpush

@section('content')
    <!-- Navbar Component -->
    @include('layouts.navbar')

    <main class="dashboard-content">
        <!-- Breadcrumb -->
        <div class="breadcrumb"
            style="font-size:13px;color:var(--text-muted);margin-bottom:20px;display:flex;align-items:center;gap:8px;">
            <a href="{{ url('/') }}" style="color:var(--primary);text-decoration:none;font-weight:500;"><i
                    class="fas fa-home"></i> Home</a>
            <i class="fas fa-chevron-right" style="font-size:10px;"></i>
            <span>Data Verval BSPS</span>
        </div>

        <!-- 4 Top Stat Summary Cards -->
        <div class="stats-verval">
            <div class="stat-item">
                <div class="icon blue"><i class="fas fa-users-viewfinder"></i></div>
                <div class="info">
                    <div class="value">{{ number_format($stats['total'], 0, ',', '.') }}</div>
                    <div class="label">Total Calon Penerima</div>
                </div>
            </div>
            <div class="stat-item">
                <div class="icon green"><i class="fas fa-map-location-dot"></i></div>
                <div class="info">
                    <div class="value">{{ $stats['kecamatan'] }}</div>
                    <div class="label">Kecamatan Terdata</div>
                </div>
            </div>
            <div class="stat-item">
                <div class="icon orange"><i class="fas fa-building-columns"></i></div>
                <div class="info">
                    <div class="value">{{ $stats['desa'] }}</div>
                    <div class="label">Desa / Kelurahan</div>
                </div>
            </div>
            <div class="stat-item">
                <div class="icon purple"><i class="fas fa-filter"></i></div>
                <div class="info">
                    <div class="value">{{ number_format($stats['filter'], 0, ',', '.') }}</div>
                    <div class="label">Hasil Saringan Data</div>
                </div>
            </div>
        </div>

        <!-- Filter & Search Bar -->
        <form action="{{ url('/verval-data') }}" method="GET" class="filter-section" id="filterFormVerval">
            <div class="filter-left">
                <div class="search-input-wrap">
                    <i class="fas fa-search"></i>
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Cari nama pemohon, NIK, No. KK, atau alamat..." />
                </div>

                {{-- Hidden inputs untuk submit via dropdown --}}
                <input type="hidden" name="kecamatan" id="hiddenKecamatan" value="{{ request('kecamatan', 'all') }}" />
                <input type="hidden" name="desil" id="hiddenDesil" value="{{ request('desil', 'all') }}" />

                {{-- Custom Dropdown: Kecamatan --}}
                <div class="pupr-dropdown-wrapper" id="ddKecWrapper">
                    <button type="button" class="pupr-dropdown-toggle" id="ddKecBtn" onclick="window.PuprDropdown.toggle(document.getElementById('ddKecWrapper'))">
                        <i class="fas fa-map-marker-alt" style="font-size:12px;opacity:0.6;"></i>
                        <span class="selected-label">
                            {{ request('kecamatan') && request('kecamatan') !== 'all' ? 'Kec. '.request('kecamatan') : 'Semua Kecamatan' }}
                        </span>
                        <i class="fas fa-chevron-down" style="font-size:10px;opacity:0.5;"></i>
                    </button>
                    <div class="pupr-dropdown-menu" style="min-width:200px;max-height:300px;overflow-y:auto;">
                        <div class="pupr-dropdown-item {{ request('kecamatan', 'all') === 'all' ? 'active' : '' }}"
                             onclick="selectDropdown('hiddenKecamatan', 'ddKecWrapper', 'all', 'Semua Kecamatan', 'filterFormVerval')">
                            <i class="fas fa-th-list" style="font-size:12px;opacity:0.5;"></i> Semua Kecamatan
                        </div>
                        <div class="dropdown-divider"></div>
                        @foreach($listKecamatan as $kec)
                        <div class="pupr-dropdown-item {{ request('kecamatan') === $kec ? 'active' : '' }}"
                             onclick="selectDropdown('hiddenKecamatan', 'ddKecWrapper', '{{ $kec }}', 'Kec. {{ $kec }}', 'filterFormVerval')">
                            <i class="fas fa-map-pin" style="font-size:11px;opacity:0.4;"></i> Kec. {{ $kec }}
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- Custom Dropdown: Pengelompokan Desil --}}
                <div class="pupr-dropdown-wrapper" id="ddDesilWrapper">
                    <button type="button" class="pupr-dropdown-toggle" onclick="window.PuprDropdown.toggle(document.getElementById('ddDesilWrapper'))">
                        <i class="fas fa-layer-group" style="font-size:12px;opacity:0.6;"></i>
                        <span class="selected-label">
                            @if(request('desil') === 'Backlog 1') Backlog 1 Desil 1-4
                            @elseif(request('desil') === 'Backlog 2') Backlog 2 Desil 1-4
                            @else Semua Pengelompokan
                            @endif
                        </span>
                        <i class="fas fa-chevron-down" style="font-size:10px;opacity:0.5;"></i>
                    </button>
                    <div class="pupr-dropdown-menu" style="min-width:200px;">
                        <div class="pupr-dropdown-item {{ request('desil', 'all') === 'all' ? 'active' : '' }}"
                             onclick="selectDropdown('hiddenDesil', 'ddDesilWrapper', 'all', 'Semua Pengelompokan', 'filterFormVerval')">
                            <i class="fas fa-th-list" style="font-size:12px;opacity:0.5;"></i> Semua Pengelompokan
                        </div>
                        <div class="dropdown-divider"></div>
                        <div class="pupr-dropdown-item {{ request('desil') === 'Backlog 1' ? 'active' : '' }}"
                             onclick="selectDropdown('hiddenDesil', 'ddDesilWrapper', 'Backlog 1', 'Backlog 1 Desil 1-4', 'filterFormVerval')">
                            <i class="fas fa-circle" style="font-size:8px;color:var(--info);"></i> Backlog 1 Desil 1-4
                        </div>
                        <div class="pupr-dropdown-item {{ request('desil') === 'Backlog 2' ? 'active' : '' }}"
                             onclick="selectDropdown('hiddenDesil', 'ddDesilWrapper', 'Backlog 2', 'Backlog 2 Desil 1-4', 'filterFormVerval')">
                            <i class="fas fa-circle" style="font-size:8px;color:var(--success);"></i> Backlog 2 Desil 1-4
                        </div>
                    </div>
                </div>
            </div>
            <div style="display:flex;gap:10px;flex-wrap:wrap;">
                <a href="{{ route('verval-data.surat-pernyataan-kolektif', request()->all()) }}" target="_blank" class="btn" style="padding:10px 16px;font-size:13px;font-weight:700;background:#ffb800;color:#002855;text-decoration:none;border-radius:var(--radius-sm);display:inline-flex;align-items:center;gap:6px;" title="Cetak Surat Pernyataan seluruh data hasil filter saat ini">
                    <i class="fas fa-file-signature"></i> Cetak Kolektif Surat Pernyataan
                </a>
                <a href="{{ url('/verval-data') }}" class="btn btn-outline"
                    style="padding:10px 16px;font-size:13px;text-decoration:none;border-radius:var(--radius-sm);">
                    <i class="fas fa-redo"></i> Reset
                </a>
                @if(!auth()->check() || !auth()->user()->isAdminKecamatan())
                <a href="{{ url('/survey') }}" class="btn btn-primary"
                    style="padding:10px 20px;font-size:13px;font-weight:700;background:var(--primary);color:#fff;text-decoration:none;border-radius:var(--radius-sm);display:inline-flex;align-items:center;gap:8px;">
                    <i class="fas fa-plus"></i> Input Survei Baru
                </a>
                @endif
            </div>
        </form>

        <!-- Main Data Table -->
        <div class="table-container-card">
            <div class="table-header-bar">
                <h3><i class="fas fa-clipboard-list"></i> Database Calon Penerima Bantuan BSPS (Verval Data)</h3>
                <span style="font-size:12.5px;color:var(--text-muted);font-weight:600;">
                    Menampilkan {{ $vervals->firstItem() ?? 0 }} - {{ $vervals->lastItem() ?? 0 }} dari
                    {{ number_format($vervals->total(), 0, ',', '.') }} data
                </span>
            </div>

            <div class="table-verval-wrapper">
                <table class="table" style="width:100%;border-collapse:collapse;min-width:1050px;">
                    <thead>
                        <tr
                            style="background:var(--bg-body);border-bottom:1px solid rgba(0,40,85,0.08);text-align:left;font-size:12.5px;color:var(--text-muted);">
                            <th style="padding:14px 18px;">No</th>
                            <th style="padding:14px 18px;">Nama Calon Penerima</th>
                            <th style="padding:14px 18px;text-align:center;">L/P</th>
                            <th style="padding:14px 18px;">NIK &amp; No. KK</th>
                            <th style="padding:14px 18px;">Alamat &amp; Dusun</th>
                            <th style="padding:14px 18px;">Desa / Kelurahan</th>
                            <th style="padding:14px 18px;">Kecamatan</th>
                            <th style="padding:14px 18px;">Kelompok Desil</th>
                            <th style="padding:14px 18px;text-align:center;">Status</th>
                            <th style="padding:14px 18px;text-align:center;">Aksi</th>
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
                                    <div
                                        style="font-family:monospace;font-weight:700;color:var(--text-primary);letter-spacing:0.3px;">
                                        <span
                                            style="font-size:11px;color:var(--text-muted);font-weight:600;margin-right:4px;">NIK:</span>{{ $item->no_ktp ?: '-' }}
                                    </div>
                                    <div
                                        style="font-family:monospace;font-weight:600;color:var(--text-muted);font-size:12px;margin-top:3px;letter-spacing:0.3px;">
                                        <span
                                            style="font-size:11px;color:var(--text-muted);font-weight:600;margin-right:4px;">KK:</span>{{ $item->no_kk ?: '-' }}
                                    </div>
                                </td>
                                <td style="padding:14px 18px;color:var(--text-secondary);">
                                    {{ $item->alamat ?: '-' }}
                                </td>
                                <td style="padding:14px 18px;font-weight:600;color:var(--primary-dark);">
                                    {{ $item->desa_kelurahan ?: '-' }}
                                </td>
                                <td style="padding:14px 18px;">
                                    <span style="font-weight:700;color:var(--primary);">Kec. {{ $item->kecamatan }}</span>
                                </td>
                                <td style="padding:14px 18px;">
                                    <span
                                        class="badge-desil {{ str_contains($item->pengelompokan_desil, 'Backlog 1') ? 'backlog-1' : '' }}">
                                        <i class="fas fa-layer-group" style="font-size:10px;"></i>
                                        {{ $item->pengelompokan_desil ?: 'Desil 1-4' }}
                                    </span>
                                </td>
                                <td style="padding:14px 18px;text-align:center;" class="td-status-cell">
                                    @php
                                        $currentStatus = $item->status ?? 'ditemukan';
                                        $statusColors = [
                                            'ditemukan' => '#28a745',
                                            'meninggal' => '#343a40',
                                            'pindah' => '#ffc107',
                                            'tidak diketahui' => '#dc3545',
                                        ];
                                        $textColor = $currentStatus == 'pindah' ? '#000' : '#fff';
                                        $bgColor = $statusColors[$currentStatus] ?? '#28a745';
                                    @endphp
                                    @if(auth()->check() && auth()->user()->isAdminKecamatan())
                                        <span style="background-color: {{ $bgColor }}; color: {{ $textColor }}; font-weight: bold; border-radius: 20px; padding: 4px 12px; font-size: 11px; display: inline-block; width: 110px; text-align: center; text-transform: capitalize;">
                                            {{ $currentStatus }}
                                        </span>
                                    @else
                                        @php
                                            $statusLabels = [
                                                'ditemukan'       => 'Ditemukan',
                                                'meninggal'       => 'Meninggal',
                                                'pindah'          => 'Pindah',
                                                'tidak diketahui' => 'Tidak Diketahui',
                                            ];
                                            $ddId     = 'ddStatus_' . $item->id;
                                            $ddBtnId  = 'ddStatusBtn_' . $item->id;
                                        @endphp
                                        <div class="pupr-dropdown-wrapper" id="{{ $ddId }}" style="min-width:150px;">
                                            <button type="button" class="pupr-dropdown-toggle" id="{{ $ddBtnId }}" onclick="window.PuprDropdown.toggle(document.getElementById('{{ $ddId }}'))">
                                                <i class="fas fa-circle" style="font-size:8px;color:{{ $bgColor }};"></i>
                                                <span class="selected-label" style="text-transform:capitalize;">{{ $statusLabels[$currentStatus] ?? ucfirst($currentStatus) }}</span>
                                                <i class="fas fa-chevron-down" style="font-size:10px;opacity:0.5;"></i>
                                            </button>
                                            <div class="pupr-dropdown-menu" style="min-width:170px;">
                                                <div class="pupr-dropdown-item {{ $currentStatus === 'ditemukan' ? 'active' : '' }}"
                                                     onclick="updateVervalStatus({{ $item->id }}, 'ditemukan', 'Ditemukan', '{{ $ddId }}')"><i class="fas fa-circle" style="font-size:8px;color:#28a745;"></i> Ditemukan</div>
                                                <div class="pupr-dropdown-item {{ $currentStatus === 'meninggal' ? 'active' : '' }}"
                                                     onclick="updateVervalStatus({{ $item->id }}, 'meninggal', 'Meninggal', '{{ $ddId }}')"><i class="fas fa-circle" style="font-size:8px;color:#343a40;"></i> Meninggal</div>
                                                <div class="pupr-dropdown-item {{ $currentStatus === 'pindah' ? 'active' : '' }}"
                                                     onclick="updateVervalStatus({{ $item->id }}, 'pindah', 'Pindah', '{{ $ddId }}')"><i class="fas fa-circle" style="font-size:8px;color:#ffc107;"></i> Pindah</div>
                                                <div class="pupr-dropdown-item {{ $currentStatus === 'tidak diketahui' ? 'active' : '' }}"
                                                     onclick="updateVervalStatus({{ $item->id }}, 'tidak diketahui', 'Tidak Diketahui', '{{ $ddId }}')"><i class="fas fa-circle" style="font-size:8px;color:#dc3545;"></i> Tidak Diketahui</div>
                                            </div>
                                        </div>
                                    @endif
                                </td>
                                <td style="padding:14px 18px;text-align:center;">
                                    <div class="action-btn-group" style="justify-content:center;">
                                        <a href="{{ route('verval-data.surat-pernyataan', $item->id) }}" target="_blank" class="btn-act print" style="background:rgba(255,184,0,0.15);color:#d69e00;" title="Cetak Surat Pernyataan Pemohon Ini">
                                            <i class="fas fa-file-signature"></i>
                                        </a>
                                        <a href="{{ url('/survey/' . $item->id) }}" class="btn-act view" style="background:rgba(39,174,96,0.12);color:var(--success);" title="{{ auth()->check() && auth()->user()->isAdminKecamatan() ? 'Lihat Detail Data Verval (Read-Only)' : 'Buka Form Survei Lapangan & Lengkapi Data/Foto' }}">
                                            <i class="fas {{ auth()->check() && auth()->user()->isAdminKecamatan() ? 'fa-eye' : 'fa-clipboard-check' }}"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" style="text-align:center;padding:32px;color:var(--text-muted);">
                                    <i class="fas fa-clipboard-question"
                                        style="font-size:28px;display:block;margin-bottom:8px;opacity:0.4;"></i>
                                    Tidak ada data calon penerima yang sesuai dengan kriteria saringan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination Bar Custom -->
            <div class="pagination-custom-bar">
                <div class="pagination-info-text">
                    Menampilkan <strong>{{ $vervals->firstItem() ?? 0 }}</strong> -
                    <strong>{{ $vervals->lastItem() ?? 0 }}</strong> dari
                    <strong>{{ number_format($vervals->total(), 0, ',', '.') }}</strong> penerima (Halaman
                    <strong>{{ $vervals->currentPage() }}</strong> dari <strong>{{ $vervals->lastPage() }}</strong>)
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
                            <a href="{{ $vervals->previousPageUrl() }}" class="pg-link"><i
                                    class="fas fa-chevron-left"></i></a>
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
                            <a href="{{ $vervals->nextPageUrl() }}" class="pg-link"><i
                                    class="fas fa-chevron-right"></i></a>
                        @else
                            <span class="pg-link disabled"><i class="fas fa-chevron-right"></i></span>
                        @endif
                    </div>

                    <!-- Jump To Page Form -->
                    <form action="{{ url('/verval-data') }}" method="GET" class="jump-page-form">
                        @if(request('search')) <input type="hidden" name="search" value="{{ request('search') }}"> @endif
                        @if(request('kecamatan') && request('kecamatan') != 'all') <input type="hidden" name="kecamatan"
                        value="{{ request('kecamatan') }}"> @endif
                        @if(request('desil') && request('desil') != 'all') <input type="hidden" name="desil"
                        value="{{ request('desil') }}"> @endif
                        <span style="font-size:12px;color:var(--text-muted);font-weight:600;">Lompat:</span>
                        <input type="number" name="page" min="1" max="{{ $last }}" value="{{ $current }}"
                            class="jump-page-input" title="Masukkan nomor halaman" />
                        <button type="submit" class="jump-page-btn" title="Buka Halaman">Go</button>
                    </form>
                </div>
            </div>
        </div>
    </main>
@endsection

@push('scripts')
    <script>
        /**
         * Client-Side Filter: Menyaring baris tabel langsung di browser saat Mode Offline
         */
        function filterTableClientSide() {
            const searchVal = (document.querySelector('input[name="search"]')?.value || '').toLowerCase().trim();
            const kecVal = (document.getElementById('hiddenKecamatan')?.value || 'all').toLowerCase().trim();
            const desilVal = (document.getElementById('hiddenDesil')?.value || 'all').toLowerCase().trim();

            const rows = document.querySelectorAll('.table-verval-wrapper tbody tr');
            let visibleCount = 0;

            rows.forEach(function(row) {
                if (row.querySelector('td[colspan]')) return;

                const rowText = row.innerText.toLowerCase();
                const kecCell = row.children[6]?.innerText.toLowerCase() || '';
                const desilCell = row.children[7]?.innerText.toLowerCase() || '';

                const matchSearch = !searchVal || rowText.includes(searchVal);
                const matchKec = kecVal === 'all' || kecCell.includes(kecVal);
                const matchDesil = desilVal === 'all' || desilCell.includes(desilVal);

                if (matchSearch && matchKec && matchDesil) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });

            showToast(`Mode Offline: Menampilkan ${visibleCount} data yang cocok`, 'success');
        }

        /**
         * Helper: Pilih item di custom pupr-dropdown dan submit form
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

            // Jika sedang offline, saring tabel langsung di browser tanpa memanggil server
            if (!navigator.onLine) {
                filterTableClientSide();
                return;
            }

            if (formId) {
                const form = document.getElementById(formId);
                if (form) {
                    if (window.PuprLoading) {
                        window.PuprLoading.show('Menyaring Data Verval...');
                    }
                    form.submit();
                }
            }
        }

        function showToast(message, type = 'success') {
            let toastContainer = document.getElementById('toast-container');
            if (!toastContainer) {
                toastContainer = document.createElement('div');
                toastContainer.id = 'toast-container';
                toastContainer.style.cssText = 'position:fixed;bottom:20px;right:20px;z-index:9999;display:flex;flex-direction:column;gap:10px;';
                document.body.appendChild(toastContainer);
            }

            const toast = document.createElement('div');
            const bgColor = type === 'success' ? '#27ae60' : '#e74c3c';
            const icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';

            toast.style.cssText = `background-color:${bgColor};color:#fff;padding:12px 20px;border-radius:8px;box-shadow:0 4px 12px rgba(0,0,0,0.15);font-size:14px;font-weight:600;display:flex;align-items:center;gap:10px;transform:translateY(100%);opacity:0;transition:all 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55);`;
            toast.innerHTML = `<i class="fas ${icon}"></i> ${message}`;

            toastContainer.appendChild(toast);

            // Animate in
            setTimeout(() => {
                toast.style.transform = 'translateY(0)';
                toast.style.opacity = '1';
            }, 10);

            // Animate out and remove
            setTimeout(() => {
                toast.style.transform = 'translateY(100%)';
                toast.style.opacity = '0';
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }

        document.addEventListener('DOMContentLoaded', function () {
            // 1. Pencarian & Filter: Offline client-side filter vs Online server submit
            const filterForm = document.getElementById('filterFormVerval');
            if (filterForm) {
                filterForm.addEventListener('submit', function(e) {
                    if (!navigator.onLine) {
                        e.preventDefault();
                        filterTableClientSide();
                        return;
                    }
                    if (window.PuprLoading) {
                        window.PuprLoading.show('Mencari & Memuat Data Verval...');
                    }
                });
            }

            const searchInput = document.querySelector('input[name="search"]');
            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    if (!navigator.onLine) {
                        filterTableClientSide();
                    }
                });
            }

            // 2. Loading overlay saat klik tombol halaman (Pagination)
            document.querySelectorAll('.pg-link:not(.disabled):not(.active)').forEach(function(link) {
                link.addEventListener('click', function() {
                    if (window.PuprLoading) {
                        window.PuprLoading.show('Memuat Halaman Data...');
                    }
                });
            });

            // 3. Loading overlay saat form lompat halaman
            const jumpForm = document.querySelector('.jump-page-form');
            if (jumpForm) {
                jumpForm.addEventListener('submit', function() {
                    if (window.PuprLoading) {
                        window.PuprLoading.show('Membuka Halaman...');
                    }
                });
            }

            // 4. Auto-posisi dropdown menu (position:fixed) agar muncul tepat di bawah toggle button
            document.addEventListener('click', function (e) {
                const toggle = e.target.closest('.td-status-cell .pupr-dropdown-toggle');
                if (!toggle) return;

                const wrapper = toggle.closest('.pupr-dropdown-wrapper');
                if (!wrapper) return;

                const menu = wrapper.querySelector('.pupr-dropdown-menu');
                if (!menu) return;

                // Posisi berdasarkan toggle button
                const rect = toggle.getBoundingClientRect();
                menu.style.top  = (rect.bottom + 6) + 'px';
                menu.style.left = rect.left + 'px';
                menu.style.right = 'auto';
            }, true);
        });

        const statusIconColors = {
            'ditemukan'      : '#28a745',
            'meninggal'      : '#343a40',
            'pindah'         : '#ffc107',
            'tidak diketahui': '#dc3545'
        };

        function updateVervalStatus(id, status, label, wrapperId) {
            const wrapper = document.getElementById(wrapperId);

            if (wrapper) {
                const lbl = wrapper.querySelector('.selected-label');
                if (lbl) lbl.textContent = label;

                const iconDot = wrapper.querySelector('.pupr-dropdown-toggle .fa-circle');
                if (iconDot) iconDot.style.color = statusIconColors[status] || '#28a745';

                wrapper.querySelectorAll('.pupr-dropdown-item').forEach(i => i.classList.remove('active'));
                const activeItem = [...wrapper.querySelectorAll('.pupr-dropdown-item')]
                    .find(i => i.textContent.trim().toLowerCase().includes(status.toLowerCase()));
                if (activeItem) activeItem.classList.add('active');

                window.PuprDropdown.closeAll();
            }

            // AJAX update ke server
            if (window.PuprLoading) window.PuprLoading.show('Memperbarui status...');

            fetch(`{{ url('/data-verval') }}/${id}/status`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ status: status })
            })
            .then(res => res.json())
            .then(data => {
                if (window.PuprLoading) window.PuprLoading.hide();
                if (data.success) {
                    showToast('Status berhasil diperbarui ke "' + label + '"', 'success');
                } else {
                    showToast('Gagal memperbarui status', 'error');
                }
            })
            .catch(err => {
                if (window.PuprLoading) window.PuprLoading.hide();
                console.error(err);
                showToast('Terjadi kesalahan koneksi', 'error');
            });
        }
    </script>
@endpush
