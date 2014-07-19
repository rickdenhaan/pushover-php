<?php
require_once(dirname(__FILE__) . '/../../init.php');

use Capirussa\Pushover\Validate;
use Capirussa\Pushover\Request;

/**
 * Tests Capirussa\Pushover\Validate
 *
 */
class ValidateTest extends PHPUnit_Framework_TestCase
{
    public function testConstructWithoutParameters()
    {
        $validate = new Validate();

        $this->assertInstanceOf('Capirussa\\Pushover\\Validate', $validate);

        // the validation request should not have a recipient or device set
        $this->assertNull($this->getObjectAttribute($validate, 'recipient'));
        $this->assertNull($this->getObjectAttribute($validate, 'device'));
    }

    public function testConstructWithOnlyRecipient()
    {
        $validate = new Validate(MockClient::VALID_RECIPIENT_TOKEN);

        // the request should now have this token set as the recipient
        $this->assertNotNull($this->getObjectAttribute($validate, 'recipient'));
        $this->assertEquals(MockClient::VALID_RECIPIENT_TOKEN, $this->getObjectAttribute($validate, 'recipient'));

        // the request should not have a device set
        $this->assertNull($this->getObjectAttribute($validate, 'device'));
    }

    public function testConstructWithOnlyDevice()
    {
        $validate = new Validate(null, 'iphone');

        // the request should now have a device set
        $this->assertNotNull($this->getObjectAttribute($validate, 'device'));
        $this->assertEquals('iphone', $this->getObjectAttribute($validate, 'device'));

        // the request should not have a recipient set
        $this->assertNull($this->getObjectAttribute($validate, 'recipient'));
    }

    public function testConstructWithRecipientAndDevice()
    {
        $validate = new Validate(MockClient::VALID_RECIPIENT_TOKEN, 'iphone');

        // the message should now have a recipient and device set
        $this->assertNotNull($this->getObjectAttribute($validate, 'recipient'));
        $this->assertNotNull($this->getObjectAttribute($validate, 'device'));

        // the recipient and device should match what we set
        $this->assertEquals(MockClient::VALID_RECIPIENT_TOKEN, $this->getObjectAttribute($validate, 'recipient'));
        $this->assertEquals('iphone', $this->getObjectAttribute($validate, 'device'));
    }

    /**
     * @expectedException PHPUnit_Framework_Error_Warning
     */
    public function testSetRecipientWithoutRecipient()
    {
        $validate = new Validate();

        /** @noinspection PhpParamsInspection (this is intentional) */
        $validate->setRecipient();
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Invalid recipient token
     */
    public function testSetRecipientWithInvalidRecipient()
    {
        $validate = new Validate();

        $validate->setRecipient(MockClient::INVALID_RECIPIENT_TOKEN);
    }

    public function testSetRecipientWithValidButIncorrectRecipient()
    {
        $validate = new Validate();

        $validate->setRecipient(MockClient::INCORRECT_RECIPIENT_TOKEN);

        $this->assertNotNull($this->getObjectAttribute($validate, 'recipient'));
        $this->assertEquals(MockClient::INCORRECT_RECIPIENT_TOKEN, $this->getObjectAttribute($validate, 'recipient'));
    }

    public function testSetRecipientWithValidRecipient()
    {
        $validate = new Validate();

        $validate->setRecipient(MockClient::VALID_RECIPIENT_TOKEN);

        $this->assertNotNull($this->getObjectAttribute($validate, 'recipient'));
        $this->assertEquals(MockClient::VALID_RECIPIENT_TOKEN, $this->getObjectAttribute($validate, 'recipient'));
    }

    /**
     * @expectedException PHPUnit_Framework_Error_Warning
     */
    public function testSetDeviceWithoutDevice()
    {
        $validate = new Validate();

        /** @noinspection PhpParamsInspection (this is intentional) */
        $validate->setDevice();
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Invalid device format
     */
    public function testSetDeviceWithInvalidDevice()
    {
        $validate = new Validate();

        $validate->setDevice(MockClient::VALID_RECIPIENT_TOKEN);
    }

    public function testSetDeviceWithValidButIncorrectDevice()
    {
        $validate = new Validate();

        $validate->setDevice(MockClient::INCORRECT_DEVICE_TOKEN);

        $this->assertNotNull($this->getObjectAttribute($validate, 'device'));
        $this->assertEquals(MockClient::INCORRECT_DEVICE_TOKEN, $this->getObjectAttribute($validate, 'device'));
    }

    public function testSetDeviceWithValidDevice()
    {
        $validate = new Validate();

        $validate->setDevice('iphone');

        $this->assertNotNull($this->getObjectAttribute($validate, 'device'));
        $this->assertEquals('iphone', $this->getObjectAttribute($validate, 'device'));
    }

    public function testGetPushoverFieldsWithEmptyValidate()
    {
        $validate = new Validate();

        $pushOverFields = $validate->getPushoverFields();

        $this->assertInternalType('array', $pushOverFields);

        // since this was an empty message, the array should only contain the one required field: recipient
        $this->assertCount(1, $pushOverFields);
        $this->assertArrayHasKey(Request::RECIPIENT, $pushOverFields);

        // this field should have a null value
        $this->assertNull($pushOverFields[Request::RECIPIENT]);
    }

    public function testGetPushoverFieldsWithBasicValidate()
    {
        $validate = new Validate(MockClient::VALID_RECIPIENT_TOKEN);

        $pushOverFields = $validate->getPushoverFields();

        $this->assertInternalType('array', $pushOverFields);

        // since this was a basic validation, the array should only contain the required field: recipient
        $this->assertCount(1, $pushOverFields);
        $this->assertArrayHasKey(Request::RECIPIENT, $pushOverFields);

        // the field should match the value we set
        $this->assertEquals(MockClient::VALID_RECIPIENT_TOKEN, $pushOverFields[Request::RECIPIENT]);
    }

    public function testGetPushoverFieldsWithDevice()
    {
        $validate = new Validate(MockClient::VALID_RECIPIENT_TOKEN, 'iphone');

        $pushOverFields = $validate->getPushoverFields();

        $this->assertInternalType('array', $pushOverFields);

        // the array should contain 2 fields:
        $this->assertCount(2, $pushOverFields);
        $this->assertArrayHasKey(Request::RECIPIENT, $pushOverFields);
        $this->assertArrayHasKey(Request::DEVICE, $pushOverFields);

        // the fields should match the values we set
        $this->assertEquals(MockClient::VALID_RECIPIENT_TOKEN, $pushOverFields[Request::RECIPIENT]);
        $this->assertEquals('iphone', $pushOverFields[Request::DEVICE]);
    }
}