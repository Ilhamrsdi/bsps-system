@extends('layouts.partial.app')

@php $isEdit = isset($dataMingguan); @endphp

@section('title', $isEdit ? 'PUPR Jember - Edit Data Kegiatan' : 'PUPR Jember - Tambah Data')
@section('title_header', $isEdit ? 'Edit Data Kegiatan' : 'Tambah Data')
@section('subtitle_header', $isEdit ? 'Perbarui data kegiatan harian Dinas PUPR Kabupaten Jember' : 'Input data kegiatan harian Dinas PUPR Kabupaten Jember')

@push('styles')
<style>
    /* ============================================================
       PAGE STYLES: CREATE DATA MINGGUAN (PUPR THEME)
       ============================================================ */
    .breadcrumb {
        font-size: 13px;
        color: var(--text-muted);
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .breadcrumb a {
        color: var(--primary);
        text-decoration: none;
        font-weight: 500;
    }
    .breadcrumb a:hover {
        color: var(--secondary);
    }

    /* Form Card */
    .form-card {
        background: var(--bg-card);
        border-radius: var(--radius);
        box-shadow: var(--shadow-sm);
        border: 1px solid rgba(0, 40, 85, 0.06);
        overflow: hidden;
        max-width: 100%;
        margin: 0;
    }
    .form-card .form-card-header {
        padding: 20px 28px;
        border-bottom: 1px solid rgba(0, 40, 85, 0.07);
        display: flex;
        align-items: center;
        gap: 12px;
        background: linear-gradient(90deg, rgba(0,40,85,0.04) 0%, transparent 100%);
    }
    .form-card .form-card-header .header-icon {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        background: rgba(0, 40, 85, 0.10);
        color: var(--primary);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        flex-shrink: 0;
    }
    .form-card .form-card-header h3 {
        font-size: 17px;
        font-weight: 700;
        color: var(--primary);
        margin: 0;
    }
    .form-card .form-card-header p {
        font-size: 12px;
        color: var(--text-muted);
        margin: 2px 0 0;
    }

    .form-card .form-card-body {
        padding: 28px;
    }

    /* Form Elements */
    .form-section-title {
        font-size: 13px;
        font-weight: 700;
        color: var(--primary);
        text-transform: uppercase;
        letter-spacing: 0.6px;
        margin: 0 0 16px;
        padding-bottom: 8px;
        border-bottom: 2px solid rgba(0, 40, 85, 0.08);
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .form-group {
        margin-bottom: 20px;
    }
    .form-group label {
        display: block;
        font-size: 13px;
        font-weight: 600;
        color: var(--text-secondary);
        margin-bottom: 7px;
    }
    .form-group label .required {
        color: var(--danger);
        margin-left: 3px;
    }
    .form-group input,
    .form-group select,
    .form-group textarea {
        width: 100%;
        padding: 10px 14px;
        border-radius: var(--radius-sm);
        border: 1px solid rgba(0, 40, 85, 0.14);
        font-family: inherit;
        font-size: 14px;
        background: var(--bg-body);
        color: var(--text-primary);
        transition: var(--transition);
        outline: none;
        box-sizing: border-box;
    }
    .form-group input:focus,
    .form-group select:focus,
    .form-group textarea:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 4px rgba(0, 40, 85, 0.08);
        background: #fff;
    }
    .form-group input.is-invalid,
    .form-group select.is-invalid,
    .form-group textarea.is-invalid {
        border-color: var(--danger);
        box-shadow: 0 0 0 3px rgba(231, 76, 60, 0.10);
    }
    .form-group .invalid-feedback {
        font-size: 12px;
        color: var(--danger);
        margin-top: 5px;
        display: none;
    }
    .form-group input.is-invalid ~ .invalid-feedback,
    .form-group select.is-invalid ~ .invalid-feedback,
    .form-group textarea.is-invalid ~ .invalid-feedback {
        display: block;
    }
    .form-group textarea {
        resize: vertical;
        min-height: 100px;
    }
    .form-group .input-hint {
        font-size: 11px;
        color: var(--text-muted);
        margin-top: 5px;
    }

    /* Progress Input */
    .progress-input-wrapper {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .progress-input-wrapper input[type="range"] {
        flex: 1;
        padding: 0;
        height: 6px;
        accent-color: var(--primary);
        cursor: pointer;
        border: none;
        box-shadow: none;
        background: transparent;
    }
    .progress-input-wrapper input[type="range"]:focus {
        box-shadow: none;
        border: none;
    }
    .progress-input-wrapper .progress-value-badge {
        min-width: 52px;
        text-align: center;
        font-size: 14px;
        font-weight: 700;
        color: var(--primary);
        background: rgba(0, 40, 85, 0.08);
        border-radius: var(--radius-sm);
        padding: 6px 10px;
    }

    /* Form Row */
    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }
    @media (max-width: 640px) {
        .form-row { grid-template-columns: 1fr; }
    }

    /* Divider */
    .form-divider {
        border: none;
        border-top: 1px solid rgba(0, 40, 85, 0.07);
        margin: 24px 0;
    }

    /* Form Footer */
    .form-card-footer {
        padding: 20px 28px;
        border-top: 1px solid rgba(0, 40, 85, 0.07);
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 12px;
        background: rgba(0, 40, 85, 0.02);
    }
    .form-card-footer .btn {
        padding: 10px 24px;
        border-radius: var(--radius-sm);
        border: none;
        font-family: inherit;
        font-weight: 600;
        font-size: 14px;
        cursor: pointer;
        transition: var(--transition);
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
    }
    .form-card-footer .btn-cancel {
        background: transparent;
        color: var(--text-secondary);
        border: 1px solid rgba(0, 40, 85, 0.14);
    }
    .form-card-footer .btn-cancel:hover {
        background: rgba(0, 40, 85, 0.05);
    }
    .form-card-footer .btn-submit {
        background: var(--primary);
        color: #fff;
    }
    .form-card-footer .btn-submit:hover {
        background: var(--primary-light, #003d7a);
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0, 40, 85, 0.25);
    }

    /* Dropdown invalid state */
    .is-invalid-wrapper .pupr-dropdown-toggle {
        border-color: var(--danger) !important;
        box-shadow: 0 0 0 3px rgba(231, 76, 60, 0.10) !important;
    }

    /* Alert Error */
    .alert-error {
        background: rgba(231, 76, 60, 0.08);
        border: 1px solid rgba(231, 76, 60, 0.25);
        border-radius: var(--radius-sm);
        padding: 14px 18px;
        margin-bottom: 20px;
        font-size: 13px;
        color: var(--danger);
    }
    .alert-error ul { margin: 6px 0 0 16px; padding: 0; }
    .alert-error ul li { margin-bottom: 3px; }
</style>
@endpush

@section('content')
    @include('layouts.navbar')

    <main class="dashboard-content">
        {{-- Breadcrumb --}}
        <div class="breadcrumb">
            <a href="{{ url('/') }}"><i class="fas fa-home"></i> Home</a>
            <i class="fas fa-chevron-right" style="font-size:10px;"></i>
            <a href="{{ route('data-mingguan') }}">Data Mingguan</a>
            <i class="fas fa-chevron-right" style="font-size:10px;"></i>
            <span>{{ $isEdit ? 'Edit Data' : 'Tambah Data' }}</span>
        </div>

        {{-- Alert Validasi Error --}}
        @if ($errors->any())
            <div class="alert-error">
                <strong><i class="fas fa-exclamation-circle"></i> Terdapat kesalahan input:</strong>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Form Card --}}
        <div class="form-card">
            <div class="form-card-header">
                <div class="header-icon">
                    <i class="fas {{ $isEdit ? 'fa-edit' : 'fa-plus-circle' }}"></i>
                </div>
                <div>
                    <h3>{{ $isEdit ? 'Edit Data Kegiatan' : 'Tambah Data Kegiatan Harian' }}</h3>
                    <p>{{ $isEdit ? 'Perbarui informasi kegiatan: '.$dataMingguan->nama_kegiatan : 'Isi formulir berikut untuk menambahkan data kegiatan harian' }}</p>
                </div>
            </div>

            <div class="form-card-body">
                <form action="{{ $isEdit ? route('data-mingguan.update', $dataMingguan->id) : route('data-mingguan.store') }}" method="POST" id="formCreateKegiatan">
                    @csrf
                    @if($isEdit) @method('PUT') @endif

                    {{-- SECTION 1: Informasi Kegiatan --}}
                    <p class="form-section-title">
                        <i class="fas fa-info-circle"></i> Informasi Kegiatan
                    </p>

                    <div class="form-group">
                        <label>Nama Kegiatan <span class="required">*</span></label>
                        <input
                            type="text"
                            name="nama_kegiatan"
                            placeholder="Contoh: Perbaikan Jalan Sudirman..."
                            value="{{ old('nama_kegiatan', $isEdit ? $dataMingguan->nama_kegiatan : '') }}"
                            class="{{ $errors->has('nama_kegiatan') ? 'is-invalid' : '' }}"
                            required
                        />
                        <div class="invalid-feedback">{{ $errors->first('nama_kegiatan') }}</div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Nama Pemohon</label>
                            <input
                                type="text"
                                name="nama_pemohon"
                                placeholder="Nama pemohon/pemilik..."
                                value="{{ old('nama_pemohon', $isEdit ? $dataMingguan->nama_pemohon : '') }}"
                                class="{{ $errors->has('nama_pemohon') ? 'is-invalid' : '' }}"
                            />
                            <div class="invalid-feedback">{{ $errors->first('nama_pemohon') }}</div>
                        </div>
                        <div class="form-group">
                            <label>NIK Pemohon</label>
                            <input
                                type="text"
                                name="nik_pemohon"
                                placeholder="Nomor Induk Kependudukan (16 digit)..."
                                maxlength="16"
                                value="{{ old('nik_pemohon', $isEdit ? $dataMingguan->nik_pemohon : '') }}"
                                class="{{ $errors->has('nik_pemohon') ? 'is-invalid' : '' }}"
                            />
                            <div class="invalid-feedback">{{ $errors->first('nik_pemohon') }}</div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Alamat Lengkap</label>
                        <textarea
                            name="alamat"
                            rows="2"
                            placeholder="Alamat lengkap kegiatan/pemohon..."
                            class="{{ $errors->has('alamat') ? 'is-invalid' : '' }}"
                        >{{ old('alamat', $isEdit ? $dataMingguan->alamat : '') }}</textarea>
                        <div class="invalid-feedback">{{ $errors->first('alamat') }}</div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Lokasi / Kecamatan <span class="required">*</span></label>
                            @php $currentLokasi = old('lokasi', $isEdit ? $dataMingguan->lokasi : ''); @endphp
                            <input type="hidden" name="lokasi" id="lokasiHidden" value="{{ $currentLokasi }}" />
                            <div class="pupr-dropdown-wrapper {{ $errors->has('lokasi') ? 'is-invalid-wrapper' : '' }}" style="width:100%;">
                                <button type="button" class="pupr-dropdown-toggle" data-toggle="pupr-dropdown" style="width:100%;padding:10px 36px 10px 14px;font-size:14px;">
                                    <span class="selected-label">
                                        @php
                                            $kecList = ['Kaliwates','Sumbersari','Patrang','Ajung','Rambipuji','Balung','Ambulu','Wuluhan','Puger','Kencong','Gumukmas','Umbulsari','Semboro','Jombang','Silo','Mayang','Mumbulsari','Jenggawah','Tempurejo','Pakusari','Sukowono','Kalisat','Ledokombo','Sumberjambe','Arjasa','Jelbuk','Bangsalsari','Panti','Sukorambi','Tanggul','Sumberbaru'];
                                            $labelLokasi = '-- Pilih Kecamatan --';
                                            foreach ($kecList as $k) {
                                                if (strtolower(str_replace(' ','_',$k)) === $currentLokasi) { $labelLokasi = 'Kec. '.$k; break; }
                                            }
                                        @endphp
                                        {{ $labelLokasi }}
                                    </span>
                                    <i class="fas fa-chevron-down" style="font-size:10px;flex-shrink:0;"></i>
                                </button>
                                <div class="pupr-dropdown-menu" style="width:100%;max-height:220px;overflow-y:auto;">
                                    @foreach(['Kaliwates','Sumbersari','Patrang','Ajung','Rambipuji','Balung','Ambulu','Wuluhan','Puger','Kencong','Gumukmas','Umbulsari','Semboro','Jombang','Silo','Mayang','Mumbulsari','Jenggawah','Tempurejo','Pakusari','Sukowono','Kalisat','Ledokombo','Sumberjambe','Arjasa','Jelbuk','Bangsalsari','Panti','Sukorambi','Tanggul','Sumberbaru'] as $kec)
                                        <div class="pupr-dropdown-item {{ $currentLokasi == strtolower(str_replace(' ','_',$kec)) ? 'active' : '' }}"
                                             data-value="{{ strtolower(str_replace(' ','_',$kec)) }}"
                                             data-target="lokasiHidden">
                                            Kec. {{ $kec }}
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            @if($errors->has('lokasi'))
                                <div class="invalid-feedback" style="display:block;">{{ $errors->first('lokasi') }}</div>
                            @endif
                        </div>

                        <div class="form-group">
                            <label>Tanggal Kegiatan <span class="required">*</span></label>
                            <input
                                type="date"
                                name="tanggal"
                                value="{{ old('tanggal', $isEdit ? $dataMingguan->tanggal->format('Y-m-d') : date('Y-m-d')) }}"
                                class="{{ $errors->has('tanggal') ? 'is-invalid' : '' }}"
                                required
                            />
                            <div class="invalid-feedback">{{ $errors->first('tanggal') }}</div>
                        </div>
                    </div>

                    <hr class="form-divider" />

                    {{-- SECTION 2: Status Kegiatan --}}
                    <p class="form-section-title">
                        <i class="fas fa-chart-line"></i> Status Kegiatan
                    </p>

                    <div class="form-row">
                        <div class="form-group" style="width:100%;">
                            <label>Status Kegiatan <span class="required">*</span></label>
                            @php $currentStatus = old('status', $isEdit ? $dataMingguan->status : ''); @endphp
                            <input type="hidden" name="status" id="statusHidden" value="{{ $currentStatus }}" />
                            <div class="pupr-dropdown-wrapper {{ $errors->has('status') ? 'is-invalid-wrapper' : '' }}" style="width:100%;">
                                <button type="button" class="pupr-dropdown-toggle" data-toggle="pupr-dropdown" style="width:100%;padding:10px 36px 10px 14px;font-size:14px;">
                                    <span class="selected-label" id="statusLabel">
                                        @php
                                            $statusMap = ['proses'=>'Proses','selesai'=>'Selesai','menunggu'=>'Menunggu','survei'=>'Survei','batal'=>'Batal'];
                                            echo isset($statusMap[$currentStatus]) ? $statusMap[$currentStatus] : '-- Pilih Status --';
                                        @endphp
                                    </span>
                                    <i class="fas fa-chevron-down" style="font-size:10px;flex-shrink:0;"></i>
                                </button>
                                <div class="pupr-dropdown-menu" style="width:100%;">
                                    <div class="pupr-dropdown-item {{ $currentStatus=='proses' ? 'active' : '' }}" data-value="proses" data-target="statusHidden">Proses</div>
                                    <div class="pupr-dropdown-item {{ $currentStatus=='selesai' ? 'active' : '' }}" data-value="selesai" data-target="statusHidden">Selesai</div>
                                    <div class="pupr-dropdown-item {{ $currentStatus=='menunggu' ? 'active' : '' }}" data-value="menunggu" data-target="statusHidden">Menunggu</div>
                                    <div class="pupr-dropdown-item {{ $currentStatus=='survei' ? 'active' : '' }}" data-value="survei" data-target="statusHidden">Survei</div>
                                    <div class="pupr-dropdown-item {{ $currentStatus=='batal' ? 'active' : '' }}" data-value="batal" data-target="statusHidden">Batal</div>
                                </div>
                            </div>
                            @if($errors->has('status'))
                                <div class="invalid-feedback" style="display:block;">{{ $errors->first('status') }}</div>
                            @endif
                        </div>
                    </div>

                    <hr class="form-divider" />

                    {{-- SECTION 3: Detail Tambahan --}}
                    <p class="form-section-title">
                        <i class="fas fa-file-alt"></i> Detail Tambahan
                    </p>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Nilai Kontrak (Rp)</label>
                            <input
                                type="text"
                                name="nilai_kontrak"
                                id="nilaiKontrak"
                                placeholder="Contoh: 250.000.000"
                                value="{{ old('nilai_kontrak', $isEdit ? $dataMingguan->nilai_kontrak : '') }}"
                                class="{{ $errors->has('nilai_kontrak') ? 'is-invalid' : '' }}"
                            />
                            <div class="invalid-feedback">{{ $errors->first('nilai_kontrak') }}</div>
                            <div class="input-hint">Kosongkan jika belum ada kontrak</div>
                        </div>
                        <div class="form-group">
                            <label>Kontraktor / Pelaksana</label>
                            <input
                                type="text"
                                name="kontraktor"
                                placeholder="Nama perusahaan atau pelaksana..."
                                value="{{ old('kontraktor', $isEdit ? $dataMingguan->kontraktor : '') }}"
                                class="{{ $errors->has('kontraktor') ? 'is-invalid' : '' }}"
                            />
                            <div class="invalid-feedback">{{ $errors->first('kontraktor') }}</div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Deskripsi / Catatan Kegiatan</label>
                        <textarea
                            name="deskripsi"
                            placeholder="Tuliskan deskripsi kegiatan, kendala yang dihadapi, atau catatan penting lainnya..."
                            class="{{ $errors->has('deskripsi') ? 'is-invalid' : '' }}"
                        >{{ old('deskripsi', $isEdit ? $dataMingguan->deskripsi : '') }}</textarea>
                        <div class="invalid-feedback">{{ $errors->first('deskripsi') }}</div>
                    </div>

                </form>
            </div>

            <div class="form-card-footer">
                <a href="{{ route('data-mingguan') }}" class="btn btn-cancel">
                    <i class="fas fa-arrow-left"></i> Batal
                </a>
                <button type="submit" form="formCreateKegiatan" class="btn btn-submit" id="submitBtn">
                    <i class="fas fa-save"></i> {{ $isEdit ? 'Simpan Perubahan' : 'Simpan Data' }}
                </button>
            </div>
        </div>
    </main>
@endsection

@push('scripts')
<script>
    // Handler pupr-dropdown-item dengan data-target: sync ke hidden input
    document.addEventListener('click', function(e) {
        const item = e.target.closest('.pupr-dropdown-item[data-target]');
        if (!item) return;
        const targetId = item.getAttribute('data-target');
        const val = item.getAttribute('data-value');
        const hiddenInput = document.getElementById(targetId);
        if (hiddenInput) hiddenInput.value = val;
    });

    // Format input nilai kontrak (angka dengan titik)
    const nilaiInput = document.getElementById('nilaiKontrak');
    if (nilaiInput) {
        nilaiInput.addEventListener('input', function () {
            let raw = this.value.replace(/\D/g, '');
            this.value = raw.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        });
    }

    // Submit loading state
    document.getElementById('formCreateKegiatan').addEventListener('submit', function () {
        const btn = document.getElementById('submitBtn');
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';
    });
</script>
@endpush
