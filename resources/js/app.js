import './bootstrap';
import Alpine from 'alpinejs';

// Custom scroll animation system
Alpine.store('scrollAnimations', {
    elements: new Map(),
    scrollY: 0,
    isScrolling: false,

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

        // Initial check
        setTimeout(() => this.checkElements(), 100);
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

// Global Alpine
window.Alpine = Alpine;

// Start Alpine
Alpine.start();

// Initialize scroll animations after Alpine starts
document.addEventListener('alpine:init', () => {
    Alpine.store('scrollAnimations').init();
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

    // Helper method for making authenticated API requests
    async request(endpoint, options = {}) {
        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        const url = `${this.baseURL}${endpoint}`;

        const config = {
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': token,
                ...options.headers
            },
            credentials: 'same-origin',
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
    }
};

// Expose AdminAPI to global window object
window.AdminAPI = AdminAPI;

// Admin App Alpine Component
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

        // Initialize admin app
        init() {
            this.loadUser();
            this.loadNotifications();
            this.setupEventListeners();
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

        // Load notifications
        async loadNotifications() {
            try {
                const response = await AdminAPI.getNotifications();
                if (response.success) {
                    this.notifications = response.data.slice(0, 5);
                    this.unreadNotifications = response.data.filter(n => !n.read_at).length;
                }
            } catch (error) {
                console.error('Failed to load notifications:', error);
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

// Dashboard Data Alpine Component
function dashboardData() {
    return {
        // Stats data
        stats: {
            totalRequests: 0,
            pendingApprovals: 0,
            activeEquipment: 0,
            completedThisMonth: 0
        },

        // Chart data
        chartData: {
            requestTrends: null,
            equipmentUsage: null,
            statusDistribution: null
        },

        // Recent activities
        recentActivities: [],

        // Quick stats changes
        statsChanges: {
            totalRequests: { value: 0, isPositive: true },
            pendingApprovals: { value: 0, isPositive: false },
            activeEquipment: { value: 0, isPositive: true },
            completedThisMonth: { value: 0, isPositive: true }
        },

        // Loading states
        loading: false,
        chartsLoaded: false,

        // Initialize dashboard
        async init() {
            this.loading = true;

            try {
                await this.loadDashboardData();
                await this.loadRecentActivities();

                this.$nextTick(() => {
                    this.initCharts();
                });
            } catch (error) {
                this.$root.handleApiError(error);
            } finally {
                this.loading = false;
            }
        },

        // Load dashboard statistics
        async loadDashboardData() {
            try {
                const response = await AdminAPI.getDashboardStats();

                if (response.success) {
                    const data = response.data;

                    // Update stats
                    this.stats = {
                        totalRequests: data.total_requests || 0,
                        pendingApprovals: data.pending_approvals || 0,
                        activeEquipment: data.active_equipment || 0,
                        completedThisMonth: data.completed_this_month || 0
                    };

                    // Update changes (if provided)
                    if (data.changes) {
                        this.statsChanges = data.changes;
                    }

                    // Store chart data
                    this.chartData = {
                        requestTrends: data.request_trends || null,
                        equipmentUsage: data.equipment_usage || null,
                        statusDistribution: data.status_distribution || null
                    };
                }
            } catch (error) {
                throw error;
            }
        },

        // Load recent activities
        async loadRecentActivities() {
            try {
                const response = await AdminAPI.getActivityLogs(1, 5);

                if (response.success) {
                    this.recentActivities = response.data.data || [];
                }
            } catch (error) {
                console.warn('Failed to load recent activities:', error);
                this.recentActivities = [];
            }
        },

        // Initialize charts
        initCharts() {
            if (typeof Chart === 'undefined') {
                console.warn('Chart.js not loaded. Charts will not be displayed.');
                return;
            }

            try {
                // Request Trends Chart
                if (this.chartData.requestTrends) {
                    this.initRequestTrendsChart();
                }

                // Equipment Usage Chart
                if (this.chartData.equipmentUsage) {
                    this.initEquipmentUsageChart();
                }

                // Status Distribution Chart
                if (this.chartData.statusDistribution) {
                    this.initStatusDistributionChart();
                }

                this.chartsLoaded = true;
            } catch (error) {
                console.error('Failed to initialize charts:', error);
            }
        },

        // Initialize request trends line chart
        initRequestTrendsChart() {
            const canvas = document.getElementById('requestTrendsChart');
            if (!canvas) return;

            const ctx = canvas.getContext('2d');
            const data = this.chartData.requestTrends;

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: data.labels || [],
                    datasets: [{
                        label: 'Total Requests',
                        data: data.values || [],
                        borderColor: '#1E40AF',
                        backgroundColor: 'rgba(30, 64, 175, 0.1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: '#1E40AF',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2,
                        pointRadius: 6,
                        pointHoverRadius: 8
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0, 0, 0, 0.1)'
                            }
                        },
                        x: {
                            grid: {
                                color: 'rgba(0, 0, 0, 0.1)'
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });
        },

        // Initialize equipment usage bar chart
        initEquipmentUsageChart() {
            const canvas = document.getElementById('equipmentUsageChart');
            if (!canvas) return;

            const ctx = canvas.getContext('2d');
            const data = this.chartData.equipmentUsage;

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: data.labels || [],
                    datasets: [{
                        label: 'Usage Count',
                        data: data.values || [],
                        backgroundColor: [
                            '#1E40AF',
                            '#10B981',
                            '#F59E0B',
                            '#EF4444',
                            '#8B5CF6'
                        ],
                        borderRadius: 8
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });
        },

        // Initialize status distribution doughnut chart
        initStatusDistributionChart() {
            const canvas = document.getElementById('statusDistributionChart');
            if (!canvas) return;

            const ctx = canvas.getContext('2d');
            const data = this.chartData.statusDistribution;

            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: data.labels || [],
                    datasets: [{
                        data: data.values || [],
                        backgroundColor: [
                            '#10B981',
                            '#F59E0B',
                            '#EF4444',
                            '#3B82F6',
                            '#8B5CF6'
                        ],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '60%',
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
        },

        // Refresh dashboard data
        async refreshDashboard() {
            this.loading = true;

            try {
                await this.loadDashboardData();
                await this.loadRecentActivities();

                // Reinitialize charts if needed
                if (this.chartsLoaded) {
                    this.destroyCharts();
                    this.$nextTick(() => {
                        this.initCharts();
                    });
                }

                this.$root.showToast('Dashboard data refreshed', 'success');
            } catch (error) {
                this.$root.handleApiError(error);
            } finally {
                this.loading = false;
            }
        },

        // Destroy existing charts
        destroyCharts() {
            const chartCanvases = ['requestTrendsChart', 'equipmentUsageChart', 'statusDistributionChart'];

            chartCanvases.forEach(canvasId => {
                const canvas = document.getElementById(canvasId);
                if (canvas) {
                    const existingChart = Chart.getChart(canvas);
                    if (existingChart) {
                        existingChart.destroy();
                    }
                }
            });

            this.chartsLoaded = false;
        }
    };
}

// Make components globally available
window.adminApp = adminApp;
window.dashboardData = dashboardData;
