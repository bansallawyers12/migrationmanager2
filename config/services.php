<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Python Services Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for the unified Python services that handle PDF processing,
    | email parsing, analysis, and rendering.
    |
    */

    'python' => [
        'url' => env('PYTHON_SERVICE_URL', 'http://localhost:5000'),
        'timeout' => env('PYTHON_SERVICE_TIMEOUT', 120),
        'max_retries' => env('PYTHON_SERVICE_MAX_RETRIES', 3),
        'health_check_interval' => env('PYTHON_SERVICE_HEALTH_CHECK_INTERVAL', 60),
    ],

    /*
    |--------------------------------------------------------------------------
    | PDF Converter Services
    |--------------------------------------------------------------------------
    |
    | Configuration for PDF conversion services (legacy support)
    |
    */

    'python_pdf' => [
        'url' => env('PYTHON_PDF_SERVICE_URL', 'http://127.0.0.1:5000'),
        'timeout' => env('PYTHON_PDF_SERVICE_TIMEOUT', 60),
    ],

    'python_converter' => [
        'url' => env('PYTHON_CONVERTER_URL', 'http://localhost:5000'),
        'timeout' => env('PYTHON_CONVERTER_TIMEOUT', 120),
    ],

    /*
    |--------------------------------------------------------------------------
    | OpenAI Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for OpenAI API integration
    |
    */

    'openai' => [
        'api_key' => env('OPENAI_API_KEY'),
        'organization' => env('OPENAI_ORGANIZATION'),
        'timeout' => env('OPENAI_TIMEOUT', 30),
    ],

    /*
    |--------------------------------------------------------------------------
    | Google reCAPTCHA Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for Google reCAPTCHA integration on login forms
    |
    */

    'recaptcha' => [
        'key' => env('RECAPTCHA_SITE_KEY'),
        'secret' => env('RECAPTCHA_SITE_SECRET'),
    ],

    /*
    |--------------------------------------------------------------------------
    | SMS Service Providers Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for SMS service providers:
    | - Cellcast: Used for Australian numbers (+61)
    | - Twilio: Used for international numbers (including India +91)
    |
    */

    'cellcast' => [
        'api_key' => env('CELLCAST_API_KEY'),
        'base_url' => env('CELLCAST_BASE_URL', 'https://api.cellcast.com.au/v1'),
        'sender_id' => env('CELLCAST_SENDER_ID', ''),
        'timeout' => env('CELLCAST_TIMEOUT', 30),
    ],

    'twilio' => [
        'account_sid' => env('TWILIO_SID'),
        'auth_token' => env('TWILIO_TOKEN'),
        'from' => env('TWILIO_FROM'),
        'timeout' => env('TWILIO_TIMEOUT', 30),
    ],

];