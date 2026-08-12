<!-- Reusable PUPR Loading Overlay Component -->
<div class="pupr-loading-overlay" id="puprLoadingOverlay">
    <div class="pupr-loading-box">
        <div class="pupr-spinner-container">
            <img src="{{ asset('logo.jpg') }}" alt="PUPR Logo" class="spinner-center-logo" />
            <div class="spinner-outer-ring"></div>
        </div>
        <h4 class="pupr-loading-title" id="puprLoadingText">Memuat Data...</h4>
        <p class="pupr-loading-subtitle">Mohon tunggu sebentar, sistem sedang memproses data.</p>
    </div>
</div>

<style>
    .pupr-loading-overlay {
        position: fixed;
        inset: 0;
        background: rgba(0, 23, 55, 0.45);
        backdrop-filter: blur(6px);
        -webkit-backdrop-filter: blur(6px);
        z-index: 99999;
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.25s ease-in-out;
    }

    .pupr-loading-overlay.active {
        opacity: 1;
        pointer-events: auto;
    }

    .pupr-loading-box {
        background: var(--bg-card, #ffffff);
        color: var(--text-primary, #0A192F);
        padding: 32px 42px;
        border-radius: var(--radius, 16px);
        box-shadow: 0 16px 45px rgba(0, 40, 85, 0.25);
        border: 1px solid rgba(0, 40, 85, 0.1);
        text-align: center;
        max-width: 380px;
        width: 90%;
        transform: scale(0.92);
        transition: transform 0.25s cubic-bezier(0.34, 1.56, 0.64, 1);
    }

    .pupr-loading-overlay.active .pupr-loading-box {
        transform: scale(1);
    }

    .pupr-spinner-container {
        position: relative;
        width: 68px;
        height: 68px;
        margin: 0 auto 20px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .spinner-center-logo {
        width: 32px;
        height: 32px;
        object-fit: contain;
        position: absolute;
        z-index: 2;
    }

    .spinner-outer-ring {
        width: 68px;
        height: 68px;
        border: 3.5px solid rgba(0, 40, 85, 0.12);
        border-top-color: var(--primary, #002855);
        border-right-color: var(--secondary, #FFB800);
        border-radius: 50%;
        animation: puprSpinRing 0.85s linear infinite;
    }

    @keyframes puprSpinRing {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    .pupr-loading-title {
        font-size: 16px;
        font-weight: 800;
        color: var(--primary, #002855);
        margin-bottom: 6px;
        letter-spacing: -0.2px;
    }

    .pupr-loading-subtitle {
        font-size: 12.5px;
        color: var(--text-muted, #64748b);
        line-height: 1.4;
        margin: 0;
    }
</style>

<script>
    // Global PUPR Loading Overlay Manager
    window.PuprLoading = {
        show: function(title = 'Memuat Data...') {
            const overlay = document.getElementById('puprLoadingOverlay');
            const titleEl = document.getElementById('puprLoadingText');
            if (titleEl) titleEl.textContent = title;
            if (overlay) overlay.classList.add('active');
        },
        hide: function() {
            const overlay = document.getElementById('puprLoadingOverlay');
            if (overlay) overlay.classList.remove('active');
        }
    };

    // Auto-trigger loading overlay on form submits & pagination links
    document.addEventListener('DOMContentLoaded', function() {
        // Auto-show loading on filter forms (Kecuali form export / file download / target=_blank)
        document.querySelectorAll('form.filter-section, form[method="GET"]').forEach(form => {
            form.addEventListener('submit', function(e) {
                if (!navigator.onLine || e.defaultPrevented) {
                    return;
                }
                const action = (form.getAttribute('action') || '').toLowerCase();
                const target = (form.getAttribute('target') || '').toLowerCase();
                const noLoading = form.hasAttribute('data-no-loading') || form.getAttribute('data-no-loading') === 'true';

                if (noLoading || target === '_blank' || action.includes('export') || action.includes('download') || action.includes('cetak')) {
                    return;
                }

                window.PuprLoading.show('Memuat Hasil Pencarian...');
            });
        });

        // Auto-show loading on pagination link clicks
        document.querySelectorAll('.pagination a, .pg-link:not(.disabled):not(.active), .page-btn:not(.disabled):not(.active)').forEach(link => {
            link.addEventListener('click', function(e) {
                if (!navigator.onLine || e.defaultPrevented) return;
                const href = link.getAttribute('href');
                if (!href || href === '#' || href.startsWith('javascript:')) return;
                window.PuprLoading.show('Membuka Halaman...');
            });
        });
    });
</script>
