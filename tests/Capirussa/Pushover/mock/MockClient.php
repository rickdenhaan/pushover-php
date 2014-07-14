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
    const INACTIVE_RECIPIENT_TOKEN  = 'testValidButInactiveFakedToken';
    const INCORRECT_DEVICE_TOKEN    = 'testIncorrectDevice';
    const INCORRECT_RECEIPT_TOKEN   = 'testNonExistingReceiptToken123';
    const VALID_RECEIPT_TOKEN       = 'testValidReceiptTokenForPoll12';

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
        $data = $request->getPushoverFields();
        $simulatedResponse = '';

        if ($entryPoint === Pushover\Request::API_VALIDATE && $request instanceof Pushover\Validate) {
            if ($data[Pushover\Request::RECIPIENT] == self::VALID_RECIPIENT_TOKEN) {
                if (isset($data[Pushover\Request::DEVICE]) && $data[Pushover\Request::DEVICE] == self::INCORRECT_DEVICE_TOKEN) {
                    $simulatedResponse = file_get_contents(dirname(__FILE__) . '/validateIncorrectDevice.txt');
                } else {
                    $simulatedResponse = file_get_contents(dirname(__FILE__) . '/validateValidUser.txt');
                }
            } elseif ($data[Pushover\Request::RECIPIENT] == self::INCORRECT_RECIPIENT_TOKEN) {
                $simulatedResponse = file_get_contents(dirname(__FILE__) . '/validateIncorrectUser.txt');
            } elseif ($data[Pushover\Request::RECIPIENT] == self::INACTIVE_RECIPIENT_TOKEN) {
                $simulatedResponse = file_get_contents(dirname(__FILE__) . '/validateInactiveUser.txt');
            }
        } elseif ($entryPoint === Pushover\Request::API_MESSAGE && $request instanceof Pushover\Message) {
            if ($data[Pushover\Request::RECIPIENT] == self::VALID_RECIPIENT_TOKEN && isset($data[Pushover\Request::DEVICE]) && $data[Pushover\Request::DEVICE] == self::INCORRECT_DEVICE_TOKEN) {
                $simulatedResponse = file_get_contents(dirname(__FILE__) . '/sendUnknownDevice.txt');
            } elseif ($data[Pushover\Request::RECIPIENT] == self::INCORRECT_RECIPIENT_TOKEN) {
                $simulatedResponse = file_get_contents(dirname(__FILE__) . '/sendIncorrectUser.txt');
            } elseif ($data[Pushover\Request::RECIPIENT] == self::VALID_RECIPIENT_TOKEN) {
                if ($data[Pushover\Request::PRIORITY] == Pushover\Request::PRIORITY_EMERGENCY) {
                    $simulatedResponse = file_get_contents(dirname(__FILE__) . '/sendEmergencyMessage.txt');
                } else {
                    $simulatedResponse = file_get_contents(dirname(__FILE__) . '/sendSimpleMessage.txt');
                }
            }
        } elseif ($entryPoint === Pushover\Request::API_RECEIPT && $request instanceof Pushover\Receipt) {
            if ($request->getReceipt() == self::INCORRECT_RECEIPT_TOKEN) {
                $simulatedResponse = file_get_contents(dirname(__FILE__) . '/pollIncorrectReceipt.txt');
            } else {
                $simulatedResponse = file_get_contents(dirname(__FILE__) . '/pollValidReceipt.txt');
            }
        } else {
            throw new Pushover\Exception('Invalid request');
        }

        // the response should contain \r\n line endings, but Git sometimes screws that up
        $simulatedResponse = str_replace(array("\r\n", "\n\r", "\r", "\n"), "\r\n", $simulatedResponse);

        $this->response = new Pushover\Response($simulatedResponse);
    }
}