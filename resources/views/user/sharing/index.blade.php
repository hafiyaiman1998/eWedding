@extends('layouts.user.user')

@section('title', 'Share Wedding Card')
@section('page_title', 'Share Wedding Card')
@section('page_subtitle', 'Share your beautiful invitation with friends and family')

@section('content')
<div class="share-container">
    <div class="content-card">
        <!-- Card Preview -->
        <div class="card-preview-section">
            <h3 class="section-title">
                <i class="fas fa-eye"></i>
                Card Preview
            </h3>
            
            <div class="preview-card">
                <div class="preview-header">
                    <h4>{{ $card->title }}</h4>
                    <span class="card-url">{{ $card->view_url }}</span>
                </div>
                
                <div class="preview-content">
                    <div class="couple-info">
                        <h5>{{ $card->card_details['bride_name'] ?? 'Bride' }} & {{ $card->card_details['groom_name'] ?? 'Groom' }}</h5>
                        <p><i class="fas fa-calendar"></i> {{ $card->card_details['wedding_date'] ?? 'Date TBD' }}</p>
                        <p><i class="fas fa-map-marker-alt"></i> {{ $card->card_details['venue_name'] ?? 'Venue TBD' }}</p>
                    </div>
                    
                    @if($card->designTemplate && $card->designTemplate->preview_image)
                        <div class="template-preview">
                            <img src="{{ asset('storage/' . $card->designTemplate->preview_image) }}" alt="Template Preview">
                        </div>
                    @endif
                </div>
                
                <div class="preview-actions">
                    <a href="{{ $card->view_url }}" target="_blank" class="btn btn-secondary">
                        <i class="fas fa-external-link-alt"></i>
                        View Full Card
                    </a>
                </div>
            </div>
        </div>

        <!-- QR Code Section -->
        <div class="qr-section">
            <h3 class="section-title">
                <i class="fas fa-qrcode"></i>
                QR Code
            </h3>
            
            <div class="qr-container">
                <div class="qr-code">
                    <div class="qr-placeholder">
                        <i class="fas fa-qrcode"></i>
                        <p>QR Code for {{ $card->title }}</p>
                        <small>Scan to view wedding card</small>
                    </div>
                </div>
                
                <div class="qr-actions">
                    <button class="btn btn-primary" onclick="generateQR()">
                        <i class="fas fa-download"></i>
                        Download QR Code
                    </button>
                    <button class="btn btn-secondary" onclick="printQR()">
                        <i class="fas fa-print"></i>
                        Print QR Code
                    </button>
                </div>
            </div>
            
            <div class="qr-info">
                <p><strong>Card URL:</strong></p>
                <div class="url-container">
                    <input type="text" class="url-input" value="{{ $card->view_url }}" readonly id="cardUrl">
                    <button class="btn btn-sm btn-secondary" onclick="copyUrl()">
                        <i class="fas fa-copy"></i>
                        Copy
                    </button>
                </div>
            </div>
        </div>

        <!-- Social Sharing -->
        <div class="sharing-section">
            <h3 class="section-title">
                <i class="fas fa-share-alt"></i>
                Share via Social Media
            </h3>
            
            <div class="sharing-grid">
                <!-- WhatsApp Sharing -->
                <div class="share-option whatsapp">
                    <div class="share-icon">
                        <i class="fab fa-whatsapp"></i>
                    </div>
                    <div class="share-content">
                        <h4>WhatsApp</h4>
                        <p>Share with your contacts instantly</p>
                        
                        <div class="share-message">
                            <label>Select message format:</label>
                            <select id="whatsappFormatSelector" class="format-selector" onchange="updateWhatsAppMessage()">
                                <option value="original">Original Format</option>
                                <option value="formal">Formal Format</option>
                            </select>
                            
                            <label style="margin-top: 15px;">Customize your message:</label>
                            <textarea id="whatsappMessage" class="share-textarea" rows="8">Undangan Majlis Perkahwinan {{ $card->card_details['bride_name'] ?? 'Pengantin Perempuan' }} & {{ $card->card_details['groom_name'] ?? 'Pengantin Lelaki' }} 💌

Bismillahirrahmanirrahim
Assalamualaikum w.b.t & Salam Sejahtera.

Dengan penuh kesyukuran, kami sekeluarga menjemput Yang Berbahagia Tan Sri/ Puan Sri/ Dato' Seri/ Datin Seri/ Dato'/ Datin/ Tuan/ Puan/ Encik/ Cik untuk memeriahkan majlis perkahwinan kami.

💍 Pasangan: {{ $card->card_details['bride_name'] ?? 'Pengantin Perempuan' }} & {{ $card->card_details['groom_name'] ?? 'Pengantin Lelaki' }}
🗓 Tarikh: {{ $card->card_details['wedding_date'] ?? 'Akan dimaklumkan' }}
📍 Lokasi: {{ $card->card_details['venue_name'] ?? 'Akan dimaklumkan' }}

Maklumat lengkap (lokasi, aturcara & RSVP) boleh dilihat di kad jemputan di pautan berikut:
{{ $card->view_url }}

Kehadiran dan doa anda amat kami alu-alukan. Terima kasih banyak-banyak. 🙏🎉</textarea>
                        </div>
                        
                        <div class="share-actions">
                            <a href="#" id="whatsappLink" class="btn btn-success" target="_blank" onclick="trackShare('whatsapp')">
                                <i class="fab fa-whatsapp"></i>
                                Share on WhatsApp
                            </a>
                            <button type="button" class="btn btn-secondary" onclick="copyWhatsAppMessage()">
                                <i class="fas fa-copy"></i>
                                Copy Message
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Telegram Sharing -->
                <div class="share-option telegram">
                    <div class="share-icon">
                        <i class="fab fa-telegram"></i>
                    </div>
                    <div class="share-content">
                        <h4>Telegram</h4>
                        <p>Share with your Telegram contacts</p>
                        
                        <div class="share-message">
                            <label>Select message format:</label>
                            <select id="telegramFormatSelector" class="format-selector" onchange="updateTelegramMessage()">
                                <option value="original">Original Format</option>
                                <option value="formal">Formal Format</option>
                            </select>
                            
                            <label style="margin-top: 15px;">Customize your message:</label>
                            <textarea id="telegramMessage" class="share-textarea" rows="8">Jemputan Majlis Perkahwinan ✨

Bismillahirrahmanirrahim. Assalamualaikum & Salam Sejahtera.
Kami dengan penuh kesyukuran menjemput anda ke majlis perkahwinan:
{{ $card->card_details['bride_name'] ?? 'Pengantin Perempuan' }} & {{ $card->card_details['groom_name'] ?? 'Pengantin Lelaki' }}

💍 Pasangan: {{ $card->card_details['bride_name'] ?? 'Pengantin Perempuan' }} & {{ $card->card_details['groom_name'] ?? 'Pengantin Lelaki' }}
🗓 Tarikh: {{ $card->card_details['wedding_date'] ?? 'Akan dimaklumkan' }}
📍 Lokasi: {{ $card->card_details['venue_name'] ?? 'Akan dimaklumkan' }}

Butiran penuh & RSVP:
{{ $card->view_url }}

Mohon doa yang baik-baik. Kehadiran anda memeriahkan majlis kami. 🙏🎊</textarea>
                        </div>
                        
                        <div class="share-actions">
                            <a href="#" id="telegramLink" class="btn btn-info" target="_blank" onclick="trackShare('telegram')">
                                <i class="fab fa-telegram"></i>
                                Share on Telegram
                            </a>
                            <button type="button" class="btn btn-secondary" onclick="copyTelegramMessage()">
                                <i class="fas fa-copy"></i>
                                Copy Message
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Email Sharing -->
                <div class="share-option email">
                    <div class="share-icon">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <div class="share-content">
                        <h4>Email</h4>
                        <p>Send via email to your contacts</p>
                        
                        <div class="share-actions">
                            <a href="#" id="emailLink" class="btn btn-warning" onclick="trackShare('email')">
                                <i class="fas fa-envelope"></i>
                                Send Email
                            </a>
                            <button type="button" class="btn btn-secondary" onclick="copyEmailContent()">
                                <i class="fas fa-copy"></i>
                                Copy Content
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Facebook Sharing -->
                <div class="share-option facebook">
                    <div class="share-icon">
                        <i class="fab fa-facebook"></i>
                    </div>
                    <div class="share-content">
                        <h4>Facebook</h4>
                        <p>Share on your Facebook timeline</p>
                        
                        <div class="share-actions">
                            <a href="#" id="facebookLink" class="btn" style="background: #1877f2; color: white;" target="_blank" onclick="trackShare('facebook')">
                                <i class="fab fa-facebook"></i>
                                Share on Facebook
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sharing Stats -->
        <div class="stats-section">
            <h3 class="section-title">
                <i class="fas fa-chart-line"></i>
                Sharing Statistics
            </h3>
            
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-eye"></i>
                    </div>
                    <div class="stat-info">
                        <div class="stat-number">{{ $analytics['total_views'] ?? 0 }}</div>
                        <div class="stat-label">Total Views</div>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-share"></i>
                    </div>
                    <div class="stat-info">
                        <div class="stat-number">{{ $analytics['total_shares'] ?? 0 }}</div>
                        <div class="stat-label">Times Shared</div>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-info">
                        <div class="stat-number">{{ $analytics['total_rsvps'] ?? 0 }}</div>
                        <div class="stat-label">RSVP Responses</div>
                    </div>
                </div>
            </div>
            
            <div class="stats-actions">
                <a href="{{ route('user.cards.analytics', $card) }}" class="btn btn-primary">
                    <i class="fas fa-chart-bar"></i>
                    View Detailed Analytics
                </a>
            </div>
        </div>

        <!-- Navigation -->
        <div class="navigation-section">
            <a href="{{ route('user.cards.edit', $card) }}" class="btn btn-secondary">
                <i class="fas fa-edit"></i>
                Edit Card
            </a>
            <a href="{{ route('user.cards.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i>
                Back to My Cards
            </a>
        </div>
    </div>
</div>

<style>
.share-container {
    max-width: 1000px;
    margin: 0 auto;
}

.section-title {
    font-size: 24px;
    color: #2c3e50;
    margin-bottom: 25px;
    display: flex;
    align-items: center;
    gap: 10px;
    padding-bottom: 15px;
    border-bottom: 2px solid rgba(255, 255, 255, 0.2);
}

.card-preview-section {
    margin-bottom: 40px;
}

.preview-card {
    background: rgba(255, 255, 255, 0.2);
    border-radius: 15px;
    padding: 25px;
    border: 1px solid rgba(255, 255, 255, 0.3);
}

.preview-header {
    text-align: center;
    margin-bottom: 20px;
}

.preview-header h4 {
    color: #2c3e50;
    font-size: 20px;
    margin-bottom: 5px;
}

.card-url {
    color: #7f8c8d;
    font-size: 14px;
    word-break: break-all;
}

.preview-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.couple-info h5 {
    color: #e74c3c;
    font-size: 18px;
    margin-bottom: 10px;
}

.couple-info p {
    color: #7f8c8d;
    margin-bottom: 5px;
}

.template-preview {
    width: 120px;
    height: 80px;
    border-radius: 8px;
    overflow: hidden;
}

.template-preview img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.preview-actions {
    text-align: center;
}

.qr-section {
    margin-bottom: 40px;
}

.qr-container {
    display: flex;
    gap: 30px;
    align-items: center;
    margin-bottom: 20px;
}

.qr-code {
    width: 200px;
    height: 200px;
    background: white;
    border-radius: 15px;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 1px solid #ddd;
}

.qr-placeholder {
    text-align: center;
    color: #7f8c8d;
}

.qr-placeholder i {
    font-size: 48px;
    margin-bottom: 10px;
    display: block;
}

.qr-actions {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.qr-info {
    background: rgba(255, 255, 255, 0.1);
    padding: 20px;
    border-radius: 10px;
}

.url-container {
    display: flex;
    gap: 10px;
    margin-top: 10px;
}

.url-input {
    flex: 1;
    padding: 10px;
    border: 1px solid rgba(255, 255, 255, 0.3);
    border-radius: 8px;
    background: rgba(255, 255, 255, 0.2);
    color: #2c3e50;
}

.sharing-section {
    margin-bottom: 40px;
}

.sharing-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 25px;
    margin-bottom: 30px;
}

.share-option {
    background: rgba(255, 255, 255, 0.2);
    border-radius: 15px;
    padding: 25px;
    border: 1px solid rgba(255, 255, 255, 0.3);
}

.share-option.whatsapp {
    border-left: 4px solid #25d366;
}

.share-option.telegram {
    border-left: 4px solid #0088cc;
}

.share-option.email {
    border-left: 4px solid #ea4335;
}

.share-option.facebook {
    border-left: 4px solid #1877f2;
}

.share-icon {
    text-align: center;
    margin-bottom: 15px;
}

.share-icon i {
    font-size: 36px;
    color: #7f8c8d;
}

.share-content h4 {
    color: #2c3e50;
    font-size: 18px;
    margin-bottom: 5px;
    text-align: center;
}

.share-content p {
    color: #7f8c8d;
    text-align: center;
    margin-bottom: 20px;
}

.share-message {
    margin-bottom: 20px;
}

.share-message label {
    display: block;
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 8px;
}

.share-textarea {
    width: 100%;
    padding: 12px;
    border: 1px solid rgba(255, 255, 255, 0.3);
    border-radius: 8px;
    background: rgba(255, 255, 255, 0.2);
    color: #2c3e50;
    font-size: 14px;
    resize: vertical;
}

.format-selector {
    width: 100%;
    padding: 10px 12px;
    border: 1px solid rgba(255, 255, 255, 0.3);
    border-radius: 8px;
    background: rgba(255, 255, 255, 0.2);
    color: #2c3e50;
    font-size: 14px;
    margin-bottom: 10px;
    cursor: pointer;
}

.format-selector:focus {
    outline: none;
    border-color: #ff6b9d;
    box-shadow: 0 0 0 2px rgba(255, 107, 157, 0.2);
}

.share-actions {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

.share-actions .btn {
    flex: 1;
    min-width: 120px;
}

.stats-section {
    margin-bottom: 40px;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-bottom: 20px;
}

.stat-card {
    background: rgba(255, 255, 255, 0.2);
    border-radius: 15px;
    padding: 25px;
    text-align: center;
    border: 1px solid rgba(255, 255, 255, 0.3);
}

.stat-icon {
    font-size: 32px;
    color: #ff6b9d;
    margin-bottom: 15px;
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

.stats-actions {
    text-align: center;
}

.navigation-section {
    display: flex;
    justify-content: center;
    gap: 20px;
    border-top: 1px solid rgba(255, 255, 255, 0.2);
    padding-top: 30px;
}

@media (max-width: 768px) {
    .qr-container {
        flex-direction: column;
        text-align: center;
    }
    
    .preview-content {
        flex-direction: column;
        gap: 20px;
        text-align: center;
    }
    
    .sharing-grid {
        grid-template-columns: 1fr;
    }
    
    .navigation-section {
        flex-direction: column;
    }
    
    .share-actions {
        flex-direction: column;
    }
}
</style>

<script>
// Message format templates
const messageTemplates = {
    whatsapp: {
        original: `Undangan Majlis Perkahwinan {{ $card->card_details['bride_name'] ?? 'Pengantin Perempuan' }} & {{ $card->card_details['groom_name'] ?? 'Pengantin Lelaki' }} 💌

Bismillahirrahmanirrahim
Assalamualaikum w.b.t & Salam Sejahtera.

Dengan penuh kesyukuran, kami sekeluarga menjemput Yang Berbahagia Tan Sri/ Puan Sri/ Dato' Seri/ Datin Seri/ Dato'/ Datin/ Tuan/ Puan/ Encik/ Cik untuk memeriahkan majlis perkahwinan kami.

💍 Pasangan: {{ $card->card_details['bride_name'] ?? 'Pengantin Perempuan' }} & {{ $card->card_details['groom_name'] ?? 'Pengantin Lelaki' }}
🗓 Tarikh: {{ $card->card_details['wedding_date'] ?? 'Akan dimaklumkan' }}
📍 Lokasi: {{ $card->card_details['venue_name'] ?? 'Akan dimaklumkan' }}

Maklumat lengkap (lokasi, aturcara & RSVP) boleh dilihat di kad jemputan di pautan berikut:
{{ $card->view_url }}

Kehadiran dan doa anda amat kami alu-alukan. Terima kasih banyak-banyak. 🙏🎉`,
        formal: `Bismillahirrahmanirrahim,

WALIMATUL URUS {{ strtoupper($card->card_details['bride_name'] ?? 'PUTERI') }} & {{ strtoupper($card->card_details['groom_name'] ?? 'HAFIY') }} 💍

Pelamin mempelai dihias indah,
Tempat bertakhta dua sejoli,
Cinta dan sayang disulam sudah,
Tanda ikatan kekal abadi.

Assalamualaikum w.b.t.

Dengan izin Allah dan dengan penuh rasa kesyukuran, kami

{{ strtoupper($card->card_details['bride_father_name'] ?? 'ABDUL RASHID BIN MOHD ALI') }}
&
{{ strtoupper($card->card_details['bride_mother_name'] ?? 'RAJA ROSZINAH BINTI RAJA ADNAN') }}

Dengan rasa rendah diri ingin menjemput dan mempersilakan untuk sama-sama hadir bagi meraikan majlis perkahwinan puteri kami

👰🏻‍♀{{ $card->card_details['bride_name'] ?? 'PUTERI AKASHAH BINTI ABDUL RASHID' }}
&
🤵🏻{{ $card->card_details['groom_name'] ?? 'MOHAMAD HAFIY AIMAN BIN MOHAMAD HAFZAL' }}

Pada ketetapan berikut :
- {{ $card->card_details['wedding_date'] ?? '7 Disember 2025 | Ahad' }}
- ⁠Bersamaan {{ $card->card_details['hijri_date'] ?? '17 Jamadilakhir 1147H' }}
- ⁠Bertempat: {{ $card->card_details['venue_name'] ?? 'The Glass Tree, Zuljannah Palace, Klang' }}

Kami mengharapkan doa kalian agar majlis pernikahan ini dapat berjalan dengan lancar dan agar ikatan kasih yang dibina bahagia berkekalan sehingga ke syurga. InshaAllah.

Maklumat lengkap: {{ $card->view_url }}`
    },
    telegram: {
        original: `Jemputan Majlis Perkahwinan ✨

Bismillahirrahmanirrahim. Assalamualaikum & Salam Sejahtera.
Kami dengan penuh kesyukuran menjemput anda ke majlis perkahwinan:
{{ $card->card_details['bride_name'] ?? 'Pengantin Perempuan' }} & {{ $card->card_details['groom_name'] ?? 'Pengantin Lelaki' }}

💍 Pasangan: {{ $card->card_details['bride_name'] ?? 'Pengantin Perempuan' }} & {{ $card->card_details['groom_name'] ?? 'Pengantin Lelaki' }}
🗓 Tarikh: {{ $card->card_details['wedding_date'] ?? 'Akan dimaklumkan' }}
📍 Lokasi: {{ $card->card_details['venue_name'] ?? 'Akan dimaklumkan' }}

Butiran penuh & RSVP:
{{ $card->view_url }}

Mohon doa yang baik-baik. Kehadiran anda memeriahkan majlis kami. 🙏🎊`,
        formal: `Bismillahirrahmanirrahim,

WALIMATUL URUS {{ strtoupper($card->card_details['bride_name'] ?? 'PUTERI') }} & {{ strtoupper($card->card_details['groom_name'] ?? 'HAFIY') }} 💍

Pelamin mempelai dihias indah,
Tempat bertakhta dua sejoli,
Cinta dan sayang disulam sudah,
Tanda ikatan kekal abadi.

Assalamualaikum w.b.t.

Dengan izin Allah dan dengan penuh rasa kesyukuran, kami

{{ strtoupper($card->card_details['bride_father_name'] ?? 'ABDUL RASHID BIN MOHD ALI') }}
&
{{ strtoupper($card->card_details['bride_mother_name'] ?? 'RAJA ROSZINAH BINTI RAJA ADNAN') }}

Dengan rasa rendah diri ingin menjemput dan mempersilakan untuk sama-sama hadir bagi meraikan majlis perkahwinan puteri kami

👰🏻‍♀{{ $card->card_details['bride_name'] ?? 'PUTERI AKASHAH BINTI ABDUL RASHID' }}
&
🤵🏻{{ $card->card_details['groom_name'] ?? 'MOHAMAD HAFIY AIMAN BIN MOHAMAD HAFZAL' }}

Pada ketetapan berikut :
- {{ $card->card_details['wedding_date'] ?? '7 Disember 2025 | Ahad' }}
- ⁠Bersamaan {{ $card->card_details['hijri_date'] ?? '17 Jamadilakhir 1147H' }}
- ⁠Bertempat: {{ $card->card_details['venue_name'] ?? 'The Glass Tree, Zuljannah Palace, Klang' }}

Kami mengharapkan doa kalian agar majlis pernikahan ini dapat berjalan dengan lancar dan agar ikatan kasih yang dibina bahagia berkekalan sehingga ke syurga. InshaAllah.

Maklumat lengkap: {{ $card->view_url }}`
    }
};

// Track sharing events
function trackShare(platform) {
    // Send analytics tracking request
    fetch('/analytics/track', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            wedding_card_id: {{ $card->id }},
            event_type: 'share',
            metadata: {
                platform: platform
            }
        })
    }).catch(error => {
        console.log('Analytics tracking failed:', error);
    });
}

// Update WhatsApp message based on selected format
function updateWhatsAppMessage() {
    const formatSelector = document.getElementById('whatsappFormatSelector');
    const messageTextarea = document.getElementById('whatsappMessage');
    const selectedFormat = formatSelector.value;
    
    messageTextarea.value = messageTemplates.whatsapp[selectedFormat];
    updateSharingLinks();
}

// Update Telegram message based on selected format
function updateTelegramMessage() {
    const formatSelector = document.getElementById('telegramFormatSelector');
    const messageTextarea = document.getElementById('telegramMessage');
    const selectedFormat = formatSelector.value;
    
    messageTextarea.value = messageTemplates.telegram[selectedFormat];
    updateSharingLinks();
}

// Update sharing links when messages change
function updateSharingLinks() {
    const whatsappMessage = document.getElementById('whatsappMessage').value;
    const telegramMessage = document.getElementById('telegramMessage').value;
    const cardUrl = '{{ $card->view_url }}';
    
    // WhatsApp link (api endpoint is more robust for some devices)
    const whatsappLink = `https://api.whatsapp.com/send?text=${encodeURIComponent(whatsappMessage)}`;
    document.getElementById('whatsappLink').href = whatsappLink;
    
    // Telegram link
    const telegramLink = `https://t.me/share/url?url=${encodeURIComponent(cardUrl)}&text=${encodeURIComponent(telegramMessage)}`;
    document.getElementById('telegramLink').href = telegramLink;
    
    // Email link
    const emailSubject = `Wedding Invitation - {{ $card->card_details['bride_name'] ?? 'Bride' }} & {{ $card->card_details['groom_name'] ?? 'Groom' }}`;
    const emailBody = telegramMessage;
    const emailLink = `mailto:?subject=${encodeURIComponent(emailSubject)}&body=${encodeURIComponent(emailBody)}`;
    document.getElementById('emailLink').href = emailLink;
    
    // Facebook link
    const facebookLink = `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(cardUrl)}`;
    document.getElementById('facebookLink').href = facebookLink;
}

// Initialize sharing links
updateSharingLinks();

// Update links when textarea content changes
document.getElementById('whatsappMessage').addEventListener('input', updateSharingLinks);
document.getElementById('telegramMessage').addEventListener('input', updateSharingLinks);

// Update links when format selector changes
document.getElementById('whatsappFormatSelector').addEventListener('change', updateWhatsAppMessage);
document.getElementById('telegramFormatSelector').addEventListener('change', updateTelegramMessage);

function copyUrl() {
    const urlInput = document.getElementById('cardUrl');
    urlInput.select();
    document.execCommand('copy');
    
    trackShare('url_copy');
    
    // Show feedback
    const btn = event.target;
    const originalText = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-check"></i> Copied!';
    setTimeout(() => {
        btn.innerHTML = originalText;
    }, 2000);
}

function copyWhatsAppMessage() {
    const message = document.getElementById('whatsappMessage').value;
    navigator.clipboard.writeText(message).then(() => {
        trackShare('whatsapp_copy');
        const btn = event.target;
        const originalText = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-check"></i> Copied!';
        setTimeout(() => {
            btn.innerHTML = originalText;
        }, 2000);
    });
}

function copyTelegramMessage() {
    const message = document.getElementById('telegramMessage').value;
    navigator.clipboard.writeText(message).then(() => {
        trackShare('telegram_copy');
        const btn = event.target;
        const originalText = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-check"></i> Copied!';
        setTimeout(() => {
            btn.innerHTML = originalText;
        }, 2000);
    });
}

function copyEmailContent() {
    const message = document.getElementById('telegramMessage').value;
    navigator.clipboard.writeText(message).then(() => {
        trackShare('email_copy');
        const btn = event.target;
        const originalText = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-check"></i> Copied!';
        setTimeout(() => {
            btn.innerHTML = originalText;
        }, 2000);
    });
}

function generateQR() {
    // In a real application, you would generate and download a QR code
    alert('QR Code download functionality would be implemented here');
}

function printQR() {
    // In a real application, you would open a print dialog for the QR code
    alert('QR Code print functionality would be implemented here');
}
</script>
@endsection 