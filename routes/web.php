<?php

use App\Controllers\LoanOfferController;
use App\Factories\LoanProviderFactory;
use App\Services\ConfigurationService;
use Twig\Loader\FilesystemLoader;
use Twig\Environment;
use Symfony\Component\HttpFoundation\Request;

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

switch ($uri) {
    case '/':
        // TODO: Inject services instead of instantiating here
        $config = new ConfigurationService();
        $loanProviderFactory = new LoanProviderFactory();

        $loader = new FilesystemLoader(__DIR__ . '/../templates');
        $twig = new Environment($loader);

        $controller = new LoanOfferController($config, $loanProviderFactory, $twig);

        if ($method === 'POST') {
            $request = Request::createFromGlobals();
            $controller->fetchLoanOffers($request);
        } elseif ($method === 'GET') {
            echo $twig->render('loan_offer_dashboard.html.twig');
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