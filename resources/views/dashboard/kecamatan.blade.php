@extends('layouts.partial.app')

@section('title', 'Dashboard Monitoring Kecamatan - BSPS Verval')
@section('title_header', 'Dashboard Monitoring Kecamatan')
@section('subtitle_header', \Carbon\Carbon::now()->translatedFormat('l, d F Y') . ' — Monitoring Real-Time Verval Data Desa/Kelurahan Kecamatan ' . strtoupper($kecamatanSelected))

@push('styles')
<style>
    /* Filter Bar Card */
    .filter-bar-kecamatan {
        background: var(--bg-card);
        border-radius: var(--radius);
        padding: 16px 20px;
        margin-bottom: 24px;
        box-shadow: var(--shadow-sm);
        border: 1px solid rgba(0, 40, 85, 0.08);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
    }

    .kecamatan-badge-header {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .kecamatan-icon-box {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        background: linear-gradient(135deg, #002855, #004080);
        color: #ffb800;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        box-shadow: 0 4px 10px rgba(0, 40, 85, 0.2);
    }

    .kecamatan-title-text h2 {
        font-size: 17px;
        font-weight: 800;
        color: var(--primary-dark, #002855);
        margin: 0 0 2px 0;
    }

    .kecamatan-title-text p {
        font-size: 12.5px;
        color: var(--text-muted);
        margin: 0;
    }

    /* Stats Grid 4 Cards */
    .stats-grid-4 {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
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
        position: relative;
        overflow: hidden;
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
    .stat-card .stat-icon.yellow { background: rgba(255, 184, 0, 0.16); color: #d69e00; }
    .stat-card .stat-icon.purple { background: rgba(142, 68, 173, 0.12); color: #8e44ad; }

    .stat-card .stat-info .stat-value {
        font-size: 24px;
        font-weight: 800;
        color: var(--primary-dark, #002855);
        line-height: 1.1;
        margin-bottom: 4px;
    }

    .stat-card .stat-info .stat-label {
        font-size: 12.5px;
        font-weight: 700;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .stat-card .stat-sub {
        font-size: 11.5px;
        color: var(--text-muted);
        margin-top: 4px;
        font-weight: 500;
    }

    /* Custom Progress Bar Inside Card */
    .card-progress-bg {
        width: 100%;
        height: 6px;
        background: rgba(0, 40, 85, 0.08);
        border-radius: 4px;
        margin-top: 8px;
        overflow: hidden;
    }

    .card-progress-fill {
        height: 100%;
        background: linear-gradient(90deg, #27ae60, #2ecc71);
        border-radius: 4px;
        transition: width 0.6s ease;
    }

    /* Grid Layout 2 Columns */
    .dashboard-two-col {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 20px;
        margin-bottom: 24px;
    }

    .chart-card {
        background: var(--bg-card);
        border-radius: var(--radius);
        padding: 22px;
        box-shadow: var(--shadow-sm);
        border: 1px solid rgba(0, 40, 85, 0.06);
    }

    .card-header-flex {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 18px;
        padding-bottom: 12px;
        border-bottom: 1px solid rgba(0, 40, 85, 0.06);
    }

    .card-header-flex h3 {
        font-size: 15px;
        font-weight: 800;
        color: var(--primary-dark, #002855);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    /* Petugas List Container */
    .petugas-list-box {
        display: flex;
        flex-direction: column;
        gap: 10px;
        max-height: 330px;
        overflow-y: auto;
        padding-right: 4px;
    }

    .petugas-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 10px 12px;
        border-radius: 10px;
        background: var(--bg-body);
        border: 1px solid rgba(0, 40, 85, 0.06);
        transition: var(--transition);
    }

    .petugas-item:hover {
        border-color: var(--primary);
        background: rgba(0, 40, 85, 0.02);
    }

    .petugas-item-left {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .petugas-avatar-circle {
        width: 34px;
        height: 34px;
        border-radius: 50%;
        background: #002855;
        color: #ffb800;
        font-weight: 800;
        font-size: 13px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .petugas-name-text {
        font-size: 12.5px;
        font-weight: 700;
        color: var(--text-primary);
    }

    .petugas-sub-text {
        font-size: 11px;
        color: var(--text-muted);
    }

    /* Table Custom Styles */
    .table-monitoring-card {
        background: var(--bg-card);
        border-radius: var(--radius);
        box-shadow: var(--shadow-sm);
        border: 1px solid rgba(0, 40, 85, 0.06);
        overflow: hidden;
        margin-bottom: 30px;
    }

    .table-custom-desa {
        width: 100%;
        border-collapse: collapse;
    }

    .table-custom-desa th {
        background: var(--bg-body);
        color: var(--text-muted);
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 14px 16px;
        border-bottom: 1px solid rgba(0, 40, 85, 0.08);
        text-align: left;
    }

    .table-custom-desa td {
        padding: 14px 16px;
        border-bottom: 1px solid rgba(0, 40, 85, 0.05);
        font-size: 13px;
        color: var(--text-primary);
        vertical-align: middle;
    }

    .table-custom-desa tr:hover {
        background: rgba(0, 40, 85, 0.015);
    }

    .progress-bar-table {
        display: flex;
        align-items: center;
        gap: 8px;
        width: 130px;
    }

    .progress-bar-track {
        flex: 1;
        height: 6px;
        background: rgba(0, 40, 85, 0.1);
        border-radius: 3px;
        overflow: hidden;
    }

    .progress-bar-fill {
        height: 100%;
        background: #27ae60;
        border-radius: 3px;
    }

    @media (max-width: 1200px) {
        .stats-grid-4 { grid-template-columns: repeat(2, 1fr); }
        .dashboard-two-col { grid-template-columns: 1fr; }
    }

    @media (max-width: 600px) {
        .stats-grid-4 { grid-template-columns: 1fr; }
    }
</style>
@endpush

@section('content')
    <!-- Navbar Component -->
    @include('layouts.navbar')

    <main class="dashboard-content">

    <!-- Header & Filter Bar Kecamatan -->
    <div class="filter-bar-kecamatan">
        <div class="kecamatan-badge-header">
            <div class="kecamatan-icon-box">
                <i class="fas fa-building-flag"></i>
            </div>
            <div class="kecamatan-title-text">
                <h2>Kecamatan {{ strtoupper($kecamatanSelected) }}</h2>
                <p>Monitoring Verifikasi &amp; Validasi Data Calon Penerima BSPS</p>
            </div>
        </div>

        @if(auth()->check() && auth()->user()->isAdmin())
            <!-- Custom Dropdown Filter Kecamatan Khusus Super Admin -->
            <form action="{{ route('dashboard.kecamatan') }}" method="GET" id="formFilterKecamatan" style="display:flex;align-items:center;gap:10px;">
                <input type="hidden" name="kecamatan" id="hiddenKecamatanFilter" value="{{ $kecamatanSelected }}" />
                <label style="font-size:13px;font-weight:700;color:var(--text-muted);white-space:nowrap;">Pilih Kecamatan:</label>
                <div class="pupr-dropdown-wrapper" id="ddKecamatanMonitorWrapper" style="min-width:220px;">
                    <button type="button" class="pupr-dropdown-toggle" onclick="window.PuprDropdown.toggle(document.getElementById('ddKecamatanMonitorWrapper'))">
                        <i class="fas fa-building-flag" style="font-size:12px;opacity:0.6;"></i>
                        <span class="selected-label">
                            Kec. {{ ucwords(strtolower($kecamatanSelected)) }}
                        </span>
                        <i class="fas fa-chevron-down" style="font-size:10px;opacity:0.5;"></i>
                    </button>
                    <div class="pupr-dropdown-menu" style="min-width:220px;max-height:320px;overflow-y:auto;">
                        @foreach($listKecamatan as $kec)
                        <div class="pupr-dropdown-item {{ $kecamatanSelected === $kec ? 'active' : '' }}"
                             onclick="selectDropdown('hiddenKecamatanFilter', 'ddKecamatanMonitorWrapper', '{{ $kec }}', 'Kec. {{ ucwords(strtolower($kec)) }}', 'formFilterKecamatan')">
                            <i class="fas fa-map-pin" style="font-size:11px;opacity:0.4;"></i> Kec. {{ ucwords(strtolower($kec)) }}
                        </div>
                        @endforeach
                    </div>
                </div>
            </form>
        @else
            <div style="font-size:12px;font-weight:700;padding:6px 14px;border-radius:20px;background:rgba(0,40,85,0.08);color:var(--primary-dark);">
                <i class="fas fa-user-shield" style="margin-right:6px;color:#ffb800;"></i> Admin Wilayah Kecamatan {{ ucwords(strtolower($kecamatanSelected)) }}
            </div>
        @endif
    </div>

    <!-- 4 Ringkasan Kartu Statistik Utama -->
    <div class="stats-grid-4">
        <!-- Card 1: Total Penerima -->
        <div class="stat-card">
            <div class="stat-icon blue">
                <i class="fas fa-users-viewfinder"></i>
            </div>
            <div class="stat-info">
                <div class="stat-value">{{ number_format($totalPenerima, 0, ',', '.') }}</div>
                <div class="stat-label">Total Usulan BSPS</div>
                <div class="stat-sub"><i class="fas fa-database" style="margin-right:4px;"></i> Calon Penerima</div>
            </div>
        </div>

        <!-- Card 2: Total Desa -->
        <div class="stat-card">
            <div class="stat-icon purple">
                <i class="fas fa-city"></i>
            </div>
            <div class="stat-info">
                <div class="stat-value">{{ number_format($totalDesa, 0, ',', '.') }}</div>
                <div class="stat-label">Desa / Kelurahan</div>
                <div class="stat-sub"><i class="fas fa-map-location-dot" style="margin-right:4px;"></i> Wilayah Monitoring</div>
            </div>
        </div>

        <!-- Card 3: Progress Survei Lapangan -->
        <div class="stat-card">
            <div class="stat-icon green">
                <i class="fas fa-clipboard-check"></i>
            </div>
            <div class="stat-info" style="width:100%;">
                <div style="display:flex;align-items:center;justify-content:space-between;">
                    <div class="stat-value" style="color:#27ae60;">{{ $progressPercent }}%</div>
                    <span style="font-size:11px;font-weight:800;background:rgba(39,174,96,0.12);color:#27ae60;padding:2px 8px;border-radius:10px;">
                        {{ $totalSudahSurvei }}/{{ $totalPenerima }}
                    </span>
                </div>
                <div class="stat-label">Progress Survei Lapangan</div>
                <div class="card-progress-bg">
                    <div class="card-progress-fill" style="width: {{ $progressPercent }}%;"></div>
                </div>
            </div>
        </div>

        <!-- Card 4: Persebaran Backlog -->
        <div class="stat-card">
            <div class="stat-icon yellow">
                <i class="fas fa-layer-group"></i>
            </div>
            <div class="stat-info">
                <div class="stat-value" style="color:#d69e00;">{{ $backlog1Count + $backlog2Count }}</div>
                <div class="stat-label">Backlog 1 &amp; 2</div>
                <div class="stat-sub">
                    <strong style="color:#27ae60;">{{ $backlog1Count }}</strong> B1 &bull; 
                    <strong style="color:#e67e22;">{{ $backlog2Count }}</strong> B2
                </div>
            </div>
        </div>
    </div>

    <!-- Layout 2 Kolom (Chart Progress Desa & Petugas Lapangan) -->
    <div class="dashboard-two-col">
        <!-- Kolom Kiri: Chart Perbandingan Progress Survei per Desa -->
        <div class="chart-card">
            <div class="card-header-flex">
                <h3>
                    <i class="fas fa-chart-column" style="color:var(--primary);"></i>
                    Status Survei Lapangan per Desa / Kelurahan
                </h3>
                <span style="font-size:12px;font-weight:700;color:var(--text-muted);">
                    Kec. {{ ucwords(strtolower($kecamatanSelected)) }}
                </span>
            </div>
            <div style="position:relative; height: 310px; width: 100%;">
                <canvas id="chartProgressDesa"></canvas>
            </div>
        </div>

        <!-- Kolom Kanan: Daftar Petugas Lapangan di Kecamatan -->
        <div class="chart-card">
            <div class="card-header-flex">
                <h3>
                    <i class="fas fa-users-gear" style="color:#ffb800;"></i>
                    Petugas Lapangan
                </h3>
                <span style="font-size:12px;font-weight:700;color:var(--primary);">
                    {{ count($petugasList) }} Orang
                </span>
            </div>

            <div class="petugas-list-box">
                @forelse($petugasList as $ptg)
                    <div class="petugas-item">
                        <div class="petugas-item-left">
                            <div class="petugas-avatar-circle">
                                {{ strtoupper(substr($ptg->name, 0, 1)) }}
                            </div>
                            <div>
                                <div class="petugas-name-text">{{ $ptg->name }}</div>
                                <div class="petugas-sub-text">
                                    <i class="fas fa-location-dot" style="color:var(--primary);margin-right:3px;"></i>
                                    {{ $ptg->desa ?: 'Semua Desa' }}
                                </div>
                            </div>
                        </div>
                        <div>
                            @if($ptg->status === 'bertugas')
                                <span style="font-size:10px;font-weight:800;padding:2px 8px;border-radius:10px;background:rgba(39,174,96,0.12);color:#27ae60;">
                                    Bertugas
                                </span>
                            @elseif($ptg->status === 'aktif')
                                <span style="font-size:10px;font-weight:800;padding:2px 8px;border-radius:10px;background:rgba(0,40,85,0.1);color:var(--primary);">
                                    Aktif
                                </span>
                            @else
                                <span style="font-size:10px;font-weight:800;padding:2px 8px;border-radius:10px;background:rgba(231,76,60,0.1);color:#e74c3c;">
                                    Cuti
                                </span>
                            @endif
                        </div>
                    </div>
                @empty
                    <div style="text-align:center;padding:30px 10px;color:var(--text-muted);font-size:13px;">
                        <i class="fas fa-user-slash" style="font-size:24px;margin-bottom:8px;display:block;opacity:0.4;"></i>
                        Belum ada petugas lapangan terdaftar di kecamatan ini.
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Tabel Monitoring Rincian per Desa/Kelurahan -->
    <div class="table-monitoring-card">
        <div class="card-header-flex" style="padding:18px 22px;margin:0;border-bottom:1px solid rgba(0,40,85,0.08);">
            <h3>
                <i class="fas fa-list-check" style="color:#27ae60;"></i>
                Monitoring Progress Verifikasi &amp; Validasi per Desa / Kelurahan
            </h3>
            <a href="{{ url('/verval-data?kecamatan=' . urlencode($kecamatanSelected)) }}" class="btn btn-sm" style="background:var(--primary);color:#fff;font-size:12px;font-weight:700;padding:6px 14px;border-radius:6px;text-decoration:none;">
                <i class="fas fa-external-link-alt" style="margin-right:4px;"></i> Lihat Seluruh Data
            </a>
        </div>

        <div style="overflow-x:auto;">
            <table class="table-custom-desa">
                <thead>
                    <tr>
                        <th style="width:40px;text-align:center;">#</th>
                        <th>Desa / Kelurahan</th>
                        <th style="text-align:center;">Total Usulan</th>
                        <th style="text-align:center;">Progress Survei</th>
                        <th style="text-align:center;">Sudah Survei</th>
                        <th style="text-align:center;">Belum Survei</th>
                        <th style="text-align:center;">Backlog 1</th>
                        <th style="text-align:center;">Backlog 2</th>
                        <th style="text-align:center;">Petugas</th>
                        <th style="text-align:center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($desaStats as $index => $row)
                        <tr>
                            <td style="text-align:center;font-weight:700;color:var(--text-muted);">{{ $index + 1 }}</td>
                            <td>
                                <strong style="color:var(--primary-dark);font-size:13.5px;">
                                    {{ ucwords(strtolower($row->desa_kelurahan)) }}
                                </strong>
                            </td>
                            <td style="text-align:center;font-weight:800;color:var(--primary-dark);">
                                {{ number_format($row->total, 0, ',', '.') }}
                            </td>
                            <td style="text-align:center;">
                                <div class="progress-bar-table" style="margin:0 auto;">
                                    <div class="progress-bar-track">
                                        <div class="progress-bar-fill" style="width: {{ $row->progress_percent }}%;"></div>
                                    </div>
                                    <span style="font-size:11px;font-weight:800;color:#27ae60;">{{ $row->progress_percent }}%</span>
                                </div>
                            </td>
                            <td style="text-align:center;">
                                <span style="font-size:12px;font-weight:800;color:#27ae60;background:rgba(39,174,96,0.1);padding:3px 10px;border-radius:12px;">
                                    {{ number_format($row->sudah_survei, 0, ',', '.') }}
                                </span>
                            </td>
                            <td style="text-align:center;">
                                <span style="font-size:12px;font-weight:800;color:#d69e00;background:rgba(255,184,0,0.14);padding:3px 10px;border-radius:12px;">
                                    {{ number_format($row->belum_survei, 0, ',', '.') }}
                                </span>
                            </td>
                            <td style="text-align:center;font-weight:700;color:var(--primary);">
                                {{ number_format($row->backlog_1, 0, ',', '.') }}
                            </td>
                            <td style="text-align:center;font-weight:700;color:#e67e22;">
                                {{ number_format($row->backlog_2, 0, ',', '.') }}
                            </td>
                            <td style="text-align:center;">
                                <span style="font-size:11.5px;font-weight:700;color:var(--text-muted);">
                                    <i class="fas fa-user-check" style="color:var(--primary);margin-right:3px;"></i>
                                    {{ $row->petugas_count }}
                                </span>
                            </td>
                            <td style="text-align:center;">
                                <a href="{{ url('/verval-data?kecamatan=' . urlencode($kecamatanSelected) . '&desa=' . urlencode($row->desa_kelurahan)) }}" 
                                   style="display:inline-flex;align-items:center;gap:4px;padding:4px 10px;border-radius:6px;background:rgba(0,40,85,0.06);color:var(--primary);font-size:11.5px;font-weight:700;text-decoration:none;transition:var(--transition);">
                                    <i class="fas fa-filter"></i> Filter
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" style="text-align:center;padding:32px;color:var(--text-muted);">
                                <i class="fas fa-folder-open" style="font-size:28px;margin-bottom:8px;display:block;opacity:0.4;"></i>
                                Belum ada data desa terdaftar di kecamatan ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    </main>
@endsection

@push('scripts')
<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Chart Progress Survei per Desa
    const ctxProgress = document.getElementById('chartProgressDesa');
    if (ctxProgress) {
        const labels = {!! json_encode($chartDesaLabels) !!};
        const sudahData = {!! json_encode($chartSudahData) !!};
        const belumData = {!! json_encode($chartBelumData) !!};

        new Chart(ctxProgress, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Sudah Survei',
                        data: sudahData,
                        backgroundColor: '#27ae60',
                        borderRadius: 6,
                    },
                    {
                        label: 'Belum Survei',
                        data: belumData,
                        backgroundColor: '#ffb800',
                        borderRadius: 6,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            font: { family: 'Plus Jakarta Sans', weight: '700', size: 12 },
                            usePointStyle: true,
                            boxWidth: 10
                        }
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { font: { family: 'Plus Jakarta Sans', size: 11, weight: '600' } }
                    },
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(0, 40, 85, 0.05)' },
                        ticks: { font: { family: 'Plus Jakarta Sans', size: 11 } }
                    }
                }
            }
        });
    }
});
</script>
@endpush
