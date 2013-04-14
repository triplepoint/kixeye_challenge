<?php

namespace KixeyeChallenge\Controller;

use KixeyeLibs\ServiceContainer\ServiceContainer;
use KixeyeLibs\Http\Response;

/**
 * Classes which implement this interface are controllers,
 * suitable for dispatching business behavior and modifying
 * the given response object.
 */
interface ControllerInterface
{
    /**
     * Pass in a service container which can act as a service locator
     * and continue to provide resources for use in fulfilling this request.
     *
     * @param ServiceContainer $service_locator The service locator dependency
     *
     * @return void
     */
    public function setServiceLocator(ServiceContainer $service_locator);

    /**
     * Perform the action that this controller embodies, and populate the
     * given Response object to reflect the results of this action.
     *
     * @param  Response $response The response to use to choose the controller
     *
     * @throws \KixeyeChallenge\Exception\MethodNotAllowed if the HTTP method is not one of the allowed methods
     *
     * @return void
     */
    public function performAction(Response $response);
}
