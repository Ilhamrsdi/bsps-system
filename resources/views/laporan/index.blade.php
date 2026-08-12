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
    .laporan-tab-item.active { background: #002855; color: #ffffff; font-weight: 700; shadow: 0 3px 10px rgba(0,40,85,0.2); }

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
        min-width: 180px;
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
        width: 100px;
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

    /* Responsive adjustments */
    @media (max-width: 1024px) {
        .stats-grid { grid-template-columns: repeat(2, 1fr); }
    }
    @media (max-width: 640px) {
        .stats-grid { grid-template-columns: 1fr; }
        .filter-grid { flex-direction: column; }
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
            <a href="{{ route('laporan', array_merge(request()->query(), ['tab' => 'rekap'])) }}" class="laporan-tab-item {{ $tab === 'rekap' ? 'active' : '' }}">
                <i class="fas fa-chart-pie"></i> Rekap Sesuai vs Tidak Sesuai (Per Desa/Kec)
            </a>
            <a href="{{ route('laporan', array_merge(request()->query(), ['tab' => 'indikator'])) }}" class="laporan-tab-item {{ $tab === 'indikator' ? 'active' : '' }}">
                <i class="fas fa-sliders"></i> Capaian Indikator RTLH
            </a>
            <a href="{{ route('laporan', array_merge(request()->query(), ['tab' => 'galeri'])) }}" class="laporan-tab-item {{ $tab === 'galeri' ? 'active' : '' }}">
                <i class="fas fa-images"></i> Galeri Foto Lapangan
            </a>
            <a href="{{ route('laporan', array_merge(request()->query(), ['tab' => 'detail'])) }}" class="laporan-tab-item {{ $tab === 'detail' ? 'active' : '' }}">
                <i class="fas fa-table-list"></i> Detail Data Penerima
            </a>
        </div>

        <!-- Filter & Search Section -->
        <div class="filter-card">
            <form action="{{ route('laporan') }}" method="GET" class="filter-grid">
                <input type="hidden" name="tab" value="{{ $tab }}" />

                <div class="filter-item" style="flex:2;min-width:220px;">
                    <div style="position:relative;">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama penerima, NIK, KK, desa..." />
                        <i class="fas fa-search" style="position:absolute;right:12px;top:50%;transform:translateY(-50%);color:#94a3b8;"></i>
                    </div>
                </div>

                {{-- Filter Kecamatan --}}
                @if(!auth()->check() || !auth()->user()->isAdminKecamatan())
                <div class="filter-item">
                    <select name="kecamatan" onchange="this.form.submit()">
                        <option value="all">-- Semua Kecamatan --</option>
                        @foreach($listKecamatan as $kec)
                            <option value="{{ $kec }}" {{ request('kecamatan') == $kec ? 'selected' : '' }}>Kec. {{ $kec }}</option>
                        @endforeach
                    </select>
                </div>
                @endif

                {{-- Filter Desa --}}
                <div class="filter-item">
                    <select name="desa" onchange="this.form.submit()">
                        <option value="all">-- Semua Desa / Kelurahan --</option>
                        @foreach($listDesa as $d)
                            <option value="{{ $d }}" {{ request('desa') == $d ? 'selected' : '' }}>Desa {{ $d }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Filter Status --}}
                <div class="filter-item">
                    <select name="status" onchange="this.form.submit()">
                        <option value="all">-- Semua Status --</option>
                        <option value="layak" {{ request('status') == 'layak' ? 'selected' : '' }}>Hasil Sesuai (Layak)</option>
                        <option value="tidak_layak" {{ request('status') == 'tidak_layak' ? 'selected' : '' }}>Hasil Tidak Sesuai</option>
                        <option value="sudah" {{ request('status') == 'sudah' ? 'selected' : '' }}>Sudah Survei</option>
                        <option value="belum" {{ request('status') == 'belum' ? 'selected' : '' }}>Belum Survei</option>
                    </select>
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
                <div style="display:flex;gap:8px;">
                    <a href="{{ route('laporan.export', request()->query()) }}" class="btn btn-success" style="padding:8px 14px;border-radius:8px;font-weight:700;text-decoration:none;">
                        <i class="fas fa-file-excel"></i> Export Excel (.XLS)
                    </a>
                    <a href="{{ route('laporan.cetak', request()->query()) }}" target="_blank" class="btn btn-primary" style="padding:8px 14px;border-radius:8px;font-weight:700;text-decoration:none;">
                        <i class="fas fa-print"></i> Cetak Laporan Resmi
                    </a>
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
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $sumTotal = 0; $sumSudah = 0; $sumBelum = 0; $sumLayak = 0; $sumTidakLayak = 0;
                            @endphp
                            @forelse($rekapDesaKecamatan as $index => $row)
                                @php
                                    $belumSurvei = max(0, $row->total_penerima - $row->total_sudah_survei);
                                    $pctSurvei = $row->total_penerima > 0 ? round(($row->total_sudah_survei / $row->total_penerima) * 100, 1) : 0;
                                    $pctKesesuaian = $row->total_sudah_survei > 0 ? round(($row->total_layak / $row->total_sudah_survei) * 100, 1) : 0;
                                    $sumTotal += $row->total_penerima;
                                    $sumSudah += $row->total_sudah_survei;
                                    $sumBelum += $belumSurvei;
                                    $sumLayak += $row->total_layak;
                                    $sumTidakLayak += $row->total_tidak_layak;
                                @endphp
                                <tr>
                                    <td style="text-align:center;">{{ $index + 1 }}</td>
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
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" style="text-align:center;padding:40px;color:#94a3b8;">
                                        <i class="fas fa-inbox" style="font-size:32px;display:block;margin-bottom:10px;opacity:0.4;"></i>
                                        Belum ada data rekapitulasi desa &amp; kecamatan.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        @if($rekapDesaKecamatan->count() > 0)
                        <tfoot style="background:#f8fafc;font-weight:800;border-top:2px solid #cbd5e1;">
                            <tr>
                                <td colspan="3" style="text-align:right;padding:14px;color:#002855;">TOTAL KESELURUHAN:</td>
                                <td style="text-align:center;color:#002855;">{{ number_format($sumTotal) }}</td>
                                <td style="text-align:center;color:#002855;">{{ number_format($sumSudah) }}</td>
                                <td style="text-align:center;color:#b78100;">{{ number_format($sumBelum) }}</td>
                                <td style="text-align:center;color:#27ae60;">{{ number_format($sumLayak) }}</td>
                                <td style="text-align:center;color:#e74c3c;">{{ number_format($sumTidakLayak) }}</td>
                                <td style="text-align:center;color:#002855;">
                                    {{ $sumTotal > 0 ? round(($sumSudah / $sumTotal) * 100, 1) : 0 }}%
                                </td>
                                <td style="text-align:center;color:#27ae60;">
                                    {{ $sumSudah > 0 ? round(($sumLayak / $sumSudah) * 100, 1) : 0 }}%
                                </td>
                            </tr>
                            </tr>
                        </tfoot>
                        @endif
                    </table>
                </div>

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
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($rekapIndikator as $index => $row)
                                <tr>
                                    <td style="text-align:center;">{{ $index + 1 }}</td>
                                    <td><strong style="color:#002855;">{{ $row->kecamatan }}</strong></td>
                                    <td><strong style="color:#0f172a;">{{ $row->desa_kelurahan }}</strong></td>
                                    <td style="text-align:center;font-weight:700;">{{ number_format($row->total_sudah_survei) }}</td>
                                    <td style="text-align:center;"><span class="badge-status danger">{{ number_format($row->atap_rtlh) }}</span></td>
                                    <td style="text-align:center;"><span class="badge-status danger">{{ number_format($row->dinding_rtlh) }}</span></td>
                                    <td style="text-align:center;"><span class="badge-status danger">{{ number_format($row->lantai_rtlh) }}</span></td>
                                    <td style="text-align:center;"><span class="badge-status danger">{{ number_format($row->pondasi_rtlh) }}</span></td>
                                    <td style="text-align:center;"><span class="badge-status danger">{{ number_format($row->struktur_rtlh) }}</span></td>
                                    <td style="text-align:center;"><span class="badge-status warning">{{ number_format($row->penghasilan_rtlh) }}</span></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" style="text-align:center;padding:40px;color:#94a3b8;">
                                        Belum ada data indikator RTLH.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

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
                <div style="padding:16px 24px;">
                    {{ $penerimaList->links() }}
                </div>

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
                                        Belum ada data detail penerima.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div style="padding:16px 24px;">
                    {{ $penerimaList->links() }}
                </div>
            @endif
        </div>
    </main>

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
