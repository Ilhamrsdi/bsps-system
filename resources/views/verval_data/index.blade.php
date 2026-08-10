@extends('layouts.partial.app')

@section('title', 'BSPS Verval - Data Verifikasi & Validasi')
@section('title_header', 'Data Verifikasi & Validasi (Verval)')
@section('subtitle_header', 'Basis Data Calon Penerima Bantuan Stimulan Perumahan Swadaya (BSPS)')

@push('styles')
<style>
    .verval-page-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 24px;
        flex-wrap: wrap;
        gap: 16px;
    }

    .verval-stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
        margin-bottom: 24px;
    }

    .stat-mini-card {
        background: var(--bg-card);
        border-radius: var(--radius);
        padding: 18px 20px;
        box-shadow: var(--shadow-sm);
        border: 1px solid rgba(0, 40, 85, 0.06);
        display: flex;
        align-items: center;
        gap: 14px;
        transition: var(--transition);
    }

    .stat-mini-card:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
    }

    .stat-mini-icon {
        width: 46px;
        height: 46px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        flex-shrink: 0;
    }

    .stat-mini-info .stat-number {
        font-size: 22px;
        font-weight: 800;
        color: var(--primary);
        line-height: 1.1;
    }

    .stat-mini-info .stat-label {
        font-size: 12px;
        color: var(--text-muted);
        font-weight: 600;
        margin-top: 3px;
    }

    /* Filter & Search Bar */
    .filter-card {
        background: var(--bg-card);
        border-radius: var(--radius);
        padding: 18px 22px;
        margin-bottom: 20px;
        box-shadow: var(--shadow-sm);
        border: 1px solid rgba(0, 40, 85, 0.06);
    }

    .filter-form {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        align-items: center;
    }

    .filter-input-search {
        flex: 1;
        min-width: 240px;
        position: relative;
    }

    .filter-input-search input {
        width: 100%;
        padding: 10px 14px 10px 38px;
        border: 1px solid rgba(0, 40, 85, 0.15);
        border-radius: var(--radius-sm);
        background: var(--bg-body);
        color: var(--text-primary);
        font-size: 13.5px;
    }

    .filter-input-search i {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--text-muted);
    }

    .filter-select {
        padding: 10px 14px;
        border: 1px solid rgba(0, 40, 85, 0.15);
        border-radius: var(--radius-sm);
        background: var(--bg-body);
        color: var(--text-primary);
        font-size: 13.5px;
        min-width: 170px;
        cursor: pointer;
    }

    /* Table & Actions */
    .table-container-card {
        background: var(--bg-card);
        border-radius: var(--radius);
        box-shadow: var(--shadow-sm);
        border: 1px solid rgba(0, 40, 85, 0.06);
        overflow: hidden;
    }

    .table-header-custom {
        padding: 16px 22px;
        border-bottom: 1px solid rgba(0, 40, 85, 0.06);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .table-responsive {
        width: 100%;
        overflow-x: auto;
    }

    .data-table-verval {
        width: 100%;
        border-collapse: collapse;
        font-size: 13.5px;
        text-align: left;
    }

    .data-table-verval th {
        background: rgba(0, 40, 85, 0.03);
        color: var(--primary-dark);
        font-weight: 700;
        padding: 12px 18px;
        border-bottom: 1.5px solid rgba(0, 40, 85, 0.08);
        white-space: nowrap;
    }

    .data-table-verval td {
        padding: 14px 18px;
        border-bottom: 1px solid rgba(0, 40, 85, 0.05);
        vertical-align: middle;
    }

    .data-table-verval tbody tr:hover {
        background: rgba(0, 40, 85, 0.02);
    }

    .nik-badge {
        font-family: monospace;
        font-size: 12px;
        background: rgba(0, 40, 85, 0.06);
        padding: 2px 6px;
        border-radius: 4px;
        color: var(--primary);
    }

    .score-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        font-weight: 800;
        font-size: 12.5px;
        padding: 3px 10px;
        border-radius: 20px;
    }

    .score-high {
        background: rgba(39, 174, 96, 0.12);
        color: #27ae60;
    }

    .score-mid {
        background: rgba(243, 156, 18, 0.12);
        color: #d68910;
    }

    .score-low {
        background: rgba(231, 76, 60, 0.12);
        color: #e74c3c;
    }

    @media (max-width: 992px) {
        .verval-stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 576px) {
        .verval-stats-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')
<div class="content-body" style="padding: 24px;">

    <!-- Breadcrumb & Title -->
    <div class="verval-page-header">
        <div>
            <div style="font-size: 12px; font-weight: 700; color: var(--secondary); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 4px;">
                <i class="fas fa-database"></i> Verifikasi &amp; Validasi BSPS
            </div>
            <h1 style="font-size: 24px; font-weight: 900; color: var(--primary); margin: 0;">
                Data Calon Penerima Bantuan (BNBA)
            </h1>
        </div>

        <div style="display: flex; gap: 10px;">
            <button class="btn btn-primary" onclick="alert('Fitur Sinkronisasi Data BNBA Berhasil! Data telah diperbarui.')">
                <i class="fas fa-sync-alt"></i> Sinkronisasi Data
            </button>
            <button class="btn btn-outline" onclick="window.print()">
                <i class="fas fa-print"></i> Cetak Rekap
            </button>
        </div>
    </div>

    <!-- Mini Stat Cards -->
    <div class="verval-stats-grid">
        <div class="stat-mini-card">
            <div class="stat-mini-icon" style="background: rgba(0, 40, 85, 0.08); color: var(--primary);">
                <i class="fas fa-users-viewfinder"></i>
            </div>
            <div class="stat-mini-info">
                <div class="stat-number">{{ number_format($stats['total_usulan']) }}</div>
                <div class="stat-label">Total Usulan BNBA</div>
            </div>
        </div>

        <div class="stat-mini-card">
            <div class="stat-mini-icon" style="background: rgba(39, 174, 96, 0.12); color: var(--success);">
                <i class="fas fa-check-double"></i>
            </div>
            <div class="stat-mini-info">
                <div class="stat-number" style="color: var(--success);">{{ number_format($stats['lolos_verval']) }}</div>
                <div class="stat-label">Memenuhi Syarat (MS)</div>
            </div>
        </div>

        <div class="stat-mini-card">
            <div class="stat-mini-icon" style="background: rgba(243, 156, 18, 0.12); color: var(--warning);">
                <i class="fas fa-hourglass-half"></i>
            </div>
            <div class="stat-mini-info">
                <div class="stat-number" style="color: #d68910;">{{ number_format($stats['menunggu_survei']) }}</div>
                <div class="stat-label">Menunggu Survei</div>
            </div>
        </div>

        <div class="stat-mini-card">
            <div class="stat-mini-icon" style="background: rgba(231, 76, 60, 0.12); color: var(--danger);">
                <i class="fas fa-times-circle"></i>
            </div>
            <div class="stat-mini-info">
                <div class="stat-number" style="color: var(--danger);">{{ number_format($stats['tidak_lolos']) }}</div>
                <div class="stat-label">Tidak Memenuhi Syarat</div>
            </div>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="filter-card">
        <form action="{{ url('/data-verval') }}" method="GET" class="filter-form">
            <div class="filter-input-search">
                <i class="fas fa-search"></i>
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari NIK, Nama Kepala Keluarga, Desa, atau Fasilitator..." />
            </div>

            <select name="status" class="filter-select" onchange="this.form.submit()">
                <option value="">-- Semua Status Verval --</option>
                <option value="Memenuhi Syarat" {{ $status == 'Memenuhi Syarat' ? 'selected' : '' }}>Memenuhi Syarat (MS)</option>
                <option value="Menunggu Survei" {{ $status == 'Menunggu Survei' ? 'selected' : '' }}>Menunggu Survei</option>
                <option value="Perbaikan Berkas" {{ $status == 'Perbaikan Berkas' ? 'selected' : '' }}>Perbaikan Berkas</option>
                <option value="Tidak Memenuhi Syarat" {{ $status == 'Tidak Memenuhi Syarat' ? 'selected' : '' }}>Tidak Memenuhi Syarat (TMS)</option>
            </select>

            <select name="kecamatan" class="filter-select" onchange="this.form.submit()">
                <option value="">-- Semua Kecamatan --</option>
                <option value="Kalisat" {{ $kecamatan == 'Kalisat' ? 'selected' : '' }}>Kalisat</option>
                <option value="Sukowono" {{ $kecamatan == 'Sukowono' ? 'selected' : '' }}>Sukowono</option>
                <option value="Silo" {{ $kecamatan == 'Silo' ? 'selected' : '' }}>Silo</option>
                <option value="Arjasa" {{ $kecamatan == 'Arjasa' ? 'selected' : '' }}>Arjasa</option>
                <option value="Tempurejo" {{ $kecamatan == 'Tempurejo' ? 'selected' : '' }}>Tempurejo</option>
                <option value="Tanggul" {{ $kecamatan == 'Tanggul' ? 'selected' : '' }}>Tanggul</option>
                <option value="Sumberbaru" {{ $kecamatan == 'Sumberbaru' ? 'selected' : '' }}>Sumberbaru</option>
            </select>

            <button type="submit" class="btn btn-primary" style="padding: 10px 18px;">
                <i class="fas fa-filter"></i> Filter
            </button>

            @if(!empty($search) || !empty($status) || !empty($kecamatan))
                <a href="{{ url('/data-verval') }}" class="btn btn-outline" style="padding: 10px 16px;">
                    <i class="fas fa-rotate-left"></i> Reset
                </a>
            @endif
        </form>
    </div>

    <!-- Table Card -->
    <div class="table-container-card">
        <div class="table-header-custom">
            <div style="font-weight: 800; font-size: 15px; color: var(--primary);">
                <i class="fas fa-list-ol" style="margin-right: 6px; color: var(--secondary);"></i>
                Daftar Calon Penerima BSPS ({{ count($vervalData) }} Data Ditampilkan)
            </div>
            <span style="font-size: 12px; color: var(--text-muted);">
                Tahun Anggaran {{ date('Y') }}
            </span>
        </div>

        <div class="table-responsive">
            <table class="data-table-verval">
                <thead>
                    <tr>
                        <th style="width: 50px; text-align: center;">NO</th>
                        <th>NAMA</th>
                        <th>L/P</th>
                        <th>NO KTP</th>
                        <th>NO KK</th>
                        <th>ALAMAT</th>
                        <th>DESA/KELURAHAN</th>
                        <th>KECAMATAN</th>
                        <th style="text-align: center;">STATUS</th>
                        <th style="text-align: center;">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($vervalData as $index => $item)
                        <tr>
                            <td style="font-weight: 700; color: var(--text-muted); text-align: center;">
                                {{ $vervalData->firstItem() + $index }}
                            </td>
                            <td style="font-weight: 700; color: var(--text-primary);">
                                {{ $item->nama ?? '-' }}
                            </td>
                            <td>{{ $item->jenis_kelamin ?? '-' }}</td>
                            <td><span class="nik-badge">{{ $item->no_ktp ?? '-' }}</span></td>
                            <td><span class="nik-badge">{{ $item->no_kk ?? '-' }}</span></td>
                            <td>{{ $item->alamat ?? '-' }}</td>
                            <td>{{ $item->desa_kelurahan ?? '-' }}</td>
                            <td>{{ $item->kecamatan ?? '-' }}</td>
                            <td style="text-align: center;">
                                @php
                                    $currentStatus = $item->status ?? 'ditemukan';
                                    $statusColors = [
                                        'ditemukan' => '#28a745',
                                        'meninggal' => '#343a40',
                                        'pindah' => '#ffc107',
                                        'tidak diketahui' => '#dc3545',
                                    ];
                                    $textColor = $currentStatus == 'pindah' ? '#000' : '#fff';
                                    $bgColor = $statusColors[$currentStatus] ?? '#28a745';
                                @endphp
                                <select class="form-select status-select" data-id="{{ $item->id }}" style="background-color: {{ $bgColor }}; color: {{ $textColor }}; font-weight: bold; border: none; border-radius: 20px; padding: 4px 12px; font-size: 12px; width: 130px; text-align: center; cursor: pointer; outline: none; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                                    <option value="ditemukan" {{ $currentStatus == 'ditemukan' ? 'selected' : '' }} style="background: #fff; color: #000;">Ditemukan</option>
                                    <option value="meninggal" {{ $currentStatus == 'meninggal' ? 'selected' : '' }} style="background: #fff; color: #000;">Meninggal</option>
                                    <option value="pindah" {{ $currentStatus == 'pindah' ? 'selected' : '' }} style="background: #fff; color: #000;">Pindah</option>
                                    <option value="tidak diketahui" {{ $currentStatus == 'tidak diketahui' ? 'selected' : '' }} style="background: #fff; color: #000;">Tidak Diketahui</option>
                                </select>
                            </td>
                            <td style="text-align: center;">
                                <a href="{{ route('data-verval.edit', $item->id) }}" class="btn btn-primary" style="padding: 6px 12px; font-size: 12px; border-radius: 4px;">
                                    <i class="fas fa-edit"></i> Isi Data
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" style="text-align: center; padding: 40px; color: var(--text-muted);">
                                <i class="fas fa-folder-open" style="font-size: 32px; display: block; margin-bottom: 8px; opacity: 0.5;"></i>
                                Tidak ada data BNBA yang cocok dengan kriteria pencarian / filter Anda.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div style="padding: 16px 22px; border-top: 1px solid rgba(0, 40, 85, 0.06);">
            {{ $vervalData->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>

<!-- Modal Detail Calon Penerima BSPS -->
<div class="modal-overlay" id="modalVervalDetail">
    <div class="modal-box" style="max-width: 680px;">
        <div class="modal-header" style="background: var(--primary); color: #fff;">
            <h3 style="color: #fff; display: flex; align-items: center; gap: 8px; font-size: 16px;">
                <i class="fas fa-home-user"></i> Lembar Verifikasi &amp; Validasi BSPS
            </h3>
            <button class="close-btn" style="color:#fff;" type="button" onclick="window.PuprModal.close('modalVervalDetail')">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <div class="modal-body" style="padding: 24px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; padding-bottom: 12px; border-bottom: 1px solid rgba(0,40,85,0.08);">
                <div>
                    <span id="modalVervalId" style="font-size: 16px; font-weight: 800; color: var(--primary);">BSPS-2026-001</span>
                    <span id="modalVervalStatusBadge" style="margin-left: 8px;"></span>
                </div>
                <div id="modalVervalSkor" style="font-weight: 800; font-size: 14px;"></div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 18px;">
                <div>
                    <div style="font-size: 11px; text-transform: uppercase; font-weight: 700; color: var(--text-muted);">Nama Kepala Keluarga</div>
                    <div id="modalVervalNama" style="font-size: 15px; font-weight: 800; color: var(--text-primary); margin-top: 2px;"></div>
                </div>
                <div>
                    <div style="font-size: 11px; text-transform: uppercase; font-weight: 700; color: var(--text-muted);">Nomor Induk Kependudukan (NIK)</div>
                    <div id="modalVervalNik" style="font-size: 14px; font-family: monospace; font-weight: 700; color: var(--primary); margin-top: 2px;"></div>
                </div>
                <div>
                    <div style="font-size: 11px; text-transform: uppercase; font-weight: 700; color: var(--text-muted);">Pekerjaan &amp; Penghasilan</div>
                    <div id="modalVervalPekerjaan" style="font-size: 13.5px; font-weight: 600; color: var(--text-primary); margin-top: 2px;"></div>
                </div>
                <div>
                    <div style="font-size: 11px; text-transform: uppercase; font-weight: 700; color: var(--text-muted);">Status Legalitas Tanah</div>
                    <div id="modalVervalTanah" style="font-size: 13.5px; font-weight: 600; color: var(--text-primary); margin-top: 2px;"></div>
                </div>
            </div>

            <div style="background: rgba(0, 40, 85, 0.03); border-radius: 8px; padding: 14px; margin-bottom: 18px; border-left: 4px solid var(--secondary);">
                <div style="font-size: 11.5px; font-weight: 700; color: var(--primary-dark); text-transform: uppercase; margin-bottom: 6px;">
                    <i class="fas fa-map-marker-alt"></i> Alamat Lengkap Objek RTLH
                </div>
                <div id="modalVervalAlamat" style="font-size: 13.5px; color: var(--text-primary); font-weight: 500;"></div>
            </div>

            <div style="border: 1px solid rgba(0,40,85,0.08); border-radius: 8px; padding: 14px; margin-bottom: 18px;">
                <div style="font-size: 12px; font-weight: 800; color: var(--primary); margin-bottom: 8px;">
                    <i class="fas fa-hammer"></i> Rincian Kerusakan Komponen Rumah (RTLH):
                </div>
                <ul style="margin: 0; padding-left: 20px; font-size: 13px; color: var(--text-secondary); line-height: 1.6;">
                    <li><strong>Kondisi Atap:</strong> <span id="modalVervalAtap"></span></li>
                    <li><strong>Kondisi Dinding:</strong> <span id="modalVervalDinding"></span></li>
                    <li><strong>Kondisi Lantai:</strong> <span id="modalVervalLantai"></span></li>
                </ul>
            </div>

            <div style="background: rgba(255, 184, 0, 0.08); border-radius: 8px; padding: 12px; font-size: 12.5px; color: var(--text-primary);">
                <strong>Catatan Verifikator (TFL):</strong>
                <p id="modalVervalCatatan" style="margin: 4px 0 0 0; color: var(--text-secondary); font-style: italic;"></p>
                <div style="margin-top: 8px; font-size: 11.5px; font-weight: 700; color: var(--primary);" id="modalVervalTfl"></div>
            </div>
        </div>

        <div class="modal-footer" style="padding: 14px 20px; background: var(--bg-body); border-top: 1px solid rgba(0, 40, 85, 0.06); display: flex; justify-content: flex-end; gap: 10px;">
            <button type="button" class="btn btn-outline" onclick="window.PuprModal.close('modalVervalDetail')">
                Tutup
            </button>
            <button type="button" class="btn btn-primary" onclick="alert('Mencetak Lembar Verifikasi Lengkap (Format Resmi BSPS)...')">
                <i class="fas fa-print"></i> Cetak Lembar Verval
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function showVervalDetail(data) {
    document.getElementById('modalVervalId').innerText = data.id;
    document.getElementById('modalVervalNama').innerText = data.nama_kk + ' (' + data.umur + ' Thn / ' + data.tanggungan + ' Tanggungan)';
    document.getElementById('modalVervalNik').innerText = data.nik;
    document.getElementById('modalVervalPekerjaan').innerText = data.pekerjaan + ' - ' + data.penghasilan;
    document.getElementById('modalVervalTanah').innerText = data.legalitas_tanah;
    document.getElementById('modalVervalAlamat').innerText = data.dusun + ', Desa ' + data.desa + ', Kecamatan ' + data.kecamatan + ', Kabupaten Jember';
    document.getElementById('modalVervalAtap').innerText = data.kondisi_atap;
    document.getElementById('modalVervalDinding').innerText = data.kondisi_dinding;
    document.getElementById('modalVervalLantai').innerText = data.kondisi_lantai;
    document.getElementById('modalVervalCatatan').innerText = data.catatan;
    document.getElementById('modalVervalTfl').innerText = 'Petugas TFL: ' + data.fasilitator + ' (' + data.tgl_verval + ')';

    // Status Badge
    let badgeHtml = '';
    if(data.status_badge === 'success') {
        badgeHtml = '<span class="badge success" style="padding: 4px 10px;"><i class="fas fa-check-circle"></i> ' + data.status_verval + '</span>';
    } else if(data.status_badge === 'warning') {
        badgeHtml = '<span class="badge warning" style="padding: 4px 10px;"><i class="fas fa-clock"></i> ' + data.status_verval + '</span>';
    } else if(data.status_badge === 'info') {
        badgeHtml = '<span class="badge info" style="padding: 4px 10px; background: rgba(52, 152, 219, 0.15); color: #2980b9;"><i class="fas fa-exclamation-circle"></i> ' + data.status_verval + '</span>';
    } else {
        badgeHtml = '<span class="badge danger" style="padding: 4px 10px;"><i class="fas fa-times-circle"></i> ' + data.status_verval + '</span>';
    }
    document.getElementById('modalVervalStatusBadge').innerHTML = badgeHtml;

    // Skor Badge
    let scoreClass = data.skor_kelaikan >= 80 ? 'score-high' : (data.skor_kelaikan >= 60 ? 'score-mid' : 'score-low');
    document.getElementById('modalVervalSkor').innerHTML = '<span class="score-badge ' + scoreClass + '"><i class="fas fa-gauge-high"></i> Skor RTLH: ' + data.skor_kelaikan + '%</span>';

    window.PuprModal.open('modalVervalDetail');
}

document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.status-select').forEach(function(select) {
        select.addEventListener('change', function() {
            var id = this.getAttribute('data-id');
            var status = this.value;
            var selectElement = this;
            
            var colors = {
                'ditemukan': '#28a745',
                'meninggal': '#343a40',
                'pindah': '#ffc107',
                'tidak diketahui': '#dc3545'
            };
            
            selectElement.style.backgroundColor = colors[status];
            selectElement.style.color = (status === 'pindah') ? '#000' : '#fff';
            
            // Send AJAX
            fetch("{{ url('/data-verval') }}/" + id + "/status", {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ status: status })
            })
            .then(response => response.json())
            .then(data => {
                if(!data.success) {
                    alert('Gagal memperbarui status');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat memperbarui status');
            });
        });
    });
});
</script>
@endpush
