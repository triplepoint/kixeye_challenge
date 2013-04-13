<?php

namespace KixeyeChallenge\Controller;

use KixeyeLibs\Http\Response;

/**
 * Some demo controller
 */
class SomeController implements ControllerInterface
{
    /**
     * Investigate the given Response, and return the
     * appropriate controller.
     *
     * @param  Response $response The response to use to choose the controller
     *
     * @return ControllerInterface The controller chosen to handle the request
     */
    public function populateResponse(Response $response)
    {
        // THIS IS ALL TEMPORARY TESTING CODE, TO DEMONSTRATE A CONTROLLER'S BEHAVIOR WITH ERRORS
        // TODO REMOVE ALL THIS
        $e = new \KixeyeChallenge\Exception\MethodNotAllowed();
        $e->setAllowedMethods(['POST', 'HEAD']);
        throw $e;

        $response->setStatusCode(604);
        $response->setHeader('Content-Type', 'application/json');
        $response->setBody('yay');
    }
}
