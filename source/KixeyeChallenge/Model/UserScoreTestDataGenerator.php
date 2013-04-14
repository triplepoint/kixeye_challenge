<?php

namespace KixeyeChallenge\Model;

use \KixeyeChallenge\Model\UserScore;

/**
 * This utility class generates test data for the
 * UserScore data model.
 */
class UserScoreTestDataGenerator
{
    /**
     * A convenient constant, used for mysql date values
     *
     * @var string
     */
    const DB_TIME_FORMAT = 'Y-m-d H:i:s';

    /**
     * What score does a new user start with?
     *
     * @var integer
     */
    const STARTING_SCORE = 1000;

    /**
     * What's the maximum a users's score can jump
     * between scoring events?
     *
     * @var integer
     */
    const MAX_SCORE_INCREASE = 1e4;

    /**
     * For a given user, what is the maximum
     * duration between one scoring event and the next?
     *
     * This is in units of percentage of the current
     * amount of time left in the simulation range, counting
     * from the user's last scoring event.  0.01 = 1 percent.
     */
    const MAX_TIME_BETWEEN_SCORE_EVENTS = 0.05;

    /**
     * What are the odds of any given score event
     * being a new user?  In units of "1 in X".
     *
     * @var integer
     */
    const ODDS_OF_NEW_USER = 50;

    /**
     * What are the odds of any given scoring event
     * being the time when a random user quits?
     * In units of "1 in X".
     *
     * @var integer
     */
    const ODDS_OF_QUITTING_USER = 500;

    /**
     * The start time for the simulation
     *
     * @var \DateTime
     */
    protected $start_time;

    /**
     * The stop time for the simulation
     *
     * @var \DateTime
     */
    protected $stop_time;

    /**
     * The model object which manages user scores.
     *
     * @var UserScore
     */
    protected $user_score_model;

    /**
     * A local cache array, used to store
     * the most recent score and timestamp for a given
     * facebook ID.
     *
     * @var array[]
     */
    protected $generated_fb_ids = [];

    /**
     * Store the user score model object dependency.
     *
     * @param UserScore $user_score_model The model object responsible for user scores.
     *
     * @return void
     */
    public function __construct(UserScore $user_score_model)
    {
        $this->user_score_model = $user_score_model;
    }

    /**
     * Set the start time for the simulated data.
     *
     * @param \DateTime $start [description]
     *
     * @return void
     */
    public function setStartTime(\DateTime $start)
    {
        $this->start_time = $start;
    }

    /**
     * Set the stop time for the simulated data.
     *
     * @param \DateTime $stop [description]
     *
     * @return void
     */
    public function setStopTime(\DateTime $stop)
    {
        $this->stop_time = $stop;
    }

    /**
     * Generate the given count of test user score data records.
     *
     * @param integer $score_count the count of score records to generate
     *
     * @return void
     */
    public function generate($score_count)
    {
        for ($i=0; $i<$score_count; $i++) {

            // Should we generate a new user?  Let's say about 1 in 50 scores is against a new user.
            if ($this->isNewFacebookId()) {
                $fb_id = $this->generateRandomNewFacebookId();
                $this->generated_fb_ids[$fb_id] = [
                    'latest_score'     => self::STARTING_SCORE,
                    'latest_timestamp' => $this->start_time,
                ];

            } else {
                $fb_id = array_rand($this->generated_fb_ids);

            }

            // Increment the score and timestamp values
            $this->generated_fb_ids[$fb_id]['latest_score']     = $this->increaseScore($this->generated_fb_ids[$fb_id]['latest_score']);
            $this->generated_fb_ids[$fb_id]['latest_timestamp'] = $this->increaseTimestamp($this->generated_fb_ids[$fb_id]['latest_timestamp']);

            // Write the new score record
            $this->user_score_model->writeUserAndScoreToDatabase(
                (string) $fb_id,
                [
                    'country' => 'us',
                    'locale'  => 'en_US'
                ],
                $this->generated_fb_ids[$fb_id]['latest_score'],
                $this->generated_fb_ids[$fb_id]['latest_timestamp']->format(self::DB_TIME_FORMAT)
            );

            // Players quit - for the data to look good, people need to drop out
            $this->cullPlayers();
        }
    }

    /**
     * Is is time for a new facebook ID to be generated?
     *
     * @return boolean if true, a new facebook ID will be generated for this record
     */
    protected function isNewFacebookId()
    {
        return (count($this->generated_fb_ids) === 0 || mt_rand(1, self::ODDS_OF_NEW_USER) === 1);
    }

    /**
     * Generate a 15 character random numeric string
     *
     * @throws \Exception if the generated ID is already in the list of used ids
     *
     * @return string the new 15 character Facebook ID
     */
    protected function generateRandomNewFacebookId()
    {
        $potential_characters = '0123456790';

        $return_string = '';

        // Try 10 times to find a collision-less id, before throwing an exception
        for ($j=0; $j<10; $j++) {
            for ($i=0; $i<15; $i++) {
                $character_position = rand(0, strlen($potential_characters) - 1);
                $return_string .= substr($potential_characters, $character_position, 1);
            }

            if (!array_key_exists($return_string, $this->generated_fb_ids)) {
                return $return_string;
            }
        }

        throw new \Exception('Generated Facebook ID collision.');
    }

    /**
     * Return a new score, with a random increment between 0
     * and self::MAX_SCORE_INCREASE.
     *
     * @param  integer $score The current score
     *
     * @return integer The new score
     */
    protected function increaseScore($score)
    {
        return floor($score + mt_rand(0, self::MAX_SCORE_INCREASE));
    }

    /**
     * Return a new timestamp for the new score event.
     *
     * @param  \DateTime $timestamp The timestamp of the last score event for this user.
     *
     * @return \DateTime a new timestamp for the new score event.
     */
    protected function increaseTimestamp(\DateTime $timestamp)
    {
        $current = strtotime($timestamp->format(self::DB_TIME_FORMAT));
        $stop = strtotime($this->stop_time->format(self::DB_TIME_FORMAT));

        $duration_to_end_in_seconds = $stop - $current;

        // The next timestamp is between 0 and 1% closer to the end time than the current time stamp
        $percent_increment = mt_rand(0, self::MAX_TIME_BETWEEN_SCORE_EVENTS * 100) / 100;
        $increment_seconds = floor($duration_to_end_in_seconds * $percent_increment);

        $new_timestamp = $current + $increment_seconds;

        return new \DateTime(date(self::DB_TIME_FORMAT, $new_timestamp));
    }

    /**
     * Players have a chance to drop off the list of available user IDs.
     * This method occasssionally 'retires' a random facebook ID.
     *
     * @return void
     */
    protected function cullPlayers()
    {
        // Is someone going to die this turn?
        if (count($this->generated_fb_ids) !== 0 && mt_rand(1, self::ODDS_OF_QUITTING_USER) === 1) {
            $random_user_id = array_rand($this->generated_fb_ids);
            unset($this->generated_fb_ids[$random_user_id]);
        }
    }
}
