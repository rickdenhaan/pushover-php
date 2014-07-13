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
        if (function_exists('unittest_log')) unittest_log('ExceptionTest::testException()');
        if (function_exists('unittest_log')) unittest_log('Throwing Exception');
        throw new Exception('testException');
    }
}