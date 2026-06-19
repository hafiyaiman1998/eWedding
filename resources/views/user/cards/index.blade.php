@extends('layouts.user.user')

@section('title', 'My Wedding Cards')
@section('page_title', 'My Wedding Cards')
@section('page_subtitle', 'Manage all your beautiful wedding invitations')

@section('content')
<div class="content-card">
    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-error">
            <i class="fas fa-exclamation-circle"></i>
            {{ session('error') }}
        </div>
    @endif

    <!-- Action Buttons -->
    <div class="action-header">
        <div class="action-buttons">
            @php
                $remainingCards = \App\Models\WeddingCard::getRemainingCardsForUser(auth()->id());
                $maxCards = \App\Models\Setting::get('max_cards_per_user', 10);
            @endphp
            
            @if($remainingCards > 0)
                <a href="{{ route('user.cards.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus-circle"></i>
                    Create New Card
                </a>
                <span class="cards-limit-info">
                    {{ $remainingCards }} of {{ $maxCards }} cards remaining
                </span>
            @else
                <button class="btn btn-secondary" disabled title="Card limit reached">
                    <i class="fas fa-plus-circle"></i>
                    Create New Card (Limit Reached)
                </button>
                <span class="cards-limit-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    You've reached your maximum of {{ $maxCards }} cards. Delete some cards to create new ones.
                </span>
            @endif
        </div>
        
        <div class="cards-stats">
            <div class="stat-item">
                <div class="stat-number">{{ $cards->total() }}</div>
                <div class="stat-label">Total Cards</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">{{ $cards->where('is_published', true)->count() }}</div>
                <div class="stat-label">Published</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">{{ $cards->where('is_published', false)->count() }}</div>
                <div class="stat-label">Drafts</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">{{ $cards->filter(fn($card) => $card->isExpired())->count() }}</div>
                <div class="stat-label">Expired</div>
            </div>
        </div>
    </div>

    <!-- Cards Grid -->
    @if($cards->count() > 0)
        <div class="cards-grid">
            @foreach($cards as $card)
                <div class="card-item">
                    <div class="card-thumbnail">
                        @if($card->designTemplate && $card->designTemplate->preview_image)
                            <img src="{{ asset('storage/' . $card->designTemplate->preview_image) }}" alt="{{ $card->title }}">
                        @else
                            <div class="card-placeholder">
                                <i class="fas fa-heart"></i>
                                <span>{{ $card->designTemplate->name ?? 'Template' }}</span>
                            </div>
                        @endif
                        
                        <div class="card-status">
                            @if($card->isExpired())
                                <span class="status expired">
                                    <i class="fas fa-clock"></i> Expired
                                </span>
                            @elseif($card->isRejected())
                                <span class="status rejected">
                                    <i class="fas fa-times-circle"></i> Rejected
                                </span>
                            @elseif($card->isPending())
                                <span class="status pending">
                                    <i class="fas fa-hourglass-half"></i> Pending Approval
                                </span>
                            @elseif($card->is_published)
                                <span class="status published">
                                    <i class="fas fa-globe"></i> Published
                                </span>
                            @else
                                <span class="status draft">
                                    <i class="fas fa-edit"></i> Draft
                                </span>
                            @endif
                        </div>
                    </div>
                    
                    <div class="card-content">
                        <h3 class="card-title">{{ $card->title }}</h3>
                        <p class="card-details">
                            <strong>{{ $card->card_details['bride_name'] ?? 'Bride' }}</strong> & 
                            <strong>{{ $card->card_details['groom_name'] ?? 'Groom' }}</strong>
                        </p>
                        <p class="card-date">
                            <i class="fas fa-calendar"></i>
                            {{ $card->card_details['wedding_date'] ?? 'Date TBD' }}
                        </p>
                        <p class="card-template">
                            <i class="fas fa-palette"></i>
                            {{ $card->designTemplate->name ?? 'Unknown Template' }}
                        </p>
                        @if($card->expiry_date)
                            <p class="card-expiry {{ $card->isExpired() ? 'expired' : '' }}">
                                <i class="fas fa-hourglass-{{ $card->isExpired() ? 'end' : 'half' }}"></i>
                                @if($card->isExpired())
                                    Expired on {{ $card->expiry_date->format('M d, Y') }}
                                @else
                                    Expires on {{ $card->expiry_date->format('M d, Y') }}
                                @endif
                            </p>
                        @endif
                        
                        @if($card->isRejected() && $card->rejection_reason)
                            <div class="rejection-reason">
                                <strong>Rejection Reason:</strong>
                                <p>{{ $card->rejection_reason }}</p>
                            </div>
                        @elseif($card->isPending())
                            <div class="pending-notice">
                                <i class="fas fa-info-circle"></i>
                                Your card is awaiting admin approval. You will be notified once it's reviewed.
                            </div>
                        @endif
                    </div>
                    
                    <div class="card-actions">
                        <a href="{{ route('user.cards.preview', $card) }}" class="btn btn-icon" title="Preview">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="{{ route('user.cards.edit', $card) }}" class="btn btn-icon" title="Edit">
                            <i class="fas fa-edit"></i>
                        </a>
                        @if($card->is_published)
                            <a href="{{ route('user.cards.share', $card) }}" class="btn btn-icon btn-success" title="Share">
                                <i class="fas fa-share-alt"></i>
                            </a>
                            <a href="{{ route('user.cards.analytics', $card) }}" class="btn btn-icon btn-info" title="Analytics">
                                <i class="fas fa-chart-line"></i>
                            </a>
                        @endif
                        
                        <!-- Toggle Published Status -->
                        @php
                            $autoApprove = \App\Models\Setting::get('auto_approve_cards', true);
                        @endphp
                        
                        @if($card->is_published)
                            <form method="POST" action="{{ route('user.cards.toggle-published', $card) }}" style="display: inline;">
                                @csrf
                                <button type="submit" class="btn btn-icon btn-warning" title="Unpublish">
                                    <i class="fas fa-eye-slash"></i>
                                </button>
                            </form>
                        @elseif($card->isPending())
                            <button class="btn btn-icon btn-secondary" disabled title="Awaiting admin approval">
                                <i class="fas fa-hourglass-half"></i>
                            </button>
                        @elseif($card->isRejected())
                            @if($autoApprove)
                                <form method="POST" action="{{ route('user.cards.toggle-published', $card) }}" style="display: inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-icon btn-success" title="Publish">
                                        <i class="fas fa-globe"></i>
                                    </button>
                                </form>
                            @else
                                <form method="POST" action="{{ route('user.cards.toggle-published', $card) }}" style="display: inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-icon btn-info" title="Submit for approval">
                                        <i class="fas fa-paper-plane"></i>
                                    </button>
                                </form>
                            @endif
                        @else
                            @if($autoApprove)
                                <form method="POST" action="{{ route('user.cards.toggle-published', $card) }}" style="display: inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-icon btn-success" title="Publish">
                                        <i class="fas fa-globe"></i>
                                    </button>
                                </form>
                            @else
                                <form method="POST" action="{{ route('user.cards.toggle-published', $card) }}" style="display: inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-icon btn-info" title="Submit for approval">
                                        <i class="fas fa-paper-plane"></i>
                                    </button>
                                </form>
                            @endif
                        @endif
                        
                        <!-- Delete Card -->
                        <form method="POST" action="{{ route('user.cards.destroy', $card) }}" 
                              class="delete-form" 
                              data-delete-type="wedding card" 
                              data-delete-name="{{ $card->title ?: 'Untitled Card' }}"
                              data-delete-warning="All card data and analytics will be permanently lost."
                              style="display: inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-icon btn-danger delete-btn" title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="pagination-wrapper">
            {{ $cards->links() }}
        </div>
    @else
        <div class="empty-state">
            <div class="empty-icon">
                <i class="fas fa-heart"></i>
            </div>
            <h3>No Wedding Cards Yet</h3>
            <p>Create your first beautiful wedding invitation to get started!</p>
            <a href="{{ route('user.cards.create') }}" class="btn btn-primary">
                <i class="fas fa-plus-circle"></i>
                Create Your First Card
            </a>
        </div>
    @endif
</div>

<style>
.alert {
    padding: 15px 20px;
    border-radius: 10px;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.alert-success {
    background: rgba(46, 204, 113, 0.1);
    border: 1px solid rgba(46, 204, 113, 0.3);
    color: #27ae60;
}

.alert-error {
    background: rgba(231, 76, 60, 0.1);
    border: 1px solid rgba(231, 76, 60, 0.3);
    color: #e74c3c;
}

.action-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
    padding-bottom: 20px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.2);
}

.cards-stats {
    display: flex;
    gap: 30px;
}

.stat-item {
    text-align: center;
}

.stat-number {
    font-size: 32px;
    font-weight: 700;
    color: #2c3e50;
    margin-bottom: 5px;
}

.stat-label {
    color: #7f8c8d;
    font-size: 14px;
}

.cards-limit-info {
    color: #27ae60;
    font-size: 14px;
    font-weight: 600;
    margin-left: 15px;
    padding: 8px 12px;
    background: rgba(46, 204, 113, 0.1);
    border: 1px solid rgba(46, 204, 113, 0.3);
    border-radius: 6px;
}

.cards-limit-warning {
    color: #e74c3c;
    font-size: 14px;
    font-weight: 600;
    margin-left: 15px;
    padding: 8px 12px;
    background: rgba(231, 76, 60, 0.1);
    border: 1px solid rgba(231, 76, 60, 0.3);
    border-radius: 6px;
}

.btn-secondary {
    background: #95a5a6;
    color: white;
    cursor: not-allowed;
    opacity: 0.6;
}

.status.expired {
    background: rgba(231, 76, 60, 0.1);
    color: #e74c3c;
    border: 1px solid rgba(231, 76, 60, 0.3);
}

.status.pending {
    background: rgba(237, 137, 54, 0.1);
    color: #c05621;
    border: 1px solid rgba(237, 137, 54, 0.3);
}

.status.rejected {
    background: rgba(220, 38, 127, 0.1);
    color: #ad1457;
    border: 1px solid rgba(220, 38, 127, 0.3);
}

.card-expiry {
    font-size: 12px;
    color: #7f8c8d;
    margin-top: 8px;
}

.card-expiry.expired {
    color: #e74c3c;
    font-weight: 600;
}

.rejection-reason {
    background: rgba(220, 38, 127, 0.1);
    border: 1px solid rgba(220, 38, 127, 0.3);
    border-radius: 8px;
    padding: 12px;
    margin-top: 10px;
}

.rejection-reason strong {
    color: #ad1457;
    display: block;
    margin-bottom: 5px;
}

.rejection-reason p {
    color: #ad1457;
    margin: 0;
    font-size: 14px;
}

.pending-notice {
    background: rgba(237, 137, 54, 0.1);
    border: 1px solid rgba(237, 137, 54, 0.3);
    border-radius: 8px;
    padding: 12px;
    margin-top: 10px;
    color: #c05621;
    font-size: 14px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.cards-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 25px;
    margin-bottom: 30px;
}

.card-item {
    background: rgba(255, 255, 255, 0.3);
    border-radius: 15px;
    overflow: hidden;
    transition: all 0.3s ease;
    border: 1px solid rgba(255, 255, 255, 0.2);
}

.card-item:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 30px rgba(255, 107, 157, 0.2);
}

.card-thumbnail {
    position: relative;
    height: 200px;
    background: rgba(255, 255, 255, 0.1);
    display: flex;
    align-items: center;
    justify-content: center;
}

.card-thumbnail img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.card-placeholder {
    text-align: center;
    color: #7f8c8d;
}

.card-placeholder i {
    font-size: 48px;
    margin-bottom: 10px;
    display: block;
}

.card-status {
    position: absolute;
    top: 15px;
    right: 15px;
}

.status {
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    backdrop-filter: blur(10px);
}

.status.published {
    background: rgba(46, 204, 113, 0.9);
    color: white;
}

.status.draft {
    background: rgba(230, 126, 34, 0.9);
    color: white;
}

.status.expired {
    background: rgba(231, 76, 60, 0.9);
    color: white;
}

.card-content {
    padding: 20px;
}

.card-title {
    font-size: 18px;
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 10px;
}

.card-details {
    color: #e74c3c;
    font-weight: 500;
    margin-bottom: 8px;
}

.card-date, .card-template {
    color: #7f8c8d;
    font-size: 14px;
    margin-bottom: 5px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.card-actions {
    padding: 15px 20px;
    background: rgba(255, 255, 255, 0.1);
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}

.btn-icon {
    width: 40px;
    height: 40px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    border: none;
    cursor: pointer;
    transition: all 0.3s ease;
    text-decoration: none;
    background: rgba(255, 255, 255, 0.2);
    color: #2c3e50;
}

.btn-icon:hover {
    transform: translateY(-2px);
}

.btn-icon.btn-success {
    background: rgba(46, 204, 113, 0.2);
    color: #27ae60;
}

.btn-icon.btn-info {
    background: rgba(52, 152, 219, 0.2);
    color: #3498db;
}

.btn-icon.btn-warning {
    background: rgba(230, 126, 34, 0.2);
    color: #e67e22;
}

.btn-icon.btn-danger {
    background: rgba(231, 76, 60, 0.2);
    color: #e74c3c;
}

.empty-state {
    text-align: center;
    padding: 60px 20px;
}

.empty-icon {
    font-size: 80px;
    color: #bdc3c7;
    margin-bottom: 20px;
}

.empty-state h3 {
    font-size: 24px;
    color: #2c3e50;
    margin-bottom: 10px;
}

.empty-state p {
    color: #7f8c8d;
    margin-bottom: 30px;
}

.pagination-wrapper {
    display: flex;
    justify-content: center;
    margin-top: 30px;
}

@media (max-width: 768px) {
    .action-header {
        flex-direction: column;
        gap: 20px;
        align-items: stretch;
    }
    
    .cards-stats {
        justify-content: space-around;
    }
    
    .cards-grid {
        grid-template-columns: 1fr;
    }
}
</style>
@endsection 