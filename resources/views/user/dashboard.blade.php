@extends('layouts.user.user')

@section('title', 'My Wedding Studio Dashboard')

@section('page_title', 'My Wedding Studio')
@section('page_subtitle', 'Create and manage your beautiful wedding invitations')

@section('content')
<div class="dashboard-content">
    <!-- Statistics Cards -->
    <div class="stats-grid">
        <div class="stat-card stat-primary">
            <div class="stat-icon">
                <i class="fas fa-heart"></i>
            </div>
            <div class="stat-content">
                <h3 class="stat-number">{{ $stats['total_cards'] }}</h3>
                <p class="stat-label">Total Cards</p>
            </div>
        </div>

        <div class="stat-card stat-success">
            <div class="stat-icon">
                <i class="fas fa-globe"></i>
            </div>
            <div class="stat-content">
                <h3 class="stat-number">{{ $stats['published_cards'] }}</h3>
                <p class="stat-label">Published Cards</p>
            </div>
        </div>

        <div class="stat-card stat-warning">
            <div class="stat-icon">
                <i class="fas fa-edit"></i>
            </div>
            <div class="stat-content">
                <h3 class="stat-number">{{ $stats['draft_cards'] }}</h3>
                <p class="stat-label">Draft Cards</p>
            </div>
        </div>

        @php
            $remainingCards = \App\Models\WeddingCard::getRemainingCardsForUser(auth()->id());
            $maxCards = \App\Models\Setting::get('max_cards_per_user', 10);
            $expiredCards = auth()->user()->weddingCards()->where(function($q) {
                $q->whereNotNull('expiry_date')->where('expiry_date', '<=', now());
            })->count();
        @endphp
        
        <div class="stat-card {{ $remainingCards > 0 ? 'stat-limit-ok' : 'stat-limit-reached' }}">
            <div class="stat-icon">
                <i class="fas {{ $remainingCards > 0 ? 'fa-plus-circle' : 'fa-exclamation-triangle' }}"></i>
            </div>
            <div class="stat-content">
                <h3 class="stat-number">{{ $remainingCards }}</h3>
                <p class="stat-label">Cards Remaining</p>
                <small class="stat-detail">{{ $stats['total_cards'] }} / {{ $maxCards }} used</small>
            </div>
        </div>

        @if($expiredCards > 0)
            <div class="stat-card stat-expired">
                <div class="stat-icon">
                    <i class="fas fa-hourglass-end"></i>
                </div>
                <div class="stat-content">
                    <h3 class="stat-number">{{ $expiredCards }}</h3>
                    <p class="stat-label">Expired Cards</p>
                </div>
            </div>
        @endif

        <div class="stat-card stat-info">
            <div class="stat-icon">
                <i class="fas fa-eye"></i>
            </div>
            <div class="stat-content">
                <h3 class="stat-number">{{ $stats['total_views'] }}</h3>
                <p class="stat-label">Total Views</p>
            </div>
        </div>

        <div class="stat-card stat-purple">
            <div class="stat-icon">
                <i class="fas fa-envelope"></i>
            </div>
            <div class="stat-content">
                <h3 class="stat-number">{{ $stats['total_rsvps'] }}</h3>
                <p class="stat-label">Total RSVPs</p>
            </div>
        </div>

        <div class="stat-card stat-attending">
            <div class="stat-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-content">
                <h3 class="stat-number">{{ $stats['attending_rsvps'] }}</h3>
                <p class="stat-label">Attending Guests</p>
            </div>
        </div>
    </div>

    <!-- Recent Activity Section -->
    <div class="dashboard-grid">
        <!-- My Recent Cards -->
        <div class="dashboard-card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-heart"></i>
                    My Recent Cards
                </h3>
                <a href="{{ route('user.cards.index') }}" class="card-link">View All</a>
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
                                    <p class="activity-detail">
                                        Template: {{ $card->designTemplate->name ?? 'Custom' }}
                                    </p>
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
                        <a href="{{ route('user.cards.create') }}" class="btn btn-primary" style="margin-top: 15px;">
                            <i class="fas fa-plus"></i> Create Your First Card
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Recent RSVPs -->
        <div class="dashboard-card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-users"></i>
                    Recent RSVPs
                </h3>
                <a href="#" class="card-link">View All</a>
            </div>
            <div class="card-content">
                @if($stats['recent_rsvps']->count() > 0)
                    <div class="activity-list">
                        @foreach($stats['recent_rsvps'] as $rsvp)
                            <div class="activity-item">
                                <div class="activity-avatar {{ $rsvp->attendance_status === 'yes' ? 'rsvp-yes' : 'rsvp-no' }}">
                                    {{ strtoupper(substr($rsvp->guest_name, 0, 1)) }}
                                </div>
                                <div class="activity-info">
                                    <h4 class="activity-name">{{ $rsvp->guest_name }}</h4>
                                    <p class="activity-detail">{{ $rsvp->guest_email }}</p>
                                    <small class="activity-time">{{ $rsvp->created_at->diffForHumans() }}</small>
                                </div>
                                <div class="activity-badge {{ $rsvp->attendance_status === 'yes' ? 'badge-success' : 'badge-declined' }}">
                                    {{ $rsvp->attendance_status === 'yes' ? 'Attending' : 'Declined' }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="empty-state">
                        <i class="fas fa-users"></i>
                        <p>No RSVPs received yet</p>
                        <p style="margin-top: 10px; font-size: 14px; color: #7f8c8d;">
                            Share your wedding cards to start receiving responses!
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="quick-actions">
        <h3 class="section-title">Quick Actions</h3>
        
        @php
            $remainingCards = \App\Models\WeddingCard::getRemainingCardsForUser(auth()->id());
        @endphp
        
        @if($remainingCards <= 0)
            <div class="limit-warning">
                <div class="warning-icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div class="warning-content">
                    <h4>Card Limit Reached</h4>
                    <p>You've reached your maximum of {{ \App\Models\Setting::get('max_cards_per_user', 10) }} wedding cards. Delete some cards to create new ones.</p>
                </div>
            </div>
        @endif
        
        <div class="action-grid">
            @if($remainingCards > 0)
                <a href="{{ route('user.cards.create') }}" class="action-card">
                    <div class="action-icon">
                        <i class="fas fa-plus-circle"></i>
                    </div>
                    <div class="action-content">
                        <h4>Create New Card</h4>
                        <p>Design a beautiful wedding invitation</p>
                        <small class="action-note">{{ $remainingCards }} slots remaining</small>
                    </div>
                </a>
            @else
                <div class="action-card disabled">
                    <div class="action-icon">
                        <i class="fas fa-plus-circle"></i>
                    </div>
                    <div class="action-content">
                        <h4>Create New Card</h4>
                        <p>Card limit reached</p>
                        <small class="action-note">Delete cards to create new ones</small>
                    </div>
                </div>
            @endif

            <a href="{{ route('user.cards.index') }}" class="action-card">
                <div class="action-icon">
                    <i class="fas fa-heart"></i>
                </div>
                <div class="action-content">
                    <h4>My Cards</h4>
                    <p>View and manage your wedding cards</p>
                </div>
            </a>

            <a href="#" class="action-card">
                <div class="action-icon">
                    <i class="fas fa-share-alt"></i>
                </div>
                <div class="action-content">
                    <h4>Share Cards</h4>
                    <p>Send invitations to your guests</p>
                </div>
            </a>

            <a href="#" class="action-card">
                <div class="action-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div class="action-content">
                    <h4>View Analytics</h4>
                    <p>Track views and responses</p>
                </div>
            </a>
        </div>
    </div>
</div>

<style>
.dashboard-content {
    max-width: 1200px;
    margin: 0 auto;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.stat-card {
    background: rgba(255, 255, 255, 0.25);
    backdrop-filter: blur(15px);
    padding: 25px;
    border-radius: 20px;
    border: 1px solid rgba(255, 255, 255, 0.2);
    display: flex;
    align-items: center;
    gap: 20px;
    transition: all 0.3s ease;
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 40px rgba(255, 107, 157, 0.15);
    background: rgba(255, 255, 255, 0.3);
}

.stat-icon {
    width: 60px;
    height: 60px;
    border-radius: 15px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    color: white;
}

.stat-primary .stat-icon { background: linear-gradient(135deg, #ff6b9d 0%, #c44569 100%); }
.stat-success .stat-icon { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }
.stat-warning .stat-icon { background: linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%); color: #d69e2e; }
.stat-info .stat-icon { background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%); color: #4a90e2; }
.stat-purple .stat-icon { background: linear-gradient(135deg, #d299c2 0%, #fef9d7 100%); color: #9d50bb; }
.stat-attending .stat-icon { background: linear-gradient(135deg, #84fab0 0%, #8fd3f4 100%); color: #27ae60; }

.stat-number {
    font-size: 2rem;
    font-weight: 700;
    color: #2c3e50;
    margin: 0;
}

.stat-label {
    color: #7f8c8d;
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
    background: rgba(255, 255, 255, 0.25);
    backdrop-filter: blur(15px);
    border-radius: 20px;
    border: 1px solid rgba(255, 255, 255, 0.2);
    overflow: hidden;
}

.card-header {
    padding: 20px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.2);
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.card-title {
    margin: 0;
    color: #2c3e50;
    font-size: 1.2rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 10px;
}

.card-link {
    color: #ff6b9d;
    text-decoration: none;
    font-weight: 500;
    padding: 8px 16px;
    border-radius: 10px;
    background: rgba(255, 107, 157, 0.1);
    transition: all 0.3s ease;
}

.card-link:hover {
    background: rgba(255, 107, 157, 0.2);
    color: #c44569;
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
    width: 45px;
    height: 45px;
    border-radius: 50%;
    background: linear-gradient(135deg, #ff6b9d 0%, #c44569 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 600;
    font-size: 14px;
}

.card-avatar {
    background: linear-gradient(135deg, #ff6b9d 0%, #c44569 100%);
}

.rsvp-yes {
    background: linear-gradient(135deg, #84fab0 0%, #8fd3f4 100%);
}

.rsvp-no {
    background: linear-gradient(135deg, #ff6b9d 0%, #c44569 100%);
}

.activity-info {
    flex: 1;
}

.activity-name {
    margin: 0 0 2px 0;
    color: #2c3e50;
    font-size: 1rem;
    font-weight: 600;
}

.activity-detail {
    margin: 0 0 2px 0;
    color: #7f8c8d;
    font-size: 0.85rem;
}

.activity-time {
    color: #bdc3c7;
    font-size: 0.75rem;
}

.activity-badge {
    padding: 6px 12px;
    border-radius: 8px;
    background: rgba(255, 255, 255, 0.2);
    color: #2c3e50;
    font-size: 0.75rem;
    font-weight: 500;
}

.badge-success {
    background: rgba(132, 250, 176, 0.3);
    color: #27ae60;
}

.badge-draft {
    background: rgba(255, 107, 157, 0.2);
    color: #e74c3c;
}

.badge-declined {
    background: rgba(255, 107, 157, 0.2);
    color: #e74c3c;
}

.empty-state {
    text-align: center;
    padding: 40px 20px;
    color: #7f8c8d;
}

.empty-state i {
    font-size: 3rem;
    margin-bottom: 15px;
    opacity: 0.5;
}

.section-title {
    color: #2c3e50;
    margin-bottom: 20px;
    font-size: 1.3rem;
    font-weight: 600;
}

.action-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
}

.action-card {
    background: rgba(255, 255, 255, 0.25);
    backdrop-filter: blur(15px);
    padding: 25px;
    border-radius: 20px;
    border: 1px solid rgba(255, 255, 255, 0.2);
    display: flex;
    align-items: center;
    gap: 20px;
    text-decoration: none;
    color: inherit;
    transition: all 0.3s ease;
}

.action-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 40px rgba(255, 107, 157, 0.15);
    background: rgba(255, 255, 255, 0.3);
    color: inherit;
    text-decoration: none;
}

.action-icon {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    background: linear-gradient(135deg, #ff6b9d 0%, #c44569 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 20px;
}

.action-content h4 {
    margin: 0 0 5px 0;
    color: #2c3e50;
    font-weight: 600;
}

.action-content p {
    margin: 0;
    color: #7f8c8d;
    font-size: 0.9rem;
}

.limit-warning {
    background: rgba(255, 255, 255, 0.25);
    backdrop-filter: blur(15px);
    padding: 20px;
    border-radius: 20px;
    border: 1px solid rgba(255, 255, 255, 0.2);
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 20px;
}

.warning-icon {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    background: linear-gradient(135deg, #ff6b9d 0%, #c44569 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 20px;
}

.warning-content h4 {
    margin: 0 0 5px 0;
    color: #2c3e50;
    font-weight: 600;
}

.warning-content p {
    margin: 0;
    color: #7f8c8d;
    font-size: 0.9rem;
}

.action-note {
    color: #7f8c8d;
    font-size: 0.8rem;
}

.action-card.disabled {
    background: rgba(255, 255, 255, 0.2);
    color: #7f8c8d;
    cursor: not-allowed;
}

.action-card.disabled .action-icon {
    background: rgba(255, 255, 255, 0.2);
    color: #7f8c8d;
}

.action-card.disabled .action-content {
    color: #7f8c8d;
}

@media (max-width: 768px) {
    .stats-grid {
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    }
    
    .dashboard-grid {
        grid-template-columns: 1fr;
    }
    
    .action-grid {
        grid-template-columns: 1fr;
    }
}

.stat-card.stat-attending {
    background: linear-gradient(135deg, #10ac84 0%, #0984e3 100%);
}

.stat-card.stat-limit-ok {
    background: linear-gradient(135deg, #00b894 0%, #00cec9 100%);
}

.stat-card.stat-limit-reached {
    background: linear-gradient(135deg, #ff6b9d 0%, #c44569 100%);
}

.stat-card.stat-expired {
    background: linear-gradient(135deg, #e17055 0%, #d63031 100%);
}

.stat-detail {
    color: rgba(255, 255, 255, 0.8);
    font-size: 0.8rem;
    margin-top: 5px;
    display: block;
}
</style>
@endsection 