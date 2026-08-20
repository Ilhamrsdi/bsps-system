@extends('layouts.partial.app')

@section('title', 'Data Calon Penerima ' . ($status === 'tidak_layak' ? 'Tidak Layak' : ($status === 'all' ? 'Hasil Verval' : 'Layak Diusulkan')))
@section('title_header', 'Data Penerima BSPS (' . ($status === 'tidak_layak' ? 'Tidak Layak Diusulkan' : ($status === 'all' ? 'Semua Hasil Verval' : 'Layak Diusulkan')) . ')')
@section('subtitle_header', 'Daftar By Name By Address (BNBA) Calon Penerima Bantuan Stimulan Perumahan Swadaya')

@push('styles')
<style>
    /* Status Filter Pills */
    .kelayakan-filter-pills {
        display: flex;
        gap: 12px;
        margin-bottom: 20px;
        flex-wrap: wrap;
    }

    .kelayakan-pill-btn {
        padding: 12px 20px;
        border-radius: 12px;
        background: #ffffff;
        border: 1.5px solid #e2e8f0;
        color: #475569;
        font-weight: 700;
        font-size: 13.5px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        transition: all 0.25s ease;
        box-shadow: 0 2px 6px rgba(0, 40, 85, 0.04);
    }

    .kelayakan-pill-btn:hover {
        border-color: #cbd5e1;
        transform: translateY(-2px);
    }

    .kelayakan-pill-btn.active.pill-layak {
        background: #15803d;
        color: #ffffff;
        border-color: #15803d;
        box-shadow: 0 6px 18px rgba(21, 128, 61, 0.25);
    }

    .kelayakan-pill-btn.active.pill-tidak {
        background: #b91c1c;
        color: #ffffff;
        border-color: #b91c1c;
        box-shadow: 0 6px 18px rgba(185, 28, 28, 0.25);
    }

    .kelayakan-pill-btn.active.pill-all {
        background: #002855;
        color: #ffffff;
        border-color: #002855;
        box-shadow: 0 6px 18px rgba(0, 40, 85, 0.25);
    }

    .kelayakan-pill-btn .badge-pill-count {
        font-size: 11px;
        font-weight: 800;
        padding: 2px 8px;
        border-radius: 12px;
        background: rgba(0, 40, 85, 0.08);
        color: inherit;
    }

    .kelayakan-pill-btn.active .badge-pill-count {
        background: rgba(255, 255, 255, 0.25);
        color: #ffffff;
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
        gap: 14px;
    }

    .filter-item-box {
        flex: 1;
        min-width: 180px;
    }

    /* Indikator mini pills inside table */
    .ind-pill {
        display: inline-block;
        padding: 2px 6px;
        border-radius: 4px;
        font-size: 10px;
        font-weight: 700;
        margin: 1px;
    }
    .ind-pill.rusak { background: #fee2e2; color: #b91c1c; border: 1px solid #fca5a5; }
    .ind-pill.baik { background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0; }
</style>
@endpush

@section('content')
    @include('layouts.navbar')

    <main class="dashboard-content">
        <!-- Breadcrumb -->
        <div class="breadcrumb" style="margin-bottom: 16px; font-size: 13px; color: var(--text-muted); display: flex; align-items: center; gap: 8px;">
            <a href="{{ route('dashboard') }}" style="color: var(--primary); text-decoration: none; font-weight: 600;"><i class="fas fa-th-large"></i> Dashboard Global</a>
            <i class="fas fa-chevron-right" style="font-size: 10px;"></i>
            <span>Data Kelayakan Penerima ({{ $status === 'tidak_layak' ? 'Tidak Layak' : ($status === 'all' ? 'Semua Verval' : 'Layak Diusulkan') }})</span>
        </div>

        <!-- 3 Tab Selector: Layak Diusulkan vs Tidak Layak vs Semua Verval -->
        <div class="kelayakan-filter-pills">
            <a href="{{ route('dashboard.data-kelayakan', ['status' => 'layak', 'kecamatan' => request('kecamatan'), 'desa' => request('desa')]) }}"
               class="kelayakan-pill-btn pill-layak {{ $status === 'layak' ? 'active' : '' }}">
                <i class="fas fa-circle-check"></i>
                <span>Layak Diusulkan</span>
                <span class="badge-pill-count">{{ number_format($totalLayakGlobal) }} KK</span>
            </a>

            <a href="{{ route('dashboard.data-kelayakan', ['status' => 'tidak_layak', 'kecamatan' => request('kecamatan'), 'desa' => request('desa')]) }}"
               class="kelayakan-pill-btn pill-tidak {{ $status === 'tidak_layak' ? 'active' : '' }}">
                <i class="fas fa-circle-xmark"></i>
                <span>Tidak Layak</span>
                <span class="badge-pill-count">{{ number_format($totalTidakLayakGlobal) }} KK</span>
            </a>

            <a href="{{ route('dashboard.data-kelayakan', ['status' => 'all', 'kecamatan' => request('kecamatan'), 'desa' => request('desa')]) }}"
               class="kelayakan-pill-btn pill-all {{ $status === 'all' ? 'active' : '' }}">
                <i class="fas fa-clipboard-check"></i>
                <span>Semua Hasil Verval</span>
                <span class="badge-pill-count">{{ number_format($totalSudahSurveiGlobal) }} KK</span>
            </a>
        </div>

        <!-- Filter Bar -->
        <div class="filter-card-bar">
            <form action="{{ route('dashboard.data-kelayakan') }}" method="GET" class="filter-form-grid" id="filterFormKelayakan">
                <input type="hidden" name="status" value="{{ $status }}" />

                <!-- Filter Kecamatan -->
                @if(!auth()->check() || !auth()->user()->isAdminKecamatan())
                <div class="filter-item-box" style="max-width: 220px;">
                    <select name="kecamatan" class="form-control" onchange="this.form.submit()" style="width: 100%; padding: 8px 12px; border-radius: 8px; border: 1px solid #cbd5e1; font-size: 13px; font-weight: 600; background: #fff;">
                        <option value="all">-- Semua Kecamatan --</option>
                        @foreach($listKecamatan as $kec)
                            <option value="{{ $kec }}" {{ request('kecamatan') === $kec ? 'selected' : '' }}>
                                Kec. {{ ucwords(strtolower($kec)) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @endif

                <!-- Filter Desa -->
                <div class="filter-item-box" style="max-width: 220px;">
                    <select name="desa" class="form-control" onchange="this.form.submit()" style="width: 100%; padding: 8px 12px; border-radius: 8px; border: 1px solid #cbd5e1; font-size: 13px; font-weight: 600; background: #fff;">
                        <option value="all">-- Semua Desa --</option>
                        @foreach($listDesa as $d)
                            <option value="{{ $d }}" {{ request('desa') === $d ? 'selected' : '' }}>
                                Desa {{ ucwords(strtolower($d)) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Filter Status Foto -->
                <div class="filter-item-box" style="max-width: 200px;">
                    <select name="status_foto" class="form-control" onchange="this.form.submit()" style="width: 100%; padding: 8px 12px; border-radius: 8px; border: 1px solid #cbd5e1; font-size: 13px; font-weight: 600; background: #fff;">
                        <option value="">-- Semua Status Dokumen --</option>
                        <option value="ditolak" {{ request('status_foto') === 'ditolak' ? 'selected' : '' }}>Ada Dok. Ditolak</option>
                    </select>
                </div>

                <!-- Filter Status Verval -->
                <div class="filter-item-box" style="max-width: 200px;">
                    <select name="status_verval" class="form-control" onchange="this.form.submit()" style="width: 100%; padding: 8px 12px; border-radius: 8px; border: 1px solid {{ $statusVerval ? '#002855' : '#cbd5e1' }}; font-size: 13px; font-weight: 600; background: {{ $statusVerval ? '#f0f4ff' : '#fff' }}; color: {{ $statusVerval ? '#002855' : 'inherit' }};">
                        <option value="">-- Semua Status Verval --</option>
                        <option value="ditemukan" {{ $statusVerval === 'ditemukan' ? 'selected' : '' }}>✅ Ditemukan</option>
                        <option value="meninggal" {{ $statusVerval === 'meninggal' ? 'selected' : '' }}>🕊️ Meninggal</option>
                        <option value="pindah" {{ $statusVerval === 'pindah' ? 'selected' : '' }}>🚚 Pindah</option>
                        <option value="menolak disurvey" {{ $statusVerval === 'menolak disurvey' ? 'selected' : '' }}>✋ Menolak Survey</option>
                        <option value="tidak diketahui" {{ $statusVerval === 'tidak diketahui' ? 'selected' : '' }}>❓ Tidak Diketahui</option>
                        <option value="belum_verval" {{ $statusVerval === 'belum_verval' ? 'selected' : '' }}>⏳ Belum Verval</option>
                    </select>
                </div>

                <!-- Per Page -->
                <div class="filter-item-box" style="max-width: 130px;">
                    <select name="per_page" class="form-control" onchange="this.form.submit()" style="width: 100%; padding: 8px 12px; border-radius: 8px; border: 1px solid #cbd5e1; font-size: 13px; font-weight: 600; background: #fff;">
                        <option value="15" {{ request('per_page', '15') == '15' ? 'selected' : '' }}>15 Baris</option>
                        <option value="25" {{ request('per_page') == '25' ? 'selected' : '' }}>25 Baris</option>
                        <option value="50" {{ request('per_page') == '50' ? 'selected' : '' }}>50 Baris</option>
                        <option value="100" {{ request('per_page') == '100' ? 'selected' : '' }}>100 Baris</option>
                        <option value="all" {{ request('per_page') == 'all' ? 'selected' : '' }}>Semua</option>
                    </select>
                </div>

                <!-- Search Input -->
                <div class="filter-item-box" style="flex: 2; min-width: 220px;">
                    <div style="position: relative;">
                        <i class="fas fa-search" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 13px;"></i>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama warga, NIK, KK, atau alamat..." style="width: 100%; padding: 8px 14px 8px 34px; border-radius: 8px; border: 1px solid #cbd5e1; font-size: 13px; outline: none;" />
                    </div>
                </div>

                <!-- Action Buttons -->
                <div style="display: flex; gap: 8px;">
                    <button type="submit" class="btn btn-primary" style="padding: 8px 16px; border-radius: 8px; font-size: 13px; font-weight: 700; border: none; cursor: pointer; background: #002855; color: #ffffff;">
                        <i class="fas fa-filter"></i> Filter
                    </button>
                    <a href="{{ route('dashboard.data-kelayakan', ['status' => $status]) }}" class="btn btn-outline" style="padding: 8px 14px; border-radius: 8px; font-size: 13px; font-weight: 600; text-decoration: none; border: 1px solid #cbd5e1; color: #64748b; background: #ffffff;">
                        <i class="fas fa-redo"></i> Reset
                    </a>
                </div>
            </form>
        </div>

        <!-- Tabel Data Calon Penerima -->
        <div class="table-card">
            <div class="table-header" style="padding: 16px 20px; border-bottom: 1px solid rgba(0, 40, 85, 0.08); display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap;">
                <h3 style="margin: 0; font-size: 15px; font-weight: 800; color: #002855; display: flex; align-items: center; gap: 8px;">
                    @if($status === 'layak')
                        <i class="fas fa-circle-check" style="color: #16a34a;"></i>
                        <span>Daftar Calon Penerima Layak Diusulkan</span>
                    @elseif($status === 'tidak_layak')
                        <i class="fas fa-circle-xmark" style="color: #dc2626;"></i>
                        <span>Daftar Calon Penerima Tidak Layak</span>
                    @else
                        <i class="fas fa-clipboard-list" style="color: #002855;"></i>
                        <span>Daftar Seluruh Calon Penerima Selesai Verval</span>
                    @endif
                    <span style="font-size: 13px; color: #64748b; font-weight: 600;">({{ number_format($penerimaList->total()) }} data ditemukan)</span>
                </h3>

                <div style="display: flex; gap: 8px;">
                    <a href="{{ route('laporan.export', ['status' => ($status === 'layak' ? 'layak' : ($status === 'tidak_layak' ? 'tidak_layak' : 'all')), 'kecamatan' => request('kecamatan', 'all')]) }}" class="btn btn-outline" style="padding: 6px 14px; font-size: 12px; font-weight: 700; border-radius: 6px; text-decoration: none; border: 1px solid #107c41; color: #107c41; background: #ffffff; display: inline-flex; align-items: center; gap: 6px;">
                        <i class="fas fa-file-excel"></i> Export Excel
                    </a>
                </div>
            </div>

            <div class="table-wrapper">
                <table class="pupr-table">
                    <thead>
                        <tr>
                            <th style="width: 45px; text-align: center;">No</th>
                            <th style="min-width: 180px;">Nama Calon Penerima</th>
                            <th style="min-width: 160px;">NIK / No. KK</th>
                            <th style="min-width: 170px;">Wilayah / Desa</th>
                            <th style="min-width: 120px;">Desil</th>
                            <th style="min-width: 130px; text-align: center;">Status Verval</th>
                            <th style="min-width: 200px;">Capaian Indikator RTLH</th>
                            <th style="min-width: 140px; text-align: center;">Status Kelayakan</th>
                            <th style="min-width: 140px;">Petugas Lapangan</th>
                            <th style="min-width: 100px; text-align: center;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($penerimaList as $index => $row)
                        <tr>
                            <td style="text-align: center;">{{ $penerimaList->firstItem() + $index }}</td>
                            <td>
                                <strong style="color: #002855; font-size: 13.5px; display: block;">{{ $row->nama }}</strong>
                                <span style="font-size: 11px; color: #64748b;">
                                    <i class="fas fa-clock"></i> {{ $row->updated_at ? $row->updated_at->setTimezone('Asia/Jakarta')->translatedFormat('d M Y, H:i') : '-' }} WIB
                                </span>
                            </td>
                            <td>
                                <div style="font-family: monospace; font-size: 12.5px; font-weight: 700; color: #0f172a;">{{ $row->no_ktp ?: '-' }}</div>
                                <div style="font-family: monospace; font-size: 11px; color: #64748b;">KK: {{ $row->no_kk ?: '-' }}</div>
                            </td>
                            <td>
                                <div style="font-weight: 700; font-size: 12.5px; color: #002855;">
                                    <i class="fas fa-map-pin" style="font-size: 10px; opacity: 0.7;"></i> Desa {{ ucwords(strtolower($row->desa_kelurahan ?: '-')) }}
                                </div>
                                <div style="font-size: 11px; color: #64748b;">
                                    Kec. {{ ucwords(strtolower($row->kecamatan ?: '-')) }}
                                </div>
                                <div style="font-size: 10.5px; color: #94a3b8; margin-top: 2px;">
                                    {{ $row->alamat ?: '-' }}
                                </div>
                            </td>
                            <td>
                                <span style="font-size: 11px; font-weight: 700; padding: 3px 8px; border-radius: 6px; background: rgba(0, 40, 85, 0.08); color: #002855;">
                                    {{ $row->pengelompokan_desil ?: '-' }}
                                </span>
                            </td>
                            <td style="text-align: center;">
                                @if($row->status === 'ditemukan')
                                    <span style="font-size: 11px; font-weight: 800; padding: 4px 10px; border-radius: 12px; background: #dcfce7; color: #15803d; border: 1px solid #86efac; display: inline-flex; align-items: center; gap: 4px;">
                                        <i class="fas fa-check-circle"></i> Ditemukan
                                    </span>
                                @elseif($row->status === 'meninggal')
                                    <span style="font-size: 11px; font-weight: 800; padding: 4px 10px; border-radius: 12px; background: #fee2e2; color: #b91c1c; border: 1px solid #fecaca; display: inline-flex; align-items: center; gap: 4px;">
                                        <i class="fas fa-heart-crack"></i> Meninggal
                                    </span>
                                @elseif($row->status === 'pindah')
                                    <span style="font-size: 11px; font-weight: 800; padding: 4px 10px; border-radius: 12px; background: #ffedd5; color: #c2410c; border: 1px solid #fed7aa; display: inline-flex; align-items: center; gap: 4px;">
                                        <i class="fas fa-truck-moving"></i> Pindah
                                    </span>
                                @elseif($row->status === 'tidak diketahui')
                                    <span style="font-size: 11px; font-weight: 800; padding: 4px 10px; border-radius: 12px; background: #f1f5f9; color: #64748b; border: 1px solid #e2e8f0; display: inline-flex; align-items: center; gap: 4px;">
                                        <i class="fas fa-question-circle"></i> Tdk Diketahui
                                    </span>
                                @elseif($row->status === 'menolak disurvey')
                                    <span style="font-size: 11px; font-weight: 800; padding: 4px 10px; border-radius: 12px; background: #fff7ed; color: #ea580c; border: 1px solid #fed7aa; display: inline-flex; align-items: center; gap: 4px;">
                                        <i class="fas fa-hand-paper"></i> Menolak
                                    </span>
                                @else
                                    <span style="font-size: 11px; font-weight: 800; padding: 4px 10px; border-radius: 12px; background: #f8fafc; color: #94a3b8; border: 1px solid #f1f5f9; display: inline-flex; align-items: center; gap: 4px;">
                                        <i class="fas fa-minus"></i> Belum Verval
                                    </span>
                                @endif
                            </td>
                            <td>
                                @if($row->indikator_atap || $row->indikator_dinding || $row->indikator_lantai || $row->status_kelayakan)
                                    <div style="display: flex; flex-wrap: wrap; gap: 2px;">
                                        <span class="ind-pill {{ $row->indikator_atap === 'tidak_ada' ? 'rusak' : 'baik' }}" title="Atap">
                                            Atap: {{ $row->indikator_atap === 'tidak_ada' ? 'Rusak' : 'Baik' }}
                                        </span>
                                        <span class="ind-pill {{ $row->indikator_dinding === 'tidak_ada' ? 'rusak' : 'baik' }}" title="Dinding">
                                            Dinding: {{ $row->indikator_dinding === 'tidak_ada' ? 'Rusak' : 'Baik' }}
                                        </span>
                                        <span class="ind-pill {{ $row->indikator_lantai === 'tidak_ada' ? 'rusak' : 'baik' }}" title="Lantai">
                                            Lantai: {{ $row->indikator_lantai === 'tidak_ada' ? 'Tanah' : 'Baik' }}
                                        </span>
                                        <span class="ind-pill {{ $row->indikator_pondasi === 'tidak_ada' ? 'rusak' : 'baik' }}" title="Pondasi">
                                            Pondasi: {{ $row->indikator_pondasi === 'tidak_ada' ? 'Rusak' : 'Baik' }}
                                        </span>
                                        <span class="ind-pill {{ $row->indikator_struktur === 'tidak_ada' ? 'rusak' : 'baik' }}" title="Struktur">
                                            Struktur: {{ $row->indikator_struktur === 'tidak_ada' ? 'Rusak' : 'Baik' }}
                                        </span>
                                    </div>
                                @else
                                    <span style="font-size: 12px; color: #94a3b8; font-style: italic;">Belum Diisi</span>
                                @endif

                                @php
                                    $rejectedPhotos = [];
                                    $fields = [
                                        'foto_sudut_depan' => 'S. Depan',
                                        'foto_sudut_belakang' => 'S. Belakang',
                                        'foto_bagian_dalam' => 'B. Dalam',
                                        'foto_sudut_kiri' => 'S. Kiri',
                                        'foto_sudut_kanan' => 'S. Kanan',
                                        'ktp' => 'KTP',
                                        'kk' => 'KK',
                                        'surat_pernyataan' => 'S. Pernyataan'
                                    ];
                                    foreach($fields as $key => $label) {
                                        if($row->{'status_'.$key} === 'tidak layak') {
                                            $rejectedPhotos[] = [
                                                'label' => $label,
                                                'url' => $row->$key,
                                                'catatan' => $row->{'catatan_'.$key}
                                            ];
                                        }
                                    }
                                @endphp
                                @if(count($rejectedPhotos) > 0)
                                    <div style="margin-top: 10px; border-top: 1px dashed #cbd5e1; padding-top: 8px;">
                                        <strong style="font-size: 11px; color: #dc2626; display: block; margin-bottom: 6px;"><i class="fas fa-exclamation-triangle"></i> Dokumen Ditolak / Revisi:</strong>
                                        <div style="display: flex; flex-direction: column; gap: 4px;">
                                            @foreach($rejectedPhotos as $rp)
                                                <div style="display: flex; gap: 6px; background: #fff5f5; border: 1px solid #fecaca; border-radius: 4px; padding: 4px; align-items: flex-start;">
                                                    <a href="{{ str_starts_with($rp['url'], 'http') ? $rp['url'] : asset($rp['url']) }}" target="_blank" style="flex-shrink: 0;">
                                                        <img src="{{ str_starts_with($rp['url'], 'http') ? $rp['url'] : asset($rp['url']) }}" style="width: 40px; height: 40px; object-fit: cover; border-radius: 2px;" alt="{{ $rp['label'] }}" onerror="this.src='https://via.placeholder.com/40?text=NA'">
                                                    </a>
                                                    <div style="flex: 1; min-width: 0;">
                                                        <div style="font-size: 10.5px; font-weight: 700; color: #b91c1c; margin-bottom: 2px;">{{ $rp['label'] }}</div>
                                                        <div style="font-size: 10px; color: #7f1d1d; line-height: 1.3;">
                                                            {{ $rp['catatan'] ?: '-' }}
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </td>
                            <td style="text-align: center;">
                                @if(in_array($row->status, ['meninggal', 'pindah', 'tidak diketahui', 'menolak disurvey']))
                                    <span style="font-size: 11px; font-weight: 800; padding: 4px 10px; border-radius: 12px; background: #f1f5f9; color: #64748b; border: 1px solid #e2e8f0; display: inline-flex; align-items: center; gap: 4px;">
                                        <i class="fas fa-times"></i> Gugur
                                    </span>
                                @elseif($row->status_kelayakan === 'Layak Diusulkan')
                                    <span style="font-size: 11px; font-weight: 800; padding: 4px 10px; border-radius: 12px; background: #dcfce7; color: #15803d; border: 1px solid #86efac; display: inline-flex; align-items: center; gap: 4px;">
                                        <i class="fas fa-check-circle"></i> Layak Diusulkan
                                    </span>
                                @elseif($row->status_kelayakan === 'Tidak Layak Diusulkan')
                                    <span style="font-size: 11px; font-weight: 800; padding: 4px 10px; border-radius: 12px; background: #fee2e2; color: #b91c1c; border: 1px solid #fca5a5; display: inline-flex; align-items: center; gap: 4px;">
                                        <i class="fas fa-times-circle"></i> Tidak Layak
                                    </span>
                                @else
                                    <span style="font-size: 11px; font-weight: 700; padding: 3px 8px; border-radius: 12px; background: #fef3c7; color: #b45309; border: 1px solid #fde68a;">
                                        <i class="fas fa-clock"></i> Belum Survei
                                    </span>
                                @endif
                            </td>
                            <td>
                                <div style="font-size: 12px; font-weight: 700; color: #002855;">
                                    <i class="fas fa-user-hard-hat" style="color: #d69e00; font-size: 11px;"></i>
                                    {{ $row->petugas ? $row->petugas->name : 'Petugas Lapangan' }}
                                </div>
                            </td>
                            <td style="text-align: center;">
                                <button type="button" class="btn btn-outline" style="padding: 4px 10px; font-size: 11px; font-weight: 700; border-radius: 6px; border: 1px solid #002855; color: #002855; background: #ffffff; cursor: pointer;" data-row="{{ json_encode($row) }}" onclick="openDetailKelayakanModal(JSON.parse(this.getAttribute('data-row')))">
                                    <i class="fas fa-eye"></i> Detail
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" style="text-align: center; padding: 40px; color: #94a3b8;">
                                <i class="fas fa-inbox" style="font-size: 32px; display: block; margin-bottom: 10px; opacity: 0.4;"></i>
                                Tidak ada data penerima yang cocok dengan kriteria pencarian/filter.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination Bar Custom -->
            @if($penerimaList->hasPages() || $penerimaList->total() > 0)
            <div class="pagination-custom-bar" style="padding: 14px 20px; border-top: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px; background: #f8fafc;">
                <div class="pagination-info-text" style="font-size: 12.5px; color: #64748b;">
                    Menampilkan <strong>{{ $penerimaList->firstItem() ?? 0 }}</strong> -
                    <strong>{{ $penerimaList->lastItem() ?? 0 }}</strong> dari
                    <strong>{{ number_format($penerimaList->total(), 0, ',', '.') }}</strong> calon penerima
                    @if($penerimaList->lastPage() > 1)
                        (Halaman <strong>{{ $penerimaList->currentPage() }}</strong> dari <strong>{{ $penerimaList->lastPage() }}</strong>)
                    @endif
                </div>

                @if($penerimaList->lastPage() > 1)
                    @php
                        $current = $penerimaList->currentPage();
                        $last = $penerimaList->lastPage();
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
                        @if($penerimaList->onFirstPage())
                            <li><span class="page-btn disabled" style="padding: 6px 10px; border-radius: 6px; border: 1px solid #cbd5e1; color: #cbd5e1; display: inline-block;"><i class="fas fa-chevron-left"></i></span></li>
                        @else
                            <li><a href="{{ $penerimaList->previousPageUrl() }}" class="page-btn" style="padding: 6px 10px; border-radius: 6px; border: 1px solid #cbd5e1; color: #002855; text-decoration: none; display: inline-block;"><i class="fas fa-chevron-left"></i></a></li>
                        @endif

                        @foreach($rangeWithDots as $page)
                            @if($page === '...')
                                <li><span class="page-dots" style="padding: 6px 8px; color: #94a3b8; display: inline-block;">...</span></li>
                            @elseif($page == $current)
                                <li><span class="page-btn active" style="padding: 6px 12px; border-radius: 6px; background: #002855; color: #ffffff; font-weight: 700; display: inline-block;">{{ $page }}</span></li>
                            @else
                                <li><a href="{{ $penerimaList->url($page) }}" class="page-btn" style="padding: 6px 12px; border-radius: 6px; border: 1px solid #cbd5e1; color: #002855; text-decoration: none; font-weight: 600; display: inline-block;">{{ $page }}</a></li>
                            @endif
                        @endforeach

                        @if($penerimaList->hasMorePages())
                            <li><a href="{{ $penerimaList->nextPageUrl() }}" class="page-btn" style="padding: 6px 10px; border-radius: 6px; border: 1px solid #cbd5e1; color: #002855; text-decoration: none; display: inline-block;"><i class="fas fa-chevron-right"></i></a></li>
                        @else
                            <li><span class="page-btn disabled" style="padding: 6px 10px; border-radius: 6px; border: 1px solid #cbd5e1; color: #cbd5e1; display: inline-block;"><i class="fas fa-chevron-right"></i></span></li>
                        @endif
                    </ul>
                @endif
            </div>
            @endif
        </div>
    </main>

    <!-- Modal Detail Kelayakan -->
    <div class="modal-overlay" id="modalDetailKelayakan">
        <div class="modal-box" style="max-width: 700px; border-radius: 12px;">
            <div class="modal-header" style="border-bottom: 1px solid rgba(0,40,85,0.1); padding: 16px 24px;">
                <h3 style="margin: 0; font-size: 16px; font-weight: 800; color: #002855;">
                    <i class="fas fa-file-invoice" style="color: var(--primary); margin-right: 8px;"></i>
                    Detail Data Kelayakan
                </h3>
                <button class="close-btn" type="button" onclick="window.PuprModal.close('modalDetailKelayakan')" style="background: none; border: none; font-size: 16px; cursor: pointer; color: #64748b;">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body" style="padding: 24px; max-height: 70vh; overflow-y: auto;">
                <div class="info-section" style="margin-bottom: 20px;">
                    <h4 style="font-size: 14px; font-weight: 700; color: #002855; margin-bottom: 12px; border-bottom: 2px solid #e2e8f0; padding-bottom: 6px;">Informasi Warga</h4>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                        <div style="grid-column: span 2;">
                            <div style="font-size: 11px; color: #64748b; font-weight: 600;">Nama Lengkap</div>
                            <div id="mdl_nama" style="font-size: 13px; font-weight: 700; color: #0f172a;">-</div>
                        </div>
                        <div>
                            <div style="font-size: 11px; color: #64748b; font-weight: 600;">NIK</div>
                            <div id="mdl_nik" style="font-size: 13px; font-weight: 700; color: #0f172a; font-family: monospace;">-</div>
                        </div>
                        <div>
                            <div style="font-size: 11px; color: #64748b; font-weight: 600;">No. KK</div>
                            <div id="mdl_kk" style="font-size: 13px; font-weight: 700; color: #0f172a; font-family: monospace;">-</div>
                        </div>
                        <div>
                            <div style="font-size: 11px; color: #64748b; font-weight: 600;">Tempat, Tanggal Lahir</div>
                            <div id="mdl_ttl" style="font-size: 13px; font-weight: 600; color: #0f172a;">-</div>
                        </div>
                        <div>
                            <div style="font-size: 11px; color: #64748b; font-weight: 600;">Jenis Kelamin</div>
                            <div id="mdl_jk" style="font-size: 13px; font-weight: 600; color: #0f172a;">-</div>
                        </div>
                        <div style="grid-column: span 2;">
                            <div style="font-size: 11px; color: #64748b; font-weight: 600;">Alamat Lengkap</div>
                            <div id="mdl_alamat" style="font-size: 13px; font-weight: 600; color: #0f172a;">-</div>
                        </div>
                        <div>
                            <div style="font-size: 11px; color: #64748b; font-weight: 600;">Status Tanah</div>
                            <div id="mdl_status_tanah" style="font-size: 13px; font-weight: 600; color: #0f172a;">-</div>
                        </div>
                        <div>
                            <div style="font-size: 11px; color: #64748b; font-weight: 600;">Luas Tanah</div>
                            <div id="mdl_luas_tanah" style="font-size: 13px; font-weight: 600; color: #0f172a;">-</div>
                        </div>
                        <div>
                            <div style="font-size: 11px; color: #64748b; font-weight: 600;">Telah Ditempati Selama</div>
                            <div id="mdl_lama_menempati" style="font-size: 13px; font-weight: 600; color: #0f172a;">-</div>
                        </div>
                        <div>
                            <div style="font-size: 11px; color: #64748b; font-weight: 600;">Penghasilan / Pekerjaan</div>
                            <div id="mdl_penghasilan" style="font-size: 13px; font-weight: 600; color: #0f172a;">-</div>
                        </div>
                        <div style="grid-column: span 2;">
                            <div style="font-size: 11px; color: #64748b; font-weight: 600;">Titik Koordinat (Lat, Long)</div>
                            <div id="mdl_koordinat" style="font-size: 13px; font-weight: 600; color: #0f172a;">-</div>
                        </div>
                    </div>
                </div>

                <div class="info-section" style="margin-bottom: 20px;">
                    <h4 style="font-size: 14px; font-weight: 700; color: #002855; margin-bottom: 12px; border-bottom: 2px solid #e2e8f0; padding-bottom: 6px;">Dokumen & Foto Lapangan</h4>
                    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(100px, 1fr)); gap: 12px;" id="mdl_photos_container">
                        <!-- Photos will be injected here -->
                    </div>
                </div>

                @if(auth()->check() && auth()->user()->isAdmin())
                <div class="info-section">
                    <h4 style="font-size: 14px; font-weight: 700; color: #002855; margin-bottom: 12px; border-bottom: 2px solid #e2e8f0; padding-bottom: 6px;">Update Indikator Kelayakan</h4>
                    <form id="formUpdateKelayakan">
                        @csrf
                        @method('PUT')
                        <input type="hidden" id="mdl_id_penerima" name="id">
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 16px;">
                            <div>
                                <label style="font-size: 11px; font-weight: 700; color: #64748b;">Atap</label>
                                <select id="mdl_ind_atap" name="indikator_atap" style="width: 100%; padding: 8px; border-radius: 6px; border: 1px solid #cbd5e1; font-size: 13px;">
                                    <option value="ada">Baik</option>
                                    <option value="tidak_ada">Rusak</option>
                                </select>
                            </div>
                            <div>
                                <label style="font-size: 11px; font-weight: 700; color: #64748b;">Dinding</label>
                                <select id="mdl_ind_dinding" name="indikator_dinding" style="width: 100%; padding: 8px; border-radius: 6px; border: 1px solid #cbd5e1; font-size: 13px;">
                                    <option value="ada">Baik</option>
                                    <option value="tidak_ada">Rusak</option>
                                </select>
                            </div>
                            <div>
                                <label style="font-size: 11px; font-weight: 700; color: #64748b;">Lantai</label>
                                <select id="mdl_ind_lantai" name="indikator_lantai" style="width: 100%; padding: 8px; border-radius: 6px; border: 1px solid #cbd5e1; font-size: 13px;">
                                    <option value="ada">Baik</option>
                                    <option value="tidak_ada">Tanah/Rusak</option>
                                </select>
                            </div>
                            <div>
                                <label style="font-size: 11px; font-weight: 700; color: #64748b;">Pondasi</label>
                                <select id="mdl_ind_pondasi" name="indikator_pondasi" style="width: 100%; padding: 8px; border-radius: 6px; border: 1px solid #cbd5e1; font-size: 13px;">
                                    <option value="ada">Baik</option>
                                    <option value="tidak_ada">Rusak</option>
                                </select>
                            </div>
                            <div>
                                <label style="font-size: 11px; font-weight: 700; color: #64748b;">Struktur</label>
                                <select id="mdl_ind_struktur" name="indikator_struktur" style="width: 100%; padding: 8px; border-radius: 6px; border: 1px solid #cbd5e1; font-size: 13px;">
                                    <option value="ada">Baik</option>
                                    <option value="tidak_ada">Rusak</option>
                                </select>
                            </div>
                            <div>
                                <label style="font-size: 11px; font-weight: 700; color: #64748b;">Penghasilan (Dibawah UMK)</label>
                                <select id="mdl_ind_penghasilan" name="indikator_penghasilan" style="width: 100%; padding: 8px; border-radius: 6px; border: 1px solid #cbd5e1; font-size: 13px;">
                                    <option value="ada">Ya (< UMK)</option>
                                    <option value="tidak_ada">Tidak (> UMK)</option>
                                </select>
                            </div>
                        </div>
                        <div style="text-align: right;">
                            <button type="button" id="btnUpdateKelayakan" class="btn btn-primary" style="padding: 10px 20px; border-radius: 8px; background: #002855; color: white; border: none; font-weight: 700; cursor: pointer;">
                                Simpan Perubahan Indikator
                            </button>
                        </div>
                    </form>
                </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function openDetailKelayakanModal(data) {
        document.getElementById('mdl_nama').textContent = data.nama || '-';
        document.getElementById('mdl_nik').textContent = data.no_ktp || '-';
        document.getElementById('mdl_kk').textContent = data.no_kk || '-';
        
        let tglLahir = data.tanggal_lahir || '-';
        if (data.tanggal_lahir) {
            // Optional: format date if needed, otherwise just display
            tglLahir = data.tanggal_lahir;
        }
        document.getElementById('mdl_ttl').textContent = (data.tempat_lahir || '-') + ', ' + tglLahir;
        
        document.getElementById('mdl_jk').textContent = data.jenis_kelamin === 'L' ? 'Laki-Laki' : (data.jenis_kelamin === 'P' ? 'Perempuan' : '-');
        document.getElementById('mdl_alamat').textContent = (data.alamat || '-') + ', Ds. ' + (data.desa_kelurahan || '-') + ', Kec. ' + (data.kecamatan || '-');
        document.getElementById('mdl_status_tanah').textContent = data.status_tanah || '-';
        document.getElementById('mdl_luas_tanah').textContent = data.luas_tanah ? data.luas_tanah + ' m²' : '-';
        document.getElementById('mdl_lama_menempati').textContent = data.telah_ditempati_selama ? data.telah_ditempati_selama + ' Tahun' : '-';
        
        let teksPenghasilan = data.penghasilan || '-';
        if (data.pekerjaan && data.pekerjaan !== '-') {
            teksPenghasilan += ' / ' + data.pekerjaan;
        }
        document.getElementById('mdl_penghasilan').textContent = teksPenghasilan;

        document.getElementById('mdl_koordinat').innerHTML = (data.latitude && data.longitude) ? `<a href="https://maps.google.com/?q=${data.latitude},${data.longitude}" target="_blank" style="color:var(--primary);text-decoration:none;"><i class="fas fa-map-marker-alt"></i> ${data.latitude}, ${data.longitude}</a>` : '-';

        
        @if(auth()->check() && auth()->user()->isAdmin())
        document.getElementById('mdl_id_penerima').value = data.id;
        document.getElementById('mdl_ind_atap').value = data.indikator_atap || 'ada';
        document.getElementById('mdl_ind_dinding').value = data.indikator_dinding || 'ada';
        document.getElementById('mdl_ind_lantai').value = data.indikator_lantai || 'ada';
        document.getElementById('mdl_ind_pondasi').value = data.indikator_pondasi || 'ada';
        document.getElementById('mdl_ind_struktur').value = data.indikator_struktur || 'ada';
        document.getElementById('mdl_ind_penghasilan').value = data.indikator_penghasilan || 'ada';
        @endif

        const photosContainer = document.getElementById('mdl_photos_container');
        photosContainer.innerHTML = '';
        
        const fotoFields = [
            { key: 'ktp', label: 'KTP' },
            { key: 'kk', label: 'Kartu Keluarga' },
            { key: 'surat_pernyataan', label: 'Surat Pernyataan' },
            { key: 'sertifikat_tanah', label: 'Bukti Lahan' },
            { key: 'foto_sudut_depan', label: 'S. Depan' },
            { key: 'foto_sudut_belakang', label: 'S. Belakang' },
            { key: 'foto_bagian_dalam', label: 'B. Dalam' },
            { key: 'foto_sudut_kiri', label: 'S. Kiri' },
            { key: 'foto_sudut_kanan', label: 'S. Kanan' }
        ];

        fotoFields.forEach(f => {
            if (data[f.key]) {
                // Determine base URL, assuming it starts with 'uploads/'
                let url = data[f.key];
                if (!url.startsWith('http')) {
                    url = '/' + url;
                }
                const isVerifiable = f.key.startsWith('foto_') || f.key === 'ktp' || f.key === 'kk' || f.key === 'surat_pernyataan';
                
                let extraHtml = '';
                @if(auth()->check() && auth()->user()->isAdmin())
                if(isVerifiable) {
                    const statusKey = 'status_' + f.key;
                    const catatanKey = 'catatan_' + f.key;
                    const currentStatus = data[statusKey] || '';
                    const currentCatatan = data[catatanKey] || '';
                    
                    extraHtml = `
                        <div style="margin-top: 8px; text-align: left;">
                            <label style="font-size:10px; font-weight:700; color:#64748b;">Status</label>
                            <select id="mdl_ind_${statusKey}" style="width:100%; font-size:11px; padding:4px; border-radius:4px; border:1px solid #cbd5e1; margin-bottom:4px;">
                                <option value="">- Pilih Status -</option>
                                <option value="layak" ${currentStatus === 'layak' ? 'selected' : ''}>Terverifikasi</option>
                                <option value="tidak layak" ${currentStatus === 'tidak layak' ? 'selected' : ''}>Ditolak</option>
                            </select>
                            <div id="wrapper_${catatanKey}" style="display: ${currentStatus === 'tidak layak' ? 'block' : 'none'};">
                                <label style="font-size:10px; font-weight:700; color:#64748b;">Catatan Ditolak</label>
                                <textarea id="mdl_ind_${catatanKey}" style="width:100%; font-size:11px; padding:4px; border-radius:4px; border:1px solid #cbd5e1; resize:none;" rows="2">${currentCatatan}</textarea>
                            </div>
                        </div>
                    `;
                }
                @endif

                const item = document.createElement('div');
                item.style.textAlign = 'center';
                item.style.padding = '8px';
                item.style.border = '1px solid #e2e8f0';
                item.style.borderRadius = '8px';
                item.style.background = '#f8fafc';
                let isPdf = url.toLowerCase().endsWith('.pdf');
                let mediaHtml = isPdf ? 
                    `<div style="width:100%; height:100px; display:flex; flex-direction:column; align-items:center; justify-content:center; background:#f8fafc; border-radius:4px; border:1px dashed #cbd5e1; color:#e11d48; font-size:24px;"><i class="fas fa-file-pdf"></i><span style="font-size:10px; font-weight:700; color:#64748b; margin-top:4px;">PDF</span></div>` : 
                    `<img src="${url}" alt="${f.label}" style="width:100%; height:100px; object-fit:cover; border-radius:4px; border:1px solid #cbd5e1;"/>`;

                item.innerHTML = `
                    <a href="${url}" target="_blank" style="display:block; margin-bottom:4px; text-decoration:none;">
                        ${mediaHtml}
                    </a>
                    <span style="font-size:11px; font-weight:700; color:#002855; display:block; margin-bottom:4px;">${f.label}</span>
                    ${extraHtml}
                `;
                photosContainer.appendChild(item);
                
                @if(auth()->check() && auth()->user()->isAdmin())
                if(isVerifiable) {
                    const selectEl = document.getElementById('mdl_ind_status_' + f.key);
                    const wrapperEl = document.getElementById('wrapper_catatan_' + f.key);
                    if(selectEl) {
                        selectEl.addEventListener('change', function() {
                            if(this.value === 'tidak layak') {
                                wrapperEl.style.display = 'block';
                            } else {
                                wrapperEl.style.display = 'none';
                                document.getElementById('mdl_ind_catatan_' + f.key).value = '';
                            }
                        });
                    }
                }
                @endif
            }
        });

        if(photosContainer.innerHTML === '') {
            photosContainer.innerHTML = '<div style="grid-column: span 3; font-size:12px; color:#94a3b8; font-style:italic;">Belum ada foto/dokumen.</div>';
        }

        window.PuprModal.open('modalDetailKelayakan');
    }

    @if(auth()->check() && auth()->user()->isAdmin())
    document.getElementById('btnUpdateKelayakan').addEventListener('click', function() {
        const btn = this;
        const id = document.getElementById('mdl_id_penerima').value;
        const payload = {
            indikator_atap: document.getElementById('mdl_ind_atap').value,
            indikator_dinding: document.getElementById('mdl_ind_dinding').value,
            indikator_lantai: document.getElementById('mdl_ind_lantai').value,
            indikator_pondasi: document.getElementById('mdl_ind_pondasi').value,
            indikator_struktur: document.getElementById('mdl_ind_struktur').value,
            indikator_penghasilan: document.getElementById('mdl_ind_penghasilan').value
        };
        
        const fotoFieldsKeys = ['foto_sudut_depan', 'foto_sudut_belakang', 'foto_bagian_dalam', 'foto_sudut_kiri', 'foto_sudut_kanan', 'ktp', 'kk', 'surat_pernyataan'];
        fotoFieldsKeys.forEach(k => {
            const statusEl = document.getElementById('mdl_ind_status_' + k);
            const catatanEl = document.getElementById('mdl_ind_catatan_' + k);
            if(statusEl) {
                payload['status_' + k] = statusEl.value;
                payload['catatan_' + k] = catatanEl ? catatanEl.value : '';
            }
        });

        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';

        fetch(`/dashboard/data-kelayakan/${id}/status`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify(payload)
        })
        .then(res => res.json())
        .then(res => {
            if(res.success) {
                alert('Indikator berhasil diupdate!');
                window.location.reload();
            } else {
                alert('Gagal update indikator: ' + (res.message || 'Error'));
                btn.disabled = false;
                btn.innerHTML = 'Simpan Perubahan Indikator';
            }
        })
        .catch(err => {
            alert('Terjadi kesalahan koneksi.');
            btn.disabled = false;
            btn.innerHTML = 'Simpan Perubahan Indikator';
        });
    });
    @endif
</script>
@endpush
