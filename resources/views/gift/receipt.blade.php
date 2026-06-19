<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gift Receipt - {{ $details['bride_name'] ?? 'Bride' }} & {{ $details['groom_name'] ?? 'Groom' }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Montserrat:wght@300;400;500&family=Great+Vibes&display=swap');
        
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
            --success: #28a745;
            --error: #dc3545;
            --warning: #ffc107;
        }

        body {
            font-family: 'Montserrat', sans-serif;
            background: linear-gradient(135deg, var(--secondary) 0%, #f0f0f0 100%);
            min-height: 100vh;
            padding: 20px;
            color: var(--text-dark);
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            padding: 40px 30px;
            text-align: center;
            position: relative;
        }

        .header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="20" cy="20" r="2" fill="rgba(255,255,255,0.1)"/><circle cx="80" cy="30" r="1.5" fill="rgba(255,255,255,0.1)"/><circle cx="30" cy="80" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="70" cy="70" r="2.5" fill="rgba(255,255,255,0.1)"/></svg>');
            pointer-events: none;
        }

        .status-icon {
            font-size: 4rem;
            margin-bottom: 20px;
            position: relative;
            z-index: 2;
        }

        .status-icon.success {
            color: var(--success);
        }

        .status-icon.error {
            color: var(--error);
        }

        .status-icon.pending {
            color: var(--warning);
        }

        .header h1 {
            font-family: 'Great Vibes', cursive;
            font-size: 3rem;
            margin-bottom: 10px;
            position: relative;
            z-index: 2;
        }

        .header p {
            font-size: 1.1rem;
            opacity: 0.9;
            position: relative;
            z-index: 2;
        }

        .content {
            padding: 40px 30px;
        }

        .alert {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 30px;
            border: none;
            font-weight: 500;
        }

        .alert.success {
            background: rgba(40, 167, 69, 0.1);
            color: var(--success);
            border-left: 4px solid var(--success);
        }

        .alert.error {
            background: rgba(220, 53, 69, 0.1);
            color: var(--error);
            border-left: 4px solid var(--error);
        }

        .receipt-details {
            background: var(--secondary);
            border-radius: 10px;
            padding: 25px;
            margin-bottom: 30px;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid rgba(200, 161, 101, 0.2);
        }

        .detail-row:last-child {
            border-bottom: none;
        }

        .detail-label {
            font-weight: 600;
            color: #666;
        }

        .detail-value {
            font-weight: 500;
            text-align: right;
        }

        .amount {
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--primary);
        }

        .message-section {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 30px;
            border-left: 4px solid var(--primary);
        }

        .message-section h3 {
            font-family: 'Playfair Display', serif;
            color: var(--primary);
            margin-bottom: 10px;
        }

        .message-text {
            font-style: italic;
            color: #666;
            line-height: 1.6;
        }

        .payment-info {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 30px;
        }

        .payment-info h3 {
            font-family: 'Playfair Display', serif;
            color: var(--text-dark);
            margin-bottom: 15px;
        }

        .payment-detail {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
        }

        .payment-detail:last-child {
            margin-bottom: 0;
        }

        .actions {
            text-align: center;
            padding-top: 20px;
            border-top: 2px solid var(--secondary);
        }

        .btn {
            display: inline-block;
            padding: 12px 30px;
            border-radius: 25px;
            text-decoration: none;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s ease;
            margin: 0 10px;
            font-size: 0.9rem;
        }

        .btn-primary {
            background: var(--primary);
            color: white;
            border: 2px solid var(--primary);
        }

        .btn-primary:hover {
            background: var(--primary-dark);
            border-color: var(--primary-dark);
            transform: translateY(-2px);
        }

        .btn-outline {
            background: transparent;
            color: var(--primary);
            border: 2px solid var(--primary);
        }

        .btn-outline:hover {
            background: var(--primary);
            color: white;
            transform: translateY(-2px);
        }

        .footer {
            text-align: center;
            padding: 20px;
            background: var(--secondary);
            color: #777;
            font-size: 0.9rem;
        }

        .footer p {
            margin-bottom: 5px;
        }

        @media (max-width: 768px) {
            .container {
                margin: 10px;
                border-radius: 10px;
            }

            .header {
                padding: 30px 20px;
            }

            .header h1 {
                font-size: 2.5rem;
            }

            .content {
                padding: 30px 20px;
            }

            .detail-row {
                flex-direction: column;
                align-items: flex-start;
                gap: 5px;
            }

            .detail-value {
                text-align: left;
            }

            .btn {
                display: block;
                margin: 10px 0;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            @if($gift->isPaid())
                <div class="status-icon success">
                    <i class="fas fa-check-circle"></i>
                </div>
                <h1>Payment Successful!</h1>
                <p>Your generous gift has been received</p>
            @elseif($gift->isFailed())
                <div class="status-icon error">
                    <i class="fas fa-times-circle"></i>
                </div>
                <h1>Payment Failed</h1>
                <p>There was an issue processing your payment</p>
            @else
                <div class="status-icon pending">
                    <i class="fas fa-clock"></i>
                </div>
                <h1>Payment Pending</h1>
                <p>Your payment is being processed</p>
            @endif
        </div>

        <div class="content">
            @if(session('success'))
                <div class="alert success">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert error">
                    <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                </div>
            @endif

            <div class="receipt-details">
                <div class="detail-row">
                    <span class="detail-label">Gift From:</span>
                    <span class="detail-value">{{ $gift->guest_name }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Email:</span>
                    <span class="detail-value">{{ $gift->guest_email }}</span>
                </div>
                @if($gift->guest_phone)
                <div class="detail-row">
                    <span class="detail-label">Phone:</span>
                    <span class="detail-value">{{ $gift->guest_phone }}</span>
                </div>
                @endif
                <div class="detail-row">
                    <span class="detail-label">To:</span>
                    <span class="detail-value">
                        {{ $details['groom_name'] ?? 'Groom' }} & {{ $details['bride_name'] ?? 'Bride' }}
                    </span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Amount:</span>
                    <span class="detail-value amount">{{ $gift->currency }} {{ number_format($gift->amount, 2) }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Status:</span>
                    <span class="detail-value">
                        @if($gift->isPaid())
                            <span style="color: var(--success); font-weight: 600;">
                                <i class="fas fa-check"></i> Paid
                            </span>
                        @elseif($gift->isFailed())
                            <span style="color: var(--error); font-weight: 600;">
                                <i class="fas fa-times"></i> Failed
                            </span>
                        @else
                            <span style="color: var(--warning); font-weight: 600;">
                                <i class="fas fa-clock"></i> Pending
                            </span>
                        @endif
                    </span>
                </div>
                @if($gift->paid_at)
                <div class="detail-row">
                    <span class="detail-label">Paid At:</span>
                    <span class="detail-value">{{ $gift->paid_at->format('d M Y, h:i A') }}</span>
                </div>
                @endif
                <div class="detail-row">
                    <span class="detail-label">Reference No:</span>
                    <span class="detail-value">{{ $gift->external_reference_no }}</span>
                </div>
                @if($gift->bill_code)
                <div class="detail-row">
                    <span class="detail-label">Bill Code:</span>
                    <span class="detail-value">{{ $gift->bill_code }}</span>
                </div>
                @endif
            </div>

            @if($gift->message)
            <div class="message-section">
                <h3><i class="fas fa-heart"></i> Your Message</h3>
                <p class="message-text">"{{ $gift->message }}"</p>
            </div>
            @endif

            @if($gift->isPaid() && $gift->toyyibpay_response && isset($gift->toyyibpay_response['payment_details']))
            <div class="payment-info">
                <h3><i class="fas fa-credit-card"></i> Payment Details</h3>
                @php
                    $paymentDetails = $gift->toyyibpay_response['payment_details'];
                @endphp
                @if(isset($paymentDetails['billpaymentDate']))
                <div class="payment-detail">
                    <span>Payment Date:</span>
                    <span>{{ $paymentDetails['billpaymentDate'] }}</span>
                </div>
                @endif
                @if(isset($paymentDetails['billpaymentAmount']))
                <div class="payment-detail">
                    <span>Amount Paid:</span>
                    <span>MYR {{ number_format($paymentDetails['billpaymentAmount'], 2) }}</span>
                </div>
                @endif
                @if(isset($paymentDetails['billBankID']))
                <div class="payment-detail">
                    <span>Payment Method:</span>
                    <span>{{ $paymentDetails['billBankID'] }}</span>
                </div>
                @endif
            </div>
            @endif

            <div class="actions">
                <a href="{{ url()->previous() }}" class="btn btn-outline">
                    <i class="fas fa-arrow-left"></i> Back to Wedding
                </a>
                @if(!$gift->isPaid() && $gift->payment_url)
                <a href="{{ $gift->payment_url }}" class="btn btn-primary">
                    <i class="fas fa-credit-card"></i> Complete Payment
                </a>
                @endif
            </div>
        </div>

        <div class="footer">
            <p><i class="fas fa-heart"></i> Thank you for celebrating with us!</p>
            <p>{{ $details['groom_name'] ?? 'Groom' }} & {{ $details['bride_name'] ?? 'Bride' }}</p>
        </div>
    </div>

    <script>
        // Auto-refresh page if payment is pending (poll every 10 seconds)
        @if($gift->isPending())
        setTimeout(function() {
            location.reload();
        }, 10000);
        @endif
    </script>
</body>
</html> 