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
        if (function_exists('unittest_log')) unittest_log('MockClient::doRequest()');
        if ($entryPoint === Pushover\Request::API_VALIDATE && $request instanceof Pushover\Validate) {
            if (function_exists('unittest_log')) unittest_log('$entryPoint == API_VALIDATE && $request instanceof Pushover\\Validate');
            $data = $request->getPushoverFields();
            if ($data[Pushover\Request::RECIPIENT] == self::VALID_RECIPIENT_TOKEN) {
                if (function_exists('unittest_log')) unittest_log('$data[RECIPIENT] == VALID_RECIPIENT_TOKEN');
                return new Pushover\Response(file_get_contents(dirname(__FILE__) . '/validateValidUser.txt'));
            } elseif ($data[Pushover\Request::RECIPIENT] == self::INCORRECT_RECIPIENT_TOKEN) {
                if (function_exists('unittest_log')) unittest_log('$data[RECIPIENT] == INCORRECT_RECIPIENT_TOKEN');
                return new Pushover\Response(file_get_contents(dirname(__FILE__) . '/validateIncorrectUser.txt'));
            }
        }

        if (function_exists('unittest_log')) unittest_log('Unknown entrypoint or request, throwing Pushover\\Exception');
        throw new Pushover\Exception('Invalid request');
    }
}