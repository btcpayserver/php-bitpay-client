<?php
/**
 * @license Copyright 2019 BTCPayServer, MIT License
 * see https://github.com/btcpayserver/php-bitpay-client/blob/master/LICENSE
 */

namespace BTCPayServer;

/**
 * Item that was sold
 *
 * @package BTCPayServer
 */
interface ItemInterface
{
    /**
     * Used to display an item SKU code or part number to the buyer. Maximum string
     * length is 100 characters.
     *
     * @return string
     */
    public function getCode();

    /**
     * Used to display an item description to the buyer. Maximum string length is 100
     * characters.
     *
     * @return string
     */
    public function getDescription();

    /**
     * @return string
     */
    public function getPrice();

    /**
     * @return string
     */
    public function getTaxIncluded();

    /**
     * @return string
     */
    public function getQuantity();

    /**
     * default value: false
     * ● true: Indicates a physical item will be shipped (or picked up)
     * ● false: Indicates that nothing is to be shipped for this order
     *
     * @return boolean
     */
    public function isPhysical();
}
