<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $details["groom_1_name"] ?? "Groom 1" }} & {{ $details["bride_1_name"] ?? "Bride 1" }} and {{ $details["groom_2_name"] ?? "Groom 2" }} & {{ $details["bride_2_name"] ?? "Bride 2" }} - Wedding Invitation</title>
    <script>
        // Pass wedding card ID to JavaScript
        window.weddingCardId = {
            {
                $weddingCard - > id ?? 'null'
            }
        };
        // RSVP messages will be populated by view.blade.php
        window.rsvpMessages = [];
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
    <style>
        /* Google Fonts Import */
        @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Montserrat:wght@300;400;500;600&family=Great+Vibes&family=Allura&display=swap');

        /* Reset and Base Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            /* Royal/Classic Palette */
            --primary: #6D4C0C;
            /* Dark Gold/Brown */
            --primary-light: #8B6914;
            /* Gold */
            --primary-dark: #3E2703;
            /* Dark Brown */
            --accent: #B8860B;
            /* Goldenrod */
            --secondary: #FDFBF7;
            /* Cream/Off-white */
            --background: #FDFBF7;
            --text-dark: #3E2703;
            --text-light: #FDFBF7;
            --border-color: #D4AF37;
            /* Gold Border */

            --transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            --shadow-sm: 0 2px 8px rgba(109, 76, 12, 0.08);
            --shadow-md: 0 4px 16px rgba(109, 76, 12, 0.12);
            --shadow-lg: 0 8px 32px rgba(109, 76, 12, 0.16);
        }

        html {
            scroll-behavior: smooth;
            scroll-padding-top: 80px;
        }

        body {
            font-family: 'Montserrat', sans-serif;
            color: var(--text-dark);
            line-height: 1.7;
            background-color: var(--secondary);
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%236d4c0c' fill-opacity='0.03'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
            overflow: hidden;
            height: 100vh;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        /* Typography */
        h1,
        h2,
        h3,
        h4 {
            font-family: 'Playfair Display', serif;
            font-weight: 600;
            margin-bottom: 1rem;
            color: var(--primary);
        }

        .script-font {
            font-family: 'Great Vibes', cursive;
        }

        .section-title {
            font-family: 'Great Vibes', cursive;
            font-size: 3.5rem;
            text-align: center;
            margin-bottom: 3.5rem;
            position: relative;
            padding-bottom: 1.5rem;
            color: var(--primary);
            letter-spacing: 1px;
        }

        .section-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 150px;
            height: 2px;
            background: linear-gradient(90deg, transparent, var(--primary), transparent);
        }

        .section-title::before {
            content: '❦';
            display: block;
            text-align: center;
            color: var(--accent);
            font-size: 1.8rem;
            margin-bottom: 0.5rem;
            opacity: 0.8;
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
            /* ensure anchored scrolling accounts for fixed header */
            scroll-margin-top: 100px;
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
            display: none;
        }

        .video-section.active {
            opacity: 1;
            visibility: visible;
            display: flex;
            align-items: center;
            justify-content: center;
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
            background: linear-gradient(135deg, rgba(62, 39, 3, 0.4) 0%, rgba(109, 76, 12, 0.3) 50%, rgba(62, 39, 3, 0.4) 100%);
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
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.7);
            animation: fadeInScale 2s ease-out 1s forwards;
            opacity: 0;
            transform: scale(0.8);
        }

        .video-subtitle-overlay {
            font-size: 1.2rem;
            letter-spacing: 2px;
            text-transform: uppercase;
            margin-bottom: 2rem;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.7);
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
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.7);
        }

        @keyframes gentle-bounce {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-10px);
            }
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
            background: rgba(97, 64, 81, 0.9);
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

        /* Paper Door Animation Styles */
        .paper-container {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #FDFBF7 0%, #F5F0E6 50%, #E8DCC5 100%);
            z-index: 10000;
            transition: all 1s ease-in-out;
        }

        .paper-container.opened {
            opacity: 0;
            visibility: hidden;
            pointer-events: none;
            z-index: -1;
        }

        .paper-doors {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            perspective: 1200px;
        }

        .paper-door {
            position: absolute;
            top: 0;
            height: 100%;
            width: 50%;
            background: linear-gradient(135deg, #ffffff 0%, #FDFBF7 50%, #F5F0E6 100%);
            border: 1px solid rgba(109, 76, 12, 0.1);
            transition: transform 1.8s cubic-bezier(0.4, 0.0, 0.2, 1);
            box-shadow:
                0 0 20px rgba(109, 76, 12, 0.12),
                inset 0 0 20px rgba(255, 255, 255, 0.6);
            backface-visibility: hidden;
        }

        .paper-door::before {
            content: '';
            position: absolute;
            top: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg,
                    transparent 0%,
                    rgba(0, 0, 0, 0.03) 10%,
                    rgba(0, 0, 0, 0.08) 50%,
                    rgba(0, 0, 0, 0.03) 90%,
                    transparent 100%);
            pointer-events: none;
        }

        .paper-door.left {
            left: 0;
            transform-origin: left center;
            border-right: 1px solid rgba(109, 76, 12, 0.15);
            background: linear-gradient(90deg, #ffffff 0%, #FDFBF7 80%, #F5F0E6 100%);
            z-index: 5;
            /* Higher than right door but lower than button */
        }

        .paper-door.left::before {
            background: linear-gradient(90deg,
                    rgba(97, 64, 81, 0.02) 0%,
                    rgba(97, 64, 81, 0.05) 70%,
                    rgba(97, 64, 81, 0.12) 100%);
        }

        .paper-door.right {
            right: 0;
            transform-origin: right center;
            border-left: 1px solid rgba(109, 76, 12, 0.15);
            background: linear-gradient(90deg, #F5F0E6 0%, #FDFBF7 20%, #ffffff 100%);
            z-index: 1;
            /* Lower z-index so button appears above it */
        }

        .paper-door.right::before {
            background: linear-gradient(90deg,
                    rgba(97, 64, 81, 0.12) 0%,
                    rgba(97, 64, 81, 0.05) 30%,
                    rgba(97, 64, 81, 0.02) 100%);
        }

        .paper-doors.opening .paper-door.left {
            transform: rotateY(-120deg);
            box-shadow:
                -20px 0 40px rgba(0, 0, 0, 0.25),
                0 0 20px rgba(0, 0, 0, 0.12),
                inset 0 0 20px rgba(255, 255, 255, 0.4);
        }

        .paper-doors.opening .paper-door.right {
            transform: rotateY(120deg);
            box-shadow:
                20px 0 40px rgba(0, 0, 0, 0.25),
                0 0 20px rgba(0, 0, 0, 0.12),
                inset 0 0 20px rgba(255, 255, 255, 0.4);
        }

        /* Decorative flourishes on paper */
        .paper-door::after {
            content: '';
            position: absolute;
            top: 50px;
            width: 80px;
            height: 120px;
            opacity: 0.15;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 150'%3E%3Cpath d='M20 20 Q30 10 40 20 Q35 30 30 40 Q25 35 20 30 Z' fill='%23666' /%3E%3Cpath d='M50 60 Q60 50 70 60 Q65 70 60 80 Q55 75 50 70 Z' fill='%23666' /%3E%3Cpath d='M15 100 Q25 90 35 100 Q30 110 25 120 Q20 115 15 110 Z' fill='%23666' /%3E%3C/svg%3E");
            background-size: contain;
            background-repeat: no-repeat;
        }

        .paper-door.left::after {
            right: 30px;
        }

        .paper-door.right::after {
            left: 30px;
        }

        .center-button {
            position: absolute;
            top: 50%;
            right: -120px;
            /* Position it to overlap the center line more */
            transform: translateY(-50%);
            width: 260px;
            height: 170px;
            background: linear-gradient(145deg, #ffffff 0%, #FDFBF7 100%);
            border-radius: 85px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            z-index: 10;
            /* Higher z-index to appear above right paper */
            box-shadow:
                0 12px 40px rgba(109, 76, 12, 0.15),
                0 6px 20px rgba(109, 76, 12, 0.1),
                inset 0 1px 0 rgba(255, 255, 255, 0.9);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            border: 2px solid rgba(109, 76, 12, 0.1);
        }

        .center-button:hover {
            transform: translateY(-50%) scale(1.08);
            box-shadow:
                0 16px 50px rgba(109, 76, 12, 0.2),
                0 8px 25px rgba(109, 76, 12, 0.15),
                inset 0 1px 0 rgba(255, 255, 255, 1);
            border-color: var(--primary);
        }

        .paper-doors.opening .center-button {
            /* Keep button visible and let it follow the left paper naturally */
            opacity: 1;
            transform: translateY(-50%) scale(1);
        }

        .center-button .couple-names {
            font-family: 'Great Vibes', cursive;
            font-size: 2.4rem;
            color: var(--primary);
            margin-bottom: 10px;
            text-align: center;
            line-height: 1.2;
            text-shadow: 0 2px 4px rgba(109, 76, 12, 0.1);
        }

        .center-button .buka-text {
            font-size: 0.95rem;
            color: var(--primary-light);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 4px;
        }

        /* Main content starts after paper doors */
        .main-content {
            position: relative;
            z-index: 100;
            background: var(--secondary);
            min-height: 100vh;
            opacity: 0;
            visibility: hidden;
            transition: all 0.8s ease;
        }

        .main-content.visible {
            opacity: 1;
            visibility: visible;
        }

        /* Floating Menu CSS */
        .floating-menu {
            position: fixed;
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
            background-color: rgba(255, 255, 255, 0.95);
            border-radius: 30px;
            box-shadow: var(--shadow-lg);
            padding: 18px 12px;
            z-index: 200;
            display: flex;
            flex-direction: column;
            align-items: center;
            border: 1px solid rgba(109, 76, 12, 0.15);
            backdrop-filter: blur(10px);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            opacity: 0;
            visibility: hidden;
        }

        .floating-menu.visible {
            opacity: 1;
            visibility: visible;
        }

        .menu-item {
            margin: 10px 0;
            width: 44px;
            height: 44px;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            color: var(--primary);
            background-color: white;
            border: 2px solid rgba(109, 76, 12, 0.15);
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: var(--shadow-sm);
        }

        .menu-item:hover {
            transform: scale(1.15);
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            border-color: var(--primary-dark);
            box-shadow: var(--shadow-md);
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
            padding: 13px 28px;
            border: 2px solid var(--primary);
            border-radius: 30px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            background-color: white;
            color: var(--primary);
            box-shadow: var(--shadow-sm);
        }

        .gift-amount:hover,
        .gift-amount.selected {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
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
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            padding: 14px 45px;
            border: none;
            border-radius: 30px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-top: 10px;
            box-shadow: var(--shadow-md);
        }

        .give-btn:hover {
            background: linear-gradient(135deg, var(--primary-dark) 0%, var(--primary) 100%);
            transform: translateY(-3px);
            box-shadow: var(--shadow-lg);
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
            text-decoration: none;
            display: inline-block;
        }

        .logo-image {
            height: 50px;
            width: auto;
            max-width: 200px;
            object-fit: contain;
            transition: transform 0.3s ease;
        }

        .logo:hover .logo-image {
            transform: scale(1.05);
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

            0%,
            20%,
            50%,
            80%,
            100% {
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
            background: linear-gradient(135deg, rgba(109, 76, 12, 0.05), rgba(253, 251, 247, 0.95));
            padding: 90px 0;
            border-top: 1px solid rgba(109, 76, 12, 0.15);
            border-bottom: 1px solid rgba(109, 76, 12, 0.15);
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
            background: rgba(255, 255, 255, 0.95);
            border: 2px solid var(--primary);
            border-radius: 20px;
            padding: 2.5rem 1.8rem;
            min-width: 130px;
            box-shadow: var(--shadow-md);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
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
            background: linear-gradient(90deg, transparent, rgba(109, 76, 12, 0.08), transparent);
            transition: left 0.7s ease;
        }

        .countdown-item:hover::before {
            left: 100%;
        }

        .countdown-item:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: var(--shadow-lg);
            border-color: var(--primary-dark);
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
            background: linear-gradient(rgba(245, 240, 243, 0.1), rgba(245, 240, 243, 0.15)),
            url('{{ $details["background_image"] ?? "/asset/background/background.png" }}') center/cover;
            background-size: cover;
            background-attachment: fixed;
        }

        .parallax-bg {
            /* Simplified - kept as optional overlay layer if needed */
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
            pointer-events: none;
        }

        .background-sections-wrapper::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(245, 240, 243, 0.05);
            z-index: 1;
            pointer-events: none;
        }

        .background-sections-wrapper>section {
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
            background: rgba(109, 76, 12, 0.6);
            border-radius: 50% 0 50% 0;
            transform-origin: center;
            animation: float-down linear infinite;
            opacity: 0;
        }

        .petal:nth-child(odd) {
            background: rgba(184, 134, 11, 0.7);
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
            color: rgba(109, 76, 12, 0.4);
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
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            will-change: transform;
            z-index: -1;
        }

        /* Responsive wedding card background images */
        .hero-parallax-bg::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url('/asset/background/front_page_vertical_2.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }

        /* Horizontal/landscape orientation (desktop/laptop) */
        @media (orientation: landscape) {
            .hero-parallax-bg::before {
                background-image: url('/asset/background/front_page_horizontal_2.jpg');
            }
        }

        /* Parents Section */
        .parents {
            height: 90vh;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: var(--text-light);
            position: relative;
            margin: 0;
            padding: 0;
            background-image: url('/asset/background/second_page_vertical_2.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
        }

        /* Horizontal/landscape orientation (desktop/laptop) */
        @media (orientation: landscape) {
            .parents {
                height: 120vh;
                background-image: url('/asset/background/second_page_horizontal_2.jpg');
            }
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
            background-color: rgba(253, 251, 247, 0.1);
            backdrop-filter: blur(0px);
            border-top: 1px solid rgba(109, 76, 12, 0.2);
            border-bottom: 1px solid rgba(109, 76, 12, 0.2);
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
            border: 3px solid var(--primary);
            border-radius: 50%;
            overflow: hidden;
            margin-bottom: 1.5rem;
            width: 320px;
            height: 320px;
            margin: 0 auto 1.5rem;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.95), rgba(253, 251, 247, 0.8));
            box-shadow: var(--shadow-lg);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .photo-frame:hover {
            transform: scale(1.05);
            box-shadow: 0 15px 50px rgba(109, 76, 12, 0.25);
            border-color: var(--primary-dark);
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
            /* color: #777; */
            color: #000000;
            /* font-family: 'Times New Roman', serif; */
            /* font-size: 1.2rem;
            font-weight: 400;
            line-height: 1.5;
            text-align: justify;
            text-indent: 20px;
            margin-bottom: 1rem; */
        }

        /* Events Section */
        .events {
            background-color: rgba(253, 251, 247, 0.3);
            backdrop-filter: blur(0px);
            border-bottom: 1px solid rgba(109, 76, 12, 0.2);
        }

        /* Malaysian Wedding Card Styles */
        .malaysian-wedding-card {
            max-width: 750px;
            margin: 0 auto;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.98), rgba(253, 251, 247, 0.95));
            border-radius: 25px;
            padding: 3.5rem;
            box-shadow: var(--shadow-lg);
            position: relative;
            overflow: hidden;
            backdrop-filter: blur(15px);
            border: 3px solid var(--primary);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .malaysian-wedding-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 60px rgba(109, 76, 12, 0.2);
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

        .card-section:nth-child(1) {
            animation-delay: 0.2s;
        }

        .card-section:nth-child(2) {
            animation-delay: 0.4s;
        }

        .card-section:nth-child(3) {
            animation-delay: 0.6s;
        }

        .card-section:nth-child(4) {
            animation-delay: 0.8s;
        }

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
            background: rgba(253, 251, 247, 0.5);
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
            background: linear-gradient(135deg, rgba(253, 251, 247, 0.8), rgba(255, 255, 255, 0.9));
            border-radius: 15px;
            padding: 2rem;
            margin: 2rem 0;
            border: 1px solid rgba(109, 76, 12, 0.3);
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

        .directions-container {
            margin-top: 1rem;
        }

        .directions-btn {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 0.85rem 1.8rem;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: #fff;
            border-radius: 30px;
            text-decoration: none;
            font-family: 'Montserrat', sans-serif;
            font-weight: 600;
            font-size: 0.95rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: var(--shadow-md);
            border: 2px solid var(--primary-dark);
        }

        .directions-btn:hover {
            background: linear-gradient(135deg, var(--primary-dark) 0%, var(--primary) 100%);
            color: #fff;
            transform: translateY(-3px);
            box-shadow: var(--shadow-lg);
        }

        .directions-btn i {
            font-size: 1rem;
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
            background: rgba(253, 251, 247, 0.8);
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
            background: rgba(253, 251, 247, 0.6);
            border-radius: 12px;
            border-left: 5px solid var(--primary);
            transition: all 0.3s ease;
            position: relative;
        }

        .contact-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            background: rgba(253, 251, 247, 0.8);
        }

        .contact-name {
            font-weight: 700;
            color: var(--primary);
            flex: 1;
            text-align: left;
            font-family: 'Montserrat', sans-serif;
            font-size: 0.95rem;
        }

        .contact-number {
            font-weight: 700;
            color: rgba(0, 0, 0, 0.2) font-family: 'Montserrat', sans-serif;
            font-size: 0.95rem;
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
            border-bottom: 1px solid rgba(109, 76, 12, 0.2);
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

        /* Messages Section */
        .messages-section {
            margin-bottom: 4rem;
            text-align: center;
            position: relative;
        }

        .messages-container {
            max-width: 900px;
            margin: 0 auto;
            background: linear-gradient(135deg,
                    rgba(255, 255, 255, 0.25) 0%,
                    rgba(255, 255, 255, 0.15) 50%,
                    rgba(255, 255, 255, 0.05) 100%);
            border-radius: 30px;
            box-shadow:
                0 8px 32px rgba(31, 38, 135, 0.15),
                0 0 1px rgba(255, 255, 255, 0.3) inset;
            border: 1px solid rgba(255, 255, 255, 0.18);
            overflow: hidden;
            backdrop-filter: blur(20px) saturate(180%);
            -webkit-backdrop-filter: blur(20px) saturate(180%);
            position: relative;
        }

        .messages-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg,
                    rgba(109, 76, 12, 0.08) 0%,
                    rgba(184, 134, 11, 0.05) 50%,
                    rgba(109, 76, 12, 0.02) 100%);
            pointer-events: none;
        }

        .messages-scroll {
            max-height: 600px;
            overflow-y: auto;
            padding: 2.5rem;
            scrollbar-width: thin;
            scrollbar-color: rgba(109, 76, 12, 0.5) transparent;
            position: relative;
            z-index: 1;
        }

        .messages-scroll::-webkit-scrollbar {
            width: 6px;
        }

        .messages-scroll::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 8px;
        }

        .messages-scroll::-webkit-scrollbar-thumb {
            background: linear-gradient(180deg, var(--primary), rgba(109, 76, 12, 0.7));
            border-radius: 8px;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .messages-scroll::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(180deg, var(--primary-dark), var(--primary));
        }

        .message-item {
            background: linear-gradient(135deg,
                    rgba(255, 255, 255, 0.4) 0%,
                    rgba(255, 255, 255, 0.2) 100%);
            border-radius: 20px;
            padding: 0;
            margin-bottom: 2rem;
            text-align: center;
            animation: messageSlideIn 0.6s ease forwards;
            opacity: 0;
            transform: translateY(30px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow:
                0 8px 25px rgba(0, 0, 0, 0.1),
                0 0 1px rgba(255, 255, 255, 0.5) inset;
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            overflow: hidden;
            position: relative;
        }

        .message-item::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg,
                    rgba(109, 76, 12, 0.1) 0%,
                    rgba(184, 134, 11, 0.05) 50%,
                    rgba(255, 255, 255, 0.1) 100%);
            pointer-events: none;
        }

        .message-item:nth-child(even) {
            background: linear-gradient(135deg,
                    rgba(245, 240, 243, 0.4) 0%,
                    rgba(255, 255, 255, 0.3) 100%);
        }

        .message-item:nth-child(even)::before {
            background: linear-gradient(135deg,
                    rgba(184, 134, 11, 0.08) 0%,
                    rgba(109, 76, 12, 0.06) 50%,
                    rgba(255, 255, 255, 0.1) 100%);
        }

        .message-item:last-child {
            margin-bottom: 0;
        }

        .message-content {
            padding: 2rem 2.5rem 1.5rem;
            position: relative;
            z-index: 1;
        }

        .message-text {
            font-family: 'Montserrat', sans-serif;
            font-size: 1.1rem;
            line-height: 1.7;
            color: rgba(51, 51, 51, 0.9);
            font-weight: 400;
            font-style: italic;
            margin-bottom: 1.5rem;
            text-align: center;
            letter-spacing: 0.3px;
        }

        .message-author {
            font-family: 'Playfair Display', serif;
            font-size: 1.3rem;
            font-weight: 600;
            color: var(--primary-dark);
            text-align: right;
            margin-bottom: 0.5rem;
            position: relative;
        }

        .message-author::before {
            content: '—';
            margin-right: 0.5rem;
            opacity: 0.6;
        }

        .message-date {
            font-family: 'Montserrat', sans-serif;
            font-size: 0.8rem;
            color: rgba(102, 102, 102, 0.7);
            text-align: right;
            font-weight: 300;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        .no-messages {
            padding: 4rem 2rem;
            text-align: center;
            color: rgba(102, 102, 102, 0.8);
            position: relative;
            z-index: 1;
        }

        .no-messages-icon {
            font-size: 4rem;
            background: linear-gradient(135deg, var(--primary), #f39c12);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 1.5rem;
            animation: heartbeat 2s infinite;
            display: inline-block;
        }

        .no-messages h3 {
            color: var(--primary-dark);
            margin-bottom: 1rem;
            font-family: 'Playfair Display', serif;
            font-size: 1.8rem;
            font-weight: 600;
        }

        .no-messages p {
            color: rgba(102, 102, 102, 0.7);
            font-size: 1rem;
            font-family: 'Montserrat', sans-serif;
            line-height: 1.6;
            font-weight: 300;
            letter-spacing: 0.3px;
        }

        @keyframes messageSlideIn {
            0% {
                opacity: 0;
                transform: translateY(30px) scale(0.95);
            }

            100% {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        @keyframes heartbeat {

            0%,
            50%,
            100% {
                transform: scale(1);
            }

            25%,
            75% {
                transform: scale(1.1);
            }
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-5px);
            }
        }

        /* Floating animation for messages */
        .message-item:hover {
            transform: translateY(-3px);
            box-shadow:
                0 12px 35px rgba(0, 0, 0, 0.15),
                0 0 1px rgba(255, 255, 255, 0.6) inset;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Auto-scroll animation for messages */
        .messages-scroll.auto-scrolling {
            animation: autoScroll 20s linear infinite;
        }

        @keyframes autoScroll {
            0% {
                scroll-behavior: smooth;
            }

            50% {
                scroll-behavior: smooth;
            }

            100% {
                scroll-behavior: smooth;
            }
        }

        /* Responsive design for messages */
        @media (max-width: 768px) {
            .messages-container {
                max-width: 95%;
                margin: 0 auto;
                border-radius: 25px;
            }

            .messages-scroll {
                padding: 1.5rem;
                max-height: 480px;
            }

            .message-content {
                padding: 1.5rem 1.8rem 1.2rem;
            }

            .message-text {
                font-size: 1rem;
                line-height: 1.6;
            }

            .message-author {
                font-size: 1.1rem;
            }

            .message-date {
                font-size: 0.75rem;
            }

            .no-messages {
                padding: 3rem 1.5rem;
            }

            .no-messages-icon {
                font-size: 3rem;
            }

            .no-messages h3 {
                font-size: 1.5rem;
            }
        }

        @media (max-width: 480px) {
            .messages-scroll {
                padding: 1rem;
            }

            .message-content {
                padding: 1.2rem 1.5rem 1rem;
            }

            .message-text {
                font-size: 0.95rem;
            }

            .message-author {
                font-size: 1rem;
            }
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
            background: linear-gradient(to top, rgba(0, 0, 0, 0.7) 0%, transparent 50%);
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

        .rsvp .section-title::after {
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.8), transparent);
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
            padding: 1.1rem 1.5rem;
            border: 2px solid rgba(109, 76, 12, 0.2);
            border-radius: 15px;
            background-color: rgba(255, 255, 255, 0.95);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            font-size: 1rem;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(109, 76, 12, 0.1);
            background-color: white;
        }

        .btn {
            display: inline-block;
            padding: 1.1rem 2.5rem;
            border: none;
            border-radius: 30px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 2px;
            cursor: pointer;
            transition: var(--transition);
            box-shadow: var(--shadow-md);
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: var(--text-light);
            border: 2px solid transparent;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, var(--primary-dark) 0%, var(--primary) 100%);
            transform: translateY(-3px);
            box-shadow: var(--shadow-lg);
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
            background-color: #2E1D02;
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
            .center-button {
                width: 200px;
                height: 120px;
                right: -100px;
                border-radius: 60px;
            }

            .center-button .couple-names {
                font-size: 1.8rem;
            }

            .center-button .buka-text {
                font-size: 0.8rem;
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

        /* Background Music Player Styles */
        .music-player {
            position: fixed;
            bottom: 20px;
            left: 20px;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 25px;
            padding: 15px 20px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
            z-index: 1500;
            display: flex;
            align-items: center;
            gap: 15px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(97, 64, 81, 0.3);
            transition: all 0.4s ease;
            transform: translateY(0);
            opacity: 0;
            visibility: hidden;
            min-width: 320px;
        }

        .music-player.visible {
            opacity: 1;
            visibility: visible;
        }

        .music-player.minimized {
            transform: translateY(calc(100% - 60px));
            min-width: 60px;
            padding: 15px;
        }

        .music-player.minimized .player-info,
        .music-player.minimized .player-controls,
        .music-player.minimized .volume-control {
            display: none;
        }

        .player-toggle {
            cursor: pointer;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--primary);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            flex-shrink: 0;
        }

        .player-toggle:hover {
            background: var(--primary-dark);
            transform: scale(1.1);
        }

        .player-info {
            flex: 1;
            min-width: 0;
        }

        .song-title {
            font-weight: 600;
            font-size: 0.9rem;
            color: var(--text-dark);
            margin-bottom: 2px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .song-artist {
            font-size: 0.8rem;
            color: #666;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .player-controls {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .control-btn {
            background: none;
            border: none;
            cursor: pointer;
            padding: 8px;
            border-radius: 50%;
            color: var(--primary);
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 36px;
        }

        .control-btn:hover {
            background: rgba(97, 64, 81, 0.1);
            transform: scale(1.1);
        }

        .control-btn.play-pause {
            background: var(--primary);
            color: white;
        }

        .control-btn.play-pause:hover {
            background: var(--primary-dark);
        }

        .volume-control {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-left: 10px;
        }

        .volume-slider {
            width: 60px;
            height: 4px;
            background: #ddd;
            border-radius: 2px;
            outline: none;
            cursor: pointer;
            -webkit-appearance: none;
            appearance: none;
        }

        .volume-slider::-webkit-slider-thumb {
            -webkit-appearance: none;
            appearance: none;
            width: 16px;
            height: 16px;
            background: var(--primary);
            border-radius: 50%;
            cursor: pointer;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
        }

        .volume-slider::-moz-range-thumb {
            width: 16px;
            height: 16px;
            background: var(--primary);
            border-radius: 50%;
            cursor: pointer;
            border: none;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
        }

        .progress-bar {
            width: 100%;
            height: 3px;
            background: #ddd;
            border-radius: 2px;
            margin: 8px 0 4px 0;
            cursor: pointer;
            position: relative;
        }

        .progress-fill {
            height: 100%;
            background: var(--primary);
            border-radius: 2px;
            width: 0%;
            transition: width 0.3s ease;
        }

        .time-display {
            display: flex;
            justify-content: space-between;
            font-size: 0.75rem;
            color: #888;
            margin-top: 2px;
        }

        .playlist-modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            z-index: 2000;
            display: none;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .playlist-modal.active {
            display: flex;
            opacity: 1;
        }

        .playlist-content {
            background: white;
            border-radius: 15px;
            padding: 25px;
            width: 90%;
            max-width: 500px;
            max-height: 70vh;
            overflow-y: auto;
            position: relative;
        }

        .playlist-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }

        .playlist-title {
            font-family: 'Playfair Display', serif;
            font-size: 1.5rem;
            color: var(--primary);
            margin: 0;
        }

        .playlist-close {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: #666;
            transition: color 0.3s ease;
        }

        .playlist-close:hover {
            color: var(--primary);
        }

        .song-item {
            display: flex;
            align-items: center;
            padding: 12px;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-bottom: 8px;
        }

        .song-item:hover {
            background: rgba(97, 64, 81, 0.1);
        }

        .song-item.active {
            background: rgba(97, 64, 81, 0.15);
            border-left: 4px solid var(--primary);
        }

        .song-item-info {
            flex: 1;
            margin-left: 12px;
        }

        .song-item-title {
            font-weight: 600;
            font-size: 0.9rem;
            color: var(--text-dark);
            margin-bottom: 2px;
        }

        .song-item-artist {
            font-size: 0.8rem;
            color: #666;
        }

        .song-duration {
            font-size: 0.8rem;
            color: #888;
            margin-left: 12px;
        }

        /* Mobile Responsiveness for Music Player */
        @media (max-width: 768px) {
            .music-player {
                left: 10px;
                bottom: 10px;
                min-width: 280px;
                padding: 12px 15px;
            }

            .volume-control {
                display: none;
            }

            .playlist-content {
                width: 95%;
                padding: 20px;
                margin: 10px;
            }

            .song-item {
                padding: 10px;
            }
        }

        @media (max-width: 576px) {
            .music-player {
                min-width: 250px;
                padding: 10px 12px;
            }

            .player-toggle {
                width: 36px;
                height: 36px;
            }

            .control-btn {
                width: 32px;
                height: 32px;
                padding: 6px;
            }

            .song-title {
                font-size: 0.85rem;
            }

            .song-artist {
                font-size: 0.75rem;
            }
        }

        /* Audio fade effects */
        .audio-fade-in {
            animation: audioFadeIn 2s ease-in-out;
        }

        .audio-fade-out {
            animation: audioFadeOut 1s ease-in-out;
        }

        @keyframes audioFadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        @keyframes audioFadeOut {
            from {
                opacity: 1;
            }

            to {
                opacity: 0;
            }
        }

        /* Minimized player styles */
        .music-player.minimized .player-toggle {
            position: relative;
        }

        .music-player.minimized .player-toggle::after {
            content: '';
            position: absolute;
            top: -5px;
            right: -5px;
            width: 8px;
            height: 8px;
            background: #28a745;
            border-radius: 50%;
            border: 2px solid white;
            display: block;
        }

        /* Mini-player circular override and pulse when playing */
        .music-player.minimized {
            transform: none;
            min-width: 0;
            width: 56px;
            height: 56px;
            padding: 0;
            border-radius: 50%;
            gap: 0;
        }

        .music-player.minimized .player-toggle {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            background: var(--primary);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .music-player.minimized.playing .player-toggle::before,
        .music-player.minimized.playing .player-toggle::after {
            content: '';
            position: absolute;
            inset: 0;
            border-radius: 50%;
            border: 2px solid var(--primary);
            animation: player-pulse 1.8s ease-out infinite;
            opacity: 0.5;
        }

        .music-player.minimized.playing .player-toggle::after {
            animation-delay: 0.9s;
        }

        @keyframes player-pulse {
            0% {
                transform: scale(1);
                opacity: 0.6;
            }

            70% {
                transform: scale(1.8);
                opacity: 0.15;
            }

            100% {
                transform: scale(2.2);
                opacity: 0;
            }
        }

        /* Equalizer animation for playing state */
        .equalizer {
            display: flex;
            align-items: flex-end;
            gap: 2px;
            margin-right: 8px;
        }

        .eq-bar {
            width: 3px;
            height: 4px;
            background: var(--primary);
            border-radius: 1px;
            animation: equalizer 1s ease-in-out infinite;
        }

        .eq-bar:nth-child(2) {
            animation-delay: 0.2s;
        }

        .eq-bar:nth-child(3) {
            animation-delay: 0.4s;
        }

        @keyframes equalizer {

            0%,
            100% {
                height: 4px;
            }

            50% {
                height: 12px;
            }
        }
    </style>
</head>

<body>
    <!-- Floating Petals Animation -->
    <div class="floating-petals" id="floatingPetals">
        <!-- Petals will be generated by JavaScript -->
    </div>

    <!-- Floating Sparkles Animation -->
    <div class="floating-sparkles" id="floatingSparkles">
        <!-- Sparkles will be generated by JavaScript -->
    </div>

    <!-- Background Music Player -->
    <div class="music-player" id="musicPlayer">
        <div class="player-toggle" onclick="togglePlayer()">
            <i class="fas fa-music" id="playerToggleIcon"></i>
        </div>

        <div class="player-info">
            <div class="song-title" id="currentSongTitle">Beautiful Wedding Song</div>
            <div class="song-artist" id="currentSongArtist">Wedding Music</div>
            <div class="progress-bar" onclick="seekToPosition(event)">
                <div class="progress-fill" id="progressFill"></div>
            </div>
            <div class="time-display">
                <span id="currentTime">0:00</span>
                <span id="totalTime">0:00</span>
            </div>
        </div>

        <div class="player-controls">
            <button class="control-btn" onclick="previousSong()" title="Previous">
                <i class="fas fa-step-backward"></i>
            </button>
            <button class="control-btn play-pause" onclick="togglePlayPause()" id="playPauseBtn" title="Play/Pause">
                <i class="fas fa-play" id="playPauseIcon"></i>
            </button>
            <button class="control-btn" onclick="nextSong()" title="Next">
                <i class="fas fa-step-forward"></i>
            </button>
            <button class="control-btn" onclick="openPlaylist()" title="Playlist">
                <i class="fas fa-list"></i>
            </button>
        </div>

        <div class="volume-control">
            <i class="fas fa-volume-up" id="volumeIcon"></i>
            <input type="range" class="volume-slider" id="volumeSlider" min="0" max="100" value="30" onchange="adjustVolume(this.value)">
        </div>
    </div>

    <!-- Playlist Modal -->
    <div class="playlist-modal" id="playlistModal">
        <div class="playlist-content">
            <div class="playlist-header">
                <h3 class="playlist-title">Wedding Playlist</h3>
                <button class="playlist-close" onclick="closePlaylist()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="playlist-songs" id="playlistSongs">
                <!-- Songs will be populated by JavaScript -->
            </div>
        </div>
    </div>

    <!-- Audio elements (hidden) -->
    <audio id="audioPlayer" preload="auto">
        Your browser does not support the audio element.
    </audio>

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
            <span class="menu-tooltip">Pasangan</span>
        </div>
        <div class="menu-item" onclick="scrollToSection('events')">
            <i class="fas fa-calendar-alt"></i>
            <span class="menu-tooltip">Acara</span>
        </div>
        <div class="menu-item" onclick="scrollToSection('gallery')">
            <i class="fas fa-comments"></i>
            <span class="menu-tooltip">Ucapan</span>
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

    <!-- Paper Door Animation Container -->
    <div class="paper-container" id="paperContainer">
        <div class="paper-doors" id="paperDoors">
            <div class="paper-door left">
                <div class="center-button" id="centerButton">
                    <div class="couple-names">
                        <img src="/asset/background/PH_final.png" alt="PH" style="max-width: 200px; width: 50%; height: auto; display: inline-block;">
                    </div>
                    <div class="buka-text">BUKA</div>
                </div>
            </div>
            <div class="paper-door right"></div>
        </div>
    </div>

    <!-- Fullscreen Video Section -->
    <div class="video-section" id="videoSection">
        <!-- Horizontal Video for Desktop/Laptop -->
        <video
            id="weddingVideoHorizontal"
            class="fullscreen-video video-horizontal"
            muted
            preload="auto"
            playsinline
            autoplay
            style="display: none;"
            poster="{{ $details['video_poster_horizontal'] ?? $details['video_poster'] ?? 'https://images.unsplash.com/photo-1519225421980-715cb0215aed?ixlib=rb-4.0.3&w=1920&h=1080&fit=crop' }}">
            <source src="{{ $details['wedding_invitation_video_horizontal'] ?? $details['wedding_invitation_video'] ?? 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/BigBuckBunny.mp4' }}" type="video/mp4">
            Your browser does not support the video tag.
        </video>

        <!-- Vertical Video for Mobile -->
        <video
            id="weddingVideoVertical"
            class="fullscreen-video video-vertical"
            muted
            preload="auto"
            playsinline
            autoplay
            style="display: none;"
            poster="{{ $details['video_poster_vertical'] ?? $details['video_poster'] ?? 'https://images.unsplash.com/photo-1519225421980-715cb0215aed?ixlib=rb-4.0.3&w=1920&h=1080&fit=crop' }}">
            <source src="{{ $details['wedding_invitation_video_vertical'] ?? $details['wedding_invitation_video'] ?? 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/BigBuckBunny.mp4' }}" type="video/mp4">
            Your browser does not support the video tag.
        </video>

        <!-- Fallback Play Button (shown if autoplay fails) -->
        <div class="fallback-play-button" id="fallbackPlayBtn" onclick="playVideoFallback()" style="display: none;">
            <svg viewBox="0 0 24 24">
                <path d="M8,5.14V19.14L19,12.14L8,5.14Z" />
            </svg>
        </div>

        <!-- Video Overlay Content -->
        {{-- <div class="video-overlay-content">
            <h1 class="video-title-overlay">{{ $details["groom_1_name"] ?? "Groom 1" }} & {{ $details["bride_1_name"] ?? "Bride 1" }} and {{ $details["groom_2_name"] ?? "Groom 2" }} & {{ $details["bride_2_name"] ?? "Bride 2" }}</h1>
        <p class="video-subtitle-overlay">{{ $details["video_subtitle"] ?? "A Special Message for You" }}</p>
    </div> --}}

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
                <a href="#hero" class="logo" onclick="scrollToSection('hero'); return false;">
                    <img src="/asset/background/PH_final.png" alt="Wedding Logo" class="logo-image">
                </a>
                <nav>
                    <ul class="nav-links">
                        <li><a href="#hero" onclick="scrollToSection('hero'); return false;">Home</a></li>
                        <li><a href="#countdown" onclick="scrollToSection('countdown'); return false;">Countdown</a></li>
                        <li><a href="#couple" onclick="scrollToSection('couple'); return false;">Pasangan</a></li>
                        <li><a href="#events" onclick="scrollToSection('events'); return false;">Acara</a></li>
                        <li><a href="#gallery" onclick="scrollToSection('gallery'); return false;">Ucapan</a></li>
                        <li><a href="#rsvp" onclick="scrollToSection('rsvp'); return false;">RSVP</a></li>
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

            <div class="scroll-down">
                <a href="#couple">
                    <svg viewBox="0 0 24 24">
                        <path d="M7.41,8.58L12,13.17L16.59,8.58L18,10L12,16L6,10L7.41,8.58Z" />
                    </svg>
                </a>
            </div>
        </section>

        <!-- Parents Section -->
        <section id="parents" class="parents section-reveal">
            <div class="scroll-down">
                <a href="#countdown">
                    <svg viewBox="0 0 24 24">
                        <path d="M7.41,8.58L12,13.17L16.59,8.58L18,10L12,16L6,10L7.41,8.58Z" />
                    </svg>
                </a>
            </div>
        </section>

        <!-- Countdown Timer Section -->
        <section id="countdown" class="countdown section-reveal">
            <div class="container">
                <div class="countdown-container fade-in">
                    <h2 class="countdown-title">Menghitung Hari Kesyukuran</h2>
                    <p class="countdown-subtitle">Mari bersama meraikan detik-detik manis majlis persandingan kami</p>

                    <div class="countdown-timer" id="countdownTimer">
                        <div class="countdown-item">
                            <span class="countdown-number" id="days">00</span>
                            <span class="countdown-label">Hari</span>
                        </div>
                        <div class="countdown-item">
                            <span class="countdown-number" id="hours">00</span>
                            <span class="countdown-label">Jam</span>
                        </div>
                        <div class="countdown-item">
                            <span class="countdown-number" id="minutes">00</span>
                            <span class="countdown-label">Menit</span>
                        </div>
                        <div class="countdown-item">
                            <span class="countdown-number" id="seconds">00</span>
                            <span class="countdown-label">Detik</span>
                        </div>
                    </div>

                    <div class="countdown-expired" id="countdownExpired">
                        <h3>Terima Kasih Atas Kehadiran Anda!</h3>
                        <p>Majlis persandingan kami telah berlangsung dengan jayanya</p>
                    </div>

                    <p class="countdown-message">Tidak sabar untuk berkongsi kegembiraan bersama Anda!</p>
                </div>
            </div>
        </section>

        <!-- Background Sections Wrapper -->
        <div class="background-sections-wrapper">
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
                    <h2 class="section-title fade-in">Pasangan Kami</h2>

                    <!-- Couple 1 -->
                    <h3 class="section-subtitle fade-in" style="text-align: center; font-family: 'Playfair Display', serif; font-size: 1.5rem; margin-bottom: 2rem; color: var(--primary);">Pasangan Pertama</h3>
                    <div class="couple-photos">
                        <div class="couple-photo fade-in-left">
                            <div class="photo-frame">
                                @if($details["groom_1_photo"] ?? false)
                                <img src="{{ $details['groom_1_photo'] }}" alt="Groom 1">
                                @else
                                <div class="photo-placeholder">
                                    <i class="fas fa-user"></i>
                                </div>
                                @endif
                            </div>
                            <h3 class="couple-name">{{ $details["groom_1_full_name"] ?? $details["groom_1_name"] ?? "Groom 1 Full Name" }}</h3>
                            <div class="separator">
                                <div class="separator-line"></div>
                                <svg viewBox="0 0 24 24">
                                    <path d="M12,21.35L10.55,20.03C5.4,15.36 2,12.27 2,8.5C2,5.41 4.42,3 7.5,3C9.24,3 10.91,3.81 12,5.08C13.09,3.81 14.76,3 16.5,3C19.58,3 22,5.41 22,8.5C22,12.27 18.6,15.36 13.45,20.03L12,21.35Z" />
                                </svg>
                                <div class="separator-line"></div>
                            </div>
                        </div>
                        <div class="couple-photo fade-in-right">
                            <div class="photo-frame">
                                @if($details["bride_1_photo"] ?? false)
                                <img src="{{ $details['bride_1_photo'] }}" alt="Bride 1">
                                @else
                                <div class="photo-placeholder">
                                    <i class="fas fa-user"></i>
                                </div>
                                @endif
                            </div>
                            <h3 class="couple-name">{{ $details["bride_1_full_name"] ?? $details["bride_1_name"] ?? "Bride 1 Full Name" }}</h3>
                            <div class="separator">
                                <div class="separator-line"></div>
                                <svg viewBox="0 0 24 24">
                                    <path d="M12,21.35L10.55,20.03C5.4,15.36 2,12.27 2,8.5C2,5.41 4.42,3 7.5,3C9.24,3 10.91,3.81 12,5.08C13.09,3.81 14.76,3 16.5,3C19.58,3 22,5.41 22,8.5C22,12.27 18.6,15.36 13.45,20.03L12,21.35Z" />
                                </svg>
                                <div class="separator-line"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Couple 2 -->
                    <h3 class="section-subtitle fade-in" style="text-align: center; font-family: 'Playfair Display', serif; font-size: 1.5rem; margin: 3rem 0 2rem 0; color: var(--primary);">Pasangan Kedua</h3>
                    <div class="couple-photos">
                        <div class="couple-photo fade-in-left">
                            <div class="photo-frame">
                                @if($details["groom_2_photo"] ?? false)
                                <img src="{{ $details['groom_2_photo'] }}" alt="Groom 2">
                                @else
                                <div class="photo-placeholder">
                                    <i class="fas fa-user"></i>
                                </div>
                                @endif
                            </div>
                            <h3 class="couple-name">{{ $details["groom_2_full_name"] ?? $details["groom_2_name"] ?? "Groom 2 Full Name" }}</h3>
                            <div class="separator">
                                <div class="separator-line"></div>
                                <svg viewBox="0 0 24 24">
                                    <path d="M12,21.35L10.55,20.03C5.4,15.36 2,12.27 2,8.5C2=5.41 4.42,3 7.5,3C9.24,3 10.91,3.81 12,5.08C13.09,3.81 14.76,3 16.5,3C19.58,3 22,5.41 22,8.5C22,12.27 18.6,15.36 13.45,20.03L12,21.35Z" />
                                </svg>
                                <div class="separator-line"></div>
                            </div>
                        </div>
                        <div class="couple-photo fade-in-right">
                            <div class="photo-frame">
                                @if($details["bride_2_photo"] ?? false)
                                <img src="{{ $details['bride_2_photo'] }}" alt="Bride 2">
                                @else
                                <div class="photo-placeholder">
                                    <i class="fas fa-user"></i>
                                </div>
                                @endif
                            </div>
                            <h3 class="couple-name">{{ $details["bride_2_full_name"] ?? $details["bride_2_name"] ?? "Bride 2 Full Name" }}</h3>
                            <div class="separator">
                                <div class="separator-line"></div>
                                <svg viewBox="0 0 24 24">
                                    <path d="M12,21.35L10.55,20.03C5.4,15.36 2,12.27 2,8.5C2,5.41 4.42,3 7.5,3C9.24,3 10.91,3.81 12,5.08C13.09,3.81 14.76,3 16.5,3C19.58,3 22,5.41 22,8.5C22,12.27 18.6,15.36 13.45,20.03L12,21.35Z" />
                                </svg>
                                <div class="separator-line"></div>
                            </div>
                        </div>
                    </div>
                    <div class="doa-selamat fade-in">
                        <div class="arabic-text" style="text-align:center; margin-bottom: 10px;">
                            <img src="/asset/background/bismillah_final.png" alt="بسم الله الرحمن الرحيم" style="max-width: 200px; width: 75%; height: auto; display: inline-block;">
                        </div>
                        <div class="arabic-text">
                            بَارَكَ اللهُ لَكَ وَبَارَكَ عَلَيْكَ وَجَمَعَ بَيْنَكُمَا فِي خَيْرٍ
                        </div>
                        <p class="translation">“Semoga Allah memberkati di waktu bahagia dan memberkati di waktu susah, serta semoga Allah mempersatukan kalian berdua dalam kebaikan”</p>
                        <p class="translation">(Hadis riwayat Abu Daud)</p>
                    </div>
                </div>
            </section>

            <!-- Events Section -->
            <section id="events" class="events section-reveal">
                <div class="container">
                    <h2 class="section-title fade-in">Acara Pernikahan</h2>

                    <!-- Malaysian Wedding Card Format -->
                    <div class="malaysian-wedding-card fade-in">
                        <div class="card-border">
                            <!-- Date Section -->
                            <div class="card-section">
                                <h3 class="card-section-title">TARIKH</h3>
                                <div class="card-content">
                                    <p class="main-date">{{ $details["wedding_date"] ?? "7 Disember 2025, Ahad" }}</p>
                                    <p class="hijri-date">{{ $details["hijri_date"] ?? "Bersamaan 17 Jamadilakhir 1447H" }}</p>
                                </div>
                            </div>

                            <!-- Program Schedule Section -->
                            <div class="card-section">
                                <h3 class="card-section-title">ATURCARA MAJLIS</h3>
                                <div class="card-content">
                                    <div class="program-schedule">
                                        @if($details["reception_meal_time"] ?? false)
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
                                        @if($details["venue_map_link"] ?? false)
                                        <div class="directions-container">
                                            <a href="{{ $details['venue_map_link'] }}" target="_blank" class="directions-btn">
                                                <i class="fas fa-directions"></i>
                                                <span>Get Directions</span>
                                            </a>
                                        </div>
                                        @endif
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
                    <div class="map-container fade-in" style="margin-top: 2.5rem;">
                        <iframe src="{{ $details['venue_map_embed'] }}" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade" style="width: 100%; height: 400px; border: none;"></iframe>
                        <div class="venue-info" style="margin-top: 1rem; padding: 1rem 1.25rem; background: rgba(255,255,255,0.95); border-radius: 12px; box-shadow: 0 8px 24px rgba(0,0,0,0.08); border-left: 3px solid var(--primary);">
                            <h3 style="margin: 0 0 0.6rem; color: var(--primary); font-family: 'Playfair Display', serif; letter-spacing: .5px;">{{ $details["venue"] ?? "Main Venue" }}</h3>
                        </div>
                    </div>
                    @endif
                </div>
            </section>

            <!-- Messages Section -->
            <section id="gallery" class="gallery section-reveal">
                <div class="container">
                    <h2 class="section-title fade-in">Ucapan</h2>

                    <!-- Messages Display Section -->
                    <div class="messages-section fade-in">
                        <div class="messages-container" id="messagesContainer">
                            <div class="messages-scroll" id="messagesScroll">
                                <!-- Messages will be populated by JavaScript -->
                                <div class="no-messages" id="noMessages">
                                    <div class="no-messages-icon">
                                        <i class="fas fa-heart"></i>
                                    </div>
                                    <h3>Be the first to leave a message!</h3>
                                    <p>Share your wishes for the happy couple by submitting your RSVP below.</p>
                                </div>
                            </div>
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
                        <input type="number" name="number_of_guests" class="form-control" placeholder="Number of Guests" min="1" max="2" value="1">
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
                    <h2 class="footer-title">{{ $details["groom_1_name"] ?? "Groom 1" }} & {{ $details["bride_1_name"] ?? "Bride 1" }} and {{ $details["groom_2_name"] ?? "Groom 2" }} & {{ $details["bride_2_name"] ?? "Bride 2" }}</h2>
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
                    <p class="copyright">&copy; {{ date('Y') }} {{ $details["groom_1_name"] ?? "Groom 1" }} & {{ $details["bride_1_name"] ?? "Bride 1" }} and {{ $details["groom_2_name"] ?? "Groom 2" }} & {{ $details["bride_2_name"] ?? "Bride 2" }}. All Rights Reserved.</p>
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

            // Close mobile menu when a navigation link is clicked
            const navLinkItems = navLinks.querySelectorAll('a');
            navLinkItems.forEach(link => {
                link.addEventListener('click', () => {
                    navLinks.classList.remove('active');
                });
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

                            // If there's a message in the form, refresh messages display
                            const messageField = formData.get('message');
                            if (messageField && messageField.trim()) {
                                // Simulate adding the new message to the display
                                // In a real implementation, you might want to reload the page or fetch updated messages
                                setTimeout(() => {
                                    location.reload();
                                }, 2000);
                            }
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
            successDiv.scrollIntoView({
                behavior: 'smooth',
                block: 'center'
            });
        }

        function showErrorMessage(message) {
            const errorDiv = document.getElementById('rsvp-error');
            const messageP = document.getElementById('error-message');
            messageP.textContent = message;
            errorDiv.style.display = 'block';
            errorDiv.scrollIntoView({
                behavior: 'smooth',
                block: 'center'
            });
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

        // RSVP Messages Display Functions
        function initializeMessagesDisplay() {
            const messagesContainer = document.getElementById('messagesContainer');
            const messagesScroll = document.getElementById('messagesScroll');
            const noMessages = document.getElementById('noMessages');

            if (!messagesContainer || !messagesScroll) return;

            // Check if we have messages
            if (window.rsvpMessages && window.rsvpMessages.length > 0) {
                // Hide no messages placeholder
                if (noMessages) noMessages.style.display = 'none';

                // Populate messages
                populateMessages(window.rsvpMessages);

                // Start auto-scroll if there are many messages
                if (window.rsvpMessages.length > 3) {
                    startAutoScroll();
                }
            } else {
                // Show no messages placeholder
                if (noMessages) noMessages.style.display = 'block';
            }
        }

        function populateMessages(messages) {
            const messagesScroll = document.getElementById('messagesScroll');
            if (!messagesScroll) return;

            // Clear existing content except no-messages placeholder
            const noMessages = document.getElementById('noMessages');
            messagesScroll.innerHTML = '';
            if (noMessages && messages.length === 0) {
                messagesScroll.appendChild(noMessages);
                return;
            }

            // Create message items with beautiful formatting
            messages.forEach((message, index) => {
                const messageItem = document.createElement('div');
                messageItem.className = 'message-item';
                messageItem.style.animationDelay = `${index * 0.15}s`;

                messageItem.innerHTML = `
                    <div class="message-content">
                        <div class="message-text">"${escapeHtml(message.message)}"</div>
                        <div class="message-author">${escapeHtml(message.guest_name)}</div>
                        <div class="message-date">${message.created_at}</div>
                    </div>
                `;

                messagesScroll.appendChild(messageItem);
            });
        }

        let autoScrollInterval = null;
        let userScrolling = false;
        let userScrollTimeout = null;

        function startAutoScroll() {
            const messagesScroll = document.getElementById('messagesScroll');
            if (!messagesScroll) return;

            // Clear any existing auto-scroll
            if (autoScrollInterval) {
                clearInterval(autoScrollInterval);
            }

            let scrollDirection = 1;
            let scrollAmount = 0;
            const maxScroll = messagesScroll.scrollHeight - messagesScroll.clientHeight;

            // Add scroll event listener to detect manual scrolling
            messagesScroll.addEventListener('scroll', function() {
                // User is manually scrolling
                userScrolling = true;

                // Clear the auto-scroll interval
                if (autoScrollInterval) {
                    clearInterval(autoScrollInterval);
                    autoScrollInterval = null;
                }

                // Clear existing timeout
                if (userScrollTimeout) {
                    clearTimeout(userScrollTimeout);
                }

                // Resume auto-scroll after 3 seconds of inactivity
                userScrollTimeout = setTimeout(() => {
                    userScrolling = false;
                    // Restart auto-scroll from current position
                    scrollAmount = messagesScroll.scrollTop;
                    startAutoScrollLoop();
                }, 3000);
            }, {
                passive: true
            });

            // Start the auto-scroll loop
            startAutoScrollLoop();

            function startAutoScrollLoop() {
                if (userScrolling) return;

                autoScrollInterval = setInterval(() => {
                    // Stop if user is scrolling
                    if (userScrolling) {
                        clearInterval(autoScrollInterval);
                        autoScrollInterval = null;
                        return;
                    }

                    if (maxScroll <= 0) {
                        clearInterval(autoScrollInterval);
                        autoScrollInterval = null;
                        return;
                    }

                    scrollAmount += scrollDirection * 2;

                    if (scrollAmount >= maxScroll) {
                        scrollDirection = -1;
                        setTimeout(() => {
                            if (!userScrolling) {
                                scrollDirection = 1;
                                scrollAmount = 0;
                            }
                        }, 3000); // Pause at bottom for 3 seconds
                    } else if (scrollAmount <= 0) {
                        scrollDirection = 1;
                        setTimeout(() => {
                            if (!userScrolling) {
                                scrollDirection = 1;
                            }
                        }, 3000); // Pause at top for 3 seconds
                    }

                    // Only set scroll position if user is not scrolling
                    if (!userScrolling) {
                        messagesScroll.scrollTop = scrollAmount;
                    }
                }, 50);
            }
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        // Video and paper doors functionality
        let videoStarted = false;
        let autoplayFailed = false;
        let video = null; // active video element based on orientation

        document.addEventListener('DOMContentLoaded', function() {
            const centerButton = document.getElementById('centerButton');
            const paperDoors = document.getElementById('paperDoors');
            const paperContainer = document.getElementById('paperContainer');
            const floatingMenu = document.querySelector('.floating-menu');
            const videoSection = document.getElementById('videoSection');
            const mainContent = document.getElementById('mainContent');
            const videoHorizontal = document.getElementById('weddingVideoHorizontal');
            const videoVertical = document.getElementById('weddingVideoVertical');
            const fallbackPlayBtn = document.getElementById('fallbackPlayBtn');
            let isOpening = false;

            // Initialize RSVP Messages Display
            initializeMessagesDisplay();

            // Choose video by device orientation
            const chooseVideoByOrientation = () => {
                const isPortrait = (window.matchMedia && window.matchMedia('(orientation: portrait)').matches) ||
                    (window.matchMedia && window.matchMedia('(max-aspect-ratio: 1/1)').matches) ||
                    (window.innerHeight >= window.innerWidth);
                if (videoHorizontal || videoVertical) {
                    if (isPortrait && videoVertical) {
                        video = videoVertical;
                        if (videoHorizontal) videoHorizontal.style.display = 'none';
                        videoVertical.style.display = 'block';
                    } else {
                        video = videoHorizontal || videoVertical;
                        if (videoHorizontal) videoHorizontal.style.display = 'block';
                        if (videoVertical) videoVertical.style.display = 'none';
                    }
                } else {
                    video = document.getElementById('weddingVideo');
                }
            };

            chooseVideoByOrientation();
            // Update selection on orientation change if video not started yet
            const onResizeReevaluate = () => {
                if (!videoStarted) chooseVideoByOrientation();
            };
            window.addEventListener('orientationchange', onResizeReevaluate);
            window.addEventListener('resize', onResizeReevaluate);

            // Video ended handler
            if (video) {
                video.addEventListener('ended', function() {
                    setTimeout(() => {
                        skipToMainContent();
                    }, 2000);
                });
            }

            // Paper doors click handler
            centerButton.addEventListener('click', function() {
                if (isOpening) return;

                console.log('Opening paper doors...');
                isOpening = true;
                paperDoors.classList.add('opening');

                // After paper doors animation completes, show video section
                setTimeout(() => {
                    console.log('Hiding paper container...');
                    paperContainer.classList.add('opened');

                    setTimeout(() => {
                        console.log('Showing video section...');
                        if (videoSection) {
                            videoSection.classList.add('active');
                        }

                        // Start only the selected orientation video
                        if (video) {
                            try {
                                if (videoHorizontal && video !== videoHorizontal) videoHorizontal.pause();
                            } catch (e) {}
                            try {
                                if (videoVertical && video !== videoVertical) videoVertical.pause();
                            } catch (e) {}

                            video.play().then(() => {
                                if (fallbackPlayBtn) fallbackPlayBtn.style.display = 'none';
                                videoStarted = true;
                                console.log('Video started playing');
                            }).catch(() => {
                                autoplayFailed = true;
                                if (fallbackPlayBtn) fallbackPlayBtn.style.display = 'flex';
                                console.log('Video autoplay failed, showing fallback button');
                            });
                        }

                    }, 500);
                }, 2200);
            });

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

            // Enable scrolling from video to main content
            let isScrolling = false;

            function handleScroll() {
                if (isScrolling) return;

                const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
                if (videoSection && videoSection.classList.contains('active')) {
                    const videoRect = videoSection.getBoundingClientRect();

                    // If user scrolls down significantly while video is visible
                    if (videoRect.top < -window.innerHeight * 0.3) {
                        skipToMainContent();
                    }
                }
            }

            // Add scroll listener when video section is active
            document.addEventListener('scroll', handleScroll, {
                passive: true
            });

            // Handle touch scrolling for mobile (when video is active)
            let touchStartY = 0;
            let touchEndY = 0;

            if (videoSection) {
                videoSection.addEventListener('touchstart', function(e) {
                    touchStartY = e.touches[0].clientY;
                }, {
                    passive: true
                });

                videoSection.addEventListener('touchmove', function(e) {
                    touchEndY = e.touches[0].clientY;
                }, {
                    passive: true
                });

                videoSection.addEventListener('touchend', function(e) {
                    const deltaY = touchStartY - touchEndY;

                    if (deltaY > 100) { // Swipe up
                        skipToMainContent();
                    }
                }, {
                    passive: true
                });
            }
        });

        function playVideoFallback() {
            const activeVideo = video || document.getElementById('weddingVideoHorizontal') || document.getElementById('weddingVideoVertical');
            const fallbackPlayBtn = document.getElementById('fallbackPlayBtn');

            if (activeVideo) {
                activeVideo.play().then(() => {
                    if (fallbackPlayBtn) fallbackPlayBtn.style.display = 'none';
                    videoStarted = true;
                    console.log('Video started playing via fallback');
                }).catch(error => {
                    console.log('Video play failed:', error);
                });
            }
        }

        function skipToMainContent() {
            console.log('Transitioning to main content...');
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

                    // Pause all possible videos to save battery/data
                    try {
                        const vh = document.getElementById('weddingVideoHorizontal');
                        const vv = document.getElementById('weddingVideoVertical');
                        if (vh && !vh.paused) vh.pause();
                        if (vv && !vv.paused) vv.pause();
                    } catch (e) {}

                    // Show main content
                    mainContent.classList.add('visible');
                    document.body.classList.add('content-visible');
                    document.body.style.overflow = 'auto';

                    // Show floating menu
                    if (floatingMenu) {
                        setTimeout(() => {
                            floatingMenu.classList.add('visible');
                        }, 500);
                    }

                    // Scroll to hero section
                    const heroSection = document.getElementById('hero');
                    if (heroSection) {
                        heroSection.scrollIntoView({
                            behavior: 'smooth'
                        });
                    }

                    // Trigger scroll animations
                    if (typeof scrollAnimation === 'function') {
                        scrollAnimation();
                    }
                }, 1000);
            }
        }

        // Floating Menu Functions
        function scrollToSection(sectionId) {
            // Find the section anywhere in the document
            const section = document.getElementById(sectionId);
            if (!section) {
                console.error('Section not found:', sectionId);
                return;
            }

            // Get the fixed header height
            const header = document.querySelector('header');
            const headerHeight = header ? header.offsetHeight : 80; // fallback to 80px

            // Calculate the absolute position of the section
            const rect = section.getBoundingClientRect();
            const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
            const sectionTop = rect.top + scrollTop;

            // Calculate target position with offset for header
            const targetPosition = sectionTop - headerHeight - 20;

            // Scroll to the calculated position
            window.scrollTo({
                top: Math.max(0, targetPosition), // Ensure we don't scroll to negative position
                behavior: 'smooth'
            });
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

            // Show loading state
            const sendBtn = document.querySelector('.give-btn');
            const originalText = sendBtn.textContent;
            sendBtn.textContent = 'Processing...';
            sendBtn.disabled = true;

            // Get wedding card ID from URL or set it dynamically
            const urlParts = window.location.pathname.split('/');
            const uniqueUrl = urlParts[urlParts.length - 1];

            // You'll need to pass the wedding_card_id to the template
            // For now, we'll use a global variable or get it from the URL
            const weddingCardId = window.weddingCardId || 1; // Set this in the template

            // Get guest information
            const guestName = prompt('Please enter your full name:');
            if (!guestName) {
                sendBtn.textContent = originalText;
                sendBtn.disabled = false;
                return;
            }

            const guestEmail = prompt('Please enter your email address (required):');
            if (!guestEmail) {
                sendBtn.textContent = originalText;
                sendBtn.disabled = false;
                return;
            }

            const guestPhone = prompt('Please enter your phone number (required):') || '';
            const giftMessage = prompt('Add a message to your gift (optional):') || '';

            const giftData = {
                wedding_card_id: weddingCardId,
                guest_name: guestName,
                guest_email: guestEmail,
                guest_phone: guestPhone,
                amount: selectedAmount,
                message: giftMessage,
                _token: document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            };

            // Send request to create gift payment
            fetch('/gift/create', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': giftData._token
                    },
                    body: JSON.stringify(giftData)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Redirect to toyyibPay payment page
                        window.location.href = data.payment_url;
                    } else {
                        alert('Error: ' + (data.error || 'Failed to create payment. Please try again.'));
                        sendBtn.textContent = originalText;
                        sendBtn.disabled = false;
                    }
                })
                .catch(error => {
                    console.error('Gift payment error:', error);
                    alert('An error occurred. Please try again.');
                    sendBtn.textContent = originalText;
                    sendBtn.disabled = false;
                });
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
                window.addEventListener('scroll', () => this.onScroll(), {
                    passive: true
                });
                window.addEventListener('resize', () => this.onResize(), {
                    passive: true
                });
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

        // Enhanced Smooth Scrolling - using the main scrollToSection function above

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

        const galleryImages = [{
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

        const stories = [{
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
            }, {
                passive: false
            });

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

        // Background Music Player System
        class MusicPlayer {
            constructor() {
                this.audioPlayer = document.getElementById('audioPlayer');
                this.musicPlayer = document.getElementById('musicPlayer');
                this.isPlaying = false;
                this.isMinimized = false;
                this.currentSongIndex = 0;
                this.volume = 0.3; // 30% volume by default
                this.isFading = false;

                // Wedding playlist - you can replace these with your actual songs
                this.playlist = [{
                        title: "{{ $details['song_1_title'] ?? 'Perfect' }}",
                        artist: "{{ $details['song_1_artist'] ?? 'Ed Sheeran' }}",
                        src: "{{ $details['song_1_url'] ?? '/audio/perfect.mp3' }}",
                        duration: "4:23"
                    },
                    {
                        title: "{{ $details['song_2_title'] ?? 'All of Me' }}",
                        artist: "{{ $details['song_2_artist'] ?? 'John Legend' }}",
                        src: "{{ $details['song_2_url'] ?? '/audio/all-of-me.mp3' }}",
                        duration: "4:29"
                    },
                    {
                        title: "{{ $details['song_3_title'] ?? 'Thinking Out Loud' }}",
                        artist: "{{ $details['song_3_artist'] ?? 'Ed Sheeran' }}",
                        src: "{{ $details['song_3_url'] ?? '/audio/thinking-out-loud.mp3' }}",
                        duration: "4:41"
                    },
                    {
                        title: "{{ $details['song_4_title'] ?? 'A Thousand Years' }}",
                        artist: "{{ $details['song_4_artist'] ?? 'Christina Perri' }}",
                        src: "{{ $details['song_4_url'] ?? '/audio/thousand-years.mp3' }}",
                        duration: "4:45"
                    },
                    {
                        title: "{{ $details['song_5_title'] ?? 'Marry Me' }}",
                        artist: "{{ $details['song_5_artist'] ?? 'Train' }}",
                        src: "{{ $details['song_5_url'] ?? '/audio/marry-me.mp3' }}",
                        duration: "3:58"
                    }
                ];

                this.init();
            }

            init() {
                this.setupAudioPlayer();
                this.loadSong(0);
                this.generatePlaylist();
                this.bindEvents();

                // Show music player when main content is visible
                this.showPlayerWhenReady();
            }

            setupAudioPlayer() {
                this.audioPlayer.volume = this.volume;
                this.audioPlayer.addEventListener('timeupdate', () => this.updateProgress());
                this.audioPlayer.addEventListener('ended', () => this.nextSong());
                this.audioPlayer.addEventListener('loadedmetadata', () => this.updateDuration());
                this.audioPlayer.addEventListener('canplaythrough', () => this.handleCanPlay());
                this.audioPlayer.addEventListener('error', (e) => this.handleAudioError(e));
            }

            showPlayerWhenReady() {
                const checkContentVisible = () => {
                    if (document.body.classList.contains('content-visible')) {
                        setTimeout(() => {
                            this.musicPlayer.classList.add('visible');
                            this.fadeInAudio();
                        }, 2000); // Show 2 seconds after main content
                    } else {
                        setTimeout(checkContentVisible, 500);
                    }
                };
                checkContentVisible();
            }

            loadSong(index) {
                if (index >= 0 && index < this.playlist.length) {
                    this.currentSongIndex = index;
                    const song = this.playlist[index];

                    this.audioPlayer.src = song.src;
                    document.getElementById('currentSongTitle').textContent = song.title;
                    document.getElementById('currentSongArtist').textContent = song.artist;

                    this.updatePlaylistUI();
                }
            }

            togglePlayPause() {
                if (this.isFading) return;

                if (this.isPlaying) {
                    this.pause();
                } else {
                    this.play();
                }
            }

            play() {
                if (this.audioPlayer.src) {
                    this.audioPlayer.play().then(() => {
                        this.isPlaying = true;
                        this.updatePlayPauseButton();
                        this.addEqualizer();
                        this.musicPlayer.classList.add('playing');
                    }).catch(error => {
                        console.log('Autoplay prevented:', error);
                        this.handlePlayError();
                    });
                }
            }

            pause() {
                this.audioPlayer.pause();
                this.isPlaying = false;
                this.updatePlayPauseButton();
                this.removeEqualizer();
                this.musicPlayer.classList.remove('playing');
            }

            nextSong() {
                const nextIndex = (this.currentSongIndex + 1) % this.playlist.length;
                this.loadSong(nextIndex);
                if (this.isPlaying) {
                    setTimeout(() => this.play(), 100);
                }
            }

            previousSong() {
                const prevIndex = this.currentSongIndex === 0 ? this.playlist.length - 1 : this.currentSongIndex - 1;
                this.loadSong(prevIndex);
                if (this.isPlaying) {
                    setTimeout(() => this.play(), 100);
                }
            }

            fadeInAudio() {
                if (this.isFading) return;

                this.isFading = true;
                this.audioPlayer.volume = 0;
                this.play();

                const fadeInInterval = setInterval(() => {
                    if (this.audioPlayer.volume < this.volume - 0.01) {
                        this.audioPlayer.volume = Math.min(this.audioPlayer.volume + 0.01, this.volume);
                    } else {
                        this.audioPlayer.volume = this.volume;
                        clearInterval(fadeInInterval);
                        this.isFading = false;
                    }
                }, 50);
            }

            fadeOutAudio(callback) {
                if (this.isFading) return;

                this.isFading = true;
                const originalVolume = this.audioPlayer.volume;

                const fadeOutInterval = setInterval(() => {
                    if (this.audioPlayer.volume > 0.01) {
                        this.audioPlayer.volume = Math.max(this.audioPlayer.volume - 0.02, 0);
                    } else {
                        this.audioPlayer.volume = 0;
                        clearInterval(fadeOutInterval);
                        this.pause();
                        this.audioPlayer.volume = originalVolume;
                        this.isFading = false;
                        if (callback) callback();
                    }
                }, 50);
            }

            updateProgress() {
                if (this.audioPlayer.duration) {
                    const progress = (this.audioPlayer.currentTime / this.audioPlayer.duration) * 100;
                    document.getElementById('progressFill').style.width = progress + '%';
                    document.getElementById('currentTime').textContent = this.formatTime(this.audioPlayer.currentTime);
                }
            }

            updateDuration() {
                if (this.audioPlayer.duration) {
                    document.getElementById('totalTime').textContent = this.formatTime(this.audioPlayer.duration);
                }
            }

            formatTime(seconds) {
                const mins = Math.floor(seconds / 60);
                const secs = Math.floor(seconds % 60);
                return `${mins}:${secs.toString().padStart(2, '0')}`;
            }

            updatePlayPauseButton() {
                const icon = document.getElementById('playPauseIcon');
                icon.className = this.isPlaying ? 'fas fa-pause' : 'fas fa-play';
            }

            addEqualizer() {
                const title = document.getElementById('currentSongTitle');
                if (!title.querySelector('.equalizer')) {
                    const equalizer = document.createElement('div');
                    equalizer.className = 'equalizer';
                    equalizer.innerHTML = '<div class="eq-bar"></div><div class="eq-bar"></div><div class="eq-bar"></div>';
                    title.insertBefore(equalizer, title.firstChild);
                }
            }

            removeEqualizer() {
                const equalizer = document.querySelector('.equalizer');
                if (equalizer) {
                    equalizer.remove();
                }
            }

            generatePlaylist() {
                const playlistContainer = document.getElementById('playlistSongs');
                playlistContainer.innerHTML = '';

                this.playlist.forEach((song, index) => {
                    const songItem = document.createElement('div');
                    songItem.className = 'song-item';
                    songItem.onclick = () => this.playSongFromPlaylist(index);

                    songItem.innerHTML = `
                        <i class="fas fa-music"></i>
                        <div class="song-item-info">
                            <div class="song-item-title">${song.title}</div>
                            <div class="song-item-artist">${song.artist}</div>
                        </div>
                        <div class="song-duration">${song.duration}</div>
                    `;

                    playlistContainer.appendChild(songItem);
                });
            }

            updatePlaylistUI() {
                const songItems = document.querySelectorAll('.song-item');
                songItems.forEach((item, index) => {
                    item.classList.toggle('active', index === this.currentSongIndex);
                });
            }

            playSongFromPlaylist(index) {
                this.loadSong(index);
                this.play();
                this.closePlaylist();
            }

            togglePlayer() {
                this.isMinimized = !this.isMinimized;
                this.musicPlayer.classList.toggle('minimized', this.isMinimized);

                const icon = document.getElementById('playerToggleIcon');
                icon.className = 'fas fa-music';
            }

            openPlaylist() {
                document.getElementById('playlistModal').classList.add('active');
                document.body.style.overflow = 'hidden';
            }

            closePlaylist() {
                document.getElementById('playlistModal').classList.remove('active');
                document.body.style.overflow = '';
            }

            seekToPosition(event) {
                const progressBar = event.currentTarget;
                const rect = progressBar.getBoundingClientRect();
                const percent = (event.clientX - rect.left) / rect.width;
                const newTime = percent * this.audioPlayer.duration;

                if (!isNaN(newTime)) {
                    this.audioPlayer.currentTime = newTime;
                }
            }

            adjustVolume(value) {
                this.volume = value / 100;
                this.audioPlayer.volume = this.volume;

                const volumeIcon = document.getElementById('volumeIcon');
                if (value == 0) {
                    volumeIcon.className = 'fas fa-volume-mute';
                } else if (value < 50) {
                    volumeIcon.className = 'fas fa-volume-down';
                } else {
                    volumeIcon.className = 'fas fa-volume-up';
                }
            }

            handleCanPlay() {
                // Audio is ready to play
                console.log('Audio ready:', this.playlist[this.currentSongIndex].title);
            }

            handleAudioError(error) {
                console.error('Audio error:', error);
                // Try next song if current one fails
                setTimeout(() => this.nextSong(), 1000);
            }

            handlePlayError() {
                // Show user interaction required message
                console.log('User interaction required for audio playback');
            }

            bindEvents() {
                // Close playlist on outside click
                document.getElementById('playlistModal').addEventListener('click', (e) => {
                    if (e.target.id === 'playlistModal') {
                        this.closePlaylist();
                    }
                });

                // Keyboard shortcuts
                document.addEventListener('keydown', (e) => {
                    const targetTag = e.target.tagName.toLowerCase();
                    if (targetTag === 'input' || targetTag === 'textarea' || targetTag === 'select') return;

                    switch (e.code) {
                        case 'Space':
                            e.preventDefault();
                            this.togglePlayPause();
                            break;
                        case 'ArrowRight':
                            e.preventDefault();
                            this.nextSong();
                            break;
                        case 'ArrowLeft':
                            e.preventDefault();
                            this.previousSong();
                            break;
                    }
                });
            }
        }

        // Global music player functions (called by HTML onclick events)
        let musicPlayerInstance = null;

        function togglePlayer() {
            if (musicPlayerInstance) {
                musicPlayerInstance.togglePlayer();
            }
        }

        function togglePlayPause() {
            if (musicPlayerInstance) {
                musicPlayerInstance.togglePlayPause();
            }
        }

        function nextSong() {
            if (musicPlayerInstance) {
                musicPlayerInstance.nextSong();
            }
        }

        function previousSong() {
            if (musicPlayerInstance) {
                musicPlayerInstance.previousSong();
            }
        }

        function openPlaylist() {
            if (musicPlayerInstance) {
                musicPlayerInstance.openPlaylist();
            }
        }

        function closePlaylist() {
            if (musicPlayerInstance) {
                musicPlayerInstance.closePlaylist();
            }
        }

        function seekToPosition(event) {
            if (musicPlayerInstance) {
                musicPlayerInstance.seekToPosition(event);
            }
        }

        function adjustVolume(value) {
            if (musicPlayerInstance) {
                musicPlayerInstance.adjustVolume(value);
            }
        }

        // Initialize when DOM is ready
        document.addEventListener('DOMContentLoaded', () => {
            // Initialize CSS-based 3D letter animation
            new LetterAnimation();

            // Initialize parallax effects
            initParallaxEffects();

            // Initialize music player
            musicPlayerInstance = new MusicPlayer();

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