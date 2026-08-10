@extends('layouts.partial.app')

@section('title', 'BSPS Verval - Tugas Sudah Di-survei')
@section('title_header', 'Tugas Sudah Di-survei')
@section('subtitle_header', 'Daftar Calon Penerima BSPS Desa {{ Auth::user()->desa ?? "-" }} yang Telah Selesai Verifikasi Fisik Lapangan')

@push('styles')
<style>
    .filter-section {
        background: var(--bg-card);
        border-radius: var(--radius);
        padding: 16px 20px;
        margin-bottom: 18px;
        box-shadow: var(--shadow-sm);
        border: 1px solid rgba(0,40,85,0.06);
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
    }
    .search-input-wrap { position: relative; flex: 1; min-width: 260px; }
    .search-input-wrap input {
        width: 100%; padding: 10px 14px 10px 38px;
        border-radius: var(--radius-sm);
        border: 1px solid rgba(0,40,85,0.14);
        background: var(--bg-body); color: var(--text-primary);
        font-size: 13.5px; outline: none; box-sizing: border-box;
    }
    .search-input-wrap input:focus { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(0,40,85,0.08); background: var(--bg-card); }
    .search-input-wrap i { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 14px; }

    .table-container-card {
        background: var(--bg-card);
        border-radius: var(--radius);
        box-shadow: var(--shadow-sm);
        border: 1px solid rgba(0, 40, 85, 0.06);
        overflow: hidden;
    }
    .table-header-bar {
        padding: 18px 24px;
        border-bottom: 1px solid rgba(0,40,85,0.06);
        display: flex; align-items: center; justify-content: space-between; gap: 12px;
    }
    .table-header-bar h3 { font-size: 15px; font-weight: 800; color: var(--success); display: flex; align-items: center; gap: 8px; margin: 0; }

    .badge-status-sudah {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 11.5px;
        font-weight: 700;
        background: rgba(39, 174, 96, 0.12);
        color: var(--success);
    }

    .badge-gender {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 26px;
        height: 26px;
        border-radius: 6px;
        font-size: 11px;
        font-weight: 800;
    }
    .badge-gender.l { background: rgba(0, 40, 85, 0.10); color: var(--primary); }
    .badge-gender.p { background: rgba(212, 63, 120, 0.12); color: #d43f78; }

    .thumb-survey-mini {
        width: 44px;
        height: 44px;
        border-radius: 6px;
        object-fit: cover;
        border: 1px solid rgba(0,40,85,0.12);
    }

    .btn-act {
        padding: 6px 12px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 700;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s ease;
        border: none;
        cursor: pointer;
    }
    .btn-act.edit { background: rgba(0, 40, 85, 0.08); color: var(--primary); }
    .btn-act.edit:hover { background: var(--primary); color: #fff; }
    .btn-act.print { background: rgba(255, 184, 0, 0.15); color: #002855; }
    .btn-act.print:hover { background: #ffb800; }

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
    .pagination-info-text { font-size: 13px; color: var(--text-muted); font-weight: 500; }
    .pagination-info-text strong { color: var(--primary-dark); font-weight: 700; }
    .pagination-nav { display: inline-flex; align-items: center; gap: 6px; margin: 0; padding: 0; }
    .pg-link {
        display: inline-flex; align-items: center; justify-content: center;
        min-width: 36px; height: 36px; padding: 0 12px;
        border-radius: 8px; font-size: 13px; font-weight: 700;
        color: var(--text-primary); background: var(--bg-body);
        border: 1px solid rgba(0, 40, 85, 0.14); text-decoration: none;
        transition: all 0.2s ease;
    }
    .pg-link:hover { background: var(--primary); color: #fff; border-color: var(--primary); transform: translateY(-1px); }
    .pg-link.active { background: var(--primary); color: #fff; border-color: var(--primary); box-shadow: 0 2px 6px rgba(0, 40, 85, 0.25); }
    .pg-link.disabled { opacity: 0.4; cursor: not-allowed; pointer-events: none; }
    .pg-dots { display: inline-flex; align-items: center; justify-content: center; min-width: 30px; height: 36px; font-size: 14px; font-weight: 700; color: var(--text-muted); letter-spacing: 2px; }

    /* Jump to Page Form */
    .jump-page-form { display: inline-flex; align-items: center; gap: 6px; margin-left: 12px; }
    .jump-page-input {
        width: 54px; height: 36px; text-align: center;
        border-radius: 8px; border: 1px solid rgba(0, 40, 85, 0.16);
        background: var(--bg-body); color: var(--text-primary); font-size: 13px; font-weight: 700; outline: none;
    }
    .table-petugas-wrapper {
        width: 100%;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    .table-container-card table {
        width: 100%;
        min-width: 950px;
        border-collapse: collapse;
        white-space: nowrap;
    }

    .table-container-card table th,
    .table-container-card table td {
        white-space: nowrap;
    }

    @media (max-width: 768px) {
        .table-header-bar {
            flex-direction: column;
            align-items: flex-start;
            gap: 8px;
        }
        .pagination-custom-bar {
            flex-direction: column;
            align-items: center;
            gap: 12px;
            text-align: center;
        }
    }

    @media (max-width: 576px) {
        .filter-section { flex-direction: column; align-items: stretch; padding: 14px; gap: 10px; }
        .search-input-wrap { min-width: 100%; }
        .pagination-nav { flex-wrap: wrap; justify-content: center; }
    }
</style>
@endpush

@section('content')
    <!-- Navbar Component -->
    @include('layouts.navbar')

    <main class="dashboard-content">
        <!-- Breadcrumb -->
        <div class="breadcrumb" style="font-size:13px;color:var(--text-muted);margin-bottom:20px;display:flex;align-items:center;gap:8px;">
            <a href="{{ route('petugas.dashboard') }}" style="color:var(--primary);text-decoration:none;font-weight:600;"><i class="fas fa-home"></i> Dashboard Petugas</a>
            <i class="fas fa-chevron-right" style="font-size:10px;"></i>
            <span>Tugas Sudah Survei (Desa {{ $user->desa ?: '-' }})</span>
        </div>

        {{-- Filter & Search --}}
        <form action="{{ route('petugas.sudah-survei') }}" method="GET" class="filter-section">
            <div class="search-input-wrap">
                <i class="fas fa-search"></i>
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama calon penerima yang sudah disurvei, NIK, atau alamat..." />
            </div>
            <a href="{{ route('verval-data.surat-pernyataan-kolektif', array_merge(['desa' => $user->desa, 'status' => 'sudah'], request()->all())) }}" target="_blank" class="btn" style="padding:10px 16px;font-size:13px;font-weight:700;background:#ffb800;color:#002855;text-decoration:none;border-radius:var(--radius-sm);display:inline-flex;align-items:center;gap:6px;" title="Cetak Surat Pernyataan Kolektif untuk warga yang sudah disurvei di Desa {{ $user->desa ?: '-' }}">
                <i class="fas fa-file-signature"></i> Cetak Kolektif (Sudah Survei)
            </a>
        </form>

        {{-- Tabel Data Sudah Di-survei --}}
        <div class="table-container-card">
            <div class="table-header-bar">
                <h3><i class="fas fa-clipboard-check"></i> Daftar Calon Penerima Selesai Di-survei — Desa {{ $user->desa ?: '-' }}</h3>
                <div style="display:flex;align-items:center;gap:12px;flex-wrap:wrap;">
                    <a href="{{ route('verval-data.surat-pernyataan-kolektif', array_merge(['desa' => $user->desa, 'status' => 'sudah'], request()->all())) }}" target="_blank" class="btn" style="padding:8px 14px;font-size:12.5px;font-weight:800;background:#ffb800;color:#002855;text-decoration:none;border-radius:var(--radius-sm);display:inline-flex;align-items:center;gap:6px;">
                        <i class="fas fa-print"></i> Cetak Kolektif
                    </a>
                    <span style="font-size:12.5px;color:var(--text-muted);font-weight:600;">
                        Menampilkan {{ $penerimas->firstItem() ?? 0 }} - {{ $penerimas->lastItem() ?? 0 }} dari {{ number_format($penerimas->total(), 0, ',', '.') }} calon penerima
                    </span>
                </div>
            </div>

            <div class="table-petugas-wrapper">
                <table class="table" style="width:100%;border-collapse:collapse;min-width:950px;">
                    <thead>
                        <tr style="background:var(--bg-body);border-bottom:1px solid rgba(0,40,85,0.08);text-align:left;font-size:12.5px;color:var(--text-muted);">
                            <th style="padding:14px 18px;width:50px;">No</th>
                            <th style="padding:14px 18px;width:70px;text-align:center;">Foto Rumah</th>
                            <th style="padding:14px 18px;min-width:180px;">Nama Calon Penerima</th>
                            <th style="padding:14px 18px;text-align:center;width:60px;">L/P</th>
                            <th style="padding:14px 18px;min-width:180px;">NIK &amp; No. KK</th>
                            <th style="padding:14px 18px;min-width:200px;">Alamat / Dusun</th>
                            <th style="padding:14px 18px;text-align:center;width:120px;">Status</th>
                            <th style="padding:14px 18px;text-align:center;min-width:160px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($penerimas as $index => $item)
                            <tr style="border-bottom:1px solid rgba(0,40,85,0.06);font-size:13px;">
                                <td style="padding:14px 18px;font-weight:700;color:var(--text-muted);">
                                    {{ $penerimas->firstItem() + $index }}
                                </td>
                                <td style="padding:14px 18px;text-align:center;">
                                    @if($item->foto_sudut_depan)
                                        <img src="{{ asset(str_starts_with($item->foto_sudut_depan, 'uploads/') ? $item->foto_sudut_depan : 'uploads/' . $item->foto_sudut_depan) }}" class="thumb-survey-mini" alt="Foto">
                                    @else
                                        <div style="width:44px;height:44px;border-radius:6px;background:#eee;display:flex;align-items:center;justify-content:center;margin:0 auto;color:#999;font-size:16px;">
                                            <i class="fas fa-image"></i>
                                        </div>
                                    @endif
                                </td>
                                <td style="padding:14px 18px;">
                                    <div style="font-weight:800;color:var(--primary-dark);">{{ $item->nama }}</div>
                                </td>
                                <td style="padding:14px 18px;text-align:center;">
                                    <span class="badge-gender {{ strtolower($item->jenis_kelamin) }}">
                                        {{ $item->jenis_kelamin ?: '-' }}
                                    </span>
                                </td>
                                <td style="padding:14px 18px;">
                                    <div style="font-family:monospace;font-weight:700;color:var(--text-primary);">NIK: {{ $item->no_ktp ?: '-' }}</div>
                                    <div style="font-family:monospace;font-size:12px;color:var(--text-muted);margin-top:2px;">KK: {{ $item->no_kk ?: '-' }}</div>
                                </td>
                                <td style="padding:14px 18px;color:var(--text-secondary);">
                                    {{ $item->alamat ?: '-' }}
                                </td>
                                <td style="padding:14px 18px;text-align:center;">
                                    <span class="badge-status-sudah"><i class="fas fa-check-circle"></i> Selesai</span>
                                </td>
                                <td style="padding:14px 18px;text-align:center;">
                                    <div style="display:flex;gap:6px;justify-content:center;">
                                        <a href="{{ route('verval-data.surat-pernyataan', $item->id) }}" target="_blank" class="btn-act print" title="Cetak Surat Pernyataan">
                                            <i class="fas fa-file-signature"></i> Cetak
                                        </a>
                                        <button type="button" class="btn-act edit" title="Edit Hasil Survei & Foto" onclick="startSurveyWithGps('{{ url('/survey/' . $item->id) }}')">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" style="text-align:center;padding:40px;color:var(--text-muted);">
                                    <i class="fas fa-clipboard-question" style="font-size:36px;opacity:0.4;display:block;margin-bottom:10px;"></i>
                                    Belum ada data calon penerima yang disurvei di Desa {{ $user->desa ?: '-' }}.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Custom Pagination Bar -->
            <div class="pagination-custom-bar">
                <div class="pagination-info-text">
                    Menampilkan <strong>{{ $penerimas->firstItem() ?? 0 }}</strong> - <strong>{{ $penerimas->lastItem() ?? 0 }}</strong> dari <strong>{{ number_format($penerimas->total(), 0, ',', '.') }}</strong> calon penerima (Halaman <strong>{{ $penerimas->currentPage() }}</strong> dari <strong>{{ $penerimas->lastPage() }}</strong>)
                </div>

                @php
                    $current = $penerimas->currentPage();
                    $last = $penerimas->lastPage();
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

                <div style="display:flex;align-items:center;flex-wrap:wrap;gap:8px;">
                    <div class="pagination-nav">
                        @if($penerimas->onFirstPage())
                            <span class="pg-link disabled"><i class="fas fa-chevron-left"></i></span>
                        @else
                            <a href="{{ $penerimas->previousPageUrl() }}" class="pg-link"><i class="fas fa-chevron-left"></i></a>
                        @endif

                        @foreach($rangeWithDots as $pageItem)
                            @if($pageItem === '...')
                                <span class="pg-dots">&hellip;</span>
                            @elseif($pageItem == $current)
                                <span class="pg-link active">{{ $pageItem }}</span>
                            @else
                                <a href="{{ $penerimas->url($pageItem) }}" class="pg-link">{{ $pageItem }}</a>
                            @endif
                        @endforeach

                        @if($penerimas->hasMorePages())
                            <a href="{{ $penerimas->nextPageUrl() }}" class="pg-link"><i class="fas fa-chevron-right"></i></a>
                        @else
                            <span class="pg-link disabled"><i class="fas fa-chevron-right"></i></span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Modal GPS Wajib (Saat Petugas Mau Mulai Survei) -->
    <div class="modal-overlay" id="modalGpsRequired">
        <div class="modal-box" style="max-width: 440px;">
            <div class="modal-header" style="background: #fff3cd; border-bottom-color: #ffeeba;">
                <h3 style="color: #856404; display: flex; align-items: center; gap: 10px; font-size: 16px;">
                    <i class="fas fa-location-dot"></i> Akses GPS / Lokasi Wajib
                </h3>
                <button class="close-btn" type="button" onclick="window.PuprModal.close('modalGpsRequired')">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="modal-body" style="padding: 24px; text-align: center;">
                <div style="width: 60px; height: 60px; border-radius: 50%; background: rgba(220, 53, 69, 0.1); color: #dc3545; display: inline-flex; align-items: center; justify-content: center; font-size: 24px; margin: 0 auto 16px;">
                    <i class="fas fa-map-location-dot"></i>
                </div>
                <h4 style="font-size: 16px; font-weight: 800; color: var(--text-primary); margin-bottom: 8px;">
                    Izin Lokasi Belum Diaktifkan!
                </h4>
                <p style="font-size: 13.5px; color: var(--text-secondary); line-height: 1.5; margin-bottom: 0;">
                    Sebagai Petugas Lapangan, Anda <strong>wajib mengaktifkan izin GPS / Lokasi</strong> pada perangkat/browser Anda untuk memastikan koordinat geotagging rumah calon penerima tercatat secara akurat saat survei.
                </p>
            </div>

            <div class="modal-footer" style="padding: 16px 20px; background: var(--bg-body); border-top: 1px solid rgba(0, 40, 85, 0.06); display: flex; gap: 10px; justify-content: center;">
                <button type="button" class="btn btn-outline" style="padding:10px 18px;" onclick="window.PuprModal.close('modalGpsRequired')">
                    Batal
                </button>
                <button type="button" class="btn btn-primary" id="btnRetryGps" style="padding:10px 20px; font-weight:800;" onclick="retryLocationPermission()">
                    <i class="fas fa-location-crosshairs"></i> Izinkan Lokasi &amp; Lanjutkan
                </button>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    let pendingSurveyUrl = null;

    function startSurveyWithGps(targetUrl) {
        pendingSurveyUrl = targetUrl;

        // 1. Tampilkan Reusable Loading Overlay
        if (window.PuprLoading) {
            window.PuprLoading.show('Sedang Memuat Lokasi GPS...');
        }

        if (!navigator.geolocation) {
            if (window.PuprLoading) window.PuprLoading.hide();
            alert("Perangkat atau browser Anda tidak mendukung fitur Geolocation/GPS.");
            return;
        }

        navigator.geolocation.getCurrentPosition(
            function(position) {
                if (window.PuprLoading) {
                    window.PuprLoading.show('Lokasi Terdeteksi, Membuka Form Survei...');
                }
                window.location.href = targetUrl;
            },
            function(error) {
                if (window.PuprLoading) {
                    window.PuprLoading.hide();
                }
                if (window.PuprModal) {
                    window.PuprModal.open('modalGpsRequired');
                } else {
                    alert("Silakan aktifkan lokasi jika mau melakukan survei!");
                }
            },
            { enableHighAccuracy: true, timeout: 8000, maximumAge: 0 }
        );
    }

    function retryLocationPermission() {
        if (window.PuprModal) window.PuprModal.close('modalGpsRequired');
        if (window.PuprLoading) window.PuprLoading.show('Sedang Mendeteksi Ulang Lokasi GPS...');

        if (!navigator.geolocation) {
            if (window.PuprLoading) window.PuprLoading.hide();
            alert("Perangkat tidak mendukung GPS.");
            return;
        }

        navigator.geolocation.getCurrentPosition(
            function(position) {
                if (window.PuprLoading) {
                    window.PuprLoading.show('Lokasi Berhasil Didapat, Membuka Form...');
                }
                if (pendingSurveyUrl) {
                    window.location.href = pendingSurveyUrl;
                }
            },
            function(error) {
                if (window.PuprLoading) {
                    window.PuprLoading.hide();
                }
                if (window.PuprModal) {
                    window.PuprModal.open('modalGpsRequired');
                }
            },
            { enableHighAccuracy: true, timeout: 8000 }
        );
    }
</script>
@endpush
