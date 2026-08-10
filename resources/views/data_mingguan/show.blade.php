@extends('layouts.partial.app')

@section('title', 'PUPR Jember - Detail Kegiatan')
@section('title_header', 'Detail Kegiatan')
@section('subtitle_header', 'Informasi lengkap data kegiatan harian')

@push('styles')
<style>
    /* ============================================================
       PAGE STYLES: SHOW DATA MINGGUAN (PUPR THEME)
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
    .breadcrumb a:hover { color: var(--secondary); }

    /* Detail Card */
    .detail-card {
        background: var(--bg-card);
        border-radius: var(--radius);
        box-shadow: var(--shadow-sm);
        border: 1px solid rgba(0, 40, 85, 0.06);
        overflow: hidden;
    }
    .detail-card-header {
        padding: 20px 28px;
        border-bottom: 1px solid rgba(0, 40, 85, 0.07);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        background: linear-gradient(90deg, rgba(0,40,85,0.04) 0%, transparent 100%);
    }
    .detail-card-header .header-left {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .detail-card-header .header-icon {
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
    .detail-card-header h3 {
        font-size: 17px;
        font-weight: 700;
        color: var(--primary);
        margin: 0;
    }
    .detail-card-header p {
        font-size: 12px;
        color: var(--text-muted);
        margin: 2px 0 0;
    }
    .detail-card-header .header-actions {
        display: flex;
        gap: 8px;
        flex-shrink: 0;
    }

    .detail-card-body { padding: 28px; }

    /* Section Title */
    .detail-section-title {
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

    /* Info Grid */
    .info-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        margin-bottom: 20px;
    }
    @media (max-width: 640px) { .info-grid { grid-template-columns: 1fr; } }

    .info-item {}
    .info-item .info-label {
        font-size: 11px;
        font-weight: 700;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 5px;
    }
    .info-item .info-value {
        font-size: 14px;
        color: var(--text-primary);
        font-weight: 500;
    }
    .info-item .info-value.full-width { grid-column: 1 / -1; }

    /* Badge Status */
    .badge-status {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 700;
        text-transform: capitalize;
    }
    .badge-status.success { background: rgba(39,174,96,0.12); color: #27ae60; }
    .badge-status.warning { background: rgba(243,156,18,0.12); color: #e67e22; }
    .badge-status.info    { background: rgba(52,152,219,0.12); color: #2980b9; }
    .badge-status.danger  { background: rgba(231,76,60,0.12);  color: #c0392b; }
    .badge-status.secondary { background: rgba(0,40,85,0.08); color: var(--primary); }

    /* Progress Bar */
    .progress-bar-wrapper {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .progress-bar-track {
        flex: 1;
        height: 8px;
        background: rgba(0, 40, 85, 0.08);
        border-radius: 4px;
        overflow: hidden;
    }
    .progress-bar-fill {
        height: 100%;
        border-radius: 4px;
        background: var(--primary);
        transition: width 0.4s ease;
    }
    .progress-bar-fill.high   { background: #27ae60; }
    .progress-bar-fill.medium { background: #e67e22; }
    .progress-bar-fill.low    { background: #e74c3c; }
    .progress-bar-label {
        font-size: 14px;
        font-weight: 700;
        color: var(--primary);
        min-width: 42px;
        text-align: right;
    }

    /* Deskripsi Box */
    .deskripsi-box {
        background: rgba(0, 40, 85, 0.03);
        border: 1px solid rgba(0, 40, 85, 0.07);
        border-radius: var(--radius-sm);
        padding: 14px 18px;
        font-size: 14px;
        color: var(--text-primary);
        line-height: 1.7;
        white-space: pre-wrap;
    }
    .deskripsi-box.empty { color: var(--text-muted); font-style: italic; }

    /* Divider */
    .detail-divider { border: none; border-top: 1px solid rgba(0, 40, 85, 0.07); margin: 24px 0; }

    /* Footer */
    .detail-card-footer {
        padding: 18px 28px;
        border-top: 1px solid rgba(0, 40, 85, 0.07);
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: rgba(0, 40, 85, 0.02);
        flex-wrap: wrap;
        gap: 10px;
    }
    .detail-card-footer .meta-info {
        font-size: 12px;
        color: var(--text-muted);
        display: flex;
        align-items: center;
        gap: 16px;
    }
    .detail-card-footer .meta-info span { display: flex; align-items: center; gap: 5px; }
    .detail-card-footer .footer-actions { display: flex; gap: 8px; }

    /* Buttons */
    .btn {
        padding: 9px 20px;
        border-radius: var(--radius-sm);
        border: none;
        font-family: inherit;
        font-weight: 600;
        font-size: 13px;
        cursor: pointer;
        transition: var(--transition);
        display: inline-flex;
        align-items: center;
        gap: 7px;
        text-decoration: none;
    }
    .btn-back {
        background: transparent;
        color: var(--text-secondary);
        border: 1px solid rgba(0, 40, 85, 0.14);
    }
    .btn-back:hover { background: rgba(0, 40, 85, 0.05); }
    .btn-edit { background: var(--primary); color: #fff; }
    .btn-edit:hover {
        background: var(--primary-light, #003d7a);
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0, 40, 85, 0.25);
    }
    .btn-delete {
        background: rgba(231, 76, 60, 0.08);
        color: var(--danger);
        border: 1px solid rgba(231, 76, 60, 0.20);
    }
    .btn-delete:hover {
        background: var(--danger);
        color: #fff;
    }

    /* Alert Success */
    .alert-success {
        background: rgba(39, 174, 96, 0.08);
        border: 1px solid rgba(39, 174, 96, 0.25);
        border-radius: var(--radius-sm);
        padding: 12px 18px;
        margin-bottom: 20px;
        font-size: 13px;
        color: #27ae60;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    /* Modal Hapus */
    .modal-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,0.45);
        z-index: 9999;
        align-items: center;
        justify-content: center;
    }
    .modal-overlay.active { display: flex; }
    .modal-box {
        background: var(--bg-card);
        border-radius: var(--radius);
        padding: 32px 28px;
        max-width: 420px;
        width: 90%;
        box-shadow: 0 20px 60px rgba(0,0,0,0.2);
        text-align: center;
    }
    .modal-box .modal-icon {
        width: 60px; height: 60px;
        border-radius: 50%;
        background: rgba(231, 76, 60, 0.10);
        color: var(--danger);
        display: flex; align-items: center; justify-content: center;
        font-size: 26px;
        margin: 0 auto 16px;
    }
    .modal-box h4 { font-size: 18px; font-weight: 700; color: var(--text-primary); margin: 0 0 8px; }
    .modal-box p { font-size: 14px; color: var(--text-muted); margin: 0 0 24px; line-height: 1.6; }
    .modal-box .modal-actions { display: flex; gap: 10px; justify-content: center; }
    .modal-box .btn-modal-cancel {
        padding: 10px 22px; border-radius: var(--radius-sm);
        background: transparent; border: 1px solid rgba(0,40,85,0.14);
        color: var(--text-secondary); font-family: inherit; font-weight: 600;
        font-size: 14px; cursor: pointer; transition: var(--transition);
    }
    .modal-box .btn-modal-cancel:hover { background: rgba(0,40,85,0.05); }
    .modal-box .btn-modal-delete {
        padding: 10px 22px; border-radius: var(--radius-sm);
        background: var(--danger); border: none;
        color: #fff; font-family: inherit; font-weight: 600;
        font-size: 14px; cursor: pointer; transition: var(--transition);
        display: inline-flex; align-items: center; gap: 7px;
    }
    .modal-box .btn-modal-delete:hover { background: #c0392b; }
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
            <span>Detail Kegiatan</span>
        </div>

        {{-- Alert Success --}}
        @if(session('success'))
            <div class="alert-success">
                <i class="fas fa-check-circle"></i>
                {{ session('success') }}
            </div>
        @endif

        {{-- Detail Card --}}
        <div class="detail-card">
            <div class="detail-card-header">
                <div class="header-left">
                    <div class="header-icon">
                        <i class="fas fa-clipboard-list"></i>
                    </div>
                    <div>
                        <h3>{{ $dataMingguan->nama_kegiatan }}</h3>
                        <p>
                            <i class="fas fa-map-marker-alt"></i>
                            Kec. {{ ucwords(str_replace('_', ' ', $dataMingguan->lokasi)) }}
                            &nbsp;&bull;&nbsp;
                            <i class="fas fa-calendar"></i>
                            {{ $dataMingguan->tanggal->translatedFormat('d F Y') }}
                        </p>
                    </div>
                </div>
                <div class="header-actions">
                    <a href="{{ route('data-mingguan.edit', $dataMingguan->id) }}" class="btn btn-edit">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    <button type="button" class="btn btn-delete" onclick="openDeleteModal()">
                        <i class="fas fa-trash"></i> Hapus
                    </button>
                </div>
            </div>

            <div class="detail-card-body">

                {{-- SECTION 1: Informasi Kegiatan --}}
                <p class="detail-section-title">
                    <i class="fas fa-info-circle"></i> Informasi Kegiatan
                </p>

                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label"><i class="fas fa-tasks"></i> Nama Kegiatan</div>
                        <div class="info-value">{{ $dataMingguan->nama_kegiatan }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label"><i class="fas fa-user"></i> Nama Pemohon</div>
                        <div class="info-value">{{ $dataMingguan->nama_pemohon ?: '-' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label"><i class="fas fa-id-card"></i> NIK Pemohon</div>
                        <div class="info-value">{{ $dataMingguan->nik_pemohon ?: '-' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label"><i class="fas fa-map-marker-alt"></i> Lokasi / Kecamatan</div>
                        <div class="info-value">Kec. {{ ucwords(str_replace('_', ' ', $dataMingguan->lokasi)) }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label"><i class="fas fa-map-pin"></i> Alamat Lengkap</div>
                        <div class="info-value">{{ $dataMingguan->alamat ?: '-' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label"><i class="fas fa-calendar-day"></i> Tanggal Kegiatan</div>
                        <div class="info-value">{{ $dataMingguan->tanggal->translatedFormat('l, d F Y') }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label"><i class="fas fa-calendar-week"></i> Minggu Ke</div>
                        <div class="info-value">Minggu {{ $dataMingguan->minggu ?? '-' }}</div>
                    </div>
                </div>

                <hr class="detail-divider" />

                {{-- SECTION 2: Status Kegiatan --}}
                <p class="detail-section-title">
                    <i class="fas fa-chart-line"></i> Status Kegiatan
                </p>

                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label"><i class="fas fa-flag"></i> Status Kegiatan</div>
                        <div class="info-value">
                            @php
                                $colorMap = ['selesai'=>'success','proses'=>'warning','survei'=>'info','batal'=>'danger','menunggu'=>'secondary'];
                                $iconMap  = ['selesai'=>'check-circle','proses'=>'spinner','survei'=>'search','batal'=>'times-circle','menunggu'=>'clock'];
                                $color = $colorMap[$dataMingguan->status] ?? 'secondary';
                                $icon  = $iconMap[$dataMingguan->status] ?? 'circle';
                            @endphp
                            <span class="badge-status {{ $color }}">
                                <i class="fas fa-{{ $icon }}"></i>
                                {{ ucfirst($dataMingguan->status) }}
                            </span>
                        </div>
                    </div>
                </div>

                <hr class="detail-divider" />

                {{-- SECTION 3: Detail Tambahan --}}
                <p class="detail-section-title">
                    <i class="fas fa-file-alt"></i> Detail Tambahan
                </p>

                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label"><i class="fas fa-money-bill-wave"></i> Nilai Kontrak</div>
                        <div class="info-value">{{ $dataMingguan->nilaiKontrakFormatted() }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label"><i class="fas fa-hard-hat"></i> Kontraktor / Pelaksana</div>
                        <div class="info-value">{{ $dataMingguan->kontraktor ?: '-' }}</div>
                    </div>
                </div>

                <div class="info-item" style="margin-top: 4px;">
                    <div class="info-label" style="margin-bottom:8px;"><i class="fas fa-align-left"></i> Deskripsi / Catatan</div>
                    @if($dataMingguan->deskripsi)
                        <div class="deskripsi-box">{{ $dataMingguan->deskripsi }}</div>
                    @else
                        <div class="deskripsi-box empty">Tidak ada deskripsi atau catatan.</div>
                    @endif
                </div>

            </div>

            <div class="detail-card-footer">
                <div class="meta-info">
                    <span><i class="fas fa-clock"></i> Dibuat: {{ $dataMingguan->created_at->diffForHumans() }}</span>
                    @if($dataMingguan->updated_at != $dataMingguan->created_at)
                        <span><i class="fas fa-edit"></i> Diperbarui: {{ $dataMingguan->updated_at->diffForHumans() }}</span>
                    @endif
                </div>
                <div class="footer-actions">
                    <a href="{{ route('data-mingguan') }}" class="btn btn-back">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                    <a href="{{ route('data-mingguan.edit', $dataMingguan->id) }}" class="btn btn-edit">
                        <i class="fas fa-edit"></i> Edit Data
                    </a>
                </div>
            </div>
        </div>
    </main>

    {{-- Modal Konfirmasi Hapus --}}
    <div class="modal-overlay" id="deleteModal">
        <div class="modal-box">
            <div class="modal-icon">
                <i class="fas fa-trash-alt"></i>
            </div>
            <h4>Hapus Data Kegiatan?</h4>
            <p>Data <strong>{{ $dataMingguan->nama_kegiatan }}</strong> akan dihapus secara permanen dan tidak dapat dikembalikan.</p>
            <div class="modal-actions">
                <button type="button" class="btn-modal-cancel" onclick="closeDeleteModal()">
                    Batal
                </button>
                <form action="{{ route('data-mingguan.destroy', $dataMingguan->id) }}" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-modal-delete">
                        <i class="fas fa-trash"></i> Ya, Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function openDeleteModal() {
        document.getElementById('deleteModal').classList.add('active');
    }
    function closeDeleteModal() {
        document.getElementById('deleteModal').classList.remove('active');
    }
    // Tutup modal jika klik overlay
    document.getElementById('deleteModal').addEventListener('click', function(e) {
        if (e.target === this) closeDeleteModal();
    });
</script>
@endpush
