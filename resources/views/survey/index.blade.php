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
            border: 2px dashed rgba(0, 40, 85, 0.20);
            border-radius: 12px;
            padding: 20px 16px;
            text-align: center;
            position: relative;
            transition: all 0.25s ease;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 190px;
            overflow: hidden;
            box-sizing: border-box;
        }

        .camera-upload-card:hover {
            border-color: var(--primary);
            background: rgba(0, 40, 85, 0.02);
            box-shadow: 0 4px 14px rgba(0, 40, 85, 0.06);
        }

        .camera-upload-card.has-image {
            border-style: solid;
            border-color: rgba(39, 174, 96, 0.35);
            background: rgba(39, 174, 96, 0.03);
        }

        .camera-icon-bubble {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: rgba(0, 40, 85, 0.08);
            color: var(--primary);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 19px;
            margin-bottom: 10px;
            transition: all 0.2s ease;
        }

        .camera-icon-bubble.success {
            background: rgba(39, 174, 96, 0.12);
            color: var(--success);
            font-size: 22px;
        }

        .camera-upload-card:not(.has-image):hover .camera-icon-bubble {
            background: var(--primary);
            color: #fff;
            transform: scale(1.06);
        }

        .camera-upload-title {
            font-weight: 800;
            font-size: 13.5px;
            color: var(--primary-dark);
            margin-bottom: 4px;
        }

        .camera-upload-sub {
            font-size: 11.5px;
            color: var(--text-muted);
            margin-bottom: 12px;
        }

        .camera-upload-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
            background: rgba(39, 174, 96, 0.12);
            color: var(--success);
            margin-bottom: 12px;
        }

        .camera-file-input {
            display: none;
        }

        .camera-placeholder-box,
        .camera-uploaded-box {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            width: 100%;
        }

        .camera-upload-btn-fake {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 700;
            background: rgba(0, 40, 85, 0.08);
            color: var(--primary);
            border: none;
            cursor: pointer;
            transition: all 0.2s;
        }

        .camera-upload-btn-fake:hover {
            background: var(--primary);
            color: #fff;
        }

        /* 2 Action Buttons on Photo (Lihat Foto & Hapus) */
        .camera-actions-box {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            width: 100%;
            margin-top: 4px;
        }

        .btn-photo-action {
            flex: 1;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 4px;
            padding: 5px 8px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 700;
            white-space: nowrap;
            border: 1px solid transparent;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
            line-height: 1.2;
        }

        .btn-photo-action.view {
            background: rgba(0, 40, 85, 0.08);
            color: var(--primary);
            border-color: rgba(0, 40, 85, 0.15);
        }
        .btn-photo-action.view:hover {
            background: var(--primary);
            color: #ffffff;
            border-color: var(--primary);
        }

        .btn-photo-action.delete {
            background: rgba(239, 68, 68, 0.10);
            color: #dc2626;
            border-color: rgba(239, 68, 68, 0.25);
        }
        .btn-photo-action.delete:hover {
            background: #dc2626;
            color: #ffffff;
            border-color: #dc2626;
        }

        /* Loading Spinner Overlay */
        .camera-loading-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.94);
            backdrop-filter: blur(3px);
            display: none;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 10px;
            z-index: 20;
            border-radius: 10px;
        }

        .camera-loading-overlay.active {
            display: flex;
        }

        .camera-spinner {
            width: 32px;
            height: 32px;
            border: 3px solid rgba(0, 40, 85, 0.15);
            border-top-color: var(--primary);
            border-radius: 50%;
            animation: cameraSpin 0.75s linear infinite;
        }

        .camera-loading-text {
            font-size: 11.5px;
            font-weight: 700;
            color: var(--primary);
        }

        @keyframes cameraSpin {
            to { transform: rotate(360deg); }
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
            .recipient-selector-bar {
                padding: 18px 16px;
                border-radius: 12px;
                gap: 14px;
            }
            .recipient-selector-bar h3 {
                font-size: 18px;
                margin-bottom: 4px;
            }
            .recipient-selector-bar p {
                font-size: 12.5px;
                line-height: 1.45;
            }
            .recipient-actions {
                width: 100%;
                display: flex;
                flex-direction: column;
                gap: 8px;
            }
            .recipient-actions .btn,
            .recipient-actions .btn-outline {
                width: 100%;
                justify-content: center;
                padding: 10px 14px;
                font-size: 12.5px;
                box-sizing: border-box;
            }
            .form-section {
                padding: 18px 14px;
                border-radius: 12px;
                margin-bottom: 16px;
            }
            .form-section h4 {
                font-size: 14.5px;
                margin-bottom: 14px;
                padding-bottom: 10px;
            }
        }

        .survey-footer-actions {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 12px;
            padding-top: 16px;
            padding-bottom: 40px;
            margin-top: 10px;
        }

        .survey-footer-actions .btn {
            padding: 12px 28px;
            font-size: 13.5px;
            font-weight: 800;
            border-radius: var(--radius-sm);
        }

        @media (max-width: 768px) {
            .survey-footer-actions {
                flex-direction: column-reverse;
                gap: 10px;
                padding-bottom: 30px;
            }
            .survey-footer-actions .btn {
                width: 100%;
                justify-content: center;
                padding: 13px 20px;
                font-size: 13.5px;
                box-sizing: border-box;
            }
        }

        .is-invalid-highlight {
            border-color: #e11d48 !important;
            box-shadow: 0 0 0 3px rgba(225, 29, 72, 0.20) !important;
            animation: pulseInvalid 1.5s infinite;
        }

        @keyframes pulseInvalid {
            0%, 100% {
                border-color: #e11d48;
            }
            50% {
                border-color: #fda4af;
            }
        }
    </style>
@endpush

@section('content')
    <!-- Dedicated Public Navbar Component (Sama Seperti Landing Page) -->
    @include('layouts.navbar_public')

    <main class="dashboard-content dashboard-content-public">
        <div class="survey-container">
            <!-- Status Koneksi Online / Offline Banner -->
            <div id="offlineSyncBanner" style="background:linear-gradient(135deg, #1e293b 0%, #0f172a 100%);color:#fff;border-radius:12px;padding:14px 20px;margin-bottom:20px;display:flex;align-items:center;justify-content:space-between;gap:14px;box-shadow:0 4px 16px rgba(0,0,0,0.12);border-left:5px solid #22c55e;transition:all 0.3s ease;">
                <div style="display:flex;align-items:center;gap:12px;">
                    <div id="networkStatusIcon" style="width:36px;height:36px;border-radius:50%;background:rgba(34,197,94,0.16);color:#22c55e;display:flex;align-items:center;justify-content:center;font-size:16px;flex-shrink:0;">
                        <i class="fas fa-wifi"></i>
                    </div>
                    <div>
                        <div id="networkStatusTitle" style="font-weight:800;font-size:13.5px;color:#fff;">Mode Terhubung (Online)</div>
                        <div id="networkStatusSub" style="font-size:12px;color:rgba(255,255,255,0.75);">Koneksi lancar. Data survei dikirim langsung ke server.</div>
                    </div>
                </div>
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
                <div class="recipient-actions" style="display:flex;gap:10px;align-items:center;flex-wrap:wrap;">
                    <button type="button" class="btn" onclick="openDetailProfilModal()" style="background:rgba(255,255,255,0.95);color:var(--primary);font-weight:800;font-size:13px;padding:10px 18px;border-radius:var(--radius-sm);border:none;cursor:pointer;display:inline-flex;align-items:center;gap:8px;box-shadow:0 4px 12px rgba(0,0,0,0.15);transition:all 0.2s;">
                        <i class="fas fa-id-card"></i> Detail Profil
                    </button>
                    <a href="{{ route('verval-data.surat-pernyataan', $vervalData->id) }}" target="_blank" class="btn" style="background:#ffb800;color:#002855;font-weight:800;font-size:13px;padding:10px 18px;border-radius:var(--radius-sm);text-decoration:none;display:inline-flex;align-items:center;gap:8px;box-shadow:0 4px 12px rgba(255,184,0,0.3);">
                        <i class="fas fa-file-signature"></i> Cetak Surat Pernyataan
                    </a>
                    <a href="{{ Auth::user() && Auth::user()->role === 'petugas' ? route('petugas.dashboard') : route('verval-data') }}" class="btn btn-outline" style="background:rgba(255,255,255,0.15);color:#fff;border:1px solid rgba(255,255,255,0.3);font-size:13px;font-weight:600;padding:10px 18px;border-radius:var(--radius-sm);text-decoration:none;display:inline-flex;align-items:center;gap:8px;">
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

            <!-- Form Utama Input / Edit Survei Lapangan -->
            <form action="{{ route('survey.store', $vervalData->id) }}" method="POST" enctype="multipart/form-data" id="surveyForm" onsubmit="return validateSurveyForm(event)">
                @csrf

                <!-- 1. Data Tambahan & Lahan (Dengan Custom Dropdown PUPR) -->
                <div class="form-section">
                    <h4><i class="fas fa-user-pen"></i> 1. Data Tambahan &amp; Kelaikan Hunian</h4>

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
                            <label>Penghasilan Per Bulan <span style="font-size:11.5px; color:#64748b; font-weight:normal;">(Opsional)</span></label>
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

                <!-- 2. Dokumen Administrasi & Berkas (Upload Style Kamera + Custom Dropdown) -->
                <div class="form-section">
                    <h4><i class="fas fa-file-invoice"></i> 2. Berkas &amp; Dokumen Administrasi</h4>

                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 20px;">
                        {{-- Upload KTP --}}
                        <div>
                            <label style="font-weight:700;font-size:13px;display:block;margin-bottom:8px;">Foto / Scan KTP</label>
                            <div class="camera-upload-card {{ $vervalData->ktp ? 'has-image' : '' }}" id="card_ktp">
                                <input type="file" id="input_ktp" name="ktp" class="camera-file-input" accept="image/*" onchange="previewPhoto(this, 'ktp')">
                                <input type="hidden" id="url_ktp" value="{{ $vervalData->ktp ? url('/uploads/' . basename($vervalData->ktp)) : '' }}">
                                
                                {{-- State Kosong --}}
                                <div class="camera-placeholder-box" id="placeholder_ktp" style="{{ $vervalData->ktp ? 'display:none;' : 'display:flex;' }}">
                                    <div class="camera-icon-bubble">
                                        <i class="fas fa-id-card"></i>
                                    </div>
                                    <div class="camera-upload-title">Unggah Berkas KTP</div>
                                    <div class="camera-upload-sub">Klik untuk pilih atau foto via kamera</div>
                                    <button type="button" class="camera-upload-btn-fake" onclick="triggerPhotoInput('input_ktp')">
                                        <i class="fas fa-camera"></i> Ambil / Pilih Foto
                                    </button>
                                </div>

                                {{-- State Tersimpan / Terpilih (2 Tombol Aksi) --}}
                                <div class="camera-uploaded-box" id="uploaded_ktp" style="{{ $vervalData->ktp ? 'display:flex;' : 'display:none;' }}">
                                    <div class="camera-icon-bubble success">
                                        <i class="fas fa-check-circle"></i>
                                    </div>
                                    <div class="camera-upload-title">Berkas KTP</div>
                                    <span class="camera-upload-badge"><i class="fas fa-file-image"></i> {{ $vervalData->ktp ? 'Foto Tersimpan' : 'Foto Terpilih' }}</span>
                                    <div class="camera-actions-box">
                                        <button type="button" class="btn-photo-action view" onclick="openPhotoNewTab('ktp')" title="Buka / Lihat Berkas KTP">
                                            <i class="fas fa-eye"></i> Lihat
                                        </button>
                                        <button type="button" class="btn-photo-action delete" onclick="removePhoto('ktp')" title="Hapus / Ganti Berkas KTP">
                                            <i class="fas fa-trash-alt"></i> Hapus
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Upload KK --}}
                        <div>
                            <label style="font-weight:700;font-size:13px;display:block;margin-bottom:8px;">Foto / Scan Kartu Keluarga (KK)</label>
                            <div class="camera-upload-card {{ $vervalData->kk ? 'has-image' : '' }}" id="card_kk">
                                <input type="file" id="input_kk" name="kk" class="camera-file-input" accept="image/*" onchange="previewPhoto(this, 'kk')">
                                <input type="hidden" id="url_kk" value="{{ $vervalData->kk ? url('/uploads/' . basename($vervalData->kk)) : '' }}">
                                
                                {{-- State Kosong --}}
                                <div class="camera-placeholder-box" id="placeholder_kk" style="{{ $vervalData->kk ? 'display:none;' : 'display:flex;' }}">
                                    <div class="camera-icon-bubble">
                                        <i class="fas fa-users-rectangle"></i>
                                    </div>
                                    <div class="camera-upload-title">Unggah Berkas KK</div>
                                    <div class="camera-upload-sub">Klik untuk pilih atau foto via kamera</div>
                                    <button type="button" class="camera-upload-btn-fake" onclick="triggerPhotoInput('input_kk')">
                                        <i class="fas fa-camera"></i> Ambil / Pilih Foto
                                    </button>
                                </div>

                                {{-- State Tersimpan / Terpilih (2 Tombol Aksi) --}}
                                <div class="camera-uploaded-box" id="uploaded_kk" style="{{ $vervalData->kk ? 'display:flex;' : 'display:none;' }}">
                                    <div class="camera-icon-bubble success">
                                        <i class="fas fa-check-circle"></i>
                                    </div>
                                    <div class="camera-upload-title">Berkas KK</div>
                                    <span class="camera-upload-badge"><i class="fas fa-file-image"></i> {{ $vervalData->kk ? 'Foto Tersimpan' : 'Foto Terpilih' }}</span>
                                    <div class="camera-actions-box">
                                        <button type="button" class="btn-photo-action view" onclick="openPhotoNewTab('kk')" title="Buka / Lihat Berkas KK">
                                            <i class="fas fa-eye"></i> Lihat
                                        </button>
                                        <button type="button" class="btn-photo-action delete" onclick="removePhoto('kk')" title="Hapus / Ganti Berkas KK">
                                            <i class="fas fa-trash-alt"></i> Hapus
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Upload Sertifikat Tanah --}}
                        <div>
                            <label style="font-weight:700;font-size:13px;display:block;margin-bottom:8px;">Foto Sertipikat / Bukti Tanah</label>
                            <div class="camera-upload-card {{ $vervalData->sertifikat_tanah ? 'has-image' : '' }}" id="card_sertifikat_tanah">
                                <input type="file" id="input_sertifikat_tanah" name="sertifikat_tanah" class="camera-file-input" accept="image/*" onchange="previewPhoto(this, 'sertifikat_tanah')">
                                <input type="hidden" id="url_sertifikat_tanah" value="{{ $vervalData->sertifikat_tanah ? url('/uploads/' . basename($vervalData->sertifikat_tanah)) : '' }}">
                                
                                {{-- State Kosong --}}
                                <div class="camera-placeholder-box" id="placeholder_sertifikat_tanah" style="{{ $vervalData->sertifikat_tanah ? 'display:none;' : 'display:flex;' }}">
                                    <div class="camera-icon-bubble">
                                        <i class="fas fa-file-contract"></i>
                                    </div>
                                    <div class="camera-upload-title">Unggah Bukti Tanah</div>
                                    <div class="camera-upload-sub">Sertipikat / Surat Keterangan Tanah</div>
                                    <button type="button" class="camera-upload-btn-fake" onclick="triggerPhotoInput('input_sertifikat_tanah')">
                                        <i class="fas fa-camera"></i> Ambil / Pilih Foto
                                    </button>
                                </div>

                                {{-- State Tersimpan / Terpilih (2 Tombol Aksi) --}}
                                <div class="camera-uploaded-box" id="uploaded_sertifikat_tanah" style="{{ $vervalData->sertifikat_tanah ? 'display:flex;' : 'display:none;' }}">
                                    <div class="camera-icon-bubble success">
                                        <i class="fas fa-check-circle"></i>
                                    </div>
                                    <div class="camera-upload-title">Bukti Kepemilikan Lahan</div>
                                    <span class="camera-upload-badge"><i class="fas fa-file-image"></i> {{ $vervalData->sertifikat_tanah ? 'Foto Tersimpan' : 'Foto Terpilih' }}</span>
                                    <div class="camera-actions-box">
                                        <button type="button" class="btn-photo-action view" onclick="openPhotoNewTab('sertifikat_tanah')" title="Buka / Lihat Bukti Lahan">
                                            <i class="fas fa-eye"></i> Lihat
                                        </button>
                                        <button type="button" class="btn-photo-action delete" onclick="removePhoto('sertifikat_tanah')" title="Hapus / Ganti Bukti Lahan">
                                            <i class="fas fa-trash-alt"></i> Hapus
                                        </button>
                                    </div>
                                </div>
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

                <!-- 3. Indikator Kelayakan Calon Penerima (Status Layak Diusulkan) -->
                <div class="form-section" style="border: 2px solid rgba(0, 40, 85, 0.12); background: #ffffff;">
                    <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px; margin-bottom:16px; border-bottom:1px solid rgba(0, 40, 85, 0.08); padding-bottom:12px;">
                        <h4 style="margin:0; border:none; padding:0;">
                            <i class="fas fa-list-check" style="color:var(--primary);"></i> 3. Indikator Kelayakan RTLH (Kriteria Layak Diusulkan)
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
                        <div class="indicator-row" id="row_indikator_lantai" style="display:flex; align-items:center; justify-content:space-between; background:#f8fafc; border:1px solid #cbd5e1; border-radius:10px; padding:12px 18px; flex-wrap:wrap; gap:12px; transition:all 0.2s;">
                            <div style="font-size:13.5px; font-weight:700; color:#1e293b; flex:1; min-width:240px;">
                                1. Lantai Keramik
                            </div>
                            <div style="display:flex; align-items:center; gap:12px;">
                                <label class="pill-indicator pill-ada">
                                    <input type="radio" name="indikator_lantai" value="ada" class="indikator-radio" {{ old('indikator_lantai', $vervalData->indikator_lantai) === 'ada' ? 'checked' : '' }} style="accent-color:#16a34a; cursor:pointer;">
                                    <i class="fas fa-check-circle"></i> Ada
                                </label>
                                <label class="pill-indicator pill-tidak-ada">
                                    <input type="radio" name="indikator_lantai" value="tidak_ada" class="indikator-radio" {{ old('indikator_lantai', $vervalData->indikator_lantai) === 'tidak_ada' ? 'checked' : '' }} style="accent-color:#dc2626; cursor:pointer;">
                                    <i class="fas fa-times-circle"></i> Tidak Ada
                                </label>
                            </div>
                        </div>

                        {{-- 2. Pondasi Bangunan --}}
                        <div class="indicator-row" id="row_indikator_pondasi" style="display:flex; align-items:center; justify-content:space-between; background:#f8fafc; border:1px solid #cbd5e1; border-radius:10px; padding:12px 18px; flex-wrap:wrap; gap:12px; transition:all 0.2s;">
                            <div style="font-size:13.5px; font-weight:700; color:#1e293b; flex:1; min-width:240px;">
                                2. Pondasi Bangunan
                            </div>
                            <div style="display:flex; align-items:center; gap:12px;">
                                <label class="pill-indicator pill-ada">
                                    <input type="radio" name="indikator_pondasi" value="ada" class="indikator-radio" {{ old('indikator_pondasi', $vervalData->indikator_pondasi) === 'ada' ? 'checked' : '' }} style="accent-color:#16a34a; cursor:pointer;">
                                    <i class="fas fa-check-circle"></i> Ada
                                </label>
                                <label class="pill-indicator pill-tidak-ada">
                                    <input type="radio" name="indikator_pondasi" value="tidak_ada" class="indikator-radio" {{ old('indikator_pondasi', $vervalData->indikator_pondasi) === 'tidak_ada' ? 'checked' : '' }} style="accent-color:#dc2626; cursor:pointer;">
                                    <i class="fas fa-times-circle"></i> Tidak Ada
                                </label>
                            </div>
                        </div>

                        {{-- 3. Dinding Bata / Tembok --}}
                        <div class="indicator-row" id="row_indikator_dinding" style="display:flex; align-items:center; justify-content:space-between; background:#f8fafc; border:1px solid #cbd5e1; border-radius:10px; padding:12px 18px; flex-wrap:wrap; gap:12px; transition:all 0.2s;">
                            <div style="font-size:13.5px; font-weight:700; color:#1e293b; flex:1; min-width:240px;">
                                3. Dinding Bata / Tembok
                            </div>
                            <div style="display:flex; align-items:center; gap:12px;">
                                <label class="pill-indicator pill-ada">
                                    <input type="radio" name="indikator_dinding" value="ada" class="indikator-radio" {{ old('indikator_dinding', $vervalData->indikator_dinding) === 'ada' ? 'checked' : '' }} style="accent-color:#16a34a; cursor:pointer;">
                                    <i class="fas fa-check-circle"></i> Ada
                                </label>
                                <label class="pill-indicator pill-tidak-ada">
                                    <input type="radio" name="indikator_dinding" value="tidak_ada" class="indikator-radio" {{ old('indikator_dinding', $vervalData->indikator_dinding) === 'tidak_ada' ? 'checked' : '' }} style="accent-color:#dc2626; cursor:pointer;">
                                    <i class="fas fa-times-circle"></i> Tidak Ada
                                </label>
                            </div>
                        </div>

                        {{-- 4. Struktur Bangunan (Sloof, Kolom, Ring Balok) --}}
                        <div class="indicator-row" id="row_indikator_struktur" style="display:flex; align-items:center; justify-content:space-between; background:#f8fafc; border:1px solid #cbd5e1; border-radius:10px; padding:12px 18px; flex-wrap:wrap; gap:12px; transition:all 0.2s;">
                            <div style="font-size:13.5px; font-weight:700; color:#1e293b; flex:1; min-width:240px;">
                                4. Struktur Bangunan (Sloof, Kolom, Ring Balok)
                            </div>
                            <div style="display:flex; align-items:center; gap:12px;">
                                <label class="pill-indicator pill-ada">
                                    <input type="radio" name="indikator_struktur" value="ada" class="indikator-radio" {{ old('indikator_struktur', $vervalData->indikator_struktur) === 'ada' ? 'checked' : '' }} style="accent-color:#16a34a; cursor:pointer;">
                                    <i class="fas fa-check-circle"></i> Ada
                                </label>
                                <label class="pill-indicator pill-tidak-ada">
                                    <input type="radio" name="indikator_struktur" value="tidak_ada" class="indikator-radio" {{ old('indikator_struktur', $vervalData->indikator_struktur) === 'tidak_ada' ? 'checked' : '' }} style="accent-color:#dc2626; cursor:pointer;">
                                    <i class="fas fa-times-circle"></i> Tidak Ada
                                </label>
                            </div>
                        </div>

                        {{-- 5. Penutup Atap Genteng --}}
                        <div class="indicator-row" id="row_indikator_atap" style="display:flex; align-items:center; justify-content:space-between; background:#f8fafc; border:1px solid #cbd5e1; border-radius:10px; padding:12px 18px; flex-wrap:wrap; gap:12px; transition:all 0.2s;">
                            <div style="font-size:13.5px; font-weight:700; color:#1e293b; flex:1; min-width:240px;">
                                5. Penutup Atap Genteng
                            </div>
                            <div style="display:flex; align-items:center; gap:12px;">
                                <label class="pill-indicator pill-ada">
                                    <input type="radio" name="indikator_atap" value="ada" class="indikator-radio" {{ old('indikator_atap', $vervalData->indikator_atap) === 'ada' ? 'checked' : '' }} style="accent-color:#16a34a; cursor:pointer;">
                                    <i class="fas fa-check-circle"></i> Ada
                                </label>
                                <label class="pill-indicator pill-tidak-ada">
                                    <input type="radio" name="indikator_atap" value="tidak_ada" class="indikator-radio" {{ old('indikator_atap', $vervalData->indikator_atap) === 'tidak_ada' ? 'checked' : '' }} style="accent-color:#dc2626; cursor:pointer;">
                                    <i class="fas fa-times-circle"></i> Tidak Ada
                                </label>
                            </div>
                        </div>

                        {{-- 6. Penghasilan Kurang dari UMK --}}
                        <div class="indicator-row" id="row_indikator_penghasilan" style="display:flex; align-items:center; justify-content:space-between; background:#f8fafc; border:1px solid #cbd5e1; border-radius:10px; padding:12px 18px; flex-wrap:wrap; gap:12px; transition:all 0.2s;">
                            <div style="font-size:13.5px; font-weight:700; color:#1e293b; flex:1; min-width:240px;">
                                6. Penghasilan Kurang dari UMK
                            </div>
                            <div style="display:flex; align-items:center; gap:12px;">
                                <label class="pill-indicator pill-ada">
                                    <input type="radio" name="indikator_penghasilan" value="ada" class="indikator-radio" {{ old('indikator_penghasilan', $vervalData->indikator_penghasilan) === 'ada' ? 'checked' : '' }} style="accent-color:#16a34a; cursor:pointer;">
                                    <i class="fas fa-check-circle"></i> Ya / Ada
                                </label>
                                <label class="pill-indicator pill-tidak-ada">
                                    <input type="radio" name="indikator_penghasilan" value="tidak_ada" class="indikator-radio" {{ old('indikator_penghasilan', $vervalData->indikator_penghasilan) === 'tidak_ada' ? 'checked' : '' }} style="accent-color:#dc2626; cursor:pointer;">
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

                <!-- 4. Dokumentasi Foto Fisik RTLH 5 Sudut (Style Kamera Modern - Lampiran Evidence) -->
                <div class="form-section">
                    <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:10px; margin-bottom:16px; border-bottom:1px solid rgba(0, 40, 85, 0.06); padding-bottom:12px;">
                        <h4 style="margin:0; border:none; padding:0;">
                            <i class="fas fa-camera-retro"></i> 4. Dokumentasi Foto Fisik RTLH (Lampiran Evidence 5 Sudut)
                        </h4>
                        <span style="font-size:12px; font-weight:600; color:#64748b;">
                            <i class="fas fa-paperclip"></i> Dokumen Foto Berfungsi Sebagai Berkas Lampiran Verval
                        </span>
                    </div>

                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px;">
                        {{-- 1. Sudut Depan --}}
                        <div>
                            <label style="font-weight:700;font-size:13px;display:block;margin-bottom:8px;">1. Tampak Depan</label>
                            <div class="camera-upload-card {{ $vervalData->foto_sudut_depan ? 'has-image' : '' }}" id="card_foto_sudut_depan">
                                <input type="file" id="input_foto_sudut_depan" name="foto_sudut_depan" class="camera-file-input" accept="image/*" onchange="previewPhoto(this, 'foto_sudut_depan')">
                                <input type="hidden" id="url_foto_sudut_depan" value="{{ $vervalData->foto_sudut_depan ? url('/uploads/' . basename($vervalData->foto_sudut_depan)) : '' }}">
                                
                                {{-- State Kosong --}}
                                <div class="camera-placeholder-box" id="placeholder_foto_sudut_depan" style="{{ $vervalData->foto_sudut_depan ? 'display:none;' : 'display:flex;' }}">
                                    <div class="camera-icon-bubble">
                                        <i class="fas fa-camera"></i>
                                    </div>
                                    <div class="camera-upload-title">Tampak Depan</div>
                                    <div class="camera-upload-sub">Fasad &amp; pintu utama rumah</div>
                                    <button type="button" class="camera-upload-btn-fake" onclick="triggerPhotoInput('input_foto_sudut_depan')">
                                        <i class="fas fa-camera"></i> Ambil / Pilih Foto
                                    </button>
                                </div>

                                {{-- State Tersimpan / Terpilih (2 Tombol Aksi) --}}
                                <div class="camera-uploaded-box" id="uploaded_foto_sudut_depan" style="{{ $vervalData->foto_sudut_depan ? 'display:flex;' : 'display:none;' }}">
                                    <div class="camera-icon-bubble success">
                                        <i class="fas fa-check-circle"></i>
                                    </div>
                                    <div class="camera-upload-title">Tampak Depan</div>
                                    <span class="camera-upload-badge"><i class="fas fa-file-image"></i> {{ $vervalData->foto_sudut_depan ? 'Foto Tersimpan' : 'Foto Terpilih' }}</span>
                                    <div class="camera-actions-box">
                                        <button type="button" class="btn-photo-action view" onclick="openPhotoNewTab('foto_sudut_depan')" title="Buka / Lihat Foto Tampak Depan">
                                            <i class="fas fa-eye"></i> Lihat
                                        </button>
                                        <button type="button" class="btn-photo-action delete" onclick="removePhoto('foto_sudut_depan')" title="Hapus / Ganti Foto Tampak Depan">
                                            <i class="fas fa-trash-alt"></i> Hapus
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- 2. Sudut Belakang --}}
                        <div>
                            <label style="font-weight:700;font-size:13px;display:block;margin-bottom:8px;">2. Tampak Belakang</label>
                            <div class="camera-upload-card {{ $vervalData->foto_sudut_belakang ? 'has-image' : '' }}" id="card_foto_sudut_belakang">
                                <input type="file" id="input_foto_sudut_belakang" name="foto_sudut_belakang" class="camera-file-input" accept="image/*" onchange="previewPhoto(this, 'foto_sudut_belakang')">
                                <input type="hidden" id="url_foto_sudut_belakang" value="{{ $vervalData->foto_sudut_belakang ? url('/uploads/' . basename($vervalData->foto_sudut_belakang)) : '' }}">
                                
                                {{-- State Kosong --}}
                                <div class="camera-placeholder-box" id="placeholder_foto_sudut_belakang" style="{{ $vervalData->foto_sudut_belakang ? 'display:none;' : 'display:flex;' }}">
                                    <div class="camera-icon-bubble">
                                        <i class="fas fa-camera"></i>
                                    </div>
                                    <div class="camera-upload-title">Tampak Belakang</div>
                                    <div class="camera-upload-sub">Dapur / area belakang rumah</div>
                                    <button type="button" class="camera-upload-btn-fake" onclick="triggerPhotoInput('input_foto_sudut_belakang')">
                                        <i class="fas fa-camera"></i> Ambil / Pilih Foto
                                    </button>
                                </div>

                                {{-- State Tersimpan / Terpilih (2 Tombol Aksi) --}}
                                <div class="camera-uploaded-box" id="uploaded_foto_sudut_belakang" style="{{ $vervalData->foto_sudut_belakang ? 'display:flex;' : 'display:none;' }}">
                                    <div class="camera-icon-bubble success">
                                        <i class="fas fa-check-circle"></i>
                                    </div>
                                    <div class="camera-upload-title">Tampak Belakang</div>
                                    <span class="camera-upload-badge"><i class="fas fa-file-image"></i> {{ $vervalData->foto_sudut_belakang ? 'Foto Tersimpan' : 'Foto Terpilih' }}</span>
                                    <div class="camera-actions-box">
                                        <button type="button" class="btn-photo-action view" onclick="openPhotoNewTab('foto_sudut_belakang')" title="Buka / Lihat Foto Tampak Belakang">
                                            <i class="fas fa-eye"></i> Lihat
                                        </button>
                                        <button type="button" class="btn-photo-action delete" onclick="removePhoto('foto_sudut_belakang')" title="Hapus / Ganti Foto Tampak Belakang">
                                            <i class="fas fa-trash-alt"></i> Hapus
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- 3. Bagian Dalam / Interior --}}
                        <div>
                            <label style="font-weight:700;font-size:13px;display:block;margin-bottom:8px;">3. Bagian Dalam / Interior</label>
                            <div class="camera-upload-card {{ $vervalData->foto_bagian_dalam ? 'has-image' : '' }}" id="card_foto_bagian_dalam">
                                <input type="file" id="input_foto_bagian_dalam" name="foto_bagian_dalam" class="camera-file-input" accept="image/*" onchange="previewPhoto(this, 'foto_bagian_dalam')">
                                <input type="hidden" id="url_foto_bagian_dalam" value="{{ $vervalData->foto_bagian_dalam ? url('/uploads/' . basename($vervalData->foto_bagian_dalam)) : '' }}">
                                
                                {{-- State Kosong --}}
                                <div class="camera-placeholder-box" id="placeholder_foto_bagian_dalam" style="{{ $vervalData->foto_bagian_dalam ? 'display:none;' : 'display:flex;' }}">
                                    <div class="camera-icon-bubble">
                                        <i class="fas fa-camera"></i>
                                    </div>
                                    <div class="camera-upload-title">Bagian Dalam</div>
                                    <div class="camera-upload-sub">Ruang keluarga / lantai &amp; atap</div>
                                    <button type="button" class="camera-upload-btn-fake" onclick="triggerPhotoInput('input_foto_bagian_dalam')">
                                        <i class="fas fa-camera"></i> Ambil / Pilih Foto
                                    </button>
                                </div>

                                {{-- State Tersimpan / Terpilih (2 Tombol Aksi) --}}
                                <div class="camera-uploaded-box" id="uploaded_foto_bagian_dalam" style="{{ $vervalData->foto_bagian_dalam ? 'display:flex;' : 'display:none;' }}">
                                    <div class="camera-icon-bubble success">
                                        <i class="fas fa-check-circle"></i>
                                    </div>
                                    <div class="camera-upload-title">Bagian Dalam / Interior</div>
                                    <span class="camera-upload-badge"><i class="fas fa-file-image"></i> {{ $vervalData->foto_bagian_dalam ? 'Foto Tersimpan' : 'Foto Terpilih' }}</span>
                                    <div class="camera-actions-box">
                                        <button type="button" class="btn-photo-action view" onclick="openPhotoNewTab('foto_bagian_dalam')" title="Buka / Lihat Foto Bagian Dalam">
                                            <i class="fas fa-eye"></i> Lihat
                                        </button>
                                        <button type="button" class="btn-photo-action delete" onclick="removePhoto('foto_bagian_dalam')" title="Hapus / Ganti Foto Bagian Dalam">
                                            <i class="fas fa-trash-alt"></i> Hapus
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- 4. Samping Kiri --}}
                        <div>
                            <label style="font-weight:700;font-size:13px;display:block;margin-bottom:8px;">4. Samping Kiri</label>
                            <div class="camera-upload-card {{ $vervalData->foto_sudut_kiri ? 'has-image' : '' }}" id="card_foto_sudut_kiri">
                                <input type="file" id="input_foto_sudut_kiri" name="foto_sudut_kiri" class="camera-file-input" accept="image/*" onchange="previewPhoto(this, 'foto_sudut_kiri')">
                                <input type="hidden" id="url_foto_sudut_kiri" value="{{ $vervalData->foto_sudut_kiri ? url('/uploads/' . basename($vervalData->foto_sudut_kiri)) : '' }}">
                                
                                {{-- State Kosong --}}
                                <div class="camera-placeholder-box" id="placeholder_foto_sudut_kiri" style="{{ $vervalData->foto_sudut_kiri ? 'display:none;' : 'display:flex;' }}">
                                    <div class="camera-icon-bubble">
                                        <i class="fas fa-camera"></i>
                                    </div>
                                    <div class="camera-upload-title">Samping Kiri</div>
                                    <div class="camera-upload-sub">Dinding / struktur sisi kiri</div>
                                    <button type="button" class="camera-upload-btn-fake" onclick="triggerPhotoInput('input_foto_sudut_kiri')">
                                        <i class="fas fa-camera"></i> Ambil / Pilih Foto
                                    </button>
                                </div>

                                {{-- State Tersimpan / Terpilih (2 Tombol Aksi) --}}
                                <div class="camera-uploaded-box" id="uploaded_foto_sudut_kiri" style="{{ $vervalData->foto_sudut_kiri ? 'display:flex;' : 'display:none;' }}">
                                    <div class="camera-icon-bubble success">
                                        <i class="fas fa-check-circle"></i>
                                    </div>
                                    <div class="camera-upload-title">Samping Kiri</div>
                                    <span class="camera-upload-badge"><i class="fas fa-file-image"></i> {{ $vervalData->foto_sudut_kiri ? 'Foto Tersimpan' : 'Foto Terpilih' }}</span>
                                    <div class="camera-actions-box">
                                        <button type="button" class="btn-photo-action view" onclick="openPhotoNewTab('foto_sudut_kiri')" title="Buka / Lihat Foto Samping Kiri">
                                            <i class="fas fa-eye"></i> Lihat
                                        </button>
                                        <button type="button" class="btn-photo-action delete" onclick="removePhoto('foto_sudut_kiri')" title="Hapus / Ganti Foto Samping Kiri">
                                            <i class="fas fa-trash-alt"></i> Hapus
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- 5. Samping Kanan --}}
                        <div>
                            <label style="font-weight:700;font-size:13px;display:block;margin-bottom:8px;">5. Samping Kanan</label>
                            <div class="camera-upload-card {{ $vervalData->foto_sudut_kanan ? 'has-image' : '' }}" id="card_foto_sudut_kanan">
                                <input type="file" id="input_foto_sudut_kanan" name="foto_sudut_kanan" class="camera-file-input" accept="image/*" onchange="previewPhoto(this, 'foto_sudut_kanan')">
                                <input type="hidden" id="url_foto_sudut_kanan" value="{{ $vervalData->foto_sudut_kanan ? url('/uploads/' . basename($vervalData->foto_sudut_kanan)) : '' }}">
                                
                                {{-- State Kosong --}}
                                <div class="camera-placeholder-box" id="placeholder_foto_sudut_kanan" style="{{ $vervalData->foto_sudut_kanan ? 'display:none;' : 'display:flex;' }}">
                                    <div class="camera-icon-bubble">
                                        <i class="fas fa-camera"></i>
                                    </div>
                                    <div class="camera-upload-title">Samping Kanan</div>
                                    <div class="camera-upload-sub">Dinding / struktur sisi kanan</div>
                                    <button type="button" class="camera-upload-btn-fake" onclick="triggerPhotoInput('input_foto_sudut_kanan')">
                                        <i class="fas fa-camera"></i> Ambil / Pilih Foto
                                    </button>
                                </div>

                                {{-- State Tersimpan / Terpilih (2 Tombol Aksi) --}}
                                <div class="camera-uploaded-box" id="uploaded_foto_sudut_kanan" style="{{ $vervalData->foto_sudut_kanan ? 'display:flex;' : 'display:none;' }}">
                                    <div class="camera-icon-bubble success">
                                        <i class="fas fa-check-circle"></i>
                                    </div>
                                    <div class="camera-upload-title">Samping Kanan</div>
                                    <span class="camera-upload-badge"><i class="fas fa-file-image"></i> {{ $vervalData->foto_sudut_kanan ? 'Foto Tersimpan' : 'Foto Terpilih' }}</span>
                                    <div class="camera-actions-box">
                                        <button type="button" class="btn-photo-action view" onclick="openPhotoNewTab('foto_sudut_kanan')" title="Buka / Lihat Foto Samping Kanan">
                                            <i class="fas fa-eye"></i> Lihat
                                        </button>
                                        <button type="button" class="btn-photo-action delete" onclick="removePhoto('foto_sudut_kanan')" title="Hapus / Ganti Foto Samping Kanan">
                                            <i class="fas fa-trash-alt"></i> Hapus
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Koordinat Lokasi Geotagging GPS (Auto-filled di Background dari GPS Petugas) --}}
                <input type="hidden" name="latitude" id="latitude" value="{{ old('latitude', $vervalData->latitude) }}">
                <input type="hidden" name="longitude" id="longitude" value="{{ old('longitude', $vervalData->longitude) }}">

                <!-- Tombol Submit Form -->
                <div class="survey-footer-actions">
                    <a href="{{ Auth::user() && Auth::user()->role === 'petugas' ? route('petugas.dashboard') : route('verval-data') }}" class="btn btn-outline">
                        <i class="fas fa-arrow-left"></i> Batal
                    </a>
                    <button type="submit" class="btn btn-primary">
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

        <!-- Modal Peringatan / Pengingat Kelengkapan Survei -->
        <div class="modal-overlay" id="surveyValidationModal">
            <div class="modal-box" style="max-width: 580px; padding: 0; overflow: hidden; border-radius: 16px;">
                <div class="modal-header" style="padding: 16px 22px; background: #fff1f2; border-bottom: 1px solid #ffe4e6; display: flex; align-items: center; justify-content: space-between;">
                    <h3 style="font-size: 16px; font-weight: 800; color: #e11d48; margin: 0; display: flex; align-items: center; gap: 10px;">
                        <i class="fas fa-triangle-exclamation" style="font-size: 18px;"></i>
                        <span>Form Survei Belum Lengkap!</span>
                    </h3>
                    <button type="button" style="background: transparent; border: none; font-size: 18px; color: #9f1239; cursor: pointer;" onclick="window.PuprModal.close('surveyValidationModal')">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <div class="modal-body" style="padding: 22px; max-height: 65vh; overflow-y: auto;">
                    <div style="display: flex; align-items: flex-start; gap: 12px; background: #fff; border: 1px solid #fee2e2; border-radius: 10px; padding: 12px 14px; margin-bottom: 18px;">
                        <i class="fas fa-circle-info" style="color: #e11d48; font-size: 16px; margin-top: 2px;"></i>
                        <p style="margin: 0; font-size: 13px; color: #475569; line-height: 1.5;">
                            Hasil survei lapangan belum dapat disimpan. Harap lengkapi <strong id="validationTotalCount" style="color:#e11d48;">0</strong> data / berkas yang masih kosong di bawah ini:
                        </p>
                    </div>

                    <div id="validationMissingList" style="display: flex; flex-direction: column; gap: 14px;">
                        <!-- Diisi secara otomatis lewat JavaScript -->
                    </div>
                </div>

                <div class="modal-footer" style="padding: 14px 22px; background: #f8fafc; border-top: 1px solid rgba(0, 40, 85, 0.08); display: flex; justify-content: space-between; align-items: center; gap: 10px;">
                    <button type="button" class="btn btn-outline" style="padding: 9px 18px; font-size: 12.5px;" onclick="window.PuprModal.close('surveyValidationModal')">
                        Tutup
                    </button>
                    <button type="button" class="btn btn-primary" style="padding: 9px 22px; font-size: 12.5px; background: #e11d48; border-color: #e11d48;" onclick="focusFirstInvalidField()">
                        <i class="fas fa-arrow-down"></i> Lengkapi Data Sekarang
                    </button>
                </div>
            </div>
        </div>

        <!-- Modal Konfirmasi Hapus Foto (Custom System Modal PUPR) -->
        <div class="modal-overlay" id="deletePhotoConfirmModal">
            <div class="modal-box" style="max-width: 440px;">
                <div class="modal-header" style="background: rgba(231, 76, 60, 0.08); border-bottom-color: rgba(231, 76, 60, 0.15); display: flex; align-items: center; justify-content: space-between;">
                    <h3 style="color: var(--danger, #e74c3c); display: flex; align-items: center; gap: 10px; font-size: 16px; margin: 0;">
                        <i class="fas fa-trash-alt"></i> Konfirmasi Hapus Foto
                    </h3>
                    <button class="close-btn" type="button" style="background:none;border:none;cursor:pointer;font-size:16px;color:var(--text-muted);" onclick="window.PuprModal.close('deletePhotoConfirmModal')">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <div class="modal-body" style="padding: 24px; text-align: center;">
                    <div style="width: 60px; height: 60px; border-radius: 50%; background: rgba(231, 76, 60, 0.1); color: var(--danger, #e74c3c); display: inline-flex; align-items: center; justify-content: center; font-size: 24px; margin: 0 auto 16px;">
                        <i class="fas fa-triangle-exclamation"></i>
                    </div>
                    <h4 style="font-size: 16px; font-weight: 800; color: var(--text-primary); margin-bottom: 8px;">
                        Apakah Anda yakin ingin menghapus foto ini?
                    </h4>
                    <p style="font-size: 13px; color: var(--text-muted); line-height: 1.5; margin: 0;">
                        Foto yang dihapus akan terhapus dari sistem. Anda perlu mengunggah ulang jika ingin menggantinya.
                    </p>
                </div>

                <div class="modal-footer" style="padding: 16px 20px; background: var(--bg-body); border-top: 1px solid rgba(0, 40, 85, 0.06); display: flex; gap: 10px; justify-content: flex-end;">
                    <button type="button" class="btn btn-outline" style="flex: 1; justify-content: center;" onclick="window.PuprModal.close('deletePhotoConfirmModal')">
                        <i class="fas fa-xmark"></i> Batal
                    </button>
                    <button type="button" class="btn btn-danger" style="flex: 1; justify-content: center; background: #dc2626; color: #fff; border: none; padding: 10px 16px; border-radius: var(--radius-sm); font-weight: 700; cursor: pointer;" onclick="executeDeletePhotoAjax()">
                        <i class="fas fa-trash-alt"></i> Ya, Hapus Foto
                    </button>
                </div>
            </div>
        </div>

        <!-- Modal Detail Profil Calon Penerima BSPS (Custom System Modal PUPR) -->
        <div class="modal-overlay" id="modalDetailProfil" onclick="if(event.target === this) closeDetailProfilModal()">
            <div class="modal-box" style="max-width: 680px; padding: 0; overflow: hidden; border-radius: 16px;">
                <div class="modal-header" style="background: var(--primary, #002855); color: #ffffff; padding: 18px 24px; display: flex; align-items: center; justify-content: space-between;">
                    <h3 style="color: #ffffff; display: flex; align-items: center; gap: 10px; font-size: 17px; margin: 0; font-weight: 800;">
                        <i class="fas fa-id-card-clip" style="color: var(--secondary, #ffb800);"></i> Detail Profil Calon Penerima BSPS
                    </h3>
                    <button class="close-btn" type="button" style="background: rgba(255,255,255,0.15); border: none; color: #ffffff; width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.2s;" onclick="closeDetailProfilModal()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <div class="modal-body" style="padding: 24px; max-height: 72vh; overflow-y: auto;">
                    <!-- Subheader Badge Banner -->
                    <div style="background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%); border: 1px solid #e2e8f0; border-radius: 12px; padding: 16px 20px; margin-bottom: 20px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px;">
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <div style="width: 44px; height: 44px; border-radius: 50%; background: var(--primary, #002855); color: #ffffff; display: flex; align-items: center; justify-content: center; font-size: 20px; font-weight: 900;">
                                {{ strtoupper(substr($vervalData->nama, 0, 1)) }}
                            </div>
                            <div>
                                <h4 style="margin: 0; font-size: 16px; font-weight: 800; color: var(--primary-dark, #001e40);">{{ $vervalData->nama }}</h4>
                                <span style="font-size: 12px; color: var(--text-muted); font-weight: 600;">
                                    <i class="fas fa-location-dot" style="color: var(--primary);"></i> Desa {{ $vervalData->desa_kelurahan }}, Kec. {{ $vervalData->kecamatan }}
                                </span>
                            </div>
                        </div>
                        <div>
                            <span style="font-size: 11.5px; font-weight: 800; padding: 5px 12px; border-radius: 20px; background: rgba(0, 40, 85, 0.08); color: var(--primary); display: inline-flex; align-items: center; gap: 6px;">
                                <i class="fas fa-layer-group"></i> {{ $vervalData->pengelompokan_desil ?: 'Desil 1-4' }}
                            </span>
                        </div>
                    </div>

                    <!-- 1. Identitas Kependudukan -->
                    <div style="margin-bottom: 20px;">
                        <h5 style="font-size: 13.5px; font-weight: 800; color: var(--primary); margin: 0 0 12px 0; border-bottom: 2px solid rgba(0,40,85,0.08); padding-bottom: 6px; display: flex; align-items: center; gap: 8px;">
                            <i class="fas fa-address-card"></i> Identitas Kependudukan
                        </h5>
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 12px; font-size: 13px;">
                            <div style="background: #f8fafc; padding: 10px 14px; border-radius: 8px; border: 1px solid #f1f5f9;">
                                <span style="color: var(--text-muted); font-size: 11px; display: block; font-weight: 600;">NIK (No. KTP)</span>
                                <strong style="color: var(--primary-dark); font-family: monospace; font-size: 13px;">{{ $vervalData->no_ktp ?: '-' }}</strong>
                            </div>
                            <div style="background: #f8fafc; padding: 10px 14px; border-radius: 8px; border: 1px solid #f1f5f9;">
                                <span style="color: var(--text-muted); font-size: 11px; display: block; font-weight: 600;">Nomor KK</span>
                                <strong style="color: var(--primary-dark); font-family: monospace; font-size: 13px;">{{ $vervalData->no_kk ?: '-' }}</strong>
                            </div>
                            <div style="background: #f8fafc; padding: 10px 14px; border-radius: 8px; border: 1px solid #f1f5f9;">
                                <span style="color: var(--text-muted); font-size: 11px; display: block; font-weight: 600;">Jenis Kelamin</span>
                                <strong>{{ $vervalData->jenis_kelamin == 'L' ? 'Laki-Laki (L)' : ($vervalData->jenis_kelamin == 'P' ? 'Perempuan (P)' : '-') }}</strong>
                            </div>
                            <div style="background: #f8fafc; padding: 10px 14px; border-radius: 8px; border: 1px solid #f1f5f9;">
                                <span style="color: var(--text-muted); font-size: 11px; display: block; font-weight: 600;">Tempat &amp; Tanggal Lahir</span>
                                <strong>{{ $vervalData->tempat_lahir ?: '-' }}, {{ $vervalData->tanggal_lahir ? \Carbon\Carbon::parse($vervalData->tanggal_lahir)->translatedFormat('d F Y') : '-' }}</strong>
                            </div>
                        </div>
                    </div>

                    <!-- 2. Alamat Domisili & Wilayah -->
                    <div style="margin-bottom: 20px;">
                        <h5 style="font-size: 13.5px; font-weight: 800; color: var(--primary); margin: 0 0 12px 0; border-bottom: 2px solid rgba(0,40,85,0.08); padding-bottom: 6px; display: flex; align-items: center; gap: 8px;">
                            <i class="fas fa-map-marked-alt"></i> Alamat Domisili &amp; Wilayah
                        </h5>
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 12px; font-size: 13px;">
                            <div style="background: #f8fafc; padding: 10px 14px; border-radius: 8px; border: 1px solid #f1f5f9; grid-column: 1 / -1;">
                                <span style="color: var(--text-muted); font-size: 11px; display: block; font-weight: 600;">Alamat Lengkap</span>
                                <strong>{{ $vervalData->alamat ?: '-' }}</strong>
                            </div>
                            <div style="background: #f8fafc; padding: 10px 14px; border-radius: 8px; border: 1px solid #f1f5f9;">
                                <span style="color: var(--text-muted); font-size: 11px; display: block; font-weight: 600;">Desa / Kelurahan</span>
                                <strong>{{ $vervalData->desa_kelurahan ?: '-' }}</strong>
                            </div>
                            <div style="background: #f8fafc; padding: 10px 14px; border-radius: 8px; border: 1px solid #f1f5f9;">
                                <span style="color: var(--text-muted); font-size: 11px; display: block; font-weight: 600;">Kecamatan</span>
                                <strong>Kec. {{ $vervalData->kecamatan ?: '-' }}</strong>
                            </div>
                            <div style="background: #f8fafc; padding: 10px 14px; border-radius: 8px; border: 1px solid #f1f5f9; grid-column: 1 / -1;">
                                <span style="color: var(--text-muted); font-size: 11px; display: block; font-weight: 600;">Geotagging GPS (Koordinat)</span>
                                @if($vervalData->latitude && $vervalData->longitude)
                                    <strong style="color: var(--success); font-family: monospace;">
                                        <i class="fas fa-crosshairs"></i> {{ $vervalData->latitude }}, {{ $vervalData->longitude }}
                                    </strong>
                                @else
                                    <span style="color: var(--text-muted); font-size: 12px;">Belum terdeteksi (Otomatis saat survei disimpan)</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- 3. Status Kepemilikan & Lahan -->
                    <div>
                        <h5 style="font-size: 13.5px; font-weight: 800; color: var(--primary); margin: 0 0 12px 0; border-bottom: 2px solid rgba(0,40,85,0.08); padding-bottom: 6px; display: flex; align-items: center; gap: 8px;">
                            <i class="fas fa-house-chimney-window"></i> Status Kepemilikan &amp; Lahan
                        </h5>
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 12px; font-size: 13px;">
                            <div style="background: #f8fafc; padding: 10px 14px; border-radius: 8px; border: 1px solid #f1f5f9;">
                                <span style="color: var(--text-muted); font-size: 11px; display: block; font-weight: 600;">Status Tanah</span>
                                <strong>{{ $vervalData->status_tanah ?: 'Belum Diisi' }}</strong>
                            </div>
                            <div style="background: #f8fafc; padding: 10px 14px; border-radius: 8px; border: 1px solid #f1f5f9;">
                                <span style="color: var(--text-muted); font-size: 11px; display: block; font-weight: 600;">Jenis Bukti Lahan</span>
                                <strong>{{ $vervalData->jenis_kepemilikan_lahan ?: 'Belum Diisi' }}</strong>
                            </div>
                            <div style="background: #f8fafc; padding: 10px 14px; border-radius: 8px; border: 1px solid #f1f5f9;">
                                <span style="color: var(--text-muted); font-size: 11px; display: block; font-weight: 600;">Luas Tanah</span>
                                <strong>{{ $vervalData->luas_tanah ?: 'Belum Diisi' }}</strong>
                            </div>
                            <div style="background: #f8fafc; padding: 10px 14px; border-radius: 8px; border: 1px solid #f1f5f9;">
                                <span style="color: var(--text-muted); font-size: 11px; display: block; font-weight: 600;">Penghasilan Per Bulan</span>
                                <strong>{{ $vervalData->penghasilan ?: 'Belum Diisi' }}</strong>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer" style="padding: 14px 22px; background: #f8fafc; border-top: 1px solid rgba(0, 40, 85, 0.06); display: flex; justify-content: flex-end;">
                    <button type="button" class="btn btn-primary" style="padding: 9px 20px; font-size: 13px; font-weight: 700; cursor: pointer;" onclick="closeDetailProfilModal()">
                        <i class="fas fa-check"></i> Tutup Detail
                    </button>
                </div>
            </div>
        </div>

        <!-- Modal Sukses Simpan Offline (PUPR Style) -->
        <div class="modal-overlay" id="modalSurveyOfflineSaved">
            <div class="modal-box" style="max-width: 480px; text-align: center;">
                <div class="modal-body" style="padding: 32px 24px;">
                    <div style="width: 70px; height: 70px; border-radius: 50%; background: rgba(39, 174, 96, 0.12); color: #16a34a; display: inline-flex; align-items: center; justify-content: center; font-size: 32px; margin: 0 auto 16px auto;">
                        <i class="fas fa-circle-check"></i>
                    </div>
                    <h3 style="font-size: 18px; font-weight: 800; color: var(--primary-dark); margin: 0 0 8px 0;">
                        Survei Tersimpan di HP (Mode Offline)
                    </h3>
                    <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 14px; margin: 16px 0; text-align: left;">
                        <div style="font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase;">Calon Penerima:</div>
                        <div style="font-size: 15px; font-weight: 800; color: var(--primary-dark); margin-top: 2px;">{{ $vervalData->nama }}</div>
                        <div style="display: flex; gap: 12px; margin-top: 6px; font-size: 12px; color: #475569;">
                            <span><i class="fas fa-id-card" style="color:var(--primary);"></i> NIK: <strong>{{ $vervalData->no_ktp }}</strong></span>
                        </div>
                        <div style="margin-top: 10px; padding-top: 8px; border-top: 1px dashed #cbd5e1; display: flex; align-items: center; justify-content: space-between;">
                            <span style="font-size: 11.5px; color: #64748b;">Status Sinkronisasi:</span>
                            <span style="background: rgba(255, 184, 0, 0.18); color: #b88600; padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 800; display: inline-flex; align-items: center; gap: 5px;">
                                <i class="fas fa-cloud-arrow-up"></i> Belum Sinkron (Offline)
                            </span>
                        </div>
                    </div>
                    <p style="font-size: 13px; color: var(--text-muted); line-height: 1.5; margin: 0 0 24px 0;">
                        Data formulir dan 8 foto terkompresi telah tersimpan di memori HP Anda. Begitu Anda kembali mendapatkan sinyal internet, data ini <strong>otomatis disinkronkan ke database server di latar belakang</strong>.
                    </p>
                    <div style="display: flex; flex-direction: column; gap: 10px;">
                        <a href="{{ route('petugas.sudah-survei') }}" class="btn btn-primary" style="padding: 12px; font-size: 13.5px; font-weight: 800; justify-content: center; text-decoration: none; border-radius: 8px; color: #fff;">
                            <i class="fas fa-clipboard-check"></i> Buka Daftar Sudah Survei
                        </a>
                        <a href="{{ route('petugas.dashboard') }}" class="btn btn-outline" style="padding: 10px; font-size: 13px; font-weight: 700; justify-content: center; text-decoration: none; border-radius: 8px; border: 1px solid #cbd5e1; color: var(--text-primary);">
                            <i class="fas fa-house"></i> Kembali ke Dashboard Petugas
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection

@push('scripts')
    <script>
        // Modal Detail Profil Trigger Helpers
        function openDetailProfilModal() {
            const modal = document.getElementById('modalDetailProfil');
            if (modal) {
                modal.classList.add('active');
                modal.style.display = 'flex';
                modal.style.opacity = '1';
                document.body.style.overflow = 'hidden';
            }
        }

        function closeDetailProfilModal() {
            const modal = document.getElementById('modalDetailProfil');
            if (modal) {
                modal.classList.remove('active');
                modal.style.display = 'none';
                modal.style.opacity = '0';
                document.body.style.overflow = '';
            }
        }

        let firstMissingElement = null;

        // Validasi Form Survei Lengkap Sebelum Disimpan
        function validateSurveyForm(e) {
            const missingSections = {
                '1. Data Tambahan & Kelaikan Hunian': [],
                '2. Berkas & Dokumen Administrasi': [],
                '3. Indikator Kelayakan RTLH (6 Kriteria)': [],
                '4. Dokumentasi Foto Fisik RTLH (5 Sudut)': []
            };

            firstMissingElement = null;

            // Bersihkan highlight invalid sebelumnya
            document.querySelectorAll('.is-invalid-highlight').forEach(el => el.classList.remove('is-invalid-highlight'));

            const registerMissing = (section, label, el) => {
                missingSections[section].push({ label: label, el: el });
                if (!firstMissingElement && el) {
                    firstMissingElement = el;
                }
                if (el) {
                    el.classList.add('is-invalid-highlight');
                }
            };

            // Section 1: Data Tambahan
            const tempatLahir = document.querySelector('input[name="tempat_lahir"]');
            if (!tempatLahir || !tempatLahir.value.trim()) registerMissing('1. Data Tambahan & Kelaikan Hunian', 'Tempat Lahir', tempatLahir);

            const tglLahir = document.querySelector('input[name="tanggal_lahir"]');
            if (!tglLahir || !tglLahir.value.trim()) registerMissing('1. Data Tambahan & Kelaikan Hunian', 'Tanggal Lahir', tglLahir);

            // Penghasilan per bulan bersifat opsional (dapat dikosongi)

            const luasTanah = document.querySelector('input[name="luas_tanah"]');
            if (!luasTanah || !luasTanah.value.trim()) registerMissing('1. Data Tambahan & Kelaikan Hunian', 'Luas Tanah', luasTanah);

            const lamaMenempati = document.querySelector('input[name="telah_ditempati_selama"]');
            if (!lamaMenempati || !lamaMenempati.value.trim()) registerMissing('1. Data Tambahan & Kelaikan Hunian', 'Telah Ditempati Selama', lamaMenempati);

            const statusTanah = document.getElementById('inputStatusTanah');
            const ddStatusTanah = document.getElementById('ddStatusTanahWrapper');
            if (!statusTanah || !statusTanah.value.trim()) registerMissing('1. Data Tambahan & Kelaikan Hunian', 'Status Kepemilikan Tanah', ddStatusTanah);

            // Section 2: Berkas & Dokumen
            const urlKtp = document.getElementById('url_ktp');
            const cardKtp = document.getElementById('card_ktp');
            if (!urlKtp || !urlKtp.value.trim()) registerMissing('2. Berkas & Dokumen Administrasi', 'Foto / Scan KTP', cardKtp);

            const urlKk = document.getElementById('url_kk');
            const cardKk = document.getElementById('card_kk');
            if (!urlKk || !urlKk.value.trim()) registerMissing('2. Berkas & Dokumen Administrasi', 'Foto / Scan Kartu Keluarga', cardKk);

            const urlSertifikat = document.getElementById('url_sertifikat_tanah');
            const cardSertifikat = document.getElementById('card_sertifikat_tanah');
            if (!urlSertifikat || !urlSertifikat.value.trim()) registerMissing('2. Berkas & Dokumen Administrasi', 'Foto Sertipikat / Bukti Tanah', cardSertifikat);

            const jenisKepemilikan = document.getElementById('inputKepemilikan');
            const ddKepemilikan = document.getElementById('ddKepemilikanWrapper');
            if (!jenisKepemilikan || !jenisKepemilikan.value.trim()) registerMissing('2. Berkas & Dokumen Administrasi', 'Jenis Bukti Kepemilikan Lahan', ddKepemilikan);

            // Section 3: Indikator Kelayakan RTLH (6 Kriteria)
            const indicators = [
                { name: 'indikator_lantai', label: 'Indikator 1: Lantai Keramik (Pilih Ada / Tidak Ada)' },
                { name: 'indikator_pondasi', label: 'Indikator 2: Pondasi Bangunan (Pilih Ada / Tidak Ada)' },
                { name: 'indikator_dinding', label: 'Indikator 3: Dinding Bata/Tembok (Pilih Ada / Tidak Ada)' },
                { name: 'indikator_struktur', label: 'Indikator 4: Struktur Bangunan (Pilih Ada / Tidak Ada)' },
                { name: 'indikator_atap', label: 'Indikator 5: Penutup Atap Genteng (Pilih Ada / Tidak Ada)' },
                { name: 'indikator_penghasilan', label: 'Indikator 6: Penghasilan Kurang dari UMK (Pilih Ya / Tidak)' }
            ];

            indicators.forEach(ind => {
                const checked = document.querySelector(`input[name="${ind.name}"]:checked`);
                const row = document.getElementById('row_' + ind.name);
                if (!checked) {
                    registerMissing('3. Indikator Kelayakan RTLH (6 Kriteria)', ind.label, row);
                }
            });

            // Section 4: 5 Sudut Foto Fisik
            const housePhotos = [
                { field: 'foto_sudut_depan', label: 'Foto Fisik 1: Tampak Depan' },
                { field: 'foto_sudut_belakang', label: 'Foto Fisik 2: Tampak Belakang' },
                { field: 'foto_bagian_dalam', label: 'Foto Fisik 3: Bagian Dalam / Interior' },
                { field: 'foto_sudut_kiri', label: 'Foto Fisik 4: Samping Kiri' },
                { field: 'foto_sudut_kanan', label: 'Foto Fisik 5: Samping Kanan' }
            ];

            housePhotos.forEach(p => {
                const urlEl = document.getElementById('url_' + p.field);
                const cardEl = document.getElementById('card_' + p.field);
                if (!urlEl || !urlEl.value.trim()) {
                    registerMissing('4. Dokumentasi Foto Fisik RTLH (5 Sudut)', p.label, cardEl);
                }
            });

            // Hitung total item yang belum diisi
            let totalMissing = 0;
            for (const section in missingSections) {
                totalMissing += missingSections[section].length;
            }

            if (totalMissing > 0) {
                if (e) e.preventDefault();

                // Tampilkan daftar ke modal pengingat
                const container = document.getElementById('validationMissingList');
                const totalCountEl = document.getElementById('validationTotalCount');
                if (totalCountEl) totalCountEl.textContent = totalMissing;

                if (container) {
                    let html = '';
                    for (const section in missingSections) {
                        if (missingSections[section].length > 0) {
                            html += `
                                <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 12px 16px;">
                                    <div style="font-size: 13px; font-weight: 800; color: var(--primary); margin-bottom: 8px; display: flex; align-items: center; gap: 8px;">
                                        <i class="fas fa-folder-open"></i> ${section}
                                    </div>
                                    <ul style="margin: 0; padding-left: 18px; font-size: 12.5px; color: #e11d48; display: flex; flex-direction: column; gap: 4px;">
                                        ${missingSections[section].map(item => `<li><strong style="color: #334155;">${item.label}</strong> <span style="font-size:11px; color:#e11d48;">(Belum Diisi/Diunggah)</span></li>`).join('')}
                                    </ul>
                                </div>
                            `;
                        }
                    }
                    container.innerHTML = html;
                }

                if (window.PuprModal) {
                    window.PuprModal.open('surveyValidationModal');
                }
                return false;
            }

            // JIKA SEDANG OFFLINE (SIMPAN KE MEMORI HP / INDEXEDDB)
            if (!navigator.onLine) {
                if (e) e.preventDefault();
                if (window.PuprLoading) {
                    window.PuprLoading.show('Menyimpan Data Survei ke Memori HP...');
                }

                const form = document.getElementById('surveyForm');
                const formData = new FormData(form);
                const fields = {};
                for (const [k, v] of formData.entries()) {
                    if (!k.startsWith('foto_') && k !== 'ktp' && k !== 'kk' && k !== 'sertifikat_tanah') {
                        fields[k] = v;
                    }
                }

                const photos = {
                    'ktp': document.getElementById('url_ktp')?.value || '',
                    'kk': document.getElementById('url_kk')?.value || '',
                    'sertifikat_tanah': document.getElementById('url_sertifikat_tanah')?.value || '',
                    'foto_sudut_depan': document.getElementById('url_foto_sudut_depan')?.value || '',
                    'foto_sudut_belakang': document.getElementById('url_foto_sudut_belakang')?.value || '',
                    'foto_bagian_dalam': document.getElementById('url_foto_bagian_dalam')?.value || '',
                    'foto_sudut_kiri': document.getElementById('url_foto_sudut_kiri')?.value || '',
                    'foto_sudut_kanan': document.getElementById('url_foto_sudut_kanan')?.value || '',
                };

                const surveyData = {
                    id: {{ $vervalData->id }},
                    nama: '{{ addslashes($vervalData->nama) }}',
                    nik: '{{ addslashes($vervalData->no_ktp) }}',
                    desa: '{{ addslashes($vervalData->desa_kelurahan) }}',
                    fields: fields,
                    photos: photos
                };

                if (window.BspsOffline && window.BspsOffline.saveSurveyToIndexedDB) {
                    window.BspsOffline.saveSurveyToIndexedDB(surveyData).then(() => {
                        if (window.PuprLoading) window.PuprLoading.hide();
                        if (window.BspsOffline && window.BspsOffline.showPuprToast) {
                            window.BspsOffline.showPuprToast('Data survei & foto berhasil disimpan di HP (Offline)!', 'success');
                        }
                        setTimeout(() => {
                            window.location.href = '{{ route("petugas.sudah-survei") }}';
                        }, 800);
                    });
                } else {
                    if (window.PuprLoading) window.PuprLoading.hide();
                    alert('Data survei berhasil disimpan di HP!');
                    window.location.href = '{{ route("petugas.sudah-survei") }}';
                }
                return false;
            }

            return true;
        }

        // Fokus ke Elemen Pertama yang Masih Kosong
        function focusFirstInvalidField() {
            if (window.PuprModal) {
                window.PuprModal.close('surveyValidationModal');
            }
            if (firstMissingElement) {
                setTimeout(() => {
                    firstMissingElement.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    if (firstMissingElement.focus && typeof firstMissingElement.focus === 'function') {
                        firstMissingElement.focus();
                    }
                }, 300);
            }
        }

        // =====================================================================
        // Auto Compress + Live Preview Handler (Canvas API - Tanpa Library)
        // =====================================================================
        const COMPRESS_MAX_PX  = 1200;   // panjang sisi max setelah resize (px)
        const COMPRESS_QUALITY = 0.72;   // kualitas JPEG output (0.0 – 1.0)
        const COMPRESS_MAX_KB  = 600;    // batas aman target ukuran file (KB)

        function formatKB(bytes) {
            return bytes < 1024 * 1024
                ? (bytes / 1024).toFixed(0) + ' KB'
                : (bytes / 1024 / 1024).toFixed(1) + ' MB';
        }

        function showCompressInfo(card, origBytes, compBytes) {
            let badge = card.querySelector('.compress-info-badge');
            if (!badge) {
                badge = document.createElement('span');
                badge.className = 'compress-info-badge';
                badge.style.cssText = 'position:absolute;bottom:8px;left:8px;font-size:10px;font-weight:700;background:rgba(0,0,0,0.55);color:#fff;padding:2px 7px;border-radius:20px;z-index:10;';
                card.style.position = 'relative';
                card.appendChild(badge);
            }
            const ratio = Math.round((1 - compBytes / origBytes) * 100);
            badge.innerHTML = `<i class="fas fa-compress-alt"></i> ${formatKB(origBytes)} → ${formatKB(compBytes)} (−${ratio}%)`;
        }

        function compressAndPreview(input, imgPreviewId, cardId) {
            if (!input.files || !input.files[0]) return;

            const file = input.files[0];
            const origSize = file.size;
            const card  = document.getElementById(cardId);
            const imgEl = document.getElementById(imgPreviewId);

            // Tampilkan spinner sementara kompresi berjalan
            if (card) {
                const bubble = card.querySelector('.camera-icon-bubble');
                if (bubble) bubble.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            }

            const reader = new FileReader();
            reader.onload = function(e) {
                const image = new Image();
                image.onload = function() {
                    // Hitung dimensi baru (max COMPRESS_MAX_PX, pertahankan rasio)
                    let w = image.width;
                    let h = image.height;
                    if (w > COMPRESS_MAX_PX || h > COMPRESS_MAX_PX) {
                        if (w >= h) { h = Math.round(h * COMPRESS_MAX_PX / w); w = COMPRESS_MAX_PX; }
                        else        { w = Math.round(w * COMPRESS_MAX_PX / h); h = COMPRESS_MAX_PX; }
                    }

                    // Gambar ke Canvas lalu export JPEG
                    const canvas = document.createElement('canvas');
                    canvas.width  = w;
                    canvas.height = h;
                    const ctx = canvas.getContext('2d');
                    ctx.drawImage(image, 0, 0, w, h);

                    canvas.toBlob(function(blob) {
                        if (!blob) return;

                        // Ganti file pada input dengan Blob terkompresi
                        const compressedFile = new File(
                            [blob],
                            file.name.replace(/\.[^/.]+$/, '') + '.jpg',
                            { type: 'image/jpeg', lastModified: Date.now() }
                        );
                        const dt = new DataTransfer();
                        dt.items.add(compressedFile);
                        input.files = dt.files;

                        // Update preview gambar
                        const blobUrl = URL.createObjectURL(blob);
                        if (imgEl) {
                            imgEl.src = blobUrl;
                            imgEl.style.display = 'block';
                        }

                        // Perbarui tampilan card
                        if (card) {
                            card.classList.add('has-image');
                            const bubble = card.querySelector('.camera-icon-bubble');
                            if (bubble) { bubble.style.display = 'none'; bubble.innerHTML = '<i class="fas fa-camera"></i>'; }
                            const badge = card.querySelector('.camera-upload-badge');
                            if (badge) badge.style.display = 'inline-flex';
                            const fakebtn = card.querySelector('.camera-upload-btn-fake');
                            if (fakebtn) fakebtn.style.display = 'none';
                            showCompressInfo(card, origSize, blob.size);
                        }
                    }, 'image/jpeg', COMPRESS_QUALITY);
                };
                image.src = e.target.result;
            };
            reader.readAsDataURL(file);
        }

        // Alias agar form lama yang pakai previewCameraPhoto() tetap berfungsi
        function previewCameraPhoto(input, imgPreviewId, cardId) {
            compressAndPreview(input, imgPreviewId, cardId);
        }

        // Trigger Click Hidden File Input
        function triggerPhotoInput(inputId) {
            const input = document.getElementById(inputId);
            if (input) input.click();
        }

        // Pratinjau Foto Lokal Instan (Tanpa AJAX, Tanpa Auto-Save yang Mengunci Server)
        function previewPhoto(input, field) {
            if (!input.files || !input.files[0]) return;
            const file = input.files[0];

            const placeholder = document.getElementById('placeholder_' + field);
            const uploadedBox = document.getElementById('uploaded_' + field);
            const card = placeholder ? placeholder.closest('.camera-upload-card') : null;
            const urlInput = document.getElementById('url_' + field);

            // Baca via FileReader lokal di HP (0.001 detik)
            const reader = new FileReader();
            reader.onload = function(e) {
                if (urlInput) urlInput.value = e.target.result;
                if (placeholder) placeholder.style.display = 'none';
                if (uploadedBox) uploadedBox.style.display = 'flex';
                if (card) {
                    card.classList.add('has-image');
                    card.classList.remove('is-invalid-highlight');
                    const badge = card.querySelector('.camera-upload-badge');
                    if (badge) {
                        const sizeKb = Math.round(file.size / 1024);
                        badge.innerHTML = `<i class="fas fa-check-circle"></i> Foto Terpilih (${sizeKb} KB)`;
                    }
                }
            };
            reader.readAsDataURL(file);
        }

        // Hapus / Ganti Foto Terpilih
        function removePhoto(field) {
            const input = document.getElementById('input_' + field);
            const urlInput = document.getElementById('url_' + field);
            const placeholder = document.getElementById('placeholder_' + field);
            const uploadedBox = document.getElementById('uploaded_' + field);
            const card = placeholder ? placeholder.closest('.camera-upload-card') : null;

            if (input) input.value = '';
            if (urlInput) urlInput.value = '';
            if (placeholder) placeholder.style.display = 'flex';
            if (uploadedBox) uploadedBox.style.display = 'none';
            if (card) card.classList.remove('has-image');
        }

        // Buka Foto di Tab Baru
        function openPhotoNewTab(field) {
            const urlInput = document.getElementById('url_' + field);
            if (urlInput && urlInput.value) {
                if (urlInput.value.startsWith('data:image')) {
                    const win = window.open();
                    win.document.write('<iframe src="' + urlInput.value  + '" frameborder="0" style="border:0; top:0px; left:0px; bottom:0px; right:0px; width:100%; height:100%;" allowfullscreen></iframe>');
                } else {
                    window.open(urlInput.value, '_blank');
                }
            } else {
                alert('Foto belum tersedia.');
            }
        }

        // GPS Geolocation Handler (Auto-fill hidden coordinates)
        function requestLocation() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function (position) {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;
                    const latInput = document.getElementById('latitude');
                    const lngInput = document.getElementById('longitude');

                    if (latInput) latInput.value = lat;
                    if (lngInput) lngInput.value = lng;

                    if (window.PuprModal) {
                        window.PuprModal.close('gpsModal');
                    }
                }, function (error) {
                    console.warn('GPS location request error:', error);
                }, {
                    enableHighAccuracy: true,
                    timeout: 10000,
                    maximumAge: 0
                });
            }
        }

        // Recount & Update Status Kelayakan Usulan (Kriteria RTLH Terpenuhi)
        function updateIndicatorsStatus() {
            let countRtlh = 0;
            const indicatorNames = [
                'indikator_lantai',
                'indikator_pondasi',
                'indikator_dinding',
                'indikator_struktur',
                'indikator_atap',
                'indikator_penghasilan'
            ];

            const checkRtlhDefect = (groupName, rtlhValue) => {
                const checkedRadio = document.querySelector(`input[name="${groupName}"]:checked`);
                const row = document.getElementById('row_' + groupName);

                if (!checkedRadio) {
                    if (row) {
                        row.style.background = '#f8fafc';
                        row.style.borderColor = '#cbd5e1';
                    }
                    return;
                }

                if (row) {
                    row.classList.remove('is-invalid-highlight');
                }

                if (checkedRadio.value === rtlhValue) {
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

            const answeredCount = indicatorNames.filter(name => document.querySelector(`input[name="${name}"]:checked`)).length;

            const countEl = document.getElementById('indicatorCountDisplay');
            if (countEl) countEl.textContent = countRtlh;

            const badgeEl = document.getElementById('statusBadgeDisplay');
            if (badgeEl) {
                if (countRtlh >= 2) {
                    badgeEl.style.background = '#dcfce7';
                    badgeEl.style.color = '#15803d';
                    badgeEl.style.border = '1px solid #86efac';
                    badgeEl.innerHTML = '<i class="fas fa-check-circle"></i> LAYAK DIUSULKAN';
                } else if (answeredCount === 0) {
                    badgeEl.style.background = '#f1f5f9';
                    badgeEl.style.color = '#64748b';
                    badgeEl.style.border = '1px solid #cbd5e1';
                    badgeEl.innerHTML = '<i class="fas fa-hourglass-start"></i> BELUM DISURVEI';
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

            // Clear invalid highlight when typing or selecting dropdown
            document.querySelectorAll('input, select').forEach(input => {
                input.addEventListener('input', function() {
                    this.classList.remove('is-invalid-highlight');
                });
                input.addEventListener('change', function() {
                    this.classList.remove('is-invalid-highlight');
                    const wrapper = this.closest('.pupr-dropdown-wrapper');
                    if (wrapper) wrapper.classList.remove('is-invalid-highlight');
                });
            });

        });

        @if(auth()->check() && auth()->user()->isAdminKecamatan())
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('input, select, textarea, button[type="submit"]').forEach(function(el) {
                el.disabled = true;
            });
            document.querySelectorAll('.upload-btn-group, .btn-upload, .delete-photo-btn, .file-input-drop, .btn-submit-survey').forEach(function(btn) {
                btn.style.display = 'none';
            });
        });
        @endif
    </script>
@endpush
