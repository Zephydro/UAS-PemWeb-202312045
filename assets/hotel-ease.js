/**
 * HOTEL EASE - MODERN INTERACTIVE SYSTEM
 * Hamburger Menu & Smooth Interactions
 */

class HotelEaseUI {
  constructor() {
    this.init();
  }

  init() {
    this.createHamburgerMenu();
    this.setupEventListeners();
    this.initAnimations();
    this.setupSmoothScrolling();
  }

  createHamburgerMenu() {
    // Create hamburger button
    const hamburgerBtn = document.createElement("button");
    hamburgerBtn.className = "hamburger-btn";
    hamburgerBtn.innerHTML = `
            <div class="hamburger-icon">
                <span></span>
                <span></span>
                <span></span>
            </div>
        `;

    // Create sidebar overlay
    const sidebarOverlay = document.createElement("div");
    sidebarOverlay.className = "sidebar-overlay";

    // Create sidebar
    const sidebar = document.createElement("nav");
    sidebar.className = "sidebar";

    // Add to body
    document.body.appendChild(hamburgerBtn);
    document.body.appendChild(sidebarOverlay);
    document.body.appendChild(sidebar);

    // Store references
    this.hamburgerBtn = hamburgerBtn;
    this.sidebarOverlay = sidebarOverlay;
    this.sidebar = sidebar;
    this.mainContent = document.querySelector(".main-content") || document.body;

    // Populate sidebar based on current page
    this.populateSidebar();
  }

  populateSidebar() {
    const currentPath = window.location.pathname;
    let sidebarContent = "";

    // Determine if we're in admin or user area
    if (currentPath.includes("/admin/")) {
      sidebarContent = this.getAdminSidebarContent();
    } else if (currentPath.includes("/user/")) {
      sidebarContent = this.getUserSidebarContent();
    } else {
      sidebarContent = this.getGuestSidebarContent();
    }

    this.sidebar.innerHTML = sidebarContent;
    this.highlightActiveMenuItem();
  }

  getAdminSidebarContent() {
    return `
            <div class="sidebar-header">
                <h4>HotelEase Admin</h4>
            </div>
            <div class="sidebar-nav">
                <a href="dashboard.php"><i class="bi bi-speedometer2"></i>Dashboard</a>
                <a href="users.php"><i class="bi bi-people"></i>Manajemen Pengguna</a>
                <a href="kamar.php"><i class="bi bi-door-closed"></i>Manajemen Kamar</a>
                <a href="booking_admin.php"><i class="bi bi-calendar-check"></i>Manajemen Booking</a>
                <a href="fasilitas.php"><i class="bi bi-stars"></i>Manajemen Fasilitas</a>
                <a href="metode_pembayaran.php"><i class="bi bi-credit-card"></i>Metode Pembayaran</a>
                <a href="pembayaran.php"><i class="bi bi-cash-coin"></i>Verifikasi Pembayaran</a>
                <a href="notifikasi.php"><i class="bi bi-bell"></i>Notifikasi</a>
                <a href="log_aktivitas.php"><i class="bi bi-clock-history"></i>Log Aktivitas</a>
                <a href="reports.php"><i class="bi bi-graph-up"></i>Laporan & Analytics</a>
                <a href="staff.php"><i class="bi bi-person-badge"></i>Staff</a>
                <a href="../auth/logout.php" style="color: #ff4757; margin-top: 20px;"><i class="bi bi-box-arrow-right"></i>Logout</a>
            </div>
        `;
  }

  getUserSidebarContent() {
    return `
            <div class="sidebar-header">
                <h4>HotelEase</h4>
            </div>
            <div class="sidebar-nav">
                <a href="dashboard.php"><i class="bi bi-house-door"></i>Dashboard</a>
                <a href="booking.php"><i class="bi bi-calendar-check"></i>Booking</a>
                <a href="riwayat.php"><i class="bi bi-clock-history"></i>Riwayat</a>
                <a href="profil.php"><i class="bi bi-person-circle"></i>Profil</a>
                <a href="kamar.php"><i class="bi bi-door-closed"></i>Kamar</a>
                <a href="fasilitas.php"><i class="bi bi-stars"></i>Fasilitas</a>
                <a href="../auth/logout.php" style="color: #ff4757; margin-top: 20px;"><i class="bi bi-box-arrow-right"></i>Logout</a>
            </div>
        `;
  }

  getGuestSidebarContent() {
    return `
            <div class="sidebar-header">
                <h4>HotelEase</h4>
            </div>
            <div class="sidebar-nav">
                <a href="index.php"><i class="bi bi-house-door"></i>Beranda</a>
                <a href="kamar.php"><i class="bi bi-door-closed"></i>Kamar</a>
                <a href="fasilitas.php"><i class="bi bi-stars"></i>Fasilitas</a>
                <a href="login.php"><i class="bi bi-box-arrow-in-right"></i>Login</a>
                <a href="register.php"><i class="bi bi-person-plus"></i>Register</a>
            </div>
        `;
  }

  highlightActiveMenuItem() {
    const currentPage = window.location.pathname.split("/").pop();
    const menuItems = this.sidebar.querySelectorAll(".sidebar-nav a");

    menuItems.forEach((item) => {
      const href = item.getAttribute("href");
      if (href && href.includes(currentPage)) {
        item.classList.add("active");
      }
    });
  }

  setupEventListeners() {
    // Hamburger button click
    this.hamburgerBtn.addEventListener("click", () => {
      this.toggleSidebar();
    });

    // Overlay click to close
    this.sidebarOverlay.addEventListener("click", () => {
      this.closeSidebar();
    });

    // Escape key to close
    document.addEventListener("keydown", (e) => {
      if (e.key === "Escape" && this.sidebar.classList.contains("active")) {
        this.closeSidebar();
      }
    });

    // Prevent sidebar close when clicking inside sidebar
    this.sidebar.addEventListener("click", (e) => {
      e.stopPropagation();
    });

    // Close sidebar when clicking menu items (except logout)
    this.sidebar.addEventListener("click", (e) => {
      if (e.target.tagName === "A" && !e.target.href.includes("logout")) {
        setTimeout(() => this.closeSidebar(), 200);
      }
    });

    // Window resize handler
    window.addEventListener("resize", () => {
      if (
        window.innerWidth > 768 &&
        this.sidebar.classList.contains("active")
      ) {
        this.closeSidebar();
      }
    });
  }

  toggleSidebar() {
    const isActive = this.sidebar.classList.contains("active");
    if (isActive) {
      this.closeSidebar();
    } else {
      this.openSidebar();
    }
  }

  openSidebar() {
    this.hamburgerBtn.classList.add("active");
    this.sidebar.classList.add("active");
    this.sidebarOverlay.classList.add("active");
    this.mainContent.classList.add("blur");
    document.body.style.overflow = "hidden";

    // Add entrance animation to menu items
    const menuItems = this.sidebar.querySelectorAll(".sidebar-nav a");
    menuItems.forEach((item, index) => {
      item.style.opacity = "0";
      item.style.transform = "translateX(-20px)";
      setTimeout(() => {
        item.style.transition = "all 0.3s ease";
        item.style.opacity = "1";
        item.style.transform = "translateX(0)";
      }, 100 + index * 50);
    });
  }

  closeSidebar() {
    this.hamburgerBtn.classList.remove("active");
    this.sidebar.classList.remove("active");
    this.sidebarOverlay.classList.remove("active");
    this.mainContent.classList.remove("blur");
    document.body.style.overflow = "";

    // Reset menu items animation
    const menuItems = this.sidebar.querySelectorAll(".sidebar-nav a");
    menuItems.forEach((item) => {
      item.style.transition = "";
      item.style.opacity = "";
      item.style.transform = "";
    });
  }

  initAnimations() {
    // Intersection Observer for fade-in animations
    const observerOptions = {
      threshold: 0.1,
      rootMargin: "0px 0px -50px 0px",
    };

    const observer = new IntersectionObserver((entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          entry.target.classList.add("fade-in-up");
        }
      });
    }, observerOptions);

    // Observe elements for animation
    const animateElements = document.querySelectorAll(
      ".card, .highlight-card, .table, .alert"
    );
    animateElements.forEach((el) => {
      observer.observe(el);
    });

    // Add loading animation to page
    this.addPageLoadAnimation();
  }

  addPageLoadAnimation() {
    // Add fade-in animation to main content
    const mainContent = document.querySelector(
      ".main-content, .hero-bg, .login-card"
    );
    if (mainContent) {
      mainContent.style.opacity = "0";
      mainContent.style.transform = "translateY(20px)";

      setTimeout(() => {
        mainContent.style.transition = "all 0.6s ease";
        mainContent.style.opacity = "1";
        mainContent.style.transform = "translateY(0)";
      }, 100);
    }
  }

  setupSmoothScrolling() {
    // Smooth scrolling for anchor links
    document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
      anchor.addEventListener("click", function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute("href"));
        if (target) {
          target.scrollIntoView({
            behavior: "smooth",
            block: "start",
          });
        }
      });
    });
  }

  // Enhanced form interactions
  enhanceFormInputs() {
    const inputs = document.querySelectorAll(".form-control");
    inputs.forEach((input) => {
      // Add floating label effect
      input.addEventListener("focus", function () {
        this.parentElement.classList.add("focused");
      });

      input.addEventListener("blur", function () {
        if (!this.value) {
          this.parentElement.classList.remove("focused");
        }
      });

      // Add ripple effect to buttons
      const buttons = document.querySelectorAll(".btn");
      buttons.forEach((button) => {
        button.addEventListener("click", function (e) {
          const ripple = document.createElement("span");
          const rect = this.getBoundingClientRect();
          const size = Math.max(rect.width, rect.height);
          const x = e.clientX - rect.left - size / 2;
          const y = e.clientY - rect.top - size / 2;

          ripple.style.width = ripple.style.height = size + "px";
          ripple.style.left = x + "px";
          ripple.style.top = y + "px";
          ripple.classList.add("ripple");

          this.appendChild(ripple);

          setTimeout(() => {
            ripple.remove();
          }, 600);
        });
      });
    });
  }

  // Enhanced table interactions
  enhanceTableInteractions() {
    const tables = document.querySelectorAll(".table");
    tables.forEach((table) => {
      const rows = table.querySelectorAll("tbody tr");
      rows.forEach((row) => {
        row.addEventListener("mouseenter", function () {
          this.style.transform = "scale(1.01)";
          this.style.transition = "all 0.2s ease";
        });

        row.addEventListener("mouseleave", function () {
          this.style.transform = "scale(1)";
        });
      });
    });
  }

  // Card hover effects
  enhanceCardInteractions() {
    const cards = document.querySelectorAll(".card, .highlight-card");
    cards.forEach((card) => {
      card.addEventListener("mouseenter", function () {
        this.style.transform = "translateY(-8px) scale(1.02)";
      });

      card.addEventListener("mouseleave", function () {
        this.style.transform = "translateY(0) scale(1)";
      });
    });
  }

  // Initialize all enhancements
  initializeEnhancements() {
    this.enhanceFormInputs();
    this.enhanceTableInteractions();
    this.enhanceCardInteractions();
  }
}

// CSS for ripple effect
const rippleCSS = `
    .btn {
        position: relative;
        overflow: hidden;
    }
    
    .ripple {
        position: absolute;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.6);
        transform: scale(0);
        animation: ripple-animation 0.6s linear;
        pointer-events: none;
    }
    
    @keyframes ripple-animation {
        to {
            transform: scale(4);
            opacity: 0;
        }
    }
    
    .form-group.focused .form-label {
        color: var(--primary-gold);
        transform: translateY(-5px);
        font-size: 0.9rem;
    }
`;

// Add ripple CSS to head
const style = document.createElement("style");
style.textContent = rippleCSS;
document.head.appendChild(style);

// Initialize when DOM is loaded
document.addEventListener("DOMContentLoaded", () => {
  const hotelUI = new HotelEaseUI();

  // Initialize enhancements after a short delay
  setTimeout(() => {
    hotelUI.initializeEnhancements();
  }, 500);
});

// Export for use in other scripts
window.HotelEaseUI = HotelEaseUI;
