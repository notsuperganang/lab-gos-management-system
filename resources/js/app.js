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