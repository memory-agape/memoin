<?php

// include autoloader by composer
include __DIR__ . '/../vendor/autoload.php';

// create exchanger instance (authorization is optional)
(new Memoin\Core\Exchanger(Memoin\Exchangers\Zaif::class))
    ->getExchanger()
    ->streaming(new class extends Memoin\API\Streaming {

        public function receive ($message) {

            // received streaming message
            print_r($message);
        }

    }, Memoin\Enums\Currency::MONA, Memoin\Enums\Currency::JPY);