<?php
namespace Capirussa\Pushover;

/**
 * Pushover Message defines the message that is going to be sent to the Pushover API
 *
 * @package Capirussa\Pushover
 */
class Message
{
    /**
     * Validation regular expression to validate whether a recipient token is properly formatted
     *
     */
    const RECIPIENT_REGEXP = '/^[0-9a-z]{30}$/i';
    const DEVICE_REGEXP    = '/^[0-9a-z_]{,25}$/i';

    /**
     * Valid priorities for message
     *
     */
    const PRIORITY_INVISIBLE = -2;
    const PRIORITY_SILENT    = -1;
    const PRIORITY_NORMAL    = 0;
    const PRIORITY_HIGH      = 1;
    const PRIORITY_EMERGENCY = 2;

    /**
     * POST data field names for the Pushover API
     *
     */
    const TOKEN     = 'token';
    const RECIPIENT = 'user';
    const MESSAGE   = 'message';
    const TITLE     = 'title';
    const DEVICE    = 'device';
    const URL       = 'url';
    const URL_TITLE = 'url_title';
    const PRIORITY  = 'priority';
    const TIMESTAMP = 'timestamp';
    const SOUND     = 'sound';

    /**
     * User or group key to send the message to
     *
     * @type string
     */
    protected $recipient;

    /**
     * Message body that's going to be sent
     *
     * @type string
     */
    protected $message;

    /**
     * Message title
     *
     * @type string
     */
    protected $title;

    /**
     * Recipient's device to send the message to
     *
     * @type string
     */
    protected $device;

    /**
     * Link to send along with the message
     *
     * @type string
     */
    protected $url;

    /**
     * Link title to send if a URL is set
     *
     * @type string
     */
    protected $urlTitle;

    /**
     * Priority with which this message is to be sent
     *
     * @type int
     */
    protected $priority;

    /**
     * Timestamp to send along with the message
     *
     * @type int
     */
    protected $timestamp;

    /**
     * Sound to deliver the message with
     *
     * @type string
     */
    protected $sound;

    /**
     * Constructor -- allows initializing the message with a recipient and message body
     *
     */
    public function __construct($recipient = null, $message = null)
    {
        // if a recipient was given, set it
        if ($recipient !== null) {
            $this->setRecipient($recipient);
        }

        // if a message was given, set it
        if ($message !== null) {
            $this->setMessage($message);
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
        if (!preg_match(self::RECIPIENT_REGEXP, $recipient)) {
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
     * Sets the message body
     *
     * @param string $message Message body to send
     * @throws \InvalidArgumentException if the given message is empty
     * @return static
     */
    public function setMessage($message)
    {
        // validate the message body
        if (trim($message) == '') {
            throw new \InvalidArgumentException(
                sprintf(
                    '%1$s: Invalid message body, body must not be empty',
                    __METHOD__
                )
            );
        }

        // set the message body
        $this->message = $message;

        // return this Message for easy method chaining
        return $this;
    }

    /**
     * Sets the message title
     *
     * @param string $title Message title to send
     * @throws \InvalidArgumentException if the given title is empty
     * @return static
     */
    public function setTitle($title)
    {
        // validate the message title
        if (trim($title) == '') {
            throw new \InvalidArgumentException(
                sprintf(
                    '%1$s: Invalid message title, must not be empty',
                    __METHOD__
                )
            );
        }

        // set the message title
        $this->title = $title;

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
        if (!preg_match(self::DEVICE_REGEXP, $device)) {
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
     * Sets the message url
     *
     * @param string $url Message url to send
     * @throws \InvalidArgumentException if the given url is empty
     * @return static
     */
    public function setUrl($url)
    {
        // validate the message url
        if (trim($url) == '') {
            throw new \InvalidArgumentException(
                sprintf(
                    '%1$s: Invalid URL, must not be empty',
                    __METHOD__
                )
            );
        }

        // set the message url
        $this->url = $url;

        // return this Message for easy method chaining
        return $this;
    }

    /**
     * Sets the message url's title
     *
     * @param string $title Url title to use
     * @throws \InvalidArgumentException if the given title is empty
     * @return static
     */
    public function setUrlTitle($title)
    {
        // validate the url title
        if (trim($title) == '') {
            throw new \InvalidArgumentException(
                sprintf(
                    '%1$s: Invalid URL title, must not be empty',
                    __METHOD__
                )
            );
        }

        // set the url title
        $this->urlTitle = $title;

        // return this Message for easy method chaining
        return $this;
    }

    /**
     * Sets the message priority
     *
     * @param int $priority Priority to send the message with
     * @throws \InvalidArgumentException if the given priority is invalid
     * @return static
     */
    public function setPriority($priority)
    {
        // validate the message priority
        if (!in_array($priority, array(self::PRIORITY_INVISIBLE, self::PRIORITY_SILENT, self::PRIORITY_NORMAL, self::PRIORITY_HIGH, self::PRIORITY_EMERGENCY))) {
            throw new \InvalidArgumentException(
                sprintf(
                    '%1$s: Invalid message priority',
                    __METHOD__
                )
            );
        }

        // set the message priority
        $this->priority = $priority;

        // return this Message for easy method chaining
        return $this;
    }

    /**
     * Sets the message timestamp
     *
     * @param int $timestamp Message timestamp to send
     * @throws \InvalidArgumentException if the given timestamp is invalid
     * @return static
     */
    public function setTimestamp($timestamp)
    {
        // validate the message timestamp
        if (!is_int($timestamp)) {
            throw new \InvalidArgumentException(
                sprintf(
                    '%1$s: Invalid message timestamp, must be an integer',
                    __METHOD__
                )
            );
        }

        // set the message timestamp
        $this->timestamp = $timestamp;

        // return this Message for easy method chaining
        return $this;
    }

    /**
     * Sets the message sound
     *
     * @param string|Sound $sound Message sound to use
     * @throws \InvalidArgumentException if the given sound is empty
     * @return static
     */
    public function setSound($sound)
    {
        // validate the message title
        if (!($sound instanceof Sound) && !Sound::isValidSound($sound)) {
            throw new \InvalidArgumentException(
                sprintf(
                    '%1$s: Invalid message sound, must not be empty',
                    __METHOD__
                )
            );
        }

        // set the message sound
        $this->sound = (string)$sound;

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

        $retValue[self::RECIPIENT] = $this->recipient;
        $retValue[self::MESSAGE]   = $this->message;

        if ($this->title !== null) {
            $retValue[self::TITLE] = $this->title;
        }

        if ($this->device !== null) {
            $retValue[self::DEVICE] = $this->device;
        }

        if ($this->url !== null) {
            $retValue[self::URL] = $this->url;
        }

        if ($this->urlTitle !== null) {
            $retValue[self::URL_TITLE] = $this->urlTitle;
        }

        if ($this->priority !== null) {
            $retValue[self::PRIORITY] = $this->priority;
        }

        if ($this->timestamp !== null) {
            $retValue[self::TIMESTAMP] = $this->timestamp;
        }

        if ($this->sound !== null && $this->sound !== Sound::USER_DEFAULT) {
            $retValue[self::SOUND] = $this->sound;
        }

        return $retValue;
    }
}
