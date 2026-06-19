<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $details["bride_name"] ?? "Bride" }} & {{ $details["groom_name"] ?? "Groom" }} - Wedding Invitation</title>
    <script>
        // Pass wedding card ID to JavaScript
        window.weddingCardId = {{ $weddingCard->id ?? 'null' }};
        // RSVP messages will be populated by view.blade.php
        window.rsvpMessages = [];
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
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
            --accent: #d4a574;
            --transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        body {
            font-family: 'Georgia', serif;
            background: linear-gradient(135deg, #f8f3e9 0%, #ede7db 100%);
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* Welcome Screen / Envelope Container */
        .welcome-container {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100vh;
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            background: linear-gradient(135deg, #f8f3e9 0%, #ede7db 100%);
            transition: var(--transition);
        }

        .welcome-container.hidden {
            opacity: 0;
            visibility: hidden;
            pointer-events: none;
        }

        /* Main Card Wrapper */
        .card-wrapper {
            position: relative;
            width: 100%;
            max-width: 600px;
            aspect-ratio: 9/13;
            perspective: 1200px;
        }

        /* Envelope Animation */
        .envelope {
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, #ffffff 0%, #f9f9f9 100%);
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
            z-index: 10;
            transition: var(--transition);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .envelope::before {
            content: '';
            position: absolute;
            inset: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="floral" patternUnits="userSpaceOnUse" width="100" height="100"><circle cx="20" cy="20" r="2" fill="%23c8a165" opacity="0.1"/><circle cx="80" cy="30" r="1.5" fill="%23c8a165" opacity="0.08"/><circle cx="50" cy="70" r="2" fill="%23c8a165" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23floral)"/></svg>');
            opacity: 0.3;
            pointer-events: none;
        }

        .envelope.opened {
            transform: rotateX(90deg) translateZ(-50px);
            opacity: 0;
            pointer-events: none;
        }

        .envelope-content {
            text-align: center;
            z-index: 2;
            animation: slideUp 0.8s ease-out;
        }

        .envelope-icon {
            font-size: 3rem;
            color: var(--primary);
            margin-bottom: 15px;
            animation: float 3s ease-in-out infinite;
        }

        .envelope-text {
            font-size: 1.3rem;
            color: var(--text-dark);
            font-weight: 300;
            letter-spacing: 2px;
            text-transform: uppercase;
        }

        /* Main Card Preview */
        .card-preview {
            position: absolute;
            inset: 0;
            background: linear-gradient(to bottom, rgba(255, 255, 255, 0.98) 0%, rgba(248, 243, 233, 0.98) 100%);
            border-radius: 20px;
            z-index: 0;
            padding: 40px 30px;
            overflow: hidden;
            opacity: 0;
            visibility: hidden;
            transition: var(--transition);
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
        }

        .card-preview.visible {
            opacity: 1;
            visibility: visible;
            z-index: 11;
        }

        /* Floral Decorations */
        .floral-top, .floral-bottom {
            position: absolute;
            width: 200px;
            height: 150px;
            pointer-events: none;
            opacity: 0.8;
        }

        .floral-top {
            top: -30px;
            right: -40px;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 200 150"><circle cx="50" cy="40" r="35" fill="%23fff" stroke="%23d4a574" stroke-width="2"/><circle cx="55" cy="35" r="8" fill="%23d4a574"/><circle cx="45" cy="30" r="6" fill="%23d4a574"/><path d="M60 50 Q65 60 60 70" stroke="%23999" stroke-width="2" fill="none"/><circle cx="120" cy="30" r="32" fill="%23fff" stroke="%23c8a165" stroke-width="2"/><circle cx="124" cy="26" r="7" fill="%23c8a165"/><circle cx="115" cy="22" r="5" fill="%23c8a165"/><path d="M130 45 Q140 55 135 70" stroke="%23999" stroke-width="2" fill="none"/><circle cx="160" cy="60" r="25" fill="%23f9f3e8" stroke="%23d4a574" stroke-width="1.5"/></svg>') no-repeat;
            background-size: contain;
            animation: floatFloral 6s ease-in-out infinite;
        }

        .floral-bottom {
            bottom: -20px;
            left: -30px;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 200 150"><circle cx="80" cy="90" r="32" fill="%23fff" stroke="%23c8a165" stroke-width="2"/><circle cx="85" cy="85" r="7" fill="%23c8a165"/><circle cx="75" cy="80" r="6" fill="%23c8a165"/><path d="M70 110 Q60 120 70 135" stroke="%23999" stroke-width="2" fill="none"/><circle cx="150" cy="110" r="35" fill="%23fff" stroke="%23d4a574" stroke-width="2"/><circle cx="145" cy="105" r="8" fill="%23d4a574"/><circle cx="155" cy="100" r="7" fill="%23d4a574"/><path d="M140 130 Q130 140 135 155" stroke="%23999" stroke-width="2" fill="none"/></svg>') no-repeat;
            background-size: contain;
            animation: floatFloral 8s ease-in-out infinite reverse;
        }

        /* Card Content */
        .card-header {
            text-align: center;
            margin-bottom: 20px;
            position: relative;
            z-index: 2;
        }

        .card-subtitle {
            font-size: 0.9rem;
            color: var(--primary);
            letter-spacing: 3px;
            text-transform: uppercase;
            margin-bottom: 15px;
            font-weight: 300;
        }

        .couple-names {
            font-size: 2.8rem;
            color: var(--primary);
            font-weight: 300;
            margin: 15px 0;
            font-style: italic;
            letter-spacing: 1px;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .name-divider {
            font-size: 1.5rem;
            color: var(--primary);
            margin: 10px 0;
            opacity: 0.7;
        }

        /* Event Details */
        .event-details {
            margin: 30px 0;
            position: relative;
            z-index: 2;
        }

        .detail-item {
            text-align: center;
            margin: 20px 0;
            opacity: 0;
            animation: slideInUp 0.6s ease-out forwards;
            padding: 12px;
            border-radius: 8px;
            transition: var(--transition);
            cursor: pointer;
        }

        .detail-item:nth-child(1) { animation-delay: 0.1s; }
        .detail-item:nth-child(2) { animation-delay: 0.2s; }
        .detail-item:nth-child(3) { animation-delay: 0.3s; }

        .detail-item:hover {
            background: rgba(200, 161, 101, 0.08);
            transform: translateY(-2px);
        }

        .detail-icon {
            font-size: 1.8rem;
            color: var(--primary);
            margin-bottom: 8px;
            display: block;
        }

        .detail-item:hover .detail-icon {
            animation: rotateHover 0.6s ease-out;
        }

        .detail-label {
            font-size: 0.85rem;
            color: #999;
            letter-spacing: 2px;
            text-transform: uppercase;
            margin-bottom: 5px;
        }

        .detail-text {
            font-size: 1.2rem;
            color: var(--text-dark);
            font-weight: 500;
        }

        .separator-line {
            height: 1px;
            background: linear-gradient(90deg, transparent, var(--primary), transparent);
            margin: 25px 0;
            opacity: 0.5;
        }

        /* Interactive Elements */
        .preview-actions {
            margin-top: 30px;
            text-align: center;
            position: relative;
            z-index: 2;
        }

        .preview-text {
            font-size: 0.9rem;
            color: #999;
            letter-spacing: 2px;
            text-transform: uppercase;
            margin-bottom: 12px;
        }

        .enter-button {
            background: linear-gradient(135deg, var(--primary) 0%, var(--accent) 100%);
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 25px;
            cursor: pointer;
            font-size: 0.9rem;
            letter-spacing: 2px;
            text-transform: uppercase;
            transition: var(--transition);
            box-shadow: 0 4px 15px rgba(200, 161, 101, 0.3);
            font-weight: 500;
        }

        .enter-button:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 25px rgba(200, 161, 101, 0.4);
        }

        .enter-button:active {
            transform: translateY(-1px);
        }

        /* Main Content Container */
        .main-content-container {
            display: none;
            min-height: 100vh;
            padding-top: 80px;
        }

        .main-content-container.active {
            display: block;
        }

        /* Header */
        header {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.08);
            z-index: 1000;
            padding: 15px 0;
            transition: var(--transition);
        }

        .nav-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--primary);
            text-decoration: none;
        }

        nav ul {
            display: flex;
            list-style: none;
            gap: 30px;
        }

        nav a {
            color: var(--text-dark);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s;
        }

        nav a:hover {
            color: var(--primary);
        }

        .hamburger {
            display: none;
            cursor: pointer;
            font-size: 1.5rem;
            color: var(--text-dark);
        }

        /* Sections */
        section {
            padding: 80px 20px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .section-title {
            font-size: 2.5rem;
            text-align: center;
            margin-bottom: 3rem;
            color: var(--primary);
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
            background: var(--primary);
        }

        /* Countdown Section */
        .countdown-container {
            text-align: center;
            padding: 40px 20px;
        }

        .countdown-title {
            font-size: 2rem;
            color: var(--primary);
            margin-bottom: 1rem;
        }

        .countdown-subtitle {
            font-size: 1.1rem;
            color: #666;
            margin-bottom: 2rem;
        }

        .countdown-timer {
            display: flex;
            justify-content: center;
            gap: 20px;
            flex-wrap: wrap;
        }

        .countdown-item {
            background: white;
            padding: 25px;
            border-radius: 15px;
            min-width: 120px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            transition: var(--transition);
        }

        .countdown-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 25px rgba(200, 161, 101, 0.3);
        }

        .countdown-number {
            display: block;
            font-size: 3rem;
            font-weight: 700;
            color: var(--primary);
            line-height: 1;
        }

        .countdown-label {
            display: block;
            font-size: 0.9rem;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-top: 10px;
        }

        .countdown-expired {
            display: none;
            text-align: center;
            padding: 40px;
        }

        .countdown-expired.active {
            display: block;
        }

        .countdown-message {
            margin-top: 2rem;
            font-size: 1.1rem;
            color: #666;
            font-style: italic;
        }

        /* Couple Section */
        .couple-section {
            background: white;
            padding: 80px 20px;
            border-radius: 20px;
            margin: 40px 0;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
        }

        .couple-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 40px;
            max-width: 1000px;
            margin: 0 auto;
        }

        .couple-card {
            text-align: center;
            padding: 30px;
            background: linear-gradient(135deg, #f8f3e9 0%, #ffffff 100%);
            border-radius: 15px;
            transition: var(--transition);
        }

        .couple-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 30px rgba(200, 161, 101, 0.2);
        }

        .couple-image-container {
            width: 200px;
            height: 200px;
            margin: 0 auto 20px;
            border-radius: 50%;
            overflow: hidden;
            border: 5px solid var(--primary);
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.15);
        }

        .couple-image-container img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .couple-name-title {
            font-size: 2rem;
            color: var(--primary);
            margin: 15px 0;
        }

        .couple-parents {
            font-size: 1rem;
            color: #666;
            line-height: 1.6;
        }

        /* Events Section */
        .events-card {
            background: white;
            padding: 50px 30px;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
            margin: 40px auto;
            max-width: 900px;
        }

        .event-item {
            padding: 30px;
            margin: 20px 0;
            border-left: 4px solid var(--primary);
            background: linear-gradient(to right, rgba(200, 161, 101, 0.05) 0%, transparent 100%);
            border-radius: 8px;
            transition: var(--transition);
        }

        .event-item:hover {
            transform: translateX(10px);
            background: linear-gradient(to right, rgba(200, 161, 101, 0.1) 0%, transparent 100%);
        }

        .event-title {
            font-size: 1.5rem;
            color: var(--primary);
            margin-bottom: 10px;
        }

        .event-info {
            font-size: 1.1rem;
            color: var(--text-dark);
            margin: 8px 0;
        }

        .event-icon {
            color: var(--primary);
            margin-right: 10px;
        }

        .map-container {
            margin-top: 40px;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
        }

        .map-container iframe {
            width: 100%;
            height: 400px;
            border: none;
        }

        /* Messages Section */
        .messages-container {
            max-width: 900px;
            margin: 0 auto;
            background: white;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
        }

        .messages-scroll {
            max-height: 600px;
            overflow-y: auto;
            padding: 20px;
        }

        .message-card {
            background: linear-gradient(135deg, #f8f3e9 0%, #ffffff 100%);
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 20px;
            border-left: 4px solid var(--primary);
            transition: var(--transition);
        }

        .message-card:hover {
            transform: translateX(5px);
            box-shadow: 0 5px 15px rgba(200, 161, 101, 0.2);
        }

        .message-author {
            font-weight: 600;
            color: var(--primary);
            margin-bottom: 5px;
        }

        .message-text {
            color: var(--text-dark);
            line-height: 1.6;
        }

        .no-messages {
            text-align: center;
            padding: 60px 20px;
            color: #999;
        }

        .no-messages-icon {
            font-size: 3rem;
            color: var(--primary);
            margin-bottom: 20px;
        }

        /* RSVP Section */
        .rsvp-form-container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            padding: 50px 40px;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
        }

        .form-group {
            margin: 20px 0;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: var(--text-dark);
            font-weight: 500;
            font-size: 0.95rem;
        }

        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-family: inherit;
            font-size: 1rem;
            transition: var(--transition);
            background: #fafafa;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(200, 161, 101, 0.1);
            background: white;
        }

        .form-control.is-invalid {
            border-color: #dc3545;
        }

        .invalid-feedback {
            color: #dc3545;
            font-size: 0.875rem;
            margin-top: 5px;
            display: none;
        }

        .invalid-feedback:not(:empty) {
            display: block;
        }

        .btn-primary {
            width: 100%;
            background: linear-gradient(135deg, var(--primary) 0%, var(--accent) 100%);
            color: white;
            border: none;
            padding: 15px 30px;
            border-radius: 25px;
            cursor: pointer;
            font-size: 1rem;
            letter-spacing: 2px;
            text-transform: uppercase;
            transition: var(--transition);
            box-shadow: 0 4px 15px rgba(200, 161, 101, 0.3);
            font-weight: 600;
            margin-top: 20px;
        }

        .btn-primary:hover:not(:disabled) {
            transform: translateY(-3px);
            box-shadow: 0 6px 25px rgba(200, 161, 101, 0.4);
        }

        .btn-primary:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .alert {
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 20px;
            display: flex;
            align-items: flex-start;
            gap: 15px;
        }

        .alert-success {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
        }

        .alert-danger {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
        }

        .alert h4 {
            margin: 0 0 10px 0;
            font-size: 1.1rem;
        }

        .alert p {
            margin: 0;
        }

        /* Gift Modal */
        .gift-modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(5px);
            z-index: 10000;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 20px;
            animation: fadeIn 0.3s ease;
        }

        .gift-modal.active {
            display: flex;
        }

        .gift-content {
            background: white;
            padding: 50px 40px;
            border-radius: 20px;
            max-width: 500px;
            width: 100%;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            animation: slideUpModal 0.4s ease-out;
            position: relative;
        }

        .gift-close {
            position: absolute;
            top: 20px;
            right: 20px;
            font-size: 1.5rem;
            cursor: pointer;
            color: #999;
            transition: color 0.3s;
        }

        .gift-close:hover {
            color: var(--primary);
        }

        .gift-title {
            font-size: 2rem;
            color: var(--primary);
            text-align: center;
            margin-bottom: 15px;
        }

        .gift-subtitle {
            text-align: center;
            color: #666;
            margin-bottom: 30px;
            line-height: 1.6;
        }

        .gift-options {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
            flex-wrap: wrap;
            justify-content: center;
        }

        .gift-amount {
            padding: 15px 30px;
            background: linear-gradient(135deg, #f8f3e9 0%, #ffffff 100%);
            border: 2px solid #e0e0e0;
            border-radius: 12px;
            cursor: pointer;
            transition: var(--transition);
            font-weight: 600;
            font-size: 1.1rem;
        }

        .gift-amount:hover {
            transform: translateY(-3px);
            border-color: var(--primary);
        }

        .gift-amount.selected {
            background: linear-gradient(135deg, var(--primary) 0%, var(--accent) 100%);
            color: white;
            border-color: var(--primary);
        }

        .custom-amount {
            margin: 20px 0;
        }

        .custom-amount input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 1rem;
            transition: var(--transition);
        }

        .custom-amount input:focus {
            outline: none;
            border-color: var(--primary);
        }

        .give-btn {
            width: 100%;
            background: linear-gradient(135deg, var(--primary) 0%, var(--accent) 100%);
            color: white;
            border: none;
            padding: 15px 30px;
            border-radius: 25px;
            cursor: pointer;
            font-size: 1rem;
            letter-spacing: 2px;
            text-transform: uppercase;
            transition: var(--transition);
            font-weight: 600;
            margin-top: 10px;
        }

        .give-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 25px rgba(200, 161, 101, 0.4);
        }

        /* Floating Action Button */
        .floating-menu {
            position: fixed;
            bottom: 30px;
            right: 30px;
            z-index: 999;
            display: none;
        }

        .floating-menu.active {
            display: block;
        }

        .menu-item {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--accent) 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
            margin-bottom: 15px;
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(200, 161, 101, 0.4);
            transition: var(--transition);
        }

        .menu-item:hover {
            transform: scale(1.1);
            box-shadow: 0 6px 25px rgba(200, 161, 101, 0.5);
        }

        /* Footer */
        footer {
            background: linear-gradient(135deg, var(--primary-dark) 0%, var(--primary) 100%);
            color: white;
            padding: 60px 20px 30px;
            text-align: center;
        }

        .footer-title {
            font-size: 2rem;
            margin-bottom: 15px;
        }

        footer p {
            margin: 15px 0;
            opacity: 0.9;
        }

        .social-links {
            display: flex;
            justify-content: center;
            gap: 20px;
            list-style: none;
            margin: 25px 0;
        }

        .social-links a {
            color: white;
            font-size: 1.5rem;
            transition: transform 0.3s;
            display: inline-block;
        }

        .social-links a:hover {
            transform: scale(1.2) rotate(5deg);
        }

        .copyright {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.2);
            font-size: 0.9rem;
            opacity: 0.8;
        }

        /* Animations */
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes slideUpModal {
            from {
                opacity: 0;
                transform: translateY(30px) scale(0.95);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-15px); }
        }

        @keyframes floatFloral {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-10px) rotate(2deg); }
        }

        @keyframes rotateHover {
            0% { transform: rotate(0deg) scale(1); }
            100% { transform: rotate(20deg) scale(1.1); }
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .card-wrapper {
                max-width: 100%;
            }

            .card-preview, .envelope {
                padding: 30px 20px;
            }

            .couple-names {
                font-size: 2rem;
            }

            .detail-text {
                font-size: 1rem;
            }

            .floral-top, .floral-bottom {
                opacity: 0.5;
                width: 150px;
                height: 100px;
            }

            nav ul {
                display: none;
            }

            .hamburger {
                display: block;
            }

            nav ul.active {
                display: flex;
                flex-direction: column;
                position: absolute;
                top: 70px;
                right: 20px;
                background: white;
                padding: 20px;
                border-radius: 10px;
                box-shadow: 0 5px 20px rgba(0, 0, 0, 0.15);
            }

            .countdown-item {
                min-width: 90px;
                padding: 20px;
            }

            .countdown-number {
                font-size: 2rem;
            }

            .couple-grid {
                grid-template-columns: 1fr;
            }

            .section-title {
                font-size: 2rem;
            }

            .gift-content, .rsvp-form-container {
                padding: 30px 20px;
            }

            .floating-menu {
                bottom: 20px;
                right: 20px;
            }

            .menu-item {
                width: 50px;
                height: 50px;
                font-size: 1.2rem;
            }
        }

        /* Print Styles */
        @media print {
            .welcome-container,
            .floating-menu,
            .gift-modal,
            header,
            footer {
                display: none !important;
            }

            .main-content-container {
                display: block !important;
                padding-top: 0;
            }

            body {
                background: white;
            }
        }
    </style>
</head>
<body>
    <!-- Welcome Screen with Envelope -->
    <div class="welcome-container" id="welcomeContainer">
        <div class="card-wrapper">
            <!-- Envelope -->
            <div class="envelope" id="envelope">
                <div class="envelope-content">
                    <div class="envelope-icon"><i class="fas fa-envelope"></i></div>
                    <div class="envelope-text">Click to Open</div>
                </div>
            </div>

            <!-- Card Preview -->
            <div class="card-preview" id="cardPreview">
                <!-- Floral Decorations -->
                <div class="floral-top"></div>
                <div class="floral-bottom"></div>

                <!-- Content -->
                <div class="card-header">
                    <div class="card-subtitle">Together with their families</div>
                    <div class="couple-names">{{ $details["bride_name"] ?? "Bride" }} & {{ $details["groom_name"] ?? "Groom" }}</div>
                    <div class="card-subtitle">invite you to their wedding celebration</div>
                </div>

                <div class="separator-line"></div>

                <!-- Event Details -->
                <div class="event-details">
                    <div class="detail-item">
                        <span class="detail-icon"><i class="fas fa-calendar-alt"></i></span>
                        <div class="detail-label">Date</div>
                        <div class="detail-text">{{ $details["wedding_date"] ?? "Saturday, 25th October 2025" }}</div>
                    </div>

                    <div class="detail-item">
                        <span class="detail-icon"><i class="fas fa-clock"></i></span>
                        <div class="detail-label">Time</div>
                        <div class="detail-text">{{ $details["wedding_time"] ?? "4:00 PM" }}</div>
                    </div>

                    <div class="detail-item">
                        <span class="detail-icon"><i class="fas fa-map-marker-alt"></i></span>
                        <div class="detail-label">Venue</div>
                        <div class="detail-text">{{ $details["venue_name"] ?? "Victoria Hotel" }}<br>{{ $details["venue_address"] ?? "120 Norris Road, Florida" }}</div>
                    </div>
                </div>

                <div class="separator-line"></div>

                <!-- Enter Button -->
                <div class="preview-actions">
                    <div class="preview-text">Click below to enter</div>
                    <button class="enter-button" id="enterBtn">Enter Invitation</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content-container" id="mainContent">
        <!-- Header & Navigation -->
        <header>
            <div class="nav-container">
                <a href="#" class="logo">{{ $details["bride_name"] ?? "B" }} & {{ $details["groom_name"] ?? "G" }}</a>
                <nav>
                    <ul class="nav-links" id="navLinks">
                        <li><a href="#countdown">Countdown</a></li>
                        <li><a href="#couple">Couple</a></li>
                        <li><a href="#events">Events</a></li>
                        <li><a href="#messages">Messages</a></li>
                        <li><a href="#rsvp">RSVP</a></li>
                    </ul>
                    <div class="hamburger" id="hamburger">
                        <i class="fas fa-bars"></i>
                    </div>
                </nav>
            </div>
        </header>

        <!-- Countdown Section -->
        <section id="countdown">
            <div class="countdown-container">
                <h2 class="countdown-title">{{ $details["countdown_title"] ?? "Counting Down to Our Special Day" }}</h2>
                <p class="countdown-subtitle">{{ $details["countdown_subtitle"] ?? "Join us as we count every moment until we say 'I do'" }}</p>
                
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
                    <h3>We Are Married!</h3>
                    <p>Thank you for being part of our special day</p>
                </div>

                <p class="countdown-message">{{ $details["countdown_message"] ?? "Can't wait to celebrate with you!" }}</p>
            </div>
        </section>

        <!-- Couple Section -->
        <section id="couple">
            <h2 class="section-title">Our Love Story</h2>
            <div class="couple-section">
                <div class="couple-grid">
                    <!-- Bride -->
                    <div class="couple-card">
                        <div class="couple-image-container">
                            @if($details["bride_photo"] ?? false)
                                <img src="{{ $details['bride_photo'] }}" alt="{{ $details['bride_name'] ?? 'Bride' }}">
                            @else
                                <div style="width: 100%; height: 100%; background: linear-gradient(135deg, #f8f3e9 0%, #ede7db 100%); display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-user" style="font-size: 4rem; color: var(--primary);"></i>
                                </div>
                            @endif
                        </div>
                        <h3 class="couple-name-title">{{ $details["bride_full_name"] ?? $details["bride_name"] ?? "Bride Full Name" }}</h3>
                        <div class="separator-line" style="margin: 20px auto; width: 60px;"></div>
                        <p class="couple-parents">
                            <strong>Daughter of:</strong><br>
                            {{ $details["bride_parents"] ?? "Mr. & Mrs. Bride's Parents" }}
                        </p>
                    </div>

                    <!-- Groom -->
                    <div class="couple-card">
                        <div class="couple-image-container">
                            @if($details["groom_photo"] ?? false)
                                <img src="{{ $details['groom_photo'] }}" alt="{{ $details['groom_name'] ?? 'Groom' }}">
                            @else
                                <div style="width: 100%; height: 100%; background: linear-gradient(135deg, #f8f3e9 0%, #ede7db 100%); display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-user" style="font-size: 4rem; color: var(--primary);"></i>
                                </div>
                            @endif
                        </div>
                        <h3 class="couple-name-title">{{ $details["groom_full_name"] ?? $details["groom_name"] ?? "Groom Full Name" }}</h3>
                        <div class="separator-line" style="margin: 20px auto; width: 60px;"></div>
                        <p class="couple-parents">
                            <strong>Son of:</strong><br>
                            {{ $details["groom_parents"] ?? "Mr. & Mrs. Groom's Parents" }}
                        </p>
                    </div>
                </div>

                @if($details["couple_story"] ?? false)
                <div style="text-align: center; margin-top: 50px; padding: 30px; background: linear-gradient(135deg, rgba(200, 161, 101, 0.05) 0%, transparent 100%); border-radius: 15px;">
                    <i class="fas fa-heart" style="font-size: 2rem; color: var(--primary); margin-bottom: 20px;"></i>
                    <p style="font-size: 1.1rem; line-height: 1.8; color: #666; max-width: 700px; margin: 0 auto;">{{ $details["couple_story"] }}</p>
                </div>
                @endif
            </div>
        </section>

        <!-- Events Section -->
        <section id="events">
            <h2 class="section-title">Wedding Events</h2>
            <div class="events-card">
                <div class="event-item">
                    <h3 class="event-title"><i class="fas fa-calendar-alt event-icon"></i>Wedding Date</h3>
                    <p class="event-info">{{ $details["wedding_date"] ?? "Saturday, 25th October 2025" }}</p>
                    @if($details["hijri_date"] ?? false)
                    <p class="event-info" style="color: #888; font-size: 0.95rem;">{{ $details["hijri_date"] }}</p>
                    @endif
                </div>

                @if($details["reception_meal_time"] ?? false)
                <div class="event-item">
                    <h3 class="event-title"><i class="fas fa-utensils event-icon"></i>{{ $details["reception_meal_label"] ?? "Reception Meal" }}</h3>
                    <p class="event-info"><i class="fas fa-clock event-icon"></i>{{ $details["reception_meal_time"] }}</p>
                </div>
                @endif

                @if($details["bride_arrival_time"] ?? false)
                <div class="event-item">
                    <h3 class="event-title"><i class="fas fa-ring event-icon"></i>{{ $details["bride_arrival_label"] ?? "Bride Arrival" }}</h3>
                    <p class="event-info"><i class="fas fa-clock event-icon"></i>{{ $details["bride_arrival_time"] }}</p>
                </div>
                @endif

                <div class="event-item">
                    <h3 class="event-title"><i class="fas fa-map-marker-alt event-icon"></i>Venue</h3>
                    <p class="event-info"><strong>{{ $details["venue_name"] ?? "The Wedding Venue" }}</strong></p>
                    @if($details["venue_subtitle"] ?? false)
                    <p class="event-info" style="color: #888;">{{ $details["venue_subtitle"] }}</p>
                    @endif
                    <p class="event-info">{{ $details["venue_address"] ?? "123 Wedding Street, City, State" }}</p>
                    @if($details["venue_map_link"] ?? false)
                    <a href="{{ $details['venue_map_link'] }}" target="_blank" style="display: inline-block; margin-top: 15px; padding: 10px 25px; background: var(--primary); color: white; text-decoration: none; border-radius: 25px; transition: var(--transition);">
                        <i class="fas fa-directions"></i> Get Directions
                    </a>
                    @endif
                </div>

                @if(($details["contact_person_1"] ?? false) || ($details["contact_person_2"] ?? false))
                <div class="event-item">
                    <h3 class="event-title"><i class="fas fa-phone event-icon"></i>Contact</h3>
                    @if($details["contact_person_1"] ?? false)
                    <p class="event-info">{{ $details["contact_person_1"] }}: {{ $details["contact_number_1"] ?? "" }}</p>
                    @endif
                    @if($details["contact_person_2"] ?? false)
                    <p class="event-info">{{ $details["contact_person_2"] }}: {{ $details["contact_number_2"] ?? "" }}</p>
                    @endif
                    @if($details["contact_person_3"] ?? false)
                    <p class="event-info">{{ $details["contact_person_3"] }}: {{ $details["contact_number_3"] ?? "" }}</p>
                    @endif
                </div>
                @endif
            </div>

            @if($details["venue_map_embed"] ?? false)
            <div class="map-container">
                <iframe src="{{ $details['venue_map_embed'] }}" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>
            @endif
        </section>

        <!-- Messages Section -->
        <section id="messages">
            <h2 class="section-title">Messages & Wishes</h2>
            <div class="messages-container">
                <div class="messages-scroll" id="messagesScroll">
                    <div class="no-messages" id="noMessages">
                        <div class="no-messages-icon">
                            <i class="fas fa-heart"></i>
                        </div>
                        <h3>Be the first to leave a message!</h3>
                        <p>Share your wishes for the happy couple by submitting your RSVP below.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- RSVP Section -->
        <section id="rsvp">
            <h2 class="section-title">RSVP</h2>
            
            <!-- Success Message -->
            <div id="rsvp-success" class="alert alert-success" style="display: none; max-width: 600px; margin: 0 auto 2rem;">
                <div>
                    <i class="fas fa-check-circle" style="font-size: 1.5rem;"></i>
                </div>
                <div>
                    <h4>Thank You!</h4>
                    <p id="success-message"></p>
                </div>
            </div>

            <!-- Error Message -->
            <div id="rsvp-error" class="alert alert-danger" style="display: none; max-width: 600px; margin: 0 auto 2rem;">
                <div>
                    <i class="fas fa-exclamation-circle" style="font-size: 1.5rem;"></i>
                </div>
                <div>
                    <h4>Error</h4>
                    <p id="error-message"></p>
                </div>
            </div>

            <div class="rsvp-form-container">
                <p style="text-align: center; color: #666; margin-bottom: 30px; line-height: 1.6;">
                    Please let us know if you'll be able to join us for our special day. Your presence would mean the world to us!
                </p>
                
                <form id="rsvpForm">
                    <div class="form-group">
                        <label for="guestName">Full Name *</label>
                        <input type="text" id="guestName" name="guest_name" class="form-control" required>
                        <div class="invalid-feedback"></div>
                    </div>
                    
                    <div class="form-group">
                        <label for="guestEmail">Email *</label>
                        <input type="email" id="guestEmail" name="guest_email" class="form-control" required>
                        <div class="invalid-feedback"></div>
                    </div>
                    
                    <div class="form-group">
                        <label for="guestPhone">Phone Number</label>
                        <input type="tel" id="guestPhone" name="guest_phone" class="form-control">
                        <div class="invalid-feedback"></div>
                    </div>
                    
                    <div class="form-group">
                        <label for="attendance">Will you attend? *</label>
                        <select id="attendance" name="attendance_status" class="form-control" required>
                            <option value="">Select...</option>
                            <option value="yes">Yes, I will attend</option>
                            <option value="no">Sorry, I cannot attend</option>
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>
                    
                    <div class="form-group" id="guestsGroup" style="display: none;">
                        <label for="numGuests">Number of Guests *</label>
                        <input type="number" id="numGuests" name="number_of_guests" class="form-control" min="1" max="10" value="1">
                        <div class="invalid-feedback"></div>
                    </div>
                    
                    <div class="form-group">
                        <label for="message">Message</label>
                        <textarea id="message" name="message" class="form-control" rows="4" placeholder="Leave your best wishes..."></textarea>
                        <div class="invalid-feedback"></div>
                    </div>
                    
                    <button type="submit" class="btn-primary" id="rsvpSubmitBtn">
                        <span class="btn-text">Submit RSVP</span>
                        <span class="btn-loading" style="display: none;">
                            <i class="fas fa-spinner fa-spin"></i> Sending...
                        </span>
                    </button>
                </form>
            </div>
        </section>

        <!-- Footer -->
        <footer>
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
        </footer>
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
                <input type="number" id="customAmount" placeholder="Enter custom amount (RM)" min="1" onchange="customAmountChange()">
            </div>
            <button class="give-btn" onclick="sendGift()">Send Gift</button>
        </div>
    </div>

    <!-- Floating Action Menu -->
    <div class="floating-menu" id="floatingMenu">
        <div class="menu-item" onclick="openGiftModal()">
            <i class="fas fa-gift"></i>
        </div>
        <div class="menu-item" onclick="scrollToTop()">
            <i class="fas fa-arrow-up"></i>
        </div>
    </div>

    <script>
        // Envelope opening animation
        const envelope = document.getElementById('envelope');
        const cardPreview = document.getElementById('cardPreview');
        const enterBtn = document.getElementById('enterBtn');
        const welcomeContainer = document.getElementById('welcomeContainer');
        const mainContent = document.getElementById('mainContent');
        const floatingMenu = document.getElementById('floatingMenu');

        envelope.addEventListener('click', () => {
            envelope.classList.add('opened');
            setTimeout(() => {
                cardPreview.classList.add('visible');
            }, 300);
        });

        // Enter invitation
        enterBtn.addEventListener('click', () => {
            welcomeContainer.classList.add('hidden');
            setTimeout(() => {
                mainContent.classList.add('active');
                floatingMenu.classList.add('active');
                initCountdown();
                initializeMessagesDisplay();
            }, 500);
        });

        // Navigation Toggle
        const hamburger = document.getElementById('hamburger');
        const navLinks = document.getElementById('navLinks');

        if (hamburger && navLinks) {
            hamburger.addEventListener('click', () => {
                navLinks.classList.toggle('active');
            });

            // Close mobile menu when clicking on a link
            const links = navLinks.querySelectorAll('a');
            links.forEach(link => {
                link.addEventListener('click', () => {
                    navLinks.classList.remove('active');
                });
            });
        }

        // Smooth scrolling
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Scroll to top
        function scrollToTop() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        }

        // Countdown Timer
        function initCountdown() {
            const weddingDateStr = "{{ $details['wedding_datetime'] ?? $details['wedding_date'] ?? '2025-12-31 18:00:00' }}";
            
            // Parse the date
            let weddingDate;
            try {
                weddingDate = new Date(weddingDateStr);
                if (isNaN(weddingDate.getTime())) {
                    throw new Error('Invalid date');
                }
            } catch (e) {
                console.error('Error parsing wedding date:', e);
                weddingDate = new Date('2025-12-31 18:00:00');
            }

            const daysElement = document.getElementById('days');
            const hoursElement = document.getElementById('hours');
            const minutesElement = document.getElementById('minutes');
            const secondsElement = document.getElementById('seconds');
            const countdownTimer = document.getElementById('countdownTimer');
            const countdownExpired = document.getElementById('countdownExpired');

            function updateCountdown() {
                const now = new Date().getTime();
                const timeLeft = weddingDate.getTime() - now;

                if (timeLeft < 0) {
                    countdownTimer.style.display = 'none';
                    countdownExpired.classList.add('active');
                    return;
                }

                const days = Math.floor(timeLeft / (1000 * 60 * 60 * 24));
                const hours = Math.floor((timeLeft % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const minutes = Math.floor((timeLeft % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((timeLeft % (1000 * 60)) / 1000);

                if (daysElement) daysElement.textContent = String(days).padStart(2, '0');
                if (hoursElement) hoursElement.textContent = String(hours).padStart(2, '0');
                if (minutesElement) minutesElement.textContent = String(minutes).padStart(2, '0');
                if (secondsElement) secondsElement.textContent = String(seconds).padStart(2, '0');
            }

            updateCountdown();
            setInterval(updateCountdown, 1000);
        }

        // RSVP Form
        const rsvpForm = document.getElementById('rsvpForm');
        const attendance = document.getElementById('attendance');
        const guestsGroup = document.getElementById('guestsGroup');
        const rsvpSubmitBtn = document.getElementById('rsvpSubmitBtn');

        // Show/hide guests field
        attendance.addEventListener('change', () => {
            if (attendance.value === 'yes') {
                guestsGroup.style.display = 'block';
                document.getElementById('numGuests').required = true;
            } else {
                guestsGroup.style.display = 'none';
                document.getElementById('numGuests').required = false;
            }
        });

        // Form submission
        rsvpForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            clearFormErrors();
            hideMessages();
            
            // Show loading state
            rsvpSubmitBtn.disabled = true;
            rsvpSubmitBtn.querySelector('.btn-text').style.display = 'none';
            rsvpSubmitBtn.querySelector('.btn-loading').style.display = 'inline';
            
            const formData = new FormData(rsvpForm);
            const uniqueUrl = window.location.pathname.split('/').pop();
            
            try {
                const response = await fetch(`/wedding-card/${uniqueUrl}/rsvp`, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                
                const data = await response.json();
                
                if (data.success) {
                    showSuccessMessage(data.message);
                    rsvpForm.reset();
                    guestsGroup.style.display = 'none';
                    
                    // Reload messages if message was submitted
                    const messageField = formData.get('message');
                    if (messageField && messageField.trim()) {
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
            } catch (error) {
                console.error('Error:', error);
                showErrorMessage('An error occurred. Please try again.');
            } finally {
                rsvpSubmitBtn.disabled = false;
                rsvpSubmitBtn.querySelector('.btn-text').style.display = 'inline';
                rsvpSubmitBtn.querySelector('.btn-loading').style.display = 'none';
            }
        });

        function showSuccessMessage(message) {
            const successDiv = document.getElementById('rsvp-success');
            const messageP = document.getElementById('success-message');
            messageP.textContent = message;
            successDiv.style.display = 'flex';
            successDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }

        function showErrorMessage(message) {
            const errorDiv = document.getElementById('rsvp-error');
            const messageP = document.getElementById('error-message');
            messageP.textContent = message;
            errorDiv.style.display = 'flex';
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
                if (input) {
                    input.classList.add('is-invalid');
                    const errorDiv = input.parentNode.querySelector('.invalid-feedback');
                    if (errorDiv) {
                        errorDiv.textContent = errors[field][0];
                        errorDiv.style.display = 'block';
                    }
                }
            }
        }

        // Initialize Messages Display
        function initializeMessagesDisplay() {
            const messagesScroll = document.getElementById('messagesScroll');
            const noMessages = document.getElementById('noMessages');
            
            // Check if we have messages from the backend
            if (window.rsvpMessages && window.rsvpMessages.length > 0) {
                populateMessages(window.rsvpMessages);
            }
        }

        function populateMessages(messages) {
            const messagesScroll = document.getElementById('messagesScroll');
            const noMessages = document.getElementById('noMessages');
            
            if (!messagesScroll) return;
            
            noMessages.style.display = 'none';
            messagesScroll.innerHTML = '';
            
            messages.forEach(msg => {
                const messageCard = document.createElement('div');
                messageCard.className = 'message-card';
                messageCard.innerHTML = `
                    <div class="message-author">${escapeHtml(msg.guest_name)}</div>
                    <div class="message-text">${escapeHtml(msg.message)}</div>
                `;
                messagesScroll.appendChild(messageCard);
            });
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        // Gift Modal
        let selectedAmount = 0;

        function openGiftModal() {
            const modal = document.getElementById('giftModal');
            if (modal) {
                modal.classList.add('active');
                document.body.style.overflow = 'hidden';
            }
        }

        function closeGiftModal() {
            const modal = document.getElementById('giftModal');
            if (modal) {
                modal.classList.remove('active');
                document.body.style.overflow = '';
                resetGiftSelections();
            }
        }

        function selectAmount(amount, element) {
            selectedAmount = amount;
            
            // Reset all selections
            document.querySelectorAll('.gift-amount').forEach(el => {
                el.classList.remove('selected');
            });
            
            // Highlight selected
            element.classList.add('selected');
            
            // Clear custom amount
            const customInput = document.getElementById('customAmount');
            if (customInput) {
                customInput.value = '';
            }
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

        async function sendGift() {
            if (selectedAmount <= 0) {
                alert('Please select or enter a gift amount.');
                return;
            }

            const weddingCardId = window.weddingCardId;
            if (!weddingCardId) {
                alert('Wedding card information not found.');
                return;
            }

            try {
                const response = await fetch(`/wedding-card/${weddingCardId}/gift/create-bill`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        amount: selectedAmount
                    })
                });

                const data = await response.json();

                if (data.success && data.billUrl) {
                    // Redirect to payment page
                    window.location.href = data.billUrl;
                } else {
                    alert(data.message || 'Failed to create payment. Please try again.');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
            }
        }

        function resetGiftSelections() {
            selectedAmount = 0;
            document.querySelectorAll('.gift-amount').forEach(el => {
                el.classList.remove('selected');
            });
            const customInput = document.getElementById('customAmount');
            if (customInput) {
                customInput.value = '';
            }
        }

        // Close gift modal when clicking outside
        document.getElementById('giftModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeGiftModal();
            }
        });

        // Header scroll effect
        window.addEventListener('scroll', () => {
            const header = document.querySelector('header');
            if (window.scrollY > 50) {
                header.style.boxShadow = '0 4px 30px rgba(0, 0, 0, 0.15)';
            } else {
                header.style.boxShadow = '0 2px 20px rgba(0, 0, 0, 0.08)';
            }
        });
    </script>
</body>
</html>

