<?php
require_once(dirname(__FILE__) . '/../../init.php');

use Capirussa\Pushover\Client;

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
     * @expectedException \Capirussa\Pushover\Exception
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
}