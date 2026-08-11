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

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- PWA Manifest & Mobile Capability -->
    <link rel="manifest" href="{{ asset('manifest.json') }}" />
    <meta name="theme-color" content="#002855" />
    <meta name="mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent" />

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
    $isPublicPage = Request::is('/', 'landing', 'survey', 'survei', 'survey/*', 'survei/*');
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
                <form action="{{ url('/logout') }}" method="POST" style="flex: 1; margin: 0;" onsubmit="if(!navigator.onLine){ alert('Tidak dapat logout saat Mode Offline. Anda harus terhubung ke internet untuk mengakhiri sesi akun.'); return false; }">
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

    <!-- PWA Offline Survey Engine & IndexedDB Auto-Sync -->
    <script src="{{ asset('assets/js/offline-survey.js') }}?v={{ time() }}"></script>

    <!-- PWA Service Worker Registration & Realtime Offline Detector -->
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function() {
                navigator.serviceWorker.register('/sw.js', { updateViaCache: 'none' }).then(function(reg) {
                    console.log('[PWA] Service Worker aktif:', reg.scope);

                    // Force update SW jika ada versi baru & sedang online
                    if (navigator.onLine && typeof reg.update === 'function') {
                        reg.update().catch(function(e) {
                            console.log('[PWA] Check update SW dilewati (offline/error):', e ? e.message : e);
                        });
                    }
                }).catch(function(err) {
                    console.warn('[PWA] Service Worker gagal didaftarkan:', err);
                });
            });
        }

        // ============================================================
        // OFFLINE NAVIGATION INTERCEPTOR (Anti DNS_PROBE Desktop)
        // ============================================================
        // Saat offline, Chrome Desktop langsung gagal di DNS sebelum
        // Service Worker bisa intercept. Solusi: intercept klik link
        // secara client-side, gunakan fetch() API (yang TETAP melewati
        // Service Worker), lalu render halaman dari cache.
        // ============================================================
        
        window.navigateOffline = function(href) {
            console.log('[Offline Nav] Intercepting navigation to:', href);

            if (window.PuprLoading) {
                window.PuprLoading.show('Memuat halaman dari cache offline...');
            }

            fetch(href, {
                headers: { 'Accept': 'text/html' },
                cache: 'only-if-cached',
                mode: 'same-origin'
            })
            .then(function(response) {
                if (!response || !response.ok) {
                    return fetch(href, { headers: { 'Accept': 'text/html' } });
                }
                return response;
            })
            .then(function(response) {
                if (!response || !response.ok) {
                    throw new Error('Halaman tidak ditemukan di cache offline.');
                }
                return response.text();
            })
            .then(function(html) {
                document.open();
                document.write(html);
                document.close();

                try {
                    window.history.pushState({ offlinePage: true }, '', href);
                } catch(e) {}

                console.log('[Offline Nav] Halaman berhasil dimuat dari cache:', href);
            })
            .catch(function(err) {
                console.warn('[Offline Nav] Gagal memuat halaman:', err);
                if (window.PuprLoading) window.PuprLoading.hide();
                alert('Halaman "' + href + '" tidak tersedia secara offline.\n\nPastikan Anda sudah pernah membuka halaman ini saat online agar ter-cache oleh Service Worker.');
            });
        };

        document.addEventListener('click', function(e) {
            // Hanya aktif saat offline
            if (navigator.onLine) return;

            // Cari elemen <a> terdekat yang diklik
            var link = e.target.closest('a[href]');
            if (!link) return;

            var href = link.getAttribute('href');
            if (!href) return;

            // Abaikan link khusus
            if (href.startsWith('javascript:') || href.startsWith('#') ||
                href.startsWith('mailto:') || href.startsWith('tel:') ||
                href.startsWith('blob:') || href.startsWith('data:')) {
                return;
            }

            // Abaikan target=_blank
            if (link.target === '_blank') {
                e.preventDefault();
                alert('Tidak dapat membuka tab baru saat Mode Offline.');
                return;
            }

            // Abaikan eksternal
            try {
                var linkUrl = new URL(href, window.location.origin);
                if (linkUrl.origin !== window.location.origin) {
                    e.preventDefault();
                    alert('Link ke situs eksternal tidak tersedia saat Mode Offline.');
                    return;
                }
                href = linkUrl.href;
            } catch(err) {
                return; 
            }

            e.preventDefault();
            window.navigateOffline(href);
        }, true);

        // Handle tombol Back browser saat offline
        window.addEventListener('popstate', function(e) {
            if (!navigator.onLine && e.state && e.state.offlinePage) {
                // Coba muat halaman sebelumnya dari cache
                fetch(window.location.href, {
                    headers: { 'Accept': 'text/html' },
                    cache: 'only-if-cached',
                    mode: 'same-origin'
                })
                .then(function(r) { return r.ok ? r : fetch(window.location.href); })
                .then(function(r) { return r.text(); })
                .then(function(html) {
                    document.open();
                    document.write(html);
                    document.close();
                })
                .catch(function() {
                    // Biarkan browser handle
                });
            }
        });

        // ============================================================
        // INDIKATOR STATUS KONEKSI ONLINE / OFFLINE MENGAMBANG
        // ============================================================
        function updateOnlineStatusUI() {
            let badge = document.getElementById('offlineStatusBadge');
            if (!navigator.onLine) {
                if (!badge) {
                    badge = document.createElement('div');
                    badge.id = 'offlineStatusBadge';
                    badge.style.cssText = 'position:fixed;top:14px;left:50%;transform:translateX(-50%);z-index:99999;background:#b91c1c;color:#ffffff;padding:6px 16px;border-radius:30px;font-size:12px;font-weight:800;display:flex;align-items:center;gap:8px;box-shadow:0 6px 18px rgba(0,0,0,0.25);letter-spacing:0.3px;';
                    badge.innerHTML = '<i class="fas fa-wifi" style="font-size:11px;opacity:0.8;"></i> <span>Mode Offline (Akses Lokal)</span>';
                    document.body.appendChild(badge);
                }
            } else {
                if (badge) {
                    badge.style.background = '#15803d';
                    badge.innerHTML = '<i class="fas fa-wifi"></i> <span>Koneksi Kembali Online</span>';
                    setTimeout(function() {
                        if (badge) badge.remove();
                    }, 2500);
                }
            }
        }

        window.addEventListener('online', updateOnlineStatusUI);
        window.addEventListener('offline', updateOnlineStatusUI);
        document.addEventListener('DOMContentLoaded', updateOnlineStatusUI);
    </script>

</body>
</html>
