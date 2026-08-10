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
        <div class="menu-label">Navigasi Utama</div>

        <a class="menu-item {{ Request::is('dashboard*') ? 'active' : '' }}" href="{{ url('/dashboard') }}">
            <i class="fas fa-th-large"></i>
            Dashboard
        </a>
        <a class="menu-item {{ Request::is('data-verval*', 'data_verval*', 'verval*') ? 'active' : '' }}" href="{{ url('/data-verval') }}">
            <i class="fas fa-clipboard-check"></i>
            Data Verval
        </a>

        <div class="menu-label" style="margin-top:24px;">Akun & Sesi</div>
        <a class="menu-item" href="javascript:void(0)" onclick="window.PuprModal.open('modalLogoutConfirmation')">
            <i class="fas fa-sign-out-alt"></i>
            Logout
        </a>
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
