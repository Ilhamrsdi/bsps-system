@extends('layouts.partial.app')

@section('title', 'BSPS Verval - Dashboard Petugas')
@section('title_header', 'Dashboard Petugas Verval')
@section('subtitle_header', 'Data Verval BSPS - Wilayah Desa {{ Auth::user()->desa ?? "-" }}')

@push('styles')
<style>
    /* Hero Banner Petugas */
    .welcome-card {
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
        color: #ffffff;
        border-radius: var(--radius);
        padding: 26px 30px;
        margin-bottom: 24px;
        box-shadow: 0 10px 24px rgba(0, 40, 85, 0.18);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        border-left: 6px solid var(--secondary);
    }
    .welcome-text h2 { font-size: 20px; font-weight: 800; margin-bottom: 4px; }
    .welcome-text p  { font-size: 13px; opacity: 0.88; margin: 0; }

    /* Stats Grid */
    .stats-grid-petugas {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 18px;
        margin-bottom: 24px;
    }
    .stat-card-petugas {
        background: var(--bg-card);
        border-radius: var(--radius);
        padding: 20px 22px;
        box-shadow: var(--shadow-sm);
        border: 1px solid rgba(0, 40, 85, 0.06);
        display: flex;
        align-items: center;
        gap: 16px;
        transition: var(--transition);
    }
    .stat-card-petugas:hover { transform: translateY(-3px); box-shadow: var(--shadow-md); }
    .stat-card-petugas .stat-icon {
        width: 52px; height: 52px;
        border-radius: var(--radius-sm);
        display: flex; align-items: center; justify-content: center;
        font-size: 22px; flex-shrink: 0;
    }
    .stat-card-petugas .stat-icon.blue   { background: rgba(0,40,85,0.10);      color: var(--primary); }
    .stat-card-petugas .stat-icon.green  { background: rgba(39,174,96,0.12);    color: var(--success); }
    .stat-card-petugas .stat-icon.orange { background: rgba(255,184,0,0.15);    color: #d69e00; }
    .stat-card-petugas .stat-info { flex: 1; }
    .stat-card-petugas .stat-value { font-size: 26px; font-weight: 800; line-height: 1.1; color: var(--primary-dark); }
    .stat-card-petugas .stat-label { font-size: 12px; color: var(--text-muted); font-weight: 600; margin-top: 3px; }

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
    .filter-left { display: flex; align-items: center; gap: 10px; flex: 1; flex-wrap: wrap; }
    .search-input-wrap { position: relative; flex: 1; min-width: 240px; }
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
    .badge-desil {
        display: inline-flex; align-items: center;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 11px; font-weight: 700;
        white-space: nowrap;
    }
    .badge-desil.b1 { background: rgba(0,120,255,0.10); color: #0078ff; }
    .badge-desil.b2 { background: rgba(39,174,96,0.12); color: var(--success); }
    .badge-desil.other { background: rgba(0,40,85,0.08); color: var(--text-muted); }
    .badge-gender {
        display: inline-flex; align-items: center; justify-content: center;
        width: 28px; height: 28px; border-radius: 8px;
        font-size: 12px; font-weight: 800;
    }
    .badge-gender.l { background: rgba(0,40,85,0.10); color: var(--primary); }
    .badge-gender.p { background: rgba(255,99,132,0.12); color: #e74c3c; }

    /* Pagination */
    .pagination-custom-bar {
        padding: 14px 20px;
        border-top: 1px solid rgba(0,40,85,0.06);
        display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px;
    }
    .pagination-info-text { font-size: 12.5px; color: var(--text-muted); font-weight: 600; }
    .pagination-links { display: flex; gap: 4px; flex-wrap: wrap; }
    .page-btn {
        display: inline-flex; align-items: center; justify-content: center;
        min-width: 34px; height: 34px; padding: 0 10px;
        border-radius: 8px; font-size: 13px; font-weight: 700;
        border: 1px solid rgba(0,40,85,0.12);
        background: var(--bg-body); color: var(--text-primary);
        text-decoration: none; transition: all 0.2s ease; cursor: pointer;
    }
    .page-btn:hover   { background: var(--primary); color: #fff; border-color: var(--primary); }
    .page-btn.active  { background: var(--primary); color: #fff; border-color: var(--primary); }
    .page-btn.disabled { opacity: 0.4; pointer-events: none; }

    @media (max-width: 768px) {
        .stats-grid-petugas { grid-template-columns: 1fr 1fr; }
        .welcome-card { flex-direction: column; align-items: flex-start; }
    }
    @media (max-width: 480px) {
        .stats-grid-petugas { grid-template-columns: 1fr; }
    }
</style>
@endpush

@section('content')
    @include('layouts.navbar')

    <main class="dashboard-content">
        {{-- Hero Banner Petugas --}}
        <div class="welcome-card">
            <div class="welcome-text">
                <h2>
                    <i class="fas fa-user-shield" style="color:var(--secondary);margin-right:8px;"></i>
                    Halo, {{ Auth::user()->name }}
                </h2>
                <p>
                    <i class="fas fa-map-marker-alt" style="opacity:0.7;margin-right:4px;"></i>
                    Wilayah Tugas: <strong>Desa {{ $stats['desa'] ?? '-' }}, Kec. {{ $stats['kecamatan'] ?? '-' }}</strong>
                    &bull; Data Verval BSPS Wilayah Anda
                </p>
            </div>
        </div>

        {{-- Alert Sukses --}}
        @if(session('success'))
            <div style="background:rgba(39,174,96,0.10);border:1px solid rgba(39,174,96,0.30);border-radius:var(--radius-sm);padding:14px 18px;margin-bottom:20px;font-size:13px;color:var(--success);display:flex;align-items:center;gap:10px;">
                <i class="fas fa-check-circle" style="font-size:16px;"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        {{-- Stats Cards --}}
        <div class="stats-grid-petugas">
            <div class="stat-card-petugas">
                <div class="stat-icon blue"><i class="fas fa-users"></i></div>
                <div class="stat-info">
                    <div class="stat-value">{{ number_format($stats['total_data'], 0, ',', '.') }}</div>
                    <div class="stat-label">Total Calon Penerima di Desa Anda</div>
                </div>
            </div>
            <div class="stat-card-petugas">
                <div class="stat-icon orange"><i class="fas fa-layer-group"></i></div>
                <div class="stat-info">
                    <div class="stat-value" style="color:#d69e00;">{{ number_format($stats['backlog1'], 0, ',', '.') }}</div>
                    <div class="stat-label">Backlog 1 Desil 1-4</div>
                </div>
            </div>
            <div class="stat-card-petugas">
                <div class="stat-icon green"><i class="fas fa-layer-group"></i></div>
                <div class="stat-info">
                    <div class="stat-value" style="color:var(--success);">{{ number_format($stats['backlog2'], 0, ',', '.') }}</div>
                    <div class="stat-label">Backlog 2 Desil 1-4</div>
                </div>
            </div>
        </div>

        {{-- Filter & Search --}}
        <form action="{{ route('petugas.dashboard') }}" method="GET" class="filter-section" id="filterFormPetugas">
            <div class="filter-left">
                <div class="search-input-wrap">
                    <i class="fas fa-search"></i>
                    <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama, NIK, No. KK, atau alamat..." />
                </div>

                {{-- Hidden input desil --}}
                <input type="hidden" name="desil" id="hiddenDesilPetugas" value="{{ $desilFilter }}" />

                {{-- Custom Dropdown: Pengelompokan Desil --}}
                <div class="pupr-dropdown-wrapper" id="ddDesilPetugasWrapper">
                    <button type="button" class="pupr-dropdown-toggle"
                            onclick="window.PuprDropdown.toggle(document.getElementById('ddDesilPetugasWrapper'))">
                        <i class="fas fa-layer-group" style="font-size:12px;opacity:0.6;"></i>
                        <span class="selected-label">
                            @if($desilFilter === 'Backlog 1') Backlog 1 Desil 1-4
                            @elseif($desilFilter === 'Backlog 2') Backlog 2 Desil 1-4
                            @else Semua Pengelompokan
                            @endif
                        </span>
                        <i class="fas fa-chevron-down" style="font-size:10px;opacity:0.5;"></i>
                    </button>
                    <div class="pupr-dropdown-menu" style="min-width:210px;">
                        <div class="pupr-dropdown-item {{ $desilFilter === 'all' ? 'active' : '' }}"
                             onclick="selectDropdown('hiddenDesilPetugas', 'ddDesilPetugasWrapper', 'all', 'Semua Pengelompokan', 'filterFormPetugas')">
                            <i class="fas fa-th-list" style="font-size:12px;opacity:0.5;"></i> Semua Pengelompokan
                        </div>
                        <div class="dropdown-divider"></div>
                        <div class="pupr-dropdown-item {{ $desilFilter === 'Backlog 1' ? 'active' : '' }}"
                             onclick="selectDropdown('hiddenDesilPetugas', 'ddDesilPetugasWrapper', 'Backlog 1', 'Backlog 1 Desil 1-4', 'filterFormPetugas')">
                            <i class="fas fa-circle" style="font-size:8px;color:#0078ff;"></i> Backlog 1 Desil 1-4
                        </div>
                        <div class="pupr-dropdown-item {{ $desilFilter === 'Backlog 2' ? 'active' : '' }}"
                             onclick="selectDropdown('hiddenDesilPetugas', 'ddDesilPetugasWrapper', 'Backlog 2', 'Backlog 2 Desil 1-4', 'filterFormPetugas')">
                            <i class="fas fa-circle" style="font-size:8px;color:var(--success);"></i> Backlog 2 Desil 1-4
                        </div>
                    </div>
                </div>
            </div>
            <div style="display:flex;gap:8px;align-items:center;">
                <a href="{{ route('petugas.dashboard') }}" class="btn btn-outline" style="padding:10px 16px;font-size:13px;text-decoration:none;border-radius:var(--radius-sm);border:1px solid rgba(0,40,85,0.15);color:var(--text-secondary);display:inline-flex;align-items:center;gap:6px;">
                    <i class="fas fa-redo"></i> Reset
                </a>
                <button type="submit" class="btn btn-primary" style="padding:10px 18px;font-size:13px;font-weight:700;background:var(--primary);color:#fff;border:none;border-radius:var(--radius-sm);cursor:pointer;display:inline-flex;align-items:center;gap:6px;">
                    <i class="fas fa-search"></i> Cari
                </button>
            </div>
        </form>

        {{-- Tabel Data Verval --}}
        <div class="table-container-card">
            <div class="table-header-bar">
                <h3><i class="fas fa-clipboard-list"></i> Data Calon Penerima BSPS — Desa {{ $stats['desa'] ?? '-' }}</h3>
                <span style="font-size:12.5px;color:var(--text-muted);font-weight:600;">
                    Menampilkan {{ $vervals->firstItem() ?? 0 }} - {{ $vervals->lastItem() ?? 0 }} dari {{ number_format($vervals->total(), 0, ',', '.') }} data
                </span>
            </div>

            <div style="overflow-x:auto;">
                <table class="table" style="width:100%;border-collapse:collapse;">
                    <thead>
                        <tr style="background:var(--bg-body);border-bottom:1px solid rgba(0,40,85,0.08);text-align:left;font-size:12.5px;color:var(--text-muted);">
                            <th style="padding:14px 18px;">No</th>
                            <th style="padding:14px 12px;">Nama Calon Penerima</th>
                            <th style="padding:14px 12px;text-align:center;">L/P</th>
                            <th style="padding:14px 12px;">NIK & No. KK</th>
                            <th style="padding:14px 12px;">Alamat & Dusun</th>
                            <th style="padding:14px 12px;">Kelompok Desil</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($vervals as $item)
                        <tr style="border-bottom:1px solid rgba(0,40,85,0.05);font-size:13px;transition:background 0.15s;"
                            onmouseover="this.style.background='rgba(0,40,85,0.03)'"
                            onmouseout="this.style.background='transparent'">
                            <td style="padding:13px 18px;color:var(--text-muted);font-weight:600;">
                                {{ $vervals->firstItem() + $loop->index }}
                            </td>
                            <td style="padding:13px 12px;">
                                <span style="font-weight:700;color:var(--primary-dark);">{{ $item->nama_calon_penerima }}</span>
                            </td>
                            <td style="padding:13px 12px;text-align:center;">
                                @php $jk = strtoupper(trim($item->jenis_kelamin ?? '')); @endphp
                                @if(in_array($jk, ['L','LAKI-LAKI','LAKI']))
                                    <span class="badge-gender l">L</span>
                                @elseif(in_array($jk, ['P','PEREMPUAN']))
                                    <span class="badge-gender p">P</span>
                                @else
                                    <span style="color:var(--text-muted);font-size:12px;">-</span>
                                @endif
                            </td>
                            <td style="padding:13px 12px;">
                                <div style="font-family:monospace;font-size:12px;color:var(--text-primary);font-weight:600;letter-spacing:0.5px;">
                                    {{ $item->nik ?? '-' }}
                                </div>
                                <div style="font-family:monospace;font-size:11px;color:var(--text-muted);margin-top:2px;letter-spacing:0.3px;">
                                    KK: {{ $item->no_kk ?? '-' }}
                                </div>
                            </td>
                            <td style="padding:13px 12px;">
                                <div style="font-size:13px;color:var(--text-primary);">{{ $item->alamat ?? '-' }}</div>
                                @if($item->dusun)
                                    <div style="font-size:11.5px;color:var(--text-muted);margin-top:2px;">Dusun {{ $item->dusun }}</div>
                                @endif
                            </td>
                            <td style="padding:13px 12px;">
                                @php
                                    $desil = $item->pengelompokan_desil ?? '';
                                    $cls = str_contains($desil, 'Backlog 1') ? 'b1' : (str_contains($desil, 'Backlog 2') ? 'b2' : 'other');
                                @endphp
                                <span class="badge-desil {{ $cls }}">
                                    <i class="fas fa-circle" style="font-size:6px;margin-right:5px;"></i>
                                    {{ $desil ?: '-' }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" style="padding:48px;text-align:center;color:var(--text-muted);">
                                <i class="fas fa-inbox" style="font-size:32px;opacity:0.3;display:block;margin-bottom:10px;"></i>
                                <span style="font-size:14px;font-weight:600;">Tidak ada data verval di desa Anda{{ $search ? ' untuk pencarian ini' : '' }}.</span>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($vervals->lastPage() > 1)
            <div class="pagination-custom-bar">
                <div class="pagination-info-text">
                    Halaman <strong>{{ $vervals->currentPage() }}</strong> dari <strong>{{ $vervals->lastPage() }}</strong>
                </div>
                <div class="pagination-links">
                    @if($vervals->onFirstPage())
                        <span class="page-btn disabled"><i class="fas fa-chevron-left"></i></span>
                    @else
                        <a href="{{ $vervals->previousPageUrl() }}" class="page-btn"><i class="fas fa-chevron-left"></i></a>
                    @endif

                    @foreach($vervals->getUrlRange(max(1, $vervals->currentPage()-2), min($vervals->lastPage(), $vervals->currentPage()+2)) as $page => $url)
                        <a href="{{ $url }}" class="page-btn {{ $page == $vervals->currentPage() ? 'active' : '' }}">{{ $page }}</a>
                    @endforeach

                    @if($vervals->hasMorePages())
                        <a href="{{ $vervals->nextPageUrl() }}" class="page-btn"><i class="fas fa-chevron-right"></i></a>
                    @else
                        <span class="page-btn disabled"><i class="fas fa-chevron-right"></i></span>
                    @endif
                </div>
            </div>
            @endif
        </div>

    </main>
@endsection
