<?php

namespace KixeyeLibs\Http;

/**
 * This class represents HTTP requests and their contents.
 */
class Request
{
    /**
     * The request's GET query, decoded into an array.
     *
     * @var array
     */
    protected $get_values;

    /**
     * The request's POST payload, decoded into an array.
     *
     * @var array
     */
    protected $post_values;

    /**
     * The request's cookie values.
     *
     * @var array
     */
    protected $cookies;

    /**
     * The host that this request was aimed at.
     *
     * @var string
     */
    protected $host;

    /**
     * The request method for this request.
     *
     * @var string
     */
    protected $request_method;

    /**
     * The URI path for this request.
     *
     * @var string
     */
    protected $request_path;

    /**
     * The server time at which this request began.
     *
     * @var float
     */
    protected $request_time;

    /**
     * Fill in the values for this request, given
     * the superglobal arrays typically available.
     *
     * @param array $get    Typically $_GET
     * @param array $post   Typically $_POST
     * @param array $cookie Typically $_COOKIE
     * @param array $server Typically $_SERVER
     *
     * @return void
     */
    public function setFromGlobals(array $get, array $post, array $cookie, array $server)
    {
        $this->setGetValues($get);
        $this->setPostValues($post);
        $this->setCookies($cookie);
        $this->setServerValues($server);
    }

    /**
     * Set the GET parameter values
     *
     * @param array $get_values the get values
     *
     * @return void
     */
    public function setGetValues(array $get_values)
    {
        $this->get_values = $get_values;
    }

    /**
     * Fetch the GET values
     *
     * @return array the set of get values
     */
    public function getGetVariables()
    {
        return $this->get_values;
    }

    /**
     * Set the POST parameter values
     *
     * @param array $post_values the post values
     *
     * @return void
     */
    public function setPostValues(array $post_values)
    {
        $this->post_values = $post_values;
    }

    /**
     * Fetch the POST values
     *
     * @return array the set of post values
     */
    public function getPostVariables()
    {
        return $this->post_values;
    }

    /**
     * Set the cookie values
     *
     * @param array $cookies the cookie values
     *
     * @return void
     */
    public function setCookies(array $cookies)
    {
        $this->cookies = $cookies;
    }

    /**
     * Fetch the cookie values
     *
     * @return array the set of cookie values
     */
    public function getCookies()
    {
        return $this->cookies;
    }

    /**
     * Set the useful server values
     *
     * @param array $server_values the server status values
     *
     * @return void
     */
    public function setServerValues(array $server_values)
    {
        $this->host           = $server_values['HTTP_HOST'];
        $this->request_method = $server_values['REQUEST_METHOD'];
        $this->request_path   = $server_values['PATH_INFO'];
        $this->request_time   = $server_values['REQUEST_TIME_FLOAT'];
    }

    /**
     * Fetch the request host value
     *
     * @return string the request host value
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * Fetch the request method
     *
     * @return string the request method
     */
    public function getMethod()
    {
        return $this->request_method;
    }

    /**
     * Fetch the request URI path
     *
     * @return string the request URI path
     */
    public function getPath()
    {
        return $this->request_path;
    }

    /**
     * Fetch the request start time
     *
     * @return float the request start time, in microtime
     */
    public function getTime()
    {
        return $this->request_time;
    }
}
