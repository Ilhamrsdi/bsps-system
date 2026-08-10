<header class="navbar">
    <div class="navbar-left">
        <button class="hamburger" id="hamburgerBtn" aria-label="Toggle sidebar">
            <i class="fas fa-bars"></i>
        </button>
        <div class="page-title">
            <h2>@yield('title_header', 'Dashboard BSPS Verval')</h2>
        </div>
    </div>

    <div class="navbar-right">
        <form action="{{ url('/data-verval') }}" method="GET" class="search-box" style="margin:0;">
            <button type="submit" style="background:none;border:none;padding:0;cursor:pointer;color:inherit;display:flex;align-items:center;">
                <i class="fas fa-search"></i>
            </button>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari NIK, Nama KK, Desa..." />
        </form>

        @php
            $recentNavSurveys = \App\Services\DummyVervalService::getRecentActivities();
        @endphp

        <!-- Dropdown Notifikasi Aktivitas Survei Petugas -->
        <div class="user-profile-wrapper" id="notifDropdownWrapper" style="position:relative;">
            <button class="nav-btn" id="notifBtnToggle" title="Notifikasi TFL Lapangan" type="button" style="position:relative;">
                <i class="fas fa-bell"></i>
                <span style="position:absolute;top:-4px;right:-4px;background:var(--danger);color:#fff;font-size:10px;font-weight:700;padding:2px 6px;border-radius:20px;min-width:18px;height:18px;display:flex;align-items:center;justify-content:center;box-shadow:0 2px 6px rgba(0,0,0,0.25);border:1.5px solid #fff;line-height:1;">
                    {{ count($recentNavSurveys) }}
                </span>
            </button>
            <div class="user-dropdown-menu" id="notifMenuBox" style="width:320px;right:0;left:auto;padding:0;overflow:hidden;">
                <div class="user-dropdown-header" style="background:var(--primary);color:#fff;padding:14px 18px;text-align:center;">
                    <div style="font-weight:700;font-size:14px;"><i class="fas fa-bell" style="margin-right:6px;"></i> Aktivitas TFL Lapangan</div>
                </div>
                <div style="max-height:280px;overflow-y:auto;background:var(--bg-card);">
                    @forelse($recentNavSurveys as $n)
                        <a href="{{ url('/data-verval') }}" style="display:flex;align-items:flex-start;gap:12px;padding:12px 16px;border-bottom:1px solid rgba(0,40,85,0.06);text-decoration:none;color:inherit;transition:var(--transition);">
                            <div style="width:34px;height:34px;border-radius:50%;background:rgba(39,174,96,0.12);color:var(--success);display:flex;align-items:center;justify-content:center;font-size:14px;flex-shrink:0;margin-top:2px;">
                                <i class="fas fa-clipboard-check"></i>
                            </div>
                            <div style="flex:1;min-width:0;">
                                <div style="font-size:12.5px;font-weight:700;color:var(--primary-dark);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                                    {{ $n['petugas'] }}
                                </div>
                                <div style="font-size:11.5px;color:var(--text-secondary);margin-top:2px;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">
                                    {{ $n['aktivitas'] }}
                                </div>
                                <div style="font-size:10px;color:var(--text-muted);margin-top:4px;">
                                    <i class="fas fa-clock"></i> {{ $n['waktu'] }}
                                </div>
                            </div>
                        </a>
                    @empty
                        <div style="padding:24px;text-align:center;color:var(--text-muted);font-size:12px;">
                            <i class="fas fa-bell-slash" style="font-size:24px;display:block;margin-bottom:6px;opacity:0.4;"></i>
                            Belum ada notifikasi aktivitas baru.
                        </div>
                    @endforelse
                </div>
                <a href="{{ url('/data-verval') }}" style="display:block;text-align:center;padding:10px;font-size:12px;font-weight:700;color:var(--primary);background:var(--bg-body);text-decoration:none;border-top:1px solid rgba(0,40,85,0.06);">
                    Lihat Semua Data Verval <i class="fas fa-arrow-right" style="font-size:10px;margin-left:4px;"></i>
                </a>
            </div>
        </div>

        @php
            $userName = Auth::check() ? Auth::user()->name : 'Admin BSPS';
            $userEmail = Auth::check() ? Auth::user()->email : 'admin@bsps.jember.go.id';
            $userAvatar = Auth::check() ? strtoupper(substr(Auth::user()->name, 0, 2)) : 'BS';
        @endphp

        <!-- Dropdown User Profile -->
        <div class="user-profile-wrapper" id="userProfileDropdown">
            <div class="user-profile" id="userProfileToggle">
                <div class="avatar">{{ $userAvatar }}</div>
                <div class="user-info">
                    <span class="name">{{ explode(' ', $userName)[0] }}</span>
                </div>
            </div>
            <div class="user-dropdown-menu" id="userDropdownMenu">
                <div class="user-dropdown-header">
                    <strong>{{ $userName }}</strong>
                    <span>{{ $userEmail }}</span>
                </div>
                <div class="dropdown-divider"></div>
                @auth
                    <a href="{{ url('/setting') }}" class="user-dropdown-item">
                        <i class="fas fa-cog"></i> Pengaturan
                    </a>
                    <div class="dropdown-divider"></div>
                    <a href="javascript:void(0)" class="user-dropdown-item logout" onclick="window.PuprModal.open('modalLogoutConfirmation')">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                @else
                    <a href="{{ url('/login') }}" class="user-dropdown-item">
                        <i class="fas fa-sign-in-alt"></i> Login
                    </a>
                @endauth
            </div>
        </div>
    </div>
</header>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const notifBtn = document.getElementById('notifBtnToggle');
    const notifWrapper = document.getElementById('notifDropdownWrapper');
    if (notifBtn && notifWrapper) {
        notifBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            document.querySelectorAll('.user-profile-wrapper').forEach(w => {
                if (w !== notifWrapper) w.classList.remove('active');
            });
            notifWrapper.classList.toggle('active');
        });
    }
});
</script>
