{{-- Enhanced Malaysian Wedding Template v2 with Full Functionality --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $details["bride_name"] ?? "Bride" }} & {{ $details["groom_name"] ?? "Groom" }} - Wedding Invitation</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
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

        .invitation-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100vh;
            background-color: #f5f2e9;
            z-index: 2000;
            display: flex;
            justify-content: center;
            align-items: center;
            cursor: pointer;
            opacity: 1;
            transition: opacity 1s ease-out;
            overflow: hidden;
        }

        .invitation-overlay.hidden {
            opacity: 0;
            pointer-events: none;
        }

        .envelope {
            position: relative;
            width: 90%;
            max-width: 500px;
            aspect-ratio: 1.5/1;
            background-color: #f8f3e9;
            border-radius: 5px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            border: 1px solid #d4c9a8;
            display: flex;
            justify-content: center;
            align-items: center;
            transform-style: preserve-3d;
            perspective: 1000px;
            margin: 0;
            transform: translateX(0) translateY(0);
        }

        .envelope::before {
            content: '';
            position: absolute;
            top: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, transparent 49.5%, #e6ddc6 49.5%, #e6ddc6 50.5%, transparent 50.5%),
                        linear-gradient(45deg, transparent 49.5%, #e6ddc6 49.5%, #e6ddc6 50.5%, transparent 50.5%);
            z-index: 5;
            pointer-events: none;
        }

        .invitation-content {
            width: 90%;
            height: 90%;
            background: #fff;
            border: 3px double var(--primary);
            border-radius: 5px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            padding: 1.5rem 2rem 2.5rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            position: relative;
            z-index: 10;
        }

        .envelope-seal {
            position: absolute;
            width: 60px;
            height: 60px;
            background-color: var(--primary);
            border-radius: 50%;
            top: 0;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 15;
            display: flex;
            justify-content: center;
            align-items: center;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .envelope-seal svg {
            width: 32px;
            height: 32px;
            fill: #fff;
        }

        .invitation-title {
            font-family: 'Great Vibes', cursive;
            font-size: 3.5rem;
            color: var(--primary);
            margin-bottom: 0;
            line-height: 1.2;
            margin-top: 1.5rem;
        }

        .invitation-instruction {
            position: relative;
            font-size: 1rem;
            color: #8a7142;
            display: inline-block;
            font-family: 'Montserrat', sans-serif;
            letter-spacing: 1px;
            padding: 8px 16px;
            border: 1px solid #d4c9a8;
            border-radius: 30px;
            background-color: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(3px);
            box-shadow: 0 3px 10px rgba(200, 161, 101, 0.1);
            transform-origin: center;
            transition: all 0.3s ease;
        }

        .invitation-instruction:hover {
            transform: scale(1.05);
            background-color: rgba(255, 255, 255, 0.9);
            box-shadow: 0 5px 15px rgba(200, 161, 101, 0.2);
        }

        /* Subtle animation to draw attention */
        @keyframes gentle-bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-5px); }
        }

        /* Decorative elements around instruction */
        .instruction-wrapper {
            position: relative;
            margin-top: 0.5rem;
            animation: gentle-bounce 3s infinite ease-in-out;
        }

        .instruction-wrapper::before,
        .instruction-wrapper::after {
            content: '✦';
            color: var(--primary);
            font-size: 0.8rem;
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
        }

        .instruction-wrapper::before {
            left: -15px;
        }

        .instruction-wrapper::after {
            right: -15px;
        }

        /* Arrow icon animation */
        .arrow-icon {
            display: inline-block;
            margin-left: 8px;
            transition: transform 0.3s ease;
        }

        .invitation-instruction:hover .arrow-icon {
            transform: translateX(3px);
        }

        /* Initially hide main content sections */
        body > *:not(.invitation-overlay) {
             visibility: hidden;
             opacity: 0;
             transition: opacity 0.8s ease-in, visibility 0s 0.8s;
        }
        
        /* Style to show content after overlay is hidden */
        body.content-visible > *:not(.invitation-overlay) {
             visibility: visible;
             opacity: 1;
             transition-delay: 0s;
        }

        /* Separator styling */
        .invitation-separator {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            margin: 0.2rem 0;
        }

        .invitation-separator-line {
            height: 1px;
            width: 80px;
            background-color: var(--primary);
        }

        .invitation-separator svg {
            margin: 0 10px;
            fill: var(--primary);
        }

        /* Couple names styling */
        .invitation-couple-names {
            font-family: 'Great Vibes', cursive;
            font-size: 2.5rem;
            color: #333;
            margin: 1rem 0 0.5rem;
            line-height: 1.2;
        }

        /* Add a small wedding date to fill the space below */
        .wedding-date-small {
            font-size: 0.9rem;
            color: #aaa;
            margin-top: 0.5rem;
            font-style: italic;
            letter-spacing: 1px;
            position: relative;
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
            z-index: 100;
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
            background-color: var(--secondary);
        }

        .timeline {
            position: relative;
            max-width: 800px;
            margin: 0 auto;
        }

        .timeline::after {
            content: '';
            position: absolute;
            width: 2px;
            background-color: var(--primary);
            top: 0;
            bottom: 0;
            left: 50%;
            margin-left: -1px;
        }

        .timeline-item {
            position: relative;
            width: 50%;
            padding: 1.5rem;
            margin-bottom: 3rem;
        }

        .timeline-item::after {
            content: '';
            position: absolute;
            width: 20px;
            height: 20px;
            background-color: var(--secondary);
            border: 4px solid var(--primary);
            border-radius: 50%;
            top: 20px;
            z-index: 1;
        }

        .timeline-item.left {
            left: 0;
            padding-right: 40px;
        }

        .timeline-item.right {
            left: 50%;
            padding-left: 40px;
        }

        .timeline-item.left::after {
            right: -10px;
        }

        .timeline-item.right::after {
            left: -10px;
        }

        .timeline-content {
            padding: 1.5rem;
            background-color: #fff;
            border-radius: 6px;
            box-shadow: 0 5px 25px rgba(0, 0, 0, 0.1);
        }

        .event-time {
            font-weight: 500;
            color: var(--primary);
            margin-bottom: 0.5rem;
        }

        .event-title {
            margin-bottom: 0.5rem;
        }

        .event-location {
            display: flex;
            align-items: center;
            margin-top: 1rem;
            font-size: 0.9rem;
        }

        .event-location svg {
            margin-right: 0.5rem;
            fill: var(--primary);
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
            font-family: 'Scheherazade New', serif;
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
            
            .timeline::after {
                left: 31px;
            }
            
            .timeline-item {
                width: 100%;
                padding-left: 70px;
                padding-right: 25px;
            }
            
            .timeline-item.right {
                left: 0;
            }
            
            .timeline-item.left::after,
            .timeline-item.right::after {
                left: 20px;
            }
        }

        @media (max-width: 768px) {
            .section-title {
                font-size: 2rem;
            }
            
            .couple-names {
                font-size: 3rem;
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
        }

        @media (max-width: 576px) {
            .couple-names {
                font-size: 2.5rem;
            }
            
            .wedding-date {
                font-size: 1.2rem;
            }
            
            .photo-frame {
                width: 220px;
                height: 220px;
            }
            
            .separator-line {
                width: 60px;
            }
        }
    </style>
</head>
<body class="invitation-active">
    <!-- Floating Menu -->
    <div class="floating-menu">
        <div class="menu-item" onclick="scrollToSection('hero')">
            <i class="fas fa-home"></i>
            <span class="menu-tooltip">Home</span>
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
        <div class="envelope">
            <div class="envelope-seal">
                <svg viewBox="0 0 24 24">
                    <path d="M12,21.35L10.55,20.03C5.4,15.36 2,12.27 2,8.5C2,5.41 4.42,3 7.5,3C9.24,3 10.91,3.81 12,5.08C13.09,3.81 14.76,3 16.5,3C19.58,3 22,5.41 22,8.5C22,12.27 18.6,15.36 13.45,20.03L12,21.35Z"/>
                </svg>
            </div>
            <div class="invitation-content">
                <h2 class="invitation-title">You're Invited</h2>
                <div class="invitation-separator">
                    <div class="invitation-separator-line"></div>
                    <svg viewBox="0 0 24 24" width="30" height="24">
                        <path d="M12,21.35L10.55,20.03C5.4,15.36 2,12.27 2,8.5C2,5.41 4.42,3 7.5,3C9.24,3 10.91,3.81 12,5.08C13.09,3.81 14.76,3 16.5,3C19.58,3 22,5.41 22,8.5C22,12.27 18.6,15.36 13.45,20.03L12,21.35Z"/>
                    </svg>
                    <div class="invitation-separator-line"></div>
                </div>
                <p>To celebrate the wedding of</p>
                <h1 class="invitation-couple-names">{{ $details["groom_name"] ?? "Groom" }} & {{ $details["bride_name"] ?? "Bride" }}</h1>
                <div class="instruction-wrapper">
                    <p class="invitation-instruction">
                        Open Invitation 
                        <span class="arrow-icon">→</span>
                    </p>
                </div>
                <p class="wedding-date-small">{{ $details["wedding_date"] ?? "Wedding Date" }}</p>
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

    <!-- Header & Navigation -->
    <header>
        <div class="container nav-container">
            <a href="#hero" class="logo">{{ substr($details["groom_name"] ?? "G", 0, 1) }} & {{ substr($details["bride_name"] ?? "B", 0, 1) }}</a>
            <nav>
                <ul class="nav-links">
                    <li><a href="#hero">Home</a></li>
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
    <section id="hero" class="hero">
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

    <!-- Couple Section -->
    <section id="couple" class="couple">
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
    <section id="events" class="events">
        <div class="container">
            <h2 class="section-title fade-in">Wedding Events</h2>
            <div class="timeline">
                @if($details["akad_date"] ?? false)
                <div class="timeline-item left fade-in-left">
                    <div class="timeline-content">
                        <p class="event-time">{{ $details["akad_date"] }} • {{ $details["akad_time"] ?? "10:00 AM" }}</p>
                        <h3 class="event-title">{{ $details["akad_title"] ?? "Akad Nikah (Solemnization)" }}</h3>
                        <p>{{ $details["akad_description"] ?? "The sacred ceremony where the couple will officially be joined in marriage according to Islamic tradition." }}</p>
                        @if($details["akad_venue"] ?? false)
                        <div class="event-location">
                            <svg width="16" height="16" viewBox="0 0 24 24">
                                <path d="M12,2C8.13,2 5,5.13 5,9C5,14.25 12,22 12,22C12,22 19,14.25 19,9C19,5.13 15.87,2 12,2ZM12,11.5C10.62,11.5 9.5,10.38 9.5,9C9.5,7.62 10.62,6.5 12,6.5C13.38,6.5 14.5,7.62 14.5,9C14.5,10.38 13.38,11.5 12,11.5Z"/>
                            </svg>
                            <span>{{ $details["akad_venue"] }}</span>
                        </div>
                        @endif
                    </div>
                </div>
                @endif

                <div class="timeline-item {{ $details['akad_date'] ?? false ? 'right' : 'left' }} fade-in-{{ $details['akad_date'] ?? false ? 'right' : 'left' }}">
                    <div class="timeline-content">
                        <p class="event-time">{{ $details["reception_date"] ?? $details["wedding_date"] ?? "Wedding Date" }} • {{ $details["reception_time"] ?? "12:00 PM - 5:00 PM" }}</p>
                        <h3 class="event-title">{{ $details["reception_title"] ?? "Majlis Bersanding & Reception" }}</h3>
                        <p>{{ $details["reception_description"] ?? "The grand celebration featuring the traditional Malaysian wedding throne ceremony, followed by a feast and entertainment." }}</p>
                        <div class="event-location">
                            <svg width="16" height="16" viewBox="0 0 24 24">
                                <path d="M12,2C8.13,2 5,5.13 5,9C5,14.25 12,22 12,22C12,22 19,14.25 19,9C19,5.13 15.87,2 12,2ZM12,11.5C10.62,11.5 9.5,10.38 9.5,9C9.5,7.62 10.62,6.5 12,6.5C13.38,6.5 14.5,7.62 14.5,9C14.5,10.38 13.38,11.5 12,11.5Z"/>
                            </svg>
                            <span>{{ $details["venue"] ?? "Wedding Venue" }}</span>
                        </div>
                        @if($details["address"] ?? false)
                        <div class="event-location">
                            <svg width="16" height="16" viewBox="0 0 24 24">
                                <path d="M12,2C8.13,2 5,5.13 5,9C5,14.25 12,22 12,22C12,22 19,14.25 19,9C19,5.13 15.87,2 12,2ZM12,11.5C10.62,11.5 9.5,10.38 9.5,9C9.5,7.62 10.62,6.5 12,6.5C13.38,6.5 14.5,7.62 14.5,9C14.5,10.38 13.38,11.5 12,11.5Z"/>
                            </svg>
                            <span>{{ $details["address"] }}</span>
                        </div>
                        @endif
                    </div>
                </div>

                @if($details["groom_reception_date"] ?? false)
                <div class="timeline-item left fade-in-left">
                    <div class="timeline-content">
                        <p class="event-time">{{ $details["groom_reception_date"] }} • {{ $details["groom_reception_time"] ?? "11:00 AM - 3:00 PM" }}</p>
                        <h3 class="event-title">{{ $details["groom_reception_title"] ?? "Majlis Bertandang (Groom's Reception)" }}</h3>
                        <p>{{ $details["groom_reception_description"] ?? "A second reception hosted by the groom's family, featuring traditional Malaysian cuisine and customs." }}</p>
                        @if($details["groom_reception_venue"] ?? false)
                        <div class="event-location">
                            <svg width="16" height="16" viewBox="0 0 24 24">
                                <path d="M12,2C8.13,2 5,5.13 5,9C5,14.25 12,22 12,22C12,22 19,14.25 19,9C19,5.13 15.87,2 12,2ZM12,11.5C10.62,11.5 9.5,10.38 9.5,9C9.5,7.62 10.62,6.5 12,6.5C13.38,6.5 14.5,7.62 14.5,9C14.5,10.38 13.38,11.5 12,11.5Z"/>
                            </svg>
                            <span>{{ $details["groom_reception_venue"] }}</span>
                        </div>
                        @endif
                    </div>
                </div>
                @endif
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
    <section id="gallery" class="gallery">
        <div class="container">
            <h2 class="section-title fade-in">Our Moments</h2>
            <div class="gallery-grid">
                @for($i = 1; $i <= 6; $i++)
                <div class="gallery-item fade-in">
                    @if($details["gallery_photo_$i"] ?? false)
                        <img src="{{ $details['gallery_photo_' . $i] }}" alt="Gallery Photo {{ $i }}">
                    @else
                        <img src="https://images.unsplash.com/photo-1519225421980-715cb0215aed?ixlib=rb-4.0.3&w=300&h=300&fit=crop" alt="Gallery Photo {{ $i }}">
                    @endif
                </div>
                @endfor
            </div>
        </div>
    </section>

    <!-- RSVP Section -->
    <section id="rsvp" class="rsvp">
        <div class="container">
            <h2 class="section-title fade-in">Please RSVP</h2>
            <form class="rsvp-form fade-in">
                <div class="form-group">
                    <input type="text" class="form-control" placeholder="Your Name" required>
                </div>
                <div class="form-group">
                    <input type="email" class="form-control" placeholder="Your Email" required>
                </div>
                <div class="form-group">
                    <input type="tel" class="form-control" placeholder="Your Phone Number" required>
                </div>
                <div class="form-group">
                    <select class="form-control" required>
                        <option value="" disabled selected>Will you attend?</option>
                        <option value="yes">Yes, I will attend</option>
                        <option value="no">Sorry, I cannot attend</option>
                    </select>
                </div>
                <div class="form-group">
                    <input type="number" class="form-control" placeholder="Number of Guests" min="0" max="5">
                </div>
                <div class="form-group">
                    <textarea class="form-control" placeholder="Your Message" rows="4"></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Send RSVP</button>
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
        const rsvpForm = document.querySelector('.rsvp-form');
        if (rsvpForm) {
            rsvpForm.addEventListener('submit', function(e) {
                e.preventDefault();
                alert('Thank you for your RSVP! We look forward to celebrating with you.');
                this.reset();
            });
        }

        // Invitation overlay functionality
        document.addEventListener('DOMContentLoaded', function() {
            document.body.classList.add('invitation-active');
            
            const invitationOverlay = document.querySelector('.invitation-overlay');
            const envelope = document.querySelector('.envelope');
            const floatingMenu = document.querySelector('.floating-menu');
            
            // Make sure gift modal is hidden initially
            const giftModal = document.getElementById('giftModal');
            if (giftModal) {
                giftModal.style.opacity = '0';
                giftModal.style.visibility = 'hidden';
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
                    // Opening animation
                    if (envelope) {
                        envelope.style.transition = 'transform 0.8s ease';
                        envelope.style.transform = 'scale(1.05) rotateX(5deg)';
                    }
                    const content = document.querySelector('.invitation-content');
                    if (content) {
                        content.style.transition = 'transform 0.8s ease';
                        content.style.transform = 'translateY(-10px)';
                    }
                    
                    // Hide after animation
                    setTimeout(() => {
                        invitationOverlay.classList.add('hidden');
                        document.body.classList.add('content-visible');
                        document.body.classList.remove('invitation-active');
                        
                        // Show floating menu
                        if (floatingMenu) {
                            floatingMenu.classList.add('visible');
                        }
                        
                        // Remove from DOM after transition
                        setTimeout(() => {
                            invitationOverlay.style.display = 'none';
                        }, 1000);
                    }, 800);
                });
            }
        });

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
    </script>
</body>
</html> 