{{-- Enhanced Malaysian Wedding Template v4 with Card Format --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $details["bride_name"] ?? "Bride" }} & {{ $details["groom_name"] ?? "Groom" }} - Wedding Invitation</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
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

        html {
            scroll-behavior: smooth;
            scroll-padding-top: 80px;
        }

        body {
            font-family: 'Montserrat', sans-serif;
            color: var(--text-dark);
            line-height: 1.6;
            background-color: var(--secondary);
            overflow-x: hidden;
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
            padding: 100px 0;
            position: relative;
            overflow: hidden;
        }

        /* Prevent scrolling when invitation is shown */
        body.invitation-active {
            overflow: hidden;
            height: 100%;
            margin: 0;
            padding: 0;
        }

        /* Fullscreen Video Section Styles */
        .video-section {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100vh;
            z-index: 1500;
            background: #000;
            opacity: 0;
            visibility: hidden;
            transition: all 0.8s ease;
        }

        .video-section.active {
            opacity: 1;
            visibility: visible;
        }

        .fullscreen-video {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            z-index: 1;
        }

        /* Video Overlay Content */
        .video-overlay-content {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(45deg, rgba(0,0,0,0.3) 0%, rgba(200,161,101,0.2) 50%, rgba(0,0,0,0.3) 100%);
            z-index: 2;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            color: white;
            pointer-events: none;
        }

        .video-title-overlay {
            font-family: 'Great Vibes', cursive;
            font-size: 4rem;
            margin-bottom: 1rem;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.7);
            animation: fadeInScale 2s ease-out 1s forwards;
            opacity: 0;
            transform: scale(0.8);
        }

        .video-subtitle-overlay {
            font-size: 1.2rem;
            letter-spacing: 2px;
            text-transform: uppercase;
            margin-bottom: 2rem;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.7);
            animation: fadeInUp 1.5s ease-out 2s forwards;
            opacity: 0;
            transform: translateY(30px);
        }

        @keyframes fadeInScale {
            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        @keyframes fadeInUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Video Controls */
        .video-controls-overlay {
            position: absolute;
            bottom: 30px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 3;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 15px;
            opacity: 0;
            animation: fadeInUp 1s ease-out 3s forwards;
        }

        .skip-video-btn {
            background: rgba(255, 255, 255, 0.2);
            border: 2px solid rgba(255, 255, 255, 0.8);
            color: white;
            padding: 12px 25px;
            border-radius: 30px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-size: 0.9rem;
            backdrop-filter: blur(10px);
            pointer-events: auto;
        }

        .skip-video-btn:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-2px);
        }

        .scroll-indicator {
            color: rgba(255, 255, 255, 0.8);
            text-align: center;
            pointer-events: auto;
            animation: gentle-bounce 2s infinite ease-in-out 4s;
            opacity: 0;
            animation-fill-mode: forwards;
        }

        .scroll-indicator i {
            font-size: 2rem;
            margin-bottom: 8px;
            display: block;
        }

        .scroll-indicator span {
            font-size: 0.9rem;
            letter-spacing: 1px;
            text-transform: uppercase;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.7);
        }

        /* Fallback Play Button (for when autoplay fails) */
        .fallback-play-button {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: rgba(200, 161, 101, 0.9);
            border: 4px solid white;
            display: none;
            justify-content: center;
            align-items: center;
            cursor: pointer;
            transition: all 0.3s ease;
            z-index: 4;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
        }

        .fallback-play-button:hover {
            transform: translate(-50%, -50%) scale(1.1);
            background: var(--primary);
        }

        .fallback-play-button svg {
            width: 40px;
            height: 40px;
            fill: white;
            margin-left: 5px;
        }

        /* Main content starts after video */
        .main-content {
            position: relative;
            z-index: 100;
            background: var(--secondary);
            min-height: 100vh;
        }

        /* Initially hide main content sections */
        body.invitation-active .main-content {
             visibility: hidden;
             opacity: 0;
        }
        
        /* Style to show content after invitation is opened */
        body.content-visible .main-content {
             visibility: visible;
             opacity: 1;
             transition: opacity 1s ease;
        }

        /* Enhanced 3D Letter/Envelope Styles */
        .invitation-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100vh;
            background: radial-gradient(ellipse at center, #f8f3e9 0%, #e8dcc0 100%);
            z-index: 2000;
            display: flex;
            justify-content: center;
            align-items: center;
            cursor: pointer;
            opacity: 1;
            transition: opacity 1.5s ease-out;
            overflow: hidden;
            perspective: 1500px;
        }

        .invitation-overlay.hidden {
            opacity: 0;
            pointer-events: none;
        }

        /* Floating envelope with realistic 3D perspective */
        .envelope-container {
            position: relative;
            transform-style: preserve-3d;
            transition: transform 1.5s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .envelope-container:hover {
            transform: rotateY(5deg) rotateX(-5deg) scale(1.02);
        }

        .envelope {
            position: relative;
            width: 450px;
            height: 320px;
            transform-style: preserve-3d;
            perspective: 1000px;
            cursor: pointer;
            transition: all 0.6s ease;
        }

        /* Envelope back */
        .envelope-back {
            position: absolute;
            width: 100%;
            height: 100%;
            background: linear-gradient(145deg, #f9f6ed 0%, #f2edd8 50%, #ede6ce 100%);
            border-radius: 8px;
            box-shadow: 
                0 20px 50px rgba(0, 0, 0, 0.15),
                0 10px 25px rgba(0, 0, 0, 0.1),
                inset 0 1px 0 rgba(255, 255, 255, 0.6);
            border: 1px solid rgba(200, 161, 101, 0.3);
            transform: translateZ(-5px);
        }

        /* Envelope flap - creates the 3D opening effect */
        .envelope-flap {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 60%;
            background: linear-gradient(135deg, #f2edd8 0%, #ede6ce 50%, #e6ddc6 100%);
            transform-origin: top center;
            transform: rotateX(0deg) translateZ(2px);
            transition: transform 1.8s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            border-radius: 8px 8px 0 0;
            box-shadow: 
                0 5px 15px rgba(0, 0, 0, 0.2),
                inset 0 1px 0 rgba(255, 255, 255, 0.5);
            border: 1px solid rgba(200, 161, 101, 0.3);
            border-bottom: none;
            z-index: 10;
        }

        /* Envelope flap triangle effect */
        .envelope-flap::before {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 40px;
            background: linear-gradient(to bottom, transparent 0%, rgba(200, 161, 101, 0.1) 100%);
            clip-path: polygon(0 0, 50% 100%, 100% 0);
            transform: translateY(1px);
        }

        /* Flap opening animation */
        .envelope.opening .envelope-flap {
            transform: rotateX(-180deg) translateZ(2px);
            transform-origin: top center;
        }

        /* Envelope seal/wax */
        .envelope-seal {
            position: absolute;
            width: 70px;
            height: 70px;
            background: radial-gradient(circle at 30% 30%, #ddb574, var(--primary), #9e7332);
            border-radius: 50%;
            top: 50px;
            left: 50%;
            transform: translate(-50%, 0) translateZ(5px);
            z-index: 15;
            display: flex;
            justify-content: center;
            align-items: center;
            box-shadow: 
                0 8px 20px rgba(0, 0, 0, 0.3),
                inset 0 2px 0 rgba(255, 255, 255, 0.3),
                inset 0 -2px 0 rgba(0, 0, 0, 0.2);
            border: 2px solid rgba(255, 255, 255, 0.2);
            transition: all 0.6s ease;
        }

        .envelope-seal::before {
            content: '';
            position: absolute;
            width: 90%;
            height: 90%;
            border-radius: 50%;
            background: radial-gradient(ellipse at 20% 20%, rgba(255, 255, 255, 0.4), transparent 60%);
            top: 5%;
            left: 5%;
        }

        .envelope-seal svg {
            width: 36px;
            height: 36px;
            fill: #fff;
            filter: drop-shadow(1px 1px 2px rgba(0, 0, 0, 0.3));
            z-index: 2;
            position: relative;
        }

        /* Seal breaks when opening */
        .envelope.opening .envelope-seal {
            transform: translate(-50%, 0) translateZ(5px) scale(0.8) rotateZ(15deg);
            opacity: 0.7;
        }

        /* Three.js Canvas Container for 3D Letter */
        .letter-canvas-container {
            position: absolute;
            top: 85px;
            left: 35px;
            right: 35px;
            bottom: 40px;
            border-radius: 3px;
            overflow: hidden;
            opacity: 0;
            transition: opacity 1s ease;
        }

        .letter-canvas-container.active {
            opacity: 1;
        }

        #letterCanvas {
            width: 100%;
            height: 100%;
            display: block;
        }

        /* Simplified letter content for initial display */
        .letter-content {
            position: absolute;
            top: 85px;
            left: 35px;
            right: 35px;
            bottom: 40px;
            background: linear-gradient(145deg, #fefdfb 0%, #faf9f5 50%, #f7f5f0 100%);
            border-radius: 3px;
            padding: 25px 20px;
            transform: translateZ(-3px) translateY(25px) scale(0.95);
            transform-origin: bottom center;
            opacity: 0.9;
            transition: all 2s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            box-shadow: 
                0 5px 15px rgba(0, 0, 0, 0.08),
                inset 0 1px 0 rgba(255, 255, 255, 0.9),
                inset 0 -1px 0 rgba(200, 161, 101, 0.1);
            border: 1px solid rgba(200, 161, 101, 0.15);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            overflow: hidden;
        }

        /* Letter paper texture and lines */
        .letter-content::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: 
                repeating-linear-gradient(
                    0deg,
                    transparent,
                    transparent 22px,
                    rgba(200, 161, 101, 0.06) 23px,
                    rgba(200, 161, 101, 0.06) 24px,
                    transparent 25px
                ),
                linear-gradient(
                    90deg,
                    transparent 0%,
                    transparent 40px,
                    rgba(255, 0, 0, 0.15) 41px,
                    rgba(255, 0, 0, 0.15) 42px,
                    transparent 43px
                );
            pointer-events: none;
            border-radius: 3px;
        }
        }

        .invitation-title {
            font-family: 'Great Vibes', cursive;
            font-size: 3.2rem;
            color: var(--primary);
            margin-bottom: 0.5rem;
            line-height: 1.2;
            position: relative;
            z-index: 2;
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.1);
        }

        .invitation-couple-names {
            font-family: 'Great Vibes', cursive;
            font-size: 2.2rem;
            color: #444;
            margin: 0.8rem 0;
            line-height: 1.2;
            position: relative;
            z-index: 2;
        }

        .invitation-separator {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            margin: 0.5rem 0;
            position: relative;
            z-index: 2;
        }

        .invitation-separator-line {
            height: 1px;
            width: 60px;
            background: linear-gradient(to right, transparent, var(--primary), transparent);
        }

        .invitation-separator svg {
            margin: 0 15px;
            fill: var(--primary);
            filter: drop-shadow(1px 1px 2px rgba(0, 0, 0, 0.1));
        }

        .wedding-date-small {
            font-size: 0.85rem;
            color: #888;
            margin-top: 0.8rem;
            font-style: italic;
            letter-spacing: 1px;
            position: relative;
            z-index: 2;
        }

        .instruction-wrapper {
            position: relative;
            margin-top: 1rem;
            z-index: 2;
            animation: gentle-pulse 2s infinite ease-in-out;
        }

        .invitation-instruction {
            position: relative;
            font-size: 0.9rem;
            color: #7a6c42;
            display: inline-block;
            font-family: 'Montserrat', sans-serif;
            font-weight: 500;
            letter-spacing: 1px;
            padding: 10px 20px;
            border: 1.5px solid rgba(200, 161, 101, 0.6);
            border-radius: 25px;
            background: linear-gradient(145deg, rgba(255, 255, 255, 0.9), rgba(248, 243, 233, 0.8));
            backdrop-filter: blur(5px);
            box-shadow: 
                0 4px 15px rgba(200, 161, 101, 0.15),
                inset 0 1px 0 rgba(255, 255, 255, 0.6);
            transition: all 0.3s ease;
        }

        .invitation-instruction:hover {
            transform: translateY(-2px);
            background: linear-gradient(145deg, rgba(255, 255, 255, 0.95), rgba(248, 243, 233, 0.9));
            box-shadow: 
                0 6px 20px rgba(200, 161, 101, 0.25),
                inset 0 1px 0 rgba(255, 255, 255, 0.8);
            border-color: var(--primary);
        }

        @keyframes gentle-pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }

        .arrow-icon {
            display: inline-block;
            margin-left: 8px;
            transition: transform 0.3s ease;
        }

        .invitation-instruction:hover .arrow-icon {
            transform: translateX(3px);
        }

        /* Decorative elements around instruction */
        .instruction-wrapper::before,
        .instruction-wrapper::after {
            content: '✦';
            color: var(--primary);
            font-size: 0.9rem;
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            opacity: 0.7;
        }

        .instruction-wrapper::before {
            left: -20px;
        }

        .instruction-wrapper::after {
            right: -20px;
        }

        /* Background floating elements */
        .envelope-container::before {
            content: '';
            position: absolute;
            top: -100px;
            left: -100px;
            width: 200px;
            height: 200px;
            background: radial-gradient(circle, rgba(200, 161, 101, 0.1) 0%, transparent 70%);
            border-radius: 50%;
            animation: float-decoration 8s ease-in-out infinite;
            z-index: -1;
        }

        .envelope-container::after {
            content: '';
            position: absolute;
            bottom: -80px;
            right: -80px;
            width: 150px;
            height: 150px;
            background: radial-gradient(circle, rgba(248, 243, 233, 0.6) 0%, transparent 70%);
            border-radius: 50%;
            animation: float-decoration 10s ease-in-out infinite reverse;
            z-index: -1;
        }

        @keyframes float-decoration {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-30px) rotate(5deg); }
        }

        /* Letter floating animation when displayed */
        @keyframes gentle-float {
            0%, 100% { 
                transform: translateX(-50%) translateY(-38%) translateZ(10px) rotateX(-2deg) scale(1.2);
            }
            50% { 
                transform: translateX(-50%) translateY(-40%) translateZ(12px) rotateX(-1deg) scale(1.22);
            }
        }

        /* Paper fold animation effects */
        .letter-content::before {
            transition: all 0.8s ease;
        }

        .envelope.letter-unfolding .letter-content::before {
            background-image: 
                repeating-linear-gradient(
                    0deg,
                    transparent,
                    transparent 22px,
                    rgba(200, 161, 101, 0.06) 23px,
                    rgba(200, 161, 101, 0.06) 24px,
                    transparent 25px
                ),
                linear-gradient(
                    90deg,
                    transparent 0%,
                    transparent 40px,
                    rgba(255, 0, 0, 0.15) 41px,
                    rgba(255, 0, 0, 0.15) 42px,
                    transparent 43px
                );
        }

        /* Letter sliding out animation phases */
        .envelope.letter-sliding-out .letter-content {
            transform: translateZ(8px) translateY(-20px) rotateX(-3deg) scale(1.02);
            transition: all 1.2s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .envelope.letter-emerging .letter-content {
            transform: translateZ(15px) translateY(-60px) rotateX(-8deg) scale(1.15);
            transition: all 1.5s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        }

        .envelope.letter-unfolding .letter-content {
            width: 75vw;
            max-width: 500px;
            height: 600px;
            top: 50%;
            left: 50%;
            right: auto;
            bottom: auto;
            padding: 40px 35px;
            transform: translateX(-50%) translateY(-40%) translateZ(10px) rotateX(-2deg) scale(1.2);
            transition: all 2s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            box-shadow: 
                0 25px 60px rgba(0, 0, 0, 0.3),
                inset 0 1px 0 rgba(255, 255, 255, 0.9),
                inset 0 -1px 0 rgba(200, 161, 101, 0.2);
        }

        /* Fixed floating state - keeps position locked during animation */
        .envelope.letter-unfolding .letter-content.letter-floating {
            animation: gentle-float 3s ease-in-out infinite;
            /* Disable transitions to prevent position changes */
            transition: none !important;
        }

        

        /* Letter content focus state */
        .letter-content-focused {
            position: relative;
            z-index: 25;
        }

        .letter-content-focused::before {
            animation: paper-shimmer 2s ease-in-out infinite alternate;
        }

        @keyframes paper-shimmer {
            from { opacity: 0.8; }
            to { opacity: 1; }
        }

        /* Floating Menu CSS */
        .floating-menu {
            position: fixed;
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
            background-color: rgba(255, 255, 255, 0.9);
            border-radius: 30px;
            box-shadow: 0 3px 15px rgba(0, 0, 0, 0.1);
            padding: 15px 10px;
            z-index: 200;
            display: flex;
            flex-direction: column;
            align-items: center;
            border: 1px solid rgba(200, 161, 101, 0.3);
            backdrop-filter: blur(5px);
            transition: all 0.3s ease;
            opacity: 0;
            visibility: hidden;
        }

        .floating-menu.visible {
            opacity: 1;
            visibility: visible;
        }

        .menu-item {
            margin: 10px 0;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            color: var(--primary);
            background-color: white;
            border: 1px solid rgba(200, 161, 101, 0.2);
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .menu-item:hover {
            transform: scale(1.1);
            background-color: var(--primary);
            color: white;
        }

        .menu-item i {
            font-size: 1.2rem;
        }

        .menu-tooltip {
            position: absolute;
            right: 60px;
            background-color: rgba(0, 0, 0, 0.7);
            color: white;
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 0.8rem;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s ease;
            white-space: nowrap;
        }

        .menu-item:hover .menu-tooltip {
            opacity: 1;
        }

        /* Gift Modal Styles */
        .gift-modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 3000;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
            pointer-events: none;
        }

        .gift-modal.active {
            opacity: 1;
            visibility: visible;
            pointer-events: auto;
        }

        .gift-content {
            background-color: white;
            border-radius: 10px;
            padding: 30px;
            width: 90%;
            max-width: 400px;
            text-align: center;
            position: relative;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            transform: scale(0.9);
            transition: transform 0.3s ease;
        }

        .gift-modal.active .gift-content {
            transform: scale(1);
        }

        .gift-close {
            position: absolute;
            top: 15px;
            right: 15px;
            font-size: 1.2rem;
            cursor: pointer;
            color: #777;
            transition: color 0.3s ease;
        }

        .gift-close:hover {
            color: #333;
        }

        .gift-title {
            font-family: 'Great Vibes', cursive;
            font-size: 2.5rem;
            color: var(--primary);
            margin-bottom: 10px;
        }

        .gift-subtitle {
            font-size: 0.9rem;
            color: #777;
            margin-bottom: 25px;
        }

        .gift-options {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 25px;
        }

        .gift-amount {
            padding: 12px 25px;
            border: 2px solid var(--primary);
            border-radius: 30px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            background-color: white;
            color: var(--primary);
        }

        .gift-amount:hover, .gift-amount.selected {
            background-color: var(--primary);
            color: white;
        }

        .custom-amount {
            display: flex;
            margin: 20px 0;
            justify-content: center;
        }

        .custom-amount input {
            padding: 10px 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            width: 100%;
            max-width: 200px;
            text-align: center;
            font-size: 1rem;
        }

        .give-btn {
            background-color: var(--primary);
            color: white;
            padding: 12px 40px;
            border: none;
            border-radius: 30px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-top: 10px;
        }

        .give-btn:hover {
            background-color: var(--primary-dark);
            transform: translateY(-2px);
        }

        /* Header & Navigation */
        header {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 1000;
            padding: 1rem 0;
            transition: var(--transition);
            background-color: transparent;
        }

        header.scrolled {
            background-color: rgba(255, 255, 255, 0.95);
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
            padding: 0.5rem 0;
        }

        .nav-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-family: 'Great Vibes', cursive;
            font-size: 2rem;
            color: var(--primary);
            text-decoration: none;
        }

        .nav-links {
            display: flex;
            list-style: none;
        }

        .nav-links li {
            margin-left: 2rem;
        }

        .nav-links a {
            color: var(--text-dark);
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: var(--transition);
        }

        .nav-links a:hover {
            color: var(--primary);
        }

        .hamburger {
            display: none;
            cursor: pointer;
        }

        /* Hero Section */
        .hero {
            height: 100vh;
            background: linear-gradient(rgba(0, 0, 0, 0.3), rgba(0, 0, 0, 0.5)), 
                        url('{{ $details["hero_image"] ?? "https://images.unsplash.com/photo-1519225421980-715cb0215aed?ixlib=rb-4.0.3" }}') center/cover;
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
            opacity: 0;
            transform: translateY(50px);
            animation: fadeInUp 1s forwards;
        }

        .couple-names {
            font-family: 'Great Vibes', cursive;
            font-size: 5rem;
            margin-bottom: 1rem;
            color: var(--text-light);
        }

        .wedding-date {
            font-size: 1.5rem;
            letter-spacing: 3px;
            margin-bottom: 2rem;
        }

        .scroll-down {
            position: absolute;
            bottom: 30px;
            left: 50%;
            transform: translateX(-50%);
            animation: bounce 2s infinite;
        }

        .scroll-down svg {
            fill: var(--text-light);
            width: 30px;
            height: 30px;
        }

        /* Separator */
        .separator {
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 2rem 0;
        }

        .separator svg {
            fill: var(--primary);
            width: 80px;
            height: 30px;
        }

        .separator-line {
            height: 1px;
            width: 100px;
            background-color: var(--primary);
            margin: 0 15px;
        }

        /* Animations */
        @keyframes fadeInUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeInLeft {
            from {
                opacity: 0;
                transform: translateX(-50px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes fadeInRight {
            from {
                opacity: 0;
                transform: translateX(50px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% {
                transform: translateY(0) translateX(-50%);
            }
            40% {
                transform: translateY(-30px) translateX(-50%);
            }
            60% {
                transform: translateY(-15px) translateX(-50%);
            }
        }

        /* Scroll Animations */
        .fade-in {
            opacity: 0;
            transform: translateY(50px);
            transition: opacity 0.6s ease, transform 0.6s ease;
        }

        .fade-in.active {
            opacity: 1;
            transform: translateY(0);
        }

        .fade-in-left {
            opacity: 0;
            transform: translateX(-50px);
            transition: opacity 0.6s ease, transform 0.6s ease;
        }

        .fade-in-left.active {
            opacity: 1;
            transform: translateX(0);
        }

        .fade-in-right {
            opacity: 0;
            transform: translateX(50px);
            transition: opacity 0.6s ease, transform 0.6s ease;
        }

        .fade-in-right.active {
            opacity: 1;
            transform: translateX(0);
        }

        /* Countdown Timer Section */
        .countdown {
            background: linear-gradient(135deg, rgba(200, 161, 101, 0.1), rgba(248, 243, 233, 0.8));
            padding: 80px 0;
            border-top: 1px solid rgba(200, 161, 101, 0.2);
            border-bottom: 1px solid rgba(200, 161, 101, 0.2);
        }

        .countdown-container {
            max-width: 800px;
            margin: 0 auto;
            text-align: center;
        }

        .countdown-title {
            font-family: 'Playfair Display', serif;
            font-size: 2.5rem;
            color: var(--primary);
            margin-bottom: 1rem;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .countdown-subtitle {
            font-size: 1.1rem;
            color: #666;
            margin-bottom: 3rem;
            font-style: italic;
        }

        .countdown-timer {
            display: flex;
            justify-content: center;
            gap: 2rem;
            flex-wrap: wrap;
            margin-bottom: 2rem;
        }

        .countdown-item {
            background: rgba(255, 255, 255, 0.9);
            border: 2px solid var(--primary);
            border-radius: 15px;
            padding: 2rem 1.5rem;
            min-width: 120px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .countdown-item::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(200, 161, 101, 0.1), transparent);
            transition: left 0.6s ease;
        }

        .countdown-item:hover::before {
            left: 100%;
        }

        .countdown-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
        }

        .countdown-number {
            font-family: 'Playfair Display', serif;
            font-size: 3rem;
            font-weight: 700;
            color: var(--primary);
            display: block;
            line-height: 1;
            margin-bottom: 0.5rem;
            position: relative;
            z-index: 2;
        }

        .countdown-label {
            font-size: 0.9rem;
            color: #777;
            text-transform: uppercase;
            letter-spacing: 2px;
            font-weight: 600;
            position: relative;
            z-index: 2;
        }

        .countdown-message {
            font-size: 1rem;
            color: #555;
            font-style: italic;
            margin-top: 2rem;
            opacity: 0;
            transform: translateY(20px);
            animation: fadeInUp 1s ease 2s forwards;
        }

        .countdown-expired {
            display: none;
            flex-direction: column;
            align-items: center;
            gap: 1rem;
            padding: 2rem;
            background: rgba(255, 255, 255, 0.9);
            border: 2px solid var(--primary);
            border-radius: 15px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }

        .countdown-expired.show {
            display: flex;
        }

        .countdown-expired h3 {
            font-family: 'Great Vibes', cursive;
            font-size: 3rem;
            color: var(--primary);
            margin: 0;
        }

        .countdown-expired p {
            font-size: 1.1rem;
            color: #666;
            margin: 0;
        }

        /* Parallax Background Sections Wrapper */
        .background-sections-wrapper {
            position: relative;
            overflow: hidden;
        }

        .parallax-bg {
            position: absolute;
            top: -20%;
            left: 0;
            width: 100%;
            height: 120%;
            background: linear-gradient(rgba(248, 243, 233, 0.1), rgba(248, 243, 233, 0.15)), 
                        url('{{ $details["background_image"] ?? "/asset/background/background.png" }}') center/cover;
            background-attachment: fixed;
            will-change: transform;
            z-index: 0;
        }

        .background-sections-wrapper::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(248, 243, 233, 0.05);
            z-index: 1;
            pointer-events: none;
        }

        .background-sections-wrapper > section {
            position: relative;
            z-index: 2;
        }

        /* Parallax Elements */
        .parallax-element {
            position: absolute;
            will-change: transform;
            pointer-events: none;
        }

        .parallax-slow {
            transform: translate3d(0, 0, 0);
        }

        .parallax-medium {
            transform: translate3d(0, 0, 0);
        }

        .parallax-fast {
            transform: translate3d(0, 0, 0);
        }

        /* Floating Petals Animation */
        .floating-petals {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 50;
            overflow: hidden;
        }

        .petal {
            position: absolute;
            background: rgba(200, 161, 101, 0.6);
            border-radius: 50% 0 50% 0;
            transform-origin: center;
            animation: float-down linear infinite;
            opacity: 0;
        }

        .petal:nth-child(odd) {
            background: rgba(248, 243, 233, 0.8);
            border-radius: 0 50% 0 50%;
        }

        .petal:nth-child(3n) {
            background: rgba(255, 255, 255, 0.7);
            border-radius: 50%;
        }

        @keyframes float-down {
            0% {
                transform: translateY(-100px) translateX(0px) rotate(0deg);
                opacity: 0;
            }
            10% {
                opacity: 1;
            }
            90% {
                opacity: 1;
            }
            100% {
                transform: translateY(100vh) translateX(50px) rotate(360deg);
                opacity: 0;
            }
        }

        /* Sparkles Animation */
        .floating-sparkles {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 60;
            overflow: hidden;
        }

        .sparkle {
            position: absolute;
            background: var(--primary);
            clip-path: polygon(50% 0%, 61% 35%, 98% 35%, 68% 57%, 79% 91%, 50% 70%, 21% 91%, 32% 57%, 2% 35%, 39% 35%);
            animation: sparkle-float linear infinite;
            opacity: 0;
        }

        @keyframes sparkle-float {
            0% {
                transform: translateY(-50px) scale(0) rotate(0deg);
                opacity: 0;
            }
            20% {
                opacity: 1;
                transform: translateY(20vh) scale(1) rotate(90deg);
            }
            80% {
                opacity: 1;
                transform: translateY(80vh) scale(0.8) rotate(270deg);
            }
            100% {
                transform: translateY(100vh) scale(0) rotate(360deg);
                opacity: 0;
            }
        }

        /* Smooth Section Transitions */
        section {
            padding: 100px 0;
            position: relative;
            overflow: hidden;
            transition: all 0.6s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Section Reveal Animation */
        .section-reveal {
            opacity: 0;
            transform: translateY(50px);
            transition: all 0.8s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .section-reveal.revealed {
            opacity: 1;
            transform: translateY(0);
        }

        /* Enhanced Parallax Decorative Elements */
        .parallax-decoration {
            position: absolute;
            opacity: 0.3;
            pointer-events: none;
        }

        .decoration-heart {
            color: var(--primary);
            font-size: 2rem;
            animation: gentle-float 6s ease-in-out infinite;
        }

        .decoration-flower {
            color: rgba(200, 161, 101, 0.4);
            font-size: 1.5rem;
            animation: gentle-float 8s ease-in-out infinite reverse;
        }

        .decoration-leaf {
            color: rgba(108, 142, 108, 0.3);
            font-size: 1.2rem;
            animation: gentle-float 10s ease-in-out infinite;
        }

        

        /* Hero Section Parallax Enhancement */
        .hero {
            position: relative;
            overflow: hidden;
        }

        .hero-parallax-bg {
            position: absolute;
            top: -20%;
            left: 0;
            width: 100%;
            height: 120%;
            background: linear-gradient(rgba(0, 0, 0, 0.3), rgba(0, 0, 0, 0.5)), 
                        url('{{ $details["hero_image"] ?? "https://images.unsplash.com/photo-1519225421980-715cb0215aed?ixlib=rb-4.0.3" }}') center/cover;
            will-change: transform;
            z-index: -1;
        }

        /* Smooth Scroll Enhancement */
        html {
            scroll-behavior: smooth;
            scroll-padding-top: 80px;
        }

        @media (prefers-reduced-motion: reduce) {
            .floating-petals,
            .floating-sparkles,
            .parallax-element {
                display: none;
            }
            
            * {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
            }
        }

        /* Couple Section */
        .couple {
            background-color: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(0px);
            border-top: 1px solid rgba(200, 161, 101, 0.2);
            border-bottom: 1px solid rgba(200, 161, 101, 0.2);
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
            background-color: rgba(255, 255, 255, 0.9);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .photo-frame img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 50%;
            transition: var(--transition);
        }

        .photo-placeholder {
            font-size: 4rem;
            color: #ccc;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100%;
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
            background-color: rgba(248, 243, 233, 0.1);
            backdrop-filter: blur(0px);
            border-bottom: 1px solid rgba(200, 161, 101, 0.2);
        }

        /* Malaysian Wedding Card Styles */
        .malaysian-wedding-card {
            max-width: 700px;
            margin: 0 auto;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            padding: 3rem;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.1);
            position: relative;
            overflow: hidden;
            backdrop-filter: blur(10px);
            border: 2px solid var(--primary);
        }

        .card-border {
            position: relative;
            z-index: 2;
            text-align: center;
        }

        .card-section {
            margin-bottom: 3rem;
            padding: 1.5rem 0;
            position: relative;
            opacity: 0;
            transform: translateY(30px);
            animation: cardSectionFadeIn 0.8s ease forwards;
        }

        .card-section:nth-child(1) { animation-delay: 0.2s; }
        .card-section:nth-child(2) { animation-delay: 0.4s; }
        .card-section:nth-child(3) { animation-delay: 0.6s; }
        .card-section:nth-child(4) { animation-delay: 0.8s; }

        @keyframes cardSectionFadeIn {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .card-section:last-child {
            margin-bottom: 0;
        }

        .card-section-title {
            font-family: 'Playfair Display', serif;
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 1.5rem;
            letter-spacing: 3px;
            text-transform: uppercase;
            position: relative;
            text-align: center;
        }

        .card-section-title::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 3px;
            background: linear-gradient(90deg, transparent, var(--primary), transparent);
        }

        .card-content {
            color: #333;
            line-height: 1.8;
            text-align: center;
        }

        .main-date {
            font-size: 1.4rem;
            font-weight: 700;
            margin-bottom: 0.8rem;
            color: var(--text-dark);
            font-family: 'Playfair Display', serif;
        }

        .hijri-date {
            font-size: 1rem;
            color: #666;
            font-style: italic;
            margin-bottom: 1rem;
        }

        .program-schedule {
            max-width: 500px;
            margin: 0 auto;
        }

        .program-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.2rem;
            padding: 1rem 1.5rem;
            background: rgba(248, 243, 233, 0.5);
            border-radius: 10px;
            border-left: 4px solid var(--primary);
            transition: all 0.3s ease;
        }

        .program-item:hover {
            transform: translateX(5px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .program-item:last-child {
            margin-bottom: 0;
        }

        .program-label {
            font-weight: 600;
            color: #333;
            flex: 1;
            text-align: left;
            font-size: 1rem;
        }

        .program-time {
            font-weight: 700;
            color: var(--primary);
            text-align: right;
            font-family: 'Montserrat', sans-serif;
            font-size: 0.95rem;
        }

        .location-section {
            background: linear-gradient(135deg, rgba(248, 243, 233, 0.8), rgba(255, 255, 255, 0.9));
            border-radius: 15px;
            padding: 2rem;
            margin: 2rem 0;
            border: 1px solid rgba(200, 161, 101, 0.3);
        }

        .location-content {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 3rem;
            max-width: 600px;
            margin: 0 auto;
        }

        .location-details {
            flex: 1;
            text-align: center;
        }

        .venue-name {
            font-size: 1.6rem;
            font-weight: 800;
            color: var(--primary);
            margin-bottom: 0.5rem;
            font-family: 'Playfair Display', serif;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .venue-subtitle {
            font-size: 1.1rem;
            color: #666;
            margin-bottom: 1rem;
            font-style: italic;
            font-weight: 500;
        }

        .venue-address {
            font-size: 0.95rem;
            color: #555;
            line-height: 1.6;
            font-weight: 400;
        }

        .qr-code-container {
            flex-shrink: 0;
            display: flex;
            justify-content: center;
        }

        .qr-code {
            width: 120px;
            height: 120px;
            border: 3px solid var(--primary);
            border-radius: 12px;
            background: white;
            padding: 8px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
            transition: all 0.3s ease;
        }

        .qr-code:hover {
            transform: scale(1.05);
            box-shadow: 0 12px 35px rgba(0, 0, 0, 0.2);
        }

        .qr-placeholder {
            width: 120px;
            height: 120px;
            border: 3px dashed var(--primary);
            border-radius: 12px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background: rgba(255, 255, 255, 0.9);
            color: var(--primary);
            transition: all 0.3s ease;
        }

        .qr-placeholder:hover {
            background: rgba(248, 243, 233, 0.8);
            transform: scale(1.02);
        }

        .qr-placeholder i {
            font-size: 2.5rem;
            margin-bottom: 0.5rem;
        }

        .qr-placeholder p {
            font-size: 0.9rem;
            margin: 0;
            font-weight: 600;
        }

        .contact-content {
            display: flex;
            flex-direction: column;
            gap: 1.2rem;
            max-width: 500px;
            margin: 0 auto;
        }

        .contact-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1.2rem 1.5rem;
            background: rgba(248, 243, 233, 0.6);
            border-radius: 12px;
            border-left: 5px solid var(--primary);
            transition: all 0.3s ease;
            position: relative;
        }

        .contact-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            background: rgba(248, 243, 233, 0.8);
        }

        .contact-name {
            font-weight: 600;
            color: #333;
            flex: 1;
            text-align: left;
            font-size: 1rem;
        }

        .contact-number {
            font-weight: 700;
            color: var(--primary);
            font-family: 'Courier New', monospace;
            font-size: 1rem;
            letter-spacing: 0.5px;
        }

        /* Doa Section */
        .doa-selamat {
            text-align: center;
            padding: 2.5rem;
            max-width: 800px;
            margin: 0 auto;
            border: 2px solid var(--primary);
            border-radius: 12px;
            background-color: rgba(255, 255, 255, 0.95);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
        }

        .arabic-text {
            font-family: 'Scheherazade New', serif;
            font-size: 1.8rem;
            line-height: 2.5;
            direction: rtl;
            margin-bottom: 1rem;
            color: var(--primary);
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.1);
        }

        .translation {
            font-style: italic;
            color: #666;
        }

        /* Gallery Section */
        .gallery {
            backdrop-filter: none;
            border-bottom: 1px solid rgba(200, 161, 101, 0.2);
        }

        /* Instagram Stories Section */
        .stories-section {
            margin-bottom: 4rem;
            text-align: center;
        }

        .stories-container {
            display: flex;
            justify-content: center;
            gap: 1rem;
            flex-wrap: wrap;
            margin-bottom: 2rem;
        }

        .story-item {
            position: relative;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .story-item:hover {
            transform: scale(1.05);
        }

        .story-circle {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: linear-gradient(45deg, var(--primary), #f39c12, var(--primary));
            padding: 3px;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }

        .story-image {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid white;
        }

        .story-label {
            position: absolute;
            bottom: -25px;
            left: 50%;
            transform: translateX(-50%);
            font-size: 0.8rem;
            font-weight: 500;
            color: #333;
            white-space: nowrap;
        }

        .story-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.9);
            z-index: 4000;
            display: none;
            align-items: center;
            justify-content: center;
        }

        .story-content {
            position: relative;
            max-width: 400px;
            max-height: 80vh;
            background: white;
            border-radius: 15px;
            overflow: hidden;
        }

        .story-content img {
            width: 100%;
            height: auto;
            display: block;
        }

        .story-progress {
            position: absolute;
            top: 10px;
            left: 15px;
            right: 15px;
            height: 3px;
            background: rgba(255, 255, 255, 0.3);
            border-radius: 2px;
            overflow: hidden;
        }

        .story-progress-bar {
            height: 100%;
            background: white;
            border-radius: 2px;
            transition: width 3s linear;
            width: 0%;
        }

        .story-close {
            position: absolute;
            top: 20px;
            right: 20px;
            color: white;
            font-size: 1.5rem;
            cursor: pointer;
            z-index: 10;
        }

        /* Before/After Slider */
        .before-after-section {
            margin-bottom: 4rem;
        }

        .comparison-slider {
            position: relative;
            max-width: 600px;
            margin: 0 auto;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
        }

        .comparison-container {
            position: relative;
            overflow: hidden;
        }

        .comparison-before,
        .comparison-after {
            display: block;
            width: 100%;
            height: auto;
        }

        .comparison-after {
            position: absolute;
            top: 0;
            left: 0;
            clip-path: inset(0 50% 0 0);
            transition: clip-path 0.3s ease;
        }

        .comparison-slider-handle {
            position: absolute;
            top: 0;
            left: 50%;
            width: 4px;
            height: 100%;
            background: white;
            cursor: col-resize;
            transform: translateX(-50%);
            z-index: 10;
        }

        .comparison-slider-handle::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 40px;
            height: 40px;
            background: var(--primary);
            border: 3px solid white;
            border-radius: 50%;
            transform: translate(-50%, -50%);
            cursor: col-resize;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        .comparison-labels {
            position: absolute;
            top: 20px;
            left: 0;
            right: 0;
            display: flex;
            justify-content: space-between;
            padding: 0 20px;
            z-index: 5;
        }

        .comparison-label {
            background: rgba(0, 0, 0, 0.7);
            color: white;
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 500;
        }

        /* Photo Gallery */
        .photo-gallery-section {
            margin-bottom: 2rem;
        }

        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            grid-gap: 1.5rem;
        }

        .gallery-item {
            position: relative;
            overflow: hidden;
            border-radius: 8px;
            height: 250px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
            transition: var(--transition);
            cursor: pointer;
        }

        .gallery-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
        }

        .gallery-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: var(--transition);
        }

        .gallery-item:hover img {
            transform: scale(1.1);
        }

        .gallery-item-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(to top, rgba(0,0,0,0.7) 0%, transparent 50%);
            opacity: 0;
            transition: all 0.3s ease;
            display: flex;
            align-items: flex-end;
            padding: 20px;
        }

        .gallery-item:hover .gallery-item-overlay {
            opacity: 1;
        }

        .gallery-item-info {
            color: white;
        }

        .gallery-item-title {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .gallery-item-category {
            font-size: 0.9rem;
            opacity: 0.8;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* Lightbox Modal */
        .lightbox-modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.95);
            z-index: 5000;
            display: none;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .lightbox-modal.active {
            display: flex;
            opacity: 1;
        }

        .lightbox-content {
            position: relative;
            max-width: 90vw;
            max-height: 90vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .lightbox-image {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
            border-radius: 8px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
        }

        .lightbox-nav {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(255, 255, 255, 0.2);
            border: none;
            color: white;
            font-size: 2rem;
            padding: 15px 20px;
            cursor: pointer;
            border-radius: 50%;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
        }

        .lightbox-nav:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-50%) scale(1.1);
        }

        .lightbox-prev {
            left: 20px;
        }

        .lightbox-next {
            right: 20px;
        }

        .lightbox-close {
            position: absolute;
            top: 20px;
            right: 20px;
            background: rgba(255, 255, 255, 0.2);
            border: none;
            color: white;
            font-size: 1.5rem;
            padding: 10px 15px;
            cursor: pointer;
            border-radius: 50%;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
        }

        .lightbox-close:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: scale(1.1);
        }

        .lightbox-counter {
            position: absolute;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            color: white;
            background: rgba(0, 0, 0, 0.5);
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 0.9rem;
        }

        /* RSVP Section */
        .rsvp {
            background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)), 
                        url('{{ $details["rsvp_bg"] ?? "https://images.unsplash.com/photo-1519225421980-715cb0215aed?ixlib=rb-4.0.3" }}') center/cover;
            background-attachment: fixed;
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

        .btn-primary:hover {
            background-color: var(--primary-dark);
        }

        /* RSVP Form Enhancements */
        .form-control.is-invalid {
            border-color: #dc3545;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='none' stroke='%23dc3545' viewBox='0 0 12 12'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath d='M5.8 3.6h.4L6 6.5z'/%3e%3ccircle cx='6' cy='8.2' r='.6' fill='%23dc3545' stroke='none'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right calc(0.375em + 0.1875rem) center;
            background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
        }

        .invalid-feedback {
            display: none;
            width: 100%;
            margin-top: 0.25rem;
            font-size: 0.875em;
            color: #dc3545;
        }

        .alert {
            border-radius: 8px;
            border: none;
            padding: 1rem 1.5rem;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border-left: 4px solid #28a745;
        }

        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border-left: 4px solid #dc3545;
        }

        .alert h4 {
            margin-bottom: 0.5rem;
            font-size: 1.1rem;
        }

        .alert i {
            margin-right: 0.5rem;
        }

        /* Footer */
        footer {
            background-color: #222;
            color: var(--text-light);
            padding: 3rem 0;
            text-align: center;
        }

        .footer-content {
            max-width: 600px;
            margin: 0 auto;
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

        .social-links a {
            color: var(--text-light);
            font-size: 1.5rem;
            transition: var(--transition);
        }

        .social-links a:hover {
            color: var(--primary);
        }

        .copyright {
            font-size: 0.9rem;
            opacity: 0.7;
        }

        /* Responsive Design */
        @media (max-width: 992px) {
            .couple-names {
                font-size: 4rem;
            }
        }

        @media (max-width: 768px) {
            .section-title {
                font-size: 2rem;
            }
            
            .couple-names {
                font-size: 3rem;
            }

            .countdown-title {
                font-size: 2rem;
            }

            .countdown-timer {
                gap: 1rem;
            }

            .countdown-item {
                min-width: 100px;
                padding: 1.5rem 1rem;
            }

            .countdown-number {
                font-size: 2.5rem;
            }
            
            .nav-links {
                display: none;
                position: absolute;
                top: 100%;
                left: 0;
                width: 100%;
                background-color: #fff;
                flex-direction: column;
                padding: 1rem 0;
                box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
            }
            
            .nav-links.active {
                display: flex;
            }
            
            .nav-links li {
                margin: 0.5rem 0;
                text-align: center;
            }
            
            .hamburger {
                display: block;
            }
            
            .couple-photo {
                width: 90%;
                margin: 0 auto 2rem;
            }

            .floating-menu {
                right: 10px;
                padding: 10px 8px;
            }
            
            .menu-item {
                width: 35px;
                height: 35px;
            }
            
            .menu-item i {
                font-size: 1rem;
            }

            .video-title-overlay {
                font-size: 3rem;
            }

            .video-subtitle-overlay {
                font-size: 1rem;
            }

            .fallback-play-button {
                width: 80px;
                height: 80px;
            }

            .fallback-play-button svg {
                width: 30px;
                height: 30px;
            }

            /* Enhanced Envelope Mobile Styles */
            .envelope {
                width: 90vw;
                max-width: 380px;
                height: 270px;
            }

            .envelope-seal {
                width: 60px;
                height: 60px;
                top: 40px;
            }

            .envelope-seal svg {
                width: 30px;
                height: 30px;
            }

            .letter-content {
                top: 75px;
                left: 25px;
                right: 25px;
                bottom: 30px;
                padding: 20px 15px;
            }

            .envelope.opening .letter-content {
                transform: translateZ(15px) translateY(-100px) rotateX(-6deg) scale(1.08) scaleY(0.4);
            }

            .envelope.letter-unfolding .letter-content {
                width: 85vw;
                max-width: 320px;
                height: 420px;
                top: 50%;
                left: 50%;
                right: auto;
                bottom: auto;
                padding: 15px 12px;
                transform: translateX(-50%) translateY(-40%) translateZ(10px) rotateX(-2deg) scale(1.2);
            }

            /* Fixed floating state for tablet */
            .envelope.letter-unfolding .letter-content.letter-floating {
                animation: gentle-float 3s ease-in-out infinite;
                transition: none !important;
            }


            /* @keyframes gentle-float {
                0%, 100% { 
                    transform: translateX(-50%) translateY(-40%) translateZ(18px) rotateX(0deg) scale(1.25);
                }
                50% { 
                    transform: translateX(-50%) translateY(-42%) translateZ(20px) rotateX(1deg) scale(1.27);
                }
            } */

            .invitation-title {
                font-size: 2.8rem;
            }

            .invitation-couple-names {
                font-size: 1.9rem;
                margin: 0.6rem 0;
            }

            .invitation-instruction {
                font-size: 0.85rem;
                padding: 8px 16px;
            }

            .instruction-wrapper::before,
            .instruction-wrapper::after {
                font-size: 0.8rem;
                left: -15px;
                right: -15px;
            }

            /* Malaysian Wedding Card Mobile Styles */
            .malaysian-wedding-card {
                padding: 2rem 1.5rem;
                margin: 0 1rem;
            }

            .card-section-title {
                font-size: 1.4rem;
                letter-spacing: 2px;
            }

            .location-content {
                flex-direction: column;
                gap: 2rem;
            }

            .location-details {
                text-align: center;
            }

            .program-item {
                flex-direction: column;
                align-items: center;
                gap: 0.5rem;
                text-align: center;
            }

            .program-label,
            .program-time {
                text-align: center;
                width: 100%;
            }

            .contact-item {
                flex-direction: column;
                gap: 0.5rem;
                text-align: center;
            }

            .qr-code,
            .qr-placeholder {
                width: 100px;
                height: 100px;
            }

            .qr-placeholder i {
                font-size: 2rem;
            }

            .venue-name {
                font-size: 1.3rem;
            }

            .main-date {
                font-size: 1.2rem;
            }
        }

        @media (max-width: 576px) {
            .couple-names {
                font-size: 2.5rem;
            }
            
            .wedding-date {
                font-size: 1.2rem;
            }

            .countdown-title {
                font-size: 1.8rem;
            }

            .countdown-timer {
                gap: 0.8rem;
            }

            .countdown-item {
                min-width: 80px;
                padding: 1.2rem 0.8rem;
            }

            .countdown-number {
                font-size: 2rem;
            }

            .countdown-label {
                font-size: 0.8rem;
            }
            
            .photo-frame {
                width: 220px;
                height: 220px;
            }
            
            .separator-line {
                width: 60px;
            }

            .video-title-overlay {
                font-size: 2.5rem;
            }

            .video-subtitle-overlay {
                font-size: 0.9rem;
            }

            /* Enhanced Envelope Small Mobile Styles */
            .envelope {
                width: 95vw;
                max-width: 320px;
                height: 230px;
            }

            .envelope-seal {
                width: 50px;
                height: 50px;
                top: 35px;
            }

            .envelope-seal svg {
                width: 26px;
                height: 26px;
            }

            .letter-content {
                top: 65px;
                left: 20px;
                right: 20px;
                bottom: 25px;
                padding: 18px 12px;
            }

            .envelope.letter-sliding-out .letter-content {
                transform: translateZ(6px) translateY(-15px) rotateX(-2deg) scale(1.01);
            }

            .envelope.letter-emerging .letter-content {
                transform: translateZ(12px) translateY(-40px) rotateX(-5deg) scale(1.08);
            }

            .envelope.letter-unfolding .letter-content {
                width: 90vw;
                max-width: 280px;
                height: 420px;
                top: 50%;
                left: 50%;
                right: auto;
                bottom: auto;
                padding: 15px 12px;
                transform: translateX(-50%) translateY(-40%) translateZ(10px) rotateX(-2deg) scale(1.2);
            }

            /* Fixed floating state for small mobile */
            .envelope.letter-unfolding .letter-content.letter-floating {
                animation: gentle-float 3s ease-in-out infinite;
                transition: none !important;
            }


            /* @keyframes gentle-float {
                0%, 100% { 
                    transform: translateX(-50%) translateY(-40%) translateZ(15px) rotateX(0deg) scale(1.2);
                }
                50% { 
                    transform: translateX(-50%) translateY(-42%) translateZ(17px) rotateX(1deg) scale(1.22);
                }
            } */

            .invitation-title {
                font-size: 2.4rem;
                margin-bottom: 0.3rem;
            }

            .invitation-couple-names {
                font-size: 1.6rem;
                margin: 0.5rem 0;
            }

            .invitation-instruction {
                font-size: 0.8rem;
                padding: 7px 14px;
            }

            .wedding-date-small {
                font-size: 0.8rem;
                margin-top: 0.6rem;
            }

            .instruction-wrapper::before,
            .instruction-wrapper::after {
                display: none; /* Hide decorative elements on very small screens */
            }

            .malaysian-wedding-card {
                padding: 1.5rem 1rem;
            }

            .card-section {
                margin-bottom: 2rem;
            }

            .card-section-title {
                font-size: 1.2rem;
                letter-spacing: 1px;
            }

            .venue-name {
                font-size: 1.1rem;
            }

            .venue-subtitle {
                font-size: 1rem;
            }

            .venue-address {
                font-size: 0.9rem;
            }

            .program-item {
                padding: 0.8rem 1rem;
            }

            .contact-item {
                padding: 1rem;
            }

            .qr-code,
            .qr-placeholder {
                width: 80px;
                height: 80px;
            }

            /* Gallery Mobile Styles */
            .stories-container {
                gap: 0.8rem;
            }

            .story-circle {
                width: 60px;
                height: 60px;
            }

            .story-label {
                font-size: 0.7rem;
                bottom: -20px;
            }

            .story-content {
                max-width: 90vw;
                max-height: 70vh;
            }

            .before-after-section {
                margin-bottom: 3rem;
            }

            .comparison-slider {
                margin: 0 1rem;
                border-radius: 10px;
            }

            .comparison-labels {
                padding: 0 15px;
                top: 15px;
            }

            .comparison-label {
                padding: 6px 12px;
                font-size: 0.8rem;
            }

            .comparison-slider-handle::before {
                width: 30px;
                height: 30px;
            }

            .gallery-grid {
                grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
                grid-gap: 1rem;
            }

            .gallery-item {
                height: 150px;
            }

            .gallery-item-overlay {
                padding: 15px;
            }

            .gallery-item-title {
                font-size: 1rem;
            }

            .gallery-item-category {
                font-size: 0.8rem;
            }

            .lightbox-nav {
                font-size: 1.5rem;
                padding: 10px 15px;
            }

            .lightbox-prev {
                left: 10px;
            }

            .lightbox-next {
                right: 10px;
            }

            .lightbox-close {
                top: 10px;
                right: 10px;
                padding: 8px 12px;
                font-size: 1.3rem;
            }

            .lightbox-counter {
                bottom: 15px;
                font-size: 0.8rem;
                padding: 6px 12px;
            }

            .story-close {
                top: 15px;
                right: 15px;
                font-size: 1.3rem;
            }
        }
    </style>
</head>
<body class="invitation-active">
    <!-- Floating Petals Animation -->
    <div class="floating-petals" id="floatingPetals">
        <!-- Petals will be generated by JavaScript -->
    </div>

    <!-- Floating Sparkles Animation -->
    <div class="floating-sparkles" id="floatingSparkles">
        <!-- Sparkles will be generated by JavaScript -->
    </div>

    <!-- Floating Menu -->
    <div class="floating-menu">
        <div class="menu-item" onclick="scrollToSection('hero')">
            <i class="fas fa-home"></i>
            <span class="menu-tooltip">Home</span>
        </div>
        <div class="menu-item" onclick="scrollToSection('countdown')">
            <i class="fas fa-clock"></i>
            <span class="menu-tooltip">Countdown</span>
        </div>
        <div class="menu-item" onclick="scrollToSection('couple')">
            <i class="fas fa-heart"></i>
            <span class="menu-tooltip">Couple</span>
        </div>
        <div class="menu-item" onclick="scrollToSection('events')">
            <i class="fas fa-calendar-alt"></i>
            <span class="menu-tooltip">Events</span>
        </div>
        <div class="menu-item" onclick="scrollToSection('gallery')">
            <i class="fas fa-images"></i>
            <span class="menu-tooltip">Gallery</span>
        </div>
        <div class="menu-item" onclick="scrollToSection('rsvp')">
            <i class="fas fa-envelope"></i>
            <span class="menu-tooltip">RSVP</span>
        </div>
        <div class="menu-item" onclick="openGiftModal()">
            <i class="fas fa-gift"></i>
            <span class="menu-tooltip">Send Gift</span>
        </div>
    </div>
    
    <!-- Invitation Overlay -->
    <div class="invitation-overlay">
        <div class="envelope-container">
            <div class="envelope" id="envelope">
                <!-- Envelope back -->
                <div class="envelope-back"></div>
                
                <!-- Envelope flap that opens -->
                <div class="envelope-flap"></div>
                
                <!-- Wax seal -->
                <div class="envelope-seal">
                    <svg viewBox="0 0 24 24">
                        <path d="M12,21.35L10.55,20.03C5.4,15.36 2,12.27 2,8.5C2,5.41 4.42,3 7.5,3C9.24,3 10.91,3.81 12,5.08C13.09,3.81 14.76,3 16.5,3C19.58,3 22,5.41 22,8.5C22,12.27 18.6,15.36 13.45,20.03L12,21.35Z"/>
                    </svg>
                </div>
                
                <!-- Three.js Letter Canvas (hidden initially) -->
                <div class="letter-canvas-container" id="letterCanvasContainer">
                    <canvas id="letterCanvas"></canvas>
                </div>
                
                <!-- Initial Letter Content (shows before 3D animation) -->
                <div class="letter-content" id="letterContent">
                    <h2 class="invitation-title" style="font-size: 2.2rem; margin-bottom: 0.3rem;">You're Invited</h2>
                    <div class="invitation-separator">
                        <div class="invitation-separator-line"></div>
                        <svg viewBox="0 0 24 24" width="24" height="20">
                            <path d="M12,21.35L10.55,20.03C5.4,15.36 2,12.27 2,8.5C2,5.41 4.42,3 7.5,3C9.24,3 10.91,3.81 12,5.08C13.09,3.81 14.76,3 16.5,3C19.58,3 22,5.41 22,8.5C22,12.27 18.6,15.36 13.45,20.03L12,21.35Z"/>
                        </svg>
                        <div class="invitation-separator-line"></div>
                    </div>
                    
                    <p style="font-size: 0.7rem; color: #666; margin: 0.2rem 0;">To celebrate the wedding of</p>
                    <h1 class="invitation-couple-names" style="font-size: 1.4rem; margin: 0.3rem 0; line-height: 1.1;">{{ $details["groom_name"] ?? "Ahmad Bin Abdullah" }} & {{ $details["bride_name"] ?? "Siti Fatimah" }}</h1>
                    
                    <div style="margin: 0.5rem 0; padding: 0.5rem; background: rgba(248, 243, 233, 0.3); border-radius: 4px; border-left: 2px solid var(--primary);">
                        <p style="font-size: 0.65rem; color: #555; margin: 0.15rem 0; line-height: 1.2;"><strong>Date:</strong> {{ $details["wedding_date"] ?? "7 December 2025, Sunday" }}</p>
                        <p style="font-size: 0.65rem; color: #555; margin: 0.15rem 0; line-height: 1.2;"><strong>Time:</strong> {{ $details["wedding_time"] ?? "6:00 PM - 10:00 PM" }}</p>
                        <p style="font-size: 0.65rem; color: #555; margin: 0.15rem 0; line-height: 1.2;"><strong>Venue:</strong> {{ $details["venue_name"] ?? "The Glass Tree by Zuljanah Palace" }}</p>
                        <p style="font-size: 0.6rem; color: #777; margin: 0.15rem 0; line-height: 1.2;">{{ $details["venue_address"] ?? "S03, B5, Kampung Seri Kembangan, 43300 Klang, Selangor" }}</p>
                    </div>
                    
                    <div style="text-align: center; margin: 0.5rem 0;">
                        <p style="font-size: 0.6rem; color: #888; font-style: italic; margin: 0.2rem 0;">Your presence would be the greatest gift of all</p>
                    </div>
                    
                    <div style="margin-top: 0.5rem; font-size: 0.55rem; color: #aaa; text-align: center;">
                        <p style="margin: 0;">With love,</p>
                        <p style="margin: 0;">{{ $details["groom_parents"] ?? "The Families" }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Fullscreen Video Section -->
    <div class="video-section" id="videoSection">
        <video 
            id="weddingVideo" 
            class="fullscreen-video"
            autoplay 
            muted 
            preload="auto"
            poster="{{ $details['video_poster'] ?? 'https://images.unsplash.com/photo-1519225421980-715cb0215aed?ixlib=rb-4.0.3&w=1920&h=1080&fit=crop' }}"
        >
            <source src="{{ $details['wedding_invitation_video'] ?? 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/BigBuckBunny.mp4' }}" type="video/mp4">
            Your browser does not support the video tag.
        </video>
        
        <!-- Fallback Play Button (shown if autoplay fails) -->
        <div class="fallback-play-button" id="fallbackPlayBtn" onclick="playVideoFallback()">
            <svg viewBox="0 0 24 24">
                <path d="M8,5.14V19.14L19,12.14L8,5.14Z"/>
            </svg>
        </div>
        
        <!-- Video Overlay Content -->
        <div class="video-overlay-content">
            <h1 class="video-title-overlay">{{ $details["groom_name"] ?? "Groom" }} & {{ $details["bride_name"] ?? "Bride" }}</h1>
            <p class="video-subtitle-overlay">{{ $details["video_subtitle"] ?? "A Special Message for You" }}</p>
        </div>
        
        <!-- Video Controls -->
        <div class="video-controls-overlay">
            <button class="skip-video-btn" onclick="skipToMainContent()">Skip Video</button>
            <div class="scroll-indicator" onclick="skipToMainContent()">
                <i class="fas fa-chevron-down"></i>
                <span>Scroll to Continue</span>
            </div>
        </div>
    </div>

    <!-- Gift Modal -->
    <div class="gift-modal" id="giftModal">
        <div class="gift-content">
            <div class="gift-close" onclick="closeGiftModal()">
                <i class="fas fa-times"></i>
            </div>
            <h2 class="gift-title">Wedding Gift</h2>
            <p class="gift-subtitle">Your presence is our present, but if you wish to gift us something, we would be truly grateful.</p>
            <div class="gift-options">
                <div class="gift-amount" onclick="selectAmount(30, this)">RM30</div>
                <div class="gift-amount" onclick="selectAmount(50, this)">RM50</div>
                <div class="gift-amount" onclick="selectAmount(100, this)">RM100</div>
            </div>
            <div class="custom-amount">
                <input type="number" id="customAmount" placeholder="Enter custom amount" min="1" onchange="customAmountChange()">
            </div>
            <button class="give-btn" onclick="sendGift()">Send Gift</button>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content" id="mainContent">
        <!-- Header & Navigation -->
        <header>
            <div class="container nav-container">
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
                    <div class="hamburger">
                        <i class="fas fa-bars"></i>
                    </div>
                </nav>
            </div>
        </header>

        <!-- Hero Section -->
        <section id="hero" class="hero section-reveal">
            <div class="hero-parallax-bg parallax-element parallax-slow" data-speed="0.5"></div>
            
            <!-- Decorative Elements -->
            <div class="parallax-decoration decoration-heart parallax-element parallax-medium" 
                 style="top: 20%; left: 10%;" data-speed="0.3">
                <i class="fas fa-heart"></i>
            </div>
            <div class="parallax-decoration decoration-flower parallax-element parallax-fast" 
                 style="top: 60%; right: 15%;" data-speed="0.7">
                <i class="fas fa-leaf"></i>
            </div>
            <div class="parallax-decoration decoration-heart parallax-element parallax-medium" 
                 style="top: 80%; left: 20%;" data-speed="0.4">
                <i class="fas fa-heart"></i>
            </div>
            
            <div class="hero-content">
                <h2 class="script-font">We're Getting Married</h2>
                <h1 class="couple-names">{{ $details["groom_name"] ?? "Groom" }} & {{ $details["bride_name"] ?? "Bride" }}</h1>
                <p class="wedding-date">{{ strtoupper($details["wedding_date"] ?? "WEDDING DATE") }}</p>
                <div class="separator">
                    <div class="separator-line"></div>
                    <svg viewBox="0 0 24 24">
                        <path d="M12,21.35L10.55,20.03C5.4,15.36 2,12.27 2,8.5C2,5.41 4.42,3 7.5,3C9.24,3 10.91,3.81 12,5.08C13.09,3.81 14.76,3 16.5,3C19.58,3 22,5.41 22,8.5C22,12.27 18.6,15.36 13.45,20.03L12,21.35Z"/>
                    </svg>
                    <div class="separator-line"></div>
                </div>
                <p>Kindly Join Us On Our Special Day</p>
            </div>
            <div class="scroll-down">
                <a href="#couple">
                    <svg viewBox="0 0 24 24">
                        <path d="M7.41,8.58L12,13.17L16.59,8.58L18,10L12,16L6,10L7.41,8.58Z"/>
                    </svg>
                </a>
            </div>
        </section>

        <!-- Countdown Timer Section -->
        <section id="countdown" class="countdown section-reveal">
            <div class="container">
                <div class="countdown-container fade-in">
                    <h2 class="countdown-title">Count Down to Our Big Day</h2>
                    <p class="countdown-subtitle">Join us in counting the moments until we say "I Do"</p>
                    
                    <div class="countdown-timer" id="countdownTimer">
                        <div class="countdown-item">
                            <span class="countdown-number" id="days">00</span>
                            <span class="countdown-label">Days</span>
                        </div>
                        <div class="countdown-item">
                            <span class="countdown-number" id="hours">00</span>
                            <span class="countdown-label">Hours</span>
                        </div>
                        <div class="countdown-item">
                            <span class="countdown-number" id="minutes">00</span>
                            <span class="countdown-label">Minutes</span>
                        </div>
                        <div class="countdown-item">
                            <span class="countdown-number" id="seconds">00</span>
                            <span class="countdown-label">Seconds</span>
                        </div>
                    </div>

                    <div class="countdown-expired" id="countdownExpired">
                        <h3>We're Married!</h3>
                        <p>Thank you for being part of our special day</p>
                    </div>

                    <p class="countdown-message">Can't wait to celebrate with you!</p>
                </div>
            </div>
        </section>

        <!-- Background Sections Wrapper -->
        <div class="background-sections-wrapper">
            <div class="parallax-bg parallax-element parallax-slow" data-speed="0.3"></div>
            
            <!-- Parallax Decorative Elements -->
            <div class="parallax-decoration decoration-flower parallax-element parallax-medium" 
                 style="top: 10%; left: 5%;" data-speed="0.4">
                <i class="fas fa-seedling"></i>
            </div>
            <div class="parallax-decoration decoration-heart parallax-element parallax-fast" 
                 style="top: 30%; right: 8%;" data-speed="0.6">
                <i class="fas fa-heart"></i>
            </div>
            <div class="parallax-decoration decoration-leaf parallax-element parallax-medium" 
                 style="top: 50%; left: 12%;" data-speed="0.35">
                <i class="fas fa-leaf"></i>
            </div>
            <div class="parallax-decoration decoration-flower parallax-element parallax-fast" 
                 style="top: 70%; right: 10%;" data-speed="0.55">
                <i class="fas fa-flower"></i>
            </div>
            <div class="parallax-decoration decoration-heart parallax-element parallax-medium" 
                 style="top: 85%; left: 8%;" data-speed="0.4">
                <i class="fas fa-heart"></i>
            </div>
            
            <!-- Couple Section -->
            <section id="couple" class="couple section-reveal">
                <div class="container">
                    <h2 class="section-title fade-in">The Couple</h2>
                    <div class="couple-photos">
                        <div class="couple-photo fade-in-left">
                            <div class="photo-frame">
                                @if($details["groom_photo"] ?? false)
                                    <img src="{{ $details['groom_photo'] }}" alt="Groom">
                                @else
                                    <div class="photo-placeholder">
                                        <i class="fas fa-user"></i>
                                    </div>
                                @endif
                            </div>
                            <h3 class="couple-name">{{ $details["groom_full_name"] ?? $details["groom_name"] ?? "Groom Full Name" }}</h3>
                            <div class="separator">
                                <div class="separator-line"></div>
                                <svg viewBox="0 0 24 24">
                                    <path d="M12,21.35L10.55,20.03C5.4,15.36 2,12.27 2,8.5C2,5.41 4.42,3 7.5,3C9.24,3 10.91,3.81 12,5.08C13.09,3.81 14.76,3 16.5,3C19.58,3 22,5.41 22,8.5C22,12.27 18.6,15.36 13.45,20.03L12,21.35Z"/>
                                </svg>
                                <div class="separator-line"></div>
                            </div>
                            <p class="couple-desc">{{ $details["groom_parents"] ?? "Son of Mr. & Mrs. Groom's Parents" }}</p>
                        </div>
                        <div class="couple-photo fade-in-right">
                            <div class="photo-frame">
                                @if($details["bride_photo"] ?? false)
                                    <img src="{{ $details['bride_photo'] }}" alt="Bride">
                                @else
                                    <div class="photo-placeholder">
                                        <i class="fas fa-user"></i>
                                    </div>
                                @endif
                            </div>
                            <h3 class="couple-name">{{ $details["bride_full_name"] ?? $details["bride_name"] ?? "Bride Full Name" }}</h3>
                            <div class="separator">
                                <div class="separator-line"></div>
                                <svg viewBox="0 0 24 24">
                                    <path d="M12,21.35L10.55,20.03C5.4,15.36 2,12.27 2,8.5C2,5.41 4.42,3 7.5,3C9.24,3 10.91,3.81 12,5.08C13.09,3.81 14.76,3 16.5,3C19.58,3 22,5.41 22,8.5C22,12.27 18.6,15.36 13.45,20.03L12,21.35Z"/>
                                </svg>
                                <div class="separator-line"></div>
                            </div>
                            <p class="couple-desc">{{ $details["bride_parents"] ?? "Daughter of Mr. & Mrs. Bride's Parents" }}</p>
                        </div>
                    </div>
                    <div class="doa-selamat fade-in">
                        <div class="arabic-text">
                            بارك الله لكما وبارك عليكما وجمع بينكما في خير
                        </div>
                        <p class="translation">"May Allah bless you both, shower His blessings upon you, and join you in goodness."</p>
                    </div>
                </div>
            </section>

            <!-- Events Section -->
            <section id="events" class="events section-reveal">
                <div class="container">
                    <h2 class="section-title fade-in">Wedding Events</h2>
                    
                    <!-- Malaysian Wedding Card Format -->
                    <div class="malaysian-wedding-card fade-in">
                        <div class="card-border">
                            <!-- Date Section -->
                            <div class="card-section">
                                <h3 class="card-section-title">TARIKH</h3>
                                <div class="card-content">
                                    <p class="main-date">{{ $details["wedding_date"] ?? "7 Disember 2025, Ahad" }}</p>
                                    <p class="hijri-date">{{ $details["hijri_date"] ?? "bermula 6 Jamadilakhir 1447" }}</p>
                                </div>
                            </div>

                            <!-- Program Schedule Section -->
                            <div class="card-section">
                                <h3 class="card-section-title">ATURCARA MAJLIS</h3>
                                <div class="card-content">
                                    <div class="program-schedule">
                                        @if($details["akad_time"] ?? false)
                                        <div class="program-item">
                                            <span class="program-label">{{ $details["akad_title"] ?? "Jamuan makan" }}</span>
                                            <span class="program-time">{{ $details["akad_time"] ?? "10:00am" }}</span>
                                        </div>
                                        @endif
                                        <div class="program-item">
                                            <span class="program-label">{{ $details["reception_meal_label"] ?? "Jamuan makan" }}</span>
                                            <span class="program-time">{{ $details["reception_meal_time"] ?? "6:00pm - 10:00pm" }}</span>
                                        </div>
                                        <div class="program-item">
                                            <span class="program-label">{{ $details["bride_arrival_label"] ?? "Ketibaan pengantin" }}</span>
                                            <span class="program-time">{{ $details["bride_arrival_time"] ?? "8:00pm" }}</span>
                                        </div>
                                        @if($details["additional_program_label"] ?? false)
                                        <div class="program-item">
                                            <span class="program-label">{{ $details["additional_program_label"] ?? "Program tambahan" }}</span>
                                            <span class="program-time">{{ $details["additional_program_time"] ?? "9:00pm" }}</span>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Location Section -->
                            <div class="card-section location-section">
                                <h3 class="card-section-title">LOKASI</h3>
                                <div class="card-content location-content">
                                    <div class="location-details">
                                        <h4 class="venue-name">{{ $details["venue_name"] ?? "THE GLASS TREE" }}</h4>
                                        <p class="venue-subtitle">{{ $details["venue_subtitle"] ?? "by Zuljanah Palace" }}</p>
                                        <p class="venue-address">{{ $details["venue_address"] ?? "S03, B5, Kampung Seri Kembangan, 43300 Klang, Selangor" }}</p>
                                    </div>
                                    <div class="qr-code-container">
                                        @if($details["qr_code_image"] ?? false)
                                            <img src="{{ $details['qr_code_image'] }}" alt="QR Code for Location" class="qr-code">
                                        @else
                                            <div class="qr-placeholder">
                                                <i class="fas fa-qrcode"></i>
                                                <p>QR Code</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Contact Section -->
                            <div class="card-section">
                                <h3 class="card-section-title">HUBUNGI</h3>
                                <div class="card-content contact-content">
                                    @if($details["contact_person_1"] ?? false)
                                    <div class="contact-item">
                                        <span class="contact-name">{{ $details["contact_person_1"] ?? "Rashid (Bapa Pengantin)" }}</span>
                                        <span class="contact-number">{{ $details["contact_number_1"] ?? "016 - 233 6054" }}</span>
                                    </div>
                                    @endif
                                    @if($details["contact_person_2"] ?? false)
                                    <div class="contact-item">
                                        <span class="contact-name">{{ $details["contact_person_2"] ?? "Noriha (Emak Pengantin)" }}</span>
                                        <span class="contact-number">{{ $details["contact_number_2"] ?? "019 - 322 8247" }}</span>
                                    </div>
                                    @endif
                                    @if($details["contact_person_3"] ?? false)
                                    <div class="contact-item">
                                        <span class="contact-name">{{ $details["contact_person_3"] ?? "Asyikin (Adik Pengantin)" }}</span>
                                        <span class="contact-number">{{ $details["contact_number_3"] ?? "012 - 363 6229" }}</span>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($details["venue_map_embed"] ?? false)
                    <!-- Venue Map -->
                    <div class="map-container fade-in">
                        <iframe src="{{ $details['venue_map_embed'] }}" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade" style="width: 100%; height: 400px; border: none;"></iframe>
                        <div class="venue-info">
                            <h3>{{ $details["venue"] ?? "Main Venue" }}</h3>
                            <p>{{ $details["venue_description"] ?? "The wedding reception will be held at this beautiful venue with ample parking and easy access." }}</p>
                            @if($details["venue_map_link"] ?? false)
                            <a href="{{ $details['venue_map_link'] }}" target="_blank" class="venue-directions">
                                <i class="fas fa-directions"></i> Get Directions
                            </a>
                            @endif
                        </div>
                    </div>
                    @endif
                </div>
            </section>

            <!-- Gallery Section -->
            <section id="gallery" class="gallery section-reveal">
                <div class="container">
                    <h2 class="section-title fade-in">Our Journey</h2>
                    
                    <!-- Instagram Stories Section -->
                    <div class="stories-section fade-in">
                        <h3 style="margin-bottom: 2rem; color: var(--primary); font-family: 'Playfair Display', serif;">Our Stories</h3>
                        <div class="stories-container">
                            <div class="story-item" onclick="openStory(0)">
                                <div class="story-circle">
                                    <img src="{{ $details['story_1'] ?? 'https://images.unsplash.com/photo-1469371670807-013ccf25f16a?ixlib=rb-4.0.3&w=150&h=150&fit=crop' }}" alt="First Meet" class="story-image">
                                </div>
                                <span class="story-label">First Meet</span>
                            </div>
                            <div class="story-item" onclick="openStory(1)">
                                <div class="story-circle">
                                    <img src="{{ $details['story_2'] ?? 'https://images.unsplash.com/photo-1518568814500-bf0f8d125f46?ixlib=rb-4.0.3&w=150&h=150&fit=crop' }}" alt="Proposal" class="story-image">
                                </div>
                                <span class="story-label">Proposal</span>
                            </div>
                            <div class="story-item" onclick="openStory(2)">
                                <div class="story-circle">
                                    <img src="{{ $details['story_3'] ?? 'https://images.unsplash.com/photo-1464375117522-1311d6a5b81f?ixlib=rb-4.0.3&w=150&h=150&fit=crop' }}" alt="Engagement" class="story-image">
                                </div>
                                <span class="story-label">Engagement</span>
                            </div>
                            <div class="story-item" onclick="openStory(3)">
                                <div class="story-circle">
                                    <img src="{{ $details['story_4'] ?? 'https://images.unsplash.com/photo-1511285560929-80b456fea0bc?ixlib=rb-4.0.3&w=150&h=150&fit=crop' }}" alt="Pre-Wedding" class="story-image">
                                </div>
                                <span class="story-label">Pre-Wedding</span>
                            </div>
                        </div>
                    </div>

                    <!-- Before/After Slider Section -->
                    <div class="before-after-section fade-in">
                        <h3 style="text-align: center; margin-bottom: 2rem; color: var(--primary); font-family: 'Playfair Display', serif;">Our Journey Together</h3>
                        <div class="comparison-slider">
                            <div class="comparison-container">
                                <img src="{{ $details['before_photo'] ?? 'https://images.unsplash.com/photo-1494790108755-2616c4e50b47?ixlib=rb-4.0.3&w=600&h=400&fit=crop' }}" alt="Before" class="comparison-before">
                                <img src="{{ $details['after_photo'] ?? 'https://images.unsplash.com/photo-1606216794074-735e91aa2c92?ixlib=rb-4.0.3&w=600&h=400&fit=crop' }}" alt="After" class="comparison-after">
                                <div class="comparison-slider-handle" id="comparisonHandle"></div>
                                <div class="comparison-labels">
                                    <span class="comparison-label">{{ $details['before_label'] ?? 'When We First Met' }}</span>
                                    <span class="comparison-label">{{ $details['after_label'] ?? 'Ready to Say I Do' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Photo Gallery with Lightbox -->
                    <div class="photo-gallery-section fade-in">
                        <h3 style="text-align: center; margin-bottom: 2rem; color: var(--primary); font-family: 'Playfair Display', serif;">Photo Gallery</h3>
                        <div class="gallery-grid" id="galleryGrid">
                            @for($i = 1; $i <= 6; $i++)
                            <div class="gallery-item fade-in" onclick="openLightbox({{ $i - 1 }})">
                                @if($details["gallery_photo_$i"] ?? false)
                                    <img src="{{ $details['gallery_photo_' . $i] }}" alt="Gallery Photo {{ $i }}">
                                @else
                                    <img src="https://images.unsplash.com/photo-1519225421980-715cb0215aed?ixlib=rb-4.0.3&w=300&h=300&fit=crop" alt="Gallery Photo {{ $i }}">
                                @endif
                                <div class="gallery-item-overlay">
                                    <div class="gallery-item-info">
                                        <div class="gallery-item-title">Beautiful Moment {{ $i }}</div>
                                        <div class="gallery-item-category">Gallery</div>
                                    </div>
                                </div>
                            </div>
                            @endfor
                        </div>
                    </div>
                </div>
            </section>

            <!-- Story Overlay -->
            <div class="story-overlay" id="storyOverlay">
                <div class="story-content">
                    <div class="story-progress">
                        <div class="story-progress-bar" id="storyProgressBar"></div>
                    </div>
                    <img id="storyImage" src="" alt="Story">
                    <div class="story-close" onclick="closeStory()">
                        <i class="fas fa-times"></i>
                    </div>
                </div>
            </div>

            <!-- Lightbox Modal -->
            <div class="lightbox-modal" id="lightboxModal">
                <div class="lightbox-content">
                    <img id="lightboxImage" src="" alt="" class="lightbox-image">
                    <button class="lightbox-nav lightbox-prev" onclick="navigateLightbox(-1)">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <button class="lightbox-nav lightbox-next" onclick="navigateLightbox(1)">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                    <button class="lightbox-close" onclick="closeLightbox()">
                        <i class="fas fa-times"></i>
                    </button>
                    <div class="lightbox-counter" id="lightboxCounter">1 / 6</div>
                </div>
            </div>
        </div>

        <!-- RSVP Section -->
        <section id="rsvp" class="rsvp section-reveal">
            <div class="container">
                <h2 class="section-title fade-in">Please RSVP</h2>
                
                <!-- RSVP Success Message -->
                <div id="rsvp-success" class="alert alert-success" style="display: none; max-width: 600px; margin: 0 auto 2rem;">
                    <h4><i class="fas fa-check-circle"></i> Thank You!</h4>
                    <p id="success-message"></p>
                </div>

                <!-- RSVP Error Message -->
                <div id="rsvp-error" class="alert alert-danger" style="display: none; max-width: 600px; margin: 0 auto 2rem;">
                    <h4><i class="fas fa-exclamation-circle"></i> Error</h4>
                    <p id="error-message"></p>
                </div>

                <form class="rsvp-form fade-in" id="rsvpForm">
                    <div class="form-group">
                        <input type="text" name="guest_name" class="form-control" placeholder="Your Name" required>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="form-group">
                        <input type="email" name="guest_email" class="form-control" placeholder="Your Email" required>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="form-group">
                        <input type="tel" name="guest_phone" class="form-control" placeholder="Your Phone Number">
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="form-group">
                        <select name="attendance_status" class="form-control" required>
                            <option value="" disabled selected>Will you attend?</option>
                            <option value="yes">Yes, I will attend</option>
                            <option value="no">Sorry, I cannot attend</option>
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="form-group" id="guests-group">
                        <input type="number" name="number_of_guests" class="form-control" placeholder="Number of Guests" min="1" max="10" value="1">
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="form-group">
                        <textarea name="message" class="form-control" placeholder="Your Message" rows="4"></textarea>
                        <div class="invalid-feedback"></div>
                    </div>
                    <button type="submit" class="btn btn-primary" id="rsvp-submit-btn">
                        <span class="btn-text">Send RSVP</span>
                        <span class="btn-loading" style="display: none;">
                            <i class="fas fa-spinner fa-spin"></i> Sending...
                        </span>
                    </button>
                </form>
            </div>
        </section>

        <!-- Footer -->
        <footer>
            <div class="container">
                <div class="footer-content">
                    <h2 class="footer-title">{{ $details["groom_name"] ?? "Groom" }} & {{ $details["bride_name"] ?? "Bride" }}</h2>
                    <p>{{ $details["footer_message"] ?? "Thank you for celebrating our special day with us!" }}</p>
                    @if($details["social_instagram"] ?? false || $details["social_facebook"] ?? false || $details["social_twitter"] ?? false)
                    <ul class="social-links">
                        @if($details["social_instagram"] ?? false)
                        <li><a href="{{ $details['social_instagram'] }}" target="_blank"><i class="fab fa-instagram"></i></a></li>
                        @endif
                        @if($details["social_facebook"] ?? false)
                        <li><a href="{{ $details['social_facebook'] }}" target="_blank"><i class="fab fa-facebook"></i></a></li>
                        @endif
                        @if($details["social_twitter"] ?? false)
                        <li><a href="{{ $details['social_twitter'] }}" target="_blank"><i class="fab fa-twitter"></i></a></li>
                        @endif
                    </ul>
                    @endif
                    <p class="copyright">&copy; {{ date('Y') }} {{ $details["groom_name"] ?? "Groom" }} & {{ $details["bride_name"] ?? "Bride" }}. All Rights Reserved.</p>
                </div>
            </div>
        </footer>
    </div>

    <!-- JavaScript -->
    <script>
        // Navigation Toggle
        const hamburger = document.querySelector('.hamburger');
        const navLinks = document.querySelector('.nav-links');
        
        if (hamburger && navLinks) {
            hamburger.addEventListener('click', () => {
                navLinks.classList.toggle('active');
            });
        }
        
        // Sticky Header
        window.addEventListener('scroll', () => {
            const header = document.querySelector('header');
            if (header) {
                header.classList.toggle('scrolled', window.scrollY > 50);
            }
        });
        
        // Scroll Animation
        const fadeElements = document.querySelectorAll('.fade-in, .fade-in-left, .fade-in-right');
        
        const scrollAnimation = () => {
            fadeElements.forEach(element => {
                const elementTop = element.getBoundingClientRect().top;
                const elementVisible = 150;
                
                if (elementTop < window.innerHeight - elementVisible) {
                    element.classList.add('active');
                }
            });
        };
        
        // Run animation on load and scroll
        window.addEventListener('load', scrollAnimation);
        window.addEventListener('scroll', scrollAnimation);
        
        // RSVP Form Submission
        const rsvpForm = document.getElementById('rsvpForm');
        if (rsvpForm) {
            const submitBtn = document.getElementById('rsvp-submit-btn');
            const btnText = submitBtn.querySelector('.btn-text');
            const btnLoading = submitBtn.querySelector('.btn-loading');
            const attendanceSelect = rsvpForm.querySelector('[name="attendance_status"]');
            const guestsGroup = document.getElementById('guests-group');
            
            // Show/hide guests field based on attendance
            attendanceSelect.addEventListener('change', function() {
                if (this.value === 'yes') {
                    guestsGroup.style.display = 'block';
                    guestsGroup.querySelector('input').required = true;
                } else {
                    guestsGroup.style.display = 'none';
                    guestsGroup.querySelector('input').required = false;
                    guestsGroup.querySelector('input').value = '';
                }
            });

            rsvpForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                // Clear previous errors
                clearFormErrors();
                hideMessages();
                
                // Show loading state
                submitBtn.disabled = true;
                btnText.style.display = 'none';
                btnLoading.style.display = 'inline';
                
                const formData = new FormData(this);
                const uniqueUrl = window.location.pathname.split('/').pop();
                
                // Set up headers for AJAX request
                const headers = {
                    'X-Requested-With': 'XMLHttpRequest'
                };
                
                fetch(`/wedding-card/${uniqueUrl}/rsvp`, {
                    method: 'POST',
                    body: formData,
                    headers: headers
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showSuccessMessage(data.message);
                        rsvpForm.reset();
                        rsvpForm.style.display = 'none';
                    } else {
                        if (data.errors) {
                            showFieldErrors(data.errors);
                        } else {
                            showErrorMessage(data.message || 'An error occurred. Please try again.');
                        }
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showErrorMessage('An error occurred. Please try again.');
                })
                .finally(() => {
                    // Reset button state
                    submitBtn.disabled = false;
                    btnText.style.display = 'inline';
                    btnLoading.style.display = 'none';
                });
            });
        }
        
        // Helper functions for RSVP form
        function showSuccessMessage(message) {
            const successDiv = document.getElementById('rsvp-success');
            const messageP = document.getElementById('success-message');
            messageP.textContent = message;
            successDiv.style.display = 'block';
            successDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
        
        function showErrorMessage(message) {
            const errorDiv = document.getElementById('rsvp-error');
            const messageP = document.getElementById('error-message');
            messageP.textContent = message;
            errorDiv.style.display = 'block';
            errorDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
        
        function hideMessages() {
            document.getElementById('rsvp-success').style.display = 'none';
            document.getElementById('rsvp-error').style.display = 'none';
        }
        
        function clearFormErrors() {
            const errorElements = document.querySelectorAll('.invalid-feedback');
            const inputElements = document.querySelectorAll('.form-control');
            
            errorElements.forEach(el => {
                el.textContent = '';
                el.style.display = 'none';
            });
            
            inputElements.forEach(el => {
                el.classList.remove('is-invalid');
            });
        }
        
        function showFieldErrors(errors) {
            for (const field in errors) {
                const input = document.querySelector(`[name="${field}"]`);
                const errorDiv = input.parentNode.querySelector('.invalid-feedback');
                
                if (input && errorDiv) {
                    input.classList.add('is-invalid');
                    errorDiv.textContent = errors[field][0];
                    errorDiv.style.display = 'block';
                }
            }
        }

        // Video and invitation functionality
        let videoStarted = false;
        let autoplayFailed = false;

        document.addEventListener('DOMContentLoaded', function() {
            document.body.classList.add('invitation-active');
            
            const invitationOverlay = document.querySelector('.invitation-overlay');
            const envelope = document.querySelector('.envelope');
            const floatingMenu = document.querySelector('.floating-menu');
            const videoSection = document.getElementById('videoSection');
            const mainContent = document.getElementById('mainContent');
            const video = document.getElementById('weddingVideo');
            const fallbackPlayBtn = document.getElementById('fallbackPlayBtn');
            
            // Make sure gift modal is hidden initially
            const giftModal = document.getElementById('giftModal');
            if (giftModal) {
                giftModal.style.opacity = '0';
                giftModal.style.visibility = 'hidden';
            }

            // Check if video can autoplay
            if (video) {
                // Listen for autoplay failure
                video.addEventListener('play', function() {
                    videoStarted = true;
                    fallbackPlayBtn.style.display = 'none';
                });

                video.addEventListener('pause', function() {
                    if (!videoStarted && !autoplayFailed) {
                        autoplayFailed = true;
                        fallbackPlayBtn.style.display = 'flex';
                    }
                });

                // Set up video end behavior
                video.addEventListener('ended', function() {
                    setTimeout(() => {
                        skipToMainContent();
                    }, 2000); // Auto-proceed 2 seconds after video ends
                });

                // Check if video is actually playing after a delay
                setTimeout(() => {
                    if (video.paused && !videoStarted) {
                        autoplayFailed = true;
                        fallbackPlayBtn.style.display = 'flex';
                    }
                }, 1000);
            }
            
            // Prevent scrolling on overlay
            if (invitationOverlay) {
                invitationOverlay.addEventListener('touchmove', function(e) {
                    e.preventDefault();
                }, { passive: false });
                
                invitationOverlay.addEventListener('wheel', function(e) {
                    e.preventDefault();
                }, { passive: false });
                
                invitationOverlay.addEventListener('click', () => {
                    // Enhanced 3D Letter opening animation sequence
                    if (envelope) {
                        // Phase 1: Envelope opens (1.5s)
                        envelope.classList.add('opening');
                        
                        // Add subtle rotation and scale to envelope container
                        const envelopeContainer = document.querySelector('.envelope-container');
                        if (envelopeContainer) {
                            envelopeContainer.style.transition = 'transform 2s cubic-bezier(0.25, 0.46, 0.45, 0.94)';
                            envelopeContainer.style.transform = 'rotateY(8deg) rotateX(-3deg) scale(1.05)';
                        }
                        
                        // Phase 2: Letter starts sliding out (starts at 0.8s)
                        setTimeout(() => {
                            envelope.classList.add('letter-sliding-out');
                        }, 800);
                        
                        // Phase 3: Letter emerges more (at 2s)
                        setTimeout(() => {
                            envelope.classList.remove('letter-sliding-out');
                            envelope.classList.add('letter-emerging');
                        }, 2000);
                        
                        // Phase 4: Letter begins to unfold (at 3.5s)
                        setTimeout(() => {
                            envelope.classList.remove('letter-emerging');
                            envelope.classList.add('letter-unfolding');
                        }, 3500);
                        
                        // Phase 5: Keep letter unfolded and focused (at 5.5s) - removed extended phase
                        setTimeout(() => {
                            // Add gentle floating animation to the letter content while keeping unfolding state
                            const letterContent = document.querySelector('.letter-content');
                            if (letterContent) {
                                // Use CSS class instead of inline style to maintain position
                                letterContent.classList.add('letter-floating');
                                letterContent.classList.add('letter-content-focused');
                            }
                        }, 5500);
                    }
                    
                    // Hide invitation and show video after displaying letter for 5 seconds in unfolding state
                    setTimeout(() => {
                        invitationOverlay.classList.add('hidden');
                        document.body.classList.remove('invitation-active');
                        
                        // Show video section
                        if (videoSection) {
                            videoSection.classList.add('active');
                        }

                        // Try to play video after user interaction
                        if (video && autoplayFailed) {
                            video.play().then(() => {
                                fallbackPlayBtn.style.display = 'none';
                                videoStarted = true;
                            }).catch(() => {
                                // Keep fallback button visible
                            });
                        }
                        
                        // Remove invitation from DOM after transition
                        setTimeout(() => {
                            invitationOverlay.style.display = 'none';
                        }, 1500);
                    }, 10500); // Total animation: 5.5s + 5s display = 10.5s
                });
            }

            // Enable scrolling from video to main content
            let isScrolling = false;

            function handleScroll() {
                if (isScrolling) return;
                
                const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
                const videoRect = videoSection.getBoundingClientRect();
                
                // If user scrolls down significantly while video is visible
                if (videoRect.top < -window.innerHeight * 0.3) {
                    skipToMainContent();
                }
            }

            // Add scroll listener when video section is active
            document.addEventListener('scroll', handleScroll, { passive: true });

            // Handle touch scrolling for mobile
            let touchStartY = 0;
            let touchEndY = 0;

            videoSection.addEventListener('touchstart', function(e) {
                touchStartY = e.touches[0].clientY;
            }, { passive: true });

            videoSection.addEventListener('touchmove', function(e) {
                touchEndY = e.touches[0].clientY;
            }, { passive: true });

            videoSection.addEventListener('touchend', function(e) {
                const deltaY = touchStartY - touchEndY;
                
                if (deltaY > 100) { // Swipe up
                    skipToMainContent();
                }
            }, { passive: true });

            // Initialize Malaysian card animations on scroll
            const cardSections = document.querySelectorAll('.card-section');
            const observerOptions = {
                threshold: 0.3,
                rootMargin: '0px 0px -50px 0px'
            };

            const cardObserver = new IntersectionObserver(function(entries) {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.opacity = '1';
                        entry.target.style.transform = 'translateY(0)';
                    }
                });
            }, observerOptions);

            cardSections.forEach(section => {
                cardObserver.observe(section);
            });
        });

        function playVideoFallback() {
            const video = document.getElementById('weddingVideo');
            const fallbackPlayBtn = document.getElementById('fallbackPlayBtn');
            
            if (video) {
                video.play().then(() => {
                    fallbackPlayBtn.style.display = 'none';
                    videoStarted = true;
                }).catch(error => {
                    console.log('Video play failed:', error);
                });
            }
        }

        function skipToMainContent() {
            const videoSection = document.getElementById('videoSection');
            const mainContent = document.getElementById('mainContent');
            const floatingMenu = document.querySelector('.floating-menu');
            
            // Smooth transition to main content
            if (videoSection && mainContent) {
                videoSection.style.transition = 'opacity 1s ease';
                videoSection.style.opacity = '0';
                
                setTimeout(() => {
                    videoSection.style.display = 'none';
                    mainContent.style.display = 'block';
                    document.body.classList.add('content-visible');
                    
                    // Show floating menu
                    if (floatingMenu) {
                        setTimeout(() => {
                            floatingMenu.classList.add('visible');
                        }, 500);
                    }
                    
                    // Scroll to hero section
                    const heroSection = document.getElementById('hero');
                    if (heroSection) {
                        heroSection.scrollIntoView({ behavior: 'smooth' });
                    }
                    
                    // Trigger scroll animations
                    scrollAnimation();
                }, 1000);
            }
        }

        // Floating Menu Functions
        function scrollToSection(sectionId) {
            const section = document.getElementById(sectionId);
            if (section) {
                section.scrollIntoView({ behavior: 'smooth' });
            }
        }
        
        function openGiftModal() {
            const modal = document.getElementById('giftModal');
            if (modal) {
                modal.style.opacity = '1';
                modal.style.visibility = 'visible';
                modal.style.pointerEvents = 'auto';
                modal.classList.add('active');
            }
        }
        
        function closeGiftModal() {
            const modal = document.getElementById('giftModal');
            if (modal) {
                modal.classList.remove('active');
                modal.style.opacity = '0';
                modal.style.visibility = 'hidden';
                modal.style.pointerEvents = 'none';
                resetGiftSelections();
            }
        }
        
        let selectedAmount = 0;
        
        function selectAmount(amount, element) {
            selectedAmount = amount;
            
            // Reset all selections
            document.querySelectorAll('.gift-amount').forEach(el => {
                el.classList.remove('selected');
            });
            
            // Clear custom amount
            const customAmount = document.getElementById('customAmount');
            if (customAmount) {
                customAmount.value = '';
            }
            
            // Add selected class
            element.classList.add('selected');
        }
        
        function customAmountChange() {
            const customInput = document.getElementById('customAmount');
            if (customInput && customInput.value) {
                selectedAmount = parseInt(customInput.value);
                
                // Reset predefined selections
                document.querySelectorAll('.gift-amount').forEach(el => {
                    el.classList.remove('selected');
                });
            }
        }
        
        function sendGift() {
            if (selectedAmount <= 0) {
                alert('Please select or enter a gift amount.');
                return;
            }
            
            alert(`Thank you for your gift of RM${selectedAmount}! We appreciate your generosity.`);
            closeGiftModal();
        }
        
        function resetGiftSelections() {
            selectedAmount = 0;
            document.querySelectorAll('.gift-amount').forEach(el => {
                el.classList.remove('selected');
            });
            const customAmount = document.getElementById('customAmount');
            if (customAmount) {
                customAmount.value = '';
            }
        }
        
        // Gift modal close on outside click
        document.addEventListener('DOMContentLoaded', function() {
            const giftModal = document.getElementById('giftModal');
            if (giftModal) {
                giftModal.addEventListener('click', function(e) {
                    if (e.target === giftModal) {
                        closeGiftModal();
                    }
                });
            }
        });

        // Countdown Timer Functionality
        function initCountdown() {
            // Get wedding date from PHP - you might need to adjust the format
            const weddingDateStr = "{{ $details['wedding_datetime'] ?? $details['wedding_date'] ?? '2025-12-31 18:00:00' }}";
            
            // Try to parse the date - handle different formats
            let weddingDate;
            
            try {
                // Handle different date formats
                let dateStr = weddingDateStr.trim();
                
                // If it contains 'T' (ISO format like 2025-12-07T20:00), convert to standard format
                if (dateStr.includes('T')) {
                    // Add seconds if missing (2025-12-07T20:00 -> 2025-12-07T20:00:00)
                    if (dateStr.match(/T\d{2}:\d{2}$/)) {
                        dateStr += ':00';
                    }
                    // Replace T with space for better browser compatibility
                    dateStr = dateStr.replace('T', ' ');
                }
                
                // If it's just a date without time, add default time
                if (!dateStr.includes(' ') && !dateStr.includes('T')) {
                    dateStr += ' 18:00:00';
                }
                
                weddingDate = new Date(dateStr);
                
                // Check if date is valid
                if (isNaN(weddingDate.getTime())) {
                    throw new Error('Invalid date after parsing');
                }
                
            } catch (error) {
                console.warn('Invalid wedding date format:', weddingDateStr, 'Error:', error.message);
                // Fallback to a default future date
                weddingDate = new Date();
                weddingDate.setMonth(weddingDate.getMonth() + 3); // 3 months from now
            }

            const countdownTimer = document.getElementById('countdownTimer');
            const countdownExpired = document.getElementById('countdownExpired');
            const daysElement = document.getElementById('days');
            const hoursElement = document.getElementById('hours');
            const minutesElement = document.getElementById('minutes');
            const secondsElement = document.getElementById('seconds');

            function updateCountdown() {
                const now = new Date().getTime();
                const timeLeft = weddingDate.getTime() - now;

                if (timeLeft <= 0) {
                    // Wedding day has passed
                    if (countdownTimer) countdownTimer.style.display = 'none';
                    if (countdownExpired) countdownExpired.classList.add('show');
                    return;
                }

                // Calculate time units
                const days = Math.floor(timeLeft / (1000 * 60 * 60 * 24));
                const hours = Math.floor((timeLeft % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const minutes = Math.floor((timeLeft % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((timeLeft % (1000 * 60)) / 1000);

                // Update the display with leading zeros
                if (daysElement) daysElement.textContent = days.toString().padStart(2, '0');
                if (hoursElement) hoursElement.textContent = hours.toString().padStart(2, '0');
                if (minutesElement) minutesElement.textContent = minutes.toString().padStart(2, '0');
                if (secondsElement) secondsElement.textContent = seconds.toString().padStart(2, '0');
            }

            // Update countdown immediately
            updateCountdown();

            // Update every second
            setInterval(updateCountdown, 1000);
        }

        // Initialize countdown when DOM is loaded
        document.addEventListener('DOMContentLoaded', initCountdown);

        // Parallax and Animation Effects
        class ParallaxEffects {
            constructor() {
                this.parallaxElements = document.querySelectorAll('.parallax-element');
                this.sectionRevealElements = document.querySelectorAll('.section-reveal');
                this.floatingPetals = document.getElementById('floatingPetals');
                this.floatingSparkles = document.getElementById('floatingSparkles');
                this.ticking = false;
                
                this.init();
            }

            init() {
                this.createFloatingElements();
                this.bindEvents();
                this.revealSectionsOnScroll();
                
                // Initial parallax calculation
                this.updateParallax();
            }

            bindEvents() {
                window.addEventListener('scroll', () => this.onScroll(), { passive: true });
                window.addEventListener('resize', () => this.onResize(), { passive: true });
            }

            onScroll() {
                if (!this.ticking) {
                    requestAnimationFrame(() => {
                        this.updateParallax();
                        this.revealSectionsOnScroll();
                        this.ticking = false;
                    });
                    this.ticking = true;
                }
            }

            onResize() {
                this.updateParallax();
            }

            updateParallax() {
                const scrollTop = window.pageYOffset;
                const windowHeight = window.innerHeight;

                this.parallaxElements.forEach(element => {
                    const rect = element.getBoundingClientRect();
                    const speed = parseFloat(element.dataset.speed) || 0.5;
                    const yPos = scrollTop * speed;
                    
                    // Only apply transform if element is near viewport
                    if (rect.bottom >= -200 && rect.top <= windowHeight + 200) {
                        element.style.transform = `translate3d(0, ${yPos}px, 0)`;
                    }
                });
            }

            revealSectionsOnScroll() {
                const scrollTop = window.pageYOffset;
                const windowHeight = window.innerHeight;

                this.sectionRevealElements.forEach(element => {
                    const rect = element.getBoundingClientRect();
                    const elementTop = rect.top + scrollTop;
                    const elementHeight = rect.height;
                    const revealPoint = elementTop + elementHeight * 0.1;

                    if (scrollTop + windowHeight >= revealPoint) {
                        element.classList.add('revealed');
                    }
                });
            }

            createFloatingElements() {
                this.createPetals();
                this.createSparkles();
            }

            createPetals() {
                if (!this.floatingPetals) return;

                const petalCount = 15;
                for (let i = 0; i < petalCount; i++) {
                    setTimeout(() => {
                        this.createPetal();
                    }, i * 2000); // Stagger creation
                }

                // Continue creating petals
                setInterval(() => {
                    this.createPetal();
                }, 8000);
            }

            createPetal() {
                if (!this.floatingPetals) return;

                const petal = document.createElement('div');
                petal.className = 'petal';
                
                // Random size
                const size = Math.random() * 15 + 8;
                petal.style.width = size + 'px';
                petal.style.height = size + 'px';
                
                // Random starting position
                petal.style.left = Math.random() * 100 + '%';
                
                // Random animation duration
                const duration = Math.random() * 10 + 15;
                petal.style.animationDuration = duration + 's';
                
                // Random delay
                const delay = Math.random() * 5;
                petal.style.animationDelay = delay + 's';

                this.floatingPetals.appendChild(petal);

                // Remove after animation
                setTimeout(() => {
                    if (petal.parentNode) {
                        petal.parentNode.removeChild(petal);
                    }
                }, (duration + delay) * 1000);
            }

            createSparkles() {
                if (!this.floatingSparkles) return;

                const sparkleCount = 8;
                for (let i = 0; i < sparkleCount; i++) {
                    setTimeout(() => {
                        this.createSparkle();
                    }, i * 3000); // Stagger creation
                }

                // Continue creating sparkles
                setInterval(() => {
                    this.createSparkle();
                }, 12000);
            }

            createSparkle() {
                if (!this.floatingSparkles) return;

                const sparkle = document.createElement('div');
                sparkle.className = 'sparkle';
                
                // Random size
                const size = Math.random() * 10 + 6;
                sparkle.style.width = size + 'px';
                sparkle.style.height = size + 'px';
                
                // Random starting position
                sparkle.style.left = Math.random() * 100 + '%';
                
                // Random animation duration
                const duration = Math.random() * 8 + 12;
                sparkle.style.animationDuration = duration + 's';
                
                // Random delay
                const delay = Math.random() * 3;
                sparkle.style.animationDelay = delay + 's';

                this.floatingSparkles.appendChild(sparkle);

                // Remove after animation
                setTimeout(() => {
                    if (sparkle.parentNode) {
                        sparkle.parentNode.removeChild(sparkle);
                    }
                }, (duration + delay) * 1000);
            }
        }

        // Enhanced Smooth Scrolling
        function scrollToSection(sectionId) {
            const section = document.getElementById(sectionId);
            if (section) {
                const headerHeight = document.querySelector('header').offsetHeight;
                const targetPosition = section.offsetTop - headerHeight;
                
                window.scrollTo({
                    top: targetPosition,
                    behavior: 'smooth'
                });
            }
        }

        // Initialize parallax effects when main content is visible
        function initParallaxEffects() {
            // Wait for main content to be visible
            const checkContentVisible = () => {
                if (document.body.classList.contains('content-visible')) {
                    new ParallaxEffects();
                } else {
                    setTimeout(checkContentVisible, 500);
                }
            };
            checkContentVisible();
        }

        // Gallery functionality
        let currentLightboxIndex = 0;
        let currentStoryIndex = 0;
        let storyTimer = null;
        
        const galleryImages = [
            {
                src: "{{ $details['gallery_photo_1'] ?? 'https://images.unsplash.com/photo-1519225421980-715cb0215aed?ixlib=rb-4.0.3&w=300&h=300&fit=crop' }}",
                alt: "Gallery Photo 1",
                title: "Beautiful Moment 1"
            },
            {
                src: "{{ $details['gallery_photo_2'] ?? 'https://images.unsplash.com/photo-1519225421980-715cb0215aed?ixlib=rb-4.0.3&w=300&h=300&fit=crop' }}",
                alt: "Gallery Photo 2",
                title: "Beautiful Moment 2"
            },
            {
                src: "{{ $details['gallery_photo_3'] ?? 'https://images.unsplash.com/photo-1519225421980-715cb0215aed?ixlib=rb-4.0.3&w=300&h=300&fit=crop' }}",
                alt: "Gallery Photo 3",
                title: "Beautiful Moment 3"
            },
            {
                src: "{{ $details['gallery_photo_4'] ?? 'https://images.unsplash.com/photo-1519225421980-715cb0215aed?ixlib=rb-4.0.3&w=300&h=300&fit=crop' }}",
                alt: "Gallery Photo 4",
                title: "Beautiful Moment 4"
            },
            {
                src: "{{ $details['gallery_photo_5'] ?? 'https://images.unsplash.com/photo-1519225421980-715cb0215aed?ixlib=rb-4.0.3&w=300&h=300&fit=crop' }}",
                alt: "Gallery Photo 5",
                title: "Beautiful Moment 5"
            },
            {
                src: "{{ $details['gallery_photo_6'] ?? 'https://images.unsplash.com/photo-1519225421980-715cb0215aed?ixlib=rb-4.0.3&w=300&h=300&fit=crop' }}",
                alt: "Gallery Photo 6",
                title: "Beautiful Moment 6"
            }
        ];

        const stories = [
            {
                image: "{{ $details['story_1'] ?? 'https://images.unsplash.com/photo-1469371670807-013ccf25f16a?ixlib=rb-4.0.3&w=400&h=600&fit=crop' }}",
                title: "First Meet",
                description: "The magical moment we first met"
            },
            {
                image: "{{ $details['story_2'] ?? 'https://images.unsplash.com/photo-1518568814500-bf0f8d125f46?ixlib=rb-4.0.3&w=400&h=600&fit=crop' }}",
                title: "Proposal",
                description: "The day he asked the most important question"
            },
            {
                image: "{{ $details['story_3'] ?? 'https://images.unsplash.com/photo-1464375117522-1311d6a5b81f?ixlib=rb-4.0.3&w=400&h=600&fit=crop' }}",
                title: "Engagement",
                description: "Celebrating our engagement with family"
            },
            {
                image: "{{ $details['story_4'] ?? 'https://images.unsplash.com/photo-1511285560929-80b456fea0bc?ixlib=rb-4.0.3&w=400&h=600&fit=crop' }}",
                title: "Pre-Wedding",
                description: "Beautiful pre-wedding moments"
            }
        ];

        // Lightbox functions
        function openLightbox(index) {
            currentLightboxIndex = index;
            const modal = document.getElementById('lightboxModal');
            const image = document.getElementById('lightboxImage');
            const counter = document.getElementById('lightboxCounter');
            
            const imageData = galleryImages[index];
            
            image.src = imageData.src;
            image.alt = imageData.alt;
            counter.textContent = `${index + 1} / ${galleryImages.length}`;
            
            modal.classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeLightbox() {
            const modal = document.getElementById('lightboxModal');
            modal.classList.remove('active');
            document.body.style.overflow = '';
        }

        function navigateLightbox(direction) {
            currentLightboxIndex += direction;
            
            if (currentLightboxIndex < 0) {
                currentLightboxIndex = galleryImages.length - 1;
            } else if (currentLightboxIndex >= galleryImages.length) {
                currentLightboxIndex = 0;
            }
            
            openLightbox(currentLightboxIndex);
        }

        // Story functions
        function openStory(index) {
            currentStoryIndex = index;
            const overlay = document.getElementById('storyOverlay');
            const image = document.getElementById('storyImage');
            const progressBar = document.getElementById('storyProgressBar');
            
            const story = stories[index];
            
            image.src = story.image;
            image.alt = story.title;
            
            overlay.style.display = 'flex';
            document.body.style.overflow = 'hidden';
            
            // Start progress animation
            progressBar.style.width = '0%';
            setTimeout(() => {
                progressBar.style.width = '100%';
            }, 100);
            
            // Auto-advance story
            storyTimer = setTimeout(() => {
                nextStory();
            }, 3000);
        }

        function closeStory() {
            const overlay = document.getElementById('storyOverlay');
            overlay.style.display = 'none';
            document.body.style.overflow = '';
            
            if (storyTimer) {
                clearTimeout(storyTimer);
                storyTimer = null;
            }
        }

        function nextStory() {
            if (storyTimer) {
                clearTimeout(storyTimer);
            }
            
            currentStoryIndex++;
            if (currentStoryIndex >= stories.length) {
                closeStory();
                return;
            }
            
            openStory(currentStoryIndex);
        }

        // Comparison slider functionality
        function initComparisonSlider() {
            const handle = document.getElementById('comparisonHandle');
            const afterImage = document.querySelector('.comparison-after');
            const container = document.querySelector('.comparison-container');
            
            if (!handle || !afterImage || !container) return;

            let isDragging = false;

            const updateSlider = (clientX) => {
                const rect = container.getBoundingClientRect();
                const x = clientX - rect.left;
                const percentage = Math.max(0, Math.min(100, (x / rect.width) * 100));
                
                handle.style.left = percentage + '%';
                afterImage.style.clipPath = `inset(0 ${100 - percentage}% 0 0)`;
            };

            // Mouse events
            handle.addEventListener('mousedown', (e) => {
                isDragging = true;
                document.addEventListener('mousemove', onMouseMove);
                document.addEventListener('mouseup', onMouseUp);
                e.preventDefault();
            });

            const onMouseMove = (e) => {
                if (isDragging) {
                    updateSlider(e.clientX);
                }
            };

            const onMouseUp = () => {
                isDragging = false;
                document.removeEventListener('mousemove', onMouseMove);
                document.removeEventListener('mouseup', onMouseUp);
            };

            // Touch events for mobile
            handle.addEventListener('touchstart', (e) => {
                isDragging = true;
                e.preventDefault();
            });

            document.addEventListener('touchmove', (e) => {
                if (isDragging) {
                    const touch = e.touches[0];
                    updateSlider(touch.clientX);
                    e.preventDefault();
                }
            }, { passive: false });

            document.addEventListener('touchend', () => {
                isDragging = false;
            });

            // Click to position
            container.addEventListener('click', (e) => {
                if (!isDragging) {
                    updateSlider(e.clientX);
                }
            });
        }

        // Keyboard navigation
        document.addEventListener('keydown', (e) => {
            const lightbox = document.getElementById('lightboxModal');
            const storyOverlay = document.getElementById('storyOverlay');
            
            if (lightbox.classList.contains('active')) {
                if (e.key === 'ArrowLeft') {
                    navigateLightbox(-1);
                } else if (e.key === 'ArrowRight') {
                    navigateLightbox(1);
                } else if (e.key === 'Escape') {
                    closeLightbox();
                }
            }
            
            if (storyOverlay.style.display === 'flex') {
                if (e.key === 'Escape') {
                    closeStory();
                }
            }
        });

        // Close modals on outside click
        document.addEventListener('DOMContentLoaded', function() {
            const lightboxModal = document.getElementById('lightboxModal');
            const storyOverlay = document.getElementById('storyOverlay');
            
            if (lightboxModal) {
                lightboxModal.addEventListener('click', (e) => {
                    if (e.target === e.currentTarget) {
                        closeLightbox();
                    }
                });
            }

            if (storyOverlay) {
                storyOverlay.addEventListener('click', (e) => {
                    if (e.target === e.currentTarget) {
                        closeStory();
                    }
                });
            }

            // Initialize comparison slider
            initComparisonSlider();
        });

        // Enhanced 3D Letter Animation System (CSS-based)
        class LetterAnimation {
            constructor() {
                this.envelope = null;
                this.isAnimating = false;
                this.init();
            }

            init() {
                this.envelope = document.getElementById('envelope');
                // Animation is handled by the invitation overlay click event
            }

            cleanup() {
                // No cleanup needed for CSS animations
            }
        }

        // Initialize when DOM is ready
        document.addEventListener('DOMContentLoaded', () => {
            // Initialize CSS-based 3D letter animation
            new LetterAnimation();
            
            // Initialize parallax effects
            initParallaxEffects();
            
            // Enhanced intersection observer for better performance
            if ('IntersectionObserver' in window) {
                const observerOptions = {
                    threshold: 0.1,
                    rootMargin: '50px 0px'
                };

                const observer = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            entry.target.classList.add('revealed');
                        }
                    });
                }, observerOptions);

                // Observe all section reveal elements
                document.querySelectorAll('.section-reveal').forEach(section => {
                    observer.observe(section);
                });
            }
        });
    </script>
</body>
</html>