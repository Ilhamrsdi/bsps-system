@extends('layouts.partial.app')

@section('title', 'PUPR Jember - BAP')
@section('title_header', 'BAP')
@section('subtitle_header', 'Berita Acara Pemeriksaan & Dokumentasi Kegiatan Dinas PUPR Jember')

@push('styles')
<style>
    /* ============================================================
       PAGE STYLES: BAP
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

    /* Stats BAP */
    .stats-bap {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
        margin-bottom: 24px;
    }
    .stats-bap .stat-item {
        background: var(--bg-card);
        border-radius: var(--radius-sm);
        padding: 16px 20px;
        box-shadow: var(--shadow-sm);
        border: 1px solid rgba(0, 40, 85, 0.06);
        display: flex;
        align-items: center;
        gap: 14px;
    }
    .stats-bap .stat-item .icon {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        flex-shrink: 0;
    }
    .stats-bap .stat-item .icon.blue { background: rgba(0, 40, 85, 0.10); color: var(--primary); }
    .stats-bap .stat-item .icon.green { background: rgba(39, 174, 96, 0.12); color: var(--success); }
    .stats-bap .stat-item .icon.orange { background: rgba(255, 184, 0, 0.15); color: #d69e00; }
    .stats-bap .stat-item .icon.purple { background: rgba(142, 68, 173, 0.12); color: var(--purple); }

    .stats-bap .stat-item .info .value {
        font-size: 22px;
        font-weight: 800;
        line-height: 1.2;
        color: var(--primary-dark);
    }
    .stats-bap .stat-item .info .label {
        font-size: 12px;
        color: var(--text-muted);
        font-weight: 500;
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
    .filter-section .filter-actions .btn-primary {
        background: var(--primary);
        color: #fff;
    }
    .filter-section .filter-actions .btn-primary:hover {
        background: var(--primary-light);
    }
    .filter-section .filter-actions .btn-success {
        background: var(--success);
        color: #fff;
    }
    .filter-section .filter-actions .btn-success:hover {
        background: #219a52;
    }
    .filter-section .filter-actions .btn-outline {
        background: transparent;
        color: var(--text-secondary);
        border: 1px solid rgba(0, 40, 85, 0.12);
    }
    .filter-section .filter-actions .btn-outline:hover {
        background: var(--bg-body);
    }

    /* BAP Table */
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
    .table-card .table-header h3 {
        font-size: 16px;
        font-weight: 700;
        color: var(--primary);
    }
    .table-card .table-header .table-actions {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }
    .table-card .table-header .table-actions .btn {
        padding: 6px 16px;
        border-radius: var(--radius-sm);
        border: none;
        font-family: inherit;
        font-weight: 600;
        font-size: 12px;
        cursor: pointer;
        transition: var(--transition);
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .table-card .table-header .table-actions .btn-success {
        background: var(--success);
        color: #fff;
    }
    .table-card .table-header .table-actions .btn-outline {
        background: transparent;
        color: var(--text-secondary);
        border: 1px solid rgba(0, 40, 85, 0.12);
    }

    .table-card .table-wrapper,
    .table-wrapper {
        overflow-x: auto !important;
        -webkit-overflow-scrolling: touch !important;
        width: 100% !important;
        display: block !important;
    }
    .table-card table {
        width: 100% !important;
        min-width: 1000px !important;
        border-collapse: collapse;
        font-size: 13.5px;
        white-space: nowrap !important;
    }
    .table-card table thead { background: var(--bg-body); }
    .table-card table thead th { padding: 12px 18px; text-align: left; font-weight: 600; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; color: var(--text-muted); border-bottom: 1px solid rgba(0, 40, 85, 0.06); white-space: nowrap !important; }
    .table-card table tbody td { padding: 12px 18px; border-bottom: 1px solid rgba(0, 40, 85, 0.06); vertical-align: middle; white-space: nowrap !important; }
    .table-card table tbody tr:hover { background: rgba(0, 40, 85, 0.02); }

    .table-card .table-actions-cell { display: flex; gap: 6px; flex-wrap: wrap; }
    .table-card .table-actions-cell .btn-icon { padding: 5px 12px; border-radius: var(--radius-sm); border: none; font-family: inherit; font-weight: 600; font-size: 11px; cursor: pointer; transition: var(--transition); display: inline-flex; align-items: center; gap: 4px; }
    .table-card .table-actions-cell .btn-icon.view { background: rgba(0, 40, 85, 0.08); color: var(--primary); }
    .table-card .table-actions-cell .btn-icon.view:hover { background: var(--primary); color: #fff; }
    .table-card .table-actions-cell .btn-icon.download { background: rgba(39, 174, 96, 0.10); color: var(--success); }
    .table-card .table-actions-cell .btn-icon.download:hover { background: var(--success); color: #fff; }
    .table-card .table-actions-cell .btn-icon.print { background: rgba(255, 184, 0, 0.15); color: #d69e00; }
    .table-card .table-actions-cell .btn-icon.print:hover { background: var(--secondary); color: var(--primary-dark); }
    .table-card .table-actions-cell .btn-icon.delete { background: rgba(231, 76, 60, 0.10); color: var(--danger); }
    .table-card .table-actions-cell .btn-icon.delete:hover { background: var(--danger); color: #fff; }

    .table-footer { padding: 14px 24px; border-top: 1px solid rgba(0, 40, 85, 0.06); display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px; font-size: 13px; color: var(--text-muted); }
    /* Pagination styles moved to table.css */

    /* Modal */
    .modal-overlay { display: none; position: fixed; inset: 0; background: rgba(0, 23, 55, 0.5); backdrop-filter: blur(4px); z-index: 2000; align-items: center; justify-content: center; padding: 20px; }
    .modal-overlay.active { display: flex; }
    .modal-box { background: var(--bg-card); border-radius: var(--radius); max-width: 650px; width: 100%; max-height: 90vh; overflow-y: auto; box-shadow: var(--shadow-lg); color: var(--text-primary); animation: modalIn 0.3s ease; }
    @keyframes modalIn { from { transform: scale(0.95) translateY(20px); opacity: 0; } to { transform: scale(1) translateY(0); opacity: 1; } }
    .modal-box .modal-header { padding: 20px 24px; border-bottom: 1px solid rgba(0, 40, 85, 0.06); display: flex; align-items: center; justify-content: space-between; }
    .modal-box .modal-header h3 { font-size: 18px; font-weight: 700; color: var(--primary); }
    .modal-box .modal-header .close-btn { width: 36px; height: 36px; border-radius: 50%; border: none; background: transparent; font-size: 20px; cursor: pointer; transition: var(--transition); color: var(--text-muted); }
    .modal-box .modal-header .close-btn:hover { background: var(--bg-body); }
    .modal-box .modal-body { padding: 24px; }
    .modal-box .modal-body .form-group { margin-bottom: 18px; }
    .modal-box .modal-body .form-group label { display: block; font-size: 13px; font-weight: 600; margin-bottom: 6px; color: var(--text-secondary); }
    .modal-box .modal-body .form-group input, .modal-box .modal-body .form-group select, .modal-box .modal-body .form-group textarea { width: 100%; padding: 10px 14px; border-radius: var(--radius-sm); border: 1px solid rgba(0, 40, 85, 0.12); font-family: inherit; font-size: 14px; background: var(--bg-body); transition: var(--transition); outline: none; }
    .modal-box .modal-body .form-group input:focus, .modal-box .modal-body .form-group select:focus, .modal-box .modal-body .form-group textarea:focus { border-color: var(--primary); box-shadow: 0 0 0 4px rgba(0, 40, 85, 0.08); background: #fff; }
    .modal-box .modal-body .form-group textarea { resize: vertical; min-height: 80px; }
    .modal-box .modal-body .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
    .modal-box .modal-footer { padding: 16px 24px; border-top: 1px solid rgba(0, 40, 85, 0.06); display: flex; justify-content: flex-end; gap: 10px; }
    .modal-box .modal-footer .btn { padding: 10px 24px; border-radius: var(--radius-sm); border: none; font-family: inherit; font-weight: 600; font-size: 14px; cursor: pointer; transition: var(--transition); }
    .modal-box .modal-footer .btn-cancel { background: var(--bg-body); color: var(--text-secondary); }
    .modal-box .modal-footer .btn-submit { background: var(--primary); color: #fff; }

    @media (max-width: 1024px) {
        .stats-bap { grid-template-columns: repeat(2, 1fr); gap: 14px; }
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

    @media (max-width: 768px) {
        .stats-bap .stat-item { padding: 14px 16px; }
        .stats-bap .stat-item .info .value { font-size: 20px; }
        .modal-box .modal-body .form-row { grid-template-columns: 1fr; }
        .table-footer { flex-direction: column; align-items: center; text-align: center; gap: 10px; }
    }

    @media (max-width: 480px) {
        .stats-bap { grid-template-columns: 1fr; gap: 10px; }
        .dashboard-content { padding: 12px; }
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
            <span>BAP</span>
        </div>

        {{-- Alert --}}
        @if(session('success'))
            <div style="background:rgba(39,174,96,0.10);border:1px solid rgba(39,174,96,0.30);border-radius:var(--radius-sm);padding:14px 18px;margin-bottom:20px;font-size:13px;color:var(--success);display:flex;align-items:center;gap:10px;">
                <i class="fas fa-check-circle" style="font-size:16px;"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif
        @if(session('error'))
            <div style="background:rgba(231,76,60,0.10);border:1px solid rgba(231,76,60,0.30);border-radius:var(--radius-sm);padding:14px 18px;margin-bottom:20px;font-size:13px;color:var(--danger);display:flex;align-items:center;gap:10px;">
                <i class="fas fa-exclamation-circle" style="font-size:16px;"></i>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <!-- Stats BAP (dari DB) -->
        <div class="stats-bap">
            <div class="stat-item">
                <div class="icon blue"><i class="fas fa-file-pdf"></i></div>
                <div class="info">
                    <div class="value">{{ $stats['total'] }}</div>
                    <div class="label">Total BAP</div>
                </div>
            </div>
            <div class="stat-item">
                <div class="icon green"><i class="fas fa-check-circle"></i></div>
                <div class="info">
                    <div class="value">{{ $stats['terbit'] }}</div>
                    <div class="label">BAP Terbit / Resmi</div>
                </div>
            </div>
            <div class="stat-item">
                <div class="icon orange"><i class="fas fa-pen"></i></div>
                <div class="info">
                    <div class="value">{{ $stats['draft'] }}</div>
                    <div class="label">BAP Draft</div>
                </div>
            </div>
            <div class="stat-item">
                <div class="icon red" style="background:rgba(231,76,60,0.12);color:var(--danger);"><i class="fas fa-file-circle-xmark"></i></div>
                <div class="info">
                    <div class="value">{{ $stats['belum'] }}</div>
                    <div class="label">Belum Memiliki BAP</div>
                </div>
            </div>
        </div>

        <!-- Filter & Search (Form GET ke DB) -->
        <form action="{{ route('bab') }}" method="GET" class="filter-section">
            <div class="filter-group">
                <div class="pupr-search-group">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nomor BAP, kegiatan, lokasi..." class="pupr-search-input" />
                    <button type="submit" class="pupr-search-btn"><i class="fas fa-search"></i></button>
                </div>
            </div>

            <div class="filter-group">
                <div class="pupr-dropdown-wrapper">
                    <button type="button" class="btn btn-outline pupr-dropdown-toggle" data-toggle="pupr-dropdown">
                        @php
                            $reqStatus = request('status');
                            $statusLabels = ['draft'=>'Draft','terbit'=>'Terbit','ttd'=>'Ditandatangani','revisi'=>'Revisi'];
                            $labelStatus = isset($statusLabels[$reqStatus]) ? $statusLabels[$reqStatus] : 'Semua Status';
                        @endphp
                        <span class="selected-label">{{ $labelStatus }}</span>
                        <i class="fas fa-chevron-down" style="font-size:10px;margin-left:8px;"></i>
                    </button>
                    <div class="pupr-dropdown-menu" id="dropdownBapStatusMenu">
                        <div class="pupr-dropdown-item {{ !$reqStatus || $reqStatus=='all' ? 'active' : '' }}" data-value="all">Semua Status</div>
                        <div class="pupr-dropdown-item {{ $reqStatus=='draft' ? 'active' : '' }}" data-value="draft">Draft</div>
                        <div class="pupr-dropdown-item {{ $reqStatus=='terbit' ? 'active' : '' }}" data-value="terbit">Terbit</div>
                        <div class="pupr-dropdown-item {{ $reqStatus=='ttd' ? 'active' : '' }}" data-value="ttd">Ditandatangani</div>
                        <div class="pupr-dropdown-item {{ $reqStatus=='revisi' ? 'active' : '' }}" data-value="revisi">Revisi</div>
                    </div>
                </div>
                <input type="hidden" name="status" id="inputBapStatus" value="{{ request('status') }}" />
            </div>

            <div class="filter-group">
                <div class="pupr-dropdown-wrapper">
                    <button type="button" class="btn btn-outline pupr-dropdown-toggle" data-toggle="pupr-dropdown">
                        @php
                            $reqLokasi = request('lokasi');
                            $labelLokasi = $reqLokasi && $reqLokasi != 'all' ? 'Kec. '.ucwords(str_replace('_',' ',$reqLokasi)) : 'Semua Lokasi';
                        @endphp
                        <span class="selected-label">{{ $labelLokasi }}</span>
                        <i class="fas fa-chevron-down" style="font-size:10px;margin-left:8px;"></i>
                    </button>
                    <div class="pupr-dropdown-menu" id="dropdownBapLokasiMenu" style="max-height:220px;overflow-y:auto;">
                        <div class="pupr-dropdown-item {{ !$reqLokasi || $reqLokasi=='all' ? 'active' : '' }}" data-value="all">Semua Lokasi</div>
                        @foreach(['Kaliwates','Sumbersari','Patrang','Ajung','Rambipuji','Balung','Ambulu','Wuluhan','Puger','Kencong','Gumukmas','Umbulsari','Semboro','Jombang','Silo','Mayang','Mumbulsari','Jenggawah','Tempurejo','Pakusari','Sukowono','Kalisat','Ledokombo','Sumberjambe','Arjasa','Jelbuk','Bangsalsari','Panti','Sukorambi','Tanggul','Sumberbaru'] as $kec)
                            <div class="pupr-dropdown-item {{ $reqLokasi == strtolower(str_replace(' ','_',$kec)) ? 'active' : '' }}" data-value="{{ strtolower(str_replace(' ','_',$kec)) }}">Kec. {{ $kec }}</div>
                        @endforeach
                    </div>
                </div>
                <input type="hidden" name="lokasi" id="inputBapLokasi" value="{{ request('lokasi') }}" />
            </div>

            <div class="filter-actions">
                <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Filter</button>
                <a href="{{ route('bab') }}" class="btn btn-outline"><i class="fas fa-redo"></i> Reset</a>
            </div>
        </form>

        <!-- BAP Table (dari DB) -->
        <div class="table-card">
            <div class="table-header">
                <h3><i class="fas fa-list-ul" style="color:var(--primary);margin-right:10px;"></i>Daftar BAP</h3>
            </div>

            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th style="width:50px;">No</th>
                            <th style="min-width:170px;">Nomor BAP</th>
                            <th style="min-width:200px;">Kegiatan</th>
                            <th style="min-width:140px;">Lokasi</th>
                            <th style="min-width:120px;">Tanggal</th>
                            <th style="min-width:120px;">Status</th>
                            <th style="min-width:100px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($baps as $index => $bap)
                        @php
                            $dm = $bap->dataMingguan;
                        @endphp
                        <tr>
                            <td>{{ $baps->firstItem() + $index }}</td>
                            <td><strong style="font-family:monospace;font-size:12px;color:var(--primary);">{{ $bap->nomor_bap }}</strong></td>
                            <td>{{ $dm->nama_kegiatan ?? '-' }}</td>
                            <td><i class="fas fa-location-dot" style="color:var(--text-muted);font-size:12px;"></i> Kec. {{ ucwords(str_replace('_',' ', $dm->lokasi ?? '')) }}</td>
                            <td>{{ $dm ? $dm->tanggal->format('d M Y') : '-' }}</td>
                            <td><span class="badge-status {{ $bap->statusColor() }}">{{ $bap->statusLabel() }}</span></td>
                            <td>
                                <div class="table-actions-cell">
                                    {{-- Hanya Tombol Lihat BAP --}}
                                    <a href="{{ url('/cetak-bap/'.$bap->id) }}" target="_blank" class="btn-icon view" title="Lihat BAP" style="text-decoration:none;">
                                        <i class="fas fa-eye"></i> Lihat BAP
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" style="text-align:center;padding:40px;color:var(--text-muted);">
                                <i class="fas fa-file-pdf" style="font-size:32px;display:block;margin-bottom:12px;opacity:0.3;"></i>
                                Belum ada data BAP. Tambah kegiatan di
                                <a href="{{ route('data-mingguan.create') }}" style="color:var(--primary);font-weight:600;">Data Mingguan</a>
                                untuk generate BAP otomatis.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="table-footer">
                <span>Menampilkan {{ $baps->firstItem() ?? 0 }}-{{ $baps->lastItem() ?? 0 }} dari {{ $baps->total() }} BAP</span>

                @if($baps->hasPages())
                    <div class="pagination">
                        @if($baps->onFirstPage())
                            <span class="page disabled"><i class="fas fa-chevron-left"></i></span>
                        @else
                            <a href="{{ $baps->previousPageUrl() }}" class="page"><i class="fas fa-chevron-left"></i></a>
                        @endif

                        @foreach($baps->getUrlRange(max(1, $baps->currentPage()-2), min($baps->lastPage(), $baps->currentPage()+2)) as $page => $url)
                            @if($page == $baps->currentPage())
                                <span class="page active">{{ $page }}</span>
                            @else
                                <a href="{{ $url }}" class="page">{{ $page }}</a>
                            @endif
                        @endforeach

                        @if($baps->hasMorePages())
                            <a href="{{ $baps->nextPageUrl() }}" class="page"><i class="fas fa-chevron-right"></i></a>
                        @else
                            <span class="page disabled"><i class="fas fa-chevron-right"></i></span>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </main>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {

        // Dropdown filter klik langsung submit
        document.querySelectorAll('#dropdownBapStatusMenu .pupr-dropdown-item').forEach(function(item) {
            item.addEventListener('click', function() {
                document.getElementById('inputBapStatus').value = this.dataset.value;
                this.closest('form').submit();
            });
        });

        document.querySelectorAll('#dropdownBapLokasiMenu .pupr-dropdown-item').forEach(function(item) {
            item.addEventListener('click', function() {
                document.getElementById('inputBapLokasi').value = this.dataset.value;
                this.closest('form').submit();
            });
        });
    });
</script>
@endpush

