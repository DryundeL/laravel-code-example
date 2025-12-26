<?php

return [
    'app_url' => env('APP_URL'),
    'landing_url' => env('APP_LANDING_URL', 'http://localhost'),
    'yandex_s3_url' => env('YANDEX_S3_URL', 'http://localhost/'),
    'dist_url' => env('DIST_URL', 'https://dist.inpsycho.ru'),

    'sso_url' => env('SSO_URL'),

    'mobile_sso_redirect_url' => env('MOBILE_SSO_REDIRECT_URL'),
    'mobile_client_id' => env('MOBILE_CLIENT_ID'),
    'mobile_client_secret' => env('MOBILE_CLIENT_SECRET'),

    'desktop_sso_redirect_url' => env('DESKTOP_SSO_REDIRECT_URL'),
    'desktop_client_id' => env('DESKTOP_CLIENT_ID'),
    'desktop_client_secret' => env('DESKTOP_CLIENT_SECRET'),

    'gpt_api_key' => env('INSTUDY_GPT_API_KEY'),
    'gpt_integration_domain' => env('INSTUDY_GPT_INTEGRATION_DOMAIN'),

    'ESB_url' => env('ESB_URL'),
    'ESB_token_ttl' => env('ESB_TOKEN_TTL'),
    'ESB_adapter' => env('ESB_ADAPTER'),

    'inStudy_secret' => env('INSTUDY_SECRET'),

    'help_mail' => env('HELP_MAIL'),
    'mts_mail' => env('MTS_MAIL'),

    'imobis_url' => env('IMOBIS_URL'),
    'imobis_api_token' => env('IMOBIS_API_TOKEN'),

    'admin_token' => env('ADMIN_TOKEN'),

    'crypt_key' => env('CRYPT_KEY'),

    'time_restriction_start' => env('TIME_RESTRICTION_START', '18:40'),
    'time_restriction_end' => env('TIME_RESTRICTION_END', '19:20'),
];
