<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $details["bride_name"] ?? "Bride" }} & {{ $details["groom_name"] ?? "Groom" }} - Wedding Invitation</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Great+Vibes&family=Poppins:wght@300;400;500;600;700&family=Dancing+Script:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #f7f3e9 0%, #e8dcc6 100%);
            overflow: hidden;
            height: 100vh;
        }

        /* Paper Door Animation Styles */
        .paper-container {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #f7f3e9 0%, #e8dcc6 100%);
            z-index: 10000;
            transition: all 1s ease-in-out;
        }

        .paper-container.opened {
            opacity: 0;
            visibility: hidden;
            pointer-events: none;
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
            background: linear-gradient(135deg, #ffffff 0%, #f8f8f8 50%, #f5f5f5 100%);
            border: 1px solid #e0e0e0;
            transition: transform 1.8s cubic-bezier(0.4, 0.0, 0.2, 1);
            box-shadow: 
                0 0 20px rgba(0, 0, 0, 0.15),
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
                rgba(0,0,0,0.03) 10%, 
                rgba(0,0,0,0.08) 50%, 
                rgba(0,0,0,0.03) 90%, 
                transparent 100%);
            pointer-events: none;
        }

        .paper-door.left {
            left: 0;
            transform-origin: left center;
            border-right: 1px solid #d0d0d0;
            background: linear-gradient(90deg, #ffffff 0%, #f8f8f8 80%, #f0f0f0 100%);
            z-index: 5; /* Higher than right door but lower than button */
        }

        .paper-door.left::before {
            background: linear-gradient(90deg, 
                rgba(0,0,0,0.02) 0%, 
                rgba(0,0,0,0.05) 70%, 
                rgba(0,0,0,0.15) 100%);
        }

        .paper-door.right {
            right: 0;
            transform-origin: right center;
            border-left: 1px solid #d0d0d0;
            background: linear-gradient(90deg, #f0f0f0 0%, #f8f8f8 20%, #ffffff 100%);
            z-index: 1; /* Lower z-index so button appears above it */
        }

        .paper-door.right::before {
            background: linear-gradient(90deg, 
                rgba(0,0,0,0.15) 0%, 
                rgba(0,0,0,0.05) 30%, 
                rgba(0,0,0,0.02) 100%);
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
            right: -120px; /* Position it to overlap the center line more */
            transform: translateY(-50%);
            width: 240px;
            height: 160px;
            background: linear-gradient(145deg, #ffffff 0%, #f8f8f8 100%);
            border-radius: 80px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            z-index: 10; /* Higher z-index to appear above right paper */
            box-shadow: 
                0 12px 35px rgba(0, 0, 0, 0.2),
                0 6px 15px rgba(0, 0, 0, 0.15),
                inset 0 1px 0 rgba(255, 255, 255, 0.9);
            transition: all 0.3s ease;
            border: 1px solid #e0e0e0;
        }

        .center-button:hover {
            transform: translateY(-50%) scale(1.05);
            box-shadow: 
                0 15px 40px rgba(0, 0, 0, 0.25),
                0 8px 20px rgba(0, 0, 0, 0.2),
                inset 0 1px 0 rgba(255, 255, 255, 1);
        }

        .paper-doors.opening .center-button {
            /* Keep button visible and let it follow the left paper naturally */
            opacity: 1;
            transform: translateY(-50%) scale(1);
        }

        .center-button .couple-names {
            font-family: 'Great Vibes', cursive;
            font-size: 2.2rem;
            color: #8b4513;
            margin-bottom: 8px;
            text-align: center;
            line-height: 1.1;
        }

        .center-button .buka-text {
            font-size: 1rem;
            color: #666;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 3px;
        }



        /* Main Content Styles */
        .main-content {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow-y: auto;
            background: #faf8f3;
            opacity: 0;
            visibility: hidden;
            transition: all 0.8s ease;
        }

        .main-content.visible {
            opacity: 1;
            visibility: visible;
        }

        /* Wedding Card Content */
        .wedding-container {
            max-width: 600px;
            margin: 0 auto;
            background: #fffef9;
            min-height: 100vh;
            position: relative;
            box-shadow: 0 0 30px rgba(139, 69, 19, 0.15);
        }

        .hero-section {
            background: linear-gradient(rgba(139, 69, 19, 0.8), rgba(139, 69, 19, 0.8)), 
                        url('{{ $details["hero_image"] ?? "https://images.unsplash.com/photo-1519225421980-715cb0215aed?ixlib=rb-4.0.3&auto=format&fit=crop&w=2070&q=80" }}');
            background-size: cover;
            background-position: center;
            height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: white;
            position: relative;
        }

        .hero-ornament {
            position: absolute;
            top: 50px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 80px;
            background: rgba(255, 248, 231, 0.3);
            border: 2px solid rgba(255, 248, 231, 0.5);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            color: #fff8e7;
            box-shadow: 0 4px 15px rgba(139, 69, 19, 0.2);
        }

        .hero-names {
            font-family: 'Great Vibes', cursive;
            font-size: 4rem;
            margin: 20px 0;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }

        .hero-subtitle {
            font-size: 1.2rem;
            text-transform: uppercase;
            letter-spacing: 3px;
            margin-bottom: 30px;
        }

        .hero-date {
            font-size: 1.5rem;
            font-weight: 300;
            margin-bottom: 40px;
        }

        .scroll-indicator {
            position: absolute;
            bottom: 30px;
            left: 50%;
            transform: translateX(-50%);
            color: white;
            animation: bounce 2s infinite;
        }

        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% { transform: translateX(-50%) translateY(0); }
            40% { transform: translateX(-50%) translateY(-10px); }
            60% { transform: translateX(-50%) translateY(-5px); }
        }

        /* Content Sections */
        .content-section {
            padding: 60px 40px;
            text-align: center;
        }

        .section-title {
            font-family: 'Great Vibes', cursive;
            font-size: 2.5rem;
            color: #8b4513;
            margin-bottom: 30px;
        }

        .bismillah-section {
            background: #f9f6f0;
            border-top: 3px solid #8b4513;
            border-bottom: 3px solid #8b4513;
        }

        .bismillah-text {
            font-family: 'Dancing Script', cursive;
            font-size: 1.8rem;
            color: #8b4513;
            line-height: 1.6;
            margin-bottom: 20px;
        }

        .bismillah-translation {
            font-style: italic;
            color: #666;
            font-size: 1rem;
        }

        .couple-info {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            margin: 40px 0;
        }

        .couple-card {
            text-align: center;
        }

        .couple-photo {
            width: 200px;
            height: 200px;
            border-radius: 50%;
            margin: 0 auto 20px;
            background: #ddd;
            background-size: cover;
            background-position: center;
            border: 5px solid #8b4513;
            position: relative;
            overflow: hidden;
        }

        .groom-photo {
            background-image: url('{{ $details["groom_photo"] ?? "https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80" }}');
        }

        .bride-photo {
            background-image: url('{{ $details["bride_photo"] ?? "https://images.unsplash.com/photo-1494790108755-2616c4e50b47?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80" }}');
        }

        .couple-name {
            font-family: 'Great Vibes', cursive;
            font-size: 2rem;
            color: #8b4513;
            margin-bottom: 10px;
        }

        .couple-details {
            color: #666;
            line-height: 1.6;
        }

        .event-details {
            background: linear-gradient(135deg, #f9f6f0 0%, #f0e8d6 100%);
            color: #4a3728;
        }

        .event-card {
            background: rgba(139, 69, 19, 0.1);
            border-radius: 15px;
            padding: 30px;
            margin: 20px 0;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(139, 69, 19, 0.2);
            box-shadow: 0 4px 15px rgba(139, 69, 19, 0.1);
        }

        .event-title {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 15px;
        }

        .event-date {
            font-size: 1.2rem;
            margin-bottom: 10px;
        }

        .event-time {
            font-size: 1rem;
            margin-bottom: 20px;
        }

        .event-location {
            font-size: 1rem;
            line-height: 1.6;
        }

        .map-section {
            background: #f9f6f0;
        }

        .map-container {
            width: 100%;
            height: 300px;
            background: linear-gradient(135deg, #e8dcc6 0%, #d4c4a8 100%);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #8b4513;
            margin-top: 20px;
            border: 1px solid rgba(139, 69, 19, 0.2);
        }

        .rsvp-section {
            background: linear-gradient(135deg, #d4c4a8 0%, #b8a082 100%);
            color: #4a3728;
        }

        .rsvp-form {
            max-width: 400px;
            margin: 0 auto;
        }

        .form-group {
            margin-bottom: 20px;
            text-align: left;
        }

        .form-label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
            color: #4a3728;
        }

        .form-input {
            width: 100%;
            padding: 12px;
            border: 1px solid rgba(139, 69, 19, 0.2);
            border-radius: 8px;
            font-size: 1rem;
            background: rgba(255, 254, 249, 0.95);
            color: #4a3728;
        }

        .form-textarea {
            resize: vertical;
            min-height: 100px;
        }

        .form-select {
            width: 100%;
            padding: 12px;
            border: 1px solid rgba(139, 69, 19, 0.2);
            border-radius: 8px;
            font-size: 1rem;
            background: rgba(255, 254, 249, 0.95);
            color: #4a3728;
        }

        .submit-btn {
            background: #8b4513;
            color: white;
            padding: 15px 40px;
            border: none;
            border-radius: 25px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .submit-btn:hover {
            background: #a0522d;
            transform: translateY(-2px);
        }

        .footer-section {
            background: linear-gradient(135deg, #8b4513 0%, #a0522d 100%);
            color: #fff8e7;
            padding: 40px;
            text-align: center;
        }

        .footer-text {
            font-family: 'Great Vibes', cursive;
            font-size: 1.5rem;
            margin-bottom: 10px;
        }

        /* Bottom Navigation Bar */
        .bottom-nav {
            position: fixed;
            bottom: 0;
            left: 50%;
            transform: translateX(-51.5%);
            width: 100%;
            max-width: 600px;
            background: linear-gradient(135deg, #8b4513 0%, #a0522d 100%);
            padding: 15px 0;
            z-index: 1000;
            box-shadow: 0 -4px 20px rgba(139, 69, 19, 0.3);
            border-top: 1px solid rgba(255, 248, 231, 0.2);
        }

        .nav-container {
            display: flex;
            justify-content: space-around;
            align-items: center;
            padding: 0 20px;
        }

        .nav-button {
            display: flex;
            flex-direction: column;
            align-items: center;
            color: #fff8e7;
            text-decoration: none;
            padding: 8px 12px;
            border-radius: 12px;
            transition: all 0.3s ease;
            cursor: pointer;
            border: none;
            background: transparent;
            font-family: 'Poppins', sans-serif;
        }

        .nav-button:hover {
            background: rgba(255, 248, 231, 0.1);
            transform: translateY(-2px);
        }

        .nav-button i {
            font-size: 1.2rem;
            margin-bottom: 4px;
        }

        .nav-button span {
            font-size: 0.8rem;
            font-weight: 500;
        }

        /* Contact Popup */
        .popup-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 2000;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }

        .popup-overlay.active {
            opacity: 1;
            visibility: visible;
        }

        .popup-content {
            position: absolute;
            bottom: 120px;
            left: 50%;
            transform: translateX(-50%) translateY(100%);
            background: #8b4513;
            border-radius: 20px 20px 8px 8px;
            padding: 25px;
            max-width: 350px;
            width: 90%;
            transition: all 0.4s cubic-bezier(0.4, 0.0, 0.2, 1);
            box-shadow: 0 8px 30px rgba(139, 69, 19, 0.4);
        }

        .popup-overlay.active .popup-content {
            transform: translateX(-50%) translateY(0);
        }

        .popup-header {
            color: #fff8e7;
            font-size: 1.3rem;
            font-weight: 600;
            margin-bottom: 20px;
            text-align: center;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .contact-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid rgba(255, 248, 231, 0.2);
        }

        .contact-item:last-child {
            border-bottom: none;
        }

        .contact-info {
            flex: 1;
        }

        .contact-name {
            color: #fff8e7;
            font-weight: 600;
            font-size: 1rem;
            margin-bottom: 4px;
        }

        .contact-role {
            color: rgba(255, 248, 231, 0.7);
            font-size: 0.85rem;
            font-style: italic;
        }

        .contact-actions {
            display: flex;
            gap: 10px;
        }

        .contact-btn {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: rgba(255, 248, 231, 0.1);
            border: 1px solid rgba(255, 248, 231, 0.3);
            color: #fff8e7;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            transition: all 0.3s ease;
            font-size: 1rem;
        }

        .contact-btn:hover {
            background: rgba(255, 248, 231, 0.2);
            transform: scale(1.1);
        }

        .close-popup {
            position: absolute;
            top: 10px;
            right: 15px;
            background: none;
            border: none;
            color: #fff8e7;
            font-size: 1.5rem;
            cursor: pointer;
            padding: 5px;
            border-radius: 50%;
            transition: all 0.3s ease;
        }

        .close-popup:hover {
            background: rgba(255, 248, 231, 0.1);
        }

        /* Location Popup */
        .location-popup {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 2000;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }

        .location-popup.active {
            opacity: 1;
            visibility: visible;
        }

        .location-content {
            position: absolute;
            bottom: 120px;
            left: 50%;
            transform: translateX(-50%) translateY(100%);
            background: #8b4513;
            border-radius: 20px 20px 8px 8px;
            padding: 25px;
            max-width: 350px;
            width: 90%;
            transition: all 0.4s cubic-bezier(0.4, 0.0, 0.2, 1);
            box-shadow: 0 8px 30px rgba(139, 69, 19, 0.4);
        }

        .location-popup.active .location-content {
            transform: translateX(-50%) translateY(0);
        }

        .location-header {
            color: #fff8e7;
            font-size: 1.3rem;
            font-weight: 600;
            margin-bottom: 20px;
            text-align: center;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .location-address {
            color: #fff8e7;
            text-align: center;
            margin-bottom: 25px;
            line-height: 1.6;
        }

        .location-venue {
            font-weight: 600;
            font-size: 1.1rem;
            margin-bottom: 10px;
        }

        .location-details {
            font-size: 0.95rem;
            opacity: 0.9;
        }

        .location-actions {
            display: flex;
            gap: 15px;
        }

        .location-btn {
            flex: 1;
            padding: 12px 20px;
            border-radius: 25px;
            background: rgba(255, 248, 231, 0.1);
            border: 1px solid rgba(255, 248, 231, 0.3);
            color: #fff8e7;
            text-decoration: none;
            text-align: center;
            font-weight: 600;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .location-btn:hover {
            background: rgba(255, 248, 231, 0.2);
            transform: translateY(-2px);
        }

        .location-btn i {
            font-size: 1rem;
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
        }

        .gift-modal.active {
            opacity: 1;
            visibility: visible;
        }

        .gift-content {
            background-color: #fffef9;
            border-radius: 15px;
            padding: 30px;
            width: 90%;
            max-width: 500px;
            max-height: 90vh;
            overflow-y: auto;
            position: relative;
            box-shadow: 0 10px 30px rgba(139, 69, 19, 0.2);
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
            color: #8b4513;
            transition: color 0.3s ease;
            background: none;
            border: none;
            padding: 5px;
            border-radius: 50%;
        }

        .gift-close:hover {
            color: #a0522d;
            background: rgba(139, 69, 19, 0.1);
        }

        .gift-title {
            font-family: 'Great Vibes', cursive;
            font-size: 2.5rem;
            color: #8b4513;
            margin-bottom: 10px;
            text-align: center;
        }

        .gift-subtitle {
            font-size: 0.9rem;
            color: #666;
            margin-bottom: 25px;
            text-align: center;
            line-height: 1.5;
        }

        .gift-form {
            width: 100%;
        }

        .gift-step {
            width: 100%;
        }

        .step-title {
            font-size: 1.3rem;
            color: #8b4513;
            margin-bottom: 20px;
            text-align: center;
            font-weight: 600;
        }

        .gift-options {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 20px;
        }

        .gift-amount {
            padding: 12px 25px;
            border: 2px solid #8b4513;
            border-radius: 30px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            background-color: #fffef9;
            color: #8b4513;
            min-width: 80px;
            text-align: center;
        }

        .gift-amount:hover, .gift-amount.selected {
            background-color: #8b4513;
            color: #fffef9;
            transform: translateY(-2px);
        }

        .custom-amount {
            margin: 20px 0;
        }

        .custom-amount input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e8dcc6;
            border-radius: 8px;
            font-size: 1rem;
            text-align: center;
            background: #fffef9;
            color: #4a3728;
        }

        .custom-amount input:focus {
            outline: none;
            border-color: #8b4513;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 6px;
            font-weight: 600;
            color: #4a3728;
            font-size: 0.9rem;
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e8dcc6;
            border-radius: 8px;
            font-size: 0.9rem;
            background: #fffef9;
            color: #4a3728;
            transition: all 0.3s ease;
        }

        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #8b4513;
            box-shadow: 0 0 0 3px rgba(139, 69, 19, 0.1);
        }

        .form-group textarea {
            resize: vertical;
            min-height: 80px;
        }

        .gift-summary {
            background: #f9f6f0;
            border-radius: 8px;
            padding: 15px;
            margin: 20px 0;
            border: 1px solid #e8dcc6;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-weight: 600;
            color: #4a3728;
        }

        .summary-row span:last-child {
            color: #8b4513;
            font-size: 1.1rem;
        }

        .gift-buttons {
            display: flex;
            gap: 15px;
            margin-top: 25px;
        }

        .continue-btn,
        .give-btn,
        .back-btn {
            padding: 12px 25px;
            border: none;
            border-radius: 25px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .continue-btn,
        .give-btn {
            background-color: #8b4513;
            color: #fffef9;
            flex: 1;
        }

        .continue-btn:hover,
        .give-btn:hover {
            background-color: #a0522d;
            transform: translateY(-2px);
        }

        .back-btn {
            background-color: transparent;
            color: #8b4513;
            border: 2px solid #8b4513;
            flex: 0 0 auto;
            min-width: 100px;
        }

        .back-btn:hover {
            background-color: #8b4513;
            color: #fffef9;
        }

        .give-btn:disabled {
            background-color: #ccc;
            cursor: not-allowed;
            transform: none;
        }

        .btn-loading {
            display: none;
        }

        /* Add bottom padding to main content to account for bottom nav */
        .main-content {
            padding-bottom: 80px;
        }

        /* Responsive */
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

            .hero-names {
                font-size: 3rem;
            }

            .couple-info {
                grid-template-columns: 1fr;
                gap: 30px;
            }

            .couple-photo {
                width: 150px;
                height: 150px;
            }

            .content-section {
                padding: 40px 20px;
            }

            .nav-button span {
                font-size: 0.75rem;
            }

            .nav-button i {
                font-size: 1.1rem;
            }

            .popup-content {
                bottom: 110px;
                padding: 20px;
            }

            .location-content {
                bottom: 110px;
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <!-- Paper Door Animation Container -->
    <div class="paper-container" id="paperContainer">
        <div class="paper-doors" id="paperDoors">
            <div class="paper-door left">
                <div class="center-button" id="centerButton">
                    <div class="couple-names">{{ $details["groom_name"] ?? "Groom" }}<br>{{ $details["bride_name"] ?? "Bride" }}</div>
                    <div class="buka-text">BUKA</div>
                </div>
            </div>
            <div class="paper-door right"></div>
        </div>
    </div>

    <!-- Main Wedding Card Content -->
    <div class="main-content" id="mainContent">
        <div class="wedding-container">
            <!-- Hero Section -->
            <section class="hero-section">
                <div class="hero-ornament">
                    <i class="fas fa-heart"></i>
                </div>
                <h1 class="hero-names">{{ $details["groom_name"] ?? "Groom" }} & {{ $details["bride_name"] ?? "Bride" }}</h1>
                <p class="hero-subtitle">Wedding Invitation</p>
                <p class="hero-date">{{ $details["wedding_date"] ?? "Wedding Date" }}</p>
                <div class="scroll-indicator">
                    <i class="fas fa-chevron-down"></i>
                </div>
            </section>

            <!-- Bismillah Section -->
            <section class="content-section bismillah-section">
                <h2 class="section-title">Bismillahirrahmanirrahim</h2>
                <p class="bismillah-text">
                    {{ $details["bismillah_text"] ?? '"Dan di antara tanda-tanda (kebesaran)-Nya ialah Dia menciptakan pasangan-pasangan untukmu dari jenismu sendiri, agar kamu cenderung dan merasa tenteram kepadanya, dan Dia menjadikan di antaramu rasa kasih dan sayang."' }}
                </p>
                <p class="bismillah-translation">{{ $details["bismillah_translation"] ?? "- Surah Ar-Rum: 21 -" }}</p>
            </section>

            <!-- Couple Section -->
            <section class="content-section">
                <h2 class="section-title">The Couple</h2>
                <div class="couple-info">
                    <div class="couple-card">
                        <div class="couple-photo groom-photo"></div>
                        <h3 class="couple-name">{{ $details["groom_name"] ?? "Groom" }}</h3>
                        <p class="couple-details">
                            Son of<br>
                            {{ $details["groom_parents"] ?? "Mr. Hafiz Ahmad & Mrs. Siti Fatimah" }}
                        </p>
                    </div>
                    <div class="couple-card">
                        <div class="couple-photo bride-photo"></div>
                        <h3 class="couple-name">{{ $details["bride_name"] ?? "Bride" }}</h3>
                        <p class="couple-details">
                            Daughter of<br>
                            {{ $details["bride_parents"] ?? "Mr. Ahmad Rahman & Mrs. Khadijah Ali" }}
                        </p>
                    </div>
                </div>
            </section>

            <!-- Event Details -->
            <section class="content-section event-details">
                <h2 class="section-title">Wedding Details</h2>
                
                <div class="event-card">
                    <h3 class="event-title">Akad Nikah</h3>
                    <p class="event-date">{{ $details["akad_date"] ?? $details["wedding_date"] ?? "Wedding Date" }}</p>
                    <p class="event-time">{{ $details["akad_time"] ?? "10:00 AM" }}</p>
                    <p class="event-location">
                        {{ $details["akad_venue_name"] ?? "Masjid Al-Hidayah" }}<br>
                        {{ $details["akad_venue_address"] ?? "Jalan Masjid 123, Kuala Lumpur" }}
                    </p>
                </div>

                <div class="event-card">
                    <h3 class="event-title">Wedding Reception</h3>
                    <p class="event-date">{{ $details["reception_date"] ?? $details["wedding_date"] ?? "Wedding Date" }}</p>
                    <p class="event-time">{{ $details["reception_time"] ?? "7:00 PM - 11:00 PM" }}</p>
                    <p class="event-location">
                        {{ $details["venue_name"] ?? "Dewan Serbaguna Taman Melawati" }}<br>
                        {{ $details["venue_address"] ?? "Jalan Taman 456, Kuala Lumpur" }}
                    </p>
                </div>
            </section>

            <!-- Map Section -->
            <section class="content-section map-section" id="map-section">
                <h2 class="section-title">Location</h2>
                <div class="map-container">
                    <i class="fas fa-map-marker-alt" style="font-size: 2rem; margin-right: 10px;"></i>
                    <span>Interactive Map</span>
                </div>
            </section>

            <!-- RSVP Section -->
            <section class="content-section rsvp-section" id="rsvp-section">
                <h2 class="section-title">RSVP</h2>
                <p style="margin-bottom: 30px;">Please confirm your attendance</p>
                
                <form class="rsvp-form" id="rsvpForm">
                    <div class="form-group">
                        <label class="form-label">Full Name</label>
                        <input type="text" class="form-input" name="name" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Phone Number</label>
                        <input type="tel" class="form-input" name="phone" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Will you attend?</label>
                        <select class="form-select" name="attendance" required>
                            <option value="">Please select</option>
                            <option value="yes">Yes, I will attend</option>
                            <option value="no">Sorry, I cannot attend</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Number of Guests</label>
                        <input type="number" class="form-input" name="guests" min="1" max="5" value="1">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Message</label>
                        <textarea class="form-input form-textarea" name="message" placeholder="Your wishes for the couple..."></textarea>
                    </div>
                    
                    <button type="submit" class="submit-btn">Send RSVP</button>
                </form>
            </section>

            <!-- Footer -->
            <section class="footer-section">
                <p class="footer-text">{{ $details["footer_message"] ?? "Thank You" }}</p>
                <p>{{ $details["groom_name"] ?? "Groom" }} & {{ $details["bride_name"] ?? "Bride" }}</p>
                <p style="margin-top: 20px; font-size: 0.9rem; opacity: 0.8;">
                    © {{ date('Y') }} - {{ $details["footer_subtitle"] ?? "With Love" }}
                </p>
            </section>
        </div>
    </div>

    <!-- Bottom Navigation Bar -->
    <div class="bottom-nav">
        <div class="nav-container">
            <button class="nav-button" id="contactBtn">
                <i class="fas fa-phone"></i>
                <span>Hubungi</span>
            </button>
            <button class="nav-button" id="locationBtn">
                <i class="fas fa-map-marker-alt"></i>
                <span>Lokasi</span>
            </button>
            <button class="nav-button" id="giftBtn">
                <i class="fas fa-gift"></i>
                <span>Hadiah</span>
            </button>
            <a href="#rsvp-section" class="nav-button">
                <i class="fas fa-envelope"></i>
                <span>RSVP</span>
            </a>
        </div>
    </div>

    <!-- Contact Popup -->
    <div class="popup-overlay" id="contactPopup">
        <div class="popup-content">
            <button class="close-popup" id="closePopup">
                <i class="fas fa-times"></i>
            </button>
            <div class="popup-header">HUBUNGI</div>
            
            @if($details["contact_person_1"] ?? false)
            <div class="contact-item">
                <div class="contact-info">
                    <div class="contact-name">{{ $details["contact_person_1"] ?? "Contact Person 1" }}</div>
                    <div class="contact-role">{{ $details["contact_role_1"] ?? "Ibu pengantin" }}</div>
                </div>
                <div class="contact-actions">
                    <a href="https://wa.me/{{ str_replace(['+', '-', ' '], '', $details['contact_number_1'] ?? '601234567890') }}" class="contact-btn" target="_blank">
                        <i class="fab fa-whatsapp"></i>
                    </a>
                    <a href="tel:{{ $details['contact_number_1'] ?? '+601234567890' }}" class="contact-btn">
                        <i class="fas fa-phone"></i>
                    </a>
                </div>
            </div>
            @endif

            @if($details["contact_person_2"] ?? false)
            <div class="contact-item">
                <div class="contact-info">
                    <div class="contact-name">{{ $details["contact_person_2"] ?? "Contact Person 2" }}</div>
                    <div class="contact-role">{{ $details["contact_role_2"] ?? "Ayah pengantin" }}</div>
                </div>
                <div class="contact-actions">
                    <a href="https://wa.me/{{ str_replace(['+', '-', ' '], '', $details['contact_number_2'] ?? '601234567891') }}" class="contact-btn" target="_blank">
                        <i class="fab fa-whatsapp"></i>
                    </a>
                    <a href="tel:{{ $details['contact_number_2'] ?? '+601234567891' }}" class="contact-btn">
                        <i class="fas fa-phone"></i>
                    </a>
                </div>
            </div>
            @endif

            @if($details["contact_person_3"] ?? false)
            <div class="contact-item">
                <div class="contact-info">
                    <div class="contact-name">{{ $details["contact_person_3"] ?? "Contact Person 3" }}</div>
                    <div class="contact-role">{{ $details["contact_role_3"] ?? "Abang pengantin" }}</div>
                </div>
                <div class="contact-actions">
                    <a href="https://wa.me/{{ str_replace(['+', '-', ' '], '', $details['contact_number_3'] ?? '601234567892') }}" class="contact-btn" target="_blank">
                        <i class="fab fa-whatsapp"></i>
                    </a>
                    <a href="tel:{{ $details['contact_number_3'] ?? '+601234567892' }}" class="contact-btn">
                        <i class="fas fa-phone"></i>
                    </a>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Location Popup -->
    <div class="location-popup" id="locationPopup">
        <div class="location-content">
            <button class="close-popup" id="closeLocationPopup">
                <i class="fas fa-times"></i>
            </button>
            <div class="location-header">LOKASI</div>
            
            <div class="location-address">
                <div class="location-venue">{{ $details["venue_name"] ?? "Dewan Serbaguna Taman Melawati" }}</div>
                <div class="location-details">
                    {{ $details["venue_address"] ?? "Jalan Taman 456, Kuala Lumpur" }}<br>
                    {{ $details["venue_full_address"] ?? "53100 Kuala Lumpur, Malaysia" }}
                </div>
            </div>

            <div class="location-actions">
                <a href="{{ $details['venue_map_link'] ?? 'https://maps.google.com/maps?q=Dewan+Serbaguna+Taman+Melawati,+Jalan+Taman+456,+Kuala+Lumpur' }}" target="_blank" class="location-btn">
                    <i class="fas fa-map-marked-alt"></i>
                    Maps
                </a>
                <a href="{{ $details['venue_waze_link'] ?? 'https://waze.com/ul?q=Dewan+Serbaguna+Taman+Melawati,+Jalan+Taman+456,+Kuala+Lumpur&navigate=yes' }}" target="_blank" class="location-btn">
                    <i class="fab fa-waze"></i>
                    Waze
                </a>
            </div>
        </div>
    </div>

    <!-- Gift Modal -->
    <div class="gift-modal" id="giftModal">
        <div class="gift-content">
            <button class="gift-close" id="closeGiftModal">
                <i class="fas fa-times"></i>
            </button>
            <h2 class="gift-title">Wedding Gift</h2>
            <p class="gift-subtitle">Your presence is our present, but if you wish to gift us something, we would be truly grateful.</p>
            
            <form id="giftForm" class="gift-form">
                <div class="gift-step" id="step1">
                    <h3 class="step-title">Choose Amount</h3>
                    <div class="gift-options">
                        <div class="gift-amount" onclick="selectAmount(30, this)">RM30</div>
                        <div class="gift-amount" onclick="selectAmount(50, this)">RM50</div>
                        <div class="gift-amount" onclick="selectAmount(100, this)">RM100</div>
                    </div>
                    <div class="custom-amount">
                        <input type="number" id="customAmount" placeholder="Enter custom amount" min="1" onchange="customAmountChange()">
                    </div>
                    <button type="button" class="continue-btn" onclick="proceedToGuestInfo()">Continue</button>
                </div>

                <div class="gift-step" id="step2" style="display: none;">
                    <h3 class="step-title">Your Information</h3>
                    <div class="form-group">
                        <label for="guestName">Full Name *</label>
                        <input type="text" id="guestName" name="guest_name" required placeholder="Enter your full name">
                    </div>
                    <div class="form-group">
                        <label for="guestEmail">Email Address *</label>
                        <input type="email" id="guestEmail" name="guest_email" required placeholder="Enter your email">
                    </div>
                    <div class="form-group">
                        <label for="guestPhone">Phone Number</label>
                        <input type="tel" id="guestPhone" name="guest_phone" placeholder="Enter your phone number (optional)">
                    </div>
                    <div class="form-group">
                        <label for="giftMessage">Personal Message</label>
                        <textarea id="giftMessage" name="message" rows="3" placeholder="Add a personal message to your gift (optional)"></textarea>
                    </div>
                    <div class="gift-summary">
                        <div class="summary-row">
                            <span>Amount:</span>
                            <span id="selectedAmountDisplay">RM0</span>
                        </div>
                    </div>
                    <div class="gift-buttons">
                        <button type="button" class="back-btn" onclick="backToAmountSelection()">Back</button>
                        <button type="submit" class="give-btn" id="sendGiftBtn">
                            <span class="btn-text">Send Gift</span>
                            <span class="btn-loading" style="display: none;">
                                <i class="fas fa-spinner fa-spin"></i> Processing...
                            </span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Pass wedding card ID to JavaScript
        window.weddingCardId = {{ $weddingCard->id ?? 'null' }};
        
        document.addEventListener('DOMContentLoaded', function() {
            const centerButton = document.getElementById('centerButton');
            const paperDoors = document.getElementById('paperDoors');
            const paperContainer = document.getElementById('paperContainer');
            const mainContent = document.getElementById('mainContent');
            let isOpening = false;

            // Paper doors click handler
            centerButton.addEventListener('click', function() {
                if (isOpening) return;
                
                isOpening = true;
                paperDoors.classList.add('opening');
                
                // After paper doors animation completes, show main content
                setTimeout(() => {
                    paperContainer.classList.add('opened');
                    
                    setTimeout(() => {
                        mainContent.classList.add('visible');
                        document.body.style.overflow = 'auto';
                    }, 500);
                }, 2200);
            });

            // Smooth scrolling for internal links
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

            // RSVP Form Handler
            const rsvpForm = document.getElementById('rsvpForm');
            rsvpForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                // Collect form data
                const formData = new FormData(this);
                const data = Object.fromEntries(formData);
                
                // Simple validation
                if (!data.name || !data.phone || !data.attendance) {
                    alert('Please fill in all required fields.');
                    return;
                }
                
                // Simulate form submission
                const submitBtn = this.querySelector('.submit-btn');
                const originalText = submitBtn.textContent;
                submitBtn.textContent = 'Sending...';
                submitBtn.disabled = true;
                
                setTimeout(() => {
                    alert('Thank you for your RSVP! We look forward to celebrating with you.');
                    this.reset();
                    submitBtn.textContent = originalText;
                    submitBtn.disabled = false;
                }, 2000);
            });

            // Parallax effect for hero section
            window.addEventListener('scroll', function() {
                const scrolled = window.pageYOffset;
                const heroSection = document.querySelector('.hero-section');
                if (heroSection && scrolled < window.innerHeight) {
                    heroSection.style.transform = `translateY(${scrolled * 0.5}px)`;
                }
            });

            // Add hover effect to center button
            function addCenterButtonEffects() {
                const centerButton = document.getElementById('centerButton');
                if (centerButton) {
                    centerButton.addEventListener('mouseenter', function() {
                        if (!isOpening) {
                            this.style.transform = 'translateY(-50%) scale(1.05)';
                        }
                    });
                    
                    centerButton.addEventListener('mouseleave', function() {
                        if (!isOpening) {
                            this.style.transform = 'translateY(-50%) scale(1)';
                        }
                    });
                }
            }

            addCenterButtonEffects();

            // Gift Modal functionality
            const giftBtn = document.getElementById('giftBtn');
            const giftModal = document.getElementById('giftModal');
            const closeGiftModal = document.getElementById('closeGiftModal');
            const giftForm = document.getElementById('giftForm');

            // Show gift modal
            giftBtn.addEventListener('click', function() {
                giftModal.classList.add('active');
                document.body.style.overflow = 'hidden';
                resetGiftForm();
            });

            // Hide gift modal when clicking close button
            closeGiftModal.addEventListener('click', function() {
                giftModal.classList.remove('active');
                document.body.style.overflow = 'auto';
                resetGiftForm();
            });

            // Hide gift modal when clicking outside
            giftModal.addEventListener('click', function(e) {
                if (e.target === giftModal) {
                    giftModal.classList.remove('active');
                    document.body.style.overflow = 'auto';
                    resetGiftForm();
                }
            });

            // Handle gift form submission
            giftForm.addEventListener('submit', function(e) {
                e.preventDefault();
                sendGift();
            });

            // Contact Popup functionality
            const contactBtn = document.getElementById('contactBtn');
            const contactPopup = document.getElementById('contactPopup');
            const closePopup = document.getElementById('closePopup');

            // Show contact popup
            contactBtn.addEventListener('click', function() {
                contactPopup.classList.add('active');
                document.body.style.overflow = 'hidden'; // Prevent background scrolling
            });

            // Hide contact popup when clicking close button
            closePopup.addEventListener('click', function() {
                contactPopup.classList.remove('active');
                document.body.style.overflow = 'auto';
            });

            // Hide contact popup when clicking outside
            contactPopup.addEventListener('click', function(e) {
                if (e.target === contactPopup) {
                    contactPopup.classList.remove('active');
                    document.body.style.overflow = 'auto';
                }
            });

            // Location Popup functionality
            const locationBtn = document.getElementById('locationBtn');
            const locationPopup = document.getElementById('locationPopup');
            const closeLocationPopup = document.getElementById('closeLocationPopup');

            // Show location popup
            locationBtn.addEventListener('click', function() {
                locationPopup.classList.add('active');
                document.body.style.overflow = 'hidden'; // Prevent background scrolling
            });

            // Hide location popup when clicking close button
            closeLocationPopup.addEventListener('click', function() {
                locationPopup.classList.remove('active');
                document.body.style.overflow = 'auto';
            });

            // Hide location popup when clicking outside
            locationPopup.addEventListener('click', function(e) {
                if (e.target === locationPopup) {
                    locationPopup.classList.remove('active');
                    document.body.style.overflow = 'auto';
                }
            });

            // Add smooth scrolling for navigation links
            document.querySelectorAll('.nav-button[href^="#"]').forEach(anchor => {
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
        });

        // Add some sparkle effect around envelope (optional)
        function createSparkle() {
            const sparkle = document.createElement('div');
            sparkle.style.cssText = `
                position: absolute;
                width: 4px;
                height: 4px;
                background: #fff8e7;
                border-radius: 50%;
                pointer-events: none;
                animation: sparkle 2s ease-out forwards;
                left: ${Math.random() * window.innerWidth}px;
                top: ${Math.random() * window.innerHeight}px;
            `;
            
            document.body.appendChild(sparkle);
            
            setTimeout(() => {
                if (sparkle.parentNode) {
                    sparkle.parentNode.removeChild(sparkle);
                }
            }, 2000);
        }

        // Create sparkles periodically
        setInterval(createSparkle, 500);

        // Add sparkle animation keyframes
        const sparkleStyle = document.createElement('style');
        sparkleStyle.textContent = `
            @keyframes sparkle {
                0% {
                    opacity: 0;
                    transform: scale(0) rotate(0deg);
                }
                50% {
                    opacity: 1;
                    transform: scale(1) rotate(180deg);
                }
                100% {
                    opacity: 0;
                    transform: scale(0) rotate(360deg);
                }
            }
        `;
        document.head.appendChild(sparkleStyle);

        // Gift Modal Functions
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

        function proceedToGuestInfo() {
            if (selectedAmount <= 0) {
                alert('Please select or enter a gift amount.');
                return;
            }

            // Hide step 1 and show step 2
            document.getElementById('step1').style.display = 'none';
            document.getElementById('step2').style.display = 'block';
            
            // Update amount display
            document.getElementById('selectedAmountDisplay').textContent = 'RM' + selectedAmount;
        }

        function backToAmountSelection() {
            // Show step 1 and hide step 2
            document.getElementById('step1').style.display = 'block';
            document.getElementById('step2').style.display = 'none';
        }

        function resetGiftForm() {
            // Reset amount selection
            selectedAmount = 0;
            document.querySelectorAll('.gift-amount').forEach(el => {
                el.classList.remove('selected');
            });
            
            // Clear form inputs
            const customAmount = document.getElementById('customAmount');
            const guestName = document.getElementById('guestName');
            const guestEmail = document.getElementById('guestEmail');
            const guestPhone = document.getElementById('guestPhone');
            const giftMessage = document.getElementById('giftMessage');
            
            if (customAmount) customAmount.value = '';
            if (guestName) guestName.value = '';
            if (guestEmail) guestEmail.value = '';
            if (guestPhone) guestPhone.value = '';
            if (giftMessage) giftMessage.value = '';
            
            // Reset to step 1
            document.getElementById('step1').style.display = 'block';
            document.getElementById('step2').style.display = 'none';
            
            // Reset button state
            const sendBtn = document.getElementById('sendGiftBtn');
            const btnText = sendBtn.querySelector('.btn-text');
            const btnLoading = sendBtn.querySelector('.btn-loading');
            sendBtn.disabled = false;
            btnText.style.display = 'inline';
            btnLoading.style.display = 'none';
        }

        function sendGift() {
            if (selectedAmount <= 0) {
                alert('Please select or enter a gift amount.');
                return;
            }

            // Get form data
            const guestName = document.getElementById('guestName').value.trim();
            const guestEmail = document.getElementById('guestEmail').value.trim();
            const guestPhone = document.getElementById('guestPhone').value.trim();
            const giftMessage = document.getElementById('giftMessage').value.trim();

            // Validate required fields
            if (!guestName) {
                alert('Please enter your full name.');
                document.getElementById('guestName').focus();
                return;
            }

            if (!guestEmail) {
                alert('Please enter your email address.');
                document.getElementById('guestEmail').focus();
                return;
            }

            // Email validation
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(guestEmail)) {
                alert('Please enter a valid email address.');
                document.getElementById('guestEmail').focus();
                return;
            }

            // Show loading state
            const sendBtn = document.getElementById('sendGiftBtn');
            const btnText = sendBtn.querySelector('.btn-text');
            const btnLoading = sendBtn.querySelector('.btn-loading');
            sendBtn.disabled = true;
            btnText.style.display = 'none';
            btnLoading.style.display = 'inline';

            // Get wedding card ID from URL
            const urlParts = window.location.pathname.split('/');
            const uniqueUrl = urlParts[urlParts.length - 1];

            // Validate wedding card ID
            if (!window.weddingCardId) {
                alert('Error: Wedding card not found. Please refresh the page.');
                resetButtonState();
                return;
            }

            // Prepare gift data
            const giftData = {
                wedding_card_id: window.weddingCardId,
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
                    resetButtonState();
                }
            })
            .catch(error => {
                console.error('Gift payment error:', error);
                alert('An error occurred. Please try again.');
                resetButtonState();
            });

            function resetButtonState() {
                sendBtn.disabled = false;
                btnText.style.display = 'inline';
                btnLoading.style.display = 'none';
            }
        }
    </script>
</body>
</html>
