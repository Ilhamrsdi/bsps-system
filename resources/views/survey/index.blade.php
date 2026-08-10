@extends('layouts.partial.app')

@section('title', 'BSPS Verval - Form Survei Lapangan')
@section('title_header', 'Form Survei Lapangan BSPS')
@section('subtitle_header', 'Verifikasi Dokumen & Pengambilan Foto Fisik RTLH Calon Penerima Bantuan')

@push('styles')
    <style>
        .survey-container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .form-section {
            background: var(--bg-card);
            border-radius: var(--radius);
            padding: 26px 28px;
            box-shadow: var(--shadow-sm);
            border: 1px solid rgba(0, 40, 85, 0.06);
            margin-bottom: 24px;
        }

        .form-section h4 {
            margin-top: 0;
            color: var(--primary);
            font-weight: 800;
            font-size: 16px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            border-bottom: 1px solid rgba(0, 40, 85, 0.06);
            padding-bottom: 12px;
        }

        .form-group {
            margin-bottom: 16px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 700;
            font-size: 13px;
            color: var(--text-primary);
        }

        .form-control {
            width: 100%;
            padding: 10px 14px;
            border: 1px solid rgba(0, 40, 85, 0.15);
            border-radius: var(--radius-sm);
            background: var(--bg-body);
            color: var(--text-primary);
            font-size: 13.5px;
            box-sizing: border-box;
            outline: none;
            transition: var(--transition);
        }

        .form-control:focus {
            border-color: var(--primary);
            background: var(--bg-card);
            box-shadow: 0 0 0 3px rgba(0, 40, 85, 0.08);
        }

        .form-control:disabled {
            background: #f4f6f8;
            color: #6b7280;
            cursor: not-allowed;
            border-color: rgba(0, 40, 85, 0.08);
            font-weight: 600;
        }

        /* Recipient Quick Header Bar */
        .recipient-selector-bar {
            background: linear-gradient(135deg, #001e40 0%, #002855 60%, #004080 100%);
            color: #fff;
            border-radius: var(--radius);
            padding: 24px 28px;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            flex-wrap: wrap;
            box-shadow: 0 8px 24px rgba(0, 40, 85, 0.18);
            border-left: 6px solid var(--secondary);
        }

        .recipient-selector-bar h3 {
            margin: 0 0 6px 0;
            font-size: 20px;
            font-weight: 900;
            display: flex;
            align-items: center;
            gap: 10px;
            color: #ffffff;
        }

        .recipient-selector-bar p {
            margin: 0;
            font-size: 13.5px;
            opacity: 0.9;
            color: rgba(255, 255, 255, 0.9);
        }

        /* Camera Upload Cards */
        .camera-upload-card {
            background: var(--bg-body);
            border: 2px dashed rgba(0, 40, 85, 0.18);
            border-radius: 12px;
            padding: 18px;
            text-align: center;
            position: relative;
            transition: all 0.25s ease;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 220px;
            cursor: pointer;
            overflow: hidden;
        }

        .camera-upload-card:hover {
            border-color: var(--primary);
            background: rgba(0, 40, 85, 0.02);
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(0, 40, 85, 0.08);
        }

        .camera-upload-card.has-image {
            border-style: solid;
            border-color: rgba(39, 174, 96, 0.35);
            background: #fff;
        }

        .camera-icon-bubble {
            width: 54px;
            height: 54px;
            border-radius: 50%;
            background: rgba(0, 40, 85, 0.08);
            color: var(--primary);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            margin-bottom: 12px;
            transition: all 0.2s ease;
        }

        .camera-upload-card:hover .camera-icon-bubble {
            background: var(--primary);
            color: #fff;
            transform: scale(1.08);
        }

        .camera-upload-title {
            font-weight: 800;
            font-size: 14px;
            color: var(--primary-dark);
            margin-bottom: 4px;
        }

        .camera-upload-sub {
            font-size: 12px;
            color: var(--text-muted);
            margin-bottom: 12px;
        }

        .camera-file-input {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
            cursor: pointer;
            z-index: 10;
        }

        .camera-preview-img {
            width: 100%;
            height: 140px;
            object-fit: cover;
            border-radius: 8px;
            margin-bottom: 10px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .camera-upload-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 11.5px;
            font-weight: 700;
            background: rgba(39, 174, 96, 0.12);
            color: var(--success);
        }

        .camera-upload-btn-fake {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 14px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 700;
            background: rgba(0, 40, 85, 0.08);
            color: var(--primary);
            transition: all 0.2s;
        }

        .camera-upload-card:hover .camera-upload-btn-fake {
            background: var(--primary);
            color: #fff;
        }

        /* Indicator Radio Pills */
        .pill-indicator {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            cursor: pointer;
            font-size: 13px;
            font-weight: 700;
            padding: 7px 18px;
            border-radius: 20px;
            transition: all 0.2s ease;
            user-select: none;
        }

        .pill-indicator.pill-ada {
            background: rgba(34, 197, 94, 0.08);
            color: #15803d;
            border: 1px solid rgba(34, 197, 94, 0.3);
        }

        .pill-indicator.pill-ada:hover {
            background: rgba(34, 197, 94, 0.22);
            border-color: #16a34a;
            color: #14532d;
            box-shadow: 0 2px 8px rgba(34, 197, 94, 0.2);
            transform: translateY(-1px);
        }

        .pill-indicator.pill-ada:has(input:checked) {
            background: #dcfce7 !important;
            color: #15803d !important;
            border-color: #22c55e !important;
            box-shadow: 0 0 0 2px rgba(34, 197, 94, 0.25);
        }

        .pill-indicator.pill-tidak-ada {
            background: rgba(239, 68, 68, 0.08);
            color: #b91c1c;
            border: 1px solid rgba(239, 68, 68, 0.3);
        }

        .pill-indicator.pill-tidak-ada:hover {
            background: rgba(239, 68, 68, 0.22);
            border-color: #ef4444;
            color: #7f1d1d;
            box-shadow: 0 2px 8px rgba(239, 68, 68, 0.2);
            transform: translateY(-1px);
        }

        .pill-indicator.pill-tidak-ada:has(input:checked) {
            background: #fee2e2 !important;
            color: #b91c1c !important;
            border-color: #ef4444 !important;
            box-shadow: 0 0 0 2px rgba(239, 68, 68, 0.25);
        }

        @media (max-width: 768px) {
            .recipient-selector-bar { padding: 18px; }
            .form-section { padding: 18px 16px; }
        }
    </style>
@endpush

@section('content')
    <!-- Dedicated Public Navbar Component (Sama Seperti Landing Page) -->
    @include('layouts.navbar_public')

    <main class="dashboard-content dashboard-content-public" style="max-width:1280px; margin:0 auto; padding: 28px 32px 60px; box-sizing:border-box;">
        <div class="survey-container">
            <!-- Breadcrumb -->
            <div class="breadcrumb" style="font-size:13px;color:var(--text-muted);margin-bottom:20px;display:flex;align-items:center;gap:8px;">
                <a href="{{ url('/') }}" style="color:var(--primary);text-decoration:none;font-weight:600;"><i class="fas fa-home"></i> Home</a>
                <i class="fas fa-chevron-right" style="font-size:10px;"></i>
                <a href="{{ route('verval-data') }}" style="color:var(--primary);text-decoration:none;font-weight:600;">Data Verval</a>
                <i class="fas fa-chevron-right" style="font-size:10px;"></i>
                <span>Form Survei &amp; Verifikasi Fisik</span>
            </div>

            <!-- Recipient Quick Header Bar -->
            <div class="recipient-selector-bar">
                <div>
                    <h3>
                        <i class="fas fa-user-check" style="color:var(--secondary);"></i>
                        {{ $vervalData->nama }}
                    </h3>
                    <p>
                        NIK: <strong>{{ $vervalData->no_ktp ?: '-' }}</strong> &bull;
                        No. KK: <strong>{{ $vervalData->no_kk ?: '-' }}</strong> &bull;
                        Desa {{ $vervalData->desa_kelurahan }}, Kec. {{ $vervalData->kecamatan }}
                    </p>
                </div>
                <div style="display:flex;gap:10px;align-items:center;flex-wrap:wrap;">
                    <a href="{{ route('verval-data.surat-pernyataan', $vervalData->id) }}" target="_blank" class="btn" style="background:#ffb800;color:#002855;font-weight:800;font-size:13px;padding:10px 18px;border-radius:var(--radius-sm);text-decoration:none;display:inline-flex;align-items:center;gap:8px;box-shadow:0 4px 12px rgba(255,184,0,0.3);">
                        <i class="fas fa-file-signature"></i> Cetak Surat Pernyataan
                    </a>
                    <a href="{{ route('verval-data') }}" class="btn btn-outline" style="background:rgba(255,255,255,0.15);color:#fff;border:1px solid rgba(255,255,255,0.3);font-size:13px;font-weight:600;padding:10px 18px;border-radius:var(--radius-sm);text-decoration:none;">
                        <i class="fas fa-arrow-left"></i> Kembali ke Daftar
                    </a>
                </div>
            </div>

            <!-- Display Validation Errors / Success if any -->
            @if ($errors->any())
                <div style="background: #fee2e2; border-left: 4px solid #ef4444; color: #b91c1c; padding: 16px 20px; margin-bottom: 24px; border-radius: var(--radius-sm);">
                    <strong><i class="fas fa-exclamation-circle"></i> Terdapat kesalahan pengisian:</strong>
                    <ul style="margin-top: 8px; margin-bottom: 0; padding-left: 20px;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if(session('success'))
                <div style="background:rgba(39,174,96,0.10);border-left:4px solid var(--success);color:var(--success);padding:16px 20px;margin-bottom:24px;border-radius:var(--radius-sm);display:flex;align-items:center;gap:10px;font-weight:700;">
                    <i class="fas fa-check-circle" style="font-size:20px;"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            <!-- 1. Identitas Kependudukan Calon Penerima -->
            <div class="form-section">
                <h4><i class="fas fa-id-card"></i> 1. Identitas Kependudukan Calon Penerima (Dari Database Verval)</h4>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 16px;">
                    <div class="form-group">
                        <label>Nama Lengkap</label>
                        <input type="text" class="form-control" value="{{ $vervalData->nama }}" disabled>
                    </div>
                    <div class="form-group">
                        <label>Jenis Kelamin</label>
                        <input type="text" class="form-control" value="{{ $vervalData->jenis_kelamin == 'L' ? 'Laki-laki (L)' : ($vervalData->jenis_kelamin == 'P' ? 'Perempuan (P)' : $vervalData->jenis_kelamin) }}" disabled>
                    </div>
                    <div class="form-group">
                        <label>Nomor Induk Kependudukan (NIK)</label>
                        <input type="text" class="form-control" value="{{ $vervalData->no_ktp ?: '-' }}" disabled>
                    </div>
                    <div class="form-group">
                        <label>Nomor Kartu Keluarga (No. KK)</label>
                        <input type="text" class="form-control" value="{{ $vervalData->no_kk ?: '-' }}" disabled>
                    </div>
                    <div class="form-group">
                        <label>Alamat / Dusun</label>
                        <input type="text" class="form-control" value="{{ $vervalData->alamat ?: '-' }}" disabled>
                    </div>
                    <div class="form-group">
                        <label>Desa / Kelurahan</label>
                        <input type="text" class="form-control" value="{{ $vervalData->desa_kelurahan ?: '-' }}" disabled>
                    </div>
                    <div class="form-group">
                        <label>Kecamatan</label>
                        <input type="text" class="form-control" value="{{ $vervalData->kecamatan ?: '-' }}" disabled>
                    </div>
                    <div class="form-group">
                        <label>Kelompok Desil</label>
                        <input type="text" class="form-control" value="{{ $vervalData->pengelompokan_desil ?: 'Desil 1-4' }}" disabled>
                    </div>
                </div>
            </div>

            <!-- Form Utama Input / Edit Survei Lapangan -->
            <form action="{{ route('survey.store', $vervalData->id) }}" method="POST" enctype="multipart/form-data">
                @csrf

                <!-- 2. Data Tambahan & Lahan (Dengan Custom Dropdown PUPR) -->
                <div class="form-section">
                    <h4><i class="fas fa-user-pen"></i> 2. Data Tambahan &amp; Kelaikan Hunian</h4>

                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px;">
                        <div class="form-group">
                            <label>Tempat Lahir</label>
                            <input type="text" name="tempat_lahir" class="form-control"
                                value="{{ old('tempat_lahir', $vervalData->tempat_lahir) }}" placeholder="Contoh: Jember">
                        </div>
                        <div class="form-group">
                            <label>Tanggal Lahir</label>
                            <input type="date" name="tanggal_lahir" class="form-control"
                                value="{{ old('tanggal_lahir', $vervalData->tanggal_lahir) }}">
                        </div>
                        <div class="form-group">
                            <label>Penghasilan Per Bulan</label>
                            <input type="text" name="penghasilan" class="form-control"
                                value="{{ old('penghasilan', $vervalData->penghasilan) }}" placeholder="Contoh: Rp 1.500.000">
                        </div>
                        <div class="form-group">
                            <label>Luas Tanah (m&sup2;)</label>
                            <input type="text" name="luas_tanah" class="form-control"
                                value="{{ old('luas_tanah', $vervalData->luas_tanah) }}" placeholder="Contoh: 72 m2">
                        </div>
                        <div class="form-group">
                            <label>Telah Ditempati Selama</label>
                            <input type="text" name="telah_ditempati_selama" class="form-control"
                                value="{{ old('telah_ditempati_selama', $vervalData->telah_ditempati_selama) }}"
                                placeholder="Contoh: 10 Tahun">
                        </div>

                        {{-- Custom Dropdown PUPR: Status Kepemilikan Tanah --}}
                        <div class="form-group">
                            <label>Status Kepemilikan Tanah</label>
                            @php
                                $curStatusTanah = old('status_tanah', $vervalData->status_tanah);
                            @endphp
                            <div class="pupr-dropdown-wrapper" id="ddStatusTanahWrapper" style="width:100%;">
                                <button type="button" class="pupr-dropdown-toggle" style="width:100%;justify-content:space-between;padding:10px 14px;background:var(--bg-body);border:1px solid rgba(0,40,85,0.15);" onclick="window.PuprDropdown.toggle(document.getElementById('ddStatusTanahWrapper'))">
                                    <span style="display:inline-flex;align-items:center;gap:8px;font-size:13.5px;">
                                        <i class="fas fa-landmark" style="font-size:12px;opacity:0.6;"></i>
                                        <span class="selected-label">{{ $curStatusTanah ?: '-- Pilih Status Tanah --' }}</span>
                                    </span>
                                    <i class="fas fa-chevron-down" style="font-size:10px;opacity:0.5;"></i>
                                </button>
                                <input type="hidden" name="status_tanah" id="inputStatusTanah" value="{{ $curStatusTanah }}" />
                                <div class="pupr-dropdown-menu" style="width:100%;">
                                    <div class="pupr-dropdown-item {{ !$curStatusTanah ? 'active' : '' }}" data-target="inputStatusTanah" data-value="">
                                        <i class="fas fa-minus" style="font-size:11px;opacity:0.4;"></i> -- Pilih Status Tanah --
                                    </div>
                                    <div class="dropdown-divider"></div>
                                    <div class="pupr-dropdown-item {{ $curStatusTanah == 'Milik Sendiri' ? 'active' : '' }}" data-target="inputStatusTanah" data-value="Milik Sendiri">
                                        <i class="fas fa-house-circle-check" style="font-size:12px;color:var(--success);"></i> Milik Sendiri
                                    </div>
                                    <div class="pupr-dropdown-item {{ $curStatusTanah == 'Bukan Milik Sendiri / Menumpang' ? 'active' : '' }}" data-target="inputStatusTanah" data-value="Bukan Milik Sendiri / Menumpang">
                                        <i class="fas fa-handshake" style="font-size:12px;color:var(--warning);"></i> Bukan Milik Sendiri / Menumpang
                                    </div>
                                    <div class="pupr-dropdown-item {{ $curStatusTanah == 'Tanah Kas Desa / Bengkok' ? 'active' : '' }}" data-target="inputStatusTanah" data-value="Tanah Kas Desa / Bengkok">
                                        <i class="fas fa-building-columns" style="font-size:12px;color:var(--info);"></i> Tanah Kas Desa / Bengkok
                                    </div>
                                    <div class="pupr-dropdown-item {{ $curStatusTanah == 'Lainnya' ? 'active' : '' }}" data-target="inputStatusTanah" data-value="Lainnya">
                                        <i class="fas fa-ellipsis" style="font-size:12px;opacity:0.5;"></i> Lainnya
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 3. Dokumen Administrasi & Berkas (Upload Style Kamera + Custom Dropdown) -->
                <div class="form-section">
                    <h4><i class="fas fa-file-invoice"></i> 3. Berkas &amp; Dokumen Administrasi</h4>

                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 20px;">
                        {{-- Upload KTP --}}
                        <div>
                            <label style="font-weight:700;font-size:13px;display:block;margin-bottom:8px;">Foto / Scan KTP</label>
                            <div class="camera-upload-card {{ $vervalData->ktp ? 'has-image' : '' }}" id="card_ktp">
                                <input type="file" name="ktp" class="camera-file-input" accept="image/*" onchange="previewCameraPhoto(this, 'preview_ktp', 'card_ktp')">
                                <img src="{{ $vervalData->ktp ? asset(str_starts_with($vervalData->ktp, 'uploads/') ? $vervalData->ktp : 'uploads/' . $vervalData->ktp) : '' }}" class="camera-preview-img" id="preview_ktp" style="{{ $vervalData->ktp ? '' : 'display:none;' }}" alt="KTP">
                                <div class="camera-icon-bubble" id="icon_ktp" style="{{ $vervalData->ktp ? 'display:none;' : '' }}">
                                    <i class="fas fa-id-card"></i>
                                </div>
                                <div class="camera-upload-title">Unggah Berkas KTP</div>
                                <div class="camera-upload-sub">Klik atau foto langsung via kamera</div>
                                @if($vervalData->ktp)
                                    <span class="camera-upload-badge"><i class="fas fa-check-circle"></i> Berkas Tersimpan</span>
                                @else
                                    <span class="camera-upload-btn-fake"><i class="fas fa-camera"></i> Pilih / Ambil Foto</span>
                                @endif
                            </div>
                        </div>

                        {{-- Upload KK --}}
                        <div>
                            <label style="font-weight:700;font-size:13px;display:block;margin-bottom:8px;">Foto / Scan Kartu Keluarga (KK)</label>
                            <div class="camera-upload-card {{ $vervalData->kk ? 'has-image' : '' }}" id="card_kk">
                                <input type="file" name="kk" class="camera-file-input" accept="image/*" onchange="previewCameraPhoto(this, 'preview_kk', 'card_kk')">
                                <img src="{{ $vervalData->kk ? asset(str_starts_with($vervalData->kk, 'uploads/') ? $vervalData->kk : 'uploads/' . $vervalData->kk) : '' }}" class="camera-preview-img" id="preview_kk" style="{{ $vervalData->kk ? '' : 'display:none;' }}" alt="KK">
                                <div class="camera-icon-bubble" id="icon_kk" style="{{ $vervalData->kk ? 'display:none;' : '' }}">
                                    <i class="fas fa-users-rectangle"></i>
                                </div>
                                <div class="camera-upload-title">Unggah Berkas KK</div>
                                <div class="camera-upload-sub">Klik atau foto langsung via kamera</div>
                                @if($vervalData->kk)
                                    <span class="camera-upload-badge"><i class="fas fa-check-circle"></i> Berkas Tersimpan</span>
                                @else
                                    <span class="camera-upload-btn-fake"><i class="fas fa-camera"></i> Pilih / Ambil Foto</span>
                                @endif
                            </div>
                        </div>

                        {{-- Upload Sertifikat Tanah --}}
                        <div>
                            <label style="font-weight:700;font-size:13px;display:block;margin-bottom:8px;">Foto Sertipikat / Bukti Tanah</label>
                            <div class="camera-upload-card {{ $vervalData->sertifikat_tanah ? 'has-image' : '' }}" id="card_sertifikat">
                                <input type="file" name="sertifikat_tanah" class="camera-file-input" accept="image/*" onchange="previewCameraPhoto(this, 'preview_sertifikat', 'card_sertifikat')">
                                <img src="{{ $vervalData->sertifikat_tanah ? asset(str_starts_with($vervalData->sertifikat_tanah, 'uploads/') ? $vervalData->sertifikat_tanah : 'uploads/' . $vervalData->sertifikat_tanah) : '' }}" class="camera-preview-img" id="preview_sertifikat" style="{{ $vervalData->sertifikat_tanah ? '' : 'display:none;' }}" alt="Sertifikat">
                                <div class="camera-icon-bubble" id="icon_sertifikat" style="{{ $vervalData->sertifikat_tanah ? 'display:none;' : '' }}">
                                    <i class="fas fa-file-contract"></i>
                                </div>
                                <div class="camera-upload-title">Unggah Bukti Tanah</div>
                                <div class="camera-upload-sub">Sertipikat / Surat Keterangan Tanah</div>
                                @if($vervalData->sertifikat_tanah)
                                    <span class="camera-upload-badge"><i class="fas fa-check-circle"></i> Berkas Tersimpan</span>
                                @else
                                    <span class="camera-upload-btn-fake"><i class="fas fa-camera"></i> Pilih / Ambil Foto</span>
                                @endif
                            </div>
                        </div>

                        {{-- Custom Dropdown: Jenis Bukti Kepemilikan Lahan --}}
                        <div class="form-group" style="display:flex;flex-direction:column;justify-content:center;">
                            <label>Jenis Bukti Kepemilikan Lahan</label>
                            @php
                                $curKepemilikan = old('jenis_kepemilikan_lahan', $vervalData->jenis_kepemilikan_lahan);
                            @endphp
                            <div class="pupr-dropdown-wrapper" id="ddKepemilikanWrapper" style="width:100%;">
                                <button type="button" class="pupr-dropdown-toggle" style="width:100%;justify-content:space-between;padding:10px 14px;background:var(--bg-body);border:1px solid rgba(0,40,85,0.15);" onclick="window.PuprDropdown.toggle(document.getElementById('ddKepemilikanWrapper'))">
                                    <span style="display:inline-flex;align-items:center;gap:8px;font-size:13.5px;">
                                        <i class="fas fa-file-lines" style="font-size:12px;opacity:0.6;"></i>
                                        <span class="selected-label">{{ $curKepemilikan ?: '-- Pilih Jenis Bukti --' }}</span>
                                    </span>
                                    <i class="fas fa-chevron-down" style="font-size:10px;opacity:0.5;"></i>
                                </button>
                                <input type="hidden" name="jenis_kepemilikan_lahan" id="inputKepemilikan" value="{{ $curKepemilikan }}" />
                                <div class="pupr-dropdown-menu" style="width:100%;max-height:260px;overflow-y:auto;">
                                    <div class="pupr-dropdown-item {{ !$curKepemilikan ? 'active' : '' }}" data-target="inputKepemilikan" data-value="">
                                        <i class="fas fa-minus" style="font-size:11px;opacity:0.4;"></i> -- Pilih Jenis Bukti --
                                    </div>
                                    <div class="dropdown-divider"></div>
                                    <div class="pupr-dropdown-item {{ $curKepemilikan == 'SHM' ? 'active' : '' }}" data-target="inputKepemilikan" data-value="SHM">
                                        <i class="fas fa-stamp" style="font-size:12px;color:var(--success);"></i> SHM (Sertifikat Hak Milik)
                                    </div>
                                    <div class="pupr-dropdown-item {{ $curKepemilikan == 'SHGB' ? 'active' : '' }}" data-target="inputKepemilikan" data-value="SHGB">
                                        <i class="fas fa-building" style="font-size:12px;color:var(--info);"></i> SHGB (Sertifikat Hak Guna Bangunan)
                                    </div>
                                    <div class="pupr-dropdown-item {{ $curKepemilikan == 'Girik/Letter C' ? 'active' : '' }}" data-target="inputKepemilikan" data-value="Girik/Letter C">
                                        <i class="fas fa-scroll" style="font-size:12px;color:#d69e00;"></i> Girik / Letter C
                                    </div>
                                    <div class="pupr-dropdown-item {{ $curKepemilikan == 'SKT' ? 'active' : '' }}" data-target="inputKepemilikan" data-value="SKT">
                                        <i class="fas fa-file-certificate" style="font-size:12px;color:var(--primary);"></i> SKT (Surat Keterangan Tanah)
                                    </div>
                                    <div class="pupr-dropdown-item {{ $curKepemilikan == 'AJB' ? 'active' : '' }}" data-target="inputKepemilikan" data-value="AJB">
                                        <i class="fas fa-file-signature" style="font-size:12px;color:#8e44ad;"></i> AJB (Akta Jual Beli)
                                    </div>
                                    <div class="pupr-dropdown-item {{ $curKepemilikan == 'Surat Perjanjian/Izin Tinggal' ? 'active' : '' }}" data-target="inputKepemilikan" data-value="Surat Perjanjian/Izin Tinggal">
                                        <i class="fas fa-file-contract" style="font-size:12px;color:var(--warning);"></i> Surat Perjanjian / Izin Tinggal
                                    </div>
                                    <div class="pupr-dropdown-item {{ $curKepemilikan == 'Lainnya' ? 'active' : '' }}" data-target="inputKepemilikan" data-value="Lainnya">
                                        <i class="fas fa-ellipsis" style="font-size:12px;opacity:0.5;"></i> Lainnya
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 4. Indikator Kelayakan Calon Penerima (Status Layak Diusulkan) -->
                <div class="form-section" style="border: 2px solid rgba(0, 40, 85, 0.12); background: #ffffff;">
                    <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px; margin-bottom:16px; border-bottom:1px solid rgba(0, 40, 85, 0.08); padding-bottom:12px;">
                        <h4 style="margin:0; border:none; padding:0;">
                            <i class="fas fa-list-check" style="color:var(--primary);"></i> 4. Indikator Kelayakan RTLH (Kriteria Layak Diusulkan)
                        </h4>
                        <span style="font-size:12.5px; font-weight:700; color:#475569; background:#f1f5f9; padding:6px 12px; border-radius:20px;">
                            <i class="fas fa-circle-info" style="color:var(--primary);"></i> Memenuhi minimal 2 indikator
                        </span>
                    </div>

                    <p style="font-size:13.5px; color:#334155; margin-top:0; margin-bottom:20px; font-weight:600;">
                        Indikator yang digunakan untuk menentukan layak diusulkan (Pilih <strong>Ada</strong> jika indikator terpenuhi atau <strong>Tidak Ada</strong> jika indikator tidak terpenuhi):
                    </p>

                    <div style="display: flex; flex-direction: column; gap: 12px; margin-bottom:24px;">
                        {{-- 1. Lantai Keramik --}}
                        <div class="indicator-row" style="display:flex; align-items:center; justify-content:space-between; background:#f8fafc; border:1px solid #cbd5e1; border-radius:10px; padding:12px 18px; flex-wrap:wrap; gap:12px; transition:all 0.2s;">
                            <div style="font-size:13.5px; font-weight:700; color:#1e293b; flex:1; min-width:240px;">
                                1. Lantai Keramik
                            </div>
                            <div style="display:flex; align-items:center; gap:12px;">
                                <label class="pill-indicator pill-ada">
                                    <input type="radio" name="indikator_lantai" value="ada" class="indikator-radio" {{ old('indikator_lantai', $vervalData->indikator_lantai) == 'ada' || $vervalData->indikator_lantai === null ? 'checked' : '' }} style="accent-color:#16a34a; cursor:pointer;">
                                    <i class="fas fa-check-circle"></i> Ada
                                </label>
                                <label class="pill-indicator pill-tidak-ada">
                                    <input type="radio" name="indikator_lantai" value="tidak_ada" class="indikator-radio" {{ old('indikator_lantai', $vervalData->indikator_lantai) == 'tidak_ada' ? 'checked' : '' }} style="accent-color:#dc2626; cursor:pointer;">
                                    <i class="fas fa-times-circle"></i> Tidak Ada
                                </label>
                            </div>
                        </div>

                        {{-- 2. Pondasi Bangunan --}}
                        <div class="indicator-row" style="display:flex; align-items:center; justify-content:space-between; background:#f8fafc; border:1px solid #cbd5e1; border-radius:10px; padding:12px 18px; flex-wrap:wrap; gap:12px; transition:all 0.2s;">
                            <div style="font-size:13.5px; font-weight:700; color:#1e293b; flex:1; min-width:240px;">
                                2. Pondasi Bangunan
                            </div>
                            <div style="display:flex; align-items:center; gap:12px;">
                                <label class="pill-indicator pill-ada">
                                    <input type="radio" name="indikator_pondasi" value="ada" class="indikator-radio" {{ old('indikator_pondasi', $vervalData->indikator_pondasi) == 'ada' || $vervalData->indikator_pondasi === null ? 'checked' : '' }} style="accent-color:#16a34a; cursor:pointer;">
                                    <i class="fas fa-check-circle"></i> Ada
                                </label>
                                <label class="pill-indicator pill-tidak-ada">
                                    <input type="radio" name="indikator_pondasi" value="tidak_ada" class="indikator-radio" {{ old('indikator_pondasi', $vervalData->indikator_pondasi) == 'tidak_ada' ? 'checked' : '' }} style="accent-color:#dc2626; cursor:pointer;">
                                    <i class="fas fa-times-circle"></i> Tidak Ada
                                </label>
                            </div>
                        </div>

                        {{-- 3. Dinding Bata / Tembok --}}
                        <div class="indicator-row" style="display:flex; align-items:center; justify-content:space-between; background:#f8fafc; border:1px solid #cbd5e1; border-radius:10px; padding:12px 18px; flex-wrap:wrap; gap:12px; transition:all 0.2s;">
                            <div style="font-size:13.5px; font-weight:700; color:#1e293b; flex:1; min-width:240px;">
                                3. Dinding Bata / Tembok
                            </div>
                            <div style="display:flex; align-items:center; gap:12px;">
                                <label class="pill-indicator pill-ada">
                                    <input type="radio" name="indikator_dinding" value="ada" class="indikator-radio" {{ old('indikator_dinding', $vervalData->indikator_dinding) == 'ada' || $vervalData->indikator_dinding === null ? 'checked' : '' }} style="accent-color:#16a34a; cursor:pointer;">
                                    <i class="fas fa-check-circle"></i> Ada
                                </label>
                                <label class="pill-indicator pill-tidak-ada">
                                    <input type="radio" name="indikator_dinding" value="tidak_ada" class="indikator-radio" {{ old('indikator_dinding', $vervalData->indikator_dinding) == 'tidak_ada' ? 'checked' : '' }} style="accent-color:#dc2626; cursor:pointer;">
                                    <i class="fas fa-times-circle"></i> Tidak Ada
                                </label>
                            </div>
                        </div>

                        {{-- 4. Struktur Bangunan (Sloof, Kolom, Ring Balok) --}}
                        <div class="indicator-row" style="display:flex; align-items:center; justify-content:space-between; background:#f8fafc; border:1px solid #cbd5e1; border-radius:10px; padding:12px 18px; flex-wrap:wrap; gap:12px; transition:all 0.2s;">
                            <div style="font-size:13.5px; font-weight:700; color:#1e293b; flex:1; min-width:240px;">
                                4. Struktur Bangunan (Sloof, Kolom, Ring Balok)
                            </div>
                            <div style="display:flex; align-items:center; gap:12px;">
                                <label class="pill-indicator pill-ada">
                                    <input type="radio" name="indikator_struktur" value="ada" class="indikator-radio" {{ old('indikator_struktur', $vervalData->indikator_struktur) == 'ada' || $vervalData->indikator_struktur === null ? 'checked' : '' }} style="accent-color:#16a34a; cursor:pointer;">
                                    <i class="fas fa-check-circle"></i> Ada
                                </label>
                                <label class="pill-indicator pill-tidak-ada">
                                    <input type="radio" name="indikator_struktur" value="tidak_ada" class="indikator-radio" {{ old('indikator_struktur', $vervalData->indikator_struktur) == 'tidak_ada' ? 'checked' : '' }} style="accent-color:#dc2626; cursor:pointer;">
                                    <i class="fas fa-times-circle"></i> Tidak Ada
                                </label>
                            </div>
                        </div>

                        {{-- 5. Penutup Atap Genteng --}}
                        <div class="indicator-row" style="display:flex; align-items:center; justify-content:space-between; background:#f8fafc; border:1px solid #cbd5e1; border-radius:10px; padding:12px 18px; flex-wrap:wrap; gap:12px; transition:all 0.2s;">
                            <div style="font-size:13.5px; font-weight:700; color:#1e293b; flex:1; min-width:240px;">
                                5. Penutup Atap Genteng
                            </div>
                            <div style="display:flex; align-items:center; gap:12px;">
                                <label class="pill-indicator pill-ada">
                                    <input type="radio" name="indikator_atap" value="ada" class="indikator-radio" {{ old('indikator_atap', $vervalData->indikator_atap) == 'ada' || $vervalData->indikator_atap === null ? 'checked' : '' }} style="accent-color:#16a34a; cursor:pointer;">
                                    <i class="fas fa-check-circle"></i> Ada
                                </label>
                                <label class="pill-indicator pill-tidak-ada">
                                    <input type="radio" name="indikator_atap" value="tidak_ada" class="indikator-radio" {{ old('indikator_atap', $vervalData->indikator_atap) == 'tidak_ada' ? 'checked' : '' }} style="accent-color:#dc2626; cursor:pointer;">
                                    <i class="fas fa-times-circle"></i> Tidak Ada
                                </label>
                            </div>
                        </div>

                        {{-- 6. Penghasilan Kurang dari UMK --}}
                        <div class="indicator-row" style="display:flex; align-items:center; justify-content:space-between; background:#f8fafc; border:1px solid #cbd5e1; border-radius:10px; padding:12px 18px; flex-wrap:wrap; gap:12px; transition:all 0.2s;">
                            <div style="font-size:13.5px; font-weight:700; color:#1e293b; flex:1; min-width:240px;">
                                6. Penghasilan Kurang dari UMK
                            </div>
                            <div style="display:flex; align-items:center; gap:12px;">
                                <label class="pill-indicator pill-ada">
                                    <input type="radio" name="indikator_penghasilan" value="ada" class="indikator-radio" {{ old('indikator_penghasilan', $vervalData->indikator_penghasilan) == 'ada' ? 'checked' : '' }} style="accent-color:#16a34a; cursor:pointer;">
                                    <i class="fas fa-check-circle"></i> Ya / Ada
                                </label>
                                <label class="pill-indicator pill-tidak-ada">
                                    <input type="radio" name="indikator_penghasilan" value="tidak_ada" class="indikator-radio" {{ old('indikator_penghasilan', $vervalData->indikator_penghasilan) == 'tidak_ada' || $vervalData->indikator_penghasilan === null ? 'checked' : '' }} style="accent-color:#dc2626; cursor:pointer;">
                                    <i class="fas fa-times-circle"></i> Tidak
                                </label>
                            </div>
                        </div>
                    </div>

                    {{-- Dynamic Status Summary Card --}}
                    <div id="statusSummaryBox" style="background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%); border-radius:12px; padding:18px 22px; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:16px; border:1px solid #cbd5e1;">
                        <div>
                            <div style="font-size:12px; font-weight:700; text-transform:uppercase; letter-spacing:0.5px; color:#64748b;">
                                Total Indikator Terpenuhi (Ada)
                            </div>
                            <div style="font-size:22px; font-weight:900; color:#0f172a; margin-top:2px;">
                                <span id="indicatorCountDisplay">0</span> / 6 Indikator
                            </div>
                        </div>

                        <div style="text-align:right;">
                            <div style="font-size:12px; font-weight:700; text-transform:uppercase; letter-spacing:0.5px; color:#64748b; margin-bottom:4px;">
                                Status Kelayakan Usulan
                            </div>
                            <span id="statusBadgeDisplay" class="badge" style="padding:8px 18px; border-radius:30px; font-size:14px; font-weight:800; display:inline-flex; align-items:center; gap:8px;">
                                <!-- Updated via JS -->
                            </span>
                        </div>
                    </div>
                </div>

                <!-- 5. Dokumentasi Foto Fisik RTLH 5 Sudut (Style Kamera Modern - Lampiran Evidence) -->
                <div class="form-section">
                    <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:10px; margin-bottom:16px; border-bottom:1px solid rgba(0, 40, 85, 0.06); padding-bottom:12px;">
                        <h4 style="margin:0; border:none; padding:0;">
                            <i class="fas fa-camera-retro"></i> 5. Dokumentasi Foto Fisik RTLH (Lampiran Evidence 5 Sudut)
                        </h4>
                        <span style="font-size:12px; font-weight:600; color:#64748b;">
                            <i class="fas fa-paperclip"></i> Dokumen Foto Berfungsi Sebagai Berkas Lampiran Verval
                        </span>
                    </div>

                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px;">
                        {{-- 1. Sudut Depan --}}
                        <div>
                            <label style="font-weight:700;font-size:13px;display:block;margin-bottom:8px;">1. Tampak Depan</label>
                            <div class="camera-upload-card {{ $vervalData->foto_sudut_depan ? 'has-image' : '' }}" id="card_depan">
                                <input type="file" name="foto_sudut_depan" class="camera-file-input" accept="image/*" onchange="previewCameraPhoto(this, 'preview_depan', 'card_depan')">
                                <img src="{{ $vervalData->foto_sudut_depan ? asset(str_starts_with($vervalData->foto_sudut_depan, 'uploads/') ? $vervalData->foto_sudut_depan : 'uploads/' . $vervalData->foto_sudut_depan) : '' }}" class="camera-preview-img" id="preview_depan" style="{{ $vervalData->foto_sudut_depan ? '' : 'display:none;' }}" alt="Tampak Depan">
                                <div class="camera-icon-bubble" id="icon_depan" style="{{ $vervalData->foto_sudut_depan ? 'display:none;' : '' }}">
                                    <i class="fas fa-camera"></i>
                                </div>
                                <div class="camera-upload-title">Tampak Depan</div>
                                <div class="camera-upload-sub">Fasad &amp; pintu utama rumah</div>
                                @if($vervalData->foto_sudut_depan)
                                    <span class="camera-upload-badge"><i class="fas fa-check-circle"></i> Foto Tersimpan</span>
                                @else
                                    <span class="camera-upload-btn-fake"><i class="fas fa-camera"></i> Ambil Foto</span>
                                @endif
                            </div>
                        </div>

                        {{-- 2. Sudut Belakang --}}
                        <div>
                            <label style="font-weight:700;font-size:13px;display:block;margin-bottom:8px;">2. Tampak Belakang</label>
                            <div class="camera-upload-card {{ $vervalData->foto_sudut_belakang ? 'has-image' : '' }}" id="card_belakang">
                                <input type="file" name="foto_sudut_belakang" class="camera-file-input" accept="image/*" onchange="previewCameraPhoto(this, 'preview_belakang', 'card_belakang')">
                                <img src="{{ $vervalData->foto_sudut_belakang ? asset(str_starts_with($vervalData->foto_sudut_belakang, 'uploads/') ? $vervalData->foto_sudut_belakang : 'uploads/' . $vervalData->foto_sudut_belakang) : '' }}" class="camera-preview-img" id="preview_belakang" style="{{ $vervalData->foto_sudut_belakang ? '' : 'display:none;' }}" alt="Tampak Belakang">
                                <div class="camera-icon-bubble" id="icon_belakang" style="{{ $vervalData->foto_sudut_belakang ? 'display:none;' : '' }}">
                                    <i class="fas fa-camera"></i>
                                </div>
                                <div class="camera-upload-title">Tampak Belakang</div>
                                <div class="camera-upload-sub">Dapur / area belakang rumah</div>
                                @if($vervalData->foto_sudut_belakang)
                                    <span class="camera-upload-badge"><i class="fas fa-check-circle"></i> Foto Tersimpan</span>
                                @else
                                    <span class="camera-upload-btn-fake"><i class="fas fa-camera"></i> Ambil Foto</span>
                                @endif
                            </div>
                        </div>

                        {{-- 3. Bagian Dalam / Interior --}}
                        <div>
                            <label style="font-weight:700;font-size:13px;display:block;margin-bottom:8px;">3. Bagian Dalam / Interior</label>
                            <div class="camera-upload-card {{ $vervalData->foto_bagian_dalam ? 'has-image' : '' }}" id="card_dalam">
                                <input type="file" name="foto_bagian_dalam" class="camera-file-input" accept="image/*" onchange="previewCameraPhoto(this, 'preview_dalam', 'card_dalam')">
                                <img src="{{ $vervalData->foto_bagian_dalam ? asset(str_starts_with($vervalData->foto_bagian_dalam, 'uploads/') ? $vervalData->foto_bagian_dalam : 'uploads/' . $vervalData->foto_bagian_dalam) : '' }}" class="camera-preview-img" id="preview_dalam" style="{{ $vervalData->foto_bagian_dalam ? '' : 'display:none;' }}" alt="Bagian Dalam">
                                <div class="camera-icon-bubble" id="icon_dalam" style="{{ $vervalData->foto_bagian_dalam ? 'display:none;' : '' }}">
                                    <i class="fas fa-camera"></i>
                                </div>
                                <div class="camera-upload-title">Bagian Dalam</div>
                                <div class="camera-upload-sub">Ruang keluarga / lantai &amp; atap</div>
                                @if($vervalData->foto_bagian_dalam)
                                    <span class="camera-upload-badge"><i class="fas fa-check-circle"></i> Foto Tersimpan</span>
                                @else
                                    <span class="camera-upload-btn-fake"><i class="fas fa-camera"></i> Ambil Foto</span>
                                @endif
                            </div>
                        </div>

                        {{-- 4. Samping Kiri --}}
                        <div>
                            <label style="font-weight:700;font-size:13px;display:block;margin-bottom:8px;">4. Samping Kiri</label>
                            <div class="camera-upload-card {{ $vervalData->foto_sudut_kiri ? 'has-image' : '' }}" id="card_kiri">
                                <input type="file" name="foto_sudut_kiri" class="camera-file-input" accept="image/*" onchange="previewCameraPhoto(this, 'preview_kiri', 'card_kiri')">
                                <img src="{{ $vervalData->foto_sudut_kiri ? asset(str_starts_with($vervalData->foto_sudut_kiri, 'uploads/') ? $vervalData->foto_sudut_kiri : 'uploads/' . $vervalData->foto_sudut_kiri) : '' }}" class="camera-preview-img" id="preview_kiri" style="{{ $vervalData->foto_sudut_kiri ? '' : 'display:none;' }}" alt="Samping Kiri">
                                <div class="camera-icon-bubble" id="icon_kiri" style="{{ $vervalData->foto_sudut_kiri ? 'display:none;' : '' }}">
                                    <i class="fas fa-camera"></i>
                                </div>
                                <div class="camera-upload-title">Samping Kiri</div>
                                <div class="camera-upload-sub">Dinding / struktur sisi kiri</div>
                                @if($vervalData->foto_sudut_kiri)
                                    <span class="camera-upload-badge"><i class="fas fa-check-circle"></i> Foto Tersimpan</span>
                                @else
                                    <span class="camera-upload-btn-fake"><i class="fas fa-camera"></i> Ambil Foto</span>
                                @endif
                            </div>
                        </div>

                        {{-- 5. Samping Kanan --}}
                        <div>
                            <label style="font-weight:700;font-size:13px;display:block;margin-bottom:8px;">5. Samping Kanan</label>
                            <div class="camera-upload-card {{ $vervalData->foto_sudut_kanan ? 'has-image' : '' }}" id="card_kanan">
                                <input type="file" name="foto_sudut_kanan" class="camera-file-input" accept="image/*" onchange="previewCameraPhoto(this, 'preview_kanan', 'card_kanan')">
                                <img src="{{ $vervalData->foto_sudut_kanan ? asset(str_starts_with($vervalData->foto_sudut_kanan, 'uploads/') ? $vervalData->foto_sudut_kanan : 'uploads/' . $vervalData->foto_sudut_kanan) : '' }}" class="camera-preview-img" id="preview_kanan" style="{{ $vervalData->foto_sudut_kanan ? '' : 'display:none;' }}" alt="Samping Kanan">
                                <div class="camera-icon-bubble" id="icon_kanan" style="{{ $vervalData->foto_sudut_kanan ? 'display:none;' : '' }}">
                                    <i class="fas fa-camera"></i>
                                </div>
                                <div class="camera-upload-title">Samping Kanan</div>
                                <div class="camera-upload-sub">Dinding / struktur sisi kanan</div>
                                @if($vervalData->foto_sudut_kanan)
                                    <span class="camera-upload-badge"><i class="fas fa-check-circle"></i> Foto Tersimpan</span>
                                @else
                                    <span class="camera-upload-btn-fake"><i class="fas fa-camera"></i> Ambil Foto</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 6. Titik Koordinat GPS (Geotagging) -->
                <div class="form-section">
                    <h4><i class="fas fa-map-location-dot"></i> 5. Titik Koordinat Lokasi Rumah (Geotagging GPS)</h4>
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px;">
                        <div class="form-group">
                            <label>Latitude</label>
                            <input type="text" id="disp_latitude" class="form-control" value="{{ $vervalData->latitude }}"
                                disabled placeholder="Mendeteksi GPS...">
                            <input type="hidden" name="latitude" id="latitude" value="{{ $vervalData->latitude }}">
                        </div>
                        <div class="form-group">
                            <label>Longitude</label>
                            <input type="text" id="disp_longitude" class="form-control" value="{{ $vervalData->longitude }}"
                                disabled placeholder="Mendeteksi GPS...">
                            <input type="hidden" name="longitude" id="longitude" value="{{ $vervalData->longitude }}">
                        </div>
                    </div>
                    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;margin-top:12px;">
                        <small style="color: var(--text-muted);font-weight:600;">
                            <i class="fas fa-info-circle"></i> Koordinat lokasi otomatis dicatat dari sensor GPS perangkat saat form dibuka di lapangan.
                        </small>
                        <button type="button" class="btn btn-outline" style="padding:8px 16px;font-size:12.5px;font-weight:700;display:inline-flex;align-items:center;gap:6px;" onclick="requestLocation()">
                            <i class="fas fa-location-crosshairs"></i> Refresh Koordinat GPS
                        </button>
                    </div>
                </div>

                <!-- Tombol Submit Form -->
                <div style="text-align: right; padding-bottom: 40px; display:flex; justify-content:flex-end; gap:12px; flex-wrap:wrap;">
                    <a href="{{ route('verval-data') }}" class="btn btn-outline" style="padding: 12px 24px; border-radius: var(--radius-sm); font-weight:700;">
                        Batal
                    </a>
                    <button type="submit" class="btn btn-primary" style="padding: 12px 32px; font-weight: 800; border-radius: var(--radius-sm); display:inline-flex; align-items:center; gap:8px; box-shadow:0 4px 14px rgba(0,40,85,0.25);">
                        <i class="fas fa-save"></i> Simpan Hasil Survei Lapangan
                    </button>
                </div>
            </form>
        </div>

        <!-- Modal GPS (Custom System Modal) -->
        <div class="modal-overlay" id="gpsModal">
            <div class="modal-box" style="max-width: 440px;">
                <div class="modal-header" style="background: #fff3cd; border-bottom-color: #ffeeba;">
                    <h3 style="color: #856404; display: flex; align-items: center; gap: 10px; font-size: 16px;">
                        <i class="fas fa-exclamation-triangle"></i> GPS/Lokasi Dibutuhkan
                    </h3>
                </div>

                <div class="modal-body" style="padding: 24px; text-align: center;">
                    <div style="width: 60px; height: 60px; border-radius: 50%; background: rgba(220, 53, 69, 0.1); color: #dc3545; display: inline-flex; align-items: center; justify-content: center; font-size: 24px; margin: 0 auto 16px;">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <p style="font-size: 14px; margin-bottom: 0;">Aktifkan akses GPS (Lokasi) pada browser Anda untuk otomatis menyimpan koordinat Geotagging rumah calon penerima BSPS.</p>
                </div>

                <div class="modal-footer" style="padding: 16px 20px; background: var(--bg-body); border-top: 1px solid rgba(0, 40, 85, 0.06); display: flex; justify-content: center;">
                    <button type="button" class="btn btn-primary" onclick="requestLocation()">
                        <i class="fas fa-sync-alt"></i> Coba Deteksi Lokasi
                    </button>
                </div>
            </div>
        </div>
    </main>
@endsection

@push('scripts')
    <script>
        // Live Photo Preview Handler
        function previewCameraPhoto(input, imgPreviewId, cardId) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = document.getElementById(imgPreviewId);
                    const card = document.getElementById(cardId);
                    if (img) {
                        img.src = e.target.result;
                        img.style.display = 'block';
                    }
                    if (card) {
                        card.classList.add('has-image');
                        const bubble = card.querySelector('.camera-icon-bubble');
                        if (bubble) bubble.style.display = 'none';
                    }
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        // GPS Geolocation Handler
        function requestLocation() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function (position) {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;
                    document.getElementById('disp_latitude').value = lat;
                    document.getElementById('latitude').value = lat;
                    document.getElementById('disp_longitude').value = lng;
                    document.getElementById('longitude').value = lng;

                    if (window.PuprModal) {
                        window.PuprModal.close('gpsModal');
                    }
                }, function (error) {
                    if (window.PuprModal) {
                        window.PuprModal.open('gpsModal');
                    }
                }, {
                    enableHighAccuracy: true
                });
            }
        }

        // Recount & Update Status Kelayakan Usulan (Kriteria RTLH Terpenuhi)
        function updateIndicatorsStatus() {
            let countRtlh = 0;

            const checkRtlhDefect = (groupName, rtlhValue) => {
                const checkedRadio = document.querySelector(`input[name="${groupName}"]:checked`);
                const row = checkedRadio ? checkedRadio.closest('.indicator-row') : null;

                if (checkedRadio && checkedRadio.value === rtlhValue) {
                    countRtlh++;
                    if (row) {
                        row.style.background = 'rgba(239, 68, 68, 0.05)';
                        row.style.borderColor = 'rgba(239, 68, 68, 0.35)';
                    }
                } else if (row) {
                    row.style.background = 'rgba(34, 197, 94, 0.04)';
                    row.style.borderColor = 'rgba(34, 197, 94, 0.3)';
                }
            };

            // Komponen fisik bernilai 'tidak_ada' -> Indikator RTLH terpenuhi
            checkRtlhDefect('indikator_lantai', 'tidak_ada');
            checkRtlhDefect('indikator_pondasi', 'tidak_ada');
            checkRtlhDefect('indikator_dinding', 'tidak_ada');
            checkRtlhDefect('indikator_struktur', 'tidak_ada');
            checkRtlhDefect('indikator_atap', 'tidak_ada');
            // Penghasilan < UMK bernilai 'ada' -> Indikator RTLH terpenuhi
            checkRtlhDefect('indikator_penghasilan', 'ada');

            const countEl = document.getElementById('indicatorCountDisplay');
            if (countEl) countEl.textContent = countRtlh;

            const badgeEl = document.getElementById('statusBadgeDisplay');
            if (badgeEl) {
                if (countRtlh >= 2) {
                    badgeEl.style.background = '#dcfce7';
                    badgeEl.style.color = '#15803d';
                    badgeEl.style.border = '1px solid #86efac';
                    badgeEl.innerHTML = '<i class="fas fa-check-circle"></i> LAYAK DIUSULKAN';
                } else {
                    badgeEl.style.background = '#fee2e2';
                    badgeEl.style.color = '#b91c1c';
                    badgeEl.style.border = '1px solid #fca5a5';
                    badgeEl.innerHTML = '<i class="fas fa-times-circle"></i> TIDAK LAYAK DIUSULKAN';
                }
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            requestLocation();
            
            const radios = document.querySelectorAll('.indikator-radio');
            radios.forEach(r => {
                r.addEventListener('change', updateIndicatorsStatus);
            });
            updateIndicatorsStatus();
        });
    </script>
@endpush
