<?php

namespace Memoin\Exchangers\BitFlyer;
use Memoin\API\BaseAdapter;

class Adapter extends BaseAdapter {

    /**
     * Get currency middle price, pay price, ask price and traded prices.
     *
     * @param string $name currency name
     * @param string $from trade from currency name
     * @return object return currency information
     */
    public function getCurrency ($name, $from = Currency::JPY) {
        if (empty($this->markets)) {
            $this->markets = array_unique(array_map(function ($data) {
                return $data->product_code;
            }, $this->getController()->get('/v1/markets')));
        }
        $productCode = $name . '_' . $from;
        if (in_array($productCode, $this->markets)) {
            $board = $this->getController()->get('/v1/board?product_code=' . $productCode);
            $bid = $board->bids[0] ?? null;
            $ask = $board->asks[0] ?? null;

            $tradeBid = null;
            $tradeAsk = null;

            if (isset($bid->price, $bid->size, $ask->price, $ask->size)) {
                if (function_exists('bcmul')) {

                    $tradeBid = bcmul($bid->price, $bid->size, 30);
                    $tradeAsk = bcmul($ask->price, $ask->size, 30);

                } else if (function_exists('gmp_mul')) {

                    $tradeBid = (string) gmp_mul(gmp_init($bid->price, 10), gmp_init($bid->size, 10));
                    $tradeAsk = (string) gmp_mul(gmp_init($ask->price, 10), gmp_init($ask->size, 10));

                } else {

                    $tradeBid = (string) ($bid->price / $bid->size);
                    $tradeAsk = (string) ($ask->price / $ask->size);
                }
            }

            return (object) [
                'middle' => $board->mid_price ?? null,
                'bid' => $bid->price ?? null,
                'ask' => $ask->price ?? null,
                'trade' => (object) [
                    'bid' => $tradeBid,
                    'ask' => $tradeAsk,
                ],
            ];
        }
        return (object) [
            'middle' => null,
            'bid' => null,
            'ask' => null,
            'trade' => (object) [
                'bid' => null,
                'ask' => null,
            ],
        ];
    }

}