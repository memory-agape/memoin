<?php

// include autoloader by composer
include __DIR__ . '/vendor/autoload.php';

// create exchanger instance (authorization is optional)
(new Memoin\Core\Exchanger('Coincheck'))
    ->getExchanger()
    ->streaming(new class extends Memoin\API\Streaming {

        public function receive ($message) {

            // received streaming message
            print_r($message);
        }

    }, Memoin\Enums\Currency::BTC, Memoin\Enums\Currency::JPY, 'orderbook');