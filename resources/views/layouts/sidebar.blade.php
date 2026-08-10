<aside class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <div class="brand-icon">
            <img src="{{ asset('logo.jpg') }}" alt="BSPS Logo" class="brand-logo-img" />
        </div>
        <div class="brand-text">
            <h1>BSPS Verval</h1>
            <span>SISTEM VERIFIKASI &amp; VALIDASI<br>PERUMAHAN SWADAYA</span>
        </div>
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
                $userAssigned = auth()->user()->kegiatans()->get();
                $countBelum = $userAssigned->filter(fn($k) => $k->surveys->count() == 0)->count();
                $countSudah = $userAssigned->filter(fn($k) => $k->surveys->count() > 0)->count();
            @endphp
            <div class="menu-label" style="margin-top:16px;">Workspace Petugas</div>
            <a class="menu-item {{ Request::is('petugas/belum-survei') ? 'active' : '' }}" href="{{ url('/petugas/belum-survei') }}">
                <i class="fas fa-clipboard-question"></i>
                Belum Survei
                @if($countBelum > 0)
                    <span class="badge warning">{{ $countBelum }}</span>
                @endif
            </a>
            <a class="menu-item {{ Request::is('petugas/sudah-survei') ? 'active' : '' }}" href="{{ url('/petugas/sudah-survei') }}">
                <i class="fas fa-clipboard-check"></i>
                Sudah Survei
                @if($countSudah > 0)
                    <span class="badge success">{{ $countSudah }}</span>
                @endif
            </a>
        @else
            {{-- NAVIGATION ADMINISTRATOR --}}
            <div class="menu-label">Navigasi Utama</div>

            <a class="menu-item {{ Request::is('dashboard') ? 'active' : '' }}" href="{{ url('/dashboard') }}">
                <i class="fas fa-th-large"></i>
                Dashboard
            </a>
            <a class="menu-item {{ Request::is('verval-data*', 'data-verval*') ? 'active' : '' }}" href="{{ url('/verval-data') }}">
                <i class="fas fa-clipboard-list"></i>
                Data Verval
            </a>
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

            <a class="menu-item {{ Request::is('laporan*') ? 'active' : '' }}" href="{{ url('/laporan') }}">
                <i class="fas fa-clipboard-list"></i>
                Laporan
            </a>
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

        <a class="menu-item" style="margin-top:10px;" href="javascript:void(0)" onclick="window.PuprModal.open('modalLogoutConfirmation')">
            <i class="fas fa-sign-out-alt"></i>
            Logout
        </a>
        @endif
    </nav>

    <div class="sidebar-footer">
        @if(auth()->check())
            <div style="display:flex;align-items:center;gap:8px;margin-bottom:6px;">
                <div style="width:32px;height:32px;border-radius:50%;background:var(--primary);color:#fff;display:flex;align-items:center;justify-content:center;font-weight:800;font-size:13px;flex-shrink:0;">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <div style="overflow:hidden;">
                    <div style="font-size:12px;font-weight:700;color:var(--text-primary);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ auth()->user()->name }}</div>
                    <div style="display:flex;align-items:center;gap:4px;margin-top:2px;">
                        @if(auth()->user()->isAdmin())
                            <span style="font-size:10px;font-weight:700;padding:2px 8px;border-radius:10px;background:rgba(0,40,85,0.12);color:var(--primary);">
                                <i class="fas fa-shield-halved"></i> Admin
                            </span>
                        @else
                            <span style="font-size:10px;font-weight:700;padding:2px 8px;border-radius:10px;background:rgba(255,184,0,0.15);color:#d69e00;">
                                <i class="fas fa-user-hard-hat"></i> Petugas
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        @endif
        BSPS Verval &bull; {{ date('Y') }}
    </div>
</aside>
