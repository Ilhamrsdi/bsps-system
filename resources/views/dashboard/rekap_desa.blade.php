@extends('layouts.partial.app')

@section('title', 'Rekapitulasi Target & Capaian Seluruh Desa - BSPS Verval')
@section('title_header', 'Rekapitulasi Target & Capaian Desa')
@section('subtitle_header', 'Monitoring Progres Verval & Persentase Kelayakan 248 Desa/Kelurahan se-Kabupaten Jember')

@push('styles')
<style>
    /* Header Card Rekap Desa */
    .rekap-desa-hero {
        background: linear-gradient(135deg, #002855 0%, #003b7a 100%);
        border-radius: var(--radius);
        padding: 22px 26px;
        color: #ffffff;
        margin-bottom: 22px;
        box-shadow: 0 8px 24px rgba(0, 40, 85, 0.16);
    }

    .rekap-desa-hero .title-area h2 {
        font-size: 19px;
        font-weight: 900;
        margin: 0 0 4px 0;
        display: flex;
        align-items: center;
        gap: 10px;
        color: #ffffff;
    }

    .rekap-desa-hero .title-area p {
        font-size: 13px;
        color: rgba(255, 255, 255, 0.8);
        margin: 0 0 16px 0;
    }

    .rekap-stats-chips {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
    }

    .rekap-chip {
        background: rgba(255, 255, 255, 0.12);
        border: 1px solid rgba(255, 255, 255, 0.2);
        padding: 8px 16px;
        border-radius: 10px;
        font-size: 12.5px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        color: #ffffff;
    }

    .rekap-chip strong {
        font-weight: 800;
        color: #ffb800;
    }

    /* Filter Bar */
    .filter-card-bar {
        background: var(--bg-card);
        border-radius: var(--radius);
        padding: 16px 20px;
        box-shadow: var(--shadow-sm);
        border: 1px solid rgba(0, 40, 85, 0.06);
        margin-bottom: 22px;
    }

    .filter-form-grid {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 12px;
    }

    .filter-item-box {
        flex: 1;
        min-width: 180px;
    }

    /* Progress bar in table */
    .prog-track {
        width: 100px;
        height: 6px;
        background: #e2e8f0;
        border-radius: 3px;
        overflow: hidden;
        display: inline-block;
        vertical-align: middle;
        margin-right: 6px;
    }

    .prog-fill {
        height: 100%;
        background: linear-gradient(90deg, #ffb800, #27ae60);
        border-radius: 3px;
    }
</style>
@endpush

@section('content')
    @include('layouts.navbar')

    <main class="dashboard-content">
        <!-- Breadcrumb -->
        <div class="breadcrumb" style="margin-bottom: 16px; font-size: 13px; color: var(--text-muted); display: flex; align-items: center; gap: 8px;">
            <a href="{{ route('dashboard') }}" style="color: var(--primary); text-decoration: none; font-weight: 600;"><i class="fas fa-th-large"></i> Dashboard Global</a>
            <i class="fas fa-chevron-right" style="font-size: 10px;"></i>
            <span>Rekapitulasi Capaian Seluruh Desa</span>
        </div>

        <!-- Hero Header Card -->
        <div class="rekap-desa-hero">
            <div class="title-area">
                <h2>
                    <i class="fas fa-city" style="color: #ffb800;"></i>
                    <span>Rekapitulasi Target &amp; Capaian 248 Desa / Kelurahan</span>
                </h2>
                <p>
                    Monitoring capaian survei lapangan &amp; persentase kelayakan calon penerima BSPS di seluruh desa se-Kabupaten Jember.
                </p>
            </div>

            <div class="rekap-stats-chips">
                <div class="rekap-chip">
                    <i class="fas fa-building"></i>
                    <span>Total: <strong>{{ number_format($totalDesaGlobal) }} Desa/Kelurahan</strong></span>
                </div>
                <div class="rekap-chip">
                    <i class="fas fa-users"></i>
                    <span>Target Total: <strong>{{ number_format($totalTargetGlobal) }} KK</strong></span>
                </div>
                <div class="rekap-chip">
                    <i class="fas fa-clipboard-check"></i>
                    <span>Sudah Survei: <strong>{{ number_format($totalSudahGlobal) }} KK ({{ $totalTargetGlobal > 0 ? round(($totalSudahGlobal / $totalTargetGlobal) * 100, 1) : 0 }}%)</strong></span>
                </div>
                <div class="rekap-chip">
                    <i class="fas fa-check-circle" style="color: #4ade80;"></i>
                    <span>Layak: <strong>{{ number_format($totalLayakGlobal) }} KK</strong></span>
                </div>
                <div class="rekap-chip">
                    <i class="fas fa-circle-xmark" style="color: #f87171;"></i>
                    <span>Tidak Layak: <strong>{{ number_format($totalTidakLayakGlobal) }} KK</strong></span>
                </div>
            </div>
        </div>

        <!-- Filter Bar -->
        <div class="filter-card-bar">
            <form action="{{ route('dashboard.rekap-desa') }}" method="GET" class="filter-form-grid" id="filterFormRekapDesa">
                <!-- Filter Kecamatan -->
                @if(!auth()->check() || !auth()->user()->isAdminKecamatan())
                <div class="filter-item-box" style="max-width: 240px;">
                    <select name="kecamatan" class="form-control" onchange="this.form.submit()" style="width: 100%; padding: 8px 12px; border-radius: 8px; border: 1px solid #cbd5e1; font-size: 13px; font-weight: 600; background: #fff;">
                        <option value="all">-- Semua Kecamatan (31 Kec) --</option>
                        @foreach($listKecamatan as $kec)
                            <option value="{{ $kec }}" {{ request('kecamatan') === $kec ? 'selected' : '' }}>
                                Kec. {{ ucwords(strtolower($kec)) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @endif

                <!-- Per Page -->
                <div class="filter-item-box" style="max-width: 140px;">
                    <select name="per_page" class="form-control" onchange="this.form.submit()" style="width: 100%; padding: 8px 12px; border-radius: 8px; border: 1px solid #cbd5e1; font-size: 13px; font-weight: 600; background: #fff;">
                        <option value="20" {{ request('per_page', '20') == '20' ? 'selected' : '' }}>20 Desa</option>
                        <option value="50" {{ request('per_page') == '50' ? 'selected' : '' }}>50 Desa</option>
                        <option value="100" {{ request('per_page') == '100' ? 'selected' : '' }}>100 Desa</option>
                        <option value="all" {{ request('per_page') == 'all' ? 'selected' : '' }}>Semua Desa (248)</option>
                    </select>
                </div>

                <!-- Search Input -->
                <div class="filter-item-box" style="flex: 2; min-width: 220px;">
                    <div style="position: relative;">
                        <i class="fas fa-search" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 13px;"></i>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama Desa atau Kecamatan..." style="width: 100%; padding: 8px 14px 8px 34px; border-radius: 8px; border: 1px solid #cbd5e1; font-size: 13px; outline: none;" />
                    </div>
                </div>

                <!-- Action Buttons -->
                <div style="display: flex; gap: 8px;">
                    <button type="submit" class="btn btn-primary" style="padding: 8px 16px; border-radius: 8px; font-size: 13px; font-weight: 700; border: none; cursor: pointer; background: #002855; color: #ffffff;">
                        <i class="fas fa-filter"></i> Filter
                    </button>
                    <a href="{{ route('dashboard.rekap-desa') }}" class="btn btn-outline" style="padding: 8px 14px; border-radius: 8px; font-size: 13px; font-weight: 600; text-decoration: none; border: 1px solid #cbd5e1; color: #64748b; background: #ffffff;">
                        <i class="fas fa-redo"></i> Reset
                    </a>
                </div>
            </form>
        </div>

        <!-- Tabel Rekapitulasi Seluruh Desa -->
        <div class="table-card">
            <div class="table-header" style="padding: 16px 20px; border-bottom: 1px solid rgba(0, 40, 85, 0.08); display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap;">
                <h3 style="margin: 0; font-size: 15px; font-weight: 800; color: #002855; display: flex; align-items: center; gap: 8px;">
                    <i class="fas fa-table-list" style="color: #ffb800;"></i>
                    <span>Tabel Target &amp; Capaian Seluruh Desa</span>
                    <span style="font-size: 13px; color: #64748b; font-weight: 600;">({{ number_format($desaList->total()) }} desa ditemukan)</span>
                </h3>

                <div style="display: flex; gap: 8px;">
                    <a href="{{ route('laporan.export', ['type' => 'desa', 'kecamatan' => request('kecamatan', 'all')]) }}" class="btn btn-outline" style="padding: 6px 14px; font-size: 12px; font-weight: 700; border-radius: 6px; text-decoration: none; border: 1px solid #107c41; color: #107c41; background: #ffffff; display: inline-flex; align-items: center; gap: 6px;">
                        <i class="fas fa-file-excel"></i> Export Excel
                    </a>
                </div>
            </div>

            <div class="table-wrapper">
                <table class="pupr-table">
                    <thead>
                        <tr>
                            <th style="width: 45px; text-align: center;">No</th>
                            <th style="min-width: 170px;">Kecamatan</th>
                            <th style="min-width: 180px;">Desa / Kelurahan</th>
                            <th style="width: 110px; text-align: center;">Target (KK)</th>
                            <th style="width: 110px; text-align: center;">Sudah Survei</th>
                            <th style="width: 110px; text-align: center;">Belum Survei</th>
                            <th style="min-width: 160px; text-align: center;">Progres Capaian</th>
                            <th style="min-width: 130px; text-align: center;">Layak Diusulkan</th>
                            <th style="min-width: 130px; text-align: center;">Tidak Layak</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($desaList as $index => $row)
                        <tr>
                            <td style="text-align: center;">{{ $desaList->firstItem() + $index }}</td>
                            <td>
                                <strong style="color: #002855; font-size: 13px;">
                                    Kec. {{ ucwords(strtolower($row->kecamatan)) }}
                                </strong>
                            </td>
                            <td>
                                <div style="font-weight: 700; color: #0f172a; font-size: 13.5px;">
                                    <i class="fas fa-house-chimney" style="color: #002855; font-size: 11px; margin-right: 4px;"></i>
                                    Desa {{ ucwords(strtolower($row->desa_kelurahan)) }}
                                </div>
                            </td>
                            <td style="text-align: center; font-weight: 800; color: #002855; font-size: 13.5px;">
                                {{ number_format($row->total_target, 0, ',', '.') }}
                            </td>
                            <td style="text-align: center;">
                                <span style="font-size: 12px; font-weight: 800; color: #15803d; background: #dcfce7; padding: 3px 10px; border-radius: 12px; border: 1px solid #86efac;">
                                    {{ number_format($row->total_sudah, 0, ',', '.') }}
                                </span>
                            </td>
                            <td style="text-align: center;">
                                <span style="font-size: 12px; font-weight: 800; color: #b45309; background: #fef3c7; padding: 3px 10px; border-radius: 12px; border: 1px solid #fde68a;">
                                    {{ number_format($row->total_belum, 0, ',', '.') }}
                                </span>
                            </td>
                            <td style="text-align: center;">
                                <div style="display: flex; align-items: center; justify-content: center; gap: 8px;">
                                    <div class="prog-track">
                                        <div class="prog-fill" style="width: {{ $row->progres_percent }}%;"></div>
                                    </div>
                                    <strong style="font-size: 12px; color: #15803d; min-width: 42px; text-align: right;">{{ $row->progres_percent }}%</strong>
                                </div>
                            </td>
                            <td style="text-align: center;">
                                <span style="font-weight: 800; color: #15803d; font-size: 13px;">{{ number_format($row->total_layak, 0, ',', '.') }} KK</span>
                                <div style="font-size: 10.5px; color: #64748b;">{{ $row->persen_layak }}% dari verval</div>
                            </td>
                            <td style="text-align: center;">
                                <span style="font-weight: 800; color: #b91c1c; font-size: 13px;">{{ number_format($row->total_tidak_layak, 0, ',', '.') }} KK</span>
                                <div style="font-size: 10.5px; color: #64748b;">{{ $row->persen_tidak }}% dari verval</div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" style="text-align: center; padding: 40px; color: #94a3b8;">
                                <i class="fas fa-inbox" style="font-size: 32px; display: block; margin-bottom: 10px; opacity: 0.4;"></i>
                                Tidak ada data desa yang cocok dengan kriteria pencarian/filter.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination Bar Custom -->
            @if($desaList->hasPages() || $desaList->total() > 0)
            <div class="pagination-custom-bar" style="padding: 14px 20px; border-top: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px; background: #f8fafc;">
                <div class="pagination-info-text" style="font-size: 12.5px; color: #64748b;">
                    Menampilkan <strong>{{ $desaList->firstItem() ?? 0 }}</strong> -
                    <strong>{{ $desaList->lastItem() ?? 0 }}</strong> dari
                    <strong>{{ number_format($desaList->total(), 0, ',', '.') }}</strong> desa/kelurahan
                    @if($desaList->lastPage() > 1)
                        (Halaman <strong>{{ $desaList->currentPage() }}</strong> dari <strong>{{ $desaList->lastPage() }}</strong>)
                    @endif
                </div>

                @if($desaList->lastPage() > 1)
                    @php
                        $current = $desaList->currentPage();
                        $last = $desaList->lastPage();
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
                        @if($desaList->onFirstPage())
                            <li><span class="page-btn disabled" style="padding: 6px 10px; border-radius: 6px; border: 1px solid #cbd5e1; color: #cbd5e1; display: inline-block;"><i class="fas fa-chevron-left"></i></span></li>
                        @else
                            <li><a href="{{ $desaList->previousPageUrl() }}" class="page-btn" style="padding: 6px 10px; border-radius: 6px; border: 1px solid #cbd5e1; color: #002855; text-decoration: none; display: inline-block;"><i class="fas fa-chevron-left"></i></a></li>
                        @endif

                        @foreach($rangeWithDots as $page)
                            @if($page === '...')
                                <li><span class="page-dots" style="padding: 6px 8px; color: #94a3b8; display: inline-block;">...</span></li>
                            @elseif($page == $current)
                                <li><span class="page-btn active" style="padding: 6px 12px; border-radius: 6px; background: #002855; color: #ffffff; font-weight: 700; display: inline-block;">{{ $page }}</span></li>
                            @else
                                <li><a href="{{ $desaList->url($page) }}" class="page-btn" style="padding: 6px 12px; border-radius: 6px; border: 1px solid #cbd5e1; color: #002855; text-decoration: none; font-weight: 600; display: inline-block;">{{ $page }}</a></li>
                            @endif
                        @endforeach

                        @if($desaList->hasMorePages())
                            <li><a href="{{ $desaList->nextPageUrl() }}" class="page-btn" style="padding: 6px 10px; border-radius: 6px; border: 1px solid #cbd5e1; color: #002855; text-decoration: none; display: inline-block;"><i class="fas fa-chevron-right"></i></a></li>
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
