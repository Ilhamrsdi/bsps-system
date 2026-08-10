@extends('layouts.partial.app')

@section('title', 'BSPS Verval - Dashboard')
@section('title_header', 'Dashboard BSPS')
@section('subtitle_header', \Carbon\Carbon::now()->translatedFormat('l, d F Y') . ' - Sistem Informasi Verifikasi & Validasi BSPS')

@php
    // ==========================================
    // DUMMY DATA LOKAL LANGSUNG DI VIEW (1-5 DATA)
    // ==========================================
    $totalKegiatan = 24;
    $surveiSelesai = 18;
    $menungguSurvei = 6;
    $bapTerbit = 18;

    $pipeline = [
        'proses'   => 5,
        'selesai'  => 18,
        'menunggu' => 6,
        'survei'   => 4,
        'bap'      => 18,
    ];

    $statusCounts = [
        'proses'   => 5,
        'selesai'  => 18,
        'menunggu' => 6,
        'survei'   => 4,
        'batal'    => 1,
    ];

    $latestKegiatan = [
        (object)[
            'id' => 1,
            'nama_kegiatan' => 'Verval Calon Penerima Bantuan BSPS - Bpk. Slamet Riyadi',
            'lokasi' => 'Kaliwates (Kel. Sempusari)',
            'nama_pemohon' => 'Bpk. Slamet Riyadi',
            'status' => 'selesai',
            'pct' => '100%',
        ],
        (object)[
            'id' => 2,
            'nama_kegiatan' => 'Verifikasi Lapangan RTLH - Ibu Siti Aminah',
            'lokasi' => 'Patrang (Kel. Gebang)',
            'nama_pemohon' => 'Ibu Siti Aminah',
            'status' => 'proses',
            'pct' => '65%',
        ],
        (object)[
            'id' => 3,
            'nama_kegiatan' => 'Verifikasi Validasi Rumah Swadaya - Bpk. Bambang Sutrisno',
            'lokasi' => 'Sumbersari (Kel. Antirogo)',
            'nama_pemohon' => 'Bpk. Bambang Sutrisno',
            'status' => 'survei',
            'pct' => '45%',
        ],
        (object)[
            'id' => 4,
            'nama_kegiatan' => 'Survei Kelaikan Komponen Bangunan - Ibu Nurul Hidayati',
            'lokasi' => 'Rambipuji (Desa Kaliwining)',
            'nama_pemohon' => 'Ibu Nurul Hidayati',
            'status' => 'selesai',
            'pct' => '100%',
        ],
        (object)[
            'id' => 5,
            'nama_kegiatan' => 'Verifikasi Data Usulan BSPS - Bpk. Joko Santoso',
            'lokasi' => 'Arjasa (Desa Kemuning)',
            'nama_pemohon' => 'Bpk. Joko Santoso',
            'status' => 'menunggu',
            'pct' => '20%',
        ],
    ];

    $recentActivities = [
        (object)[
            'nama_petugas_1' => 'Ahmad Fauzi (TFL BSPS)',
            'nama_kegiatan' => 'Survei Lapangan Rumah Bpk. Slamet Riyadi',
            'waktu' => '35 menit yang lalu',
        ],
        (object)[
            'nama_petugas_1' => 'Budi Pratama (Fasilitator)',
            'nama_kegiatan' => 'Validasi Data Administrasi Ibu Nurul Hidayati',
            'waktu' => '2 jam yang lalu',
        ],
        (object)[
            'nama_petugas_1' => 'Dwi Handoko (Koordinator)',
            'nama_kegiatan' => 'Pengecekan Komponen Struktur Rumah Bpk. Bambang',
            'waktu' => '4 jam yang lalu',
        ],
    ];

    $petugasList = [
        (object)['name' => 'Ahmad Fauzi (TFL 01)', 'role' => 'petugas'],
        (object)['name' => 'Budi Pratama (TFL 02)', 'role' => 'petugas'],
        (object)['name' => 'Dwi Handoko (Koordinator)', 'role' => 'petugas'],
    ];
@endphp

@push('styles')
<style>
    /* ============================================================
       PAGE STYLES: DASHBOARD MAIN CONTENT (PUPR COLOR THEME)
       ============================================================ */
    .greeting {
        margin-bottom: 28px;
    }

    .greeting h1 {
        font-size: 26px;
        font-weight: 800;
        letter-spacing: -0.5px;
        color: var(--primary);
    }

    .greeting h1 span {
        color: var(--secondary);
    }

    .greeting p {
        color: var(--text-muted);
        font-size: 15px;
        margin-top: 4px;
    }

    /* Stats Cards */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 20px;
        margin-bottom: 28px;
    }

    .stat-card {
        background: var(--bg-card);
        border-radius: var(--radius);
        padding: 22px 24px;
        box-shadow: var(--shadow-sm);
        transition: var(--transition);
        border: 1px solid rgba(0, 40, 85, 0.06);
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: var(--shadow-md);
    }

    .stat-card .stat-icon {
        width: 54px;
        height: 54px;
        border-radius: var(--radius-sm);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        flex-shrink: 0;
    }

    .stat-card .stat-icon.blue { background: rgba(0, 40, 85, 0.10); color: var(--primary); }
    .stat-card .stat-icon.green { background: rgba(39, 174, 96, 0.12); color: var(--success); }
    .stat-card .stat-icon.orange { background: rgba(255, 184, 0, 0.15); color: #d69e00; }
    .stat-card .stat-icon.red { background: rgba(231, 76, 60, 0.12); color: var(--danger); }
    .stat-card .stat-icon.purple { background: rgba(142, 68, 173, 0.12); color: var(--purple); }

    .stat-card .stat-info {
        flex: 1;
    }

    .stat-card .stat-info .stat-value {
        font-size: 26px;
        font-weight: 800;
        letter-spacing: -0.5px;
        line-height: 1.2;
        color: var(--primary-dark);
    }

    .stat-card .stat-info .stat-label {
        font-size: 13px;
        color: var(--text-muted);
        font-weight: 500;
    }

    .stat-card .stat-info .stat-change {
        font-size: 12px;
        font-weight: 600;
        margin-top: 4px;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 2px 10px;
        border-radius: 20px;
    }

    .stat-card .stat-info .stat-change.up {
        color: var(--success);
        background: rgba(39, 174, 96, 0.10);
    }

    .stat-card .stat-info .stat-change.down {
        color: var(--danger);
        background: rgba(231, 76, 60, 0.10);
    }

    /* Hot Issue Banner (Theme Dynamic) */
    .hot-issue-banner {
        background: linear-gradient(135deg, var(--primary-dark) 0%, var(--primary) 100%);
        border-radius: var(--radius);
        padding: 20px 28px;
        margin-bottom: 28px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        flex-wrap: wrap;
        border-left: 6px solid var(--secondary);
        box-shadow: var(--shadow-md);
    }

    .hot-issue-banner .issue-left {
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .hot-issue-banner .issue-left .issue-icon {
        width: 48px;
        height: 48px;
        background: rgba(255, 255, 255, 0.15);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        color: var(--secondary);
    }

    .hot-issue-banner .issue-left .issue-text h4 {
        color: #ffffff;
        font-size: 16px;
        font-weight: 700;
    }

    .hot-issue-banner .issue-left .issue-text p {
        color: rgba(255, 255, 255, 0.7);
        font-size: 13px;
    }

    .hot-issue-banner .issue-left .issue-text .issue-meta {
        display: flex;
        gap: 16px;
        margin-top: 2px;
        font-size: 12px;
        color: var(--secondary-light);
    }

    .hot-issue-banner .issue-right {
        display: flex;
        gap: 10px;
    }

    .hot-issue-banner .issue-right .btn {
        padding: 10px 24px;
        border-radius: var(--radius-sm);
        border: none;
        font-family: inherit;
        font-weight: 700;
        font-size: 13px;
        cursor: pointer;
        transition: var(--transition);
    }

    .hot-issue-banner .issue-right .btn-primary {
        background: var(--secondary);
        color: var(--primary-dark);
        box-shadow: 0 4px 12px rgba(255, 184, 0, 0.3);
    }

    .hot-issue-banner .issue-right .btn-primary:hover {
        background: #e6a700;
        transform: translateY(-2px);
    }

    .hot-issue-banner .issue-right .btn-outline {
        background: transparent;
        color: rgba(255, 255, 255, 0.9);
        border: 1px solid rgba(255, 255, 255, 0.3);
    }

    .hot-issue-banner .issue-right .btn-outline:hover {
        background: rgba(255, 255, 255, 0.1);
    }

    /* Chart Section */
    .chart-section {
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

    .chart-card:hover {
        box-shadow: var(--shadow-md);
    }

    .chart-card .card-header {
        padding: 18px 24px;
        border-bottom: 1px solid rgba(0, 40, 85, 0.06);
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .chart-card .card-header h3 {
        font-size: 16px;
        font-weight: 700;
        color: var(--primary);
    }

    .chart-card .card-header .card-action {
        font-size: 13px;
        color: var(--primary);
        font-weight: 600;
        cursor: pointer;
        transition: var(--transition);
        text-decoration: none;
    }

    .chart-card .card-header .card-action:hover {
        color: var(--secondary);
    }

    .chart-card .card-body {
        padding: 20px 24px;
    }

    .chart-wrapper {
        position: relative;
        height: 260px;
    }

    .chart-wrapper.pie-wrapper {
        height: 240px;
    }

    /* Pipeline Section */
    .pipeline-section {
        margin-bottom: 28px;
    }

    .pipeline-section .section-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 16px;
    }

    .pipeline-section .section-header h3 {
        font-size: 18px;
        font-weight: 700;
        color: var(--primary);
    }

    .pipeline-section .section-header .view-all {
        font-size: 13px;
        color: var(--primary);
        font-weight: 600;
        cursor: pointer;
        transition: var(--transition);
        text-decoration: none;
    }

    .pipeline-section .section-header .view-all:hover {
        color: var(--secondary);
    }

    .pipeline-steps {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 12px;
    }

    .pipeline-step {
        background: var(--bg-card);
        border-radius: var(--radius);
        padding: 16px 20px;
        box-shadow: var(--shadow-sm);
        border: 1px solid rgba(0, 40, 85, 0.06);
        text-align: center;
        transition: var(--transition);
    }

    .pipeline-step:hover {
        box-shadow: var(--shadow-md);
        transform: translateY(-3px);
    }

    .pipeline-step .step-icon {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        margin: 0 auto 10px;
    }

    .pipeline-step .step-icon.blue { background: rgba(0, 40, 85, 0.10); color: var(--primary); }
    .pipeline-step .step-icon.green { background: rgba(39, 174, 96, 0.12); color: var(--success); }
    .pipeline-step .step-icon.orange { background: rgba(255, 184, 0, 0.15); color: #d69e00; }
    .pipeline-step .step-icon.purple { background: rgba(142, 68, 173, 0.12); color: var(--purple); }
    .pipeline-step .step-icon.red { background: rgba(231, 76, 60, 0.12); color: var(--danger); }

    .pipeline-step .step-count {
        font-size: 24px;
        font-weight: 800;
        color: var(--primary-dark);
    }

    .pipeline-step .step-label {
        font-size: 13px;
        color: var(--text-muted);
        font-weight: 500;
    }

    .pipeline-step .step-progress {
        margin-top: 8px;
        height: 4px;
        background: var(--bg-body);
        border-radius: 10px;
        overflow: hidden;
    }

    .pipeline-step .step-progress .step-fill {
        height: 100%;
        border-radius: 10px;
    }

    .pipeline-step .step-progress .step-fill.blue { background: var(--primary); }
    .pipeline-step .step-progress .step-fill.green { background: var(--success); }
    .pipeline-step .step-progress .step-fill.orange { background: var(--secondary); }
    .pipeline-step .step-progress .step-fill.purple { background: var(--purple); }
    .pipeline-step .step-progress .step-fill.red { background: var(--danger); }

    /* Dashboard Grid (Bottom Layout) */
    .dashboard-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 24px;
    }

    .dashboard-grid .col {
        display: flex;
        flex-direction: column;
        gap: 24px;
    }

    /* Task Items */
    .task-item {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 12px 0;
        border-bottom: 1px solid rgba(0, 40, 85, 0.04);
    }
    .task-item:last-child { border-bottom: none; padding-bottom: 0; }
    .task-item .task-status { width: 10px; height: 10px; border-radius: 50%; flex-shrink: 0; }
    .task-item .task-status.orange { background: var(--secondary); }
    .task-item .task-status.blue { background: var(--primary); }
    .task-item .task-status.green { background: var(--success); }
    .task-item .task-status.red { background: var(--danger); }
    .task-item .task-status.purple { background: var(--purple); }
    .task-item .task-info { flex: 1; }
    .task-item .task-info .task-title { font-size: 14px; font-weight: 600; color: var(--primary); }
    .task-item .task-info .task-meta { font-size: 12px; color: var(--text-muted); display: flex; gap: 12px; margin-top: 2px; flex-wrap: wrap; }
    .task-item .task-progress { display: flex; flex-direction: column; align-items: flex-end; gap: 2px; }
    .task-item .task-progress .progress-bar { width: 80px; height: 4px; background: var(--bg-body); border-radius: 10px; overflow: hidden; }
    .task-item .task-progress .progress-bar .fill { height: 100%; border-radius: 10px; background: var(--primary); transition: width 0.6s ease; }
    .task-item .task-progress .progress-text { font-size: 11px; font-weight: 600; color: var(--text-muted); }

    /* Brief Items */
    .brief-item { display: flex; align-items: center; gap: 14px; padding: 12px 0; border-bottom: 1px solid rgba(0, 40, 85, 0.04); }
    .brief-item:last-child { border-bottom: none; padding-bottom: 0; }
    .brief-item .brief-avatar { width: 40px; height: 40px; border-radius: 50%; background: rgba(0, 40, 85, 0.08); display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 14px; color: var(--primary); flex-shrink: 0; }
    .brief-item .brief-info { flex: 1; }
    .brief-item .brief-info .brief-title { font-size: 14px; font-weight: 600; color: var(--text-primary); }
    .brief-item .brief-info .brief-meta { font-size: 12px; color: var(--text-muted); display: flex; gap: 12px; margin-top: 2px; flex-wrap: wrap; }
    .brief-item .brief-info .brief-meta .tag { background: var(--bg-body); padding: 0 10px; border-radius: 20px; font-size: 10px; font-weight: 600; }
    .brief-item .brief-action { display: flex; gap: 6px; }
    .brief-item .brief-action .btn-approve { padding: 6px 16px; border-radius: var(--radius-sm); border: none; background: var(--primary); color: #fff; font-weight: 600; font-size: 12px; cursor: pointer; transition: var(--transition); }
    .brief-item .brief-action .btn-approve:hover { background: var(--primary-light); }
    .brief-item .brief-action .btn-reject { padding: 6px 16px; border-radius: var(--radius-sm); border: 1px solid rgba(0, 40, 85, 0.12); background: transparent; color: var(--text-muted); font-weight: 600; font-size: 12px; cursor: pointer; transition: var(--transition); }
    .brief-item .brief-action .btn-reject:hover { background: var(--danger); color: #fff; border-color: var(--danger); }

    /* Activity Items */
    .activity-item { display: flex; gap: 14px; padding: 10px 0; border-bottom: 1px solid rgba(0, 40, 85, 0.04); align-items: flex-start; }
    .activity-item:last-child { border-bottom: none; padding-bottom: 0; }
    .activity-item .activity-icon { width: 34px; height: 34px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 14px; flex-shrink: 0; }
    .activity-item .activity-icon.blue { background: rgba(0, 40, 85, 0.10); color: var(--primary); }
    .activity-item .activity-icon.green { background: rgba(39, 174, 96, 0.12); color: var(--success); }
    .activity-item .activity-icon.orange { background: rgba(255, 184, 0, 0.15); color: #d69e00; }
    .activity-item .activity-icon.red { background: rgba(231, 76, 60, 0.12); color: var(--danger); }
    .activity-item .activity-content { flex: 1; }
    .activity-item .activity-content .activity-text { font-size: 14px; font-weight: 500; }
    .activity-item .activity-content .activity-text strong { font-weight: 700; color: var(--primary); }
    .activity-item .activity-content .activity-time { font-size: 12px; color: var(--text-muted); margin-top: 2px; }

    /* Report Status Items */
    .report-status-item { display: flex; align-items: center; gap: 12px; padding: 8px 0; border-bottom: 1px solid rgba(0, 40, 85, 0.04); }
    .report-status-item:last-child { border-bottom: none; padding-bottom: 0; }
    .report-status-item .report-user { font-size: 14px; font-weight: 600; min-width: 80px; color: var(--primary-dark); }
    .report-status-item .report-status { font-size: 13px; display: flex; align-items: center; gap: 6px; }
    .report-status-item .report-status .status-dot { width: 8px; height: 8px; border-radius: 50%; }
    .report-status-item .report-status .status-dot.green { background: var(--success); }
    .report-status-item .report-status .status-dot.red { background: var(--danger); }
    .report-status-item .report-status .status-dot.orange { background: var(--secondary); }
    .report-status-item .report-time { margin-left: auto; font-size: 12px; color: var(--text-muted); }

    /* Team Mini Items */
    .team-mini-item { display: flex; align-items: center; gap: 12px; padding: 8px 0; border-bottom: 1px solid rgba(0, 40, 85, 0.04); }
    .team-mini-item:last-child { border-bottom: none; padding-bottom: 0; }
    .team-mini-item .team-avatar { width: 34px; height: 34px; border-radius: 50%; background: var(--primary); color: var(--secondary); display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 12px; flex-shrink: 0; }
    .team-mini-item .team-info { flex: 1; }
    .team-mini-item .team-info .team-name { font-size: 14px; font-weight: 600; color: var(--primary-dark); }
    .team-mini-item .team-info .team-role { font-size: 12px; color: var(--text-muted); }
    .team-mini-item .team-grade { font-size: 14px; font-weight: 700; color: var(--success); }
    .team-mini-item .team-grade.good { color: #d69e00; }
    .team-mini-item .team-grade.red { color: var(--danger); }

    /* Responsive Dashboard */
    @media (max-width: 1200px) {
        .stats-grid { grid-template-columns: repeat(2, 1fr); }
        .chart-section { grid-template-columns: 1fr; }
        .pipeline-steps { grid-template-columns: repeat(3, 1fr); }
    }
    @media (max-width: 1024px) {
        .dashboard-grid { grid-template-columns: 1fr; }
        .pipeline-steps { grid-template-columns: repeat(2, 1fr); }
    }
    @media (max-width: 768px) {
        .stats-grid { grid-template-columns: 1fr 1fr; gap: 12px; }
        .stat-card { padding: 16px; }
        .stat-card .stat-info .stat-value { font-size: 20px; }
        .hot-issue-banner { flex-direction: column; align-items: flex-start; }
        .hot-issue-banner .issue-right { width: 100%; }
        .hot-issue-banner .issue-right .btn { flex: 1; text-align: center; }
        .greeting h1 { font-size: 20px; }
        .card-header { padding: 14px 16px; }
        .card-body { padding: 14px 16px; }
        .brief-item .brief-action { display: flex; flex-direction: column; gap: 4px; }
        .task-item .task-progress .progress-bar { width: 60px; }
        .chart-wrapper, .chart-wrapper.pie-wrapper { height: 200px; }
        .pipeline-steps { grid-template-columns: 1fr 1fr; }
        .pipeline-step { padding: 12px 14px; }
        .pipeline-step .step-count { font-size: 20px; }
    }
    @media (max-width: 480px) {
        .stats-grid { grid-template-columns: 1fr; }
        .stat-card .stat-icon { width: 44px; height: 44px; font-size: 20px; }
        .report-status-item { flex-wrap: wrap; }
        .report-status-item .report-time { margin-left: 0; width: 100%; padding-left: 92px; }
        .pipeline-steps { grid-template-columns: 1fr; }
    }
</style>
@endpush

@section('content')
    <!-- Navbar Component -->
    @include('layouts.navbar')

    <!-- Main Content Area -->
    <main class="dashboard-content">

    <!-- Greeting -->
    <div class="greeting">
        <h1>Halo, <span>Admin BSPS!</span></h1>
        <p>Ringkasan pelaksanaan verifikasi dan validasi calon penerima bantuan stimulan perumahan swadaya.</p>
    </div>

    <!-- Stats Cards (4 Cards) -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon blue">
                <i class="fas fa-home"></i>
            </div>
            <div class="stat-info">
                <div class="stat-value">{{ $totalKegiatan }}</div>
                <div class="stat-label">Total Data Verval</div>
                <div class="stat-change up">
                    <i class="fas fa-check-double"></i> Terdaftar Sistem
                </div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon green">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-info">
                <div class="stat-value">{{ $surveiSelesai }}</div>
                <div class="stat-label">Verval Selesai</div>
                <div class="stat-change up">
                    <i class="fas fa-camera"></i> Data & Foto Lengkap
                </div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon orange">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-info">
                <div class="stat-value">{{ $menungguSurvei }}</div>
                <div class="stat-label">Belum Disurvei</div>
                <div class="stat-change down">
                    <i class="fas fa-user-clock"></i> Perlu Penugasan
                </div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon red">
                <div class="stat-icon red" style="width:100%;height:100%;">
                    <i class="fas fa-file-signature"></i>
                </div>
            </div>
            <div class="stat-info">
                <div class="stat-value">{{ $bapTerbit }}</div>
                <div class="stat-label">Layak Bantuan</div>
                <div class="stat-change up">
                    <i class="fas fa-award"></i> Memenuhi Kriteria
                </div>
            </div>
        </div>
    </div>

    <!-- Chart Section -->
    <div class="chart-section">
        <!-- LINE CHART: Progres Kegiatan -->
        <div class="chart-card">
            <div class="card-header">
                <h3><i class="fas fa-chart-line" style="color:var(--primary);margin-right:10px;"></i>Progres Verval (7 Hari)</h3>
                <a href="{{ url('/dashboard') }}" class="card-action">Detail <i class="fas fa-arrow-right"></i></a>
            </div>
            <div class="card-body">
                <div class="chart-wrapper">
                    <canvas id="lineChart"></canvas>
                </div>
            </div>
        </div>

        <!-- PIE CHART: Status Kegiatan -->
        <div class="chart-card">
            <div class="card-header">
                <h3><i class="fas fa-chart-pie" style="color:var(--secondary);margin-right:10px;"></i>Status Verifikasi</h3>
                <span style="font-size:12px;color:var(--text-muted);">Total: {{ $totalKegiatan }} data</span>
            </div>
            <div class="card-body">
                <div class="chart-wrapper pie-wrapper">
                    <canvas id="pieChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Pipeline Kegiatan -->
    <div class="pipeline-section">
        <div class="section-header">
            <h3><i class="fas fa-stream" style="color:var(--primary);margin-right:10px;"></i>Alur Verifikasi &amp; Validasi</h3>
            <a href="{{ url('/dashboard') }}" class="view-all">Lihat semua <i class="fas fa-arrow-right"></i></a>
        </div>
        <div class="pipeline-steps">
            <div class="pipeline-step">
                <div class="step-icon blue"><i class="fas fa-file-alt"></i></div>
                <div class="step-count">{{ $pipeline['menunggu'] }}</div>
                <div class="step-label">Pengusulan</div>
                <div class="step-progress"><div class="step-fill blue" style="width:100%;"></div></div>
            </div>
            <div class="pipeline-step">
                <div class="step-icon orange"><i class="fas fa-user-check"></i></div>
                <div class="step-count">{{ $pipeline['proses'] }}</div>
                <div class="step-label">Verval Berkas</div>
                <div class="step-progress"><div class="step-fill orange" style="width:60%;"></div></div>
            </div>
            <div class="pipeline-step">
                <div class="step-icon purple"><i class="fas fa-clipboard-check"></i></div>
                <div class="step-count">{{ $pipeline['survei'] }}</div>
                <div class="step-label">Survei Lapangan</div>
                <div class="step-progress"><div class="step-fill purple" style="width:40%;"></div></div>
            </div>
            <div class="pipeline-step">
                <div class="step-icon green"><i class="fas fa-house-circle-check"></i></div>
                <div class="step-count">{{ $pipeline['bap'] }}</div>
                <div class="step-label">Rekomendasi Layak</div>
                <div class="step-progress"><div class="step-fill green" style="width:75%;"></div></div>
            </div>
            <div class="pipeline-step">
                <div class="step-icon red"><i class="fas fa-flag-checkered"></i></div>
                <div class="step-count">{{ $pipeline['selesai'] }}</div>
                <div class="step-label">Penerima SK</div>
                <div class="step-progress"><div class="step-fill red" style="width:100%;"></div></div>
            </div>
        </div>
    </div>

    <!-- Dashboard Grid (Bottom Layout) -->
    <div class="dashboard-grid">
        <!-- LEFT COLUMN -->
        <div class="col">
            <!-- Data Verval Terbaru -->
            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-home" style="color:var(--primary);margin-right:10px;"></i>Data Verval Calon Penerima Terbaru</h3>
                    <a href="{{ url('/survey') }}" class="card-action">Input Survey <i class="fas fa-plus-circle"></i></a>
                </div>
                <div class="card-body">
                    @forelse($latestKegiatan as $item)
                        @php
                            $statusColors = [
                                'selesai' => ['status' => 'green', 'bg' => 'var(--success)', 'pct' => '100%'],
                                'proses'  => ['status' => 'orange', 'bg' => 'var(--secondary)', 'pct' => '65%'],
                                'survei'  => ['status' => 'purple', 'bg' => 'var(--purple)', 'pct' => '50%'],
                                'menunggu'=> ['status' => 'blue', 'bg' => 'var(--primary)', 'pct' => '25%'],
                                'batal'   => ['status' => 'red', 'bg' => 'var(--danger)', 'pct' => '0%'],
                            ];
                            $c = $statusColors[$item->status] ?? ['status'=>'blue','bg'=>'var(--primary)','pct'=>'50%'];
                        @endphp
                        <div class="task-item">
                            <span class="task-status {{ $c['status'] }}"></span>
                            <div class="task-info">
                                <div class="task-title">{{ $item->nama_kegiatan }}</div>
                                <div class="task-meta">
                                    <span><i class="fas fa-location-dot"></i> Kec. {{ ucwords(str_replace('_',' ',$item->lokasi)) }}</span>
                                    <span><i class="fas fa-user"></i> {{ $item->nama_pemohon ?: 'Dinas PUPR' }}</span>
                                    <span class="badge-status {{ $item->status }}">{{ ucfirst($item->status) }}</span>
                                </div>
                            </div>
                            <div class="task-progress">
                                <div class="progress-bar">
                                    <div class="fill" style="width:{{ $c['pct'] }};background:{{ $c['bg'] }};"></div>
                                </div>
                                <span class="progress-text">{{ $c['pct'] }}</span>
                            </div>
                        </div>
                    @empty
                        <div style="text-align:center;padding:20px;color:var(--text-muted);">Belum ada data kegiatan.</div>
                    @endforelse
                </div>
            </div>

            <!-- Petugas Survei & Hasil Lapangan -->
            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-clipboard-check" style="color:var(--primary);margin-right:10px;"></i>Survei Petugas Lapangan</h3>
                    <a href="{{ url('/penugasan') }}" class="card-action">Kelola Penugasan <i class="fas fa-arrow-right"></i></a>
                </div>
                <div class="card-body">
                    @forelse($recentActivities as $log)
                        @php
                            $petugasName = $log->nama_petugas_1 ?: ($log->user->name ?? 'Petugas Survei');
                            $initial = strtoupper(substr($petugasName, 0, 1));
                        @endphp
                        <div class="brief-item">
                            <div class="brief-avatar">{{ $initial }}</div>
                            <div class="brief-info">
                                <div class="brief-title">{{ $log->nama_kegiatan ?? 'Survei Lapangan BSPS' }}</div>
                                <div class="brief-meta">
                                    <span><i class="fas fa-user"></i> {{ $petugasName }}</span>
                                    <span class="tag"><i class="fas fa-check-circle" style="color:var(--success);"></i> Verval Selesai</span>
                                </div>
                            </div>
                            <div class="brief-action">
                                <a href="{{ url('/geomaps') }}" class="btn-approve" style="text-decoration:none;"><i class="fas fa-map-marked-alt"></i> Geo Maps</a>
                            </div>
                        </div>
                    @empty
                        <div style="text-align:center;padding:20px;color:var(--text-muted);">Belum ada riwayat survei petugas.</div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- RIGHT COLUMN -->
        <div class="col">
            <!-- Aktivitas Terbaru -->
            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-bolt" style="color:var(--primary);margin-right:10px;"></i>Aktivitas Terbaru</h3>
                    <a href="{{ url('/setting') }}" class="card-action">Log lengkap <i class="fas fa-arrow-right"></i></a>
                </div>
                <div class="card-body">
                    @forelse($recentActivities as $log)
                        @php
                            $petugasName = $log->nama_petugas_1 ?: ($log->user->name ?? 'Petugas');
                        @endphp
                        <div class="activity-item">
                            <div class="activity-icon green"><i class="fas fa-check-circle"></i></div>
                            <div class="activity-content">
                                <div class="activity-text"><strong>{{ $petugasName }}</strong> menyelesaikan {{ $log->nama_kegiatan }}</div>
                                <div class="activity-time">{{ $log->waktu ?? 'Baru saja' }}</div>
                            </div>
                        </div>
                    @empty
                        <div class="activity-item">
                            <div class="activity-icon blue"><i class="fas fa-info-circle"></i></div>
                            <div class="activity-content">
                                <div class="activity-text">Sistem Siap Digunakan</div>
                                <div class="activity-time">Baru saja</div>
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Status Laporan Petugas -->
            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-users" style="color:var(--secondary);margin-right:10px;"></i>Status Petugas Survei</h3>
                    <a href="{{ url('/user') }}" class="card-action">Pengguna <i class="fas fa-arrow-right"></i></a>
                </div>
                <div class="card-body">
                    @forelse($petugasList as $p)
                        <div class="report-status-item">
                            <span class="report-user">{{ $p->name }}</span>
                            <div class="report-status"><span class="status-dot green"></span> Petugas Aktif</div>
                            <span class="report-time"><i class="fas fa-user-check" style="color:var(--success);"></i></span>
                        </div>
                    @empty
                        <div style="text-align:center;padding:15px;color:var(--text-muted);font-size:13px;">Belum ada akun petugas terdaftar.</div>
                    @endforelse
                </div>
            </div>

            <!-- Lokasi Aktif -->
            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-map-pin" style="color:var(--secondary);margin-right:10px;"></i>Lokasi Survei</h3>
                    <a href="{{ url('/geomaps') }}" class="card-action">Buka Peta <i class="fas fa-arrow-right"></i></a>
                </div>
                <div class="card-body">
                    @foreach(['Kaliwates', 'Sumbersari', 'Patrang', 'Ajung', 'Rambipuji'] as $idx => $kecName)
                        <div class="team-mini-item">
                            <div class="team-avatar">{{ substr($kecName, 0, 1) }}</div>
                            <div class="team-info">
                                <div class="team-name">Kec. {{ $kecName }}</div>
                                <div class="team-role">Wilayah Survei PUPR</div>
                            </div>
                            <span class="team-grade">A</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    </main>
@endsection

@push('scripts')
    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Theme Aware Chart Colors
            const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
            const chartTextColor = isDark ? '#F8FAFC' : '#0A192F';
            const chartGridColor = isDark ? 'rgba(255, 255, 255, 0.1)' : 'rgba(0, 40, 85, 0.05)';
            const chartTickColor = isDark ? '#CBD5E1' : '#64748B';
            const pieBorderColor = isDark ? '#152238' : '#ffffff';

            // LINE CHART - Progres Kegiatan (PUPR Color Palette)
            const lineCtx = document.getElementById('lineChart');
            if (lineCtx) {
                const ctx = lineCtx.getContext('2d');
                const gradient = ctx.createLinearGradient(0, 0, 0, 260);
                gradient.addColorStop(0, isDark ? 'rgba(56, 189, 248, 0.35)' : 'rgba(0, 40, 85, 0.25)');
                gradient.addColorStop(0.5, isDark ? 'rgba(56, 189, 248, 0.10)' : 'rgba(0, 40, 85, 0.08)');
                gradient.addColorStop(1, 'rgba(0, 40, 85, 0)');

                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'],
                        datasets: [{
                            label: 'Kegiatan Dilaksanakan',
                            data: [4, 7, 5, 9, 12, 8, 6],
                            borderColor: isDark ? '#38BDF8' : '#002855',
                            backgroundColor: gradient,
                            borderWidth: 3,
                            fill: true,
                            tension: 0.4,
                            pointBackgroundColor: isDark ? '#38BDF8' : '#002855',
                            pointBorderColor: '#FFB800',
                            pointBorderWidth: 2,
                            pointRadius: 5,
                            pointHoverRadius: 8,
                        }, {
                            label: 'Survei Dilakukan',
                            data: [2, 3, 2, 5, 6, 4, 3],
                            borderColor: '#FFB800',
                            backgroundColor: 'transparent',
                            borderWidth: 3,
                            borderDash: [6, 4],
                            fill: false,
                            tension: 0.4,
                            pointBackgroundColor: '#FFB800',
                            pointBorderColor: isDark ? '#38BDF8' : '#002855',
                            pointBorderWidth: 2,
                            pointRadius: 5,
                            pointHoverRadius: 8,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: { intersect: false, mode: 'index' },
                        plugins: {
                            legend: {
                                display: true,
                                position: 'top',
                                labels: {
                                    usePointStyle: true,
                                    pointStyle: 'circle',
                                    padding: 20,
                                    font: { size: 12, weight: '700', family: 'Inter' },
                                    color: chartTextColor
                                }
                            },
                            tooltip: {
                                backgroundColor: 'rgba(15, 23, 42, 0.95)',
                                titleColor: '#FFB800',
                                bodyColor: '#ffffff',
                                borderColor: 'rgba(255, 184, 0, 0.3)',
                                borderWidth: 1,
                                padding: 12,
                                cornerRadius: 10,
                                callbacks: {
                                    label: function(context) {
                                        let label = context.dataset.label || '';
                                        let value = context.parsed.y;
                                        return label + ': ' + value + ' kegiatan';
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                grid: { display: false },
                                ticks: { font: { size: 11, weight: '500' }, color: chartTickColor }
                            },
                            y: {
                                grid: { color: chartGridColor, drawBorder: false },
                                ticks: { font: { size: 11, weight: '500' }, color: chartTickColor, stepSize: 2 },
                                beginAtZero: true
                            }
                        }
                    }
                });
            }

            // PIE CHART - Status Kegiatan (PUPR Theme)
            const pieCtx = document.getElementById('pieChart');
            if (pieCtx) {
                const ctx = pieCtx.getContext('2d');
                new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Selesai', 'Proses', 'Menunggu', 'Survei', 'Batal'],
                        datasets: [{
                            data: [{{ $statusCounts['selesai'] }}, {{ $statusCounts['proses'] }}, {{ $statusCounts['menunggu'] }}, {{ $statusCounts['survei'] }}, {{ $statusCounts['batal'] }}],
                            backgroundColor: ['#27ae60', '#FFB800', isDark ? '#38BDF8' : '#002855', '#8e44ad', '#e74c3c'],
                            borderColor: pieBorderColor,
                            borderWidth: 3,
                            hoverOffset: 12
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '65%',
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    usePointStyle: true,
                                    pointStyle: 'circle',
                                    padding: 16,
                                    font: { size: 12, weight: '600', family: 'Inter' },
                                    color: chartTextColor
                                }
                            },
                            tooltip: {
                                backgroundColor: 'rgba(15, 23, 42, 0.95)',
                                titleColor: '#FFB800',
                                bodyColor: '#ffffff',
                                borderColor: 'rgba(255, 184, 0, 0.3)',
                                borderWidth: 1,
                                padding: 12,
                                cornerRadius: 10,
                                callbacks: {
                                    label: function(context) {
                                        let total = context.dataset.data.reduce((a, b) => a + b, 0);
                                        let percentage = ((context.parsed / total) * 100).toFixed(0);
                                        return context.label + ': ' + context.parsed + ' (' + percentage + '%)';
                                    }
                                }
                            }
                        }
                    }
                });
            }

            // TOMBOL INTERAKSI KHUSUS DASHBOARD
            document.querySelectorAll('.btn-approve').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    const briefItem = this.closest('.brief-item');
                    const title = briefItem ? briefItem.querySelector('.brief-title').textContent : 'Kegiatan';
                    alert('Buka Geo Maps untuk: ' + title.trim() + '\n\nFungsi: Menampilkan titik koordinat survei di peta interaktif.');
                });
            });

            document.querySelectorAll('.btn-reject').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    const briefItem = this.closest('.brief-item');
                    const title = briefItem ? briefItem.querySelector('.brief-title').textContent : 'Kegiatan';
                    alert('Generate BAP PDF untuk: ' + title.trim() + '\n\nFungsi: Menghasilkan Berita Acara Pemeriksaan dalam format PDF.');
                });
            });

            const bannerBtnPrimary = document.querySelector('.hot-issue-banner .btn-primary');
            if (bannerBtnPrimary) {
                bannerBtnPrimary.addEventListener('click', function() {
                    alert('Membuka form Input Data Mingguan\n\nFungsi: Admin menginput data progres kegiatan per minggu.');
                });
            }

            const bannerBtnOutline = document.querySelector('.hot-issue-banner .btn-outline');
            if (bannerBtnOutline) {
                bannerBtnOutline.addEventListener('click', function() {
                    alert('Menampilkan semua data kegiatan minggu ini.');
                });
            }
        });
    </script>
@endpush