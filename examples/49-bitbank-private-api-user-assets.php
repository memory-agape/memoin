<?php

// include autoloader by composer
include __DIR__ . '/../vendor/autoload.php';

$exchanger = (new Memoin\Core\Exchanger(Memoin\Exchangers\bitbank::class, new Memoin\Credentials\Credential([
    'API_KEY'   => 'API_KEY_HERE',
    'API_SECRET' => 'API_SECRET_HERE',
])))->getExchanger();

print_r($exchanger->get(Memoin\Exchangers\bitbank::PRIVATE_ENDPOINT . '/v1/user/assets'));
