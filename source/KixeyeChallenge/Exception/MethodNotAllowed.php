<?php

namespace KixeyeChallenge\Exception;

/**
 * This exception is thrown when a request is made
 * with an HTTP method that is not allowed.  This
 * exception can optionally report the methods that
 * would have been allowed.
 *
 * This information is useful for sending 405 status
 * codes.
 */
class MethodNotAllowed extends \Exception
{
    /**
     * The methods that would have been allowed instead
     * of the method that was given.
     *
     * @var string[]
     */
    protected $allowed_methods = [];

    /**
     * Get the methods that would be allowed
     *
     * @return string[] The collection of allowed methods
     */
    public function getAllowedMethods()
    {
        return $this->allowed_methods;
    }

    /**
     * Get the methods that would be allowed
     *
     * @param string[] The new collection of allowed methods
     *
     * @return void
     */
    public function setAllowedMethods(array $allowed_methods)
    {
        $this->allowed_methods = $allowed_methods;
    }
}
