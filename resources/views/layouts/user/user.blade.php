<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'My Wedding Studio') - eWeddingCard</title>
    
    <!-- External CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Dancing+Script:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
    
    <!-- Main CSS -->
    <link href="{{ asset('css/main.css') }}" rel="stylesheet">
    
    <style>
        /* Dynamic CSS Variables for Preferences */
        :root {
            --accent-color: #e91e63;
            --accent-light: #f8bbd9;
            --accent-dark: #c2185b;
            --bg-primary: #ffffff;
            --bg-secondary: #f8f9fa;
            --text-primary: #1f2937;
            --text-secondary: #6b7280;
            --border-color: #e5e7eb;
            --card-bg: #ffffff;
            --input-bg: #ffffff;
            --base-font-size: 16px;
            --layout-padding: 30px;
            --element-spacing: 20px;
            --animation-duration: 0.3s;
        }

        /* Dark theme overrides */
        body.theme-dark {
            --bg-primary: #1a1a1a;
            --bg-secondary: #2d2d2d;
            --text-primary: #ffffff;
            --text-secondary: #cccccc;
            --card-bg: #2d2d2d;
            --input-bg: #333333;
            --border-color: #444444;
        }

        /* Color scheme classes */
        body.scheme-pink {
            --accent-color: #e91e63;
            --accent-light: #f8bbd9;
            --accent-dark: #c2185b;
        }

        body.scheme-purple {
            --accent-color: #9c27b0;
            --accent-light: #e1bee7;
            --accent-dark: #7b1fa2;
        }

        body.scheme-blue {
            --accent-color: #2196f3;
            --accent-light: #bbdefb;
            --accent-dark: #1976d2;
        }

        body.scheme-green {
            --accent-color: #4caf50;
            --accent-light: #c8e6c9;
            --accent-dark: #388e3c;
        }

        body.scheme-orange {
            --accent-color: #ff9800;
            --accent-light: #ffe0b2;
            --accent-dark: #f57c00;
        }

        /* Font size adjustments */
        * {
            font-size: var(--base-font-size);
        }

        /* Layout density adjustments */
        .main-content {
            padding: var(--layout-padding);
        }

        .preference-card,
        .content-area,
        .action-card {
            padding: var(--layout-padding);
            margin-bottom: var(--element-spacing);
        }

        /* Sidebar collapsed state */
        .sidebar.collapsed {
            margin-left: -320px;
        }

        /* Animation control */
        * {
            transition-duration: var(--animation-duration);
        }

        /* Disable animations if preference is set */
        body.animations-disabled *,
        body.animations-disabled *::before,
        body.animations-disabled *::after {
            animation-duration: 0.01ms !important;
            animation-iteration-count: 1 !important;
            transition-duration: 0.01ms !important;
            scroll-behavior: auto !important;
        }

        /* User-specific layout adjustments - use admin container structure */
        
        /* Override sidebar for user layout */
        .sidebar {
            grid-area: sidebar;
        }
        
        /* Override header for user layout */
        .header {
            grid-area: header;
        }
        
        /* Override main content for user layout */
        .main-content {
            grid-area: main;
            padding: 30px;
            overflow-y: auto;
        }
        
        /* Override footer for user layout */
        .footer {
            grid-area: footer;
        }
        
        /* User-specific navigation category styling */
        .nav-category {
            padding: 15px 30px 10px;
            font-size: 12px;
            font-weight: 600;
            color: var(--text-secondary);
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        /* Mobile responsive adjustments are handled by main.css */
    </style>
    
    <!-- Apply User Preferences Server-Side -->
    @php
        try {
            $userPreferences = Auth::user()->getPreferences();
            $cssVariables = $userPreferences->getCssVariables();
        } catch (Exception $e) {
            $userPreferences = null;
            $cssVariables = [];
        }
    @endphp
    
    @if($userPreferences)
    <style>
        :root {
            @foreach($cssVariables as $property => $value)
                {{ $property }}: {{ $value }};
            @endforeach
        }
        
        @if(!$userPreferences->floating_hearts_enabled)
        .floating-hearts {
            display: none !important;
        }
        @endif
        
        @if(!$userPreferences->animations_enabled)
        *, *::before, *::after {
            animation-duration: 0.01ms !important;
            animation-iteration-count: 1 !important;
            transition-duration: 0.01ms !important;
        }
        @endif
    </style>
    
    <script>
        // Apply theme and preference classes on body
        document.addEventListener('DOMContentLoaded', function() {
            document.body.classList.add('theme-{{ $userPreferences->theme }}');
            document.body.classList.add('scheme-{{ $userPreferences->color_scheme }}');
            
            @if(!$userPreferences->floating_hearts_enabled)
            document.body.classList.add('hearts-disabled');
            @endif
            
            @if(!$userPreferences->animations_enabled)
            document.body.classList.add('animations-disabled');
            @endif
            
            @if($userPreferences->sidebar_collapsed)
            const sidebar = document.getElementById('sidebar');
            if (sidebar) sidebar.classList.add('collapsed');
            @endif
        });
    </script>
    @endif
    
    <!-- Additional CSS -->
    @yield('additional_css')
</head>
<body>
    <!-- Floating Hearts Animation -->
    <div class="floating-hearts" id="floatingHearts"></div>

    <!-- Mobile Overlay -->
    <div class="mobile-overlay" id="mobileOverlay"></div>

    <div class="admin-container">
        <!-- Sidebar -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <div class="logo">eWeddingCard</div>
                <div class="logo-subtitle">Your Wedding Studio</div>
            </div>
            
            <nav class="sidebar-nav">
                <div class="nav-item">
                    <a href="{{ route('user.dashboard') }}" class="nav-link {{ request()->routeIs('user.dashboard') ? 'active' : '' }}">
                        <i class="fas fa-home"></i>
                        <span>Dashboard</span>
                    </a>
                </div>
                
                <div class="nav-category">💕 My Wedding Cards</div>
                <div class="nav-item">
                    <a href="{{ route('user.cards.index') }}" class="nav-link {{ request()->routeIs('user.cards.index') ? 'active' : '' }}">
                        <i class="fas fa-heart"></i>
                        <span>All My Cards</span>
                    </a>
                </div>
                <div class="nav-item">
                    <a href="{{ route('user.cards.create') }}" class="nav-link {{ request()->routeIs('user.cards.create') ? 'active' : '' }}">
                        <i class="fas fa-plus-circle"></i>
                        <span>Create New Card</span>
                    </a>
                </div>
                
                <div class="nav-category">🎨 Templates</div>
                <div class="nav-item">
                    <a href="{{ route('user.cards.create') }}" class="nav-link">
                        <i class="fas fa-th-large"></i>
                        <span>Browse Templates</span>
                    </a>
                </div>
                
                <div class="nav-category">📤 Share & Connect</div>
                <div class="nav-item">
                    <a href="{{ route('user.cards.index') }}#sharing" class="nav-link">
                        <i class="fas fa-share-alt"></i>
                        <span>Share My Cards</span>
                    </a>
                </div>
                <div class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="fas fa-download"></i>
                        <span>Export Guest List</span>
                    </a>
                </div>
                
                <div class="nav-category">📊 Insights</div>
                <div class="nav-item">
                    <a href="{{ route('user.cards.index') }}#analytics" class="nav-link">
                        <i class="fas fa-chart-line"></i>
                        <span>View Analytics</span>
                    </a>
                </div>
                <div class="nav-item">
                    <a href="{{ route('user.cards.index') }}#rsvps" class="nav-link">
                        <i class="fas fa-users"></i>
                        <span>Manage RSVPs</span>
                    </a>
                </div>
            </nav>
        </aside>

        <!-- Header -->
        <header class="header">
            <div class="header-left">
                <button class="menu-toggle" id="menuToggle">
                    <i class="fas fa-bars"></i>
                </button>
                <div class="search-container">
                    <div class="search-wrapper">
                        <input type="text" class="search-input" placeholder="Search your wedding cards...">
                        <button class="search-btn">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
            </div>
            
            <div class="header-right">
                <button class="header-icon">
                    <i class="fas fa-bell"></i>
                    <span class="notification-badge">3</span>
                </button>
                
                <button class="header-icon">
                    <i class="fas fa-heart"></i>
                </button>
                
                <button class="header-icon">
                    <i class="fas fa-palette"></i>
                </button>
                
                <div class="user-profile" id="userProfile">
                    <div class="user-avatar">{{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}</div>
                    <div class="user-info">
                        <div class="user-name">{{ Auth::user()->name ?? 'User' }}</div>
                        <div class="user-role">{{ Auth::user()->type ?? 'Creative User' }}</div>
                    </div>
                    <i class="fas fa-chevron-down dropdown-arrow" id="dropdownArrow"></i>
                    
                    <!-- Dropdown Menu -->
                    <div class="user-dropdown" id="userDropdown">
                        <div class="dropdown-header">
                            <div class="dropdown-avatar">{{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}</div>
                            <div class="dropdown-info">
                                <div class="dropdown-name">{{ Auth::user()->name ?? 'User' }}</div>
                                <div class="dropdown-email">{{ Auth::user()->email ?? 'user@example.com' }}</div>
                            </div>
                        </div>
                        <div class="dropdown-divider"></div>
                        <div class="dropdown-menu">
                            <a href="#" class="dropdown-item">
                                <i class="fas fa-user-cog"></i>
                                <span>Profile Settings</span>
                            </a>
                            <a href="{{ route('user.preferences.index') }}" class="dropdown-item">
                                <i class="fas fa-palette"></i>
                                <span>Design Preferences</span>
                            </a>
                            <a href="#" class="dropdown-item">
                                <i class="fas fa-bell"></i>
                                <span>Notifications</span>
                            </a>
                            <a href="#" class="dropdown-item">
                                <i class="fas fa-question-circle"></i>
                                <span>Help & Support</span>
                            </a>
                            <div class="dropdown-divider"></div>
                            <form method="POST" action="{{ route('logout') }}" class="dropdown-form">
                                @csrf
                                <button type="submit" class="dropdown-item logout-item">
                                    <i class="fas fa-sign-out-alt"></i>
                                    <span>Logout</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="main-content">
            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="footer">
            <div class="footer-left">
                Made with <span class="footer-heart">💕</span> by eWeddingCard Creative Studio © {{ date('Y') }}
            </div>
            <div class="footer-right">
                <a href="#">Love Stories</a>
                <a href="#">Design Terms</a>
                <a href="#">Creative Support</a>
                <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                    @csrf
                    <a href="#" onclick="event.preventDefault(); this.closest('form').submit();">Logout</a>
                </form>
            </div>
        </footer>
    </div>

    <!-- JavaScript -->
    <script>
        // Floating Hearts Animation
        function createFloatingHeart() {
            const heartsContainer = document.getElementById('floatingHearts');
            const heart = document.createElement('div');
            heart.className = 'heart';
            
            const hearts = ['💕', '💖', '💗', '💓', '💘', '💝'];
            heart.innerHTML = hearts[Math.floor(Math.random() * hearts.length)];
            
            heart.style.left = Math.random() * 100 + 'vw';
            heart.style.animationDuration = (Math.random() * 4 + 6) + 's';
            heart.style.opacity = Math.random() * 0.3 + 0.1;
            heart.style.fontSize = (Math.random() * 15 + 20) + 'px';
            
            heartsContainer.appendChild(heart);
            
            setTimeout(() => {
                heart.remove();
            }, 10000);
        }

        // Create hearts periodically
        setInterval(createFloatingHeart, 3000);

        // Mobile menu toggle with enhanced functionality
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

        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(event) {
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

        // User profile dropdown
        const userProfile = document.getElementById('userProfile');
        const userDropdown = document.getElementById('userDropdown');
        
        if (userProfile) {
            userProfile.addEventListener('click', function(e) {
                e.stopPropagation();
                this.classList.toggle('active');
            });
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function() {
            if (userProfile) {
                userProfile.classList.remove('active');
            }
        });

        // Prevent dropdown from closing when clicking inside it
        if (userDropdown) {
            userDropdown.addEventListener('click', function(e) {
                e.stopPropagation();
            });
        }
    </script>

    <!-- SweetAlert for delete confirmations -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('js/admin-delete.js') }}"></script>
    
    <!-- Preferences Manager -->
    <script src="{{ asset('js/preferences.js') }}"></script>

    <!-- Additional JavaScript -->
    @yield('additional_js')
</body>
</html> 