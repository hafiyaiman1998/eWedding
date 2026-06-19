{{-- Malaysian Wedding Invitation Template - Hafiy & Puteri Style --}}
<style>
/* Google Fonts Import */
@import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Montserrat:wght@300;400;500&family=Great+Vibes&display=swap');

/* Reset and Base Styles */
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

:root {
  --primary: #c8a165;
  --primary-dark: #a17d42;
  --secondary: #f8f3e9;
  --text-dark: #333;
  --text-light: #fff;
  --transition: all 0.4s ease;
}

.wedding-card-container {
  font-family: 'Montserrat', sans-serif;
  color: var(--text-dark);
  line-height: 1.6;
  background-color: var(--secondary);
  overflow-x: hidden;
  position: relative;
}

/* Typography */
h1, h2, h3, h4 {
  font-family: 'Playfair Display', serif;
  font-weight: 600;
  margin-bottom: 1rem;
}

.script-font {
  font-family: 'Great Vibes', cursive;
}

.section-title {
  font-size: 2.5rem;
  text-align: center;
  margin-bottom: 3rem;
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

/* Layout */
.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 1.5rem;
}

section {
  padding: 80px 0;
  position: relative;
  overflow: hidden;
}

/* Invitation Overlay */
.invitation-overlay {
  background-color: #f5f2e9;
  padding: 40px 20px;
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 60vh;
}

.envelope {
  position: relative;
  width: 90%;
  max-width: 500px;
  background-color: #f8f3e9;
  border-radius: 5px;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
  border: 1px solid #d4c9a8;
  display: flex;
  justify-content: center;
  align-items: center;
  padding: 40px 20px;
}

.invitation-content {
  width: 100%;
  background: #fff;
  border: 3px double var(--primary);
  border-radius: 5px;
  box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
  padding: 2rem;
  text-align: center;
}

.invitation-title {
  font-family: 'Great Vibes', cursive;
  font-size: 3rem;
  color: var(--primary);
  margin-bottom: 0;
  line-height: 1.2;
}

.invitation-couple-names {
  font-family: 'Great Vibes', cursive;
  font-size: 2.5rem;
  color: #333;
  margin: 1rem 0 0.5rem;
  line-height: 1.2;
}

.invitation-separator {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 100%;
  margin: 1rem 0;
}

.invitation-separator-line {
  height: 1px;
  width: 80px;
  background-color: var(--primary);
}

.wedding-date-small {
  font-size: 0.9rem;
  color: #aaa;
  margin-top: 0.5rem;
  font-style: italic;
  letter-spacing: 1px;
}

/* Hero Section */
.hero {
  min-height: 80vh;
  background: linear-gradient(rgba(0, 0, 0, 0.3), rgba(0, 0, 0, 0.5)), 
              url('https://images.unsplash.com/photo-1519225421980-715cb0215aed?ixlib=rb-4.0.3') center/cover;
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
}

.couple-names {
  font-family: 'Great Vibes', cursive;
  font-size: 4rem;
  margin-bottom: 1rem;
  color: var(--text-light);
}

.wedding-date {
  font-size: 1.5rem;
  letter-spacing: 3px;
  margin-bottom: 2rem;
}

.separator {
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 2rem 0;
}

.separator-line {
  height: 1px;
  width: 100px;
  background-color: var(--primary);
  margin: 0 15px;
}

/* Couple Section */
.couple {
  background-color: #fff;
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
  position: relative;
  padding: 1rem;
  border: 1px solid var(--primary);
  border-radius: 50%;
  overflow: hidden;
  margin-bottom: 1.5rem;
  width: 300px;
  height: 300px;
  margin: 0 auto 1.5rem;
}

.photo-frame img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  border-radius: 50%;
}

.couple-name {
  font-family: 'Playfair Display', serif;
  font-size: 1.8rem;
  margin-bottom: 0.5rem;
}

.couple-desc {
  color: #777;
}

/* Events Section */
.events {
  background-color: var(--secondary);
}

.timeline {
  position: relative;
  max-width: 800px;
  margin: 0 auto;
}

.timeline-item {
  background-color: #fff;
  padding: 1.5rem;
  margin-bottom: 2rem;
  border-radius: 6px;
  box-shadow: 0 5px 25px rgba(0, 0, 0, 0.1);
  border-left: 4px solid var(--primary);
}

.event-time {
  font-weight: 500;
  color: var(--primary);
  margin-bottom: 0.5rem;
}

.event-title {
  margin-bottom: 0.5rem;
  color: var(--text-dark);
}

.event-location {
  display: flex;
  align-items: center;
  margin-top: 1rem;
  font-size: 0.9rem;
  color: #666;
}

/* RSVP Section */
.rsvp {
  background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)), 
              url('https://images.unsplash.com/photo-1478146896981-b80fe463b330?ixlib=rb-4.0.3') center/cover;
  color: var(--text-light);
}

.rsvp .section-title {
  color: var(--text-light);
}

.rsvp-form {
  max-width: 600px;
  margin: 0 auto;
}

.form-group {
  margin-bottom: 1.5rem;
}

.form-control {
  width: 100%;
  padding: 1rem;
  border: none;
  border-radius: 4px;
  background-color: rgba(255, 255, 255, 0.9);
}

.btn {
  display: inline-block;
  padding: 1rem 2rem;
  border: none;
  border-radius: 4px;
  font-weight: 500;
  text-transform: uppercase;
  letter-spacing: 1px;
  cursor: pointer;
  transition: var(--transition);
}

.btn-primary {
  background-color: var(--primary);
  color: var(--text-light);
}

/* Gallery Section */
.gallery {
  background-color: #fff;
}

.gallery-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
  grid-gap: 1.5rem;
}

.gallery-item {
  position: relative;
  overflow: hidden;
  border-radius: 4px;
  height: 250px;
  background-color: #f0f0f0;
  display: flex;
  align-items: center;
  justify-content: center;
}

.gallery-placeholder {
  color: #999;
  font-size: 3rem;
}

/* Doa Section */
.doa-selamat {
  text-align: center;
  padding: 2rem;
  max-width: 800px;
  margin: 0 auto;
  border: 1px solid var(--primary);
  border-radius: 8px;
  background-color: rgba(255, 255, 255, 0.9);
}

.arabic-text {
  font-size: 1.8rem;
  line-height: 2.5;
  direction: rtl;
  margin-bottom: 1rem;
  color: var(--primary);
}

.translation {
  font-style: italic;
  color: #666;
}

/* Footer */
.footer {
  background-color: #222;
  color: var(--text-light);
  padding: 3rem 0;
  text-align: center;
}

.footer-title {
  font-family: 'Great Vibes', cursive;
  font-size: 2rem;
  margin-bottom: 1rem;
}

.social-links {
  display: flex;
  justify-content: center;
  list-style: none;
  margin: 1.5rem 0;
}

.social-links li {
  margin: 0 1rem;
}

.copyright {
  font-size: 0.9rem;
  opacity: 0.7;
}

/* Responsive Design */
@media (max-width: 768px) {
  .section-title {
    font-size: 2rem;
  }
  
  .couple-names {
    font-size: 3rem;
  }
  
  .invitation-title {
    font-size: 2.5rem;
  }
  
  .couple-photo {
    width: 90%;
    margin: 0 auto 2rem;
  }
  
  .photo-frame {
    width: 220px;
    height: 220px;
  }
}

@media (max-width: 576px) {
  .couple-names {
    font-size: 2.5rem;
  }
  
  .wedding-date {
    font-size: 1.2rem;
  }
}
</style>

<div class="wedding-card-container">
    <!-- Invitation Overlay -->
    <div class="invitation-overlay">
        <div class="envelope">
            <div class="invitation-content">
                <h2 class="invitation-title">You're Invited</h2>
                <div class="invitation-separator">
                    <div class="invitation-separator-line"></div>
                    <span style="color: var(--primary); font-size: 1.5rem;">♥</span>
                    <div class="invitation-separator-line"></div>
                </div>
                <p>To celebrate the wedding of</p>
                <h1 class="invitation-couple-names">{{ $details["groom_name"] ?? "Ahmad" }} & {{ $details["bride_name"] ?? "Fatimah" }}</h1>
                <p class="wedding-date-small">{{ $details["wedding_date"] ?? "15 Ogos 2024" }}</p>
            </div>
        </div>
    </div>

    <!-- Hero Section -->
    <section id="hero" class="hero">
        <div class="hero-content">
            <h2 class="script-font">We're Getting Married</h2>
            <h1 class="couple-names">{{ $details["groom_name"] ?? "Ahmad" }} & {{ $details["bride_name"] ?? "Fatimah" }}</h1>
            <p class="wedding-date">{{ strtoupper($details["wedding_date"] ?? "15 OGOS 2024") }}</p>
            <div class="separator">
                <div class="separator-line"></div>
                <span style="color: var(--text-light); font-size: 1.5rem;">♥</span>
                <div class="separator-line"></div>
            </div>
            <p>Kindly Join Us On Our Special Day</p>
        </div>
    </section>

    <!-- Couple Section -->
    <section id="couple" class="couple">
        <div class="container">
            <h2 class="section-title">The Couple</h2>
            <div class="couple-photos">
                <div class="couple-photo">
                    <div class="photo-frame">
                        @if($details["groom_photo"] ?? false)
                            <img src="{{ $details['groom_photo'] }}" alt="Groom">
                        @else
                            <div style="background: #f0f0f0; width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; color: #999;">
                                <i class="fas fa-user" style="font-size: 4rem;"></i>
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
                <div class="couple-photo">
                    <div class="photo-frame">
                        @if($details["bride_photo"] ?? false)
                            <img src="{{ $details['bride_photo'] }}" alt="Bride">
                        @else
                            <div style="background: #f0f0f0; width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; color: #999;">
                                <i class="fas fa-user" style="font-size: 4rem;"></i>
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
            <div class="doa-selamat">
                <div class="arabic-text">
                    بارك الله لكما وبارك عليكما وجمع بينكما في خير
                </div>
                <p class="translation">"May Allah bless you both, shower His blessings upon you, and join you in goodness."</p>
            </div>
        </div>
    </section>

    <!-- Events Section -->
    <section id="events" class="events">
        <div class="container">
            <h2 class="section-title">Wedding Events</h2>
            <div class="timeline">
                @if($details["akad_date"] ?? false)
                <div class="timeline-item">
                    <p class="event-time">{{ $details["akad_date"] ?? "Friday, 14 Ogos 2024" }} • {{ $details["akad_time"] ?? "10:00 AM" }}</p>
                    <h3 class="event-title">{{ $details["akad_title"] ?? "Akad Nikah (Solemnization)" }}</h3>
                    <p>{{ $details["akad_description"] ?? "The sacred ceremony where the couple will officially be joined in marriage according to Islamic tradition." }}</p>
                    <div class="event-location">
                        <span style="margin-right: 0.5rem;">📍</span>
                        <span>{{ $details["akad_venue"] ?? "Masjid Al-Hidayah" }}</span>
                    </div>
                </div>
                @endif
                
                <div class="timeline-item">
                    <p class="event-time">{{ $details["reception_date"] ?? $details["wedding_date"] ?? "Saturday, 15 Ogos 2024" }} • {{ $details["reception_time"] ?? "12:00 PM - 5:00 PM" }}</p>
                    <h3 class="event-title">{{ $details["reception_title"] ?? "Majlis Bersanding & Reception" }}</h3>
                    <p>{{ $details["reception_description"] ?? "The grand celebration featuring the traditional Malaysian wedding throne ceremony, followed by a feast and entertainment." }}</p>
                    <div class="event-location">
                        <span style="margin-right: 0.5rem;">📍</span>
                        <span>{{ $details["venue"] ?? "Dewan Serbaguna Komuniti" }}</span>
                    </div>
                </div>

                @if($details["groom_reception_date"] ?? false)
                <div class="timeline-item">
                    <p class="event-time">{{ $details["groom_reception_date"] ?? "Sunday, 16 Ogos 2024" }} • {{ $details["groom_reception_time"] ?? "11:00 AM - 3:00 PM" }}</p>
                    <h3 class="event-title">{{ $details["groom_reception_title"] ?? "Majlis Bertandang (Groom's Reception)" }}</h3>
                    <p>{{ $details["groom_reception_description"] ?? "A second reception hosted by the groom's family, featuring traditional Malaysian cuisine and customs." }}</p>
                    <div class="event-location">
                        <span style="margin-right: 0.5rem;">📍</span>
                        <span>{{ $details["groom_reception_venue"] ?? "Dewan Komuniti" }}</span>
                    </div>
                </div>
                @endif
            </div>
            
            <!-- Venue Information -->
            @if($details["address"] ?? false)
            <div style="margin-top: 3rem; background-color: white; padding: 1.5rem; border-radius: 8px; box-shadow: 0 5px 25px rgba(0, 0, 0, 0.1);">
                <h3 style="color: var(--primary); margin-bottom: 0.5rem;">{{ $details["venue"] ?? "Main Venue" }}</h3>
                <p>{{ $details["address"] }}</p>
                @if($details["venue_map_link"] ?? false)
                <a href="{{ $details['venue_map_link'] }}" target="_blank" style="display: inline-block; margin-top: 1rem; padding: 0.5rem 1rem; background-color: var(--primary); color: white; text-decoration: none; border-radius: 4px;">
                    🗺️ Get Directions
                </a>
                @endif
            </div>
            @endif
        </div>
    </section>

    <!-- Gallery Section -->
    <section id="gallery" class="gallery">
        <div class="container">
            <h2 class="section-title">Our Moments</h2>
            <div class="gallery-grid">
                @for($i = 1; $i <= 6; $i++)
                    <div class="gallery-item">
                        @if($details["gallery_photo_$i"] ?? false)
                            <img src="{{ $details['gallery_photo_' . $i] }}" alt="Couple Photo {{ $i }}" style="width: 100%; height: 100%; object-fit: cover;">
                        @else
                            <div class="gallery-placeholder">
                                <i class="fas fa-image"></i>
                            </div>
                        @endif
                    </div>
                @endfor
            </div>
        </div>
    </section>

    <!-- RSVP Section -->
    <section id="rsvp" class="rsvp">
        <div class="container">
            <h2 class="section-title">Please RSVP</h2>
            <div class="rsvp-form">
                <div style="background: rgba(255,255,255,0.1); padding: 2rem; border-radius: 8px; text-align: center;">
                    <p style="font-size: 1.2rem; margin-bottom: 1rem;">Please confirm your attendance</p>
                    @if($details["contact_bride"] ?? $details["contact_groom"] ?? false)
                    <p style="margin-bottom: 1rem;">
                        Contact us:
                        @if($details["contact_bride"] ?? false)
                            <br>{{ $details["contact_bride"] }}
                        @endif
                        @if($details["contact_groom"] ?? false)
                            <br>{{ $details["contact_groom"] }}
                        @endif
                    </p>
                    @endif
                    <p style="font-style: italic;">{{ $details["custom_message"] ?? "Your presence would mean the world to us on our special day." }}</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <h2 class="footer-title">{{ $details["groom_name"] ?? "Ahmad" }} & {{ $details["bride_name"] ?? "Fatimah" }}</h2>
            <p>Thank you for celebrating our special day with us!</p>
            <p class="copyright">&copy; {{ date('Y') }} {{ $details["groom_name"] ?? "Ahmad" }} & {{ $details["bride_name"] ?? "Fatimah" }}. All Rights Reserved.</p>
        </div>
    </footer>
</div> 