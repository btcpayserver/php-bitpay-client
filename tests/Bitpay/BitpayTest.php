<?php
/**
 * @license Copyright 2011-2014 BitPay Inc., MIT License
 * see https://github.com/bitpay/php-bitpay-client/blob/master/LICENSE
 */

namespace Bitpay;

use org\bovigo\vfs\vfsStream;

class BitpayTest extends \PHPUnit_Framework_TestCase
{

    public function testConstruct()
    {
        $bitpay = new \Bitpay\Bitpay(
            array(
                'bitpay' => array()
            )
        );
    }

    public function testGetContainer()
    {
        $bitpay = new \Bitpay\Bitpay();
        $this->assertInstanceOf('Symfony\Component\DependencyInjection\ContainerInterface', $bitpay->getContainer());
    }

    public function testGet()
    {
        $bitpay = new \Bitpay\Bitpay();
        $this->assertInstanceOf('Bitpay\Client\Adapter\CurlAdapter', $bitpay->get('adapter'));
    }

    /**
     * @expectedException \Symfony\Component\DependencyInjection\Exception\InvalidArgumentException
     */
    public function testGetInvalidService()
    {
        $bitpay = new \Bitpay\Bitpay();
        $bitpay->get('coins');
    }

    public function testConfigAbleToPersistAndLoadKeys()
    {
        $root   = vfsStream::setup('tmp');
        $bitpay = new \Bitpay\Bitpay(
            array(
                'bitpay' => array(
                    'private_key' => vfsStream::url('tmp/key.pri'),
                    'public_key'  => vfsStream::url('tmp/key.pub'),
                )
            )
        );

        $pri = new \Bitpay\PrivateKey(vfsStream::url('tmp/key.pri'));
        $pri->generate();
        $pub = new \Bitpay\PublicKey(vfsStream::url('tmp/key.pub'));
        $pub->setPrivateKey($pri)->generate();

        /**
         * Save keys to the filesystem
         */
        $storage = $bitpay->get('key_storage');
        $storage->persist($pri);
        $storage->persist($pub);

        /**
         * This will load the keys, if you have not already persisted them, than
         * this WILL throw an Exception since this will load the keys from the
         * storage class
         */
        $pri = $bitpay->get('private_key');
        $pub = $bitpay->get('public_key');
    }
}
