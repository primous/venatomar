<?php

return [
    /*
    |--------------------------------------------------------------------------
    | VAPID Configuration
    |--------------------------------------------------------------------------
    |
    | VAPID keys for Web Push Notifications (RFC 8292).
    | Subject must be a mailto: or a valid URL.
    | Keys must be base64url encoded NIST P-256 elliptic curve keys.
    |
    */
    'vapid' => [
        'subject' => env('VAPID_SUBJECT', 'mailto:contact@venatomar.com'),
        'public_key' => env('VAPID_PUBLIC_KEY', 'BH1O3rD33Q8Dq_sVba5eZi1aQuSXO2LqYb6-9-6yfovRDc3VxMC550W4IIkbHR5je5VuqfNkBi3L3Zg__iDGXm0'),
        'private_key' => env('VAPID_PRIVATE_KEY', 'Op4O6KmiJRinYhf4ZIq3QKwoC9BZWLBb0KFVY-StIZE'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Notification Options
    |--------------------------------------------------------------------------
    */
    'defaults' => [
        'icon' => env('WEBPUSH_DEFAULT_ICON', '/images/logo.png'),
        'badge' => env('WEBPUSH_DEFAULT_BADGE', '/images/logo.png'),
        'ttl' => 2419200, // 4 weeks
    ],
];
