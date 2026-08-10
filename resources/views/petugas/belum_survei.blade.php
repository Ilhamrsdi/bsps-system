@extends('layouts.partial.app')

@section('title', 'PUPR Jember - Tugas Belum Di-survei')
@section('title_header', 'Tugas Belum Di-survei')
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
            <span>Belum Survei</span>
        </div>

        {{-- Alert Sukses --}}
        @if(session('success'))
            <div style="background:rgba(39,174,96,0.10);border:1px solid rgba(39,174,96,0.30);border-radius:var(--radius-sm);padding:14px 18px;margin-bottom:20px;font-size:13px;color:var(--success);display:flex;align-items:center;gap:10px;">
                <i class="fas fa-check-circle" style="font-size:16px;"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <!-- Filter & Search Section -->
        <form action="{{ route('petugas.belum-survei') }}" method="GET" class="filter-section">
            <div class="filter-group">
                <div class="pupr-search-group">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari kegiatan atau lokasi penugasan Anda..." class="pupr-search-input" />
                    <button type="submit" class="pupr-search-btn"><i class="fas fa-search"></i></button>
                </div>
            </div>

            <div class="filter-actions">
                <a href="{{ route('petugas.belum-survei') }}" class="btn btn-outline"><i class="fas fa-redo"></i> Reset</a>
            </div>
        </form>

        <!-- Table Tugas Belum Survei -->
        <div class="table-card">
            <div class="table-header">
                <h3><i class="fas fa-clipboard-question" style="color:#d69e00;margin-right:10px;"></i>Kegiatan Belum Di-survei (Menunggu Pengisian Form)</h3>
            </div>

            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th style="width:60px;">No</th>
                            <th style="min-width:280px;">Nama Kegiatan &amp; Alamat</th>
                            <th style="min-width:160px;">Lokasi &amp; Tanggal</th>
                            <th style="min-width:140px;">Status Penugasan</th>
                            <th style="width:180px;">Aksi</th>
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
                                <span class="badge-status warning" style="background:rgba(255,184,0,0.15);color:#d69e00;">
                                    <i class="fas fa-clock"></i> Belum Di-survei
                                </span>
                            </td>
                            <td>
                                <div class="table-actions-cell">
                                    <button type="button" class="btn" style="padding:8px 18px;border-radius:6px;background:var(--primary);color:#fff;font-weight:700;border:none;cursor:pointer;display:inline-flex;align-items:center;gap:6px;" onclick="startSurveyWithGps({{ $item->id }}, '{{ addslashes($item->nama_kegiatan) }}')" title="Mulai Survei Lapangan">
                                        <i class="fas fa-pen-to-square"></i> Mulai Survei
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" style="text-align:center;padding:44px;color:var(--text-muted);">
                                <i class="fas fa-circle-check" style="font-size:40px;display:block;margin-bottom:12px;color:var(--success);opacity:0.6;"></i>
                                <strong style="font-size:15px;color:var(--primary-dark);">Tidak Ada Tugas Pending!</strong>
                                <p style="margin-top:6px;font-size:13px;">Semua kegiatan yang ditugaskan Admin telah selesai Anda survei.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="table-footer">
                <span>Menampilkan {{ $kegiatans->firstItem() ?? 0 }}-{{ $kegiatans->lastItem() ?? 0 }} dari {{ $kegiatans->total() }} kegiatan pending</span>

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

    <!-- PUPR Custom Modal Wajib Lokasi GPS -->
    <div class="modal-overlay" id="modalWajibLokasi">
        <div class="modal-box" style="max-width: 440px;">
            <div class="modal-header" style="background: rgba(231, 76, 60, 0.08); border-bottom: 1px solid rgba(231, 76, 60, 0.18); padding:16px 20px;">
                <h3 style="color: #e74c3c; display: flex; align-items: center; gap: 8px; margin: 0; font-size: 16px; font-weight: 800;">
                    <i class="fas fa-location-crosshairs"></i> Akses Lokasi (GPS) Wajib Diaktifkan
                </h3>
            </div>
            <div class="modal-body" style="padding: 24px 20px; text-align: center;">
                <div style="width:64px;height:64px;border-radius:50%;background:rgba(231,76,60,0.12);color:#e74c3c;display:flex;align-items:center;justify-content:center;font-size:28px;margin:0 auto 16px;">
                    <i class="fas fa-location-slash"></i>
                </div>
                <h4 style="font-size:16px;font-weight:800;color:var(--primary-dark);margin-bottom:8px;">Izin GPS Perangkat Diperlukan!</h4>
                <p style="font-size:13px;color:var(--text-muted);line-height:1.5;margin-bottom:16px;">
                    Untuk melakukan survei pada kegiatan <strong id="modalNamaKegiatanText" style="color:var(--primary);">...</strong>, Anda <strong>wajib mengaktifkan &amp; memberikan izin akses lokasi GPS</strong> pada perangkat HP Anda.
                </p>
                <div style="background:rgba(255,184,0,0.12);border:1px solid rgba(255,184,0,0.3);border-radius:8px;padding:12px;font-size:12px;color:#9e7300;text-align:left;line-height:1.4;">
                    <i class="fas fa-triangle-exclamation" style="margin-right:4px;"></i> <strong>Penting:</strong> Tanpa mengaktifkan lokasi GPS, Anda <strong>tidak dapat masuk atau mengisi form survei</strong> kegiatan tersebut.
                </div>
            </div>
            <div class="modal-footer" style="padding: 16px 20px; border-top: 1px solid rgba(0, 40, 85, 0.08); display: flex; gap: 10px; justify-content: flex-end; background:var(--bg-body);">
                <button type="button" class="btn btn-outline" onclick="closeWajibLokasiModal()" style="padding:10px 18px;border-radius:var(--radius-sm);font-weight:600;font-size:13px;">
                    <i class="fas fa-xmark"></i> Batal (Tidak Bisa Survei)
                </button>
                <button type="button" class="btn btn-primary" onclick="retryGpsPermission()" style="padding:10px 20px;border-radius:var(--radius-sm);font-weight:700;background:var(--primary);color:#fff;font-size:13px;border:none;">
                    <i class="fas fa-rotate"></i> Coba Lagi / Izinkan Lokasi
                </button>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    let activeKegiatanId = null;
    let activeNamaKegiatan = '';

    function startSurveyWithGps(kegiatanId, namaKegiatan) {
        activeKegiatanId = kegiatanId;
        activeNamaKegiatan = namaKegiatan;

        if (!navigator.geolocation) {
            showWajibLokasiModal(namaKegiatan);
            return;
        }

        if (window.PuprLoading) {
            window.PuprLoading.show('Verifikasi & Mengambil Koordinat GPS Perangkat HP...');
        }

        navigator.geolocation.getCurrentPosition(
            function(position) {
                const lat = position.coords.latitude.toFixed(6);
                const lng = position.coords.longitude.toFixed(6);
                const deviceType = /Mobi|Android|iPhone|iPad/i.test(navigator.userAgent) ? 'Mobile Phone' : 'Desktop / Laptop';

                // Kirim AJAX untuk mengupdate data lokasi GPS, IP & Device petugas di database users
                fetch('{{ route("petugas.update-location") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        latitude: lat,
                        longitude: lng,
                        device_type: deviceType
                    })
                })
                .then(res => res.json())
                .catch(err => console.log('Update location error:', err))
                .finally(() => {
                    if (window.PuprLoading) window.PuprLoading.hide();
                    sessionStorage.setItem('survey_lat', lat);
                    sessionStorage.setItem('survey_lng', lng);
                    window.location.href = '/survey?kegiatan_id=' + kegiatanId + '&lat=' + lat + '&lng=' + lng;
                });
            },
            function(error) {
                if (window.PuprLoading) window.PuprLoading.hide();
                showWajibLokasiModal(namaKegiatan);
            },
            {
                enableHighAccuracy: true,
                timeout: 12000,
                maximumAge: 0
            }
        );
    }

    function showWajibLokasiModal(namaKegiatan) {
        document.getElementById('modalNamaKegiatanText').textContent = namaKegiatan || 'Kegiatan Lapangan';
        if (window.PuprModal) {
            window.PuprModal.open('modalWajibLokasi');
        } else {
            const overlay = document.getElementById('modalWajibLokasi');
            if (overlay) overlay.classList.add('active');
        }
    }

    function closeWajibLokasiModal() {
        if (window.PuprModal) {
            window.PuprModal.close('modalWajibLokasi');
        } else {
            const overlay = document.getElementById('modalWajibLokasi');
            if (overlay) overlay.classList.remove('active');
        }
    }

    function retryGpsPermission() {
        closeWajibLokasiModal();
        if (activeKegiatanId) {
            startSurveyWithGps(activeKegiatanId, activeNamaKegiatan);
        }
    }
</script>
@endpush
