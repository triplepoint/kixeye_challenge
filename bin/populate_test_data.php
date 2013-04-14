#!/usr/bin/env php
<?php
/**
 * This script will insert test data into the score database.
 */

chdir(__DIR__ . '/../');

ini_set('error_log', 'logs/php_cli_error.log');
ini_set('date.timezone', 'UTC');
ini_set('upload_max_filesize', '10M');
ini_set('log_errors', 'On');
ini_set('display_errors', 'On');
ini_set('display_startup_errors', 'On');
ini_set('error_reporting', E_ALL);
mb_internal_encoding('UTF-8');

// Convert error events to ErrorException, and log all other warnings and notices
$error_handler = function ($number, $string, $file, $line, $context) {
    if (in_array($number, [E_USER_ERROR, E_RECOVERABLE_ERROR])) {
        throw new \ErrorException($string, 1, $number, $file, $line);
    } else if ((bool) ($number & ini_get('error_reporting'))) {
        error_log($string, 0);
    }

    return false;
};
set_error_handler($error_handler);

// Initialize the class autoloader
require __DIR__ . '/../vendor/autoload.php';

// Initialize the service container and proceed to handling the page request
$service_container = new \KixeyeChallenge\ServiceContainer();

$data_generator = new \KixeyeChallenge\Model\UserScoreTestDataGenerator($service_container['user_score_model']);
$data_generator->setStartTime(new DateTime('now - 10 days'));
$data_generator->setStopTime(new DateTime('now'));

$start = microtime(true);
$data_generator->generate(1e6);
$stop = microtime(true);

echo "\nelapsed time: " . number_format($stop-$start, 3) . "(s)\n";
