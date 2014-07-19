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
        // will contain the parsed API response
        $response = array(
            'headers' => array(),
            'status' => 0,
            'body' => ''
        );

        // parse the API response into usable components
        $responseLines = explode("\r\n", $apiResponse);
        foreach ($responseLines as $responseLine) {
            if (empty($responseLine)) continue;

            if (substr($responseLine, 0, 1) == '{') {
                $response['body'] = trim($responseLine);
                break;
            } elseif (strtoupper(substr($responseLine, 0, 5)) == 'HTTP/') {
                $response['status'] = substr($responseLine, 9, 3);
            } else {
                $header = explode(':', $responseLine, 2);

                // sanity check
                if (!isset($header[1])) {
                    continue;
                }

                $response['headers'][trim($header[0])] = trim($header[1]);
            }
        }

        // check whether the API rate limit information was received
        if (isset($response['headers']['X-Limit-App-Limit'])) {
            $this->appLimit = $response['headers']['X-Limit-App-Limit'];
        }
        if (isset($response['headers']['X-Limit-App-Remaining'])) {
            $this->appRemaining = $response['headers']['X-Limit-App-Remaining'];
        }
        if (isset($response['headers']['X-Limit-App-Reset'])) {
            $this->appReset = new \DateTime();
            $this->appReset->setTimestamp($response['headers']['X-Limit-App-Reset']);
        }

        // check whether the body can be parsed
        if (strlen($response['body']) > 0) {
            $body = json_decode($response['body'], true);
            if ($body !== null) {
                if (isset($body['status'])) {
                    $this->status = $body['status'] == 1 ? self::STATUS_SUCCESS : self::STATUS_FAILURE;
                }

                if (isset($body['group']) && $body['group'] != 0) {
                    $this->group = $body['group'];
                }

                if (isset($body['devices']) && is_array($body['devices'])) {
                    $this->devices = $body['devices'];
                }

                if (isset($body['request'])) {
                    $this->request = $body['request'];
                }

                if (isset($body['errors']) && is_array($body['errors'])) {
                    $this->errors = $body['errors'];
                }

                if (isset($body['acknowledged'])) {
                    $this->acknowledged = $body['acknowledged'] == 1 ? self::ACKNOWLEDGED_YES : self::ACKNOWLEDGED_NO;
                }

                if (isset($body['acknowledged_at']) && $body['acknowledged_at'] > 0) {
                    $this->acknowledgedAt = new \DateTime();
                    $this->acknowledgedAt->setTimestamp($body['acknowledged_at']);
                }

                if (isset($body['acknowledged_by']) && strlen($body['acknowledged_by']) > 0) {
                    $this->acknowledgedBy = $body['acknowledged_by'];
                }

                if (isset($body['last_delivered_at']) && $body['last_delivered_at'] > 0) {
                    $this->lastDeliveredAt = new \DateTime();
                    $this->lastDeliveredAt->setTimestamp($body['last_delivered_at']);
                }

                if (isset($body['expired'])) {
                    $this->expired = $body['expired'] == 1 ? self::EXPIRED_YES : self::EXPIRED_NO;
                }

                if (isset($body['expires_at']) && $body['expires_at'] > 0) {
                    $this->expiresAt = new \DateTime();
                    $this->expiresAt->setTimestamp($body['expires_at']);
                }

                if (isset($body['called_back'])) {
                    $this->calledBack = $body['called_back'] == 1 ? self::CALLED_BACK_YES : self::CALLED_BACK_NO;
                }

                if (isset($body['called_back_at']) && $body['called_back_at'] > 0) {
                    $this->calledBackAt = new \DateTime();
                    $this->calledBackAt->setTimestamp($body['called_back_at']);
                }

                if (isset($body['receipt']) && strlen($body['receipt']) > 0) {
                    $this->receipt = $body['receipt'];
                }
            }
        }

        // check whether Pushover is experiencing issues
        if ($response['status'] == 500) {
            throw new Exception('Pushover is experiencing temporary server issues, please try again later');
        } elseif ($response['status'] == 429) {
            if ($this->appReset !== null) {
                throw new Exception(sprintf('Your monthly rate limit has been reached. Please try again after %1$s', $this->appReset->format('r')));
            } else {
                throw new Exception('Your monthly rate limit has been reached. Please try again after the rate limit has been reset');
            }
        } elseif ($response['status'] >= 400) {
            // check whether we have any errors
            if ($this->errors !== null) {
                throw new Exception(sprintf('Pushover experienced errors processing your request: %1$s', implode(', ', $this->errors)));
            } else {
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
        return $this->status;
    }

    /**
     * Returns the group (if any)
     *
     * @return int|null
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * Returns the devices (if any)
     *
     * @return string[]|null
     */
    public function getDevices()
    {
        return $this->devices;
    }

    /**
     * Returns the request identifier
     *
     * @return string|null
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Returns the request errors (if any)
     *
     * @return string[]|null
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Returns the application's monthly rate limit
     *
     * @return int|null
     */
    public function getAppLimit()
    {
        return $this->appLimit;
    }

    /**
     * Returns the application's remaining message quota
     *
     * @return int|null
     */
    public function getAppRemaining()
    {
        return $this->appRemaining;
    }

    /**
     * Returns the message rate limit reset date
     *
     * @return \DateTime|null
     */
    public function getAppReset()
    {
        return $this->appReset;
    }

    /**
     * Returns whether or not the message for the requested receipt has been acknowledged by a user
     *
     * @return int|null
     */
    public function getAcknowledged()
    {
        return $this->acknowledged;
    }

    /**
     * Returns the date and time at which the message for the requested receipt has been acknowledged
     *
     * @return \DateTime|null
     */
    public function getAcknowledgedAt()
    {
        return $this->acknowledgedAt;
    }

    /**
     * Returns the user token for the user who acknowledged the message for the requested receipt
     *
     * @return string|null
     */
    public function getAcknowledgedBy()
    {
        return $this->acknowledgedBy;
    }

    /**
     * Returns the date and time at which the message for the requested receipt has last been sent
     *
     * @return \DateTime|null
     */
    public function getLastDeliveredAt()
    {
        return $this->lastDeliveredAt;
    }

    /**
     * Returns whether or not the message for the requested receipt has expired
     *
     * @return int|null
     */
    public function getExpired()
    {
        return $this->expired;
    }

    /**
     * Returns the date and time at which the message for the requested receipt will expire
     *
     * @return \DateTime|null
     */
    public function getExpiresAt()
    {
        return $this->expiresAt;
    }

    /**
     * Returns whether or not Pushover has sent a callback request to the callback URL submitted with the message for the requested receipt
     *
     * @return int|null
     */
    public function getCalledBack()
    {
        return $this->calledBack;
    }

    /**
     * Returns the date and time at which Pushover sent the callback request for the message for the requested receipt
     *
     * @return \DateTime|null
     */
    public function getCalledBackAt()
    {
        return $this->calledBackAt;
    }

    /**
     * Returns the receipt token which can be used for the Receipts API, in case the submitted message had Emergency priority
     *
     * @return string|null
     */
    public function getReceipt()
    {
        return $this->receipt;
    }
}