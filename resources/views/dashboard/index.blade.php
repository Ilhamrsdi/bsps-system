@extends('layouts.partial.app')

@section('title', 'BSPS Verval - Dashboard')
@section('title_header', 'Dashboard BSPS')
@section('subtitle_header', \Carbon\Carbon::now()->translatedFormat('l, d F Y') . ' - Analisis Data Calon Penerima BSPS Berdasarkan Pengelompokan Desil Kabupaten Jember')

@push('styles')
<style>
    /* Stats Grid */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 18px;
        margin-bottom: 24px;
    }

    .stat-card {
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

    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
    }

    .stat-card .stat-icon {
        width: 52px;
        height: 52px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        flex-shrink: 0;
    }

    .stat-card .stat-icon.blue   { background: rgba(0, 40, 85, 0.10); color: var(--primary); }
    .stat-card .stat-icon.green  { background: rgba(39, 174, 96, 0.12); color: var(--success); }
    .stat-card .stat-icon.orange { background: rgba(255, 184, 0, 0.16); color: #d69e00; }
    .stat-card .stat-icon.purple { background: rgba(142, 68, 173, 0.12); color: var(--purple, #8e44ad); }

    .stat-card .stat-info .stat-value {
        font-size: 24px;
        font-weight: 800;
        line-height: 1.1;
        color: var(--primary-dark);
    }

    .stat-card .stat-info .stat-label {
        font-size: 12.5px;
        color: var(--text-muted);
        font-weight: 600;
        margin-top: 3px;
    }

    .stat-card .stat-info .stat-change {
        font-size: 11px;
        font-weight: 700;
        margin-top: 4px;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 2px 6px;
        border-radius: 4px;
    }

    .stat-card .stat-info .stat-change.up {
        color: var(--success);
        background: rgba(39, 174, 96, 0.10);
    }

    .stat-card .stat-info .stat-change.blue {
        color: var(--primary);
        background: rgba(0, 40, 85, 0.08);
    }

    /* Chart Section */
    .chart-section {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 20px;
        margin-bottom: 24px;
    }

    .chart-card {
        background: var(--bg-card);
        border-radius: var(--radius);
        box-shadow: var(--shadow-sm);
        border: 1px solid rgba(0, 40, 85, 0.06);
        overflow: hidden;
        display: flex;
        flex-direction: column;
    }

    .chart-card .card-header {
        padding: 18px 24px;
        border-bottom: 1px solid rgba(0, 40, 85, 0.06);
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .chart-card .card-header h3 {
        font-size: 15px;
        font-weight: 800;
        color: var(--primary);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .chart-card .card-body {
        padding: 20px 24px;
        flex: 1;
    }

    .chart-wrapper {
        position: relative;
        height: 280px;
        width: 100%;
    }

    /* Dashboard Split Grid (Leaderboard & Breakdown) */
    .dashboard-split-grid {
        display: grid;
        grid-template-columns: 1.4fr 1fr;
        gap: 20px;
        margin-bottom: 24px;
    }

    .card-panel {
        background: var(--bg-card);
        border-radius: var(--radius);
        box-shadow: var(--shadow-sm);
        border: 1px solid rgba(0, 40, 85, 0.06);
        overflow: hidden;
    }

    .card-panel .panel-header {
        padding: 18px 22px;
        border-bottom: 1px solid rgba(0, 40, 85, 0.06);
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .card-panel .panel-header h3 {
        font-size: 15px;
        font-weight: 800;
        color: var(--primary);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .card-panel .panel-body {
        padding: 16px 22px;
    }

    /* Kecamatan Leaderboard Items */
    .kecamatan-list {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .kecamatan-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 6px 0;
    }

    .kec-rank {
        width: 26px;
        height: 26px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 11.5px;
        font-weight: 800;
        flex-shrink: 0;
        background: var(--bg-body);
        color: var(--text-muted);
    }

    .kec-rank.top-1 { background: #FFD700; color: #000; box-shadow: 0 2px 6px rgba(255,215,0,0.4); }
    .kec-rank.top-2 { background: #C0C0C0; color: #000; }
    .kec-rank.top-3 { background: #CD7F32; color: #fff; }

    .kec-info {
        flex: 1;
    }

    .kec-header {
        display: flex;
        justify-content: space-between;
        font-size: 13px;
        font-weight: 700;
        margin-bottom: 4px;
        color: var(--primary-dark);
    }

    .kec-desil-pills {
        display: flex;
        gap: 6px;
        margin-top: 4px;
        font-size: 11px;
    }

    .pill-desil-b1 {
        background: rgba(39, 174, 96, 0.12);
        color: var(--success);
        padding: 1px 6px;
        border-radius: 4px;
        font-weight: 600;
    }

    .pill-desil-b2 {
        background: rgba(0, 40, 85, 0.08);
        color: var(--primary);
        padding: 1px 6px;
        border-radius: 4px;
        font-weight: 600;
    }

    .kec-bar-wrap {
        width: 100%;
        height: 7px;
        background: var(--bg-body);
        border-radius: 10px;
        overflow: hidden;
        display: flex;
    }

    .kec-bar-fill-b2 {
        height: 100%;
        background: var(--primary);
    }

    .kec-bar-fill-b1 {
        height: 100%;
        background: var(--success);
    }

    /* Gender & Desil Mini Grid */
    .metric-mini-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
        margin-bottom: 16px;
    }

    .mini-metric-card {
        background: var(--bg-body);
        border-radius: 10px;
        padding: 14px;
        border: 1px solid rgba(0, 40, 85, 0.06);
    }

    .mini-metric-card .title {
        font-size: 11.5px;
        font-weight: 700;
        color: var(--text-muted);
        text-transform: uppercase;
        margin-bottom: 6px;
    }

    .mini-metric-card .num {
        font-size: 20px;
        font-weight: 800;
        color: var(--primary-dark);
    }

    .mini-metric-card .sub {
        font-size: 11px;
        color: var(--text-muted);
        margin-top: 2px;
    }

    /* Table Sample */
    .table-sample-card {
        background: var(--bg-card);
        border-radius: var(--radius);
        box-shadow: var(--shadow-sm);
        border: 1px solid rgba(0, 40, 85, 0.06);
        overflow: hidden;
    }

    .table-sample-wrapper {
        width: 100%;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    .table-sample-card table {
        width: 100%;
        min-width: 880px;
        border-collapse: collapse;
        white-space: nowrap;
    }

    .table-sample-card table th,
    .table-sample-card table td {
        white-space: nowrap;
    }

    .badge-gender {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 22px;
        height: 22px;
        border-radius: 6px;
        font-size: 11px;
        font-weight: 800;
    }
    .badge-gender.l { background: rgba(0, 40, 85, 0.10); color: var(--primary); }
    .badge-gender.p { background: rgba(212, 63, 120, 0.12); color: #d43f78; }

    .dashboard-hero-title-wrap {
        margin-bottom: 20px;
    }
    .dashboard-main-title {
        font-size: 22px;
        font-weight: 800;
        color: var(--primary-dark);
        margin-bottom: 6px;
        letter-spacing: -0.3px;
        line-height: 1.3;
    }
    .dashboard-main-subtitle {
        font-size: 13.5px;
        color: var(--text-muted);
        margin: 0;
        line-height: 1.5;
    }

    @media (max-width: 1100px) {
        .stats-grid { grid-template-columns: repeat(2, 1fr); gap: 14px; }
        .desil-banner-grid { grid-template-columns: 1fr; }
        .chart-section { grid-template-columns: 1fr; }
        .dashboard-split-grid { grid-template-columns: 1fr; }
    }

    @media (max-width: 768px) {
        .dashboard-hero-title-wrap { margin-bottom: 16px; }
        .dashboard-main-title { font-size: 18px; }
        .dashboard-main-subtitle { font-size: 12.5px; }
        .chart-wrapper { height: 240px; }
    }

    @media (max-width: 576px) {
        .stats-grid { grid-template-columns: 1fr; gap: 10px; }
        .stat-card { padding: 14px 16px; gap: 12px; border-radius: 12px; }
        .stat-card .stat-icon { width: 44px; height: 44px; font-size: 18px; border-radius: 10px; }
        .stat-card .stat-info .stat-value { font-size: 20px; }
        .stat-card .stat-info .stat-label { font-size: 11.5px; }
        .stat-card .stat-info .stat-change { font-size: 10.5px; }
        .metric-mini-grid { grid-template-columns: 1fr; }
        .card-panel .panel-body { padding: 14px 16px; }
    }
</style>
@endpush

@section('content')
    @include('layouts.navbar')

    <main class="dashboard-content">
        <!-- Greeting Header -->
        <div class="dashboard-hero-title-wrap">
            <h1 class="dashboard-main-title">
                Dashboard Analisis Desil BSPS Verval
            </h1>
            <p class="dashboard-main-subtitle">
                Sebaran data calon penerima Bantuan Stimulan Perumahan Swadaya berdasarkan kriteria <strong>Backlog 1 &amp; Backlog 2 Desil 1-4</strong> se-Kabupaten Jember.
            </p>
        </div>

        <!-- 6 Stat Summary Cards (Desain Seragam & Rapi) -->
        <div class="stats-grid">
            <!-- 1. Total Calon Penerima -->
            <div class="stat-card">
                <div class="stat-icon blue">
                    <i class="fas fa-users-viewfinder"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value">{{ number_format($totalPenerima, 0, ',', '.') }}</div>
                    <div class="stat-label">Total Calon Penerima</div>
                    <div class="stat-change blue">
                        <i class="fas fa-database"></i> Database Lengkap
                    </div>
                </div>
            </div>

            <!-- 2. Backlog 2 Desil 1-4 -->
            <div class="stat-card">
                <div class="stat-icon blue">
                    <i class="fas fa-layer-group"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value">{{ number_format($backlog2Count, 0, ',', '.') }}</div>
                    <div class="stat-label">Backlog 2 Desil 1-4</div>
                    <div class="stat-change blue">
                        <i class="fas fa-percent"></i> {{ round(($backlog2Count / $totalPenerima) * 100, 1) }}% Dominan (PK)
                    </div>
                </div>
            </div>

            <!-- 3. Backlog 1 Desil 1-4 -->
            <div class="stat-card">
                <div class="stat-icon green">
                    <i class="fas fa-house-chimney-crack"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value">{{ number_format($backlog1Count, 0, ',', '.') }}</div>
                    <div class="stat-label">Backlog 1 Desil 1-4</div>
                    <div class="stat-change up">
                        <i class="fas fa-award"></i> {{ round(($backlog1Count / $totalPenerima) * 100, 1) }}% Prioritas Bantuan
                    </div>
                </div>
            </div>

            <!-- 4. Kecamatan Terdata -->
            <div class="stat-card">
                <div class="stat-icon green">
                    <i class="fas fa-map-location-dot"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value">{{ $totalKecamatan }}</div>
                    <div class="stat-label">Kecamatan Terdata</div>
                    <div class="stat-change up">
                        <i class="fas fa-check-circle"></i> 100% Wilayah Jember
                    </div>
                </div>
            </div>

            <!-- 5. Desa / Kelurahan -->
            <div class="stat-card">
                <div class="stat-icon orange">
                    <i class="fas fa-city"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value">{{ $totalDesa }}</div>
                    <div class="stat-label">Desa / Kelurahan</div>
                    <div class="stat-change blue">
                        <i class="fas fa-house-chimney-user"></i> Titik Sebaran
                    </div>
                </div>
            </div>

            <!-- 6. Usulan Terbanyak -->
            <div class="stat-card">
                <div class="stat-icon purple">
                    <i class="fas fa-trophy"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value" style="font-size:18px;">
                        Kec. {{ ucwords(strtolower($topKecamatan[0]->kecamatan ?? 'Ledokombo')) }}
                    </div>
                    <div class="stat-label">Usulan Terbanyak ({{ number_format($topKecamatan[0]->total ?? 0, 0, ',', '.') }})</div>
                    <div class="stat-change up">
                        <i class="fas fa-arrow-trend-up"></i> Peringkat #1
                    </div>
                </div>
            </div>
        </div>

        <!-- Chart Section: Stacked Bar Desil per Kecamatan & Doughnut Chart -->
        <div class="chart-section">
            <div class="chart-card">
                <div class="card-header">
                    <h3><i class="fas fa-chart-column" style="color:var(--primary);"></i> Sebaran Backlog 1 &amp; Backlog 2 per Kecamatan</h3>
                    <div style="display:flex;gap:12px;font-size:12px;font-weight:700;">
                        <span><i class="fas fa-square" style="color:#002855;margin-right:4px;"></i> Backlog 2</span>
                        <span><i class="fas fa-square" style="color:#27ae60;margin-right:4px;"></i> Backlog 1</span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-wrapper">
                        <canvas id="stackedKecamatanDesilChart"></canvas>
                    </div>
                </div>
            </div>

            <div class="chart-card">
                <div class="card-header">
                    <h3><i class="fas fa-chart-pie" style="color:var(--secondary);"></i> Proporsi Desil</h3>
                    <span style="font-size:12px;color:var(--text-muted);font-weight:600;">{{ number_format($totalPenerima, 0, ',', '.') }} Data</span>
                </div>
                <div class="card-body">
                    <div class="chart-wrapper" style="height:210px;">
                        <canvas id="pieDesilChart"></canvas>
                    </div>
                    <div style="display:flex;justify-content:space-around;margin-top:14px;font-size:12px;">
                        <div style="text-align:center;">
                            <span style="display:inline-block;width:10px;height:10px;border-radius:50%;background:#002855;margin-right:4px;"></span>
                            <strong>Backlog 2:</strong> {{ number_format($backlog2Count, 0, ',', '.') }} ({{ round(($backlog2Count / $totalPenerima) * 100, 1) }}%)
                        </div>
                        <div style="text-align:center;">
                            <span style="display:inline-block;width:10px;height:10px;border-radius:50%;background:#27ae60;margin-right:4px;"></span>
                            <strong>Backlog 1:</strong> {{ number_format($backlog1Count, 0, ',', '.') }} ({{ round(($backlog1Count / $totalPenerima) * 100, 1) }}%)
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Leaderboard & Demographic Split Grid -->
        <div class="dashboard-split-grid">
            <!-- Left: Top 10 Kecamatan Leaderboard with Desil breakdown -->
            <div class="card-panel">
                <div class="panel-header">
                    <h3><i class="fas fa-ranking-star" style="color:#d69e00;"></i> Peringkat 10 Kecamatan &amp; Komposisi Desil</h3>
                    <span style="font-size:12px;color:var(--text-muted);font-weight:600;">Kabupaten Jember</span>
                </div>
                <div class="panel-body">
                    <div class="kecamatan-list">
                        @php
                            $maxKecVal = $topKecamatan->max('total') ?: 1;
                        @endphp
                        @foreach($topKecamatan as $idx => $kec)
                            @php
                                $rank = $idx + 1;
                                $pct = round(($kec->total / $totalPenerima) * 100, 1);
                                $b2Pct = round(($kec->backlog_2 / $maxKecVal) * 100);
                                $b1Pct = round(($kec->backlog_1 / $maxKecVal) * 100);
                            @endphp
                            <div class="kecamatan-item">
                                <div class="kec-rank {{ $rank == 1 ? 'top-1' : ($rank == 2 ? 'top-2' : ($rank == 3 ? 'top-3' : '')) }}">
                                    {{ $rank }}
                                </div>
                                <div class="kec-info">
                                    <div class="kec-header">
                                        <span>Kec. {{ ucwords(strtolower($kec->kecamatan)) }}</span>
                                        <span>{{ number_format($kec->total, 0, ',', '.') }} data <span style="font-size:11px;color:var(--text-muted);font-weight:500;">({{ $pct }}%)</span></span>
                                    </div>
                                    <div class="kec-bar-wrap">
                                        <div class="kec-bar-fill-b2" style="width: {{ $b2Pct }}%;" title="Backlog 2: {{ $kec->backlog_2 }}"></div>
                                        <div class="kec-bar-fill-b1" style="width: {{ $b1Pct }}%;" title="Backlog 1: {{ $kec->backlog_1 }}"></div>
                                    </div>
                                    <div class="kec-desil-pills">
                                        <span class="pill-desil-b2">Backlog 2: {{ number_format($kec->backlog_2, 0, ',', '.') }}</span>
                                        <span class="pill-desil-b1">Backlog 1: {{ number_format($kec->backlog_1, 0, ',', '.') }}</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Right: Demografi & Top Desa -->
            <div>
                <!-- Demografi Kepala Keluarga (Gender) -->
                <div class="card-panel" style="margin-bottom:20px;">
                    <div class="panel-header">
                        <h3><i class="fas fa-venus-mars" style="color:var(--primary);"></i> Kepala Keluarga Berdasarkan Gender</h3>
                    </div>
                    <div class="panel-body">
                        <div class="metric-mini-grid">
                            <div class="mini-metric-card">
                                <div class="title"><i class="fas fa-mars" style="color:var(--primary);"></i> Laki-Laki (L)</div>
                                <div class="num">{{ number_format($lakiCount, 0, ',', '.') }}</div>
                                <div class="sub">{{ round(($lakiCount / $totalPenerima) * 100, 1) }}% dari total penerima</div>
                            </div>
                            <div class="mini-metric-card">
                                <div class="title"><i class="fas fa-venus" style="color:#d43f78;"></i> Perempuan (P)</div>
                                <div class="num">{{ number_format($perempuanCount, 0, ',', '.') }}</div>
                                <div class="sub">{{ round(($perempuanCount / $totalPenerima) * 100, 1) }}% dari total penerima</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Top 6 Desa / Kelurahan -->
                <div class="card-panel">
                    <div class="panel-header">
                        <h3><i class="fas fa-tree-city" style="color:var(--success);"></i> Top Desa / Kelurahan Terbanyak</h3>
                    </div>
                    <div class="panel-body" style="padding:10px 22px;">
                        @foreach($topDesa as $desa)
                            <div style="display:flex;align-items:center;justify-content:space-between;padding:10px 0;border-bottom:1px solid rgba(0,40,85,0.05);font-size:13px;">
                                <div>
                                    <div style="font-weight:700;color:var(--primary-dark);">Desa {{ ucwords(strtolower($desa->desa_kelurahan)) }}</div>
                                    <div style="font-size:11px;color:var(--text-muted);">Kec. {{ ucwords(strtolower($desa->kecamatan)) }}</div>
                                </div>
                                <span style="font-weight:800;color:var(--primary);background:rgba(0,40,85,0.06);padding:4px 10px;border-radius:12px;font-size:12px;">
                                    {{ number_format($desa->total, 0, ',', '.') }} KK
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Sampel Calon Penerima Terbaru -->
        <div class="table-sample-card">
            <div class="panel-header" style="padding:18px 24px;border-bottom:1px solid rgba(0,40,85,0.06);display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;">
                <h3 style="font-size:15px;font-weight:800;color:var(--primary);margin:0;display:flex;align-items:center;gap:8px;">
                    <i class="fas fa-clipboard-list"></i> Sampel Usulan Calon Penerima BSPS Terdaftar
                </h3>
                <a href="{{ url('/verval-data') }}" class="btn btn-primary" style="padding:8px 16px;font-size:12.5px;font-weight:700;background:var(--primary);color:#fff;text-decoration:none;border-radius:var(--radius-sm);display:inline-flex;align-items:center;gap:6px;">
                    Buka Semua Data Verval ({{ number_format($totalPenerima, 0, ',', '.') }}) <i class="fas fa-arrow-right"></i>
                </a>
            </div>

            <div class="table-sample-wrapper">
                <table class="table" style="width:100%;border-collapse:collapse;min-width:880px;">
                    <thead>
                        <tr style="background:var(--bg-body);border-bottom:1px solid rgba(0,40,85,0.08);text-align:left;font-size:12px;color:var(--text-muted);">
                            <th style="padding:12px 18px;">Nama Calon Penerima</th>
                            <th style="padding:12px 18px;text-align:center;">L/P</th>
                            <th style="padding:12px 18px;">NIK &amp; KK</th>
                            <th style="padding:12px 18px;">Alamat</th>
                            <th style="padding:12px 18px;">Desa / Kelurahan</th>
                            <th style="padding:12px 18px;">Kecamatan</th>
                            <th style="padding:12px 18px;">Kelompok Desil</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($latestCandidates as $cand)
                            <tr style="border-bottom:1px solid rgba(0,40,85,0.05);font-size:13px;">
                                <td style="padding:12px 18px;font-weight:800;color:var(--primary-dark);">
                                    {{ $cand->nama }}
                                </td>
                                <td style="padding:12px 18px;text-align:center;">
                                    <span class="badge-gender {{ strtolower($cand->jenis_kelamin) }}">{{ $cand->jenis_kelamin }}</span>
                                </td>
                                <td style="padding:12px 18px;font-family:monospace;font-size:12px;">
                                    <div><strong>NIK:</strong> {{ $cand->no_ktp }}</div>
                                    <div style="color:var(--text-muted);"><strong>KK:</strong> {{ $cand->no_kk }}</div>
                                </td>
                                <td style="padding:12px 18px;color:var(--text-secondary);font-size:12.5px;">
                                    {{ $cand->alamat }}
                                </td>
                                <td style="padding:12px 18px;font-weight:600;">
                                    {{ $cand->desa_kelurahan }}
                                </td>
                                <td style="padding:12px 18px;font-weight:700;color:var(--primary);">
                                    Kec. {{ $cand->kecamatan }}
                                </td>
                                <td style="padding:12px 18px;">
                                    <span style="font-size:11.5px;font-weight:700;padding:3px 8px;border-radius:12px;background:rgba(0,40,85,0.06);color:var(--primary-dark);">
                                        {{ $cand->pengelompokan_desil }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </main>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Stacked Bar Chart: Desil per Kecamatan
    const ctxBar = document.getElementById('stackedKecamatanDesilChart');
    if (ctxBar) {
        new Chart(ctxBar, {
            type: 'bar',
            data: {
                labels: {!! json_encode($chartKecamatanLabels) !!},
                datasets: [
                    {
                        label: 'Backlog 2 Desil 1-4',
                        data: {!! json_encode($chartBacklog2Data) !!},
                        backgroundColor: '#002855',
                        borderRadius: 4,
                    },
                    {
                        label: 'Backlog 1 Desil 1-4',
                        data: {!! json_encode($chartBacklog1Data) !!},
                        backgroundColor: '#27ae60',
                        borderRadius: 4,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        callbacks: {
                            label: function(ctx) {
                                return ctx.dataset.label + ': ' + ctx.parsed.y.toLocaleString('id-ID') + ' KK';
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        stacked: true,
                        grid: { display: false }
                    },
                    y: {
                        stacked: true,
                        beginAtZero: true,
                        grid: { color: 'rgba(0,40,85,0.06)' },
                        ticks: {
                            callback: function(val) { return val.toLocaleString('id-ID'); }
                        }
                    }
                }
            }
        });
    }

    // 2. Proporsi Desil Doughnut Chart
    const ctxPie = document.getElementById('pieDesilChart');
    if (ctxPie) {
        new Chart(ctxPie, {
            type: 'doughnut',
            data: {
                labels: ['Backlog 2 Desil 1-4', 'Backlog 1 Desil 1-4'],
                datasets: [{
                    data: [{{ $backlog2Count }}, {{ $backlog1Count }}],
                    backgroundColor: ['#002855', '#27ae60'],
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
                                const total = {{ $totalPenerima }};
                                const val = ctx.parsed;
                                const pct = ((val / total) * 100).toFixed(1);
                                return ctx.label + ': ' + val.toLocaleString('id-ID') + ' (' + pct + '%)';
                            }
                        }
                    }
                },
                cutout: '68%'
            }
        });
    }
});
</script>
@endpush