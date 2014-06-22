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
        // if a recipient was given, set it
        if ($recipient !== null) {
            $this->setRecipient($recipient);
        }

        // if a device was given, set it
        if ($device !== null) {
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
        // validate the recipient token
        if (!preg_match(Request::RECIPIENT_REGEXP, $recipient)) {
            throw new \InvalidArgumentException(
                sprintf(
                    '%1$s: Invalid recipient token "%2$s", token should be a 30-character alphanumeric string',
                    __METHOD__,
                    $recipient
                )
            );
        }

        // set the recipient token
        $this->recipient = $recipient;

        // return this Message for easy method chaining
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
        // validate the device title
        if (!preg_match(Request::DEVICE_REGEXP, $device)) {
            throw new \InvalidArgumentException(
                sprintf(
                    '%1$s: Invalid device format, must contain only a-z, A-Z, 0-9, _ or - and must be 25 characters or less',
                    __METHOD__
                )
            );
        }

        // set the device
        $this->device = $device;

        // return this Message for easy method chaining
        return $this;
    }

    /**
     * Returns the array of POST data to submit to Pushover for this message
     *
     * @return array
     */
    public function getPushoverFields()
    {
        $retValue = array();

        $retValue[Request::RECIPIENT] = $this->recipient;

        if ($this->device !== null) {
            $retValue[Request::DEVICE] = $this->device;
        }

        return $retValue;
    }
}
