@extends('layouts.partial.app')

@section('title', 'BSPS Verval - Petugas Survei & Fasilitator')
@section('title_header', 'Petugas & Fasilitator BSPS')
@section('subtitle_header', 'Kelola data Tenaga Fasilitator Lapangan (TFL), koordinator, dan petugas desa sistem BSPS Verval')

@push('styles')
<style>
    /* Breadcrumb */
    .breadcrumb {
        font-size: 13px;
        color: var(--text-muted);
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .breadcrumb a { color: var(--primary); text-decoration: none; font-weight: 600; }
    .breadcrumb a:hover { color: var(--secondary); }

    /* Stats Grid Petugas */
    .stats-petugas {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
        margin-bottom: 24px;
    }
    .stats-petugas .stat-item {
        background: var(--bg-card);
        border-radius: var(--radius);
        padding: 18px 20px;
        box-shadow: var(--shadow-sm);
        border: 1px solid rgba(0, 40, 85, 0.06);
        display: flex;
        align-items: center;
        gap: 14px;
        transition: var(--transition);
    }
    .stats-petugas .stat-item:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
    }
    .stats-petugas .stat-item .icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        flex-shrink: 0;
    }
    .stats-petugas .stat-item .icon.blue   { background: rgba(0, 40, 85, 0.10); color: var(--primary); }
    .stats-petugas .stat-item .icon.green  { background: rgba(39, 174, 96, 0.12); color: var(--success); }
    .stats-petugas .stat-item .icon.orange { background: rgba(255, 184, 0, 0.16); color: #d69e00; }
    .stats-petugas .stat-item .icon.red    { background: rgba(231, 76, 60, 0.12); color: var(--danger); }

    .stats-petugas .stat-item .info .value {
        font-size: 24px;
        font-weight: 800;
        line-height: 1.1;
        color: var(--primary-dark);
    }
    .stats-petugas .stat-item .info .label {
        font-size: 12px;
        color: var(--text-muted);
        font-weight: 600;
        margin-top: 3px;
    }

    /* Filter Bar */
    .filter-bar-card {
        background: var(--bg-card);
        border-radius: var(--radius);
        padding: 16px 20px;
        box-shadow: var(--shadow-sm);
        border: 1px solid rgba(0, 40, 85, 0.06);
        margin-bottom: 24px;
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
    }

    .filter-left-group {
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
        flex: 1;
    }

    .search-input-group {
        position: relative;
        min-width: 260px;
        flex: 1;
        max-width: 380px;
    }

    .search-input-group input {
        width: 100%;
        padding: 9px 40px 9px 14px;
        border-radius: var(--radius-sm);
        border: 1px solid rgba(0, 40, 85, 0.14);
        background: var(--bg-body);
        color: var(--text-primary);
        font-size: 13px;
        font-family: inherit;
        outline: none;
        transition: var(--transition);
        box-sizing: border-box;
    }

    .search-input-group input:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(0, 40, 85, 0.1);
        background: var(--bg-card);
    }

    .search-input-group button {
        position: absolute;
        right: 4px;
        top: 50%;
        transform: translateY(-50%);
        background: var(--primary);
        color: #fff;
        border: none;
        width: 32px;
        height: 30px;
        border-radius: 6px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
    }

    .custom-select-box {
        padding: 9px 14px;
        border-radius: var(--radius-sm);
        border: 1px solid rgba(0, 40, 85, 0.14);
        background: var(--bg-body);
        color: var(--text-primary);
        font-size: 13px;
        font-family: inherit;
        outline: none;
        cursor: pointer;
        min-width: 160px;
    }

    .filter-right-group {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    /* Table Component */
    .table-container-card {
        background: var(--bg-card);
        border-radius: var(--radius);
        box-shadow: var(--shadow-sm);
        border: 1px solid rgba(0, 40, 85, 0.06);
        overflow: hidden;
        width: 100%;
        box-sizing: border-box;
    }

    .table-container-card .header {
        padding: 18px 24px;
        border-bottom: 1px solid rgba(0, 40, 85, 0.06);
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .table-container-card .header h3 {
        font-size: 15px;
        font-weight: 800;
        color: var(--primary);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .table-scroll-wrap {
        width: 100%;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    .table-custom {
        width: 100%;
        border-collapse: collapse;
        text-align: left;
    }

    .table-custom th {
        background: var(--bg-body);
        color: var(--text-muted);
        font-size: 12.5px;
        font-weight: 700;
        padding: 14px 18px;
        border-bottom: 1px solid rgba(0, 40, 85, 0.08);
        white-space: nowrap;
    }

    .table-custom td {
        padding: 14px 18px;
        border-bottom: 1px solid rgba(0, 40, 85, 0.05);
        font-size: 13px;
        color: var(--text-primary);
        vertical-align: middle;
    }

    .table-custom tr:hover {
        background: rgba(0, 40, 85, 0.015);
    }

    .petugas-avatar {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        background: var(--primary);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 14px;
        flex-shrink: 0;
    }

    /* Custom Pagination */
    .pagination-bar {
        padding: 16px 24px;
        background: var(--bg-card);
        border-top: 1px solid rgba(0, 40, 85, 0.06);
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 14px;
        font-size: 13px;
    }

    .pagination-nav {
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .pagination-btn {
        min-width: 32px;
        height: 32px;
        padding: 0 8px;
        border-radius: 6px;
        border: 1px solid rgba(0, 40, 85, 0.12);
        background: var(--bg-body);
        color: var(--text-primary);
        font-size: 12.5px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        transition: var(--transition);
        cursor: pointer;
    }

    .pagination-btn:hover:not(.disabled):not(.active) {
        background: rgba(0, 40, 85, 0.08);
        border-color: var(--primary);
        color: var(--primary);
    }

    .pagination-btn.active {
        background: var(--primary);
        color: #ffffff;
        border-color: var(--primary);
    }

    .pagination-btn.disabled {
        opacity: 0.4;
        cursor: not-allowed;
        pointer-events: none;
    }

    @media (max-width: 1100px) {
        .stats-petugas { grid-template-columns: repeat(2, 1fr); }
    }
    @media (max-width: 600px) {
        .stats-petugas { grid-template-columns: 1fr; }
        .filter-bar-card { flex-direction: column; align-items: stretch; }
        .filter-left-group { flex-direction: column; align-items: stretch; }
        .search-input-group { max-width: 100%; }
        .custom-select-box { width: 100%; }
        .filter-right-group { width: 100%; justify-content: space-between; }
        .pagination-bar { flex-direction: column; align-items: center; text-align: center; }
    }
</style>
@endpush

@section('content')
    @include('layouts.navbar')

    <main class="dashboard-content">
        <!-- Breadcrumb -->
        <div class="breadcrumb">
            <a href="{{ url('/dashboard') }}"><i class="fas fa-home"></i> Dashboard</a>
            <i class="fas fa-chevron-right" style="font-size:10px;"></i>
            <span>Petugas Survei</span>
        </div>

        @if(session('success'))
            <div style="padding:14px 18px;border-radius:var(--radius-sm);background:rgba(39,174,96,0.12);border:1px solid rgba(39,174,96,0.3);color:var(--success);font-weight:600;margin-bottom:20px;display:flex;align-items:center;gap:10px;">
                <i class="fas fa-check-circle" style="font-size:18px;"></i>
                <div>{{ session('success') }}</div>
            </div>
        @endif

        <!-- 4 Stats Petugas Counter Cards -->
        <div class="stats-petugas">
            <div class="stat-item">
                <div class="icon blue"><i class="fas fa-users-gear"></i></div>
                <div class="info">
                    <div class="value">{{ number_format($totalCount, 0, ',', '.') }}</div>
                    <div class="label">Total Pengguna Terdaftar</div>
                </div>
            </div>
            <div class="stat-item">
                <div class="icon green"><i class="fas fa-user-check"></i></div>
                <div class="info">
                    <div class="value">{{ number_format($aktifCount, 0, ',', '.') }}</div>
                    <div class="label">Status Akun Aktif</div>
                </div>
            </div>
            <div class="stat-item">
                <div class="icon orange"><i class="fas fa-house-user"></i></div>
                <div class="info">
                    <div class="value">{{ number_format(\App\Models\User::where('role', 'petugas')->count(), 0, ',', '.') }}</div>
                    <div class="label">Petugas Verval Desa</div>
                </div>
            </div>
            <div class="stat-item">
                <div class="icon red"><i class="fas fa-shield-halved"></i></div>
                <div class="info">
                    <div class="value">{{ number_format(\App\Models\User::where('role', 'admin')->count(), 0, ',', '.') }}</div>
                    <div class="label">Administrator Sistem</div>
                </div>
            </div>
        </div>

        <!-- Filter & Search Bar Form (Custom PuprDropdown) -->
        <form action="{{ url('/user') }}" method="GET" class="filter-section" id="filterFormUser">
            <div class="filter-left" style="display:flex;align-items:center;gap:12px;flex:1;flex-wrap:wrap;">
                <div class="search-input-wrap" style="position:relative;min-width:260px;flex:1;max-width:380px;">
                    <i class="fas fa-search" style="position:absolute;left:14px;top:50%;transform:translateY(-50%);color:var(--text-muted);font-size:14px;"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, email, desa, kecamatan..." style="width:100%;padding:10px 14px 10px 38px;border-radius:var(--radius-sm);border:1px solid rgba(0,40,85,0.14);background:var(--bg-body);color:var(--text-primary);font-size:13.5px;outline:none;box-sizing:border-box;" />
                </div>

                {{-- Hidden inputs --}}
                <input type="hidden" name="status" id="hiddenUserStatus" value="{{ request('status', 'all') }}" />
                <input type="hidden" name="kecamatan" id="hiddenUserKec" value="{{ request('kecamatan', 'all') }}" />

                {{-- Custom Dropdown: Status --}}
                <div class="pupr-dropdown-wrapper" id="ddStatusWrapper">
                    <button type="button" class="pupr-dropdown-toggle" onclick="window.PuprDropdown.toggle(document.getElementById('ddStatusWrapper'))">
                        <i class="fas fa-circle-check" style="font-size:12px;opacity:0.6;"></i>
                        <span class="selected-label">
                            @if(request('status') === 'aktif') Aktif
                            @elseif(request('status') === 'bertugas') Bertugas
                            @elseif(request('status') === 'cuti') Cuti
                            @else Semua Status
                            @endif
                        </span>
                        <i class="fas fa-chevron-down" style="font-size:10px;opacity:0.5;"></i>
                    </button>
                    <div class="pupr-dropdown-menu" style="min-width:180px;">
                        <div class="pupr-dropdown-item {{ request('status', 'all') === 'all' ? 'active' : '' }}"
                             onclick="selectDropdown('hiddenUserStatus', 'ddStatusWrapper', 'all', 'Semua Status', 'filterFormUser')">
                            <i class="fas fa-th-list" style="font-size:12px;opacity:0.5;"></i> Semua Status
                        </div>
                        <div class="dropdown-divider"></div>
                        <div class="pupr-dropdown-item {{ request('status') === 'aktif' ? 'active' : '' }}"
                             onclick="selectDropdown('hiddenUserStatus', 'ddStatusWrapper', 'aktif', 'Aktif', 'filterFormUser')">
                            <i class="fas fa-circle" style="font-size:8px;color:var(--success);"></i> Aktif
                        </div>
                        <div class="pupr-dropdown-item {{ request('status') === 'bertugas' ? 'active' : '' }}"
                             onclick="selectDropdown('hiddenUserStatus', 'ddStatusWrapper', 'bertugas', 'Bertugas', 'filterFormUser')">
                            <i class="fas fa-circle" style="font-size:8px;color:var(--info);"></i> Bertugas
                        </div>
                        <div class="pupr-dropdown-item {{ request('status') === 'cuti' ? 'active' : '' }}"
                             onclick="selectDropdown('hiddenUserStatus', 'ddStatusWrapper', 'cuti', 'Cuti', 'filterFormUser')">
                            <i class="fas fa-circle" style="font-size:8px;color:var(--warning);"></i> Cuti
                        </div>
                    </div>
                </div>

                {{-- Custom Dropdown: Kecamatan --}}
                <div class="pupr-dropdown-wrapper" id="ddUserKecWrapper">
                    <button type="button" class="pupr-dropdown-toggle" onclick="window.PuprDropdown.toggle(document.getElementById('ddUserKecWrapper'))">
                        <i class="fas fa-map-marker-alt" style="font-size:12px;opacity:0.6;"></i>
                        <span class="selected-label">
                            {{ request('kecamatan') && request('kecamatan') !== 'all' ? 'Kec. '.ucwords(strtolower(request('kecamatan'))) : 'Semua Kecamatan' }}
                        </span>
                        <i class="fas fa-chevron-down" style="font-size:10px;opacity:0.5;"></i>
                    </button>
                    <div class="pupr-dropdown-menu" style="min-width:200px;max-height:300px;overflow-y:auto;">
                        <div class="pupr-dropdown-item {{ request('kecamatan', 'all') === 'all' ? 'active' : '' }}"
                             onclick="selectDropdown('hiddenUserKec', 'ddUserKecWrapper', 'all', 'Semua Kecamatan', 'filterFormUser')">
                            <i class="fas fa-th-list" style="font-size:12px;opacity:0.5;"></i> Semua Kecamatan
                        </div>
                        <div class="dropdown-divider"></div>
                        @if(isset($kecamatanList))
                            @foreach($kecamatanList as $kec)
                            <div class="pupr-dropdown-item {{ request('kecamatan') === $kec ? 'active' : '' }}"
                                 onclick="selectDropdown('hiddenUserKec', 'ddUserKecWrapper', '{{ $kec }}', 'Kec. {{ ucwords(strtolower($kec)) }}', 'filterFormUser')">
                                <i class="fas fa-map-pin" style="font-size:11px;opacity:0.4;"></i> Kec. {{ ucwords(strtolower($kec)) }}
                            </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>

            <div style="display:flex;gap:10px;align-items:center;">
                <a href="{{ route('user.export-admin-kecamatan') }}" class="btn btn-primary" style="padding:10px 16px;background:var(--primary);color:#fff;text-decoration:none;border-radius:var(--radius-sm);font-weight:700;font-size:13px;display:inline-flex;align-items:center;gap:6px;" title="Download File Excel Kredensial Akun Admin Kecamatan">
                    <i class="fas fa-file-excel"></i> Export Excel Admin Kec.
                </a>
                <a href="{{ url('/user') }}" class="btn btn-outline" style="padding:10px 16px;border:1px solid rgba(0,40,85,0.15);text-decoration:none;color:var(--text-secondary);border-radius:var(--radius-sm);font-weight:600;font-size:13px;display:inline-flex;align-items:center;gap:6px;">
                    <i class="fas fa-redo"></i> Reset
                </a>
                <button type="button" class="btn btn-success" id="tambahPetugasBtn" style="padding:10px 18px;background:var(--success);color:#fff;border:none;border-radius:var(--radius-sm);font-weight:700;font-size:13px;cursor:pointer;display:inline-flex;align-items:center;gap:6px;">
                    <i class="fas fa-user-plus"></i> Tambah Pengguna
                </button>
            </div>
        </form>

        <!-- Table Card -->
        <div class="table-container-card">
            <div class="header">
                <h3><i class="fas fa-users-viewfinder"></i> Daftar Petugas Verval Desa &amp; Pengguna Sistem</h3>
                <span style="font-size:12.5px;color:var(--text-muted);font-weight:600;">Total: {{ number_format($users->total(), 0, ',', '.') }} Akun</span>
            </div>

            <div class="table-scroll-wrap">
                <table class="table-custom">
                    <thead>
                        <tr>
                            <th style="width:50px;text-align:center;">No</th>
                            <th style="min-width:260px;">Nama &amp; Email Akun</th>
                            <th style="min-width:200px;">Wilayah Penugasan</th>
                            <th style="min-width:200px;">Jabatan</th>
                            <th style="min-width:110px;text-align:center;">Role</th>
                            <th style="min-width:100px;text-align:center;">Status</th>
                            <th style="min-width:140px;text-align:center;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $index => $item)
                            <tr>
                                <td style="text-align:center;font-weight:700;color:var(--text-muted);">
                                    {{ $users->firstItem() + $index }}
                                </td>
                                <td>
                                    <div style="display:flex;align-items:center;gap:12px;">
                                        <div class="petugas-avatar" style="{{ $item->role === 'admin' ? 'background:var(--primary-dark);' : 'background:var(--primary);' }}">
                                            {{ strtoupper(substr($item->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div style="font-weight:800;color:var(--primary-dark);">{{ $item->name }}</div>
                                            <div style="font-size:12px;color:var(--text-muted);font-family:monospace;margin-top:2px;">{{ $item->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if($item->desa)
                                        <div style="font-weight:700;color:var(--primary-dark);display:flex;align-items:center;gap:5px;">
                                            <i class="fas fa-location-dot" style="color:var(--primary);font-size:11px;"></i>
                                            Desa {{ $item->desa }}
                                        </div>
                                        <div style="font-size:11.5px;color:var(--text-muted);margin-top:2px;">
                                            Kec. {{ $item->kecamatan }}
                                        </div>
                                    @else
                                        <span style="font-weight:600;color:var(--text-primary);"><i class="fas fa-building" style="color:var(--text-muted);font-size:11px;margin-right:4px;"></i> {{ $item->kecamatan }}</span>
                                    @endif
                                </td>
                                <td>
                                    <span style="font-weight:600;color:var(--text-secondary);">{{ $item->jabatan }}</span>
                                </td>
                                <td style="text-align:center;">
                                    @if($item->role === 'admin')
                                        <span style="display:inline-flex;align-items:center;gap:4px;padding:3px 8px;border-radius:6px;font-size:11px;font-weight:800;background:rgba(0,40,85,0.12);color:var(--primary);">
                                            <i class="fas fa-shield-halved"></i> Admin
                                        </span>
                                    @else
                                        <span style="display:inline-flex;align-items:center;gap:4px;padding:3px 8px;border-radius:6px;font-size:11px;font-weight:800;background:rgba(255,184,0,0.15);color:#d69e00;">
                                            <i class="fas fa-user"></i> Petugas
                                        </span>
                                    @endif
                                </td>
                                <td style="text-align:center;">
                                    <span style="display:inline-block;padding:3px 8px;border-radius:6px;font-size:11px;font-weight:800;text-transform:capitalize;background:rgba(39,174,96,0.12);color:var(--success);">
                                        {{ $item->status ?: 'Aktif' }}
                                    </span>
                                </td>
                                <td style="text-align:center;">
                                    <div style="display:inline-flex;align-items:center;gap:6px;">
                                        <button type="button" class="btn-icon edit" onclick="editUserModal({{ json_encode($item) }})" style="padding:5px 10px;border-radius:6px;border:1px solid rgba(0,40,85,0.15);background:transparent;color:var(--primary);cursor:pointer;font-size:12px;font-weight:600;">
                                            <i class="fas fa-pen"></i> Edit
                                        </button>
                                        <button type="button" class="btn-icon delete" onclick="konfirmasiHapus({{ $item->id }}, '{{ addslashes($item->name) }}', '{{ addslashes($item->jabatan) }}')" style="padding:5px 10px;border-radius:6px;border:1px solid rgba(231,76,60,0.25);background:transparent;color:var(--danger);cursor:pointer;font-size:12px;font-weight:600;">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        <form id="formHapusUser_{{ $item->id }}" action="{{ url('/user/' . $item->id) }}" method="POST" style="display:none;">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" style="text-align:center;padding:32px;color:var(--text-muted);">
                                    <i class="fas fa-folder-open" style="font-size:28px;margin-bottom:8px;display:block;opacity:0.5;"></i>
                                    Tidak ada data petugas yang cocok dengan pencarian.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Custom Clean Pagination Bar -->
            <div class="pagination-bar">
                <div style="color:var(--text-muted);">
                    Menampilkan <strong>{{ $users->firstItem() ?? 0 }} - {{ $users->lastItem() ?? 0 }}</strong> dari total <strong>{{ number_format($users->total(), 0, ',', '.') }}</strong> akun
                </div>

                @if($users->hasPages())
                    <div class="pagination-nav">
                        <!-- First & Prev -->
                        <a href="{{ $users->url(1) }}" class="pagination-btn {{ $users->onFirstPage() ? 'disabled' : '' }}" title="Halaman Pertama">
                            <i class="fas fa-angles-left"></i>
                        </a>
                        <a href="{{ $users->previousPageUrl() }}" class="pagination-btn {{ $users->onFirstPage() ? 'disabled' : '' }}" title="Sebelumnya">
                            <i class="fas fa-chevron-left"></i>
                        </a>

                        <!-- Window Numbers -->
                        @php
                            $start = max(1, $users->currentPage() - 2);
                            $end = min($users->lastPage(), $users->currentPage() + 2);
                        @endphp

                        @for($i = $start; $i <= $end; $i++)
                            <a href="{{ $users->url($i) }}" class="pagination-btn {{ $i == $users->currentPage() ? 'active' : '' }}">
                                {{ $i }}
                            </a>
                        @endfor

                        <!-- Next & Last -->
                        <a href="{{ $users->nextPageUrl() }}" class="pagination-btn {{ !$users->hasMorePages() ? 'disabled' : '' }}" title="Berikutnya">
                            <i class="fas fa-chevron-right"></i>
                        </a>
                        <a href="{{ $users->url($users->lastPage()) }}" class="pagination-btn {{ !$users->hasMorePages() ? 'disabled' : '' }}" title="Halaman Terakhir">
                            <i class="fas fa-angles-right"></i>
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </main>

    <!-- Modal Konfirmasi Hapus Petugas -->
    <div class="modal-overlay" id="modalKonfirmasiHapus">
        <div class="modal-box" style="max-width:440px;">
            <div class="modal-header" style="border-bottom-color:rgba(231,76,60,0.15);">
                <h3 style="color:var(--danger);"><i class="fas fa-triangle-exclamation" style="margin-right:10px;"></i>Konfirmasi Hapus</h3>
                <button class="close-btn" type="button" onclick="window.PuprModal.close('modalKonfirmasiHapus')"><i class="fas fa-times"></i></button>
            </div>
            <div class="modal-body" style="text-align:center;padding:28px 24px 20px;">
                <div style="width:68px;height:68px;border-radius:50%;background:rgba(231,76,60,0.10);display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
                    <i class="fas fa-user-xmark" style="font-size:28px;color:var(--danger);"></i>
                </div>
                <p style="font-size:15px;font-weight:700;color:var(--text-primary);margin-bottom:6px;">Yakin ingin menghapus akun ini?</p>
                <p style="font-size:13px;color:var(--text-muted);margin-bottom:4px;">Nama: <strong id="hapusNamaPetugas" style="color:var(--primary);"></strong></p>
                <p style="font-size:13px;color:var(--text-muted);margin-bottom:16px;">Jabatan: <strong id="hapusJabatanPetugas"></strong></p>
                <div style="background:rgba(231,76,60,0.06);border:1px solid rgba(231,76,60,0.15);border-radius:8px;padding:8px 12px;">
                    <p style="font-size:11.5px;color:var(--danger);margin:0;"><i class="fas fa-circle-info" style="margin-right:4px;"></i>Data yang dihapus <strong>tidak dapat dikembalikan</strong>.</p>
                </div>
            </div>
            <div class="modal-footer" style="justify-content:center;gap:12px;padding:14px 20px;">
                <button type="button" class="btn btn-cancel" style="min-width:110px;" onclick="window.PuprModal.close('modalKonfirmasiHapus')">
                    <i class="fas fa-xmark"></i> Batal
                </button>
                <button type="button" class="btn btn-submit" id="btnKonfirmasiHapus" style="min-width:130px;background:var(--danger) !important;border-color:var(--danger) !important;color:#fff;">
                    <i class="fas fa-trash"></i> Ya, Hapus!
                </button>
            </div>
        </div>
    </div>

    <!-- Modal Form Tambah / Edit Petugas -->
    <div class="modal-overlay" id="modalPetugas">
        <div class="modal-box" style="max-width:560px;">
            <div class="modal-header">
                <h3 id="modalPetugasTitle"><i class="fas fa-user-plus" style="color:var(--primary);margin-right:10px;"></i>Tambah Petugas Baru</h3>
                <button class="close-btn" id="closeModalBtn" type="button" onclick="window.PuprModal.close('modalPetugas')"><i class="fas fa-times"></i></button>
            </div>
            <form id="formPetugas" action="{{ url('/user') }}" method="POST">
                @csrf
                <div id="methodSpoofing"></div>
                <div class="modal-body" style="padding:20px 24px;">
                    <div class="form-group" style="margin-bottom:14px;">
                        <label class="form-label" style="display:block;font-size:12.5px;font-weight:700;margin-bottom:6px;">Nama Lengkap <span style="color:var(--danger);">*</span></label>
                        <input type="text" class="form-control" name="name" id="inputName" required placeholder="Contoh: Petugas Verval Desa Sukamakmur" style="width:100%;padding:9px 12px;border-radius:6px;border:1px solid rgba(0,40,85,0.14);box-sizing:border-box;" />
                    </div>

                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:14px;">
                        <div class="form-group">
                            <label class="form-label" style="display:block;font-size:12.5px;font-weight:700;margin-bottom:6px;">Email Login <span style="color:var(--danger);">*</span></label>
                            <input type="email" class="form-control" name="email" id="inputEmail" required placeholder="verval.desa@gmail.com" style="width:100%;padding:9px 12px;border-radius:6px;border:1px solid rgba(0,40,85,0.14);box-sizing:border-box;" />
                        </div>
                        <div class="form-group">
                            <label class="form-label" style="display:block;font-size:12.5px;font-weight:700;margin-bottom:6px;">Password <span id="passReqStar" style="color:var(--danger);">*</span></label>
                            <input type="password" class="form-control" name="password" id="inputPassword" placeholder="Min. 6 karakter" style="width:100%;padding:9px 12px;border-radius:6px;border:1px solid rgba(0,40,85,0.14);box-sizing:border-box;" />
                        </div>
                    </div>

                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:14px;">
                        <div class="form-group">
                            <label class="form-label" style="display:block;font-size:12.5px;font-weight:700;margin-bottom:6px;">Kecamatan <span style="color:var(--danger);">*</span></label>
                            <input type="text" class="form-control" name="kecamatan" id="inputKecamatan" required placeholder="Contoh: Ajung" style="width:100%;padding:9px 12px;border-radius:6px;border:1px solid rgba(0,40,85,0.14);box-sizing:border-box;" />
                        </div>
                        <div class="form-group">
                            <label class="form-label" style="display:block;font-size:12.5px;font-weight:700;margin-bottom:6px;">Desa / Kelurahan</label>
                            <input type="text" class="form-control" name="desa" id="inputDesa" placeholder="Contoh: Sukamakmur" style="width:100%;padding:9px 12px;border-radius:6px;border:1px solid rgba(0,40,85,0.14);box-sizing:border-box;" />
                        </div>
                    </div>

                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:14px;">
                        <div class="form-group">
                            <label class="form-label" style="display:block;font-size:12.5px;font-weight:700;margin-bottom:6px;">Jabatan <span style="color:var(--danger);">*</span></label>
                            <input type="text" class="form-control" name="jabatan" id="inputJabatan" required value="Petugas Verval Lapangan" style="width:100%;padding:9px 12px;border-radius:6px;border:1px solid rgba(0,40,85,0.14);box-sizing:border-box;" />
                        </div>
                        <div class="form-group">
                            <label class="form-label" style="display:block;font-size:12.5px;font-weight:700;margin-bottom:6px;">No. HP</label>
                            <input type="text" class="form-control" name="phone" id="inputPhone" placeholder="08xxxxxxxxxx" style="width:100%;padding:9px 12px;border-radius:6px;border:1px solid rgba(0,40,85,0.14);box-sizing:border-box;" />
                        </div>
                    </div>

                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                        <div class="form-group">
                            <label class="form-label" style="display:block;font-size:12.5px;font-weight:700;margin-bottom:6px;">Role <span style="color:var(--danger);">*</span></label>
                            <select class="form-control" name="role" id="inputRole" style="width:100%;padding:9px 12px;border-radius:6px;border:1px solid rgba(0,40,85,0.14);box-sizing:border-box;">
                                <option value="petugas">Petugas Lapangan</option>
                                <option value="admin_kecamatan">Admin Kecamatan</option>
                                <option value="admin">Administrator</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label" style="display:block;font-size:12.5px;font-weight:700;margin-bottom:6px;">Status Akun <span style="color:var(--danger);">*</span></label>
                            <select class="form-control" name="status" id="inputStatus" style="width:100%;padding:9px 12px;border-radius:6px;border:1px solid rgba(0,40,85,0.14);box-sizing:border-box;">
                                <option value="aktif">Aktif</option>
                                <option value="bertugas">Bertugas</option>
                                <option value="cuti">Cuti</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="padding:14px 24px;display:flex;justify-content:flex-end;gap:10px;background:var(--bg-body);border-top:1px solid rgba(0,40,85,0.06);">
                    <button type="button" class="btn btn-cancel" onclick="window.PuprModal.close('modalPetugas')">Batal</button>
                    <button type="submit" class="btn btn-submit" style="background:var(--primary);color:#fff;font-weight:700;padding:9px 20px;border-radius:6px;border:none;cursor:pointer;">Simpan Data</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
let currentDeleteUserId = null;

function konfirmasiHapus(userId, namaPetugas, jabatanPetugas) {
    currentDeleteUserId = userId;
    document.getElementById('hapusNamaPetugas').textContent = namaPetugas || '-';
    document.getElementById('hapusJabatanPetugas').textContent = jabatanPetugas || '-';
    window.PuprModal.open('modalKonfirmasiHapus');
}

document.addEventListener('DOMContentLoaded', function() {
    const btnKonfirm = document.getElementById('btnKonfirmasiHapus');
    if (btnKonfirm) {
        btnKonfirm.addEventListener('click', function() {
            if (currentDeleteUserId) {
                const targetForm = document.getElementById('formHapusUser_' + currentDeleteUserId);
                if (targetForm) {
                    targetForm.submit();
                }
            }
        });
    }

    const tambahBtn = document.getElementById('tambahPetugasBtn');
    if (tambahBtn) {
        tambahBtn.addEventListener('click', function() {
            document.getElementById('modalPetugasTitle').innerHTML = '<i class="fas fa-user-plus" style="color:var(--primary);margin-right:10px;"></i>Tambah Petugas Baru';
            document.getElementById('formPetugas').action = '{{ url("/user") }}';
            document.getElementById('methodSpoofing').innerHTML = '';
            document.getElementById('inputName').value = '';
            document.getElementById('inputEmail').value = '';
            document.getElementById('inputPassword').value = '';
            document.getElementById('inputPassword').required = true;
            document.getElementById('passReqStar').style.display = 'inline';
            document.getElementById('inputKecamatan').value = '';
            document.getElementById('inputDesa').value = '';
            document.getElementById('inputJabatan').value = 'Petugas Verval Lapangan';
            document.getElementById('inputPhone').value = '';
            document.getElementById('inputRole').value = 'petugas';
            document.getElementById('inputStatus').value = 'aktif';
            window.PuprModal.open('modalPetugas');
        });
    }
});

function editUserModal(user) {
    document.getElementById('modalPetugasTitle').innerHTML = '<i class="fas fa-user-pen" style="color:var(--primary);margin-right:10px;"></i>Edit Data Petugas';
    document.getElementById('formPetugas').action = '{{ url("/user") }}/' + user.id;
    document.getElementById('methodSpoofing').innerHTML = '@method("PUT")';
    document.getElementById('inputName').value = user.name || '';
    document.getElementById('inputEmail').value = user.email || '';
    document.getElementById('inputPassword').value = '';
    document.getElementById('inputPassword').required = false;
    document.getElementById('passReqStar').style.display = 'none';
    document.getElementById('inputKecamatan').value = user.kecamatan || '';
    document.getElementById('inputDesa').value = user.desa || '';
    document.getElementById('inputJabatan').value = user.jabatan || '';
    document.getElementById('inputPhone').value = user.phone || '';
    document.getElementById('inputRole').value = user.role || 'petugas';
    document.getElementById('inputStatus').value = user.status || 'aktif';
    window.PuprModal.open('modalPetugas');
}

/**
 * Helper: Custom PuprDropdown filter submit
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

    if (formId) {
        const form = document.getElementById(formId);
        if (form) form.submit();
    }
}
</script>
@endpush
