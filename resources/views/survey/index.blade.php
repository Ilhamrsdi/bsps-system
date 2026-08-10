@extends('layouts.partial.app')

@section('title', 'BSPS Verval - Form Survei Lapangan')
@section('title_header', 'Form Survei Lapangan BSPS')
@section('subtitle_header', 'Input Data Verifikasi Fisik RTLH, Koordinat GPS, dan Penilaian Kelaikan Hunian Rumah Swadaya')

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
        margin-bottom: 20px;
        padding-bottom: 12px;
        border-bottom: 2px solid rgba(0, 40, 85, 0.08);
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-group label {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 13px;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 8px !important;
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
        font-size: 13.5px;
        border-radius: var(--radius-sm);
        border: 1px solid rgba(0, 40, 85, 0.16) !important;
        background: var(--bg-card) !important;
        color: var(--text-primary);
        transition: all 0.2s ease;
        box-sizing: border-box;
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
        gap: 20px;
    }

    .form-grid-3 {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 20px;
    }

    /* Checklist RTLH Card */
    .checklist-item {
        background: var(--bg-body);
        border-radius: var(--radius-sm);
        padding: 18px 20px;
        margin-bottom: 16px;
        border: 1px solid rgba(0, 40, 85, 0.08);
    }

    .checklist-item-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 14px;
    }

    .checklist-item-title {
        font-size: 14px;
        font-weight: 700;
        color: var(--primary-dark);
    }

    .checklist-item-desc {
        font-size: 12px;
        color: var(--text-muted);
        margin-top: 2px;
    }

    .checklist-toggle-group {
        display: flex;
        gap: 8px;
    }

    .checklist-radio-label {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 14px;
        border-radius: 20px;
        border: 1px solid rgba(0, 40, 85, 0.15);
        font-size: 12px;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.2s ease;
        background: var(--bg-card);
        color: var(--text-muted);
    }

    .checklist-radio-label input {
        display: none;
    }

    .checklist-radio-label.sesuai.active,
    .checklist-radio-label.sesuai:has(input:checked) {
        background: #27ae60 !important;
        border-color: #27ae60 !important;
        color: #ffffff !important;
        box-shadow: 0 4px 12px rgba(39, 174, 96, 0.35);
    }

    .checklist-radio-label.tidak.active,
    .checklist-radio-label.tidak:has(input:checked) {
        background: #e74c3c !important;
        border-color: #e74c3c !important;
        color: #ffffff !important;
        box-shadow: 0 4px 12px rgba(231, 76, 60, 0.35);
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

    .checklist-foto-box:hover {
        border-color: var(--primary);
        background: rgba(0, 40, 85, 0.03);
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
    }

    .checklist-foto-box input[type="file"] {
        position: absolute;
        inset: 0;
        opacity: 0;
        cursor: pointer;
    }

    @media (max-width: 768px) {
        .survey-card { padding: 18px 14px; }
        .form-grid-2, .form-grid-3, .checklist-foto-row { grid-template-columns: 1fr; }
        .checklist-item-header { flex-direction: column; align-items: stretch; gap: 10px; }
        .checklist-toggle-group { width: 100%; display: flex; }
        .checklist-radio-label { flex: 1; justify-content: center; }
    }
</style>
@endpush

@php
    $selNamaKegiatan = isset($selectedKegiatan) ? $selectedKegiatan->nama_kegiatan : 'Verval Calon Penerima Bantuan BSPS - Bpk. Slamet Riyadi';
    $selPemohon = isset($selectedKegiatan) ? $selectedKegiatan->nama_pemohon : 'Bpk. Slamet Riyadi';
    $selNik = isset($selectedKegiatan) ? $selectedKegiatan->nik_pemohon : '3509191204850001';
    $selAlamat = isset($selectedKegiatan) ? $selectedKegiatan->alamat : 'Jl. Hayam Wuruk No. 45, RT 02/RW 05, Kel. Sempusari';
    $selKecamatan = isset($selectedKegiatan) ? $selectedKegiatan->lokasi : 'Kaliwates';

    $p1 = 'Ahmad Fauzi (TFL BSPS)';
    $p2 = 'Budi Pratama (Fasilitator)';
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

        <!-- Banner Info Mode Dummy & Cek Lapangan -->
        <div style="padding:16px 20px;border-radius:var(--radius);background:linear-gradient(135deg, rgba(0,40,85,0.08) 0%, rgba(255,184,0,0.12) 100%);border:1px solid rgba(0,40,85,0.12);color:var(--primary-dark);margin-bottom:24px;display:flex;align-items:center;justify-content:space-between;gap:14px;flex-wrap:wrap;">
            <div style="display:flex;align-items:center;gap:12px;">
                <div style="width:40px;height:40px;border-radius:50%;background:var(--primary);color:#fff;display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0;">
                    <i class="fas fa-house-chimney-crack"></i>
                </div>
                <div>
                    <h4 style="font-size:15px;font-weight:800;margin:0 0 2px 0;">Mode Survei Verval Lapangan (Dummy Aktif)</h4>
                    <p style="font-size:12.5px;color:var(--text-muted);margin:0;">Form ini telah dimuat dengan data dummy verifikasi fisik RTLH calon penerima bantuan BSPS untuk kemudahan demonstrasi.</p>
                </div>
            </div>
            <div style="display:flex;gap:10px;">
                <button type="button" class="btn btn-outline" onclick="isiDataDummyOtomatis()" style="padding:8px 16px;font-size:12.5px;font-weight:700;">
                    <i class="fas fa-magic"></i> Muat Ulang Dummy
                </button>
            </div>
        </div>

        <form action="{{ url('/survey') }}" method="POST" id="formSurveyUtama" enctype="multipart/form-data">
            @csrf

            <!-- 1. Data Usulan & Fasilitator Lapangan (TFL) -->
            <div class="survey-card">
                <div class="survey-section-title">
                    <i class="fas fa-user-hard-hat"></i> 1. Data Usulan BSPS &amp; Tim Fasilitator Lapangan (TFL)
                </div>
                <div class="form-grid-2">
                    <div class="form-group">
                        <label><i class="fas fa-list-check"></i> Pilih Usulan Calon Penerima Bantuan</label>
                        <select name="kegiatan_id" id="selectKegiatan" class="form-control" onchange="window.location='{{ url('/survey?kegiatan_id=') }}' + this.value">
                            @foreach($kegiatans as $k)
                                <option value="{{ $k->id }}" {{ (isset($selectedKegiatan) && $selectedKegiatan->id == $k->id) ? 'selected' : '' }}>
                                    {{ $k->nama_kegiatan }} (Kec. {{ $k->lokasi }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-calendar"></i> Tanggal Pelaksanaan Survei Lapangan <span style="color:var(--danger);">*</span></label>
                        <input type="date" name="tanggal_survei" id="inputTanggal" value="{{ old('tanggal_survei', date('Y-m-d')) }}" required />
                    </div>
                </div>

                <div class="form-grid-2">
                    <div class="form-group">
                        <label><i class="fas fa-user"></i> Tenaga Fasilitator Lapangan 1 (TFL) <span style="color:var(--danger);">*</span></label>
                        <input type="text" name="nama_petugas_1" id="inputPetugas1" value="{{ old('nama_petugas_1', $p1) }}" placeholder="Nama TFL 1..." required />
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-user-tie"></i> Koordinator / Fasilitator 2</label>
                        <input type="text" name="nama_petugas_2" id="inputPetugas2" value="{{ old('nama_petugas_2', $p2) }}" placeholder="Nama Pendamping / TFL 2..." />
                    </div>
                </div>
            </div>

            <!-- 2. Data Calon Penerima Bantuan (Kepala Keluarga) -->
            <div class="survey-card">
                <div class="survey-section-title">
                    <i class="fas fa-users"></i> 2. Data Calon Penerima Bantuan (Kepala Keluarga)
                </div>
                <div class="form-grid-3">
                    <div class="form-group">
                        <label><i class="fas fa-user"></i> Nama Kepala Keluarga <span style="color:var(--danger);">*</span></label>
                        <input type="text" name="nama_pemohon" id="inputPemohon" value="{{ old('nama_pemohon', $selPemohon) }}" placeholder="Nama lengkap..." required />
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-id-card"></i> NIK (Nomor Induk Kependudukan) <span style="color:var(--danger);">*</span></label>
                        <input type="text" name="nik_pemohon" id="inputNik" value="{{ old('nik_pemohon', $selNik) }}" placeholder="16 digit NIK..." maxlength="16" required />
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-address-card"></i> No. Kartu Keluarga (KK)</label>
                        <input type="text" name="no_kk" id="inputNoKk" value="{{ old('no_kk', '3509191204050012') }}" placeholder="16 digit No KK..." maxlength="16" />
                    </div>
                </div>

                <div class="form-grid-2">
                    <div class="form-group">
                        <label><i class="fas fa-people-roof"></i> Jumlah Anggota Keluarga (Jiwa)</label>
                        <input type="number" name="jumlah_jiwa" id="inputJumlahJiwa" value="{{ old('jumlah_jiwa', 4) }}" min="1" max="20" placeholder="Contoh: 4" />
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-money-bill-wave"></i> Estimasi Penghasilan Bulanan (Kategori MBR)</label>
                        <input type="text" name="penghasilan" id="inputPenghasilan" value="{{ old('penghasilan', 'Rp 1.500.000 / bulan (MBR)') }}" placeholder="Contoh: Rp 1.500.000 (MBR)" />
                    </div>
                </div>

                <div class="form-group">
                    <label><i class="fas fa-location-dot"></i> Alamat Domisili Calon Penerima <span style="color:var(--danger);">*</span></label>
                    <input type="text" name="alamat_pemohon" id="inputAlamat" value="{{ old('alamat_pemohon', $selAlamat) }}" placeholder="Dusun, RT/RW, Desa, Kecamatan..." required />
                </div>
            </div>

            <!-- 3. Data Rumah & Legalitas Tanah -->
            <div class="survey-card">
                <div class="survey-section-title">
                    <i class="fas fa-house-laptop"></i> 3. Data Fisik Rumah Eksisting &amp; Legalitas Tanah
                </div>
                <div class="form-grid-3">
                    <div class="form-group">
                        <label><i class="fas fa-file-contract"></i> Status Kepemilikan Tanah <span style="color:var(--danger);">*</span></label>
                        <select name="status_hak_tanah" id="selectStatusTanah" class="form-control">
                            <option value="Hak Milik (Sertifikat / SHM)" selected>Hak Milik (Sertifikat / SHM)</option>
                            <option value="Surat Keterangan Tanah Desa (Petok/Letter C)">Surat Keterangan Tanah Desa (Petok/Letter C)</option>
                            <option value="Surat Hibah / Waris Sah">Surat Hibah / Waris Sah</option>
                            <option value="Hak Guna Bangunan (HGB)">Hak Guna Bangunan (HGB)</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-expand"></i> Luas Bangunan Eksisting (m²)</label>
                        <input type="number" name="luas_bangunan" id="inputLuasBangunan" value="{{ old('luas_bangunan', 36) }}" placeholder="Contoh: 36" />
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-vector-square"></i> Luas Lahan / Tanah (m²)</label>
                        <input type="number" name="luas_tanah" id="inputLuasTanah" value="{{ old('luas_tanah', 72) }}" placeholder="Contoh: 72" />
                    </div>
                </div>
            </div>

            <!-- 4. Lokasi & Titik Koordinat GPS -->
            <div class="survey-card">
                <div class="survey-section-title">
                    <i class="fas fa-map-location-dot"></i> 4. Titik Geotagging &amp; Koordinat GPS Rumah
                </div>
                <div class="form-grid-3">
                    <div class="form-group">
                        <label><i class="fas fa-map-pin"></i> Kecamatan <span style="color:var(--danger);">*</span></label>
                        <input type="text" name="kecamatan" id="inputKecamatan" value="{{ old('kecamatan', $selKecamatan) }}" required />
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-city"></i> Desa / Kelurahan <span style="color:var(--danger);">*</span></label>
                        <input type="text" name="desa_kelurahan" id="inputDesa" value="{{ old('desa_kelurahan', 'Sempusari') }}" required />
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-road"></i> Nama Ruas / Gang</label>
                        <input type="text" name="nama_jalan" id="inputJalan" value="{{ old('nama_jalan', 'Jl. Hayam Wuruk Gg. Mawar No. 12') }}" />
                    </div>
                </div>
                <div class="form-grid-2">
                    <div class="form-group">
                        <label><i class="fas fa-compass"></i> Latitude GPS <span style="color:var(--danger);">*</span></label>
                        <input type="text" id="gpsLat" name="latitude" value="{{ old('latitude', '-8.1721') }}" required />
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-compass"></i> Longitude GPS <span style="color:var(--danger);">*</span></label>
                        <div style="display:flex;gap:10px;">
                            <input type="text" id="gpsLng" name="longitude" value="{{ old('longitude', '113.6997') }}" required style="flex:1;" />
                            <button type="button" class="btn btn-outline" id="btnAutoGps" style="padding:10px 16px;white-space:nowrap;">
                                <i class="fas fa-crosshairs"></i> Deteksi GPS
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 5. Daftar Simak Pemeriksaan Fisik RTLH -->
            <div class="survey-card">
                <div class="survey-section-title">
                    <i class="fas fa-clipboard-check"></i> 5. Pemeriksaan Komponen Fisik RTLH (Daftar Simak Lapangan)
                </div>

                <!-- Item 1: Kelengkapan Administrasi & Lahan -->
                <div class="checklist-item">
                    <div class="checklist-item-header">
                        <div>
                            <div class="checklist-item-title">1. Kelengkapan Berkas &amp; Legalitas Tanah</div>
                            <div class="checklist-item-desc">Memiliki KTP/KK domisili setempat, bukti kepemilikan tanah tidak dalam sengketa, dan belum pernah menerima bantuan perumahan.</div>
                        </div>
                        <div class="checklist-toggle-group">
                            <label class="checklist-radio-label sesuai active">
                                <input type="radio" name="item_admin" value="sesuai" checked />
                                <i class="fas fa-check-circle"></i> Memenuhi Syarat
                            </label>
                            <label class="checklist-radio-label tidak">
                                <input type="radio" name="item_admin" value="tidak" />
                                <i class="fas fa-times-circle"></i> Tidak Memenuhi
                            </label>
                        </div>
                    </div>
                    <div class="checklist-foto-row">
                        <div class="checklist-foto-box has-file">
                            <i class="fas fa-image" style="color:#27ae60;"></i>
                            <div class="title">Foto KTP / KK Pemohon</div>
                            <div class="subtitle" style="color:#27ae60;font-weight:700;">foto_ktp_terlampir.jpg</div>
                            <input type="file" name="foto_admin_1" accept="image/*" />
                        </div>
                        <div class="checklist-foto-box has-file">
                            <i class="fas fa-image" style="color:#27ae60;"></i>
                            <div class="title">Foto Bukti Kepemilikan Tanah</div>
                            <div class="subtitle" style="color:#27ae60;font-weight:700;">surat_tanah.jpg</div>
                            <input type="file" name="foto_admin_2" accept="image/*" />
                        </div>
                        <div class="checklist-foto-box">
                            <i class="fas fa-camera"></i>
                            <div class="title">Dokumen Pendukung Lainnya</div>
                            <div class="subtitle">Klik untuk unggah (opsional)</div>
                            <input type="file" name="foto_admin_3" accept="image/*" />
                        </div>
                    </div>
                </div>

                <!-- Item 2: Struktur Keselamatan Bangunan -->
                <div class="checklist-item">
                    <div class="checklist-item-header">
                        <div>
                            <div class="checklist-item-title">2. Kelaikan Struktur Keselamatan (Pondasi, Kolom &amp; Rangka Atap)</div>
                            <div class="checklist-item-desc">Penilaian kondisi pondasi amblas/retak, kolom/balok kayu lapuk/tanpa tulangan, dan kuda-kuda atap rawan runtuh.</div>
                        </div>
                        <div class="checklist-toggle-group">
                            <label class="checklist-radio-label sesuai active">
                                <input type="radio" name="item_struktur" value="sesuai" checked />
                                <i class="fas fa-check-circle"></i> Rusak / Perlu Stimulan
                            </label>
                            <label class="checklist-radio-label tidak">
                                <input type="radio" name="item_struktur" value="tidak" />
                                <i class="fas fa-times-circle"></i> Masih Kokoh / Laik
                            </label>
                        </div>
                    </div>
                    <div class="checklist-foto-row">
                        <div class="checklist-foto-box has-file">
                            <i class="fas fa-image" style="color:#27ae60;"></i>
                            <div class="title">Foto Kondisi Pondasi</div>
                            <div class="subtitle" style="color:#27ae60;font-weight:700;">pondasi_rusak.jpg</div>
                            <input type="file" name="foto_struktur_1" accept="image/*" />
                        </div>
                        <div class="checklist-foto-box has-file">
                            <i class="fas fa-image" style="color:#27ae60;"></i>
                            <div class="title">Foto Kolom &amp; Rangka Atap</div>
                            <div class="subtitle" style="color:#27ae60;font-weight:700;">rangka_kayu_lapuk.jpg</div>
                            <input type="file" name="foto_struktur_2" accept="image/*" />
                        </div>
                        <div class="checklist-foto-box has-file">
                            <i class="fas fa-image" style="color:#27ae60;"></i>
                            <div class="title">Foto Tampak Depan 0%</div>
                            <div class="subtitle" style="color:#27ae60;font-weight:700;">tampak_depan_0pct.jpg</div>
                            <input type="file" name="foto_struktur_3" accept="image/*" />
                        </div>
                    </div>
                </div>

                <!-- Item 3: Dinding & Penutup Atap -->
                <div class="checklist-item">
                    <div class="checklist-item-header">
                        <div>
                            <div class="checklist-item-title">3. Kondisi Dinding Pengisi &amp; Penutup Atap</div>
                            <div class="checklist-item-desc">Dinding bambu/gedek/kayu tidak permanen, dinding retak tembus, atap bocor/sebagian rusak.</div>
                        </div>
                        <div class="checklist-toggle-group">
                            <label class="checklist-radio-label sesuai active">
                                <input type="radio" name="item_dinding" value="sesuai" checked />
                                <i class="fas fa-check-circle"></i> Rusak / Perlu Bantuan
                            </label>
                            <label class="checklist-radio-label tidak">
                                <input type="radio" name="item_dinding" value="tidak" />
                                <i class="fas fa-times-circle"></i> Dinding &amp; Atap Baik
                            </label>
                        </div>
                    </div>
                    <div class="checklist-foto-row">
                        <div class="checklist-foto-box has-file">
                            <i class="fas fa-image" style="color:#27ae60;"></i>
                            <div class="title">Foto Dinding Rusak</div>
                            <div class="subtitle" style="color:#27ae60;font-weight:700;">dinding_bambu.jpg</div>
                            <input type="file" name="foto_dinding_1" accept="image/*" />
                        </div>
                        <div class="checklist-foto-box has-file">
                            <i class="fas fa-image" style="color:#27ae60;"></i>
                            <div class="title">Foto Penutup Atap</div>
                            <div class="subtitle" style="color:#27ae60;font-weight:700;">atap_bocor.jpg</div>
                            <input type="file" name="foto_dinding_2" accept="image/*" />
                        </div>
                        <div class="checklist-foto-box">
                            <i class="fas fa-camera"></i>
                            <div class="title">Foto Detail Lain</div>
                            <div class="subtitle">Klik untuk unggah</div>
                            <input type="file" name="foto_dinding_3" accept="image/*" />
                        </div>
                    </div>
                </div>

                <!-- Item 4: Kesehatan & Sanitasi (Lantai, Ventilasi, MCK) -->
                <div class="checklist-item">
                    <div class="checklist-item-header">
                        <div>
                            <div class="checklist-item-title">4. Kelaikan Kesehatan &amp; Sanitasi (Lantai, Ventilasi, MCK)</div>
                            <div class="checklist-item-desc">Lantai tanah/semen rusak, minim ventilasi &amp; cahaya alami, belum memiliki jamban/septic tank sehat.</div>
                        </div>
                        <div class="checklist-toggle-group">
                            <label class="checklist-radio-label sesuai active">
                                <input type="radio" name="item_sanitasi" value="sesuai" checked />
                                <i class="fas fa-check-circle"></i> Belum Memenuhi Standar
                            </label>
                            <label class="checklist-radio-label tidak">
                                <input type="radio" name="item_sanitasi" value="tidak" />
                                <i class="fas fa-times-circle"></i> Sanitasi Sehat
                            </label>
                        </div>
                    </div>
                    <div class="checklist-foto-row">
                        <div class="checklist-foto-box has-file">
                            <i class="fas fa-image" style="color:#27ae60;"></i>
                            <div class="title">Foto Kondisi Lantai</div>
                            <div class="subtitle" style="color:#27ae60;font-weight:700;">lantai_tanah.jpg</div>
                            <input type="file" name="foto_sanitasi_1" accept="image/*" />
                        </div>
                        <div class="checklist-foto-box has-file">
                            <i class="fas fa-image" style="color:#27ae60;"></i>
                            <div class="title">Foto Akses Jamban / Sanitasi</div>
                            <div class="subtitle" style="color:#27ae60;font-weight:700;">mck_tidak_laik.jpg</div>
                            <input type="file" name="foto_sanitasi_2" accept="image/*" />
                        </div>
                        <div class="checklist-foto-box">
                            <i class="fas fa-camera"></i>
                            <div class="title">Foto Ventilasi / Ruang Dalam</div>
                            <div class="subtitle">Klik untuk unggah</div>
                            <input type="file" name="foto_sanitasi_3" accept="image/*" />
                        </div>
                    </div>
                </div>
            </div>

            <!-- 6. Kesimpulan & Rekomendasi Petugas TFL -->
            <div class="survey-card">
                <div class="survey-section-title">
                    <i class="fas fa-award"></i> 6. Kesimpulan &amp; Rekomendasi Hasil Survei
                </div>
                <div class="form-grid-2">
                    <div class="form-group">
                        <label><i class="fas fa-check-double"></i> Rekomendasi Bantuan BSPS <span style="color:var(--danger);">*</span></label>
                        <select name="rekomendasi" id="selectRekomendasi" class="form-control" style="font-weight:700;color:var(--primary);">
                            <option value="Layak Bantuan BSPS (Peningkatan Kualitas)" selected>Layak Bantuan BSPS (Peningkatan Kualitas - PK)</option>
                            <option value="Layak Bantuan BSPS (Pembangunan Baru)">Layak Bantuan BSPS (Pembangunan Baru - PB)</option>
                            <option value="Ditangguhkan (Perlu Perbaikan Berkas)">Ditangguhkan (Perlu Perbaikan Berkas)</option>
                            <option value="Tidak Layak / Tidak Memenuhi Kriteria">Tidak Layak / Tidak Memenuhi Kriteria</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-calculator"></i> Estimasi Usulan Bantuan Stimulan</label>
                        <input type="text" name="estimasi_bantuan" id="inputEstimasi" value="Rp 20.000.000 (Bahan Rp 17.5 Jt + Upah Rp 2.5 Jt)" readonly style="background:var(--bg-body) !important;font-weight:700;color:var(--success);" />
                    </div>
                </div>
                <div class="form-group">
                    <label><i class="fas fa-comment-dots"></i> Catatan &amp; Evaluasi Khusus Petugas TFL</label>
                    <textarea name="catatan_petugas" id="inputCatatan" rows="3" placeholder="Tuliskan catatan teknis kondisi rumah...">Kondisi bangunan tidak memenuhi standar keselamatan konstruksi dan kesehatan hunian (RTLH). Komponen struktur utama dan dinding memerlukan intervensi bantuan stimulan peningkatan kualitas rumah swadaya.</textarea>
                </div>

                <div style="display:flex;gap:12px;justify-content:flex-end;margin-top:20px;flex-wrap:wrap;">
                    <a href="{{ url('/dashboard') }}" class="btn btn-outline" style="padding:12px 24px;border-radius:var(--radius-sm);font-weight:700;text-decoration:none;display:inline-flex;align-items:center;gap:8px;">
                        <i class="fas fa-arrow-left"></i> Kembali ke Dashboard
                    </a>
                    <button type="submit" class="btn btn-primary" style="padding:12px 30px;border-radius:var(--radius-sm);font-weight:700;background:var(--primary);color:#fff;display:inline-flex;align-items:center;gap:8px;border:none;box-shadow:0 4px 14px rgba(0,40,85,0.25);">
                        <i class="fas fa-save"></i> Simpan Hasil Verifikasi &amp; Validasi
                    </button>
                </div>
            </div>
        </form>
    </main>
@endsection

@push('scripts')
<script>
    function isiDataDummyOtomatis() {
        document.getElementById('inputPemohon').value = 'Bpk. Slamet Riyadi';
        document.getElementById('inputNik').value = '3509191204850001';
        document.getElementById('inputNoKk').value = '3509191204050012';
        document.getElementById('inputJumlahJiwa').value = '4';
        document.getElementById('inputPenghasilan').value = 'Rp 1.500.000 / bulan (MBR)';
        document.getElementById('inputAlamat').value = 'Jl. Hayam Wuruk No. 45, RT 02/RW 05, Kel. Sempusari';
        document.getElementById('inputKecamatan').value = 'Kaliwates';
        document.getElementById('inputDesa').value = 'Sempusari';
        document.getElementById('inputJalan').value = 'Jl. Hayam Wuruk Gg. Mawar No. 12';
        document.getElementById('gpsLat').value = '-8.1721';
        document.getElementById('gpsLng').value = '113.6997';
        document.getElementById('inputLuasBangunan').value = '36';
        document.getElementById('inputLuasTanah').value = '72';
        alert('Data dummy survei lapangan BSPS berhasil dimuat!');
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Radio toggle styling
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

        // Auto GPS Detection Trigger
        const autoGpsBtn = document.getElementById('btnAutoGps');
        if (autoGpsBtn) {
            autoGpsBtn.addEventListener('click', function() {
                if (navigator.geolocation) {
                    this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Mendeteksi...';
                    navigator.geolocation.getCurrentPosition(
                        (pos) => {
                            document.getElementById('gpsLat').value = pos.coords.latitude.toFixed(6);
                            document.getElementById('gpsLng').value = pos.coords.longitude.toFixed(6);
                            this.innerHTML = '<i class="fas fa-check"></i> GPS Terdeteksi';
                            setTimeout(() => { this.innerHTML = '<i class="fas fa-crosshairs"></i> Deteksi GPS'; }, 2000);
                        },
                        () => {
                            document.getElementById('gpsLat').value = '-8.1721';
                            document.getElementById('gpsLng').value = '113.6997';
                            alert('Menggunakan koordinat default wilayah Jember.');
                            this.innerHTML = '<i class="fas fa-crosshairs"></i> Deteksi GPS';
                        }
                    );
                } else {
                    alert('Browser tidak mendukung GPS Geolocation.');
                }
            });
        }

        // Form submit handler
        const form = document.getElementById('formSurveyUtama');
        if (form) {
            form.addEventListener('submit', function() {
                alert('Data Verifikasi & Validasi Lapangan BSPS berhasil disimpan!');
            });
        }
    });
</script>
@endpush
