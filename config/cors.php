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

    // Allowing CORS for all paths
    'paths' => ['*'],

    // Allowing specific HTTP methods: GET, POST, PUT (Update), DELETE
    'allowed_methods' => ['GET', 'POST', 'PUT', 'DELETE'],

    // Allowing all origins
    'allowed_origins' => ['*'],

    // No specific patterns for allowed origins
    'allowed_origins_patterns' => [],

    // Allowing all headers
    'allowed_headers' => ['*'],

    // No specific headers to expose
    'exposed_headers' => [],

    // No caching for preflight responses
    'max_age' => 0,

    // Allowing credentials such as cookies and authorization headers
    'supports_credentials' => true,

];
