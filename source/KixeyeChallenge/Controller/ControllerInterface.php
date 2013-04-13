<?php

namespace KixeyeChallenge\Controller;

use KixeyeLibs\Http\Response;
use KixeyeChallenge\Controller\ControllerInterface;

/**
 * Classes which implement this interface are controllers,
 * suitable for dispatching business behavior and modifying
 * the given response object.
 */
interface ControllerInterface
{
    /**
     * Investigate the given Response, and return the
     * appropriate controller.
     *
     * @param  Response $response The response to use to choose the controller
     *
     * @return ControllerInterface The controller chosen to handle the request
     */
    public function populateResponse(Response $response);
}
