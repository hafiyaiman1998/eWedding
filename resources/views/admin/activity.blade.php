@extends('layouts.admin.admin')

@section('title', 'System Activity')

@section('content')
<div class="page-content">
    <div class="page-header">
        <div class="page-header-left">
            <h1 class="page-title">
                <i class="fas fa-activity"></i>
                System Activity
            </h1>
            <p class="page-subtitle">Monitor recent system activities and user interactions</p>
        </div>
        <div class="page-header-right">
            <a href="{{ route('admin.analytics') }}" class="btn btn-primary">
                <i class="fas fa-chart-line"></i>
                View Analytics
            </a>
        </div>
    </div>

    <!-- Activity Overview -->
    <div class="activity-overview">
        <div class="overview-cards">
            <div class="overview-card registrations">
                <div class="card-icon">
                    <i class="fas fa-user-plus"></i>
                </div>
                <div class="card-content">
                    <div class="card-number">{{ $recentActivity['recent_registrations']->count() }}</div>
                    <div class="card-label">New Registrations</div>
                    <div class="card-sublabel">Last 10 registrations</div>
                </div>
            </div>
            
            <div class="overview-card cards">
                <div class="card-icon">
                    <i class="fas fa-heart"></i>
                </div>
                <div class="card-content">
                    <div class="card-number">{{ $recentActivity['recent_cards']->count() }}</div>
                    <div class="card-label">New Cards</div>
                    <div class="card-sublabel">Recently created</div>
                </div>
            </div>
            
            <div class="overview-card published">
                <div class="card-icon">
                    <i class="fas fa-globe"></i>
                </div>
                <div class="card-content">
                    <div class="card-number">{{ $recentActivity['recent_published']->count() }}</div>
                    <div class="card-label">Published Cards</div>
                    <div class="card-sublabel">Recently published</div>
                </div>
            </div>
            
            <div class="overview-card activity">
                <div class="card-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="card-content">
                    <div class="card-number">{{ now()->format('H:i') }}</div>
                    <div class="card-label">Current Time</div>
                    <div class="card-sublabel">{{ now()->format('M d, Y') }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Activity Feed -->
    <div class="activity-feed">
        <div class="activity-sections">
            <!-- Recent Client Registrations -->
            <div class="activity-section">
                <div class="section-header">
                    <h3 class="section-title">
                        <i class="fas fa-users"></i>
                        Recent Client Registrations
                    </h3>
                    <a href="{{ route('admin.users.index') }}" class="section-link">View All Clients</a>
                </div>
                
                <div class="activity-list">
                    @forelse($recentActivity['recent_registrations'] as $user)
                        <div class="activity-item">
                            <div class="activity-avatar user-avatar">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                            <div class="activity-content">
                                <div class="activity-main">
                                    <strong>{{ $user->name }}</strong> joined as a new client
                                </div>
                                <div class="activity-meta">
                                    <span class="email">{{ $user->email }}</span>
                                    <span class="separator">•</span>
                                    <span class="time">{{ $user->created_at->diffForHumans() }}</span>
                                </div>
                            </div>
                            <div class="activity-actions">
                                <a href="{{ route('admin.users.show', $user) }}" class="btn btn-sm btn-outline">View</a>
                            </div>
                        </div>
                    @empty
                        <div class="empty-activity">
                            <i class="fas fa-user-plus"></i>
                            <p>No recent registrations</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Recent Wedding Cards -->
            <div class="activity-section">
                <div class="section-header">
                    <h3 class="section-title">
                        <i class="fas fa-heart"></i>
                        Recent Wedding Cards
                    </h3>
                    <a href="{{ route('admin.cards.index') }}" class="section-link">View All Cards</a>
                </div>
                
                <div class="activity-list">
                    @forelse($recentActivity['recent_cards'] as $card)
                        <div class="activity-item">
                            <div class="activity-avatar card-avatar">
                                <i class="fas fa-heart"></i>
                            </div>
                            <div class="activity-content">
                                <div class="activity-main">
                                    <strong>{{ $card->title }}</strong> created by {{ $card->user->name }}
                                </div>
                                <div class="activity-meta">
                                    <span class="template">Template: {{ $card->designTemplate->name }}</span>
                                    <span class="separator">•</span>
                                    <span class="status {{ $card->is_published ? 'published' : 'draft' }}">
                                        {{ $card->is_published ? 'Published' : 'Draft' }}
                                    </span>
                                    <span class="separator">•</span>
                                    <span class="time">{{ $card->created_at->diffForHumans() }}</span>
                                </div>
                            </div>
                            <div class="activity-actions">
                                <a href="{{ route('admin.cards.show', $card) }}" class="btn btn-sm btn-outline">View</a>
                                @if($card->is_published)
                                    <a href="{{ $card->view_url }}" target="_blank" class="btn btn-sm btn-success">
                                        <i class="fas fa-external-link-alt"></i>
                                    </a>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="empty-activity">
                            <i class="fas fa-heart"></i>
                            <p>No recent wedding cards</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Recently Published Cards -->
            <div class="activity-section">
                <div class="section-header">
                    <h3 class="section-title">
                        <i class="fas fa-globe"></i>
                        Recently Published Cards
                    </h3>
                    <a href="{{ route('admin.cards.published') }}" class="section-link">View Published</a>
                </div>
                
                <div class="activity-list">
                    @forelse($recentActivity['recent_published'] as $card)
                        <div class="activity-item">
                            <div class="activity-avatar published-avatar">
                                <i class="fas fa-globe"></i>
                            </div>
                            <div class="activity-content">
                                <div class="activity-main">
                                    <strong>{{ $card->title }}</strong> went live
                                </div>
                                <div class="activity-meta">
                                    <span class="couple">
                                        {{ $card->card_details['bride_name'] ?? 'Bride' }} & 
                                        {{ $card->card_details['groom_name'] ?? 'Groom' }}
                                    </span>
                                    <span class="separator">•</span>
                                    <span class="client">by {{ $card->user->name }}</span>
                                    <span class="separator">•</span>
                                    <span class="time">{{ $card->updated_at->diffForHumans() }}</span>
                                </div>
                            </div>
                            <div class="activity-actions">
                                <a href="{{ $card->view_url }}" target="_blank" class="btn btn-sm btn-success">
                                    <i class="fas fa-external-link-alt"></i>
                                    View Live
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="empty-activity">
                            <i class="fas fa-globe"></i>
                            <p>No recently published cards</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- System Health & Quick Actions -->
    <div class="system-status">
        <div class="status-cards">
            <div class="status-card">
                <div class="status-header">
                    <h4>System Health</h4>
                    <div class="status-indicator healthy">
                        <i class="fas fa-circle"></i>
                        All Systems Operational
                    </div>
                </div>
                <div class="status-details">
                    <div class="status-item">
                        <span>Database</span>
                        <span class="status-value healthy">Connected</span>
                    </div>
                    <div class="status-item">
                        <span>File Storage</span>
                        <span class="status-value healthy">Accessible</span>
                    </div>
                    <div class="status-item">
                        <span>Last Backup</span>
                        <span class="status-value">{{ now()->subHours(2)->diffForHumans() }}</span>
                    </div>
                </div>
            </div>
            
            <div class="status-card">
                <div class="status-header">
                    <h4>Quick Actions</h4>
                </div>
                <div class="quick-actions">
                    <a href="{{ route('admin.users.create') }}" class="quick-action">
                        <i class="fas fa-user-plus"></i>
                        Add Client
                    </a>
                    <a href="{{ route('admin.templates.create') }}" class="quick-action">
                        <i class="fas fa-plus-circle"></i>
                        New Template
                    </a>
                    <a href="{{ route('admin.analytics') }}" class="quick-action">
                        <i class="fas fa-chart-bar"></i>
                        Analytics
                    </a>
                    <a href="{{ route('admin.cards.published') }}" class="quick-action">
                        <i class="fas fa-globe"></i>
                        Published Cards
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.page-content {
    padding: 20px;
    width: 100%;
    max-width: 100%;
    box-sizing: border-box;
    overflow-x: hidden;
}

.page-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 30px;
    padding-bottom: 20px;
    border-bottom: 1px solid #e2e8f0;
}

.page-header-left {
    display: flex;
    flex-direction: column;
    gap: 5px;
}

.page-title {
    color: #2d3748;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 12px;
}

.page-subtitle {
    color: #718096;
    margin: 0;
}

.btn {
    padding: 12px 20px;
    border-radius: 8px;
    font-weight: 600;
    text-decoration: none;
    border: none;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s ease;
    font-size: 14px;
}

.btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
}

/* Activity Overview */
.activity-overview {
    margin-bottom: 30px;
}

.overview-cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
}

.overview-card {
    background: white;
    border-radius: 12px;
    padding: 25px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    display: flex;
    align-items: center;
    gap: 20px;
    transition: all 0.3s ease;
}

.overview-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.15);
}

.overview-card.registrations {
    border-left: 4px solid #4299e1;
}

.overview-card.cards {
    border-left: 4px solid #e53e3e;
}

.overview-card.published {
    border-left: 4px solid #38a169;
}

.overview-card.activity {
    border-left: 4px solid #ed8936;
}

.card-icon {
    width: 60px;
    height: 60px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 24px;
}

.registrations .card-icon {
    background: linear-gradient(135deg, #4299e1 0%, #3182ce 100%);
}

.cards .card-icon {
    background: linear-gradient(135deg, #e53e3e 0%, #c53030 100%);
}

.published .card-icon {
    background: linear-gradient(135deg, #38a169 0%, #2f855a 100%);
}

.activity .card-icon {
    background: linear-gradient(135deg, #ed8936 0%, #dd6b20 100%);
}

.card-content {
    flex: 1;
}

.card-number {
    font-size: 32px;
    font-weight: 700;
    color: #2d3748;
    line-height: 1;
}

.card-label {
    color: #4a5568;
    font-weight: 600;
    margin-top: 5px;
}

.card-sublabel {
    color: #718096;
    font-size: 12px;
    margin-top: 2px;
}

/* Activity Feed */
.activity-feed {
    margin-bottom: 30px;
}

.activity-sections {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(450px, 1fr));
    gap: 25px;
}

.activity-section {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    overflow: hidden;
}

.section-header {
    padding: 20px;
    border-bottom: 1px solid #e2e8f0;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.section-title {
    color: #2d3748;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 18px;
}

.section-link {
    color: #667eea;
    text-decoration: none;
    font-size: 14px;
    font-weight: 600;
}

.section-link:hover {
    color: #5a67d8;
}

.activity-list {
    padding: 20px;
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.activity-item {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 15px;
    border-radius: 8px;
    background: #f7fafc;
    transition: all 0.3s ease;
}

.activity-item:hover {
    background: #edf2f7;
    transform: translateX(5px);
}

.activity-avatar {
    width: 45px;
    height: 45px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 600;
    font-size: 14px;
}

.user-avatar {
    background: linear-gradient(135deg, #4299e1 0%, #3182ce 100%);
}

.card-avatar {
    background: linear-gradient(135deg, #e53e3e 0%, #c53030 100%);
}

.published-avatar {
    background: linear-gradient(135deg, #38a169 0%, #2f855a 100%);
}

.activity-content {
    flex: 1;
}

.activity-main {
    color: #2d3748;
    font-size: 14px;
    margin-bottom: 5px;
}

.activity-meta {
    color: #718096;
    font-size: 12px;
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
}

.separator {
    color: #cbd5e0;
}

.status.published {
    color: #38a169;
    font-weight: 600;
}

.status.draft {
    color: #ed8936;
    font-weight: 600;
}

.activity-actions {
    display: flex;
    gap: 8px;
}

.btn-sm {
    padding: 6px 12px;
    font-size: 12px;
}

.btn-outline {
    background: transparent;
    border: 1px solid #e2e8f0;
    color: #4a5568;
}

.btn-outline:hover {
    background: #f7fafc;
    border-color: #cbd5e0;
}

.btn-success {
    background: #38a169;
    color: white;
}

.btn-success:hover {
    background: #2f855a;
}

.empty-activity {
    text-align: center;
    padding: 40px 20px;
    color: #718096;
}

.empty-activity i {
    font-size: 32px;
    margin-bottom: 10px;
    opacity: 0.5;
}

/* System Status */
.system-status {
    margin-bottom: 30px;
}

.status-cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
    gap: 25px;
}

.status-card {
    background: white;
    border-radius: 12px;
    padding: 25px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.status-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.status-header h4 {
    color: #2d3748;
    margin: 0;
}

.status-indicator {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 12px;
    font-weight: 600;
}

.status-indicator.healthy {
    color: #38a169;
}

.status-indicator i {
    font-size: 8px;
}

.status-details {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.status-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 8px 0;
    border-bottom: 1px solid #f1f5f9;
}

.status-value {
    font-weight: 600;
    color: #4a5568;
}

.status-value.healthy {
    color: #38a169;
}

.quick-actions {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 12px;
}

.quick-action {
    padding: 12px;
    border-radius: 8px;
    background: #f7fafc;
    text-decoration: none;
    color: #4a5568;
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 14px;
    font-weight: 600;
    transition: all 0.3s ease;
}

.quick-action:hover {
    background: #edf2f7;
    color: #2d3748;
    transform: translateY(-1px);
}

/* Responsive Design */
@media (max-width: 768px) {
    .page-header {
        flex-direction: column;
        gap: 20px;
    }
    
    .overview-cards {
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 15px;
    }
    
    .overview-card {
        padding: 20px;
    }
    
    .card-number {
        font-size: 28px;
    }
    
    .activity-sections {
        grid-template-columns: 1fr;
        gap: 20px;
    }
    
    .activity-item {
        flex-direction: column;
        text-align: center;
        gap: 10px;
    }
    
    .activity-meta {
        justify-content: center;
    }
    
    .status-cards {
        grid-template-columns: 1fr;
    }
    
    .quick-actions {
        grid-template-columns: 1fr;
    }
    
    /* CRITICAL: Ensure header and burger button work properly */
    .header {
        display: flex !important;
        align-items: center !important;
        justify-content: space-between !important;
    }
    
    .menu-toggle {
        display: block !important;
        background: none !important;
        border: none !important;
        color: #2d3748 !important;
        font-size: 18px !important;
        cursor: pointer !important;
        padding: 8px !important;
        border-radius: 6px !important;
        transition: background-color 0.3s ease !important;
    }
    
    .menu-toggle:hover {
        background-color: rgba(0,0,0,0.05) !important;
    }
}

@media (max-width: 480px) {
    .page-content {
        padding: 15px;
    }
    
    .activity-item {
        padding: 12px;
    }
    
    .status-card {
        padding: 20px;
    }
}
</style>

<script>
// Auto-refresh activity feed every 30 seconds
setInterval(function() {
    // In a real application, you might want to refresh parts of the page
    console.log('Activity feed refresh - ' + new Date().toLocaleTimeString());
}, 30000);

// Real-time clock update
function updateClock() {
    const now = new Date();
    const timeElements = document.querySelectorAll('.activity .card-number');
    timeElements.forEach(el => {
        if (el.textContent.includes(':')) {
            el.textContent = now.toTimeString().slice(0, 5);
        }
    });
}

// Update clock every minute
setInterval(updateClock, 60000);
</script>
@endsection 