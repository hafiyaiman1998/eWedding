// Floating Hearts Animation (with preference checking)
function createFloatingHeart() {
    const heartsContainer = document.getElementById('floatingHearts');
    if (!heartsContainer) return;
    
    // Check if hearts are disabled by user preference
    if (document.body.classList.contains('hearts-disabled')) {
        return;
    }
    
    const heart = document.createElement('div');
    heart.className = 'heart';
    heart.innerHTML = Math.random() > 0.5 ? '💕' : '💖';
    heart.style.left = Math.random() * 100 + 'vw';
    
    // Use CSS variable for animation duration or fallback
    const animationSpeed = getComputedStyle(document.documentElement)
        .getPropertyValue('--animation-duration') || '0.3s';
    const duration = parseFloat(animationSpeed) * 1000;
    
    heart.style.animationDuration = (Math.random() * 3 + 3) + 's';
    heart.style.opacity = Math.random() * 0.3 + 0.1;
    heart.style.fontSize = (Math.random() * 10 + 15) + 'px';
    
    heartsContainer.appendChild(heart);
    
    setTimeout(() => {
        heart.remove();
    }, 6000);
}

// Mobile menu toggle with smooth animation
function initMobileMenu() {
    const menuToggle = document.getElementById('menuToggle');
    const sidebar = document.getElementById('sidebar');
    const mobileOverlay = document.getElementById('mobileOverlay');
    
    if (menuToggle && sidebar) {
        menuToggle.addEventListener('click', function(e) {
            e.stopPropagation();
            sidebar.classList.toggle('active');
            
            // Toggle mobile overlay
            if (mobileOverlay) {
                mobileOverlay.classList.toggle('active');
            }
            
            // Add pulse effect to button
            this.style.transform = 'scale(0.95)';
            setTimeout(() => {
                this.style.transform = 'scale(1)';
            }, 150);
            
            // Update aria attributes for accessibility
            const isExpanded = sidebar.classList.contains('active');
            this.setAttribute('aria-expanded', isExpanded);
        });
        
        // Set initial aria attributes
        menuToggle.setAttribute('aria-expanded', 'false');
        menuToggle.setAttribute('aria-label', 'Toggle navigation menu');
        
        // Close sidebar when clicking overlay
        if (mobileOverlay) {
            mobileOverlay.addEventListener('click', function() {
                sidebar.classList.remove('active');
                this.classList.remove('active');
                menuToggle.setAttribute('aria-expanded', 'false');
            });
        }
    }
}

// Close sidebar when clicking outside on mobile
function initSidebarClose() {
    document.addEventListener('click', function(event) {
        const sidebar = document.getElementById('sidebar');
        const menuToggle = document.getElementById('menuToggle');
        const mobileOverlay = document.getElementById('mobileOverlay');
        
        if (window.innerWidth <= 768 && 
            sidebar && menuToggle &&
            !sidebar.contains(event.target) && 
            !menuToggle.contains(event.target)) {
            sidebar.classList.remove('active');
            menuToggle.setAttribute('aria-expanded', 'false');
            if (mobileOverlay) {
                mobileOverlay.classList.remove('active');
            }
        }
    });
    
    // Close sidebar on window resize if switching to desktop
    window.addEventListener('resize', function() {
        const sidebar = document.getElementById('sidebar');
        const menuToggle = document.getElementById('menuToggle');
        const mobileOverlay = document.getElementById('mobileOverlay');
        
        if (window.innerWidth > 768 && sidebar) {
            sidebar.classList.remove('active');
            if (menuToggle) {
                menuToggle.setAttribute('aria-expanded', 'false');
            }
            if (mobileOverlay) {
                mobileOverlay.classList.remove('active');
            }
        }
    });
}

// Enhanced search functionality
function initSearch() {
    const searchInput = document.querySelector('.search-input');
    if (searchInput) {
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                // Add sparkle effect
                this.style.background = 'linear-gradient(45deg, rgba(255,107,157,0.1), rgba(196,69,105,0.1))';
                setTimeout(() => {
                    this.style.background = 'none';
                }, 500);
                alert('✨ Searching through love stories and creative magic...');
            }
        });
    }
}

// Enhanced navigation with smooth transitions
function initNavigation() {
    document.querySelectorAll('.nav-link').forEach(link => {
        link.addEventListener('click', function(e) {
            // Don't prevent default - let the navigation happen
            // Add ripple effect without preventing navigation
            const ripple = document.createElement('div');
            ripple.style.position = 'absolute';
            ripple.style.borderRadius = '50%';
            ripple.style.background = 'rgba(255,255,255,0.6)';
            ripple.style.transform = 'scale(0)';
            ripple.style.animation = 'ripple 0.6s linear';
            ripple.style.left = '50%';
            ripple.style.top = '50%';
            ripple.style.pointerEvents = 'none';
            this.appendChild(ripple);
            
            setTimeout(() => {
                ripple.remove();
            }, 600);
        });
    });
}

// Creative notification system
function initNotifications() {
    const notificationBtn = document.querySelector('.header-icon');
    if (notificationBtn) {
        notificationBtn.addEventListener('click', function() {
            this.style.transform = 'scale(1.2) rotate(15deg)';
            setTimeout(() => {
                this.style.transform = 'scale(1) rotate(0deg)';
            }, 200);
            alert('🔔 5 new love stories await your creative touch!');
        });
    }
}

// Enhanced user profile interaction with dropdown
function initUserProfile() {
    const userProfile = document.getElementById('userProfile');
    const userDropdown = document.getElementById('userDropdown');
    
    if (userProfile && userDropdown) {
        userProfile.addEventListener('click', function(e) {
            e.stopPropagation();
            this.classList.toggle('active');
        });
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!userProfile.contains(e.target)) {
                userProfile.classList.remove('active');
            }
        });
        
        // Prevent dropdown from closing when clicking inside it
        userDropdown.addEventListener('click', function(e) {
            e.stopPropagation();
        });
        
        // Handle dropdown item clicks
        const dropdownItems = userDropdown.querySelectorAll('.dropdown-item:not(.logout-item)');
        dropdownItems.forEach(item => {
            item.addEventListener('click', function(e) {
                e.preventDefault();
                const itemText = this.querySelector('span').textContent;
                showAlert(`✨ Opening ${itemText}...`);
                userProfile.classList.remove('active');
            });
        });
    }
}

// Creative table interactions
function initTableActions() {
    document.querySelectorAll('.action-icon').forEach(icon => {
        icon.addEventListener('click', function() {
            const action = this.classList.contains('view') ? 'Viewing' : 
                         this.classList.contains('edit') ? 'Editing' : 'Removing';
            
            this.style.transform = 'scale(1.3) rotate(10deg)';
            setTimeout(() => {
                this.style.transform = 'scale(1) rotate(0deg)';
            }, 200);
            
            if (action === 'Removing') {
                if (confirm('💔 Are you sure you want to remove this love story?')) {
                    alert('💔 Love story archived with care');
                }
            } else {
                alert(`✨ ${action} this beautiful love story...`);
            }
        });
    });
}

// Stats cards animation on load
function initStatsAnimation() {
    const statCards = document.querySelectorAll('.stat-card');
    statCards.forEach((card, index) => {
        setTimeout(() => {
            card.style.transform = 'translateY(0) scale(1)';
            card.style.opacity = '1';
        }, index * 150);
    });
}

// Enhanced button interactions
function initButtonEffects() {
    document.querySelectorAll('.btn:not([type="submit"])').forEach(btn => {
        // Only add sparkle effects to decorative buttons - no alerts
        const isDecorativeButton = !btn.closest('form') && 
                                  !btn.href && 
                                  !btn.hasAttribute('onclick') && 
                                  !btn.textContent.toLowerCase().includes('save') &&
                                  !btn.textContent.toLowerCase().includes('submit') &&
                                  !btn.textContent.toLowerCase().includes('delete') &&
                                  !btn.textContent.toLowerCase().includes('edit') &&
                                  !btn.textContent.toLowerCase().includes('create') &&
                                  !btn.textContent.toLowerCase().includes('update') &&
                                  !btn.closest('.settings-section') &&
                                  !btn.closest('.section-header') &&
                                  !btn.id &&
                                  !btn.className.includes('primary') &&
                                  !btn.className.includes('success') &&
                                  !btn.className.includes('warning') &&
                                  !btn.className.includes('danger');
        
        if (isDecorativeButton) {
            btn.addEventListener('click', function(e) {
                // Only add sparkle effect, no alert
                const sparkles = ['✨', '💫', '⭐', '🌟'];
                const sparkle = document.createElement('span');
                sparkle.innerHTML = sparkles[Math.floor(Math.random() * sparkles.length)];
                sparkle.style.position = 'absolute';
                sparkle.style.left = e.offsetX + 'px';
                sparkle.style.top = e.offsetY + 'px';
                sparkle.style.pointerEvents = 'none';
                sparkle.style.animation = 'sparkleUp 1s ease-out forwards';
                
                this.appendChild(sparkle);
                
                setTimeout(() => {
                    sparkle.remove();
                }, 1000);
            });
        }
    });
}

// Logout functionality
function initLogout() {
    const logoutBtn = document.querySelector('.logout-btn');
    if (logoutBtn) {
        logoutBtn.addEventListener('click', function(e) {
            if (!confirm('Are you sure you want to logout from the admin dashboard?')) {
                e.preventDefault();
            }
        });
    }
}

// Initialize all functionality when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Create floating hearts periodically
    setInterval(createFloatingHeart, 2000);
    
    // Initialize all modules
    initMobileMenu();
    initSidebarClose();
    initSearch();
    initNavigation();
    initNotifications();
    initUserProfile();
    initTableActions();
    initStatsAnimation();
    initButtonEffects();
    initLogout();
});

// Additional helper functions
function showAlert(message, type = 'info') {
    // You can enhance this with a custom toast notification system
    alert(message);
}

function animateElement(element, animation) {
    element.style.animation = animation;
    element.addEventListener('animationend', function() {
        element.style.animation = '';
    }, { once: true });
}

// Export functions for use in other files if needed
window.eWeddingCardAdmin = {
    createFloatingHeart,
    showAlert,
    animateElement
}; 