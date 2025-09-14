import './bootstrap';
import AlpineImport from 'alpinejs';

// Robust Alpine initialization to prevent race conditions
let alpineInitialized = false;

function initializeAlpine() {
    if (alpineInitialized) return;
    alpineInitialized = true;

    // Use existing Alpine instance if available (CDN), otherwise use our import
    const Alpine = window.Alpine || AlpineImport;
    window.Alpine = Alpine;

    // Register stores/directives before starting Alpine
    setupScrollAnimations(Alpine);

    // Start Alpine if it hasn't been started yet
    if (!Alpine._hasStarted) {
        Alpine.start();
    }
}

function setupScrollAnimations(Alpine) {
    // Custom scroll animation system with enhanced reliability
    Alpine.store('scrollAnimations', {
        elements: new Map(),
        scrollY: 0,
        isScrolling: false,
        isReady: false,
        pendingChecks: [],

        init() {
            // Throttled scroll listener
            let ticking = false;
            window.addEventListener('scroll', () => {
                if (!ticking) {
                    requestAnimationFrame(() => {
                        this.scrollY = window.scrollY;
                        this.checkElements();
                        ticking = false;
                    });
                    ticking = true;
                }
            });

            // Mark as ready and process any pending checks
            this.isReady = true;

            // Initial check with proper timing
            setTimeout(() => {
                this.checkElements();
                // Fallback check to ensure content shows
                setTimeout(() => this.checkElements(), 200);
            }, 100);
        },

        register(element, options = {}) {
            const id = Math.random().toString(36).substr(2, 9);
            this.elements.set(id, {
                element,
                options: {
                    offset: options.offset || 100,
                    once: options.once || false,
                    ...options
                },
                triggered: false
            });

            // If store is ready, do an immediate check for this element
            if (this.isReady) {
                setTimeout(() => this.checkElements(), 50);
            }

            return id;
        },

        unregister(id) {
            this.elements.delete(id);
        },

        checkElements() {
            const windowHeight = window.innerHeight;

            this.elements.forEach((item, id) => {
                const { element, options, triggered } = item;

                if (options.once && triggered) return;

                const rect = element.getBoundingClientRect();
                const elementTop = rect.top;
                const elementVisible = elementTop < (windowHeight - options.offset);

                if (elementVisible && !triggered) {
                    // Trigger show animation
                    element.dispatchEvent(new CustomEvent('scroll-reveal', {
                        detail: { visible: true }
                    }));

                    if (options.once) {
                        item.triggered = true;
                    }
                } else if (!elementVisible && triggered && !options.once) {
                    // Element scrolled out of view (for repeating animations)
                    element.dispatchEvent(new CustomEvent('scroll-reveal', {
                        detail: { visible: false }
                    }));
                    item.triggered = false;
                }
            });
        }
    });

    // Custom directive for scroll-based animations
    Alpine.directive('scroll-animate', (el, { expression, modifiers }, { evaluate, cleanup }) => {
        const options = {
            once: modifiers.includes('once'),
            offset: modifiers.find(mod => mod.startsWith('offset'))?.split('-')[1] || 100
        };

        let elementId;

        // Register element when Alpine initializes
        Alpine.nextTick(() => {
            elementId = Alpine.store('scrollAnimations').register(el, options);

            // Listen for scroll reveal events
            el.addEventListener('scroll-reveal', (event) => {
                if (event.detail.visible) {
                    evaluate(expression);
                }
            });
        });

        cleanup(() => {
            if (elementId) {
                Alpine.store('scrollAnimations').unregister(elementId);
            }
        });
    });
}

// Initialize Alpine when DOM is ready or immediately if already ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializeAlpine);
} else {
    initializeAlpine();
}

// Also handle alpine:init event for compatibility
document.addEventListener('alpine:init', () => {
    // Initialize scroll animations store if Alpine was started externally
    if (window.Alpine && window.Alpine.store('scrollAnimations')) {
        window.Alpine.store('scrollAnimations').init();
    }
});

// LabGOS API Client
const LabGOS = {
    baseURL: '/api',

    // Helper method for making API requests
    async request(endpoint, options = {}) {
        const url = `${this.baseURL}${endpoint}`;
        const config = {
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                ...options.headers
            },
            ...options
        };

        try {
            const response = await fetch(url, config);
            const data = await response.json();

            if (!response.ok) {
                throw {
                    status: response.status,
                    response: { data }
                };
            }

            return data;
        } catch (error) {
            if (error.response) {
                throw error;
            }
            throw {
                status: 0,
                response: {
                    data: {
                        message: 'Koneksi bermasalah. Periksa jaringan internet Anda.'
                    }
                }
            };
        }
    },

    // Get equipment with filters and pagination
    async getEquipment(params = {}) {
        const searchParams = new URLSearchParams();

        if (params.category_id) searchParams.append('category_id', params.category_id);
        if (params.available_only !== undefined) searchParams.append('available_only', params.available_only);
        if (params.search) searchParams.append('search', params.search);
        if (params.page) searchParams.append('page', params.page);

        const queryString = searchParams.toString();
        const endpoint = `/equipment${queryString ? '?' + queryString : ''}`;

        return await this.request(endpoint);
    },

    // Get equipment categories
    async getCategories() {
        return await this.request('/equipment/categories');
    },

    // Get equipment detail by ID
    async getEquipmentDetail(id) {
        return await this.request(`/equipment/${id}`);
    },

    // Submit borrow request
    async submitBorrowRequest(payload) {
        return await this.request('/requests/borrow', {
            method: 'POST',
            body: JSON.stringify(payload)
        });
    },

    // Track borrow request
    async trackBorrow(requestId) {
        return await this.request(`/tracking/borrow/${requestId}`);
    },

    // Cancel borrow request
    async cancelBorrow(requestId) {
        return await this.request(`/tracking/borrow/${requestId}/cancel`, {
            method: 'DELETE'
        });
    },

    // Submit visit request
    async submitVisitRequest(payload) {
        return await this.request('/requests/visit', {
            method: 'POST',
            body: JSON.stringify(payload)
        });
    },

    // Track visit request
    async trackVisit(requestId) {
        return await this.request(`/tracking/visit/${requestId}`);
    },

    // Cancel visit request
    async cancelVisit(requestId) {
        return await this.request(`/tracking/visit/${requestId}/cancel`, {
            method: 'DELETE'
        });
    },

    // Get available time slots for visit scheduling
    async getAvailableTimeSlots(date, duration) {
        const searchParams = new URLSearchParams();
        searchParams.append('date', date);
        searchParams.append('duration', duration);

        return await this.request(`/visits/available-slots?${searchParams.toString()}`);
    },

    // Submit testing request
    async submitTestingRequest(payload) {
        return await this.request('/requests/testing', {
            method: 'POST',
            body: JSON.stringify(payload)
        });
    },

    // Track testing request
    async trackTesting(requestId) {
        return await this.request(`/tracking/testing/${requestId}`);
    },

    // Alias for consistency
    async getTestingRequest(requestId) {
        return await this.trackTesting(requestId);
    },

    // Cancel testing request
    async cancelTestingRequest(requestId) {
        return await this.request(`/tracking/testing/${requestId}/cancel`, {
            method: 'DELETE'
        });
    },

    // Get site settings with caching
    async getSiteSettings() {
        // Simple caching mechanism (5 minutes)
        if (this._siteSettingsCache && this._siteSettingsCacheTime &&
            (Date.now() - this._siteSettingsCacheTime < 300000)) {
            return this._siteSettingsCache;
        }

        try {
            const response = await this.request('/site/settings');
            if (response.success) {
                this._siteSettingsCache = response;
                this._siteSettingsCacheTime = Date.now();
            }
            return response;
        } catch (error) {
            console.error('Failed to fetch site settings:', error);
            throw error;
        }
    }
};

// Expose LabGOS to global window object
window.LabGOS = LabGOS;
// ========================================
// ADMIN INTERFACE COMPONENTS
// ========================================

// Admin API Client
const AdminAPI = {
    baseURL: '/api/admin',

    // Helper method for making Sanctum Bearer token authenticated API requests
    async request(endpoint, options = {}) {
        const bearerToken = localStorage.getItem('admin_token') || sessionStorage.getItem('admin_token');
        const url = `${this.baseURL}${endpoint}`;

        const config = {
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'Authorization': bearerToken ? `Bearer ${bearerToken}` : '',
                ...options.headers
            },
            ...options
        };

        try {
            const response = await fetch(url, config);

            // Handle different content types
            let data;
            const contentType = response.headers.get('content-type');

            try {
                if (contentType && contentType.includes('application/json')) {
                    data = await response.json();
                } else {
                    // Server returned non-JSON response (likely HTML error page)
                    const text = await response.text();
                    console.error('Server returned non-JSON response:', text.substring(0, 200));

                    data = {
                        success: false,
                        message: `Server error: Expected JSON but received ${contentType || 'unknown content type'}. This usually indicates a 404 or 500 error.`
                    };
                }
            } catch (parseError) {
                console.error('Failed to parse response:', parseError);
                data = {
                    success: false,
                    message: `Failed to parse server response: ${parseError.message}`
                };
            }

            if (!response.ok) {
                // Handle authentication errors specifically
                if (response.status === 401) {
                    console.warn('Authentication expired. Please log in again.');
                    // Could redirect to login here if needed
                }

                throw {
                    status: response.status,
                    response: { data }
                };
            }

            return data;
        } catch (error) {
            // Log error for debugging
            console.error('AdminAPI Request Error:', {
                endpoint,
                error: error.message || error,
                status: error.status
            });

            if (error.response) {
                throw error;
            }
            throw {
                status: 0,
                response: {
                    data: {
                        success: false,
                        message: 'Network error. Please check your connection.'
                    }
                }
            };
        }
    },

    // Dashboard Stats
    async getDashboardStats(dateFrom = null, dateTo = null) {
        let params = '';
        if (dateFrom && dateTo) {
            params = `?date_from=${dateFrom}&date_to=${dateTo}`;
        }
        return await this.request(`/dashboard/stats${params}`);
    },

    // Activity Logs
    async getActivityLogs(page = 1, limit = 10) {
        return await this.request(`/activity-logs?page=${page}&limit=${limit}`);
    },

    // Notifications
    async getNotifications() {
        return await this.request('/notifications');
    },

    // Borrow Requests Management
    async getBorrowRequests(filters = {}) {
        const params = new URLSearchParams(filters).toString();
        return await this.request(`/requests/borrow${params ? '?' + params : ''}`);
    },

    async getBorrowRequest(id) {
        return await this.request(`/requests/borrow/${id}`);
    },

    async approveBorrowRequest(id) {
        return await this.request(`/requests/borrow/${id}/approve`, { method: 'PUT' });
    },

    async rejectBorrowRequest(id, reason = '') {
        return await this.request(`/requests/borrow/${id}/reject`, {
            method: 'PUT',
            body: JSON.stringify({ reason })
        });
    },

    // Visit Requests Management
    async getVisitRequests(filters = {}) {
        const params = new URLSearchParams(filters).toString();
        return await this.request(`/requests/visit${params ? '?' + params : ''}`);
    },

    async getVisitRequest(id) {
        return await this.request(`/requests/visit/${id}`);
    },

    async approveVisitRequest(id) {
        return await this.request(`/requests/visit/${id}/approve`, { method: 'PUT' });
    },

    async rejectVisitRequest(id, reason = '') {
        return await this.request(`/requests/visit/${id}/reject`, {
            method: 'PUT',
            body: JSON.stringify({ reason })
        });
    },

    // Testing Requests Management
    async getTestingRequests(filters = {}) {
        const params = new URLSearchParams(filters).toString();
        return await this.request(`/requests/testing${params ? '?' + params : ''}`);
    },

    async getTestingRequest(id) {
        return await this.request(`/requests/testing/${id}`);
    },

    async approveTestingRequest(id) {
        return await this.request(`/requests/testing/${id}/approve`, { method: 'PUT' });
    },

    async rejectTestingRequest(id, reason = '') {
        return await this.request(`/requests/testing/${id}/reject`, {
            method: 'PUT',
            body: JSON.stringify({ reason })
        });
    },

    // Equipment Management
    async getEquipmentAdmin(filters = {}) {
        const params = new URLSearchParams(filters).toString();
        return await this.request(`/equipment${params ? '?' + params : ''}`);
    },

    async getEquipmentDetails(id) {
        return await this.request(`/equipment/${id}`);
    },

    async createEquipment(data) {
        return await this.request('/equipment', {
            method: 'POST',
            body: JSON.stringify(data)
        });
    },

    async updateEquipment(id, data) {
        return await this.request(`/equipment/${id}`, {
            method: 'PUT',
            body: JSON.stringify(data)
        });
    },

    async deleteEquipment(id) {
        return await this.request(`/equipment/${id}`, { method: 'DELETE' });
    },

    // Super Admin - User Management
    async getUsers(filters = {}) {
        const params = new URLSearchParams(filters).toString();
        return await this.request(`/superadmin/users${params ? '?' + params : ''}`);
    },

    async getUser(id) {
        return await this.request(`/superadmin/users/${id}`);
    },

    async createUser(data) {
        return await this.request('/superadmin/users', {
            method: 'POST',
            body: JSON.stringify(data)
        });
    },

    async updateUser(id, data) {
        return await this.request(`/superadmin/users/${id}`, {
            method: 'PUT',
            body: JSON.stringify(data)
        });
    },

    async deleteUser(id) {
        return await this.request(`/superadmin/users/${id}`, { method: 'DELETE' });
    },

    async updateUserStatus(id, isActive) {
        return await this.request(`/superadmin/users/${id}/status`, {
            method: 'PUT',
            body: JSON.stringify({ is_active: isActive })
        });
    },

    // Site Settings Management (Content Management)
    async getSiteSettings(filters = {}) {
        const params = new URLSearchParams(filters).toString();
        return await this.request(`/content/site-settings${params ? '?' + params : ''}`);
    },

    async updateSiteSettings(settingsData) {
        return await this.request('/content/site-settings', {
            method: 'PUT',
            body: JSON.stringify({ settings: settingsData })
        });
    },

    // Content Management - Articles
    async getArticles(filters = {}) {
        const params = new URLSearchParams(filters).toString();
        return await this.request(`/content/articles${params ? '?' + params : ''}`);
    },

    async getArticle(id) {
        return await this.request(`/content/articles/${id}`);
    },

    async createArticle(data) {
        // Handle form data for file uploads
        const bearerToken = localStorage.getItem('admin_token') || sessionStorage.getItem('admin_token');
        return await this.request('/content/articles', {
            method: 'POST',
            headers: {
                // Remove Content-Type to allow browser to set boundary for FormData
                'Accept': 'application/json',
                'Authorization': bearerToken ? `Bearer ${bearerToken}` : '',
            },
            body: data // Should be FormData for file uploads
        });
    },

    async updateArticle(id, data) {
        const bearerToken = localStorage.getItem('admin_token') || sessionStorage.getItem('admin_token');
        return await this.request(`/content/articles/${id}`, {
            method: 'PUT',
            headers: data instanceof FormData ? {
                'Accept': 'application/json',
                'Authorization': bearerToken ? `Bearer ${bearerToken}` : '',
            } : undefined,
            body: data
        });
    },

    async deleteArticle(id) {
        return await this.request(`/content/articles/${id}`, { method: 'DELETE' });
    },

    // Content Management - Staff
    async getStaff(filters = {}) {
        const params = new URLSearchParams(filters).toString();
        return await this.request(`/content/staff${params ? '?' + params : ''}`);
    },

    async getStaffMember(id) {
        return await this.request(`/content/staff/${id}`);
    },

    async createStaffMember(data) {
        const bearerToken = localStorage.getItem('admin_token') || sessionStorage.getItem('admin_token');
        return await this.request('/content/staff', {
            method: 'POST',
            headers: data instanceof FormData ? {
                'Accept': 'application/json',
                'Authorization': bearerToken ? `Bearer ${bearerToken}` : '',
            } : undefined,
            body: data
        });
    },

    async updateStaffMember(id, data) {
        const bearerToken = localStorage.getItem('admin_token') || sessionStorage.getItem('admin_token');
        return await this.request(`/content/staff/${id}`, {
            method: 'PUT',
            headers: data instanceof FormData ? {
                'Accept': 'application/json',
                'Authorization': bearerToken ? `Bearer ${bearerToken}` : '',
            } : undefined,
            body: data
        });
    },

    async deleteStaffMember(id) {
        return await this.request(`/content/staff/${id}`, { method: 'DELETE' });
    },

    // Content Management - Gallery
    async getGallery(filters = {}) {
        const params = new URLSearchParams(filters).toString();
        return await this.request(`/content/gallery${params ? '?' + params : ''}`);
    },

    async getGalleryItem(id) {
        return await this.request(`/content/gallery/${id}`);
    },

    async createGalleryItem(data) {
        const bearerToken = localStorage.getItem('admin_token') || sessionStorage.getItem('admin_token');
        return await this.request('/content/gallery', {
            method: 'POST',
            headers: data instanceof FormData ? {
                'Accept': 'application/json',
                'Authorization': bearerToken ? `Bearer ${bearerToken}` : '',
            } : undefined,
            body: data
        });
    },

    async updateGalleryItem(id, data) {
        const bearerToken = localStorage.getItem('admin_token') || sessionStorage.getItem('admin_token');
        return await this.request(`/content/gallery/${id}`, {
            method: 'PUT',
            headers: data instanceof FormData ? {
                'Accept': 'application/json',
                'Authorization': bearerToken ? `Bearer ${bearerToken}` : '',
            } : undefined,
            body: data
        });
    },

    async deleteGalleryItem(id) {
        return await this.request(`/content/gallery/${id}`, { method: 'DELETE' });
    }
};

// Expose AdminAPI to global window object
window.AdminAPI = AdminAPI;

// Ensure AdminAPI is available before Alpine components
if (typeof window.AdminAPI === 'undefined') {
    console.error('AdminAPI failed to initialize');
}

// Export utility functions globally for Alpine templates
window.getTrendColor = function(trend) {
    if (trend > 0) return 'text-red-600';
    if (trend < 0) return 'text-green-600';
    return 'text-gray-500';
};

window.formatDate = function(dateString) {
    if (!dateString) return 'N/A';
    const date = new Date(dateString);
    return date.toLocaleDateString('id-ID', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};

// Admin App Alpine Component with dependency checks
function adminApp() {
    return {
        // Sidebar state
        sidebarCollapsed: false,
        sidebarOpen: false,

        // Loading states
        loading: false,
        pageLoading: false,

        // Toast notifications
        toasts: [],

        // User data
        user: null,
        notifications: [],
        unreadNotifications: 0,

        // Initialize admin app with dependency checks
        init() {
            console.log('Initializing adminApp...');

            // Check dependencies
            if (typeof AdminAPI === 'undefined') {
                console.error('AdminAPI not available during adminApp init');
                this.showToast('System initialization error: API not available', 'error');
                return;
            }

            // Initialize components
            try {
                this.loadUser();
                this.loadNotifications();
                this.setupEventListeners();
                console.log('adminApp initialized successfully');
            } catch (error) {
                console.error('Error initializing adminApp:', error);
                this.showToast('System initialization error', 'error');
            }
        },

        // Toggle sidebar collapse (desktop)
        toggleSidebarCollapse() {
            this.sidebarCollapsed = !this.sidebarCollapsed;
            localStorage.setItem('admin-sidebar-collapsed', this.sidebarCollapsed);
        },

        // Toggle sidebar open (mobile)
        toggleSidebarOpen() {
            this.sidebarOpen = !this.sidebarOpen;
        },

        // Load current user data
        async loadUser() {
            try {
                // User data should be available from Laravel's auth system
                // This would typically come from a meta tag or embedded data
                const userElement = document.querySelector('meta[name="user-data"]');
                if (userElement) {
                    this.user = JSON.parse(userElement.getAttribute('content'));
                }
            } catch (error) {
                console.error('Failed to load user data:', error);
            }
        },

        // Load notifications with better error handling
        async loadNotifications() {
            try {
                console.log('Loading notifications...');
                const response = await AdminAPI.getNotifications();

                if (response && response.success) {
                    // Handle different response formats
                    const notificationData = response.data?.data || response.data || [];
                    this.notifications = notificationData.slice(0, 5);
                    this.unreadNotifications = notificationData.filter(n => !n.read_at).length;
                    console.log(`Loaded ${this.notifications.length} notifications (${this.unreadNotifications} unread)`);
                } else {
                    console.warn('Invalid response format for notifications:', response);
                    this.notifications = [];
                    this.unreadNotifications = 0;
                }
            } catch (error) {
                console.error('Failed to load notifications:', error);
                this.notifications = [];
                this.unreadNotifications = 0;

                // Handle specific error cases
                if (error.status === 401) {
                    console.warn('Authentication required for notifications');
                } else if (error.status === 404) {
                    console.warn('Notifications endpoint not found');
                } else if (error.status >= 400) {
                    console.error('API error loading notifications:', error.response?.data?.message || 'Unknown error');
                }
            }
        },

        // Show toast notification
        showToast(message, type = 'info', duration = 5000) {
            const id = Math.random().toString(36).substr(2, 9);
            const toast = {
                id,
                message,
                type,
                show: false
            };

            this.toasts.push(toast);

            // Trigger show animation
            this.$nextTick(() => {
                const toastElement = document.querySelector(`[data-toast-id="${id}"]`);
                if (toastElement) {
                    toastElement.classList.add('show');
                }

                // Auto dismiss
                setTimeout(() => {
                    this.dismissToast(id);
                }, duration);
            });
        },

        // Dismiss toast
        dismissToast(id) {
            const toastElement = document.querySelector(`[data-toast-id="${id}"]`);
            if (toastElement) {
                toastElement.classList.remove('show');

                setTimeout(() => {
                    this.toasts = this.toasts.filter(toast => toast.id !== id);
                }, 300);
            }
        },

        // Format date for display
        formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('id-ID', {
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        },

        // Format number with thousands separator
        formatNumber(number) {
            return new Intl.NumberFormat('id-ID').format(number);
        },

        // Get trend color for dashboard metrics
        getTrendColor(trend) {
            if (trend > 0) return 'text-red-600';
            if (trend < 0) return 'text-green-600';
            return 'text-gray-500';
        },

        // Set up global event listeners
        setupEventListeners() {
            // Load sidebar state from localStorage
            const savedCollapsed = localStorage.getItem('admin-sidebar-collapsed');
            if (savedCollapsed !== null) {
                this.sidebarCollapsed = savedCollapsed === 'true';
            }

            // Close mobile sidebar when clicking outside
            document.addEventListener('click', (e) => {
                if (this.sidebarOpen && !e.target.closest('.admin-sidebar') && !e.target.closest('[data-sidebar-toggle]')) {
                    this.sidebarOpen = false;
                }
            });

            // Handle window resize
            window.addEventListener('resize', () => {
                if (window.innerWidth >= 768) {
                    this.sidebarOpen = false;
                }
            });
        },

        // Handle API errors with user-friendly messages
        handleApiError(error) {
            console.error('API Error:', error);

            let message = 'Terjadi kesalahan. Silakan coba lagi.';

            if (error.status === 401) {
                message = 'Sesi Anda telah berakhir. Silakan login kembali.';
                window.location.href = '/login';
                return;
            } else if (error.status === 403) {
                message = 'Anda tidak memiliki izin untuk melakukan tindakan ini.';
            } else if (error.status === 422) {
                // Validation errors
                if (error.response.data.errors) {
                    const errors = Object.values(error.response.data.errors).flat();
                    message = errors.join(', ');
                } else {
                    message = error.response.data.message || message;
                }
            } else if (error.response.data.message) {
                message = error.response.data.message;
            }

            this.showToast(message, 'error');
        }
    };
}

// Make the simplified adminApp globally available for basic functionality
window.adminApp = adminApp;
