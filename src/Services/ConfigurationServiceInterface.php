<?php

namespace App\Services;
interface ConfigurationServiceInterface
{
    public function get(string $key): mixed;
}
