<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your settings for cross-origin resource sharing
    | or "CORS". This determines what cross-origin operations may execute
    | in web browsers. You are free to adjust these settings as needed.
    |
    | To learn more: https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
    |
    */

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    'allowed_origins' => [
        'http://localhost:3000',           // React/Vue web apps
        'http://127.0.0.1:3000',
        'http://localhost:8080',           // Local development
        'capacitor://localhost',           // Capacitor apps (if using)
        'ionic://localhost',               // Ionic apps (if using)
        'http://localhost',                // Flutter web local
        'https://your-domain.com',         // Production domain
    ],

    'allowed_origins_patterns' => [
        '/^http:\/\/localhost:\d+$/',      // Any localhost port
        '/^http:\/\/127\.0\.0\.1:\d+$/',   // Any 127.0.0.1 port
    ],

    'allowed_headers' => [
        'Accept',
        'Authorization',
        'Content-Type',
        'X-Requested-With',
        'X-CSRF-TOKEN',
        'X-XSRF-TOKEN',
    ],

    'exposed_headers' => [
        'Authorization',
        'X-Pagination-Current-Page',
        'X-Pagination-Page-Count',
        'X-Pagination-Per-Page',
        'X-Pagination-Total-Count',
    ],

    'max_age' => 86400, // 24 hours

    'supports_credentials' => true, // Enable for JWT with cookies

];
