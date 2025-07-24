/**
 * HotelEase Universal Hamburger Panel System
 * Optimized and consistent hamburger panel for all pages
 * Supports both admin and user interfaces with smooth animations
 * Version: 1.1.0 (Optimized)
 */

// Check if class is already defined to prevent duplicate declaration
if (typeof window.UniversalHamburgerPanel === 'undefined') {
class UniversalHamburgerPanel {
    constructor() {
        this.isAdmin = this.detectPageType();
        this.initialized = false;
        this.darkMode = this.detectDarkMode();
        
        // Restore panel state from localStorage if available
        const savedState = localStorage.getItem('hotelease-panel-state');
        this.isOpen = savedState === 'open';
        
        this.init();
    }
    
    detectDarkMode() {
        // Check for system preference
        const prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
        
        // Check for stored preference
        const storedPreference = localStorage.getItem('hotelease-theme');
        
        // Return stored preference if available, otherwise system preference
        return storedPreference ? storedPreference === 'dark' : prefersDark;
    }

    detectPageType() {
        return window.location.pathname.includes('/admin/') || 
               document.body.classList.contains('admin-page') ||
               document.querySelector('meta[name="page-type"]')?.content === 'admin';
    }
    
    cleanupExistingPanels() {
        // Remove existing hamburger elements
        const elementsToRemove = [
            '.hamburger-btn', 
            '.hamburger-menu', 
            '.hamburger-overlay', 
            '.sidebar-overlay',
            '.hamburger-panel', 
            '.admin-panel', 
            '.user-panel'
        ];
        
        elementsToRemove.forEach(selector => {
            const elements = document.querySelectorAll(selector);
            elements.forEach(el => {
                if (el && el.parentNode) {
                    el.parentNode.removeChild(el);
                }
            });
        });
        
        // Reset any body styles that might have been set
        document.body.style.overflow = '';
    }

    init() {
        try {
            // Prevent multiple initializations
            if (this.initialized) return;
            
            // Check if another hamburger panel already exists
            if (document.querySelector('.hamburger-btn, .hamburger-menu, .admin-panel, .user-panel')) {
                this.cleanupExistingPanels();
            }
            
            this.createElements();
            
            // Only proceed if elements were created successfully
            if (this.hamburgerBtn && this.overlay && this.panel) {
                this.bindEvents();
                this.setupKeyboardShortcuts();
                this.setupTouchGestures();
                this.applyTheme();
                this.adjustPanelPosition();
                
                // Apply saved panel state if it was open
                if (this.isOpen) {
                    // Use a small delay to ensure smooth animation
                    setTimeout(() => {
                        if (this.hamburgerBtn && this.overlay && this.panel) {
                            this.hamburgerBtn.classList.add('active');
                            this.overlay.classList.add('active');
                            this.panel.classList.add('active');
                            document.body.style.overflow = 'hidden';
                        }
                    }, 100);
                }
                
                this.initialized = true;
                
                // Listen for system theme changes
                if (window.matchMedia) {
                    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', e => {
                        if (!localStorage.getItem('hotelease-theme')) {
                            this.darkMode = e.matches;
                            this.applyTheme();
                        }
                    });
                }
            } else {
                console.error('⚠️ Failed to initialize Universal Hamburger Panel - required elements not created');
            }
        } catch (error) {
            console.error('⚠️ Error initializing Universal Hamburger Panel:', error);
        }
    }
    
    applyTheme() {
        if (this.darkMode) {
            document.documentElement.classList.add('dark-theme');
        } else {
            document.documentElement.classList.remove('dark-theme');
        }
    }
    
    toggleTheme() {
        this.darkMode = !this.darkMode;
        localStorage.setItem('hotelease-theme', this.darkMode ? 'dark' : 'light');
        this.applyTheme();
        
        // Update theme toggle button if it exists
        const themeToggleBtn = document.querySelector('.theme-toggle-btn');
        if (themeToggleBtn) {
            const icon = themeToggleBtn.querySelector('i');
            if (icon) {
                icon.className = this.darkMode ? 'bi bi-sun' : 'bi bi-moon';
            }
            themeToggleBtn.setAttribute('aria-label', this.darkMode ? 'Switch to Light Mode' : 'Switch to Dark Mode');
            themeToggleBtn.title = this.darkMode ? 'Switch to Light Mode' : 'Switch to Dark Mode';
        }
    }

    createElements() {
        try {
            // Create all elements at once
            this.createHamburgerButton();
            this.createOverlay();
            this.createPanel();
            
            // Verify elements were created successfully
            if (!this.hamburgerBtn || !this.overlay || !this.panel) {
                console.error('⚠️ Failed to create one or more hamburger panel elements');
            }
        } catch (error) {
            console.error('⚠️ Error creating hamburger panel elements:', error);
        }
    }

    createHamburgerButton() {
        try {
            // Remove existing hamburger elements
            const existingBtn = document.querySelector('.hamburger-btn, .hamburger-menu');
            if (existingBtn) existingBtn.remove();

            const hamburgerBtn = document.createElement('button');
            hamburgerBtn.className = 'hamburger-btn';
            hamburgerBtn.setAttribute('aria-label', 'Toggle Navigation Menu');
            hamburgerBtn.innerHTML = `
                <div class="hamburger-lines">
                    <div class="hamburger-line"></div>
                    <div class="hamburger-line"></div>
                    <div class="hamburger-line"></div>
                </div>
            `;
            
            document.body.appendChild(hamburgerBtn);
            this.hamburgerBtn = hamburgerBtn;
        } catch (error) {
            console.error('⚠️ Error creating hamburger button:', error);
        }
    }

    createOverlay() {
        try {
            let overlay = document.querySelector('.hamburger-overlay, .sidebar-overlay');
            if (!overlay) {
                overlay = document.createElement('div');
                overlay.className = 'hamburger-overlay';
                document.body.appendChild(overlay);
            }
            
            this.overlay = overlay;
        } catch (error) {
            console.error('⚠️ Error creating overlay:', error);
        }
    }

    createPanel() {
        try {
            // Remove existing panel if any
            const existingPanel = document.querySelector('.hamburger-panel');
            if (existingPanel) existingPanel.remove();
            
            const panel = document.createElement('div');
            panel.className = 'hamburger-panel';
            panel.innerHTML = this.isAdmin ? this.getAdminPanelHTML() : this.getUserPanelHTML();
            
            document.body.appendChild(panel);
            this.panel = panel;
        } catch (error) {
            console.error('⚠️ Error creating panel:', error);
        }
    }

    getAdminPanelHTML() {
        const adminName = document.querySelector('meta[name="admin-name"]')?.content || 'Admin';
        const themeIcon = this.darkMode ? 'bi-sun' : 'bi-moon';
        const themeTitle = this.darkMode ? 'Switch to Light Mode' : 'Switch to Dark Mode';
        
        return `
            <div class="panel-header">
                <button class="theme-toggle-btn" aria-label="${themeTitle}" title="${themeTitle}">
                    <i class="bi ${themeIcon}"></i>
                </button>
                <h3>
                    <i class="bi bi-shield-check"></i>
                    HotelEase Admin
                </h3>
                <div class="admin-info">Selamat datang, ${adminName}</div>
            </div>
            
            <nav class="panel-nav">
                <div class="nav-section">
                    <div class="nav-section-title">Dashboard</div>
                    <div class="nav-item">
                        <a href="dashboard.php" class="nav-link" data-page="dashboard">
                            <i class="bi bi-speedometer2"></i>
                            <span>Dashboard</span>
                        </a>
                    </div>
                </div>
                
                <div class="nav-section">
                    <div class="nav-section-title">Manajemen Hotel</div>
                    <div class="nav-item">
                        <a href="kamar.php" class="nav-link" data-page="kamar">
                            <i class="bi bi-door-open"></i>
                            <span>Kelola Kamar</span>
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="fasilitas.php" class="nav-link" data-page="fasilitas">
                            <i class="bi bi-star"></i>
                            <span>Fasilitas</span>
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="users.php" class="nav-link" data-page="users">
                            <i class="bi bi-people"></i>
                            <span>Data User</span>
                        </a>
                    </div>
                </div>
                
                <div class="nav-section">
                    <div class="nav-section-title">Reservasi</div>
                    <div class="nav-item">
                        <a href="booking_admin.php" class="nav-link" data-page="booking">
                            <i class="bi bi-calendar-check"></i>
                            <span>Manajemen Booking</span>
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="pembayaran.php" class="nav-link" data-page="pembayaran">
                            <i class="bi bi-credit-card"></i>
                            <span>Pembayaran</span>
                        </a>
                    </div>
                </div>
                
                <div class="nav-section">
                    <div class="nav-section-title">Laporan</div>
                    <div class="nav-item">
                        <a href="reports.php" class="nav-link" data-page="reports">
                            <i class="bi bi-bar-chart"></i>
                            <span>Laporan & Statistik</span>
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="log_aktivitas.php" class="nav-link" data-page="log_aktivitas">
                            <i class="bi bi-activity"></i>
                            <span>Log Aktivitas</span>
                        </a>
                    </div>
                </div>
                
                <div class="nav-section">
                    <div class="nav-section-title">Lainnya</div>
                    <div class="nav-item">
                        <a href="testimoni.php" class="nav-link" data-page="testimoni">
                            <i class="bi bi-chat-quote"></i>
                            <span>Testimoni</span>
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="notifikasi.php" class="nav-link" data-page="notifikasi">
                            <i class="bi bi-bell"></i>
                            <span>Notifikasi</span>
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
    }

    getUserPanelHTML() {
        const userName = document.querySelector('meta[name="user-name"]')?.content || 'User';
        const themeIcon = this.darkMode ? 'bi-sun' : 'bi-moon';
        const themeTitle = this.darkMode ? 'Switch to Light Mode' : 'Switch to Dark Mode';
        
        return `
            <div class="panel-header">
                <button class="theme-toggle-btn" aria-label="${themeTitle}" title="${themeTitle}">
                    <i class="bi ${themeIcon}"></i>
                </button>
                <h3>
                    <i class="bi bi-person-circle"></i>
                    HotelEase User
                </h3>
                <div class="user-info">Halo, ${userName}</div>
            </div>
            
            <nav class="panel-nav">
                <div class="nav-section">
                    <div class="nav-section-title">Menu Utama</div>
                    <div class="nav-item">
                        <a href="dashboard.php" class="nav-link" data-page="dashboard">
                            <i class="bi bi-house"></i>
                            <span>Dashboard</span>
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="kamar.php" class="nav-link" data-page="kamar">
                            <i class="bi bi-door-open"></i>
                            <span>Daftar Kamar</span>
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="fasilitas.php" class="nav-link" data-page="fasilitas">
                            <i class="bi bi-star"></i>
                            <span>Fasilitas</span>
                        </a>
                    </div>
                </div>
                
                <div class="nav-section">
                    <div class="nav-section-title">Booking & Transaksi</div>
                    <div class="nav-item">
                        <a href="booking.php" class="nav-link" data-page="booking">
                            <i class="bi bi-calendar-plus"></i>
                            <span>Booking Baru</span>
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="riwayat.php" class="nav-link" data-page="riwayat">
                            <i class="bi bi-clock-history"></i>
                            <span>Riwayat Booking</span>
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="transaksi.php" class="nav-link" data-page="transaksi">
                            <i class="bi bi-receipt"></i>
                            <span>Transaksi</span>
                        </a>
                    </div>
                </div>
                
                <div class="nav-section">
                    <div class="nav-section-title">Akun</div>
                    <div class="nav-item">
                        <a href="profil.php" class="nav-link" data-page="profil">
                            <i class="bi bi-person-gear"></i>
                            <span>Profil Saya</span>
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="testimoni.php" class="nav-link" data-page="testimoni">
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
    }

    bindEvents() {
        // Hamburger button click - check if element exists first
        if (this.hamburgerBtn) {
            this.hamburgerBtn.addEventListener('click', (e) => {
                e.preventDefault();
                this.toggle();
            });
        }

        // Overlay click to close - check if element exists first
        if (this.overlay) {
            this.overlay.addEventListener('click', () => {
                this.close();
            });
        }

        // Close on window resize (mobile orientation change)
        window.addEventListener('resize', () => {
            if (this.isOpen && window.innerWidth > 768) {
                this.close();
            }
        });
        
        // Handle device orientation changes
        if (window.matchMedia) {
            window.matchMedia('(orientation: portrait)').addEventListener('change', e => {
                // Close panel on orientation change for better UX
                if (this.isOpen) {
                    this.close();
                }
                
                // Adjust panel position after orientation change
                setTimeout(() => {
                    this.adjustPanelPosition();
                }, 300);
            });
        }
        
        // Theme toggle button click - with safety check
        setTimeout(() => {
            const themeToggleBtn = document.querySelector('.theme-toggle-btn');
            if (themeToggleBtn) {
                themeToggleBtn.addEventListener('click', (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    this.toggleTheme();
                });
            }
        }, 100); // Small delay to ensure the button is in the DOM

        // Set active navigation item
        this.setActiveNavItem();
    }

    setupKeyboardShortcuts() {
        document.addEventListener('keydown', (e) => {
            // ESC to close panel
            if (e.key === 'Escape' && this.isOpen) {
                this.close();
            }
            
            // Alt + M to toggle panel
            if (e.key === 'm' && e.altKey) {
                e.preventDefault();
                this.toggle();
            }
        });
    }

    setupTouchGestures() {
        let touchStartX = 0;
        let touchStartY = 0;

        document.addEventListener('touchstart', (e) => {
            touchStartX = e.touches[0].clientX;
            touchStartY = e.touches[0].clientY;
        }, { passive: true });

        document.addEventListener('touchend', (e) => {
            const touchEndX = e.changedTouches[0].clientX;
            const touchEndY = e.changedTouches[0].clientY;
            
            const deltaX = touchEndX - touchStartX;
            const deltaY = touchEndY - touchStartY;
            
            // Swipe right to open panel (from left edge)
            if (deltaX > 50 && Math.abs(deltaY) < 100 && touchStartX < 50 && !this.isOpen) {
                this.open();
            }
            
            // Swipe left to close panel
            if (deltaX < -50 && Math.abs(deltaY) < 100 && this.isOpen) {
                this.close();
            }
        }, { passive: true });
    }

    setActiveNavItem() {
        const currentPage = window.location.pathname.split('/').pop().replace('.php', '');
        const navLinks = this.panel.querySelectorAll('.nav-link');
        
        navLinks.forEach(link => {
            const page = link.getAttribute('data-page');
            if (page === currentPage) {
                link.classList.add('active');
            }
        });
    }

    toggle() {
        if (this.isOpen) {
            this.close();
        } else {
            this.open();
        }
    }

    open() {
        try {
            if (!this.hamburgerBtn || !this.overlay || !this.panel) {
                console.error('⚠️ Cannot open panel - required elements not found');
                return;
            }
            
            this.isOpen = true;
            this.hamburgerBtn.classList.add('active');
            this.overlay.classList.add('active');
            this.panel.classList.add('active');
            document.body.style.overflow = 'hidden';
            
            // Save panel state to localStorage
            localStorage.setItem('hotelease-panel-state', 'open');
            
            // Animation is now handled by CSS transitions and staggered delays
            // This improves performance by avoiding JavaScript-based animations
        } catch (error) {
            console.error('⚠️ Error opening panel:', error);
        }
    }

    close() {
        try {
            if (!this.hamburgerBtn || !this.overlay || !this.panel) {
                console.error('⚠️ Cannot close panel - required elements not found');
                return;
            }
            
            this.isOpen = false;
            this.hamburgerBtn.classList.remove('active');
            this.overlay.classList.remove('active');
            this.panel.classList.remove('active');
            document.body.style.overflow = '';
            
            // Save panel state to localStorage
            localStorage.setItem('hotelease-panel-state', 'closed');
            
            // Animation reset is now handled by CSS when .active class is removed
            // This improves performance by avoiding unnecessary DOM manipulations
        } catch (error) {
            console.error('⚠️ Error closing panel:', error);
        }
    }
    
    adjustPanelPosition() {
        try {
            // Check if elements exist
            if (!this.hamburgerBtn || !this.panel) {
                console.error('⚠️ Cannot adjust panel position - required elements not found');
                return;
            }
            
            // Adjust panel position based on device orientation and screen size
            const isPortrait = window.innerHeight > window.innerWidth;
            const isMobile = window.innerWidth < 768;
            
            // Adjust hamburger button position
            if (isMobile) {
                this.hamburgerBtn.style.top = isPortrait ? '15px' : '10px';
                this.hamburgerBtn.style.left = isPortrait ? '15px' : '10px';
            } else {
                this.hamburgerBtn.style.top = '20px';
                this.hamburgerBtn.style.left = '20px';
            }
            
            // Adjust panel width for different screen sizes
            if (isMobile) {
                this.panel.style.width = isPortrait ? '280px' : '320px';
            } else {
                this.panel.style.width = '320px';
            }
        } catch (error) {
            console.error('⚠️ Error adjusting panel position:', error);
        }
    }
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    // Check if already initialized
    if (window.universalHamburgerPanel) {
        return;
    }

    // Use requestAnimationFrame for smoother initialization
    requestAnimationFrame(() => {
        window.universalHamburgerPanel = new UniversalHamburgerPanel();
    });
    
    // Set up MutationObserver to detect dynamic content changes
    if (window.MutationObserver) {
        const observer = new MutationObserver((mutations) => {
            // Check if our panel was removed by some script
            if (!document.querySelector('.hamburger-btn') && window.universalHamburgerPanel?.initialized) {
                console.log('⚠️ Hamburger panel was removed, reinitializing...');
                window.universalHamburgerPanel = new UniversalHamburgerPanel();
            }
        });
        
        // Start observing the document with the configured parameters
        observer.observe(document.body, { childList: true, subtree: true });
    }
});

// Export for global access
window.UniversalHamburgerPanel = UniversalHamburgerPanel;
} // End of if block checking for duplicate class declaration
