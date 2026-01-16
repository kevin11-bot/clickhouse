<?php

return [
    'connection' => [
        'host' => env('CLICKHOUSE_HOST', 'localhost'),
        'port' => env('CLICKHOUSE_PORT', 8123),

        'database' => env('CLICKHOUSE_DATABASE', 'default'),
        'username' => env('CLICKHOUSE_USERNAME', 'default'),
        'password' => env('CLICKHOUSE_PASSWORD', ''),

        'timeout' => env('CLICKHOUSE_TIMEOUT', 1),
        'connectTimeout' => env('CLICKHOUSE_CONNECT_TIMEOUT', 2),
    ],

    'migrations' => [
        'path' => database_path('clickhouse-migrations'),
    ],
];
