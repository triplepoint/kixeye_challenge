<?php

namespace KixeyeChallenge;

use KixeyeLibs\Http\Request;
use KixeyeChallenge\Controller\SomeController;

/**
 * This router is capable of matching all the handled
 * requests for this project to the appropriate controller.
 */
class Router
{
    /**
     * Given the request, return a controller suitable for
     * performing the necessary business behavior and mutating
     * a response object.
     */
    public function getController(Request $request)
    {
        // TODO - This router doesn't yet route

        // Interrogate the request and figure out which controller to build TODO
        $request->getHost();

        $controller = new SomeController();
        return $controller;
    }
}
