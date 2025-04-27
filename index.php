<?php

// Include Composer Autoloader
require __DIR__ . '/vendor/autoload.php';

// Include bootstrap file (for configuration, environment settings, etc.)
require_once __DIR__ . '/bootstrap.php';

// Require routes (after config and error handling)
require_once __DIR__ . '/routes/web.php';
