@extends('layouts.partial.app')

@section('title', 'BSPS Verval - Tugas Belum Di-survei')
@section('title_header', 'Tugas Belum Di-survei')
@section('subtitle_header', 'Daftar Calon Penerima BSPS Desa {{ Auth::user()->desa ?? "-" }} yang Menunggu Verifikasi Lapangan')

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
    .table-header-bar h3 { font-size: 15px; font-weight: 800; color: #b88600; display: flex; align-items: center; gap: 8px; margin: 0; }

    .badge-status-belum {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 11.5px;
        font-weight: 700;
        background: rgba(255, 184, 0, 0.15);
        color: #b88600;
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

    .btn-mulai-survey {
        padding: 8px 16px;
        border-radius: 6px;
        font-size: 12.5px;
        font-weight: 800;
        background: var(--primary);
        color: #ffffff;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s ease;
        box-shadow: 0 2px 6px rgba(0,40,85,0.15);
        border: none;
        cursor: pointer;
    }
    .btn-mulai-survey:hover {
        background: var(--primary-dark);
        transform: translateY(-1px);
        color: #fff;
    }

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
        min-width: 920px;
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
            <span>Tugas Belum Survei (Desa {{ $user->desa ?: '-' }})</span>
        </div>

        {{-- Filter & Search --}}
        <form action="{{ route('petugas.belum-survei') }}" method="GET" class="filter-section">
            <div class="search-input-wrap">
                <i class="fas fa-search"></i>
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama calon penerima yang belum disurvei, NIK, atau alamat..." />
            </div>
            <a href="{{ route('verval-data.surat-pernyataan-kolektif', array_merge(['desa' => $user->desa, 'status' => 'belum'], request()->all())) }}" target="_blank" class="btn" style="padding:10px 16px;font-size:13px;font-weight:700;background:#ffb800;color:#002855;text-decoration:none;border-radius:var(--radius-sm);display:inline-flex;align-items:center;gap:6px;" title="Cetak Surat Pernyataan Kolektif untuk warga yang belum disurvei di Desa {{ $user->desa ?: '-' }}">
                <i class="fas fa-file-signature"></i> Cetak Kolektif (Belum Survei)
            </a>
        </form>

        {{-- Tabel Data Belum Di-survei --}}
        <div class="table-container-card">
            <div class="table-header-bar">
                <h3><i class="fas fa-clock"></i> Daftar Calon Penerima Belum Di-survei — Desa {{ $user->desa ?: '-' }}</h3>
                <div style="display:flex;align-items:center;gap:12px;flex-wrap:wrap;">
                    <a href="{{ route('verval-data.surat-pernyataan-kolektif', array_merge(['desa' => $user->desa, 'status' => 'belum'], request()->all())) }}" target="_blank" class="btn" style="padding:8px 14px;font-size:12.5px;font-weight:800;background:#ffb800;color:#002855;text-decoration:none;border-radius:var(--radius-sm);display:inline-flex;align-items:center;gap:6px;">
                        <i class="fas fa-print"></i> Cetak Kolektif
                    </a>
                    <span style="font-size:12.5px;color:var(--text-muted);font-weight:600;">
                        Menampilkan {{ $penerimas->firstItem() ?? 0 }} - {{ $penerimas->lastItem() ?? 0 }} dari {{ number_format($penerimas->total(), 0, ',', '.') }} calon penerima
                    </span>
                </div>
            </div>

            <div class="table-petugas-wrapper">
                <table class="table" style="width:100%;border-collapse:collapse;min-width:920px;">
                    <thead>
                        <tr style="background:var(--bg-body);border-bottom:1px solid rgba(0,40,85,0.08);text-align:left;font-size:12.5px;color:var(--text-muted);">
                            <th style="padding:14px 18px;width:50px;">No</th>
                            <th style="padding:14px 18px;min-width:180px;">Nama Calon Penerima</th>
                            <th style="padding:14px 18px;text-align:center;width:60px;">L/P</th>
                            <th style="padding:14px 18px;min-width:180px;">NIK &amp; No. KK</th>
                            <th style="padding:14px 18px;min-width:200px;">Alamat / Dusun</th>
                            <th style="padding:14px 18px;min-width:140px;">Pengelompokan</th>
                            <th style="padding:14px 18px;text-align:center;width:120px;">Status</th>
                            <th style="padding:14px 18px;text-align:center;min-width:150px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($penerimas as $index => $item)
                            <tr style="border-bottom:1px solid rgba(0,40,85,0.06);font-size:13px;">
                                <td style="padding:14px 18px;font-weight:700;color:var(--text-muted);">
                                    {{ $penerimas->firstItem() + $index }}
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
                                <td style="padding:14px 18px;">
                                    <span style="font-size:12px;font-weight:700;color:var(--primary);">{{ $item->pengelompokan_desil ?: 'Desil 1-4' }}</span>
                                </td>
                                <td style="padding:14px 18px;text-align:center;">
                                    @if($item->status == 'ditemukan')
                                        <span class="badge btn-trigger-status-modal" style="background:#dcfce7;color:#15803d;padding:5px 12px;border-radius:20px;font-size:11.5px;font-weight:800;cursor:pointer;"
                                              data-id="{{ $item->id }}" data-nama="{{ e($item->nama) }}" data-nik="{{ e($item->no_ktp ?: '-') }}" data-alamat="{{ e($item->alamat ?: '-') }}" data-status="{{ e($item->status) }}" data-url="{{ url('/survey/' . $item->id) }}" title="Klik untuk ubah status">
                                            <i class="fas fa-check-circle"></i> Ditemukan
                                        </span>
                                    @elseif($item->status == 'meninggal')
                                        <span class="badge btn-trigger-status-modal" style="background:#fee2e2;color:#b91c1c;padding:5px 12px;border-radius:20px;font-size:11.5px;font-weight:800;cursor:pointer;"
                                              data-id="{{ $item->id }}" data-nama="{{ e($item->nama) }}" data-nik="{{ e($item->no_ktp ?: '-') }}" data-alamat="{{ e($item->alamat ?: '-') }}" data-status="{{ e($item->status) }}" data-url="{{ url('/survey/' . $item->id) }}" title="Klik untuk ubah status">
                                            <i class="fas fa-cross"></i> Meninggal
                                        </span>
                                    @elseif($item->status == 'pindah')
                                        <span class="badge btn-trigger-status-modal" style="background:#fef3c7;color:#b45309;padding:5px 12px;border-radius:20px;font-size:11.5px;font-weight:800;cursor:pointer;"
                                              data-id="{{ $item->id }}" data-nama="{{ e($item->nama) }}" data-nik="{{ e($item->no_ktp ?: '-') }}" data-alamat="{{ e($item->alamat ?: '-') }}" data-status="{{ e($item->status) }}" data-url="{{ url('/survey/' . $item->id) }}" title="Klik untuk ubah status">
                                            <i class="fas fa-truck-ramp-box"></i> Pindah
                                        </span>
                                    @elseif($item->status == 'tidak diketahui')
                                        <span class="badge btn-trigger-status-modal" style="background:#f1f5f9;color:#64748b;padding:5px 12px;border-radius:20px;font-size:11.5px;font-weight:800;cursor:pointer;"
                                              data-id="{{ $item->id }}" data-nama="{{ e($item->nama) }}" data-nik="{{ e($item->no_ktp ?: '-') }}" data-alamat="{{ e($item->alamat ?: '-') }}" data-status="{{ e($item->status) }}" data-url="{{ url('/survey/' . $item->id) }}" title="Klik untuk ubah status">
                                            <i class="fas fa-question-circle"></i> Tidak Diketahui
                                        </span>
                                    @else
                                        <span class="badge-status-belum btn-trigger-status-modal" style="cursor:pointer;"
                                              data-id="{{ $item->id }}" data-nama="{{ e($item->nama) }}" data-nik="{{ e($item->no_ktp ?: '-') }}" data-alamat="{{ e($item->alamat ?: '-') }}" data-status="{{ e($item->status) }}" data-url="{{ url('/survey/' . $item->id) }}" title="Klik untuk update status">
                                            <i class="fas fa-clock"></i> Belum Survei
                                        </span>
                                    @endif
                                </td>
                                <td style="padding:14px 18px;text-align:center;">
                                    <button type="button" class="btn-mulai-survey btn-trigger-status-modal"
                                            data-id="{{ $item->id }}" data-nama="{{ e($item->nama) }}" data-nik="{{ e($item->no_ktp ?: '-') }}" data-alamat="{{ e($item->alamat ?: '-') }}" data-status="{{ e($item->status) }}" data-url="{{ url('/survey/' . $item->id) }}">
                                        <i class="fas fa-camera"></i> Mulai Survei
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" style="text-align:center;padding:40px;color:var(--text-muted);">
                                    <i class="fas fa-check-double" style="font-size:36px;color:var(--success);display:block;margin-bottom:10px;"></i>
                                    <strong>Luar biasa!</strong> Seluruh calon penerima di Desa {{ $user->desa ?: '-' }} telah selesai di-survei.
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

    <!-- Modal Verifikasi Status Keberadaan Wajib Sebelum Survei -->
    <div class="modal-overlay" id="modalStatusVerification">
        <div class="modal-box" style="max-width: 480px;">
            <div class="modal-header" style="background: var(--primary); color: #ffffff;">
                <h3 style="color: #ffffff; display: flex; align-items: center; gap: 10px; font-size: 15px; font-weight: 800; margin: 0;">
                    <i class="fas fa-user-check"></i> Update Status Keberadaan Petugas
                </h3>
                <button class="close-btn" type="button" onclick="window.PuprModal.close('modalStatusVerification')" style="color: #ffffff;">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="modal-body" style="padding: 20px 24px;">
                {{-- Recipient Info Box --}}
                <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 12px 16px; margin-bottom: 16px;">
                    <div style="font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px;">Calon Penerima</div>
                    <div id="statusModalNama" style="font-size: 15px; font-weight: 800; color: var(--primary-dark); margin-top: 2px;">-</div>
                    <div style="display: flex; gap: 12px; margin-top: 6px; font-size: 12px; color: #475569; flex-wrap: wrap;">
                        <span><i class="fas fa-id-card" style="color: var(--primary);"></i> NIK: <strong id="statusModalNik">-</strong></span>
                        <span><i class="fas fa-location-dot" style="color: var(--primary);"></i> Alamat: <strong id="statusModalAlamat">-</strong></span>
                    </div>
                </div>

                <label style="font-size: 13px; font-weight: 800; color: #1e293b; display: block; margin-bottom: 10px;">
                    Pilih Status Keberadaan Lapangan <span style="color: #dc2626;">*</span>
                </label>

                <div style="display: flex; flex-direction: column; gap: 10px;">
                    <label class="status-option-card opt-ditemukan" style="display: flex; align-items: center; gap: 12px; padding: 12px 16px; border: 1px solid #cbd5e1; border-radius: 10px; cursor: pointer; transition: all 0.2s;">
                        <input type="radio" name="modal_verval_status" value="ditemukan" checked onchange="onModalStatusChange('ditemukan')" style="accent-color: #16a34a; width: 18px; height: 18px;">
                        <div style="flex: 1;">
                            <div style="font-size: 13.5px; font-weight: 800; color: #15803d; display: flex; align-items: center; gap: 6px;">
                                <i class="fas fa-check-circle"></i> Ditemukan (Ada di Lokasi)
                            </div>
                            <div style="font-size: 11.5px; color: #475569; margin-top: 2px;">
                                Penerima berada di lokasi &amp; siap untuk disurvei fisik
                            </div>
                        </div>
                    </label>

                    <label class="status-option-card opt-meninggal" style="display: flex; align-items: center; gap: 12px; padding: 12px 16px; border: 1px solid #cbd5e1; border-radius: 10px; cursor: pointer; transition: all 0.2s;">
                        <input type="radio" name="modal_verval_status" value="meninggal" onchange="onModalStatusChange('meninggal')" style="accent-color: #dc2626; width: 18px; height: 18px;">
                        <div style="flex: 1;">
                            <div style="font-size: 13.5px; font-weight: 800; color: #b91c1c; display: flex; align-items: center; gap: 6px;">
                                <i class="fas fa-heart-crack"></i> Meninggal Dunia
                            </div>
                            <div style="font-size: 11.5px; color: #475569; margin-top: 2px;">
                                Penerima telah meninggal dunia
                            </div>
                        </div>
                    </label>

                    <label class="status-option-card opt-pindah" style="display: flex; align-items: center; gap: 12px; padding: 12px 16px; border: 1px solid #cbd5e1; border-radius: 10px; cursor: pointer; transition: all 0.2s;">
                        <input type="radio" name="modal_verval_status" value="pindah" onchange="onModalStatusChange('pindah')" style="accent-color: #d97706; width: 18px; height: 18px;">
                        <div style="flex: 1;">
                            <div style="font-size: 13.5px; font-weight: 800; color: #b45309; display: flex; align-items: center; gap: 6px;">
                                <i class="fas fa-house-circle-xmark"></i> Pindah Alamat
                            </div>
                            <div style="font-size: 11.5px; color: #475569; margin-top: 2px;">
                                Penerima telah pindah tempat tinggal
                            </div>
                        </div>
                    </label>

                    <label class="status-option-card opt-tidak-diketahui" style="display: flex; align-items: center; gap: 12px; padding: 12px 16px; border: 1px solid #cbd5e1; border-radius: 10px; cursor: pointer; transition: all 0.2s;">
                        <input type="radio" name="modal_verval_status" value="tidak diketahui" onchange="onModalStatusChange('tidak diketahui')" style="accent-color: #475569; width: 18px; height: 18px;">
                        <div style="flex: 1;">
                            <div style="font-size: 13.5px; font-weight: 800; color: #475569; display: flex; align-items: center; gap: 6px;">
                                <i class="fas fa-question-circle"></i> Tidak Diketahui
                            </div>
                            <div style="font-size: 11.5px; color: #475569; margin-top: 2px;">
                                Keberadaan penerima tidak ditemukan / tidak diketahui
                            </div>
                        </div>
                    </label>
                </div>
            </div>

            <div class="modal-footer" style="padding: 14px 20px; background: var(--bg-body); border-top: 1px solid rgba(0, 40, 85, 0.06); display: flex; gap: 10px; justify-content: flex-end;">
                <button type="button" class="btn btn-outline" style="padding: 9px 16px;" onclick="window.PuprModal.close('modalStatusVerification')">
                    Batal
                </button>
                <button type="button" class="btn btn-primary" id="btnSubmitStatusVerification" style="padding: 9px 20px; font-weight: 800; display: inline-flex; align-items: center; gap: 6px;" onclick="submitStatusVerification()">
                    <i class="fas fa-location-crosshairs"></i> Simpan &amp; Lanjutkan Survei
                </button>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    let pendingSurveyUrl = null;
    let currentVervalId = null;
    let currentTargetSurveyUrl = null;

    document.addEventListener('DOMContentLoaded', function () {
        document.addEventListener('click', function (e) {
            const btn = e.target.closest('.btn-trigger-status-modal');
            if (btn) {
                e.preventDefault();
                const id = btn.getAttribute('data-id');
                const nama = btn.getAttribute('data-nama');
                const nik = btn.getAttribute('data-nik');
                const alamat = btn.getAttribute('data-alamat');
                const status = btn.getAttribute('data-status');
                const url = btn.getAttribute('data-url');

                openStatusVerificationModal(id, nama, nik, alamat, status, url);
            }
        });
    });

    function openStatusVerificationModal(id, nama, nik, alamat, currentStatus, surveyUrl) {
        currentVervalId = id;
        currentTargetSurveyUrl = surveyUrl;

        document.getElementById('statusModalNama').textContent = nama || '-';
        document.getElementById('statusModalNik').textContent = nik || '-';
        document.getElementById('statusModalAlamat').textContent = alamat || '-';

        const statusToSelect = (currentStatus && ['ditemukan', 'meninggal', 'pindah', 'tidak diketahui'].includes(currentStatus)) ? currentStatus : 'ditemukan';
        const radio = document.querySelector(`input[name="modal_verval_status"][value="${statusToSelect}"]`);
        if (radio) {
            radio.checked = true;
        }
        onModalStatusChange(statusToSelect);

        if (window.PuprModal) {
            window.PuprModal.open('modalStatusVerification');
        }
    }

    function onModalStatusChange(statusVal) {
        const btn = document.getElementById('btnSubmitStatusVerification');
        if (!btn) return;

        if (statusVal === 'ditemukan') {
            btn.className = 'btn btn-primary';
            btn.style.background = 'var(--primary)';
            btn.style.color = '#fff';
            btn.innerHTML = '<i class="fas fa-location-crosshairs"></i> Simpan &amp; Lanjutkan Survei';
        } else {
            btn.className = 'btn btn-warning';
            btn.style.background = '#d97706';
            btn.style.color = '#fff';
            btn.innerHTML = '<i class="fas fa-save"></i> Simpan Status Baru';
        }
    }

    function submitStatusVerification() {
        const selectedRadio = document.querySelector('input[name="modal_verval_status"]:checked');
        if (!selectedRadio || !currentVervalId) return;

        const newStatus = selectedRadio.value;

        if (window.PuprLoading) {
            window.PuprLoading.show('Memperbarui Status Penerima...');
        }

        fetch(`/data-verval/${currentVervalId}/status`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ status: newStatus })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                if (newStatus === 'ditemukan') {
                    if (window.PuprModal) window.PuprModal.close('modalStatusVerification');
                    startSurveyWithGps(currentTargetSurveyUrl);
                } else {
                    if (window.PuprLoading) window.PuprLoading.hide();
                    if (window.PuprModal) window.PuprModal.close('modalStatusVerification');
                    if (window.PuprToast) {
                        window.PuprToast.success(`Status berhasil diperbarui menjadi "${newStatus.toUpperCase()}"`);
                    } else {
                        alert(`Status berhasil diperbarui menjadi "${newStatus.toUpperCase()}"`);
                    }
                    setTimeout(() => { window.location.reload(); }, 1000);
                }
            } else {
                if (window.PuprLoading) window.PuprLoading.hide();
                alert(data.message || 'Gagal memperbarui status.');
            }
        })
        .catch(err => {
            if (window.PuprLoading) window.PuprLoading.hide();
            console.error(err);
            alert('Terjadi kesalahan koneksi saat memperbarui status.');
        });
    }

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
