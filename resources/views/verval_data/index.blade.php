@extends('layouts.partial.app')

@section('title', 'BSPS Verval - Data Verifikasi & Validasi')
@section('title_header', 'Data Verval BSPS')
@section('subtitle_header', 'Database Calon Penerima Bantuan Stimulan Perumahan Swadaya (BSPS) Kabupaten Jember')

@push('styles')
<style>
    /* Stats Grid */
    .stats-verval {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
        margin-bottom: 24px;
    }

    .stat-item {
        background: var(--bg-card);
        border-radius: var(--radius-sm);
        padding: 18px 20px;
        box-shadow: var(--shadow-sm);
        border: 1px solid rgba(0, 40, 85, 0.06);
        display: flex;
        align-items: center;
        gap: 14px;
        transition: var(--transition);
    }

    .stat-item:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
    }

    .stat-item .icon {
        width: 46px;
        height: 46px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        flex-shrink: 0;
    }

    .stat-item .icon.blue { background: rgba(0, 40, 85, 0.10); color: var(--primary); }
    .stat-item .icon.green { background: rgba(39, 174, 96, 0.12); color: var(--success); }
    .stat-item .icon.orange { background: rgba(255, 184, 0, 0.15); color: #d69e00; }
    .stat-item .icon.purple { background: rgba(142, 68, 173, 0.12); color: var(--purple, #8e44ad); }

    .stat-item .info .value {
        font-size: 24px;
        font-weight: 800;
        line-height: 1.2;
        color: var(--primary-dark);
    }

    .stat-item .info .label {
        font-size: 12px;
        color: var(--text-muted);
        font-weight: 600;
        margin-top: 2px;
    }

    /* Filter Section */
    .filter-section {
        background: var(--bg-card);
        border-radius: var(--radius);
        padding: 16px 20px;
        box-shadow: var(--shadow-sm);
        border: 1px solid rgba(0, 40, 85, 0.06);
        margin-bottom: 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
    }

    .filter-left {
        display: flex;
        align-items: center;
        gap: 12px;
        flex: 1;
        flex-wrap: wrap;
    }

    .search-input-wrap {
        position: relative;
        min-width: 260px;
        flex: 1;
    }

    .search-input-wrap input {
        width: 100%;
        padding: 10px 14px 10px 38px;
        border-radius: var(--radius-sm);
        border: 1px solid rgba(0, 40, 85, 0.14);
        background: var(--bg-body);
        color: var(--text-primary);
        font-size: 13.5px;
        outline: none;
        transition: all 0.2s ease;
        box-sizing: border-box;
    }

    .search-input-wrap input:focus {
        border-color: var(--primary);
        background: var(--bg-card);
        box-shadow: 0 0 0 3px rgba(0, 40, 85, 0.08);
    }

    .search-input-wrap i {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--text-muted);
        font-size: 14px;
    }

    .filter-select {
        padding: 10px 14px;
        border-radius: var(--radius-sm);
        border: 1px solid rgba(0, 40, 85, 0.14);
        background: var(--bg-body);
        color: var(--text-primary);
        font-size: 13px;
        font-weight: 600;
        outline: none;
        cursor: pointer;
    }

    /* Table Container */
    .table-container-card {
        background: var(--bg-card);
        border-radius: var(--radius);
        box-shadow: var(--shadow-sm);
        border: 1px solid rgba(0, 40, 85, 0.06);
        overflow: hidden;
    }

    .table-header-bar {
        padding: 18px 24px;
        border-bottom: 1px solid rgba(0, 40, 85, 0.06);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
    }

    .table-header-bar h3 {
        font-size: 16px;
        font-weight: 800;
        color: var(--primary);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .badge-desil {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 700;
        background: rgba(0, 40, 85, 0.08);
        color: var(--primary);
    }

    .badge-desil.backlog-1 {
        background: rgba(39, 174, 96, 0.12);
        color: var(--success);
    }

    .badge-gender {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 28px;
        height: 28px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 800;
    }
    .badge-gender.l { background: rgba(0, 40, 85, 0.10); color: var(--primary); border: 1px solid rgba(0, 40, 85, 0.18); }
    .badge-gender.p { background: rgba(212, 63, 120, 0.12); color: #d43f78; border: 1px solid rgba(212, 63, 120, 0.22); }

    .action-btn-group {
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .btn-act {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        border: none;
        cursor: pointer;
        text-decoration: none;
        transition: all 0.2s ease;
    }

    .btn-act.view { background: rgba(0, 40, 85, 0.08); color: var(--primary); }
    .btn-act.view:hover { background: var(--primary); color: #fff; }
    .btn-act.print { background: rgba(255, 184, 0, 0.18); color: #b88600; }
    .btn-act.print:hover { background: #ffb800; color: #002855; }
    .btn-act.map  { background: rgba(39, 174, 96, 0.12); color: var(--success); }
    .btn-act.map:hover { background: var(--success); color: #fff; }

    /* Custom Pagination Styling */
    .pagination-custom-bar {
        padding: 16px 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
        border-top: 1px solid rgba(0, 40, 85, 0.08);
        background: var(--bg-card);
    }

    .pagination-info-text {
        font-size: 13px;
        color: var(--text-muted);
        font-weight: 500;
    }

    .pagination-info-text strong {
        color: var(--primary-dark);
        font-weight: 700;
    }

    .pagination-nav {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        margin: 0;
        padding: 0;
    }

    .pg-link {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 36px;
        height: 36px;
        padding: 0 12px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 700;
        color: var(--text-primary);
        background: var(--bg-body);
        border: 1px solid rgba(0, 40, 85, 0.14);
        text-decoration: none;
        transition: all 0.2s ease;
    }

    .pg-link:hover:not(.disabled):not(.active) {
        background: rgba(0, 40, 85, 0.08);
        border-color: var(--primary);
        color: var(--primary);
    }

    .pg-link.active {
        background: var(--primary) !important;
        color: #ffffff !important;
        border-color: var(--primary) !important;
        box-shadow: 0 3px 8px rgba(0, 40, 85, 0.25);
    }

    .pg-link.disabled {
        opacity: 0.4;
        cursor: not-allowed;
        pointer-events: none;
        background: var(--bg-body);
    }

    .pg-dots {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 24px;
        height: 36px;
        font-size: 13px;
        color: var(--text-muted);
        letter-spacing: 2px;
    }

    .jump-page-form {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        margin-left: 6px;
    }

    .jump-page-input {
        width: 60px;
        height: 36px;
        padding: 0 6px;
        text-align: center;
        border-radius: 8px;
        border: 1px solid rgba(0, 40, 85, 0.16);
        background: var(--bg-body);
        color: var(--text-primary);
        font-size: 13px;
        font-weight: 700;
        outline: none;
        transition: all 0.2s ease;
    }

    .jump-page-input:focus {
        border-color: var(--primary);
        background: var(--bg-card);
        box-shadow: 0 0 0 3px rgba(0, 40, 85, 0.08);
    }

    .jump-page-btn {
        height: 36px;
        padding: 0 14px;
        border-radius: 8px;
        border: none;
        background: var(--primary);
        color: #ffffff;
        font-size: 12.5px;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .jump-page-btn:hover {
        background: var(--primary-light);
    }

    @media (max-width: 1024px) {
        .stats-verval { grid-template-columns: repeat(2, 1fr); }
    }

    @media (max-width: 768px) {
        .filter-section { flex-direction: column; align-items: stretch; }
        .filter-left { flex-direction: column; }
        .search-input-wrap { width: 100%; }
        .filter-select { width: 100%; }
        .pagination-custom-bar { flex-direction: column; align-items: stretch; gap: 14px; }
        .pagination-nav { flex-wrap: wrap; justify-content: center; }
        .jump-page-form { justify-content: center; width: 100%; }
    }
</style>
@endpush

@section('content')
    @include('layouts.navbar')

    <main class="dashboard-content">
        <div class="breadcrumb" style="font-size:13px;color:var(--text-muted);margin-bottom:20px;display:flex;align-items:center;gap:8px;">
            <a href="{{ url('/dashboard') }}" style="color:var(--primary);text-decoration:none;font-weight:600;"><i class="fas fa-home"></i> Dashboard</a>
            <i class="fas fa-chevron-right" style="font-size:10px;"></i>
            <span>Data Verval BSPS</span>
        </div>

        <!-- 4 Stat Counters -->
        <div class="stats-verval">
            <div class="stat-item">
                <div class="icon blue"><i class="fas fa-users"></i></div>
                <div class="info">
                    <div class="value">{{ number_format($stats['total'], 0, ',', '.') }}</div>
                    <div class="label">Total Calon Penerima</div>
                </div>
            </div>
            <div class="stat-item">
                <div class="icon green"><i class="fas fa-map-location-dot"></i></div>
                <div class="info">
                    <div class="value">{{ $stats['kecamatan'] }}</div>
                    <div class="label">Kecamatan Terdata</div>
                </div>
            </div>
            <div class="stat-item">
                <div class="icon orange"><i class="fas fa-city"></i></div>
                <div class="info">
                    <div class="value">{{ $stats['desa'] }}</div>
                    <div class="label">Desa / Kelurahan</div>
                </div>
            </div>
            <div class="stat-item">
                <div class="icon purple"><i class="fas fa-filter"></i></div>
                <div class="info">
                    <div class="value">{{ number_format($stats['filter'], 0, ',', '.') }}</div>
                    <div class="label">Hasil Saringan Data</div>
                </div>
            </div>
        </div>

        <!-- Filter & Search Bar -->
        <form action="{{ url('/verval-data') }}" method="GET" class="filter-section">
            <div class="filter-left">
                <div class="search-input-wrap">
                    <i class="fas fa-search"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama pemohon, NIK, No. KK, atau alamat..." />
                </div>
                <select name="kecamatan" class="filter-select" onchange="this.form.submit()">
                    <option value="all" {{ request('kecamatan') == 'all' ? 'selected' : '' }}>-- Semua Kecamatan --</option>
                    @foreach($listKecamatan as $kec)
                        <option value="{{ $kec }}" {{ request('kecamatan') == $kec ? 'selected' : '' }}>Kec. {{ $kec }}</option>
                    @endforeach
                </select>
                <select name="desil" class="filter-select" onchange="this.form.submit()">
                    <option value="all" {{ request('desil') == 'all' ? 'selected' : '' }}>-- Semua Pengelompokan --</option>
                    <option value="Backlog 1" {{ request('desil') == 'Backlog 1' ? 'selected' : '' }}>Backlog 1 Desil 1-4</option>
                    <option value="Backlog 2" {{ request('desil') == 'Backlog 2' ? 'selected' : '' }}>Backlog 2 Desil 1-4</option>
                </select>
            </div>
            <div style="display:flex;gap:10px;flex-wrap:wrap;">
                <a href="{{ route('verval-data.surat-pernyataan-kolektif', request()->all()) }}" target="_blank" class="btn" style="padding:10px 16px;font-size:13px;font-weight:700;background:#ffb800;color:#002855;text-decoration:none;border-radius:var(--radius-sm);display:inline-flex;align-items:center;gap:6px;" title="Cetak Surat Pernyataan seluruh data hasil filter saat ini">
                    <i class="fas fa-file-signature"></i> Cetak Kolektif Surat Pernyataan
                </a>
                <a href="{{ url('/verval-data') }}" class="btn btn-outline" style="padding:10px 16px;font-size:13px;text-decoration:none;border-radius:var(--radius-sm);">
                    <i class="fas fa-redo"></i> Reset
                </a>
                <a href="{{ url('/survey') }}" class="btn btn-primary" style="padding:10px 20px;font-size:13px;font-weight:700;background:var(--primary);color:#fff;text-decoration:none;border-radius:var(--radius-sm);display:inline-flex;align-items:center;gap:8px;">
                    <i class="fas fa-plus"></i> Input Survei Baru
                </a>
            </div>
        </form>

        <!-- Main Data Table -->
        <div class="table-container-card">
            <div class="table-header-bar">
                <h3><i class="fas fa-clipboard-list"></i> Database Calon Penerima Bantuan BSPS (Verval Data)</h3>
                <span style="font-size:12.5px;color:var(--text-muted);font-weight:600;">
                    Menampilkan {{ $vervals->firstItem() ?? 0 }} - {{ $vervals->lastItem() ?? 0 }} dari {{ number_format($vervals->total(), 0, ',', '.') }} data
                </span>
            </div>

            <div style="overflow-x:auto;">
                <table class="table" style="width:100%;border-collapse:collapse;">
                    <thead>
                        <tr style="background:var(--bg-body);border-bottom:1px solid rgba(0,40,85,0.08);text-align:left;font-size:12.5px;color:var(--text-muted);">
                            <th style="padding:14px 18px;">No</th>
                            <th style="padding:14px 18px;">Nama Calon Penerima</th>
                            <th style="padding:14px 18px;text-align:center;">L/P</th>
                            <th style="padding:14px 18px;">NIK &amp; No. KK</th>
                            <th style="padding:14px 18px;">Alamat &amp; Dusun</th>
                            <th style="padding:14px 18px;">Desa / Kelurahan</th>
                            <th style="padding:14px 18px;">Kecamatan</th>
                            <th style="padding:14px 18px;">Kelompok Desil</th>
                            <th style="padding:14px 18px;text-align:center;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($vervals as $index => $item)
                            <tr style="border-bottom:1px solid rgba(0,40,85,0.06);font-size:13px;transition:all 0.15s ease;">
                                <td style="padding:14px 18px;font-weight:700;color:var(--text-muted);">
                                    {{ $vervals->firstItem() + $index }}
                                </td>
                                <td style="padding:14px 18px;">
                                    <div style="font-weight:800;color:var(--primary-dark);">
                                        {{ $item->nama }}
                                    </div>
                                </td>
                                <td style="padding:14px 18px;text-align:center;">
                                    <span class="badge-gender {{ strtolower($item->jenis_kelamin) }}">
                                        {{ $item->jenis_kelamin == 'L' ? 'L' : ($item->jenis_kelamin == 'P' ? 'P' : ($item->jenis_kelamin ?: '-')) }}
                                    </span>
                                </td>
                                <td style="padding:14px 18px;">
                                    <div style="font-family:monospace;font-weight:700;color:var(--text-primary);letter-spacing:0.3px;">
                                        <span style="font-size:11px;color:var(--text-muted);font-weight:600;margin-right:4px;">NIK:</span>{{ $item->no_ktp ?: '-' }}
                                    </div>
                                    <div style="font-family:monospace;font-weight:600;color:var(--text-muted);font-size:12px;margin-top:3px;letter-spacing:0.3px;">
                                        <span style="font-size:11px;color:var(--text-muted);font-weight:600;margin-right:4px;">KK:</span>{{ $item->no_kk ?: '-' }}
                                    </div>
                                </td>
                                <td style="padding:14px 18px;color:var(--text-secondary);">
                                    {{ $item->alamat ?: '-' }}
                                </td>
                                <td style="padding:14px 18px;font-weight:600;color:var(--primary-dark);">
                                    {{ $item->desa_kelurahan ?: '-' }}
                                </td>
                                <td style="padding:14px 18px;">
                                    <span style="font-weight:700;color:var(--primary);">Kec. {{ $item->kecamatan }}</span>
                                </td>
                                <td style="padding:14px 18px;">
                                    <span class="badge-desil {{ str_contains($item->pengelompokan_desil, 'Backlog 1') ? 'backlog-1' : '' }}">
                                        <i class="fas fa-layer-group" style="font-size:10px;"></i>
                                        {{ $item->pengelompokan_desil ?: 'Desil 1-4' }}
                                    </span>
                                </td>
                                <td style="padding:14px 18px;text-align:center;">
                                    <div class="action-btn-group" style="justify-content:center;">
                                        <a href="{{ route('verval-data.surat-pernyataan', $item->id) }}" target="_blank" class="btn-act print" title="Cetak Surat Pernyataan Pemohon Ini">
                                            <i class="fas fa-file-signature"></i>
                                        </a>
                                        <a href="{{ url('/survey?nik=' . $item->no_ktp . '&nama=' . urlencode($item->nama) . '&desa=' . urlencode($item->desa_kelurahan) . '&kecamatan=' . urlencode($item->kecamatan) . '&alamat=' . urlencode($item->alamat)) }}" class="btn-act view" title="Mulai Survei RTLH untuk Pemohon Ini">
                                            <i class="fas fa-clipboard-check"></i>
                                        </a>
                                        <a href="{{ url('/geomaps') }}" class="btn-act map" title="Lihat Peta Lokasi">
                                            <i class="fas fa-map-marker-alt"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" style="text-align:center;padding:32px;color:var(--text-muted);">
                                    <i class="fas fa-clipboard-question" style="font-size:28px;display:block;margin-bottom:8px;opacity:0.4;"></i>
                                    Tidak ditemukan data calon penerima yang sesuai dengan filter pencarian.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination Bar Custom -->
            <div class="pagination-custom-bar">
                <div class="pagination-info-text">
                    Menampilkan <strong>{{ $vervals->firstItem() ?? 0 }}</strong> - <strong>{{ $vervals->lastItem() ?? 0 }}</strong> dari <strong>{{ number_format($vervals->total(), 0, ',', '.') }}</strong> penerima (Halaman <strong>{{ $vervals->currentPage() }}</strong> dari <strong>{{ $vervals->lastPage() }}</strong>)
                </div>

                @php
                    $current = $vervals->currentPage();
                    $last = $vervals->lastPage();
                    $delta = 2;
                    $left = $current - $delta;
                    $right = $current + $delta + 1;
                    $range = [];
                    $rangeWithDots = [];
                    $l = null;

                    for ($i = 1; $i <= $last; $i++) {
                        if ($i == 1 || $i == $last || ($i >= $left && $i < $right)) {
                            $range[] = $i;
                        }
                    }

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

                <div style="display:flex;align-items:center;gap:12px;flex-wrap:wrap;">
                    <!-- Navigation Buttons -->
                    <div class="pagination-nav">
                        <!-- Prev Button -->
                        <a href="{{ $vervals->previousPageUrl() ?: '#' }}" class="pg-link {{ $vervals->onFirstPage() ? 'disabled' : '' }}" title="Halaman Sebelumnya">
                            <i class="fas fa-chevron-left" style="margin-right:4px;"></i> Prev
                        </a>

                        <!-- Page Numbers with Sliding Window -->
                        @foreach($rangeWithDots as $p)
                            @if($p === '...')
                                <span class="pg-dots">&bull;&bull;&bull;</span>
                            @else
                                <a href="{{ $vervals->url($p) }}" class="pg-link {{ $p == $current ? 'active' : '' }}">
                                    {{ $p }}
                                </a>
                            @endif
                        @endforeach

                        <!-- Next Button -->
                        <a href="{{ $vervals->nextPageUrl() ?: '#' }}" class="pg-link {{ !$vervals->hasMorePages() ? 'disabled' : '' }}" title="Halaman Berikutnya">
                            Next <i class="fas fa-chevron-right" style="margin-left:4px;"></i>
                        </a>
                    </div>

                    <!-- Jump To Page Form -->
                    <form action="{{ url('/verval-data') }}" method="GET" class="jump-page-form">
                        @if(request('search')) <input type="hidden" name="search" value="{{ request('search') }}"> @endif
                        @if(request('kecamatan') && request('kecamatan') != 'all') <input type="hidden" name="kecamatan" value="{{ request('kecamatan') }}"> @endif
                        @if(request('desil') && request('desil') != 'all') <input type="hidden" name="desil" value="{{ request('desil') }}"> @endif
                        <span style="font-size:12px;color:var(--text-muted);font-weight:600;">Lompat:</span>
                        <input type="number" name="page" min="1" max="{{ $last }}" value="{{ $current }}" class="jump-page-input" title="Masukkan nomor halaman" />
                        <button type="submit" class="jump-page-btn" title="Buka Halaman">Go</button>
                    </form>
                </div>
            </div>
        </div>
    </main>
@endsection
