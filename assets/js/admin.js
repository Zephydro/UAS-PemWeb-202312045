document.addEventListener('DOMContentLoaded', function() {
    const hamburgerBtn = document.querySelector('.hamburger-btn');
    const sidebar = document.querySelector('.sidebar');
    const sidebarOverlay = document.querySelector('.sidebar-overlay');

    if (hamburgerBtn && sidebar && sidebarOverlay) {
        hamburgerBtn.addEventListener('click', function() {
            hamburgerBtn.classList.toggle('active');
            sidebar.classList.toggle('active');
            sidebarOverlay.classList.toggle('active');
        });

        sidebarOverlay.addEventListener('click', function() {
            hamburgerBtn.classList.remove('active');
            sidebar.classList.remove('active');
            sidebarOverlay.classList.remove('active');
        });
    }
});