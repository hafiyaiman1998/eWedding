@extends('layouts.admin.admin')

@section('title', 'Wedding Card Details')

@section('content')
<div class="page-content">
    <div class="page-header">
        <div class="page-header-left">
            <h1 class="page-title">
                <i class="fas fa-heart"></i>
                Wedding Card Details
            </h1>
            <p class="page-subtitle">View complete information about this wedding invitation</p>
        </div>
        <div class="page-header-right">
            <div class="header-actions">
                @if($card->is_published)
                    <a href="{{ $card->view_url }}" target="_blank" class="btn btn-success">
                        <i class="fas fa-external-link-alt"></i>
                        View Live Card
                    </a>
                @endif
                <a href="{{ route('admin.cards.edit', $card) }}" class="btn btn-warning">
                    <i class="fas fa-edit"></i>
                    Edit Card
                </a>
                <a href="{{ route('admin.cards.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i>
                    Back to Cards
                </a>
            </div>
        </div>
    </div>

    <!-- Card Overview -->
    <div class="card-overview">
        <div class="overview-sections">
            <!-- Card Status -->
            <div class="status-section">
                <div class="status-card">
                    <div class="status-header">
                        <h3>Card Status</h3>
                        <div class="status-badge {{ $card->is_published ? 'published' : 'draft' }}">
                            <i class="fas {{ $card->is_published ? 'fa-globe' : 'fa-edit' }}"></i>
                            {{ $card->is_published ? 'Published' : 'Draft' }}
                        </div>
                    </div>
                    <div class="status-details">
                        <div class="detail-item">
                            <span class="label">Created:</span>
                            <span class="value">{{ $card->created_at->format('M d, Y g:i A') }}</span>
                        </div>
                        <div class="detail-item">
                            <span class="label">Last Updated:</span>
                            <span class="value">{{ $card->updated_at->format('M d, Y g:i A') }}</span>
                        </div>
                        @if($card->is_published)
                            <div class="detail-item">
                                <span class="label">Public URL:</span>
                                <span class="value">
                                    <a href="{{ $card->view_url }}" target="_blank" class="url-link">
                                        {{ $card->view_url }}
                                        <i class="fas fa-external-link-alt"></i>
                                    </a>
                                </span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="stats-section">
                <div class="stats-grid">
                    <div class="stat-item">
                        <div class="stat-icon views">
                            <i class="fas fa-eye"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-number">{{ $card->analytics->where('event_type', 'view')->count() }}</div>
                            <div class="stat-label">Total Views</div>
                        </div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-icon shares">
                            <i class="fas fa-share"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-number">{{ $card->analytics->where('event_type', 'share')->count() }}</div>
                            <div class="stat-label">Total Shares</div>
                        </div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-icon rsvps">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-number">{{ $card->rsvps->count() }}</div>
                            <div class="stat-label">RSVP Responses</div>
                        </div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-icon attending">
                            <i class="fas fa-user-check"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-number">{{ $card->rsvps->where('attendance_status', 'yes')->count() }}</div>
                            <div class="stat-label">Attending</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="content-sections">
            <!-- Card Information -->
            <div class="info-section">
                <div class="section-card">
                    <div class="section-header">
                        <h3>
                            <i class="fas fa-info-circle"></i>
                            Card Information
                        </h3>
                    </div>
                    <div class="section-content">
                        <div class="info-grid">
                            <div class="info-item">
                                <label>Card Title:</label>
                                <value>{{ $card->title }}</value>
                            </div>
                            <div class="info-item">
                                <label>Template Used:</label>
                                <value>{{ $card->designTemplate->name }}</value>
                            </div>
                            <div class="info-item">
                                <label>Template Category:</label>
                                <value>{{ $card->designTemplate->category ?? 'General' }}</value>
                            </div>
                            @if($card->custom_message)
                                <div class="info-item full-width">
                                    <label>Custom Message:</label>
                                    <value class="message">{{ $card->custom_message }}</value>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Client Information -->
            <div class="client-section">
                <div class="section-card">
                    <div class="section-header">
                        <h3>
                            <i class="fas fa-user"></i>
                            Client Information
                        </h3>
                    </div>
                    <div class="section-content">
                        <div class="client-info">
                            <div class="client-avatar-large">
                                {{ strtoupper(substr($card->user->name, 0, 1)) }}
                            </div>
                            <div class="client-details">
                                <h4>{{ $card->user->name }}</h4>
                                <p class="client-email">{{ $card->user->email }}</p>
                                <p class="client-joined">Joined {{ $card->user->created_at->format('M d, Y') }}</p>
                                <div class="client-actions">
                                    <a href="{{ route('admin.users.show', $card->user) }}" class="btn btn-sm btn-outline">
                                        <i class="fas fa-user"></i>
                                        View Client Profile
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Wedding Details -->
            <div class="details-section">
                <div class="section-card">
                    <div class="section-header">
                        <h3>
                            <i class="fas fa-heart"></i>
                            Wedding Details
                        </h3>
                    </div>
                    <div class="section-content">
                        <div class="wedding-preview">
                            <div class="couple-info">
                                <h4 class="couple-names">
                                    {{ $card->card_details['bride_name'] ?? 'Bride Name' }} 
                                    & 
                                    {{ $card->card_details['groom_name'] ?? 'Groom Name' }}
                                </h4>
                            </div>
                            
                            <div class="wedding-details-grid">
                                <div class="detail-row">
                                    <div class="detail-icon">
                                        <i class="fas fa-calendar"></i>
                                    </div>
                                    <div class="detail-content">
                                        <strong>Wedding Date</strong>
                                        <span>{{ $card->card_details['wedding_date'] ?? 'Date not set' }}</span>
                                    </div>
                                </div>
                                
                                @if(!empty($card->card_details['wedding_time']))
                                    <div class="detail-row">
                                        <div class="detail-icon">
                                            <i class="fas fa-clock"></i>
                                        </div>
                                        <div class="detail-content">
                                            <strong>Wedding Time</strong>
                                            <span>{{ $card->card_details['wedding_time'] }}</span>
                                        </div>
                                    </div>
                                @endif
                                
                                @if(!empty($card->card_details['venue']))
                                    <div class="detail-row">
                                        <div class="detail-icon">
                                            <i class="fas fa-map-marker-alt"></i>
                                        </div>
                                        <div class="detail-content">
                                            <strong>Venue</strong>
                                            <span>{{ $card->card_details['venue'] }}</span>
                                        </div>
                                    </div>
                                @endif
                                
                                @if(!empty($card->card_details['address']))
                                    <div class="detail-row">
                                        <div class="detail-icon">
                                            <i class="fas fa-location-arrow"></i>
                                        </div>
                                        <div class="detail-content">
                                            <strong>Address</strong>
                                            <span>{{ $card->card_details['address'] }}</span>
                                        </div>
                                    </div>
                                @endif
                                
                                @if(!empty($card->card_details['contact_bride']))
                                    <div class="detail-row">
                                        <div class="detail-icon">
                                            <i class="fas fa-phone"></i>
                                        </div>
                                        <div class="detail-content">
                                            <strong>Bride Contact</strong>
                                            <span>{{ $card->card_details['contact_bride'] }}</span>
                                        </div>
                                    </div>
                                @endif
                                
                                @if(!empty($card->card_details['contact_groom']))
                                    <div class="detail-row">
                                        <div class="detail-icon">
                                            <i class="fas fa-phone"></i>
                                        </div>
                                        <div class="detail-content">
                                            <strong>Groom Contact</strong>
                                            <span>{{ $card->card_details['contact_groom'] }}</span>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="action-section">
        <div class="action-buttons">
            <form method="POST" action="{{ route('admin.cards.toggle-published', $card) }}" style="display: inline;">
                @csrf
                <button type="submit" class="btn {{ $card->is_published ? 'btn-warning' : 'btn-success' }}">
                    <i class="fas {{ $card->is_published ? 'fa-eye-slash' : 'fa-globe' }}"></i>
                    {{ $card->is_published ? 'Unpublish Card' : 'Publish Card' }}
                </button>
            </form>
            
            <form method="POST" action="{{ route('admin.cards.destroy', $card) }}" 
                  class="delete-form" 
                  data-delete-type="wedding card" 
                  data-delete-name="{{ $card->title ?: 'Untitled Card' }}"
                  data-delete-warning="All card data, analytics, and RSVP responses will be permanently lost."
                  style="display: inline;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger delete-btn">
                    <i class="fas fa-trash"></i>
                    Delete Card
                </button>
            </form>
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

.header-actions {
    display: flex;
    gap: 10px;
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

.btn-success {
    background: #38a169;
    color: white;
}

.btn-success:hover {
    background: #2f855a;
}

.btn-warning {
    background: #ed8936;
    color: white;
}

.btn-warning:hover {
    background: #dd6b20;
}

.btn-secondary {
    background: #e2e8f0;
    color: #4a5568;
}

.btn-secondary:hover {
    background: #cbd5e0;
}

.btn-danger {
    background: #e53e3e;
    color: white;
}

.btn-danger:hover {
    background: #c53030;
}

/* Card Overview */
.card-overview {
    margin-bottom: 30px;
}

.overview-sections {
    display: grid;
    grid-template-columns: 1fr 2fr;
    gap: 25px;
}

.status-section, .stats-section {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    overflow: hidden;
}

.status-card {
    padding: 25px;
}

.status-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.status-header h3 {
    color: #2d3748;
    margin: 0;
}

.status-badge {
    padding: 8px 16px;
    border-radius: 20px;
    font-size: 14px;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 8px;
}

.status-badge.published {
    background: #c6f6d5;
    color: #22543d;
}

.status-badge.draft {
    background: #fed7d7;
    color: #742a2a;
}

.status-details {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.detail-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 8px 0;
    border-bottom: 1px solid #f7fafc;
}

.detail-item .label {
    color: #718096;
    font-weight: 600;
}

.detail-item .value {
    color: #2d3748;
}

.url-link {
    color: #667eea;
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 5px;
}

.url-link:hover {
    color: #5a67d8;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 20px;
    padding: 25px;
}

.stat-item {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 15px;
    background: #f7fafc;
    border-radius: 8px;
}

.stat-icon {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 20px;
}

.stat-icon.views {
    background: linear-gradient(135deg, #4299e1 0%, #3182ce 100%);
}

.stat-icon.shares {
    background: linear-gradient(135deg, #38a169 0%, #2f855a 100%);
}

.stat-icon.rsvps {
    background: linear-gradient(135deg, #ed8936 0%, #dd6b20 100%);
}

.stat-icon.attending {
    background: linear-gradient(135deg, #e53e3e 0%, #c53030 100%);
}

.stat-number {
    font-size: 24px;
    font-weight: 700;
    color: #2d3748;
    line-height: 1;
}

.stat-label {
    color: #718096;
    font-size: 12px;
    margin-top: 2px;
}

/* Main Content */
.main-content {
    margin-bottom: 30px;
}

.content-sections {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 25px;
}

.details-section {
    grid-column: 1 / -1;
}

.section-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    overflow: hidden;
}

.section-header {
    padding: 20px;
    border-bottom: 1px solid #e2e8f0;
    background: #f7fafc;
}

.section-header h3 {
    color: #2d3748;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 10px;
}

.section-content {
    padding: 25px;
}

.info-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}

.info-item {
    display: flex;
    flex-direction: column;
    gap: 5px;
}

.info-item.full-width {
    grid-column: 1 / -1;
}

.info-item label {
    color: #718096;
    font-weight: 600;
    font-size: 14px;
}

.info-item value {
    color: #2d3748;
    font-size: 16px;
}

.info-item value.message {
    background: #f7fafc;
    padding: 12px;
    border-radius: 8px;
    border-left: 4px solid #667eea;
}

.wedding-preview {
    text-align: center;
}

.couple-names {
    color: #2d3748;
    font-size: 28px;
    font-weight: 700;
    margin: 0 0 30px 0;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.wedding-details-grid {
    display: flex;
    flex-direction: column;
    gap: 20px;
    text-align: left;
}

.detail-row {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 15px;
    background: #f7fafc;
    border-radius: 8px;
}

.detail-icon {
    width: 40px;
    height: 40px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 16px;
}

.detail-content {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 5px;
}

.detail-content strong {
    color: #2d3748;
    font-weight: 600;
}

.detail-content span {
    color: #4a5568;
}

.client-info {
    display: flex;
    align-items: center;
    gap: 20px;
}

.client-avatar-large {
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 32px;
    font-weight: 700;
}

.client-details {
    flex: 1;
}

.client-details h4 {
    color: #2d3748;
    margin: 0 0 5px 0;
    font-size: 20px;
}

.client-email {
    color: #4a5568;
    margin: 0 0 5px 0;
}

.client-joined {
    color: #718096;
    margin: 0 0 15px 0;
    font-size: 14px;
}

.btn-sm {
    padding: 8px 16px;
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

.action-section {
    background: white;
    border-radius: 12px;
    padding: 25px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.action-buttons {
    display: flex;
    justify-content: center;
    gap: 15px;
}

/* Responsive Design */
@media (max-width: 768px) {
    .page-header {
        flex-direction: column;
        gap: 20px;
    }
    
    .header-actions {
        flex-direction: column;
        width: 100%;
    }
    
    .overview-sections {
        grid-template-columns: 1fr;
    }
    
    .stats-grid {
        grid-template-columns: 1fr;
    }
    
    .content-sections {
        grid-template-columns: 1fr;
    }
    
    .info-grid {
        grid-template-columns: 1fr;
    }
    
    .client-info {
        flex-direction: column;
        text-align: center;
    }
    
    .action-buttons {
        flex-direction: column;
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
    
    .couple-names {
        font-size: 24px;
    }
    
    .detail-row {
        padding: 12px;
    }
    
    .client-avatar-large {
        width: 60px;
        height: 60px;
        font-size: 24px;
    }
}
</style>
@endsection 