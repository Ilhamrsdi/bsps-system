@extends('layouts.partial.app')

@section('title', 'BSPS Verval - Dashboard')
@section('title_header', 'Dashboard BSPS')
@section('subtitle_header', \Carbon\Carbon::now()->translatedFormat('l, d F Y') . ' - Analisis Data Calon Penerima BSPS Berdasarkan Pengelompokan Desil Kabupaten Jember')

@push('styles')
<style>
    /* Stats Grid */
    .stats-grid {
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

    /* 2 Card Kelayakan Utama (Layak vs Tidak Layak) */
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

    /* Accordion Capaian Global Desa */
    .global-verval-card {
        background: var(--bg-card);
        border-radius: var(--radius);
        box-shadow: var(--shadow-sm);
        border: 1px solid rgba(0, 40, 85, 0.08);
        margin-bottom: 26px;
        overflow: hidden;
    }

    .global-verval-header {
        padding: 20px 24px;
        background: linear-gradient(135deg, #002855 0%, #003b7a 100%);
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
    }

    .global-verval-header .title-area h2 {
        font-size: 17px;
        font-weight: 800;
        margin: 0 0 4px 0;
        display: flex;
        align-items: center;
        gap: 10px;
        color: #ffffff;
    }

    .global-verval-header .title-area p {
        font-size: 12.5px;
        color: #cbd5e1;
        margin: 0;
    }

    .global-stats-pills {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .global-stat-badge {
        background: rgba(255, 255, 255, 0.12);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 8px;
        padding: 6px 12px;
        font-size: 12px;
        color: #ffffff;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .global-stat-badge strong {
        font-size: 13px;
        color: #ffb800;
    }

    .global-stat-badge.badge-layak strong {
        color: #4ade80;
    }

    .global-verval-toolbar {
        padding: 14px 24px;
        background: #f8fafc;
        border-bottom: 1px solid rgba(0, 40, 85, 0.08);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        flex-wrap: wrap;
    }

    .search-kec-input-wrap {
        position: relative;
        flex: 1;
        max-width: 380px;
    }

    .search-kec-input-wrap i {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
        font-size: 13px;
    }

    .search-kec-input {
        width: 100%;
        padding: 9px 14px 9px 36px;
        border-radius: 8px;
        border: 1px solid #cbd5e1;
        font-size: 13px;
        font-weight: 500;
        background: #ffffff;
        color: #0f172a;
        outline: none;
        transition: var(--transition);
    }

    .search-kec-input:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(0, 40, 85, 0.1);
    }

    .accordion-kecamatan-list {
        padding: 16px 20px;
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .accordion-kec-item {
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        background: #ffffff;
        transition: all 0.25s ease;
        overflow: hidden;
    }

    .accordion-kec-item:hover {
        border-color: #cbd5e1;
        box-shadow: 0 4px 12px rgba(0, 40, 85, 0.05);
    }

    .accordion-kec-item.active {
        border-color: #002855;
        box-shadow: 0 6px 18px rgba(0, 40, 85, 0.08);
    }

    .accordion-kec-header {
        padding: 14px 18px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        user-select: none;
        background: #ffffff;
        transition: background 0.2s ease;
    }

    .accordion-kec-item.active .accordion-kec-header {
        background: #f1f5f9;
        border-bottom: 1px solid #e2e8f0;
    }

    .kec-header-left {
        display: flex;
        align-items: center;
        gap: 12px;
        min-width: 220px;
    }

    .kec-icon-circle {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        background: #002855;
        color: #ffb800;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 15px;
        flex-shrink: 0;
    }

    .kec-title-wrap h4 {
        font-size: 14.5px;
        font-weight: 800;
        color: #002855;
        margin: 0;
    }

    .kec-title-wrap span {
        font-size: 11.5px;
        color: #64748b;
        font-weight: 600;
    }

    .kec-header-center {
        display: flex;
        align-items: center;
        gap: 20px;
        flex: 1;
        flex-wrap: wrap;
        justify-content: flex-end;
    }

    .kec-stat-item {
        display: flex;
        flex-direction: column;
        align-items: flex-end;
    }

    .kec-stat-label {
        font-size: 10.5px;
        font-weight: 700;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }

    .kec-stat-val {
        font-size: 13.5px;
        font-weight: 800;
        color: #0f172a;
    }

    .kec-stat-val.layak { color: #16a34a; }
    .kec-stat-val.tidak { color: #dc2626; }

    .kec-progress-bar-wrap {
        width: 140px;
    }

    .kec-progress-bar-bg {
        height: 7px;
        background: #e2e8f0;
        border-radius: 4px;
        overflow: hidden;
        margin-top: 3px;
    }

    .kec-progress-bar-fill {
        height: 100%;
        background: linear-gradient(90deg, #002855, #27ae60);
        border-radius: 4px;
        transition: width 0.4s ease;
    }

    .kec-header-toggle-btn {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        background: #f1f5f9;
        color: #002855;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        transition: transform 0.25s ease, background 0.2s ease;
        flex-shrink: 0;
    }

    .accordion-kec-item.active .kec-header-toggle-btn {
        transform: rotate(180deg);
        background: #002855;
        color: #ffffff;
    }

    .accordion-kec-body {
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.35s ease;
        background: #f8fafc;
    }

    .accordion-kec-item.active .accordion-kec-body {
        max-height: 3000px;
    }

    /* Grid Kartu-Kartu Desa */
    .desa-cards-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 14px;
        padding: 18px 20px;
    }

    .desa-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 14px 16px;
        box-shadow: 0 2px 6px rgba(0, 40, 85, 0.03);
        transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
        position: relative;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .desa-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(0, 40, 85, 0.08);
        border-color: #cbd5e1;
    }

    .desa-card-top {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        margin-bottom: 10px;
        gap: 8px;
    }

    .desa-card-name {
        font-size: 13.5px;
        font-weight: 800;
        color: #002855;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .desa-card-badge {
        font-size: 10px;
        font-weight: 800;
        padding: 2px 8px;
        border-radius: 12px;
        white-space: nowrap;
    }

    .badge-done { background: #dcfce7; color: #15803d; border: 1px solid #86efac; }
    .badge-progress { background: #dbeafe; color: #1d4ed8; border: 1px solid #93c5fd; }
    .badge-none { background: #f1f5f9; color: #64748b; border: 1px solid #cbd5e1; }

    .desa-card-progress {
        margin-bottom: 12px;
    }

    .desa-prog-header {
        display: flex;
        justify-content: space-between;
        font-size: 11px;
        margin-bottom: 3px;
        color: #475569;
        font-weight: 600;
    }

    .desa-prog-bg {
        height: 6px;
        background: #e2e8f0;
        border-radius: 3px;
        overflow: hidden;
    }

    .desa-prog-fill {
        height: 100%;
        background: #22c55e;
        border-radius: 3px;
    }

    .desa-card-metric-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 8px;
        margin-bottom: 12px;
    }

    .desa-metric-box {
        padding: 8px 10px;
        border-radius: 6px;
        background: #f8fafc;
        border: 1px solid #f1f5f9;
    }

    .desa-metric-box.layak {
        background: #f0fdf4;
        border-color: #bbf7d0;
    }

    .desa-metric-box.tidak {
        background: #fef2f2;
        border-color: #fecaca;
    }

    .desa-metric-box .label {
        font-size: 9.5px;
        font-weight: 700;
        text-transform: uppercase;
        color: #64748b;
        margin-bottom: 2px;
    }

    .desa-metric-box.layak .label { color: #15803d; }
    .desa-metric-box.tidak .label { color: #b91c1c; }

    .desa-metric-box .val {
        font-size: 13px;
        font-weight: 800;
        color: #0f172a;
    }

    .desa-metric-box.layak .val { color: #16a34a; }
    .desa-metric-box.tidak .val { color: #dc2626; }

    .desa-metric-box .sub-val {
        font-size: 10px;
        font-weight: 600;
        color: #64748b;
    }

    .desa-card-footer {
        border-top: 1px solid #f1f5f9;
        padding-top: 8px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-size: 11px;
    }

    .desa-card-footer a {
        color: #002855;
        font-weight: 700;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    .desa-card-footer a:hover {
        color: #ffb800;
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
            <!-- 1. Total Calon Penerima (Klik untuk Buka/Tutup Rekap Capaian Seluruh Desa) -->
            <div class="stat-card" id="cardTotalCalonPenerima" onclick="toggleGlobalVervalSection()" style="cursor: pointer; position: relative; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);" title="Klik untuk membuka / menutup Rekapitulasi Capaian Seluruh Desa">
                <div class="stat-icon blue">
                    <i class="fas fa-users-viewfinder"></i>
                </div>
                <div class="stat-info" style="width: 100%;">
                    <div style="display: flex; align-items: center; justify-content: space-between;">
                        <div class="stat-value">{{ number_format($totalPenerima, 0, ',', '.') }}</div>
                        <span id="badgeToggleGlobalVerval" style="font-size: 10.5px; font-weight: 800; background: rgba(0, 40, 85, 0.08); color: #002855; padding: 2px 7px; border-radius: 6px; display: inline-flex; align-items: center; gap: 4px;">
                            <i class="fas fa-chevron-down" id="iconToggleGlobalVerval"></i>
                            <span id="textToggleGlobalVerval">Lihat Kelayakan</span>
                        </span>
                    </div>
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
                        <i class="fas fa-percent"></i> {{ $totalPenerima > 0 ? round(($backlog2Count / $totalPenerima) * 100, 1) : 0 }}% Dominan (PK)
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
                        <i class="fas fa-award"></i> {{ $totalPenerima > 0 ? round(($backlog1Count / $totalPenerima) * 100, 1) : 0 }}% Prioritas Bantuan
                    </div>
                </div>
            </div>

            <!-- 4. Usulan Baru Petugas -->
            <div class="stat-card">
                <div class="stat-icon orange">
                    <i class="fas fa-user-plus"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value">{{ number_format($usulanBaruCount, 0, ',', '.') }}</div>
                    <div class="stat-label">Usulan Baru Petugas</div>
                    <div class="stat-change orange" style="color:#d69e00;">
                        <i class="fas fa-file-signature"></i> {{ $totalPenerima > 0 ? round(($usulanBaruCount / $totalPenerima) * 100, 1) : 0 }}% Usulan Lapangan
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

            <!-- 5. Desa / Kelurahan (Klik untuk Buka Tabel Target & Progres Seluruh Desa) -->
            <div class="stat-card" onclick="window.location.href='{{ route('dashboard.rekap-desa') }}'" style="cursor: pointer; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);" title="Klik untuk melihat tabel rekapitulasi target & progres seluruh {{ $totalDesa }} Desa/Kelurahan">
                <div class="stat-icon orange">
                    <i class="fas fa-city"></i>
                </div>
                <div class="stat-info" style="width: 100%;">
                    <div style="display: flex; align-items: center; justify-content: space-between;">
                        <div class="stat-value">{{ $totalDesa }}</div>
                        <span style="font-size: 10.5px; font-weight: 800; background: rgba(0, 40, 85, 0.08); color: #002855; padding: 2px 7px; border-radius: 6px; display: inline-flex; align-items: center; gap: 4px;">
                            <span>Lihat Rekap</span>
                            <i class="fas fa-arrow-right" style="font-size: 9px;"></i>
                        </span>
                    </div>
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

        <!-- SECTION CAPAIAN VERVAL GLOBAL & DROP-EXPAND DESA CARDS (KESELURUHAN KABUPATEN JEMBER) -->
        <div class="global-verval-card" id="globalVervalSection" style="display: none;">
            <!-- 2 CARD UTAMA: KELAYAKAN HASIL VERVAL (KLIK UNTUK PINDAH KE TABEL RINCIAN) -->
            <div style="padding: 20px 20px 12px 20px; display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 18px; background: transparent;">
                <!-- 1. CARD LAYAK DIUSULKAN -->
                <a href="{{ route('dashboard.data-kelayakan', ['status' => 'layak']) }}" class="kelayakan-action-card card-layak" style="text-decoration: none;" title="Klik untuk membuka tabel rincian data calon penerima Layak Diusulkan">
                    <div class="card-top-icon">
                        <div class="icon-circle icon-green">
                            <i class="fas fa-circle-check"></i>
                        </div>
                        <span class="badge-status-pill pill-green">
                            <i class="fas fa-award"></i> Memenuhi Kriteria (≥2 RTLH)
                        </span>
                    </div>
                    <div class="card-main-metric">
                        <div class="metric-val text-green">{{ number_format($globalVervalStats['total_layak']) }} <span class="unit">KK</span></div>
                        <div class="metric-title">CALON PENERIMA LAYAK DIUSULKAN</div>
                    </div>
                    <div class="card-footer-info">
                        <div class="pct-text">
                            <strong>{{ $globalVervalStats['persen_layak'] }}%</strong> dari total verval selesai
                        </div>
                        <div class="btn-goto-table text-green">
                            <span>Buka Tabel Data Layak</span>
                            <i class="fas fa-arrow-right"></i>
                        </div>
                    </div>
                </a>

                <!-- 2. CARD TIDAK LAYAK DIUSULKAN -->
                <a href="{{ route('dashboard.data-kelayakan', ['status' => 'tidak_layak']) }}" class="kelayakan-action-card card-tidak" style="text-decoration: none;" title="Klik untuk membuka tabel rincian data calon penerima Tidak Layak">
                    <div class="card-top-icon">
                        <div class="icon-circle icon-red">
                            <i class="fas fa-circle-xmark"></i>
                        </div>
                        <span class="badge-status-pill pill-red">
                            <i class="fas fa-circle-exclamation"></i> Tidak Memenuhi (&lt;2 RTLH)
                        </span>
                    </div>
                    <div class="card-main-metric">
                        <div class="metric-val text-red">{{ number_format($globalVervalStats['total_tidak_layak']) }} <span class="unit">KK</span></div>
                        <div class="metric-title">CALON PENERIMA TIDAK LAYAK</div>
                    </div>
                    <div class="card-footer-info">
                        <div class="pct-text">
                            <strong>{{ $globalVervalStats['total_sudah'] > 0 ? round(($globalVervalStats['total_tidak_layak'] / $globalVervalStats['total_sudah']) * 100, 1) : 0 }}%</strong> dari total verval selesai
                        </div>
                        <div class="btn-goto-table text-red">
                            <span>Buka Tabel Data Tidak Layak</span>
                            <i class="fas fa-arrow-right"></i>
                        </div>
                    </div>
                </a>
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
                            <a href="{{ route('dashboard.desa', ['kecamatan' => $desa->kecamatan, 'desa' => $desa->desa_kelurahan]) }}" style="display:flex;align-items:center;justify-content:space-between;padding:10px 0;border-bottom:1px solid rgba(0,40,85,0.05);font-size:13px;text-decoration:none;transition:var(--transition);" title="Lihat Capaian & Kelayakan Desa {{ $desa->desa_kelurahan }}">
                                <div>
                                    <div style="font-weight:700;color:var(--primary-dark);display:flex;align-items:center;gap:5px;">
                                        <span>Desa {{ ucwords(strtolower($desa->desa_kelurahan)) }}</span>
                                        <i class="fas fa-arrow-up-right-from-square" style="font-size:10px;color:var(--primary);opacity:0.7;"></i>
                                    </div>
                                    <div style="font-size:11px;color:var(--text-muted);">Kec. {{ ucwords(strtolower($desa->kecamatan)) }}</div>
                                </div>
                                <span style="font-weight:800;color:var(--primary);background:rgba(0,40,85,0.06);padding:4px 10px;border-radius:12px;font-size:12px;">
                                    {{ number_format($desa->total, 0, ',', '.') }} KK
                                </span>
                            </a>
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

// Toggle Open / Close Global Verval Section when Total Calon Penerima card is clicked
function toggleGlobalVervalSection() {
    const sec = document.getElementById('globalVervalSection');
    const icon = document.getElementById('iconToggleGlobalVerval');
    const text = document.getElementById('textToggleGlobalVerval');
    const card = document.getElementById('cardTotalCalonPenerima');
    if (!sec) return;

    if (sec.style.display === 'none' || !sec.style.display) {
        sec.style.display = 'block';
        if (icon) icon.className = 'fas fa-chevron-up';
        if (text) text.textContent = 'Tutup';
        if (card) {
            card.style.borderColor = '#002855';
            card.style.boxShadow = '0 6px 20px rgba(0, 40, 85, 0.15)';
        }
        setTimeout(() => {
            sec.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }, 50);
    } else {
        sec.style.display = 'none';
        if (icon) icon.className = 'fas fa-chevron-down';
        if (text) text.textContent = 'Lihat Kelayakan';
        if (card) {
            card.style.borderColor = '';
            card.style.boxShadow = '';
        }
    }
}
</script>
@endpush
