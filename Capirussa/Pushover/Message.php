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
     * Constructor -- allows initializing the message with a recipient and message body
     *
     */
    public function __construct($recipient = null, $message = null)
    {
        if (function_exists('unittest_log')) unittest_log('Message::__construct()');
        // if a recipient was given, set it
        if ($recipient !== null) {
            if (function_exists('unittest_log')) unittest_log('Setting recipient token to ' . $recipient);
            $this->setRecipient($recipient);
        }

        // if a message was given, set it
        if ($message !== null) {
            if (function_exists('unittest_log')) unittest_log('Setting message to ' . $message);
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
        if (function_exists('unittest_log')) unittest_log('Message::setRecipient()');
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
        if (function_exists('unittest_log')) unittest_log('Recipient token matches regular expression, setting it');
        $this->recipient = $recipient;

        // return this Message for easy method chaining
        if (function_exists('unittest_log')) unittest_log('Returning');
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
        if (function_exists('unittest_log')) unittest_log('Message::setMessage()');
        // validate the message body
        if (trim($message) == '') {
            if (function_exists('unittest_log')) unittest_log('Message is empty, throwing InvalidArgumentException');
            throw new \InvalidArgumentException(
                sprintf(
                    '%1$s: Invalid message body, body must not be empty',
                    __METHOD__
                )
            );
        }

        // make sure the message is not too long
        if ((mb_strlen($message) + mb_strlen($this->title)) > 512) {
            if (function_exists('unittest_log')) unittest_log('Message+Title combination is too long, throwing InvalidArgumentException');
            throw new \InvalidArgumentException(
                sprintf(
                    '%1$s: Message is too long, message + title cannot be more than 512 characters',
                    __METHOD__
                )
            );
        }

        // set the message body
        if (function_exists('unittest_log')) unittest_log('Message appears to be OK, setting it');
        $this->message = $message;

        // return this Message for easy method chaining
        if (function_exists('unittest_log')) unittest_log('Returning');
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
        if (function_exists('unittest_log')) unittest_log('Message::setTitle()');
        // validate the message title
        if (trim($title) == '') {
            if (function_exists('unittest_log')) unittest_log('Title is empty, throwing InvalidArgumentException');
            throw new \InvalidArgumentException(
                sprintf(
                    '%1$s: Invalid message title, must not be empty',
                    __METHOD__
                )
            );
        }

        // make sure the title is not too long
        if (mb_strlen($title) > 100) {
            if (function_exists('unittest_log')) unittest_log('Title is too long, throwing InvalidArgumentException');
            throw new \InvalidArgumentException(
                sprintf(
                    '%1$s: Title is too long, cannot be more than 100 characters',
                    __METHOD__
                )
            );
        }

        // make sure the title is not too long
        if ((mb_strlen($title) + mb_strlen($this->message)) > 512) {
            if (function_exists('unittest_log')) unittest_log('Title+Message combination is too long, throwing InvalidArgumentException');
            throw new \InvalidArgumentException(
                sprintf(
                    '%1$s: Title is too long, message + title cannot be more than 512 characters',
                    __METHOD__
                )
            );
        }

        // set the message title
        if (function_exists('unittest_log')) unittest_log('Title appears to be OK, setting it');
        $this->title = $title;

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
        if (function_exists('unittest_log')) unittest_log('Message::setDevice()');
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
        if (function_exists('unittest_log')) unittest_log('Device identifier matches regular expression, setting it');
        $this->device = $device;

        // return this Message for easy method chaining
        if (function_exists('unittest_log')) unittest_log('Returning');
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
        if (function_exists('unittest_log')) unittest_log('Message::setUrl()');
        // validate the message url
        if (trim($url) == '') {
            if (function_exists('unittest_log')) unittest_log('URL is empty, throwing InvalidArgumentException');
            throw new \InvalidArgumentException(
                sprintf(
                    '%1$s: Invalid URL, must not be empty',
                    __METHOD__
                )
            );
        }

        // make sure the url is not too long
        if (mb_strlen($url) > 512) {
            if (function_exists('unittest_log')) unittest_log('URL is too long, throwing InvalidArgumentException');
            throw new \InvalidArgumentException(
                sprintf(
                    '%1$s: URL is too long, cannot be more than 512 characters',
                    __METHOD__
                )
            );
        }

        // set the message url
        if (function_exists('unittest_log')) unittest_log('URL appears to be OK, setting it');
        $this->url = $url;

        // return this Message for easy method chaining
        if (function_exists('unittest_log')) unittest_log('Returning');
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
        if (function_exists('unittest_log')) unittest_log('Message::setUrlTitle()');
        // validate the url title
        if (trim($title) == '') {
            if (function_exists('unittest_log')) unittest_log('URL title is empty, throwing InvalidArgumentException');
            throw new \InvalidArgumentException(
                sprintf(
                    '%1$s: Invalid URL title, must not be empty',
                    __METHOD__
                )
            );
        }

        // make sure the title is not too long
        if (mb_strlen($title) > 100) {
            if (function_exists('unittest_log')) unittest_log('URL title is too long, throwing InvalidArgumentException');
            throw new \InvalidArgumentException(
                sprintf(
                    '%1$s: URL title is too long, cannot be more than 100 characters',
                    __METHOD__
                )
            );
        }

        // set the url title
        if (function_exists('unittest_log')) unittest_log('URL title appears to be OK, setting it');
        $this->urlTitle = $title;

        // return this Message for easy method chaining
        if (function_exists('unittest_log')) unittest_log('Returning');
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
        if (function_exists('unittest_log')) unittest_log('Message::setPriority()');
        // validate the message priority
        if (!in_array($priority, array(Request::PRIORITY_INVISIBLE, Request::PRIORITY_SILENT, Request::PRIORITY_NORMAL, Request::PRIORITY_HIGH, Request::PRIORITY_EMERGENCY))) {
            if (function_exists('unittest_log')) unittest_log('Priority is not in the list of accepted priorities, throwing InvalidArgumentException');
            throw new \InvalidArgumentException(
                sprintf(
                    '%1$s: Invalid message priority',
                    __METHOD__
                )
            );
        }

        // set the message priority
        if (function_exists('unittest_log')) unittest_log('Priority appears to be OK, setting it');
        $this->priority = $priority;

        // return this Message for easy method chaining
        if (function_exists('unittest_log')) unittest_log('Returning');
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
        if (function_exists('unittest_log')) unittest_log('Message::setTimestamp()');
        // validate the message timestamp
        if (!is_int($timestamp) && !($timestamp instanceof \DateTime)) {
            if (function_exists('unittest_log')) unittest_log('Timestamp is not an int or DateTime, throwing InvalidArgumentException');
            throw new \InvalidArgumentException(
                sprintf(
                    '%1$s: Invalid message timestamp, must be an integer or DateTime object',
                    __METHOD__
                )
            );
        }

        // if the timestamp is a DateTime object, retrieve the timestamp from it
        if ($timestamp instanceof \DateTime) {
            if (function_exists('unittest_log')) unittest_log('Timestamp is a DateTime, getting timestamp');
            $timestamp = $timestamp->getTimestamp();
        }

        // set the message timestamp
        if (function_exists('unittest_log')) unittest_log('Setting timestamp');
        $this->timestamp = $timestamp;

        // return this Message for easy method chaining
        if (function_exists('unittest_log')) unittest_log('Returning');
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
        if (function_exists('unittest_log')) unittest_log('Message::setSound()');
        // validate the message title
        if (!($sound instanceof Sound) && !Sound::isValidSound($sound)) {
            if (function_exists('unittest_log')) unittest_log('Sound is invalid, throwing InvalidArgumentException');
            throw new \InvalidArgumentException(
                sprintf(
                    '%1$s: Invalid message sound, must not be empty',
                    __METHOD__
                )
            );
        }

        // set the message sound
        if (function_exists('unittest_log')) unittest_log('Sound appears to be OK, setting it');
        $this->sound = (string)$sound;

        // return this Message for easy method chaining
        if (function_exists('unittest_log')) unittest_log('Returning');
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
        if (function_exists('unittest_log')) unittest_log('Message::setCallbackUrl()');
        // validate the callback url
        if (trim($url) == '') {
            if (function_exists('unittest_log')) unittest_log('Callback URL is empty, throwing InvalidArgumentException');
            throw new \InvalidArgumentException(
                sprintf(
                    '%1$s: Invalid URL, must not be empty',
                    __METHOD__
                )
            );
        }

        // set the callback url
        if (function_exists('unittest_log')) unittest_log('Callback URL appears to be OK, setting it');
        $this->callback = $url;

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
        if (function_exists('unittest_log')) unittest_log('Message::getPushoverFields()');
        $retValue = array();

        if (function_exists('unittest_log')) unittest_log('Adding recipient token and message to return data');
        $retValue[Request::RECIPIENT] = $this->recipient;
        $retValue[Request::MESSAGE]   = $this->message;

        if ($this->title !== null) {
            if (function_exists('unittest_log')) unittest_log('Adding title to return data');
            $retValue[Request::TITLE] = $this->title;
        }

        if ($this->device !== null) {
            if (function_exists('unittest_log')) unittest_log('Adding device identifier to return data');
            $retValue[Request::DEVICE] = $this->device;
        }

        if ($this->url !== null) {
            if (function_exists('unittest_log')) unittest_log('Adding URL to return data');
            $retValue[Request::URL] = $this->url;

            if ($this->urlTitle !== null) {
                if (function_exists('unittest_log')) unittest_log('Adding URL title to return data');
                $retValue[Request::URL_TITLE] = $this->urlTitle;
            }
        }

        if ($this->priority !== null) {
            if (function_exists('unittest_log')) unittest_log('Adding priority to return data');
            $retValue[Request::PRIORITY] = $this->priority;

            if ($this->priority == Request::PRIORITY_EMERGENCY && $this->callback !== null) {
                if (function_exists('unittest_log')) unittest_log('Adding callback URL to return data');
                $retValue[Request::CALLBACK] = $this->callback;
            }
        }

        if ($this->timestamp !== null) {
            if (function_exists('unittest_log')) unittest_log('Adding timestamp to return data');
            $retValue[Request::TIMESTAMP] = $this->timestamp;
        }

        if ($this->sound !== null && $this->sound !== Sound::USER_DEFAULT) {
            if (function_exists('unittest_log')) unittest_log('Adding sound to return data');
            $retValue[Request::SOUND] = $this->sound;
        }

        if (function_exists('unittest_log')) unittest_log('Returning');
        return $retValue;
    }
}
