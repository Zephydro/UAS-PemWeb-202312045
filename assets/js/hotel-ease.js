/**
 * Hotel Ease - Modern Hotel Management System JavaScript
 * Author: Hotel Ease Team
 * Version: 1.0.0
 */

'use strict';

// Main Hotel Ease Object
const HotelEase = {
    // Configuration
    config: {
        animationSpeed: 300,
        toastDuration: 3000,
        sidebarWidth: 350
    },

    // Initialize the application
    init() {
        console.log('🏨 Hotel Ease System Loading...');
        this.setupEventListeners();
        this.initSidebar();
        this.initTooltips();
        this.initAnimations();
        console.log('✅ Hotel Ease System Ready!');
    },

    // Setup global event listeners
    setupEventListeners() {
        document.addEventListener('DOMContentLoaded', () => {
            this.initComponents();
        });

        // Handle form submissions
        document.addEventListener('submit', (e) => {
            const form = e.target;
            if (form.classList.contains('needs-validation')) {
                this.handleFormValidation(e);
            }
        });

        // Handle clicks
        document.addEventListener('click', (e) => {
            if (e.target.matches('.btn-delete, .btn-delete *')) {
                this.confirmDelete(e);
            }
        });
    },

    // Initialize sidebar functionality
    initSidebar() {
        const hamburgerBtn = document.querySelector('.hamburger-btn');
        const sidebar = document.querySelector('.sidebar');
        const sidebarOverlay = document.querySelector('.sidebar-overlay');
        const mainContent = document.querySelector('.main-content');

        if (hamburgerBtn && sidebar) {
            hamburgerBtn.addEventListener('click', () => {
                this.toggleSidebar();
            });

            if (sidebarOverlay) {
                sidebarOverlay.addEventListener('click', () => {
                    this.closeSidebar();
                });
            }

            // Close sidebar on ESC key
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && sidebar.classList.contains('active')) {
                    this.closeSidebar();
                }
            });
        }
    },

    // Toggle sidebar
    toggleSidebar() {
        const hamburgerBtn = document.querySelector('.hamburger-btn');
        const sidebar = document.querySelector('.sidebar');
        const sidebarOverlay = document.querySelector('.sidebar-overlay');
        const mainContent = document.querySelector('.main-content');

        if (sidebar.classList.contains('active')) {
            this.closeSidebar();
        } else {
            this.openSidebar();
        }
    },

    // Open sidebar
    openSidebar() {
        const hamburgerBtn = document.querySelector('.hamburger-btn');
        const sidebar = document.querySelector('.sidebar');
        const sidebarOverlay = document.querySelector('.sidebar-overlay');
        const mainContent = document.querySelector('.main-content');

        hamburgerBtn?.classList.add('active');
        sidebar?.classList.add('active');
        sidebarOverlay?.classList.add('active');
        mainContent?.classList.add('blur');
        document.body.style.overflow = 'hidden';
    },

    // Close sidebar
    closeSidebar() {
        const hamburgerBtn = document.querySelector('.hamburger-btn');
        const sidebar = document.querySelector('.sidebar');
        const sidebarOverlay = document.querySelector('.sidebar-overlay');
        const mainContent = document.querySelector('.main-content');

        hamburgerBtn?.classList.remove('active');
        sidebar?.classList.remove('active');
        sidebarOverlay?.classList.remove('active');
        mainContent?.classList.remove('blur');
        document.body.style.overflow = '';
    },

    // Initialize tooltips
    initTooltips() {
        const tooltipElements = document.querySelectorAll('[data-tooltip]');
        tooltipElements.forEach(element => {
            element.addEventListener('mouseenter', this.showTooltip);
            element.addEventListener('mouseleave', this.hideTooltip);
        });
    },

    // Show tooltip
    showTooltip(e) {
        const text = e.target.getAttribute('data-tooltip');
        if (!text) return;

        const tooltip = document.createElement('div');
        tooltip.className = 'tooltip-custom';
        tooltip.textContent = text;
        document.body.appendChild(tooltip);

        const rect = e.target.getBoundingClientRect();
        tooltip.style.left = `${rect.left + rect.width / 2}px`;
        tooltip.style.top = `${rect.top - tooltip.offsetHeight - 10}px`;
    },

    // Hide tooltip
    hideTooltip() {
        const tooltip = document.querySelector('.tooltip-custom');
        if (tooltip) {
            tooltip.remove();
        }
    },

    // Initialize animations
    initAnimations() {
        // Add fade-in animation to cards
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('fade-in-up');
                }
            });
        }, observerOptions);

        // Observe cards and other elements
        const animatedElements = document.querySelectorAll('.card, .highlight-card, .facility-card');
        animatedElements.forEach(element => {
            observer.observe(element);
        });
    },

    // Initialize components
    initComponents() {
        this.initDatePickers();
        this.initDataTables();
        this.initFormValidation();
    },

    // Initialize date pickers
    initDatePickers() {
        const dateInputs = document.querySelectorAll('input[type="date"]');
        dateInputs.forEach(input => {
            // Add calendar icon
            const wrapper = document.createElement('div');
            wrapper.className = 'date-input-wrapper';
            input.parentNode.insertBefore(wrapper, input);
            wrapper.appendChild(input);
        });
    },

    // Initialize data tables
    initDataTables() {
        const tables = document.querySelectorAll('.table');
        tables.forEach(table => {
            this.makeTableResponsive(table);
        });
    },

    // Make table responsive
    makeTableResponsive(table) {
        const wrapper = document.createElement('div');
        wrapper.className = 'table-responsive';
        table.parentNode.insertBefore(wrapper, table);
        wrapper.appendChild(table);
    },

    // Initialize form validation
    initFormValidation() {
        const forms = document.querySelectorAll('.needs-validation');
        forms.forEach(form => {
            form.addEventListener('submit', this.handleFormValidation.bind(this));
        });
    },

    // Handle form validation
    handleFormValidation(e) {
        const form = e.target;
        if (!form.checkValidity()) {
            e.preventDefault();
            e.stopPropagation();
            this.showValidationErrors(form);
        }
        form.classList.add('was-validated');
    },

    // Show validation errors
    showValidationErrors(form) {
        const firstInvalidInput = form.querySelector(':invalid');
        if (firstInvalidInput) {
            firstInvalidInput.focus();
            this.showToast('Please fill in all required fields correctly.', 'error');
        }
    },

    // Confirm delete action
    confirmDelete(e) {
        e.preventDefault();
        const message = e.target.getAttribute('data-confirm') || 'Are you sure you want to delete this item?';
        
        if (confirm(message)) {
            // Proceed with deletion
            const href = e.target.getAttribute('href') || e.target.closest('a').getAttribute('href');
            if (href) {
                window.location.href = href;
            }
        }
    },

    // Show toast notification
    showToast(message, type = 'info') {
        const toast = document.createElement('div');
        toast.className = `toast toast-${type}`;
        toast.innerHTML = `
            <div class="toast-content">
                <span class="toast-message">${message}</span>
                <button class="toast-close">&times;</button>
            </div>
        `;

        document.body.appendChild(toast);

        // Show toast
        setTimeout(() => {
            toast.classList.add('show');
        }, 100);

        // Auto hide toast
        setTimeout(() => {
            this.hideToast(toast);
        }, this.config.toastDuration);

        // Close button
        toast.querySelector('.toast-close').addEventListener('click', () => {
            this.hideToast(toast);
        });
    },

    // Hide toast
    hideToast(toast) {
        toast.classList.remove('show');
        setTimeout(() => {
            if (toast.parentNode) {
                toast.parentNode.removeChild(toast);
            }
        }, 300);
    },

    // Utility functions
    utils: {
        // Format currency
        formatCurrency(amount) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR'
            }).format(amount);
        },

        // Format date
        formatDate(date) {
            return new Intl.DateTimeFormat('id-ID', {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            }).format(new Date(date));
        },

        // Debounce function
        debounce(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        },

        // Get CSRF token
        getCSRFToken() {
            const token = document.querySelector('meta[name="csrf-token"]');
            return token ? token.getAttribute('content') : null;
        }
    }
};

// Initialize when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => HotelEase.init());
} else {
    HotelEase.init();
}

// Add CSS for toast notifications and tooltips
const additionalCSS = `
<style>
.tooltip-custom {
    position: absolute;
    background: rgba(0, 0, 0, 0.9);
    color: white;
    padding: 8px 12px;
    border-radius: 4px;
    font-size: 0.8rem;
    pointer-events: none;
    z-index: 10000;
    transform: translateX(-50%);
}

.toast {
    position: fixed;
    top: 20px;
    right: 20px;
    min-width: 300px;
    padding: 16px;
    border-radius: 8px;
    color: white;
    z-index: 10001;
    transform: translateX(400px);
    transition: transform 0.3s ease;
}

.toast.show {
    transform: translateX(0);
}

.toast-info {
    background: linear-gradient(135deg, #17a2b8, #20c997);
}

.toast-success {
    background: linear-gradient(135deg, #28a745, #20c997);
}

.toast-error {
    background: linear-gradient(135deg, #dc3545, #e74c3c);
}

.toast-warning {
    background: linear-gradient(135deg, #ffc107, #fd7e14);
}

.toast-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.toast-close {
    background: none;
    border: none;
    color: white;
    font-size: 1.5rem;
    cursor: pointer;
    margin-left: 16px;
}

.date-input-wrapper {
    position: relative;
}

.date-input-wrapper::after {
    content: '📅';
    position: absolute;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
    pointer-events: none;
}
</style>
`;

document.head.insertAdjacentHTML('beforeend', additionalCSS);

// Export for global use
window.HotelEase = HotelEase;

console.log('✅ Hotel Ease JavaScript loaded successfully!');
