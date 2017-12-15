<?php

// include autoloader by composer
include __DIR__ . '/vendor/autoload.php';

// create exchanger instance
$exchanger = (new Memoin\Core\Exchanger('Zaif', new Memoin\Credentials\Credential([
    'API_KEY'   => 'API_KEY_HERE',
    'API_SECRET' => 'API_SECRET_HERE',
])))->getExchanger();

$exchanger->post('', [], [
    'method' => 'get_info',
]);