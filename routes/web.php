<?php


use App\Controllers\LoanOfferController;
use App\Factories\LoanProviderFactory;
use App\Services\ConfigurationService;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Twig\Loader\FilesystemLoader;
use Twig\Environment;
use Symfony\Component\HttpFoundation\Request;

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];
$twig = initializeTwigService();

// Define routes
switch ($uri) {
    case '/':
        if ($method === 'GET') {

            // Render the dashboard template
            echo $twig->render('loan_offer_dashboard.html.twig');
        } else {
            // Handle unsupported HTTP Methods
            echo $twig->render('error.html.twig', [
                'status_code' => 405
            ]);
        }
        break;

    case '/fetch-loan-offers':
        if ($method === 'POST') {
            // Initialize the required services and create Controller instance
            $controller = createLoanOfferController();
            $request = Request::createFromGlobals();

            // Fetch offers from loan providers and render the dashboard
            $controller->fetchLoanOffers($request);
        } else {
            // Handle unsupported HTTP Methods
            echo $twig->render('error.html.twig', [
                'status_code' => 405
            ]);
        }
        break;

    default:
        echo $twig->render('error.html.twig', [
            'status_code' => 404
        ]);
        break;
}

function createLoanOfferController(): LoanOfferController
{
    $config = new ConfigurationService();
    $loanProviderFactory = new LoanProviderFactory();
    $twig = initializeTwigService();
    $logger = initializeLogger();

    return new LoanOfferController($config, $loanProviderFactory, $twig, $logger);
}

function initializeTwigService(): Environment
{
    $loader = new FilesystemLoader(__DIR__ . '/../templates');
    return new Environment($loader);
}

function initializeLogger(): Logger
{
    $logger = new Logger('app');
    $logger->pushHandler(new StreamHandler(__DIR__ . '/../app.log'));
    return $logger;
}
