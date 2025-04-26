<?php

namespace App\Services;

use App\Traits\LoanOfferFormatter;
use InvalidArgumentException;

class IngDibaService implements ProviderServiceInterface
{
    use LoanOfferFormatter;

    public function __construct(
        private ConfigurationServiceInterface $config,
    ) {}

    public function fetchLoanOffers(array $parameters): array
    {
        $this->validateRequest($parameters);

        $response = $this->sendRequest($parameters);

        if (!is_array($response)) {
            return [];
        }

        return $this->validateAndFormatResponse($response);
    }

    private function validateRequest(array $parameters): void
    {
        if (!isset($parameters['amount']) || !is_numeric($parameters['amount'])) {
            throw new InvalidArgumentException('Invalid loan amount provided.');
        }
    }

    private function sendRequest(array $parameters): mixed
    {
        $curl = curl_init();
        $curlOptions = $this->getCurlOptions($parameters);

        curl_setopt_array($curl, $curlOptions);

        $response = curl_exec($curl);
        curl_close($curl);

        return json_decode($response, true);
    }

    private function getCurlOptions(array $parameters): array
    {
        $ingDibaSettings = $this->config->get('ing_diba_settings');

        return [
            CURLOPT_URL => $ingDibaSettings['url'] . $parameters['amount'],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'X-Access-key: ' . $ingDibaSettings['access_token'],
            ],
        ];
    }

    private function validateAndFormatResponse(array $response): array
    {
        $offer = [];

        if (isset($response['zinsen'], $response['duration'])) {
            $offer = [
                'interest' => $this->formatInterest($response['zinsen']),
                'duration' => $this->formatDuration($response['duration']),
            ];
        }

        return $offer;
    }
}
