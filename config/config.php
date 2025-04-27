<?php

return [
    // Fetch and format the providers from .env
    'loan_providers' => explode(',', $_ENV['LOAN_PROVIDERS'] ?? 'ing-diba,Smava'),

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

        // Use GET method in non-production environments as the mock server does not support POST requests
        'method' => ($_ENV['APP_ENV'] ?? 'local') === 'production' ? 'POST' : 'GET'
    ],

    'loan_constraints' => [
        'amount' => [
            'min' => $_ENV['MIN_LOAN_AMOUNT'] ?? 100,
            'max' => $_ENV['MAX_LOAN_AMOUNT'] ?? 99999999,
        ],
        'duration' => [
            'min' => $_ENV['MIN_LOAN_DURATION'] ?? 1,
            'max' => $_ENV['MAX_LOAN_DURATION'] ?? 480,
        ]
    ]
];
