<?php

namespace App\Controllers;

use App\Factories\LoanProviderFactory;
use App\Services\ConfigurationServiceInterface;
use InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Twig\Environment;

readonly class LoanOfferController
{
    public function __construct(
        private ConfigurationServiceInterface $config,
        private LoanProviderFactory $providerFactory,
        private Environment $twig,
        private LoggerInterface $logger
    ) {
    }

    public function fetchLoanOffers(Request $request): void
    {
        try {
            $offers = [];
            $errors = [];

            $this->validateOfferFetchRequest($request);

            // Retrieve the list of loan providers from configuration
            $loanProviders = $this->config->get('loan_providers');

            // Instantiate service for each loan provider and fetch loan offers
            foreach ($loanProviders as $provider) {
                $service = $this->providerFactory->make($provider);
                $offers[$provider] = $service->fetchLoanOffers($request->request->all());
            }
        } catch (InvalidArgumentException $e) {
            // Clear fetched offers in case of an error to avoid inconsistent/partial data
            $offers = [];
            $errors[] = $e->getMessage();
        } catch (\Exception $e) {
            $this->logger->error("Error fetching loan offers: " . $e->getMessage());

            $offers = [];
            $errors[] = 'Failed to fetch loan offers. Please try again later.';
        }

        $html = $this->twig->render('loan_offer_dashboard.html.twig', [
            'offers' => $offers,
            'errors' => $errors,
            'amount' => $request->request->get('amount', ''),
            'month' => $request->request->get('month', ''),
        ]);

        echo $html;
    }

    private function validateOfferFetchRequest(Request $request): void
    {
        // Fetch validation rules for dashboard request
        $constraints = $this->config->get('loan_constraints');

        $amount = $request->request->get('amount');
        $duration = $request->request->get('month');

        if (!is_numeric($amount)) {
            throw new \InvalidArgumentException('Loan amount must be a number.');
        }

        // Check if the amount is within the configured range
        if ($amount < $constraints['amount']['min'] || $amount > $constraints['amount']['max']) {
            throw new \InvalidArgumentException(
                "Loan amount must be between {$constraints['amount']['min']} and {$constraints['amount']['max']}."
            );
        }

        if (!is_numeric($duration)) {
            throw new \InvalidArgumentException('Loan duration must be a number.');
        }

        // Check if duration falls within the configured range
        if ($duration < $constraints['duration']['min'] || $duration > $constraints['duration']['max']) {
            throw new \InvalidArgumentException(
                "Loan amount must be between {$constraints['amount']['min']} and {$constraints['amount']['max']}."
            );
        }
    }
}
