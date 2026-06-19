<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Dashboard') - eWeddingCard</title>
    
    <!-- External CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Dancing+Script:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
    
    <!-- Main CSS -->
    <link href="{{ asset('css/main.css') }}" rel="stylesheet">
    
    <!-- Additional CSS -->
    @yield('additional_css')
    
    <style>
    .nav-badge {
        background: #e53e3e;
        color: white;
        font-size: 10px;
        padding: 2px 6px;
        border-radius: 10px;
        margin-left: 8px;
        font-weight: 600;
        min-width: 18px;
        text-align: center;
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0% { opacity: 1; }
        50% { opacity: 0.7; }
        100% { opacity: 1; }
    }
    </style>
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
                <div class="logo-subtitle">Creative Studio</div>
            </div>
            
            <nav class="sidebar-nav">
                <div class="nav-item">
                    <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>Dashboard</span>
                    </a>
                </div>
                
                <div class="nav-category">👥 Client Management</div>
                <div class="nav-item">
                    <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                        <i class="fas fa-users"></i>
                        <span>All Clients</span>
                    </a>
                </div>
                <div class="nav-item">
                    <a href="{{ route('admin.users.create') }}" class="nav-link {{ request()->routeIs('admin.users.create') ? 'active' : '' }}">
                        <i class="fas fa-user-plus"></i>
                        <span>Add New Client</span>
                    </a>
                </div>
                
                <div class="nav-category">🎨 Template Management</div>
                <div class="nav-item">
                    <a href="{{ route('admin.templates.index') }}" class="nav-link {{ request()->routeIs('admin.templates.*') ? 'active' : '' }}">
                        <i class="fas fa-th-large"></i>
                        <span>All Templates</span>
                    </a>
                </div>
                <div class="nav-item">
                    <a href="{{ route('admin.templates.create') }}" class="nav-link {{ request()->routeIs('admin.templates.create') ? 'active' : '' }}">
                        <i class="fas fa-plus-circle"></i>
                        <span>Create Template</span>
                    </a>
                </div>
                <div class="nav-item">
                    <a href="{{ route('admin.templates.malaysian') }}" class="nav-link {{ request()->routeIs('admin.templates.malaysian') ? 'active' : '' }}">
                        <i class="fas fa-star-and-crescent"></i>
                        <span>Malaysian Designs</span>
                    </a>
                </div>
                
                <div class="nav-category">💌 Wedding Cards</div>
                <div class="nav-item">
                    <a href="{{ route('admin.cards.index') }}" class="nav-link {{ request()->routeIs('admin.cards.index') ? 'active' : '' }}">
                        <i class="fas fa-heart"></i>
                        <span>All Wedding Cards</span>
                    </a>
                </div>
                <div class="nav-item">
                    <a href="{{ route('admin.cards.pending') }}" class="nav-link {{ request()->routeIs('admin.cards.pending') ? 'active' : '' }}">
                        <i class="fas fa-clock"></i>
                        <span>Pending Approval</span>
                        @php
                            $pendingCount = \App\Models\WeddingCard::pendingApproval()->count();
                        @endphp
                        @if($pendingCount > 0)
                            <span class="nav-badge">{{ $pendingCount }}</span>
                        @endif
                    </a>
                </div>
                <div class="nav-item">
                    <a href="{{ route('admin.cards.published') }}" class="nav-link {{ request()->routeIs('admin.cards.published') ? 'active' : '' }}">
                        <i class="fas fa-globe"></i>
                        <span>Published Cards</span>
                    </a>
                </div>
                
                <div class="nav-category">📊 System Overview</div>
                <div class="nav-item">
                    <a href="{{ route('admin.analytics') }}" class="nav-link {{ request()->routeIs('admin.analytics') ? 'active' : '' }}">
                        <i class="fas fa-chart-line"></i>
                        <span>Analytics</span>
                    </a>
                </div>
                <div class="nav-item">
                    <a href="{{ route('admin.activity') }}" class="nav-link {{ request()->routeIs('admin.activity') ? 'active' : '' }}">
                        <i class="fas fa-activity"></i>
                        <span>System Activity</span>
                    </a>
                </div>
                
                <div class="nav-category">⚙️ Settings</div>
                <div class="nav-item">
                    <a href="{{ route('admin.settings') }}" class="nav-link {{ request()->routeIs('admin.settings') ? 'active' : '' }}">
                        <i class="fas fa-cog"></i>
                        <span>System Settings</span>
                    </a>
                </div>
                <div class="nav-item">
                    <a href="{{ route('admin.profile') }}" class="nav-link {{ request()->routeIs('admin.profile') ? 'active' : '' }}">
                        <i class="fas fa-user-shield"></i>
                        <span>Admin Profile</span>
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
                        <input type="text" class="search-input" placeholder="Search clients, templates, or wedding cards...">
                        <button class="search-btn">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
            </div>
            
            <div class="header-right">
                <button class="header-icon">
                    <i class="fas fa-bell"></i>
                    <span class="notification-badge">5</span>
                </button>
                
                <button class="header-icon">
                    <i class="fas fa-heart"></i>
                </button>
                
                <button class="header-icon">
                    <i class="fas fa-palette"></i>
                </button>
                
                <div class="user-profile" id="userProfile">
                    <div class="user-avatar">{{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}</div>
                    <div class="user-info">
                        <div class="user-name">{{ Auth::user()->name ?? 'Admin' }}</div>
                        <div class="user-role">{{ Auth::user()->type ?? 'Administrator' }}</div>
                    </div>
                    <i class="fas fa-chevron-down dropdown-arrow" id="dropdownArrow"></i>
                    
                    <!-- Dropdown Menu -->
                    <div class="user-dropdown" id="userDropdown">
                        <div class="dropdown-header">
                            <div class="dropdown-avatar">{{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}</div>
                            <div class="dropdown-info">
                                <div class="dropdown-name">{{ Auth::user()->name ?? 'Admin' }}</div>
                                <div class="dropdown-email">{{ Auth::user()->email ?? 'admin@example.com' }}</div>
                            </div>
                        </div>
                        <div class="dropdown-divider"></div>
                        <div class="dropdown-menu">
                            <a href="#" class="dropdown-item">
                                <i class="fas fa-user-cog"></i>
                                <span>Account Settings</span>
                            </a>
                            <a href="{{ route('admin.preferences.index') }}" class="dropdown-item">
                                <i class="fas fa-palette"></i>
                                <span>Preferences</span>
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

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.1/dist/sweetalert2.all.min.js"></script>
    
    <!-- Main JavaScript -->
    <script src="{{ asset('js/main.js') }}"></script>
    
    <!-- Admin Delete Operations -->
    <script src="{{ asset('js/admin-delete.js') }}"></script>
    
    <!-- Preferences Manager -->
    <script src="{{ asset('js/preferences.js') }}"></script>
    
    <!-- Additional JavaScript -->
    @yield('additional_js')
</body>
</html> 