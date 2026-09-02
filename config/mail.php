<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Mailer
    |--------------------------------------------------------------------------
    |
    | This option controls the default mailer that is used to send all email
    | messages unless another mailer is explicitly specified when sending
    | the message. All additional mailers can be configured within the
    | "mailers" array. Examples of each type of mailer are provided.
    |
    */

    'default' => env('MAIL_MAILER', 'failover'),

    /*
    |--------------------------------------------------------------------------
    | Mailer Configurations
    |--------------------------------------------------------------------------
    |
    | Here you may configure all of the mailers used by your application plus
    | their respective settings. Several examples have been configured for
    | you and you are free to add your own as your application requires.
    |
    | Laravel supports a variety of mail "transport" drivers that can be used
    | when delivering an email. You may specify which one you're using for
    | your mailers below. You may also add additional mailers if needed.
    |
    | Supported: "smtp", "sendmail", "mailgun", "ses", "ses-v2",
    |            "postmark", "resend", "log", "array",
    |            "failover", "roundrobin"
    |
    */

    'mailers' => [

        'ses' => [
            'transport' => 'ses',
        ],

        'zoho' => [
            'transport' => 'smtp',
            'host' => env('ZOHO_MAIL_HOST', 'smtp.zoho.com'),
            'port' => (int) env('ZOHO_MAIL_PORT', 587),
            'username' => env('ZOHO_MAIL_USERNAME'),
            'password' => env('ZOHO_MAIL_PASSWORD'),
            'encryption' => env('ZOHO_MAIL_ENCRYPTION', 'tls'),
            'timeout' => null,
            'local_domain' => env('MAIL_EHLO_DOMAIN', parse_url(env('APP_URL', 'http://localhost'), PHP_URL_HOST)),
        ],

        'failover' => [
            'transport' => 'failover',
            'mailers' => [
                'ses',
                'zoho',
            ],
            'retry_after' => 60,
        ],

        /*
         * Deprecated name kept so queued jobs that still address "sendgrid"
         * keep sending via SES → Zoho instead of a missing mailer.
         */
        'sendgrid' => [
            'transport' => 'failover',
            'mailers' => [
                'ses',
                'zoho',
            ],
            'retry_after' => 60,
        ],

        'resend' => [
            'transport' => 'resend',
        ],

        'sendmail' => [
            'transport' => 'sendmail',
            'path' => env('MAIL_SENDMAIL_PATH', '/usr/sbin/sendmail -bs -i'),
        ],

        'log' => [
            'transport' => 'log',
            'channel' => env('MAIL_LOG_CHANNEL'),
        ],

        'array' => [
            'transport' => 'array',
        ],

        'roundrobin' => [
            'transport' => 'roundrobin',
            'mailers' => [
                'ses',
                'zoho',
            ],
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Global "From" Address
    |--------------------------------------------------------------------------
    |
    | You may wish for all emails sent by your application to be sent from
    | the same address. Here you may specify a name and address that is
    | used globally for all emails that are sent by your application.
    |
    */

    'from' => [
        'address' => env('MAIL_FROM_ADDRESS', 'hello@example.com'),
        'name' => env('MAIL_FROM_NAME', 'Example'),
    ],

    /*
    |--------------------------------------------------------------------------
    | No-reply From Address (Appointment + Signature)
    |--------------------------------------------------------------------------
    |
    | Shared from address for appointment and signature system emails.
    | Does not change the global from used by CRM compose, EOI, etc.
    | Display name uses mail.from.name (MAIL_FROM_NAME).
    |
    */

    'noreply' => [
        'address' => env('NOREPLY_MAIL_FROM_ADDRESS', env('MAIL_FROM_ADDRESS', 'info@bansalimmigration.com.au')),
    ],

    /*
    | Admin / office copy for client self-service appointment actions.
    */
    'info' => [
        'address' => env('MAIL_INFO_ADDRESS', env('MAIL_FROM_ADDRESS', 'info@bansalimmigration.com.au')),
    ],

];
