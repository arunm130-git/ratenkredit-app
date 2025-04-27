<?php

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Check environment variable for error reporting
$env = $_ENV['APP_ENV'] ?: 'production';
$isDebugMode = $_ENV['APP_DEBUG'] === 'true';

if ($env === 'production') {
    ini_set('display_errors', 'off');
    ini_set('log_errors', 'on');
    ini_set('error_log', '/var/www/html/logs/php_errors.log');
} else {
    ini_set('display_errors', $isDebugMode ? 'on' : 'off');
    ini_set('log_errors', 'on');
    ini_set('error_log', '/var/www/html/logs/php_errors.log');
}