<?php
require_once(dirname(__FILE__) . '/../../init.php');

use Capirussa\Pushover;

class MockClient extends Pushover\Client
{
    const INVALID_APPLICATION_TOKEN = 'testInvalidToken';
    const VALID_APPLICATION_TOKEN   = 'testValidTokenOf30AlNumSymbols';
    const INVALID_RECIPIENT_TOKEN   = 'testInvalidToken';
    const VALID_RECIPIENT_TOKEN     = 'testValidTokenOf30AlNumSymbols';
    const INCORRECT_RECIPIENT_TOKEN = 'testValidButIncorrectFakeToken';

    protected function doRequest($entryPoint, $request)
    {
        if ($entryPoint === Pushover\Request::API_VALIDATE && $request instanceof Pushover\Validate) {
            $data = $request->getPushoverFields();
            if ($data[Pushover\Request::RECIPIENT] == self::VALID_RECIPIENT_TOKEN) {
                return new Pushover\Response(file_get_contents(dirname(__FILE__) . '/mock/validateValidUser.txt'));
            } elseif ($data[Pushover\Request::RECIPIENT] == self::INCORRECT_RECIPIENT_TOKEN) {
                return new Pushover\Response(file_get_contents(dirname(__FILE__) . '/mock/validateIncorrectUser.txt'));
            }
        }

        throw new Pushover\Exception('Invalid request');
    }
}