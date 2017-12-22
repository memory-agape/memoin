<?php

// include autoloader by composer
include __DIR__ . '/../vendor/autoload.php';

$exchanger = (new Memoin\Core\Exchanger(Memoin\Exchangers\bitbank::class, new Memoin\Credentials\Credential([
    'API_KEY'   => 'API_KEY_HERE',
    'API_SECRET' => 'API_SECRET_HERE',
])))->getExchanger();

$pair = strtolower(Memoin\Enums\Currency::BTC . '_' . Memoin\Enums\Currency::JPY);
print_r($exchanger->get(Memoin\Exchangers\bitbank::PUBLIC_ENDPOINT . '/' . $pair . '/ticker'));
