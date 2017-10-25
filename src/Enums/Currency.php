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
     * Light Coin
     */
    const LTC = 'LTC';

    /**
     * Bit coin cash
     */
    const BCH = 'BCH';

    public static function getNames () {
        return (new \ReflectionClass(__CLASS__))->getConstants();
    }

}