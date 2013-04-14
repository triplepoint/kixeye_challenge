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
class AddScoreRecord implements ControllerInterface
{
    /**
     * The request which led to this controller.
     *
     * @var Request
     */
    protected $request;

    /**
     * The helper object which parses and validates facebook signed requests
     *
     * @var \KixeyeLibs\Facebook\SignedRequestParser
     */
    protected $facebook_request_parser;

    /**
     * The model object which handles User scores.
     *
     * @var \KixeyeChallenge\Model\UserScore
     */
    protected $user_score_model;

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
        $this->request                 = $service_locator['request'];
        $this->facebook_request_parser = $service_locator['facebook_request_parser'];
        $this->user_score_model        = $service_locator['user_score_model'];
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
        if ($this->request->getMethod() !== 'POST') {
            $e = new \KixeyeChallenge\Exception\MethodNotAllowed();
            $e->setAllowedMethods(['POST']);
            throw $e;
        }

        $parsed_message = $this->getFacebookSignedRequest();

        $score = $this->getScoreFromRequest();

        $this->user_score_model->writeUserAndScoreToDatabase(
            $parsed_message['user_id'],
            $parsed_message['user'],
            $score
        );

        // Set the response
        $response->setStatusCode(201);
        $response->setBody(
            json_encode(
                [
                    'fb_id'   => $parsed_message['user_id'],
                    'fb_user' => $parsed_message['user'],
                    'score'   => $score,
                    'status'  => 'Score recorded'
                ]
            )
        );
    }

    /**
     * Extract the parsed facebook signed request from the submission
     *
     * @return array The parsed and validated facebook signed request
     */
    protected function getFacebookSignedRequest()
    {
        $post_vars = $this->request->getPostVariables();
        if (!array_key_exists('signed_request', $post_vars)) {
            throw new \Exception('The signed_request is not present in the request payload.');
        }
        $signed_request = $post_vars['signed_request'];

        $parsed_message = $this->facebook_request_parser->parse($signed_request);
        if (!array_key_exists('user_id', $parsed_message)) {
            throw new \Exception('The user ID is not present in the request.');
        }

        return $parsed_message;
    }

    /**
     * Extract the user's score from the submission
     *
     * @return integer The user's new score value
     */
    protected function getScoreFromRequest()
    {
        $post_vars = $this->request->getPostVariables();
        if (!array_key_exists('score', $post_vars)) {
            throw new \Exception('The score is not present in the request payload.');
        }

        if (!is_numeric($post_vars['score'])) {
            throw new \Exception('The score ('.$post_vars['score'].') is not valid.');
        }

        $score = (integer) $post_vars['score'];

        return $score;
    }
}
