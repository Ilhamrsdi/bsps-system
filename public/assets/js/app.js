// Global Theme Switcher Function
window.setPuprTheme = function(themeName) {
    const validThemes = ['pupr', 'dark', 'emerald', 'ocean', 'pink', 'crimson'];
    if (!validThemes.includes(themeName)) themeName = 'pupr';
    document.documentElement.setAttribute('data-theme', themeName);
    localStorage.setItem('pupr_theme', themeName);
};

// Immediate execution to prevent flicker on load
(function() {
    const currentTheme = localStorage.getItem('pupr_theme') || 'pupr';
    document.documentElement.setAttribute('data-theme', currentTheme);
})();

document.addEventListener('DOMContentLoaded', function() {
    // Apply theme on load
    const currentTheme = localStorage.getItem('pupr_theme') || 'pupr';
    document.documentElement.setAttribute('data-theme', currentTheme);

    // Sidebar Toggle (Mobile & Desktop)
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    const hamburger = document.getElementById('hamburgerBtn');
    const closeBtn = document.getElementById('sidebarCloseBtn');

    function openSidebar() {
        if (sidebar) sidebar.classList.add('open');
        if (overlay) overlay.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeSidebar() {
        if (sidebar) sidebar.classList.remove('open');
        if (overlay) overlay.classList.remove('active');
        document.body.style.overflow = '';
    }

    function toggleSidebar() {
        if (sidebar && sidebar.classList.contains('open')) {
            closeSidebar();
        } else {
            openSidebar();
        }
    }

    if (hamburger) {
        hamburger.addEventListener('click', function(e) {
            e.stopPropagation();
            toggleSidebar();
        });
    }

    if (closeBtn) {
        closeBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            closeSidebar();
        });
    }

    if (overlay) {
        overlay.addEventListener('click', closeSidebar);
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && sidebar && sidebar.classList.contains('open')) {
            closeSidebar();
        }
    });

    // Auto-close sidebar on mobile when a link is clicked
    if (sidebar) {
        sidebar.querySelectorAll('.menu-item').forEach(link => {
            link.addEventListener('click', function() {
                if (window.innerWidth <= 1024) {
                    closeSidebar();
                }
            });
        });
    }

    // Auto-close sidebar on window resize to desktop
    let windowWidth = window.innerWidth;
    window.addEventListener('resize', function() {
        const newWidth = window.innerWidth;
        if (newWidth > 1024 && newWidth !== windowWidth) {
            closeSidebar();
        }
        windowWidth = newWidth;
    });

    // Navbar User Profile Dropdown Toggle
    const userProfileDropdown = document.getElementById('userProfileDropdown');
    const userProfileToggle = document.getElementById('userProfileToggle');

    if (userProfileDropdown && userProfileToggle) {
        userProfileToggle.addEventListener('click', function(e) {
            e.stopPropagation();
            const notifDropdown = document.getElementById('notifDropdownWrapper');
            if (notifDropdown) notifDropdown.classList.remove('active', 'open');
            userProfileDropdown.classList.toggle('open');
        });
    }

    // Close any dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (userProfileDropdown && !userProfileDropdown.contains(e.target)) {
            userProfileDropdown.classList.remove('open', 'active');
        }
        const notifDropdown = document.getElementById('notifDropdownWrapper');
        if (notifDropdown && !notifDropdown.contains(e.target)) {
            notifDropdown.classList.remove('active', 'open');
        }
    });
});
