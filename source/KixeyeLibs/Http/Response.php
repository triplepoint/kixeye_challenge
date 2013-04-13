<?php

namespace KixeyeLibs\Http;

/**
 * This class represents HTTP responses, and their contents.
 *
 * It can also handle serializing itself in order to generate
 * an HTTP string response.
 */
class Response
{
    /**
     * The response's body.
     *
     * @var string
     */
    protected $body;

    /**
     * The response's headers.
     *
     * This array is indexed by the header name,
     * and the values are arrays of header values, to allow
     * for multiple headers of the same name.
     *
     * @var array[]
     */
    protected $headers = [];

    /**
     * The response status code.
     *
     * @var integer
     */
    protected $status_code;

    /**
     * Set the response's HTTP status code.
     * See http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html
     *
     * @param integer $status_code the new status code
     *
     * @return void
     */
    public function setStatusCode($status_code)
    {
        $this->status_code = (integer) $status_code;
    }

    /**
     * Add a new header to the collection of response headers.
     *
     * Note that this method will not overwrite existing headers
     * of the same name, and will instead add a new header in
     * addition to the existing one. To replace all existing headers
     * of the given name, use SetHeader() instead.
     *
     * @param string $header_name  The name of the new header
     * @param string $header_value The value for the new header
     *
     * @return void
     */
    public function addHeader($header_name, $header_value)
    {
        $this->headers[$header_name][] = $header_value;
    }

    /**
     * Set the header of the given name.
     *
     * @param string $header_name  The name of the new header
     * @param string $header_value The value for the new header
     *
     * @return void
     */
    public function setHeader($header_name, $header_value)
    {
        $this->headers[$header_name] = [$header_value];
    }

    /**
     * Remove all headers of the given name.
     *
     * @param string $header_name the header to remove
     *
     * @return void
     */
    public function clearHeader($header_name)
    {
        $this->headers[$header_name] = [];
    }

    /**
     * Set the body for this response.
     *
     * @param string $body The text body for this response
     *
     * @return void
     */
    public function setBody($body)
    {
        $this->body = $body;
    }

    /**
     * Serialize this response into a HTTP response
     * message, and drop it to the current buffer.
     *
     * @return void
     */
    public function send()
    {
        foreach ($this->headers as $header_name => $header_collection) {
            foreach ($header_collection as $header_value) {
                header("{$header_name}: $header_value", false);
            }
        }

        header("Status: ".$this->lookupHttpStatusCodeHeader($this->status_code), true);

        echo $this->body;
    }

    /**
     * Find the header string that goes with a given status code.
     *
     * @param integer $status_code
     *
     * @throws \Exception if the status code is not one of the known status codes.
     *
     * @return string the HTTP header string, suitable for returning in a response
     */
    protected function lookupHttpStatusCodeHeader($status_code)
    {
        switch ($status_code) {
            case 100:
                return $status_code.' Continue';
                break;
            case 101:
                return $status_code.'  Switching Protocols';
                break;
            case 102:
                return $status_code.'  Processing';
                break;
            case 200:
                return $status_code.' OK';
                break;
            case 201:
                return $status_code.' Created';
                break;
            case 202:
                return $status_code.' Accepted';
                break;
            case 203:
                return $status_code.' Non-Authoritative Information';
                break;
            case 204:
                return $status_code.' No Content';
                break;
            case 205:
                return $status_code.' Reset Content';
                break;
            case 206:
                return $status_code.' Partial Content';
                break;
            case 207:
                return $status_code.' Multi-Status';
                break;
            case 208:
                return $status_code.' Already Reported';
                break;
            case 226:
                return $status_code.' IM Used';
                break;
            case 300:
                return $status_code.' Multiple Choices';
                break;
            case 301:
                return $status_code.' Moved Permanently';
                break;
            case 302:
                return $status_code.' Found';
                break;
            case 303:
                return $status_code.' See Other';
                break;
            case 304:
                return $status_code.' Not Modified';
                break;
            case 305:
                return $status_code.' Use Proxy';
                break;
            case 306:
                return $status_code.' Reserved';
                break;
            case 307:
                return $status_code.' Temporary Redirect';
                break;
            case 308:
                return $status_code.' Permanent Redirect';
                break;
            case 400:
                return $status_code.' Bad Request';
                break;
            case 401:
                return $status_code.' Unauthorized';
                break;
            case 402:
                return $status_code.' Payment Required';
                break;
            case 403:
                return $status_code.' Forbidden';
                break;
            case 404:
                return $status_code.' Not Found';
                break;
            case 405:
                return $status_code.' Method Not Allowed';
                break;
            case 406:
                return $status_code.' Not Acceptable';
                break;
            case 407:
                return $status_code.' Proxy Authentication Required';
                break;
            case 408:
                return $status_code.' Request Timeout';
                break;
            case 409:
                return $status_code.' Conflict';
                break;
            case 410:
                return $status_code.' Gone';
                break;
            case 411:
                return $status_code.' Length Required';
                break;
            case 412:
                return $status_code.' Precondition Failed';
                break;
            case 413:
                return $status_code.' Request Entity Too Large';
                break;
            case 414:
                return $status_code.' Request-URI Too Long';
                break;
            case 415:
                return $status_code.' Unsupported Media Type';
                break;
            case 416:
                return $status_code.' Requested Range Not Satisfiable';
                break;
            case 417:
                return $status_code.' Expectation Failed';
                break;
            case 418:
                return $status_code.' I\'m a teapot';
                break;
            case 422:
                return $status_code.' Unprocessable Entity';
                break;
            case 423:
                return $status_code.' Locked';
                break;
            case 424:
                return $status_code.' Failed Dependency';
                break;
            case 425:
                return $status_code.' Reserved for WebDAV advanced collections expired proposal';
                break;
            case 426:
                return $status_code.' Upgrade Required';
                break;
            case 428:
                return $status_code.' Precondition Required';
                break;
            case 429:
                return $status_code.' Too Many Requests';
                break;
            case 431:
                return $status_code.' Request Header Fields Too Large';
                break;
            case 500:
                return $status_code.' Internal Server Error';
                break;
            case 501:
                return $status_code.' Not Implemented';
                break;
            case 502:
                return $status_code.' Bad Gateway';
                break;
            case 503:
                return $status_code.' Service Unavailable';
                break;
            case 504:
                return $status_code.' Gateway Timeout';
                break;
            case 505:
                return $status_code.' HTTP Version Not Supported';
                break;
            case 506:
                return $status_code.' Variant Also Negotiates (Experimental)';
                break;
            case 507:
                return $status_code.' Insufficient Storage';
                break;
            case 508:
                return $status_code.' Loop Detected';
                break;
            case 510:
                return $status_code.' Not Extended';
                break;
            case 511:
                return $status_code.' Network Authentication Required';
                break;
            default:
                throw new \Exception("Unknown status code ({$status_code}).");
                break;
        }
    }
}
