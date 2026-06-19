# toyyibPay Gift Payment Integration Setup

This guide explains how to set up toyyibPay payment gateway for wedding gift functionality in your eWeddingCard application.

## Prerequisites

1. Create a toyyibPay account at [toyyibpay.com](https://toyyibpay.com)
2. For testing, register at [dev.toyyibpay.com](https://dev.toyyibpay.com)
3. Create a category for your wedding gifts

## Environment Configuration

Add the following variables to your `.env` file:

```env
# toyyibPay Configuration
TOYYIBPAY_SANDBOX=true
TOYYIBPAY_USER_SECRET_KEY=your_toyyibpay_user_secret_key_here
TOYYIBPAY_CATEGORY_CODE=your_toyyibpay_category_code_here
TOYYIBPAY_RETURN_URL="${APP_URL}/gift/{gift}/return"
TOYYIBPAY_CALLBACK_URL="${APP_URL}/gift/callback"
```

### Getting Your Credentials

1. **User Secret Key**: 
   - Login to your toyyibPay dashboard
   - Go to Account Settings → API Key
   - Copy your User Secret Key

2. **Category Code**:
   - In your toyyibPay dashboard, go to Categories
   - Create a new category for "Wedding Gifts" 
   - Copy the Category Code

3. **Sandbox Mode**:
   - Set `TOYYIBPAY_SANDBOX=true` for testing
   - Set `TOYYIBPAY_SANDBOX=false` for production

## Features

✅ **Complete Payment Flow**
- Create gift payments with toyyibPay
- Handle payment callbacks automatically
- Display beautiful payment receipts
- Track payment status in real-time

✅ **Guest Experience**
- Select preset amounts (RM30, RM50, RM100) or enter custom amount
- Add personal message to the gift
- Secure payment via toyyibPay (FPX & Credit Card)
- Receive payment confirmation receipt

✅ **Security & Reliability**
- CSRF protection on all requests
- Database transaction safety
- Comprehensive error handling
- Payment status verification

## How It Works

1. **Guest clicks "Send Gift"** in the wedding invitation
2. **Amount selection** - Choose from preset amounts or enter custom amount
3. **Guest information** - Enter name, email, phone, and optional message
4. **Payment creation** - System creates a toyyibPay bill and redirects to payment page
5. **Payment processing** - Guest completes payment via FPX or Credit Card
6. **Callback handling** - toyyibPay sends payment status to your callback URL
7. **Receipt display** - Guest sees beautiful payment confirmation receipt

## Database Structure

The system creates a `gifts` table to track all wedding gifts:

- Guest information (name, email, phone)
- Gift amount and currency
- Payment status (pending, paid, failed, cancelled)
- toyyibPay bill code and payment details
- Personal message from guest
- Payment timestamps

## Testing

For testing purposes:

1. Set `TOYYIBPAY_SANDBOX=true` in your `.env`
2. Use test credentials from dev.toyyibpay.com
3. Test payments will use simulated banks
4. No real money will be charged

## Production Setup

Before going live:

1. Set `TOYYIBPAY_SANDBOX=false`
2. Use production credentials from toyyibpay.com
3. Verify callback URL is accessible from the internet
4. Test the complete payment flow

## API Endpoints

- `POST /gift/create` - Create a new gift payment
- `POST /gift/callback` - Handle toyyibPay payment callbacks
- `GET /gift/{gift}/return` - Handle return from payment page
- `GET /gift/{gift}/receipt` - Display payment receipt
- `GET /gift/{gift}/status` - Check payment status (AJAX)

## Troubleshooting

### Common Issues

1. **"Invalid callback"** errors:
   - Verify TOYYIBPAY_CALLBACK_URL is publicly accessible
   - Check your server logs for detailed error messages

2. **"Connection error"** messages:
   - Verify internet connectivity
   - Check toyyibPay service status

3. **Payment not updating**:
   - Check callback URL configuration
   - Verify toyyibPay webhook settings

### Debug Mode

Enable Laravel debug mode to see detailed error messages:
```env
APP_DEBUG=true
```

Check logs at `storage/logs/laravel.log` for detailed error information.

## Support

For toyyibPay specific issues:
- Visit [toyyibpay.com/support](https://toyyibpay.com/support)
- Check API documentation at [toyyibpay.com/apireference](https://toyyibpay.com/apireference/)

For integration issues:
- Check the application logs
- Verify environment configuration
- Test with sandbox mode first

## Security Notes

- Never expose your User Secret Key in client-side code
- Always validate callback data on the server side
- Use HTTPS in production for secure payment processing
- Regularly update your application dependencies 