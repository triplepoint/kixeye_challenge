<?php

namespace KixeyeChallenge\Model;

/**
 * This simple model object handles the storage and retrieval
 * of User scores.
 *
 * Future development should replace this with a more traditional
 * ORM.
 */
class UserScore
{
    /**
     * The database connection object
     *
     * @var \mysqli
     */
    protected $database;

    /**
     * Store this object's dependencies
     *
     * @param \mysqli $database The Database connection object
     */
    public function __construct(\mysqli $database)
    {
        $this->database = $database;
    }

    /**
     * Write the user and score data out to the database
     *
     * @param string  $user_id   The Facebook user ID
     * @param array   $user      The Facebook user description
     * @param integer $score     The user's new score
     * @param string  $timestamp The time of the user's score.  If null, assume now.
     *
     * @return void
     */
    public function writeUserAndScoreToDatabase($user_id, $user, $score, $timestamp = null)
    {
        $score        = $this->database->real_escape_string($score);
        $user_id      = $this->database->real_escape_string($user_id);
        $user_country = $this->database->real_escape_string($user['country']);
        $user_locale  = $this->database->real_escape_string($user['locale']);
        $timestamp    = $timestamp ?: date('Y-m-d H:i:s');

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
            SELECT `id`, '{$score}', '{$timestamp}' AS `tmp`
                FROM `users` WHERE `fb_id`='{$user_id}';"
        );
    }
}
