<?php

// include autoloader by composer
include __DIR__ . '/vendor/autoload.php';

$exchanger = (new Memoin\Core\Exchanger('BitFlyer', new Memoin\Credentials\Credential([
    'API_KEY'   => 'API_KEY_HERE',
    'API_SECRET' => 'API_SECRET_HERE',
])))->getExchanger();

print_r($exchanger->get('/v1/getaddresses'));
