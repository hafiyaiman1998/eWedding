@extends('layouts.admin.admin')

@section('title', 'Admin Dashboard')

@section('content')
<div class="dashboard-content">
    <!-- Header with welcome message -->
    <div class="dashboard-header">
        <h1 class="dashboard-title">Welcome back, {{ Auth::user()->name }}!</h1>
        <p class="dashboard-subtitle">Here's what's happening with your eWeddingCard platform today.</p>
    </div>

    <!-- Statistics Cards -->
    <div class="stats-grid">
        <div class="stat-card stat-primary">
            <div class="stat-icon">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-content">
                <h3 class="stat-number">{{ $stats['total_clients'] }}</h3>
                <p class="stat-label">Total Clients</p>
            </div>
        </div>

        <div class="stat-card stat-success">
            <div class="stat-icon">
                <i class="fas fa-th-large"></i>
            </div>
            <div class="stat-content">
                <h3 class="stat-number">{{ $stats['total_templates'] }}</h3>
                <p class="stat-label">Design Templates</p>
            </div>
        </div>

        <div class="stat-card stat-info">
            <div class="stat-icon">
                <i class="fas fa-heart"></i>
            </div>
            <div class="stat-content">
                <h3 class="stat-number">{{ $stats['total_cards'] }}</h3>
                <p class="stat-label">Wedding Cards</p>
            </div>
        </div>

        <div class="stat-card stat-warning">
            <div class="stat-icon">
                <i class="fas fa-globe"></i>
            </div>
            <div class="stat-content">
                <h3 class="stat-number">{{ $stats['published_cards'] }}</h3>
                <p class="stat-label">Published Cards</p>
            </div>
        </div>

        <div class="stat-card stat-purple">
            <div class="stat-icon">
                <i class="fas fa-star-and-crescent"></i>
            </div>
            <div class="stat-content">
                <h3 class="stat-number">{{ $stats['malaysian_templates'] }}</h3>
                <p class="stat-label">Malaysian Designs</p>
            </div>
        </div>

        <div class="stat-card stat-admin">
            <div class="stat-icon">
                <i class="fas fa-user-shield"></i>
            </div>
            <div class="stat-content">
                <h3 class="stat-number">{{ $stats['total_admins'] }}</h3>
                <p class="stat-label">Admin Users</p>
            </div>
        </div>
    </div>

    <!-- Recent Activity Section -->
    <div class="dashboard-grid">
        <!-- Recent Clients -->
        <div class="dashboard-card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-users-plus"></i>
                    Recent Clients
                </h3>
                <a href="{{ route('admin.users.index') }}" class="card-link">View All</a>
            </div>
            <div class="card-content">
                @if($stats['recent_clients']->count() > 0)
                    <div class="activity-list">
                        @foreach($stats['recent_clients'] as $client)
                            <div class="activity-item">
                                <div class="activity-avatar">
                                    {{ strtoupper(substr($client->name, 0, 1)) }}
                                </div>
                                <div class="activity-info">
                                    <h4 class="activity-name">{{ $client->name }}</h4>
                                    <p class="activity-detail">{{ $client->email }}</p>
                                    <small class="activity-time">{{ $client->created_at->diffForHumans() }}</small>
                                </div>
                                <div class="activity-badge">
                                    {{ $client->weddingCards->count() }} cards
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="empty-state">
                        <i class="fas fa-user-plus"></i>
                        <p>No clients registered yet</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Recent Wedding Cards -->
        <div class="dashboard-card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-heart"></i>
                    Recent Wedding Cards
                </h3>
                <a href="{{ route('admin.cards.index') }}" class="card-link">View All</a>
            </div>
            <div class="card-content">
                @if($stats['recent_cards']->count() > 0)
                    <div class="activity-list">
                        @foreach($stats['recent_cards'] as $card)
                            <div class="activity-item">
                                <div class="activity-avatar card-avatar">
                                    <i class="fas fa-heart"></i>
                                </div>
                                <div class="activity-info">
                                    <h4 class="activity-name">{{ $card->title }}</h4>
                                    <p class="activity-detail">by {{ $card->user->name }}</p>
                                    <small class="activity-time">{{ $card->created_at->diffForHumans() }}</small>
                                </div>
                                <div class="activity-badge {{ $card->is_published ? 'badge-success' : 'badge-draft' }}">
                                    {{ $card->is_published ? 'Published' : 'Draft' }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="empty-state">
                        <i class="fas fa-heart"></i>
                        <p>No wedding cards created yet</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="quick-actions">
        <h3 class="section-title">Quick Actions</h3>
        <div class="action-grid">
            <a href="{{ route('admin.users.create') }}" class="action-card">
                <div class="action-icon">
                    <i class="fas fa-user-plus"></i>
                </div>
                <div class="action-content">
                    <h4>Add New Client</h4>
                    <p>Create a new client account</p>
                </div>
            </a>

            <a href="{{ route('admin.templates.create') }}" class="action-card">
                <div class="action-icon">
                    <i class="fas fa-plus-circle"></i>
                </div>
                <div class="action-content">
                    <h4>Create Template</h4>
                    <p>Design a new wedding card template</p>
                </div>
            </a>

            <a href="{{ route('admin.templates.malaysian') }}" class="action-card">
                <div class="action-icon">
                    <i class="fas fa-star-and-crescent"></i>
                </div>
                <div class="action-content">
                    <h4>Malaysian Designs</h4>
                    <p>Manage traditional templates</p>
                </div>
            </a>

            <a href="{{ route('admin.analytics') }}" class="action-card">
                <div class="action-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div class="action-content">
                    <h4>View Analytics</h4>
                    <p>Check system performance</p>
                </div>
            </a>
        </div>
    </div>
</div>

<style>
.dashboard-content {
    padding: 20px;
    width: 100%;
    max-width: 100%;
    box-sizing: border-box;
    overflow-x: hidden;
}

.dashboard-header {
    margin-bottom: 30px;
}

.dashboard-title {
    font-size: 2rem;
    color: #2d3748;
    margin-bottom: 5px;
}

.dashboard-subtitle {
    color: #718096;
    font-size: 1.1rem;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

/* Responsive adjustments for dashboard */
@media (max-width: 768px) {
    .dashboard-content {
        padding: 15px 10px !important;
        width: 100% !important;
        max-width: 100% !important;
        overflow-x: hidden !important;
    }
    
    .stats-grid {
        grid-template-columns: 1fr;
        gap: 15px;
        margin-bottom: 25px;
        width: 100% !important;
        max-width: 100% !important;
    }
    
    .stat-card {
        padding: 15px 10px !important;
        gap: 10px;
        width: 100% !important;
        max-width: 100% !important;
        box-sizing: border-box !important;
        margin: 0 !important;
    }
    
    .stat-icon {
        width: 50px;
        height: 50px;
        font-size: 20px;
    }
    
    .stat-number {
        font-size: 1.5rem;
    }
    
    .dashboard-grid {
        grid-template-columns: 1fr;
        gap: 15px;
        margin-bottom: 25px;
    }
    
    .dashboard-header {
        margin-bottom: 25px;
        text-align: center;
    }
    
    .dashboard-title {
        font-size: 1.5rem;
    }
    
    .dashboard-subtitle {
        font-size: 1rem;
    }
    
    .card-content {
        padding: 15px;
    }
    
    .card-header {
        padding: 15px;
    }
    
    .action-grid {
        grid-template-columns: 1fr;
        gap: 15px;
    }
    
    .action-card {
        padding: 20px;
        gap: 15px;
    }
    
    .section-title {
        font-size: 1.2rem;
        margin-bottom: 15px;
    }
}

@media (max-width: 480px) {
    .dashboard-content {
        padding: 10px 5px;
        width: 100%;
        max-width: 100vw;
        overflow-x: hidden;
    }
    
    .dashboard-title {
        font-size: 1.3rem;
    }
    
    .dashboard-subtitle {
        font-size: 0.9rem;
    }
    
    .stat-card {
        padding: 15px;
        flex-direction: column;
        text-align: center;
        gap: 10px;
    }
    
    .stat-icon {
        width: 40px;
        height: 40px;
        font-size: 18px;
        margin: 0 auto;
    }
    
    .stat-number {
        font-size: 1.3rem;
    }
    
    .stat-label {
        font-size: 0.85rem;
    }
    
    .card-content {
        padding: 12px;
    }
    
    .card-header {
        padding: 12px;
        flex-direction: column;
        align-items: flex-start;
        gap: 10px;
    }
    
    .card-title {
        font-size: 1rem;
    }
    
    .action-card {
        padding: 15px;
        flex-direction: column;
        text-align: center;
        gap: 10px;
    }
    
    .action-icon {
        width: 40px;
        height: 40px;
        margin: 0 auto;
    }
    
    .action-content h4 {
        font-size: 0.9rem;
        margin-bottom: 3px;
    }
    
    .action-content p {
        font-size: 0.8rem;
    }
    
    .activity-item {
        flex-direction: column;
        align-items: flex-start;
        gap: 10px;
        padding: 10px 0;
        border-bottom: 1px solid #e2e8f0;
    }
    
    .activity-item:last-child {
        border-bottom: none;
    }
    
    .activity-avatar {
        width: 35px;
        height: 35px;
        font-size: 14px;
    }
    
    .activity-badge {
        align-self: flex-end;
    }
}

.stat-card {
    background: white;
    padding: 25px;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    display: flex;
    align-items: center;
    gap: 20px;
    transition: transform 0.2s;
}

.stat-card:hover {
    transform: translateY(-2px);
}

.stat-icon {
    width: 60px;
    height: 60px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    color: white;
}

.stat-primary .stat-icon { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
.stat-success .stat-icon { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }
.stat-info .stat-icon { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }
.stat-warning .stat-icon { background: linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%); color: #d69e2e; }
.stat-purple .stat-icon { background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%); color: #805ad5; }
.stat-admin .stat-icon { background: linear-gradient(135deg, #d299c2 0%, #fef9d7 100%); color: #553c9a; }

.stat-number {
    font-size: 2rem;
    font-weight: 700;
    color: #2d3748;
    margin: 0;
}

.stat-label {
    color: #718096;
    margin: 0;
    font-weight: 500;
}

.dashboard-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.dashboard-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    overflow: hidden;
}

.card-header {
    padding: 20px;
    border-bottom: 1px solid #e2e8f0;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.card-title {
    margin: 0;
    color: #2d3748;
    font-size: 1.2rem;
    display: flex;
    align-items: center;
    gap: 10px;
}

.card-link {
    color: #667eea;
    text-decoration: none;
    font-weight: 500;
}

.card-link:hover {
    color: #5a67d8;
}

.card-content {
    padding: 20px;
}

.activity-list {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.activity-item {
    display: flex;
    align-items: center;
    gap: 15px;
}

.activity-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 600;
}

.card-avatar {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
}

.activity-info {
    flex: 1;
}

.activity-name {
    margin: 0 0 2px 0;
    color: #2d3748;
    font-size: 0.95rem;
}

.activity-detail {
    margin: 0 0 2px 0;
    color: #718096;
    font-size: 0.85rem;
}

.activity-time {
    color: #a0aec0;
    font-size: 0.75rem;
}

.activity-badge {
    padding: 4px 8px;
    border-radius: 6px;
    background: #edf2f7;
    color: #4a5568;
    font-size: 0.75rem;
    font-weight: 500;
}

.badge-success {
    background: #c6f6d5;
    color: #2f855a;
}

.badge-draft {
    background: #fed7d7;
    color: #c53030;
}

.empty-state {
    text-align: center;
    padding: 40px 20px;
    color: #718096;
}

.empty-state i {
    font-size: 3rem;
    margin-bottom: 15px;
    opacity: 0.5;
}

.section-title {
    color: #2d3748;
    margin-bottom: 20px;
}

.action-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
}

.action-card {
    background: white;
    padding: 25px;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    display: flex;
    align-items: center;
    gap: 20px;
    text-decoration: none;
    color: inherit;
    transition: transform 0.2s, box-shadow 0.2s;
}

.action-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 16px rgba(0,0,0,0.15);
    color: inherit;
    text-decoration: none;
}

.action-icon {
    width: 50px;
    height: 50px;
    border-radius: 10px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 20px;
}

.action-content h4 {
    margin: 0 0 5px 0;
    color: #2d3748;
}

.action-content p {
    margin: 0;
    color: #718096;
    font-size: 0.9rem;
}
</style>
@endsection 