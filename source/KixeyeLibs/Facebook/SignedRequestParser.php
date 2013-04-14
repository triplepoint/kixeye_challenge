<?php

namespace KixeyeLibs\Facebook;

/**
 * This parser is capable of decoding and validating
 * a signed Facebook request.
 */
class SignedRequestParser
{
    /**
     * The Facebook Application ID
     *
     * This value is optional, and only serves
     * to convey information regarding which
     * Facebook application is involved in this
     * conversation.
     *
     * @var string
     */
    protected $app_id;

    /**
     * The Facebook Application's signature secret
     *
     * @var string
     */
    protected $secret;

    /**
     * Store the application data
     *
     * @return void
     */
    public function __construct($app_id, $secret)
    {
        $this->app_id = $app_id;
        $this->secret = $secret;
    }

    /**
     * Parse the given signed message and return the data payload.
     *
     * @param  string $signed_content The signed payload from Facebook
     *
     * @throws \Exception if the message algorithm is not expected
     * @throws \Exception if the signature is not valid for this message
     *
     * @return array The decoded signed JSON payload
     */
    public function parse($signed_content)
    {
        list($encoded_signature, $payload) = explode('.', $signed_content, 2);

        $signature = $this->base64UrlDecode($encoded_signature);
        $data = json_decode($this->base64UrlDecode($payload), true);

        if (strtoupper($data['algorithm']) !== 'HMAC-SHA256') {
            throw new \Exception('Unknown algorithm ('.$data['algorithm'].'). Expected HMAC-SHA256.');
        }

        $expected_signature = $this->generateExpectedSignature($payload);

        if ($signature !== $expected_signature) {
            throw new \Exception('Payload signature does not validate.');
        }

        return $data;
    }

    /**
     * Decode the message pieces, taking into account URL encoding.
     *
     * @param  string $input The url fragment to be decoded
     *
     * @return string        The decoded fragment
     */
    protected function base64UrlDecode($input)
    {
        return base64_decode(strtr($input, '-_', '+/'));
    }

    /**
     * Generate a signature for the given payload, suitable for
     * comparing to the actual signature for verification.
     *
     * @param  string $original_payload The original payload, without the signature attached
     *
     * @return string                   The expected signature for this payload
     */
    protected function generateExpectedSignature($original_payload)
    {
        return hash_hmac('sha256', $original_payload, $this->secret, true);
    }
}
