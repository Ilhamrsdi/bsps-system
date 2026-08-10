<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <!-- Instant Theme Loader (Zero Flash / FOUC Fix) -->
    <script>
        (function() {
            var theme = localStorage.getItem('pupr_theme') || 'pupr';
            document.documentElement.setAttribute('data-theme', theme);
        })();
    </script>

    <title>@yield('title', 'BSPS Verval - Sistem Informasi Verifikasi & Validasi')</title>

    <!-- Favicon Logo PUPR -->
    <link rel="icon" href="{{ asset('logo.jpg') }}" type="image/jpeg" />

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet" />
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />

    <!-- App System & Reusable Components CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/app.css') }}?v={{ time() }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/component.css') }}?v={{ time() }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/modal.css') }}?v={{ time() }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/table.css') }}?v={{ time() }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/dropdown.css') }}?v={{ time() }}" />

    <!-- Page Specific Styles -->
    @stack('styles')
    @yield('styles')
</head>
<body>

@php
    $isPublicPage = Request::is('/', 'landing', 'survey', 'survei');
@endphp

    @if(!$isPublicPage)
        <!-- Sidebar Overlay (mobile) -->
        <div class="sidebar-overlay" id="sidebarOverlay"></div>

        <!-- Sidebar Component -->
        @include('layouts.sidebar')
    @endif

    <!-- Main Wrapper -->
    <div class="main-wrapper" @if($isPublicPage) style="margin-left:0;width:100%;max-width:100%;" @endif>
        @yield('content')
        @include('layouts.footer')
    </div>

    <!-- App System & Layout JS -->
    <script src="{{ asset('assets/js/app.js') }}?v={{ time() }}"></script>
    <script src="{{ asset('assets/js/modal.js') }}?v={{ time() }}"></script>
    <script src="{{ asset('assets/js/dropdown.js') }}?v={{ time() }}"></script>

    <!-- Reusable Loading Overlay Component (resources/views/components/loading_overlay.blade.php) -->
    @include('components.loading_overlay')

    <!-- Reusable Global Logout Confirmation Modal Component -->
    <div class="modal-overlay" id="modalLogoutConfirmation">
        <div class="modal-box" style="max-width: 440px;">
            <div class="modal-header" style="background: rgba(231, 76, 60, 0.05); border-bottom-color: rgba(231, 76, 60, 0.12);">
                <h3 style="color: var(--danger); display: flex; align-items: center; gap: 10px; font-size: 16px;">
                    <i class="fas fa-sign-out-alt"></i> Konfirmasi Logout
                </h3>
                <button class="close-btn" type="button" onclick="window.PuprModal.close('modalLogoutConfirmation')">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="modal-body" style="padding: 24px; text-align: center;">
                <div style="width: 60px; height: 60px; border-radius: 50%; background: rgba(231, 76, 60, 0.1); color: var(--danger); display: inline-flex; align-items: center; justify-content: center; font-size: 24px; margin: 0 auto 16px;">
                    <i class="fas fa-arrow-right-from-bracket"></i>
                </div>
                <h4 style="font-size: 17px; font-weight: 800; color: var(--text-primary); margin-bottom: 8px;">
                    Apakah Anda yakin ingin keluar?
                </h4>
                <p style="font-size: 13px; color: var(--text-muted); line-height: 1.5; margin: 0;">
                    Sesi Anda akan diakhiri dan Anda perlu masuk kembali untuk mengakses sistem BSPS Verval.
                </p>
            </div>

            <div class="modal-footer" style="padding: 16px 20px; background: var(--bg-body); border-top: 1px solid rgba(0, 40, 85, 0.06); display: flex; gap: 10px; justify-content: flex-end;">
                <button type="button" class="btn btn-outline btn-modal-cancel" style="flex: 1; justify-content: center;" onclick="window.PuprModal.close('modalLogoutConfirmation')">
                    <i class="fas fa-xmark"></i> Batal
                </button>
                <form action="{{ url('/logout') }}" method="POST" style="flex: 1; margin: 0;">
                    @csrf
                    <button type="submit" class="btn btn-danger btn-modal-logout" style="width: 100%; justify-content: center;">
                        <i class="fas fa-sign-out-alt"></i> Ya, Keluar
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Page Specific Scripts -->
    @stack('scripts')
    @yield('scripts')

</body>
</html>
