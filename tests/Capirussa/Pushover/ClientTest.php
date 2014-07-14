<?php
require_once(dirname(__FILE__) . '/../../init.php');

use Capirussa\Pushover\Client;
use Capirussa\Pushover\Message;
use Capirussa\Pushover\Validate;
use Capirussa\Pushover\Receipt;
use Capirussa\Pushover\Request;
use Capirussa\Pushover\Response;

/**
 * Tests Capirussa\Pushover\Client
 *
 */
class ClientTest extends PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $client = new Client();

        $this->assertInstanceOf('Capirussa\\Pushover\\Client', $client);

        // a fresh client should not have a token
        $this->assertNull($this->getObjectAttribute($client, 'appToken'));

        // a fresh client must always validate SSL certificates
        $this->assertTrue($this->getObjectAttribute($client, 'validateSsl'));
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Invalid application token
     */
    public function testSetInvalidToken()
    {
        $client = new Client();

        // a fresh client should not have a token
        $this->assertNull($this->getObjectAttribute($client, 'appToken'));

        $client->setToken(MockClient::INVALID_APPLICATION_TOKEN);
    }

    public function testSetValidToken()
    {
        $client = new Client();

        // a fresh client should not have a token
        $this->assertNull($this->getObjectAttribute($client, 'appToken'));

        $client->setToken(MockClient::VALID_APPLICATION_TOKEN);

        // the client should now have the token set
        $this->assertEquals(MockClient::VALID_APPLICATION_TOKEN, $this->getObjectAttribute($client, 'appToken'));
    }

    public function testGetToken()
    {
        $client = new Client();

        // a fresh client should not have a token
        $this->assertNull($client->getToken());

        $client->setToken(MockClient::VALID_APPLICATION_TOKEN);

        // the client should now have the token set
        $this->assertEquals(MockClient::VALID_APPLICATION_TOKEN, $client->getToken());
    }

    public function testGetInstance()
    {
        $client = Client::getInstance();

        $this->assertInstanceOf('Capirussa\\Pushover\\Client', $client);

        // a fresh client should not have a token
        $this->assertNull($client->getToken());

        // a fresh client must always validate SSL certificates
        $this->assertTrue($this->getObjectAttribute($client, 'validateSsl'));
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Invalid application token
     */
    public function testInitWithInvalidToken()
    {
        Client::init(MockClient::INVALID_APPLICATION_TOKEN);
    }

    public function testInitWithValidToken()
    {
        $client = Client::init(MockClient::VALID_APPLICATION_TOKEN);

        $this->assertInstanceOf('Capirussa\\Pushover\\Client', $client);

        // the client should have the token set
        $this->assertEquals(MockClient::VALID_APPLICATION_TOKEN, $client->getToken());

        // a fresh client must always validate SSL certificates
        $this->assertTrue($this->getObjectAttribute($client, 'validateSsl'));
    }

    public function testDisableSslVerification()
    {
        $client = new Client();

        // a fresh client must always validate SSL certificates
        $this->assertTrue($this->getObjectAttribute($client, 'validateSsl'));

        $client->disableSslVerification();

        // the client should now skip SSL certificate validation
        $this->assertFalse($this->getObjectAttribute($client, 'validateSsl'));
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Invalid recipient token
     */
    public function testGetUserDevicesForInvalidUser()
    {
        $client = MockClient::init(MockClient::VALID_APPLICATION_TOKEN);

        $client->getUserDevices(MockClient::INVALID_RECIPIENT_TOKEN);
    }

    /**
     * @expectedException Capirussa\Pushover\Exception
     * @expectedExceptionMessage user key is invalid
     */
    public function testGetUserDevicesForIncorrectUser()
    {
        $client = MockClient::init(MockClient::VALID_APPLICATION_TOKEN);

        $client->getUserDevices(MockClient::INCORRECT_RECIPIENT_TOKEN);
    }

    public function testGetUserDevicesForValidUser()
    {
        $client = MockClient::init(MockClient::VALID_APPLICATION_TOKEN);

        // fetch the list of devices
        $devices = $client->getUserDevices(MockClient::VALID_RECIPIENT_TOKEN);

        // this should have given us an array with three devices: iphone, ipad and android
        $this->assertInternalType('array', $devices);
        $this->assertNotEmpty($devices);
        $this->assertCount(3, $devices);
        $this->assertContains('iphone', $devices);
        $this->assertContains('ipad', $devices);
        $this->assertContains('android', $devices);
    }

    public function testGetUserDevicesForUserWithoutDevices()
    {
        $client = MockClient::init(MockClient::VALID_APPLICATION_TOKEN);

        // fetch the list of devices
        $devices = $client->getUserDevices(MockClient::INACTIVE_RECIPIENT_TOKEN);

        // this should have given us an array with three devices: iphone, ipad and android
        $this->assertInternalType('array', $devices);
        $this->assertEmpty($devices);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Invalid recipient token
     */
    public function testSendToInvalidUser()
    {
        $client = MockClient::init(MockClient::VALID_APPLICATION_TOKEN);

        $message = new Message(MockClient::INVALID_RECIPIENT_TOKEN, 'Test message');

        $client->send($message);
    }

    /**
     * @expectedException Capirussa\Pushover\Exception
     * @expectedExceptionMessage user identifier is invalid
     */
    public function testSendToIncorrectUser()
    {
        $client = MockClient::init(MockClient::VALID_APPLICATION_TOKEN);

        $message = new Message(MockClient::INCORRECT_RECIPIENT_TOKEN, 'Test message');

        $client->send($message);
    }

    public function testSendToUnknownDevice()
    {
        $client = MockClient::init(MockClient::VALID_APPLICATION_TOKEN);

        $message = new Message(MockClient::VALID_RECIPIENT_TOKEN, 'Test message');
        $message->setDevice(MockClient::INCORRECT_DEVICE_TOKEN);

        $receipt = $client->send($message);

        // if the device identifier is unknown, Pushover actually sends the message to all the user's devices, so this
        // should have been a success

        // this was not an emergency message, so there should not be a receipt token
        $this->assertNull($receipt);

        $response = $client->getLastResponse();
        $this->assertInstanceOf('\Capirussa\Pushover\Response', $response);

        $this->assertEquals(Response::STATUS_SUCCESS, $response->getStatus());
    }

    public function testSendSimpleMessage()
    {
        $client = MockClient::init(MockClient::VALID_APPLICATION_TOKEN);

        $message = new Message(MockClient::VALID_RECIPIENT_TOKEN, 'Test message');

        $receipt = $client->send($message);

        // this was not an emergency message, so there should not be a receipt token
        $this->assertNull($receipt);

        $response = $client->getLastResponse();
        $this->assertInstanceOf('\Capirussa\Pushover\Response', $response);

        $this->assertEquals(Response::STATUS_SUCCESS, $response->getStatus());
    }

    public function testSendEmergencyMessage()
    {
        $client = MockClient::init(MockClient::VALID_APPLICATION_TOKEN);

        $message = new Message(MockClient::VALID_RECIPIENT_TOKEN, 'Test message');
        $message->setPriority(Request::PRIORITY_EMERGENCY);

        $receipt = $client->send($message);

        // this was not an emergency message, so there should not be a receipt token
        $this->assertNotNull($receipt);

        $response = $client->getLastResponse();
        $this->assertInstanceOf('\Capirussa\Pushover\Response', $response);

        $this->assertEquals(Response::STATUS_SUCCESS, $response->getStatus());
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Invalid recipient token
     */
    public function testValidateInvalidUser()
    {
        $client = MockClient::init(MockClient::VALID_APPLICATION_TOKEN);

        $validate = new Validate(MockClient::INVALID_RECIPIENT_TOKEN);

        $client->validate($validate);
    }

    /**
     * @expectedException Capirussa\Pushover\Exception
     * @expectedExceptionMessage user key is invalid
     */
    public function testValidateIncorrectUser()
    {
        $client = MockClient::init(MockClient::VALID_APPLICATION_TOKEN);

        $validate = new Validate(MockClient::INCORRECT_RECIPIENT_TOKEN);

        $client->validate($validate);
    }

    public function testValidateInvalidDevice()
    {
        $client = MockClient::init(MockClient::VALID_APPLICATION_TOKEN);

        $validate = new Validate(MockClient::VALID_RECIPIENT_TOKEN, MockClient::INCORRECT_DEVICE_TOKEN);

        $isValid = $client->validate($validate);

        $this->assertFalse($isValid);
    }

    public function testValidateValidUser()
    {
        $client = MockClient::init(MockClient::VALID_APPLICATION_TOKEN);

        $validate = new Validate(MockClient::VALID_RECIPIENT_TOKEN);

        $isValid = $client->validate($validate);

        $this->assertTrue($isValid);
    }

    /**
     * @expectedException Capirussa\Pushover\Exception
     * @expectedExceptionMessage receipt not found
     */
    public function testPollInvalidReceipt()
    {
        $client = MockClient::init(MockClient::VALID_APPLICATION_TOKEN);

        $receipt = new Receipt(MockClient::INCORRECT_RECEIPT_TOKEN);

        $client->pollReceipt($receipt);
    }

    public function testPollValidReceipt()
    {
        $client = MockClient::init(MockClient::VALID_APPLICATION_TOKEN);

        $receipt = new Receipt(MockClient::VALID_RECEIPT_TOKEN);

        $response = $client->pollReceipt($receipt);

        $this->assertInstanceOf('\Capirussa\Pushover\Response', $response);

        $this->assertEquals(Response::STATUS_SUCCESS, $response->getStatus());
        $this->assertEquals(Response::ACKNOWLEDGED_YES, $response->getAcknowledged());
        $this->assertInstanceOf('\DateTime', $response->getAcknowledgedAt());
        $this->assertEquals('2014-07-14 20:28:03', $response->getAcknowledgedAt()->format('Y-m-d H:i:s'));
        $this->assertEquals(MockClient::VALID_RECIPIENT_TOKEN, $response->getAcknowledgedBy());
        $this->assertInstanceOf('\DateTime', $response->getLastDeliveredAt());
        $this->assertEquals('2014-07-14 20:27:46', $response->getLastDeliveredAt()->format('Y-m-d H:i:s'));
        $this->assertEquals(Response::EXPIRED_NO, $response->getExpired());
        $this->assertInstanceOf('\DateTime', $response->getExpiresAt());
        $this->assertEquals('2014-07-14 21:25:45', $response->getExpiresAt()->format('Y-m-d H:i:s'));
        $this->assertEquals(Response::CALLED_BACK_NO, $response->getCalledBack());
        $this->assertNull($response->getCalledBackAt());
    }
}