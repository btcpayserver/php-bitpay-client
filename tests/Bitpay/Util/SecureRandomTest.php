<?php
/**
 * @license Copyright 2019 BTCPayServer, MIT License
 * see https://github.com/btcpayserver/php-bitpay-client/blob/master/LICENSE
 */

namespace BTCPayServer\Util;

/**
 * @package Bitcore
 *
 * @requires function openssl_random_pseudo_bytes
 */
class SecureRandomTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->enableOpenSSL();
    }

    public function testHasOpenSSL()
    {
        $this->assertTrue(SecureRandom::hasOpenSSL());
        $this->disableOpenSSL();
        $this->assertFalse(SecureRandom::hasOpenSSL());
    }

    public function testGenerateRandom()
    {
        $randomNumber = SecureRandom::generateRandom();
        $this->assertEquals(32, strlen($randomNumber));
    }

    /**
     * @expectedException Exception
     */
    public function testWithoutOpenssl()
    {
        $this->disableOpenSSL();

        SecureRandom::generateRandom();
    }

    /**
     * @expectedException Exception
     */
    public function testWithoutStrongCryptographicNumber()
    {
        SecureRandom::generateRandom(0);
    }

    private function disableOpenSSL()
    {
        $ref = new \ReflectionProperty('BTCPayServer\Util\SecureRandom', 'hasOpenSSL');
        $ref->setAccessible(true);
        $ref->setValue(false);
        $ref->setAccessible(false);
    }

    private function enableOpenSSL()
    {
        $ref = new \ReflectionProperty('BTCPayServer\Util\SecureRandom', 'hasOpenSSL');
        $ref->setAccessible(true);
        $ref->setValue(null);
        $ref->setAccessible(false);
    }
}
