/**
 * HiiStyle Optimized Hamburger Panel System
 * Enhanced with smooth animations, better UX, and responsive design
 * Supports both admin and user interfaces
 */

class HamburgerPanel {
    constructor(type = 'admin') {
        this.type = type; // 'admin' or 'user'
        this.isOpen = false;
        this.cartCount = 0;
        this.init();
    }

    init() {
        this.createElements();
        this.bindEvents();
        this.setActiveNavItem();
        if (this.type === 'user') {
            this.loadUserStats();
        }
    }

    createElements() {
        this.createHamburgerButton();
        this.createOverlay();
        this.createPanel();
    }

    createHamburgerButton() {
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
        this.hamburgerBtn = hamburgerBtn;
    }

    createOverlay() {
        const overlay = document.createElement('div');
        overlay.className = 'hamburger-overlay';
        document.body.appendChild(overlay);
        this.overlay = overlay;
    }

    createPanel() {
        const panel = document.createElement('div');
        panel.className = `${this.type}-panel`;
        
        if (this.type === 'admin') {
            panel.innerHTML = this.getAdminPanelHTML();
        } else {
            panel.innerHTML = this.getUserPanelHTML();
        }
        
        document.body.appendChild(panel);
        this.panel = panel;
    }

    getAdminPanelHTML() {
        const adminName = document.querySelector('meta[name="admin-name"]')?.content || 'Admin';
        return `
            <div class="panel-header">
                <h3>
                    <i class="bi bi-building"></i>
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
                        <a href="log_aktivitas.php" class="nav-link" data-page="log">
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
                <a href="../logout.php" class="logout-btn">
                    <i class="bi bi-box-arrow-right"></i>
                    Logout
                </a>
            </div>
        `;
    }

    getUserPanelHTML() {
        const userName = document.querySelector('meta[name="user-name"]')?.content || 'User';
        return `
            <div class="panel-header">
                <h3>
                    <i class="bi bi-person-circle"></i>
                    User Panel
                </h3>
                <div class="user-info">Halo, ${userName}</div>
            </div>
            
            <div class="user-stats">
                <h6>Statistik Anda</h6>
                <div class="stats-grid">
                    <div class="stat-item">
                        <span class="stat-number" id="total-orders">0</span>
                        <span class="stat-label">Pesanan</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number" id="cart-items">${this.cartCount}</span>
                        <span class="stat-label">Keranjang</span>
                    </div>
                </div>
            </div>
            
            <nav class="panel-nav">
                <div class="nav-section">
                    <div class="nav-section-title">Beranda</div>
                    <div class="nav-item">
                        <a href="${this.getBaseUrl()}/user/dashboard.php" class="nav-link" data-page="dashboard">
                            <i class="bi bi-house"></i>
                            <span>Dashboard</span>
                        </a>
                    </div>
                </div>
                
                <div class="nav-section">
                    <div class="nav-section-title">Belanja</div>
                    <div class="nav-item">
                        <a href="${this.getBaseUrl()}/user/produk.php" class="nav-link" data-page="produk">
                            <i class="bi bi-bag"></i>
                            <span>Produk</span>
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="${this.getBaseUrl()}/user/keranjang.php" class="nav-link" data-page="keranjang">
                            <i class="bi bi-cart"></i>
                            <span>Keranjang</span>
                            <span class="cart-badge" id="cart-badge" style="display: ${this.cartCount > 0 ? 'flex' : 'none'}">${this.cartCount}</span>
                        </a>
                    </div>
                </div>
                
                <div class="nav-section">
                    <div class="nav-section-title">Akun</div>
                    <div class="nav-item">
                        <a href="${this.getBaseUrl()}/user/riwayat_pembelian.php" class="nav-link" data-page="riwayat">
                            <i class="bi bi-clock-history"></i>
                            <span>Riwayat Pembelian</span>
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="${this.getBaseUrl()}/user/profil.php" class="nav-link" data-page="profil">
                            <i class="bi bi-person"></i>
                            <span>Profil Saya</span>
                        </a>
                    </div>
                </div>
            </nav>
            
            <div class="panel-footer">
                <a href="${this.getBaseUrl()}/logout.php" class="logout-btn">
                    <i class="bi bi-box-arrow-right"></i>
                    Logout
                </a>
            </div>
        `;
    }

    bindEvents() {
        // Hamburger button click
        this.hamburgerBtn.addEventListener('click', (e) => {
            e.preventDefault();
            this.toggle();
        });

        // Overlay click
        this.overlay.addEventListener('click', () => {
            this.close();
        });

        // ESC key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && this.isOpen) {
                this.close();
            }
        });

        // Navigation links
        this.panel.querySelectorAll('.nav-link').forEach(link => {
            link.addEventListener('click', (e) => {
                this.addLoadingEffect(e.target.closest('.nav-link'));
                setTimeout(() => {
                    this.close();
                }, 200);
            });
        });

        // Close panel when clicking outside
        document.addEventListener('click', (e) => {
            if (this.isOpen && 
                !this.panel.contains(e.target) && 
                !this.hamburgerBtn.contains(e.target)) {
                this.close();
            }
        });

        // Prevent panel close when clicking inside panel
        this.panel.addEventListener('click', (e) => {
            e.stopPropagation();
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
        this.isOpen = true;
        this.hamburgerBtn.classList.add('active');
        this.overlay.classList.add('active');
        this.panel.classList.add('active');
        
        // Add blur to main content
        const mainContent = document.querySelector('.main-content') || document.body;
        mainContent.classList.add('panel-open');
        
        // Disable body scroll
        document.body.style.overflow = 'hidden';
        
        // Add slide-in animation
        this.panel.classList.add('slide-in');
        setTimeout(() => {
            this.panel.classList.remove('slide-in');
        }, 500);

        // Update stats when panel opens (for user type)
        if (this.type === 'user') {
            this.updateUserStats();
        }
    }

    close() {
        this.isOpen = false;
        this.hamburgerBtn.classList.remove('active');
        this.overlay.classList.remove('active');
        
        // Add slide-out animation
        this.panel.classList.add('slide-out');
        
        setTimeout(() => {
            this.panel.classList.remove('active', 'slide-out');
            
            // Remove blur from main content
            const mainContent = document.querySelector('.main-content') || document.body;
            mainContent.classList.remove('panel-open');
            
            // Enable body scroll
            document.body.style.overflow = '';
        }, 300);
    }

    setActiveNavItem() {
        const currentPath = window.location.pathname;
        const navLinks = this.panel.querySelectorAll('.nav-link');
        
        navLinks.forEach(link => {
            link.classList.remove('active');
            if (currentPath.includes(link.getAttribute('href'))) {
                link.classList.add('active');
            }
        });
    }

    addLoadingEffect(element) {
        element.style.opacity = '0.7';
        element.style.transform = 'scale(0.95)';
        
        setTimeout(() => {
            element.style.opacity = '';
            element.style.transform = '';
        }, 200);
    }

    loadUserStats() {
        // Load cart count from localStorage or session
        this.cartCount = parseInt(localStorage.getItem('cartCount') || '0');
        this.updateCartBadge();
    }

    updateUserStats() {
        // Update cart count
        this.loadUserStats();
        
        const totalOrdersElement = this.panel.querySelector('#total-orders');
        const cartItemsElement = this.panel.querySelector('#cart-items');
        
        if (totalOrdersElement) {
            totalOrdersElement.textContent = localStorage.getItem('totalOrders') || '0';
        }
        
        if (cartItemsElement) {
            cartItemsElement.textContent = this.cartCount;
        }
    }

    updateCartBadge() {
        const cartBadge = this.panel.querySelector('#cart-badge');
        if (cartBadge) {
            cartBadge.textContent = this.cartCount;
            cartBadge.style.display = this.cartCount > 0 ? 'flex' : 'none';
        }
    }

    // Public method to update cart count from other scripts
    updateCartCount(count) {
        this.cartCount = count;
        localStorage.setItem('cartCount', count.toString());
        this.updateCartBadge();
        
        // Update stats display
        const cartItemsElement = this.panel.querySelector('#cart-items');
        if (cartItemsElement) {
            cartItemsElement.textContent = this.cartCount;
        }
    }

    getBaseUrl() {
        // Try to get base URL from meta tag or use current origin
        const baseUrl = document.querySelector('meta[name="base-url"]')?.content;
        if (baseUrl) return baseUrl;
        
        // Fallback: construct from current location
        const pathArray = window.location.pathname.split('/');
        const basePath = pathArray.slice(0, -2).join('/');
        return window.location.origin + basePath;
    }
}

// Global variable to access from other scripts
let hamburgerPanel;

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    // Determine panel type based on current page
    let panelType = 'user';
    
    if (document.body.classList.contains('admin-page') || 
        window.location.pathname.includes('/admin/')) {
        panelType = 'admin';
    }
    
    // Initialize hamburger panel
    hamburgerPanel = new HamburgerPanel(panelType);
});

// Helper function to update cart count from anywhere
function updateCartCount(count) {
    if (hamburgerPanel && hamburgerPanel.type === 'user') {
        hamburgerPanel.updateCartCount(count);
    }
}

// Expose globally
window.updateCartCount = updateCartCount;
