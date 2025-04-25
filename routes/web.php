<?php

use App\Controllers\LoanOfferController;

$config = require_once __DIR__ . '/../config/config.php';

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

switch ($uri) {
    case '/':
        $controller = new LoanOfferController($config);

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