<?php
namespace Memoin\Exchangers;

use Memoin\API\BaseExchanger;
use Memoin\API\Streaming;
use Memoin\Credentials\Credential;
use Memoin\Enums\Currency;
use Memoin\Exceptions;

class Zaif extends BaseExchanger
{

    const ENDPOINT = 'https://api.zaif.jp/tapi';

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

    /**
     * Call any Zaif APIs
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
        $headers = [];
        $timestamp = time();

        $body .= '&nonce=' . $timestamp;
        parse_str($body, $parsedUrl);
        $body = [];
        foreach ($parsedUrl as $key => $value) {
            $body[] = $key . '=' . rawurlencode($value);
        }
        $body = implode('&', $body);
        if ($auth) {
            if (empty($this->credential)) {
                throw new Exceptions\Credential('You must be set credential which use method "$class->setCredential" or set first argument from constructor');
            }
            $headers = array_merge($headers, [
                'key' => $this->credential->getApiKey(),
                'sign' => hash_hmac('sha512', $body, $this->credential->getApiSecret()),
            ]);
        }
        $headers = array_merge($headers, $extendHeaders ?? []);
        return parent::call($api, $method, $auth, $headers, $body);
    }

    /**
     * Easy to use as call method (set POST method)
     *
     * @param string|array|null $api The body
     * @param array|null $body No required parameter
     * @param array $extendHeaders No required parameter
     * @return Object return json decoded object
     */
    public function post($api, $body = null, array $extendHeaders = [])
    {
        return $this->call('', 'POST', true, $extendHeaders, is_array($api) ? http_build_query($api) : $api);
    }

    /**
     * Easy to use as call method (set GET method)
     *
     * @param string|array|null $api The API
     * @param array|null $body send body
     * @param array $extendHeaders extend headers for request (or override)
     */
    public function get($api, $body = null, array $extendHeaders = [])
    {
        throw new \RuntimeException('Zaif API does not support GET method');
    }

    /**
     * Show realtime streaming for Zaif
     *
     * @param Streaming $streaming Set callback class
     * @param string $name trade to currency name
     * @param string $from trade from currency name
     * @return void
     */
    public function streaming(Streaming $streaming, $name, $from = Currency::JPY)
    {
        \Ratchet\Client\connect('wss://ws.zaif.jp:8888/stream?currency_pair=' . strtolower($name . '_' . $from))->then(function($connection) use ($streaming) {
            $streaming->connect();
            $connection->on('message', function($message) use ($connection, $streaming) {
                $data = json_decode($message);
                $streaming->receive($data);
            });

            $connection->on('close', function($message) use ($connection, $streaming) {
                $streaming->disconnect();
            });

            $connection->on('error', function($message) use ($connection, $streaming) {
                $streaming->error();
            });

        }, function ($e) {
            echo "Could not connect: " . $e->getMessage() . "\n";
        });

    }

}