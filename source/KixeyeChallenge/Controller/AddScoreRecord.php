<?php

namespace KixeyeChallenge\Controller;

use KixeyeChallenge\Controller\ControllerInterface;
use KixeyeLibs\ServiceContainer;
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
     * The database connection object
     *
     * @var \mysqli
     */
    protected $database;

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
        $this->database                = $service_locator['database_connection'];
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

        $this->writeUserAndScoreToDatabase(
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

    /**
     * Write the user and score data out to the database
     *
     * @param string  $user_id The Facebook user ID
     * @param array   $user    The Facebook user description
     * @param integer $score   The user's new score
     *
     * @return void
     */
    protected function writeUserAndScoreToDatabase($user_id, $user, $score)
    {
        $score        = $this->database->real_escape_string($score);
        $user_id      = $this->database->real_escape_string($user_id);
        $user_country = $this->database->real_escape_string($user['country']);
        $user_locale  = $this->database->real_escape_string($user['locale']);
        $now          = date('Y-m-d H:i:s');

        // Insert the user record, if it doesn't already exist
        $this->database->query(
            "INSERT INTO `users` (`fb_id`, `country`, `locale`)
            SELECT *
                FROM (
                    SELECT '{$user_id}', '{$user_country}', '{$user_locale}') AS `tmp`
                    WHERE NOT EXISTS (
                        SELECT `fb_id` FROM `users` WHERE `fb_id` = '{$user_id}'
                    )
                LIMIT 1;"
        );

        // Insert the score record
        $this->database->query(
            "INSERT INTO `user_scores` (`user_id`, `score`, `timestamp`)
            SELECT `id`, '{$score}', '{$now}' AS `tmp`
                FROM `users` WHERE `fb_id`='{$user_id}';"
        );
    }
}
