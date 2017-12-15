<?php
namespace Memoin\Exchangers\Coincheck;

use Memoin\API\BaseController;
use Memoin\API\Streaming;
use Memoin\Credentials\Credential;
use Memoin\Enums\Currency;
use Memoin\Exceptions;

class Controller extends BaseController
{

    const ENDPOINT = 'https://coincheck.com/';

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

    public function getAdapter()
    {
        return new Adapter($this);
    }


    /**
     * Call any Coincheck APIs
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
            $text = $timestamp . rtrim(self::ENDPOINT, '/') . $api . ($body ?? '');
            $headers = array_merge($headers, [
                'ACCESS-KEY' => $this->credential->getApiKey(),
                'ACCESS-NONCE' => $timestamp,
                'ACCESS-SIGNATURE' => hash_hmac('sha256', $text, $this->credential->getApiSecret()),
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
        return $this->call($api, 'POST', true, $extendHeaders, is_array($body) ? http_build_query($body) : $body);
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
        return $this->call($api, 'GET', true, $extendHeaders, is_array($body) ? http_build_query($body) : $body);
    }

    /**
     * Show realtime streaming for Coincheck
     *
     * @param Streaming $streaming Set callback class
     * @param string $name trade to currency name
     * @param string $from trade from currency name
     * @return void
     */
    public function streaming(Streaming $streaming, $name, $from = Currency::JPY)
    {
        throw new \RuntimeException('Incomplete yet.');
    }

}