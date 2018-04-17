<?php

require_once 'vendor/autoload.php';
require_once 'OmniCron.php';
require_once 'vendor/vlucas/phpdotenv/src/Dotenv.php';

$dotenv = new Dotenv\Dotenv(__DIR__, '.env');
$dotenv->load();

// Test Code
$api = new OmniCron\OmniCron();

$api->sendError(1, "Test error");