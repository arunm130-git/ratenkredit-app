<?php

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

return [
    'loan_providers' => [
        'ing-diba',
        'Smava',
    ],

    'ing_diba_settings' => [
        'url' => $_ENV['ING_DIBA_API_ENDPOINT'] ??
            'https://api.jsonbin.io/v3/b/65a6e50e266cfc3fde79aa14?meta=false&amount=',
        'access_token' => $_ENV['ING_DIBA_ACCESS_TOKEN'] ?? 'API-ACCESS-TOKEN',
        'method' => 'GET',
    ],

    'smava_settings' => [
        'url' => $_ENV['SMAVA_API_ENDPOINT'] ??
            'https://api.jsonbin.io/v3/b/65a6e71e1f5677401f1ebd2c?meta=false',
        'access_token' => $_ENV['SMAVA_ACCESS_TOKEN'] ?? 'API-ACCESS-TOKEN',

        // Use POST in production, GET for mock/testing
        'method' => ($_ENV['APP_ENV'] ?? 'local') === 'production' ? 'POST' : 'GET'
    ],
];