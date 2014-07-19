<?php
namespace Capirussa\Pushover;

/**
 * Pushover Message defines the message that is going to be sent to the Pushover API
 *
 * @package Capirussa\Pushover
 */
class Message implements Request
{
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
     * Callback URL (only applicable to Emergency messages)
     *
     * @type string
     */
    protected $callback;

    /**
     * Number of seconds until the message expires (only applicable to Emergency messages)
     *
     * @type int
     */
    protected $expire;

    /**
     * Number of seconds between message deliveries (only applicable to Emergency messages)
     *
     * @type int
     */
    protected $retry;

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

        // make sure the message is not too long
        if ((mb_strlen($message) + mb_strlen($this->title)) > 512) {
            throw new \InvalidArgumentException(
                sprintf(
                    '%1$s: Message is too long, message + title cannot be more than 512 characters',
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

        // make sure the title is not too long
        if (mb_strlen($title) > 100) {
            throw new \InvalidArgumentException(
                sprintf(
                    '%1$s: Title is too long, cannot be more than 100 characters',
                    __METHOD__
                )
            );
        }

        // make sure the title is not too long
        if ((mb_strlen($title) + mb_strlen($this->message)) > 512) {
            throw new \InvalidArgumentException(
                sprintf(
                    '%1$s: Title is too long, message + title cannot be more than 512 characters',
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

        // make sure the url is not too long
        if (mb_strlen($url) > 512) {
            throw new \InvalidArgumentException(
                sprintf(
                    '%1$s: URL is too long, cannot be more than 512 characters',
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

        // make sure the title is not too long
        if (mb_strlen($title) > 100) {
            throw new \InvalidArgumentException(
                sprintf(
                    '%1$s: URL title is too long, cannot be more than 100 characters',
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
        if (!in_array($priority, array(Request::PRIORITY_INVISIBLE, Request::PRIORITY_SILENT, Request::PRIORITY_NORMAL, Request::PRIORITY_HIGH, Request::PRIORITY_EMERGENCY), true)) {
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
     * @param int|\DateTime $timestamp Message timestamp to send
     * @throws \InvalidArgumentException if the given timestamp is invalid
     * @return static
     */
    public function setTimestamp($timestamp)
    {
        // validate the message timestamp
        if (!is_int($timestamp) && !($timestamp instanceof \DateTime)) {
            throw new \InvalidArgumentException(
                sprintf(
                    '%1$s: Invalid message timestamp, must be an integer or DateTime object',
                    __METHOD__
                )
            );
        }

        // if the timestamp is a DateTime object, retrieve the timestamp from it
        if ($timestamp instanceof \DateTime) {
            $timestamp = $timestamp->getTimestamp();
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
     * Sets the callback url
     *
     * @param string $url Callback url to send
     * @throws \InvalidArgumentException if the given url is empty
     * @return static
     */
    public function setCallbackUrl($url)
    {
        // validate the callback url
        if (trim($url) == '') {
            throw new \InvalidArgumentException(
                sprintf(
                    '%1$s: Invalid URL, must not be empty',
                    __METHOD__
                )
            );
        }

        // set the callback url
        $this->callback = $url;

        // return this Message for easy method chaining
        return $this;
    }

    /**
     * Sets the number of seconds until the message expires (only for Emergency messages)
     *
     * @param int $expire Number of seconds
     * @throws \InvalidArgumentException if the given value is invalid
     * @return static
     */
    public function setExpire($expire)
    {
        // validate the expire time
        $expire = intval($expire, 10);

        if ($expire <= 0 || $expire > 86400) {
            throw new \InvalidArgumentException(
                sprintf(
                    '%1$s: Invalid expire delay, must be between 1 and 86400 seconds',
                    __METHOD__
                )
            );
        }

        // set the expire time
        $this->expire = $expire;

        // return this Message for easy method chaining
        return $this;
    }

    /**
     * Sets the delay between message deliveries for Emergency messages
     *
     * @param int $retry Number of seconds between deliveries
     * @throws \InvalidArgumentException if the given value is invalid
     * @return static
     */
    public function setRetry($retry)
    {
        // validate the retry delay
        $retry = intval($retry, 10);

        if ($retry < 30 || $retry > 86400) {
            throw new \InvalidArgumentException(
                sprintf(
                    '%1$s: Invalid retry delay, must be between 30 and 86400 seconds',
                    __METHOD__
                )
            );
        }

        // set the retry delay
        $this->retry = $retry;

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
        $retValue[Request::MESSAGE]   = $this->message;

        if ($this->title !== null) {
            $retValue[Request::TITLE] = $this->title;
        }

        if ($this->device !== null) {
            $retValue[Request::DEVICE] = $this->device;
        }

        if ($this->url !== null) {
            $retValue[Request::URL] = $this->url;

            if ($this->urlTitle !== null) {
                $retValue[Request::URL_TITLE] = $this->urlTitle;
            }
        }

        if ($this->priority !== null) {
            $retValue[Request::PRIORITY] = $this->priority;

            if ($this->priority == Request::PRIORITY_EMERGENCY) {
                $retValue[Request::EXPIRE] = $this->expire === null ? 3600 : $this->expire;
                $retValue[Request::RETRY]  = $this->retry === null ? 30 : $this->retry;

                if ($this->callback !== null) {
                    $retValue[Request::CALLBACK] = $this->callback;
                }
            }
        }

        if ($this->timestamp !== null) {
            $retValue[Request::TIMESTAMP] = $this->timestamp;
        }

        if ($this->sound !== null && $this->sound !== Sound::USER_DEFAULT) {
            $retValue[Request::SOUND] = $this->sound;
        }

        return $retValue;
    }
}
