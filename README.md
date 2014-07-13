Pushover PHP client
===================

[![Build Status](https://travis-ci.org/rickdenhaan/pushover-php.png?branch=master)](https://travis-ci.org/rickdenhaan/pushover-php)
[![Coverage Status](https://coveralls.io/repos/rickdenhaan/pushover-php/badge.png?branch=master)](https://coveralls.io/r/rickdenhaan/pushover-php)

This simple PHP client uses the Pushover REST API to send push notifications to mobile devices. For more information about Pushover, see http://pushover.net


Usage
-----

```php
use Capirussa\Pushover;

try {
    $client = Pushover\Client::init($applicationToken);
    $message = new Pushover\Message('userKey', 'message');

    $client->send($message);
} catch (Pushover\Exception $exception) {
    // something went wrong, fix it and try again!
}
```


Pushover\Client
---------------

You need to supply your application's token when initializing the Pushover\Client. You can find this token in your online dashboard when you log in to Pushover.

On some servers, usually localhost development servers, you might encounter SSL errors. To skip the SSL validation, call $client->disableSslVerification().

To set or retrieve the application token, you can call $client->setToken() or $client->getToken(). To completely reset the Client, including the SSL verification flag, you can also call $client->init() with an application token.

To send a push notification to a specific user or group, call $client->send() with a Pushover\Message object as parameter. This method will return null by default, however, for messages with the PRIORITY_EMERGENCY priority, it will return the message receipt token. You can use this token to poll the Pushover API and check whether the message has been acknowledged.

To verify with the Pushover server whether a given user or group is valid, call $client->validate() with a Pushover\Validate object as parameter.

To check whether a message you sent with the Emergency priority has been acknowledged, has expired or whether the callback URL has been requested, call $client->pollReceipt() with a Pushover\Receipt object as parameter.

Finally, you can retrieve a list of all devices for a user by calling $client->getUserDevices() with a user token as parameter.


Pushover\Message
----------------

To send a push notification via Pushover, you need to supply the Client with a Message object. The message has the following properties (all can be set using their respective getters):

* Recipient - the User or Group token to deliver the message to. Must be a 30-character alphanumeric string.
* Message - the message body to send. Must not exceed 512 characters when combined with the optional message title.
* Title - an optional message title to send. Must not exceed 100 characters, and when combined with the message body the total must not exceed 512 characters.
* Device - an optional device identifier to deliver the message to, in case the user has multiple devices and you only want to send this message to one specific device.
* URL - an optional URL to include in the message. Must not be more than 512 characters.
* URL title - an optional title to display in the message instead of the URL itself. Must not be more than 100 characters.
* Priority - the priority for this message. Must be one of the priorities defined as class constants in the Pushover\Request interface. Defaults to PRIORITY_NORMAL
* Timestamp - an optional timestamp for this message. The setter will accept a DateTime object as well as a timestamp.
* Sound - the sound to use for this message (defaults to the recipient user's preference). Must be one of the sounds defined in Pushover\Sound.
* Callback URL - for messages with the priority set to PRIORITY_EMERGENCY, it's possible to supply a callback URL that Pushover will call when the message has been acknowledged.

It is possible to initialize the Pushover\Message object with a recipient and message, as in the Usage example above. This is optional, you can also set these properties using their setters:

```php
$message = new Pushover\Message();
$message->setRecipient('userKey');
$message->setMessage('message');
$message->setSound(Pushover\Sound::SPACE_ALARM);
```


Pushover\Receipt
----------------

The Receipt object must be supplied to the Client to poll whether a Message with PRIORITY_EMERGENCY has been acknowledged or has expired, and whether the callback URL has been called.

The Receipt object only has one property:

* Receipt - the Receipt token that will be polled for its status.

The receipt token can be set either when initializing the object, or using the setter.

```php
$receipt = new Pushover\Receipt();
$receipt->setReceipt('receiptToken');
```

The receipt token must be a 30-character alphanumeric string.


Pushover\Validate
-----------------

To validate whether a user or group token is valid, optionally while also validating whether a specific device belongs to the given user, you must supply the Client with a Validate object. This object has the following properties:

* Recipient - the User or Group token to validate. Must be a 30-character alphanumeric string.
* Device - the specific device to validate.

```php
$validate = new Pushover\Validate();
$validate->setRecipient('userToken');
$validate->setDevice('iphone');
```


Pushover\Response
-----------------

Ordinarily, you never see the response. However, when you're polling the Pushover API the check whether an emergency message has been acknowledged, you might be interested in various properties. I could have chosen to return an array with all relevant properties, but I've chosen instead to simply return the Response object and allow you to retrieve whatever data you want. The Reponse object has the following properties:

* Status - can be one of Response::STATUS_SUCCESS or Response::STATUS_FAILURE.
* Group - used in response to a Validate request, contains the group token.
* Devices - used in response to a Validate request, contains a list of devices for this user.
* Request - contains a unique request identifier for the request that was submitted to Pushover.
* Errors - contains a list of errors (if any) returned by Pushover.
* AppLimit - contains the total number of messages your application is allowed to submit per month.
* AppRemaining - contains the number of messages your application is still allowed to submit this month.
* AppReset - contains a DateTime object that indicates when your application's message quota will be reset.
* Acknowledged - whether the message for this receipt was acknowledged. Can be one of Response::ACKNOWLEDGED_YES or Response::ACKNOWLEDGED_NO.
* AcknowledgedAt - contains a DateTime object that indicates when the message for this receipt was acknowledged.
* AcknowledgedBy - contains the user token for the user who acknowledged the message for this receipt (if any).
* LastDeliveredAt - contains a DateTime object indicating when the message for this receipt was last sent to the user.
* Expired - whether the message for this receipt has expired. Can be one of Response::EXPIRED_YES or Response::EXPIRED_NO.
* ExpiresAt - contains a DateTime object indicating when the message for this receipt will expire (if not expired yet).
* CalledBack - whether the callback URL for the message for this receipt has been called. Can be one of Response::CALLED_BACK_YES or Response::CALLED_BACK_NO.
* CalledBackAt - contains a DateTime object indicating when the callback URL for the message for this request was called.
* Receipt - contains the receipt token for the message that has just been sent.


So that's great, but how do I put this all together?
----------------------------------------------------

Here are a few examples. Let's assume an application token of '123apptoken' and a user token of 'usertoken123'. These are obviously invalid but this is, after all, just an example.

```php
use Capirussa\Pushover;

// initialize the client
$client = new Pushover\Client('123apptoken');

// optional: when running on an ill-configured development server
if ($sslIsBroken) {
    $client->disableSslVerification();
}

// let's verify whether the user token is valid
$validateRequest = new Pushover\Validate('usertoken123');
if (!$client->validate($validateRequest)) {
    echo 'Hey, "usertoken123" is not a valid token!';
} else {
    // let's send a message with regular priority
    $messageRequest = new Pushover\Message('usertoken123');
    $messageRequest->setTitle('Normal priority message');
    $messageRequest->setMessage('This is just a regular message.');

    $client->send($messageRequest);

    // now let's get a list of all devices for this user
    $devices = $client->getUserDevices('usertoken123');

    // now let's send a message with emergency priority to the user's first device
    $emergency = new Pushover\Message('usertoken123');
    $emergency->setDevice($devices[0]);
    $emergency->setPriority(Pushover\Request::PRIORITY_EMERGENCY);
    $emergency->setSound(Pushover\Sound::SIREN);
    $emergency->setTitle('Everything is broken');
    $emergency->setMessage('You must fix it, now!');
    $emergency->setCallbackUrl('http://example.com/fixitnow');

    $receiptToken = $client->send($emergency);

    // now let's see whether the user has acknowledged the message
    $receiptRequest = new Pushover\Receipt($receiptToken);

    $response = $client->pollReceipt($receiptRequest);

    // check whether that request was successful
    if ($response->getStatus() === Pushover\Response::STATUS_SUCCESS) {
        // check whether the request was acknowledged
        $acknowledged = ($response->getAcknowledged() === Pushover\Response::ACKNOWLEDGED_YES);
        if ($acknowledged) {
            // get the user token of the user who acknowledged the request
            $userToken = $response->getAcknowledgedBy();

            // get the DateTime at which the token was acknowledged
            $when = $response->getAcknowledgedAt();

            echo 'The emergency was acknowledged by ' . $userToken . ' on ' . $when->format('Y-m-d') . ' at ' . $when->format('H:i:s');

            // get whether the callback URL was called
            $calledBack = ($response->getCalledBack() === Pushover\Response::CALLED_BACK_YES);
            if ($calledBack) {
                echo 'The callback URL was requested by Pushover on ' . $response->getCalledBackAt()->format('Y-m-d') . ' at ' . $response->getCalledBackAt()->format('H:i:s');
            }
        } else {
            // check whether the message has expired
            $expired = ($response->getExpired() == Pushover\Response::EXPIRED_YES);

            if ($expired) {
                echo 'The message has not been acknowledged, and has expired. Tough cookie.';
            } else {
                echo 'The message has not been acknowledged yet, it was last sent to the user at ' . $response->getLastDeliveredAt()->format('H:i:s') . ' and will expire on ' . $response->getExpiresAt()->format('Y-m-d') . ' at ' . $response->getExpiresAt()->format('H:i:s');
            }
        }
    }
}
```

Note that if you copy/paste this code directly and run it, you'll flood the Pushover API with a lot of requests in a fraction of a second. They don't like that, so you shouldn't do that. Keep their friendliness rules in mind when you use these classes.

Also note that most of this code is hardly tested at all. If you find any bugs in it, please raise an issue on Github.

Happy coding!