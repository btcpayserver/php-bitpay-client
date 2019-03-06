<?php
/**
 * Copyright (c) 2014-2015 BTCPayServer
 */

/**
 * This example show how to persist and load keys from the filesystem. Any key
 * can be persisted and loaded and the same way.
 */

require __DIR__ . '/../vendor/autoload.php';

// Create and generate
$pri = new \BTCPayServer\PrivateKey('/tmp/private.key');
$pri->generate();

// Use the key manager to persist key
$manager = new \BTCPayServer\KeyManager(new \BTCPayServer\Storage\EncryptedFilesystemStorage('password'));

// Saved to /tmp/private.key
$manager->persist($pri);

// Load from /tmp/private.key
$key = $manager->load('/tmp/private.key');
printf("Private Key: %s\n", $key);
