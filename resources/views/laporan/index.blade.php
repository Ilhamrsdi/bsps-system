@extends('layouts.partial.app')

@section('title', 'BSPS Verval - Laporan & Rekapitulasi')
@section('title_header', 'Laporan & Rekapitulasi BSPS')
@section('subtitle_header', 'Rekapitulasi Laporan Verifikasi &amp; Validasi Calon Penerima Bantuan Stimulan Perumahan Swadaya')

@push('styles')
<style>
    /* Stats Grid Laporan */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
        margin-bottom: 24px;
    }
    .stat-card {
        background: var(--bg-card);
        border-radius: var(--radius-sm);
        padding: 18px 20px;
        box-shadow: var(--shadow-sm);
        border: 1px solid rgba(0, 40, 85, 0.06);
        display: flex;
        align-items: center;
        gap: 14px;
    }
    .stat-card .icon {
        width: 46px;
        height: 46px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        flex-shrink: 0;
    }
    .stat-card .icon.blue { background: rgba(0, 40, 85, 0.10); color: var(--primary); }
    .stat-card .icon.green { background: rgba(39, 174, 96, 0.12); color: var(--success); }
    .stat-card .icon.orange { background: rgba(255, 184, 0, 0.15); color: #d69e00; }
    .stat-card .icon.red { background: rgba(231, 76, 60, 0.12); color: var(--danger); }
    .stat-card .info .value { font-size: 24px; font-weight: 800; color: var(--primary-dark); }
    .stat-card .info .label { font-size: 12px; color: var(--text-muted); font-weight: 500; }

    /* Navigasi Tab Rekapitulasi */
    .laporan-tabs {
        display: flex;
        gap: 6px;
        margin-bottom: 20px;
        background: var(--bg-card);
        padding: 6px;
        border-radius: var(--radius-sm);
        border: 1px solid rgba(0, 40, 85, 0.06);
        box-shadow: var(--shadow-sm);
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }
    .laporan-tabs::-webkit-scrollbar { display: none; }
    .laporan-tab-item {
        padding: 10px 18px;
        border-radius: 8px;
        font-size: 13.5px;
        font-weight: 600;
        color: var(--text-secondary);
        text-decoration: none;
        white-space: nowrap;
        transition: var(--transition);
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    .laporan-tab-item:hover { background: var(--bg-body); color: var(--primary); }
    .laporan-tab-item.active { background: var(--primary); color: #ffffff; font-weight: 700; }

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
    .table-card .table-header h3 { font-size: 16px; font-weight: 700; color: var(--primary); margin: 0; }

    /* Button Detail Premium PUPR Theme */
    .btn-detail-laporan {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 7px 15px;
        border-radius: 8px;
        background: rgba(0, 40, 85, 0.08);
        color: var(--primary);
        font-weight: 700;
        font-size: 12.5px;
        border: 1px solid rgba(0, 40, 85, 0.14);
        cursor: pointer;
        transition: all 0.22s cubic-bezier(0.4, 0, 0.2, 1);
        text-decoration: none;
    }
    .btn-detail-laporan:hover {
        background: var(--primary);
        color: #ffffff;
        border-color: var(--primary);
        box-shadow: 0 4px 14px rgba(0, 40, 85, 0.25);
        transform: translateY(-2px);
    }
    .btn-detail-laporan i {
        font-size: 13px;
        transition: transform 0.2s ease;
    }
    .btn-detail-laporan:hover i {
        transform: scale(1.18);
    }

    /* Responsive Touch Table */
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
        min-width: 1000px !important;
        border-collapse: collapse;
        font-size: 13.5px;
        white-space: nowrap !important;
    }
    .table-card table thead { background: var(--bg-body); }
    .table-card table thead th { padding: 12px 18px; text-align: left; font-weight: 600; font-size: 12px; text-transform: uppercase; color: var(--text-muted); border-bottom: 1px solid rgba(0, 40, 85, 0.06); white-space: nowrap !important; }
    .table-card table tbody td { padding: 12px 18px; border-bottom: 1px solid rgba(0, 40, 85, 0.06); vertical-align: middle; white-space: nowrap !important; }
    .table-card table tr, .table-card table th, .table-card table td { transition: none !important; }

    /* Responsive Laporan Layout */
    @media (max-width: 1024px) {
        .stats-grid { grid-template-columns: repeat(2, 1fr); gap: 14px; }
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
        .stat-card { padding: 14px 16px; }
        .stat-card .info .value { font-size: 20px; }
        .table-footer { flex-direction: column; align-items: center; text-align: center; gap: 10px; }
    }

    @media (max-width: 480px) {
        .stats-grid { grid-template-columns: 1fr; gap: 10px; }
        .dashboard-content { padding: 12px; }
        .stat-card { padding: 12px 16px; }
    }
</style>
@endpush

@section('content')
    @include('layouts.navbar')

    <main class="dashboard-content">
        <!-- Breadcrumb -->
        <div class="breadcrumb" style="font-size:13px;color:var(--text-muted);margin-bottom:20px;display:flex;align-items:center;gap:8px;">
            <a href="{{ url('/') }}" style="color:var(--primary);text-decoration:none;font-weight:500;"><i class="fas fa-home"></i> Home</a>
            <i class="fas fa-chevron-right" style="font-size:10px;"></i>
            <span>Laporan &amp; Rekapitulasi</span>
        </div>

        <!-- 4 Stat Counters Dinamis Real-Time -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="icon blue"><i class="fas fa-file-invoice"></i></div>
                <div class="info">
                    <div class="value">{{ $stats['total_kegiatan'] }}</div>
                    <div class="label">Total Laporan Kegiatan</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="icon green"><i class="fas fa-file-circle-check"></i></div>
                <div class="info">
                    <div class="value">{{ $stats['bap_terbit'] }}</div>
                    <div class="label">BAP Terbit / Resmi</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="icon orange"><i class="fas fa-clipboard-check"></i></div>
                <div class="info">
                    <div class="value">{{ $stats['survei_selesai'] }}</div>
                    <div class="label">Survei Lapangan Selesai</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="icon red"><i class="fas fa-file-circle-exclamation"></i></div>
                <div class="info">
                    <div class="value">{{ $stats['belum_bap'] }}</div>
                    <div class="label">Belum Memiliki BAP</div>
                </div>
            </div>
        </div>

        <!-- Navigasi Tab Rekapitulasi Laporan -->
        <div class="laporan-tabs">
            <a href="{{ route('laporan', array_merge(request()->query(), ['tab' => 'progress'])) }}" class="laporan-tab-item {{ $tab === 'progress' ? 'active' : '' }}">
                <i class="fas fa-chart-line"></i> Rekap Progress Pekerjaan
            </a>
            <a href="{{ route('laporan', array_merge(request()->query(), ['tab' => 'survei'])) }}" class="laporan-tab-item {{ $tab === 'survei' ? 'active' : '' }}">
                <i class="fas fa-clipboard-check"></i> Rekap Hasil Survei
            </a>
            <a href="{{ route('laporan', array_merge(request()->query(), ['tab' => 'bap'])) }}" class="laporan-tab-item {{ $tab === 'bap' ? 'active' : '' }}">
                <i class="fas fa-file-pdf"></i> Rekap Status BAP
            </a>
            <a href="{{ route('laporan', array_merge(request()->query(), ['tab' => 'petugas'])) }}" class="laporan-tab-item {{ $tab === 'petugas' ? 'active' : '' }}">
                <i class="fas fa-user-shield"></i> Rekap Kinerja Petugas
            </a>
        </div>

        <!-- Filter & Search Section Form PUPR -->
        <form action="{{ route('laporan') }}" method="GET" class="filter-section">
            <input type="hidden" name="tab" value="{{ $tab }}" />

            <div class="filter-group">
                <div class="pupr-search-group">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama kegiatan, lokasi, kontraktor..." class="pupr-search-input" />
                    <button type="submit" class="pupr-search-btn"><i class="fas fa-search"></i></button>
                </div>
            </div>

            {{-- Filter Kecamatan --}}
            <div class="filter-group">
                <div class="pupr-dropdown-wrapper">
                    <button type="button" class="btn btn-outline pupr-dropdown-toggle" data-toggle="pupr-dropdown">
                        <span class="selected-label">{{ request('kecamatan') && request('kecamatan') != 'all' ? 'Kec. ' . ucwords(str_replace('_',' ',request('kecamatan'))) : 'Semua Kecamatan' }}</span>
                        <i class="fas fa-chevron-down" style="font-size:10px;margin-left:8px;"></i>
                    </button>
                    <div class="pupr-dropdown-menu" id="dropdownKecamatanMenu" style="max-height:220px;overflow-y:auto;">
                        <div class="pupr-dropdown-item {{ !request('kecamatan') || request('kecamatan')=='all' ? 'active' : '' }}" data-value="all">Semua Kecamatan</div>
                        @foreach(['Kaliwates','Sumbersari','Patrang','Ajung','Rambipuji','Balung','Ambulu','Wuluhan','Puger','Kencong','Silo','Mayang','Sukowono','Kalisat','Arjasa','Tanggul'] as $kec)
                            <div class="pupr-dropdown-item {{ request('kecamatan') == strtolower(str_replace(' ','_',$kec)) ? 'active' : '' }}" data-value="{{ strtolower(str_replace(' ','_',$kec)) }}">Kec. {{ $kec }}</div>
                        @endforeach
                    </div>
                </div>
                <input type="hidden" name="kecamatan" id="inputFilterKecamatan" value="{{ request('kecamatan', 'all') }}" />
            </div>

            {{-- Filter Status BAP --}}
            <div class="filter-group">
                <div class="pupr-dropdown-wrapper">
                    <button type="button" class="btn btn-outline pupr-dropdown-toggle" data-toggle="pupr-dropdown">
                        <span class="selected-label">{{ request('status_bap') == 'terbit' ? 'BAP Terbit' : (request('status_bap') == 'draft' ? 'Draft BAP' : (request('status_bap') == 'belum' ? 'Belum BAP' : 'Semua Status BAP')) }}</span>
                        <i class="fas fa-chevron-down" style="font-size:10px;margin-left:8px;"></i>
                    </button>
                    <div class="pupr-dropdown-menu" id="dropdownStatusBapMenu">
                        <div class="pupr-dropdown-item {{ !request('status_bap') || request('status_bap')=='all' ? 'active' : '' }}" data-value="all">Semua Status BAP</div>
                        <div class="pupr-dropdown-item {{ request('status_bap')=='terbit' ? 'active' : '' }}" data-value="terbit">BAP Terbit</div>
                        <div class="pupr-dropdown-item {{ request('status_bap')=='draft' ? 'active' : '' }}" data-value="draft">Draft BAP</div>
                        <div class="pupr-dropdown-item {{ request('status_bap')=='belum' ? 'active' : '' }}" data-value="belum">Belum Memiliki BAP</div>
                    </div>
                </div>
                <input type="hidden" name="status_bap" id="inputFilterStatusBap" value="{{ request('status_bap', 'all') }}" />
            </div>

            <div class="filter-actions">
                <a href="{{ route('laporan', ['tab' => $tab]) }}" class="btn btn-outline"><i class="fas fa-redo"></i> Reset</a>
            </div>
        </form>

        <!-- Tabel Data Rekapitulasi berdasarkan Tab -->
        <div class="table-card">
            <div class="table-header">
                <h3>
                    @if($tab === 'progress')
                        <i class="fas fa-chart-line" style="color:var(--primary);margin-right:8px;"></i>Rekapitulasi Progress Pekerjaan Lapangan
                    @elseif($tab === 'survei')
                        <i class="fas fa-clipboard-check" style="color:var(--success);margin-right:8px;"></i>Rekapitulasi Hasil Verifikasi Survei Lapangan
                    @elseif($tab === 'bap')
                        <i class="fas fa-file-pdf" style="color:var(--danger);margin-right:8px;"></i>Rekapitulasi Berita Acara Pemeriksaan (BAP)
                    @elseif($tab === 'petugas')
                        <i class="fas fa-user-shield" style="color:#8e44ad;margin-right:8px;"></i>Rekapitulasi Kinerja &amp; Penugasan Petugas
                    @endif
                </h3>
                <div class="table-actions" style="display:flex;gap:8px;">
                    <a href="{{ route('laporan.export', request()->query()) }}" class="btn btn-success" style="text-decoration:none;">
                        <i class="fas fa-file-excel"></i> Export Excel (.CSV)
                    </a>
                    <a href="{{ route('laporan.cetak', request()->query()) }}" target="_blank" class="btn btn-primary" style="text-decoration:none;">
                        <i class="fas fa-print"></i> Cetak Laporan Resmi
                    </a>
                </div>
            </div>

            <div class="table-wrapper">
                @if($tab === 'progress')
                    {{-- TAB 1: REKAP PROGRESS PEKERJAAN --}}
                    <table>
                        <thead>
                            <tr>
                                <th style="width:50px;">No</th>
                                <th style="min-width:240px;">Nama Kegiatan Lapangan</th>
                                <th style="min-width:160px;">Lokasi &amp; Alamat</th>
                                <th style="min-width:140px;">Nilai Kontrak</th>
                                <th style="min-width:150px;">Kontraktor / Pelaksana</th>
                                <th style="min-width:130px;">Survei Lapangan</th>
                                <th style="min-width:130px;">Status BAP</th>
                                <th style="width:100px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($kegiatans as $index => $item)
                                <tr>
                                    <td>{{ $kegiatans->firstItem() + $index }}</td>
                                    <td>
                                        <strong style="color:var(--primary-dark);font-size:14px;display:block;margin-bottom:3px;">{{ $item->nama_kegiatan }}</strong>
                                        <span style="font-size:12px;color:var(--text-muted);"><i class="fas fa-calendar-day"></i> {{ $item->tanggal ? $item->tanggal->format('d M Y') : '-' }} &bull; Minggu ke-{{ $item->minggu }}</span>
                                    </td>
                                    <td>
                                        <div><i class="fas fa-location-dot" style="color:var(--primary);font-size:12px;"></i> Kec. {{ ucwords(str_replace('_',' ',$item->lokasi)) }}</div>
                                        <div style="font-size:12px;color:var(--text-muted);margin-top:2px;">{{ $item->alamat ?: '-' }}</div>
                                    </td>
                                    <td>
                                        @php
                                            $valKontrak = is_numeric($item->nilai_kontrak) ? (float)$item->nilai_kontrak : (float)preg_replace('/[^0-9.]/', '', (string)$item->nilai_kontrak);
                                        @endphp
                                        <strong style="color:var(--primary);">{{ $valKontrak > 0 ? 'Rp ' . number_format($valKontrak, 0, ',', '.') : ($item->nilai_kontrak ?: '-') }}</strong>
                                    </td>
                                    <td><span style="font-weight:600;">{{ $item->kontraktor ?: '-' }}</span></td>
                                    <td>
                                        @if($item->surveys->count() > 0)
                                            <span class="badge-status success"><i class="fas fa-check-circle"></i> {{ $item->surveys->count() }}x Survei</span>
                                        @else
                                            <span class="badge-status warning"><i class="fas fa-clock"></i> Belum Survei</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($item->bap && $item->bap->status === 'terbit')
                                            <span class="badge-status success"><i class="fas fa-shield-check"></i> BAP Terbit</span>
                                        @elseif($item->bap && $item->bap->status === 'draft')
                                            <span class="badge-status warning"><i class="fas fa-file-pen"></i> Draft BAP</span>
                                        @else
                                            <span class="badge-status danger"><i class="fas fa-circle-xmark"></i> Belum BAP</span>
                                        @endif
                                    </td>
                                    <td>
                                        <button type="button" class="btn-detail-laporan" onclick="openDetailModal({{ json_encode($item) }})">
                                            <i class="fas fa-eye"></i> Detail
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" style="text-align:center;padding:40px;color:var(--text-muted);">
                                        <i class="fas fa-inbox" style="font-size:32px;display:block;margin-bottom:10px;opacity:0.4;"></i>
                                        Belum ada data rekapitulasi kegiatan.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div class="table-footer">
                        <span>Menampilkan {{ $kegiatans->firstItem() ?? 0 }}-{{ $kegiatans->lastItem() ?? 0 }} dari {{ $kegiatans->total() }} data kegiatan</span>
                        @if($kegiatans->hasPages())
                            <div class="pagination">
                                @if($kegiatans->onFirstPage())
                                    <span class="page disabled"><i class="fas fa-chevron-left"></i></span>
                                @else
                                    <a href="{{ $kegiatans->previousPageUrl() }}" class="page"><i class="fas fa-chevron-left"></i></a>
                                @endif

                                @foreach($kegiatans->getUrlRange(max(1, $kegiatans->currentPage() - 2), min($kegiatans->lastPage(), $kegiatans->currentPage() + 2)) as $page => $url)
                                    @if($page == $kegiatans->currentPage())
                                        <span class="page active">{{ $page }}</span>
                                    @else
                                        <a href="{{ $url }}" class="page">{{ $page }}</a>
                                    @endif
                                @endforeach

                                @if($kegiatans->hasMorePages())
                                    <a href="{{ $kegiatans->nextPageUrl() }}" class="page"><i class="fas fa-chevron-right"></i></a>
                                @else
                                    <span class="page disabled"><i class="fas fa-chevron-right"></i></span>
                                @endif
                            </div>
                        @endif
                    </div>

                @elseif($tab === 'survei')
                    {{-- TAB 2: REKAP HASIL SURVEI --}}
                    <table>
                        <thead>
                            <tr>
                                <th style="width:50px;">No</th>
                                <th style="min-width:220px;">Nama Kegiatan Lapangan</th>
                                <th style="min-width:180px;">Petugas Survei</th>
                                <th style="min-width:140px;">Tanggal Input</th>
                                <th style="min-width:160px;">Koordinat GPS</th>
                                <th style="min-width:140px;">Sampel Fisik</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($surveylist as $index => $s)
                                <tr>
                                    <td>{{ $surveylist->firstItem() + $index }}</td>
                                    <td>
                                        <strong style="color:var(--primary-dark);">{{ $s->dataMingguan->nama_kegiatan ?? '-' }}</strong>
                                        <div style="font-size:12px;color:var(--text-muted);">Kec. {{ ucwords(str_replace('_',' ',$s->dataMingguan->lokasi ?? '')) }}</div>
                                    </td>
                                    <td>
                                        <span style="font-weight:700;color:var(--primary);"><i class="fas fa-user-shield"></i> {{ $s->user->name ?? '-' }}</span>
                                    </td>
                                    <td>{{ $s->created_at ? $s->created_at->format('d M Y H:i') : '-' }}</td>
                                    <td>
                                        <span style="font-family:monospace;font-size:12px;"><i class="fas fa-location-crosshairs" style="color:var(--danger);"></i> {{ $s->latitude ?? '-' }}, {{ $s->longitude ?? '-' }}</span>
                                    </td>
                                    <td>
                                        @php
                                            $sampleCount = is_array($s->sampel_fisik) ? count($s->sampel_fisik) : 0;
                                        @endphp
                                        <span class="badge-status success"><i class="fas fa-list-check"></i> {{ $sampleCount }} Sampel Cek</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" style="text-align:center;padding:40px;color:var(--text-muted);">Belum ada data survei lapangan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div class="table-footer">
                        <span>Menampilkan {{ $surveylist->firstItem() ?? 0 }}-{{ $surveylist->lastItem() ?? 0 }} dari {{ $surveylist->total() }} survei</span>
                        @if($surveylist->hasPages())
                            <div class="pagination">
                                @if($surveylist->onFirstPage())
                                    <span class="page disabled"><i class="fas fa-chevron-left"></i></span>
                                @else
                                    <a href="{{ $surveylist->previousPageUrl() }}" class="page"><i class="fas fa-chevron-left"></i></a>
                                @endif

                                @foreach($surveylist->getUrlRange(max(1, $surveylist->currentPage() - 2), min($surveylist->lastPage(), $surveylist->currentPage() + 2)) as $page => $url)
                                    @if($page == $surveylist->currentPage())
                                        <span class="page active">{{ $page }}</span>
                                    @else
                                        <a href="{{ $url }}" class="page">{{ $page }}</a>
                                    @endif
                                @endforeach

                                @if($surveylist->hasMorePages())
                                    <a href="{{ $surveylist->nextPageUrl() }}" class="page"><i class="fas fa-chevron-right"></i></a>
                                @else
                                    <span class="page disabled"><i class="fas fa-chevron-right"></i></span>
                                @endif
                            </div>
                        @endif
                    </div>

                @elseif($tab === 'bap')
                    {{-- TAB 3: REKAP BAP --}}
                    <table>
                        <thead>
                            <tr>
                                <th style="width:50px;">No</th>
                                <th style="min-width:180px;">Nomor BAP</th>
                                <th style="min-width:240px;">Nama Kegiatan Lapangan</th>
                                <th style="min-width:140px;">Tanggal BAP</th>
                                <th style="min-width:130px;">Status BAP</th>
                                <th style="width:120px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($baplist as $index => $b)
                                <tr>
                                    <td>{{ $baplist->firstItem() + $index }}</td>
                                    <td><strong style="color:var(--primary);font-family:monospace;">{{ $b->nomor_bap }}</strong></td>
                                    <td>
                                        <strong>{{ $b->dataMingguan->nama_kegiatan ?? '-' }}</strong>
                                    </td>
                                    <td>{{ $b->tanggal_bap ? $b->tanggal_bap->format('d M Y') : '-' }}</td>
                                    <td>
                                        <span class="badge-status {{ $b->status === 'terbit' ? 'success' : 'warning' }}">
                                            {{ ucfirst($b->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('bap.cetak', $b->id) }}" target="_blank" class="btn-icon edit" style="text-decoration:none;">
                                            <i class="fas fa-print"></i> Cetak BAP
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" style="text-align:center;padding:40px;color:var(--text-muted);">Belum ada data BAP terbit.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div class="table-footer">
                        <span>Menampilkan {{ $baplist->firstItem() ?? 0 }}-{{ $baplist->lastItem() ?? 0 }} dari {{ $baplist->total() }} BAP</span>
                        @if($baplist->hasPages())
                            <div class="pagination">
                                @if($baplist->onFirstPage())
                                    <span class="page disabled"><i class="fas fa-chevron-left"></i></span>
                                @else
                                    <a href="{{ $baplist->previousPageUrl() }}" class="page"><i class="fas fa-chevron-left"></i></a>
                                @endif

                                @foreach($baplist->getUrlRange(max(1, $baplist->currentPage() - 2), min($baplist->lastPage(), $baplist->currentPage() + 2)) as $page => $url)
                                    @if($page == $baplist->currentPage())
                                        <span class="page active">{{ $page }}</span>
                                    @else
                                        <a href="{{ $url }}" class="page">{{ $page }}</a>
                                    @endif
                                @endforeach

                                @if($baplist->hasMorePages())
                                    <a href="{{ $baplist->nextPageUrl() }}" class="page"><i class="fas fa-chevron-right"></i></a>
                                @else
                                    <span class="page disabled"><i class="fas fa-chevron-right"></i></span>
                                @endif
                            </div>
                        @endif
                    </div>

                @elseif($tab === 'petugas')
                    {{-- TAB 4: REKAP KINERJA PETUGAS --}}
                    <table>
                        <thead>
                            <tr>
                                <th style="width:50px;">No</th>
                                <th style="min-width:200px;">Nama Petugas Lapangan</th>
                                <th style="min-width:140px;">NIP</th>
                                <th style="min-width:140px;">Wilayah Kecamatan</th>
                                <th style="min-width:140px;">Total Penugasan</th>
                                <th style="min-width:140px;">Survei Selesai</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($petugaslist as $index => $p)
                                <tr>
                                    <td>{{ $petugaslist->firstItem() + $index }}</td>
                                    <td>
                                        <div style="display:flex;align-items:center;gap:10px;">
                                            <div style="width:34px;height:34px;border-radius:50%;background:var(--primary);color:#fff;display:flex;align-items:center;justify-content:center;font-weight:800;">
                                                {{ strtoupper(substr($p->name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <strong style="color:var(--primary-dark);">{{ $p->name }}</strong>
                                                <div style="font-size:11px;color:var(--text-muted);">{{ $p->email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td><span style="font-family:monospace;">{{ $p->nip ?? '-' }}</span></td>
                                    <td><i class="fas fa-location-dot" style="color:var(--primary);"></i> {{ $p->kecamatan ?: '-' }}</td>
                                    <td><span class="badge-status warning">{{ $p->kegiatans->count() }} Kegiatan</span></td>
                                    <td><span class="badge-status success">{{ $p->surveys->count() }} Survei</span></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" style="text-align:center;padding:40px;color:var(--text-muted);">Belum ada data petugas.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div class="table-footer">
                        <span>Menampilkan {{ $petugaslist->firstItem() ?? 0 }}-{{ $petugaslist->lastItem() ?? 0 }} dari {{ $petugaslist->total() }} petugas</span>
                        @if($petugaslist->hasPages())
                            <div class="pagination">
                                @if($petugaslist->onFirstPage())
                                    <span class="page disabled"><i class="fas fa-chevron-left"></i></span>
                                @else
                                    <a href="{{ $petugaslist->previousPageUrl() }}" class="page"><i class="fas fa-chevron-left"></i></a>
                                @endif

                                @foreach($petugaslist->getUrlRange(max(1, $petugaslist->currentPage() - 2), min($petugaslist->lastPage(), $petugaslist->currentPage() + 2)) as $page => $url)
                                    @if($page == $petugaslist->currentPage())
                                        <span class="page active">{{ $page }}</span>
                                    @else
                                        <a href="{{ $url }}" class="page">{{ $page }}</a>
                                    @endif
                                @endforeach

                                @if($petugaslist->hasMorePages())
                                    <a href="{{ $petugaslist->nextPageUrl() }}" class="page"><i class="fas fa-chevron-right"></i></a>
                                @else
                                    <span class="page disabled"><i class="fas fa-chevron-right"></i></span>
                                @endif
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </main>

    <!-- Modal Popup Detail Laporan (window.PuprModal) -->
    <div class="modal-overlay" id="modalDetailLaporan">
        <div class="modal-box" style="max-width:620px;">
            <div class="modal-header">
                <h3><i class="fas fa-file-invoice" style="color:var(--primary);margin-right:8px;"></i>Detail Laporan &amp; Kegiatan</h3>
                <button class="close-btn" type="button" onclick="window.PuprModal.close('modalDetailLaporan')"><i class="fas fa-times"></i></button>
            </div>
            <div class="modal-body" style="padding:24px;">
                <div style="background:rgba(0,40,85,0.04);border:1px solid rgba(0,40,85,0.08);border-radius:10px;padding:16px;margin-bottom:18px;">
                    <div style="font-size:11px;font-weight:700;text-transform:uppercase;color:var(--text-muted);letter-spacing:0.5px;">Nama Kegiatan Lapangan</div>
                    <div style="font-size:16px;font-weight:800;color:var(--primary-dark);margin-top:2px;" id="detailNamaKegiatan">-</div>
                    <div style="font-size:12.5px;color:var(--text-secondary);margin-top:6px;" id="detailLokasiKegiatan">-</div>
                </div>

                <div class="form-row" style="margin-bottom:14px;">
                    <div>
                        <div style="font-size:12px;color:var(--text-muted);font-weight:600;">Nilai Kontrak</div>
                        <div style="font-size:14px;font-weight:700;color:var(--primary);" id="detailNilaiKontrak">-</div>
                    </div>
                    <div>
                        <div style="font-size:12px;color:var(--text-muted);font-weight:600;">Kontraktor / Pelaksana</div>
                        <div style="font-size:14px;font-weight:700;color:var(--text-primary);" id="detailKontraktor">-</div>
                    </div>
                </div>

                <div class="form-row" style="margin-bottom:14px;">
                    <div>
                        <div style="font-size:12px;color:var(--text-muted);font-weight:600;">Tanggal &amp; Minggu Ke</div>
                        <div style="font-size:13.5px;font-weight:600;color:var(--text-primary);" id="detailTanggalMinggu">-</div>
                    </div>
                    <div>
                        <div style="font-size:12px;color:var(--text-muted);font-weight:600;">Status Berita Acara (BAP)</div>
                        <div style="font-size:13.5px;font-weight:700;" id="detailStatusBap">-</div>
                    </div>
                </div>
            </div>
            <div class="modal-footer" style="padding:16px 24px;background:var(--bg-body);border-top:1px solid rgba(0,40,85,0.06);display:flex;justify-content:flex-end;gap:10px;">
                <button type="button" class="btn btn-outline" onclick="window.PuprModal.close('modalDetailLaporan')">Tutup</button>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function openDetailModal(item) {
        document.getElementById('detailNamaKegiatan').innerText = item.nama_kegiatan || '-';
        document.getElementById('detailLokasiKegiatan').innerText = 'Kec. ' + (item.lokasi ? item.lokasi.replace('_', ' ') : '-') + ' • ' + (item.alamat || '-');
        document.getElementById('detailNilaiKontrak').innerText = item.nilai_kontrak ? 'Rp ' + new Intl.NumberFormat('id-ID').format(item.nilai_kontrak) : '-';
        document.getElementById('detailKontraktor').innerText = item.kontraktor || '-';
        document.getElementById('detailTanggalMinggu').innerText = (item.tanggal ? item.tanggal : '-') + ' (Minggu ke-' + (item.minggu || 1) + ')';
        
        const bapStatus = item.bap ? item.bap.status.toUpperCase() + ' (' + item.bap.nomor_bap + ')' : 'BELUM MEMILIKI BAP';
        document.getElementById('detailStatusBap').innerText = bapStatus;

        window.PuprModal.open('modalDetailLaporan');
    }
</script>
@endpush
