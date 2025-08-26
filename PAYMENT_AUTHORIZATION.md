# Laravel Payment Authorization System

This document describes the comprehensive payment authorization system implemented in your Laravel application.

## Overview

The payment authorization system provides secure payment processing, verification, and access control for your Laravel application. It integrates with Stripe for payment processing and includes middleware for protecting routes that require payment authorization.

## Features

### 🔐 Payment Authorization Middleware
- **Route Protection**: Automatically protects routes that require payment authorization
- **User Authentication**: Ensures users are logged in before checking payment status
- **Payment Verification**: Validates that users have completed payments
- **Automatic Redirects**: Redirects unauthorized users to payment completion pages

### 💳 Payment Processing
- **Stripe Integration**: Secure payment processing through Stripe
- **Payment Verification**: Real-time payment status verification
- **Multiple Payment Statuses**: Support for pending, completed, failed, cancelled, and refunded payments
- **Payment History**: Complete payment history tracking

### 🛡️ Security Features
- **CSRF Protection**: Built-in CSRF protection for all payment routes
- **Rate Limiting**: Configurable rate limiting for payment attempts
- **Secure Token Handling**: Secure handling of payment tokens
- **Logging**: Comprehensive payment event logging

### 📧 Notifications
- **Email Notifications**: Automatic email notifications for payment events
- **Payment Verification**: Email-based payment verification system
- **Status Updates**: Real-time payment status updates

## Installation & Setup

### 1. Environment Variables

Add these variables to your `.env` file:

```env
# Stripe Configuration
STRIPE_KEY=your_stripe_public_key
STRIPE_SECRET=your_stripe_secret_key
STRIPE_WEBHOOK_SECRET=your_stripe_webhook_secret

# Payment Authorization Settings
REQUIRE_PAYMENT_AUTHORIZATION=true
PAYMENT_REDIRECT_ROUTE=payment.required
PAYMENT_CACHE_DURATION=30

# Payment Verification
AUTO_VERIFY_PAYMENTS=true
PAYMENT_VERIFICATION_TIMEOUT=30
PAYMENT_MAX_VERIFICATION_ATTEMPTS=3
PAYMENT_EMAIL_VERIFICATION=true

# Payment Amounts
DEFAULT_PAYMENT_AMOUNT=5000
PAYMENT_CURRENCY=USD
MINIMUM_PAYMENT_AMOUNT=100
MAXIMUM_PAYMENT_AMOUNT=100000

# Security
PAYMENT_CSRF_PROTECTION=true
PAYMENT_RATE_LIMIT=10,1
PAYMENT_REQUIRE_AUTH=true
PAYMENT_LOG_ATTEMPTS=true

# Notifications
PAYMENT_EMAIL_NOTIFICATIONS=true
PAYMENT_SMS_NOTIFICATIONS=false

# Logging
PAYMENT_LOGGING=true
PAYMENT_LOG_LEVEL=info
PAYMENT_LOG_DETAILS=false
```

### 2. Middleware Registration

The payment authorization middleware is automatically registered in `bootstrap/app.php`:

```php
$middleware->alias([
    'payment.authorization' => \App\Http\Middleware\PaymentAuthorization::class,
]);
```

### 3. Routes

Payment authorization routes are defined in `routes/web.php`:

```php
// Payment Authorization Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/payment/required', [PaymentAuthorizationController::class, 'paymentRequired'])->name('payment.required');
    Route::get('/payment/history', [PaymentAuthorizationController::class, 'paymentHistory'])->name('payment.history');
    Route::post('/payment/verify', [PaymentAuthorizationController::class, 'verifyPayment'])->name('payment.verify');
    Route::get('/payment/check', [PaymentAuthorizationController::class, 'checkAuthorization'])->name('payment.check');
    Route::post('/payment/resend-verification', [PaymentAuthorizationController::class, 'resendVerification'])->name('payment.resend-verification');
    Route::post('/payment/cancel', [PaymentAuthorizationController::class, 'cancelPayment'])->name('payment.cancel');
});

// Protected routes that require payment authorization
Route::middleware(['auth', 'payment.authorization'])->group(function () {
    Route::get('/protected-content', function () {
        return view('protected.content');
    })->name('protected.content');
});
```

## Usage

### Protecting Routes

To protect routes that require payment authorization, use the `payment.authorization` middleware:

```php
Route::middleware(['auth', 'payment.authorization'])->group(function () {
    Route::get('/premium-content', [ContentController::class, 'premium']);
    Route::get('/member-only', [MemberController::class, 'exclusive']);
});
```

### Checking Payment Authorization

You can check payment authorization status in your controllers:

```php
public function someMethod(Request $request)
{
    // Get the authorized member from the request
    $authorizedMember = $request->attributes->get('authorized_member');
    
    if ($authorizedMember) {
        // User has payment authorization
        return view('premium.content', compact('authorizedMember'));
    }
    
    // User doesn't have payment authorization
    return redirect()->route('payment.required');
}
```

### JavaScript Integration

Include the payment authorization JavaScript file in your views:

```html
<script src="{{ asset('js/payment-authorization.js') }}"></script>
```

The JavaScript provides:
- Automatic payment authorization checks
- Payment verification functionality
- Success/error notifications
- Payment status updates

### Payment Status Checking

Check payment authorization via AJAX:

```javascript
// Check if user has payment authorization
fetch('/payment/check')
    .then(response => response.json())
    .then(data => {
        if (data.authorized) {
            console.log('Payment authorized');
        } else {
            console.log('Payment authorization required');
        }
    });
```

## Controllers

### PaymentAuthorizationController

Handles payment authorization logic:

- `paymentRequired()` - Shows payment required page
- `verifyPayment()` - Verifies payment with Stripe
- `checkAuthorization()` - Checks user's payment authorization status
- `paymentHistory()` - Shows payment history
- `resendVerification()` - Resends payment verification email
- `cancelPayment()` - Cancels pending payments

### MemberController (Enhanced)

Enhanced with payment authorization features:

- `checkPaymentAuthorization()` - Checks payment authorization
- `getPaymentStatus()` - Gets payment status for current user
- `resendPaymentVerification()` - Resends payment verification

## Views

### Payment Required Page
- **File**: `resources/views/payment/required.blade.php`
- **Purpose**: Shown when users need to complete payment authorization
- **Features**: Payment benefits, call-to-action buttons

### Payment History
- **File**: `resources/views/payment/history.blade.php`
- **Purpose**: Shows user's payment history
- **Features**: Payment status, actions, verification

### Protected Content
- **File**: `resources/views/protected/content.blade.php`
- **Purpose**: Example of protected content
- **Features**: Premium features, member details

### Payment Status
- **File**: `resources/views/members/payment-status.blade.php`
- **Purpose**: Shows detailed payment status
- **Features**: Completed/pending payments, actions

## Configuration

### Payment Configuration

The payment configuration is in `config/payment.php`:

```php
'authorization' => [
    'require_authorization' => true,
    'protected_routes' => ['/protected-content', '/premium-features'],
    'exempt_routes' => ['/', '/login', '/register'],
    'redirect_route' => 'payment.required',
    'session_key' => 'payment_authorized',
    'cache_duration' => 30,
],
```

### Stripe Configuration

Stripe settings in `config/services.php`:

```php
'stripe' => [
    'key' => env('STRIPE_KEY'),
    'secret' => env('STRIPE_SECRET'),
],
```

## Security Features

### CSRF Protection
All payment routes are protected with CSRF tokens.

### Rate Limiting
Payment attempts are rate-limited to prevent abuse.

### Secure Token Handling
Payment tokens are handled securely and never logged.

### Authentication Required
All payment operations require user authentication.

## Testing

### Testing Payment Authorization

1. **Create a test user** with a completed payment
2. **Access protected routes** to verify authorization works
3. **Test payment verification** with Stripe test cards
4. **Verify middleware** blocks unauthorized access

### Testing Payment Flow

1. **Register a new user**
2. **Complete payment process**
3. **Verify payment authorization** is granted
4. **Access protected content**

## Troubleshooting

### Common Issues

1. **Payment not authorized**
   - Check if user has completed payment
   - Verify payment status in database
   - Check Stripe webhook configuration

2. **Middleware not working**
   - Verify middleware is registered
   - Check route middleware configuration
   - Ensure user is authenticated

3. **Payment verification failing**
   - Check Stripe API keys
   - Verify webhook configuration
   - Check payment intent status

### Debug Mode

Enable debug logging in `.env`:

```env
PAYMENT_LOGGING=true
PAYMENT_LOG_LEVEL=debug
PAYMENT_LOG_DETAILS=true
```

## API Endpoints

### Payment Authorization API

- `GET /payment/check` - Check payment authorization status
- `POST /payment/verify` - Verify payment with Stripe
- `POST /payment/resend-verification` - Resend verification email
- `POST /payment/cancel` - Cancel pending payment
- `GET /payment/history` - Get payment history
- `GET /members/payment/status` - Get member payment status

### Response Format

```json
{
    "authorized": true,
    "member": {
        "id": 1,
        "membership_number": "SRRR0001",
        "payment_status": "completed"
    },
    "message": "Payment authorized"
}
```

## Contributing

When contributing to the payment authorization system:

1. **Follow Laravel conventions**
2. **Add proper validation**
3. **Include error handling**
4. **Write tests**
5. **Update documentation**

## Support

For support with the payment authorization system:

1. Check the troubleshooting section
2. Review the configuration options
3. Check the Laravel logs
4. Verify Stripe integration
5. Test with Stripe test mode

---

This payment authorization system provides a robust, secure, and user-friendly way to handle payments and protect content in your Laravel application. 