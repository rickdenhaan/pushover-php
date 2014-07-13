<?php
namespace Capirussa\Pushover;

/**
 * Response object represents a Pushover API response
 *
 * @package Capirussa\Pushover
 */
class Response
{
    /**
     * Valid status codes
     *
     */
    const STATUS_SUCCESS = 1;
    const STATUS_FAILURE = 0;

    /**
     * Valid acknowledged states
     *
     */
    const ACKNOWLEDGED_YES = 1;
    const ACKNOWLEDGED_NO  = 0;

    /**
     * Valid expired states
     *
     */
    const EXPIRED_YES = 1;
    const EXPIRED_NO  = 0;

    /**
     * Valid callback states
     *
     */
    const CALLED_BACK_YES = 1;
    const CALLED_BACK_NO  = 0;

    /**
     * The status code returned by the API
     *
     * @type int
     */
    protected $status;

    /**
     * Group (if any)
     *
     * @type int
     */
    protected $group;

    /**
     * List of devices for the user/group
     *
     * @type string[]
     */
    protected $devices;

    /**
     * Unique request identifier
     *
     * @type string
     */
    protected $request;

    /**
     * List of errors (if any)
     *
     * @type string[]
     */
    protected $errors;

    /**
     * The number of messages allowed for this application per month
     *
     * @type int
     */
    protected $appLimit;

    /**
     * The number of messages remaining for the application's monthly limit
     *
     * @type int
     */
    protected $appRemaining;

    /**
     * The date and time on which the application's message limit will be reset
     *
     * @type \DateTime
     */
    protected $appReset;

    /**
     * Whether or not the message for the requested receipt has been acknowledged by a user
     *
     * @type int
     */
    protected $acknowledged;

    /**
     * Date and time at which the message for the requested receipt has been acknowledged
     *
     * @type \DateTime
     */
    protected $acknowledgedAt;

    /**
     * User token for the user who acknowledged the message for the requested receipt
     *
     * @type string
     */
    protected $acknowledgedBy;

    /**
     * Date and time at which the message for the requested receipt has last been sent
     *
     * @type \DateTime
     */
    protected $lastDeliveredAt;

    /**
     * Whether or not the message for the requested receipt has expired
     *
     * @type int
     */
    protected $expired;

    /**
     * Date and time at which the message for the requested receipt will expire
     *
     * @type \DateTime
     */
    protected $expiresAt;

    /**
     * Whether or not Pushover has sent a callback request to the callback URL submitted with the message for the requested receipt
     *
     * @type int
     */
    protected $calledBack;

    /**
     * Date and time at which Pushover sent the callback request for the message for the requested receipt
     *
     * @type \DateTime
     */
    protected $calledBackAt;

    /**
     * Receipt token which can be used for the Receipts API, in case the submitted message had Emergency priority
     *
     * @type string
     */
    protected $receipt;

    /**
     * Builds the Response object from a raw API response string
     *
     * @param string $apiResponse
     * @throws Exception
     */
    public function __construct($apiResponse)
    {
        if (function_exists('unittest_log')) unittest_log('Response::__construct()');
        // will contain the parsed API response
        $response = array(
            'headers' => array(),
            'status' => 0,
            'body' => ''
        );

        // parse the API response into usable components
        $responseLines = explode("\r\n", $apiResponse);
        if (function_exists('unittest_log')) unittest_log('Split response data by newlines, output contains ' . count($responseLines) . ' lines');
        foreach ($responseLines as $responseLine) {
            if (empty($responseLine)) continue;

            if (substr($responseLine, 0, 1) == '{') {
                if (function_exists('unittest_log')) unittest_log('Setting response JSON body');
                $response['body'] = $responseLine;
                break;
            } elseif (strtoupper(substr($responseLine, 0, 5)) == 'HTTP/') {
                if (function_exists('unittest_log')) unittest_log('Setting response status code to ' . substr($responseLine, 9, 3));
                $response['status'] = substr($responseLine, 9, 3);
            } else {
                $header = explode(':', $responseLine, 2);
                if (function_exists('unittest_log')) unittest_log('Setting response header ' . trim($header[0]) . ' to ' . trim($header[1]));
                $response['headers'][trim($header[0])] = trim($header[1]);
            }
        }

        // check whether the API rate limit information was received
        if (isset($response['headers']['X-Limit-App-Limit'])) {
            if (function_exists('unittest_log')) unittest_log('Headers contain rate limit, setting it');
            $this->appLimit = $response['headers']['X-Limit-App-Limit'];
        }
        if (isset($response['headers']['X-Limit-App-Remaining'])) {
            if (function_exists('unittest_log')) unittest_log('Headers contain remaining rate limit, setting it');
            $this->appRemaining = $response['headers']['X-Limit-App-Remaining'];
        }
        if (isset($response['headers']['X-Limit-App-Reset'])) {
            if (function_exists('unittest_log')) unittest_log('Headers contain next rate limit reset date, setting it');
            $this->appReset = new \DateTime();
            $this->appReset->setTimestamp($response['headers']['X-Limit-App-Reset']);
        }

        // check whether the body can be parsed
        if (strlen($response['body']) > 0) {
            if (function_exists('unittest_log')) unittest_log('Found a JSON body, decoding it');
            $body = json_decode($response['body'], true);
            if ($body !== null) {
                if (function_exists('unittest_log')) unittest_log('Successfully decoded, parsing it');
                if (isset($body['status'])) {
                    if (function_exists('unittest_log')) unittest_log('Body contains a status, setting it');
                    $this->status = $body['status'] == 1 ? self::STATUS_SUCCESS : self::STATUS_FAILURE;
                }

                if (isset($body['group']) && $body['group'] != 0) {
                    if (function_exists('unittest_log')) unittest_log('Body contains a group, setting it');
                    $this->group = $body['group'];
                }

                if (isset($body['devices']) && is_array($body['devices'])) {
                    if (function_exists('unittest_log')) unittest_log('Body contains devices, setting them');
                    $this->devices = $body['devices'];
                }

                if (isset($body['request'])) {
                    if (function_exists('unittest_log')) unittest_log('Body contains a request token, setting it');
                    $this->request = $body['request'];
                }

                if (isset($body['errors']) && is_array($body['errors'])) {
                    if (function_exists('unittest_log')) unittest_log('Body contains errors, setting them');
                    $this->errors = $body['errors'];
                }

                if (isset($body['acknowledged'])) {
                    if (function_exists('unittest_log')) unittest_log('Body contains acknowledged state, setting it');
                    $this->acknowledged = $body['acknowledged'] == 1 ? self::ACKNOWLEDGED_YES : self::ACKNOWLEDGED_NO;
                }

                if (isset($body['acknowledged_at']) && $body['acknowledged_at'] > 0) {
                    if (function_exists('unittest_log')) unittest_log('Body contains acknowledged date/time, setting it');
                    $this->acknowledgedAt = new \DateTime();
                    $this->acknowledgedAt->setTimestamp($body['acknowledged_at']);
                }

                if (isset($body['acknowledged_by']) && strlen($body['acknowledged_by']) > 0) {
                    if (function_exists('unittest_log')) unittest_log('Body contains acknowledged user, setting it');
                    $this->acknowledgedBy = $body['acknowledged_by'];
                }

                if (isset($body['last_delivered_at']) && $body['last_delivered_at'] > 0) {
                    if (function_exists('unittest_log')) unittest_log('Body contains last delivery date/time, setting it');
                    $this->lastDeliveredAt = new \DateTime();
                    $this->lastDeliveredAt->setTimestamp($body['last_delivered_at']);
                }

                if (isset($body['expired'])) {
                    if (function_exists('unittest_log')) unittest_log('Body contains expiration state, setting it');
                    $this->expired = $body['expired'] == 1 ? self::EXPIRED_YES : self::EXPIRED_NO;
                }

                if (isset($body['expires_at']) && $body['expires_at'] > 0) {
                    if (function_exists('unittest_log')) unittest_log('Body contains expiration date/time, setting it');
                    $this->expiresAt = new \DateTime();
                    $this->expiresAt->setTimestamp($body['expires_at']);
                }

                if (isset($body['called_back'])) {
                    if (function_exists('unittest_log')) unittest_log('Body contains callback state, setting it');
                    $this->calledBack = $body['called_back'] == 1 ? self::CALLED_BACK_YES : self::CALLED_BACK_NO;
                }

                if (isset($body['called_back_at']) && $body['called_back_at'] > 0) {
                    if (function_exists('unittest_log')) unittest_log('Body contains callback date/time, setting it');
                    $this->calledBackAt = new \DateTime();
                    $this->calledBackAt->setTimestamp($body['called_back_at']);
                }

                if (isset($body['receipt']) && strlen($body['receipt']) > 0) {
                    if (function_exists('unittest_log')) unittest_log('Body contains receipt token, setting it');
                    $this->receipt = $body['receipt'];
                }
            }
        }

        // check whether Pushover is experiencing issues
        if ($response['status'] == 500) {
            if (function_exists('unittest_log')) unittest_log('Response status is 500, throwing Exception');
            throw new Exception('Pushover is experiencing temporary server issues, please try again later');
        } elseif ($response['status'] == 429) {
            if ($this->appReset !== null) {
                if (function_exists('unittest_log')) unittest_log('Response status is 429 (rate limit exceeded) and reset date/time is given, throwing Exception');
                throw new Exception(sprintf('Your monthly rate limit has been reached. Please try again after %1$s', $this->appReset->format('r')));
            } else {
                if (function_exists('unittest_log')) unittest_log('Response status is 429 (rate limit exceeded) and reset date/time is unknown, throwing Exception');
                throw new Exception('Your monthly rate limit has been reached. Please try again after the rate limit has been reset');
            }
        } elseif ($response['status'] >= 400) {
            // check whether we have any errors
            if ($this->errors !== null) {
                if (function_exists('unittest_log')) unittest_log('Response status is a 4xx error and errors are given, throwing Exception');
                throw new Exception(sprintf('Pushover experienced errors processing your request: %1$s', implode(', ', $this->errors)));
            } else {
                if (function_exists('unittest_log')) unittest_log('Response status is a 4xx error but no errors are given, throwing Exception');
                throw new Exception('Pushover experienced an unknown error processing your request');
            }
        }
    }

    /**
     * Returns the response status
     *
     * @return int|null
     */
    public function getStatus()
    {
        if (function_exists('unittest_log')) unittest_log('Response::getStatus()');
        if (function_exists('unittest_log')) unittest_log('Returning status ' . $this->status);
        return $this->status;
    }

    /**
     * Returns the group (if any)
     *
     * @return int|null
     */
    public function getGroup()
    {
        if (function_exists('unittest_log')) unittest_log('Response::getGroup()');
        if (function_exists('unittest_log')) unittest_log('Returning group ' . $this->group);
        return $this->group;
    }

    /**
     * Returns the devices (if any)
     *
     * @return string[]|null
     */
    public function getDevices()
    {
        if (function_exists('unittest_log')) unittest_log('Response::getDevices()');
        if (function_exists('unittest_log')) unittest_log('Returning devices ' . implode(',', $this->devices));
        return $this->devices;
    }

    /**
     * Returns the request identifier
     *
     * @return string|null
     */
    public function getRequest()
    {
        if (function_exists('unittest_log')) unittest_log('Response::getRequest()');
        if (function_exists('unittest_log')) unittest_log('Returning request token ' . $this->request);
        return $this->request;
    }

    /**
     * Returns the request errors (if any)
     *
     * @return string[]|null
     */
    public function getErrors()
    {
        if (function_exists('unittest_log')) unittest_log('Response::getErrors()');
        if (function_exists('unittest_log')) unittest_log('Returning errors ' . implode(',' , $this->errors));
        return $this->errors;
    }

    /**
     * Returns the application's monthly rate limit
     *
     * @return int|null
     */
    public function getAppLimit()
    {
        if (function_exists('unittest_log')) unittest_log('Response::getAppLimit()');
        if (function_exists('unittest_log')) unittest_log('Returning rate limit ' . $this->appLimit);
        return $this->appLimit;
    }

    /**
     * Returns the application's remaining message quota
     *
     * @return int|null
     */
    public function getAppRemaining()
    {
        if (function_exists('unittest_log')) unittest_log('Response::getAppRemaining()');
        if (function_exists('unittest_log')) unittest_log('Returning remaining rate limit ' . $this->appRemaining);
        return $this->appRemaining;
    }

    /**
     * Returns the message rate limit reset date
     *
     * @return \DateTime|null
     */
    public function getAppReset()
    {
        if (function_exists('unittest_log')) unittest_log('Response::getAppReset()');
        if (function_exists('unittest_log')) unittest_log('Returning rate limit reset date ' . (string)$this->appReset);
        return $this->appReset;
    }

    /**
     * Returns whether or not the message for the requested receipt has been acknowledged by a user
     *
     * @return int|null
     */
    public function getAcknowledged()
    {
        if (function_exists('unittest_log')) unittest_log('Response::getAcknowledged()');
        if (function_exists('unittest_log')) unittest_log('Returning acknowledged state ' . $this->acknowledged);
        return $this->acknowledged;
    }

    /**
     * Returns the date and time at which the message for the requested receipt has been acknowledged
     *
     * @return \DateTime|null
     */
    public function getAcknowledgedAt()
    {
        if (function_exists('unittest_log')) unittest_log('Response::getAcknowledgedAt()');
        if (function_exists('unittest_log')) unittest_log('Returning acknowledged date ' . (string)$this->acknowledgedAt);
        return $this->acknowledgedAt;
    }

    /**
     * Returns the user token for the user who acknowledged the message for the requested receipt
     *
     * @return string|null
     */
    public function getAcknowledgedBy()
    {
        if (function_exists('unittest_log')) unittest_log('Response::getAcknowledgedBy()');
        if (function_exists('unittest_log')) unittest_log('Returning acknowledged by user ' . $this->acknowledgedBy);
        return $this->acknowledgedBy;
    }

    /**
     * Returns the date and time at which the message for the requested receipt has last been sent
     *
     * @return \DateTime|null
     */
    public function getLastDeliveredAt()
    {
        if (function_exists('unittest_log')) unittest_log('Response::getLastDeliveredAt()');
        if (function_exists('unittest_log')) unittest_log('Returning last delivery date ' . (string)$this->lastDeliveredAt);
        return $this->lastDeliveredAt;
    }

    /**
     * Returns whether or not the message for the requested receipt has expired
     *
     * @return int|null
     */
    public function getExpired()
    {
        if (function_exists('unittest_log')) unittest_log('Response::getExpired()');
        if (function_exists('unittest_log')) unittest_log('Returning expired state ' . $this->expired);
        return $this->expired;
    }

    /**
     * Returns the date and time at which the message for the requested receipt will expire
     *
     * @return \DateTime|null
     */
    public function getExpiresAt()
    {
        if (function_exists('unittest_log')) unittest_log('Response::getExpiresAt()');
        if (function_exists('unittest_log')) unittest_log('Returning expiration date ' . (string)$this->expiresAt);
        return $this->expiresAt;
    }

    /**
     * Returns whether or not Pushover has sent a callback request to the callback URL submitted with the message for the requested receipt
     *
     * @return int|null
     */
    public function getCalledBack()
    {
        if (function_exists('unittest_log')) unittest_log('Response::getCalledBack()');
        if (function_exists('unittest_log')) unittest_log('Returning callback state ' . $this->calledBack);
        return $this->calledBack;
    }

    /**
     * Returns the date and time at which Pushover sent the callback request for the message for the requested receipt
     *
     * @return \DateTime|null
     */
    public function getCalledBackAt()
    {
        if (function_exists('unittest_log')) unittest_log('Response::getCalledBackAt()');
        if (function_exists('unittest_log')) unittest_log('Returning callback date ' . (string)$this->calledBackAt);
        return $this->calledBackAt;
    }

    /**
     * Returns the receipt token which can be used for the Receipts API, in case the submitted message had Emergency priority
     *
     * @return string|null
     */
    public function getReceipt()
    {
        if (function_exists('unittest_log')) unittest_log('Response::getReceipt()');
        if (function_exists('unittest_log')) unittest_log('Returning receipt token ' . $this->receipt);
        return $this->receipt;
    }
}