<?php
namespace Capirussa\Pushover;

/**
 * Pushover Client is the class that communicates with the Pushover API
 *
 * @package Capirussa\Pushover
 */
class Client
{
    /**
     * Validation regular expression to validate whether an application token is properly formatted
     *
     */
    const TOKEN_REGEXP = '/^[0-9a-z]{30}$/i';

    /**
     * A static instance of the Client, so we only have to initialize it once
     *
     * @type static
     */
    private static $instance;

    /**
     * The application token used to identify with
     *
     * @type string
     */
    private $appToken;

    /**
     * Returns the current instance of the Client. Does not perform lazy instantiation, because you should use
     * static::init() for that.
     *
     * @return static|null
     */
    public static function getInstance()
    {
        return self::$instance;
    }

    /**
     * Initializes the Client
     *
     * @param string $appToken The token for your application
     * @throws \InvalidArgumentException if the token is not of a valid format
     * @return static
     */
    public static function init($appToken)
    {
        // get the current Client instance
        $self = self::getInstance();

        // if we don't have on yet, or we're resetting the token, reset it
        if ($self === null || $self->getToken() != $appToken) {
            $self = new static();
            $self->setToken($appToken);
        }

        return $self;
    }

    /**
     * Returns the current application token
     *
     * @return string|null
     */
    public function getToken()
    {
        return $this->appToken;
    }

    /**
     * Validates and sets the application token
     *
     * @param string $appToken The token for the application
     * @throws \InvalidArgumentException if the token is not of a valid format
     * @return static
     */
    public function setToken($appToken)
    {
        // validate the application token
        if (!preg_match(self::TOKEN_REGEXP, $appToken)) {
            throw new \InvalidArgumentException(
                sprintf(
                    '%1$s: Invalid application token "%2$s", token should be a 30-character alphanumeric string',
                    __METHOD__,
                    $appToken
                )
            );
        }

        // set the application token
        $this->appToken = $appToken;

        // return this Client for easy method chaining
        return $this;
    }

    /**
     * Tries to send the message to the Pushover API
     *
     * @todo Comply with Pushover friendly requirements (i.e. handle rate limits and rapid messages)
     *
     * @param Message $message Message to send
     * @throws Exception if the message could not be delivered
     * @return void
     */
    public function send(Message $message)
    {
        $curl = curl_init('https://api.pushover.net/1/messages.json');
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_USERAGENT, 'Capirussa/1.0 (+http://www.capirussa.nl/)');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $postFields = $message->getPushoverFields();
        $postFields[Message::TOKEN] = $this->appToken;

        curl_setopt($curl, CURLOPT_POSTFIELDS, $postFields);

        $result = curl_exec($curl);

        curl_close($curl);
    }
}
