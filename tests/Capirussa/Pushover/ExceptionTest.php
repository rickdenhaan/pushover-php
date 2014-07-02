<?php
require_once(dirname(__FILE__) . '/../../init.php');

use Capirussa\Pushover\Exception;

/**
 * Tests Capirussa\Pushover\Exception
 *
 */
class ExceptionTest extends PHPUnit_Framework_TestCase
{
    /**
     * @expectedException Capirussa\Pushover\Exception
     * @expectedExceptionMessage testException
     */
    public function testException()
    {
        throw new Exception('testException');
    }
}