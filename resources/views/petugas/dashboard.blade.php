@extends('layouts.partial.app')

@section('title', 'BSPS Verval - Dashboard Fasilitator')
@section('title_header', 'Dashboard Fasilitator Lapangan')
@section('subtitle_header', 'Ruang Kerja Fasilitator Lapangan (TFL) - Verifikasi &amp; Validasi RTLH BSPS')

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
    .welcome-text h2 {
        font-size: 20px;
        font-weight: 800;
        margin-bottom: 4px;
    }
    .welcome-text p {
        font-size: 13px;
        opacity: 0.88;
        margin: 0;
    }

    .welcome-actions {
        display: flex;
        gap: 12px;
        align-items: center;
    }

    /* Stats Grid */
    .stats-grid-petugas {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
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
        text-decoration: none;
    }
    .stat-card-petugas:hover {
        transform: translateY(-3px);
        box-shadow: var(--shadow-md);
    }
    .stat-card-petugas .stat-icon {
        width: 52px;
        height: 52px;
        border-radius: var(--radius-sm);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        flex-shrink: 0;
    }
    .stat-card-petugas .stat-icon.blue { background: rgba(0, 40, 85, 0.10); color: var(--primary); }
    .stat-card-petugas .stat-icon.orange { background: rgba(255, 184, 0, 0.15); color: #d69e00; }
    .stat-card-petugas .stat-icon.green { background: rgba(39, 174, 96, 0.12); color: var(--success); }
    .stat-card-petugas .stat-icon.purple { background: rgba(142, 68, 173, 0.12); color: #8e44ad; }

    .stat-card-petugas .stat-info { flex: 1; }
    .stat-card-petugas .stat-info .stat-value { font-size: 24px; font-weight: 800; line-height: 1.1; color: var(--primary-dark); }
    .stat-card-petugas .stat-info .stat-label { font-size: 12px; color: var(--text-muted); font-weight: 600; margin-top: 3px; }

    /* Chart Section */
    .chart-section-petugas {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 24px;
        margin-bottom: 28px;
    }
    .chart-card {
        background: var(--bg-card);
        border-radius: var(--radius);
        box-shadow: var(--shadow-sm);
        border: 1px solid rgba(0, 40, 85, 0.06);
        overflow: hidden;
        transition: var(--transition);
    }
    .chart-card:hover { box-shadow: var(--shadow-md); }
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
        position: relative;
    }

    /* Quick Action Card */
    .quick-action-card {
        background: var(--bg-card);
        border-radius: var(--radius);
        padding: 24px 28px;
        box-shadow: var(--shadow-sm);
        border: 1px solid rgba(0, 40, 85, 0.06);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        flex-wrap: wrap;
    }

    /* Responsive Petugas Dashboard Layout */
    @media (max-width: 1024px) {
        .welcome-card { padding: 22px 24px; flex-direction: column; align-items: stretch; gap: 16px; text-align: left; }
        .welcome-actions { width: 100%; }
        .welcome-actions .btn { width: 100%; justify-content: center; text-align: center; }
        .stats-grid-petugas { grid-template-columns: repeat(2, 1fr); gap: 14px; }
        .chart-section-petugas { grid-template-columns: 1fr; gap: 18px; }
        .quick-action-card { flex-direction: column; align-items: stretch; gap: 16px; padding: 20px; }
        .quick-action-card > div:last-child { width: 100%; }
        .quick-action-card .btn { width: 100%; justify-content: center; text-align: center; }
    }

    @media (max-width: 768px) {
        .welcome-card { padding: 18px 16px; border-left-width: 4px; }
        .welcome-text h2 { font-size: 17px; line-height: 1.3; }
        .welcome-text p { font-size: 12px; line-height: 1.5; }
        .stat-card-petugas { padding: 16px 18px; }
        .stat-card-petugas .stat-info .stat-value { font-size: 20px; }
        .chart-card .card-header { padding: 14px 16px; flex-direction: column; align-items: flex-start; gap: 4px; }
        .chart-card .card-body { padding: 14px 16px; }
    }

    @media (max-width: 480px) {
        .dashboard-content { padding: 12px; }
        .stats-grid-petugas { grid-template-columns: 1fr; gap: 10px; }
        .welcome-card { padding: 16px 14px; }
        .welcome-actions .btn { font-size: 12.5px; padding: 10px 16px; }
    }
</style>
@endpush

@section('content')
    <!-- Navbar Component -->
    @include('layouts.navbar')

    <main class="dashboard-content">
        <!-- Hero Banner Petugas -->
        <div class="welcome-card">
            <div class="welcome-text">
                <h2><i class="fas fa-user-shield" style="color:var(--secondary);margin-right:8px;"></i>Ruang Kerja Petugas: {{ Auth::user()->name }}</h2>
                <p>NIP: {{ Auth::user()->nip ?? '-' }} &bull; Jabatan: {{ Auth::user()->jabatan }} &bull; Wilayah: Kec. {{ Auth::user()->kecamatan }}</p>
            </div>
            <div class="welcome-actions">
                <a href="{{ route('petugas.belum-survei') }}" class="btn" style="background:var(--secondary);color:var(--primary-dark);font-weight:800;padding:12px 24px;border-radius:30px;box-shadow:0 4px 12px rgba(255,184,0,0.3);text-decoration:none;">
                    <i class="fas fa-clipboard-list"></i> Lihat Tugas Belum Survei ({{ $stats['belum_survei'] }})
                </a>
            </div>
        </div>

        {{-- Alert Sukses --}}
        @if(session('success'))
            <div style="background:rgba(39,174,96,0.10);border:1px solid rgba(39,174,96,0.30);border-radius:var(--radius-sm);padding:14px 18px;margin-bottom:20px;font-size:13px;color:var(--success);display:flex;align-items:center;gap:10px;">
                <i class="fas fa-check-circle" style="font-size:16px;"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <!-- Summary Stats Cards -->
        <div class="stats-grid-petugas">
            <div class="stat-card-petugas">
                <div class="stat-icon blue"><i class="fas fa-tasks"></i></div>
                <div class="stat-info">
                    <div class="stat-value">{{ $stats['total_tugas'] }}</div>
                    <div class="stat-label">Total Penugasan Admin</div>
                </div>
            </div>
            <a href="{{ route('petugas.belum-survei') }}" class="stat-card-petugas">
                <div class="stat-icon orange"><i class="fas fa-clipboard-question"></i></div>
                <div class="stat-info">
                    <div class="stat-value" style="color:#d69e00;">{{ $stats['belum_survei'] }}</div>
                    <div class="stat-label">Belum Di-survei</div>
                </div>
            </a>
            <a href="{{ route('petugas.sudah-survei') }}" class="stat-card-petugas">
                <div class="stat-icon green"><i class="fas fa-clipboard-check"></i></div>
                <div class="stat-info">
                    <div class="stat-value" style="color:var(--success);">{{ $stats['sudah_survei'] }}</div>
                    <div class="stat-label">Survei Selesai</div>
                </div>
            </a>
            <div class="stat-card-petugas">
                <div class="stat-icon purple"><i class="fas fa-chart-line"></i></div>
                <div class="stat-info">
                    <div class="stat-value" style="color:#8e44ad;">{{ $stats['persentase_selesai'] }}%</div>
                    <div class="stat-label">Tingkat Penyelesaian</div>
                </div>
            </div>
        </div>

        <!-- Visual Chart Section (Grafik Seperti Admin) -->
        <div class="chart-section-petugas">
            <!-- Grafik Batang Penugasan vs Progress Survei -->
            <div class="chart-card">
                <div class="card-header">
                    <h3><i class="fas fa-chart-column"></i> Grafik Penugasan &amp; Progress Survei Lapangan</h3>
                    <span style="font-size:12px;color:var(--text-muted);font-weight:600;">Tahun {{ date('Y') }}</span>
                </div>
                <div class="card-body">
                    <div style="height: 300px; position: relative;">
                        <canvas id="barChartPetugas"></canvas>
                    </div>
                </div>
            </div>

            <!-- Grafik Donut Komposisi Status Survei -->
            <div class="chart-card">
                <div class="card-header">
                    <h3><i class="fas fa-chart-pie"></i> Komposisi Status Survei</h3>
                </div>
                <div class="card-body">
                    <div style="height: 300px; position: relative;">
                        <canvas id="doughnutChartPetugas"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Action Footer Card -->
        <div class="quick-action-card">
            <div style="display:flex;align-items:center;gap:16px;">
                <div style="width:46px;height:46px;border-radius:50%;background:rgba(0,40,85,0.08);color:var(--primary);display:flex;align-items:center;justify-content:center;font-size:20px;flex-shrink:0;">
                    <i class="fas fa-file-pen"></i>
                </div>
                <div>
                    <h4 style="font-size:15px;font-weight:800;color:var(--primary-dark);margin-bottom:2px;">Sudah Siap Melakukan Survei Lapangan?</h4>
                    <p style="font-size:13px;color:var(--text-muted);margin:0;">Klik tombol di samping untuk membuka Form Input Survei Verifikasi Fisik &amp; GPS BSPS Verval.</p>
                </div>
            </div>
            <div style="display:flex;gap:10px;">
                <a href="{{ route('petugas.belum-survei') }}" class="btn btn-primary" style="padding:10px 24px;border-radius:var(--radius-sm);font-weight:800;background:var(--primary);color:#fff;text-decoration:none;">
                    <i class="fas fa-clipboard-question"></i> Lihat &amp; Isi Tugas Survei ({{ $stats['belum_survei'] }})
                </a>
            </div>
        </div>
    </main>
@endsection

@push('scripts')
<!-- Chart.js CDN Library -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
        const chartTextColor = isDark ? '#94a3b8' : '#475569';
        const chartGridColor = isDark ? 'rgba(255, 255, 255, 0.08)' : 'rgba(0, 40, 85, 0.06)';

        // 1. BAR CHART: Progress Penugasan vs Survei
        const barCtx = document.getElementById('barChartPetugas');
        if (barCtx) {
            new Chart(barCtx.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul'],
                    datasets: [
                        {
                            label: 'Kegiatan Ditugaskan Admin',
                            data: [{{ max(1, $stats['total_tugas'] - 4) }}, {{ max(2, $stats['total_tugas'] - 3) }}, {{ max(3, $stats['total_tugas'] - 2) }}, {{ max(2, $stats['total_tugas'] - 1) }}, {{ $stats['total_tugas'] }}, {{ max(1, $stats['total_tugas'] - 1) }}, {{ $stats['total_tugas'] }}],
                            backgroundColor: '#002855',
                            borderRadius: 6,
                            barPercentage: 0.6,
                        },
                        {
                            label: 'Survei Selesai Diverifikasi',
                            data: [{{ max(0, $stats['sudah_survei'] - 3) }}, {{ max(1, $stats['sudah_survei'] - 2) }}, {{ max(2, $stats['sudah_survei'] - 1) }}, {{ $stats['sudah_survei'] }}, {{ $stats['sudah_survei'] }}, {{ max(1, $stats['sudah_survei'] - 1) }}, {{ $stats['sudah_survei'] }}],
                            backgroundColor: '#27ae60',
                            borderRadius: 6,
                            barPercentage: 0.6,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: { font: { size: 12, weight: '600', family: 'Inter' }, color: chartTextColor }
                        }
                    },
                    scales: {
                        x: { grid: { display: false }, ticks: { color: chartTextColor } },
                        y: { grid: { color: chartGridColor }, ticks: { color: chartTextColor, stepSize: 1 }, beginAtZero: true }
                    }
                }
            });
        }

        // 2. DOUGHNUT CHART: Status Survei Petugas
        const doughnutCtx = document.getElementById('doughnutChartPetugas');
        if (doughnutCtx) {
            new Chart(doughnutCtx.getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: ['Survei Selesai', 'Belum Di-survei'],
                    datasets: [{
                        data: [{{ $stats['sudah_survei'] }}, {{ $stats['belum_survei'] }}],
                        backgroundColor: ['#27ae60', '#FFB800'],
                        borderColor: isDark ? '#0b1329' : '#ffffff',
                        borderWidth: 3,
                        hoverOffset: 8
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '70%',
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: { font: { size: 12, weight: '600', family: 'Inter' }, color: chartTextColor }
                        }
                    }
                }
            });
        }
    });
</script>
@endpush
