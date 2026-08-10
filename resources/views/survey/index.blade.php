@extends('layouts.partial.app')

@section('title', 'PUPR Jember - Form Survey Lapangan')
@section('title_header', 'Form Survey Lapangan')
@section('subtitle_header', 'Input Data Verifikasi Lapangan, Koordinat GPS, dan Dokumentasi Fisik PUPR Jember')

@push('styles')
<style>
    .survey-card {
        background: var(--bg-card);
        border-radius: var(--radius);
        box-shadow: var(--shadow-sm);
        border: 1px solid rgba(0, 40, 85, 0.06);
        padding: 28px 32px;
        margin-bottom: 24px;
    }

    .survey-section-title {
        font-size: 16px;
        font-weight: 800;
        color: var(--primary);
        margin-bottom: 24px;
        padding-bottom: 12px;
        border-bottom: 2px solid rgba(0, 40, 85, 0.08);
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .form-group {
        margin-bottom: 22px;
    }

    .form-group label {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 13.5px;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 10px !important;
        line-height: 1.3;
    }

    .form-group input[type="text"],
    .form-group input[type="number"],
    .form-group input[type="date"],
    .form-group select,
    .form-group textarea {
        display: block;
        width: 100%;
        padding: 11px 15px !important;
        font-size: 14px;
        border-radius: var(--radius-sm);
        border: 1px solid rgba(0, 40, 85, 0.16) !important;
        background: var(--bg-card) !important;
        color: var(--text-primary);
        transition: all 0.2s ease;
        margin-top: 2px !important;
    }

    .form-group input:focus,
    .form-group select:focus,
    .form-group textarea:focus {
        border-color: var(--primary) !important;
        box-shadow: 0 0 0 3px rgba(0, 40, 85, 0.10) !important;
        outline: none;
    }

    .form-grid-2 {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 24px;
        margin-bottom: 24px;
    }

    .form-grid-3 {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 24px;
        margin-bottom: 24px;
    }

    .photo-upload-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 16px;
        margin-top: 10px;
    }

    .photo-upload-box {
        border: 2px dashed rgba(0, 40, 85, 0.18);
        border-radius: var(--radius-sm);
        padding: 20px;
        text-align: center;
        background: var(--bg-body);
        cursor: pointer;
        transition: var(--transition);
        position: relative;
    }

    .photo-upload-box:hover {
        border-color: var(--primary);
        background: rgba(0, 40, 85, 0.03);
    }

    .photo-upload-box i {
        font-size: 28px;
        color: var(--primary);
        margin-bottom: 8px;
    }

    .photo-upload-box .title {
        font-size: 13px;
        font-weight: 700;
        color: var(--text-primary);
    }

    .photo-upload-box .subtitle {
        font-size: 11px;
        color: var(--text-muted);
        margin-top: 4px;
    }

    .photo-upload-box input[type="file"] {
        position: absolute;
        inset: 0;
        opacity: 0;
        cursor: pointer;
    }

    @media (max-width: 768px) {
        .survey-card { padding: 18px 14px; }
        .form-grid-2, .form-grid-3, .photo-upload-grid, .checklist-foto-row {
            grid-template-columns: 1fr;
            gap: 14px;
        }
        .checklist-item-header {
            flex-direction: column;
            align-items: stretch;
            gap: 12px;
        }
        .checklist-toggle-group {
            width: 100%;
            display: flex;
            gap: 10px;
        }
        .checklist-radio-label {
            flex: 1;
            justify-content: center;
            text-align: center;
            padding: 10px 12px;
        }
        .form-actions {
            flex-direction: column;
        }
        .form-actions .btn {
            width: 100%;
            justify-content: center;
        }
    }

    @media (max-width: 480px) {
        .dashboard-content { padding: 12px; }
        .survey-card { padding: 16px 12px; }
        .survey-section-title { font-size: 15px; }
    }

    /* ---- Checklist Item Card & Radio Buttons ---- */
    .checklist-item-card {
        border: 1px solid rgba(0, 40, 85, 0.1);
        border-radius: var(--radius-sm);
        padding: 20px;
        margin-bottom: 18px;
        background: var(--bg-body);
        transition: border-color 0.2s;
    }

    .checklist-item-card:last-of-type {
        margin-bottom: 0;
    }

    .checklist-item-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 16px;
        flex-wrap: wrap;
    }

    .checklist-item-label {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        flex: 1;
    }

    .checklist-nomor {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 32px;
        height: 32px;
        border-radius: 50%;
        background: var(--primary);
        color: #fff;
        font-size: 12px;
        font-weight: 800;
        flex-shrink: 0;
    }

    .checklist-title {
        font-size: 14px;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 3px;
    }

    .checklist-desc {
        font-size: 12px;
        color: var(--text-muted);
        line-height: 1.4;
    }

    .checklist-toggle-group {
        display: flex;
        gap: 10px;
        flex-shrink: 0;
    }

    /* Vibrant Glowing Radio Buttons */
    .checklist-radio-label input[type="radio"] {
        display: none !important;
        position: absolute !important;
        opacity: 0 !important;
        width: 0 !important;
        height: 0 !important;
        pointer-events: none !important;
    }

    .checklist-radio-label {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 9px 20px;
        border-radius: 30px;
        border: 2px solid rgba(0, 40, 85, 0.18);
        font-size: 13.5px;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        user-select: none;
        white-space: nowrap;
        background: var(--bg-card);
        color: var(--text-muted);
    }

    .checklist-radio-label:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    }

    .checklist-radio-label.sesuai:has(input:checked),
    .checklist-radio-label.sesuai.active {
        background: #27ae60 !important;
        border-color: #27ae60 !important;
        color: #ffffff !important;
        box-shadow: 0 6px 20px rgba(39, 174, 96, 0.45) !important;
        transform: translateY(-2px) scale(1.04) !important;
    }

    .checklist-radio-label.tidak:has(input:checked),
    .checklist-radio-label.tidak.active {
        background: #e74c3c !important;
        border-color: #e74c3c !important;
        color: #ffffff !important;
        box-shadow: 0 6px 20px rgba(231, 76, 60, 0.45) !important;
        transform: translateY(-2px) scale(1.04) !important;
    }

    .checklist-radio-label.sesuai:has(input:checked) i,
    .checklist-radio-label.tidak:has(input:checked) i,
    .checklist-radio-label.sesuai.active i,
    .checklist-radio-label.tidak.active i {
        color: #ffffff !important;
    }

    .checklist-foto-row {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 12px;
    }

    .checklist-foto-box {
        position: relative;
        border: 1.5px dashed rgba(0, 40, 85, 0.2);
        border-radius: var(--radius-sm);
        padding: 14px 10px;
        text-align: center;
        cursor: pointer;
        transition: border-color 0.2s, background 0.2s;
        background: var(--bg-card);
    }

    .checklist-foto-box.has-file {
        border-style: solid;
        border-color: #27ae60;
        background: rgba(39, 174, 96, 0.05);
        cursor: default;
    }

    .checklist-foto-actions {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        margin-top: 8px;
        position: relative;
        z-index: 5;
    }

    .btn-view-photo i,
    .btn-delete-photo-inline i {
        color: inherit !important;
        font-size: 10px;
        margin: 0 !important;
    }

    .btn-view-photo {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        font-size: 11px;
        font-weight: 700;
        color: var(--primary);
        text-decoration: none;
        background: rgba(0, 40, 85, 0.08);
        padding: 4px 10px;
        border-radius: 20px;
        transition: background 0.2s, color 0.2s;
    }

    .btn-view-photo:hover {
        background: var(--primary);
        color: #ffffff;
    }

    .btn-delete-photo-inline {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        font-size: 11px;
        font-weight: 700;
        color: #e74c3c;
        background: rgba(231, 76, 60, 0.1);
        border: none;
        padding: 4px 10px;
        border-radius: 20px;
        cursor: pointer;
        transition: background 0.2s, color 0.2s;
    }

    .btn-delete-photo-inline:hover {
        background: #e74c3c;
        color: #ffffff;
    }

    .checklist-foto-box:hover {
        border-color: var(--primary);
        background: rgba(0, 40, 85, 0.03);
    }

    .checklist-foto-box.has-file:hover {
        border-color: #27ae60;
        background: rgba(39, 174, 96, 0.05);
    }

    .checklist-foto-box i {
        font-size: 20px;
        color: var(--primary);
        margin-bottom: 6px;
        display: block;
    }

    .checklist-foto-box .title {
        font-size: 12px;
        font-weight: 700;
        color: var(--text-primary);
    }

    .checklist-foto-box .subtitle {
        font-size: 10px;
        color: var(--text-muted);
        margin-top: 3px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .checklist-foto-box input[type="file"] {
        position: absolute;
        inset: 0;
        opacity: 0;
        cursor: pointer;
    }

    @media (max-width: 768px) {
        .checklist-item-header {
            flex-direction: column;
        }
        .checklist-foto-row {
            grid-template-columns: 1fr;
        }
        .checklist-toggle-group {
            width: 100%;
        }
    }
</style>
@endpush

@php
    $selNamaKegiatan = isset($selectedKegiatan) ? $selectedKegiatan->nama_kegiatan : '';
    $selPemohon = isset($selectedKegiatan) ? $selectedKegiatan->nama_pemohon : '';
    $selNik = isset($selectedKegiatan) ? $selectedKegiatan->nik_pemohon : '';
    $selAlamat = isset($selectedKegiatan) ? $selectedKegiatan->alamat : '';
    $selKecamatan = isset($selectedKegiatan) ? ucwords(str_replace('_',' ',$selectedKegiatan->lokasi)) : '';

    $p1 = isset($selectedKegiatan) && $selectedKegiatan->petugas->count() > 0 
        ? $selectedKegiatan->petugas->get(0)->name 
        : (Auth::check() ? Auth::user()->name : '');

    $p2 = isset($selectedKegiatan) && $selectedKegiatan->petugas->count() > 1 
        ? $selectedKegiatan->petugas->get(1)->name 
        : '';
@endphp

@section('content')
    @include('layouts.navbar_public')

    <main class="dashboard-content dashboard-content-public">

            @if(session('success'))
                <div style="padding:14px 18px;border-radius:var(--radius-sm);background:rgba(39,174,96,0.12);border:1px solid rgba(39,174,96,0.3);color:var(--success);font-weight:600;margin-bottom:20px;display:flex;align-items:center;gap:10px;">
                    <i class="fas fa-check-circle" style="font-size:18px;"></i>
                    <div>{{ session('success') }}</div>
                </div>
            @endif

            @php
                $bapKegiatanId = isset($selectedKegiatan) ? $selectedKegiatan->id : ($existingSurvey ? $existingSurvey->data_mingguan_id : null);
            @endphp

            @if(isset($existingSurvey) && $existingSurvey)
                <div style="padding:14px 18px;border-radius:var(--radius-sm);background:rgba(41,128,185,0.12);border:1px solid rgba(41,128,185,0.3);color:var(--primary-dark);font-weight:600;margin-bottom:20px;display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;">
                    <div style="display:flex;align-items:center;gap:10px;">
                        <i class="fas fa-edit" style="font-size:18px;color:var(--primary);"></i>
                        <div><strong>Mode Cek &amp; Edit Survei:</strong> Data hasil survei yang telah tersimpan dimuat secara otomatis. Anda dapat memeriksa ulang atau memperbarui isi data.</div>
                    </div>
                    @if($bapKegiatanId)
                        <a href="{{ url('/cetak-bap/' . $bapKegiatanId) }}" target="_blank" class="btn" style="padding:9px 20px;border-radius:var(--radius-sm);background:var(--primary);color:#fff;font-weight:700;white-space:nowrap;text-decoration:none;display:inline-flex;align-items:center;gap:8px;box-shadow:0 4px 12px rgba(0,40,85,0.2);">
                            <i class="fas fa-print"></i> Cetak BAP (Berita Acara)
                        </a>
                    @endif
                </div>
            @endif

            <form action="{{ url('/survey') }}" method="POST" id="formSurveyUtama" enctype="multipart/form-data">
                @csrf

                <div class="survey-card">
                    <div class="survey-section-title">
                        <i class="fas fa-list-check"></i> 1. Data Kegiatan Lapangan &amp; Petugas Survei
                    </div>
                    <div class="form-grid-2">
                        <div class="form-group">
                            <label><i class="fas fa-tasks"></i> Pilih Kegiatan Lapangan (Relasi Data Kegiatan)</label>
                            <input type="hidden" name="data_mingguan_id" id="data_mingguan_id_hidden" value="{{ request('kegiatan_id') ?? (isset($selectedKegiatan) ? $selectedKegiatan->id : '') }}" />

                            @if(Auth::check() && Auth::user()->isPetugas())
                                {{-- Jika Petugas: Dikunci / Locked --}}
                                <div style="width:100%;display:flex;align-items:center;justify-content:space-between;padding:11px 16px;background:var(--bg-body);border:1px solid rgba(0,40,85,0.14);border-radius:var(--radius-sm);color:var(--text-primary);font-weight:700;font-size:13.5px;cursor:not-allowed;">
                                    <span>
                                        <i class="fas fa-lock" style="color:var(--secondary);margin-right:8px;"></i>
                                        {{ isset($selectedKegiatan) ? $selectedKegiatan->nama_kegiatan.' (Kec. '.ucwords(str_replace('_',' ',$selectedKegiatan->lokasi)).')' : '-- Kegiatan Lapangan Terkunci --' }}
                                    </span>
                                    <span style="font-size:11px;background:rgba(0,40,85,0.08);color:var(--text-muted);padding:3px 10px;border-radius:20px;font-weight:600;">
                                        Terkunci (Petugas)
                                    </span>
                                </div>
                                <div style="font-size:11.5px;color:var(--text-muted);margin-top:6px;">
                                    <i class="fas fa-info-circle"></i> Petugas tidak dapat mengganti kegiatan di halaman ini. Untuk memilih kegiatan lain, silakan ke menu <a href="{{ route('petugas.belum-survei') }}" style="color:var(--primary);font-weight:700;">Belum Survei</a> atau <a href="{{ route('petugas.sudah-survei') }}" style="color:var(--primary);font-weight:700;">Sudah Survei</a>.
                                </div>
                            @else
                                {{-- Jika Admin: Dropdown Pilihan Bebas --}}
                                <div class="pupr-dropdown-wrapper" style="width:100%;">
                                    <button type="button" class="pupr-dropdown-toggle" data-toggle="pupr-dropdown" style="width:100%;display:flex;align-items:center;justify-content:space-between;padding:11px 16px;">
                                        <span class="selected-label">
                                            {{ isset($selectedKegiatan) ? $selectedKegiatan->nama_kegiatan.' (Kec. '.ucwords(str_replace('_',' ',$selectedKegiatan->lokasi)).')' : '-- Non-Kegiatan / Survei Mandiri --' }}
                                        </span>
                                        <i class="fas fa-chevron-down" style="font-size:11px;opacity:0.6;"></i>
                                    </button>
                                    <div class="pupr-dropdown-menu" style="width:100%;max-height:220px;overflow-y:auto;">
                                        <div class="pupr-dropdown-item {{ (!request('kegiatan_id') && !isset($selectedKegiatan)) ? 'active' : '' }}"
                                             data-value=""
                                             data-target="data_mingguan_id_hidden"
                                             onclick="loadKegiatanSurvey('')">
                                            -- Non-Kegiatan / Survei Mandiri --
                                        </div>
                                        @foreach($kegiatans as $k)
                                            <div class="pupr-dropdown-item {{ (request('kegiatan_id') == $k->id || (isset($selectedKegiatan) && $selectedKegiatan->id == $k->id)) ? 'active' : '' }}"
                                                 data-value="{{ $k->id }}"
                                                 data-target="data_mingguan_id_hidden"
                                                 onclick="loadKegiatanSurvey('{{ $k->id }}')">
                                                {{ $k->nama_kegiatan }} (Kec. {{ ucwords(str_replace('_',' ',$k->lokasi)) }})
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                        <div class="form-group">
                            <label><i class="fas fa-heading"></i> Nama Kegiatan Survei Lapangan <span style="color:var(--danger);">*</span></label>
                            <input type="text" name="nama_kegiatan" id="inputNamaKegiatanSurvey" value="{{ old('nama_kegiatan', $existingSurvey ? $existingSurvey->nama_kegiatan : $selNamaKegiatan) }}" placeholder="Masukkan nama kegiatan survei..." required />
                        </div>
                    </div>

                    <div class="form-grid-3">
                        <div class="form-group">
                            <label><i class="fas fa-user"></i> Nama Petugas Survei 1 <span style="color:var(--danger);">*</span></label>
                            <input type="text" name="nama_petugas_1" value="{{ old('nama_petugas_1', $existingSurvey ? $existingSurvey->nama_petugas_1 : $p1) }}" placeholder="Nama lengkap petugas 1..." required />
                        </div>
                        <div class="form-group">
                            <label><i class="fas fa-user"></i> Nama Petugas Survei 2</label>
                            <input type="text" name="nama_petugas_2" value="{{ old('nama_petugas_2', $existingSurvey ? $existingSurvey->nama_petugas_2 : $p2) }}" placeholder="Nama lengkap petugas 2 (opsional)..." />
                        </div>
                        <div class="form-group">
                            <label><i class="fas fa-calendar"></i> Tanggal Survei <span style="color:var(--danger);">*</span></label>
                            <input type="date" name="tanggal_survei" value="{{ old('tanggal_survei', $existingSurvey ? $existingSurvey->tanggal_survei->format('Y-m-d') : date('Y-m-d')) }}" required />
                        </div>
                    </div>
                </div>

                <div class="survey-card">
                    <div class="survey-section-title">
                        <i class="fas fa-person"></i> 2. Data Pemohon
                    </div>
                    <div class="form-grid-2">
                        <div class="form-group">
                            <label><i class="fas fa-user-tie"></i> Nama Pemohon <span style="color:var(--danger);">*</span></label>
                            <input type="text" name="nama_pemohon" value="{{ old('nama_pemohon', $existingSurvey ? $existingSurvey->nama_pemohon : $selPemohon) }}" placeholder="Nama lengkap pemohon..." required />
                        </div>
                        <div class="form-group">
                            <label><i class="fas fa-id-card"></i> NIK Pemohon</label>
                            <input type="text" name="nik_pemohon" value="{{ old('nik_pemohon', $existingSurvey ? $existingSurvey->nik_pemohon : $selNik) }}" placeholder="Nomor Induk Kependudukan..." maxlength="16" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-house"></i> Alamat Pemohon <span style="color:var(--danger);">*</span></label>
                        <input type="text" name="alamat_pemohon" value="{{ old('alamat_pemohon', $existingSurvey ? $existingSurvey->alamat_pemohon : $selAlamat) }}" placeholder="Dusun, RT/RW, Desa, Kecamatan..." required />
                    </div>
                </div>

                <div class="survey-card">
                    <div class="survey-section-title">
                        <i class="fas fa-building"></i> 3. Data Bangunan Gedung
                    </div>
                    <div class="form-grid-2">
                        <div class="form-group">
                            <label><i class="fas fa-layer-group"></i> Jenis Bangunan <span style="color:var(--danger);">*</span></label>
                            @php $valJenis = old('jenis_bangunan', $existingSurvey ? $existingSurvey->jenis_bangunan : ''); @endphp
                            <input type="hidden" name="jenis_bangunan" id="jenis_bangunan_hidden" value="{{ $valJenis }}" required />
                            <div class="pupr-dropdown-wrapper" style="width:100%;">
                                <button type="button" class="pupr-dropdown-toggle" data-toggle="pupr-dropdown" style="width:100%;display:flex;align-items:center;justify-content:space-between;padding:11px 16px;">
                                    <span class="selected-label">{{ $valJenis ? $valJenis : '-- Pilih Jenis Bangunan --' }}</span>
                                    <i class="fas fa-chevron-down" style="font-size:11px;opacity:0.6;"></i>
                                </button>
                                <div class="pupr-dropdown-menu" style="width:100%;max-height:220px;overflow-y:auto;">
                                    @foreach(['Rumah Kediaman','Ruko / Toko','Gedung Kantor','Gudang','Fasilitas Umum','Lainnya'] as $jb)
                                        <div class="pupr-dropdown-item {{ $valJenis === $jb ? 'active' : '' }}" data-value="{{ $jb }}" data-target="jenis_bangunan_hidden">{{ $jb }}</div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label><i class="fas fa-home"></i> Fungsi Bangunan <span style="color:var(--danger);">*</span></label>
                            @php $valFungsi = old('fungsi_bangunan', $existingSurvey ? $existingSurvey->fungsi_bangunan : ''); @endphp
                            <input type="hidden" name="fungsi_bangunan" id="fungsi_bangunan_hidden" value="{{ $valFungsi }}" required />
                            <div class="pupr-dropdown-wrapper" style="width:100%;">
                                <button type="button" class="pupr-dropdown-toggle" data-toggle="pupr-dropdown" style="width:100%;display:flex;align-items:center;justify-content:space-between;padding:11px 16px;">
                                    <span class="selected-label">{{ $valFungsi ? $valFungsi : '-- Pilih Fungsi Bangunan --' }}</span>
                                    <i class="fas fa-chevron-down" style="font-size:11px;opacity:0.6;"></i>
                                </button>
                                <div class="pupr-dropdown-menu" style="width:100%;max-height:220px;overflow-y:auto;">
                                    @foreach(['Fungsi Hunian','Fungsi Usaha','Fungsi Sosial Budaya','Fungsi Khusus','Fungsi Campuran'] as $fb)
                                        <div class="pupr-dropdown-item {{ $valFungsi === $fb ? 'active' : '' }}" data-value="{{ $fb }}" data-target="fungsi_bangunan_hidden">{{ $fb }}</div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-grid-3">
                        <div class="form-group">
                            <label><i class="fas fa-stairs"></i> Jumlah Lantai <span style="color:var(--danger);">*</span></label>
                            <input type="number" name="jumlah_lantai" min="1" max="50" value="{{ old('jumlah_lantai', $existingSurvey ? $existingSurvey->jumlah_lantai : '') }}" placeholder="Contoh: 2" required />
                        </div>
                        <div class="form-group">
                            <label><i class="fas fa-arrows-up-down"></i> Tinggi Bangunan (m)</label>
                            <input type="number" name="tinggi_bangunan" step="0.1" value="{{ old('tinggi_bangunan', $existingSurvey ? $existingSurvey->tinggi_bangunan : '') }}" placeholder="Contoh: 8.5" />
                        </div>
                        <div class="form-group">
                            <label><i class="fas fa-vector-square"></i> Luas Bangunan (m²)</label>
                            <input type="number" name="luas_bangunan" step="0.01" value="{{ old('luas_bangunan', $existingSurvey ? $existingSurvey->luas_bangunan : '') }}" placeholder="Contoh: 450" />
                        </div>
                    </div>
                    <div class="form-grid-2">
                        <div class="form-group">
                            <label><i class="fas fa-expand"></i> Luas Tanah (m²)</label>
                            <input type="number" name="luas_tanah" step="0.01" value="{{ old('luas_tanah', $existingSurvey ? $existingSurvey->luas_tanah : '') }}" placeholder="Contoh: 4789" />
                        </div>
                        <div class="form-group">
                            <label><i class="fas fa-file-contract"></i> Status Hak Tanah</label>
                            @php $valHak = old('status_hak_tanah', $existingSurvey ? $existingSurvey->status_hak_tanah : ''); @endphp
                            <input type="hidden" name="status_hak_tanah" id="status_hak_tanah_hidden" value="{{ $valHak }}" />
                            <div class="pupr-dropdown-wrapper" style="width:100%;">
                                <button type="button" class="pupr-dropdown-toggle" data-toggle="pupr-dropdown" style="width:100%;display:flex;align-items:center;justify-content:space-between;padding:11px 16px;">
                                    <span class="selected-label">{{ $valHak ? $valHak : '-- Pilih Status Hak Tanah --' }}</span>
                                    <i class="fas fa-chevron-down" style="font-size:11px;opacity:0.6;"></i>
                                </button>
                                <div class="pupr-dropdown-menu" style="width:100%;max-height:220px;overflow-y:auto;">
                                    @foreach(['Hak Milik','Hak Guna Bangunan','Hak Guna Usaha','Hak Pakai','Tanah Negara'] as $st)
                                        <div class="pupr-dropdown-item {{ $valHak === $st ? 'active' : '' }}" data-value="{{ $st }}" data-target="status_hak_tanah_hidden">{{ $st }}</div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="survey-card">
                    <div class="survey-section-title">
                        <i class="fas fa-map-marked-alt"></i> 4. Lokasi &amp; Koordinat GPS
                    </div>
                    <div class="form-grid-3">
                        <div class="form-group">
                            <label><i class="fas fa-location-dot"></i> Kecamatan <span style="color:var(--danger);">*</span></label>
                            @php $valKec = old('kecamatan', $existingSurvey ? $existingSurvey->kecamatan : $selKecamatan); @endphp
                            <input type="hidden" name="kecamatan" id="kecamatan_hidden" value="{{ $valKec }}" required />
                            <div class="pupr-dropdown-wrapper" style="width:100%;">
                                <button type="button" class="pupr-dropdown-toggle" data-toggle="pupr-dropdown" style="width:100%;display:flex;align-items:center;justify-content:space-between;padding:11px 16px;">
                                    <span class="selected-label">{{ $valKec ? $valKec : '-- Pilih Kecamatan --' }}</span>
                                    <i class="fas fa-chevron-down" style="font-size:11px;opacity:0.6;"></i>
                                </button>
                                <div class="pupr-dropdown-menu" style="width:100%;max-height:220px;overflow-y:auto;">
                                    @foreach(['Kaliwates','Sumbersari','Patrang','Ajung','Rambipuji','Balung','Ambulu','Wuluhan','Puger','Kencong','Gumukmas','Umbulsari','Semboro','Jombang','Silo','Mayang','Mumbulsari','Jenggawah','Tempurejo','Pakusari','Sukowono','Kalisat','Ledokombo','Sumberjambe','Arjasa','Jelbuk','Bangsalsari','Panti','Sukorambi','Tanggul','Sumberbaru'] as $kec)
                                        <div class="pupr-dropdown-item {{ strtolower($valKec) == strtolower($kec) ? 'active' : '' }}" data-value="{{ $kec }}" data-target="kecamatan_hidden">{{ $kec }}</div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label><i class="fas fa-map-pin"></i> Desa / Kelurahan <span style="color:var(--danger);">*</span></label>
                            <input type="text" name="desa_kelurahan" value="{{ old('desa_kelurahan', $existingSurvey ? $existingSurvey->desa_kelurahan : '') }}" placeholder="Nama desa atau kelurahan..." required />
                        </div>
                        <div class="form-group">
                            <label><i class="fas fa-road"></i> Nama Jalan / Ruas <span style="color:var(--danger);">*</span></label>
                            <input type="text" name="nama_jalan" value="{{ old('nama_jalan', $existingSurvey ? $existingSurvey->nama_jalan : '') }}" placeholder="Contoh: Jl. Puger No. 45..." required />
                        </div>
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-map"></i> Alamat Lengkap Lokasi Bangunan <span style="color:var(--danger);">*</span></label>
                        <input type="text" name="alamat_lokasi" value="{{ old('alamat_lokasi', $existingSurvey ? $existingSurvey->alamat_lokasi : $selAlamat) }}" placeholder="Alamat lengkap letak bangunan yang disurvey..." required />
                    </div>
                    <div class="form-grid-2">
                        <div class="form-group">
                            <label><i class="fas fa-compass"></i> Latitude GPS <span style="color:var(--danger);">*</span></label>
                            <input type="text" id="gpsLat" name="latitude" value="{{ old('latitude', $existingSurvey ? $existingSurvey->latitude : '') }}" placeholder="-8.1721" required />
                        </div>
                        <div class="form-group">
                            <label><i class="fas fa-compass"></i> Longitude GPS <span style="color:var(--danger);">*</span></label>
                            <div style="display:flex;gap:10px;">
                                <input type="text" id="gpsLng" name="longitude" value="{{ old('longitude', $existingSurvey ? $existingSurvey->longitude : '') }}" placeholder="113.6997" required style="flex:1;" />
                                <button type="button" class="btn btn-outline" id="btnAutoGps" style="padding:10px 16px;white-space:nowrap;">
                                    <i class="fas fa-crosshairs"></i> Deteksi GPS
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="survey-card">
                    <div class="survey-section-title">
                        <i class="fas fa-clipboard-check"></i> 5. Daftar Simak Pemeriksaan Lapangan
                    </div>
                    <p style="font-size:13px;color:var(--text-muted);margin-bottom:20px;">
                        Centang status kesesuaian untuk setiap item pemeriksaan, lalu unggah maksimal 3 foto bukti lapangan.
                    </p>

                    {{-- ITEM 1: Persyaratan Administratif --}}
                    @php $vAdmin = old('item_admin', $existingSurvey ? $existingSurvey->item_admin : null); @endphp
                    <div class="checklist-item-card" id="item-admin">
                        <div class="checklist-item-header">
                            <div class="checklist-item-label">
                                <span class="checklist-nomor">1</span>
                                <div>
                                    <div class="checklist-title">Persyaratan Administratif</div>
                                    <div class="checklist-desc">Kelengkapan dokumen IMB/PBG, surat permohonan, dan berkas administrasi lainnya</div>
                                </div>
                            </div>
                            <div class="checklist-toggle-group">
                                <label class="checklist-radio-label sesuai {{ $vAdmin === 'Sesuai' ? 'active' : '' }}">
                                    <input type="radio" name="item_admin" value="Sesuai" {{ $vAdmin === 'Sesuai' ? 'checked' : '' }} required />
                                    <i class="fas fa-check-circle"></i> Sesuai
                                </label>
                                <label class="checklist-radio-label tidak {{ $vAdmin === 'Tidak Sesuai' ? 'active' : '' }}">
                                    <input type="radio" name="item_admin" value="Tidak Sesuai" {{ $vAdmin === 'Tidak Sesuai' ? 'checked' : '' }} />
                                    <i class="fas fa-times-circle"></i> Tidak Sesuai
                                </label>
                            </div>
                        </div>
                        <div class="checklist-foto-row">
                            @foreach([1, 2, 3] as $idx)
                                @php $fKey = 'foto_admin_' . $idx; $fVal = $existingSurvey ? $existingSurvey->$fKey : null; @endphp
                                <div class="checklist-foto-box {{ $fVal ? 'has-file' : '' }}">
                                    <i class="{{ $fVal ? 'fas fa-circle-check' : 'fas fa-camera' }}" style="{{ $fVal ? 'color:#27ae60;' : '' }}"></i>
                                    <div class="title">Foto Dokumen {{ $idx }}</div>
                                    <div class="subtitle" id="fn_admin_{{ $idx }}" style="{{ $fVal ? 'color:#27ae60;font-weight:700;' : '' }}">{{ $fVal ? 'Terunggah & Tersimpan' : 'Pilih gambar' }}</div>
                                    @if($fVal)
                                        <div class="checklist-foto-actions">
                                            <a href="{{ asset($fVal) }}" target="_blank" class="btn-view-photo" onclick="event.stopPropagation();">
                                                <i class="fas fa-external-link-alt" style="font-size:10px;"></i> Buka Foto
                                            </a>
                                            <button type="button" class="btn-delete-photo-inline" title="Hapus / Ganti Foto" onclick="resetPhotoBox(this, event, 'foto_admin_{{ $idx }}')">
                                                <i class="fas fa-trash-alt" style="font-size:10px;"></i> Hapus
                                            </button>
                                        </div>
                                    @endif
                                    <input type="file" name="foto_admin_{{ $idx }}" accept="image/*" onchange="uploadSinglePhoto(this,'fn_admin_{{ $idx }}')" style="{{ $fVal ? 'display:none;' : '' }}" />
                                </div>
                            @endforeach
                        </div>
                        <div class="form-group" style="margin-top:12px;">
                            <label style="font-size:12px;color:var(--text-muted);"><i class="fas fa-pen"></i> Catatan item ini (opsional)</label>
                            <input type="text" name="catatan_admin" value="{{ old('catatan_admin', $existingSurvey ? $existingSurvey->catatan_admin : '') }}" placeholder="Keterangan tambahan..." />
                        </div>
                    </div>

                    {{-- ITEM 2a: Fungsi Bangunan Gedung --}}
                    @php $vFungsi = old('item_fungsi', $existingSurvey ? $existingSurvey->item_fungsi : null); @endphp
                    <div class="checklist-item-card" id="item-fungsi">
                        <div class="checklist-item-header">
                            <div class="checklist-item-label">
                                <span class="checklist-nomor">2a</span>
                                <div>
                                    <div class="checklist-title">Fungsi Bangunan Gedung</div>
                                    <div class="checklist-desc">Kesesuaian fungsi bangunan dengan peruntukan yang tercantum dalam IMB/PBG</div>
                                </div>
                            </div>
                            <div class="checklist-toggle-group">
                                <label class="checklist-radio-label sesuai {{ $vFungsi === 'Sesuai' ? 'active' : '' }}">
                                    <input type="radio" name="item_fungsi" value="Sesuai" {{ $vFungsi === 'Sesuai' ? 'checked' : '' }} required />
                                    <i class="fas fa-check-circle"></i> Sesuai
                                </label>
                                <label class="checklist-radio-label tidak {{ $vFungsi === 'Tidak Sesuai' ? 'active' : '' }}">
                                    <input type="radio" name="item_fungsi" value="Tidak Sesuai" {{ $vFungsi === 'Tidak Sesuai' ? 'checked' : '' }} />
                                    <i class="fas fa-times-circle"></i> Tidak Sesuai
                                </label>
                            </div>
                        </div>
                        <div class="checklist-foto-row">
                            @foreach([1, 2, 3] as $idx)
                                @php $fKey = 'foto_fungsi_' . $idx; $fVal = $existingSurvey ? $existingSurvey->$fKey : null; @endphp
                                <div class="checklist-foto-box {{ $fVal ? 'has-file' : '' }}">
                                    <i class="{{ $fVal ? 'fas fa-circle-check' : 'fas fa-camera' }}" style="{{ $fVal ? 'color:#27ae60;' : '' }}"></i>
                                    <div class="title">Foto {{ $idx }}</div>
                                    <div class="subtitle" id="fn_fungsi_{{ $idx }}" style="{{ $fVal ? 'color:#27ae60;font-weight:700;' : '' }}">{{ $fVal ? 'Terunggah & Tersimpan' : 'Pilih gambar' }}</div>
                                    @if($fVal)
                                        <div class="checklist-foto-actions">
                                            <a href="{{ asset($fVal) }}" target="_blank" class="btn-view-photo" onclick="event.stopPropagation();">
                                                <i class="fas fa-external-link-alt" style="font-size:10px;"></i> Buka Foto
                                            </a>
                                            <button type="button" class="btn-delete-photo-inline" title="Hapus / Ganti Foto" onclick="resetPhotoBox(this, event, 'foto_fungsi_{{ $idx }}')">
                                                <i class="fas fa-trash-alt" style="font-size:10px;"></i> Hapus
                                            </button>
                                        </div>
                                    @endif
                                    <input type="file" name="foto_fungsi_{{ $idx }}" accept="image/*" onchange="uploadSinglePhoto(this,'fn_fungsi_{{ $idx }}')" style="{{ $fVal ? 'display:none;' : '' }}" />
                                </div>
                            @endforeach
                        </div>
                        <div class="form-group" style="margin-top:12px;">
                            <label style="font-size:12px;color:var(--text-muted);"><i class="fas fa-pen"></i> Catatan item ini (opsional)</label>
                            <input type="text" name="catatan_fungsi" value="{{ old('catatan_fungsi', $existingSurvey ? $existingSurvey->catatan_fungsi : '') }}" placeholder="Keterangan tambahan..." />
                        </div>
                    </div>

                    {{-- ITEM 2b: Peruntukan --}}
                    @php $vPeruntukan = old('item_peruntukan', $existingSurvey ? $existingSurvey->item_peruntukan : null); @endphp
                    <div class="checklist-item-card" id="item-peruntukan">
                        <div class="checklist-item-header">
                            <div class="checklist-item-label">
                                <span class="checklist-nomor">2b</span>
                                <div>
                                    <div class="checklist-title">Peruntukan</div>
                                    <div class="checklist-desc">Kesesuaian peruntukan lahan dengan Rencana Tata Ruang Wilayah (RTRW)</div>
                                </div>
                            </div>
                            <div class="checklist-toggle-group">
                                <label class="checklist-radio-label sesuai {{ $vPeruntukan === 'Sesuai' ? 'active' : '' }}">
                                    <input type="radio" name="item_peruntukan" value="Sesuai" {{ $vPeruntukan === 'Sesuai' ? 'checked' : '' }} required />
                                    <i class="fas fa-check-circle"></i> Sesuai
                                </label>
                                <label class="checklist-radio-label tidak {{ $vPeruntukan === 'Tidak Sesuai' ? 'active' : '' }}">
                                    <input type="radio" name="item_peruntukan" value="Tidak Sesuai" {{ $vPeruntukan === 'Tidak Sesuai' ? 'checked' : '' }} />
                                    <i class="fas fa-times-circle"></i> Tidak Sesuai
                                </label>
                            </div>
                        </div>
                        <div class="checklist-foto-row">
                            @foreach([1, 2, 3] as $idx)
                                @php $fKey = 'foto_peruntukan_' . $idx; $fVal = $existingSurvey ? $existingSurvey->$fKey : null; @endphp
                                <div class="checklist-foto-box {{ $fVal ? 'has-file' : '' }}">
                                    <i class="{{ $fVal ? 'fas fa-circle-check' : 'fas fa-camera' }}" style="{{ $fVal ? 'color:#27ae60;' : '' }}"></i>
                                    <div class="title">Foto {{ $idx }}</div>
                                    <div class="subtitle" id="fn_peruntukan_{{ $idx }}" style="{{ $fVal ? 'color:#27ae60;font-weight:700;' : '' }}">{{ $fVal ? 'Terunggah & Tersimpan' : 'Pilih gambar' }}</div>
                                    @if($fVal)
                                        <div class="checklist-foto-actions">
                                            <a href="{{ asset($fVal) }}" target="_blank" class="btn-view-photo" onclick="event.stopPropagation();">
                                                <i class="fas fa-external-link-alt" style="font-size:10px;"></i> Buka Foto
                                            </a>
                                            <button type="button" class="btn-delete-photo-inline" title="Hapus / Ganti Foto" onclick="resetPhotoBox(this, event, 'foto_peruntukan_{{ $idx }}')">
                                                <i class="fas fa-trash-alt" style="font-size:10px;"></i> Hapus
                                            </button>
                                        </div>
                                    @endif
                                    <input type="file" name="foto_peruntukan_{{ $idx }}" accept="image/*" onchange="uploadSinglePhoto(this,'fn_peruntukan_{{ $idx }}')" style="{{ $fVal ? 'display:none;' : '' }}" />
                                </div>
                            @endforeach
                        </div>
                        <div class="form-group" style="margin-top:12px;">
                            <label style="font-size:12px;color:var(--text-muted);"><i class="fas fa-pen"></i> Catatan item ini (opsional)</label>
                            <input type="text" name="catatan_peruntukan" value="{{ old('catatan_peruntukan', $existingSurvey ? $existingSurvey->catatan_peruntukan : '') }}" placeholder="Keterangan tambahan..." />
                        </div>
                    </div>

                    {{-- ITEM 2c: Tata Bangunan --}}
                    @php $vTata = old('item_tata', $existingSurvey ? $existingSurvey->item_tata : null); @endphp
                    <div class="checklist-item-card" id="item-tata">
                        <div class="checklist-item-header">
                            <div class="checklist-item-label">
                                <span class="checklist-nomor">2c</span>
                                <div>
                                    <div class="checklist-title">Tata Bangunan</div>
                                    <div class="checklist-desc">Kesesuaian tata letak, garis sempadan, dan ketinggian bangunan dengan ketentuan</div>
                                </div>
                            </div>
                            <div class="checklist-toggle-group">
                                <label class="checklist-radio-label sesuai {{ $vTata === 'Sesuai' ? 'active' : '' }}">
                                    <input type="radio" name="item_tata" value="Sesuai" {{ $vTata === 'Sesuai' ? 'checked' : '' }} required />
                                    <i class="fas fa-check-circle"></i> Sesuai
                                </label>
                                <label class="checklist-radio-label tidak {{ $vTata === 'Tidak Sesuai' ? 'active' : '' }}">
                                    <input type="radio" name="item_tata" value="Tidak Sesuai" {{ $vTata === 'Tidak Sesuai' ? 'checked' : '' }} />
                                    <i class="fas fa-times-circle"></i> Tidak Sesuai
                                </label>
                            </div>
                        </div>
                        <div class="checklist-foto-row">
                            @foreach([1, 2, 3] as $idx)
                                @php $fKey = 'foto_tata_' . $idx; $fVal = $existingSurvey ? $existingSurvey->$fKey : null; @endphp
                                <div class="checklist-foto-box {{ $fVal ? 'has-file' : '' }}">
                                    <i class="{{ $fVal ? 'fas fa-circle-check' : 'fas fa-camera' }}" style="{{ $fVal ? 'color:#27ae60;' : '' }}"></i>
                                    <div class="title">Foto {{ $idx }}</div>
                                    <div class="subtitle" id="fn_tata_{{ $idx }}" style="{{ $fVal ? 'color:#27ae60;font-weight:700;' : '' }}">{{ $fVal ? 'Terunggah & Tersimpan' : 'Pilih gambar' }}</div>
                                    @if($fVal)
                                        <div class="checklist-foto-actions">
                                            <a href="{{ asset($fVal) }}" target="_blank" class="btn-view-photo" onclick="event.stopPropagation();">
                                                <i class="fas fa-external-link-alt" style="font-size:10px;"></i> Buka Foto
                                            </a>
                                            <button type="button" class="btn-delete-photo-inline" title="Hapus / Ganti Foto" onclick="resetPhotoBox(this, event, 'foto_tata_{{ $idx }}')">
                                                <i class="fas fa-trash-alt" style="font-size:10px;"></i> Hapus
                                            </button>
                                        </div>
                                    @endif
                                    <input type="file" name="foto_tata_{{ $idx }}" accept="image/*" onchange="uploadSinglePhoto(this,'fn_tata_{{ $idx }}')" style="{{ $fVal ? 'display:none;' : '' }}" />
                                </div>
                            @endforeach
                        </div>
                        <div class="form-group" style="margin-top:12px;">
                            <label style="font-size:12px;color:var(--text-muted);"><i class="fas fa-pen"></i> Catatan item ini (opsional)</label>
                            <input type="text" name="catatan_tata" value="{{ old('catatan_tata', $existingSurvey ? $existingSurvey->catatan_tata : '') }}" placeholder="Keterangan tambahan..." />
                        </div>
                    </div>

                    {{-- ITEM 2d: Kelaikan Fungsi Bangunan --}}
                    @php $vKelaikan = old('item_kelaikan', $existingSurvey ? $existingSurvey->item_kelaikan : null); @endphp
                    <div class="checklist-item-card" id="item-kelaikan">
                        <div class="checklist-item-header">
                            <div class="checklist-item-label">
                                <span class="checklist-nomor">2d</span>
                                <div>
                                    <div class="checklist-title">Kelaikan Fungsi Bangunan</div>
                                    <div class="checklist-desc">Kondisi fisik bangunan memenuhi standar kelaikan fungsi untuk diterbitkan SLF</div>
                                </div>
                            </div>
                            <div class="checklist-toggle-group">
                                <label class="checklist-radio-label sesuai {{ $vKelaikan === 'Sesuai' ? 'active' : '' }}">
                                    <input type="radio" name="item_kelaikan" value="Sesuai" {{ $vKelaikan === 'Sesuai' ? 'checked' : '' }} required />
                                    <i class="fas fa-check-circle"></i> Sesuai
                                </label>
                                <label class="checklist-radio-label tidak {{ $vKelaikan === 'Tidak Sesuai' ? 'active' : '' }}">
                                    <input type="radio" name="item_kelaikan" value="Tidak Sesuai" {{ $vKelaikan === 'Tidak Sesuai' ? 'checked' : '' }} />
                                    <i class="fas fa-times-circle"></i> Tidak Sesuai
                                </label>
                            </div>
                        </div>
                        <div class="checklist-foto-row">
                            @foreach([1, 2, 3] as $idx)
                                @php $fKey = 'foto_kelaikan_' . $idx; $fVal = $existingSurvey ? $existingSurvey->$fKey : null; @endphp
                                <div class="checklist-foto-box {{ $fVal ? 'has-file' : '' }}">
                                    <i class="{{ $fVal ? 'fas fa-circle-check' : 'fas fa-camera' }}" style="{{ $fVal ? 'color:#27ae60;' : '' }}"></i>
                                    <div class="title">Foto {{ $idx }}</div>
                                    <div class="subtitle" id="fn_kelaikan_{{ $idx }}" style="{{ $fVal ? 'color:#27ae60;font-weight:700;' : '' }}">{{ $fVal ? 'Terunggah & Tersimpan' : 'Pilih gambar' }}</div>
                                    @if($fVal)
                                        <div class="checklist-foto-actions">
                                            <a href="{{ asset($fVal) }}" target="_blank" class="btn-view-photo" onclick="event.stopPropagation();">
                                                <i class="fas fa-external-link-alt" style="font-size:10px;"></i> Buka Foto
                                            </a>
                                            <button type="button" class="btn-delete-photo-inline" title="Hapus / Ganti Foto" onclick="resetPhotoBox(this, event, 'foto_kelaikan_{{ $idx }}')">
                                                <i class="fas fa-trash-alt" style="font-size:10px;"></i> Hapus
                                            </button>
                                        </div>
                                    @endif
                                    <input type="file" name="foto_kelaikan_{{ $idx }}" accept="image/*" onchange="uploadSinglePhoto(this,'fn_kelaikan_{{ $idx }}')" style="{{ $fVal ? 'display:none;' : '' }}" />
                                </div>
                            @endforeach
                        </div>
                        <div class="form-group" style="margin-top:12px;">
                            <label style="font-size:12px;color:var(--text-muted);"><i class="fas fa-pen"></i> Catatan item ini (opsional)</label>
                            <input type="text" name="catatan_kelaikan" value="{{ old('catatan_kelaikan', $existingSurvey ? $existingSurvey->catatan_kelaikan : '') }}" placeholder="Keterangan tambahan..." />
                        </div>
                    </div>

                    {{-- Catatan Umum & Data Sempadan --}}
                    <div style="margin-top:8px;padding-top:20px;border-top:1px solid rgba(0,40,85,0.08);">
                        <div class="form-grid-3" style="margin-bottom:16px;">
                            <div class="form-group">
                                <label><i class="fas fa-ruler-horizontal"></i> Garis Sempadan Tritis / GR (m)</label>
                                <input type="number" name="garis_sempadan_tritis" step="0.1" value="{{ old('garis_sempadan_tritis', $existingSurvey ? $existingSurvey->garis_sempadan_tritis : '') }}" placeholder="Contoh: 6" />
                            </div>
                            <div class="form-group">
                                <label><i class="fas fa-ruler"></i> Jarak dari AS Jalan (m)</label>
                                <input type="number" name="jarak_as_jalan" step="0.1" value="{{ old('jarak_as_jalan', $existingSurvey ? $existingSurvey->jarak_as_jalan : '') }}" placeholder="Contoh: 6" />
                            </div>
                            <div class="form-group">
                                <label><i class="fas fa-triangle-exclamation"></i> Pelanggaran Sempadan (m)</label>
                                <input type="number" name="pelanggaran_sempadan" step="0.1" value="{{ old('pelanggaran_sempadan', $existingSurvey ? $existingSurvey->pelanggaran_sempadan : '') }}" placeholder="0 jika tidak melanggar" />
                            </div>
                        </div>
                        <div class="form-group">
                            <label><i class="fas fa-align-left"></i> Catatan & Rekomendasi Umum Hasil Survei</label>
                            <textarea name="catatan_survei" rows="4" placeholder="Tuliskan kesimpulan umum, temuan lapangan, dan rekomendasi perbaikan...">{{ old('catatan_survei', $existingSurvey ? $existingSurvey->catatan_survei : '') }}</textarea>
                        </div>
                    </div>

                    {{-- FOTO BANGUNAN & AKSES LOKASI --}}
                    <div style="margin-top:24px;padding:20px;background:rgba(0,40,85,0.02);border:1px solid rgba(0,40,85,0.08);border-radius:var(--radius-sm);">
                        <div style="display:flex;align-items:center;gap:10px;margin-bottom:18px;">
                            <div style="width:36px;height:36px;border-radius:50%;background:rgba(0,40,85,0.1);color:var(--primary);display:flex;align-items:center;justify-content:center;font-size:16px;flex-shrink:0;">
                                <i class="fas fa-camera-retro"></i>
                            </div>
                            <div>
                                <div style="font-size:14px;font-weight:800;color:var(--primary-dark);">Bagian 6 — Foto Bangunan & Akses Lokasi</div>
                                <div style="font-size:12px;color:var(--text-muted);">Dokumentasi visual kondisi bangunan dan akses lokasi lapangan survei</div>
                            </div>
                        </div>

                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                            {{-- FOTO TAMPAK DEPAN BANGUNAN & LAPANGAN --}}
                            @php
                                $fBangunan = $existingSurvey?->foto_bangunan ?? null;
                            @endphp
                            <div>
                                <label style="display:block;font-size:13px;font-weight:700;color:var(--text-secondary);margin-bottom:8px;">
                                    <i class="fas fa-building" style="color:var(--primary);"></i> Foto Tampak Depan Bangunan & Lapangan
                                </label>
                                <div class="checklist-foto-box" style="height:180px;display:flex;flex-direction:column;align-items:center;justify-content:center;{{ $fBangunan ? 'border-color:#27ae60;background:rgba(39,174,96,0.04);' : '' }}" onclick="if(!{{ $fBangunan ? 'true' : 'false' }}) this.querySelector('input[type=file]').click()">
                                    @if($fBangunan)
                                        <img src="{{ asset($fBangunan) }}" alt="Foto Bangunan" style="width:100%;height:100%;object-fit:cover;border-radius:var(--radius-sm);">
                                        <div class="checklist-foto-actions" style="position:absolute;bottom:8px;right:8px;display:flex;gap:6px;">
                                            <a href="{{ asset($fBangunan) }}" target="_blank" class="btn-view-photo" onclick="event.stopPropagation();"><i class="fas fa-external-link-alt" style="font-size:10px;"></i> Buka</a>
                                            <button type="button" class="btn-delete-photo-inline" onclick="resetPhotoBox(this, event, 'foto_bangunan')"><i class="fas fa-trash-alt" style="font-size:10px;"></i> Hapus</button>
                                        </div>
                                    @else
                                        <i class="fas fa-image" style="font-size:32px;color:var(--text-muted);opacity:0.5;margin-bottom:10px;"></i>
                                        <div style="font-size:13px;font-weight:600;color:var(--text-muted);" id="fn_bangunan">Klik untuk memilih foto</div>
                                        <div style="font-size:11px;color:var(--text-muted);margin-top:4px;">JPG, PNG (maks. 10 MB)</div>
                                    @endif
                                    <input type="file" name="foto_bangunan" accept="image/*" style="{{ $fBangunan ? 'display:none;' : 'position:absolute;inset:0;opacity:0;cursor:pointer;' }}" onchange="uploadSinglePhoto(this,'fn_bangunan')">
                                </div>
                            </div>

                            {{-- FOTO AKSES JALAN & DRAINASE LOKASI --}}
                            @php
                                $fAkses = $existingSurvey?->foto_akses ?? null;
                            @endphp
                            <div>
                                <label style="display:block;font-size:13px;font-weight:700;color:var(--text-secondary);margin-bottom:8px;">
                                    <i class="fas fa-road" style="color:var(--primary);"></i> Foto Akses Jalan & Drainase Lokasi
                                </label>
                                <div class="checklist-foto-box" style="height:180px;display:flex;flex-direction:column;align-items:center;justify-content:center;{{ $fAkses ? 'border-color:#27ae60;background:rgba(39,174,96,0.04);' : '' }}" onclick="if(!{{ $fAkses ? 'true' : 'false' }}) this.querySelector('input[type=file]').click()">
                                    @if($fAkses)
                                        <img src="{{ asset($fAkses) }}" alt="Foto Akses" style="width:100%;height:100%;object-fit:cover;border-radius:var(--radius-sm);">
                                        <div class="checklist-foto-actions" style="position:absolute;bottom:8px;right:8px;display:flex;gap:6px;">
                                            <a href="{{ asset($fAkses) }}" target="_blank" class="btn-view-photo" onclick="event.stopPropagation();"><i class="fas fa-external-link-alt" style="font-size:10px;"></i> Buka</a>
                                            <button type="button" class="btn-delete-photo-inline" onclick="resetPhotoBox(this, event, 'foto_akses')"><i class="fas fa-trash-alt" style="font-size:10px;"></i> Hapus</button>
                                        </div>
                                    @else
                                        <i class="fas fa-image" style="font-size:32px;color:var(--text-muted);opacity:0.5;margin-bottom:10px;"></i>
                                        <div style="font-size:13px;font-weight:600;color:var(--text-muted);" id="fn_akses">Klik untuk memilih foto</div>
                                        <div style="font-size:11px;color:var(--text-muted);margin-top:4px;">JPG, PNG (maks. 10 MB)</div>
                                    @endif
                                    <input type="file" name="foto_akses" accept="image/*" style="{{ $fAkses ? 'display:none;' : 'position:absolute;inset:0;opacity:0;cursor:pointer;' }}" onchange="uploadSinglePhoto(this,'fn_akses')">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            <!-- Submit Action Bar -->
            <div style="display:flex;justify-content:flex-end;align-items:center;gap:12px;margin-bottom:40px;flex-wrap:wrap;">
                @if($bapKegiatanId)
                    <a href="{{ url('/cetak-bap/' . $bapKegiatanId) }}" target="_blank" class="btn" style="padding:12px 24px;border-radius:var(--radius-sm);font-weight:700;background:#27ae60;color:#fff;text-decoration:none;display:inline-flex;align-items:center;gap:8px;box-shadow:0 4px 12px rgba(39,174,96,0.25);">
                        <i class="fas fa-print"></i> CETAK BAP (BERITA ACARA)
                    </a>
                @endif
                <button type="reset" class="btn btn-outline" style="padding:12px 28px;border-radius:var(--radius-sm);font-weight:700;">
                    <i class="fas fa-redo"></i> Reset Form
                </button>
                <button type="submit" class="btn btn-primary" style="padding:12px 32px;border-radius:var(--radius-sm);font-weight:700;background:var(--primary);color:#fff;">
                    <i class="fas fa-paper-plane"></i> KIRIM DATA SURVEI
                </button>
            </div>
        </form>

        <!-- PUPR Custom Modal Konfirmasi Hapus Foto -->
        <div class="modal-overlay" id="modalKonfirmasiHapusFoto">
            <div class="modal-box" style="max-width: 440px;">
                <div class="modal-header" style="background: rgba(231, 76, 60, 0.05); border-bottom: 1px solid rgba(231, 76, 60, 0.15);">
                    <h3 style="color: #e74c3c;">
                        <i class="fas fa-triangle-exclamation"></i> Konfirmasi Hapus Foto
                    </h3>
                    <button type="button" class="close-btn" onclick="window.PuprModal.close('modalKonfirmasiHapusFoto')">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="modal-body" style="text-align: center; padding: 28px 24px;">
                    <div style="width: 60px; height: 60px; border-radius: 50%; background: rgba(231, 76, 60, 0.1); color: #e74c3c; display: flex; align-items: center; justify-content: center; font-size: 24px; margin: 0 auto 16px auto;">
                        <i class="fas fa-trash-alt"></i>
                    </div>
                    <h4 style="margin: 0 0 8px 0; font-size: 16px; font-weight: 800; color: var(--text-primary);">Hapus Foto Terunggah?</h4>
                    <p style="margin: 0; font-size: 13px; color: var(--text-muted); line-height: 1.5;">
                        Apakah Anda yakin ingin menghapus foto yang sudah terunggah ini? Setelah dihapus, Anda dapat memilih foto pengganti.
                    </p>
                </div>
                <div class="modal-footer" style="justify-content: center; background: var(--bg-card); gap: 10px;">
                    <button type="button" class="btn btn-cancel" onclick="window.PuprModal.close('modalKonfirmasiHapusFoto')">
                        <i class="fas fa-times"></i> Batal
                    </button>
                    <button type="button" class="btn" id="btnConfirmDeleteFoto" style="background: #e74c3c; color: #fff; border: none;">
                        <i class="fas fa-trash-alt"></i> Ya, Hapus Foto
                    </button>
                </div>
            </div>
        </div>
    </main>
@endsection

@push('scripts')
<script>
    let targetDeleteBtn = null;

    function loadKegiatanSurvey(id) {
        if (@json(Auth::check() && Auth::user()->isPetugas())) {
            alert('Petugas tidak dapat mengganti kegiatan secara bebas di halaman ini. Silakan kembali ke menu Belum Survei atau Sudah Survei untuk memilih kegiatan.');
            return;
        }
        if (id) {
            window.location.href = '{{ url("/survey") }}?kegiatan_id=' + id;
        } else {
            window.location.href = '{{ url("/survey") }}';
        }
    }

    function triggerKegiatanChange(nama, pemohon, nik, alamat, lokasi, p1, p2) {
        document.getElementById('inputNamaKegiatanSurvey').value = nama;
        
        const inputPetugas1 = document.querySelector('input[name="nama_petugas_1"]');
        if (inputPetugas1 && p1) inputPetugas1.value = p1;

        const inputPetugas2 = document.querySelector('input[name="nama_petugas_2"]');
        if (inputPetugas2) inputPetugas2.value = p2;

        const inputPemohon = document.querySelector('input[name="nama_pemohon"]');
        if (inputPemohon && pemohon) inputPemohon.value = pemohon;

        const inputNik = document.querySelector('input[name="nik_pemohon"]');
        if (inputNik) inputNik.value = nik;

        const inputAlamatPemohon = document.querySelector('input[name="alamat_pemohon"]');
        if (inputAlamatPemohon && alamat) inputAlamatPemohon.value = alamat;

        const inputAlamatLokasi = document.querySelector('input[name="alamat_lokasi"]');
        if (inputAlamatLokasi && alamat) inputAlamatLokasi.value = alamat;

        if (lokasi) {
            const inputKecHidden = document.getElementById('kecamatan_hidden');
            if (inputKecHidden) {
                inputKecHidden.value = lokasi;
                const wrapper = inputKecHidden.closest('.pupr-dropdown-wrapper');
                if (wrapper) {
                    const label = wrapper.querySelector('.selected-label');
                    if (label) label.textContent = lokasi;
                }
            }
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.checklist-radio-label input[type="radio"]').forEach(radio => {
            radio.addEventListener('change', function() {
                const groupName = this.name;
                document.querySelectorAll(`input[name="${groupName}"]`).forEach(r => {
                    r.closest('.checklist-radio-label').classList.remove('active');
                });
                if (this.checked) {
                    this.closest('.checklist-radio-label').classList.add('active');
                }
            });
        });

        const btnConfirmDel = document.getElementById('btnConfirmDeleteFoto');
        if (btnConfirmDel) {
            btnConfirmDel.addEventListener('click', function() {
                if (targetDeleteBtn) {
                    doResetPhoto(targetDeleteBtn);
                    targetDeleteBtn = null;
                }
                window.PuprModal.close('modalKonfirmasiHapusFoto');
            });
        }
    });

    function doResetPhoto(btn) {
        const box = btn.closest('.checklist-foto-box');
        box.classList.remove('has-file');
        box.style.borderColor = '';
        box.style.background = '';

        // Hapus gambar preview jika ada (kasus foto_bangunan / foto_akses)
        const imgPreview = box.querySelector('img');
        if (imgPreview) imgPreview.remove();

        // Hapus icon lama, tambahkan icon placeholder baru jika tidak ada
        let icon = box.querySelector('i:not(.fa-external-link-alt):not(.fa-trash-alt)');
        if (icon) {
            icon.className = 'fas fa-camera';
            icon.style.color = 'var(--primary)';
            icon.style.opacity = '';
            icon.style.fontSize = '';
            icon.style.marginBottom = '';
        } else {
            const newIcon = document.createElement('i');
            newIcon.className = 'fas fa-image';
            newIcon.style.cssText = 'font-size:32px;color:var(--text-muted);opacity:0.5;margin-bottom:10px;';
            box.prepend(newIcon);
        }

        // Update subtitle (class .subtitle atau [id^="fn_"])
        let subtitle = box.querySelector('.subtitle');
        if (!subtitle) subtitle = box.querySelector('[id^="fn_"]');
        if (subtitle) {
            subtitle.textContent = 'Klik untuk memilih foto';
            subtitle.style.color = '';
            subtitle.style.fontWeight = 'normal';
        }

        const actions = box.querySelector('.checklist-foto-actions');
        if (actions) actions.remove();

        const input = box.querySelector('input[type="file"]');
        if (input) {
            input.value = '';
            input.style.cssText = 'position:absolute;inset:0;opacity:0;cursor:pointer;';
        }

        // Restore onclick agar klik box buka file picker
        box.onclick = function() { box.querySelector('input[type="file"]')?.click(); };
    }

    function resetPhotoBox(btn, e, fieldName) {
        e.stopPropagation();
        targetDeleteBtn = btn;
        if (window.PuprModal) {
            window.PuprModal.open('modalKonfirmasiHapusFoto');
        } else {
            if (confirm('Apakah Anda yakin ingin menghapus / mengganti foto ini?')) {
                doResetPhoto(btn);
            }
        }
    }

    function uploadSinglePhoto(input, subtitleId) {
        if (!input.files || !input.files[0]) return;

        const file = input.files[0];
        const box = input.closest('.checklist-foto-box');
        let icon = box.querySelector('i:not(.fa-external-link-alt):not(.fa-trash-alt)');
        const subtitle = document.getElementById(subtitleId);
        const fieldName = input.name;
        const kegiatanId = document.getElementById('data_mingguan_id_hidden')?.value || '';

        // UI Loading State saat mengunggah foto
        if (icon) {
            icon.className = 'fas fa-spinner fa-spin';
            icon.style.color = 'var(--primary)';
        }
        subtitle.textContent = 'Mengunggah & Menyimpan...';
        subtitle.style.color = 'var(--primary)';
        subtitle.style.fontWeight = '600';

        const formData = new FormData();
        formData.append('photo', file);
        formData.append('field_name', fieldName);
        formData.append('data_mingguan_id', kegiatanId);
        formData.append('_token', '{{ csrf_token() }}');

        fetch('{{ route("survey.upload-photo") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                box.classList.add('has-file');

                // Sembunyikan input file agar tidak terklik secara tidak sengaja
                input.style.display = 'none';

                if (icon) {
                    icon.className = 'fas fa-circle-check';
                    icon.style.color = '#27ae60';
                }
                subtitle.textContent = 'Terunggah & Tersimpan';
                subtitle.style.color = '#27ae60';
                subtitle.style.fontWeight = '700';

                // Buat / Update Container Tombol Aksi Samping-Sampingan (Buka Foto & Hapus)
                let actions = box.querySelector('.checklist-foto-actions');
                if (!actions) {
                    actions = document.createElement('div');
                    actions.className = 'checklist-foto-actions';
                    box.appendChild(actions);
                }
                actions.innerHTML = `
                    <a href="${data.file_url}" target="_blank" class="btn-view-photo" onclick="event.stopPropagation();">
                        <i class="fas fa-external-link-alt" style="font-size:10px;"></i> Buka Foto
                    </a>
                    <button type="button" class="btn-delete-photo-inline" title="Hapus / Ganti Foto" onclick="resetPhotoBox(this, event, '${input.name}')">
                        <i class="fas fa-trash-alt" style="font-size:10px;"></i> Hapus
                    </button>
                `;
            } else {
                if (icon) icon.className = 'fas fa-camera';
                subtitle.textContent = 'Gagal unggah. Coba lagi.';
                subtitle.style.color = 'var(--danger)';
            }
        })
        .catch(err => {
            console.error(err);
            if (icon) icon.className = 'fas fa-camera';
            subtitle.textContent = 'Gagal unggah. Coba lagi.';
            subtitle.style.color = 'var(--danger)';
        });
    }

    // Auto GPS geolocation trigger
    const autoGpsBtn = document.getElementById('btnAutoGps');
    if (autoGpsBtn) {
        autoGpsBtn.addEventListener('click', function() {
            if (navigator.geolocation) {
                this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memproses...';
                navigator.geolocation.getCurrentPosition(
                    (pos) => {
                        document.getElementById('gpsLat').value = pos.coords.latitude.toFixed(6);
                        document.getElementById('gpsLng').value = pos.coords.longitude.toFixed(6);
                        this.innerHTML = '<i class="fas fa-check"></i> GPS Terdeteksi';
                        setTimeout(() => { this.innerHTML = '<i class="fas fa-crosshairs"></i> Deteksi GPS'; }, 2000);
                    },
                    () => {
                        alert('Tidak dapat mengoperasikan GPS. Koordinat standar Jember telah diterapkan.');
                        this.innerHTML = '<i class="fas fa-crosshairs"></i> Deteksi GPS';
                    }
                );
            } else {
                alert('Browser Anda tidak mendukung fitur Geolocation GPS.');
            }
        });
    }

    // Submit handler alert confirmation
    document.getElementById('formSurveyUtama').addEventListener('submit', function(e) {
        alert('Data survei lapangan berhasil terverifikasi dan disimpan di sistem!');
    });
</script>
@endpush
