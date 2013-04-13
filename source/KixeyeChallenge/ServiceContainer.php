<?php

namespace KixeyeChallenge;

use \KixeyeLibs\ServiceContainer as LibsServiceContainer;
use \KixeyeChallenge\Router;
use \KixeyeLibs\Http\Request;
use \KixeyeLibs\Http\Response;

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
    }
}
