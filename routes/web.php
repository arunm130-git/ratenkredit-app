<?php

use App\Controllers\LoanOfferController;
use App\Factories\LoanProviderFactory;
use App\Services\ConfigurationService;

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

switch ($uri) {
    case '/':
        // TODO: Inject services instead of instantiating here
        $config = new ConfigurationService();
        $loanProviderFactory = new LoanProviderFactory();

        $controller = new LoanOfferController($config, $loanProviderFactory);

        if ($method === 'POST') {
            $controller->fetchLoanOffers();
        } elseif ($method === 'GET') {
            include __DIR__ . '/../view.phtml';
        } else {
            http_response_code(405);
            echo json_encode(['error' => 'Method Not Allowed']);
        }

        break;

    default:
        http_response_code(404);
        echo json_encode(['error' => 'Route not found']);
        break;
}