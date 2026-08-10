@extends('layouts.partial.app')

@section('title', 'BSPS Verval - Pencocokan Data Dataguse')
@section('title_header', 'Pencocokan Data Kependudukan')
@section('subtitle_header', 'Verifikasi & Validasi Silang Identitas Penerima BSPS dengan Database Dataguse Resmi')

@push('styles')
<style>
    /* Card Stat Grid */
    .stats-pencocokan-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
        margin-bottom: 24px;
    }
    .stat-card-pencocokan {
        background: var(--bg-card);
        border-radius: var(--radius);
        padding: 20px;
        box-shadow: var(--shadow-sm);
        border: 1px solid rgba(0, 40, 85, 0.06);
        display: flex;
        align-items: center;
        gap: 16px;
    }
    .stat-card-pencocokan .stat-icon {
        width: 48px; height: 48px;
        border-radius: var(--radius-sm);
        display: flex; align-items: center; justify-content: center;
        font-size: 20px; flex-shrink: 0;
    }
    .stat-card-pencocokan .stat-icon.blue   { background: rgba(0,40,85,0.10);   color: var(--primary); }
    .stat-card-pencocokan .stat-icon.green  { background: rgba(39,174,96,0.12); color: var(--success); }
    .stat-card-pencocokan .stat-icon.orange { background: rgba(217,119,6,0.12); color: #d97706; }
    .stat-card-pencocokan .stat-icon.red    { background: rgba(239,68,68,0.12);  color: #ef4444; }

    .stat-card-pencocokan .stat-value { font-size: 24px; font-weight: 800; line-height: 1.1; color: var(--primary-dark); }
    .stat-card-pencocokan .stat-label { font-size: 12px; color: var(--text-muted); font-weight: 600; margin-top: 3px; }

    /* Filter Bar */
    .filter-pencocokan-bar {
        background: var(--bg-card);
        border-radius: var(--radius);
        padding: 16px 20px;
        margin-bottom: 20px;
        box-shadow: var(--shadow-sm);
        border: 1px solid rgba(0,40,85,0.06);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
    }

    /* Comparison Table Cell Styles */
    .cell-data-block {
        font-size: 12.5px;
        line-height: 1.45;
    }
    .cell-data-title {
        font-weight: 800;
        color: var(--primary-dark);
        font-size: 13.5px;
        margin-bottom: 3px;
    }
    .cell-data-sub {
        color: var(--text-secondary);
        font-size: 12px;
    }
    .cell-data-tag {
        display: inline-block;
        font-family: monospace;
        font-weight: 700;
        background: rgba(0,40,85,0.06);
        padding: 2px 6px;
        border-radius: 4px;
        color: var(--text-primary);
        font-size: 11.5px;
    }
    .diff-highlight {
        background: #fef3c7;
        color: #92400e;
        padding: 1px 5px;
        border-radius: 4px;
        font-weight: 700;
    }

    /* Status Badges */
    .badge-match {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 11.5px;
        font-weight: 800;
    }
    .badge-match.cocok { background: rgba(39,174,96,0.14); color: #15803d; }
    .badge-match.beda  { background: rgba(217,119,6,0.14); color: #b45309; }
    .badge-match.tidak { background: rgba(239,68,68,0.12); color: #b91c1c; }

    /* Action Buttons */
    .btn-apply-dg {
        background: var(--primary);
        color: #ffffff;
        border: none;
        padding: 7px 13px;
        border-radius: var(--radius-sm);
        font-size: 12px;
        font-weight: 700;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s;
    }
    .btn-apply-dg:hover {
        background: var(--primary-dark);
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0,40,85,0.2);
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
        box-sizing: border-box;
    }
    .pg-link:hover { background: var(--primary); color: #fff; border-color: var(--primary); transform: translateY(-1px); }
    .pg-link.active { background: var(--primary); color: #fff; border-color: var(--primary); box-shadow: 0 2px 6px rgba(0, 40, 85, 0.25); }
    .pg-link.disabled { opacity: 0.4; cursor: not-allowed; pointer-events: none; }
    .pg-dots { display: inline-flex; align-items: center; justify-content: center; min-width: 30px; height: 36px; font-size: 14px; font-weight: 700; color: var(--text-muted); letter-spacing: 2px; }

</style>
@endpush

@section('content')
<main class="main-content">
    <div class="content-wrapper">

        {{-- Connection Warning --}}
        @if(!$dataguseConnected)
            <div style="background:#fff3cd;border:1px solid #ffeeba;color:#856404;border-radius:10px;padding:14px 20px;margin-bottom:20px;display:flex;align-items:center;gap:12px;">
                <i class="fas fa-exclamation-triangle" style="font-size:22px;color:#d97706;"></i>
                <div>
                    <strong style="font-size:14px;display:block;">Koneksi ke Database Dataguse Terbatas</strong>
                    <span style="font-size:12.5px;">Tidak dapat terhubung langsung ke IP server Dataguse (153.92.15.118) dari perangkat lokal ini. Di server Production Hostinger, koneksi ke Dataguse berjalan otomatis.</span>
                </div>
            </div>
        @endif

        {{-- Stats Grid --}}
        <div class="stats-pencocokan-grid">
            <div class="stat-card-pencocokan">
                <div class="stat-icon blue"><i class="fas fa-id-card"></i></div>
                <div class="stat-info">
                    <div class="stat-value">{{ number_format($stats['total'], 0, ',', '.') }}</div>
                    <div class="stat-label">Total Data Ditampilkan</div>
                </div>
            </div>
            <div class="stat-card-pencocokan">
                <div class="stat-icon green"><i class="fas fa-check-double"></i></div>
                <div class="stat-info">
                    <div class="stat-value" style="color:#15803d;">{{ number_format($stats['cocok'], 0, ',', '.') }}</div>
                    <div class="stat-label">Cocok Sempurna (Match)</div>
                </div>
            </div>
            <div class="stat-card-pencocokan">
                <div class="stat-icon orange"><i class="fas fa-triangle-exclamation"></i></div>
                <div class="stat-info">
                    <div class="stat-value" style="color:#d97706;">{{ number_format($stats['beda'], 0, ',', '.') }}</div>
                    <div class="stat-label">Perlu Di-sync (Beda Data)</div>
                </div>
            </div>
            <div class="stat-card-pencocokan">
                <div class="stat-icon red"><i class="fas fa-user-xmark"></i></div>
                <div class="stat-info">
                    <div class="stat-value" style="color:#ef4444;">{{ number_format($stats['tidak_ditemukan'], 0, ',', '.') }}</div>
                    <div class="stat-label">Tidak Ditemukan di Dataguse</div>
                </div>
            </div>
        </div>

        {{-- Filter & Batch Action Toolbar --}}
        <div class="filter-pencocokan-bar">
            <form action="{{ url('/pencocokan-data') }}" method="GET" style="display:flex;align-items:center;gap:12px;flex:1;flex-wrap:wrap;">
                <div style="position:relative;flex:1;min-width:240px;">
                    <i class="fas fa-search" style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:var(--text-muted);font-size:13px;"></i>
                    <input type="text" name="search" value="{{ $search }}" placeholder="Cari Nama, NIK, Desa, atau Kecamatan..." 
                           style="width:100%;padding:9px 12px 9px 36px;border-radius:8px;border:1px solid rgba(0,40,85,0.15);font-size:13px;outline:none;box-sizing:border-box;">
                </div>

                {{-- Status Filter --}}
                <select name="status" onchange="this.form.submit()" style="padding:9px 14px;border-radius:8px;border:1px solid rgba(0,40,85,0.15);font-size:13px;font-weight:600;outline:none;background:#fff;cursor:pointer;">
                    <option value="all" {{ $statusFilter === 'all' ? 'selected' : '' }}>Semua Status Match</option>
                    <option value="cocok" {{ $statusFilter === 'cocok' ? 'selected' : '' }}>🟢 Cocok Sempurna</option>
                    <option value="beda" {{ $statusFilter === 'beda' ? 'selected' : '' }}>🟡 Ada Perbedaan Data</option>
                    <option value="tidak_ditemukan" {{ $statusFilter === 'tidak_ditemukan' ? 'selected' : '' }}>🔴 Tidak Ditemukan</option>
                </select>

                <button type="submit" class="btn btn-primary" style="padding:9px 16px;font-size:13px;font-weight:700;">
                    <i class="fas fa-filter"></i> Saring
                </button>
                @if(!empty($search) || $statusFilter !== 'all')
                    <a href="{{ url('/pencocokan-data') }}" class="btn btn-outline" style="padding:9px 14px;font-size:13px;">Reset</a>
                @endif
            </form>

            <button type="button" class="btn btn-success" id="btnBatchSync" onclick="runBatchSync()" style="padding:9px 16px;font-size:13px;font-weight:800;background:#27ae60;border:none;color:#fff;border-radius:8px;cursor:pointer;display:inline-flex;align-items:center;gap:6px;">
                <i class="fas fa-rotate"></i> Sync Data Terpilih
            </button>
        </div>

        {{-- Main Table Comparison --}}
        <div class="table-container-card" style="background:#fff;border-radius:12px;box-shadow:var(--shadow-sm);border:1px solid rgba(0,40,85,0.06);overflow:hidden;">
            <div style="padding:16px 20px;border-bottom:1px solid rgba(0,40,85,0.06);display:flex;align-items:center;justify-content:space-between;">
                <h3 style="font-size:15px;font-weight:800;color:var(--primary-dark);margin:0;display:flex;align-items:center;gap:8px;">
                    <i class="fas fa-object-ungroup" style="color:var(--primary);"></i> Perbandingan Data BSPS (Lokal) vs Dataguse (Resmi)
                </h3>
                <span style="font-size:12px;color:var(--text-muted);font-weight:600;">
                    Menampilkan {{ $penerimas->firstItem() ?? 0 }} - {{ $penerimas->lastItem() ?? 0 }} dari {{ number_format($penerimas->total(), 0, ',', '.') }} data
                </span>
            </div>

            <div style="overflow-x:auto;">
                <table style="width:100%;border-collapse:collapse;min-width:1000px;">
                    <thead>
                        <tr style="background:#f8fafc;border-bottom:1px solid rgba(0,40,85,0.08);text-align:left;font-size:12px;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.4px;">
                            <th style="padding:12px 16px;width:40px;text-align:center;">
                                <input type="checkbox" id="selectAllCheckbox" onchange="toggleSelectAll(this)" style="width:16px;height:16px;cursor:pointer;">
                            </th>
                            <th style="padding:12px 16px;width:45px;">No</th>
                            <th style="padding:12px 16px;width:38%;">Identitas Data Penerima (Lokal BSPS)</th>
                            <th style="padding:12px 16px;width:38%;">Identitas Kependudukan (Dataguse)</th>
                            <th style="padding:12px 16px;text-align:center;width:12%;">Status Match</th>
                            <th style="padding:12px 16px;text-align:center;width:12%;">Aksi Sync</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($penerimas as $index => $item)
                            @php
                                $dg = $item->dg_data;
                                $diffs = $item->dg_diffs ?? [];
                            @endphp
                            <tr style="border-bottom:1px solid rgba(0,40,85,0.06);font-size:13px;">
                                <td style="padding:14px 16px;text-align:center;">
                                    <input type="checkbox" class="row-checkbox" value="{{ $item->id }}" style="width:16px;height:16px;cursor:pointer;">
                                </td>
                                <td style="padding:14px 16px;font-weight:700;color:var(--text-muted);">
                                    {{ $penerimas->firstItem() + $index }}
                                </td>

                                {{-- Kolom 1: Data Lokal BSPS --}}
                                <td style="padding:14px 16px;vertical-align:top;">
                                    <div class="cell-data-block">
                                        <div class="cell-data-title">{{ $item->nama }}</div>
                                        <div style="margin-bottom:4px;">
                                            <span class="cell-data-tag">NIK: {{ $item->no_ktp ?: '-' }}</span>
                                            <span class="cell-data-tag" style="margin-left:4px;">KK: {{ $item->no_kk ?: '-' }}</span>
                                        </div>
                                        <div class="cell-data-sub">
                                            <i class="fas fa-location-dot" style="color:var(--primary);font-size:11px;"></i>
                                            {{ $item->alamat ?: '-' }},
                                            Desa/Kel: <strong>{{ $item->desa_kelurahan ?: '-' }}</strong>,
                                            Kec: <strong>{{ $item->kecamatan ?: '-' }}</strong>,
                                            Kab: <strong>{{ $item->kabupaten_kota ?: 'Banyuwangi' }}</strong>
                                        </div>
                                        @if($item->tempat_lahir || $item->tanggal_lahir)
                                            <div style="font-size:11.5px;color:var(--text-muted);margin-top:4px;">
                                                <i class="fas fa-cake-candles" style="font-size:10px;"></i>
                                                {{ $item->tempat_lahir ?: '-' }}, {{ $item->tanggal_lahir ? date('d-m-Y', strtotime($item->tanggal_lahir)) : '-' }}
                                            </div>
                                        @endif
                                    </div>
                                </td>

                                {{-- Kolom 2: Data Dataguse --}}
                                <td style="padding:14px 16px;vertical-align:top;background:rgba(248,250,252,0.6);">
                                    @if($dg)
                                        <div class="cell-data-block">
                                            <div class="cell-data-title">
                                                <span class="{{ isset($diffs['nama']) ? 'diff-highlight' : '' }}">
                                                    {{ $dg->nama ?: '-' }}
                                                </span>
                                            </div>
                                            <div style="margin-bottom:4px;">
                                                <span class="cell-data-tag">NIK: {{ $dg->nik }}</span>
                                                <span class="cell-data-tag" style="margin-left:4px;">KK: {{ $dg->no_kk ?: '-' }}</span>
                                            </div>
                                            <div class="cell-data-sub">
                                                <i class="fas fa-house" style="color:#27ae60;font-size:11px;"></i>
                                                <span class="{{ isset($diffs['alamat']) ? 'diff-highlight' : '' }}">{{ $dg->alamat ?: '-' }}</span>
                                                @if($dg->rt_rw)
                                                    <span style="font-weight:700;color:var(--primary);">({{ $dg->rt_rw }})</span>,
                                                @endif
                                                Desa/Kel: <strong class="{{ isset($diffs['desa_kelurahan']) ? 'diff-highlight' : '' }}">{{ $dg->desa_kelurahan ?: '-' }}</strong>,
                                                Kec: <strong class="{{ isset($diffs['kecamatan']) ? 'diff-highlight' : '' }}">{{ $dg->kecamatan ?: '-' }}</strong>,
                                                Kab: <strong class="{{ isset($diffs['kabupaten_kota']) ? 'diff-highlight' : '' }}">{{ $dg->kabupaten_kota ?: 'Banyuwangi' }}</strong>
                                            </div>
                                            @if($dg->tempat_lahir || $dg->tanggal_lahir)
                                                <div style="font-size:11.5px;color:var(--text-muted);margin-top:4px;">
                                                    <i class="fas fa-cake-candles" style="font-size:10px;"></i>
                                                    {{ $dg->tempat_lahir ?: '-' }}, {{ $dg->tanggal_lahir ? date('d-m-Y', strtotime($dg->tanggal_lahir)) : '-' }}
                                                </div>
                                            @endif
                                        </div>
                                    @else
                                        <div style="color:var(--text-muted);font-style:italic;font-size:12px;padding:8px 0;">
                                            <i class="fas fa-user-slash" style="color:#ef4444;margin-right:4px;"></i>
                                            NIK tidak terdaftar / tidak ditemukan di Dataguse
                                        </div>
                                    @endif
                                </td>

                                {{-- Kolom 3: Status Match --}}
                                <td style="padding:14px 16px;text-align:center;vertical-align:middle;">
                                    @if($item->dg_status === 'cocok')
                                        <span class="badge-match cocok"><i class="fas fa-circle-check"></i> Cocok</span>
                                    @elseif($item->dg_status === 'beda')
                                        <span class="badge-match beda"><i class="fas fa-triangle-exclamation"></i> {{ count($diffs) }} Beda</span>
                                    @else
                                        <span class="badge-match tidak"><i class="fas fa-circle-xmark"></i> Tidak Ada</span>
                                    @endif
                                </td>

                                {{-- Kolom 4: Aksi --}}
                                <td style="padding:14px 16px;text-align:center;vertical-align:middle;">
                                    @if($dg)
                                        <button type="button" class="btn-apply-dg" onclick="applySingleSync({{ $item->id }}, '{{ e($item->nama) }}')">
                                            <i class="fas fa-cloud-arrow-down"></i> Terapkan
                                        </button>
                                    @else
                                        <span style="font-size:11px;color:var(--text-muted);">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="text-align:center;padding:36px;color:var(--text-muted);">
                                    <i class="fas fa-id-card-clip" style="font-size:32px;display:block;margin-bottom:8px;opacity:0.4;"></i>
                                    Tidak ada data penerima yang ditemukan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Custom Pagination Bar -->
            <div class="pagination-custom-bar" style="padding:16px 20px;border-top:1px solid rgba(0,40,85,0.06);display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;">
                <div class="pagination-info-text" style="font-size:13px;color:var(--text-muted);">
                    Menampilkan <strong>{{ $penerimas->firstItem() ?? 0 }}</strong> - <strong>{{ $penerimas->lastItem() ?? 0 }}</strong> dari <strong>{{ number_format($penerimas->total(), 0, ',', '.') }}</strong> data (Halaman <strong>{{ $penerimas->currentPage() }}</strong> dari <strong>{{ $penerimas->lastPage() }}</strong>)
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
    </div>
</main>
@endsection

@push('scripts')
<script>
    function toggleSelectAll(master) {
        document.querySelectorAll('.row-checkbox').forEach(cb => {
            cb.checked = master.checked;
        });
    }

    function applySingleSync(id, nama) {
        if (!confirm(`Apakah Anda yakin ingin memperbarui data "${nama}" sesuai data resmi Dataguse?`)) return;

        if (window.PuprLoading) window.PuprLoading.show('Memperbarui Data Penerima...');

        fetch(`/pencocokan-data/${id}/sync`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (window.PuprLoading) window.PuprLoading.hide();
            if (data.success) {
                alert(data.message);
                window.location.reload();
            } else {
                alert(data.message || 'Gagal memperbarui data.');
            }
        })
        .catch(err => {
            if (window.PuprLoading) window.PuprLoading.hide();
            console.error(err);
            alert('Terjadi kesalahan koneksi.');
        });
    }

    function runBatchSync() {
        const checked = Array.from(document.querySelectorAll('.row-checkbox:checked')).map(cb => cb.value);
        if (checked.length === 0) {
            alert('Silakan pilih setidaknya satu penerima pada tabel dengan mencentang checkbox.');
            return;
        }

        if (!confirm(`Apakah Anda yakin ingin memperbarui ${checked.length} data penerima terpilih dari Dataguse?`)) return;

        if (window.PuprLoading) window.PuprLoading.show(`Meng-update ${checked.length} data penerima...`);

        fetch(`/pencocokan-data/sync-batch`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ ids: checked })
        })
        .then(res => res.json())
        .then(data => {
            if (window.PuprLoading) window.PuprLoading.hide();
            if (data.success) {
                alert(data.message);
                window.location.reload();
            } else {
                alert(data.message || 'Gagal memperbarui batch data.');
            }
        })
        .catch(err => {
            if (window.PuprLoading) window.PuprLoading.hide();
            console.error(err);
            alert('Terjadi kesalahan koneksi saat batch sync.');
        });
    }
</script>
@endpush
