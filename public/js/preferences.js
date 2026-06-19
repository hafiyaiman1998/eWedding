/**
 * Preferences Manager
 * Handles dynamic application of user preferences
 */

class PreferencesManager {
    constructor() {
        this.preferences = null;
        this.cssVariables = {};
        this.init();
    }

    async init() {
        await this.loadPreferences();
        this.applyPreferences();
        this.setupEventListeners();
    }

    async loadPreferences() {
        try {
            // Determine the correct endpoint based on current URL
            const endpoint = window.location.pathname.includes('/admin/') 
                ? '/admin/preferences/json' 
                : '/user/preferences/json';
            
            const response = await fetch(endpoint);
            const data = await response.json();
            
            this.preferences = data.preferences;
            this.cssVariables = data.css_variables;
        } catch (error) {
            console.warn('Could not load user preferences:', error);
            this.preferences = this.getDefaultPreferences();
        }
    }

    getDefaultPreferences() {
        return {
            theme: 'light',
            color_scheme: 'default',
            sidebar_collapsed: false,
            layout_density: 'comfortable',
            font_size: 'medium',
            floating_hearts_enabled: true,
            animations_enabled: true,
            animation_speed: 'normal'
        };
    }

    applyPreferences() {
        if (!this.preferences) return;

        // Apply CSS variables to root
        const root = document.documentElement;
        Object.entries(this.cssVariables).forEach(([property, value]) => {
            root.style.setProperty(property, value);
        });

        // Apply theme class
        document.body.classList.remove('theme-light', 'theme-dark', 'theme-auto');
        document.body.classList.add(`theme-${this.preferences.theme}`);

        // Apply color scheme class
        document.body.classList.remove('scheme-default', 'scheme-pink', 'scheme-purple', 'scheme-blue', 'scheme-green', 'scheme-orange');
        document.body.classList.add(`scheme-${this.preferences.color_scheme}`);

        // Apply sidebar state
        const sidebar = document.getElementById('sidebar');
        if (sidebar && this.preferences.sidebar_collapsed) {
            sidebar.classList.add('collapsed');
        }

        // Handle floating hearts
        if (!this.preferences.floating_hearts_enabled) {
            this.disableFloatingHearts();
            document.body.classList.add('hearts-disabled');
        } else {
            document.body.classList.remove('hearts-disabled');
        }

        // Handle animations
        if (!this.preferences.animations_enabled) {
            this.disableAnimations();
            document.body.classList.add('animations-disabled');
        } else {
            document.body.classList.remove('animations-disabled');
        }

        // Apply animation speed
        root.style.setProperty('--animation-duration', this.getAnimationDuration());
        
        // Apply font size
        root.style.setProperty('--base-font-size', this.getFontSize());
        
        // Apply layout density
        const spacing = this.getLayoutSpacing();
        root.style.setProperty('--layout-padding', spacing.padding);
        root.style.setProperty('--element-spacing', spacing.element);
    }

    getAnimationDuration() {
        switch (this.preferences.animation_speed) {
            case 'slow': return '0.5s';
            case 'fast': return '0.1s';
            default: return '0.3s';
        }
    }

    getFontSize() {
        switch (this.preferences.font_size) {
            case 'small': return '14px';
            case 'large': return '18px';
            default: return '16px';
        }
    }

    getLayoutSpacing() {
        switch (this.preferences.layout_density) {
            case 'compact': 
                return { padding: '15px', element: '10px' };
            case 'spacious': 
                return { padding: '40px', element: '30px' };
            default: 
                return { padding: '30px', element: '20px' };
        }
    }

    disableFloatingHearts() {
        const heartsContainer = document.getElementById('floatingHearts');
        if (heartsContainer) {
            heartsContainer.style.display = 'none';
        }
    }

    disableAnimations() {
        const style = document.createElement('style');
        style.textContent = `
            *, *::before, *::after {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
                scroll-behavior: auto !important;
            }
        `;
        document.head.appendChild(style);
    }

    setupEventListeners() {
        // Listen for preference updates
        document.addEventListener('preferencesUpdated', () => {
            this.init();
        });

        // Handle theme auto detection
        if (this.preferences && this.preferences.theme === 'auto') {
            const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
            mediaQuery.addEventListener('change', (e) => {
                document.body.classList.remove('theme-light', 'theme-dark');
                document.body.classList.add(e.matches ? 'theme-dark' : 'theme-light');
            });
            
            // Apply initial auto theme
            document.body.classList.remove('theme-light', 'theme-dark');
            document.body.classList.add(mediaQuery.matches ? 'theme-dark' : 'theme-light');
        }
    }

    // Method to trigger preference reload (call after saving preferences)
    static triggerUpdate() {
        document.dispatchEvent(new CustomEvent('preferencesUpdated'));
    }
}

// Color scheme definitions for CSS variables
const COLOR_SCHEMES = {
    default: {
        primary: '#e91e63',
        light: '#f8bbd9',
        dark: '#c2185b'
    },
    pink: {
        primary: '#e91e63',
        light: '#f8bbd9',
        dark: '#c2185b'
    },
    purple: {
        primary: '#9c27b0',
        light: '#e1bee7',
        dark: '#7b1fa2'
    },
    blue: {
        primary: '#2196f3',
        light: '#bbdefb',
        dark: '#1976d2'
    },
    green: {
        primary: '#4caf50',
        light: '#c8e6c9',
        dark: '#388e3c'
    },
    orange: {
        primary: '#ff9800',
        light: '#ffe0b2',
        dark: '#f57c00'
    }
};

// Initialize preferences manager when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    new PreferencesManager();
});

// Utility functions for preference forms
window.PreferencesUI = {
    updateColorScheme(scheme) {
        const colors = COLOR_SCHEMES[scheme] || COLOR_SCHEMES.default;
        const root = document.documentElement;
        
        root.style.setProperty('--accent-color', colors.primary);
        root.style.setProperty('--accent-light', colors.light);
        root.style.setProperty('--accent-dark', colors.dark);
        
        // Update live preview if it exists
        const preview = document.querySelector('.live-preview');
        if (preview) {
            preview.style.background = `linear-gradient(135deg, ${colors.primary} 0%, ${colors.light} 100%)`;
        }
    },

    updateFontSize(size) {
        const root = document.documentElement;
        const fontSize = size === 'small' ? '14px' : size === 'large' ? '18px' : '16px';
        root.style.setProperty('--base-font-size', fontSize);
    },

    updateTheme(theme) {
        document.body.classList.remove('theme-light', 'theme-dark', 'theme-auto');
        document.body.classList.add(`theme-${theme}`);
        
        if (theme === 'auto') {
            const isDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            document.body.classList.remove('theme-auto');
            document.body.classList.add(isDark ? 'theme-dark' : 'theme-light');
        }
    },

    toggleHeartDemo() {
        const checkbox = document.querySelector('[name="floating_hearts_enabled"]');
        const demo = document.getElementById('heartDemo');
        if (demo && checkbox) {
            demo.style.display = checkbox.checked ? 'flex' : 'none';
        }
    }
}; 