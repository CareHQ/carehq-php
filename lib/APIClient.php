<?php

namespace CareHQ;


/**
 * A client for the CareHQ API.
 */
class APIClient
{
    //  The Id of the CareHQ account the API key relates to
    private $account_id;

    // A key used to authenticate API calls to an account
    private $api_key;

    // A secret used to generate a signature for each API request
    private $api_secret;

    // The base URL to use when calling the API
    private $api_base_url;

    // The period of time before requests to the API should timeout
    private $timeout;

    // NOTE: Rate limiting information is only available after a request has
    // been made.

    // The maximum number of requests per second that can be made with the
    // given API key.
    public $rate_limit = NULL;

    // The time (seconds since epoch) when the current rate limit will reset.
    public $rate_limit_reset = NULL;

    // The number of requests remaining within the current limit before the
    // next reset.
    public $rate_limit_remaining = NULL;

    public function __construct(
        $account_id,
        $api_key,
        $api_secret,
        $api_base_url='https://api.carehq.co.uk',
        $timeout=NULL
    )
    {
        $this->account_id = $account_id;
        $this->api_key = $api_key;
        $this->api_secret = $api_secret;
        $this->api_base_url = $api_base_url;
        $this->timeout = $timeout;
    }

    public function request(
        $method,
        $path,
        $params=NULL,
        $data=NULL
    )
    {
        if ($params) {
            $params = array_filter(
                $params,
                function ($v) { return !is_null($v); }
            );
            $params = array_map('CareHQ\ensure_string', $params);
        }

        if ($data) {
            $data = array_filter(
                $data,
                function ($v) { return !is_null($v); }
            );
            $data = array_map('CareHQ\ensure_string', $data);
        }

        // Build the signature
        $signature_data = [];
        if ($params) {
            $signature_data = $params;
        } elseif ($data) {
            $signature_data = $data;
        }

        $signature_values = [];
        foreach ($signature_data as $key => $value) {
            array_push($signature_values, $key);
            if (is_array($value)) {
                $signature_values = array_merge($signature_values, $value);
            } else {
                array_push($signature_values, $value);
            }
        }
        $signature_body = join('', $signature_values);

        $timestamp = strval(microtime(true));
        $signature = sha1($timestamp . $signature_body . $this->api_secret);

        // Build the headers
        $headers = [
            'Accept' => 'application/json',
            'X-CareHQ-AccountId' => $this->account_id,
            'X-CareHQ-APIKey' => $this->api_key,
            'X-CareHQ-Signature' => $signature,
            'X-CareHQ-Timestamp' => $timestamp
        ];

        // Make the request
        $url = $this->api_base_url . '/v1/' . $path;
        if ($params) {
            $url = $url . '?' . build_query($params);
        }

        $r = \WpOrg\Requests\Requests::request(
            $url,
            $headers,
            $data ? build_query($data) : NULL,
            $type=strtoupper($method),
            $options=['timeout'=>$this->timeout]
        );

        // Update the rate limit
        if (array_key_exists('X-CareHQ-RateLimit-Limit', $r->headers)) {
            $this->rate_limit
                = intval($r->headers['X-CareHQ-RateLimit-Limit']);
            $this->rate_limit_reset
                = floatval($r->headers['X-CareHQ-RateLimit-Reset']);
            $this->rate_limit_remaining
                = intval($r->headers['X-CareHQ-RateLimit-Remaining']);
        }

        // Handle a successful response
        if (in_array($r->status_code, [200, 204])) {
            return $r->decode_body();
        }

        // Raise an error related to the response
        try {
            $error = $r->decode_body();
        } catch (\Exception $e) {
            $error = [];
        }

        $error_cls = \CareHQ\Exception\APIException::get_class_by_status_code(
            $r->status_code
        );

        throw new $error_cls(
            $r->status_code,
            array_key_exists('hint', $error) ? $error['hint'] : NULL,
            array_key_exists('arg_errors', $error) ? $error['arg_errors'] : NULL
        );
    }
}


/**
 * Alternative to `http_build_query` that takes the approach id=1&id=2 to
 * handle array submissions.
 */
function build_query($params) {
    $query = [];
    foreach($params as $name => $value) {
        if (is_array($value)) {
            foreach ($value as $v) {
                array_push($query, urlencode($name) . '=' . urlencode($v));
            }
        } else {
            array_push($query, urlencode($name) . '=' . urlencode($value));
        }
    }
    return join('&', $query);
};


/**
 * Ensure values that will be convered to a form-encoded value is a string
 * (or list of strings).
 */
function ensure_string($v) {

    if (is_array($v)) {
        return array_map('strval', $v);
    }

    return strval($v);
};
