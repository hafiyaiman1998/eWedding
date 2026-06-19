@extends('layouts.user.user')

@section('title', 'Preview Wedding Card')
@section('page_title', 'Preview Wedding Card')
@section('page_subtitle', 'See how your guests will view your invitation')

@section('content')
<div class="preview-container">
    <div class="content-card">
        <!-- Preview Controls -->
        <div class="preview-controls">
            <div class="controls-left">
                <h3>{{ $card->title }}</h3>
                <p class="preview-note">
                    <i class="fas fa-info-circle"></i>
                    This is how your guests will see your wedding invitation
                </p>
            </div>
            
            <div class="controls-right">
                <button class="btn btn-secondary" onclick="toggleMobileView()">
                    <i class="fas fa-mobile-alt"></i>
                    <span id="viewToggleText">Mobile View</span>
                </button>
                
                <a href="{{ route('user.cards.edit', $card) }}" class="btn btn-primary">
                    <i class="fas fa-edit"></i>
                    Edit Card
                </a>
                
                @if($card->is_published)
                    <a href="{{ route('user.cards.share', $card) }}" class="btn btn-success">
                        <i class="fas fa-share-alt"></i>
                        Share Card
                    </a>
                @endif
            </div>
        </div>

        <!-- Preview Frame -->
        <div class="preview-wrapper">
            <div class="device-frame" id="deviceFrame">
                <div class="device-screen">
                    <div class="card-preview" id="cardPreview">
                        <!-- Render the actual card content -->
                        @if($card->designTemplate && $card->designTemplate->full_html_template)
                            <!-- Full HTML Template Preview -->
                            <iframe 
                                src="{{ route('wedding-card.view', $card->unique_url) }}" 
                                class="preview-iframe"
                                id="previewIframe">
                            </iframe>
                        @else
                            <!-- Fallback Preview -->
                            <div class="fallback-preview">
                                <div class="preview-header">
                                    <h1 class="wedding-title">{{ $card->title }}</h1>
                                </div>
                                
                                <div class="preview-content">
                                    <div class="couple-section">
                                        <h2 class="couple-names">
                                            {{ $card->card_details['bride_name'] ?? 'Bride Name' }}
                                            <span class="separator">&</span>
                                            {{ $card->card_details['groom_name'] ?? 'Groom Name' }}
                                        </h2>
                                        
                                        @if(isset($card->card_details['bride_parents']) || isset($card->card_details['groom_parents']))
                                            <div class="parents-section">
                                                @if(isset($card->card_details['bride_parents']))
                                                    <p>Daughter of {{ $card->card_details['bride_parents'] }}</p>
                                                @endif
                                                @if(isset($card->card_details['groom_parents']))
                                                    <p>Son of {{ $card->card_details['groom_parents'] }}</p>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                    
                                    <div class="event-section">
                                        <h3>Wedding Ceremony</h3>
                                        <div class="event-details">
                                            <p class="date">
                                                <i class="fas fa-calendar"></i>
                                                {{ $card->card_details['wedding_date'] ?? 'Wedding Date' }}
                                            </p>
                                            <p class="time">
                                                <i class="fas fa-clock"></i>
                                                {{ $card->card_details['wedding_time'] ?? 'Wedding Time' }}
                                            </p>
                                            <p class="venue">
                                                <i class="fas fa-map-marker-alt"></i>
                                                {{ $card->card_details['venue_name'] ?? 'Venue Name' }}
                                            </p>
                                            <p class="address">
                                                {{ $card->card_details['venue_address'] ?? 'Venue Address' }}
                                            </p>
                                        </div>
                                    </div>
                                    
                                    @if(isset($card->card_details['akad_date']) && $card->card_details['akad_date'])
                                        <div class="akad-section">
                                            <h3>Akad Nikah</h3>
                                            <div class="event-details">
                                                <p class="date">
                                                    <i class="fas fa-calendar"></i>
                                                    {{ $card->card_details['akad_date'] }}
                                                </p>
                                                @if(isset($card->card_details['akad_time']))
                                                    <p class="time">
                                                        <i class="fas fa-clock"></i>
                                                        {{ $card->card_details['akad_time'] }}
                                                    </p>
                                                @endif
                                                @if(isset($card->card_details['akad_venue']))
                                                    <p class="venue">
                                                        <i class="fas fa-map-marker-alt"></i>
                                                        {{ $card->card_details['akad_venue'] }}
                                                    </p>
                                                @endif
                                            </div>
                                        </div>
                                    @endif
                                    
                                    @if($card->custom_message)
                                        <div class="message-section">
                                            <h3>Special Message</h3>
                                            <p class="custom-message">{{ $card->custom_message }}</p>
                                        </div>
                                    @endif
                                    
                                    <div class="rsvp-section">
                                        <h3>RSVP</h3>
                                        @if(isset($card->card_details['rsvp_date']))
                                            <p>Please respond by {{ $card->card_details['rsvp_date'] }}</p>
                                        @endif
                                        
                                        <div class="contact-info">
                                            @if(isset($card->card_details['contact_bride']))
                                                <p>Bride: {{ $card->card_details['contact_bride'] }}</p>
                                            @endif
                                            @if(isset($card->card_details['contact_groom']))
                                                <p>Groom: {{ $card->card_details['contact_groom'] }}</p>
                                            @endif
                                        </div>
                                        
                                        <div class="rsvp-buttons">
                                            <button class="rsvp-btn attending">
                                                <i class="fas fa-check"></i>
                                                I'll be there
                                            </button>
                                            <button class="rsvp-btn not-attending">
                                                <i class="fas fa-times"></i>
                                                Can't make it
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="preview-footer">
                                    <p>Created with eWeddingCard</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Preview Actions -->
        <div class="preview-actions">
            <div class="action-group">
                <h4>Test Your Card</h4>
                <div class="test-buttons">
                    <button class="btn btn-secondary" onclick="refreshPreview()">
                        <i class="fas fa-sync"></i>
                        Refresh Preview
                    </button>
                    
                    <a href="{{ $card->view_url }}" target="_blank" class="btn btn-info">
                        <i class="fas fa-external-link-alt"></i>
                        Open in New Tab
                    </a>
                </div>
            </div>
            
            <div class="action-group">
                <h4>Share & Publish</h4>
                <div class="publish-buttons">
                    @if(!$card->is_published)
                        <form method="POST" action="{{ route('user.cards.toggle-published', $card) }}" style="display: inline;">
                            @csrf
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-globe"></i>
                                Publish Card
                            </button>
                        </form>
                    @else
                        <span class="status published">
                            <i class="fas fa-check-circle"></i>
                            Card is Published
                        </span>
                        
                        <form method="POST" action="{{ route('user.cards.toggle-published', $card) }}" style="display: inline;">
                            @csrf
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-eye-slash"></i>
                                Unpublish
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        <!-- Navigation -->
        <div class="navigation-section">
            <a href="{{ route('user.cards.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i>
                Back to My Cards
            </a>
            
            <div class="nav-buttons">
                <a href="{{ route('user.cards.edit', $card) }}" class="btn btn-primary">
                    <i class="fas fa-edit"></i>
                    Edit Card
                </a>
                
                @if($card->is_published)
                    <a href="{{ route('user.cards.analytics', $card) }}" class="btn btn-info">
                        <i class="fas fa-chart-line"></i>
                        View Analytics
                    </a>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
.preview-container {
    max-width: 1200px;
    margin: 0 auto;
}

.preview-controls {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
    padding: 25px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 15px;
    border: 1px solid rgba(255, 255, 255, 0.2);
}

.controls-left h3 {
    color: #2c3e50;
    font-size: 24px;
    margin-bottom: 5px;
}

.preview-note {
    color: #7f8c8d;
    font-size: 14px;
    margin: 0;
}

.controls-right {
    display: flex;
    gap: 15px;
}

.preview-wrapper {
    display: flex;
    justify-content: center;
    margin-bottom: 40px;
    padding: 40px 20px;
    background: rgba(255, 255, 255, 0.05);
    border-radius: 15px;
}

.device-frame {
    position: relative;
    background: #2c3e50;
    border-radius: 20px;
    padding: 20px;
    transition: all 0.3s ease;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
}

.device-frame.mobile {
    width: 375px;
    height: 667px;
}

.device-frame.desktop {
    width: 1024px;
    height: 768px;
}

.device-screen {
    width: 100%;
    height: 100%;
    background: white;
    border-radius: 10px;
    overflow: hidden;
    position: relative;
}

.card-preview {
    width: 100%;
    height: 100%;
    overflow-y: auto;
}

.preview-iframe {
    width: 100%;
    height: 100%;
    border: none;
}

.fallback-preview {
    padding: 40px 30px;
    font-family: 'Poppins', sans-serif;
    text-align: center;
    background: linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%);
    min-height: 100%;
    box-sizing: border-box;
}

.preview-header {
    margin-bottom: 40px;
}

.wedding-title {
    font-family: 'Dancing Script', cursive;
    font-size: 36px;
    color: #2c3e50;
    margin-bottom: 20px;
}

.couple-section {
    margin-bottom: 40px;
}

.couple-names {
    font-family: 'Dancing Script', cursive;
    font-size: 48px;
    color: #e74c3c;
    margin-bottom: 20px;
    line-height: 1.2;
}

.separator {
    font-size: 36px;
    color: #f39c12;
    margin: 0 15px;
}

.parents-section {
    color: #7f8c8d;
    font-size: 16px;
    margin-top: 15px;
}

.event-section, .akad-section, .message-section, .rsvp-section {
    margin-bottom: 35px;
    padding: 25px;
    background: rgba(255, 255, 255, 0.8);
    border-radius: 15px;
    backdrop-filter: blur(10px);
}

.event-section h3, .akad-section h3, .message-section h3, .rsvp-section h3 {
    font-family: 'Dancing Script', cursive;
    font-size: 32px;
    color: #2c3e50;
    margin-bottom: 20px;
}

.event-details p {
    margin-bottom: 10px;
    color: #2c3e50;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
}

.date, .time, .venue {
    font-size: 18px;
    font-weight: 600;
}

.address {
    font-size: 16px;
    color: #7f8c8d;
}

.custom-message {
    font-size: 18px;
    line-height: 1.6;
    color: #2c3e50;
    font-style: italic;
}

.contact-info {
    margin-bottom: 20px;
    color: #7f8c8d;
}

.rsvp-buttons {
    display: flex;
    gap: 15px;
    justify-content: center;
}

.rsvp-btn {
    padding: 12px 24px;
    border: none;
    border-radius: 25px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 8px;
}

.rsvp-btn.attending {
    background: #2ecc71;
    color: white;
}

.rsvp-btn.not-attending {
    background: #e74c3c;
    color: white;
}

.rsvp-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
}

.preview-footer {
    margin-top: 40px;
    padding-top: 20px;
    border-top: 1px solid rgba(255, 255, 255, 0.3);
    color: #7f8c8d;
    font-size: 14px;
}

.preview-actions {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 30px;
    margin-bottom: 40px;
    padding: 30px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 15px;
}

.action-group h4 {
    color: #2c3e50;
    font-size: 18px;
    margin-bottom: 15px;
}

.test-buttons, .publish-buttons {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

.status.published {
    padding: 8px 16px;
    background: rgba(46, 204, 113, 0.2);
    color: #27ae60;
    border-radius: 20px;
    font-size: 14px;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.navigation-section {
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-top: 1px solid rgba(255, 255, 255, 0.2);
    padding-top: 30px;
}

.nav-buttons {
    display: flex;
    gap: 15px;
}

@media (max-width: 768px) {
    .preview-controls {
        flex-direction: column;
        gap: 20px;
        text-align: center;
    }
    
    .controls-right {
        flex-wrap: wrap;
        justify-content: center;
    }
    
    .device-frame {
        width: 100% !important;
        max-width: 400px;
        height: 600px !important;
    }
    
    .fallback-preview {
        padding: 20px 15px;
    }
    
    .couple-names {
        font-size: 32px;
    }
    
    .wedding-title {
        font-size: 28px;
    }
    
    .preview-actions {
        grid-template-columns: 1fr;
        gap: 20px;
    }
    
    .navigation-section {
        flex-direction: column;
        gap: 20px;
    }
    
    .nav-buttons {
        justify-content: center;
        flex-wrap: wrap;
    }
}
</style>

<script>
let isMobileView = false;

function toggleMobileView() {
    const deviceFrame = document.getElementById('deviceFrame');
    const toggleText = document.getElementById('viewToggleText');
    
    isMobileView = !isMobileView;
    
    if (isMobileView) {
        deviceFrame.className = 'device-frame mobile';
        toggleText.textContent = 'Desktop View';
    } else {
        deviceFrame.className = 'device-frame desktop';
        toggleText.textContent = 'Mobile View';
    }
}

function refreshPreview() {
    const iframe = document.getElementById('previewIframe');
    if (iframe) {
        iframe.src = iframe.src;
    } else {
        location.reload();
    }
}

// Initialize with desktop view
document.addEventListener('DOMContentLoaded', function() {
    const deviceFrame = document.getElementById('deviceFrame');
    deviceFrame.className = 'device-frame desktop';
});
</script>
@endsection 