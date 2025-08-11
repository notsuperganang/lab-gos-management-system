<?php

return [

    /*
    |--------------------------------------------------------------------------
    | WhatsApp API Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for WhatsApp Business API integration used for sending
    | notifications to users and administrators about laboratory requests.
    |
    */

    'enabled' => env('WHATSAPP_ENABLED', false),

    'api_url' => env('WHATSAPP_API_URL', 'https://graph.facebook.com/v17.0'),

    'phone_number_id' => env('WHATSAPP_PHONE_NUMBER_ID'),

    'access_token' => env('WHATSAPP_ACCESS_TOKEN'),

    'webhook_verify_token' => env('WHATSAPP_WEBHOOK_VERIFY_TOKEN'),

    /*
    |--------------------------------------------------------------------------
    | Message Settings
    |--------------------------------------------------------------------------
    */

    'timeout' => env('WHATSAPP_TIMEOUT', 30),

    'max_retries' => env('WHATSAPP_MAX_RETRIES', 3),

    'retry_delay' => env('WHATSAPP_RETRY_DELAY', 5), // seconds

    /*
    |--------------------------------------------------------------------------
    | Queue Settings
    |--------------------------------------------------------------------------
    */

    'queue' => env('WHATSAPP_QUEUE', 'default'),

    'queue_delay' => env('WHATSAPP_QUEUE_DELAY', 0), // seconds

    /*
    |--------------------------------------------------------------------------
    | Template Settings
    |--------------------------------------------------------------------------
    */

    'templates_path' => storage_path('app/templates'),

    'default_language' => 'id', // Indonesian

    /*
    |--------------------------------------------------------------------------
    | Phone Number Settings
    |--------------------------------------------------------------------------
    */

    'country_code' => '+62', // Indonesia

    'admin_numbers' => [
        env('WHATSAPP_ADMIN_PHONE_1'),
        env('WHATSAPP_ADMIN_PHONE_2'),
        env('WHATSAPP_ADMIN_PHONE_3'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Laboratory Settings
    |--------------------------------------------------------------------------
    */

    'lab_name' => 'Lab GOS - USK',
    
    'lab_phone' => env('WHATSAPP_LAB_PHONE', '+62651755555'),

    'lab_email' => 'lab.gos@usk.ac.id',

    'working_hours' => 'Senin-Jumat 08:00-16:00 WIB',

];