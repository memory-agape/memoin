<?php

// include autoloader by composer
include __DIR__ . '/vendor/autoload.php';

// create exchanger instance
$exchanger = (new Memoin\Core\Exchanger('bitFlyer', new Memoin\Credentials\Credential([
    'API_KEY'   => 'API_KEY_HERE',
    'API_SECRET' => 'API_SECRET_HERE',
])))->getExchanger();

print_r($exchanger->post('/v1/me/withdraw', [], [
    'currency_code' => 'JPY',
    'bank_account_id' => 'XXX',
    'amount' => 1000,
]));
