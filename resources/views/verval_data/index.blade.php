@extends('layouts.partial.app')

@section('title', 'BSPS Verval - Data Verifikasi & Validasi')
@section('title_header', 'Data Verval BSPS')
@section('subtitle_header', 'Daftar Calon Penerima Bantuan Stimulan Perumahan Swadaya & Status Verifikasi Lapangan RTLH')

@push('styles')
<style>
    /* Stats Grid */
    .stats-verval {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
        margin-bottom: 24px;
    }

    .stat-item {
        background: var(--bg-card);
        border-radius: var(--radius-sm);
        padding: 18px 20px;
        box-shadow: var(--shadow-sm);
        border: 1px solid rgba(0, 40, 85, 0.06);
        display: flex;
        align-items: center;
        gap: 14px;
        transition: var(--transition);
    }

    .stat-item:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
    }

    .stat-item .icon {
        width: 46px;
        height: 46px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        flex-shrink: 0;
    }

    .stat-item .icon.blue { background: rgba(0, 40, 85, 0.10); color: var(--primary); }
    .stat-item .icon.green { background: rgba(39, 174, 96, 0.12); color: var(--success); }
    .stat-item .icon.orange { background: rgba(255, 184, 0, 0.15); color: #d69e00; }
    .stat-item .icon.purple { background: rgba(142, 68, 173, 0.12); color: var(--purple, #8e44ad); }

    .stat-item .info .value {
        font-size: 24px;
        font-weight: 800;
        line-height: 1.2;
        color: var(--primary-dark);
    }

    .stat-item .info .label {
        font-size: 12px;
        color: var(--text-muted);
        font-weight: 600;
        margin-top: 2px;
    }

    /* Filter Section */
    .filter-section {
        background: var(--bg-card);
        border-radius: var(--radius);
        padding: 16px 20px;
        box-shadow: var(--shadow-sm);
        border: 1px solid rgba(0, 40, 85, 0.06);
        margin-bottom: 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
    }

    .filter-left {
        display: flex;
        align-items: center;
        gap: 12px;
        flex: 1;
        flex-wrap: wrap;
    }

    .search-input-wrap {
        position: relative;
        min-width: 260px;
        flex: 1;
    }

    .search-input-wrap input {
        width: 100%;
        padding: 10px 14px 10px 38px;
        border-radius: var(--radius-sm);
        border: 1px solid rgba(0, 40, 85, 0.14);
        background: var(--bg-body);
        color: var(--text-primary);
        font-size: 13.5px;
        outline: none;
        transition: all 0.2s ease;
        box-sizing: border-box;
    }

    .search-input-wrap input:focus {
        border-color: var(--primary);
        background: var(--bg-card);
        box-shadow: 0 0 0 3px rgba(0, 40, 85, 0.08);
    }

    .search-input-wrap i {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--text-muted);
        font-size: 14px;
    }

    .filter-select {
        padding: 10px 14px;
        border-radius: var(--radius-sm);
        border: 1px solid rgba(0, 40, 85, 0.14);
        background: var(--bg-body);
        color: var(--text-primary);
        font-size: 13px;
        font-weight: 600;
        outline: none;
        cursor: pointer;
    }

    /* Table Container */
    .table-container-card {
        background: var(--bg-card);
        border-radius: var(--radius);
        box-shadow: var(--shadow-sm);
        border: 1px solid rgba(0, 40, 85, 0.06);
        overflow: hidden;
    }

    .table-header-bar {
        padding: 18px 24px;
        border-bottom: 1px solid rgba(0, 40, 85, 0.06);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
    }

    .table-header-bar h3 {
        font-size: 16px;
        font-weight: 800;
        color: var(--primary);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .badge-status-verval {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 700;
    }

    .badge-status-verval.selesai { background: rgba(39, 174, 96, 0.12); color: var(--success); }
    .badge-status-verval.proses  { background: rgba(255, 184, 0, 0.15); color: #d69e00; }
    .badge-status-verval.survei  { background: rgba(142, 68, 173, 0.12); color: #8e44ad; }
    .badge-status-verval.menunggu{ background: rgba(0, 40, 85, 0.08); color: var(--primary); }

    .action-btn-group {
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .btn-act {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        border: none;
        cursor: pointer;
        text-decoration: none;
        transition: all 0.2s ease;
    }

    .btn-act.view { background: rgba(0, 40, 85, 0.08); color: var(--primary); }
    .btn-act.view:hover { background: var(--primary); color: #fff; }
    .btn-act.map  { background: rgba(39, 174, 96, 0.12); color: var(--success); }
    .btn-act.map:hover { background: var(--success); color: #fff; }

    @media (max-width: 1024px) {
        .stats-verval { grid-template-columns: repeat(2, 1fr); }
    }

    @media (max-width: 768px) {
        .filter-section { flex-direction: column; align-items: stretch; }
        .filter-left { flex-direction: column; }
        .search-input-wrap { width: 100%; }
        .filter-select { width: 100%; }
    }
</style>
@endpush

@section('content')
    @include('layouts.navbar')

    <main class="dashboard-content">
        <div class="breadcrumb" style="font-size:13px;color:var(--text-muted);margin-bottom:20px;display:flex;align-items:center;gap:8px;">
            <a href="{{ url('/dashboard') }}" style="color:var(--primary);text-decoration:none;font-weight:600;"><i class="fas fa-home"></i> Dashboard</a>
            <i class="fas fa-chevron-right" style="font-size:10px;"></i>
            <span>Data Verval BSPS</span>
        </div>

        <!-- 4 Stat Counters -->
        <div class="stats-verval">
            <div class="stat-item">
                <div class="icon blue"><i class="fas fa-house-user"></i></div>
                <div class="info">
                    <div class="value">{{ $stats['total'] }}</div>
                    <div class="label">Total Usulan RTLH</div>
                </div>
            </div>
            <div class="stat-item">
                <div class="icon green"><i class="fas fa-circle-check"></i></div>
                <div class="info">
                    <div class="value">{{ $stats['layak'] }}</div>
                    <div class="label">Layak Bantuan (PK)</div>
                </div>
            </div>
            <div class="stat-item">
                <div class="icon orange"><i class="fas fa-clock-rotate-left"></i></div>
                <div class="info">
                    <div class="value">{{ $stats['proses'] }}</div>
                    <div class="label">Proses Verifikasi</div>
                </div>
            </div>
            <div class="stat-item">
                <div class="icon purple"><i class="fas fa-user-clock"></i></div>
                <div class="info">
                    <div class="value">{{ $stats['menunggu'] }}</div>
                    <div class="label">Belum Disurvei TFL</div>
                </div>
            </div>
        </div>

        <!-- Filter & Search Bar -->
        <form action="{{ url('/verval-data') }}" method="GET" class="filter-section">
            <div class="filter-left">
                <div class="search-input-wrap">
                    <i class="fas fa-search"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama calon penerima, NIK, No Berkas..." />
                </div>
                <select name="status" class="filter-select" onchange="this.form.submit()">
                    <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>-- Semua Status --</option>
                    <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>Selesai / Layak</option>
                    <option value="proses" {{ request('status') == 'proses' ? 'selected' : '' }}>Proses Verifikasi</option>
                    <option value="survei" {{ request('status') == 'survei' ? 'selected' : '' }}>Sedang Disurvei</option>
                    <option value="menunggu" {{ request('status') == 'menunggu' ? 'selected' : '' }}>Belum Disurvei</option>
                </select>
                <select name="kecamatan" class="filter-select" onchange="this.form.submit()">
                    <option value="all" {{ request('kecamatan') == 'all' ? 'selected' : '' }}>-- Semua Kecamatan --</option>
                    @foreach(['Kaliwates', 'Patrang', 'Sumbersari', 'Rambipuji', 'Arjasa', 'Ajung'] as $kec)
                        <option value="{{ $kec }}" {{ strtolower(request('kecamatan')) == strtolower($kec) ? 'selected' : '' }}>Kec. {{ $kec }}</option>
                    @endforeach
                </select>
            </div>
            <div style="display:flex;gap:10px;">
                <a href="{{ url('/verval-data') }}" class="btn btn-outline" style="padding:10px 16px;font-size:13px;text-decoration:none;border-radius:var(--radius-sm);">
                    <i class="fas fa-redo"></i> Reset
                </a>
                <a href="{{ url('/survey') }}" class="btn btn-primary" style="padding:10px 20px;font-size:13px;font-weight:700;background:var(--primary);color:#fff;text-decoration:none;border-radius:var(--radius-sm);display:inline-flex;align-items:center;gap:8px;">
                    <i class="fas fa-plus"></i> Input Survei Baru
                </a>
            </div>
        </form>

        <!-- Main Data Table -->
        <div class="table-container-card">
            <div class="table-header-bar">
                <h3><i class="fas fa-clipboard-list"></i> Daftar Hasil Verifikasi &amp; Validasi BSPS</h3>
                <span style="font-size:12.5px;color:var(--text-muted);font-weight:600;">Menampilkan {{ $vervals->count() }} dari {{ $stats['total'] }} data</span>
            </div>

            <div style="overflow-x:auto;">
                <table class="table" style="width:100%;border-collapse:collapse;">
                    <thead>
                        <tr style="background:var(--bg-body);border-bottom:1px solid rgba(0,40,85,0.08);text-align:left;font-size:12.5px;color:var(--text-muted);">
                            <th style="padding:14px 18px;">No. Berkas</th>
                            <th style="padding:14px 18px;">Calon Penerima (KK)</th>
                            <th style="padding:14px 18px;">Lokasi / Alamat Rumah</th>
                            <th style="padding:14px 18px;">Luas &amp; Status Tanah</th>
                            <th style="padding:14px 18px;">Fasilitator (TFL)</th>
                            <th style="padding:14px 18px;">Status &amp; Rekomendasi</th>
                            <th style="padding:14px 18px;text-align:center;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($vervals as $item)
                            <tr style="border-bottom:1px solid rgba(0,40,85,0.06);font-size:13px;transition:all 0.15s ease;">
                                <td style="padding:14px 18px;">
                                    <div style="font-weight:700;color:var(--primary-dark);">{{ $item->no_berkas }}</div>
                                    <div style="font-size:11px;color:var(--text-muted);margin-top:2px;"><i class="fas fa-calendar"></i> {{ $item->tanggal_survei }}</div>
                                </td>
                                <td style="padding:14px 18px;">
                                    <div style="font-weight:800;color:var(--text-primary);">{{ $item->nama_pemohon }}</div>
                                    <div style="font-size:11.5px;color:var(--text-muted);margin-top:2px;">NIK: {{ $item->nik_pemohon }}</div>
                                </td>
                                <td style="padding:14px 18px;">
                                    <div style="font-weight:600;color:var(--primary-dark);">Kec. {{ $item->lokasi }}</div>
                                    <div style="font-size:11.5px;color:var(--text-muted);margin-top:2px;">{{ $item->desa }} - {{ $item->alamat }}</div>
                                </td>
                                <td style="padding:14px 18px;">
                                    <div style="font-weight:600;">{{ $item->luas_rumah }}</div>
                                    <div style="font-size:11px;color:var(--text-muted);margin-top:2px;">{{ $item->status_tanah }}</div>
                                </td>
                                <td style="padding:14px 18px;">
                                    <div style="font-weight:600;color:var(--text-primary);">
                                        <i class="fas fa-user-hard-hat" style="color:var(--primary);font-size:11px;margin-right:4px;"></i>
                                        {{ $item->tfl }}
                                    </div>
                                </td>
                                <td style="padding:14px 18px;">
                                    <span class="badge-status-verval {{ $item->status }}">
                                        <i class="fas fa-circle" style="font-size:6px;"></i>
                                        {{ $item->rekomendasi }}
                                    </span>
                                </td>
                                <td style="padding:14px 18px;text-align:center;">
                                    <div class="action-btn-group" style="justify-content:center;">
                                        <a href="{{ url('/survey?kegiatan_id=' . $item->id) }}" class="btn-act view" title="Buka Detail & Form Survei">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ url('/geomaps') }}" class="btn-act map" title="Lihat Peta Koordinat GPS">
                                            <i class="fas fa-map-marker-alt"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" style="text-align:center;padding:32px;color:var(--text-muted);">
                                    <i class="fas fa-clipboard-question" style="font-size:28px;display:block;margin-bottom:8px;opacity:0.4;"></i>
                                    Tidak ditemukan data verval yang sesuai dengan filter pencarian.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </main>
@endsection
