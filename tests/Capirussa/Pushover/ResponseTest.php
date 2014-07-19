<?php
require_once(dirname(__FILE__) . '/../../init.php');

use Capirussa\Pushover\Response;

/**
 * Tests Capirussa\Pushover\Response
 *
 */
class ResponseTest extends PHPUnit_Framework_TestCase
{
    /**
     * @expectedException PHPUnit_Framework_Error_Warning
     */
    public function testConstructWithoutParameters()
    {
        /** @noinspection PhpParamsInspection (this is intentional) */
        new Response();
    }

    public function testConstructWithEmptyResponse()
    {
        $response = new Response(
            ''
        );

        // all data should be null, because we did not provide any parseable data
        $reflectionResponse = new ReflectionObject($response);
        foreach ($reflectionResponse->getProperties() as $property) {
            $this->assertNull($this->getObjectAttribute($response, $property->getName()), $property->getName());

            $getter = 'get' . strtoupper(substr($property->getName(), 0, 1)) . substr($property->getName(), 1);
            $this->assertNull($response->{$getter}(), $property->getName());
        }
    }

    /**
     * @expectedException Capirussa\Pushover\Exception
     * @expectedExceptionMessage temporary server issues
     */
    public function testInternalServiceErrorError()
    {
        new Response(
            'HTTP/1.1 500 Internal Service Error'
        );
    }

    /**
     * @expectedException Capirussa\Pushover\Exception
     * @expectedExceptionMessage try again after the rate limit has been reset
     */
    public function testQuotaExceededErrorWithoutResetDate()
    {
        new Response(
            'HTTP/1.1 429 Quota Exceeded'
        );
    }

    /**
     * @expectedException Capirussa\Pushover\Exception
     * @expectedExceptionMessage try again after Fri, 18 Jul 2014 21:30:00 +0200
     */
    public function testQuotaExceededErrorWithResetDate()
    {
        new Response(
            'HTTP/1.1 429 Quota Exceeded' . "\r\n" .
            'X-Limit-App-Reset: 1405711800'
        );
    }

    /**
     * @expectedException Capirussa\Pushover\Exception
     * @expectedExceptionMessage an unknown error
     */
    public function testApiErrorWithoutErrors()
    {
        new Response(
            'HTTP/1.1 404 Not Found'
        );
    }

    /**
     * @expectedException Capirussa\Pushover\Exception
     * @expectedExceptionMessage testError1
     */
    public function testApiErrorWithOneError()
    {
        new Response(
            'HTTP/1.1 400 Bad Request' . "\r\n" .
            '{"errors":["testError1"]}'
        );
    }

    /**
     * @expectedException Capirussa\Pushover\Exception
     * @expectedExceptionMessage testError1, testError2, testError3
     */
    public function testApiErrorWithMultipleErrors()
    {
        new Response(
            'HTTP/1.1 400 Bad Request' . "\r\n" .
            '{"errors":["testError1","testError2","testError3"]}'
        );
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage fake exception, all is well
     */
    public function testParseInvalidHttpHeader()
    {
        new Response(
            'HTTP/1.1 200 OK' . "\r\n" .
            'Content-Type:' . "\r\n" . // invalid because there is no content type specified, should throw a warning if not properly handler
            'Connection: Close'
        );

        throw new \Exception('This is a fake exception, all is well otherwise you would have gotten a PHPUnit_Framework_Error_Warning exception');
    }

    public function testParseAppRateLimit()
    {
        $response = new Response(
            'X-Limit-App-Limit: 12345'
        );

        $this->assertNotNull($this->getObjectAttribute($response, 'appLimit'));
        $this->assertEquals(12345, $this->getObjectAttribute($response, 'appLimit'));

        $this->assertNotNull($response->getAppLimit());
        $this->assertEquals(12345, $response->getAppLimit());
    }

    public function testParseAppRemainingLimit()
    {
        $response = new Response(
            'X-Limit-App-Remaining: 12345'
        );

        $this->assertNotNull($this->getObjectAttribute($response, 'appRemaining'));
        $this->assertEquals(12345, $this->getObjectAttribute($response, 'appRemaining'));

        $this->assertNotNull($response->getAppRemaining());
        $this->assertEquals(12345, $response->getAppRemaining());
    }

    public function testParseAppRateLimitResetTimestamp()
    {
        $response = new Response(
            'X-Limit-App-Reset: 1405711800'
        );

        $this->assertNotNull($this->getObjectAttribute($response, 'appReset'));
        $this->assertInstanceOf('DateTime', $this->getObjectAttribute($response, 'appReset'));
        $this->assertEquals(1405711800, $this->getObjectAttribute($response, 'appReset')->getTimestamp());

        $this->assertNotNull($response->getAppReset());
        $this->assertInstanceOf('DateTime', $response->getAppReset());
        $this->assertEquals(1405711800, $response->getAppReset()->getTimestamp());
    }

    public function testParseInvalidJsonBody()
    {
        $response = new Response(
            '{invalidJson'
        );

        // all properties should still be null
        $reflectionResponse = new ReflectionObject($response);
        foreach ($reflectionResponse->getProperties() as $property) {
            $this->assertNull($this->getObjectAttribute($response, $property->getName()), $property->getName());

            $getter = 'get' . strtoupper(substr($property->getName(), 0, 1)) . substr($property->getName(), 1);
            $this->assertNull($response->{$getter}(), $property->getName());
        }
    }

    public function testParseStatusSuccessFromBody()
    {
        $response = new Response(
            '{"status":1}'
        );

        $this->assertNotNull($this->getObjectAttribute($response, 'status'));
        $this->assertEquals(Response::STATUS_SUCCESS, $this->getObjectAttribute($response, 'status'));

        $this->assertNotNull($response->getStatus());
        $this->assertEquals(Response::STATUS_SUCCESS, $response->getStatus());
    }

    public function testParseStatusFailureFromBody()
    {
        $response = new Response(
            '{"status":0}'
        );

        $this->assertNotNull($this->getObjectAttribute($response, 'status'));
        $this->assertEquals(Response::STATUS_FAILURE, $this->getObjectAttribute($response, 'status'));

        $this->assertNotNull($response->getStatus());
        $this->assertEquals(Response::STATUS_FAILURE, $response->getStatus());
    }

    public function testParseNoGroupFromBody()
    {
        $response = new Response(
            '{"group":0}'
        );

        $this->assertNull($this->getObjectAttribute($response, 'group'));
        $this->assertNull($response->getGroup());
    }

    public function testParseGroupFromBody()
    {
        $response = new Response(
            '{"group":"' . MockClient::VALID_RECIPIENT_TOKEN . '"}'
        );

        $this->assertNotNull($this->getObjectAttribute($response, 'group'));
        $this->assertEquals(MockClient::VALID_RECIPIENT_TOKEN, $this->getObjectAttribute($response, 'group'));

        $this->assertNotNull($response->getGroup());
        $this->assertEquals(MockClient::VALID_RECIPIENT_TOKEN, $response->getGroup());
    }

    public function testParseDevicesFromBody()
    {
        $response = new Response(
            '{"devices":["iphone","ipad","android"]}'
        );

        $this->assertNotNull($this->getObjectAttribute($response, 'devices'));
        $this->assertInternalType('array', $this->getObjectAttribute($response, 'devices'));
        $this->assertCount(3, $this->getObjectAttribute($response, 'devices'));
        $this->assertContains('iphone', $this->getObjectAttribute($response, 'devices'));
        $this->assertContains('ipad', $this->getObjectAttribute($response, 'devices'));
        $this->assertContains('android', $this->getObjectAttribute($response, 'devices'));

        $this->assertNotNull($response->getDevices());
        $this->assertInternalType('array', $response->getDevices());
        $this->assertCount(3, $response->getDevices());
        $this->assertContains('iphone', $response->getDevices());
        $this->assertContains('ipad', $response->getDevices());
        $this->assertContains('android', $response->getDevices());
    }

    public function testParseRequestFromBody()
    {
        $response = new Response(
            '{"request":"' . MockClient::VALID_RECEIPT_TOKEN . '"}'
        );

        $this->assertNotNull($this->getObjectAttribute($response, 'request'));
        $this->assertEquals(MockClient::VALID_RECEIPT_TOKEN, $this->getObjectAttribute($response, 'request'));

        $this->assertNotNull($response->getRequest());
        $this->assertEquals(MockClient::VALID_RECEIPT_TOKEN, $response->getRequest());
    }

    public function testParseErrorsFromBody()
    {
        $response = new Response(
            '{"errors":["testError1","testError2","testError3"]}'
        );

        $this->assertNotNull($this->getObjectAttribute($response, 'errors'));
        $this->assertInternalType('array', $this->getObjectAttribute($response, 'errors'));
        $this->assertCount(3, $this->getObjectAttribute($response, 'errors'));
        $this->assertContains('testError1', $this->getObjectAttribute($response, 'errors'));
        $this->assertContains('testError2', $this->getObjectAttribute($response, 'errors'));
        $this->assertContains('testError3', $this->getObjectAttribute($response, 'errors'));

        $this->assertNotNull($response->getErrors());
        $this->assertInternalType('array', $response->getErrors());
        $this->assertCount(3, $response->getErrors());
        $this->assertContains('testError1', $response->getErrors());
        $this->assertContains('testError2', $response->getErrors());
        $this->assertContains('testError3', $response->getErrors());
    }

    public function testParseNotAcknowledgedFromBody()
    {
        $response = new Response(
            '{"acknowledged":0}'
        );

        $this->assertNotNull($this->getObjectAttribute($response, 'acknowledged'));
        $this->assertEquals(Response::ACKNOWLEDGED_NO, $this->getObjectAttribute($response, 'acknowledged'));

        $this->assertNotNull($response->getAcknowledged());
        $this->assertEquals(Response::ACKNOWLEDGED_NO, $response->getAcknowledged());
    }

    public function testParseAcknowledgedFromBody()
    {
        $response = new Response(
            '{"acknowledged":1}'
        );

        $this->assertNotNull($this->getObjectAttribute($response, 'acknowledged'));
        $this->assertEquals(Response::ACKNOWLEDGED_YES, $this->getObjectAttribute($response, 'acknowledged'));

        $this->assertNotNull($response->getAcknowledged());
        $this->assertEquals(Response::ACKNOWLEDGED_YES, $response->getAcknowledged());
    }

    public function testParseAcknowledgedAtFromBody()
    {
        $response = new Response(
            '{"acknowledged_at":1405711800}'
        );

        $this->assertNotNull($this->getObjectAttribute($response, 'acknowledgedAt'));
        $this->assertInstanceOf('DateTime', $this->getObjectAttribute($response, 'acknowledgedAt'));
        $this->assertEquals(1405711800, $this->getObjectAttribute($response, 'acknowledgedAt')->getTimestamp());

        $this->assertNotNull($response->getAcknowledgedAt());
        $this->assertInstanceOf('DateTime', $response->getAcknowledgedAt());
        $this->assertEquals(1405711800, $response->getAcknowledgedAt()->getTimestamp());
    }

    public function testParseAcknowledgedByFromBody()
    {
        $response = new Response(
            '{"acknowledged_by":"' . MockClient::VALID_RECIPIENT_TOKEN . '"}'
        );

        $this->assertNotNull($this->getObjectAttribute($response, 'acknowledgedBy'));
        $this->assertEquals(MockClient::VALID_RECIPIENT_TOKEN, $this->getObjectAttribute($response, 'acknowledgedBy'));

        $this->assertNotNull($response->getAcknowledgedBy());
        $this->assertEquals(MockClient::VALID_RECIPIENT_TOKEN, $response->getAcknowledgedBy());
    }

    public function testParseLastDeliveredAtFromBody()
    {
        $response = new Response(
            '{"last_delivered_at":1405711800}'
        );

        $this->assertNotNull($this->getObjectAttribute($response, 'lastDeliveredAt'));
        $this->assertInstanceOf('DateTime', $this->getObjectAttribute($response, 'lastDeliveredAt'));
        $this->assertEquals(1405711800, $this->getObjectAttribute($response, 'lastDeliveredAt')->getTimestamp());

        $this->assertNotNull($response->getLastDeliveredAt());
        $this->assertInstanceOf('DateTime', $response->getLastDeliveredAt());
        $this->assertEquals(1405711800, $response->getLastDeliveredAt()->getTimestamp());
    }

    public function testParseNotExpiredFromBody()
    {
        $response = new Response(
            '{"expired":0}'
        );

        $this->assertNotNull($this->getObjectAttribute($response, 'expired'));
        $this->assertEquals(Response::EXPIRED_NO, $this->getObjectAttribute($response, 'expired'));

        $this->assertNotNull($response->getExpired());
        $this->assertEquals(Response::EXPIRED_NO, $response->getExpired());
    }

    public function testParseExpiredFromBody()
    {
        $response = new Response(
            '{"expired":1}'
        );

        $this->assertNotNull($this->getObjectAttribute($response, 'expired'));
        $this->assertEquals(Response::EXPIRED_YES, $this->getObjectAttribute($response, 'expired'));

        $this->assertNotNull($response->getExpired());
        $this->assertEquals(Response::EXPIRED_YES, $response->getExpired());
    }

    public function testParseExpiresAtFromBody()
    {
        $response = new Response(
            '{"expires_at":1405711800}'
        );

        $this->assertNotNull($this->getObjectAttribute($response, 'expiresAt'));
        $this->assertInstanceOf('DateTime', $this->getObjectAttribute($response, 'expiresAt'));
        $this->assertEquals(1405711800, $this->getObjectAttribute($response, 'expiresAt')->getTimestamp());

        $this->assertNotNull($response->getExpiresAt());
        $this->assertInstanceOf('DateTime', $response->getExpiresAt());
        $this->assertEquals(1405711800, $response->getExpiresAt()->getTimestamp());
    }

    public function testParseNotCalledBackFromBody()
    {
        $response = new Response(
            '{"called_back":0}'
        );

        $this->assertNotNull($this->getObjectAttribute($response, 'calledBack'));
        $this->assertEquals(Response::CALLED_BACK_NO, $this->getObjectAttribute($response, 'calledBack'));

        $this->assertNotNull($response->getCalledBack());
        $this->assertEquals(Response::CALLED_BACK_NO, $response->getCalledBack());
    }

    public function testParseCalledBackFromBody()
    {
        $response = new Response(
            '{"called_back":1}'
        );

        $this->assertNotNull($this->getObjectAttribute($response, 'calledBack'));
        $this->assertEquals(Response::CALLED_BACK_YES, $this->getObjectAttribute($response, 'calledBack'));

        $this->assertNotNull($response->getCalledBack());
        $this->assertEquals(Response::CALLED_BACK_YES, $response->getCalledBack());
    }

    public function testParseCalledBackAtFromBody()
    {
        $response = new Response(
            '{"called_back_at":1405711800}'
        );

        $this->assertNotNull($this->getObjectAttribute($response, 'calledBackAt'));
        $this->assertInstanceOf('DateTime', $this->getObjectAttribute($response, 'calledBackAt'));
        $this->assertEquals(1405711800, $this->getObjectAttribute($response, 'calledBackAt')->getTimestamp());

        $this->assertNotNull($response->getCalledBackAt());
        $this->assertInstanceOf('DateTime', $response->getCalledBackAt());
        $this->assertEquals(1405711800, $response->getCalledBackAt()->getTimestamp());
    }

    public function testParseReceiptFromBody()
    {
        $response = new Response(
            '{"receipt":"' . MockClient::VALID_RECEIPT_TOKEN . '"}'
        );

        $this->assertNotNull($this->getObjectAttribute($response, 'receipt'));
        $this->assertEquals(MockClient::VALID_RECEIPT_TOKEN, $this->getObjectAttribute($response, 'receipt'));

        $this->assertNotNull($response->getReceipt());
        $this->assertEquals(MockClient::VALID_RECEIPT_TOKEN, $response->getReceipt());
    }
}