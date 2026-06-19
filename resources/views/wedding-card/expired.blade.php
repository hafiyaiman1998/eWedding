<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $card->title }} - Wedding Card Expired</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .expired-container {
            background: white;
            border-radius: 20px;
            padding: 60px 40px;
            text-align: center;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            max-width: 600px;
            width: 100%;
        }

        .expired-icon {
            font-size: 80px;
            color: #e74c3c;
            margin-bottom: 30px;
        }

        .expired-title {
            font-size: 32px;
            color: #2c3e50;
            margin-bottom: 20px;
            font-weight: 700;
        }

        .expired-message {
            font-size: 18px;
            color: #7f8c8d;
            margin-bottom: 30px;
            line-height: 1.6;
        }

        .card-info {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 25px;
            margin: 30px 0;
            border-left: 4px solid #e74c3c;
        }

        .card-title {
            font-size: 24px;
            color: #2c3e50;
            margin-bottom: 15px;
            font-weight: 600;
        }

        .card-details {
            color: #7f8c8d;
            font-size: 16px;
            margin-bottom: 8px;
        }

        .expiry-info {
            background: rgba(231, 76, 60, 0.1);
            color: #e74c3c;
            padding: 15px 20px;
            border-radius: 8px;
            margin: 20px 0;
            font-weight: 600;
        }

        .contact-info {
            margin-top: 40px;
            padding-top: 30px;
            border-top: 1px solid #e9ecef;
        }

        .contact-title {
            font-size: 18px;
            color: #2c3e50;
            margin-bottom: 15px;
            font-weight: 600;
        }

        .contact-item {
            color: #7f8c8d;
            margin-bottom: 8px;
        }

        .powered-by {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #e9ecef;
            color: #95a5a6;
            font-size: 14px;
        }

        @media (max-width: 768px) {
            .expired-container {
                padding: 40px 25px;
            }
            
            .expired-title {
                font-size: 26px;
            }
            
            .expired-message {
                font-size: 16px;
            }
        }
    </style>
</head>
<body>
    <div class="expired-container">
        <div class="expired-icon">
            <i class="fas fa-hourglass-end"></i>
        </div>
        
        <h1 class="expired-title">Wedding Card Expired</h1>
        
        <p class="expired-message">
            This beautiful wedding invitation has expired and is no longer available for viewing. 
            Thank you for your interest in celebrating this special moment.
        </p>

        <div class="card-info">
            <h2 class="card-title">{{ $card->title }}</h2>
            
            @if(isset($card->card_details['bride_name']) && isset($card->card_details['groom_name']))
                <div class="card-details">
                    <strong>{{ $card->card_details['bride_name'] }}</strong> & 
                    <strong>{{ $card->card_details['groom_name'] }}</strong>
                </div>
            @endif
            
            @if(isset($card->card_details['wedding_date']))
                <div class="card-details">
                    <i class="fas fa-calendar"></i> 
                    Wedding Date: {{ $card->card_details['wedding_date'] }}
                </div>
            @endif
            
            <div class="expiry-info">
                <i class="fas fa-clock"></i>
                This invitation expired on {{ $card->expiry_date->format('F d, Y') }}
            </div>
        </div>

        @if(isset($card->card_details['contact_bride']) || isset($card->card_details['contact_groom']))
            <div class="contact-info">
                <h3 class="contact-title">Contact Information</h3>
                
                @if(isset($card->card_details['contact_bride']))
                    <div class="contact-item">
                        <i class="fas fa-phone"></i> 
                        {{ $card->card_details['bride_name'] ?? 'Bride' }}: {{ $card->card_details['contact_bride'] }}
                    </div>
                @endif
                
                @if(isset($card->card_details['contact_groom']))
                    <div class="contact-item">
                        <i class="fas fa-phone"></i> 
                        {{ $card->card_details['groom_name'] ?? 'Groom' }}: {{ $card->card_details['contact_groom'] }}
                    </div>
                @endif
            </div>
        @endif

        <div class="powered-by">
            <i class="fas fa-heart"></i>
            Powered by eWeddingCard
        </div>
    </div>
</body>
</html> 