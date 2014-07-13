<?php
require_once(dirname(__FILE__) . '/../../init.php');

use Capirussa\Pushover\Message;

/**
 * Tests Capirussa\Pushover\Message
 *
 */
class MessageTest extends PHPUnit_Framework_TestCase
{
    /**
     * @expectedException Capirussa\Pushover\Exception
     * @expectedExceptionMessage testException
     */
    public function testException()
    {
        throw new \Capirussa\Pushover\Exception('testException');
    }
}