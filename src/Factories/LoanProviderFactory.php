<?php

namespace App\Factories;

use App\Services\ConfigurationService;
use App\Services\IngDibaService;
use App\Services\ProviderServiceInterface;
use App\Services\SmavaService;
use GuzzleHttp\Client;

class LoanProviderFactory
{
    public function make(string $provider): ProviderServiceInterface
    {
        $config = new ConfigurationService();
        $guzzleHttpClient = new Client();

        return match (strtolower($provider)) {
            'ing-diba' => new IngDibaService($config, $guzzleHttpClient),
            'smava' => new SmavaService($config, $guzzleHttpClient),
            default => throw new \Exception("Unsupported provider: $provider"),
        };
    }
}
