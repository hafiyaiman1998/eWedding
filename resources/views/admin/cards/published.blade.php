@extends('layouts.admin.admin')

@section('title', 'Published Cards')

@section('content')
<div class="page-content">
    <div class="page-header">
        <div class="page-header-left">
            <h1 class="page-title">
                <i class="fas fa-globe"></i>
                Published Wedding Cards
            </h1>
            <p class="page-subtitle">View all publicly available wedding invitations</p>
        </div>
        <div class="page-header-right">
            <a href="{{ route('admin.cards.index') }}" class="btn btn-secondary">
                <i class="fas fa-heart"></i>
                All Cards
            </a>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="stats-row">
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-globe"></i>
            </div>
            <div class="stat-content">
                <div class="stat-number">{{ $cards->total() }}</div>
                <div class="stat-label">Published Cards</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-eye"></i>
            </div>
            <div class="stat-content">
                <div class="stat-number">{{ $cards->sum(function($card) { return $card->analytics->where('event_type', 'view')->count(); }) }}</div>
                <div class="stat-label">Total Views</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-share"></i>
            </div>
            <div class="stat-content">
                <div class="stat-number">{{ $cards->sum(function($card) { return $card->analytics->where('event_type', 'share')->count(); }) }}</div>
                <div class="stat-label">Total Shares</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-content">
                <div class="stat-number">{{ $cards->sum(function($card) { return $card->rsvps->count(); }) }}</div>
                <div class="stat-label">Total RSVPs</div>
            </div>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="filters-section">
        <form method="GET" action="{{ route('admin.cards.published') }}" class="filter-form">
            <div class="filter-group">
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Search published cards or clients..." class="form-input">
                
                <button type="submit" class="btn btn-search">
                    <i class="fas fa-search"></i>
                    Search
                </button>
                
                @if(request('search'))
                    <a href="{{ route('admin.cards.published') }}" class="btn btn-clear">
                        <i class="fas fa-times"></i>
                        Clear
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Published Cards Grid -->
    <div class="cards-container">
        @if($cards->count() > 0)
            <div class="cards-grid">
                @foreach($cards as $card)
                    <div class="published-card">
                        <div class="card-preview">
                            <div class="card-preview-header">
                                <div class="template-badge">{{ $card->designTemplate->name }}</div>
                                <div class="published-badge">
                                    <i class="fas fa-globe"></i>
                                    Live
                                </div>
                            </div>
                            <div class="card-preview-content">
                                <h3 class="couple-names">
                                    {{ $card->card_details['bride_name'] ?? 'Bride' }} & 
                                    {{ $card->card_details['groom_name'] ?? 'Groom' }}
                                </h3>
                                <p class="wedding-date">
                                    <i class="fas fa-calendar"></i>
                                    {{ $card->card_details['wedding_date'] ?? 'Date TBD' }}
                                </p>
                                @if(!empty($card->card_details['venue']))
                                    <p class="wedding-venue">
                                        <i class="fas fa-map-marker-alt"></i>
                                        {{ $card->card_details['venue'] }}
                                    </p>
                                @endif
                            </div>
                        </div>
                        
                        <div class="card-info">
                            <div class="card-title">{{ $card->title }}</div>
                            <div class="client-info">
                                <div class="client-avatar">
                                    {{ strtoupper(substr($card->user->name, 0, 1)) }}
                                </div>
                                <div class="client-details">
                                    <strong>{{ $card->user->name }}</strong>
                                    <small>{{ $card->user->email }}</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="card-stats">
                            <div class="stat-item">
                                <i class="fas fa-eye"></i>
                                <span>{{ $card->analytics->where('event_type', 'view')->count() }} views</span>
                            </div>
                            <div class="stat-item">
                                <i class="fas fa-users"></i>
                                <span>{{ $card->rsvps->count() }} RSVPs</span>
                            </div>
                            <div class="stat-item">
                                <i class="fas fa-calendar"></i>
                                <span>{{ $card->updated_at->format('M d, Y') }}</span>
                            </div>
                        </div>
                        
                        <div class="card-actions">
                            <a href="{{ $card->view_url }}" target="_blank" class="btn btn-icon btn-success" title="View Card">
                                <i class="fas fa-external-link-alt"></i>
                            </a>
                            <a href="{{ route('admin.cards.show', $card) }}" class="btn btn-icon btn-info" title="Details">
                                <i class="fas fa-info-circle"></i>
                            </a>
                            <a href="{{ route('admin.cards.edit', $card) }}" class="btn btn-icon btn-warning" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form method="POST" action="{{ route('admin.cards.toggle-published', $card) }}" style="display: inline;">
                                @csrf
                                <button type="submit" class="btn btn-icon btn-secondary" title="Unpublish">
                                    <i class="fas fa-eye-slash"></i>
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
                    <i class="fas fa-globe"></i>
                </div>
                <h3>No Published Cards Found</h3>
                <p>
                    @if(request('search'))
                        No published cards match your search criteria.
                    @else
                        No wedding cards have been published yet.
                    @endif
                </p>
                <a href="{{ route('admin.cards.index') }}" class="btn btn-primary">
                    <i class="fas fa-heart"></i>
                    View All Cards
                </a>
            </div>
        @endif
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

.stats-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.stat-card {
    background: white;
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    display: flex;
    align-items: center;
    gap: 15px;
    border-left: 4px solid #667eea;
}

.stat-icon {
    width: 50px;
    height: 50px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 20px;
}

.stat-content {
    flex: 1;
}

.stat-number {
    font-size: 28px;
    font-weight: 700;
    color: #2d3748;
    line-height: 1;
}

.stat-label {
    color: #718096;
    font-size: 14px;
    margin-top: 5px;
}

.filters-section {
    background: white;
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    margin-bottom: 30px;
}

.filter-form {
    display: flex;
    gap: 10px;
}

.filter-group {
    display: flex;
    gap: 10px;
    flex: 1;
}

.form-input {
    flex: 1;
    padding: 12px 16px;
    border: 2px solid #e2e8f0;
    border-radius: 8px;
    font-size: 14px;
    transition: all 0.3s ease;
}

.form-input:focus {
    outline: none;
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
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

.btn-search {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.btn-search:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
}

.btn-clear {
    background: #f56565;
    color: white;
}

.btn-clear:hover {
    background: #e53e3e;
}

.btn-secondary {
    background: #e2e8f0;
    color: #4a5568;
}

.btn-secondary:hover {
    background: #cbd5e0;
}

.cards-container {
    background: white;
    border-radius: 12px;
    padding: 30px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.cards-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    gap: 25px;
}

.published-card {
    background: white;
    border-radius: 12px;
    border: 2px solid #e2e8f0;
    overflow: hidden;
    transition: all 0.3s ease;
}

.published-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    border-color: #667eea;
}

.card-preview {
    position: relative;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 25px;
    text-align: center;
}

.card-preview-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.template-badge {
    background: rgba(255,255,255,0.2);
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
}

.published-badge {
    background: #48bb78;
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 5px;
}

.couple-names {
    font-size: 22px;
    font-weight: 700;
    margin: 0 0 10px 0;
    text-shadow: 0 2px 4px rgba(0,0,0,0.3);
}

.wedding-date, .wedding-venue {
    margin: 8px 0;
    opacity: 0.9;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
}

.card-info {
    padding: 20px;
    border-bottom: 1px solid #e2e8f0;
}

.card-title {
    font-weight: 700;
    color: #2d3748;
    margin-bottom: 15px;
    font-size: 16px;
}

.client-info {
    display: flex;
    align-items: center;
    gap: 12px;
}

.client-avatar {
    width: 40px;
    height: 40px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 600;
    font-size: 14px;
}

.client-details strong {
    color: #2d3748;
    display: block;
}

.client-details small {
    color: #718096;
    font-size: 12px;
}

.card-stats {
    padding: 15px 20px;
    background: #f7fafc;
    display: flex;
    justify-content: space-between;
    border-bottom: 1px solid #e2e8f0;
}

.stat-item {
    display: flex;
    align-items: center;
    gap: 5px;
    color: #718096;
    font-size: 12px;
}

.stat-item i {
    color: #4a5568;
}

.card-actions {
    padding: 15px 20px;
    display: flex;
    justify-content: center;
    gap: 10px;
}

.btn-icon {
    width: 36px;
    height: 36px;
    padding: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 8px;
}

.btn-success {
    background: #48bb78;
    color: white;
}

.btn-success:hover {
    background: #38a169;
}

.btn-info {
    background: #4299e1;
    color: white;
}

.btn-info:hover {
    background: #3182ce;
}

.btn-warning {
    background: #ed8936;
    color: white;
}

.btn-warning:hover {
    background: #dd6b20;
}

.empty-state {
    text-align: center;
    padding: 60px 20px;
}

.empty-icon {
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 20px;
    color: white;
    font-size: 32px;
}

.empty-state h3 {
    color: #2d3748;
    margin-bottom: 10px;
}

.empty-state p {
    color: #718096;
    margin-bottom: 25px;
}

.btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
}

.pagination-wrapper {
    margin-top: 30px;
    display: flex;
    justify-content: center;
}

/* Responsive Design */
@media (max-width: 768px) {
    .page-header {
        flex-direction: column;
        gap: 20px;
    }
    
    .stats-row {
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 15px;
    }
    
    .stat-card {
        padding: 15px;
    }
    
    .stat-number {
        font-size: 24px;
    }
    
    .filter-form {
        flex-direction: column;
    }
    
    .cards-grid {
        grid-template-columns: 1fr;
        gap: 20px;
    }
    
    .card-stats {
        flex-direction: column;
        gap: 10px;
    }
    
    .card-actions {
        flex-wrap: wrap;
    }
}

@media (max-width: 480px) {
    .page-content {
        padding: 15px;
    }
    
    .cards-container {
        padding: 20px;
    }
    
    .published-card {
        margin: 0 -5px;
    }
}

/* Ensure responsive behavior on mobile */
@media (max-width: 768px) {
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
</style>
@endsection 