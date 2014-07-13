<?php
namespace Capirussa\Pushover;

/**
 * Pushover Receipt defines the request that is used to check whether an emergency message has been acknowledged
 *
 * @package Capirussa\Pushover
 */
class Receipt implements Request
{
    /**
     * Receipt to validate
     *
     * @type string
     */
    protected $receipt;

    /**
     * Constructor -- allows initializing the request with a receipt
     *
     */
    public function __construct($receipt = null)
    {
        if (function_exists('unittest_log')) unittest_log('Receipt::__construct()');
        // if a receipt was given, set it
        if ($receipt !== null) {
            if (function_exists('unittest_log')) unittest_log('Setting receipt token ' . $receipt);
            $this->setReceipt($receipt);
        }
    }

    /**
     * Validates whether the receipt is properly formatted and sets it
     *
     * @param string $receipt Receipt token to check
     * @throws \InvalidArgumentException if the given receipt token is not of a valid syntax
     * @return static
     */
    public function setReceipt($receipt)
    {
        if (function_exists('unittest_log')) unittest_log('Receipt::setReceipt()');
        // validate the receipt token
        if (!preg_match(Request::RECEIPT_REGEXP, $receipt)) {
            if (function_exists('unittest_log')) unittest_log('Receipt token does not match regular expression, throwing InvalidArgumentException');
            throw new \InvalidArgumentException(
                sprintf(
                    '%1$s: Invalid receipt "%2$s", token should be a 30-character alphanumeric string',
                    __METHOD__,
                    $receipt
                )
            );
        }

        // set the receipt token
        if (function_exists('unittest_log')) unittest_log('Receipt token appears to be OK, setting it');
        $this->receipt = $receipt;

        // return this Receipt for easy method chaining
        if (function_exists('unittest_log')) unittest_log('Returning');
        return $this;
    }

    /**
     * Returns the receipt token currently set
     *
     * @return string|null
     */
    public function getReceipt()
    {
        if (function_exists('unittest_log')) unittest_log('Receipt::getReceipt()');
        if (function_exists('unittest_log')) unittest_log('Returning receipt ' . $this->receipt);
        return $this->receipt;
    }

    /**
     * Returns the array of POST data to submit to Pushover for this receipt
     *
     * @return array
     */
    public function getPushoverFields()
    {
        if (function_exists('unittest_log')) unittest_log('Receipt::getPushoverFields()');
        if (function_exists('unittest_log')) unittest_log('Returning empty array, Receipt does not have any post data');
        return array();
    }
}
