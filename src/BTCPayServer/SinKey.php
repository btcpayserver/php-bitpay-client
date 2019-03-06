<?php
/**
 * @license Copyright 2019 BTCPayServer, MIT License
 * see https://github.com/btcpayserver/php-bitpay-client/blob/master/LICENSE
 */

namespace BTCPayServer;

use BTCPayServer\Util\Base58;
use BTCPayServer\Util\Gmp;
use BTCPayServer\Util\Util;

/**
 * @package Bitcore
 */
class SinKey extends Key
{
    // Type 2 (ephemeral)
    const SIN_TYPE    = '02';

    // Always the prefix!
    // (well, right now)
    const SIN_VERSION = '0F';

    /**
     * @var string
     */
    protected $value;

    /**
     * @var PublicKey
     */
    protected $publicKey;

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->value;
    }

    /**
     * @param PublicKey
     * @return SinKey
     */
    public function setPublicKey(PublicKey $publicKey)
    {
        $this->publicKey = $publicKey;

        return $this;
    }

    /**
     * Generates a Service Identification Number (SIN), see:
     * https://en.bitcoin.it/wiki/Identity_protocol_v1
     *
     * @return SinKey
     */
    public function generate()
    {
        if (is_null($this->publicKey)) {
            throw new \Exception('Public Key has not been set');
        }

        $compressedValue = $this->publicKey;

        if (empty($compressedValue)) {
            throw new \Exception('The Public Key needs to be generated.');
        }

        $step1 = Util::sha256(Util::binConv($compressedValue), true);

        $step2 = Util::ripe160($step1);

        $step3 = sprintf(
            '%s%s%s',
            self::SIN_VERSION,
            self::SIN_TYPE,
            $step2
        );

        $step4 = Util::twoSha256(Util::binConv($step3), true);

        $step5 = substr(bin2hex($step4), 0, 8);

        $step6 = $step3.$step5;

        $this->value = Base58::encode($step6);

        return $this;
    }

    /**
     * Checks to make sure that this SIN is a valid object.
     *
     * @return boolean
     */
    public function isValid()
    {
        return (!is_null($this->value) && (substr($this->value, 0, 1) == 'T'));
    }
}
