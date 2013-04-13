<?php

namespace KixeyeLibs\Http;

class Request
{
    protected $get_values;

    protected $post_values;

    protected $cookies;

    protected $host;

    protected $request_method;

    protected $request_path;

    protected $request_time;

    public function setFromGlobals(array $get, array $post, array $cookie, array $server)
    {
        $this->setGetValues($get);
        $this->setPostValues($post);
        $this->setCookies($cookie);
        $this->setServerValues($server);
    }

    public function setGetValues(array $get_values)
    {
        $this->get_values = $get_values;
    }

    public function getGetVariables()
    {
        return $this->get_values;
    }

    public function setPostValues(array $post_values)
    {
        $this->post_values = $post_values;
    }

    public function getPostVariables()
    {
        return $this->post_values;
    }

    public function setCookies(array $cookies)
    {
        $this->cookies = $cookies;
    }

    public function getCookies()
    {
        return $this->cookies;
    }

    public function setServerValues(array $server_values)
    {
        $this->host           = $server_values['HTTP_HOST'];
        $this->request_method = $server_values['REQUEST_METHOD'];
        $this->request_path   = $server_values['PATH_INFO'];
        $this->request_time   = $server_values['REQUEST_TIME_FLOAT'];
    }

    public function getHost()
    {
        return $this->host;
    }

    public function getMethod()
    {
        return $this->request_method;
    }

    public function getPath()
    {
        return $this->request_path;
    }

    public function getTime()
    {
        return $this->request_time;
    }
}
