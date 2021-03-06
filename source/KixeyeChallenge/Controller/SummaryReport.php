<?php

namespace KixeyeChallenge\Controller;

use KixeyeChallenge\Controller\ControllerInterface;
use KixeyeLibs\ServiceContainer\ServiceContainer;
use KixeyeLibs\Http\Request;
use KixeyeLibs\Http\Response;

/**
 * This controller is responsible for receiving
 * new user score information and storing it in the database
 */
class SummaryReport implements ControllerInterface
{
    /**
     * The request which led to this controller.
     *
     * @var Request
     */
    protected $request;

    /**
     * The model object which handles User scores.
     *
     * @var \KixeyeChallenge\Model\UserScore
     */
    protected $user_score_model;

    /**
     * The template renderer that helps deal with the more complex output of
     * an HTML controller.
     *
     * @var \Kixeye\Template\Renderer
     */
    protected $template_renderer;

    /**
     * Pass in a service container which can act as a service locator
     * and continue to provide resources for use in fulfilling this request.
     *
     * @param ServiceContainer $service_locator The service locator dependency
     *
     * @return void
     */
    public function setServiceLocator(ServiceContainer $service_locator)
    {
        $this->request           = $service_locator['request'];
        $this->user_score_model  = $service_locator['user_score_model'];
        $this->template_renderer = $service_locator['template_renderer'];
    }

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
    public function performAction(Response $response)
    {
        // Enforce the HTTP method for this route
        if ($this->request->getMethod() !== 'GET') {
            $e = new \KixeyeChallenge\Exception\MethodNotAllowed();
            $e->setAllowedMethods(['GET']);
            throw $e;
        }

        // Fetch the report data
        $player_count = $this->user_score_model->countTotalPlayerCount();

        $daily_player_count = $this->user_score_model->countUsersWithScoreEventsInPeriod(
            new \DateTime('now - 24 hours'),
            new \DateInterval('P1D')
        );

        $top_ten_players = $this->user_score_model->getTopPlayers(10);

        $top_ten_improving_players = $this->user_score_model->getTopImprovingPlayers(
            10,
            new \DateTime('last monday 00:00:00'),
            new \DateTime('this sunday 23:59:59')
        );

        // Render the template for the report page
        $body = $this->template_renderer->render(
            __DIR__ . '/templates/report.html',
            [
                'player_count'              => $player_count,
                'daily_player_count'        => $daily_player_count,
                'top_ten_players'           => $top_ten_players,
                'top_ten_improving_players' => $top_ten_improving_players,
            ]
        );

        // Pack the response object with the results
        $response->setStatusCode(200);
        $response->setBody($body);
    }
}
