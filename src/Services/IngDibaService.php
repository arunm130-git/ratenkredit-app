<?php

namespace App\Services;

use App\Traits\LoanOfferFormatter;
use GuzzleHttp\ClientInterface;

readonly class IngDibaService implements ProviderServiceInterface
{
    use LoanOfferFormatter;

    public function __construct(
        private ConfigurationServiceInterface $config,
        private ClientInterface $client,
    ) {
    }

    public function fetchLoanOffers(array $parameters): array
    {
        $response = $this->sendRequest($parameters);
        return $this->validateAndFormatResponse($response);
    }

    private function sendRequest(array $parameters): array
    {
        $ingDibaSettings = $this->config->get('ing_diba_settings');

        // Add amount parameter value to ing-diba endpoint
        $url = $ingDibaSettings['url'] . $parameters['amount'];

        $response = $this->client->request('GET', $url, [
            'headers' => [
                'X-Access-key' => $ingDibaSettings['access_token'],
            ],
            'timeout' => 10,
        ]);

        // TODO: Throw custom exceptions on API failures

        return json_decode($response->getBody()->getContents(), true);
    }

    private function validateAndFormatResponse(array $response): array
    {
        // Ensure that required fields exist in the response
        if (isset($response['zinsen'], $response['duration'])) {
            return [
                'interest' => $this->formatInterest($response['zinsen']),
                'duration' => $this->formatDuration($response['duration']),
            ];
        }

        throw new \Exception('Invalid API response format from ING DiBa : ' . json_encode($response));
    }
}
