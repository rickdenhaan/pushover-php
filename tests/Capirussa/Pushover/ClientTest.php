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
        if (function_exists('unittest_log')) unittest_log('ClientTest::testConstructor()');
        if (function_exists('unittest_log')) unittest_log('Initializing new client');
        $client = new Client();

        if (function_exists('unittest_log')) unittest_log('Making sure we got the correct object');
        $this->assertInstanceOf('Capirussa\\Pushover\\Client', $client);

        // a fresh client should not have a token
        if (function_exists('unittest_log')) unittest_log('Making sure $client->appToken is not yet set');
        $this->assertNull($this->getObjectAttribute($client, 'appToken'));

        // a fresh client must always validate SSL certificates
        if (function_exists('unittest_log')) unittest_log('Making sure $client will validate SSL');
        $this->assertTrue($this->getObjectAttribute($client, 'validateSsl'));
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Invalid application token
     */
    public function testSetInvalidToken()
    {
        if (function_exists('unittest_log')) unittest_log('ClientTest::testSetInvalidToken');
        if (function_exists('unittest_log')) unittest_log('Initializing new client');
        $client = new Client();

        // a fresh client should not have a token
        if (function_exists('unittest_log')) unittest_log('Making sure $client->appToken is not yet set');
        $this->assertNull($this->getObjectAttribute($client, 'appToken'));

        if (function_exists('unittest_log')) unittest_log('Setting INVALID_APPLICATION_TOKEN, should throw an InvalidArgumentException');
        $client->setToken(MockClient::INVALID_APPLICATION_TOKEN);
    }

    public function testSetValidToken()
    {
        if (function_exists('unittest_log')) unittest_log('ClientTest::testSetValidToken()');
        if (function_exists('unittest_log')) unittest_log('Initializing new client');
        $client = new Client();

        // a fresh client should not have a token
        if (function_exists('unittest_log')) unittest_log('Making sure $client->appToken is not yet set');
        $this->assertNull($this->getObjectAttribute($client, 'appToken'));

        if (function_exists('unittest_log')) unittest_log('Setting VALID_APPLICATION_TOKEN');
        $client->setToken(MockClient::VALID_APPLICATION_TOKEN);

        // the client should now have the token set
        if (function_exists('unittest_log')) unittest_log('Making sure $client->appToken is now set and matches VALID_APPLICATION_TOKEN');
        $this->assertEquals(MockClient::VALID_APPLICATION_TOKEN, $this->getObjectAttribute($client, 'appToken'));
    }

    public function testGetToken()
    {
        if (function_exists('unittest_log')) unittest_log('ClientTest::testGetToken()');
        if (function_exists('unittest_log')) unittest_log('Initializing new client');
        $client = new Client();

        // a fresh client should not have a token
        if (function_exists('unittest_log')) unittest_log('Making sure getToken() returns nothing, as appToken is not yet set');
        $this->assertNull($client->getToken());

        if (function_exists('unittest_log')) unittest_log('Setting VALID_APPLICATION_TOKEN');
        $client->setToken(MockClient::VALID_APPLICATION_TOKEN);

        // the client should now have the token set
        if (function_exists('unittest_log')) unittest_log('Verifying that getToken() now returns VALID_APPLICATION_TOKEN');
        $this->assertEquals(MockClient::VALID_APPLICATION_TOKEN, $client->getToken());
    }

    public function testGetInstance()
    {
        if (function_exists('unittest_log')) unittest_log('ClientTest::testGetInstance()');
        if (function_exists('unittest_log')) unittest_log('Getting instance, should initialize a new client');
        $client = Client::getInstance();

        if (function_exists('unittest_log')) unittest_log('Making sure we got a client object');
        $this->assertInstanceOf('Capirussa\\Pushover\\Client', $client);

        // a fresh client should not have a token
        if (function_exists('unittest_log')) unittest_log('Making sure the client does not have a token');
        $this->assertNull($client->getToken());

        // a fresh client must always validate SSL certificates
        if (function_exists('unittest_log')) unittest_log('Making sure the client will validate SSL');
        $this->assertTrue($this->getObjectAttribute($client, 'validateSsl'));
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Invalid application token
     */
    public function testInitWithInvalidToken()
    {
        if (function_exists('unittest_log')) unittest_log('ClientTest::testInitWithInvalidToken()');
        if (function_exists('unittest_log')) unittest_log('Initializing a new client with an invalid token, should throw an InvalidArgumentException');
        Client::init(MockClient::INVALID_APPLICATION_TOKEN);
    }

    public function testInitWithValidToken()
    {
        if (function_exists('unittest_log')) unittest_log('ClientTest::testInitWithValidToken()');
        if (function_exists('unittest_log')) unittest_log('Initializing a new client with a valid token');
        $client = Client::init(MockClient::VALID_APPLICATION_TOKEN);

        if (function_exists('unittest_log')) unittest_log('Making sure we got a client object');
        $this->assertInstanceOf('Capirussa\\Pushover\\Client', $client);

        // the client should have the token set
        if (function_exists('unittest_log')) unittest_log('Making sure VALID_APPLICATION_TOKEN is set');
        $this->assertEquals(MockClient::VALID_APPLICATION_TOKEN, $client->getToken());

        // a fresh client must always validate SSL certificates
        if (function_exists('unittest_log')) unittest_log('Making sure the client will validate SSL');
        $this->assertTrue($this->getObjectAttribute($client, 'validateSsl'));
    }

    public function testDisableSslVerification()
    {
        if (function_exists('unittest_log')) unittest_log('ClientTest::testDisableSslVerification()');
        if (function_exists('unittest_log')) unittest_log('Initializing a new client');
        $client = new Client();

        // a fresh client must always validate SSL certificates
        if (function_exists('unittest_log')) unittest_log('Verifying that the new client will validate SSL');
        $this->assertTrue($this->getObjectAttribute($client, 'validateSsl'));

        if (function_exists('unittest_log')) unittest_log('Disabling SSL verification');
        $client->disableSslVerification();

        // the client should now skip SSL certificate validation
        if (function_exists('unittest_log')) unittest_log('Making sure the client will now skip SSL validation');
        $this->assertFalse($this->getObjectAttribute($client, 'validateSsl'));
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Invalid recipient token
     */
    public function testGetUserDevicesForInvalidUser()
    {
        if (function_exists('unittest_log')) unittest_log('ClientTest::testGetUserDevicesForInvalidUser()');
        if (function_exists('unittest_log')) unittest_log('Creating a mock client');
        $client = MockClient::init(MockClient::VALID_APPLICATION_TOKEN);

        if (function_exists('unittest_log')) unittest_log('Getting user devices for INVALID_RECIPIENT_TOKEN, should throw an InvalidArgumentException');
        $client->getUserDevices(MockClient::INVALID_RECIPIENT_TOKEN);
    }

    /**
     * @expectedException Capirussa\Pushover\Exception
     * @expectedExceptionMessage user key is invalid
     */
    public function testGetUserDevicesForIncorrectUser()
    {
        if (function_exists('unittest_log')) unittest_log('ClientTest::testGetUserDevicesForIncorrectUser()');
        if (function_exists('unittest_log')) unittest_log('Creating a mock client');
        $client = MockClient::init(MockClient::VALID_APPLICATION_TOKEN);

        if (function_exists('unittest_log')) unittest_log('Getting user devices for INCORRECT_RECIPIENT_TOKEN, should throw an Exception');
        $client->getUserDevices(MockClient::INCORRECT_RECIPIENT_TOKEN);
    }

    public function testGetUserDevicesForValidUser()
    {
        if (function_exists('unittest_log')) unittest_log('ClientTest::testGetUserDevicesForValidUser()');
        if (function_exists('unittest_log')) unittest_log('Creating a mock client');
        $client = MockClient::init(MockClient::VALID_APPLICATION_TOKEN);

        // fetch the list of devices
        if (function_exists('unittest_log')) unittest_log('Getting user devices for VALID_RECIPIENT_TOKEN');
        $devices = $client->getUserDevices(MockClient::VALID_RECIPIENT_TOKEN);

        // this should have given us an array with three devices: iphone, ipad and android
        if (function_exists('unittest_log')) unittest_log('Making sure we got an array');
        $this->assertInternalType('array', $devices);
        if (function_exists('unittest_log')) unittest_log('Making sure the array is not empty');
        $this->assertNotEmpty($devices);
        if (function_exists('unittest_log')) unittest_log('Making sure the array contains 3 entries');
        $this->assertCount(3, $devices);
        if (function_exists('unittest_log')) unittest_log('Verifying that the array contains iphone');
        $this->assertContains('iphone', $devices);
        if (function_exists('unittest_log')) unittest_log('Verifying that the array contains ipad');
        $this->assertContains('ipad', $devices);
        if (function_exists('unittest_log')) unittest_log('Verifying that the array contains android');
        $this->assertContains('android', $devices);
    }
}