@extends('layouts.partial.app')

@section('title', 'PUPR Jember - Penugasan Petugas Survei')
@section('title_header', 'Penugasan Petugas Survei')
@section('subtitle_header', 'Kelola alokasi & penugasan petugas survei lapangan untuk kegiatan Dinas PUPR Jember')

@push('styles')
<style>
    /* Custom Petugas Badges in Table */
    .petugas-badge-list {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        align-items: center;
    }
    .petugas-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 10px;
        border-radius: 20px;
        background: rgba(0, 40, 85, 0.06);
        border: 1px solid rgba(0, 40, 85, 0.10);
        font-size: 12px;
        font-weight: 600;
        color: var(--primary-dark);
    }
    .petugas-pill .avatar-mini {
        width: 20px;
        height: 20px;
        border-radius: 50%;
        background: var(--primary);
        color: #fff;
        font-size: 10px;
        font-weight: 800;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    /* Option Item Hover in Searchable Multi-Select */
    .petugas-option-item:hover {
        background: rgba(0, 40, 85, 0.06);
    }
    html[data-theme="dark"] .petugas-option-item:hover {
        background: rgba(255, 255, 255, 0.08);
    }

    /* Responsive Penugasan Layout */
    @media (max-width: 1024px) {
        .filter-section { padding: 16px; flex-direction: column; align-items: stretch; gap: 12px; }
        .filter-section .filter-group { width: 100%; }
        .filter-section .filter-group .pupr-search-group,
        .filter-section .filter-group .pupr-dropdown-wrapper,
        .filter-section .filter-group .pupr-dropdown-toggle { width: 100%; justify-content: space-between; }
        .filter-section .filter-actions { width: 100%; margin-left: 0; flex-wrap: wrap; }
        .filter-section .filter-actions .btn { flex: 1; min-width: 120px; justify-content: center; }
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
            min-width: 750px !important;
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
        .modal-box { padding: 20px 16px; }
    }
</style>
@endpush

@section('content')
    <!-- Navbar Component -->
    @include('layouts.navbar')

    <main class="dashboard-content">
        <!-- Breadcrumb -->
        <div class="breadcrumb" style="font-size:13px;color:var(--text-muted);margin-bottom:20px;display:flex;align-items:center;gap:8px;">
            <a href="{{ url('/') }}" style="color:var(--primary);text-decoration:none;font-weight:500;"><i class="fas fa-home"></i> Home</a>
            <i class="fas fa-chevron-right" style="font-size:10px;"></i>
            <span>Penugasan Petugas</span>
        </div>

        {{-- Alert Sukses --}}
        @if(session('success'))
            <div style="background:rgba(39,174,96,0.10);border:1px solid rgba(39,174,96,0.30);border-radius:var(--radius-sm);padding:14px 18px;margin-bottom:20px;font-size:13px;color:var(--success);display:flex;align-items:center;gap:10px;">
                <i class="fas fa-check-circle" style="font-size:16px;"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <!-- Filter & Search Section (System Component Design System) -->
        <form action="{{ route('penugasan') }}" method="GET" class="filter-section">
            <div class="filter-group">
                <div class="pupr-search-group">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama kegiatan, kecamatan, petugas..." class="pupr-search-input" />
                    <button type="submit" class="pupr-search-btn"><i class="fas fa-search"></i></button>
                </div>
            </div>

            {{-- Filter Status Penugasan --}}
            <div class="filter-group">
                <div class="pupr-dropdown-wrapper">
                    <button type="button" class="btn btn-outline pupr-dropdown-toggle" data-toggle="pupr-dropdown">
                        @php
                            $reqPenugasan = request('status_penugasan');
                            $labelPenugasan = $reqPenugasan === 'sudah' ? 'Sudah Ditugaskan' : ($reqPenugasan === 'belum' ? 'Belum Ditugaskan' : 'Semua Status Penugasan');
                        @endphp
                        <span class="selected-label">{{ $labelPenugasan }}</span>
                        <i class="fas fa-chevron-down" style="font-size:10px;margin-left:8px;"></i>
                    </button>
                    <div class="pupr-dropdown-menu" id="dropdownPenugasanMenu">
                        <div class="pupr-dropdown-item {{ !$reqPenugasan || $reqPenugasan=='all' ? 'active' : '' }}" data-value="all">Semua Status Penugasan</div>
                        <div class="pupr-dropdown-item {{ $reqPenugasan=='sudah' ? 'active' : '' }}" data-value="sudah">Sudah Ditugaskan</div>
                        <div class="pupr-dropdown-item {{ $reqPenugasan=='belum' ? 'active' : '' }}" data-value="belum">Belum Ditugaskan</div>
                    </div>
                </div>
                <input type="hidden" name="status_penugasan" id="inputFilterStatusPenugasan" value="{{ request('status_penugasan') }}" />
            </div>

            {{-- Filter Kecamatan --}}
            <div class="filter-group">
                <div class="pupr-dropdown-wrapper">
                    <button type="button" class="btn btn-outline pupr-dropdown-toggle" data-toggle="pupr-dropdown">
                        @php
                            $reqLokasi = request('lokasi');
                            $labelLokasi = $reqLokasi && $reqLokasi != 'all' ? 'Kec. '.ucwords(str_replace('_',' ',$reqLokasi)) : 'Semua Kecamatan';
                        @endphp
                        <span class="selected-label">{{ $labelLokasi }}</span>
                        <i class="fas fa-chevron-down" style="font-size:10px;margin-left:8px;"></i>
                    </button>
                    <div class="pupr-dropdown-menu" id="dropdownLokasiMenu" style="max-height:220px;overflow-y:auto;">
                        <div class="pupr-dropdown-item {{ !$reqLokasi || $reqLokasi=='all' ? 'active' : '' }}" data-value="all">Semua Kecamatan</div>
                        @foreach(['Kaliwates','Sumbersari','Patrang','Ajung','Rambipuji','Balung','Ambulu','Wuluhan','Puger','Kencong','Gumukmas','Umbulsari','Semboro','Jombang','Silo','Mayang','Mumbulsari','Jenggawah','Tempurejo','Pakusari','Sukowono','Kalisat','Ledokombo','Sumberjambe','Arjasa','Jelbuk','Bangsalsari','Panti','Sukorambi','Tanggul','Sumberbaru'] as $kec)
                            <div class="pupr-dropdown-item {{ $reqLokasi == strtolower(str_replace(' ','_',$kec)) ? 'active' : '' }}" data-value="{{ strtolower(str_replace(' ','_',$kec)) }}">Kec. {{ $kec }}</div>
                        @endforeach
                    </div>
                </div>
                <input type="hidden" name="lokasi" id="inputFilterLokasi" value="{{ request('lokasi') }}" />
            </div>

            <div class="filter-actions">
                <a href="{{ route('penugasan') }}" class="btn btn-outline"><i class="fas fa-redo"></i> Reset</a>
            </div>
        </form>

        <!-- Tabel Penugasan (Menggunakan Desain Tabel PUPR Sistem) -->
        <div class="table-card">
            <div class="table-header">
                <h3><i class="fas fa-tasks" style="color:var(--primary);margin-right:10px;"></i>Daftar Penugasan Petugas Lapangan</h3>
            </div>

            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th style="width:60px;">No</th>
                            <th style="min-width:280px;">Nama Kegiatan &amp; Kecamatan</th>
                            <th style="min-width:260px;">Petugas Lapangan (Ditugaskan)</th>
                            <th style="width:140px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($kegiatans as $index => $item)
                        <tr>
                            <td>{{ $kegiatans->firstItem() + $index }}</td>
                            <td>
                                <strong style="color:var(--primary-dark);font-size:14px;display:block;margin-bottom:4px;">{{ $item->nama_kegiatan }}</strong>
                                <span style="font-size:12px;color:var(--text-muted);display:inline-flex;align-items:center;gap:4px;">
                                    <i class="fas fa-location-dot" style="color:var(--primary);font-size:11px;"></i> Kec. {{ ucwords(str_replace('_',' ',$item->lokasi)) }}
                                </span>
                            </td>
                            <td>
                                @if($item->petugas->count() > 0)
                                    <div class="petugas-badge-list">
                                        @foreach($item->petugas as $p)
                                            <span class="petugas-pill" title="{{ $p->name }} (NIP: {{ $p->nip ?? '-' }})">
                                                <span class="avatar-mini">{{ strtoupper(substr($p->name, 0, 1)) }}</span>
                                                <span>{{ $p->name }}</span>
                                            </span>
                                        @endforeach
                                    </div>
                                @else
                                    <span style="font-size:12px;color:var(--text-muted);font-style:italic;">
                                        <i class="fas fa-user-slash" style="opacity:0.5;margin-right:4px;"></i> Belum ada petugas
                                    </span>
                                @endif
                            </td>
                            <td>
                                <div class="table-actions-cell" style="display:flex;gap:6px;">
                                    <button type="button" class="btn-icon edit"
                                        style="padding:6px 14px;border-radius:6px;"
                                        onclick="openModalPenugasan({{ $item->id }}, '{{ addslashes($item->nama_kegiatan) }}', 'Kec. {{ ucwords(str_replace('_',' ',$item->lokasi)) }}', {{ json_encode($item->petugas->pluck('id')->toArray()) }})"
                                    >
                                        <i class="fas fa-user-plus"></i> Penugasan
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" style="text-align:center;padding:40px;color:var(--text-muted);">
                                <i class="fas fa-inbox" style="font-size:32px;display:block;margin-bottom:10px;opacity:0.4;"></i>
                                Belum ada data kegiatan untuk penugasan.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="table-footer">
                <span>Menampilkan {{ $kegiatans->firstItem() ?? 0 }}-{{ $kegiatans->lastItem() ?? 0 }} dari {{ $kegiatans->total() }} data kegiatan</span>

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

    <!-- ============================================================
         Modal Form Penugasan (Menggunakan PuprModal Sistem Seperti user/index)
         ============================================================ -->
    <div class="modal-overlay" id="modalPenugasan">
        <div class="modal-box" style="max-width:540px;">
            <div class="modal-header">
                <h3 id="modalPenugasanTitle"><i class="fas fa-user-plus" style="color:var(--primary);margin-right:10px;"></i>Penugasan Petugas Survei</h3>
                <button class="close-btn" id="closeModalPenugasanBtn" type="button" onclick="window.PuprModal.close('modalPenugasan')"><i class="fas fa-times"></i></button>
            </div>

            <form id="formPenugasan" action="" method="POST">
                @csrf
                <div class="modal-body">
                    <!-- Detail Informasi Kegiatan -->
                    <div style="background:rgba(0,40,85,0.04);border:1px solid rgba(0,40,85,0.08);border-radius:10px;padding:14px 16px;margin-bottom:18px;">
                        <div style="font-size:11px;font-weight:700;text-transform:uppercase;color:var(--text-muted);letter-spacing:0.5px;margin-bottom:4px;">Nama Kegiatan</div>
                        <div style="font-size:15px;font-weight:800;color:var(--primary-dark);" id="modalNamaKegiatan">-</div>
                        <div style="font-size:12px;color:var(--text-secondary);margin-top:4px;" id="modalLokasiKegiatan">-</div>
                    </div>

                    <div class="form-group">
                        <label style="display:flex;align-items:center;justify-content:space-between;font-size:13px;font-weight:700;color:var(--text-primary);margin-bottom:8px;">
                            <span><i class="fas fa-users" style="color:var(--primary);margin-right:6px;"></i>Pilih Petugas Survei Lapangan</span>
                            <span style="font-size:11px;font-weight:400;color:var(--text-muted);" id="counterPetugasSelected">0 dipilih</span>
                        </label>

                        <!-- Input Live Search Petugas -->
                        <div class="pupr-search-group" style="margin-bottom:10px;">
                            <input type="text" id="searchPetugasInput" placeholder="Ketik nama, NIP, atau kecamatan petugas..." class="pupr-search-input" style="padding:8px 12px;font-size:13px;" />
                            <button type="button" class="pupr-search-btn"><i class="fas fa-search"></i></button>
                        </div>

                        <!-- Checkbox Container List Petugas (User Role 'petugas') -->
                        <div id="petugasListContainer" style="max-height:220px;overflow-y:auto;border:1px solid rgba(0,40,85,0.14);border-radius:8px;padding:6px;background:var(--bg-body);">
                            @foreach($allPetugas as $p)
                                <label class="petugas-option-item" data-search="{{ strtolower($p->name . ' ' . $p->nip . ' ' . $p->kecamatan . ' ' . $p->jabatan) }}" style="display:flex;align-items:center;gap:10px;padding:8px 12px;border-radius:6px;cursor:pointer;transition:background 0.15s ease;margin-bottom:2px;">
                                    <input type="checkbox" name="petugas_ids[]" value="{{ $p->id }}" class="petugas-checkbox" style="width:17px;height:17px;cursor:pointer;accent-color:var(--primary);" onchange="handlePetugasCheckboxChange(this)" />
                                    <div class="avatar-mini" style="width:28px;height:28px;border-radius:50%;background:var(--primary);color:#fff;font-weight:800;font-size:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                        {{ strtoupper(substr($p->name, 0, 1)) }}
                                    </div>
                                    <div style="flex:1;overflow:hidden;">
                                        <div style="font-weight:700;font-size:13px;color:var(--text-primary);">{{ $p->name }}</div>
                                        <div style="font-size:11px;color:var(--text-muted);">{{ $p->jabatan }} &bull; <span style="color:var(--primary);font-weight:600;">Kec. {{ $p->kecamatan }}</span></div>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-cancel" id="cancelModalPenugasanBtn" onclick="window.PuprModal.close('modalPenugasan')">Batal</button>
                    <button type="submit" class="btn btn-submit" id="btnSimpanPenugasan">
                        <i class="fas fa-save"></i> Simpan Penugasan
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Event listener filter dropdown penugasan
        document.querySelectorAll('#dropdownPenugasanMenu .pupr-dropdown-item').forEach(item => {
            item.addEventListener('click', function() {
                document.getElementById('inputFilterStatusPenugasan').value = this.dataset.value;
                this.closest('form').submit();
            });
        });

        // Event listener filter dropdown lokasi
        document.querySelectorAll('#dropdownLokasiMenu .pupr-dropdown-item').forEach(item => {
            item.addEventListener('click', function() {
                document.getElementById('inputFilterLokasi').value = this.dataset.value;
                this.closest('form').submit();
            });
        });

        // Live Search Input Filter di dalam Modal Penugasan
        const searchInput = document.getElementById('searchPetugasInput');
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                const query = this.value.toLowerCase().trim();
                document.querySelectorAll('.petugas-option-item').forEach(item => {
                    const text = item.getAttribute('data-search') || '';
                    item.style.display = text.includes(query) ? 'flex' : 'none';
                });
            });
        }

        // Trigger Loading Overlay saat submit form penugasan
        const form = document.getElementById('formPenugasan');
        if (form) {
            form.addEventListener('submit', function() {
                window.PuprLoading.show('Menyimpan Penugasan...');
            });
        }
    });

    // Buka Modal Penugasan
    function openModalPenugasan(kegiatanId, namaKegiatan, lokasiKegiatan, assignedUserIds) {
        document.getElementById('modalNamaKegiatan').textContent = namaKegiatan;
        document.getElementById('modalLokasiKegiatan').textContent = lokasiKegiatan;
        document.getElementById('formPenugasan').action = "{{ url('/penugasan') }}/" + kegiatanId;

        // Reset input search
        const searchInput = document.getElementById('searchPetugasInput');
        if (searchInput) {
            searchInput.value = '';
            document.querySelectorAll('.petugas-option-item').forEach(item => item.style.display = 'flex');
        }

        // Check checkbox yang sudah ditugaskan
        document.querySelectorAll('.petugas-checkbox').forEach(cb => {
            cb.checked = assignedUserIds.includes(parseInt(cb.value));
        });

        updateSelectedCounter();

        // Buka modal menggunakan sistem PuprModal global persis seperti user/index
        window.PuprModal.open('modalPenugasan');
    }

    // Counter update jumlah petugas dipilih
    function handlePetugasCheckboxChange(checkbox) {
        const checkedList = document.querySelectorAll('.petugas-checkbox:checked');
        if (checkedList.length > 2) {
            checkbox.checked = false;
            alert('Maksimal penugasan adalah 2 orang petugas survei per kegiatan!');
        }
        updateSelectedCounter();
    }

    function updateSelectedCounter() {
        const checkedCount = document.querySelectorAll('.petugas-checkbox:checked').length;
        const counterEl = document.getElementById('counterPetugasSelected');
        if (counterEl) {
            counterEl.textContent = checkedCount + ' / 2 petugas dipilih';
        }
    }
</script>
@endpush
