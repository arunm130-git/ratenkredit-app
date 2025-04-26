<?php

namespace App\Controllers;

use App\Factories\LoanProviderFactory;
use App\Services\ConfigurationServiceInterface;

readonly class LoanOfferController
{
    public function __construct(
        private ConfigurationServiceInterface $config,
        private LoanProviderFactory $providerFactory
    )
    {
    }

    public function fetchLoanOffers(): void
    {
        if (!$this->validateRequest()) {
            return;
        }

        $request = $_POST;
        $offers = [];

        $loanProviders = $this->config->get('loan_providers');

        try {
            foreach ($loanProviders as $provider) {
                $service = $this->providerFactory->make($provider);
                $offers[$provider] = $service->fetchLoanOffers($request);
            }

            include(dirname(__FILE__) . '/../../view.phtml');
        } catch (\Exception $e) {
            var_dump($e);
        }

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