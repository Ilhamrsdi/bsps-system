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

    if (sidebar && overlay && hamburger) {
        function toggleSidebar() {
            sidebar.classList.toggle('open');
            overlay.classList.toggle('active');
            document.body.style.overflow = sidebar.classList.contains('open') ? 'hidden' : '';
        }

        hamburger.addEventListener('click', toggleSidebar);
        overlay.addEventListener('click', toggleSidebar);

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && sidebar.classList.contains('open')) {
                toggleSidebar();
            }
        });

        let windowWidth = window.innerWidth;
        window.addEventListener('resize', function() {
            const newWidth = window.innerWidth;
            if (newWidth > 768 && newWidth !== windowWidth) {
                sidebar.classList.remove('open');
                overlay.classList.remove('active');
                document.body.style.overflow = '';
            }
            windowWidth = newWidth;
        });
    }

    // Navbar User Profile Dropdown Toggle
    const userProfileDropdown = document.getElementById('userProfileDropdown');
    const userProfileToggle = document.getElementById('userProfileToggle');

    if (userProfileDropdown && userProfileToggle) {
        userProfileToggle.addEventListener('click', function(e) {
            e.stopPropagation();
            userProfileDropdown.classList.toggle('open');
        });

        document.addEventListener('click', function(e) {
            if (!userProfileDropdown.contains(e.target)) {
                userProfileDropdown.classList.remove('open');
            }
        });
    }
});
