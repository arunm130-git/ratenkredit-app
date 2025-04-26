<?php

namespace App\Factories;

use App\Services\ConfigurationService;
use App\Services\IngDibaService;
use App\Services\ProviderServiceInterface;
use App\Services\SmavaService;

class LoanProviderFactory
{
    public function make(string $provider): ProviderServiceInterface
    {
        $config = new ConfigurationService();

        return match (strtolower($provider)) {
            'ing-diba' => new IngDibaService($config),
            'smava' => new SmavaService($config),
            default => throw new \InvalidArgumentException("Unsupported provider: $provider"),
        };
    }
}
