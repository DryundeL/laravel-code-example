<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Sentry Configuration
    |--------------------------------------------------------------------------
    |
    | Конфигурация для интеграции с Sentry для мониторинга ошибок
    | и производительности приложения.
    |
    */

    // Хост Sentry сервера
    'host' => env('SENTRY_HOST', 'https://instudy-kn.sentry.io/'),

    // Разрешенные project_id для туннеля
    'project_ids' => array_filter(explode(',', env('SENTRY_PROJECT_IDS', '4510189498597456,4508302873067600,4510342130303056,4510342154027088'))),

    // Настройки туннеля
    'tunnel' => [
        'enabled' => env('SENTRY_TUNNEL_ENABLED', true),
        'rate_limit' => env('SENTRY_TUNNEL_RATE_LIMIT', 100), // запросов в минуту
        'timeout' => env('SENTRY_TUNNEL_TIMEOUT', 5), // секунд
        'max_envelope_size' => env('SENTRY_TUNNEL_MAX_SIZE', 1024 * 1024), // 1MB
    ],

    // CORS настройки для туннеля
    'cors' => [
        'allowed_origins' => array_filter(explode(',', env('SENTRY_CORS_ORIGINS', '*'))),
        'allowed_headers' => [
            'Content-Type',
            'X-Requested-With',
            'Authorization',
            'X-Sentry-Auth',
            'X-Sentry-Envelope',
        ],
        'allowed_methods' => ['POST', 'OPTIONS'],
    ],
];
