<?php

namespace App\Services;

class SmavaService implements ProviderServiceInterface
{
    public function __construct(
        private ConfigurationServiceInterface $config,
    )
    {
    }

    public function fetchLoanOffers(array $parameters): mixed
    {
        $smavaSettings = $this->config->get('smava_settings');

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => $smavaSettings['url'],
            // TODO: Remove these comments and handle both cases
            /*post does not work with mock server CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => array(
                'month' => 3,
                'loan' => $_GET['amount']
            ),*/
            CURLOPT_HTTPHEADER => [
                'X-access-key: ' . $smavaSettings['access_token'],
            ]
        ));

        $response = curl_exec($curl);

        // TODO: Add error handling for curl_exec and log failures
        curl_close($curl);

        return json_decode($response, true);
    }
}