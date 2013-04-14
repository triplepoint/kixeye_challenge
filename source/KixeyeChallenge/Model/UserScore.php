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
     * A convenient constant, used for mysql date values
     *
     * @var string
     */
    const DB_TIME_FORMAT = 'Y-m-d H:i:s';

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
        $result = $this->database->query(
            "INSERT INTO `users` (`fb_id`, `country`, `locale`)
            SELECT *
                FROM (
                    SELECT '{$user_id}', '{$user_country}', '{$user_locale}') AS `tmp`
                    WHERE NOT EXISTS (
                        SELECT `fb_id` FROM `users` WHERE `fb_id` = '{$user_id}'
                    )
                LIMIT 1;"
        );

        if ($result === false) {
            throw new \Exception('Database error when inserting user record: '.$this->database->error);
        }

        // Insert the score record
        $result = $this->database->query(
            "INSERT INTO `user_scores` (`user_id`, `score`, `timestamp`)
            SELECT `id`, '{$score}', '{$timestamp}' AS `tmp`
                FROM `users` WHERE `fb_id`='{$user_id}';"
        );

        if ($result === false) {
            throw new \Exception('Database error when inserting user score record: '.$this->database->error);
        }
    }

    /**
     * Fetch the total count of unique players in the system, for all time
     *
     * @return integer the count of unique players
     */
    public function countTotalPlayerCount()
    {
        $query = "SELECT count(*) AS `count` FROM `users`;";

        $result = $this->database->query($query);

        if ($result === false) {
            throw new \Exception('Database error when loading total player count: '.$this->database->error);
        }

        $row = $result->fetch_assoc();
        return $row['count'];
    }

    /**
     * Fetch the count of unique users who registered scoring events during the given
     * time interval.
     *
     * @param  \DateTime     $start    The beginning of the target window
     * @param  \DateInterval $duration The duration of the target window
     *
     * @return integer the count of unique players who logged scores during the time interval
     */
    public function countUsersWithScoreEventsInPeriod(\DateTime $start, \DateInterval $duration)
    {
        $start_string = $start->format(self::DB_TIME_FORMAT);
        $stop_string  = $start->add($duration)->format(self::DB_TIME_FORMAT);

        $query = "SELECT count( DISTINCT `users`.`id`) AS `count`
                FROM `users`
            INNER JOIN `user_scores`
                ON `users`.`id` = `user_scores`.`user_id`
            WHERE `user_scores`.`timestamp` BETWEEN '{$start_string}' AND '{$stop_string}';";

        $result = $this->database->query($query);

        if ($result === false) {
            throw new \Exception('Database error when loading player interval count: '.$this->database->error);
        }

        $row = $result->fetch_assoc();
        return $row['count'];
    }

    /**
     * Fetch the collection of the top N players, by score.
     *
     * @param  integer $count The number of players to fetch from the top of the leaderboard.
     *
     * @return array[] The ordered collection of user rows
     */
    public function getTopPlayers($count)
    {
        $query = "SELECT `t1`.*, `t2`.*
                FROM `users` AS `t1`
            INNER JOIN
                `user_scores` AS `t2`
                ON `t1`.`id` = `t2`.`user_id`
            INNER JOIN
            (
                SELECT max(`score`) AS `max_score`, `user_id`
                FROM `user_scores`
                GROUP BY `user_id`
            ) AS `t3`
                ON `t2`.`user_id` = `t3`.`user_id`
                AND `t2`.`score` = `t3`.`max_score`
            ORDER BY `t2`.`score` DESC
            LIMIT {$count};";

        $result = $this->database->query($query);

        if ($result === false) {
            throw new \Exception('Database error when loading player interval count: '.$this->database->error);
        }

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Fetch the collection of the top N players, by score improvement over the given interval.
     *
     * TODO This one doesn't work
     *
     * @param  integer   $count The number of players to fetch from the top of the leaderboard.
     * @param  \DateTime $start The beginning of the target window
     * @param  \DateTime $stop  The end of the target window
     *
     * @return array[] The ordered collection of user rows
     */
    public function getTopImprovingPlayers($count, \DateTime $start, \DateTime $stop)
    {
        $start_string = $start->format(self::DB_TIME_FORMAT);
        $stop_string  = $stop->format(self::DB_TIME_FORMAT);

        $query = "SELECT `t1`.*, `t2`.*, `t3`.`min_score`, `t3`.`max_score`, `t3`.`delta_score`
                FROM `users` AS `t1`
            INNER JOIN
                `user_scores` AS `t2`
                ON `t1`.`id` = `t2`.`user_id`
            INNER JOIN
            (
                SELECT max(`score`) AS `max_score`, min(`score`) AS `min_score`, max(`score`)-min(`score`) AS `delta_score`, `user_id`
                FROM `user_scores`
                WHERE `user_scores`.`timestamp` BETWEEN '{$start_string}' AND '{$stop_string}'
                GROUP BY `user_id`
            ) AS `t3`
                ON `t2`.`user_id` = `t3`.`user_id`
                AND `t2`.`score` = `t3`.`max_score`
            ORDER BY `t2`.`score` DESC
            LIMIT {$count};";

        $result = $this->database->query($query);

        if ($result === false) {
            throw new \Exception('Database error when loading player interval count: '.$this->database->error);
        }

        return $result->fetch_all(MYSQLI_ASSOC);
    }
}
