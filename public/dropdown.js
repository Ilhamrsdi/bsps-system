/**
 * System Universal Dropdown Manager JS (Dinas PUPR Kabupaten Jember)
 */

window.PuprDropdown = {
    // Toggle active state pada wrapper dropdown
    toggle: function(wrapperElement) {
        if (!wrapperElement) return;
        
        // Tutup dropdown aktif lainnya
        document.querySelectorAll('.user-profile-wrapper.active, .pupr-dropdown-wrapper.active').forEach(item => {
            if (item !== wrapperElement) {
                item.classList.remove('active');
            }
        });

        wrapperElement.classList.toggle('active');
    },

    // Tutup seluruh dropdown aktif
    closeAll: function() {
        document.querySelectorAll('.user-profile-wrapper.active, .pupr-dropdown-wrapper.active').forEach(item => {
            item.classList.remove('active');
        });
    },

    // Inisialisasi event listener global dropdown
    init: function() {
        // Toggle User Profile Dropdown di Navbar
        const profileToggle = document.getElementById('userProfileToggle');
        const profileWrapper = document.getElementById('userProfileDropdown');
        if (profileToggle && profileWrapper) {
            profileToggle.addEventListener('click', function(e) {
                e.stopPropagation();
                window.PuprDropdown.toggle(profileWrapper);
            });
        }

        // Toggle Custom Dropdown Triggers
        document.querySelectorAll('[data-toggle="pupr-dropdown"]').forEach(trigger => {
            trigger.addEventListener('click', function(e) {
                e.stopPropagation();
                const wrapper = this.closest('.pupr-dropdown-wrapper');
                if (wrapper) {
                    window.PuprDropdown.toggle(wrapper);
                }
            });
        });

        // Item selection handler pada .pupr-dropdown-item
        document.addEventListener('click', function(e) {
            const item = e.target.closest('.pupr-dropdown-item');
            if (item) {
                const wrapper = item.closest('.pupr-dropdown-wrapper');
                if (wrapper) {
                    wrapper.querySelectorAll('.pupr-dropdown-item').forEach(i => i.classList.remove('active'));
                    item.classList.add('active');

                    const label = wrapper.querySelector('.selected-label');
                    if (label) {
                        label.textContent = item.textContent.trim();
                    }

                    // Sync nilai ke input hidden jika ada di dalam wrapper/parent
                    const hiddenInput = wrapper.querySelector('input[type="hidden"]') || wrapper.parentElement.querySelector('input[type="hidden"]');
                    if (hiddenInput) {
                        hiddenInput.value = item.dataset.value || item.textContent.trim();
                    }

                    wrapper.classList.remove('active');
                }
            }
        });

        // Klik area luar dokumen untuk menutup dropdown
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.user-profile-wrapper') && !e.target.closest('.pupr-dropdown-wrapper')) {
                window.PuprDropdown.closeAll();
            }
        });

        // Tekan tombol ESC keyboard untuk menutup dropdown
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                window.PuprDropdown.closeAll();
            }
        });
    }
};

// Auto run saat DOM Siap
document.addEventListener('DOMContentLoaded', function() {
    window.PuprDropdown.init();
});
