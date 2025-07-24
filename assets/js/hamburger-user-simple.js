/**
 * Simple Hamburger Panel System for User
 */

document.addEventListener('DOMContentLoaded', function() {
    console.log('Hamburger User Script Loaded');
    
    // Only initialize if we're in user area
    if (window.location.pathname.includes('/user/')) {
        console.log('In user area, initializing hamburger panel');
        initUserHamburgerPanel();
    }
});

function initUserHamburgerPanel() {
    // Create hamburger button
    const hamburgerBtn = document.createElement('button');
    hamburgerBtn.className = 'hamburger-btn';
    hamburgerBtn.innerHTML = `
        <div class="hamburger-lines">
            <div class="hamburger-line"></div>
            <div class="hamburger-line"></div>
            <div class="hamburger-line"></div>
        </div>
    `;
    document.body.appendChild(hamburgerBtn);

    // Create overlay
    const overlay = document.createElement('div');
    overlay.className = 'hamburger-overlay';
    document.body.appendChild(overlay);

    // Create panel
    const panel = document.createElement('div');
    panel.className = 'user-panel';
    
    // Get user name
    const userName = window.userName || document.querySelector('meta[name="user-name"]')?.content || 'User';
    
    panel.innerHTML = `
        <div class="panel-header">
            <h3>
                <i class="bi bi-person-circle"></i>
                HotelEase User
            </h3>
            <div class="user-info">Halo, ${userName}</div>
        </div>
        
        <div class="user-stats">
            <h6>Statistik Anda</h6>
            <div class="stats-grid">
                <div class="stat-item">
                    <span class="stat-number">0</span>
                    <span class="stat-label">Booking</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number">0</span>
                    <span class="stat-label">Notifikasi</span>
                </div>
            </div>
        </div>
        
        <nav class="panel-nav">
            <div class="nav-section">
                <div class="nav-section-title">Beranda</div>
                <div class="nav-item">
                    <a href="dashboard.php" class="nav-link">
                        <i class="bi bi-house"></i>
                        <span>Dashboard</span>
                    </a>
                </div>
            </div>
            
            <div class="nav-section">
                <div class="nav-section-title">Hotel Services</div>
                <div class="nav-item">
                    <a href="kamar.php" class="nav-link">
                        <i class="bi bi-door-open"></i>
                        <span>Pilih Kamar</span>
                    </a>
                </div>
                <div class="nav-item">
                    <a href="fasilitas.php" class="nav-link">
                        <i class="bi bi-star-fill"></i>
                        <span>Fasilitas</span>
                    </a>
                </div>
                <div class="nav-item">
                    <a href="booking.php" class="nav-link">
                        <i class="bi bi-calendar-check"></i>
                        <span>Booking Baru</span>
                    </a>
                </div>
            </div>
            
            <div class="nav-section">
                <div class="nav-section-title">Riwayat & Transaksi</div>
                <div class="nav-item">
                    <a href="riwayat.php" class="nav-link">
                        <i class="bi bi-clock-history"></i>
                        <span>Riwayat Booking</span>
                    </a>
                </div>
                <div class="nav-item">
                    <a href="transaksi.php" class="nav-link">
                        <i class="bi bi-credit-card"></i>
                        <span>Transaksi</span>
                    </a>
                </div>
            </div>
            
            <div class="nav-section">
                <div class="nav-section-title">Akun & Lainnya</div>
                <div class="nav-item">
                    <a href="profil.php" class="nav-link">
                        <i class="bi bi-person"></i>
                        <span>Profil Saya</span>
                    </a>
                </div>
                <div class="nav-item">
                    <a href="testimoni.php" class="nav-link">
                        <i class="bi bi-chat-quote"></i>
                        <span>Testimoni</span>
                    </a>
                </div>
            </div>
        </nav>
        
        <div class="panel-footer">
            <a href="../auth/logout.php" class="logout-btn">
                <i class="bi bi-box-arrow-right"></i>
                Logout
            </a>
        </div>
    `;
    document.body.appendChild(panel);

    let isOpen = false;

    // Button click handler
    hamburgerBtn.addEventListener('click', function(e) {
        e.preventDefault();
        console.log('Hamburger button clicked');
        
        if (isOpen) {
            closePanel();
        } else {
            openPanel();
        }
    });

    // Overlay click handler
    overlay.addEventListener('click', function() {
        closePanel();
    });

    // ESC key handler
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && isOpen) {
            closePanel();
        }
    });

    function openPanel() {
        console.log('Opening panel');
        isOpen = true;
        hamburgerBtn.classList.add('active');
        overlay.classList.add('active');
        panel.classList.add('active');
        
        // Add blur to main content
        const mainContent = document.querySelector('.main-content');
        if (mainContent) {
            mainContent.classList.add('panel-open');
        }
        
        // Disable body scroll
        document.body.style.overflow = 'hidden';
        
        // Set active nav item
        setActiveNavItem();
    }

    function closePanel() {
        console.log('Closing panel');
        isOpen = false;
        hamburgerBtn.classList.remove('active');
        overlay.classList.remove('active');
        panel.classList.remove('active');
        
        // Remove blur from main content
        const mainContent = document.querySelector('.main-content');
        if (mainContent) {
            mainContent.classList.remove('panel-open');
        }
        
        // Enable body scroll
        document.body.style.overflow = '';
    }

    function setActiveNavItem() {
        const currentPath = window.location.pathname;
        const navLinks = panel.querySelectorAll('.nav-link');
        
        navLinks.forEach(link => {
            link.classList.remove('active');
            const href = link.getAttribute('href');
            if (currentPath.includes(href.replace('.php', ''))) {
                link.classList.add('active');
            }
        });
    }

    // Navigation link handlers
    panel.querySelectorAll('.nav-link').forEach(link => {
        link.addEventListener('click', function() {
            setTimeout(() => {
                closePanel();
            }, 200);
        });
    });

    console.log('Hamburger panel initialized successfully');
}
