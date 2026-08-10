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

        // Toggle semua .pupr-dropdown-toggle (termasuk yang pakai onclick)
        // Gunakan event delegation agar tombol yang dibuat dinamis juga tercover
        document.addEventListener('click', function(e) {
            const toggle = e.target.closest('.pupr-dropdown-toggle');
            if (toggle) {
                e.stopPropagation();
                const wrapper = toggle.closest('.pupr-dropdown-wrapper');
                if (wrapper) {
                    window.PuprDropdown.toggle(wrapper);
                }
                return; // jangan proses lanjut
            }

            // Item selection handler pada .pupr-dropdown-item
            const item = e.target.closest('.pupr-dropdown-item');
            if (item) {
                // Jika item tidak punya onclick custom (pakai data-target/data-value)
                const targetId = item.getAttribute('data-target');
                const val = item.getAttribute('data-value');
                if (targetId && val !== null) {
                    const targetEl = document.getElementById(targetId);
                    if (targetEl) {
                        targetEl.value = val;
                        targetEl.dispatchEvent(new Event('change'));
                    }
                    const wrapper = item.closest('.pupr-dropdown-wrapper');
                    if (wrapper) {
                        wrapper.querySelectorAll('.pupr-dropdown-item').forEach(i => i.classList.remove('active'));
                        item.classList.add('active');
                        const label = wrapper.querySelector('.selected-label');
                        if (label) label.textContent = item.textContent.trim();
                        wrapper.classList.remove('active');
                    }
                }
                // Jika punya onclick sendiri (selectDropdown), biarkan onclick berjalan
                return;
            }

            // Klik area luar: tutup semua dropdown
            if (!e.target.closest('.user-profile-wrapper') && !e.target.closest('.pupr-dropdown-wrapper')) {
                window.PuprDropdown.closeAll();
            }
        }, true); // useCapture = true agar berjalan lebih awal

        // Tekan tombol ESC keyboard untuk menutup dropdown
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                window.PuprDropdown.closeAll();
            }
        });
    }
};

/**
 * Helper global: Pilih item di custom pupr-dropdown dan submit form
 * Dipakai via onclick di blade template
 */
window.selectDropdown = function(hiddenInputId, wrapperId, value, label, formId) {
    // Set hidden input value
    const hidden = document.getElementById(hiddenInputId);
    if (hidden) hidden.value = value;

    // Update label tombol & tandai aktif
    const wrapper = document.getElementById(wrapperId);
    if (wrapper) {
        const lbl = wrapper.querySelector('.selected-label');
        if (lbl) lbl.textContent = label;
        wrapper.querySelectorAll('.pupr-dropdown-item').forEach(i => i.classList.remove('active'));
        wrapper.classList.remove('active');
    }

    // Auto submit form
    if (formId) {
        const form = document.getElementById(formId);
        if (form) {
            setTimeout(function() { form.submit(); }, 50);
        }
    }
};

// Auto run saat DOM Siap
document.addEventListener('DOMContentLoaded', function() {
    window.PuprDropdown.init();
});
