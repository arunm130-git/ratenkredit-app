<?php

namespace App\Controllers;

class LoanOfferController
{
    public function __construct(private $config)
    {
    }
    public function fetchLoanOffers(): void
    {
        if (!$this->validateRequest()) {
            return;
        }

        $amount = $_POST['amount'];
        $offers = [];

        $loanProviders = $this->config['loan_providers'];


        // TODO: Move business logic to services
        foreach ($loanProviders as $provider) {
            switch ($provider) {
                // TODO: Use Factories for different providers
                case 'ing-diba':
                    $ingDibaSettings = $this->config['ing_diba_settings'];

                    $response = file_get_contents($ingDibaSettings['url'], false, stream_context_create([
                        "http" => [
                            "method" => "GET",
                            "header" => 'X-Access-key: ' . $ingDibaSettings['access_token']
                        ]
                    ]));

                    $offers[$provider] = json_decode($response, true);
                    break;

                case 'Smava':
                    // TODO: Use consistent approach across the project for fetching API details
                    $smavaSettings = $this->config['s_mava_settings'];

                    // TODO: Implement parallel CURL requests
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

                    // TODO: Add error handling for curl_exec and log failures
                    $offers[$provider] = json_decode(curl_exec($curl), true);
                    curl_close($curl);
                    break;
            }

        }
        include(dirname(__FILE__).'/../../view.phtml');
    }

    private function validateRequest(): bool
    {
        // TODO: Make a custom Validator class
        $amount = $_POST['amount'] ?? null;

        if (!isset($amount)) {
            $this->sendErrorResponse('Amount is required.');
            return false;
        }

        $amount = trim($amount);

        if (empty($amount) || !is_numeric($amount)) {
            $this->sendErrorResponse('Amount must be a valid number.');
            return false;
        }

        if ($amount <= 0) {
            $this->sendErrorResponse('Amount must be greater than zero.');
            return false;
        }

        return true;
    }
    private function sendErrorResponse(string $errorMessage, int $errorCode = 400)
    {
        // TODO: Move response handling to a ResponseHelper or use a Response object
        http_response_code($errorCode);
        header('Content-Type: application/json');

        $response = [
                'error' => $errorMessage
        ];

        echo json_encode($response);
    }
}