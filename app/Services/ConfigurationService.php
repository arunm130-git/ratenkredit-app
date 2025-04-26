<?php

namespace App\Services;


class ConfigurationService implements ConfigurationServiceInterface
{
    private array $config;

    public function __construct()
    {
        $this->config = require __DIR__ . '/../../config/config.php';
    }

    public function get(string $key): mixed
    {
        return $this->config[$key] ?? null;
    }
}