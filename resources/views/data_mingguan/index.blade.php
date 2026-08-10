@extends('layouts.partial.app')

@section('title', 'PUPR Jember - Data Mingguan')
@section('title_header', 'Data Mingguan')
@section('subtitle_header', 'Kelola data kegiatan per minggu Dinas PUPR Kabupaten Jember')

@push('styles')
<style>
    /* ============================================================
       PAGE STYLES: DATA MINGGUAN (PUPR THEME)
       ============================================================ */
    .breadcrumb {
        font-size: 13px;
        color: var(--text-muted);
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .breadcrumb a {
        color: var(--primary);
        text-decoration: none;
        font-weight: 500;
    }
    .breadcrumb a:hover {
        color: var(--secondary);
    }

    /* Filter Section */
    .filter-section {
        background: var(--bg-card);
        border-radius: var(--radius);
        padding: 18px 24px;
        box-shadow: var(--shadow-sm);
        border: 1px solid rgba(0, 40, 85, 0.06);
        margin-bottom: 24px;
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 16px;
    }
    .filter-section .filter-group {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }
    .filter-section .filter-group label {
        font-size: 13px;
        font-weight: 600;
        color: var(--text-secondary);
    }
    .filter-section .filter-group select,
    .filter-section .filter-group input {
        padding: 8px 14px;
        border-radius: var(--radius-sm);
        border: 1px solid rgba(0, 40, 85, 0.12);
        font-family: inherit;
        font-size: 13px;
        background: var(--bg-body);
        color: var(--text-primary);
        transition: var(--transition);
        outline: none;
    }
    .filter-section .filter-group select:focus,
    .filter-section .filter-group input:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 4px rgba(0, 40, 85, 0.08);
        background: #fff;
    }
    .filter-section .filter-actions {
        display: flex;
        gap: 10px;
        margin-left: auto;
    }
    .filter-section .filter-actions .btn {
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
    .filter-section .filter-actions .btn-primary { background: var(--primary); color: #fff; }
    .filter-section .filter-actions .btn-primary:hover { background: var(--primary-light); }
    .filter-section .filter-actions .btn-success { background: var(--success); color: #fff; }
    .filter-section .filter-actions .btn-outline { background: transparent; color: var(--text-secondary); border: 1px solid rgba(0, 40, 85, 0.12); }

    /* Mini Stats */
    .stats-mini {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
        margin-bottom: 24px;
    }
    .stats-mini .stat-mini {
        background: var(--bg-card);
        border-radius: var(--radius-sm);
        padding: 16px 20px;
        box-shadow: var(--shadow-sm);
        border: 1px solid rgba(0, 40, 85, 0.06);
        display: flex;
        align-items: center;
        gap: 14px;
    }
    .stats-mini .stat-mini .icon {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        flex-shrink: 0;
    }
    .stats-mini .stat-mini .icon.blue { background: rgba(0, 40, 85, 0.10); color: var(--primary); }
    .stats-mini .stat-mini .icon.green { background: rgba(39, 174, 96, 0.12); color: var(--success); }
    .stats-mini .stat-mini .icon.orange { background: rgba(255, 184, 0, 0.15); color: #d69e00; }
    .stats-mini .stat-mini .icon.red { background: rgba(231, 76, 60, 0.12); color: var(--danger); }
    .stats-mini .stat-mini .info .value { font-size: 22px; font-weight: 800; line-height: 1.2; color: var(--primary-dark); }
    .stats-mini .stat-mini .info .label { font-size: 12px; color: var(--text-muted); font-weight: 500; }

    /* Table Data Mingguan */
    .table-card {
        background: var(--bg-card);
        border-radius: var(--radius);
        box-shadow: var(--shadow-sm);
        border: 1px solid rgba(0, 40, 85, 0.06);
        overflow: hidden;
    }
    .table-card .table-header {
        padding: 18px 24px;
        border-bottom: 1px solid rgba(0, 40, 85, 0.06);
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
    }
    .table-card .table-header h3 { font-size: 16px; font-weight: 700; color: var(--primary); }
    .table-card .table-header .table-actions { display: flex; gap: 8px; flex-wrap: wrap; }
    .table-card .table-header .table-actions .btn {
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
    .table-card .table-header .table-actions .btn-primary { background: var(--primary); color: #fff; }
    .table-card .table-header .table-actions .btn-success { background: var(--success); color: #fff; }
    .table-card .table-header .table-actions .btn-outline { background: transparent; color: var(--text-secondary); border: 1px solid rgba(0, 40, 85, 0.12); }

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
        min-width: 1100px !important;
        border-collapse: collapse;
        font-size: 13.5px;
        white-space: nowrap !important;
    }
    .table-card table tr,
    .table-card table th,
    .table-card table td {
        transition: none !important;
    }
    .table-card table thead { background: var(--bg-body); }
    .table-card table thead th { padding: 12px 18px; text-align: left; font-weight: 600; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; color: var(--text-muted); border-bottom: 1px solid rgba(0, 40, 85, 0.06); white-space: nowrap !important; }
    .table-card table tbody td { padding: 12px 18px; border-bottom: 1px solid rgba(0, 40, 85, 0.06); vertical-align: middle; white-space: nowrap !important; }
    .table-card table tbody tr:hover { background: rgba(0, 40, 85, 0.02); }

    .progress-bar-mini { width: 60px; height: 6px; background: var(--bg-body); border-radius: 10px; overflow: hidden; display: inline-block; margin-right: 6px; vertical-align: middle; }
    .progress-bar-mini .fill { height: 100%; border-radius: 10px; }

    .table-actions-cell { display: flex; gap: 6px; }
    .table-actions-cell .btn-icon { width: 32px; height: 32px; border-radius: 50%; border: none; font-size: 13px; cursor: pointer; transition: var(--transition); display: flex; align-items: center; justify-content: center; text-decoration: none; }
    .table-actions-cell .btn-icon.view { background: rgba(0, 40, 85, 0.08); color: var(--primary); }
    .table-actions-cell .btn-icon.view:hover { background: var(--primary); color: #fff; }
    .table-actions-cell .btn-icon.edit { background: rgba(255, 184, 0, 0.15); color: #d69e00; }
    .table-actions-cell .btn-icon.edit:hover { background: var(--secondary); color: var(--primary-dark); }
    .table-actions-cell .btn-icon.delete { background: rgba(231, 76, 60, 0.10); color: var(--danger); }
    .table-actions-cell .btn-icon.delete:hover { background: var(--danger); color: #fff; }

    .table-footer { padding: 14px 24px; border-top: 1px solid rgba(0, 40, 85, 0.06); display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px; font-size: 13px; color: var(--text-muted); }
    /* Pagination styles moved to table.css */

    /* Modal Hapus */
    .modal-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,0.45);
        z-index: 9999;
        align-items: center;
        justify-content: center;
    }
    .modal-overlay.active { display: flex; }
    .modal-box {
        background: var(--bg-card);
        border-radius: var(--radius);
        padding: 32px 28px;
        max-width: 420px;
        width: 90%;
        box-shadow: 0 20px 60px rgba(0,0,0,0.2);
        text-align: center;
    }
    .modal-box .modal-icon {
        width: 60px; height: 60px;
        border-radius: 50%;
        background: rgba(231, 76, 60, 0.10);
        color: var(--danger);
        display: flex; align-items: center; justify-content: center;
        font-size: 26px;
        margin: 0 auto 16px;
    }
    .modal-box h4 { font-size: 18px; font-weight: 700; color: var(--text-primary); margin: 0 0 8px; }
    .modal-box p { font-size: 14px; color: var(--text-muted); margin: 0 0 24px; line-height: 1.6; }
    .modal-box .modal-actions { display: flex; gap: 10px; justify-content: center; }
    .btn-modal-cancel {
        padding: 10px 22px; border-radius: var(--radius-sm);
        background: transparent; border: 1px solid rgba(0,40,85,0.14);
        color: var(--text-secondary); font-family: inherit; font-weight: 600;
        font-size: 14px; cursor: pointer; transition: var(--transition);
    }
    .btn-modal-cancel:hover { background: rgba(0,40,85,0.05); }
    .btn-modal-delete {
        padding: 10px 22px; border-radius: var(--radius-sm);
        background: var(--danger); border: none;
        color: #fff; font-family: inherit; font-weight: 600;
        font-size: 14px; cursor: pointer; transition: var(--transition);
        display: inline-flex; align-items: center; gap: 7px;
    }
    .btn-modal-delete:hover { background: #c0392b; }

    @media (max-width: 1024px) {
        .stats-mini { grid-template-columns: repeat(2, 1fr); gap: 14px; }
        .filter-section { padding: 16px; flex-direction: column; align-items: stretch; gap: 12px; }
        .filter-section .filter-group { width: 100%; }
        .filter-section .filter-group .pupr-search-group,
        .filter-section .filter-group .pupr-dropdown-wrapper,
        .filter-section .filter-group .pupr-dropdown-toggle { width: 100%; justify-content: space-between; }
        .filter-section .filter-actions { width: 100%; margin-left: 0; flex-wrap: wrap; }
        .filter-section .filter-actions .btn { flex: 1; min-width: 120px; justify-content: center; }
        .table-card .table-header { flex-direction: column; align-items: stretch; gap: 12px; }
        .table-card .table-header .table-actions { width: 100%; flex-wrap: wrap; }
        .table-card .table-header .table-actions .btn { flex: 1; min-width: 140px; justify-content: center; }
    }

    @media (max-width: 480px) {
        .stats-mini { grid-template-columns: 1fr; gap: 10px; }
        .dashboard-content { padding: 12px; }
        .stat-mini { padding: 12px 16px; }
        .table-footer { flex-direction: column; align-items: center; text-align: center; gap: 10px; }
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
            <span>Data Mingguan</span>
        </div>

        {{-- Alert Sukses --}}
        @if(session('success'))
            <div style="background:rgba(39,174,96,0.10);border:1px solid rgba(39,174,96,0.30);border-radius:var(--radius-sm);padding:14px 18px;margin-bottom:20px;font-size:13px;color:var(--success);display:flex;align-items:center;gap:10px;">
                <i class="fas fa-check-circle" style="font-size:16px;"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <!-- Mini Stats -->
        <div class="stats-mini">
            <div class="stat-mini">
                <div class="icon blue"><i class="fas fa-tasks"></i></div>
                <div class="info">
                    <div class="value">{{ $stats['total'] }}</div>
                    <div class="label">Total Kegiatan</div>
                </div>
            </div>
            <div class="stat-mini">
                <div class="icon green"><i class="fas fa-check-circle"></i></div>
                <div class="info">
                    <div class="value">{{ $stats['selesai'] }}</div>
                    <div class="label">Selesai</div>
                </div>
            </div>
            <div class="stat-mini">
                <div class="icon orange"><i class="fas fa-clock"></i></div>
                <div class="info">
                    <div class="value">{{ $stats['menunggu'] }}</div>
                    <div class="label">Menunggu</div>
                </div>
            </div>
            <div class="stat-mini">
                <div class="icon red"><i class="fas fa-spinner"></i></div>
                <div class="info">
                    <div class="value">{{ $stats['proses'] }}</div>
                    <div class="label">Proses</div>
                </div>
            </div>
        </div>

        <!-- Filter & Search Section -->
        <form action="{{ route('data-mingguan') }}" method="GET" class="filter-section">
            <div class="filter-group">
                <div class="pupr-search-group">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama kegiatan, lokasi..." class="pupr-search-input" />
                    <button type="submit" class="pupr-search-btn"><i class="fas fa-search"></i></button>
                </div>
            </div>

            <div class="filter-group">
                {{-- Toggle Tampilan: Harian (default) / Mingguan --}}
                <div class="pupr-dropdown-wrapper" id="wrapperTampilan">
                    <button type="button" class="btn btn-outline pupr-dropdown-toggle" data-toggle="pupr-dropdown">
                        <span class="selected-label"><i class="fas fa-calendar-day" style="margin-right:6px;"></i>Harian</span>
                        <i class="fas fa-chevron-down" style="font-size:10px;margin-left:8px;"></i>
                    </button>
                    <div class="pupr-dropdown-menu">
                        <div class="pupr-dropdown-item active" data-value="harian" id="optHarian"><i class="fas fa-calendar-day" style="width:16px;"></i> Harian</div>
                        <div class="pupr-dropdown-item" data-value="mingguan" id="optMingguan"><i class="fas fa-calendar-week" style="width:16px;"></i> Mingguan</div>
                    </div>
                </div>
            </div>

            {{-- Filter Minggu: hanya tampil saat mode Mingguan --}}
            <div class="filter-group" id="filterMingguGroup" style="display:none;">
                <div class="pupr-dropdown-wrapper">
                    <button type="button" class="btn btn-outline pupr-dropdown-toggle" data-toggle="pupr-dropdown">
                        <span class="selected-label">{{ request('minggu') ? 'Minggu ke-'.request('minggu') : 'Semua Minggu' }}</span>
                        <i class="fas fa-chevron-down" style="font-size:10px;margin-left:8px;"></i>
                    </button>
                    <div class="pupr-dropdown-menu" id="dropdownMingguMenu">
                        <div class="pupr-dropdown-item {{ !request('minggu') ? 'active' : '' }}" data-value="all">Semua Minggu</div>
                        @for($w = 30; $w >= 1; $w--)
                            <div class="pupr-dropdown-item {{ request('minggu') == $w ? 'active' : '' }}" data-value="{{ $w }}">Minggu ke-{{ $w }}</div>
                        @endfor
                    </div>
                </div>
                <input type="hidden" name="minggu" id="inputFilterMinggu" value="{{ request('minggu') }}" />
            </div>

            <div class="filter-group">
                <div class="pupr-dropdown-wrapper">
                    <button type="button" class="btn btn-outline pupr-dropdown-toggle" data-toggle="pupr-dropdown">
                        @php
                            $reqLokasi = request('lokasi');
                            $labelLokasi = $reqLokasi && $reqLokasi != 'all' ? 'Kec. '.ucwords(str_replace('_',' ',$reqLokasi)) : 'Semua Kecamatan';
                        @endphp
                        <span class="selected-label">{{ $labelLokasi }}</span>
                        <i class="fas fa-chevron-down" style="font-size:10px;margin-left:8px;"></i>
                    </button>
                    <div class="pupr-dropdown-menu" id="dropdownLokasiMenu" style="max-height:220px;overflow-y:auto;">
                        <div class="pupr-dropdown-item {{ !$reqLokasi || $reqLokasi=='all' ? 'active' : '' }}" data-value="all">Semua Kecamatan</div>
                        @foreach(['Kaliwates','Sumbersari','Patrang','Ajung','Rambipuji','Balung','Ambulu','Wuluhan','Puger','Kencong','Gumukmas','Umbulsari','Semboro','Jombang','Silo','Mayang','Mumbulsari','Jenggawah','Tempurejo','Pakusari','Sukowono','Kalisat','Ledokombo','Sumberjambe','Arjasa','Jelbuk','Bangsalsari','Panti','Sukorambi','Tanggul','Sumberbaru'] as $kec)
                            <div class="pupr-dropdown-item {{ $reqLokasi == strtolower(str_replace(' ','_',$kec)) ? 'active' : '' }}" data-value="{{ strtolower(str_replace(' ','_',$kec)) }}">Kec. {{ $kec }}</div>
                        @endforeach
                    </div>
                </div>
                <input type="hidden" name="lokasi" id="inputFilterLokasi" value="{{ request('lokasi') }}" />
            </div>

            <div class="filter-group">
                <div class="pupr-dropdown-wrapper">
                    <button type="button" class="btn btn-outline pupr-dropdown-toggle" data-toggle="pupr-dropdown">
                        @php
                            $reqStatus = request('status');
                            $statusLabels = ['proses'=>'Proses','selesai'=>'Selesai','menunggu'=>'Menunggu','survei'=>'Survei','batal'=>'Batal'];
                            $labelStatus = isset($statusLabels[$reqStatus]) ? $statusLabels[$reqStatus] : 'Semua Status';
                        @endphp
                        <span class="selected-label">{{ $labelStatus }}</span>
                        <i class="fas fa-chevron-down" style="font-size:10px;margin-left:8px;"></i>
                    </button>
                    <div class="pupr-dropdown-menu" id="dropdownStatusMenu">
                        <div class="pupr-dropdown-item {{ !$reqStatus || $reqStatus=='all' ? 'active' : '' }}" data-value="all">Semua Status</div>
                        <div class="pupr-dropdown-item {{ $reqStatus=='proses' ? 'active' : '' }}" data-value="proses">Proses</div>
                        <div class="pupr-dropdown-item {{ $reqStatus=='selesai' ? 'active' : '' }}" data-value="selesai">Selesai</div>
                        <div class="pupr-dropdown-item {{ $reqStatus=='menunggu' ? 'active' : '' }}" data-value="menunggu">Menunggu</div>
                        <div class="pupr-dropdown-item {{ $reqStatus=='survei' ? 'active' : '' }}" data-value="survei">Survei</div>
                        <div class="pupr-dropdown-item {{ $reqStatus=='batal' ? 'active' : '' }}" data-value="batal">Batal</div>
                    </div>
                </div>
                <input type="hidden" name="status" id="inputFilterStatus" value="{{ request('status') }}" />
            </div>

            <div class="filter-actions">
                <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Filter</button>
                <a href="{{ route('data-mingguan') }}" class="btn btn-outline"><i class="fas fa-redo"></i> Reset</a>
            </div>
        </form>

        <!-- Table Data Mingguan -->
        <div class="table-card">
            <div class="table-header">
                <h3 id="tableTitle"><i class="fas fa-list-ul" style="color:var(--primary);margin-right:10px;"></i>Data Kegiatan Harian</h3>
                <div class="table-actions">
                    <button class="btn btn-primary" id="addDataBtn" onclick="window.location='{{ route('data-mingguan.create') }}'"><i class="fas fa-plus"></i> Tambah Data</button>
                    <form method="POST" action="{{ route('bap.generate-all') }}" style="display:inline;" onsubmit="return handleBapAllSubmit(this)">
                        @csrf
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-file-pdf"></i> Generate BAP Semua
                        </button>
                    </form>
                </div>
            </div>

            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th style="width:50px;">No</th>
                            <th style="min-width:180px;">Nama Kegiatan</th>
                            <th style="min-width:150px;">Pemohon</th>
                            <th style="min-width:140px;">Lokasi</th>
                            <th class="col-tanggal" style="min-width:120px;">Tanggal</th>
                            <th class="col-minggu" style="min-width:120px;display:none;">Minggu</th>
                            <th style="min-width:120px;">Status</th>
                            <th style="min-width:100px;">BAP</th>
                            <th style="min-width:180px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data as $index => $item)
                        @php
                            $colorMap = ['selesai'=>'success','proses'=>'warning','survei'=>'info','batal'=>'danger','menunggu'=>'secondary'];
                            $badgeColor = $colorMap[$item->status] ?? 'secondary';
                        @endphp
                        <tr>
                            <td>{{ $data->firstItem() + $index }}</td>
                            <td><strong>{{ $item->nama_kegiatan }}</strong></td>
                            <td>{{ $item->nama_pemohon ?: '-' }}</td>
                            <td><i class="fas fa-location-dot" style="color:var(--text-muted);"></i> Kec. {{ ucwords(str_replace('_',' ',$item->lokasi)) }}</td>
                            <td class="col-tanggal">{{ $item->tanggal->format('d M Y') }}</td>
                            <td class="col-minggu" style="display:none;">Minggu {{ $item->minggu ?? '-' }}</td>
                            <td><span class="badge-status {{ $badgeColor }}">{{ ucfirst($item->status) }}</span></td>
                            <td>
                                @if($item->status_bap === 'sudah')
                                    <span class="badge-status success" title="BAP Sudah Digenerate"><i class="fas fa-file-circle-check"></i> Sudah</span>
                                @else
                                    <span class="badge-status secondary" title="BAP Belum Digenerate"><i class="fas fa-file-circle-xmark"></i> Belum</span>
                                @endif
                            </td>
                            <td>
                                <div class="table-actions-cell">
                                    <a href="{{ route('data-mingguan.show', $item->id) }}" class="btn-icon view" title="Lihat Detail"><i class="fas fa-eye"></i></a>
                                    <a href="{{ route('data-mingguan.edit', $item->id) }}" class="btn-icon edit" title="Edit"><i class="fas fa-pen"></i></a>

                                    @if($item->status_bap === 'belum')
                                        <form method="POST" action="{{ route('bap.generate-from-kegiatan', $item->id) }}" style="display:inline;" onsubmit="return handleBapSingleSubmit(this)">
                                            @csrf
                                            <button type="submit" class="btn-icon print" title="Generate BAP" style="background:rgba(39,174,96,0.10);color:var(--success);">
                                                <i class="fas fa-file-pdf"></i>
                                            </button>
                                        </form>
                                    @endif

                                    <button type="button" class="btn-icon delete" title="Hapus"
                                        onclick="openDeleteModal({{ $item->id }}, '{{ addslashes($item->nama_kegiatan) }}')"
                                    ><i class="fas fa-trash"></i></button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" style="text-align:center;padding:40px;color:var(--text-muted);">
                                <i class="fas fa-inbox" style="font-size:32px;display:block;margin-bottom:10px;opacity:0.4;"></i>
                                Belum ada data kegiatan. <a href="{{ route('data-mingguan.create') }}" style="color:var(--primary);font-weight:600;">Tambah sekarang</a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="table-footer">
                <span>Menampilkan {{ $data->firstItem() ?? 0 }}-{{ $data->lastItem() ?? 0 }} dari {{ $data->total() }} data</span>

                @if($data->hasPages())
                    <div class="pagination">
                        {{-- Prev --}}
                        @if($data->onFirstPage())
                            <span class="page disabled"><i class="fas fa-chevron-left"></i></span>
                        @else
                            <a href="{{ $data->previousPageUrl() }}" class="page"><i class="fas fa-chevron-left"></i></a>
                        @endif

                        {{-- Nomor halaman --}}
                        @foreach($data->getUrlRange(max(1, $data->currentPage() - 2), min($data->lastPage(), $data->currentPage() + 2)) as $page => $url)
                            @if($page == $data->currentPage())
                                <span class="page active">{{ $page }}</span>
                            @else
                                <a href="{{ $url }}" class="page">{{ $page }}</a>
                            @endif
                        @endforeach

                        {{-- Next --}}
                        @if($data->hasMorePages())
                            <a href="{{ $data->nextPageUrl() }}" class="page"><i class="fas fa-chevron-right"></i></a>
                        @else
                            <span class="page disabled"><i class="fas fa-chevron-right"></i></span>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </main>

    {{-- Modal Konfirmasi Hapus --}}
    <div class="modal-overlay" id="deleteModal">
        <div class="modal-box">
            <div class="modal-icon">
                <i class="fas fa-trash-alt"></i>
            </div>
            <h4>Hapus Data Kegiatan?</h4>
            <p id="deleteModalText">Data ini akan dihapus secara permanen dan tidak dapat dikembalikan.</p>
            <div class="modal-actions">
                <button type="button" class="btn-modal-cancel" onclick="closeDeleteModal()">Batal</button>
                <form id="deleteForm" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-modal-delete">
                        <i class="fas fa-trash"></i> Ya, Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function handleBapAllSubmit(form) {
        if (!confirm('Generate BAP untuk semua kegiatan yang belum memiliki BAP?')) {
            return false;
        }
        if (window.PuprLoading) {
            window.PuprLoading.show('Generating BAP Semua Kegiatan...');
        }
        return true;
    }

    function handleBapSingleSubmit(form) {
        if (window.PuprLoading) {
            window.PuprLoading.show('Membuat Dokumen BAP...');
        }
        return true;
    }

    document.addEventListener('DOMContentLoaded', function() {

        // Dropdown Lokasi: klik langsung filter
        document.querySelectorAll('#dropdownLokasiMenu .pupr-dropdown-item').forEach(function(item) {
            item.addEventListener('click', function() {
                document.getElementById('inputFilterLokasi').value = this.dataset.value;
                this.closest('form').submit();
            });
        });

        // Dropdown Status: klik langsung filter
        document.querySelectorAll('#dropdownStatusMenu .pupr-dropdown-item').forEach(function(item) {
            item.addEventListener('click', function() {
                document.getElementById('inputFilterStatus').value = this.dataset.value;
                this.closest('form').submit();
            });
        });

        // Dropdown Minggu: klik langsung filter
        document.querySelectorAll('#dropdownMingguMenu .pupr-dropdown-item').forEach(function(item) {
            item.addEventListener('click', function() {
                document.getElementById('inputFilterMinggu').value = (this.dataset.value === 'all') ? '' : this.dataset.value;
                this.closest('form').submit();
            });
        });

        // Toggle Harian / Mingguan (visual saja, tidak submit)
        const filterMingguGroup = document.getElementById('filterMingguGroup');
        const tableTitle        = document.getElementById('tableTitle');

        function setMode(mode) {
            const isMingguan = mode === 'mingguan';
            filterMingguGroup.style.display = isMingguan ? '' : 'none';
            tableTitle.innerHTML = isMingguan
                ? '<i class="fas fa-list-ul" style="color:var(--primary);margin-right:10px;"></i>Data Kegiatan Mingguan'
                : '<i class="fas fa-list-ul" style="color:var(--primary);margin-right:10px;"></i>Data Kegiatan Harian';
            document.querySelectorAll('.col-tanggal').forEach(el => { el.style.display = isMingguan ? 'none' : ''; });
            document.querySelectorAll('.col-minggu').forEach(el => { el.style.display = isMingguan ? '' : 'none'; });
        }

        document.addEventListener('click', function(e) {
            const item = e.target.closest('#wrapperTampilan .pupr-dropdown-item');
            if (!item) return;
            setMode(item.getAttribute('data-value'));
        });

        // Aktifkan mode mingguan jika ada filter minggu di URL
        if ('{{ request('minggu') }}') setMode('mingguan');
        else setMode('harian');
    });

    // Modal Hapus
    function openDeleteModal(id, nama) {
        document.getElementById('deleteModalText').innerHTML =
            'Data <strong>' + nama + '</strong> akan dihapus secara permanen dan tidak dapat dikembalikan.';
        document.getElementById('deleteForm').action = '/data-mingguan/' + id;
        document.getElementById('deleteModal').classList.add('active');
    }
    function closeDeleteModal() {
        document.getElementById('deleteModal').classList.remove('active');
    }
    document.getElementById('deleteModal').addEventListener('click', function(e) {
        if (e.target === this) closeDeleteModal();
    });
</script>
@endpush
