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

// Define routes
switch ($uri) {
    case '/':
        if ($method === 'GET') {
            $twig = initializeTwigService();

            // Render the dashboard template
            echo $twig->render('loan_offer_dashboard.html.twig');
        } else {
            // Handle unsupported HTTP Methods
            sendJsonResponse(405, ['error' => 'Method Not Allowed']);
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
            sendJsonResponse(405, ['error' => 'Method Not Allowed']);
        }
        break;

    default:
        sendJsonResponse(404, ['error' => 'Route not found']);
        break;
}

// Function to create the LoanOfferController instance
function createLoanOfferController(): LoanOfferController
{
    $config = new ConfigurationService();
    $loanProviderFactory = new LoanProviderFactory();
    $twig = initializeTwigService();
    $logger = initializeLogger();

    return new LoanOfferController($config, $loanProviderFactory, $twig, $logger);
}

// Function to initialize the Twig service
function initializeTwigService(): Environment
{
    $loader = new FilesystemLoader(__DIR__ . '/../templates');
    return new Environment($loader);
}

// Function to initialize the Logger service
function initializeLogger(): Logger
{
    $logger = new Logger('app');
    $logger->pushHandler(new StreamHandler(__DIR__ . '/../app.log'));
    return $logger;
}

// Function to send JSON responses with status codes
function sendJsonResponse(int $statusCode, array $data): void
{
    http_response_code($statusCode);
    echo json_encode($data);
}
