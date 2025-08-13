<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Solar API Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for various solar monitoring APIs
    |
    */

    'enphase' => [
        'api_key' => env('ENPHASE_API_KEY', 'ff758cebecf04e6acb7936e10c7acdd6'),
        'client_id' => env('ENPHASE_CLIENT_ID', '57a5960f4e42911bf87e814b4112bbce'),
        'client_secret' => env('ENPHASE_CLIENT_SECRET', '818fe4b0c56be49de5b08fd54239405a'),
        'redirect_uri' => env('ENPHASE_REDIRECT_URI', 'http://localhost:8001/api/enphase/callback'),
        'base_url' => env('ENPHASE_BASE_URL', 'https://api.enphaseenergy.com/api/v4'),
    ],

    'solaredge' => [
        'api_key' => env('SOLAREDGE_API_KEY', 'PSJJ158A7XWN4OC7LOJV1SD95WMDZE5C'),
        'base_url' => env('SOLAREDGE_BASE_URL', 'https://monitoringapi.solaredge.com'),
    ],

    'tesla' => [
        'client_id' => env('TESLA_CLIENT_ID', 'edaaa5a3-6a84-4608-9b30-da0c1dfe759a'),
        'client_secret' => env('TESLA_CLIENT_SECRET', 'ta-secret.uiQpnhishNTD4j%7'),
        'redirect_uri' => env('TESLA_REDIRECT_URI', 'http://localhost:8001/api/tesla/callback'),
        'base_url' => env('TESLA_BASE_URL', 'https://fleet-api.prd.na.vn.cloud.tesla.com'),
    ],

    'weather' => [
        'api_key' => env('WEATHER_API_KEY', '8f4a75e106424cbfbef202351252807'),
        'base_url' => env('WEATHER_API_BASE_URL', 'https://api.weatherapi.com/v1'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Data Sync Configuration
    |--------------------------------------------------------------------------
    */

    'sync' => [
        'interval' => env('SOLAR_SYNC_INTERVAL', 900), // 15 minutes in seconds
        'timeout' => env('SOLAR_API_TIMEOUT', 30), // API timeout in seconds
        'retry_attempts' => env('SOLAR_RETRY_ATTEMPTS', 3),
    ],

    /*
    |--------------------------------------------------------------------------
    | Supported Manufacturers
    |--------------------------------------------------------------------------
    */

    'manufacturers' => [
        'enphase' => 'Enphase Energy',
        'solaredge' => 'SolarEdge',
        'tesla' => 'Tesla Energy',
    ],
];
