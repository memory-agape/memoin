<?php

// include autoloader by composer
include __DIR__ . '/../vendor/autoload.php';

// create exchanger instance
$exchanger = (new Memoin\Core\Exchanger(Memoin\Exchangers\bitFlyer::class, new Memoin\Credentials\Credential([
    'API_KEY'   => 'API_KEY_HERE',
    'API_SECRET' => 'API_SECRET_HERE',
])))->getExchanger();

// get board
print_r($exchanger->get('/v1/board?product_code=BTC_JPY'));
