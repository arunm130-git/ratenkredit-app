<?php

namespace App\Services;

use App\Traits\LoanOfferFormatter;
use InvalidArgumentException;

class SmavaService implements ProviderServiceInterface
{
    use LoanOfferFormatter;
    public function __construct(
        private readonly ConfigurationServiceInterface $config,
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

        if (isset($parameters['month']) && !is_numeric($parameters['month'])) {
            throw new InvalidArgumentException('Invalid loan duration provided.');
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
        $smavaSettings = $this->config->get('smava_settings');

        $curlOptions = [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_URL => $smavaSettings['url'],
            CURLOPT_HTTPHEADER => [
                'X-access-key: ' . $smavaSettings['access_token'],
            ]
        ];

        if (strtoupper($smavaSettings['method']) === 'POST') {
            $curlOptions[CURLOPT_POST] = true;
            $curlOptions[CURLOPT_POSTFIELDS] = [
                'month' => $parameters['month'],
                'loan' => $parameters['amount'],
            ];
        }

        return $curlOptions;
    }

    private function validateAndFormatResponse(array $response): array
    {
        $offer = [];

        if (isset($response['Interest'], $response['Terms']['Duration'])) {
            $offer = [
                'interest' => $this->formatInterest($response['Interest']),
                'duration' => $this->formatDuration($response['Terms']['Duration']),
            ];
        }

        return $offer;
    }
}
