<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $template->name }} - Template Preview</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Dancing+Script:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        body {
            margin: 0;
            padding: 20px;
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #ffecd2 0%, #fcb69f 25%, #ffecd2 50%, #a8edea 75%, #fed6e3 100%);
            background-size: 400% 400%;
            animation: gradientShift 15s ease infinite;
            min-height: 100vh;
        }

        @keyframes gradientShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .preview-container {
            max-width: 800px;
            margin: 0 auto;
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.1);
        }

        .preview-header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        }

        .template-name {
            font-family: 'Dancing Script', cursive;
            font-size: 36px;
            background: linear-gradient(135deg, #ff6b9d 0%, #c44569 50%, #f8b500 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .template-category {
            color: #7f8c8d;
            font-size: 16px;
            margin-bottom: 10px;
        }

        .preview-note {
            background: rgba(52, 152, 219, 0.1);
            border: 1px solid rgba(52, 152, 219, 0.3);
            color: #2980b9;
            padding: 15px;
            border-radius: 10px;
            font-size: 14px;
            margin-bottom: 30px;
        }

        .template-content {
            background: white;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        .close-btn {
            position: fixed;
            top: 20px;
            right: 20px;
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            border: none;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            color: #2c3e50;
            font-size: 20px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .close-btn:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: scale(1.1);
        }
    </style>
</head>
<body>
    <button class="close-btn" onclick="window.close()">
        <i class="fas fa-times"></i>
    </button>

    <div class="preview-container">
        <div class="preview-header">
            <h1 class="template-name">{{ $template->name }}</h1>
            <p class="template-category">{{ ucfirst($template->category) }} Template</p>
            @if($template->description)
                <p style="color: #7f8c8d; margin-bottom: 0;">{{ $template->description }}</p>
            @endif
        </div>

        <div class="preview-note">
            <i class="fas fa-info-circle"></i>
            This is a preview using sample data. When you create your card, you'll be able to customize all the details with your own wedding information.
        </div>

        <div class="template-content">
            <!-- Default template preview with sample data -->
            <div style="font-family: 'Poppins', sans-serif; text-align: center; padding: 40px; background: linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%); border-radius: 15px;">
                <h1 style="font-family: 'Dancing Script', cursive; font-size: 48px; color: #2c3e50; margin-bottom: 30px;">Wedding Invitation</h1>
                
                <div style="margin-bottom: 40px;">
                    <h2 style="font-family: 'Dancing Script', cursive; font-size: 36px; color: #e74c3c; margin-bottom: 20px;">
                        {{ $previewData['bride_name'] ?? 'Sarah' }}
                        <span style="color: #f39c12; margin: 0 15px;">&</span>
                        {{ $previewData['groom_name'] ?? 'Ahmad' }}
                    </h2>
                </div>
                
                <div style="background: rgba(255, 255, 255, 0.8); padding: 25px; border-radius: 15px; margin-bottom: 30px;">
                    <h3 style="color: #2c3e50; margin-bottom: 15px;">Wedding Ceremony</h3>
                    <p style="color: #2c3e50; margin-bottom: 8px;">
                        <i class="fas fa-calendar" style="margin-right: 8px;"></i>
                        {{ $previewData['wedding_date'] ?? '15 Januari 2024' }}
                    </p>
                    <p style="color: #2c3e50; margin-bottom: 8px;">
                        <i class="fas fa-clock" style="margin-right: 8px;"></i>
                        {{ $previewData['wedding_time'] ?? '10:00 AM' }}
                    </p>
                    <p style="color: #2c3e50; margin-bottom: 8px;">
                        <i class="fas fa-map-marker-alt" style="margin-right: 8px;"></i>
                        {{ $previewData['venue_name'] ?? 'Dewan Serbaguna' }}
                    </p>
                    <p style="color: #7f8c8d; font-size: 14px;">{{ $previewData['venue_address'] ?? 'Jalan Raya No. 123, Kuala Lumpur' }}</p>
                </div>
                
                <div style="background: rgba(255, 255, 255, 0.8); padding: 25px; border-radius: 15px;">
                    <h3 style="color: #2c3e50; margin-bottom: 15px;">RSVP</h3>
                    <p style="color: #7f8c8d; margin-bottom: 15px;">Please respond by {{ $previewData['rsvp_date'] ?? '10 January 2024' }}</p>
                    <div style="display: flex; gap: 15px; justify-content: center;">
                        <button style="background: #2ecc71; color: white; border: none; padding: 12px 24px; border-radius: 25px; cursor: pointer;">
                            <i class="fas fa-check" style="margin-right: 8px;"></i>
                            I'll be there
                        </button>
                        <button style="background: #e74c3c; color: white; border: none; padding: 12px 24px; border-radius: 25px; cursor: pointer;">
                            <i class="fas fa-times" style="margin-right: 8px;"></i>
                            Can't make it
                        </button>
                    </div>
                </div>
                
                <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid rgba(255, 255, 255, 0.3); color: #7f8c8d; font-size: 14px;">
                    Created with eWeddingCard
                </div>
            </div>
        </div>
    </div>
</body>
</html> 