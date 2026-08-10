@extends('layouts.partial.app')

@section('title', 'BSPS Verval - Dashboard')
@section('title_header', 'Dashboard BSPS Verval')
@section('subtitle_header', \Carbon\Carbon::now()->translatedFormat('l, d F Y') . ' - Monitoring Verifikasi & Validasi BSPS')

@push('styles')
<style>
    /* Hero Banner */
    .dashboard-hero-bsps {
        background: linear-gradient(135deg, #001737 0%, #002855 60%, #003E75 100%);
        border-radius: var(--radius);
        padding: 28px 32px;
        color: #ffffff;
        margin-bottom: 24px;
        box-shadow: 0 10px 25px rgba(0, 40, 85, 0.15);
        position: relative;
        overflow: hidden;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 20px;
    }

    .dashboard-hero-bsps::after {
        content: '';
        position: absolute;
        right: -30px;
        bottom: -30px;
        width: 250px;
        height: 250px;
        background: radial-gradient(circle, rgba(255, 184, 0, 0.15) 0%, rgba(255, 184, 0, 0) 70%);
        border-radius: 50%;
        pointer-events: none;
    }

    .hero-badge-bsps {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: rgba(255, 184, 0, 0.2);
        color: #FFB800;
        border: 1px solid rgba(255, 184, 0, 0.4);
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 11.5px;
        font-weight: 800;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        margin-bottom: 8px;
    }

    /* Stats Grid */
    .stats-grid-bsps {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 18px;
        margin-bottom: 24px;
    }

    .stat-card-bsps {
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

    .stat-card-bsps:hover {
        transform: translateY(-3px);
        box-shadow: var(--shadow-md);
    }

    .stat-icon-bsps {
        width: 52px;
        height: 52px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        flex-shrink: 0;
    }

    .stat-info-bsps .num {
        font-size: 24px;
        font-weight: 900;
        color: var(--primary);
        line-height: 1.1;
    }

    .stat-info-bsps .lbl {
        font-size: 12.5px;
        color: var(--text-muted);
        font-weight: 600;
        margin-top: 4px;
    }

    /* Pipeline BSPS */
    .pipeline-card {
        background: var(--bg-card);
        border-radius: var(--radius);
        padding: 24px;
        margin-bottom: 24px;
        box-shadow: var(--shadow-sm);
        border: 1px solid rgba(0, 40, 85, 0.06);
    }

    .pipeline-steps {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 12px;
        margin-top: 18px;
    }

    .pipeline-step {
        background: var(--bg-body);
        border: 1px solid rgba(0, 40, 85, 0.08);
        border-radius: var(--radius-sm);
        padding: 14px 16px;
        text-align: center;
        position: relative;
    }

    .pipeline-step .step-number {
        font-size: 11px;
        font-weight: 800;
        color: var(--secondary);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .pipeline-step .step-val {
        font-size: 20px;
        font-weight: 900;
        color: var(--primary);
        margin: 4px 0 2px;
    }

    .pipeline-step .step-label {
        font-size: 11.5px;
        color: var(--text-muted);
        font-weight: 600;
    }

    /* Main Section Grid (2 Columns) */
    .dashboard-two-col {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 24px;
    }

    .content-card {
        background: var(--bg-card);
        border-radius: var(--radius);
        box-shadow: var(--shadow-sm);
        border: 1px solid rgba(0, 40, 85, 0.06);
        overflow: hidden;
    }

    .card-header-custom {
        padding: 16px 20px;
        border-bottom: 1px solid rgba(0, 40, 85, 0.06);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .activity-item {
        padding: 14px 20px;
        border-bottom: 1px solid rgba(0, 40, 85, 0.05);
        display: flex;
        align-items: flex-start;
        gap: 12px;
    }

    .activity-item:last-child {
        border-bottom: none;
    }

    @media (max-width: 992px) {
        .stats-grid-bsps {
            grid-template-columns: repeat(2, 1fr);
        }
        .pipeline-steps {
            grid-template-columns: repeat(2, 1fr);
        }
        .dashboard-two-col {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 576px) {
        .stats-grid-bsps {
            grid-template-columns: 1fr;
        }
        .pipeline-steps {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')
<div class="content-body" style="padding: 24px;">

    <!-- Hero Banner -->
    <div class="dashboard-hero-bsps">
        <div style="position: relative; z-index: 2;">
            <div class="hero-badge-bsps">
                <i class="fas fa-home-user"></i> Program BSPS 2026
            </div>
            <h1 style="font-size: 22px; font-weight: 900; margin: 0 0 6px; color: #fff;">
                Sistem Verifikasi &amp; Validasi Bantuan Stimulan Perumahan Swadaya
            </h1>
            <p style="margin: 0; font-size: 13.5px; opacity: 0.85; max-width: 600px;">
                Monitoring terpadu data calon penerima bantuan rumah tidak layak huni (RTLH) di Kabupaten Jember secara akurat, transparan, dan terverifikasi di lapangan.
            </p>
        </div>
        <div style="position: relative; z-index: 2; display: flex; gap: 10px;">
            <a href="{{ url('/data-verval') }}" class="btn" style="background: #FFB800; color: #001737; font-weight: 800; border: none; padding: 10px 18px;">
                <i class="fas fa-clipboard-check"></i> Buka Data Verval
            </a>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="stats-grid-bsps">
        <div class="stat-card-bsps">
            <div class="stat-icon-bsps" style="background: rgba(0, 40, 85, 0.08); color: var(--primary);">
                <i class="fas fa-users-viewfinder"></i>
            </div>
            <div class="stat-info-bsps">
                <div class="num">{{ number_format($stats['total_usulan']) }}</div>
                <div class="lbl">Total Usulan BNBA</div>
            </div>
        </div>

        <div class="stat-card-bsps">
            <div class="stat-icon-bsps" style="background: rgba(39, 174, 96, 0.12); color: var(--success);">
                <i class="fas fa-check-double"></i>
            </div>
            <div class="stat-info-bsps">
                <div class="num" style="color: var(--success);">{{ number_format($stats['lolos_verval']) }}</div>
                <div class="lbl">Lolos Verval (MS)</div>
            </div>
        </div>

        <div class="stat-card-bsps">
            <div class="stat-icon-bsps" style="background: rgba(243, 156, 18, 0.12); color: var(--warning);">
                <i class="fas fa-hourglass-half"></i>
            </div>
            <div class="stat-info-bsps">
                <div class="num" style="color: #d68910;">{{ number_format($stats['menunggu_survei']) }}</div>
                <div class="lbl">Menunggu Survei</div>
            </div>
        </div>

        <div class="stat-card-bsps">
            <div class="stat-icon-bsps" style="background: rgba(255, 184, 0, 0.15); color: #d69e00;">
                <i class="fas fa-hand-holding-dollar"></i>
            </div>
            <div class="stat-info-bsps">
                <div class="num" style="font-size: 19px; color: var(--primary);">{{ $stats['total_anggaran'] }}</div>
                <div class="lbl">Total Pagu Anggaran</div>
            </div>
        </div>
    </div>

    <!-- Pipeline Tahapan Program -->
    <div class="pipeline-card">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <div style="font-size: 15px; font-weight: 800; color: var(--primary);">
                <i class="fas fa-diagram-project" style="color: var(--secondary); margin-right: 6px;"></i>
                Progress Tahapan Program BSPS
            </div>
            <span style="font-size: 12px; color: var(--text-muted);">
                Target Penyaluran: 100% Tepat Sasaran
            </span>
        </div>

        <div class="pipeline-steps">
            <div class="pipeline-step">
                <div class="step-number">Tahap 1</div>
                <div class="step-val">{{ $stats['progress_tahapan']['usulan'] }}</div>
                <div class="step-label">Usulan BNBA</div>
            </div>
            <div class="pipeline-step">
                <div class="step-number">Tahap 2</div>
                <div class="step-val">{{ $stats['progress_tahapan']['verval_admin'] }}</div>
                <div class="step-label">Verval Administrasi</div>
            </div>
            <div class="pipeline-step" style="border-color: rgba(39,174,96,0.4); background: rgba(39,174,96,0.04);">
                <div class="step-number" style="color: var(--success);">Tahap 3</div>
                <div class="step-val" style="color: var(--success);">{{ $stats['progress_tahapan']['survei_fisik'] }}</div>
                <div class="step-label">Survei Lapangan RTLH</div>
            </div>
            <div class="pipeline-step">
                <div class="step-number">Tahap 4</div>
                <div class="step-val">{{ $stats['progress_tahapan']['penetapan_sk'] }}</div>
                <div class="step-label">Penetapan SK Bupati</div>
            </div>
            <div class="pipeline-step">
                <div class="step-number">Tahap 5</div>
                <div class="step-val">{{ $stats['progress_tahapan']['pencairan_dana'] }}</div>
                <div class="step-label">Penyaluran Bantuan</div>
            </div>
        </div>
    </div>

    <!-- 2 Columns Grid: Recent Table & Activity Logs -->
    <div class="dashboard-two-col">

        <!-- Left: Recent Verval Submissions -->
        <div class="content-card">
            <div class="card-header-custom">
                <div style="font-size: 14px; font-weight: 800; color: var(--primary);">
                    <i class="fas fa-list-check" style="color: var(--secondary); margin-right: 6px;"></i>
                    Data Verifikasi Terkini
                </div>
                <a href="{{ url('/data-verval') }}" style="font-size: 12px; font-weight: 700; color: var(--primary); text-decoration: none;">
                    Lihat Semua <i class="fas fa-arrow-right" style="font-size: 10px;"></i>
                </a>
            </div>

            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; font-size: 13px;">
                    <thead>
                        <tr style="background: rgba(0, 40, 85, 0.02); text-align: left; border-bottom: 1px solid rgba(0,40,85,0.06);">
                            <th style="padding: 10px 16px; color: var(--primary);">Nama Calon Penerima</th>
                            <th style="padding: 10px 16px; color: var(--primary);">Lokasi RTLH</th>
                            <th style="padding: 10px 16px; color: var(--primary);">Skor</th>
                            <th style="padding: 10px 16px; color: var(--primary);">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach(array_slice($vervalList, 0, 5) as $item)
                            <tr style="border-bottom: 1px solid rgba(0,40,85,0.04);">
                                <td style="padding: 12px 16px;">
                                    <div style="font-weight: 700; color: var(--text-primary);">{{ $item['nama_kk'] }}</div>
                                    <div style="font-size: 11px; font-family: monospace; color: var(--text-muted);">{{ $item['nik'] }}</div>
                                </td>
                                <td style="padding: 12px 16px;">
                                    <div>Desa {{ $item['desa'] }}</div>
                                    <div style="font-size: 11px; color: var(--text-muted);">Kec. {{ $item['kecamatan'] }}</div>
                                </td>
                                <td style="padding: 12px 16px;">
                                    <span style="font-weight: 800; font-size: 12px; color: var(--primary);">
                                        {{ $item['skor_kelaikan'] }}%
                                    </span>
                                </td>
                                <td style="padding: 12px 16px;">
                                    @if($item['status_badge'] === 'success')
                                        <span class="badge success" style="font-size: 11px; padding: 2px 8px;">{{ $item['status_verval'] }}</span>
                                    @elseif($item['status_badge'] === 'warning')
                                        <span class="badge warning" style="font-size: 11px; padding: 2px 8px;">{{ $item['status_verval'] }}</span>
                                    @else
                                        <span class="badge danger" style="font-size: 11px; padding: 2px 8px;">{{ $item['status_verval'] }}</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Right: Recent Field Activity Logs -->
        <div class="content-card">
            <div class="card-header-custom">
                <div style="font-size: 14px; font-weight: 800; color: var(--primary);">
                    <i class="fas fa-user-clock" style="color: var(--secondary); margin-right: 6px;"></i>
                    Aktivitas TFL Lapangan
                </div>
            </div>

            <div>
                @foreach($recentActivities as $act)
                    <div class="activity-item">
                        <div style="width: 32px; height: 32px; border-radius: 50%; background: rgba(0, 40, 85, 0.08); color: var(--primary); display: flex; align-items: center; justify-content: center; font-size: 13px; flex-shrink: 0;">
                            <i class="fas fa-check"></i>
                        </div>
                        <div style="flex: 1; min-width: 0;">
                            <div style="font-size: 12.5px; font-weight: 700; color: var(--primary-dark);">
                                {{ $act['petugas'] }}
                            </div>
                            <div style="font-size: 11.5px; color: var(--text-secondary); margin-top: 2px;">
                                {{ $act['aktivitas'] }}
                            </div>
                            <div style="font-size: 10.5px; color: var(--text-muted); margin-top: 4px;">
                                <i class="fas fa-clock"></i> {{ $act['waktu'] }}
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

    </div>

</div>
@endsection