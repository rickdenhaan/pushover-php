<?php
require_once(dirname(__FILE__) . '/../../init.php');

use Capirussa\Pushover\Request;

/**
 * Tests Capirussa\Pushover\Request
 *
 */
class RequestTest extends PHPUnit_Framework_TestCase
{
    const NUMBERS    = '0123456789';
    const LOWERCASE  = 'abcdefghijklmnopqrstuvwxyz';
    const UPPERCASE  = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    const UNDERSCORE = '_';
    const SYMBOLS    = '`,./~,./;\'\\:"|[]{}§±-=_+!@#$%^&*()™';

    public function testReceiptTokenExpressions()
    {
        $validCharacters    = self::NUMBERS . self::LOWERCASE . self::UPPERCASE;
        $minimumValidLength = 30;
        $maximumValidLength = 30;

        $validTest = sprintf('/^([%1$s]{%2$d,%3$d})$/', $validCharacters, $minimumValidLength, $maximumValidLength);

        $testStringCharacters = self::NUMBERS . self::LOWERCASE . self::UPPERCASE . self::SYMBOLS;

        for ($i=0; $i<1000; $i++) {
            $testString = '';
            $testStringLength = mt_rand(1, 100);

            while (strlen($testString) < $testStringLength) {
                $testString .= substr($testStringCharacters, (mt_rand(0, strlen($testStringCharacters) - 1)), 1);
            }

            if (preg_match($validTest, $testString)) {
                $this->assertTrue((bool)preg_match(Request::RECEIPT_REGEXP, $testString), $testString);
            } else {
                $this->assertFalse((bool)preg_match(Request::RECEIPT_REGEXP, $testString), $testString);
            }
        }
    }

    public function testRecipientTokenExpressions()
    {
        $validCharacters    = self::NUMBERS . self::LOWERCASE . self::UPPERCASE;
        $minimumValidLength = 30;
        $maximumValidLength = 30;

        $validTest = sprintf('/^([%1$s]{%2$d,%3$d})$/', $validCharacters, $minimumValidLength, $maximumValidLength);

        $testStringCharacters = self::NUMBERS . self::LOWERCASE . self::UPPERCASE . self::SYMBOLS;

        for ($i=0; $i<1000; $i++) {
            $testString = '';
            $testStringLength = mt_rand(1, 100);

            while (strlen($testString) < $testStringLength) {
                $testString .= substr($testStringCharacters, (mt_rand(0, strlen($testStringCharacters) - 1)), 1);
            }

            if (preg_match($validTest, $testString)) {
                $this->assertTrue((bool)preg_match(Request::RECIPIENT_REGEXP, $testString), $testString);
            } else {
                $this->assertFalse((bool)preg_match(Request::RECIPIENT_REGEXP, $testString), $testString);
            }
        }
    }

    public function testDeviceTokenExpressions()
    {
        $validCharacters    = self::NUMBERS . self::LOWERCASE . self::UPPERCASE . self::UNDERSCORE;
        $minimumValidLength = 1;
        $maximumValidLength = 25;

        $validTest = sprintf('/^([%1$s]{%2$d,%3$d})$/', $validCharacters, $minimumValidLength, $maximumValidLength);

        $testStringCharacters = self::NUMBERS . self::LOWERCASE . self::UPPERCASE . self::SYMBOLS;

        for ($i=0; $i<1000; $i++) {
            $testString = '';
            $testStringLength = mt_rand(1, 100);

            while (strlen($testString) < $testStringLength) {
                $testString .= substr($testStringCharacters, (mt_rand(0, strlen($testStringCharacters) - 1)), 1);
            }

            if (preg_match($validTest, $testString)) {
                $this->assertTrue((bool)preg_match(Request::DEVICE_REGEXP, $testString), $testString);
            } else {
                $this->assertFalse((bool)preg_match(Request::DEVICE_REGEXP, $testString), $testString);
            }
        }
    }
}