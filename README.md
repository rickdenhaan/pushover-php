Pushover PHP client
===================

This simple PHP client uses the Pushover REST API to send push notifications to mobile devices. For more information
about Pushover, see http://pushover.net


Usage
-----

    use Capirussa\Pushover;

    $client = Pushover\Client::init($applicationToken);
    $message = new Pushover\Message('userKey', 'message');

    $client->send($message);


Additional options
------------------

You need to supply your application's token when initializing the Pushover\Client. You can find this token in your
online dashboard when you log in to Pushover.

The message you want to send must be an instance of Pushover\Message. If this object does not suit your needs, feel
free to extend it and do with it whatever you want.

For a simple application, you can initialize it with the user or group ID of whoever you want to send the message to
and the message itself. This is optional. These properties (and more) can also be set after creating a new message:

    $message = new Pushover\Message();
    $message->setRecipient('userKey');
    $message->setMessage('message');
    $message->setSound(Pushover\Sound::SPACE_ALARM);

If the client fails to deliver your message to the Pushover server, a Pushover\Exception will be thrown.
