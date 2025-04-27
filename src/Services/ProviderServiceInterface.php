<?php

namespace App\Services;

interface ProviderServiceInterface
{
    public function fetchLoanOffers(array $parameters): mixed;
}
