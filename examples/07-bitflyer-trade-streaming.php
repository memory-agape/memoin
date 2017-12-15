<?php

// include autoloader by composer
include __DIR__ . '/vendor/autoload.php';

// create exchanger instance
(new Memoin\Core\Exchanger('bitFlyer', new Memoin\Credentials\Credential([
    'API_KEY'   => 'API_KEY_HERE',
    'API_SECRET' => 'API_SECRET_HERE',
])))->getExchanger()->streaming(new class extends Memoin\API\Streaming {

    public function receive ($message) {

        // received streaming message
        print_r($message);
    }

}, Memoin\Enums\Currency::MONA, Memoin\Enums\Currency::JPY);

