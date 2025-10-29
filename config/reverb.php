<?php

return [

    'apps' => [
        [
            'id' => env('REVERB_APP_ID', 'tournament'),
            'name' => env('APP_NAME', 'Tournament Bracket'),
            'key' => env('REVERB_APP_KEY', 'local-app-key'),
            'secret' => env('REVERB_APP_SECRET', 'local-app-secret'),
            'allowed_origins' => ['*'],
            'ping_interval' => env('REVERB_PING_INTERVAL', 30),
            'max_message_size' => env('REVERB_MAX_MESSAGE_SIZE', 10000),
        ],
    ],

    'host' => env('REVERB_SERVER_HOST', '0.0.0.0'),
    'port' => env('REVERB_SERVER_PORT', 8080),
    'hostname' => env('REVERB_HOST'),
    'options' => [
        'tls' => [],
    ],

    'scaling' => [
        'enabled' => false,
    ],

    'pulse_ingest_interval' => env('REVERB_PULSE_INGEST_INTERVAL', 15),
    'telescope_ingest_interval' => env('REVERB_TELESCOPE_INGEST_INTERVAL', 15),

];
