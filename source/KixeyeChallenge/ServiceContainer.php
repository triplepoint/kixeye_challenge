<?php

namespace KixeyeChallenge;

use \KixeyeLibs\ServiceContainer as LibsServiceContainer;
use \KixeyeChallenge\Router;
use \KixeyeLibs\Http\Request;
use \KixeyeLibs\Http\Response;
use \KixeyeLibs\Facebook\SignedRequestParser;

/**
 * This is the main service container used to construct
 * and configure this project.
 */
class ServiceContainer extends LibsServiceContainer
{
    /**
     * Configure and add the assets to this container store.
     *
     * @return void
     */
    public function __construct()
    {
        $this['router'] = function () {
            return new Router();
        };

        $this['request'] = function () {
            $request = new Request();
            $request->setFromGlobals($_GET, $_POST, $_COOKIE, $_SERVER);
            return $request;
        };

        $this['response'] = function () {
            return new Response();
        };

        $this['config_values'] = require 'configuration/project_config.php';

        $this['database_connection'] = function () {
            $host     = $this['config_values']['database_credentials']['host'];
            $user     = $this['config_values']['database_credentials']['user'];
            $password = $this['config_values']['database_credentials']['password'];
            $db_name  = $this['config_values']['database_credentials']['db_name'];
            $port     = $this['config_values']['database_credentials']['port'];
            $socket   = $this['config_values']['database_credentials']['socket'];
            return new \mysqli($host, $user, $password, $db_name, $port, $socket);
        };

        $this['facebook_request_parser'] = function () {
            $app_id = $this['config_values']['facebook_application']['appId'];
            $secret = $this['config_values']['facebook_application']['secret'];
            return new SignedRequestParser($app_id, $secret);
        };
    }
}
