<?php
namespace Memoin\Exchangers\bitFlyer;

use Memoin\API\BaseController;
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

class Controller extends BaseController
{

    const ENDPOINT = 'https://api.bitFlyer.jp/';
    const PUB_NUB_SUBSCRIBE_KEY = 'sub-c-52a9ab50-291b-11e5-baaa-0619f8945a4f';

    private $markets = [];

    /**
     * Set credential
     *
     * @param Credential $credential set bitFlyer credential
     * @return $this
     * @throws Exceptions\Credential
     */
    public function setCredential(Credential $credential)
    {
        if (!$credential->hasApiKey()) {
            throw new Exceptions\Credential('Undefined API Key for credential');
        }
        if (!$credential->hasApiSecret()) {
            throw new Exceptions\Credential('Undefined API Secret for credential');
        }
        parent::setCredential($credential);
        return $this;
    }

    public function getAdapter () {
        return new Adapter($this);
    }

    /**
     * Call any bitFlyer APIs
     *
     * @param string $api The API
     * @param string $method GET, POST and other method
     * @param bool $auth if set to true, auto credential, but false not credential (Maybe not use)
     * @param array $extendHeaders extend headers for request (or override)
     * @param array|null $body send body
     * @return Object return json decoded object
     * @throws Exceptions\Credential
     */
    public function call($api, $method, $auth = true, array $extendHeaders = [], $body = null)
    {
        $headers = [
            'Content-Type' => 'application/json',
        ];
        if ($auth) {
            if (empty($this->credential)) {
                throw new Exceptions\Credential('You must be set credential which use method "$class->setCredential" or set first argument from constructor');
            }
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
    public function post($api, array $extendHeaders = [], $body = null)
    {
        return $this->call($api, 'POST', true, $extendHeaders, is_array($body) ? json_encode($body) : $body);
    }

    /**
     * Easy to use as call method (set GET method)
     *
     * @param string $api The API
     * @param array $extendHeaders extend headers for request (or override)
     * @param array|null $body send body
     * @return Object return json decoded object
     */
    public function get($api, array $extendHeaders = [], $body = null)
    {
        return $this->call($api, 'GET', true, $extendHeaders, is_array($body) ? json_encode($body) : $body);
    }

    /**
     * Show realtime streaming for bitFlyer
     *
     * @param Streaming $streaming Set callback class
     * @param string $name trade to currency name
     * @param string $from trade from currency name
     * @return void
     */
    public function streaming(Streaming $streaming, $name, $from = Currency::JPY)
    {

        $pnconf = new PNConfiguration();
        $pnconf->setSubscribeKey(self::PUB_NUB_SUBSCRIBE_KEY);
        
        // dummy value (PubNub for PHP SDK has many problems...)
        $pnconf->setPublishKey('bitFlyer');

        $pubnub = new PubNub($pnconf);
        $pubnub->setLogger((new Logger('bitFlyer'))->pushHandler(new StreamHandler(fopen('php://output', 'r'), Logger::EMERGENCY)));

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