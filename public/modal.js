/**
 * System Universal Modal Manager JS (Dinas PUPR Kabupaten Jember)
 */

window.PuprModal = {
    // Buka Modal berdasarkan ID
    open: function(modalId) {
        const modal = typeof modalId === 'string' ? document.getElementById(modalId) : modalId;
        if (modal) {
            modal.classList.add('active');
            document.body.style.overflow = 'hidden';
        }
    },

    // Tutup Modal berdasarkan ID
    close: function(modalId) {
        const modal = typeof modalId === 'string' ? document.getElementById(modalId) : modalId;
        if (modal) {
            modal.classList.remove('active');
            document.body.style.overflow = '';
            
            // Reset form di dalam modal jika ada
            const form = modal.querySelector('form');
            if (form) {
                form.reset();
                form.querySelectorAll('input, select, textarea').forEach(input => {
                    input.style.borderColor = '';
                });
            }
        }
    },

    // Inisialisasi event listener global modal
    init: function() {
        // Klik backdrop modal overlay untuk menutup modal
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('modal-overlay')) {
                window.PuprModal.close(e.target);
            }
        });

        // Tekan tombol ESC keyboard untuk menutup modal aktif
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                const activeModal = document.querySelector('.modal-overlay.active');
                if (activeModal) {
                    window.PuprModal.close(activeModal);
                }
            }
        });
    }
};

// Auto run saat DOM Siap
document.addEventListener('DOMContentLoaded', function() {
    window.PuprModal.init();
});
