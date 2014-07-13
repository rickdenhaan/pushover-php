<?php
namespace Capirussa\Pushover;

/**
 * Pushover Validate defines the validation request that is going to be sent to the Pushover API
 *
 * @package Capirussa\Pushover
 */
class Validate implements Request
{
    /**
     * User or group key to validate
     *
     * @type string
     */
    protected $recipient;

    /**
     * Recipient's device to validate
     *
     * @type string
     */
    protected $device;

    /**
     * Constructor -- allows initializing the request with a recipient and device
     *
     */
    public function __construct($recipient = null, $device = null)
    {
        if (function_exists('unittest_log')) unittest_log('Validate::__construct()');
        // if a recipient was given, set it
        if ($recipient !== null) {
            if (function_exists('unittest_log')) unittest_log('Setting recipient token ' . $recipient);
            $this->setRecipient($recipient);
        }

        // if a device was given, set it
        if ($device !== null) {
            if (function_exists('unittest_log')) unittest_log('Setting device identifier ' . $device);
            $this->setDevice($device);
        }
    }

    /**
     * Validates whether the recipient token is properly formatted and sets it
     *
     * @param string $recipient Recipient token to send the message to
     * @throws \InvalidArgumentException if the given recipient token is not of a valid syntax
     * @return static
     */
    public function setRecipient($recipient)
    {
        if (function_exists('unittest_log')) unittest_log('Validate::setRecipient()');
        // validate the recipient token
        if (!preg_match(Request::RECIPIENT_REGEXP, $recipient)) {
            if (function_exists('unittest_log')) unittest_log('Recipient token does not match regular expression, throwing InvalidArgumentException');
            throw new \InvalidArgumentException(
                sprintf(
                    '%1$s: Invalid recipient token "%2$s", token should be a 30-character alphanumeric string',
                    __METHOD__,
                    $recipient
                )
            );
        }

        // set the recipient token
        if (function_exists('unittest_log')) unittest_log('Recipient token appears to be OK, setting it');
        $this->recipient = $recipient;

        // return this Message for easy method chaining
        if (function_exists('unittest_log')) unittest_log('Returning');
        return $this;
    }

    /**
     * Sets the recipient device
     *
     * @param string $device Device to send the message to
     * @throws \InvalidArgumentException if the given device is invalid
     * @return static
     */
    public function setDevice($device)
    {
        if (function_exists('unittest_log')) unittest_log('Validate::setDevice()');
        // validate the device title
        if (!preg_match(Request::DEVICE_REGEXP, $device)) {
            if (function_exists('unittest_log')) unittest_log('Device identifier does not match regular expression, throwing InvalidArgumentException');
            throw new \InvalidArgumentException(
                sprintf(
                    '%1$s: Invalid device format, must contain only a-z, A-Z, 0-9, _ or - and must be 25 characters or less',
                    __METHOD__
                )
            );
        }

        // set the device
        if (function_exists('unittest_log')) unittest_log('Device identifier appears to be OK, setting it');
        $this->device = $device;

        // return this Message for easy method chaining
        if (function_exists('unittest_log')) unittest_log('Returning');
        return $this;
    }

    /**
     * Returns the array of POST data to submit to Pushover for this message
     *
     * @return array
     */
    public function getPushoverFields()
    {
        if (function_exists('unittest_log')) unittest_log('Validate::getPushoverFields()');
        $retValue = array();

        if (function_exists('unittest_log')) unittest_log('Adding recipient token to return data');
        $retValue[Request::RECIPIENT] = $this->recipient;

        if ($this->device !== null) {
            if (function_exists('unittest_log')) unittest_log('Adding device identifier to return data');
            $retValue[Request::DEVICE] = $this->device;
        }

        if (function_exists('unittest_log')) unittest_log('Returning return data');
        return $retValue;
    }
}
