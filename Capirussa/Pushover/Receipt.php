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
        // if a receipt was given, set it
        if ($receipt !== null) {
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
        // validate the receipt token
        if (!preg_match(Request::RECEIPT_REGEXP, $receipt)) {
            throw new \InvalidArgumentException(
                sprintf(
                    '%1$s: Invalid receipt "%2$s", token should be a 30-character alphanumeric string',
                    __METHOD__,
                    $receipt
                )
            );
        }

        // set the receipt token
        $this->receipt = $receipt;

        // return this Receipt for easy method chaining
        return $this;
    }

    /**
     * Returns the receipt token currently set
     *
     * @return string|null
     */
    public function getReceipt()
    {
        return $this->receipt;
    }

    /**
     * Returns the array of POST data to submit to Pushover for this receipt
     *
     * @return array
     */
    public function getPushoverFields()
    {
        return array();
    }
}
