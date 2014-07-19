<?php
require_once(dirname(__FILE__) . '/../../init.php');

use Capirussa\Pushover\Receipt;

/**
 * Tests Capirussa\Pushover\Receipt
 *
 */
class ReceiptTest extends PHPUnit_Framework_TestCase
{
    public function testConstructWithoutParameters()
    {
        $receipt = new Receipt();

        $this->assertInstanceOf('Capirussa\\Pushover\\Receipt', $receipt);

        // the receipt should not have a receipt token set
        $this->assertNull($this->getObjectAttribute($receipt, 'receipt'));
    }

    public function testConstructWithReceipt()
    {
        $receipt = new Receipt(MockClient::VALID_RECEIPT_TOKEN);

        // the receipt should now have this token set as the receipt
        $this->assertNotNull($this->getObjectAttribute($receipt, 'receipt'));
        $this->assertEquals(MockClient::VALID_RECEIPT_TOKEN, $this->getObjectAttribute($receipt, 'receipt'));
    }

    /**
     * @expectedException PHPUnit_Framework_Error_Warning
     */
    public function testSetReceiptWithoutReceipt()
    {
        $receipt = new Receipt();

        /** @noinspection PhpParamsInspection (this is intentional) */
        $receipt->setReceipt();
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Invalid receipt
     */
    public function testSetReceiptWithInvalidReceipt()
    {
        $receipt = new Receipt();

        $receipt->setReceipt('invalid');
    }

    public function testSetReceiptWithValidButIncorrectReceipt()
    {
        $receipt = new Receipt();

        $receipt->setReceipt(MockClient::INCORRECT_RECEIPT_TOKEN);

        $this->assertNotNull($this->getObjectAttribute($receipt, 'receipt'));
        $this->assertEquals(MockClient::INCORRECT_RECEIPT_TOKEN, $this->getObjectAttribute($receipt, 'receipt'));
    }

    public function testSetReceiptWithValidReceipt()
    {
        $receipt = new Receipt();

        $receipt->setReceipt(MockClient::VALID_RECEIPT_TOKEN);

        $this->assertNotNull($this->getObjectAttribute($receipt, 'receipt'));
        $this->assertEquals(MockClient::VALID_RECEIPT_TOKEN, $this->getObjectAttribute($receipt, 'receipt'));
    }

    public function testGetReceiptBeforeSet()
    {
        $receipt = new Receipt();

        $this->assertNull($receipt->getReceipt());
    }

    public function testGetReceiptAfterSet()
    {
        $receipt = new Receipt();

        $receipt->setReceipt(MockClient::VALID_RECEIPT_TOKEN);

        $this->assertNotNull($receipt->getReceipt());
        $this->assertEquals(MockClient::VALID_RECEIPT_TOKEN, $receipt->getReceipt());
    }

    public function testGetReceiptAfterConstruct()
    {
        $receipt = new Receipt(MockClient::VALID_RECEIPT_TOKEN);

        $this->assertNotNull($receipt->getReceipt());
        $this->assertEquals(MockClient::VALID_RECEIPT_TOKEN, $receipt->getReceipt());
    }

    public function testGetPushoverFieldsWithEmptyReceipt()
    {
        $message = new Receipt();

        $pushOverFields = $message->getPushoverFields();

        $this->assertInternalType('array', $pushOverFields);

        // the array should be empty, because receipts are not sent using a POST request but with a GET request
        $this->assertEmpty($pushOverFields);
    }

    public function testGetPushoverFieldsWithFilledReceipt()
    {
        $receipt = new Receipt(MockClient::VALID_RECEIPT_TOKEN);

        $pushOverFields = $receipt->getPushoverFields();

        $this->assertInternalType('array', $pushOverFields);

        // the array should be empty, because receipts are not sent using a POST request but with a GET request
        $this->assertEmpty($pushOverFields);
    }
}