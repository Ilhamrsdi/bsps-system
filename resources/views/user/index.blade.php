@extends('layouts.partial.app')

@section('title', 'PUPR Jember - Petugas Survei')
@section('title_header', 'Petugas Survei System')
@section('subtitle_header', 'Kelola data petugas survei lapangan dan pengguna sistem Dinas PUPR Kabupaten Jember')

@push('styles')
<style>
    /* ============================================================
       PAGE STYLES: USER / PETUGAS SURVEI (PUPR THEME)
       Page-specific styles only - table, filter & search moved to table.css / component.css
       ============================================================ */
    .breadcrumb {
        font-size: 13px;
        color: var(--text-muted);
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .breadcrumb a { color: var(--primary); text-decoration: none; font-weight: 500; }
    .breadcrumb a:hover { color: var(--secondary); }

    /* Stats Petugas */
    .stats-petugas {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
        margin-bottom: 24px;
    }
    .stats-petugas .stat-item {
        background: var(--bg-card);
        border-radius: var(--radius-sm);
        padding: 16px 20px;
        box-shadow: var(--shadow-sm);
        border: 1px solid rgba(0, 40, 85, 0.06);
        display: flex;
        align-items: center;
        gap: 14px;
    }
    .stats-petugas .stat-item .icon {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        flex-shrink: 0;
    }
    .stats-petugas .stat-item .icon.blue { background: rgba(0, 40, 85, 0.10); color: var(--primary); }
    .stats-petugas .stat-item .icon.green { background: rgba(39, 174, 96, 0.12); color: var(--success); }
    .stats-petugas .stat-item .icon.orange { background: rgba(255, 184, 0, 0.15); color: #d69e00; }
    .stats-petugas .stat-item .icon.red { background: rgba(231, 76, 60, 0.12); color: var(--danger); }
    .stats-petugas .stat-item .info .value { font-size: 22px; font-weight: 800; line-height: 1.2; color: var(--primary-dark); }
    .stats-petugas .stat-item .info .label { font-size: 12px; color: var(--text-muted); font-weight: 500; }

    /* Responsive User Petugas Layout */
    @media (max-width: 1024px) {
        .stats-petugas { grid-template-columns: repeat(2, 1fr); gap: 14px; }
        .filter-section { padding: 16px; flex-direction: column; align-items: stretch; gap: 12px; }
        .filter-section .filter-group { width: 100%; }
        .filter-section .filter-group .pupr-search-group,
        .filter-section .filter-group .pupr-dropdown-wrapper,
        .filter-section .filter-group .pupr-dropdown-toggle { width: 100%; justify-content: space-between; }
        .filter-section .filter-actions { width: 100%; margin-left: 0; flex-wrap: wrap; }
        .filter-section .filter-actions .btn { flex: 1; min-width: 120px; justify-content: center; }
        .table-card .table-header { flex-direction: column; align-items: stretch; gap: 12px; }
        .table-card .table-header .table-actions { width: 100%; flex-wrap: wrap; }
        .table-card .table-header .table-actions .btn { flex: 1; min-width: 140px; justify-content: center; }
    }

    @media (max-width: 768px) {
        .stats-petugas .stat-item { padding: 14px 16px; }
        .stats-petugas .stat-item .info .value { font-size: 20px; }
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
            min-width: 1000px !important;
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
        .stats-petugas { grid-template-columns: 1fr; gap: 10px; }
        .dashboard-content { padding: 12px; }
        .stat-item { padding: 12px 16px; }
    }
</style>
@endpush

@section('content')
    <!-- Navbar Component per Halaman -->
    @include('layouts.navbar')

    <main class="dashboard-content">
        <!-- Breadcrumb -->
        <div class="breadcrumb">
            <a href="{{ url('/dashboard') }}"><i class="fas fa-home"></i> Dashboard</a>
            <i class="fas fa-chevron-right" style="font-size:10px;"></i>
            <span>Petugas Survei</span>
        </div>

        @if(session('success'))
            <div style="padding:14px 18px;border-radius:var(--radius-sm);background:rgba(39,174,96,0.12);border:1px solid rgba(39,174,96,0.3);color:var(--success);font-weight:600;margin-bottom:20px;display:flex;align-items:center;gap:10px;">
                <i class="fas fa-check-circle" style="font-size:18px;"></i>
                <div>{{ session('success') }}</div>
            </div>
        @endif

        <!-- Stats Petugas Counter -->
        <div class="stats-petugas">
            <div class="stat-item">
                <div class="icon blue"><i class="fas fa-users"></i></div>
                <div class="info">
                    <div class="value">{{ $totalCount }}</div>
                    <div class="label">Total Petugas</div>
                </div>
            </div>
            <div class="stat-item">
                <div class="icon green"><i class="fas fa-user-check"></i></div>
                <div class="info">
                    <div class="value">{{ $aktifCount }}</div>
                    <div class="label">Status Aktif</div>
                </div>
            </div>
            <div class="stat-item">
                <div class="icon orange"><i class="fas fa-hard-hat"></i></div>
                <div class="info">
                    <div class="value">{{ $bertugasCount }}</div>
                    <div class="label">Sedang Bertugas</div>
                </div>
            </div>
            <div class="stat-item">
                <div class="icon red"><i class="fas fa-clock"></i></div>
                <div class="info">
                    <div class="value">{{ $cutiCount }}</div>
                    <div class="label">Cuti</div>
                </div>
            </div>
        </div>

        <!-- Filter & Search Section Form -->
        <form action="{{ url('/user') }}" method="GET" class="filter-section">
            <div class="filter-group">
                <div class="pupr-search-group">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, NIP, email..." class="pupr-search-input" />
                    <button type="submit" class="pupr-search-btn"><i class="fas fa-search"></i></button>
                </div>
            </div>

            <div class="filter-group">
                <div class="pupr-dropdown-wrapper">
                    <button type="button" class="btn btn-outline pupr-dropdown-toggle" data-toggle="pupr-dropdown">
                        <span class="selected-label">{{ request('status') == 'aktif' ? 'Aktif' : (request('status') == 'bertugas' ? 'Bertugas' : (request('status') == 'cuti' ? 'Cuti' : 'Semua Status')) }}</span>
                        <i class="fas fa-chevron-down" style="font-size:10px;margin-left:8px;"></i>
                    </button>
                    <div class="pupr-dropdown-menu" id="dropdownUserStatusMenu">
                        <div class="pupr-dropdown-item {{ request('status') == 'all' || !request('status') ? 'active' : '' }}" data-value="all">Semua Status</div>
                        <div class="pupr-dropdown-item {{ request('status') == 'aktif' ? 'active' : '' }}" data-value="aktif">Aktif</div>
                        <div class="pupr-dropdown-item {{ request('status') == 'bertugas' ? 'active' : '' }}" data-value="bertugas">Bertugas</div>
                        <div class="pupr-dropdown-item {{ request('status') == 'cuti' ? 'active' : '' }}" data-value="cuti">Cuti</div>
                    </div>
                </div>
                <input type="hidden" name="status" id="inputFilterStatus" value="{{ request('status', 'all') }}" />
            </div>

            <div class="filter-group">
                <div class="pupr-dropdown-wrapper">
                    <button type="button" class="btn btn-outline pupr-dropdown-toggle" data-toggle="pupr-dropdown">
                        <span class="selected-label">{{ request('kecamatan') && request('kecamatan') != 'all' ? request('kecamatan') : 'Semua Kecamatan' }}</span>
                        <i class="fas fa-chevron-down" style="font-size:10px;margin-left:8px;"></i>
                    </button>
                    <div class="pupr-dropdown-menu" id="dropdownUserKecamatanMenu">
                        <div class="pupr-dropdown-item {{ request('kecamatan') == 'all' || !request('kecamatan') ? 'active' : '' }}" data-value="all">Semua Kecamatan</div>
                        <div class="pupr-dropdown-item {{ request('kecamatan') == 'Kaliwates' ? 'active' : '' }}" data-value="Kaliwates">Kaliwates</div>
                        <div class="pupr-dropdown-item {{ request('kecamatan') == 'Sumbersari' ? 'active' : '' }}" data-value="Sumbersari">Sumbersari</div>
                        <div class="pupr-dropdown-item {{ request('kecamatan') == 'Ajung' ? 'active' : '' }}" data-value="Ajung">Ajung</div>
                        <div class="pupr-dropdown-item {{ request('kecamatan') == 'Patrang' ? 'active' : '' }}" data-value="Patrang">Patrang</div>
                        <div class="pupr-dropdown-item {{ request('kecamatan') == 'Rambipuji' ? 'active' : '' }}" data-value="Rambipuji">Rambipuji</div>
                    </div>
                </div>
                <input type="hidden" name="kecamatan" id="inputFilterKecamatan" value="{{ request('kecamatan', 'all') }}" />
            </div>

            <div class="filter-actions">
                <a href="{{ url('/user') }}" class="btn btn-outline"><i class="fas fa-redo"></i> Reset</a>
                <button type="button" class="btn btn-success" id="tambahPetugasBtn"><i class="fas fa-user-plus"></i> Tambah Petugas</button>
            </div>
        </form>

        <!-- Table Data Users -->
        <div class="table-card">
            <div class="table-header">
                <h3><i class="fas fa-users" style="color:var(--primary);margin-right:10px;"></i>Daftar Petugas Survei Lapangan</h3>
            </div>

            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th style="width:50px;">No</th>
                            <th style="min-width:220px;">Nama &amp; Email Petugas</th>
                            <th style="min-width:140px;">NIP</th>
                            <th style="min-width:160px;">Jabatan</th>
                            <th style="min-width:130px;">Kecamatan</th>
                            <th style="min-width:120px;">No. HP</th>
                            <th style="min-width:100px;">Role</th>
                            <th style="min-width:110px;">Status</th>
                            <th style="min-width:160px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $index => $item)
                            <tr>
                                <td>{{ ($users->currentPage() - 1) * $users->perPage() + $index + 1 }}</td>
                                <td>
                                    <div style="display:flex;align-items:center;gap:12px;">
                                        <div class="petugas-avatar">{{ strtoupper(substr($item->name, 0, 1)) }}</div>
                                        <div>
                                            <div style="font-weight:700;color:var(--primary-dark);">{{ $item->name }}</div>
                                            <div style="font-size:12px;color:var(--text-muted);">{{ $item->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td><span style="font-family:monospace;font-size:12px;font-weight:600;">{{ $item->nip ?? '-' }}</span></td>
                                <td><span style="font-weight:600;color:var(--primary);">{{ $item->jabatan }}</span></td>
                                <td><i class="fas fa-location-dot" style="color:var(--text-muted);font-size:12px;"></i> {{ $item->kecamatan }}</td>
                                <td>{{ $item->phone ?? '-' }}</td>
                                <td>
                                    @if($item->role === 'admin')
                                        <span class="badge-status" style="background:rgba(0,40,85,0.12);color:var(--primary);">
                                            <i class="fas fa-shield-halved"></i> Admin
                                        </span>
                                    @else
                                        <span class="badge-status" style="background:rgba(255,184,0,0.15);color:#d69e00;">
                                            <i class="fas fa-user-hard-hat"></i> Petugas
                                        </span>
                                    @endif
                                </td>
                                <td><span class="badge-status {{ $item->status }}">{{ ucfirst($item->status) }}</span></td>
                                <td>
                                    <div class="table-actions-cell" style="display:flex;gap:6px;">
                                        <button type="button" class="btn-icon edit" onclick="editUserModal({{ json_encode($item) }})">
                                            <i class="fas fa-pen"></i> Edit
                                        </button>
                                        <button type="button" class="btn-icon delete"
                                            onclick="konfirmasiHapus({{ $item->id }}, '{{ addslashes($item->name) }}', '{{ addslashes($item->jabatan) }}')"
                                        >
                                            <i class="fas fa-trash"></i> Hapus
                                        </button>
                                        {{-- Hidden form untuk delete —- disubmit via JS --}}
                                        <form id="formHapusUser_{{ $item->id }}" action="{{ url('/user/' . $item->id) }}" method="POST" style="display:none;">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" style="text-align:center;padding:24px;color:var(--text-muted);">
                                    <i class="fas fa-folder-open" style="font-size:24px;margin-bottom:8px;display:block;"></i>
                                    Belum ada data petugas survei.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="table-footer">
                <span>Menampilkan {{ $users->firstItem() ?? 0 }} - {{ $users->lastItem() ?? 0 }} dari total {{ $users->total() }} data petugas</span>
                
                @if($users->hasPages())
                    <div class="pagination">
                        {{-- Previous Page Link --}}
                        @if ($users->onFirstPage())
                            <span class="page disabled"><i class="fas fa-chevron-left"></i></span>
                        @else
                            <a href="{{ $users->previousPageUrl() }}" class="page"><i class="fas fa-chevron-left"></i></a>
                        @endif

                        {{-- Pagination Elements --}}
                        @foreach ($users->getUrlRange(max(1, $users->currentPage() - 2), min($users->lastPage(), $users->currentPage() + 2)) as $page => $url)
                            @if ($page == $users->currentPage())
                                <span class="page active">{{ $page }}</span>
                            @else
                                <a href="{{ $url }}" class="page">{{ $page }}</a>
                            @endif
                        @endforeach

                        {{-- Next Page Link --}}
                        @if ($users->hasMorePages())
                            <a href="{{ $users->nextPageUrl() }}" class="page"><i class="fas fa-chevron-right"></i></a>
                        @else
                            <span class="page disabled"><i class="fas fa-chevron-right"></i></span>
                        @endif
                    </div>
                @endif
            </div>

        </div>
    </main>

    <!-- ============================================================
         Modal Konfirmasi Hapus Petugas
         ============================================================ -->
    <div class="modal-overlay" id="modalKonfirmasiHapus">
        <div class="modal-box" style="max-width:440px;">
            <div class="modal-header" style="border-bottom-color:rgba(231,76,60,0.15);">
                <h3 style="color:var(--danger);"><i class="fas fa-triangle-exclamation" style="margin-right:10px;"></i>Konfirmasi Hapus</h3>
                <button class="close-btn" type="button" onclick="window.PuprModal.close('modalKonfirmasiHapus')"><i class="fas fa-times"></i></button>
            </div>
            <div class="modal-body" style="text-align:center;padding:28px 24px 20px;">
                <!-- Icon peringatan animasi -->
                <div style="width:72px;height:72px;border-radius:50%;background:rgba(231,76,60,0.10);display:flex;align-items:center;justify-content:center;margin:0 auto 18px;">
                    <i class="fas fa-user-xmark" style="font-size:30px;color:var(--danger);"></i>
                </div>
                <p style="font-size:15px;font-weight:600;color:var(--text-primary);margin-bottom:8px;">Yakin ingin menghapus petugas ini?</p>
                <p style="font-size:13px;color:var(--text-muted);margin-bottom:4px;">Nama: <strong id="hapusNamaPetugas" style="color:var(--primary);"></strong></p>
                <p style="font-size:13px;color:var(--text-muted);margin-bottom:20px;">Jabatan: <strong id="hapusJabatanPetugas"></strong></p>
                <div style="background:rgba(231,76,60,0.06);border:1px solid rgba(231,76,60,0.15);border-radius:8px;padding:10px 14px;margin-bottom:8px;">
                    <p style="font-size:12px;color:var(--danger);margin:0;"><i class="fas fa-circle-info" style="margin-right:6px;"></i>Data yang dihapus <strong>tidak dapat dikembalikan</strong> kembali.</p>
                </div>
            </div>
            <div class="modal-footer" style="justify-content:center;gap:12px;">
                <button type="button" class="btn btn-cancel" style="min-width:120px;" onclick="window.PuprModal.close('modalKonfirmasiHapus')">
                    <i class="fas fa-xmark"></i> Batal
                </button>
                <button type="button" class="btn btn-submit" id="btnKonfirmasiHapus"
                    style="min-width:140px;background:var(--danger) !important;border-color:var(--danger) !important;"
                >
                    <i class="fas fa-trash"></i> Ya, Hapus!
                </button>
            </div>
        </div>
    </div>

    <!-- Modal Form Tambah / Edit Petugas -->
    <div class="modal-overlay" id="modalPetugas">
        <div class="modal-box">
            <div class="modal-header">
                <h3 id="modalPetugasTitle"><i class="fas fa-user-plus" style="color:var(--primary);margin-right:10px;"></i>Tambah Petugas Baru</h3>
                <button class="close-btn" id="closeModalBtn" type="button"><i class="fas fa-times"></i></button>
            </div>
            <form id="formPetugasDb" action="{{ url('/user') }}" method="POST">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST" />

                <div class="modal-body">
                    <div class="form-row">
                        <div class="form-group">
                            <label>Nama Lengkap</label>
                            <input type="text" name="name" id="inputNama" placeholder="Masukkan nama lengkap" required />
                        </div>
                        <div class="form-group">
                            <label>NIP Petugas</label>
                            <input type="text" name="nip" id="inputNip" placeholder="19890412 201801 2 004" />
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Email (Login)</label>
                            <input type="email" name="email" id="inputEmail" placeholder="petugas@pupr.jember.go.id" required />
                        </div>
                        <div class="form-group">
                            <label>Password</label>
                            <input type="password" name="password" id="inputPassword" placeholder="••••••••" />
                            <small id="pwdHelp" style="font-size:11px;color:var(--text-muted);display:none;">Kosongkan jika tidak ingin mengubah password.</small>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Jabatan Petugas</label>
                            <input type="text" name="jabatan" id="inputJabatan" value="Petugas Survei Lapangan" required />
                        </div>
                        <div class="form-group">
                            <label>No. HP / WhatsApp</label>
                            <input type="text" name="phone" id="inputHp" placeholder="081234567890" />
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Kecamatan Tugas</label>
                            <input type="hidden" name="kecamatan" id="inputKecamatanHidden" value="Kaliwates" required />
                            <div class="pupr-dropdown-wrapper" style="width:100%;">
                                <button type="button" class="pupr-dropdown-toggle" data-toggle="pupr-dropdown" style="width:100%;display:flex;align-items:center;justify-content:space-between;padding:11px 16px;">
                                    <span class="selected-label">Kaliwates</span>
                                    <i class="fas fa-chevron-down" style="font-size:11px;opacity:0.6;"></i>
                                </button>
                                <div class="pupr-dropdown-menu" style="width:100%;max-height:180px;overflow-y:auto;">
                                    <div class="pupr-dropdown-item active" data-value="Kaliwates" data-target="inputKecamatanHidden">Kaliwates</div>
                                    <div class="pupr-dropdown-item" data-value="Sumbersari" data-target="inputKecamatanHidden">Sumbersari</div>
                                    <div class="pupr-dropdown-item" data-value="Ajung" data-target="inputKecamatanHidden">Ajung</div>
                                    <div class="pupr-dropdown-item" data-value="Patrang" data-target="inputKecamatanHidden">Patrang</div>
                                    <div class="pupr-dropdown-item" data-value="Rambipuji" data-target="inputKecamatanHidden">Rambipuji</div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Status Tugas</label>
                            <input type="hidden" name="status" id="inputStatusHidden" value="aktif" required />
                            <div class="pupr-dropdown-wrapper" style="width:100%;">
                                <button type="button" class="pupr-dropdown-toggle" data-toggle="pupr-dropdown" style="width:100%;display:flex;align-items:center;justify-content:space-between;padding:11px 16px;">
                                    <span class="selected-label">Aktif</span>
                                    <i class="fas fa-chevron-down" style="font-size:11px;opacity:0.6;"></i>
                                </button>
                                <div class="pupr-dropdown-menu" style="width:100%;max-height:180px;overflow-y:auto;">
                                    <div class="pupr-dropdown-item active" data-value="aktif" data-target="inputStatusHidden">Aktif</div>
                                    <div class="pupr-dropdown-item" data-value="bertugas" data-target="inputStatusHidden">Sedang Bertugas</div>
                                    <div class="pupr-dropdown-item" data-value="cuti" data-target="inputStatusHidden">Cuti</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label><i class="fas fa-user-shield" style="color:var(--primary);margin-right:6px;"></i>Role / Hak Akses Pengguna <span style="color:var(--danger);">*</span></label>
                            <input type="hidden" name="role" id="inputRoleHidden" value="petugas" required />
                            <div class="pupr-dropdown-wrapper" style="width:100%;">
                                <button type="button" class="pupr-dropdown-toggle" data-toggle="pupr-dropdown" style="width:100%;display:flex;align-items:center;justify-content:space-between;padding:11px 16px;">
                                    <span class="selected-label">Petugas (Akses Survei Lapangan)</span>
                                    <i class="fas fa-chevron-down" style="font-size:11px;opacity:0.6;"></i>
                                </button>
                                <div class="pupr-dropdown-menu" style="width:100%;max-height:180px;overflow-y:auto;">
                                    <div class="pupr-dropdown-item active" data-value="petugas" data-target="inputRoleHidden">Petugas (Akses Survei Lapangan)</div>
                                    <div class="pupr-dropdown-item" data-value="admin" data-target="inputRoleHidden">Admin (Administrator System)</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-cancel" id="cancelModalBtn">Batal</button>
                    <button type="submit" class="btn btn-submit"><i class="fas fa-save"></i> Simpan Data</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function setPuprDropdownValue(hiddenInputId, value) {
        const hiddenInput = document.getElementById(hiddenInputId);
        if (!hiddenInput) return;

        hiddenInput.value = value;
        const wrapper = hiddenInput.closest('.form-group').querySelector('.pupr-dropdown-wrapper');
        if (wrapper) {
            const item = wrapper.querySelector(`.pupr-dropdown-item[data-value="${value}"]`);
            if (item) {
                wrapper.querySelectorAll('.pupr-dropdown-item').forEach(i => i.classList.remove('active'));
                item.classList.add('active');
                const label = wrapper.querySelector('.selected-label');
                if (label) label.textContent = item.textContent.trim();
            }
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const tambahBtn = document.getElementById('tambahPetugasBtn');
        const closeBtn = document.getElementById('closeModalBtn');
        const cancelBtn = document.getElementById('cancelModalBtn');

        if (tambahBtn) {
            tambahBtn.addEventListener('click', function() {
                document.getElementById('modalPetugasTitle').innerHTML = '<i class="fas fa-user-plus" style="color:var(--primary);margin-right:10px;"></i>Tambah Petugas Baru';
                document.getElementById('formPetugasDb').action = "{{ url('/user') }}";
                document.getElementById('formMethod').value = 'POST';
                
                document.getElementById('inputNama').value = '';
                document.getElementById('inputNip').value = '';
                document.getElementById('inputEmail').value = '';
                document.getElementById('inputPassword').value = '';
                document.getElementById('inputPassword').required = true;
                document.getElementById('pwdHelp').style.display = 'none';
                document.getElementById('inputJabatan').value = 'Petugas Survei Lapangan';
                document.getElementById('inputHp').value = '';
                
                setPuprDropdownValue('inputKecamatanHidden', 'Kaliwates');
                setPuprDropdownValue('inputStatusHidden', 'aktif');
                setPuprDropdownValue('inputRoleHidden', 'petugas');

                window.PuprModal.open('modalPetugas');
            });
        }

        if (closeBtn) closeBtn.addEventListener('click', () => window.PuprModal.close('modalPetugas'));
        if (cancelBtn) cancelBtn.addEventListener('click', () => window.PuprModal.close('modalPetugas'));

        // Setup Custom Dropdown Selection for Filter Status & Kecamatan
        document.querySelectorAll('#dropdownUserStatusMenu .pupr-dropdown-item').forEach(item => {
            item.addEventListener('click', function() {
                document.getElementById('inputFilterStatus').value = this.dataset.value;
                window.PuprLoading.show('Memfilter Status...');
                this.closest('form').submit();
            });
        });

        document.querySelectorAll('#dropdownUserKecamatanMenu .pupr-dropdown-item').forEach(item => {
            item.addEventListener('click', function() {
                document.getElementById('inputFilterKecamatan').value = this.dataset.value;
                window.PuprLoading.show('Memfilter Kecamatan...');
                this.closest('form').submit();
            });
        });

        // Trigger Loading Overlay saat submit form filter / pencarian
        const filterForm = document.querySelector('form.filter-section');
        if (filterForm) {
            filterForm.addEventListener('submit', function() {
                window.PuprLoading.show('Memuat Hasil Pencarian...');
            });
        }

        // Trigger Loading Overlay saat simpan data petugas modal
        const formPetugas = document.getElementById('formPetugasDb');
        if (formPetugas) {
            formPetugas.addEventListener('submit', function() {
                window.PuprLoading.show('Menyimpan Data Petugas...');
            });
        }

        // Trigger Loading Overlay saat klik tombol pagination
        document.querySelectorAll('.pagination a').forEach(link => {
            link.addEventListener('click', function() {
                window.PuprLoading.show('Membuka Halaman...');
            });
        });
    });

    function editUserModal(user) {
        document.getElementById('modalPetugasTitle').innerHTML = '<i class="fas fa-user-edit" style="color:var(--primary);margin-right:10px;"></i>Edit Data Petugas';
        document.getElementById('formPetugasDb').action = "{{ url('/user') }}/" + user.id;
        document.getElementById('formMethod').value = 'PUT';

        document.getElementById('inputNama').value = user.name || '';
        document.getElementById('inputNip').value = user.nip || '';
        document.getElementById('inputEmail').value = user.email || '';
        document.getElementById('inputPassword').value = '';
        document.getElementById('inputPassword').required = false;
        document.getElementById('pwdHelp').style.display = 'block';
        document.getElementById('inputJabatan').value = user.jabatan || 'Petugas Survei Lapangan';
        document.getElementById('inputHp').value = user.phone || '';

        setPuprDropdownValue('inputKecamatanHidden', user.kecamatan || 'Kaliwates');
        setPuprDropdownValue('inputStatusHidden', user.status || 'aktif');
        setPuprDropdownValue('inputRoleHidden', user.role || 'petugas');

        window.PuprModal.open('modalPetugas');
    }

    // ============================================================
    // Modal Konfirmasi Hapus Petugas
    // ============================================================
    let _hapusUserId = null;

    function konfirmasiHapus(userId, nama, jabatan) {
        _hapusUserId = userId;
        document.getElementById('hapusNamaPetugas').textContent = nama;
        document.getElementById('hapusJabatanPetugas').textContent = jabatan;
        window.PuprModal.open('modalKonfirmasiHapus');
    }

    document.getElementById('btnKonfirmasiHapus').addEventListener('click', function() {
        if (!_hapusUserId) return;
        window.PuprLoading.show('Menghapus Data Petugas...');
        const form = document.getElementById('formHapusUser_' + _hapusUserId);
        if (form) form.submit();
    });
</script>
@endpush
