/**
 * Default Synapse Theme JavaScript
 *
 * Alpine.js components and theme-specific interactions
 * Features: Sidebar toggle, notifications, modals, search, user menu
 */

// Initialize Alpine.js components when DOM is ready
document.addEventListener('alpine:init', () => {
    // ============================================
    // SIDEBAR COMPONENT
    // ============================================
    Alpine.data('themeSidebar', () => ({
        collapsed: Alpine.$persist(false).as('sidebarCollapsed'),
        activeSubmenu: null,

        init() {
            // Apply initial state to body classes
            this.updateBodyClass();
        },

        toggle() {
            this.collapsed = !this.collapsed;
            this.updateBodyClass();

            // Close all submenus when collapsing
            if (this.collapsed) {
                this.activeSubmenu = null;
            }
        },

        toggleSubmenu(menuId) {
            if (this.activeSubmenu === menuId) {
                this.activeSubmenu = null;
            } else {
                this.activeSubmenu = menuId;
            }
        },

        isSubmenuOpen(menuId) {
            return this.activeSubmenu === menuId;
        },

        updateBodyClass() {
            if (this.collapsed) {
                document.body.classList.add('sidebar-collapsed');
            } else {
                document.body.classList.remove('sidebar-collapsed');
            }
        }
    }));

    // ============================================
    // THEME TOGGLE COMPONENT (Dark/Light Mode)
    // ============================================
    Alpine.data('themeToggle', () => ({
        dark: Alpine.$persist(false).as('darkMode'),

        init() {
            // Apply initial theme
            this.applyTheme();
        },

        toggle() {
            this.dark = !this.dark;
            this.applyTheme();
        },

        applyTheme() {
            if (this.dark) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        }
    }));

    // ============================================
    // NOTIFICATION SYSTEM
    // ============================================
    Alpine.data('notifications', () => ({
        items: [],
        unreadCount: 0,
        isOpen: false,

        init() {
            // Load sample notifications (replace with real API call)
            this.items = [
                {
                    id: 1,
                    title: 'Welcome to Synapse!',
                    message: 'Your theming system is ready.',
                    time: '2 minutes ago',
                    read: false,
                    type: 'info'
                }
            ];
            this.updateUnreadCount();
        },

        toggle() {
            this.isOpen = !this.isOpen;
        },

        markAsRead(notificationId) {
            const notification = this.items.find(n => n.id === notificationId);
            if (notification) {
                notification.read = true;
                this.updateUnreadCount();
            }
        },

        markAllAsRead() {
            this.items.forEach(n => n.read = true);
            this.updateUnreadCount();
        },

        updateUnreadCount() {
            this.unreadCount = this.items.filter(n => !n.read).length;
        },

        remove(notificationId) {
            this.items = this.items.filter(n => n.id !== notificationId);
            this.updateUnreadCount();
        }
    }));

    // ============================================
    // USER MENU DROPDOWN
    // ============================================
    Alpine.data('userMenu', () => ({
        isOpen: false,

        toggle() {
            this.isOpen = !this.isOpen;
        },

        close() {
            this.isOpen = false;
        }
    }));

    // ============================================
    // SEARCH COMPONENT
    // ============================================
    Alpine.data('globalSearch', () => ({
        query: '',
        results: [],
        isSearching: false,
        isOpen: false,

        async search() {
            if (this.query.length < 2) {
                this.results = [];
                return;
            }

            this.isSearching = true;
            this.isOpen = true;

            // Simulate API call (replace with real search endpoint)
            setTimeout(() => {
                this.results = [
                    { type: 'page', title: 'Dashboard', url: '/dashboard' },
                    { type: 'page', title: 'Settings', url: '/settings' },
                    { type: 'doc', title: 'User Guide', url: '/docs/guide' },
                ];
                this.isSearching = false;
            }, 300);
        },

        clear() {
            this.query = '';
            this.results = [];
            this.isOpen = false;
        },

        selectResult(result) {
            window.location.href = result.url;
        }
    }));

    // ============================================
    // MODAL COMPONENT
    // ============================================
    Alpine.data('modal', (initialOpen = false) => ({
        isOpen: initialOpen,

        open() {
            this.isOpen = true;
            document.body.style.overflow = 'hidden';
        },

        close() {
            this.isOpen = false;
            document.body.style.overflow = '';
        },

        closeOnEscape(event) {
            if (event.key === 'Escape') {
                this.close();
            }
        }
    }));

    // ============================================
    // TOOLTIP COMPONENT
    // ============================================
    Alpine.data('tooltip', (text = '') => ({
        text: text,
        show: false,

        mouseEnter() {
            this.show = true;
        },

        mouseLeave() {
            this.show = false;
        }
    }));

    // ============================================
    // TABS COMPONENT
    // ============================================
    Alpine.data('tabs', (defaultTab = 0) => ({
        activeTab: defaultTab,

        setTab(index) {
            this.activeTab = index;
        },

        isActive(index) {
            return this.activeTab === index;
        }
    }));

    // ============================================
    // DROPDOWN COMPONENT
    // ============================================
    Alpine.data('dropdown', () => ({
        isOpen: false,

        toggle() {
            this.isOpen = !this.isOpen;
        },

        close() {
            this.isOpen = false;
        },

        closeOnClickAway(event) {
            if (!this.$el.contains(event.target)) {
                this.close();
            }
        }
    }));

    // ============================================
    // TOAST NOTIFICATION SYSTEM
    // ============================================
    Alpine.data('toasts', () => ({
        items: [],
        nextId: 1,

        show(message, type = 'info', duration = 5000) {
            const id = this.nextId++;
            this.items.push({
                id,
                message,
                type,
                visible: true
            });

            // Auto-remove after duration
            setTimeout(() => {
                this.remove(id);
            }, duration);
        },

        success(message, duration = 5000) {
            this.show(message, 'success', duration);
        },

        error(message, duration = 5000) {
            this.show(message, 'error', duration);
        },

        warning(message, duration = 5000) {
            this.show(message, 'warning', duration);
        },

        info(message, duration = 5000) {
            this.show(message, 'info', duration);
        },

        remove(id) {
            const index = this.items.findIndex(item => item.id === id);
            if (index !== -1) {
                this.items[index].visible = false;
                setTimeout(() => {
                    this.items.splice(index, 1);
                }, 300);
            }
        }
    }));

    // ============================================
    // COLLAPSIBLE/ACCORDION COMPONENT
    // ============================================
    Alpine.data('collapsible', (initialOpen = false) => ({
        isOpen: initialOpen,

        toggle() {
            this.isOpen = !this.isOpen;
        },

        open() {
            this.isOpen = true;
        },

        close() {
            this.isOpen = false;
        }
    }));

    // ============================================
    // FORM VALIDATION HELPER
    // ============================================
    Alpine.data('formValidator', () => ({
        errors: {},
        touched: {},

        validate(field, rules) {
            this.touched[field] = true;
            // Add validation logic here
        },

        hasError(field) {
            return this.touched[field] && this.errors[field];
        },

        getError(field) {
            return this.errors[field];
        },

        clearErrors() {
            this.errors = {};
            this.touched = {};
        }
    }));
});

// ============================================
// UTILITY FUNCTIONS
// ============================================

/**
 * Debounce function for search and other input events
 */
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

/**
 * Format date for display
 */
function formatDate(date) {
    const now = new Date();
    const diff = now - new Date(date);
    const seconds = Math.floor(diff / 1000);
    const minutes = Math.floor(seconds / 60);
    const hours = Math.floor(minutes / 60);
    const days = Math.floor(hours / 24);

    if (days > 0) return `${days} day${days > 1 ? 's' : ''} ago`;
    if (hours > 0) return `${hours} hour${hours > 1 ? 's' : ''} ago`;
    if (minutes > 0) return `${minutes} minute${minutes > 1 ? 's' : ''} ago`;
    return 'Just now';
}

/**
 * Copy text to clipboard
 */
async function copyToClipboard(text) {
    try {
        await navigator.clipboard.writeText(text);
        return true;
    } catch (err) {
        console.error('Failed to copy:', err);
        return false;
    }
}

// ============================================
// GLOBAL EVENT LISTENERS
// ============================================

// Handle keyboard shortcuts
document.addEventListener('keydown', (event) => {
    // Cmd/Ctrl + K for global search
    if ((event.metaKey || event.ctrlKey) && event.key === 'k') {
        event.preventDefault();
        const searchInput = document.querySelector('[x-data*="globalSearch"] input');
        if (searchInput) {
            searchInput.focus();
        }
    }

    // Cmd/Ctrl + B for sidebar toggle
    if ((event.metaKey || event.ctrlKey) && event.key === 'b') {
        event.preventDefault();
        Alpine.store('sidebar')?.toggle();
    }
});

// Smooth scroll to anchor links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            e.preventDefault();
            target.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    });
});

// Export utility functions for global access
window.themeUtils = {
    debounce,
    formatDate,
    copyToClipboard
};
