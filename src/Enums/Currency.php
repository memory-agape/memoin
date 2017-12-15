<?php

namespace Memoin\Enums;

class Currency
{

    /**
     * JPY
     */
    const JPY = 'JPY';

    /**
     * USD
     */
    const USD = 'USD';

    /**
     * Bit coin
     */
    const BTC = 'BTC';

    /**
     * Ethereum
     */
    const ETH = 'ETH';

    /**
     * Ethereum Classic
     */
    const ETH_CLASSIC = 'ETH_CLASSIC';

    /**
     * Lite Coin
     */
    const LTC = 'LTC';

    /**
     * BitCoin cash
     */
    const BCH = 'BCH';

    /**
     * Mona
     */
    const MONA = 'MONA';

    public static function getNames () {
        return (new \ReflectionClass(__CLASS__))->getConstants();
    }

}