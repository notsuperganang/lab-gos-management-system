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