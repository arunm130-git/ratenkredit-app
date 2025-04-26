<?php

namespace App\Services;

class IngDibaService implements ProviderServiceInterface
{
    public function __construct(
        private ConfigurationServiceInterface $config,
    )
    {
    }

    public function fetchLoanOffers(array $parameters): mixed
    {
        $ingDibaSettings = $this->config->get('ing_diba_settings');

        $response = file_get_contents($ingDibaSettings['url'], false, stream_context_create([
            "http" => [
                "method" => "GET",
                "header" => 'X-Access-key: ' . $ingDibaSettings['access_token']
            ]
        ]));

        return json_decode($response, true);

    }
}
