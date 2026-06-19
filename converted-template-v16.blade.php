<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $details["bride_name"] ?? "Bride" }} & {{ $details["groom_name"] ?? "Groom" }} - Nature Wedding Invitation</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Manrope:wght@300;400;500;600;700&family=Marcellus&family=Allura&display=swap');

        :root {
            --forest: #0b3d2e;      /* deep pine */
            --forest-dark: #07281f; /* darker pine */
            --sage: #8fa693;        /* sage */
            --cedar: #5d3b22;       /* bark brown */
            --cream: #f7f3eb;       /* paper */
            --amber: #d9a441;       /* accent */
            --text: #233129;        /* copy */
            --light: #fff;
            --shadow: rgba(0,0,0,0.18);
            --long: 0.6s ease;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        html { scroll-behavior: smooth; scroll-padding-top: 80px; }

        body {
            font-family: 'Manrope', system-ui, -apple-system, Segoe UI, Roboto, 'Helvetica Neue', Arial, 'Noto Sans', 'Apple Color Emoji', 'Segoe UI Emoji', 'Segoe UI Symbol';
            color: var(--text);
            background: radial-gradient(ellipse at top, #edf4ef 0%, #e8efe9 35%, #e3ece6 100%);
            overflow-x: hidden;
        }

        h1, h2, h3, h4 { font-family: 'Marcellus', serif; letter-spacing: .5px; }
        .script { font-family: 'Allura', cursive; }

        /* Invitation lock state */
        body.invitation-active { overflow: hidden; height: 100%; }
        body.content-visible .main-content { visibility: visible; opacity: 1; transition: opacity .8s ease; }
        .main-content { visibility: hidden; opacity: 0; min-height: 100vh; }

        /* Forest Curtain Overlay */
        .forest-curtain {
            position: fixed; inset: 0; z-index: 2000; cursor: pointer; overflow: hidden;
            background: radial-gradient(ellipse at center, #e8f3ea 0%, #dcefe2 100%);
        }
        .forest-curtain .layer {
            position: absolute; top: 0; width: 50%; height: 100%; transition: transform 1.2s cubic-bezier(.25,.8,.25,1);
            background-size: cover; background-position: center; box-shadow: 0 10px 40px var(--shadow);
        }
        .forest-curtain .left {
            left: 0; transform-origin: left; transform: translateX(0);
            background-image: url('https://images.unsplash.com/photo-1469474968028-56623f02e42e?q=80&w=1600&auto=format&fit=crop');
        }
        .forest-curtain .right {
            right: 0; transform-origin: right; transform: translateX(0);
            background-image: url('https://images.unsplash.com/photo-1500530855697-b586d89ba3ee?q=80&w=1600&auto=format&fit=crop');
        }
        .forest-curtain::after { /* subtle mist */
            content: ''; position: absolute; inset: 0; background: radial-gradient(ellipse at center, rgba(255,255,255,.25), rgba(255,255,255,0)); pointer-events: none;
        }
        .forest-badge {
            position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);
            width: 110px; height: 110px; border-radius: 50%; z-index: 5;
            background: radial-gradient(circle at 30% 30%, #9bd3a7, var(--forest), var(--forest-dark));
            display: flex; align-items: center; justify-content: center; border: 4px solid rgba(255,255,255,.85);
            box-shadow: 0 15px 45px rgba(0,0,0,.35);
        }
        .forest-badge i { color: #fff; font-size: 36px; filter: drop-shadow(0 2px 2px rgba(0,0,0,.25)); }
        .forest-instruction { position: absolute; bottom: 30px; left: 50%; transform: translateX(-50%); color: #2f4f2f; background: rgba(255,255,255,.8); padding: 10px 16px; border-radius: 999px; font-weight: 600; box-shadow: 0 8px 20px var(--shadow); }
        .forest-curtain.opened .left { transform: translateX(-100%); }
        .forest-curtain.opened .right { transform: translateX(100%); }
        .forest-curtain.fade-out { transition: opacity 1s ease; opacity: 0; pointer-events: none; }

        /* Header */
        header { position: fixed; top: 0; left: 0; width: 100%; z-index: 1000; padding: 1rem 0; transition: var(--long); background: transparent; }
        header.scrolled { background: rgba(255,255,255,.95); box-shadow: 0 6px 18px var(--shadow); padding: .6rem 0; }
        .nav-container { max-width: 1200px; margin: 0 auto; padding: 0 1.5rem; display: flex; justify-content: space-between; align-items: center; }
        .logo { font-family: 'Great Vibes', cursive; font-size: 2rem; color: var(--forest); text-decoration: none; }
        .nav-links { list-style: none; display: flex; }
        .nav-links li { margin-left: 2rem; }
        .nav-links a { text-decoration: none; color: var(--text); text-transform: uppercase; font-size: .9rem; letter-spacing: 1px; }
        .nav-links a:hover { color: var(--forest); }
        .hamburger { display: none; cursor: pointer; }

        /* Hero */
        .hero { height: 100vh; color: var(--light); position: relative; display: flex; align-items: center; justify-content: center; text-align: center; }
        .hero::before {
            content: '';
            position: absolute; inset: 0;
            background: linear-gradient(rgba(0,0,0,.45), rgba(0,0,0,.65)), url('{{ $details["hero_image"] ?? "https://images.unsplash.com/photo-1482192596544-9eb780fc7f66?q=80&w=1920&auto=format&fit=crop" }}') center/cover no-repeat;
            z-index: -1;
        }
        .hero h1 { font-size: 4.4rem; margin-bottom: .4rem; text-shadow: 0 6px 24px rgba(0,0,0,.45); }
        .hero h2 { font-weight: 500; letter-spacing: 4px; margin-bottom: 1.2rem; text-transform: uppercase; }
        .hero .tagline { font-family: 'Marcellus', serif; font-size: 1rem; letter-spacing: 4px; text-transform: uppercase; opacity: .9; }
        .scroll-down { position: absolute; bottom: 30px; left: 50%; transform: translateX(-50%); animation: bounce 2s infinite; }
        @keyframes bounce { 0%,20%,50%,80%,100%{transform:translate(-50%,0)}40%{transform:translate(-50%,-24px)}60%{transform:translate(-50%,-12px)} }

        /* Sections */
        section { padding: 100px 0; position: relative; overflow: hidden; }
        .container { max-width: 1200px; margin: 0 auto; padding: 0 1.5rem; }
        .section-title { font-size: 2.4rem; text-align: center; margin-bottom: 2.2rem; color: var(--cedar); position: relative; }
        .section-title::after { content: ''; position: absolute; bottom: -12px; left: 50%; transform: translateX(-50%); width: 110px; height: 4px; background: linear-gradient(90deg, transparent, var(--amber), transparent); }

        /* Nature accents */
        .vine { position: absolute; width: 160px; opacity: .25; filter: drop-shadow(0 2px 4px rgba(0,0,0,.2)); pointer-events: none; }
        .vine.left { top: -10px; left: -10px; transform: rotate(-8deg); }
        .vine.right { top: -10px; right: -10px; transform: rotate(8deg) scaleX(-1); }

        /* Countdown */
        .countdown { background: linear-gradient(135deg, rgba(13,61,46,.07), rgba(247,243,235,.95)); border-top: 1px solid rgba(11,61,46,.25); border-bottom: 1px solid rgba(11,61,46,.25); }
        .countdown-timer { display: flex; justify-content: center; gap: 1.2rem; flex-wrap: wrap; }
        .cd-item { min-width: 110px; background: var(--cream); border: 3px solid var(--amber); border-radius: 50%; padding: 1.4rem; text-align: center; width: 130px; height: 130px; display: grid; place-content: center; box-shadow: 0 10px 26px var(--shadow); }
        .cd-num { font-family: 'Marcellus', serif; font-size: 2.2rem; color: var(--forest); font-weight: 700; line-height: 1; }
        .cd-label { text-transform: uppercase; letter-spacing: 2px; font-size: .75rem; color: #6e7c6e; }

        /* Couple */
        .couple { background: linear-gradient(180deg, rgba(255,255,255,.65), rgba(255,255,255,.95)); border-top: 1px solid rgba(11,61,46,.12); border-bottom: 1px solid rgba(11,61,46,.12); }
        .couple-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 2.5rem; align-items: start; }
        .person { text-align: center; }
        .portrait { width: 280px; height: 280px; margin: 0 auto 1rem; overflow: hidden; background: #fff; position: relative; box-shadow: 0 18px 36px var(--shadow); border: 6px solid var(--cream); }
        .portrait::after { content:''; position:absolute; inset: -8px; border: 3px solid var(--amber); clip-path: polygon(25% 6%, 75% 6%, 94% 50%, 75% 94%, 25% 94%, 6% 50%); border-radius: 12px; pointer-events:none; }
        .portrait img { width: 100%; height: 100%; object-fit: cover; clip-path: polygon(25% 6%, 75% 6%, 94% 50%, 75% 94%, 25% 94%, 6% 50%); }
        .person h3 { font-size: 1.8rem; margin-bottom: .3rem; }
        .person p { color: #6b726b; }

        /* Events (nature card) */
        .events { background: linear-gradient(180deg, #f9f7f1, #eff4f1); }
        .nature-card { max-width: 980px; margin: 0 auto; border-radius: 20px; overflow: hidden; border: 2px dashed var(--amber); box-shadow: 0 22px 46px var(--shadow); background: var(--cream); }
        .nature-card .header { padding: 2rem; text-align: center; background: linear-gradient(135deg, rgba(217,164,65,.15), rgba(11,61,46,.08)); }
        .nature-card .body { padding: 2rem; }
        .program { max-width: 640px; margin: 0 auto; }
        .program-item { display: grid; grid-template-columns: 24px 1fr auto; align-items: center; gap: 12px; padding: .9rem 1.1rem; background: #fff; border-radius: 12px; margin-bottom: .8rem; box-shadow: 0 8px 18px var(--shadow); }
        .program-item::before { content:''; width: 12px; height: 12px; background: var(--amber); border-radius: 50%; box-shadow: 0 0 0 4px rgba(217,164,65,.25); }
        .location { background: linear-gradient(180deg, #ffffff, #f9f6ee); border: 1px solid rgba(11,61,46,.2); border-radius: 16px; padding: 1.6rem; margin: 2rem 0; display: grid; grid-template-columns: 1fr auto; gap: 1.5rem; align-items: center; }
        .qr { width: 120px; height: 120px; background: #fff; border: 3px solid var(--forest); border-radius: 12px; display: grid; place-items: center; box-shadow: 0 10px 24px var(--shadow); }
        .contact-list { display: grid; gap: 1rem; max-width: 640px; margin: 0 auto; }
        .contact-item { display: grid; grid-template-columns: 1fr auto; gap: 12px; align-items: center; padding: 1rem 1.25rem; background: #fff; border-left: 6px solid var(--cedar); border-radius: 12px; box-shadow: 0 8px 18px var(--shadow); }

        /* Gallery */
        .gallery { border-top: 1px solid rgba(11,61,46,.12); border-bottom: 1px solid rgba(11,61,46,.12); }
        .gallery-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(240px,1fr)); gap: 1.25rem; }
        .g-item { position: relative; height: 240px; border-radius: 20px; overflow: hidden; box-shadow: 0 14px 30px var(--shadow); cursor: pointer; border: 2px solid var(--cream); }
        .g-item img { width: 100%; height: 100%; object-fit: cover; transition: transform .5s ease; }
        .g-item:hover img { transform: scale(1.07); }

        /* Before/After slider */
        .comparison { max-width: 900px; margin: 0 auto 2rem; }
        .comparison-container { position: relative; width: 100%; aspect-ratio: 3 / 2; border-radius: 16px; overflow: hidden; box-shadow: 0 16px 36px var(--shadow); background: #eef3ef; }
        .comparison-before,
        .comparison-after { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; display: block; }
        .comparison-after { clip-path: inset(0 50% 0 0); transition: clip-path .3s ease; }
        .comparison-slider-handle { position: absolute; top: 0; left: 50%; width: 4px; height: 100%; background: #fff; cursor: col-resize; z-index: 10; }
        .comparison-slider-handle::before { content: ''; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 42px; height: 42px; background: var(--forest); border: 3px solid #fff; border-radius: 50%; box-shadow: 0 6px 18px rgba(0,0,0,.25); }

        /* RSVP */
        .rsvp { color: #fff; background: linear-gradient(rgba(7,40,31,.85), rgba(11,61,46,.85)), url('{{ $details["rsvp_bg"] ?? "https://images.unsplash.com/photo-1482192596544-9eb780fc7f66?q=80&w=1920&auto=format&fit=crop" }}') center/cover fixed; }
        .rsvp .section-title { color: #fff; }
        .rsvp-form { max-width: 600px; margin: 0 auto; }
        .form-group { margin-bottom: 1.2rem; }
        .form-control { width: 100%; padding: 1rem; border: 2px solid rgba(255,255,255,.3); border-radius: 12px; background: rgba(255,255,255,.9); }
        .btn { display: inline-block; padding: 1rem 2rem; text-transform: uppercase; letter-spacing: 1px; border: 2px solid var(--amber); background: transparent; color: #fff; border-radius: 999px; cursor: pointer; transition: var(--long); }
        .btn-primary { border-color: var(--amber); }
        .btn-primary:hover { background: var(--amber); color: #1a1a1a; }
        .alert { border-radius: 10px; border: none; padding: 1rem 1.25rem; }
        .alert-success { background: #d4edda; color: #155724; border-left: 4px solid #28a745; }
        .alert-danger { background: #f8d7da; color: #721c24; border-left: 4px solid #dc3545; }

        /* Footer */
        footer { background: #12241d; color: #fff; text-align: center; padding: 3rem 0; }

        /* Floating menu (kept) */
        .floating-menu { position: fixed; right: 20px; top: 50%; transform: translateY(-50%); background: rgba(255,255,255,.9); backdrop-filter: blur(6px); border: 1px solid rgba(46,125,50,.25); border-radius: 28px; padding: 14px 10px; box-shadow: 0 10px 24px var(--shadow); z-index: 300; display: flex; flex-direction: column; align-items: center; gap: 10px; opacity: 0; visibility: hidden; transition: .3s ease; }
        .floating-menu.visible { opacity: 1; visibility: visible; }
        .menu-item { width: 40px; height: 40px; border-radius: 50%; display: grid; place-items: center; color: var(--forest); background: #fff; border: 1px solid rgba(46,125,50,.2); cursor: pointer; }
        .menu-item:hover { transform: scale(1.08); background: var(--forest); color: #fff; }

        /* Lightbox */
        .lightbox-modal { position: fixed; inset: 0; background: rgba(0,0,0,.95); display: none; align-items: center; justify-content: center; z-index: 5000; opacity: 0; transition: opacity .3s ease; }
        .lightbox-modal.active { display: flex; opacity: 1; }
        .lightbox-content { position: relative; max-width: 90vw; max-height: 90vh; }
        .lightbox-image { max-width: 100%; max-height: 100%; object-fit: contain; border-radius: 8px; box-shadow: 0 20px 60px rgba(0,0,0,.5); }
        .lightbox-nav { position: absolute; top: 50%; transform: translateY(-50%); background: rgba(255,255,255,.2); border: none; color: #fff; font-size: 2rem; padding: 14px 18px; border-radius: 50%; cursor: pointer; }
        .lightbox-prev { left: 20px; } .lightbox-next { right: 20px; }
        .lightbox-close { position: absolute; top: 20px; right: 20px; background: rgba(255,255,255,.2); border: none; color: #fff; font-size: 1.4rem; padding: 10px 14px; border-radius: 50%; cursor: pointer; }
        .lightbox-counter { position: absolute; bottom: 18px; left: 50%; transform: translateX(-50%); color: #fff; background: rgba(0,0,0,.5); padding: 6px 12px; border-radius: 999px; font-size: .9rem; }

        /* Video overlay section (kept) */
        .video-section { position: fixed; inset: 0; background: #000; z-index: 1500; opacity: 0; visibility: hidden; transition: all .8s ease; }
        .video-section.active { opacity: 1; visibility: visible; }
        .fullscreen-video { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; }
        .video-overlay-content { position: absolute; inset: 0; display: grid; place-items: center; color: #fff; text-align: center; background: linear-gradient(45deg, rgba(0,0,0,.35), rgba(46,125,50,.25), rgba(0,0,0,.35)); pointer-events: none; }
        .video-title-overlay { font-family: 'Great Vibes', cursive; font-size: 3.6rem; text-shadow: 0 3px 8px rgba(0,0,0,.55); }
        .video-controls-overlay { position: absolute; bottom: 30px; left: 50%; transform: translateX(-50%); display: grid; gap: 12px; place-items: center; }
        .skip-video-btn { pointer-events: auto; background: rgba(255,255,255,.2); border: 2px solid rgba(255,255,255,.85); color: #fff; padding: 10px 22px; border-radius: 999px; letter-spacing: 1px; cursor: pointer; }
        .fallback-play-button { position: absolute; top: 50%; left: 50%; transform: translate(-50%,-50%); width: 90px; height: 90px; border-radius: 50%; background: rgba(46,125,50,.9); border: 4px solid #fff; display: none; align-items: center; justify-content: center; box-shadow: 0 10px 30px rgba(0,0,0,.5); cursor: pointer; }
        .fallback-play-button svg { width: 36px; height: 36px; fill: #fff; margin-left: 4px; }

        /* Music player (kept from v15 design language) */
        .music-player { position: fixed; bottom: 20px; left: 20px; background: rgba(255,255,255,.95); border-radius: 25px; padding: 14px 18px; box-shadow: 0 10px 28px var(--shadow); z-index: 1600; display: flex; gap: 14px; align-items: center; border: 1px solid rgba(46,125,50,.25); min-width: 320px; opacity: 0; visibility: hidden; transition: .4s ease; }
        .music-player.visible { opacity: 1; visibility: visible; }
        .player-toggle { width: 40px; height: 40px; border-radius: 50%; background: var(--forest); color: #fff; display: grid; place-items: center; cursor: pointer; }
        .player-info { flex: 1; min-width: 0; }
        .song-title { font-weight: 600; font-size: .9rem; color: var(--text); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .song-artist { font-size: .8rem; color: #6e7c6e; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .progress-bar { width: 100%; height: 3px; background: #ddd; border-radius: 2px; margin: 8px 0 4px; cursor: pointer; position: relative; }
        .progress-fill { height: 100%; background: var(--forest); border-radius: 2px; width: 0%; transition: width .3s ease; }
        .time-display { display: flex; justify-content: space-between; font-size: .75rem; color: #888; }
        .player-controls { display: flex; gap: 10px; }
        .control-btn { width: 36px; height: 36px; border-radius: 50%; display: grid; place-items: center; border: none; background: rgba(46,125,50,.08); color: var(--forest); cursor: pointer; }
        .control-btn.play-pause { background: var(--forest); color: #fff; }
        .volume-control { display: flex; align-items: center; gap: 8px; }
        .volume-slider { width: 60px; height: 4px; background: #ddd; border-radius: 2px; appearance: none; }
        .volume-slider::-webkit-slider-thumb { -webkit-appearance: none; width: 16px; height: 16px; background: var(--forest); border-radius: 50%; }

        /* Responsive */
        @media (max-width: 768px) {
            .nav-links { display: none; position: absolute; top: 100%; left: 0; width: 100%; background: #fff; padding: 1rem 0; box-shadow: 0 10px 24px var(--shadow); flex-direction: column; }
            .nav-links.active { display: flex; }
            .nav-links li { margin: .5rem 0; text-align: center; }
            .hamburger { display: block; }
            .portrait { width: 220px; height: 220px; }
            .music-player { left: 10px; bottom: 10px; min-width: 280px; }
        }
    </style>
</head>
<body class="invitation-active">
    <!-- Forest Curtain Overlay -->
    <div class="forest-curtain" id="forestCurtain">
        <div class="layer left"></div>
        <div class="layer right"></div>
        <div class="forest-badge"><i class="fas fa-leaf"></i></div>
        <div class="forest-instruction">Tap to enter the forest</div>
    </div>

    <!-- Background Music Player (kept) -->
    <div class="music-player" id="musicPlayer">
        <div class="player-toggle" onclick="togglePlayer()"><i class="fas fa-music" id="playerToggleIcon"></i></div>
        <div class="player-info">
            <div class="song-title" id="currentSongTitle">Forest Waltz</div>
            <div class="song-artist" id="currentSongArtist">Wedding Music</div>
            <div class="progress-bar" onclick="seekToPosition(event)"><div class="progress-fill" id="progressFill"></div></div>
            <div class="time-display"><span id="currentTime">0:00</span><span id="totalTime">0:00</span></div>
        </div>
        <div class="player-controls">
            <button class="control-btn" onclick="previousSong()"><i class="fas fa-step-backward"></i></button>
            <button class="control-btn play-pause" onclick="togglePlayPause()" id="playPauseBtn"><i class="fas fa-play" id="playPauseIcon"></i></button>
            <button class="control-btn" onclick="nextSong()"><i class="fas fa-step-forward"></i></button>
            <button class="control-btn" onclick="openPlaylist()"><i class="fas fa-list"></i></button>
        </div>
        <div class="volume-control">
            <i class="fas fa-volume-up" id="volumeIcon"></i>
            <input type="range" class="volume-slider" id="volumeSlider" min="0" max="100" value="35" onchange="adjustVolume(this.value)">
        </div>
    </div>

    <!-- Playlist Modal -->
    <div class="playlist-modal" id="playlistModal" style="position: fixed; inset: 0; background: rgba(0,0,0,.7); display: none; align-items: center; justify-content: center; z-index: 2000; opacity: 0; transition: opacity .3s ease;">
        <div class="playlist-content" style="background: #fff; border-radius: 16px; padding: 24px; width: 90%; max-width: 520px; max-height: 70vh; overflow-y: auto; position: relative;">
            <div class="playlist-header" style="display: flex; justify-content: space-between; align-items: center; padding-bottom: 12px; border-bottom: 1px solid #eee; margin-bottom: 16px;">
                <h3 class="playlist-title" style="margin: 0; color: var(--forest); font-family: 'Cormorant Garamond', serif;">Wedding Playlist</h3>
                <button class="playlist-close" onclick="closePlaylist()" style="border: none; background: none; font-size: 1.4rem; cursor: pointer; color: #666;">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div id="playlistSongs"></div>
        </div>
    </div>

    <!-- Hidden Audio Element -->
    <audio id="audioPlayer" preload="auto">Your browser does not support the audio element.</audio>

    <!-- Floating Menu -->
    <div class="floating-menu">
        <div class="menu-item" onclick="scrollToSection('hero')"><i class="fas fa-home"></i></div>
        <div class="menu-item" onclick="scrollToSection('countdown')"><i class="fas fa-clock"></i></div>
        <div class="menu-item" onclick="scrollToSection('couple')"><i class="fas fa-heart"></i></div>
        <div class="menu-item" onclick="scrollToSection('events')"><i class="fas fa-calendar-alt"></i></div>
        <div class="menu-item" onclick="scrollToSection('gallery')"><i class="fas fa-images"></i></div>
        <div class="menu-item" onclick="scrollToSection('rsvp')"><i class="fas fa-envelope"></i></div>
    </div>

    <!-- Video Section (horizontal and vertical) -->
    <div class="video-section" id="videoSection">
        <video id="weddingVideoHorizontal" class="fullscreen-video" muted preload="auto" playsinline autoplay style="display: none;" poster="{{ $details['video_poster_horizontal'] ?? $details['video_poster'] ?? 'https://images.unsplash.com/photo-1500530855697-b586d89ba3ee?w=1920&h=1080&fit=crop' }}">
            <source src="{{ $details['wedding_invitation_video_horizontal'] ?? $details['wedding_invitation_video'] ?? 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/BigBuckBunny.mp4' }}" type="video/mp4">
        </video>
        <video id="weddingVideoVertical" class="fullscreen-video" muted preload="auto" playsinline autoplay style="display: none;" poster="{{ $details['video_poster_vertical'] ?? $details['video_poster'] ?? 'https://images.unsplash.com/photo-1500530855697-b586d89ba3ee?w=1080&h=1920&fit=crop' }}">
            <source src="{{ $details['wedding_invitation_video_vertical'] ?? $details['wedding_invitation_video'] ?? 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/BigBuckBunny.mp4' }}" type="video/mp4">
        </video>
        <div class="fallback-play-button" id="fallbackPlayBtn" onclick="playVideoFallback()" style="display:none;">
            <svg viewBox="0 0 24 24"><path d="M8,5.14V19.14L19,12.14L8,5.14Z"/></svg>
        </div>
        <div class="video-overlay-content">
            <div>
                <h1 class="video-title-overlay">{{ $details["groom_name"] ?? "Groom" }} & {{ $details["bride_name"] ?? "Bride" }}</h1>
                <p style="letter-spacing:2px;text-transform:uppercase;opacity:.9">{{ $details["video_subtitle"] ?? "A Message Among the Trees" }}</p>
            </div>
        </div>
        <div class="video-controls-overlay">
            <button class="skip-video-btn" onclick="skipToMainContent()">Skip Video</button>
            <div style="color:rgba(255,255,255,.85);text-align:center;cursor:pointer" onclick="skipToMainContent()">
                <i class="fas fa-chevron-down" style="font-size:26px"></i>
                <div style="font-size:.9rem;letter-spacing:1px">Scroll to Continue</div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content" id="mainContent">
        <header>
            <div class="nav-container">
                <a href="#hero" class="logo">{{ substr($details["groom_name"] ?? "G", 0, 1) }} & {{ substr($details["bride_name"] ?? "B", 0, 1) }}</a>
                <nav>
                    <ul class="nav-links">
                        <li><a href="#hero">Home</a></li>
                        <li><a href="#countdown">Countdown</a></li>
                        <li><a href="#couple">Couple</a></li>
                        <li><a href="#events">Events</a></li>
                        <li><a href="#gallery">Gallery</a></li>
                        <li><a href="#rsvp">RSVP</a></li>
                    </ul>
                    <div class="hamburger"><i class="fas fa-bars"></i></div>
                </nav>
            </div>
        </header>

        <!-- Hero -->
        <section id="hero" class="hero">
            <img class="vine left" src="https://upload.wikimedia.org/wikipedia/commons/5/5b/Vine_vector.svg" alt="vine"/>
            <img class="vine right" src="https://upload.wikimedia.org/wikipedia/commons/5/5b/Vine_vector.svg" alt="vine"/>
            <div>
                <div class="tagline">Among the pines and open skies</div>
                <h1 class="script">{{ $details["groom_name"] ?? "Groom" }} & {{ $details["bride_name"] ?? "Bride" }}</h1>
                <h2 style="letter-spacing:3px">{{ strtoupper($details["wedding_date"] ?? "WEDDING DATE") }}</h2>
            </div>
            <div class="scroll-down"><a href="#countdown" style="color:#fff"><i class="fas fa-chevron-down"></i></a></div>
        </section>

        <!-- Countdown -->
        <section id="countdown" class="countdown">
            <div class="container">
                <h2 class="section-title">Count Down to Our Big Day</h2>
                <p style="text-align:center;color:#6b726b;margin-bottom:1.6rem">Join us in counting the moments until we say "I Do"</p>
                <div class="countdown-timer" id="countdownTimer">
                    <div class="cd-item"><div class="cd-num" id="days">00</div><div class="cd-label">Days</div></div>
                    <div class="cd-item"><div class="cd-num" id="hours">00</div><div class="cd-label">Hours</div></div>
                    <div class="cd-item"><div class="cd-num" id="minutes">00</div><div class="cd-label">Minutes</div></div>
                    <div class="cd-item"><div class="cd-num" id="seconds">00</div><div class="cd-label">Seconds</div></div>
                </div>
                <div id="countdownExpired" style="display:none;text-align:center;margin-top:1.5rem">
                    <h3 class="script" style="font-size:2.2rem;color:var(--forest);margin:0">We're Married!</h3>
                    <p style="margin:0;color:#6b726b">Thank you for being part of our special day</p>
                </div>
            </div>
        </section>

        <!-- Couple -->
        <section id="couple" class="couple">
            <div class="container">
                <h2 class="section-title">The Couple</h2>
                <div class="couple-grid">
                    <div class="person">
                        <div class="portrait">
                            @if($details["groom_photo"] ?? false)
                                <img src="{{ $details['groom_photo'] }}" alt="Groom">
                            @else
                                <img src="https://images.unsplash.com/photo-1494790108755-2616c4e50b47?w=640&h=640&fit=crop" alt="Groom">
                            @endif
                        </div>
                        <h3>{{ $details["groom_full_name"] ?? $details["groom_name"] ?? "Groom Full Name" }}</h3>
                        <p>{{ $details["groom_parents"] ?? "Son of Mr. & Mrs. Groom's Parents" }}</p>
                    </div>
                    <div class="person">
                        <div class="portrait">
                            @if($details["bride_photo"] ?? false)
                                <img src="{{ $details['bride_photo'] }}" alt="Bride">
                            @else
                                <img src="https://images.unsplash.com/photo-1606216794074-735e91aa2c92?w=640&h=640&fit=crop" alt="Bride">
                            @endif
                        </div>
                        <h3>{{ $details["bride_full_name"] ?? $details["bride_name"] ?? "Bride Full Name" }}</h3>
                        <p>{{ $details["bride_parents"] ?? "Daughter of Mr. & Mrs. Bride's Parents" }}</p>
                    </div>
                </div>
                <div style="text-align:center;margin-top:2rem;border:2px solid var(--forest);border-radius:12px;background:#fff;padding:1.8rem;box-shadow:0 10px 24px var(--shadow)">
                    <div style="font-family:'Scheherazade New', serif;font-size:1.6rem;direction:rtl;color:var(--forest);margin-bottom:.6rem">بارك الله لكما وبارك عليكما وجمع بينكما في خير</div>
                    <div style="font-style:italic;color:#6b726b">"May Allah bless you both, shower His blessings upon you, and join you in goodness."</div>
                </div>
            </div>
        </section>

        <!-- Events -->
        <section id="events" class="events">
            <div class="container">
                <h2 class="section-title">Wedding Events</h2>
                <div class="nature-card">
                    <div class="header">
                        <div class="script" style="font-size:2rem;color:var(--forest)">You're Invited</div>
                        <div style="color:#6b726b">To celebrate the wedding of {{ $details["groom_name"] ?? "Ahmad" }} & {{ $details["bride_name"] ?? "Siti" }}</div>
                    </div>
                    <div class="body">
                        <div style="text-align:center;margin-bottom:1.2rem">
                            <div style="font-size:1.25rem;font-weight:800;color:var(--cedar);letter-spacing:.5px">{{ $details["wedding_date"] ?? "7 Disember 2025, Ahad" }}</div>
                            <div style="color:#5d6a5d;font-style:italic">{{ $details["hijri_date"] ?? "bermula 6 Jamadilakhir 1447" }}</div>
                        </div>
                        <div class="program">
                            @if($details["akad_time"] ?? false)
                            <div class="program-item"><span>{{ $details["akad_title"] ?? "Akad Nikah" }}</span><strong style="color:var(--forest)">{{ $details["akad_time"] ?? "10:00am" }}</strong></div>
                            @endif
                            <div class="program-item"><span>{{ $details["reception_meal_label"] ?? "Walima Feast" }}</span><strong style="color:var(--forest)">{{ $details["reception_meal_time"] ?? "6:00pm - 10:00pm" }}</strong></div>
                            <div class="program-item"><span>{{ $details["bride_arrival_label"] ?? "Ketibaan pengantin" }}</span><strong style="color:var(--forest)">{{ $details["bride_arrival_time"] ?? "8:00pm" }}</strong></div>
                            @if($details["additional_program_label"] ?? false)
                            <div class="program-item"><span>{{ $details["additional_program_label"] }}</span><strong style="color:var(--forest)">{{ $details["additional_program_time"] ?? "9:00pm" }}</strong></div>
                            @endif
                        </div>

                        <div class="location">
                            <div>
                                <div style="font-size:1.4rem;font-weight:800;color:var(--forest);text-transform:uppercase;letter-spacing:1px">{{ $details["venue_name"] ?? "THE GLASS TREE" }}</div>
                                <div style="color:#6b726b;font-style:italic">{{ $details["venue_subtitle"] ?? "by Zuljanah Palace" }}</div>
                                <div style="color:#4d5a4d;margin-top:.5rem">{{ $details["venue_address"] ?? "S03, B5, Kampung Seri Kembangan, 43300 Klang, Selangor" }}</div>
                            </div>
                            <div class="qr">
                                @if($details["qr_code_image"] ?? false)
                                    <img src="{{ $details['qr_code_image'] }}" alt="QR Code" style="max-width:100%;max-height:100%">
                                @else
                                    <i class="fas fa-qrcode" style="font-size:2rem;color:var(--forest)"></i>
                                @endif
                            </div>
                        </div>

                        <div class="contact-list">
                            @if($details["contact_person_1"] ?? false)
                            <div class="contact-item"><span>{{ $details["contact_person_1"] }}</span><strong style="color:var(--forest);font-family:'Courier New',monospace">{{ $details["contact_number_1"] }}</strong></div>
                            @endif
                            @if($details["contact_person_2"] ?? false)
                            <div class="contact-item"><span>{{ $details["contact_person_2"] }}</span><strong style="color:var(--forest);font-family:'Courier New',monospace">{{ $details["contact_number_2"] }}</strong></div>
                            @endif
                            @if($details["contact_person_3"] ?? false)
                            <div class="contact-item"><span>{{ $details["contact_person_3"] }}</span><strong style="color:var(--forest);font-family:'Courier New',monospace">{{ $details["contact_number_3"] }}</strong></div>
                            @endif
                        </div>

                        @if($details["venue_map_embed"] ?? false)
                        <div style="margin-top:1.8rem"><iframe src="{{ $details['venue_map_embed'] }}" style="width:100%;height:380px;border:none;border-radius:12px;box-shadow:0 12px 26px var(--shadow)" allowfullscreen loading="lazy"></iframe></div>
                        @endif
                    </div>
                </div>
            </div>
        </section>

        <!-- Gallery -->
        <section id="gallery" class="gallery">
            <div class="container">
                <h2 class="section-title">Our Journey</h2>

                <!-- Before/After Slider -->
                <div class="comparison">
                    <div class="comparison-container">
                        <img class="comparison-before" src="{{ $details['before_photo'] ?? 'https://images.unsplash.com/photo-1521510895919-46920266ddb0?auto=format&fit=crop&w=1200&q=80' }}" alt="Before">
                        <img class="comparison-after" src="{{ $details['after_photo'] ?? 'https://images.unsplash.com/photo-1515187029135-18ee286d815b?auto=format&fit=crop&w=1200&q=80' }}" alt="After">
                        <div id="comparisonHandle" class="comparison-slider-handle"></div>
                        <div class="comparison-labels" style="position:absolute;top:16px;left:0;right:0;display:flex;justify-content:space-between;padding:0 16px;z-index:5">
                            <span style="background:rgba(0,0,0,.6);color:#fff;padding:6px 12px;border-radius:999px">{{ $details['before_label'] ?? 'When We First Met' }}</span>
                            <span style="background:rgba(0,0,0,.6);color:#fff;padding:6px 12px;border-radius:999px">{{ $details['after_label'] ?? 'Ready to Say I Do' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Photos -->
                <div class="gallery-grid" id="galleryGrid">
                    @for($i = 1; $i <= 6; $i++)
                    <div class="g-item" onclick="openLightbox({{ $i - 1 }})">
                        @if($details["gallery_photo_$i"] ?? false)
                            <img src="{{ $details['gallery_photo_' . $i] }}" alt="Gallery Photo {{ $i }}" loading="lazy" crossorigin="anonymous">
                        @else
                            <img src="https://images.unsplash.com/photo-1521510895919-46920266ddb0?auto=format&fit=crop&w=1200&q=80" alt="Gallery Photo {{ $i }}" loading="lazy" crossorigin="anonymous">
                        @endif
                    </div>
                    @endfor
                </div>
            </div>
        </section>

        <!-- Lightbox Modal -->
        <div class="lightbox-modal" id="lightboxModal">
            <div class="lightbox-content">
                <img id="lightboxImage" src="" alt="" class="lightbox-image">
                <button class="lightbox-nav lightbox-prev" onclick="navigateLightbox(-1)"><i class="fas fa-chevron-left"></i></button>
                <button class="lightbox-nav lightbox-next" onclick="navigateLightbox(1)"><i class="fas fa-chevron-right"></i></button>
                <button class="lightbox-close" onclick="closeLightbox()"><i class="fas fa-times"></i></button>
                <div class="lightbox-counter" id="lightboxCounter">1 / 6</div>
            </div>
        </div>

        <!-- RSVP -->
        <section id="rsvp" class="rsvp">
            <div class="container">
                <h2 class="section-title">Please RSVP</h2>

                <div id="rsvp-success" class="alert alert-success" style="display:none; max-width: 600px; margin: 0 auto 1.5rem;">
                    <h4 style="margin:0 0 .4rem 0"><i class="fas fa-check-circle"></i> Thank You!</h4>
                    <p id="success-message" style="margin:0"></p>
                </div>
                <div id="rsvp-error" class="alert alert-danger" style="display:none; max-width: 600px; margin: 0 auto 1.5rem;">
                    <h4 style="margin:0 0 .4rem 0"><i class="fas fa-exclamation-circle"></i> Error</h4>
                    <p id="error-message" style="margin:0"></p>
                </div>

                <form class="rsvp-form" id="rsvpForm">
                    <div class="form-group"><input type="text" name="guest_name" class="form-control" placeholder="Your Name" required><div class="invalid-feedback"></div></div>
                    <div class="form-group"><input type="email" name="guest_email" class="form-control" placeholder="Your Email" required><div class="invalid-feedback"></div></div>
                    <div class="form-group"><input type="tel" name="guest_phone" class="form-control" placeholder="Your Phone Number"><div class="invalid-feedback"></div></div>
                    <div class="form-group"><select name="attendance_status" class="form-control" required>
                        <option value="" disabled selected>Will you attend?</option>
                        <option value="yes">Yes, I will attend</option>
                        <option value="no">Sorry, I cannot attend</option>
                    </select><div class="invalid-feedback"></div></div>
                    <div class="form-group" id="guests-group"><input type="number" name="number_of_guests" class="form-control" placeholder="Number of Guests" min="1" max="10" value="1"><div class="invalid-feedback"></div></div>
                    <div class="form-group"><textarea name="message" class="form-control" placeholder="Your Message" rows="4"></textarea><div class="invalid-feedback"></div></div>
                    <button type="submit" class="btn btn-primary" id="rsvp-submit-btn"><span class="btn-text">Send RSVP</span><span class="btn-loading" style="display:none"><i class="fas fa-spinner fa-spin"></i> Sending...</span></button>
                </form>
            </div>
        </section>

        <!-- Footer -->
        <footer>
            <div class="container">
                <div class="footer-content" style="max-width:640px;margin:0 auto">
                    <h2 class="script" style="font-size:2rem;margin-bottom:.6rem">{{ $details["groom_name"] ?? "Groom" }} & {{ $details["bride_name"] ?? "Bride" }}</h2>
                    <p>{{ $details["footer_message"] ?? "Thank you for celebrating our special day with us!" }}</p>
                    <p style="opacity:.75;margin-top:1rem">&copy; {{ date('Y') }} {{ $details["groom_name"] ?? "Groom" }} & {{ $details["bride_name"] ?? "Bride" }}. All Rights Reserved.</p>
                </div>
            </div>
        </footer>
    </div>

    <script>
        // Mobile nav
        const hamburger = document.querySelector('.hamburger');
        const navLinks = document.querySelector('.nav-links');
        if (hamburger && navLinks) { hamburger.addEventListener('click', () => navLinks.classList.toggle('active')); }
        window.addEventListener('scroll', () => { const h = document.querySelector('header'); if (h) h.classList.toggle('scrolled', window.scrollY > 50); });

        // RSVP AJAX (kept)
        const rsvpForm = document.getElementById('rsvpForm');
        if (rsvpForm) {
            const submitBtn = document.getElementById('rsvp-submit-btn');
            const btnText = submitBtn.querySelector('.btn-text');
            const btnLoading = submitBtn.querySelector('.btn-loading');
            const attendanceSelect = rsvpForm.querySelector('[name="attendance_status"]');
            const guestsGroup = document.getElementById('guests-group');
            attendanceSelect.addEventListener('change', function(){ if (this.value === 'yes'){ guestsGroup.style.display = 'block'; guestsGroup.querySelector('input').required = true; } else { guestsGroup.style.display = 'none'; guestsGroup.querySelector('input').required = false; guestsGroup.querySelector('input').value=''; } });
            rsvpForm.addEventListener('submit', function(e){ e.preventDefault(); clearFormErrors(); hideMessages(); submitBtn.disabled = true; btnText.style.display='none'; btnLoading.style.display='inline'; const formData = new FormData(this); const uniqueUrl = window.location.pathname.split('/').pop(); fetch(`/wedding-card/${uniqueUrl}/rsvp`, { method:'POST', body:formData, headers:{'X-Requested-With':'XMLHttpRequest'} })
                .then(r=>r.json()).then(data=>{ if (data.success){ showSuccessMessage(data.message); rsvpForm.reset(); rsvpForm.style.display='none'; } else { if (data.errors){ showFieldErrors(data.errors); } else { showErrorMessage(data.message || 'An error occurred.'); } } })
                .catch(()=> showErrorMessage('An error occurred. Please try again.'))
                .finally(()=>{ submitBtn.disabled=false; btnText.style.display='inline'; btnLoading.style.display='none'; }); });
        }
        function showSuccessMessage(m){ const d=document.getElementById('rsvp-success'); document.getElementById('success-message').textContent=m; d.style.display='block'; d.scrollIntoView({behavior:'smooth', block:'center'}); }
        function showErrorMessage(m){ const d=document.getElementById('rsvp-error'); document.getElementById('error-message').textContent=m; d.style.display='block'; d.scrollIntoView({behavior:'smooth', block:'center'}); }
        function hideMessages(){ const a=document.getElementById('rsvp-success'); const b=document.getElementById('rsvp-error'); if(a) a.style.display='none'; if(b) b.style.display='none'; }
        function clearFormErrors(){ document.querySelectorAll('.invalid-feedback').forEach(el=>{ el.textContent=''; el.style.display='none'; }); document.querySelectorAll('.form-control').forEach(el=> el.classList.remove('is-invalid')); }
        function showFieldErrors(errors){ for (const field in errors){ const input=document.querySelector(`[name="${field}"]`); const errorDiv=input?.parentNode?.querySelector('.invalid-feedback'); if(input && errorDiv){ input.classList.add('is-invalid'); errorDiv.textContent=errors[field][0]; errorDiv.style.display='block'; } } }

        // Forest curtain -> video
        let videoStarted = false; let autoplayFailed = false; let video = null;
        document.addEventListener('DOMContentLoaded', () => {
            document.body.classList.add('invitation-active');
            const curtain = document.getElementById('forestCurtain');
            const videoSection = document.getElementById('videoSection');
            const mainContent = document.getElementById('mainContent');
            const videoHorizontal = document.getElementById('weddingVideoHorizontal');
            const videoVertical = document.getElementById('weddingVideoVertical');
            const fallbackPlayBtn = document.getElementById('fallbackPlayBtn');

            const chooseVideoByOrientation = () => {
                const isPortrait = (window.matchMedia && window.matchMedia('(orientation: portrait)').matches) || (window.innerHeight >= window.innerWidth);
                if (isPortrait && videoVertical){ video = videoVertical; if (videoHorizontal) videoHorizontal.style.display='none'; videoVertical.style.display='block'; }
                else { video = videoHorizontal || videoVertical; if (videoHorizontal) videoHorizontal.style.display='block'; if (videoVertical) videoVertical.style.display='none'; }
            };
            chooseVideoByOrientation();
            window.addEventListener('orientationchange', ()=>{ if(!videoStarted) chooseVideoByOrientation(); });
            window.addEventListener('resize', ()=>{ if(!videoStarted) chooseVideoByOrientation(); });

            if (video) {
                video.addEventListener('ended', () => setTimeout(skipToMainContent, 1200));
            }

            if (curtain){
                curtain.addEventListener('click', () => {
                    curtain.classList.add('opened');
                    setTimeout(() => { curtain.classList.add('fade-out'); }, 900);
                    setTimeout(() => { curtain.style.display = 'none'; document.body.classList.remove('invitation-active'); if (videoSection) videoSection.classList.add('active'); if (video){ video.play().then(()=>{ fallbackPlayBtn.style.display='none'; videoStarted = true; }).catch(()=>{ autoplayFailed = true; fallbackPlayBtn.style.display='flex'; }); } }, 1600);
                });
            }

            // show floating menu after entering main content
            document.addEventListener('scroll', () => { const header = document.querySelector('header'); if (header) header.classList.toggle('scrolled', window.scrollY > 50); }, {passive:true});
        });

        function playVideoFallback(){ const activeVideo = window.video || document.getElementById('weddingVideoHorizontal') || document.getElementById('weddingVideoVertical'); const btn = document.getElementById('fallbackPlayBtn'); if (activeVideo){ activeVideo.play().then(()=>{ btn.style.display='none'; videoStarted=true; }).catch(()=>{}); } }

        function skipToMainContent(){ const videoSection = document.getElementById('videoSection'); const mainContent = document.getElementById('mainContent'); const floatingMenu = document.querySelector('.floating-menu'); if (videoSection && mainContent){ videoSection.style.transition='opacity 1s ease'; videoSection.style.opacity='0'; setTimeout(()=>{ videoSection.style.display='none'; mainContent.style.display='block'; document.body.classList.add('content-visible'); try{ const vh=document.getElementById('weddingVideoHorizontal'); const vv=document.getElementById('weddingVideoVertical'); if(vh && !vh.paused) vh.pause(); if(vv && !vv.paused) vv.pause(); }catch(e){} if (floatingMenu){ setTimeout(()=> floatingMenu.classList.add('visible'), 500); } const hero=document.getElementById('hero'); if (hero) hero.scrollIntoView({behavior:'smooth'}); }, 900); } }

        // Smooth scroll by id
        function scrollToSection(id){ const main = document.getElementById('mainContent'); const section = main?.querySelector('section#'+id); const header = document.querySelector('header'); if (!section) return; const target = section.offsetTop + main.offsetTop - (header ? header.offsetHeight : 0) - 10; window.scrollTo({ top: target, behavior: 'smooth' }); }

        // Countdown
        function initCountdown(){ const str = "{{ $details['wedding_datetime'] ?? $details['wedding_date'] ?? '2025-12-31 18:00:00' }}"; let d; try { let s=str.trim(); if (s.includes('T') && s.match(/T\d{2}:\d{2}$/)) s += ':00'; s = s.replace('T',' '); if (!s.includes(' ') && !s.includes('T')) s += ' 18:00:00'; d = new Date(s); if (isNaN(d.getTime())) throw new Error('bad'); } catch(e){ d = new Date(); d.setMonth(d.getMonth()+3); }
            const elD=document.getElementById('days'), elH=document.getElementById('hours'), elM=document.getElementById('minutes'), elS=document.getElementById('seconds'); const expired=document.getElementById('countdownExpired'); const timer=document.getElementById('countdownTimer'); function upd(){ const now=Date.now(); const left=d.getTime()-now; if (left<=0){ if(timer) timer.style.display='none'; if(expired) expired.style.display='block'; return; } const days=Math.floor(left/86400000); const hours=Math.floor((left%86400000)/3600000); const mins=Math.floor((left%3600000)/60000); const secs=Math.floor((left%60000)/1000); if(elD) elD.textContent = String(days).padStart(2,'0'); if(elH) elH.textContent = String(hours).padStart(2,'0'); if(elM) elM.textContent = String(mins).padStart(2,'0'); if(elS) elS.textContent = String(secs).padStart(2,'0'); } upd(); setInterval(upd,1000); }
        document.addEventListener('DOMContentLoaded', initCountdown);

        // Comparison slider
        function initComparisonSlider(){ const handle=document.getElementById('comparisonHandle'); const after=document.querySelector('.comparison-after'); const container=document.querySelector('.comparison-container'); if(!handle||!after||!container) return; let dragging=false; const update=(x)=>{ const rect=container.getBoundingClientRect(); const p=Math.max(0, Math.min(1, (x-rect.left)/rect.width)); handle.style.left=(p*100)+'%'; after.style.clipPath=`inset(0 ${100 - p*100}% 0 0)`; }; handle.addEventListener('mousedown', e=>{ dragging=true; document.addEventListener('mousemove', onMove); document.addEventListener('mouseup', onUp); e.preventDefault(); }); const onMove=e=>{ if(dragging) update(e.clientX); }; const onUp=()=>{ dragging=false; document.removeEventListener('mousemove', onMove); document.removeEventListener('mouseup', onUp); }; handle.addEventListener('touchstart', e=>{ dragging=true; e.preventDefault(); }); document.addEventListener('touchmove', e=>{ if(dragging){ update(e.touches[0].clientX); e.preventDefault(); } }, {passive:false}); document.addEventListener('touchend', ()=> dragging=false); container.addEventListener('click', e=>{ if(!dragging) update(e.clientX); }); }
        document.addEventListener('DOMContentLoaded', initComparisonSlider);

        // Lightbox & gallery arrays
        let currentLightboxIndex = 0; const galleryImages = [
            { src: "{{ $details['gallery_photo_1'] ?? 'https://images.unsplash.com/photo-1519225421980-715cb0215aed?ixlib=rb-4.0.3&w=1200&h=800&fit=crop' }}", alt: 'Gallery 1' },
            { src: "{{ $details['gallery_photo_2'] ?? 'https://images.unsplash.com/photo-1519225421980-715cb0215aed?ixlib=rb-4.0.3&w=1200&h=800&fit=crop' }}", alt: 'Gallery 2' },
            { src: "{{ $details['gallery_photo_3'] ?? 'https://images.unsplash.com/photo-1519225421980-715cb0215aed?ixlib=rb-4.0.3&w=1200&h=800&fit=crop' }}", alt: 'Gallery 3' },
            { src: "{{ $details['gallery_photo_4'] ?? 'https://images.unsplash.com/photo-1519225421980-715cb0215aed?ixlib=rb-4.0.3&w=1200&h=800&fit=crop' }}", alt: 'Gallery 4' },
            { src: "{{ $details['gallery_photo_5'] ?? 'https://images.unsplash.com/photo-1519225421980-715cb0215aed?ixlib=rb-4.0.3&w=1200&h=800&fit=crop' }}", alt: 'Gallery 5' },
            { src: "{{ $details['gallery_photo_6'] ?? 'https://images.unsplash.com/photo-1519225421980-715cb0215aed?ixlib=rb-4.0.3&w=1200&h=800&fit=crop' }}", alt: 'Gallery 6' },
        ];
        function openLightbox(i){ currentLightboxIndex = i; const m=document.getElementById('lightboxModal'); const img=document.getElementById('lightboxImage'); const c=document.getElementById('lightboxCounter'); img.src=galleryImages[i].src; img.alt=galleryImages[i].alt; c.textContent=(i+1)+' / '+galleryImages.length; m.classList.add('active'); document.body.style.overflow='hidden'; }
        function closeLightbox(){ const m=document.getElementById('lightboxModal'); m.classList.remove('active'); document.body.style.overflow=''; }
        function navigateLightbox(d){ currentLightboxIndex += d; if(currentLightboxIndex<0) currentLightboxIndex=galleryImages.length-1; if(currentLightboxIndex>=galleryImages.length) currentLightboxIndex=0; openLightbox(currentLightboxIndex); }
        document.addEventListener('DOMContentLoaded', ()=>{ const m=document.getElementById('lightboxModal'); if (m) m.addEventListener('click', e=>{ if(e.target===e.currentTarget) closeLightbox(); }); });

        // Music Player (kept) minimal class
        class MusicPlayer { constructor(){ this.audio=document.getElementById('audioPlayer'); this.root=document.getElementById('musicPlayer'); this.isPlaying=false; this.current=0; this.volume=0.35; this.playlist=[ { title: "{{ $details['song_1_title'] ?? 'Forest Song' }}", artist: "{{ $details['song_1_artist'] ?? 'Nature Ensemble' }}", src: "{{ $details['song_1_url'] ?? '/audio/perfect.mp3' }}", duration: '3:45' }, { title: "{{ $details['song_2_title'] ?? 'Whispering Leaves' }}", artist: "{{ $details['song_2_artist'] ?? 'Wedding Music' }}", src: "{{ $details['song_2_url'] ?? '/audio/all-of-me.mp3' }}", duration: '4:12' } ]; this.init(); }
            init(){ this.audio.volume=this.volume; this.audio.addEventListener('timeupdate', ()=> this.updateProgress()); this.audio.addEventListener('ended', ()=> this.next()); this.audio.addEventListener('loadedmetadata', ()=> this.updateDuration()); this.load(0); this.generatePlaylist(); this.showWhenReady(); }
            showWhenReady(){ const check=()=>{ if(document.body.classList.contains('content-visible')){ setTimeout(()=> this.root.classList.add('visible'), 1500); } else { setTimeout(check, 400); } }; check(); }
            load(i){ this.current=i; const s=this.playlist[i]; this.audio.src=s.src; document.getElementById('currentSongTitle').textContent=s.title; document.getElementById('currentSongArtist').textContent=s.artist; this.updatePlaylistUI(); }
            play(){ if(!this.audio.src) return; this.audio.play().then(()=>{ this.isPlaying=true; document.getElementById('playPauseIcon').className='fas fa-pause'; }).catch(()=>{}); }
            pause(){ this.audio.pause(); this.isPlaying=false; document.getElementById('playPauseIcon').className='fas fa-play'; }
            toggle(){ this.isPlaying? this.pause() : this.play(); }
            next(){ this.load((this.current+1)%this.playlist.length); if(this.isPlaying) setTimeout(()=>this.play(),100); }
            prev(){ this.load(this.current===0?this.playlist.length-1:this.current-1); if(this.isPlaying) setTimeout(()=>this.play(),100); }
            updateProgress(){ if(this.audio.duration){ const p=(this.audio.currentTime/this.audio.duration)*100; document.getElementById('progressFill').style.width=p+'%'; document.getElementById('currentTime').textContent=this.format(this.audio.currentTime); }}
            updateDuration(){ if(this.audio.duration){ document.getElementById('totalTime').textContent=this.format(this.audio.duration); }}
            format(s){ const m=Math.floor(s/60); const sec=Math.floor(s%60); return `${m}:${String(sec).padStart(2,'0')}`; }
            seekTo(e){ const rect=e.currentTarget.getBoundingClientRect(); const percent=(e.clientX - rect.left)/rect.width; const t=percent * this.audio.duration; if(!isNaN(t)) this.audio.currentTime=t; }
            setVolume(v){ this.volume=v/100; this.audio.volume=this.volume; const icon=document.getElementById('volumeIcon'); icon.className = v==0? 'fas fa-volume-mute' : (v<50? 'fas fa-volume-down' : 'fas fa-volume-up'); }
            open(){ document.getElementById('playlistModal').style.display='flex'; setTimeout(()=>{ document.getElementById('playlistModal').style.opacity='1'; }, 10); document.body.style.overflow='hidden'; }
            close(){ const m=document.getElementById('playlistModal'); m.style.opacity='0'; setTimeout(()=>{ m.style.display='none'; document.body.style.overflow=''; }, 250); }
            generatePlaylist(){ const c=document.getElementById('playlistSongs'); c.innerHTML=''; this.playlist.forEach((s,i)=>{ const d=document.createElement('div'); d.className='song-item'; d.style.cssText='display:flex;align-items:center;padding:12px;border-radius:8px;cursor:pointer;margin-bottom:8px'; d.onclick=()=>{ this.load(i); this.play(); this.close(); }; d.innerHTML=`<i class="fas fa-music"></i><div style="flex:1;margin-left:10px"><div style="font-weight:600">${s.title}</div><div style="font-size:.85rem;color:#666">${s.artist}</div></div><div style="font-size:.85rem;color:#888">${s.duration}</div>`; c.appendChild(d); }); }
            updatePlaylistUI(){ const items=document.querySelectorAll('#playlistSongs .song-item'); items.forEach((el,idx)=>{ el.style.background = idx===this.current? 'rgba(46,125,50,.12)' : 'transparent'; el.style.borderLeft = idx===this.current? '4px solid var(--forest)' : 'none'; }); }
        }
        let musicPlayerInstance = null;
        document.addEventListener('DOMContentLoaded', ()=>{ musicPlayerInstance = new MusicPlayer(); document.getElementById('playlistModal').addEventListener('click', e=>{ if(e.target.id==='playlistModal') musicPlayerInstance.close(); }); });
        function togglePlayer(){ if (musicPlayerInstance) musicPlayerInstance.toggle?.(); else musicPlayerInstance.toggle(); }
        function togglePlayPause(){ if (musicPlayerInstance) musicPlayerInstance.toggle(); }
        function nextSong(){ if (musicPlayerInstance) musicPlayerInstance.next(); }
        function previousSong(){ if (musicPlayerInstance) musicPlayerInstance.prev(); }
        function openPlaylist(){ if (musicPlayerInstance) musicPlayerInstance.open(); }
        function closePlaylist(){ if (musicPlayerInstance) musicPlayerInstance.close(); }
        function seekToPosition(e){ if (musicPlayerInstance) musicPlayerInstance.seekTo(e); }
        function adjustVolume(v){ if (musicPlayerInstance) musicPlayerInstance.setVolume(v); }
    </script>
</body>
</html>

