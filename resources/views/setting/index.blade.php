@extends('layouts.partial.app')

@section('title', 'PUPR Jember - Pengaturan')
@section('title_header', 'Pengaturan')
@section('subtitle_header', 'Konfigurasi sistem dan preferensi Dinas PUPR Kabupaten Jember')

@push('styles')
<style>
    /* ============================================================
       PAGE STYLES: SETTINGS (PUPR DYNAMIC THEME SYSTEM)
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

    .settings-grid {
        display: grid;
        grid-template-columns: 280px 1fr;
        gap: 24px;
    }

    .settings-sidebar {
        background: var(--bg-card);
        border-radius: var(--radius);
        box-shadow: var(--shadow-sm);
        border: 1px solid rgba(0, 40, 85, 0.06);
        padding: 8px 0;
        height: fit-content;
        position: sticky;
        top: calc(var(--navbar-height) + 20px);
    }

    .settings-sidebar .nav-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 20px;
        color: var(--text-secondary);
        text-decoration: none;
        transition: var(--transition);
        cursor: pointer;
        font-size: 14px;
        font-weight: 500;
        border-left: 3px solid transparent;
    }
    .settings-sidebar .nav-item:hover { background: var(--bg-body); color: var(--primary); }
    .settings-sidebar .nav-item.active { background: rgba(0, 40, 85, 0.06); color: var(--primary); border-left-color: var(--primary); font-weight: 700; }
    .settings-sidebar .nav-item i { width: 20px; font-size: 16px; text-align: center; }

    .settings-content { display: flex; flex-direction: column; gap: 24px; }
    .settings-panel { display: none; background: var(--bg-card); border-radius: var(--radius); box-shadow: var(--shadow-sm); border: 1px solid rgba(0, 40, 85, 0.06); overflow: hidden; }
    .settings-panel.active { display: block; animation: fadeIn 0.3s ease; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(6px); } to { opacity: 1; transform: translateY(0); } }

    .settings-panel .panel-header { padding: 20px 24px; border-bottom: 1px solid rgba(0, 40, 85, 0.06); display: flex; align-items: center; justify-content: space-between; }
    .settings-panel .panel-header h3 { font-size: 17px; font-weight: 700; color: var(--primary); }
    .settings-panel .panel-header p { font-size: 13px; color: var(--text-muted); font-weight: 400; }
    .settings-panel .panel-body { padding: 24px; }

    .form-group { margin-bottom: 20px; }
    .form-group label { display: block; font-size: 13px; font-weight: 600; margin-bottom: 6px; color: var(--text-secondary); }
    .form-group .help-text { font-size: 12px; color: var(--text-muted); margin-top: 4px; }
    .form-group input, .form-group select, .form-group textarea {
        width: 100%;
        padding: 10px 14px;
        border-radius: var(--radius-sm);
        border: 1px solid rgba(0, 40, 85, 0.12);
        font-family: inherit;
        font-size: 14px;
        background: var(--bg-body);
        color: var(--text-primary);
        transition: var(--transition);
        outline: none;
    }
    .form-group input:focus, .form-group select:focus, .form-group textarea:focus { border-color: var(--primary); box-shadow: 0 0 0 4px rgba(0, 40, 85, 0.08); background: #fff; }
    .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }

    .form-actions { margin-top: 24px; padding-top: 20px; border-top: 1px solid rgba(0, 40, 85, 0.06); display: flex; gap: 12px; }
    .btn { padding: 10px 28px; border-radius: var(--radius-sm); border: none; font-family: inherit; font-weight: 700; font-size: 14px; cursor: pointer; transition: var(--transition); display: inline-flex; align-items: center; gap: 8px; }
    .btn-primary { background: var(--primary); color: #fff; }
    .btn-primary:hover { background: var(--primary-light); }
    .btn-outline { background: transparent; color: var(--text-secondary); border: 1px solid rgba(0, 40, 85, 0.12); }
    .btn-danger { background: var(--danger); color: #fff; }

    /* Theme Switcher Options Visual Grid */
    .theme-card-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 16px;
        margin-bottom: 24px;
    }

    .theme-option-card {
        border: 2px solid rgba(0, 40, 85, 0.12);
        border-radius: var(--radius-sm);
        padding: 16px;
        cursor: pointer;
        transition: var(--transition);
        background: var(--bg-card);
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 10px;
        position: relative;
    }

    html[data-theme="dark"] .theme-option-card {
        background: rgba(255, 255, 255, 0.05) !important;
        border: 2px solid rgba(255, 255, 255, 0.18) !important;
    }

    .theme-option-card:hover {
        border-color: var(--primary) !important;
        transform: translateY(-2px);
    }

    .theme-option-card.active {
        border-color: var(--primary) !important;
        background: rgba(0, 40, 85, 0.04);
        box-shadow: 0 0 0 3px rgba(0, 40, 85, 0.15);
    }

    html[data-theme="dark"] .theme-option-card.active {
        background: rgba(56, 189, 248, 0.15) !important;
        border-color: var(--primary) !important;
        box-shadow: 0 0 0 3px rgba(56, 189, 248, 0.3) !important;
    }

    .theme-option-card .theme-preview-dots {
        display: flex;
        gap: 8px;
        align-items: center;
    }

    .theme-option-card .theme-preview-dots .dot-main {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        box-shadow: 0 2px 6px rgba(0,0,0,0.2);
        border: 2px solid rgba(255, 255, 255, 0.4);
    }

    .theme-option-card .theme-preview-dots .dot-accent {
        width: 22px;
        height: 22px;
        border-radius: 50%;
        box-shadow: 0 2px 6px rgba(0,0,0,0.2);
        border: 2px solid rgba(255, 255, 255, 0.4);
    }

    .theme-option-card .theme-name {
        font-size: 14px;
        font-weight: 700;
        color: var(--text-primary);
    }

    .theme-option-card .theme-badge {
        position: absolute;
        top: 8px;
        right: 8px;
        font-size: 10px;
        padding: 2px 8px;
        border-radius: 10px;
        background: var(--primary);
        color: #fff;
        font-weight: 700;
        display: none;
    }

    .theme-option-card.active .theme-badge {
        display: inline-block;
    }

    /* Responsive Setting Layout */
    @media (max-width: 1024px) {
        .settings-grid { grid-template-columns: 1fr; gap: 16px; width: 100%; max-width: 100%; }
        .settings-content { min-width: 0; width: 100%; max-width: 100%; }
        .settings-panel { width: 100%; max-width: 100%; box-sizing: border-box; }
        .settings-sidebar {
            display: flex;
            flex-direction: row;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            padding: 6px;
            position: static;
            border-radius: var(--radius-sm);
            gap: 6px;
            width: 100%;
            box-sizing: border-box;
            background: var(--bg-card);
        }
        .settings-sidebar::-webkit-scrollbar { display: none; }
        .settings-sidebar .nav-item {
            white-space: nowrap;
            flex-shrink: 0;
            text-align: center;
            border-left: none;
            border-bottom: 3px solid transparent;
            justify-content: center;
            padding: 10px 16px;
            border-radius: var(--radius-sm);
        }
        .settings-sidebar .nav-item.active {
            border-left-color: transparent;
            border-bottom-color: var(--primary);
        }
        .theme-card-grid { grid-template-columns: repeat(2, 1fr); gap: 14px; }
    }

    @media (max-width: 768px) {
        .theme-card-grid { grid-template-columns: 1fr; gap: 12px; }
        .form-row { grid-template-columns: 1fr; }
        .form-actions { flex-direction: column; }
        .form-actions .btn { width: 100%; justify-content: center; }
        .settings-panel .panel-header { padding: 16px; flex-direction: column; align-items: flex-start; gap: 6px; }
        .settings-panel .panel-header h3 { font-size: 15px; line-height: 1.4; }
        .settings-panel .panel-header p { font-size: 12.5px; line-height: 1.5; }
        .settings-panel .panel-body { padding: 16px; }
        .theme-option-card { padding: 16px 14px; box-sizing: border-box; width: 100%; }
        .theme-option-card .theme-badge { top: 10px; right: 10px; }
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
        .settings-panel table,
        .table-card table {
            width: 100% !important;
            min-width: 850px !important;
            border-collapse: collapse;
            font-size: 13.5px;
            white-space: nowrap !important;
        }
        .settings-panel table tr,
        .settings-panel table th,
        .settings-panel table td {
            transition: none !important;
            white-space: nowrap !important;
        }
    }

    @media (max-width: 480px) {
        .dashboard-content { padding: 12px; }
    }
</style>
@endpush

@section('content')
    @include('layouts.navbar')

    <main class="dashboard-content">
        <div class="breadcrumb">
            <a href="{{ url('/') }}"><i class="fas fa-home"></i> Home</a>
            <i class="fas fa-chevron-right" style="font-size:10px;"></i>
            <span>Pengaturan</span>
        </div>

        <div class="settings-grid">
            <!-- Settings Sidebar -->
            <div class="settings-sidebar" id="settingsSidebar">
                <a class="nav-item active" data-panel="tema"><i class="fas fa-palette"></i> Tema & Warna</a>
                <a class="nav-item" data-panel="log-petugas"><i class="fas fa-user-check"></i> Log Petugas Lapangan</a>
                <a class="nav-item" data-panel="log-admin"><i class="fas fa-user-shield"></i> Log Aktivitas Admin</a>
            </div>

            <!-- Settings Content -->
            <div class="settings-content">

                <!-- PANEL: TEMA & WARNA (FEATURED TEMA DINAMIS) -->
                <div class="settings-panel active" id="panel-tema">
                    <div class="panel-header">
                        <div>
                            <h3><i class="fas fa-palette" style="color:var(--primary);margin-right:10px;"></i>Pengaturan Tema & Warna Sistem</h3>
                            <p>Pilih tema visual yang diinginkan. Tema akan langsung diterapkan secara otomatis di seluruh halaman.</p>
                        </div>
                    </div>
                    <div class="panel-body">
                        <!-- Custom Animated Dropdown Choice -->
                        <div class="form-group" style="margin-bottom:24px;">
                            <label><i class="fas fa-sliders-h" style="color:var(--primary);margin-right:6px;"></i>Pilih Tema Utama (Dropdown)</label>
                            <div class="pupr-dropdown-wrapper" style="width:100%;">
                                <button type="button" class="btn btn-outline pupr-dropdown-toggle" data-toggle="pupr-dropdown" style="width:100%;font-size:14px;font-weight:600;padding:12px 16px;justify-content:space-between;">
                                    <span class="selected-label" id="settingThemeLabel">PUPR Official (Biru Kehitam-hitaman & Kuning PUPR)</span>
                                    <i class="fas fa-chevron-down" style="font-size:12px;margin-left:8px;"></i>
                                </button>
                                <div class="pupr-dropdown-menu" id="settingThemeMenu" style="width:100%;min-width:100%;">
                                    <div class="pupr-dropdown-item active" data-theme-val="pupr">PUPR Official (Biru Kehitam-hitaman & Kuning PUPR)</div>
                                    <div class="pupr-dropdown-item" data-theme-val="dark">PUPR Dark Mode (Slate Dark & Kuning Gold)</div>
                                    <div class="pupr-dropdown-item" data-theme-val="emerald">Emerald Green (Hijau Zamrud & Konservasi)</div>
                                    <div class="pupr-dropdown-item" data-theme-val="ocean">Ocean Blue (Biru Samudra & Cerah)</div>
                                    <div class="pupr-dropdown-item" data-theme-val="pink">Pink Rose (#d43f78 & Kuning PUPR)</div>
                                    <div class="pupr-dropdown-item" data-theme-val="crimson">Crimson Red (Merah Marun & Aksen Tegas)</div>
                                </div>
                            </div>
                            <div class="help-text" style="margin-top:8px;">Pilih salah satu tema dari daftar untuk langsung mengubah warna aplikasi secara real-time.</div>
                        </div>

                        <h4 style="font-size:15px;font-weight:700;margin-bottom:16px;color:var(--primary);">Pilihan Tema Visual (Klik Kartu / Palette Warna)</h4>

                        <!-- 6 Theme Card Grid -->
                        <div class="theme-card-grid">
                            <!-- PUPR Official -->
                            <div class="theme-option-card" data-theme-val="pupr">
                                <span class="theme-badge"><i class="fas fa-check"></i> Aktif</span>
                                <div class="theme-preview-dots">
                                    <div class="dot-main" style="background:#002855;"></div>
                                    <div class="dot-accent" style="background:#FFB800;"></div>
                                </div>
                                <div class="theme-name">PUPR Official</div>
                            </div>

                            <!-- PUPR Dark Mode -->
                            <div class="theme-option-card" data-theme-val="dark">
                                <span class="theme-badge"><i class="fas fa-check"></i> Aktif</span>
                                <div class="theme-preview-dots">
                                    <div class="dot-main" style="background:#0F172A;"></div>
                                    <div class="dot-accent" style="background:#FFB800;"></div>
                                </div>
                                <div class="theme-name">PUPR Dark Mode</div>
                            </div>

                            <!-- Emerald Green -->
                            <div class="theme-option-card" data-theme-val="emerald">
                                <span class="theme-badge"><i class="fas fa-check"></i> Aktif</span>
                                <div class="theme-preview-dots">
                                    <div class="dot-main" style="background:#064E3B;"></div>
                                    <div class="dot-accent" style="background:#10B981;"></div>
                                </div>
                                <div class="theme-name">Emerald Green</div>
                            </div>

                            <!-- Ocean Blue -->
                            <div class="theme-option-card" data-theme-val="ocean">
                                <span class="theme-badge"><i class="fas fa-check"></i> Aktif</span>
                                <div class="theme-preview-dots">
                                    <div class="dot-main" style="background:#075985;"></div>
                                    <div class="dot-accent" style="background:#38BDF8;"></div>
                                </div>
                                <div class="theme-name">Ocean Blue</div>
                            </div>

                            <!-- Pink Rose -->
                            <div class="theme-option-card" data-theme-val="pink">
                                <span class="theme-badge"><i class="fas fa-check"></i> Aktif</span>
                                <div class="theme-preview-dots">
                                    <div class="dot-main" style="background:#d43f78;"></div>
                                    <div class="dot-accent" style="background:#F472B6;"></div>
                                </div>
                                <div class="theme-name">Pink Rose</div>
                            </div>

                            <!-- Crimson Red -->
                            <div class="theme-option-card" data-theme-val="crimson">
                                <span class="theme-badge"><i class="fas fa-check"></i> Aktif</span>
                                <div class="theme-preview-dots">
                                    <div class="dot-main" style="background:#881337;"></div>
                                    <div class="dot-accent" style="background:#FB7185;"></div>
                                </div>
                                <div class="theme-name">Crimson Red</div>
                            </div>
                        </div>

                        <div class="form-actions">
                            <button type="button" class="btn btn-primary" id="saveThemeBtn"><i class="fas fa-save"></i> Simpan Pilihan Tema</button>
                            <button type="button" class="btn btn-outline" id="resetThemeBtn"><i class="fas fa-redo"></i> Reset Tema Default PUPR</button>
                        </div>
                    </div>
                </div>

                <!-- PANEL 1: LOG AKTIVITAS PETUGAS LAPANGAN -->
                <div class="settings-panel" id="panel-log-petugas">
                    <div class="panel-header">
                        <div>
                            <h3><i class="fas fa-user-check" style="color:var(--primary);margin-right:10px;"></i>Log Aktivitas Petugas Survei Lapangan</h3>
                            <p>Riwayat pengisian data survei, upload foto kegiatan, &amp; presensi GPS lokasi oleh petugas</p>
                        </div>
                    </div>
                    <div class="panel-body">
                        <div style="display:flex;flex-direction:column;gap:12px;">
                            @forelse($logPetugas as $log)
                                <div style="display:flex;align-items:center;gap:14px;padding:14px 18px;border-radius:var(--radius-sm);background:var(--bg-body);border:1px solid rgba(0,40,85,0.06);">
                                    <div style="width:42px;height:42px;border-radius:50%;background:rgba(39,174,96,0.12);color:var(--success);display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0;">
                                        <i class="fas fa-clipboard-check"></i>
                                    </div>
                                    <div style="flex:1;">
                                        <div style="font-size:14px;font-weight:700;color:var(--text-primary);">
                                            {{ $log->nama_petugas_1 ?: ($log->user->name ?? 'Petugas Survei') }}
                                            @if($log->nama_petugas_2)
                                                &amp; {{ $log->nama_petugas_2 }}
                                            @endif
                                        </div>
                                        <div style="font-size:12.5px;color:var(--text-muted);margin-top:2px;">
                                            Pengisian survei kegiatan: <strong style="color:var(--primary);">{{ $log->nama_kegiatan ?: ($log->dataMingguan->nama_kegiatan ?? 'Kegiatan Lapangan') }}</strong> (Kec. {{ ucwords(str_replace('_',' ', $log->kecamatan ?: ($log->dataMingguan->lokasi ?? 'Jember'))) }})
                                        </div>
                                    </div>
                                    <div style="font-size:12px;color:var(--text-muted);font-weight:600;white-space:nowrap;text-align:right;">
                                        <i class="fas fa-clock" style="font-size:10px;margin-right:4px;"></i>
                                        {{ $log->updated_at ? $log->updated_at->diffForHumans() : '-' }}
                                    </div>
                                </div>
                            @empty
                                <div style="text-align:center;padding:40px 20px;color:var(--text-muted);">
                                    <i class="fas fa-history" style="font-size:36px;opacity:0.3;display:block;margin-bottom:12px;"></i>
                                    <p style="font-size:14px;margin:0;">Belum ada riwayat aktivitas pengisian survei oleh petugas.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- PANEL 2: LOG AKTIVITAS & PERANGKAT LOGIN ADMIN -->
                <div class="settings-panel" id="panel-log-admin">
                    <div class="panel-header">
                        <div>
                            <h3><i class="fas fa-user-shield" style="color:var(--primary);margin-right:10px;"></i>Log Login &amp; Perangkat Pengguna / Admin</h3>
                            <p>Tabel riwayat login terakhir, alamat IP, jenis perangkat HP/PC, dan koordinat lokasi GPS petugas &amp; admin</p>
                        </div>
                    </div>
                    <div class="panel-body" style="padding:0;">
                        <div class="table-wrapper" style="overflow-x:auto;">
                            <table style="width:100%;border-collapse:collapse;font-size:13px;">
                                <thead>
                                    <tr style="background:var(--bg-body);border-bottom:1px solid rgba(0,40,85,0.08);text-align:left;">
                                        <th style="padding:14px 18px;width:50px;">No</th>
                                        <th style="padding:14px 18px;min-width:180px;">Pengguna &amp; Role</th>
                                        <th style="padding:14px 18px;min-width:160px;">Login / Akses Terakhir</th>
                                        <th style="padding:14px 18px;min-width:140px;">Alamat IP</th>
                                        <th style="padding:14px 18px;min-width:160px;">Jenis Perangkat</th>
                                        <th style="padding:14px 18px;min-width:170px;">Koordinat &amp; Lokasi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($adminLogUsers as $idx => $u)
                                        <tr style="border-bottom:1px solid rgba(0,40,85,0.04);">
                                            <td style="padding:14px 18px;">{{ $idx + 1 }}</td>
                                            <td style="padding:14px 18px;">
                                                <div style="font-weight:700;color:var(--primary-dark);">{{ $u->name }}</div>
                                                <div style="font-size:11.5px;color:var(--text-muted);">{{ $u->email }}</div>
                                                <div style="margin-top:4px;">
                                                    @if($u->role === 'admin')
                                                        <span class="badge-status" style="background:rgba(0,40,85,0.12);color:var(--primary);font-size:10px;padding:2px 8px;">
                                                            <i class="fas fa-shield-halved"></i> Admin
                                                        </span>
                                                    @else
                                                        <span class="badge-status" style="background:rgba(255,184,0,0.15);color:#d69e00;font-size:10px;padding:2px 8px;">
                                                            <i class="fas fa-user-hard-hat"></i> Petugas
                                                        </span>
                                                    @endif
                                                </div>
                                            </td>
                                            <td style="padding:14px 18px;">
                                                @if($u->last_location_at)
                                                    <div style="font-weight:600;color:var(--text-primary);">
                                                        <i class="fas fa-clock" style="font-size:11px;color:var(--primary);margin-right:4px;"></i>
                                                        {{ $u->last_location_at->format('d M Y H:i') }}
                                                    </div>
                                                    <div style="font-size:11px;color:var(--text-muted);margin-top:2px;">
                                                        ({{ $u->last_location_at->diffForHumans() }})
                                                    </div>
                                                @else
                                                    <span style="color:var(--text-muted);font-style:italic;">Belum ada log</span>
                                                @endif
                                            </td>
                                            <td style="padding:14px 18px;">
                                                @if($u->last_ip)
                                                    <span style="font-family:monospace;font-size:12px;font-weight:700;background:rgba(0,40,85,0.06);padding:3px 8px;border-radius:4px;color:var(--primary-dark);">
                                                        <i class="fas fa-network-wired" style="font-size:10px;margin-right:4px;"></i>{{ $u->last_ip }}
                                                    </span>
                                                @else
                                                    <span style="color:var(--text-muted);">-</span>
                                                @endif
                                            </td>
                                            <td style="padding:14px 18px;">
                                                <div style="font-weight:600;color:var(--text-primary);">
                                                    <i class="fas {{ str_contains(strtolower($u->device_type ?? ''), 'mobile') ? 'fa-mobile-screen-button' : 'fa-laptop' }}" style="color:var(--primary);margin-right:6px;"></i>
                                                    {{ $u->device_type ?? 'Desktop / Web' }}
                                                </div>
                                                <div style="font-size:11px;color:var(--text-muted);margin-top:2px;max-width:180px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;" title="{{ $u->user_agent }}">
                                                    {{ $u->user_agent ?? '-' }}
                                                </div>
                                            </td>
                                            <td style="padding:14px 18px;">
                                                @if($u->latitude && $u->longitude)
                                                    <div style="font-weight:600;color:var(--primary-dark);">
                                                        <i class="fas fa-location-dot" style="color:var(--danger);font-size:11px;margin-right:4px;"></i>
                                                        Kec. {{ ucwords(str_replace('_', ' ', $u->kecamatan ?? 'Jember')) }}
                                                    </div>
                                                    <div style="font-family:monospace;font-size:11px;color:var(--text-muted);margin-top:2px;">
                                                        {{ number_format((float)$u->latitude, 4) }}, {{ number_format((float)$u->longitude, 4) }}
                                                    </div>
                                                @else
                                                    <span style="color:var(--text-muted);">-</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" style="text-align:center;padding:32px;color:var(--text-muted);">
                                                <i class="fas fa-user-shield" style="font-size:32px;display:block;margin-bottom:8px;opacity:0.4;"></i>
                                                Belum ada data log aktivitas pengguna.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </main>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Tab Panel Switcher
        const navItems = document.querySelectorAll('.settings-sidebar .nav-item');
        const panels = document.querySelectorAll('.settings-panel');

        navItems.forEach(item => {
            item.addEventListener('click', function() {
                navItems.forEach(n => n.classList.remove('active'));
                panels.forEach(p => p.classList.remove('active'));

                this.classList.add('active');
                const panelId = 'panel-' + this.dataset.panel;
                const targetPanel = document.getElementById(panelId);
                if (targetPanel) targetPanel.classList.add('active');
            });
        });

        // Theme Switcher Logic (Custom Dropdown)
        const themeItems = document.querySelectorAll('#settingThemeMenu .pupr-dropdown-item');
        const cards = document.querySelectorAll('.theme-option-card');
        const currentSavedTheme = localStorage.getItem('pupr_theme') || 'pupr';

        function updateUIActiveTheme(themeName) {
            themeItems.forEach(item => {
                if (item.dataset.themeVal === themeName) {
                    item.classList.add('active');
                    const label = document.getElementById('settingThemeLabel');
                    if (label) label.textContent = item.textContent.trim();
                } else {
                    item.classList.remove('active');
                }
            });
            cards.forEach(card => {
                if (card.dataset.themeVal === themeName) {
                    card.classList.add('active');
                } else {
                    card.classList.remove('active');
                }
            });
        }

        // Init UI with saved theme
        updateUIActiveTheme(currentSavedTheme);

        // Change via Custom Dropdown Click
        themeItems.forEach(item => {
            item.addEventListener('click', function() {
                const themeVal = this.dataset.themeVal;
                if (window.setPuprTheme) window.setPuprTheme(themeVal);
                updateUIActiveTheme(themeVal);
            });
        });

        // Change via Card Click
        cards.forEach(card => {
            card.addEventListener('click', function() {
                const themeVal = this.dataset.themeVal;
                if (window.setPuprTheme) window.setPuprTheme(themeVal);
                updateUIActiveTheme(themeVal);
            });
        });

        // Save & Reset Buttons
        const saveBtn = document.getElementById('saveThemeBtn');
        if (saveBtn) {
            saveBtn.addEventListener('click', function() {
                const activeItem = document.querySelector('#settingThemeMenu .pupr-dropdown-item.active');
                const activeTheme = activeItem ? activeItem.dataset.themeVal : 'pupr';
                alert('Tema "' + activeTheme.toUpperCase() + '" berhasil disimpan sebagai tema utama Anda!');
            });
        }

        const resetBtn = document.getElementById('resetThemeBtn');
        if (resetBtn) {
            resetBtn.addEventListener('click', function() {
                if (window.setPuprTheme) window.setPuprTheme('pupr');
                updateUIActiveTheme('pupr');
                alert('Tema telah dikembalikan ke standar resmi PUPR Official!');
            });
        }
    });
</script>
@endpush
