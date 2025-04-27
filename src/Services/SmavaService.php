<?php

namespace App\Services;

use App\Traits\LoanOfferFormatter;
use GuzzleHttp\Client;
use InvalidArgumentException;

class SmavaService implements ProviderServiceInterface
{
    use LoanOfferFormatter;

    public function __construct(
        private readonly ConfigurationServiceInterface $config,
        private readonly Client $client
    ) {}

    public function fetchLoanOffers(array $parameters): array
    {
        $response = $this->sendRequest($parameters);
        return $this->validateAndFormatResponse($response);
    }

    private function sendRequest(array $parameters): array
    {
        $smavaSettings = $this->config->get('smava_settings');
        $url = $smavaSettings['url'];

        $options = [
            'headers' => [
                'X-access-key' => $smavaSettings['access_token'],
            ],
            'timeout' => 10,
        ];

        // Adding POST data if the request method is POST
        if (strtoupper($smavaSettings['method']) === 'POST') {
            $options['form_params'] = [
                'month' => $parameters['month'] ?? null,
                'loan' => $parameters['amount'],
            ];
        }

        $response = $this->client->request(strtoupper($smavaSettings['method']), $url, $options);

        // TODO: Throw custom exceptions on API failures

        return json_decode($response->getBody()->getContents(), true);
    }

    private function validateAndFormatResponse(array $response): array
    {
        if (isset($response['Interest'], $response['Terms']['Duration'])) {
            return [
                'interest' => $this->formatInterest($response['Interest']),
                'duration' => $this->formatDuration($response['Terms']['Duration']),
            ];
        }

        throw new \Exception('Invalid API response format from Smava: ' . json_encode($response));
    }
}
