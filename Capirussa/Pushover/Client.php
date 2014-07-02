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
     * Indicates whether the Pushover SSL certificate should be verified (only disable for debug purposes!)
     *
     * @type bool
     */
    private $validateSsl = true;

    /**
     * Returns the current instance of the Client, or instantiates one of that hasn't happened yet
     *
     * @return static
     */
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new static();
        }

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
            /* @type $self Client */

            $self->setToken($appToken);
        }

        return $self;
    }

    /**
     * Disables SSL verification for the Pushover API -- Only do this if you're debugging!
     *
     * @return void
     */
    public function disableSslVerification()
    {
        $this->validateSsl = false;
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
     * Retrieves a list of device names for the given user
     *
     * @param string $userToken
     * @return string[]
     */
    public function getUserDevices($userToken)
    {
        // build a Validate request, because we'll be using the Validate API for this
        $request = new Validate($userToken);

        // execute the Validate request
        $response = $this->doRequest(Request::API_VALIDATE, $request);

        // return the list of devices for the given user
        $retValue = $response->getDevices();
        if ($retValue === null) {
            $retValue = array();
        }

        return $retValue;
    }

    /**
     * Tries to send the message to the Pushover API
     *
     * @param Message $request Message to send
     * @return string|null Returns the receipt token for emergency messages, null otherwise
     */
    public function send(Message $request)
    {
        // send the message
        $response = $this->doRequest(Request::API_MESSAGE, $request);

        // return the message receipt (if any), will return null otherwise
        return $response->getReceipt();
    }

    /**
     * Validates whether a given recipient token and optional device identifier are registered Pushover users
     *
     * @param Validate $request Validation request to send
     * @return bool True if the recipient is valid, false otherwise
     */
    public function validate(Validate $request)
    {
        // send the validation request
        $response = $this->doRequest(Request::API_VALIDATE, $request);

        // return whether the request succeeded
        return ($response->getStatus() === Response::STATUS_SUCCESS);
    }

    /**
     * Checks whether the given receipt has been acknowledged or has expired
     *
     * @param Receipt $request Receipt request to send
     * @return Response Returns the raw response, because whoever submits this request might be interested in various properties
     */
    public function pollReceipt(Receipt $request)
    {
        // send the message and return the response
        return $this->doRequest(Request::API_RECEIPT, $request);
    }

    /**
     * Performs an HTTP request to the Pushover API server
     *
     * @param string $entryPoint API entry point to send the request to
     * @param mixed  $request    Request to send
     * @throws Exception
     * @return Response
     */
    protected function doRequest($entryPoint, $request)
    {
        // build the request URL
        $url = sprintf('https://api.pushover.net/1/%1$s.json', $entryPoint);

        // set up the post fields for the request
        $postFields                 = $request->getPushoverFields();
        $postFields[Message::TOKEN] = $this->appToken;

        // set up the CURL request options
        $curlOptions = array(
            CURLOPT_SSL_VERIFYPEER => $this->validateSsl,
            CURLOPT_SSL_VERIFYHOST => $this->validateSsl ? 2 : 0,
            CURLOPT_POST           => true,
            CURLOPT_FAILONERROR    => false,
            CURLOPT_HEADER         => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POSTFIELDS     => $postFields,
            CURLOPT_USERAGENT      => 'Capirussa/1.0 (+http://github.com/rickdenhaan/pushover-php)'
        );

        // if we were asked to send a Receipt request, we need to submit a GET request instead of a POST request
        if ($request instanceof Receipt) {
            $url = sprintf('https://api.pushover.net/1/receipts/%1$s.json?token=%2$s', $request->getReceipt(), $this->appToken);

            unset($curlOptions[CURLOPT_POSTFIELDS]);
            unset($curlOptions[CURLOPT_POST]);
        }

        // initialize and configure the CURL request
        $curl = curl_init($url);
        curl_setopt_array(
            $curl,
            $curlOptions
        );

        // execute the CURL request
        $result = curl_exec($curl);

        // check whether the server threw a fit (would have nothing to do with the Pushover API, because we configured the CURL request not to throw an error if the HTTP request fails)
        $error = curl_error($curl);
        if ($error != '') {
            throw new Exception($error);
        }

        // close the CURL request
        curl_close($curl);

        // parse the response body and return the Response object
        return new Response($result);
    }
}
