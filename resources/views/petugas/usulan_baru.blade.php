@extends('layouts.partial.app')

@section('title', 'BSPS Verval - Usulan Baru Lapangan')
@section('title_header', 'Usulan Baru Lapangan')
@section('subtitle_header', 'Daftar Calon Penerima BSPS Usulan Baru Lapangan Desa {{ Auth::user()->desa ?? "-" }}')

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
    .table-header-bar h3 { font-size: 15px; font-weight: 800; color: #0891b2; display: flex; align-items: center; gap: 8px; margin: 0; }

    .badge-status-usulan {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 11.5px;
        font-weight: 700;
        background: rgba(8, 145, 178, 0.12);
        color: #0891b2;
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
        border-top: 1px solid rgba(0,40,85,0.06);
        background: var(--bg-card);
    }
    .pagination-info-text { font-size: 12.5px; color: var(--text-muted); font-weight: 600; }
    .pagination-nav { display: flex; align-items: center; gap: 4px; }
    .pg-link {
        min-width: 32px; height: 32px; padding: 0 8px; border-radius: var(--radius-sm);
        display: inline-flex; align-items: center; justify-content: center;
        font-size: 12.5px; font-weight: 700; color: var(--text-primary);
        text-decoration: none; border: 1px solid rgba(0,40,85,0.12);
        background: var(--bg-card); transition: all 0.2s ease;
    }
    .pg-link:hover { border-color: var(--primary); color: var(--primary); background: var(--bg-body); }
    .pg-link.active { background: var(--primary); color: #ffffff; border-color: var(--primary); box-shadow: 0 2px 8px rgba(0,40,85,0.2); }
    .pg-link.disabled { opacity: 0.4; cursor: not-allowed; pointer-events: none; }
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
            <span>Usulan Baru Lapangan (Desa {{ $user->desa ?: '-' }})</span>
        </div>

    {{-- Alert Offline Status --}}
    <div id="offlinePetugasAlert" style="display:none;background:#fffbe6;border:1px solid #ffe58f;border-radius:10px;padding:12px 16px;margin-bottom:16px;">
        <div style="font-size:13px;font-weight:800;color:#d48806;display:flex;align-items:center;gap:8px;">
            <i class="fas fa-wifi-slash"></i> Mode Offline Aktif
        </div>
        <div style="font-size:12px;color:#8c6800;margin-top:2px;">
            Anda sedang menguji atau berada dalam kondisi tanpa internet. Pencarian &amp; filter berjalan secara lokal di memori HP/Browser.
        </div>
    </div>

    {{-- Filter & Search Bar --}}
    <form action="{{ route('petugas.usulan-baru') }}" method="GET" class="filter-section" id="filterFormUsulanBaru">
        <div class="search-input-wrap">
            <i class="fas fa-search"></i>
            <input type="text" id="searchUsulanBaru" name="search" value="{{ $search }}" placeholder="Cari nama calon penerima, NIK, KK, atau alamat..." />
        </div>
        @if($search)
            <a href="{{ route('petugas.usulan-baru') }}" class="btn btn-outline" style="padding:9px 14px;font-size:12.5px;font-weight:700;">
                <i class="fas fa-rotate-left"></i> Reset Cari
            </a>
        @endif
    </form>

    {{-- Table Container --}}
    <div class="table-container-card">
        <div class="table-header-bar">
            <h3>
                <i class="fas fa-user-plus"></i>
                Daftar Usulan Baru Lapangan — Desa {{ Auth::user()->desa ?: '-' }}
            </h3>
            <span style="font-size:12.5px;color:var(--text-muted);font-weight:600;">
                Menampilkan {{ $penerimas->firstItem() ?? 0 }} - {{ $penerimas->lastItem() ?? 0 }} dari {{ number_format($penerimas->total(), 0, ',', '.') }} calon penerima
            </span>
        </div>

        <div class="table-petugas-wrapper">
            <table class="table" style="width:100%;border-collapse:collapse;min-width:850px;">
                <thead>
                    <tr style="background:var(--bg-body);border-bottom:1px solid rgba(0,40,85,0.08);text-align:left;font-size:12.5px;color:var(--text-muted);">
                        <th style="padding:14px 18px;width:50px;">No</th>
                        <th style="padding:14px 18px;min-width:180px;">Nama Calon Penerima</th>
                        <th style="padding:14px 18px;text-align:center;width:60px;">L/P</th>
                        <th style="padding:14px 18px;min-width:180px;">NIK &amp; No. KK</th>
                        <th style="padding:14px 18px;min-width:200px;">Alamat / Dusun</th>
                        <th style="padding:14px 18px;text-align:center;width:150px;">Status Survei</th>
                        <th style="padding:14px 18px;text-align:center;min-width:160px;">Aksi Lapangan</th>
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
                                <span class="badge-status-usulan" style="margin-top:4px;">
                                    <i class="fas fa-tag"></i> {{ $item->pengelompokan_desil ?: 'Usulan Baru Lapangan' }}
                                </span>
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
                                @if(in_array($item->status, ['meninggal', 'pindah', 'tidak diketahui']))
                                    @php
                                        $statusLabel = ['meninggal' => ['icon' => 'fa-heart-crack', 'text' => 'Meninggal', 'color' => '#dc2626', 'bg' => '#fee2e2'], 'pindah' => ['icon' => 'fa-house-chimney-crack', 'text' => 'Pindah', 'color' => '#d97706', 'bg' => '#fef3c7'], 'tidak diketahui' => ['icon' => 'fa-question-circle', 'text' => 'Tdk Diketahui', 'color' => '#6b7280', 'bg' => '#f3f4f6']];
                                        $sl = $statusLabel[$item->status];
                                    @endphp
                                    <span class="badge-status-survey" style="background:{{ $sl['bg'] }};color:{{ $sl['color'] }};border-radius:20px;padding:4px 10px;font-size:11.5px;font-weight:700;display:inline-flex;align-items:center;gap:5px;">
                                        <i class="fas {{ $sl['icon'] }}"></i> {{ $sl['text'] }}
                                    </span>
                                @elseif($item->isSudahSurvei())
                                    <span class="badge-status-survey sudah" style="background:rgba(34,197,94,0.12);color:#16a34a;padding:4px 10px;border-radius:20px;font-size:11.5px;font-weight:700;display:inline-flex;align-items:center;gap:4px;">
                                        <i class="fas fa-check-circle"></i> Sudah Survei
                                    </span>
                                @else
                                    <span class="badge-status-survey belum" style="background:rgba(255,184,0,0.15);color:#b88600;padding:4px 10px;border-radius:20px;font-size:11.5px;font-weight:700;display:inline-flex;align-items:center;gap:4px;">
                                        <i class="fas fa-clock"></i> Belum Survei
                                    </span>
                                @endif
                            </td>
                            <td style="padding:14px 18px;text-align:center;">
                                <div style="display:inline-flex;align-items:center;gap:6px;">
                                    <button type="button" class="btn-mulai-survey btn-trigger-status-modal"
                                            data-id="{{ $item->id }}" data-nama="{{ e($item->nama) }}" data-nik="{{ e($item->no_ktp ?: '-') }}" data-alamat="{{ e($item->alamat ?: '-') }}" data-status="{{ e($item->status) }}" data-url="{{ url('/survey/' . $item->id) }}">
                                        @if(in_array($item->status, ['meninggal', 'pindah', 'tidak diketahui']))
                                            <i class="fas fa-info-circle"></i> Lihat Detail
                                        @elseif($item->isSudahSurvei())
                                            <i class="fas fa-camera"></i> Lihat / Edit
                                        @else
                                            <i class="fas fa-camera"></i> Mulai Survei
                                        @endif
                                    </button>
                                    <a href="{{ route('verval-data.surat-pernyataan', $item->id) }}" target="_blank" class="btn-act" style="background:rgba(0,40,85,0.08);color:var(--primary-dark);padding:7px 10px;" title="Cetak Surat Pernyataan Satuan">
                                        <i class="fas fa-file-signature"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align:center;padding:32px;color:var(--text-muted);">
                                <i class="fas fa-clipboard-question" style="font-size:28px;display:block;margin-bottom:8px;opacity:0.4;"></i>
                                Belum ada usulan baru dari lapangan di Desa {{ Auth::user()->desa ?: '-' }}. Klik <strong>"+ Tambah Usulan Baru"</strong> di atas untuk membuat usulan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Custom Pagination Bar --}}
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

                    @foreach($rangeWithDots as $page)
                        @if($page === '...')
                            <span class="pg-link disabled">...</span>
                        @elseif($page == $current)
                            <span class="pg-link active">{{ $page }}</span>
                        @else
                            <a href="{{ $penerimas->url($page) }}" class="pg-link">{{ $page }}</a>
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
    </main>

<!-- Modal Pilihan Verifikasi / Keberadaan Penerima -->
<div class="modal-overlay" id="modalStatusVerification">
    <div class="modal-box" style="max-width: 520px; border-radius: 14px; overflow: hidden; box-shadow: 0 20px 40px rgba(0, 40, 85, 0.25);">
        <div class="modal-header" style="background: linear-gradient(135deg, #002855 0%, #001835 100%); color: #fff; padding: 18px 22px; display: flex; align-items: center; justify-content: space-between;">
            <div style="display: flex; align-items: center; gap: 12px;">
                <div style="width: 38px; height: 38px; border-radius: 50%; background: rgba(255, 184, 0, 0.2); color: #ffb800; display: flex; align-items: center; justify-content: center; font-size: 16px; flex-shrink: 0;">
                    <i class="fas fa-user-check"></i>
                </div>
                <div>
                    <h3 style="font-size: 15.5px; font-weight: 800; margin: 0; color: #fff;">Status Keberadaan Penerima</h3>
                    <p style="font-size: 11.5px; color: rgba(255, 255, 255, 0.75); margin: 2px 0 0 0;">Verifikasi keberadaan fisik calon penerima sebelum mengisi survei</p>
                </div>
            </div>
            <button type="button" style="background: transparent; border: none; color: rgba(255,255,255,0.7); font-size: 22px; cursor: pointer; line-height: 1;" onclick="window.PuprModal.close('modalStatusVerification')">&times;</button>
        </div>

        <div class="modal-body" style="padding: 20px 22px;">
            <div style="background: var(--bg-body); border-radius: 10px; padding: 12px 16px; margin-bottom: 18px; border-left: 4px solid var(--primary);">
                <div style="font-size: 14px; font-weight: 800; color: var(--primary-dark);" id="statusModalNama">-</div>
                <div style="font-size: 12px; color: var(--text-muted); margin-top: 2px;">
                    NIK: <span id="statusModalNik" style="font-family: monospace; font-weight: 700; color: var(--text-primary);">-</span>
                </div>
                <div style="font-size: 12px; color: var(--text-secondary); margin-top: 2px;" id="statusModalAlamat">-</div>
            </div>

            <label style="font-size: 12px; font-weight: 800; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; display: block; margin-bottom: 10px;">
                Pilih Kondisi Lapangan:
            </label>

            <div style="display: flex; flex-direction: column; gap: 10px;">
                <label class="status-option-card opt-ditemukan" style="display: flex; align-items: center; gap: 12px; padding: 12px 16px; border: 2px solid var(--primary); background: rgba(0, 40, 85, 0.03); border-radius: 10px; cursor: pointer; transition: all 0.2s;">
                    <input type="radio" name="modal_verval_status" value="ditemukan" checked onchange="onModalStatusChange('ditemukan')" style="accent-color: var(--primary); width: 18px; height: 18px;">
                    <div style="flex: 1;">
                        <div style="font-size: 13.5px; font-weight: 800; color: var(--primary-dark); display: flex; align-items: center; gap: 6px;">
                            <i class="fas fa-house-user"></i> Ada / Ditemukan
                        </div>
                        <div style="font-size: 11.5px; color: var(--text-muted); margin-top: 2px;">
                            Penerima tinggal di lokasi &amp; siap diverifikasi fisik
                        </div>
                    </div>
                </label>

                <label class="status-option-card opt-meninggal" style="display: flex; align-items: center; gap: 12px; padding: 12px 16px; border: 1px solid #cbd5e1; border-radius: 10px; cursor: pointer; transition: all 0.2s;">
                    <input type="radio" name="modal_verval_status" value="meninggal" onchange="onModalStatusChange('meninggal')" style="accent-color: #dc2626; width: 18px; height: 18px;">
                    <div style="flex: 1;">
                        <div style="font-size: 13.5px; font-weight: 800; color: #dc2626; display: flex; align-items: center; gap: 6px;">
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

<!-- Modal Tambah Usulan Calon Penerima BSPS (PUPR Style) -->
<div class="modal-overlay" id="modalTambahUsulan">
    <div class="modal-box" style="max-width: 600px; border-radius: 14px; overflow: hidden; box-shadow: 0 20px 40px rgba(0, 40, 85, 0.25);">
        <div class="modal-header" style="background: linear-gradient(135deg, #002855 0%, #001835 100%); color: #fff; padding: 18px 22px; display: flex; align-items: center; justify-content: space-between;">
            <div style="display: flex; align-items: center; gap: 12px;">
                <div style="width: 40px; height: 40px; border-radius: 50%; background: rgba(34, 197, 94, 0.2); color: #22c55e; display: flex; align-items: center; justify-content: center; font-size: 18px; flex-shrink: 0;">
                    <i class="fas fa-user-plus"></i>
                </div>
                <div>
                    <h3 style="font-size: 16px; font-weight: 800; margin: 0; color: #fff;">Usulkan Calon Penerima Baru</h3>
                    <p style="font-size: 12px; color: rgba(255, 255, 255, 0.75); margin: 2px 0 0 0;">
                        Desa <strong>{{ $user->desa ?: '-' }}</strong> &bull; Kec. <strong>{{ $user->kecamatan ?: '-' }}</strong>
                    </p>
                </div>
            </div>
            <button type="button" style="background: transparent; border: none; color: rgba(255,255,255,0.7); font-size: 22px; cursor: pointer; line-height: 1;" onclick="window.PuprModal.close('modalTambahUsulan')">&times;</button>
        </div>

        <form action="{{ route('petugas.usulkan-penerima') }}" method="POST" id="formTambahUsulan">
            @csrf
            <div class="modal-body" style="padding: 22px; max-height: 75vh; overflow-y: auto;">
                <div style="background: rgba(34, 197, 94, 0.08); border: 1px solid rgba(34, 197, 94, 0.2); border-radius: 8px; padding: 12px 14px; margin-bottom: 18px; display: flex; align-items: center; justify-content: space-between;">
                    <span style="font-size: 12.5px; color: #15803d; font-weight: 600;">
                        <i class="fas fa-location-dot" style="margin-right: 4px;"></i> Wilayah Usulan: <strong>Desa {{ $user->desa ?: '-' }}</strong> (Kec. {{ $user->kecamatan ?: '-' }})
                    </span>
                    <span style="background: #22c55e; color: #fff; font-size: 10px; font-weight: 800; padding: 3px 8px; border-radius: 12px; text-transform: uppercase;">
                        Otomatis Terunci
                    </span>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
                    <div style="grid-column: span 2;">
                        <label style="font-size: 12px; font-weight: 700; color: #334155; display: block; margin-bottom: 4px;">
                            Nama Lengkap Calon Penerima <span style="color:#ef4444;">*</span>
                        </label>
                        <input type="text" name="nama" class="form-control" placeholder="Contoh: SAMAD" required style="width: 100%; padding: 10px 12px; border-radius: 8px; border: 1px solid #cbd5e1; font-size: 13px;">
                    </div>

                    <div>
                        <label style="font-size: 12px; font-weight: 700; color: #334155; display: block; margin-bottom: 4px;">
                            NIK / No. KTP (16 Digit) <span style="color:#ef4444;">*</span>
                        </label>
                        <input type="text" name="no_ktp" class="form-control" maxlength="16" minlength="16" placeholder="350917..." required style="width: 100%; padding: 10px 12px; border-radius: 8px; border: 1px solid #cbd5e1; font-size: 13px;">
                    </div>

                    <div>
                        <label style="font-size: 12px; font-weight: 700; color: #334155; display: block; margin-bottom: 4px;">
                            Nomor Kartu Keluarga (KK)
                        </label>
                        <input type="text" name="no_kk" class="form-control" maxlength="16" placeholder="350917..." style="width: 100%; padding: 10px 12px; border-radius: 8px; border: 1px solid #cbd5e1; font-size: 13px;">
                    </div>

                    <div>
                        <label style="font-size: 12px; font-weight: 700; color: #334155; display: block; margin-bottom: 4px;">
                            Jenis Kelamin
                        </label>
                        <select name="jenis_kelamin" class="form-control" style="width: 100%; padding: 10px 12px; border-radius: 8px; border: 1px solid #cbd5e1; font-size: 13px; background: #fff;">
                            <option value="L">Laki-laki</option>
                            <option value="P">Perempuan</option>
                        </select>
                    </div>

                    <div>
                        <label style="font-size: 12px; font-weight: 700; color: #334155; display: block; margin-bottom: 4px;">
                            Pengelompokan Desil / Status Usulan
                        </label>
                        <input type="hidden" name="pengelompokan_desil" value="Usulan Baru Lapangan">
                        <div style="width: 100%; padding: 10px 12px; border-radius: 8px; border: 1px solid #cbd5e1; font-size: 13px; background: #f8fafc; color: #15803d; font-weight: 800; display: flex; align-items: center; justify-content: space-between;">
                            <span><i class="fas fa-tag" style="margin-right:6px;color:#22c55e;"></i> Usulan Baru Lapangan</span>
                            <span style="font-size: 9.5px; background: #22c55e; color: #fff; padding: 2px 6px; border-radius: 10px; font-weight: 800; text-transform: uppercase;">Terkunci</span>
                        </div>
                    </div>

                    <div>
                        <label style="font-size: 12px; font-weight: 700; color: #334155; display: block; margin-bottom: 4px;">
                            Dusun / Dukuh
                        </label>
                        <input type="text" name="dusun" class="form-control" placeholder="Contoh: Krajan" style="width: 100%; padding: 10px 12px; border-radius: 8px; border: 1px solid #cbd5e1; font-size: 13px;">
                    </div>

                    <div style="display: flex; gap: 8px;">
                        <div style="flex: 1;">
                            <label style="font-size: 12px; font-weight: 700; color: #334155; display: block; margin-bottom: 4px;">
                                RT
                            </label>
                            <input type="text" name="rt" class="form-control" maxlength="5" placeholder="001" style="width: 100%; padding: 10px 12px; border-radius: 8px; border: 1px solid #cbd5e1; font-size: 13px;">
                        </div>
                        <div style="flex: 1;">
                            <label style="font-size: 12px; font-weight: 700; color: #334155; display: block; margin-bottom: 4px;">
                                RW
                            </label>
                            <input type="text" name="rw" class="form-control" maxlength="5" placeholder="002" style="width: 100%; padding: 10px 12px; border-radius: 8px; border: 1px solid #cbd5e1; font-size: 13px;">
                        </div>
                    </div>

                    <div style="grid-column: span 2;">
                        <label style="font-size: 12px; font-weight: 700; color: #334155; display: block; margin-bottom: 4px;">
                            Alamat Jalan / Rumah
                        </label>
                        <textarea name="alamat" class="form-control" rows="2" placeholder="Contoh: Jl. Mawar No. 12" style="width: 100%; padding: 10px 12px; border-radius: 8px; border: 1px solid #cbd5e1; font-size: 13px; font-family: inherit;"></textarea>
                    </div>
                </div>
            </div>

            <div class="modal-footer" style="padding: 14px 22px; background: var(--bg-body); border-top: 1px solid rgba(0, 40, 85, 0.06); display: flex; gap: 10px; justify-content: flex-end; align-items: center; flex-wrap: wrap;">
                <button type="button" class="btn btn-outline" style="padding: 9px 16px;" onclick="window.PuprModal.close('modalTambahUsulan')">
                    Batal
                </button>
                <button type="submit" class="btn btn-primary" style="padding: 9px 18px; font-weight: 800; background: #002855; color: #fff; display: inline-flex; align-items: center; gap: 6px;">
                    <i class="fas fa-save"></i> Simpan Usulan
                </button>
            </div>
        </form>
    </div>
</div>

<script id="allPenerimasUsulanData" type="application/json">
    {!! json_encode($allPenerimas ?? []) !!}
</script>

@push('scripts')
<script>
    let currentVervalId = null;
    let currentTargetSurveyUrl = null;

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

    function openStatusVerificationModal(id, nama, nik, alamat, currentStatus, surveyUrl) {
        currentVervalId = id;
        currentTargetSurveyUrl = surveyUrl;

        const elNama = document.getElementById('statusModalNama');
        const elNik = document.getElementById('statusModalNik');
        const elAlamat = document.getElementById('statusModalAlamat');

        if (elNama) elNama.textContent = nama || '-';
        if (elNik) elNik.textContent = nik || '-';
        if (elAlamat) elAlamat.textContent = alamat || '-';

        const statusToSelect = (currentStatus && ['ditemukan', 'meninggal', 'pindah', 'tidak diketahui'].includes(currentStatus)) ? currentStatus : 'ditemukan';
        const radio = document.querySelector(`input[name="modal_verval_status"][value="${statusToSelect}"]`);
        if (radio) {
            radio.checked = true;
            onModalStatusChange(statusToSelect);
        }

        if (window.PuprModal) window.PuprModal.open('modalStatusVerification');
    }

    function onModalStatusChange(val) {
        document.querySelectorAll('.status-option-card').forEach(card => {
            card.style.border = '1px solid #cbd5e1';
            card.style.background = 'transparent';
        });

        const selectedRadio = document.querySelector(`input[name="modal_verval_status"][value="${val}"]`);
        if (selectedRadio) {
            const parentCard = selectedRadio.closest('.status-option-card');
            if (parentCard) {
                if (val === 'ditemukan') {
                    parentCard.style.border = '2px solid var(--primary)';
                    parentCard.style.background = 'rgba(0, 40, 85, 0.03)';
                } else if (val === 'meninggal') {
                    parentCard.style.border = '2px solid #dc2626';
                    parentCard.style.background = 'rgba(220, 38, 38, 0.04)';
                } else if (val === 'pindah') {
                    parentCard.style.border = '2px solid #d97706';
                    parentCard.style.background = 'rgba(217, 119, 6, 0.04)';
                } else if (val === 'tidak diketahui') {
                    parentCard.style.border = '2px solid #475569';
                    parentCard.style.background = 'rgba(71, 85, 105, 0.04)';
                }
            }
        }

        const btnSubmit = document.getElementById('btnSubmitStatusVerification');
        if (btnSubmit) {
            if (val === 'ditemukan') {
                btnSubmit.innerHTML = `<i class="fas fa-location-crosshairs"></i> Simpan & Lanjutkan Survei`;
                btnSubmit.style.background = 'var(--primary)';
            } else {
                btnSubmit.innerHTML = `<i class="fas fa-save"></i> Simpan Status Lapangan`;
                btnSubmit.style.background = '#002855';
            }
        }
    }

    function submitStatusVerification() {
        if (!currentVervalId) return;
        const selectedRadio = document.querySelector('input[name="modal_verval_status"]:checked');
        const newStatus = selectedRadio ? selectedRadio.value : 'ditemukan';

        if (window.PuprLoading) window.PuprLoading.show('Memperbarui Status Penerima...');

        fetch(`/data-verval/${currentVervalId}/status`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ status: newStatus })
        })
        .then(res => res.json())
        .then(data => {
            if (window.PuprLoading) window.PuprLoading.hide();
            if (window.PuprModal) window.PuprModal.close('modalStatusVerification');
            if (data.success) {
                if (newStatus === 'ditemukan' && currentTargetSurveyUrl) {
                    window.location.href = currentTargetSurveyUrl;
                } else {
                    window.location.reload();
                }
            } else {
                alert(data.message || 'Gagal memperbarui status.');
            }
        })
        .catch(() => {
            if (window.PuprLoading) window.PuprLoading.hide();
            if (window.PuprModal) window.PuprModal.close('modalStatusVerification');
        });
    }

    // Offline Client-Side Search
    let ALL_DATA_USULAN = [];
    try {
        const raw = document.getElementById('allPenerimasUsulanData')?.textContent;
        if (raw) ALL_DATA_USULAN = JSON.parse(raw);
    } catch (e) {}

    let ORIGINAL_ROWS_USULAN = null;

    function escapeHtml(str) {
        if (!str) return '';
        return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }

    function filterUsulanTableOffline() {
        const searchInput = document.getElementById('searchUsulanBaru');
        const query = (searchInput?.value || '').toLowerCase().trim();
        const terms = query.split(/\s+/).filter(t => t.length > 0);
        const tbody = document.querySelector('.table-petugas-wrapper tbody');
        if (!tbody) return;

        if (ORIGINAL_ROWS_USULAN === null) ORIGINAL_ROWS_USULAN = tbody.innerHTML;

        if (terms.length === 0) {
            tbody.innerHTML = ORIGINAL_ROWS_USULAN;
            return;
        }

        const matches = ALL_DATA_USULAN.filter(item => {
            const fullText = `${item.nama || ''} ${item.no_ktp || ''} ${item.no_kk || ''} ${item.alamat || ''}`.toLowerCase();
            return terms.every(term => fullText.includes(term));
        });

        if (matches.length === 0) {
            tbody.innerHTML = `<tr><td colspan="7" style="text-align:center;padding:30px;color:var(--text-muted);font-weight:600;"><i class="fas fa-search" style="margin-right:6px;"></i> Tidak ada data usulan baru yang cocok dengan pencarian</td></tr>`;
        } else {
            let html = '';
            matches.forEach((item, idx) => {
                const genderClass = (item.jenis_kelamin || '').toLowerCase();
                const itemStatus = (item.status || '').toLowerCase();
                const isSpecial = ['meninggal', 'pindah', 'tidak diketahui'].includes(itemStatus);
                const isSudah = Boolean(item.foto_sudut_depan);

                let statusBadge = '';
                if (itemStatus === 'meninggal') {
                    statusBadge = `<span class="badge-status-survey" style="background:#fee2e2;color:#dc2626;border-radius:20px;padding:4px 10px;font-size:11.5px;font-weight:700;display:inline-flex;align-items:center;gap:5px;"><i class="fas fa-heart-crack"></i> Meninggal</span>`;
                } else if (itemStatus === 'pindah') {
                    statusBadge = `<span class="badge-status-survey" style="background:#fef3c7;color:#d97706;border-radius:20px;padding:4px 10px;font-size:11.5px;font-weight:700;display:inline-flex;align-items:center;gap:5px;"><i class="fas fa-house-chimney-crack"></i> Pindah</span>`;
                } else if (itemStatus === 'tidak diketahui') {
                    statusBadge = `<span class="badge-status-survey" style="background:#f3f4f6;color:#6b7280;border-radius:20px;padding:4px 10px;font-size:11.5px;font-weight:700;display:inline-flex;align-items:center;gap:5px;"><i class="fas fa-question-circle"></i> Tdk Diketahui</span>`;
                } else if (isSudah) {
                    statusBadge = `<span class="badge-status-survey sudah" style="background:rgba(34,197,94,0.12);color:#16a34a;padding:4px 10px;border-radius:20px;font-size:11.5px;font-weight:700;display:inline-flex;align-items:center;gap:4px;"><i class="fas fa-check-circle"></i> Sudah Survei</span>`;
                } else {
                    statusBadge = `<span class="badge-status-survey belum" style="background:rgba(255,184,0,0.15);color:#b88600;padding:4px 10px;border-radius:20px;font-size:11.5px;font-weight:700;display:inline-flex;align-items:center;gap:4px;"><i class="fas fa-clock"></i> Belum Survei</span>`;
                }

                let actionLabel = '';
                if (isSpecial) {
                    actionLabel = `<i class="fas fa-info-circle"></i> Lihat Detail`;
                } else if (isSudah) {
                    actionLabel = `<i class="fas fa-camera"></i> Lihat / Edit`;
                } else {
                    actionLabel = `<i class="fas fa-camera"></i> Mulai Survei`;
                }

                html += `
                    <tr style="border-bottom:1px solid rgba(0,40,85,0.06);font-size:13px;">
                        <td style="padding:14px 18px;font-weight:700;color:var(--text-muted);">${idx + 1}</td>
                        <td style="padding:14px 18px;">
                            <div style="font-weight:800;color:var(--primary-dark);">${escapeHtml(item.nama)}</div>
                            <span class="badge-status-usulan" style="margin-top:4px;">
                                <i class="fas fa-tag"></i> ${escapeHtml(item.pengelompokan_desil || 'Usulan Baru Lapangan')}
                            </span>
                        </td>
                        <td style="padding:14px 18px;text-align:center;">
                            <span class="badge-gender ${genderClass}">${escapeHtml(item.jenis_kelamin || '-')}</span>
                        </td>
                        <td style="padding:14px 18px;">
                            <div style="font-family:monospace;font-weight:700;color:var(--text-primary);">NIK: ${escapeHtml(item.no_ktp || '-')}</div>
                            <div style="font-family:monospace;font-size:12px;color:var(--text-muted);margin-top:2px;">KK: ${escapeHtml(item.no_kk || '-')}</div>
                        </td>
                        <td style="padding:14px 18px;color:var(--text-secondary);">${escapeHtml(item.alamat || '-')}</td>
                        <td style="padding:14px 18px;text-align:center;">${statusBadge}</td>
                        <td style="padding:14px 18px;text-align:center;">
                            <div style="display:inline-flex;align-items:center;gap:6px;">
                                <button type="button" class="btn-mulai-survey btn-trigger-status-modal"
                                        data-id="${item.id}" data-nama="${escapeHtml(item.nama)}" data-nik="${escapeHtml(item.no_ktp || '-')}" data-alamat="${escapeHtml(item.alamat || '-')}" data-status="${escapeHtml(item.status || '')}" data-url="/survey/${item.id}">
                                    ${actionLabel}
                                </button>
                                <a href="/verval-data/surat-pernyataan/${item.id}" target="_blank" class="btn-act" style="background:rgba(0,40,85,0.08);color:var(--primary-dark);padding:7px 10px;" title="Cetak Surat Pernyataan Satuan">
                                    <i class="fas fa-file-signature"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                `;
            });
            tbody.innerHTML = html;
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchUsulanBaru');
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                if (!navigator.onLine) {
                    filterUsulanTableOffline();
                }
            });
        }

        if (!navigator.onLine) {
            const alert = document.getElementById('offlinePetugasAlert');
            if (alert) alert.style.display = 'block';
        }
    });
</script>
@endpush
@endsection
