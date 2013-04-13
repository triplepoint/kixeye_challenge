<?php
/**
 * This file serves as the entry point for all HTTP traffic.
 */

chdir(__DIR__ . '/../');

ini_set('error_log', 'logs/php_error.log');
ini_set('date.timezone', 'UTC');
ini_set('upload_max_filesize', '10M');
ini_set('log_errors', 'On');
ini_set('display_errors', 'Off');
ini_set('display_startup_errors', 'Off');
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

$service_container = new \KixeyeChallenge\ServiceContainer();

try {
    $router = $service_container['router'];

    $request = $service_container['request'];

    $controller = $router->getController($request);

    $response = $service_container['response'];

    $controller->populateResponse($response);

    $response->send();

    exit();

} catch (\KixeyeChallenge\Exception\NotFound $e) {
    $response = $service_container['response'];
    $response->setStatusCode(404);
    $response->setBody('404\'ed!');
    $response->send();

} catch (\KixeyeChallenge\Exception\MethodNotAllowed $e) {
    $response = $service_container['response'];
    $response->setStatusCode(405);
    $response->setHeader('Allow', implode(', ', $e->getAllowedMethods()));
    $response->send();

} catch (\Exception $e) {
    $response = $service_container['response'];
    $response->setStatusCode(500);
    $response->setBody('This one\'s serious.');
    $response->send();
}
