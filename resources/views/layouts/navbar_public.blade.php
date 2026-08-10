<!-- Dedicated Public Navbar Component (PUPR Jember - White Background Theme) -->
<header class="public-navbar">
    <div class="public-navbar-container">
        <!-- Brand Logo & Title -->
        <a href="{{ url('/') }}" class="public-brand">
            <img src="{{ asset('logo.jpg') }}" alt="PUPR Logo" class="brand-logo-img" />
            <div class="brand-text">
                <h1>PUPR Jember</h1>
                <span>DINAS PEKERJAAN UMUM DAN PENATAAN RUANG</span>
            </div>
        </a>

        <!-- Navigation Links (Beranda) -->
        <nav class="public-nav-links">
            <a href="{{ url('/') }}" class="public-nav-item {{ Request::is('/', 'landing') ? 'active' : '' }}">
                <i class="fas fa-home"></i> Beranda
            </a>
        </nav>

        <!-- Right Action Button -->
        <div class="public-nav-right">
            @auth
                <a href="{{ url('/dashboard') }}" class="btn btn-primary public-auth-btn">
                    <i class="fas fa-th-large"></i> Dashboard
                </a>
            @else
                <a href="{{ url('/login') }}" class="btn btn-primary public-auth-btn">
                    <i class="fas fa-sign-in-alt"></i> Login
                </a>
            @endauth
        </div>
    </div>
</header>

<style>
    .public-navbar {
        background: var(--bg-card, #ffffff);
        color: var(--text-primary, #0A192F);
        padding: 14px 0;
        position: sticky;
        top: 0;
        z-index: 1000;
        box-shadow: 0 2px 12px rgba(0, 40, 85, 0.06);
        border-bottom: 1px solid rgba(0, 40, 85, 0.08);
    }

    .public-navbar-container {
        max-width: 1280px;
        margin: 0 auto;
        padding: 0 32px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
    }

    .public-brand {
        display: flex;
        align-items: center;
        gap: 12px;
        text-decoration: none;
    }

    .public-brand .brand-logo-img {
        width: 42px;
        height: 42px;
        object-fit: contain;
    }

    .public-brand .brand-text h1 {
        font-size: 18px;
        font-weight: 900;
        color: var(--primary, #002855);
        line-height: 1.1;
        letter-spacing: -0.3px;
    }

    .public-brand .brand-text span {
        font-size: 10px;
        color: #d69e00;
        font-weight: 800;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        display: block;
        margin-top: 1px;
    }

    .public-nav-links {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .public-nav-item {
        color: var(--text-secondary, #334155);
        text-decoration: none;
        padding: 8px 16px;
        border-radius: var(--radius-sm, 8px);
        font-size: 13.5px;
        font-weight: 600;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .public-nav-item:hover {
        color: var(--primary, #002855);
        background: rgba(0, 40, 85, 0.05);
    }

    .public-nav-item.active {
        color: var(--primary, #002855);
        background: rgba(0, 40, 85, 0.08);
        font-weight: 700;
    }

    .public-auth-btn {
        padding: 9px 20px;
        border-radius: var(--radius-sm, 8px);
        font-size: 13px;
        font-weight: 700;
        background: var(--primary, #002855) !important;
        color: #ffffff !important;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s ease;
        box-shadow: 0 4px 12px rgba(0, 40, 85, 0.2);
        border: none;
    }

    @media (max-width: 768px) {
        .public-navbar-container {
            padding: 0 14px !important;
            gap: 8px;
        }
        .public-brand .brand-logo-img {
            width: 32px;
            height: 32px;
        }
        .public-brand .brand-text h1 {
            font-size: 14px;
        }
        .public-brand .brand-text span {
            display: none;
        }
        .public-nav-links {
            display: flex !important;
            gap: 4px;
        }
        .public-nav-item {
            padding: 6px 10px;
            font-size: 12px;
        }
        .public-auth-btn {
            padding: 6px 12px;
            font-size: 12px;
        }
    }

    @media (max-width: 480px) {
        .public-navbar-container {
            padding: 0 12px !important;
        }
        .public-nav-item {
            font-size: 11px;
            padding: 5px 8px;
        }
        .public-auth-btn {
            font-size: 11px;
            padding: 5px 10px;
        }
    }
</style>
