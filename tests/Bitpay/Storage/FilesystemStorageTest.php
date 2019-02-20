<?php
/**
 * @license Copyright 2011-2015 BitPay Inc., MIT License
 * see https://github.com/bitpay/php-bitpay-client/blob/master/LICENSE
 */

namespace Bitpay\Storage;

use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;

class FilesystemStorageTest extends \PHPUnit_Framework_TestCase
{
    /** @var FilesystemStorage */
    private $storage;

    /** @var string */
    private $key_file_content;

    /** @var vfsStreamDirectory */
    private $root;

    public function setUp()
    {
        $this->storage = new FilesystemStorage();
        $this->key_file_content = 'C:16:"Bitpay\PublicKey":62:{a:5:{i:0;s:20:"vfs://tmp/public.key";i:1;N;i:2;N;i:3;N;i:4;N;}}';
        $this->root = vfsStream::setup('tmp');
    }

    public function testPersist()
    {
        $this->storage->persist(new \Bitpay\PublicKey(vfsStream::url('tmp/public.key')));
        $this->assertTrue($this->root->hasChild('tmp/public.key'));
    }

    public function testLoad()
    {
        vfsStream::newFile('public.key')
            ->at($this->root)
            ->setContent($this->key_file_content);

        $key = $this->storage->load(vfsStream::url('tmp/public.key'));
        $this->assertInstanceOf('Bitpay\PublicKey', $key);
    }

    /**
     * @expectedException \Exception
     */
    public function testNotFileException()
    {
        $this->storage->load(vfsStream::url('tmp/public.key'));
    }

    /**
     * @expectedException \Exception
     */
    public function testLoadNotReadableException()
    {
        if (stripos(PHP_OS, 'WIN') === 0) {
            $this->markTestSkipped('Skip \Bitpay\Storage\EncryptedFilesystemStorageTest::testLoadNotReadableException() test on Windows system');
        }

        vfsStream::newFile('public.key', 0600)
            ->at($this->root)
            ->setContent($this->key_file_content)
            ->chown(vfsStream::OWNER_ROOT)
            ->chgrp(vfsStream::GROUP_ROOT);

        $this->storage->load(vfsStream::url('tmp/public.key'));
    }
}
