<aside class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <div class="brand-icon">
            <img src="{{ asset('logo.jpg') }}" alt="BSPS Logo" class="brand-logo-img" />
        </div>
        <div class="brand-text">
            <h1>BSPS Verval</h1>
            <span>SISTEM VERIFIKASI &amp; VALIDASI<br>PERUMAHAN SWADAYA</span>
        </div>
        <button type="button" class="sidebar-close-btn" id="sidebarCloseBtn" aria-label="Tutup Sidebar">
            <i class="fas fa-times"></i>
        </button>
    </div>

    <nav class="sidebar-menu">
        @if(auth()->check() && auth()->user()->isPetugas())
            {{-- NAVIGATION KHUSUS PETUGAS SURVEI --}}
            <div class="menu-label">Navigasi Utama</div>
            <a class="menu-item {{ Request::is('petugas/dashboard') ? 'active' : '' }}" href="{{ url('/petugas/dashboard') }}">
                <i class="fas fa-th-large"></i>
                Dashboard Petugas
            </a>
            @php
                $petugasId        = auth()->user()->id;
                $petugasDesa      = auth()->user()->desa;
                $petugasKecamatan = auth()->user()->kecamatan;
                $penerimaQuery = \App\Models\DataPenerima::where(function($q) use ($petugasId, $petugasDesa, $petugasKecamatan) {
                    $q->where('user_id', $petugasId);
                    if ($petugasDesa) {
                        $q->orWhere(function ($sub) use ($petugasDesa, $petugasKecamatan) {
                            $sub->where('desa_kelurahan', $petugasDesa);
                            if ($petugasKecamatan) {
                                $sub->where('kecamatan', $petugasKecamatan);
                            }
                        });
                    }
                });
                $countSudah = (clone $penerimaQuery)->whereNotNull('foto_sudut_depan')->count();
                $countBelum = (clone $penerimaQuery)->whereNull('foto_sudut_depan')->count();
            @endphp
            <div class="menu-label" style="margin-top:16px;">Workspace Petugas</div>
            <a class="menu-item {{ Request::is('petugas/belum-survei*') ? 'active' : '' }}" href="{{ url('/petugas/belum-survei') }}">
                <i class="fas fa-clipboard-question"></i>
                Belum Survei
                <span class="badge warning" style="margin-left:auto;font-weight:800;border-radius:12px;padding:3px 9px;font-size:11.5px;background:#ffb800;color:#002855;">{{ $countBelum }}</span>
            </a>
            <a class="menu-item {{ Request::is('petugas/sudah-survei*') ? 'active' : '' }}" href="{{ url('/petugas/sudah-survei') }}">
                <i class="fas fa-clipboard-check"></i>
                Sudah Survei
                <span class="badge success" style="margin-left:auto;font-weight:800;border-radius:12px;padding:3px 9px;font-size:11.5px;background:#27ae60;color:#fff;">{{ $countSudah }}</span>
            </a>

            <div class="menu-label" style="margin-top:16px;">Akun</div>
            <a class="menu-item" style="color:var(--danger,#e74c3c);" href="javascript:void(0)" onclick="window.PuprModal.open('modalLogoutConfirmation')">
                <i class="fas fa-sign-out-alt"></i>
                Logout
            </a>
        @else
            {{-- NAVIGATION ADMINISTRATOR --}}
            <div class="menu-label">Navigasi Utama</div>

            @if(auth()->check() && auth()->user()->isAdminKecamatan())
                <a class="menu-item {{ Request::is('dashboard-kecamatan*', 'dashboard*') ? 'active' : '' }}" href="{{ url('/dashboard-kecamatan') }}">
                    <i class="fas fa-chart-pie"></i>
                    Monitoring Desa
                </a>
            @else
                <a class="menu-item {{ Request::is('dashboard') ? 'active' : '' }}" href="{{ url('/dashboard') }}">
                    <i class="fas fa-th-large"></i>
                    Dashboard Global
                </a>
                <a class="menu-item {{ Request::is('dashboard-kecamatan*') ? 'active' : '' }}" href="{{ url('/dashboard-kecamatan') }}">
                    <i class="fas fa-chart-pie"></i>
                    Monitoring Kecamatan
                </a>
            @endif

            <a class="menu-item {{ Request::is('verval-data*', 'data-verval*') ? 'active' : '' }}" href="{{ url('/verval-data') }}">
                <i class="fas fa-clipboard-list"></i>
                Data Verval
            </a>
            @if(!auth()->check() || auth()->user()->isAdmin())
            <a class="menu-item {{ Request::is('pencocokan-data*') ? 'active' : '' }}" href="{{ url('/pencocokan-data') }}">
                <i class="fas fa-id-card-clip"></i>
                Pencocokan Dataguse
            </a>
            @endif
            <a class="menu-item {{ Request::is('geomaps*', 'geoMaps*') ? 'active' : '' }}" href="{{ url('/geomaps') }}">
                <i class="fas fa-map-marked-alt"></i>
                Geo Maps
            </a>

            <div class="menu-label" style="margin-top:16px;">Manajemen</div>

            @if(!auth()->check() || auth()->user()->isAdmin())
            <a class="menu-item {{ Request::is('penugasan*') ? 'active' : '' }}" href="{{ url('/penugasan') }}">
                <i class="fas fa-tasks"></i>
                Penugasan
            </a>
            <a class="menu-item {{ Request::is('user') ? 'active' : '' }}" href="{{ url('/user') }}">
                <i class="fas fa-users"></i>
                Petugas Survei
            </a>
            @endif

            <a class="menu-item {{ Request::is('setting*') ? 'active' : '' }}" href="{{ url('/setting') }}">
                <i class="fas fa-cog"></i>
                Pengaturan
            </a>
        @endif

        @if(!auth()->check() || !auth()->user()->isPetugas())
        <div class="menu-label" style="margin-top:16px;">Publik</div>

        <a class="menu-item {{ Request::is('/', 'landing') ? 'active' : '' }}" href="{{ url('/') }}" target="_blank">
            <i class="fas fa-home"></i>
            Beranda Publik
        </a>
        <a class="menu-item {{ Request::is('survey*', 'survei*') ? 'active' : '' }}" href="{{ url('/survey') }}">
            <i class="fas fa-clipboard-check"></i>
            Form Survey
        </a>

        <div class="menu-label" style="margin-top:16px;">Akun</div>
        <a class="menu-item" style="color:var(--danger,#e74c3c);" href="javascript:void(0)" onclick="window.PuprModal.open('modalLogoutConfirmation')">
            <i class="fas fa-sign-out-alt"></i>
            Logout
        </a>
        @endif
    </nav>

    <div class="sidebar-footer">
        @if(auth()->check())
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:8px;text-align:left;">
                <div style="width:34px;height:34px;border-radius:50%;background:rgba(255,255,255,0.18);color:#ffffff;display:flex;align-items:center;justify-content:center;font-weight:800;font-size:14px;flex-shrink:0;border:1px solid rgba(255,255,255,0.25);">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <div style="overflow:hidden;">
                    <div style="font-size:13px;font-weight:700;color:#ffffff;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ auth()->user()->name }}</div>
                    <div style="display:flex;align-items:center;gap:4px;margin-top:2px;">
                        @if(auth()->user()->isAdmin())
                            <span style="font-size:10px;font-weight:700;padding:2px 8px;border-radius:10px;background:rgba(255,255,255,0.15);color:#ffffff;">
                                <i class="fas fa-shield-halved"></i> Admin Kab.
                            </span>
                        @elseif(auth()->user()->isAdminKecamatan())
                            <span style="font-size:10px;font-weight:700;padding:2px 8px;border-radius:10px;background:rgba(255,184,0,0.25);color:#ffb800;">
                                <i class="fas fa-building-flag"></i> Admin Kec.
                            </span>
                        @else
                            <span style="font-size:10px;font-weight:700;padding:2px 8px;border-radius:10px;background:rgba(255,184,0,0.2);color:#ffb800;">
                                <i class="fas fa-user-hard-hat"></i> Petugas
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        @endif
        <div style="font-size:11px;color:rgba(255,255,255,0.5);margin-top:4px;">BSPS Verval &bull; {{ date('Y') }}</div>
    </div>
</aside>
