<?php

namespace App\Controllers;

use App\Factories\LoanProviderFactory;
use App\Services\ConfigurationServiceInterface;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\Request;
use Twig\Environment;

readonly class LoanOfferController
{
    public function __construct(
        private ConfigurationServiceInterface $config,
        private LoanProviderFactory $providerFactory,
        private Environment $twig,
    ) {
    }

    public function fetchLoanOffers(Request $request): void
    {
        try {
            $offers = [];
            $errors = [];

            $loanProviders = $this->config->get('loan_providers');

            foreach ($loanProviders as $provider) {
                $service = $this->providerFactory->make($provider);
                $offers[$provider] = $service->fetchLoanOffers($request->request->all());
            }

        } catch (InvalidArgumentException $e) {
            $offers = [];
            $errors[] = $e->getMessage();
        } catch (\Throwable $e) {
            $offers = [];
            $errors[] = 'Failed to fetch loan offers. Please try again later.';
        }

        // TODO: Make Template name configurable
        $html = $this->twig->render('loan_offer_dashboard.html.twig', [
            'offers' => $offers,
            'error' => $errors,
        ]);

        echo $html;
    }
}
