<?php
namespace Memoin\Exchangers;
use Memoin\API\Base;
use Memoin\API\Streaming;
use Memoin\Credentials\Credential;
use Memoin\Enums\Currency;
use Memoin\Exceptions;

// Monologs
use Monolog\Logger;
use Monolog\Handler\FingersCrossedHandler;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Handler\FingersCrossed\ErrorLevelActivationStrategy;
use Monolog\Handler\StreamHandler;

// PubNubs
use PubNub\PubNub;
use PubNub\Enums\PNStatusCategory;
use PubNub\Callbacks\SubscribeCallback;
use PubNub\PNConfiguration;

class BitFlyer extends Base {

    const ENDPOINT = 'https://api.bitflyer.jp/';
    const PUB_NUb_SUBSCRIBE_KEY = 'sub-c-52a9ab50-291b-11e5-baaa-0619f8945a4f';

    private $markets = [];

    /**
     * Set credential
     *
     * @param Credential $credential set Bitflyer credential
     * @return $this
     * @throws Exceptions\Credential
     */
    public function setCredential(Credential $credential) {
        if (!$credential->hasApiKey()) throw new Exceptions\Credential('Undefined API Key for credential');
        if (!$credential->hasApiSecret()) throw new Exceptions\Credential('Undefined API Secret for credential');
        return parent::setCredential($credential);
    }

    /**
     * Call any BitFlyer APIs
     *
     * @param string $api The API
     * @param string $method GET, POST and other method
     * @param bool $auth if set to true, auto credential, but false not credential (Maybe not use)
     * @param array $extendHeaders extend headers for request (or override)
     * @param array|null $body send body
     * @return Object return json decoded object
     * @throws Exceptions\Credential
     */
    public function call ($api, $method, $auth = true, array $extendHeaders = [], $body = null) {
        $headers = [
            'Content-Type' => 'application/json',
        ];
        if ($auth) {
            if (empty($this->credential)) throw new Exceptions\Credential('You must be set credential which use method "$class->setCredential" or set first argument from constructor');
            $timestamp = time();
            $text = $timestamp . $method . $api . ($body ?? '');
            $headers = array_merge($headers, [
                'ACCESS-KEY' => $this->credential->getApiKey(),
                'ACCESS-TIMESTAMP' => $timestamp,
                'ACCESS-SIGN' => hash_hmac('sha256', $text, $this->credential->getApiSecret()),
            ]);
        }
        $headers = array_merge($headers, $extendHeaders ?? []);
        return parent::call($api, $method, $auth, $headers, $body);
    }

    /**
     * Easy to use as call method (set POST method)
     *
     * @param string $api The API
     * @param array $extendHeaders extend headers for request (or override)
     * @param array|null $body send body
     * @return Object return json decoded object
     */
    public function post ($api, array $extendHeaders = [], $body = null) {
        return $this->call($api, 'POST', true, $extendHeaders, is_array($body) ? json_encode($body) : null);
    }

    /**
     * Easy to use as call method (set GET method)
     *
     * @param string $api The API
     * @param array $extendHeaders extend headers for request (or override)
     * @param array|null $body send body
     * @return Object return json decoded object
     */
    public function get ($api, array $extendHeaders = [], $body = null) {
        return $this->call($api, 'GET', true, $extendHeaders, is_array($body) ? json_encode($body) : null);
    }

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
            }, $this->get('/v1/markets')));
        }
        $productCode = $name . '_' . $from;
        if (in_array($productCode, $this->markets)) {
            $board = $this->get('/v1/board?product_code=' . $productCode);
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

    /**
     * Show realtime streaming for BitFlyer
     *
     * @param Streaming $streaming Set callback class
     * @param string $name trade to currency name
     * @param string $from trade from currency name
     * @return void
     */
    public function streaming (Streaming $streaming, $name, $from = Currency::JPY) {

        $pnconf = new PNConfiguration();
        $pnconf->setSubscribeKey(self::PUB_NUb_SUBSCRIBE_KEY);
        
        // dummy value (PubNub for PHP SDK has many problems...)
        $pnconf->setPublishKey('bitflyer');

        $pubnub = new PubNub($pnconf);
        $pubnub->setLogger((new Logger('BitFlyer'))->pushHandler(new StreamHandler(fopen('php://output', 'r'), Logger::EMERGENCY)));

        $pubnub->addListener(new class($streaming) extends SubscribeCallback {

            private $callbacks = null;

            public function __construct (Streaming $callbacks) {
                $this->callbacks = $callbacks;
            }

            public function status ($pubnub, $status) {
                if ($status->getCategory() === PNStatusCategory::PNUnexpectedDisconnectCategory) {
                    $this->callbacks->disconnect();
                } else if ($status->getCategory() === PNStatusCategory::PNConnectedCategory) {
                    $this->callbacks->connect();
                } else if ($status->getCategory() === PNStatusCategory::PNDecryptionErrorCategory) {
                    $this->callbacks->error();
                } else if ($status->getCategory() === PNStatusCategory::PNAccessDeniedCategory) {
                    $this->callbacks->error();
                }
            }

            public function message ($pubnub, $message) {
                $this->callbacks->receive($message->getMessage());
            }

            public function presence ($pubnub, $presence) {
                $this->callbacks->presence($presence->getPresence());
            }

        });

        $pubnub->subscribe()
            ->channel('lightning_ticker_' . $name . '_' . $from)
            ->execute();

    }

}