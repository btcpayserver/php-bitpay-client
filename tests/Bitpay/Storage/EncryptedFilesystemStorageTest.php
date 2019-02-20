<?php
/**
 * @license Copyright 2011-2015 BitPay Inc., MIT License
 * see https://github.com/bitpay/php-bitpay-client/blob/master/LICENSE
 */

namespace Bitpay\Storage;

use org\bovigo\vfs\vfsStream;

class EncryptedFilesystemStorageTest extends \PHPUnit_Framework_TestCase
{
    private $root;
    private $pubkeyStream;
    private $pubkeyName;
    private $storage;

    public function setUp()
    {
        $this->pubkeyName = 'tmp/public.key';
        $this->root = vfsStream::setup('tmp');
        $this->pubkeyStream = vfsStream::url($this->pubkeyName);
        $this->storage = new EncryptedFilesystemStorage('satoshi');
    }

    public function testPersist()
    {
        $this->storage->persist(new \Bitpay\PublicKey($this->pubkeyStream));
        $this->assertTrue($this->root->hasChild($this->pubkeyName));
    }

    public function testLoad()
    {
        vfsStream::newFile('public.key')
            ->at($this->root)
            ->setContent('i8A7jkJy1H6oHWPGVxuBcrB360Krq8aZD8g3Ef4JYaLSZIJjhV+Kob1kNYL6091jRA3LpC1C7Tb19FDY80VXuHsd2zTn9QMgfQnl85awBzLdEBxh5Vx/Xv9FQSq9VDrKb2/jhZAnBjBC13rP2KuwS6fj8PNKz4BeVPENh09ADHo0uGhj5tzdXr80E09TkxcmRR2Ss2sGUCiCGWdjFM0AdwmEXDHqSPBHqBF7GuxzG5Ozuh7YLU4sHQoGWLRHKhaWsSyPMvuWLuEN13H7EcyKeHN/RrHP1aXbIJ6YgJWxQ7a4QEe0UH7xkhMJD0eIZM1uowNWpO0+lHUglxtqyU87ILPt2gtAGlXGuuxQqlPnKumNTdE7ji1PL/gClW6gEtACEdqVsAH5pv7EYVFqOhGakzAP2WtF2/SiNEgbu+j2dUYe4KuJ0sxir+v3LknVZ+fR1YdwNoKCbXMArLe8if2rjeNrjHOjs/FbNGIxMoPcPC0L24d3sQxzb2Lw7vJ0bu4zM4D6PoK1AiWYrR6NQuI0lvnH77Roav4qDw23Gs2SdysyCObQzoJboj1+hojEF888u+fBW9ZdqRRwhrwLwqiRC+q5XD1n79pRqh7Y6icJe6iSSLqrUbO/gGKmXguM7Ef55NYmVYESgZHgDtSVmTnk790EiEy2qA71j3UbxbgWH8DlZtMf6rEwqp/7401I1iym');

        $key = $this->storage->load($this->pubkeyStream);
        $this->assertInstanceOf('Bitpay\PublicKey', $key);
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage [ERROR] In EncryptedFilesystemStorage::readFromFile(): The file "vfs://tmp/public.key" does not exist or cannot be read, check permissions.
     */
    public function testNotFileException()
    {
        $this->storage->load($this->pubkeyStream);
    }

    /**
     * @expectedException \Exception
     */
    public function testLoadNotReadableException()
    {
        if (stripos(PHP_OS, 'WIN') === 0) {
            $this->markTestSkipped('Skip \Bitpay\Storage\EncryptedFilesystemStorageTest::testLoadNotReadableException() test on Windows system');
        }
        vfsStream::newFile('badpublic.key', 0600)
            ->at($this->root)
            ->setContent('')
            ->chown(vfsStream::OWNER_ROOT)
            ->chgrp(vfsStream::GROUP_ROOT);

        $this->storage->load(vfsStream::url('tmp/badpublic.key'));
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage [ERROR] In EncryptedFilesystemStorage::dataDecode(): Could not decode data "\".
     */
    public function testLoadCouldNotDecode()
    {
        vfsStream::newFile('badpublic.key')
            ->at($this->root)
            ->setContent('\\');

        $key = $this->storage->load(vfsStream::url('tmp/badpublic.key'));
        $this->assertInstanceOf('Bitpay\PublicKey', $key);
    }

    public function testPersistAndLoadWithoutPassword()
    {
        $storage = new EncryptedFilesystemStorage(null);

        $storage->persist(new \Bitpay\PublicKey($this->pubkeyStream));
        $this->assertTrue($this->root->hasChild($this->pubkeyName));

        $key = $storage->load($this->pubkeyStream);
        $this->assertInstanceOf('Bitpay\PublicKey', $key);
    }
}
