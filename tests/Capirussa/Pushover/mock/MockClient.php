<?php
require_once(dirname(__FILE__) . '/../../../init.php');

use Capirussa\Pushover;

class MockClient extends Pushover\Client
{
    const INVALID_APPLICATION_TOKEN = 'testInvalidToken';
    const VALID_APPLICATION_TOKEN   = 'testValidTokenOf30AlNumSymbols';
    const INVALID_RECIPIENT_TOKEN   = 'testInvalidToken';
    const VALID_RECIPIENT_TOKEN     = 'testValidTokenOf30AlNumSymbols';
    const INCORRECT_RECIPIENT_TOKEN = 'testValidButIncorrectFakeToken';

    /**
     * Overrides the real request method to simulate a predefined response
     *
     * @param string $entryPoint
     * @param mixed  $request
     * @return Pushover\Response
     * @throws Capirussa\Pushover\Exception
     */
    protected function doRequest($entryPoint, $request)
    {
        if ($entryPoint === Pushover\Request::API_VALIDATE && $request instanceof Pushover\Validate) {
            $data = $request->getPushoverFields();
            $simulatedResponse = '';

            if ($data[Pushover\Request::RECIPIENT] == self::VALID_RECIPIENT_TOKEN) {
                $simulatedResponse = file_get_contents(dirname(__FILE__) . '/validateValidUser.txt');
            } elseif ($data[Pushover\Request::RECIPIENT] == self::INCORRECT_RECIPIENT_TOKEN) {
                $simulatedResponse = file_get_contents(dirname(__FILE__) . '/validateIncorrectUser.txt');
            }

            $simulatedResponse = str_replace(array("\r\n", "\n\r", "\r", "\n"), "\r\n", $simulatedResponse);
            $this->response = new Pushover\Response($simulatedResponse);
        } else {
            throw new Pushover\Exception('Invalid request');
        }
    }
}