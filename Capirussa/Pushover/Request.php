<?php
namespace Capirussa\Pushover;

/**
 * Pushover Request Interface
 *
 * @package Capirussa\Pushover
 */
interface Request
{
    /**
     * Validation regular expression to validate whether a recipient token is properly formatted
     *
     */
    const RECEIPT_REGEXP   = '/^[0-9a-z]{30}$/i';
    const RECIPIENT_REGEXP = '/^[0-9a-z]{30}$/i';
    const DEVICE_REGEXP    = '/^[0-9a-z_]{,25}$/i';

    /**
     * Valid priorities for messages
     *
     */
    const PRIORITY_INVISIBLE = -2;
    const PRIORITY_SILENT    = -1;
    const PRIORITY_NORMAL    = 0;
    const PRIORITY_HIGH      = 1;
    const PRIORITY_EMERGENCY = 2;

    /**
     * Valid API entry points
     *
     */
    const API_MESSAGE  = 'messages';
    const API_VALIDATE = 'users/validate';
    const API_RECEIPT  = 'receipts/{receipt}';

    /**
     * POST data field names for the Pushover API
     *
     */
    const TOKEN     = 'token';
    const RECIPIENT = 'user';
    const MESSAGE   = 'message';
    const TITLE     = 'title';
    const DEVICE    = 'device';
    const URL       = 'url';
    const URL_TITLE = 'url_title';
    const PRIORITY  = 'priority';
    const TIMESTAMP = 'timestamp';
    const SOUND     = 'sound';
    const CALLBACK  = 'callback';

    /**
     * Returns the array of POST data to submit to Pushover for this message
     *
     * @return array
     */
    public function getPushoverFields();
}
