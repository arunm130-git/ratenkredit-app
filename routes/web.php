<?php

require_once __DIR__ . '/../app/Controllers/LoanOfferController.php';

$config = require_once __DIR__ . '/../config/config.php';
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

switch ($uri) {
    case '/':
        if ($method === 'POST') {
            $controller = new \App\Controllers\LoanOfferController($config);
            $controller->fetchLoanOffers();
        } elseif ($method === 'GET') {
            include(dirname(__FILE__) . '/../view.phtml');
        }
        break;

    default:
        http_response_code(404);
        echo json_encode(['error' => 'Route not found']);
        break;
}