<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Payment Authorization Settings
    |--------------------------------------------------------------------------
    |
    | This file contains configuration settings for payment authorization
    | features including Stripe integration, payment verification,
    | and authorization middleware settings.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Payment Gateway Configuration
    |--------------------------------------------------------------------------
    */

    'gateway' => env('PAYMENT_GATEWAY', 'stripe'),

    'stripe' => [
        'public_key' => env('STRIPE_KEY'),
        'secret_key' => env('STRIPE_SECRET'),
        'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),
        'currency' => env('STRIPE_CURRENCY', 'usd'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Payment Authorization Settings
    |--------------------------------------------------------------------------
    */

    'authorization' => [
        // Require payment authorization for specific routes
        'require_authorization' => env('REQUIRE_PAYMENT_AUTHORIZATION', true),
        
        // Routes that require payment authorization
        'protected_routes' => [
            '/protected-content',
            '/premium-features',
            '/member-only',
        ],
        
        // Routes that are exempt from payment authorization
        'exempt_routes' => [
            '/',
            '/login',
            '/register',
            '/payment/required',
            '/payment/history',
        ],
        
        // Redirect route when payment authorization is required
        'redirect_route' => env('PAYMENT_REDIRECT_ROUTE', 'payment.required'),
        
        // Session key for storing payment authorization status
        'session_key' => 'payment_authorized',
        
        // Cache payment authorization status (in minutes)
        'cache_duration' => env('PAYMENT_CACHE_DURATION', 30),
    ],

    /*
    |--------------------------------------------------------------------------
    | Payment Verification Settings
    |--------------------------------------------------------------------------
    */

    'verification' => [
        // Enable automatic payment verification
        'auto_verify' => env('AUTO_VERIFY_PAYMENTS', true),
        
        // Payment verification timeout (in minutes)
        'timeout' => env('PAYMENT_VERIFICATION_TIMEOUT', 30),
        
        // Maximum verification attempts
        'max_attempts' => env('PAYMENT_MAX_VERIFICATION_ATTEMPTS', 3),
        
        // Enable email verification
        'email_verification' => env('PAYMENT_EMAIL_VERIFICATION', true),
        
        // Verification email template
        'email_template' => 'emails.payment.verification',
    ],

    /*
    |--------------------------------------------------------------------------
    | Payment Status Settings
    |--------------------------------------------------------------------------
    */

    'status' => [
        'pending' => 'pending',
        'completed' => 'completed',
        'failed' => 'failed',
        'cancelled' => 'cancelled',
        'refunded' => 'refunded',
        'disputed' => 'disputed',
    ],

    /*
    |--------------------------------------------------------------------------
    | Payment Amount Settings
    |--------------------------------------------------------------------------
    */

    'amounts' => [
        'default' => env('DEFAULT_PAYMENT_AMOUNT', 5000), // in cents
        'currency' => env('PAYMENT_CURRENCY', 'USD'),
        'minimum' => env('MINIMUM_PAYMENT_AMOUNT', 100), // in cents
        'maximum' => env('MAXIMUM_PAYMENT_AMOUNT', 100000), // in cents
    ],

    /*
    |--------------------------------------------------------------------------
    | Payment Security Settings
    |--------------------------------------------------------------------------
    */

    'security' => [
        // Enable CSRF protection for payment routes
        'csrf_protection' => env('PAYMENT_CSRF_PROTECTION', true),
        
        // Rate limiting for payment attempts
        'rate_limit' => env('PAYMENT_RATE_LIMIT', '10,1'), // attempts, minutes
        
        // Require authentication for payment operations
        'require_auth' => env('PAYMENT_REQUIRE_AUTH', true),
        
        // Log payment attempts
        'log_attempts' => env('PAYMENT_LOG_ATTEMPTS', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Payment Notification Settings
    |--------------------------------------------------------------------------
    */

    'notifications' => [
        // Send email notifications for payment events
        'email_enabled' => env('PAYMENT_EMAIL_NOTIFICATIONS', true),
        
        // Send SMS notifications for payment events
        'sms_enabled' => env('PAYMENT_SMS_NOTIFICATIONS', false),
        
        // Notification events
        'events' => [
            'payment_completed' => true,
            'payment_failed' => true,
            'payment_cancelled' => true,
            'payment_refunded' => true,
            'verification_sent' => true,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Payment Logging Settings
    |--------------------------------------------------------------------------
    */

    'logging' => [
        // Enable payment logging
        'enabled' => env('PAYMENT_LOGGING', true),
        
        // Log level for payment events
        'level' => env('PAYMENT_LOG_LEVEL', 'info'),
        
        // Log payment details (be careful with sensitive data)
        'log_details' => env('PAYMENT_LOG_DETAILS', false),
        
        // Log file for payment events
        'file' => env('PAYMENT_LOG_FILE', 'payment.log'),
    ],

]; 