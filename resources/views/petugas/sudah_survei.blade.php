@extends('layouts.partial.app')

@section('title', 'PUPR Jember - Tugas Sudah Di-survei')
@section('title_header', 'Tugas Sudah Di-survei')
@push('styles')
<style>
    @media (max-width: 1024px) {
        .filter-section { padding: 16px; flex-direction: column; align-items: stretch; gap: 12px; }
        .filter-section .filter-group { width: 100%; }
        .filter-section .filter-group .pupr-search-group { width: 100%; }
        .filter-section .filter-actions { width: 100%; margin-left: 0; }
        .filter-section .filter-actions .btn { width: 100%; justify-content: center; }
        .table-card .table-header { flex-direction: column; align-items: stretch; gap: 12px; }
    }

    @media (max-width: 768px) {
        .table-wrapper {
            overflow-x: auto !important;
            overflow-y: hidden !important;
            -webkit-overflow-scrolling: touch !important;
            touch-action: pan-x pan-y !important;
            overscroll-behavior-x: contain !important;
            transform: translateZ(0);
            width: 100% !important;
            display: block !important;
        }
        .table-card table {
            width: 100% !important;
            min-width: 800px !important;
            border-collapse: collapse;
            font-size: 13.5px;
            white-space: nowrap !important;
        }
        .table-card table tr,
        .table-card table th,
        .table-card table td {
            transition: none !important;
            white-space: nowrap !important;
        }
        .table-footer { flex-direction: column; align-items: center; text-align: center; gap: 10px; }
    }

    @media (max-width: 480px) {
        .dashboard-content { padding: 12px; }
    }
</style>
@endpush

@section('content')
    <!-- Navbar Component -->
    @include('layouts.navbar')

    <main class="dashboard-content">
        <!-- Breadcrumb -->
        <div class="breadcrumb" style="font-size:13px;color:var(--text-muted);margin-bottom:20px;display:flex;align-items:center;gap:8px;">
            <a href="{{ route('petugas.dashboard') }}" style="color:var(--primary);text-decoration:none;font-weight:500;"><i class="fas fa-home"></i> Dashboard Petugas</a>
            <i class="fas fa-chevron-right" style="font-size:10px;"></i>
            <span>Sudah Survei</span>
        </div>

        {{-- Alert Sukses --}}
        @if(session('success'))
            <div style="background:rgba(39,174,96,0.10);border:1px solid rgba(39,174,96,0.30);border-radius:var(--radius-sm);padding:14px 18px;margin-bottom:20px;font-size:13px;color:var(--success);display:flex;align-items:center;gap:10px;">
                <i class="fas fa-check-circle" style="font-size:16px;"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <!-- Filter & Search Section -->
        <form action="{{ route('petugas.sudah-survei') }}" method="GET" class="filter-section">
            <div class="filter-group">
                <div class="pupr-search-group">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari kegiatan atau lokasi penugasan..." class="pupr-search-input" />
                    <button type="submit" class="pupr-search-btn"><i class="fas fa-search"></i></button>
                </div>
            </div>

            <div class="filter-actions">
                <a href="{{ route('petugas.sudah-survei') }}" class="btn btn-outline"><i class="fas fa-redo"></i> Reset</a>
            </div>
        </form>

        <!-- Table Tugas Sudah Survei -->
        <div class="table-card">
            <div class="table-header">
                <h3><i class="fas fa-clipboard-check" style="color:var(--success);margin-right:10px;"></i>Riwayat Kegiatan Selesai Di-survei</h3>
            </div>

            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th style="width:60px;">No</th>
                            <th style="min-width:260px;">Nama Kegiatan &amp; Alamat</th>
                            <th style="min-width:160px;">Lokasi &amp; Tanggal</th>
                            <th style="min-width:140px;">Status</th>
                            <th style="min-width:160px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($kegiatans as $index => $item)
                        <tr>
                            <td>{{ $kegiatans->firstItem() + $index }}</td>
                            <td>
                                <strong style="color:var(--primary-dark);font-size:14px;display:block;margin-bottom:4px;">{{ $item->nama_kegiatan }}</strong>
                                <span style="font-size:12px;color:var(--text-muted);"><i class="fas fa-house"></i> {{ $item->alamat ?: '-' }}</span>
                            </td>
                            <td>
                                <div><i class="fas fa-location-dot" style="color:var(--primary);font-size:12px;"></i> Kec. {{ ucwords(str_replace('_',' ',$item->lokasi)) }}</div>
                                <div style="font-size:12px;color:var(--text-muted);margin-top:2px;"><i class="fas fa-calendar-alt"></i> {{ $item->tanggal->format('d M Y') }}</div>
                            </td>
                            <td>
                                <span class="badge-status success" style="background:rgba(39,174,96,0.12);color:var(--success);">
                                    <i class="fas fa-circle-check"></i> Survei Selesai
                                </span>
                            </td>
                            <td>
                                <a href="{{ url('/survey?kegiatan_id='.$item->id) }}" class="btn-icon edit" style="text-decoration:none;padding:7px 14px;border-radius:6px;background:var(--primary);color:#fff;font-weight:700;font-size:13px;display:inline-flex;align-items:center;gap:6px;" title="Lihat & Edit Hasil Survei Lapangan">
                                    <i class="fas fa-pen-to-square"></i> Lihat / Edit Survei
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" style="text-align:center;padding:44px;color:var(--text-muted);">
                                <i class="fas fa-inbox" style="font-size:36px;display:block;margin-bottom:12px;opacity:0.4;"></i>
                                Belum ada riwayat kegiatan yang selesai Anda survei.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="table-footer">
                <span>Menampilkan {{ $kegiatans->firstItem() ?? 0 }}-{{ $kegiatans->lastItem() ?? 0 }} dari {{ $kegiatans->total() }} kegiatan selesai</span>

                @if($kegiatans->hasPages())
                    <div class="pagination">
                        @if($kegiatans->onFirstPage())
                            <span class="page disabled"><i class="fas fa-chevron-left"></i></span>
                        @else
                            <a href="{{ $kegiatans->previousPageUrl() }}" class="page"><i class="fas fa-chevron-left"></i></a>
                        @endif

                        @foreach($kegiatans->getUrlRange(max(1, $kegiatans->currentPage() - 2), min($kegiatans->lastPage(), $kegiatans->currentPage() + 2)) as $page => $url)
                            @if($page == $kegiatans->currentPage())
                                <span class="page active">{{ $page }}</span>
                            @else
                                <a href="{{ $url }}" class="page">{{ $page }}</a>
                            @endif
                        @endforeach

                        @if($kegiatans->hasMorePages())
                            <a href="{{ $kegiatans->nextPageUrl() }}" class="page"><i class="fas fa-chevron-right"></i></a>
                        @else
                            <span class="page disabled"><i class="fas fa-chevron-right"></i></span>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </main>
@endsection
