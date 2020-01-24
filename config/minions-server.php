<?php

return [
    /*
     |--------------------------------------------------------------------------
     | Server Configuration
     |--------------------------------------------------------------------------
     |
     | Define the server configuration including port number, SSL support etc.
     |
     */

    'host' => env('MINION_SERVER_HOST', '127.0.0.1'),
    'port' => env('MINION_SERVER_PORT', 8085),
    'secure' => env('MINION_SERVER_SECURE', false),
    'options' => [
        'tls' => array_filter([
            'local_cert' => env('MINION_SERVER_TLS_CERT', null),
            // 'crypto_method' => STREAM_CRYPTO_METHOD_TLSv1_2_SERVER
        ]),
    ],
];
