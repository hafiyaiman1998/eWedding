{{-- Hafiy & Puteri Style Malaysian Wedding Template --}}
<style>
@import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Montserrat:wght@300;400;500&family=Great+Vibes&display=swap');

:root {
  --primary: #c8a165;
  --primary-dark: #a17d42;
  --secondary: #f8f3e9;
  --text-dark: #333;
  --text-light: #fff;
}

.wedding-card {
  font-family: 'Montserrat', sans-serif;
  color: var(--text-dark);
  line-height: 1.6;
  background-color: var(--secondary);
  margin: 0;
  padding: 0;
}

.script-font { font-family: 'Great Vibes', cursive; }
h1, h2, h3, h4 { font-family: 'Playfair Display', serif; font-weight: 600; }

.container { max-width: 1200px; margin: 0 auto; padding: 0 1.5rem; }
section { padding: 60px 0; position: relative; }

/* Invitation Header */
.invitation-header {
  background: linear-gradient(rgba(0, 0, 0, 0.4), rgba(0, 0, 0, 0.6)), 
              url('https://images.unsplash.com/photo-1519225421980-715cb0215aed?ixlib=rb-4.0.3') center/cover;
  min-height: 70vh;
  display: flex;
  align-items: center;
  justify-content: center;
  text-align: center;
  color: var(--text-light);
  position: relative;
}

.hero-content {
  max-width: 800px;
  padding: 2rem;
  background: rgba(255,255,255,0.05);
  border-radius: 15px;
  backdrop-filter: blur(10px);
  border: 1px solid rgba(255,255,255,0.1);
}

.couple-names {
  font-family: 'Great Vibes', cursive;
  font-size: 4rem;
  margin: 1rem 0;
  text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
}

.wedding-date {
  font-size: 1.3rem;
  letter-spacing: 3px;
  margin-bottom: 1.5rem;
  font-weight: 300;
}

.separator {
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 1.5rem 0;
}

.separator-line {
  height: 1px;
  width: 80px;
  background-color: var(--primary);
  margin: 0 15px;
}

/* Couple Section */
.couple-section {
  background-color: #fff;
  padding: 80px 0;
}

.section-title {
  font-size: 2.5rem;
  text-align: center;
  margin-bottom: 3rem;
  color: var(--text-dark);
  position: relative;
  padding-bottom: 1rem;
}

.section-title::after {
  content: '';
  position: absolute;
  bottom: 0;
  left: 50%;
  transform: translateX(-50%);
  width: 80px;
  height: 3px;
  background-color: var(--primary);
}

.couple-photos {
  display: flex;
  justify-content: space-around;
  flex-wrap: wrap;
  margin-bottom: 3rem;
}

.couple-photo {
  width: 45%;
  max-width: 400px;
  text-align: center;
  margin-bottom: 2rem;
}

.photo-frame {
  width: 280px;
  height: 280px;
  border: 2px solid var(--primary);
  border-radius: 50%;
  overflow: hidden;
  margin: 0 auto 1.5rem;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #f8f8f8;
}

.photo-frame img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.photo-placeholder {
  font-size: 4rem;
  color: #ccc;
}

.couple-name {
  font-size: 1.8rem;
  margin-bottom: 0.5rem;
  color: var(--text-dark);
}

.couple-desc {
  color: #777;
  font-style: italic;
}

/* Events Section */
.events-section {
  background-color: var(--secondary);
  padding: 80px 0;
}

.timeline {
  max-width: 800px;
  margin: 0 auto;
}

.timeline-item {
  background-color: #fff;
  padding: 2rem;
  margin-bottom: 2rem;
  border-radius: 8px;
  box-shadow: 0 5px 25px rgba(0, 0, 0, 0.1);
  border-left: 5px solid var(--primary);
}

.event-time {
  font-weight: 600;
  color: var(--primary);
  margin-bottom: 0.5rem;
  font-size: 1.1rem;
}

.event-title {
  margin-bottom: 1rem;
  color: var(--text-dark);
  font-size: 1.5rem;
}

.event-location {
  display: flex;
  align-items: center;
  margin-top: 1rem;
  color: #666;
}

.location-icon {
  margin-right: 0.5rem;
  font-size: 1.1rem;
  color: var(--primary);
}

/* Doa Section */
.doa-section {
  text-align: center;
  padding: 2rem;
  max-width: 700px;
  margin: 2rem auto;
  border: 2px solid var(--primary);
  border-radius: 10px;
  background: rgba(255,255,255,0.9);
}

.arabic-text {
  font-size: 1.6rem;
  line-height: 2.2;
  direction: rtl;
  margin-bottom: 1rem;
  color: var(--primary);
  font-weight: 500;
}

.translation {
  font-style: italic;
  color: #666;
  font-size: 1rem;
}

/* Contact Section */
.contact-section {
  background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
  color: var(--text-light);
  text-align: center;
  padding: 60px 0;
}

.contact-info {
  max-width: 600px;
  margin: 0 auto;
}

.contact-item {
  margin: 1rem 0;
  font-size: 1.1rem;
}

.contact-label {
  font-weight: 600;
  margin-right: 0.5rem;
}

/* Footer */
.footer {
  background-color: #333;
  color: var(--text-light);
  text-align: center;
  padding: 2rem 0;
}

.footer-names {
  font-family: 'Great Vibes', cursive;
  font-size: 2rem;
  margin-bottom: 0.5rem;
}

/* Responsive */
@media (max-width: 768px) {
  .couple-names { font-size: 3rem; }
  .section-title { font-size: 2rem; }
  .couple-photo { width: 80%; margin: 0 auto 2rem; }
  .photo-frame { width: 220px; height: 220px; }
  .hero-content { padding: 1.5rem; }
}
</style>

<div class="wedding-card">
    <!-- Hero/Invitation Section -->
    <section class="invitation-header">
        <div class="hero-content">
            <h2 class="script-font" style="font-size: 2rem; margin-bottom: 1rem;">You're Invited to Celebrate</h2>
            <h1 class="couple-names">{{ $details["groom_name"] ?? "Ahmad" }} & {{ $details["bride_name"] ?? "Fatimah" }}</h1>
            <p class="wedding-date">{{ strtoupper($details["wedding_date"] ?? "15 OGOS 2024") }}</p>
            <div class="separator">
                <div class="separator-line"></div>
                <span style="color: var(--text-light); font-size: 1.5rem;">♥</span>
                <div class="separator-line"></div>
            </div>
            <p style="font-size: 1.1rem; font-weight: 300;">Kindly Join Us On Our Special Day</p>
        </div>
    </section>

    <!-- Couple Section -->
    <section class="couple-section">
        <div class="container">
            <h2 class="section-title">The Couple</h2>
            <div class="couple-photos">
                <!-- Groom -->
                <div class="couple-photo">
                    <div class="photo-frame">
                        @if($details["groom_photo"] ?? false)
                            <img src="{{ $details['groom_photo'] }}" alt="Groom">
                        @else
                            <div class="photo-placeholder">
                                <i class="fas fa-user"></i>
                            </div>
                        @endif
                    </div>
                    <h3 class="couple-name">{{ $details["groom_full_name"] ?? $details["groom_name"] ?? "Ahmad Rahman bin Abdullah" }}</h3>
                    <div class="separator">
                        <div class="separator-line"></div>
                        <span style="color: var(--primary); font-size: 1.2rem;">♥</span>
                        <div class="separator-line"></div>
                    </div>
                    <p class="couple-desc">{{ $details["groom_parents"] ?? "Son of Encik Abdullah bin Ali and Puan Siti binti Ahmad" }}</p>
                </div>

                <!-- Bride -->
                <div class="couple-photo">
                    <div class="photo-frame">
                        @if($details["bride_photo"] ?? false)
                            <img src="{{ $details['bride_photo'] }}" alt="Bride">
                        @else
                            <div class="photo-placeholder">
                                <i class="fas fa-user"></i>
                            </div>
                        @endif
                    </div>
                    <h3 class="couple-name">{{ $details["bride_full_name"] ?? $details["bride_name"] ?? "Fatimah binti Rahman" }}</h3>
                    <div class="separator">
                        <div class="separator-line"></div>
                        <span style="color: var(--primary); font-size: 1.2rem;">♥</span>
                        <div class="separator-line"></div>
                    </div>
                    <p class="couple-desc">{{ $details["bride_parents"] ?? "Daughter of Encik Rahman bin Hassan and Puan Aminah binti Yusof" }}</p>
                </div>
            </div>

            <!-- Islamic Doa -->
            <div class="doa-section">
                <div class="arabic-text">
                    بارك الله لكما وبارك عليكما وجمع بينكما في خير
                </div>
                <p class="translation">"May Allah bless you both, shower His blessings upon you, and join you in goodness."</p>
            </div>
        </div>
    </section>

    <!-- Events Section -->
    <section class="events-section">
        <div class="container">
            <h2 class="section-title">Wedding Events</h2>
            <div class="timeline">
                <!-- Akad Nikah (if provided) -->
                @if($details["akad_date"] ?? false)
                <div class="timeline-item">
                    <p class="event-time">{{ $details["akad_date"] }} • {{ $details["akad_time"] ?? "10:00 AM" }}</p>
                    <h3 class="event-title">{{ $details["akad_title"] ?? "Akad Nikah (Solemnization)" }}</h3>
                    <p>{{ $details["akad_description"] ?? "The sacred ceremony where the couple will officially be joined in marriage according to Islamic tradition." }}</p>
                    @if($details["akad_venue"] ?? false)
                    <div class="event-location">
                        <span class="location-icon">📍</span>
                        <span>{{ $details["akad_venue"] }}</span>
                    </div>
                    @endif
                </div>
                @endif

                <!-- Main Reception -->
                <div class="timeline-item">
                    <p class="event-time">{{ $details["reception_date"] ?? $details["wedding_date"] ?? "Saturday, 15 Ogos 2024" }} • {{ $details["reception_time"] ?? "12:00 PM - 5:00 PM" }}</p>
                    <h3 class="event-title">{{ $details["reception_title"] ?? "Majlis Bersanding & Reception" }}</h3>
                    <p>{{ $details["reception_description"] ?? "The grand celebration featuring the traditional Malaysian wedding throne ceremony, followed by a feast and entertainment." }}</p>
                    <div class="event-location">
                        <span class="location-icon">📍</span>
                        <span>{{ $details["venue"] ?? "Dewan Serbaguna Komuniti" }}</span>
                    </div>
                    @if($details["address"] ?? false)
                    <div class="event-location">
                        <span class="location-icon">🗺️</span>
                        <span>{{ $details["address"] }}</span>
                    </div>
                    @endif
                </div>

                <!-- Groom's Reception (if provided) -->
                @if($details["groom_reception_date"] ?? false)
                <div class="timeline-item">
                    <p class="event-time">{{ $details["groom_reception_date"] }} • {{ $details["groom_reception_time"] ?? "11:00 AM - 3:00 PM" }}</p>
                    <h3 class="event-title">{{ $details["groom_reception_title"] ?? "Majlis Bertandang (Groom's Reception)" }}</h3>
                    <p>{{ $details["groom_reception_description"] ?? "A second reception hosted by the groom's family, featuring traditional Malaysian cuisine and customs." }}</p>
                    @if($details["groom_reception_venue"] ?? false)
                    <div class="event-location">
                        <span class="location-icon">📍</span>
                        <span>{{ $details["groom_reception_venue"] }}</span>
                    </div>
                    @endif
                </div>
                @endif
            </div>
        </div>
    </section>

    <!-- Contact & RSVP Section -->
    <section class="contact-section">
        <div class="container">
            <div class="contact-info">
                <h2 style="font-family: 'Great Vibes', cursive; font-size: 2.5rem; margin-bottom: 1rem;">Please Join Us</h2>
                <p style="font-size: 1.2rem; margin-bottom: 2rem;">{{ $details["custom_message"] ?? "Your presence would mean the world to us on our special day." }}</p>
                
                @if($details["contact_bride"] || $details["contact_groom"])
                <div style="margin-top: 2rem;">
                    <h3 style="margin-bottom: 1rem;">Contact Information</h3>
                    @if($details["contact_bride"] ?? false)
                    <div class="contact-item">
                        <span class="contact-label">Bride's Family:</span>
                        <span>{{ $details["contact_bride"] }}</span>
                    </div>
                    @endif
                    @if($details["contact_groom"] ?? false)
                    <div class="contact-item">
                        <span class="contact-label">Groom's Family:</span>
                        <span>{{ $details["contact_groom"] }}</span>
                    </div>
                    @endif
                </div>
                @endif

                @if($details["venue_map_link"] ?? false)
                <div style="margin-top: 2rem;">
                    <a href="{{ $details['venue_map_link'] }}" target="_blank" 
                       style="display: inline-block; padding: 12px 30px; background: rgba(255,255,255,0.2); 
                              color: white; text-decoration: none; border-radius: 25px; border: 1px solid rgba(255,255,255,0.3);
                              transition: all 0.3s ease;">
                        🗺️ Get Directions to Venue
                    </a>
                </div>
                @endif
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <h2 class="footer-names">{{ $details["groom_name"] ?? "Ahmad" }} & {{ $details["bride_name"] ?? "Fatimah" }}</h2>
            <p>Thank you for celebrating our special day with us!</p>
            <p style="font-size: 0.9rem; opacity: 0.7; margin-top: 1rem;">
                &copy; {{ date('Y') }} {{ $details["groom_name"] ?? "Ahmad" }} & {{ $details["bride_name"] ?? "Fatimah" }}. Created with eWeddingCard.
            </p>
        </div>
    </footer>
</div>