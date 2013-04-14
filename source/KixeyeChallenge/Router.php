<?php

namespace KixeyeChallenge;

use KixeyeLibs\Http\Request;
use KixeyeChallenge\Controller\AddScoreRecord;
use KixeyeChallenge\Controller\SummaryReport;

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
     *
     * @param Request $request the HTTP request object
     *
     * @throws \KixeyeChallenge\Exception\NotFound if a controller cannot be found
     *
     * @return KixeyeChallenge\Controller\ControllerInterface the appropriate Controller
     */
    public function getController(Request $request)
    {
        switch ($request->getPath()) {
            case '/v1/user/score':
                return new AddScoreRecord();
                break;
            case '/report':
                return new SummaryReport();
                break;
            default:
                throw new \KixeyeChallenge\Exception\NotFound();
                break;
        }
    }
}
