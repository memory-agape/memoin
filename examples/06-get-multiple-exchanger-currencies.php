<?php

include __DIR__ . '/vendor/autoload.php';

// create manipulator for multiple exchangers
$manipulator = new Memoin\Core\Manipulator([
    new Memoin\Core\Exchanger('BitFlyer', new Memoin\Credentials\Credential([
        'API_KEY'   => 'API_KEY_HERE',
        'API_SECRET' => 'API_SECRET_HERE',
    ])),
    new Memoin\Core\Exchanger('BitFlyer', new Memoin\Credentials\Credential([
        'API_KEY'   => 'API_KEY_HERE',
        'API_SECRET' => 'API_SECRET_HERE',
    ])),
    new Memoin\Core\Exchanger('BitFlyer', new Memoin\Credentials\Credential([
        'API_KEY'   => 'API_KEY_HERE',
        'API_SECRET' => 'API_SECRET_HERE',
    ])),
]);

// get set all Bit coin rates
print_r($manipulator->getBTC());


// get set all Ethereum rates
print_r($manipulator->getETH());