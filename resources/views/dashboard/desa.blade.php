@extends('layouts.partial.app')

@section('title', 'Monitoring Desa ' . ucwords(strtolower($desaParam)) . ' - BSPS Verval')
@section('title_header', 'Monitoring Verval Desa ' . ucwords(strtolower($desaParam)))
@section('subtitle_header', 'Kecamatan ' . ucwords(strtolower($kecamatanParam)) . ', Kabupaten Jember — Capaian Target & Hasil Kelayakan Lapangan')

@push('styles')
<style>
    /* Header Card Desa */
    .desa-hero-card {
        background: linear-gradient(135deg, #002855 0%, #003b7a 100%);
        border-radius: var(--radius);
        padding: 22px 26px;
        color: #ffffff;
        margin-bottom: 22px;
        box-shadow: 0 8px 24px rgba(0, 40, 85, 0.16);
    }

    .desa-hero-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
        margin-bottom: 20px;
    }

    .desa-title-area h2 {
        font-size: 20px;
        font-weight: 900;
        margin: 0 0 4px 0;
        display: flex;
        align-items: center;
        gap: 10px;
        color: #ffffff;
    }

    .desa-title-area p {
        font-size: 13px;
        color: rgba(255, 255, 255, 0.8);
        margin: 0;
    }

    /* Switcher Dropdowns */
    .desa-switcher-form {
        display: flex;
        gap: 10px;
        align-items: center;
        flex-wrap: wrap;
    }

    .desa-switcher-select {
        padding: 8px 14px;
        border-radius: 8px;
        border: 1px solid rgba(255, 255, 255, 0.25);
        background: rgba(255, 255, 255, 0.15);
        color: #ffffff;
        font-size: 13px;
        font-weight: 700;
        outline: none;
        cursor: pointer;
    }

    .desa-switcher-select option {
        background: #002855;
        color: #ffffff;
    }

    /* Progress & Metrics Grid */
    .desa-progress-overview {
        background: rgba(255, 255, 255, 0.08);
        border: 1px solid rgba(255, 255, 255, 0.12);
        border-radius: 12px;
        padding: 16px 20px;
    }

    .desa-prog-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 8px;
        font-size: 13px;
    }

    .desa-prog-bar-bg {
        width: 100%;
        height: 10px;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 6px;
        overflow: hidden;
        margin-bottom: 16px;
    }

    .desa-prog-bar-fill {
        height: 100%;
        background: linear-gradient(90deg, #ffb800 0%, #27ae60 100%);
        border-radius: 6px;
        transition: width 0.8s ease;
    }

    .desa-hero-metrics {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(130px, 1fr));
        gap: 12px;
    }

    .desa-metric-chip {
        background: rgba(0, 0, 0, 0.2);
        padding: 10px 14px;
        border-radius: 10px;
        display: flex;
        flex-direction: column;
        gap: 2px;
    }

    .desa-metric-chip .chip-lbl {
        font-size: 11px;
        color: rgba(255, 255, 255, 0.7);
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .desa-metric-chip .chip-val {
        font-size: 17px;
        font-weight: 900;
        color: #ffffff;
    }

    /* 2 Executive Kelayakan Cards */
    .kelayakan-action-card {
        border-radius: 14px;
        padding: 20px 22px;
        background: #ffffff;
        border: 2px solid #e2e8f0;
        transition: all 0.28s cubic-bezier(0.4, 0, 0.2, 1);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        gap: 14px;
        position: relative;
        overflow: hidden;
        text-decoration: none;
    }
    .kelayakan-action-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 28px rgba(0, 40, 85, 0.12);
    }
    .kelayakan-action-card.card-layak {
        border-color: #86efac;
        background: linear-gradient(180deg, #f0fdf4 0%, #ffffff 100%);
    }
    .kelayakan-action-card.card-layak:hover {
        border-color: #22c55e;
        box-shadow: 0 12px 28px rgba(34, 197, 94, 0.18);
    }
    .kelayakan-action-card.card-tidak {
        border-color: #fca5a5;
        background: linear-gradient(180deg, #fef2f2 0%, #ffffff 100%);
    }
    .kelayakan-action-card.card-tidak:hover {
        border-color: #ef4444;
        box-shadow: 0 12px 28px rgba(239, 68, 68, 0.18);
    }
    .card-top-icon {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .card-top-icon .icon-circle {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
    }
    .icon-green { background: #dcfce7; color: #16a34a; }
    .icon-red { background: #fee2e2; color: #dc2626; }
    .badge-status-pill {
        font-size: 11px;
        font-weight: 800;
        padding: 4px 10px;
        border-radius: 20px;
    }
    .pill-green { background: #dcfce7; color: #15803d; border: 1px solid #86efac; }
    .pill-red { background: #fee2e2; color: #b91c1c; border: 1px solid #fca5a5; }
    .card-main-metric .metric-val {
        font-size: 28px;
        font-weight: 900;
        letter-spacing: -0.5px;
        line-height: 1.1;
    }
    .card-main-metric .unit { font-size: 16px; font-weight: 700; opacity: 0.8; }
    .text-green { color: #15803d; }
    .text-red { color: #b91c1c; }
    .card-main-metric .metric-title {
        font-size: 12px;
        font-weight: 800;
        color: #64748b;
        letter-spacing: 0.5px;
        margin-top: 4px;
        text-transform: uppercase;
    }
    .card-footer-info {
        display: flex;
        align-items: center;
        justify-content: space-between;
        border-top: 1px solid rgba(0, 0, 0, 0.06);
        padding-top: 12px;
        font-size: 12px;
    }
    .card-footer-info .pct-text {
        color: #475569;
        font-weight: 600;
    }
    .btn-goto-table {
        font-weight: 800;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 12px;
    }

    /* Status Filter Pills */
    .kelayakan-filter-pills {
        display: flex;
        gap: 10px;
        margin-bottom: 20px;
        flex-wrap: wrap;
    }

    .kelayakan-pill-btn {
        padding: 10px 18px;
        border-radius: 10px;
        background: #ffffff;
        border: 1.5px solid #e2e8f0;
        color: #475569;
        font-weight: 700;
        font-size: 13px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.25s ease;
        box-shadow: 0 2px 6px rgba(0, 40, 85, 0.04);
    }

    .kelayakan-pill-btn:hover {
        border-color: #cbd5e1;
        transform: translateY(-1px);
    }

    .kelayakan-pill-btn.active.pill-layak {
        background: #15803d;
        color: #ffffff;
        border-color: #15803d;
        box-shadow: 0 4px 14px rgba(21, 128, 61, 0.25);
    }

    .kelayakan-pill-btn.active.pill-tidak {
        background: #b91c1c;
        color: #ffffff;
        border-color: #b91c1c;
        box-shadow: 0 4px 14px rgba(185, 28, 28, 0.25);
    }

    .kelayakan-pill-btn.active.pill-belum {
        background: #d97706;
        color: #ffffff;
        border-color: #d97706;
        box-shadow: 0 4px 14px rgba(217, 119, 6, 0.25);
    }

    .kelayakan-pill-btn.active.pill-all {
        background: #002855;
        color: #ffffff;
        border-color: #002855;
        box-shadow: 0 4px 14px rgba(0, 40, 85, 0.25);
    }

    .kelayakan-pill-btn .badge-pill-count {
        font-size: 11px;
        font-weight: 800;
        padding: 2px 7px;
        border-radius: 10px;
        background: rgba(0, 40, 85, 0.08);
        color: inherit;
    }

    .kelayakan-pill-btn.active .badge-pill-count {
        background: rgba(255, 255, 255, 0.25);
        color: #ffffff;
    }

    /* Indikator mini pills inside table */
    .ind-pill {
        display: inline-block;
        padding: 2px 6px;
        border-radius: 4px;
        font-size: 10px;
        font-weight: 700;
        margin: 1px;
    }
    .ind-pill.rusak { background: #fee2e2; color: #b91c1c; border: 1px solid #fca5a5; }
    .ind-pill.baik { background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0; }
</style>
@endpush

@section('content')
    @include('layouts.navbar')

    <main class="dashboard-content">
        <!-- Breadcrumb -->
        <div class="breadcrumb" style="margin-bottom: 16px; font-size: 13px; color: var(--text-muted); display: flex; align-items: center; gap: 8px;">
            <a href="{{ route('dashboard') }}" style="color: var(--primary); text-decoration: none; font-weight: 600;"><i class="fas fa-th-large"></i> Dashboard Global</a>
            <i class="fas fa-chevron-right" style="font-size: 10px;"></i>
            <a href="{{ url('/dashboard-kecamatan?kecamatan=' . urlencode($kecamatanParam)) }}" style="color: var(--primary); text-decoration: none; font-weight: 600;">Kec. {{ ucwords(strtolower($kecamatanParam)) }}</a>
            <i class="fas fa-chevron-right" style="font-size: 10px;"></i>
            <span>Desa {{ ucwords(strtolower($desaParam)) }}</span>
        </div>

        <!-- Hero Progress Card Desa -->
        <div class="desa-hero-card">
            <div class="desa-hero-top">
                <div class="desa-title-area">
                    <h2>
                        <i class="fas fa-building-user" style="color: #ffb800;"></i>
                        <span>Desa {{ ucwords(strtolower($desaParam)) }}</span>
                    </h2>
                    <p>
                        Kecamatan <strong>{{ ucwords(strtolower($kecamatanParam)) }}</strong>, Kabupaten Jember &bull; Monitoring Verval Data RTLH &amp; Kelayakan Bantuan BSPS
                    </p>
                </div>

                <!-- Form Ganti Kecamatan / Desa Cepat -->
                <form action="{{ route('dashboard.desa') }}" method="GET" class="desa-switcher-form" id="desaSwitcherForm">
                    @if(!auth()->check() || !auth()->user()->isAdminKecamatan())
                    <select name="kecamatan" class="desa-switcher-select" onchange="this.form.submit()">
                        @foreach($listKecamatan as $k)
                            <option value="{{ $k }}" {{ strtolower($kecamatanParam) === strtolower($k) ? 'selected' : '' }}>
                                Kec. {{ ucwords(strtolower($k)) }}
                            </option>
                        @endforeach
                    </select>
                    @else
                    <input type="hidden" name="kecamatan" value="{{ $kecamatanParam }}" />
                    @endif

                    <select name="desa" class="desa-switcher-select" onchange="this.form.submit()">
                        @foreach($listDesaInKec as $d)
                            <option value="{{ $d }}" {{ strtolower($desaParam) === strtolower($d) ? 'selected' : '' }}>
                                Desa {{ ucwords(strtolower($d)) }}
                            </option>
                        @endforeach
                    </select>
                </form>
            </div>

            <!-- Visual Progress Bar & 4 Metrik Utama Desa -->
            <div class="desa-progress-overview">
                <div class="desa-prog-top">
                    <span><strong>Progres Capaian Verval Desa:</strong> {{ number_format($totalSudah) }} dari {{ number_format($totalTarget) }} KK Selesai</span>
                    <strong style="color: #ffb800; font-size: 15px;">{{ $progresPercent }}%</strong>
                </div>
                <div class="desa-prog-bar-bg">
                    <div class="desa-prog-bar-fill" style="width: {{ $progresPercent }}%;"></div>
                </div>

                <div class="desa-hero-metrics">
                    <div class="desa-metric-chip">
                        <span class="chip-lbl"><i class="fas fa-users"></i> Target Desa</span>
                        <span class="chip-val">{{ number_format($totalTarget) }} <small style="font-size: 11px; font-weight: normal;">KK</small></span>
                    </div>
                    <div class="desa-metric-chip">
                        <span class="chip-lbl"><i class="fas fa-clipboard-check"></i> Sudah Survei</span>
                        <span class="chip-val" style="color: #4ade80;">{{ number_format($totalSudah) }} <small style="font-size: 11px; font-weight: normal;">({{ $progresPercent }}%)</small></span>
                    </div>
                    <div class="desa-metric-chip">
                        <span class="chip-lbl"><i class="fas fa-clock"></i> Belum Survei</span>
                        <span class="chip-val" style="color: #fcd34d;">{{ number_format($totalBelum) }} <small style="font-size: 11px; font-weight: normal;">KK</small></span>
                    </div>
                    <div class="desa-metric-chip">
                        <span class="chip-lbl"><i class="fas fa-layer-group"></i> Backlog 1 & 2</span>
                        <span class="chip-val">{{ $backlog1 }} B1 &bull; {{ $backlog2 }} B2</span>
                    </div>
                    <div class="desa-metric-chip">
                        <span class="chip-lbl"><i class="fas fa-user-hard-hat"></i> Petugas Desa</span>
                        <span class="chip-val">{{ $petugasList->count() }} <small style="font-size: 11px; font-weight: normal;">Orang</small></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2 CARD EKSEKUTIF UTAMA: KELAYAKAN TINGKAT DESA -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 18px; margin-bottom: 22px;">
            <!-- 1. CARD LAYAK DIUSULKAN (DESA) -->
            <a href="{{ route('dashboard.desa', ['kecamatan' => $kecamatanParam, 'desa' => $desaParam, 'status' => 'layak']) }}"
               class="kelayakan-action-card card-layak {{ $status === 'layak' ? 'active-ring' : '' }}"
               title="Klik untuk memfilter daftar warga desa yang Layak Diusulkan">
                <div class="card-top-icon">
                    <div class="icon-circle icon-green">
                        <i class="fas fa-circle-check"></i>
                    </div>
                    <span class="badge-status-pill pill-green">
                        <i class="fas fa-award"></i> Memenuhi Kriteria (≥2 RTLH)
                    </span>
                </div>
                <div class="card-main-metric">
                    <div class="metric-val text-green">{{ number_format($totalLayak) }} <span class="unit">KK</span></div>
                    <div class="metric-title">CALON PENERIMA LAYAK DI DESA {{ strtoupper($desaParam) }}</div>
                </div>
                <div class="card-footer-info">
                    <div class="pct-text">
                        <strong>{{ $persenLayak }}%</strong> dari total yang sudah disurvei di desa ini
                    </div>
                    <div class="btn-goto-table text-green">
                        <span>Tampilkan di Tabel</span>
                        <i class="fas fa-arrow-down"></i>
                    </div>
                </div>
            </a>

            <!-- 2. CARD TIDAK LAYAK DIUSULKAN (DESA) -->
            <a href="{{ route('dashboard.desa', ['kecamatan' => $kecamatanParam, 'desa' => $desaParam, 'status' => 'tidak_layak']) }}"
               class="kelayakan-action-card card-tidak {{ $status === 'tidak_layak' ? 'active-ring' : '' }}"
               title="Klik untuk memfilter daftar warga desa yang Tidak Layak">
                <div class="card-top-icon">
                    <div class="icon-circle icon-red">
                        <i class="fas fa-circle-xmark"></i>
                    </div>
                    <span class="badge-status-pill pill-red">
                        <i class="fas fa-circle-exclamation"></i> Tidak Memenuhi (&lt;2 RTLH)
                    </span>
                </div>
                <div class="card-main-metric">
                    <div class="metric-val text-red">{{ number_format($totalTidakLayak) }} <span class="unit">KK</span></div>
                    <div class="metric-title">CALON PENERIMA TIDAK LAYAK DI DESA {{ strtoupper($desaParam) }}</div>
                </div>
                <div class="card-footer-info">
                    <div class="pct-text">
                        <strong>{{ $persenTidak }}%</strong> dari total yang sudah disurvei di desa ini
                    </div>
                    <div class="btn-goto-table text-red">
                        <span>Tampilkan di Tabel</span>
                        <i class="fas fa-arrow-down"></i>
                    </div>
                </div>
            </a>
        </div>

        <!-- 4 Tab Switcher Status Warga Desa -->
        <div class="kelayakan-filter-pills">
            <a href="{{ route('dashboard.desa', ['kecamatan' => $kecamatanParam, 'desa' => $desaParam, 'status' => 'all']) }}"
               class="kelayakan-pill-btn pill-all {{ $status === 'all' ? 'active' : '' }}">
                <i class="fas fa-users"></i>
                <span>Semua Target Desa</span>
                <span class="badge-pill-count">{{ number_format($totalTarget) }} KK</span>
            </a>

            <a href="{{ route('dashboard.desa', ['kecamatan' => $kecamatanParam, 'desa' => $desaParam, 'status' => 'layak']) }}"
               class="kelayakan-pill-btn pill-layak {{ $status === 'layak' ? 'active' : '' }}">
                <i class="fas fa-circle-check"></i>
                <span>Layak Diusulkan</span>
                <span class="badge-pill-count">{{ number_format($totalLayak) }} KK</span>
            </a>

            <a href="{{ route('dashboard.desa', ['kecamatan' => $kecamatanParam, 'desa' => $desaParam, 'status' => 'tidak_layak']) }}"
               class="kelayakan-pill-btn pill-tidak {{ $status === 'tidak_layak' ? 'active' : '' }}">
                <i class="fas fa-circle-xmark"></i>
                <span>Tidak Layak</span>
                <span class="badge-pill-count">{{ number_format($totalTidakLayak) }} KK</span>
            </a>

            <a href="{{ route('dashboard.desa', ['kecamatan' => $kecamatanParam, 'desa' => $desaParam, 'status' => 'belum']) }}"
               class="kelayakan-pill-btn pill-belum {{ $status === 'belum' ? 'active' : '' }}">
                <i class="fas fa-clock"></i>
                <span>Belum Disurvei</span>
                <span class="badge-pill-count">{{ number_format($totalBelum) }} KK</span>
            </a>
        </div>

        <!-- Tabel BNBA Khusus Warga Desa -->
        <div class="table-card">
            <div class="table-header" style="padding: 16px 20px; border-bottom: 1px solid rgba(0, 40, 85, 0.08); display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap;">
                <h3 style="margin: 0; font-size: 15px; font-weight: 800; color: #002855; display: flex; align-items: center; gap: 8px;">
                    <i class="fas fa-list-check" style="color: #ffb800;"></i>
                    <span>Daftar Calon Penerima Desa {{ ucwords(strtolower($desaParam)) }}</span>
                    <span style="font-size: 13px; color: #64748b; font-weight: 600;">({{ number_format($penerimaList->total()) }} data)</span>
                </h3>

                <!-- Search & Actions -->
                <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
                    <form action="{{ route('dashboard.desa') }}" method="GET" style="display: flex; gap: 6px; margin: 0;">
                        <input type="hidden" name="kecamatan" value="{{ $kecamatanParam }}" />
                        <input type="hidden" name="desa" value="{{ $desaParam }}" />
                        <input type="hidden" name="status" value="{{ $status }}" />

                        <div style="position: relative;">
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama / NIK..." style="padding: 6px 12px 6px 30px; border-radius: 6px; border: 1px solid #cbd5e1; font-size: 12.5px; outline: none;" />
                            <i class="fas fa-search" style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 11px;"></i>
                        </div>

                        <button type="submit" class="btn btn-primary" style="padding: 6px 12px; font-size: 12px; font-weight: 700; border-radius: 6px; border: none; background: #002855; color: #fff; cursor: pointer;">
                            Cari
                        </button>
                    </form>

                    <a href="{{ route('laporan.export', ['kecamatan' => $kecamatanParam, 'desa' => $desaParam, 'status' => $status]) }}" class="btn btn-outline" style="padding: 6px 14px; font-size: 12px; font-weight: 700; border-radius: 6px; text-decoration: none; border: 1px solid #107c41; color: #107c41; background: #ffffff; display: inline-flex; align-items: center; gap: 6px;">
                        <i class="fas fa-file-excel"></i> Export Excel
                    </a>
                </div>
            </div>

            <div class="table-wrapper">
                <table class="pupr-table">
                    <thead>
                        <tr>
                            <th style="width: 45px; text-align: center;">No</th>
                            <th style="min-width: 180px;">Nama Calon Penerima</th>
                            <th style="min-width: 160px;">NIK / No. KK</th>
                            <th style="min-width: 160px;">Alamat / Dusun</th>
                            <th style="min-width: 120px;">Desil</th>
                            <th style="min-width: 200px;">Capaian Indikator RTLH</th>
                            <th style="min-width: 140px; text-align: center;">Status Kelayakan</th>
                            <th style="min-width: 140px;">Petugas Lapangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($penerimaList as $index => $row)
                        <tr>
                            <td style="text-align: center;">{{ $penerimaList->firstItem() + $index }}</td>
                            <td>
                                <strong style="color: #002855; font-size: 13.5px; display: block;">{{ $row->nama }}</strong>
                                <span style="font-size: 11px; color: #64748b;">
                                    <i class="fas fa-clock"></i> {{ $row->updated_at ? $row->updated_at->setTimezone('Asia/Jakarta')->translatedFormat('d M Y, H:i') : '-' }} WIB
                                </span>
                            </td>
                            <td>
                                <div style="font-family: monospace; font-size: 12.5px; font-weight: 700; color: #0f172a;">{{ $row->no_ktp ?: '-' }}</div>
                                <div style="font-family: monospace; font-size: 11px; color: #64748b;">KK: {{ $row->no_kk ?: '-' }}</div>
                            </td>
                            <td>
                                <div style="font-size: 12px; color: #0f172a; font-weight: 600;">
                                    {{ $row->alamat ?: 'Dusun / RT / RW -' }}
                                </div>
                            </td>
                            <td>
                                <span style="font-size: 11px; font-weight: 700; padding: 3px 8px; border-radius: 6px; background: rgba(0, 40, 85, 0.08); color: #002855;">
                                    {{ $row->pengelompokan_desil ?: '-' }}
                                </span>
                            </td>
                            <td>
                                <div style="display: flex; flex-wrap: wrap; gap: 2px;">
                                    <span class="ind-pill {{ $row->indikator_atap === 'tidak_ada' ? 'rusak' : 'baik' }}" title="Atap">
                                        Atap: {{ $row->indikator_atap === 'tidak_ada' ? 'Rusak' : 'Baik' }}
                                    </span>
                                    <span class="ind-pill {{ $row->indikator_dinding === 'tidak_ada' ? 'rusak' : 'baik' }}" title="Dinding">
                                        Dinding: {{ $row->indikator_dinding === 'tidak_ada' ? 'Rusak' : 'Baik' }}
                                    </span>
                                    <span class="ind-pill {{ $row->indikator_lantai === 'tidak_ada' ? 'rusak' : 'baik' }}" title="Lantai">
                                        Lantai: {{ $row->indikator_lantai === 'tidak_ada' ? 'Tanah' : 'Baik' }}
                                    </span>
                                    <span class="ind-pill {{ $row->indikator_pondasi === 'tidak_ada' ? 'rusak' : 'baik' }}" title="Pondasi">
                                        Pondasi: {{ $row->indikator_pondasi === 'tidak_ada' ? 'Rusak' : 'Baik' }}
                                    </span>
                                    <span class="ind-pill {{ $row->indikator_struktur === 'tidak_ada' ? 'rusak' : 'baik' }}" title="Struktur">
                                        Struktur: {{ $row->indikator_struktur === 'tidak_ada' ? 'Rusak' : 'Baik' }}
                                    </span>
                                </div>
                            </td>
                            <td style="text-align: center;">
                                @if($row->status_kelayakan === 'Layak Diusulkan')
                                    <span style="font-size: 11px; font-weight: 800; padding: 4px 10px; border-radius: 12px; background: #dcfce7; color: #15803d; border: 1px solid #86efac; display: inline-flex; align-items: center; gap: 4px;">
                                        <i class="fas fa-check-circle"></i> Layak Diusulkan
                                    </span>
                                @elseif($row->status_kelayakan === 'Tidak Layak Diusulkan')
                                    <span style="font-size: 11px; font-weight: 800; padding: 4px 10px; border-radius: 12px; background: #fee2e2; color: #b91c1c; border: 1px solid #fca5a5; display: inline-flex; align-items: center; gap: 4px;">
                                        <i class="fas fa-times-circle"></i> Tidak Layak
                                    </span>
                                @else
                                    <span style="font-size: 11px; font-weight: 700; padding: 3px 8px; border-radius: 12px; background: #fef3c7; color: #b45309; border: 1px solid #fde68a;">
                                        <i class="fas fa-clock"></i> Belum Survei
                                    </span>
                                @endif
                            </td>
                            <td>
                                <div style="font-size: 12px; font-weight: 700; color: #002855;">
                                    <i class="fas fa-user-hard-hat" style="color: #d69e00; font-size: 11px;"></i>
                                    {{ $row->petugas ? $row->petugas->name : 'Petugas Lapangan' }}
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" style="text-align: center; padding: 40px; color: #94a3b8;">
                                <i class="fas fa-inbox" style="font-size: 32px; display: block; margin-bottom: 10px; opacity: 0.4;"></i>
                                Tidak ada data penerima yang cocok dengan kriteria pencarian/filter di desa ini.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination Bar Custom -->
            @if($penerimaList->hasPages() || $penerimaList->total() > 0)
            <div class="pagination-custom-bar" style="padding: 14px 20px; border-top: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px; background: #f8fafc;">
                <div class="pagination-info-text" style="font-size: 12.5px; color: #64748b;">
                    Menampilkan <strong>{{ $penerimaList->firstItem() ?? 0 }}</strong> -
                    <strong>{{ $penerimaList->lastItem() ?? 0 }}</strong> dari
                    <strong>{{ number_format($penerimaList->total(), 0, ',', '.') }}</strong> calon penerima
                    @if($penerimaList->lastPage() > 1)
                        (Halaman <strong>{{ $penerimaList->currentPage() }}</strong> dari <strong>{{ $penerimaList->lastPage() }}</strong>)
                    @endif
                </div>

                @if($penerimaList->lastPage() > 1)
                    @php
                        $current = $penerimaList->currentPage();
                        $last = $penerimaList->lastPage();
                        $delta = 2;
                        $left = $current - $delta;
                        $right = $current + $delta + 1;
                        $range = [];
                        for ($i = 1; $i <= $last; $i++) {
                            if ($i == 1 || $i == $last || ($i >= $left && $i < $right)) {
                                $range[] = $i;
                            }
                        }
                        $rangeWithDots = [];
                        $l = null;
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
                    <ul class="pagination-nav" style="display: flex; gap: 4px; list-style: none; margin: 0; padding: 0;">
                        @if($penerimaList->onFirstPage())
                            <li><span class="page-btn disabled" style="padding: 6px 10px; border-radius: 6px; border: 1px solid #cbd5e1; color: #cbd5e1; display: inline-block;"><i class="fas fa-chevron-left"></i></span></li>
                        @else
                            <li><a href="{{ $penerimaList->previousPageUrl() }}" class="page-btn" style="padding: 6px 10px; border-radius: 6px; border: 1px solid #cbd5e1; color: #002855; text-decoration: none; display: inline-block;"><i class="fas fa-chevron-left"></i></a></li>
                        @endif

                        @foreach($rangeWithDots as $page)
                            @if($page === '...')
                                <li><span class="page-dots" style="padding: 6px 8px; color: #94a3b8; display: inline-block;">...</span></li>
                            @elseif($page == $current)
                                <li><span class="page-btn active" style="padding: 6px 12px; border-radius: 6px; background: #002855; color: #ffffff; font-weight: 700; display: inline-block;">{{ $page }}</span></li>
                            @else
                                <li><a href="{{ $penerimaList->url($page) }}" class="page-btn" style="padding: 6px 12px; border-radius: 6px; border: 1px solid #cbd5e1; color: #002855; text-decoration: none; font-weight: 600; display: inline-block;">{{ $page }}</a></li>
                            @endif
                        @endforeach

                        @if($penerimaList->hasMorePages())
                            <li><a href="{{ $penerimaList->nextPageUrl() }}" class="page-btn" style="padding: 6px 10px; border-radius: 6px; border: 1px solid #cbd5e1; color: #002855; text-decoration: none; display: inline-block;"><i class="fas fa-chevron-right"></i></a></li>
                        @else
                            <li><span class="page-btn disabled" style="padding: 6px 10px; border-radius: 6px; border: 1px solid #cbd5e1; color: #cbd5e1; display: inline-block;"><i class="fas fa-chevron-right"></i></span></li>
                        @endif
                    </ul>
                @endif
            </div>
            @endif
        </div>
    </main>
@endsection
