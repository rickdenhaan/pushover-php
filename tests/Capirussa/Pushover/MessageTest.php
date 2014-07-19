<?php
require_once(dirname(__FILE__) . '/../../init.php');

use Capirussa\Pushover\Message;
use Capirussa\Pushover\Sound;
use Capirussa\Pushover\Request;

/**
 * Tests Capirussa\Pushover\Message
 *
 */
class MessageTest extends PHPUnit_Framework_TestCase
{
    public function testConstructWithoutParameters()
    {
        $message = new Message();

        $this->assertInstanceOf('Capirussa\\Pushover\\Message', $message);

        // the message should not have a recipient or message set
        $this->assertNull($this->getObjectAttribute($message, 'recipient'));
        $this->assertNull($this->getObjectAttribute($message, 'message'));
    }

    public function testConstructWithOnlyRecipient()
    {
        $message = new Message(MockClient::VALID_RECIPIENT_TOKEN);

        // the message should now have this token set as the recipient
        $this->assertNotNull($this->getObjectAttribute($message, 'recipient'));
        $this->assertEquals(MockClient::VALID_RECIPIENT_TOKEN, $this->getObjectAttribute($message, 'recipient'));

        // the message should not have a message set
        $this->assertNull($this->getObjectAttribute($message, 'message'));
    }

    public function testConstructWithOnlyMessage()
    {
        $message = new Message(null, 'testMessage');

        // the message should now have a message set
        $this->assertNotNull($this->getObjectAttribute($message, 'message'));
        $this->assertEquals('testMessage', $this->getObjectAttribute($message, 'message'));

        // the message should not have a recipient set
        $this->assertNull($this->getObjectAttribute($message, 'recipient'));
    }

    public function testConstructWithRecipientAndMessage()
    {
        $message = new Message(MockClient::VALID_RECIPIENT_TOKEN, 'testMessage');

        // the message should now have a recipient and message set
        $this->assertNotNull($this->getObjectAttribute($message, 'recipient'));
        $this->assertNotNull($this->getObjectAttribute($message, 'message'));

        // the recipient and message should match what we set
        $this->assertEquals(MockClient::VALID_RECIPIENT_TOKEN, $this->getObjectAttribute($message, 'recipient'));
        $this->assertEquals('testMessage', $this->getObjectAttribute($message, 'message'));
    }

    /**
     * @expectedException PHPUnit_Framework_Error_Warning
     */
    public function testSetRecipientWithoutRecipient()
    {
        $message = new Message();

        /** @noinspection PhpParamsInspection (this is intentional) */
        $message->setRecipient();
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Invalid recipient token
     */
    public function testSetRecipientWithInvalidRecipient()
    {
        $message = new Message();

        $message->setRecipient(MockClient::INVALID_RECIPIENT_TOKEN);
    }

    public function testSetRecipientWithValidButIncorrectReceipient()
    {
        $message = new Message();

        $message->setRecipient(MockClient::INCORRECT_RECIPIENT_TOKEN);

        $this->assertNotNull($this->getObjectAttribute($message, 'recipient'));
        $this->assertEquals(MockClient::INCORRECT_RECIPIENT_TOKEN, $this->getObjectAttribute($message, 'recipient'));
    }

    public function testSetRecipientWithValidReceipient()
    {
        $message = new Message();

        $message->setRecipient(MockClient::VALID_RECIPIENT_TOKEN);

        $this->assertNotNull($this->getObjectAttribute($message, 'recipient'));
        $this->assertEquals(MockClient::VALID_RECIPIENT_TOKEN, $this->getObjectAttribute($message, 'recipient'));
    }

    /**
     * @expectedException PHPUnit_Framework_Error_Warning
     */
    public function testSetMessageWithoutMessage()
    {
        $message = new Message();

        /** @noinspection PhpParamsInspection (this is intentional) */
        $message->setMessage();
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage must not be empty
     */
    public function testSetMessageWithEmptyMessage()
    {
        $message = new Message();

        $message->setMessage('');
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage must not be empty
     */
    public function testSetMessageWithOnlyWhiteSpace()
    {
        $message = new Message();

        $message->setMessage(" \n\t\t \r  \n\t  \r\n ");
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Message is too long
     */
    public function testSetMessageWithTooLongMessage()
    {
        $messageBody = str_repeat('b', 550);

        $message = new Message();

        $message->setMessage($messageBody);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Message is too long
     */
    public function testSetMessageWithTooLongMessageCombinedWithExistingTitle()
    {
        $messageTitle = str_repeat('t', 50);
        $messageBody = str_repeat('b', 500);

        $message = new Message();

        $message->setTitle($messageTitle);

        $message->setMessage($messageBody);
    }

    public function testSetMessageWithProperMessage()
    {
        $messageBody = str_repeat('b', 500);

        $message = new Message();

        $message->setMessage($messageBody);

        $this->assertNotNull($this->getObjectAttribute($message, 'message'));
        $this->assertEquals($messageBody, $this->getObjectAttribute($message, 'message'));
    }

    /**
     * @expectedException PHPUnit_Framework_Error_Warning
     */
    public function testSetTitleWithoutTitle()
    {
        $message = new Message();

        /** @noinspection PhpParamsInspection (this is intentional) */
        $message->setTitle();
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage must not be empty
     */
    public function testSetTitleWithEmptyTitle()
    {
        $message = new Message();

        $message->setTitle('');
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage must not be empty
     */
    public function testSetTitleWithOnlyWhiteSpace()
    {
        $message = new Message();

        $message->setTitle(" \n\t\t \r  \n\t  \r\n ");
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Title is too long
     */
    public function testSetTitleWithTooLongTitle()
    {
        $messageTitle = str_repeat('t', 150);

        $message = new Message();

        $message->setTitle($messageTitle);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Title is too long
     */
    public function testSetTitleWithTooLongTitleCombinedWithExistingMessage()
    {
        $messageTitle = str_repeat('t', 50);
        $messageBody = str_repeat('b', 500);

        $message = new Message();

        $message->setMessage($messageBody);

        $message->setTitle($messageTitle);
    }

    public function testSetTitleWithProperTitle()
    {
        $messageTitle = str_repeat('t', 50);

        $message = new Message();

        $message->setTitle($messageTitle);

        $this->assertNotNull($this->getObjectAttribute($message, 'title'));
        $this->assertEquals($messageTitle, $this->getObjectAttribute($message, 'title'));
    }

    /**
     * @expectedException PHPUnit_Framework_Error_Warning
     */
    public function testSetDeviceWithoutDevice()
    {
        $message = new Message();

        /** @noinspection PhpParamsInspection (this is intentional) */
        $message->setDevice();
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Invalid device format
     */
    public function testSetDeviceWithInvalidDevice()
    {
        $message = new Message();

        $message->setDevice(MockClient::VALID_RECIPIENT_TOKEN);
    }

    public function testSetDeviceWithValidButIncorrectDevice()
    {
        $message = new Message();

        $message->setDevice(MockClient::INCORRECT_DEVICE_TOKEN);

        $this->assertNotNull($this->getObjectAttribute($message, 'device'));
        $this->assertEquals(MockClient::INCORRECT_DEVICE_TOKEN, $this->getObjectAttribute($message, 'device'));
    }

    public function testSetDeviceWithValidDevice()
    {
        $message = new Message();

        $message->setDevice('iphone');

        $this->assertNotNull($this->getObjectAttribute($message, 'device'));
        $this->assertEquals('iphone', $this->getObjectAttribute($message, 'device'));
    }

    /**
     * @expectedException PHPUnit_Framework_Error_Warning
     */
    public function testSetUrlWithoutUrl()
    {
        $message = new Message();

        /** @noinspection PhpParamsInspection (this is intentional) */
        $message->setUrl();
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage must not be empty
     */
    public function testSetUrlWithEmptyUrl()
    {
        $message = new Message();

        $message->setUrl('');
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage must not be empty
     */
    public function testSetUrlWithOnlyWhiteSpace()
    {
        $message = new Message();

        $message->setUrl(" \n\t\t \r  \n\t  \r\n ");
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage URL is too long
     */
    public function testSetUrlWithTooLongUrl()
    {
        $messageUrl = str_repeat('u', 550);

        $message = new Message();

        $message->setUrl($messageUrl);
    }

    public function testSetUrlWithProperUrl()
    {
        $messageUrl = str_repeat('u', 50);

        $message = new Message();

        $message->setUrl($messageUrl);

        $this->assertNotNull($this->getObjectAttribute($message, 'url'));
        $this->assertEquals($messageUrl, $this->getObjectAttribute($message, 'url'));
    }

    /**
     * @expectedException PHPUnit_Framework_Error_Warning
     */
    public function testSetUrlTitleWithoutTitle()
    {
        $message = new Message();

        /** @noinspection PhpParamsInspection (this is intentional) */
        $message->setUrlTitle();
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage must not be empty
     */
    public function testSetUrlTitleWithEmptyTitle()
    {
        $message = new Message();

        $message->setUrlTitle('');
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage must not be empty
     */
    public function testSetUrlTitleWithOnlyWhiteSpace()
    {
        $message = new Message();

        $message->setUrlTitle(" \n\t\t \r  \n\t  \r\n ");
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage URL title is too long
     */
    public function testSetUrlTitleWithTooLongTitle()
    {
        $urlTitle = str_repeat('t', 150);

        $message = new Message();

        $message->setUrlTitle($urlTitle);
    }

    public function testSetUrlTitleWithProperTitle()
    {
        $urlTitle = str_repeat('t', 50);

        $message = new Message();

        $message->setUrlTitle($urlTitle);

        $this->assertNotNull($this->getObjectAttribute($message, 'urlTitle'));
        $this->assertEquals($urlTitle, $this->getObjectAttribute($message, 'urlTitle'));
    }

    /**
     * @expectedException PHPUnit_Framework_Error_Warning
     */
    public function testSetPriorityWithoutPriority()
    {
        $message = new Message();

        /** @noinspection PhpParamsInspection (this is intentional) */
        $message->setPriority();
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Invalid message priority
     */
    public function testSetPriorityWithInvalidPriority()
    {
        $message = new Message();

        $message->setPriority('');
    }

    public function testSetPriorityToInvisibleByConstant()
    {
        $message = new Message();

        $message->setPriority(Request::PRIORITY_INVISIBLE);

        $this->assertEquals(Request::PRIORITY_INVISIBLE, $this->getObjectAttribute($message, 'priority'));
    }

    public function testSetPriorityToInvisibleByInteger()
    {
        $message = new Message();

        $message->setPriority(-2);

        $this->assertEquals(Request::PRIORITY_INVISIBLE, $this->getObjectAttribute($message, 'priority'));
    }

    public function testSetPriorityToSilentByConstant()
    {
        $message = new Message();

        $message->setPriority(Request::PRIORITY_SILENT);

        $this->assertEquals(Request::PRIORITY_SILENT, $this->getObjectAttribute($message, 'priority'));
    }

    public function testSetPriorityToSilentByInteger()
    {
        $message = new Message();

        $message->setPriority(-1);

        $this->assertEquals(Request::PRIORITY_SILENT, $this->getObjectAttribute($message, 'priority'));
    }

    public function testSetPriorityToNormalByConstant()
    {
        $message = new Message();

        // priority is normal by default, so to make sure this works set it to something else first
        $message->setPriority(Request::PRIORITY_SILENT);
        $this->assertEquals(Request::PRIORITY_SILENT, $this->getObjectAttribute($message, 'priority'));

        $message->setPriority(Request::PRIORITY_NORMAL);

        $this->assertEquals(Request::PRIORITY_NORMAL, $this->getObjectAttribute($message, 'priority'));
    }

    public function testSetPriorityToNormalByInteger()
    {
        $message = new Message();

        // priority is normal by default, so to make sure this works set it to something else first
        $message->setPriority(Request::PRIORITY_SILENT);
        $this->assertEquals(Request::PRIORITY_SILENT, $this->getObjectAttribute($message, 'priority'));

        $message->setPriority(0);

        $this->assertEquals(Request::PRIORITY_NORMAL, $this->getObjectAttribute($message, 'priority'));
    }

    public function testSetPriorityToHighByConstant()
    {
        $message = new Message();

        $message->setPriority(Request::PRIORITY_HIGH);

        $this->assertEquals(Request::PRIORITY_HIGH, $this->getObjectAttribute($message, 'priority'));
    }

    public function testSetPriorityToHighByInteger()
    {
        $message = new Message();

        $message->setPriority(1);

        $this->assertEquals(Request::PRIORITY_HIGH, $this->getObjectAttribute($message, 'priority'));
    }

    public function testSetPriorityToEmergencyByConstant()
    {
        $message = new Message();

        $message->setPriority(Request::PRIORITY_EMERGENCY);

        $this->assertEquals(Request::PRIORITY_EMERGENCY, $this->getObjectAttribute($message, 'priority'));
    }

    public function testSetPriorityToEmergencyByInteger()
    {
        $message = new Message();

        $message->setPriority(2);

        $this->assertEquals(Request::PRIORITY_EMERGENCY, $this->getObjectAttribute($message, 'priority'));
    }

    /**
     * @expectedException PHPUnit_Framework_Error_Warning
     */
    public function testSetTimestampWithoutTimestamp()
    {
        $message = new Message();

        /** @noinspection PhpParamsInspection (this is intentional) */
        $message->setTimestamp();
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Invalid message timestamp
     */
    public function testSetTimestampWithInvalidArgumentType()
    {
        $message = new Message();

        $message->setTimestamp('this is a string');
    }

    public function testSetTimestampWithTimestamp()
    {
        $message = new Message();

        $message->setTimestamp(1405711800);

        $this->assertEquals(1405711800, $this->getObjectAttribute($message, 'timestamp'));
    }

    public function testSetTimestampWithDateTime()
    {
        $dateTime = new \DateTime('2014-07-18 21:30:00', new \DateTimeZone('Europe/Amsterdam'));

        $message = new Message();

        $message->setTimestamp($dateTime);

        $this->assertEquals(1405711800, $this->getObjectAttribute($message, 'timestamp'));
    }

    public function testSetTimestampWithDateTimeInOtherTimeZone()
    {
        $dateTime = new \DateTime('2014-07-18 20:30:00', new \DateTimeZone('Europe/London'));

        $message = new Message();

        $message->setTimestamp($dateTime);

        $this->assertEquals(1405711800, $this->getObjectAttribute($message, 'timestamp'));
    }

    /**
     * @expectedException PHPUnit_Framework_Error_Warning
     */
    public function testSetSoundWithoutSound()
    {
        $message = new Message();

        /** @noinspection PhpParamsInspection (this is intentional) */
        $message->setSound();
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Invalid message sound
     */
    public function testSetSoundWithInvalidSound()
    {
        $message = new Message();

        $message->setSound('invalid sound');
    }

    public function testSetSoundToPushoverByConstant()
    {
        $message = new Message();

        $message->setSound(Sound::PUSHOVER);

        $this->assertEquals(Sound::PUSHOVER, $this->getObjectAttribute($message, 'sound'));
    }

    public function testSetSoundToPushoverByString()
    {
        $message = new Message();

        $message->setSound('pushover');

        $this->assertEquals(Sound::PUSHOVER, $this->getObjectAttribute($message, 'sound'));
    }

    public function testSetSoundToPushoverByObject()
    {
        $message = new Message();

        $message->setSound(new Sound(Sound::PUSHOVER));

        $this->assertEquals(Sound::PUSHOVER, $this->getObjectAttribute($message, 'sound'));
    }

    public function testSetSoundToUserDefaultByConstant()
    {
        $message = new Message();

        // sound is user default by default, so to make sure this works set it to something else first
        $message->setSound(Sound::SPACE_ALARM);
        $this->assertEquals(Sound::SPACE_ALARM, $this->getObjectAttribute($message, 'sound'));

        $message->setSound(Sound::USER_DEFAULT);

        $this->assertEquals(Sound::USER_DEFAULT, $this->getObjectAttribute($message, 'sound'));
    }

    public function testSetSoundToUserDefaultByString()
    {
        $message = new Message();

        // sound is user default by default, so to make sure this works set it to something else first
        $message->setSound(Sound::SPACE_ALARM);
        $this->assertEquals(Sound::SPACE_ALARM, $this->getObjectAttribute($message, 'sound'));

        $message->setSound('');

        $this->assertEquals(Sound::USER_DEFAULT, $this->getObjectAttribute($message, 'sound'));
    }

    public function testSetSoundToUserDefaultByObject()
    {
        $message = new Message();

        // sound is user default by default, so to make sure this works set it to something else first
        $message->setSound(Sound::SPACE_ALARM);
        $this->assertEquals(Sound::SPACE_ALARM, $this->getObjectAttribute($message, 'sound'));

        $message->setSound(new Sound(Sound::USER_DEFAULT));

        $this->assertEquals(Sound::USER_DEFAULT, $this->getObjectAttribute($message, 'sound'));
    }

    /**
     * @expectedException PHPUnit_Framework_Error_Warning
     */
    public function testSetCallbackUrlWithoutUrl()
    {
        $message = new Message();

        /** @noinspection PhpParamsInspection (this is intentional) */
        $message->setCallbackUrl();
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage must not be empty
     */
    public function testSetCallbackUrlWithEmptyUrl()
    {
        $message = new Message();

        $message->setCallbackUrl('');
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage must not be empty
     */
    public function testSetCallbackUrlWithOnlyWhiteSpace()
    {
        $message = new Message();

        $message->setCallbackUrl(" \n\t\t \r  \n\t  \r\n ");
    }

    public function testSetCallbackUrlWithProperUrl()
    {
        $callbackUrl = str_repeat('u', 50);

        $message = new Message();

        $message->setCallbackUrl($callbackUrl);

        $this->assertNotNull($this->getObjectAttribute($message, 'callback'));
        $this->assertEquals($callbackUrl, $this->getObjectAttribute($message, 'callback'));
    }

    /**
     * @expectedException PHPUnit_Framework_Error_Warning
     */
    public function testSetExpireWithoutExpire()
    {
        $message = new Message();

        /** @noinspection PhpParamsInspection (this is intentional) */
        $message->setExpire();
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Invalid expire delay
     */
    public function testSetExpireWithNegativeExpire()
    {
        $message = new Message();

        $message->setExpire(-60);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Invalid expire delay
     */
    public function testSetExpireWithImmediateExpire()
    {
        $message = new Message();

        $message->setExpire(0);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Invalid expire delay
     */
    public function testSetExpireWithTooLongExpire()
    {
        $message = new Message();

        $message->setExpire(90000);
    }

    public function testSetExpireWithValidExpire()
    {
        $message = new Message();

        $message->setExpire(12345);

        $this->assertEquals(12345, $this->getObjectAttribute($message, 'expire'));
    }

    public function testSetExpireWithValidExpireOctal()
    {
        $message = new Message();

        $message->setExpire(030071);

        $this->assertEquals(12345, $this->getObjectAttribute($message, 'expire'));
    }

    public function testSetExpireWithValidExpireHexadecimal()
    {
        $message = new Message();

        $message->setExpire(0x3039);

        $this->assertEquals(12345, $this->getObjectAttribute($message, 'expire'));
    }

    public function testSetExpireWithValidExpireString()
    {
        $message = new Message();

        $message->setExpire('12345');

        $this->assertEquals(12345, $this->getObjectAttribute($message, 'expire'));
    }

    /**
     * @expectedException PHPUnit_Framework_Error_Warning
     */
    public function testSetRetryWithoutRetry()
    {
        $message = new Message();

        /** @noinspection PhpParamsInspection (this is intentional) */
        $message->setRetry();
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Invalid retry delay
     */
    public function testSetRetryWithNegativeRetry()
    {
        $message = new Message();

        $message->setRetry(-60);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Invalid retry delay
     */
    public function testSetRetryWithImmediateRetry()
    {
        $message = new Message();

        $message->setRetry(0);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Invalid retry delay
     */
    public function testSetRetryWithTooShortRetry()
    {
        $message = new Message();

        $message->setRetry(15);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Invalid retry delay
     */
    public function testSetRetryWithTooLongRetry()
    {
        $message = new Message();

        $message->setRetry(90000);
    }

    public function testSetRetryWithValidRetry()
    {
        $message = new Message();

        $message->setRetry(12345);

        $this->assertEquals(12345, $this->getObjectAttribute($message, 'retry'));
    }

    public function testSetRetryWithValidRetryOctal()
    {
        $message = new Message();

        $message->setRetry(030071);

        $this->assertEquals(12345, $this->getObjectAttribute($message, 'retry'));
    }

    public function testSetRetryWithValidRetryHexadecimal()
    {
        $message = new Message();

        $message->setRetry(0x3039);

        $this->assertEquals(12345, $this->getObjectAttribute($message, 'retry'));
    }

    public function testSetRetryWithValidRetryString()
    {
        $message = new Message();

        $message->setRetry('12345');

        $this->assertEquals(12345, $this->getObjectAttribute($message, 'retry'));
    }

    public function testGetPushoverFieldsWithEmptyMessage()
    {
        $message = new Message();

        $pushOverFields = $message->getPushoverFields();

        $this->assertInternalType('array', $pushOverFields);

        // since this was an empty message, the array should only contain the two required fields: recipient and message
        $this->assertCount(2, $pushOverFields);
        $this->assertArrayHasKey(Request::RECIPIENT, $pushOverFields);
        $this->assertArrayHasKey(Request::MESSAGE, $pushOverFields);

        // both these fields should have null values
        $this->assertNull($pushOverFields[Request::RECIPIENT]);
        $this->assertNull($pushOverFields[Request::MESSAGE]);
    }

    public function testGetPushoverFieldsWithBasicMessage()
    {
        $message = new Message(MockClient::VALID_RECIPIENT_TOKEN, 'testMessage');

        $pushOverFields = $message->getPushoverFields();

        $this->assertInternalType('array', $pushOverFields);

        // since this was a basic message, the array should only contain the two required fields: recipient and message
        $this->assertCount(2, $pushOverFields);
        $this->assertArrayHasKey(Request::RECIPIENT, $pushOverFields);
        $this->assertArrayHasKey(Request::MESSAGE, $pushOverFields);

        // the fields should match the values we set
        $this->assertEquals(MockClient::VALID_RECIPIENT_TOKEN, $pushOverFields[Request::RECIPIENT]);
        $this->assertEquals('testMessage', $pushOverFields[Request::MESSAGE]);
    }

    public function testGetPushoverFieldsWithTitle()
    {
        $message = new Message(MockClient::VALID_RECIPIENT_TOKEN, 'testMessage');
        $message->setTitle('testTitle');

        $pushOverFields = $message->getPushoverFields();

        $this->assertInternalType('array', $pushOverFields);

        // the array should contain 3 fields:
        $this->assertCount(3, $pushOverFields);
        $this->assertArrayHasKey(Request::RECIPIENT, $pushOverFields);
        $this->assertArrayHasKey(Request::MESSAGE, $pushOverFields);
        $this->assertArrayHasKey(Request::TITLE, $pushOverFields);

        // the fields should match the values we set
        $this->assertEquals(MockClient::VALID_RECIPIENT_TOKEN, $pushOverFields[Request::RECIPIENT]);
        $this->assertEquals('testMessage', $pushOverFields[Request::MESSAGE]);
        $this->assertEquals('testTitle', $pushOverFields[Request::TITLE]);
    }

    public function testGetPushoverFieldsWithDevice()
    {
        $message = new Message(MockClient::VALID_RECIPIENT_TOKEN, 'testMessage');
        $message->setDevice('iphone');

        $pushOverFields = $message->getPushoverFields();

        $this->assertInternalType('array', $pushOverFields);

        // the array should contain 3 fields:
        $this->assertCount(3, $pushOverFields);
        $this->assertArrayHasKey(Request::RECIPIENT, $pushOverFields);
        $this->assertArrayHasKey(Request::MESSAGE, $pushOverFields);
        $this->assertArrayHasKey(Request::DEVICE, $pushOverFields);

        // the fields should match the values we set
        $this->assertEquals(MockClient::VALID_RECIPIENT_TOKEN, $pushOverFields[Request::RECIPIENT]);
        $this->assertEquals('testMessage', $pushOverFields[Request::MESSAGE]);
        $this->assertEquals('iphone', $pushOverFields[Request::DEVICE]);
    }

    public function testGetPushoverFieldsWithUrlWithoutUrlTitle()
    {
        $message = new Message(MockClient::VALID_RECIPIENT_TOKEN, 'testMessage');
        $message->setUrl('testUrl');

        $pushOverFields = $message->getPushoverFields();

        $this->assertInternalType('array', $pushOverFields);

        // the array should contain 3 fields:
        $this->assertCount(3, $pushOverFields);
        $this->assertArrayHasKey(Request::RECIPIENT, $pushOverFields);
        $this->assertArrayHasKey(Request::MESSAGE, $pushOverFields);
        $this->assertArrayHasKey(Request::URL, $pushOverFields);

        // the fields should match the values we set
        $this->assertEquals(MockClient::VALID_RECIPIENT_TOKEN, $pushOverFields[Request::RECIPIENT]);
        $this->assertEquals('testMessage', $pushOverFields[Request::MESSAGE]);
        $this->assertEquals('testUrl', $pushOverFields[Request::URL]);
    }

    public function testGetPushoverFieldsWithUrlTitleWithoutUrl()
    {
        $message = new Message(MockClient::VALID_RECIPIENT_TOKEN, 'testMessage');
        $message->setUrlTitle('testUrlTitle');

        $pushOverFields = $message->getPushoverFields();

        $this->assertInternalType('array', $pushOverFields);

        // the array should contain 2 fields, because the url title is ignored if no URL is set:
        $this->assertCount(2, $pushOverFields);
        $this->assertArrayHasKey(Request::RECIPIENT, $pushOverFields);
        $this->assertArrayHasKey(Request::MESSAGE, $pushOverFields);

        // the fields should match the values we set
        $this->assertEquals(MockClient::VALID_RECIPIENT_TOKEN, $pushOverFields[Request::RECIPIENT]);
        $this->assertEquals('testMessage', $pushOverFields[Request::MESSAGE]);
    }

    public function testGetPushoverFieldsWithUrlAndUrlTitle()
    {
        $message = new Message(MockClient::VALID_RECIPIENT_TOKEN, 'testMessage');
        $message->setUrl('testUrl');
        $message->setUrlTitle('testUrlTitle');

        $pushOverFields = $message->getPushoverFields();

        $this->assertInternalType('array', $pushOverFields);

        // the array should contain 4 fields:
        $this->assertCount(4, $pushOverFields);
        $this->assertArrayHasKey(Request::RECIPIENT, $pushOverFields);
        $this->assertArrayHasKey(Request::MESSAGE, $pushOverFields);
        $this->assertArrayHasKey(Request::URL, $pushOverFields);
        $this->assertArrayHasKey(Request::URL_TITLE, $pushOverFields);

        // the fields should match the values we set
        $this->assertEquals(MockClient::VALID_RECIPIENT_TOKEN, $pushOverFields[Request::RECIPIENT]);
        $this->assertEquals('testMessage', $pushOverFields[Request::MESSAGE]);
        $this->assertEquals('testUrl', $pushOverFields[Request::URL]);
        $this->assertEquals('testUrlTitle', $pushOverFields[Request::URL_TITLE]);
    }

    public function testGetPushoverFieldsWithNormalPriority()
    {
        $message = new Message(MockClient::VALID_RECIPIENT_TOKEN, 'testMessage');
        $message->setPriority(Request::PRIORITY_NORMAL);

        $pushOverFields = $message->getPushoverFields();

        $this->assertInternalType('array', $pushOverFields);

        // the array should contain 3 fields
        $this->assertCount(3, $pushOverFields);
        $this->assertArrayHasKey(Request::RECIPIENT, $pushOverFields);
        $this->assertArrayHasKey(Request::MESSAGE, $pushOverFields);
        $this->assertArrayHasKey(Request::PRIORITY, $pushOverFields);

        // the fields should match the values we set
        $this->assertEquals(MockClient::VALID_RECIPIENT_TOKEN, $pushOverFields[Request::RECIPIENT]);
        $this->assertEquals('testMessage', $pushOverFields[Request::MESSAGE]);
        $this->assertEquals(Request::PRIORITY_NORMAL, $pushOverFields[Request::PRIORITY]);
    }

    public function testGetPushoverFieldsWithNormalPriorityAndExpire()
    {
        $message = new Message(MockClient::VALID_RECIPIENT_TOKEN, 'testMessage');
        $message->setPriority(Request::PRIORITY_NORMAL);
        $message->setExpire(12345);

        $pushOverFields = $message->getPushoverFields();

        $this->assertInternalType('array', $pushOverFields);

        // the array should contain 3 fields, because expire is ignored if priority is not emergency
        $this->assertCount(3, $pushOverFields);
        $this->assertArrayHasKey(Request::RECIPIENT, $pushOverFields);
        $this->assertArrayHasKey(Request::MESSAGE, $pushOverFields);
        $this->assertArrayHasKey(Request::PRIORITY, $pushOverFields);

        // the fields should match the values we set
        $this->assertEquals(MockClient::VALID_RECIPIENT_TOKEN, $pushOverFields[Request::RECIPIENT]);
        $this->assertEquals('testMessage', $pushOverFields[Request::MESSAGE]);
        $this->assertEquals(Request::PRIORITY_NORMAL, $pushOverFields[Request::PRIORITY]);
    }

    public function testGetPushoverFieldsWithNormalPriorityAndRetry()
    {
        $message = new Message(MockClient::VALID_RECIPIENT_TOKEN, 'testMessage');
        $message->setPriority(Request::PRIORITY_NORMAL);
        $message->setRetry(12345);

        $pushOverFields = $message->getPushoverFields();

        $this->assertInternalType('array', $pushOverFields);

        // the array should contain 3 fields, because retry is ignored if priority is not emergency
        $this->assertCount(3, $pushOverFields);
        $this->assertArrayHasKey(Request::RECIPIENT, $pushOverFields);
        $this->assertArrayHasKey(Request::MESSAGE, $pushOverFields);
        $this->assertArrayHasKey(Request::PRIORITY, $pushOverFields);

        // the fields should match the values we set
        $this->assertEquals(MockClient::VALID_RECIPIENT_TOKEN, $pushOverFields[Request::RECIPIENT]);
        $this->assertEquals('testMessage', $pushOverFields[Request::MESSAGE]);
        $this->assertEquals(Request::PRIORITY_NORMAL, $pushOverFields[Request::PRIORITY]);
    }

    public function testGetPushoverFieldsWithNormalPriorityAndCallbackUrl()
    {
        $message = new Message(MockClient::VALID_RECIPIENT_TOKEN, 'testMessage');
        $message->setPriority(Request::PRIORITY_NORMAL);
        $message->setCallbackUrl('testCallbackUrl');

        $pushOverFields = $message->getPushoverFields();

        $this->assertInternalType('array', $pushOverFields);

        // the array should contain 3 fields, because the callback URL is ignored if priority is not emergency
        $this->assertCount(3, $pushOverFields);
        $this->assertArrayHasKey(Request::RECIPIENT, $pushOverFields);
        $this->assertArrayHasKey(Request::MESSAGE, $pushOverFields);
        $this->assertArrayHasKey(Request::PRIORITY, $pushOverFields);

        // the fields should match the values we set
        $this->assertEquals(MockClient::VALID_RECIPIENT_TOKEN, $pushOverFields[Request::RECIPIENT]);
        $this->assertEquals('testMessage', $pushOverFields[Request::MESSAGE]);
        $this->assertEquals(Request::PRIORITY_NORMAL, $pushOverFields[Request::PRIORITY]);
    }

    public function testGetPushoverFieldsWithEmergencyPriority()
    {
        $message = new Message(MockClient::VALID_RECIPIENT_TOKEN, 'testMessage');
        $message->setPriority(Request::PRIORITY_EMERGENCY);

        $pushOverFields = $message->getPushoverFields();

        $this->assertInternalType('array', $pushOverFields);

        // the array should contain 5 fields, because for emergency messages expire and retry are required:
        $this->assertCount(5, $pushOverFields);
        $this->assertArrayHasKey(Request::RECIPIENT, $pushOverFields);
        $this->assertArrayHasKey(Request::MESSAGE, $pushOverFields);
        $this->assertArrayHasKey(Request::PRIORITY, $pushOverFields);
        $this->assertArrayHasKey(Request::EXPIRE, $pushOverFields);
        $this->assertArrayHasKey(Request::RETRY, $pushOverFields);

        // the fields should match the values we set
        $this->assertEquals(MockClient::VALID_RECIPIENT_TOKEN, $pushOverFields[Request::RECIPIENT]);
        $this->assertEquals('testMessage', $pushOverFields[Request::MESSAGE]);
        $this->assertEquals(Request::PRIORITY_EMERGENCY, $pushOverFields[Request::PRIORITY]);

        // expire was not set, so it should be the default of 3600 seconds
        $this->assertEquals(3600, $pushOverFields[Request::EXPIRE]);

        // retry was not set, so it should be the default of 30 seconds
        $this->assertEquals(30, $pushOverFields[Request::RETRY]);
    }

    public function testGetPushoverFieldsWithEmergencyPriorityAndExpire()
    {
        $message = new Message(MockClient::VALID_RECIPIENT_TOKEN, 'testMessage');
        $message->setPriority(Request::PRIORITY_EMERGENCY);
        $message->setExpire(12345);

        $pushOverFields = $message->getPushoverFields();

        $this->assertInternalType('array', $pushOverFields);

        // the array should contain 5 fields, because for emergency messages expire and retry are required:
        $this->assertCount(5, $pushOverFields);
        $this->assertArrayHasKey(Request::RECIPIENT, $pushOverFields);
        $this->assertArrayHasKey(Request::MESSAGE, $pushOverFields);
        $this->assertArrayHasKey(Request::PRIORITY, $pushOverFields);
        $this->assertArrayHasKey(Request::EXPIRE, $pushOverFields);
        $this->assertArrayHasKey(Request::RETRY, $pushOverFields);

        // the fields should match the values we set
        $this->assertEquals(MockClient::VALID_RECIPIENT_TOKEN, $pushOverFields[Request::RECIPIENT]);
        $this->assertEquals('testMessage', $pushOverFields[Request::MESSAGE]);
        $this->assertEquals(Request::PRIORITY_EMERGENCY, $pushOverFields[Request::PRIORITY]);
        $this->assertEquals(12345, $pushOverFields[Request::EXPIRE]);

        // retry was not set, so it should be the default of 30 seconds
        $this->assertEquals(30, $pushOverFields[Request::RETRY]);
    }

    public function testGetPushoverFieldsWithEmergencyPriorityAndRetry()
    {
        $message = new Message(MockClient::VALID_RECIPIENT_TOKEN, 'testMessage');
        $message->setPriority(Request::PRIORITY_EMERGENCY);
        $message->setRetry(12345);

        $pushOverFields = $message->getPushoverFields();

        $this->assertInternalType('array', $pushOverFields);

        // the array should contain 5 fields, because for emergency messages expire and retry are required:
        $this->assertCount(5, $pushOverFields);
        $this->assertArrayHasKey(Request::RECIPIENT, $pushOverFields);
        $this->assertArrayHasKey(Request::MESSAGE, $pushOverFields);
        $this->assertArrayHasKey(Request::PRIORITY, $pushOverFields);
        $this->assertArrayHasKey(Request::EXPIRE, $pushOverFields);
        $this->assertArrayHasKey(Request::RETRY, $pushOverFields);

        // the fields should match the values we set
        $this->assertEquals(MockClient::VALID_RECIPIENT_TOKEN, $pushOverFields[Request::RECIPIENT]);
        $this->assertEquals('testMessage', $pushOverFields[Request::MESSAGE]);
        $this->assertEquals(Request::PRIORITY_EMERGENCY, $pushOverFields[Request::PRIORITY]);
        $this->assertEquals(12345, $pushOverFields[Request::RETRY]);

        // expire was not set, so it should be the default of 3600 seconds
        $this->assertEquals(3600, $pushOverFields[Request::EXPIRE]);
    }

    public function testGetPushoverFieldsWithEmergencyPriorityAndCallbackUrl()
    {
        $message = new Message(MockClient::VALID_RECIPIENT_TOKEN, 'testMessage');
        $message->setPriority(Request::PRIORITY_EMERGENCY);
        $message->setCallbackUrl('testCallbackUrl');

        $pushOverFields = $message->getPushoverFields();

        $this->assertInternalType('array', $pushOverFields);

        // the array should contain 6 fields, because for emergency messages expire and retry are required:
        $this->assertCount(6, $pushOverFields);
        $this->assertArrayHasKey(Request::RECIPIENT, $pushOverFields);
        $this->assertArrayHasKey(Request::MESSAGE, $pushOverFields);
        $this->assertArrayHasKey(Request::PRIORITY, $pushOverFields);
        $this->assertArrayHasKey(Request::EXPIRE, $pushOverFields);
        $this->assertArrayHasKey(Request::RETRY, $pushOverFields);
        $this->assertArrayHasKey(Request::CALLBACK, $pushOverFields);

        // the fields should match the values we set
        $this->assertEquals(MockClient::VALID_RECIPIENT_TOKEN, $pushOverFields[Request::RECIPIENT]);
        $this->assertEquals('testMessage', $pushOverFields[Request::MESSAGE]);
        $this->assertEquals(Request::PRIORITY_EMERGENCY, $pushOverFields[Request::PRIORITY]);
        $this->assertEquals('testCallbackUrl', $pushOverFields[Request::CALLBACK]);

        // expire was not set, so it should be the default of 3600 seconds
        $this->assertEquals(3600, $pushOverFields[Request::EXPIRE]);

        // retry was not set, so it should be the default of 30 seconds
        $this->assertEquals(30, $pushOverFields[Request::RETRY]);
    }

    public function testGetPushoverFieldsWithTimestamp()
    {
        $message = new Message(MockClient::VALID_RECIPIENT_TOKEN, 'testMessage');
        $message->setTimestamp(1405711800);

        $pushOverFields = $message->getPushoverFields();

        $this->assertInternalType('array', $pushOverFields);

        // the array should contain 3 fields:
        $this->assertCount(3, $pushOverFields);
        $this->assertArrayHasKey(Request::RECIPIENT, $pushOverFields);
        $this->assertArrayHasKey(Request::MESSAGE, $pushOverFields);
        $this->assertArrayHasKey(Request::TIMESTAMP, $pushOverFields);

        // the fields should match the values we set
        $this->assertEquals(MockClient::VALID_RECIPIENT_TOKEN, $pushOverFields[Request::RECIPIENT]);
        $this->assertEquals('testMessage', $pushOverFields[Request::MESSAGE]);
        $this->assertEquals(1405711800, $pushOverFields[Request::TIMESTAMP]);
    }

    public function testGetPushoverFieldsWithSound()
    {
        $message = new Message(MockClient::VALID_RECIPIENT_TOKEN, 'testMessage');
        $message->setSound(Sound::SPACE_ALARM);

        $pushOverFields = $message->getPushoverFields();

        $this->assertInternalType('array', $pushOverFields);

        // the array should contain 3 fields:
        $this->assertCount(3, $pushOverFields);
        $this->assertArrayHasKey(Request::RECIPIENT, $pushOverFields);
        $this->assertArrayHasKey(Request::MESSAGE, $pushOverFields);
        $this->assertArrayHasKey(Request::SOUND, $pushOverFields);

        // the fields should match the values we set
        $this->assertEquals(MockClient::VALID_RECIPIENT_TOKEN, $pushOverFields[Request::RECIPIENT]);
        $this->assertEquals('testMessage', $pushOverFields[Request::MESSAGE]);
        $this->assertEquals(Sound::SPACE_ALARM, $pushOverFields[Request::SOUND]);
    }

    public function testGetPushoverFieldsWithUserDefaultSound()
    {
        $message = new Message(MockClient::VALID_RECIPIENT_TOKEN, 'testMessage');

        // the user default sound is the default, so set it to something else first to make sure it's changed
        $message->setSound(Sound::SPACE_ALARM);
        $message->setSound(Sound::USER_DEFAULT);

        $pushOverFields = $message->getPushoverFields();

        $this->assertInternalType('array', $pushOverFields);

        // the array should contain 2 fields, because the default sound is not submitted to Pushover:
        $this->assertCount(2, $pushOverFields);
        $this->assertArrayHasKey(Request::RECIPIENT, $pushOverFields);
        $this->assertArrayHasKey(Request::MESSAGE, $pushOverFields);

        // the fields should match the values we set
        $this->assertEquals(MockClient::VALID_RECIPIENT_TOKEN, $pushOverFields[Request::RECIPIENT]);
        $this->assertEquals('testMessage', $pushOverFields[Request::MESSAGE]);
    }
}