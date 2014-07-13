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
        if (function_exists('unittest_log')) unittest_log('Client::getInstance()');
        if (self::$instance === null) {
            if (function_exists('unittest_log')) unittest_log('$instance == null, instantiating');
            self::$instance = new static();
        }

        if (function_exists('unittest_log')) unittest_log('Returning $instance');
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
        if (function_exists('unittest_log')) unittest_log('Client::init()');
        // get the current Client instance
        if (function_exists('unittest_log')) unittest_log('Getting instance');
        $self = self::getInstance();

        // if we don't have one yet, or we're resetting the token, reset it
        if ($self === null || $self->getToken() != $appToken) {
            if (function_exists('unittest_log')) unittest_log('$instance == null || $instance->token != $appToken');
            if (function_exists('unittest_log')) unittest_log('Creating new instance');
            $self = new static();
            /* @type $self Client */

            if (function_exists('unittest_log')) unittest_log('Setting token ' . $appToken);
            $self->setToken($appToken);
        }

        if (function_exists('unittest_log')) unittest_log('Returning instance');
        return $self;
    }

    /**
     * Disables SSL verification for the Pushover API -- Only do this if you're debugging!
     *
     * @return void
     */
    public function disableSslVerification()
    {
        if (function_exists('unittest_log')) unittest_log('Client::disableSslVerification()');
        if (function_exists('unittest_log')) unittest_log('Disabling SSL verification');
        $this->validateSsl = false;
    }

    /**
     * Returns the current application token
     *
     * @return string|null
     */
    public function getToken()
    {
        if (function_exists('unittest_log')) unittest_log('Client::getToken()');
        if (function_exists('unittest_log')) unittest_log('Returning current client token ' . $this->appToken);
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
        if (function_exists('unittest_log')) unittest_log('Client::setToken()');
        // validate the application token
        if (function_exists('unittest_log')) unittest_log('Validating token ' . $appToken);
        if (!preg_match(self::TOKEN_REGEXP, $appToken)) {
            if (function_exists('unittest_log')) unittest_log('Token does not comply with regular expression, throwing InvalidArgumentException');
            throw new \InvalidArgumentException(
                sprintf(
                    '%1$s: Invalid application token "%2$s", token should be a 30-character alphanumeric string',
                    __METHOD__,
                    $appToken
                )
            );
        }

        // set the application token
        if (function_exists('unittest_log')) unittest_log('Token complies wiht regular expression, setting it');
        $this->appToken = $appToken;

        // return this Client for easy method chaining
        if (function_exists('unittest_log')) unittest_log('Returning');
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
        if (function_exists('unittest_log')) unittest_log('Client::getUserDevices()');
        // build a Validate request, because we'll be using the Validate API for this
        if (function_exists('unittest_log')) unittest_log('Creating a Validate request object');
        $request = new Validate($userToken);

        // execute the Validate request
        if (function_exists('unittest_log')) unittest_log('Executing the validate request');
        $response = $this->doRequest(Request::API_VALIDATE, $request);

        // return the list of devices for the given user
        if (function_exists('unittest_log')) unittest_log('Retrieving list of devices from response');
        $retValue = $response->getDevices();
        if ($retValue === null) {
            $retValue = array();
        }

        if (function_exists('unittest_log')) unittest_log('Returning list of devices');
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
        if (function_exists('unittest_log')) unittest_log('Client::send()');
        // send the message
        if (function_exists('unittest_log')) unittest_log('Executing the send request');
        $response = $this->doRequest(Request::API_MESSAGE, $request);

        // return the message receipt (if any), will return null otherwise
        if (function_exists('unittest_log')) unittest_log('Returning the message receipt (if any)');
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
        if (function_exists('unittest_log')) unittest_log('Client::validate()');
        // send the validation request
        if (function_exists('unittest_log')) unittest_log('Executing the validate request');
        $response = $this->doRequest(Request::API_VALIDATE, $request);

        // return whether the request succeeded
        if (function_exists('unittest_log')) unittest_log('Returning whether the response indicates a success status or not');
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
        if (function_exists('unittest_log')) unittest_log('Client::pollReceipt()');
        // send the message and return the response
        if (function_exists('unittest_log')) unittest_log('Executing the poll request and returning the response');
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
        if (function_exists('unittest_log')) unittest_log('Client::doRequest()');
        // build the request URL
        $url = sprintf('https://api.pushover.net/1/%1$s.json', $entryPoint);
        if (function_exists('unittest_log')) unittest_log('Build API url ' . $url);

        // set up the post fields for the request
        if (function_exists('unittest_log')) unittest_log('Collecting post request fields');
        $postFields                 = $request->getPushoverFields();
        $postFields[Message::TOKEN] = $this->appToken;

        // set up the CURL request options
        if (function_exists('unittest_log')) unittest_log('Configuring CURL');
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
            $url = sprintf($url, $request->getReceipt()) . '?token=' . $this->appToken;
            if (function_exists('unittest_log')) unittest_log('Request is a poll request, removed post data and updated request URL to ' . $url);

            unset($curlOptions[CURLOPT_POSTFIELDS]);
            unset($curlOptions[CURLOPT_POST]);
        }

        // initialize and configure the CURL request
        if (function_exists('unittest_log')) unittest_log('Initializing CURL');
        $curl = curl_init($url);
        curl_setopt_array(
            $curl,
            $curlOptions
        );

        // execute the CURL request
        if (function_exists('unittest_log')) unittest_log('Executing CURL request');
        $result = curl_exec($curl);

        // check whether the server threw a fit (would have nothing to do with the Pushover API, because we configured the CURL request not to throw an error if the HTTP request fails)
        $error = curl_error($curl);
        if ($error != '') {
            if (function_exists('unittest_log')) unittest_log('CURL gave an error: ' . $error);
            throw new Exception($error);
        }

        // close the CURL request
        curl_close($curl);

        // parse the response body and return the Response object
        if (function_exists('unittest_log')) unittest_log('Returning the response object');
        return new Response($result);
    }
}
