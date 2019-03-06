<?php
/**
 * Copyright (c) 2014-2015 BTCPayServer
 */

require __DIR__ . '/../vendor/autoload.php';

$bitpay     = new \BTCPayServer\BTCPayServer(__DIR__ . '/config.yml');
$client     = $bitpay->get('client');
$client->setUri('https://btcpay.server/');
$currencies = $client->getCurrencies();

/** @var \BTCPayServer\Currency $currencies[0] **/
var_dump($currencies[0]);
