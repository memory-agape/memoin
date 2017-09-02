<?php
namespace Memoin\API;
use Memoin\Exceptions;
use Memoin\Credentials\Credential;
use Memoin\Enums\Currency;

class Base {

    /**
     * API Endpoint
     */
    const ENDPOINT = '{{EXCHANGERS_API_ENDPOINT}}';

    /**
     * Call APi Timeout (if you need override then you can set new Exchanger class)
     */
    const TIMEOUT = 60;

    /**
     * @var Credential $credential
     */
    protected $credential = null;

    /**
     * @var \GuzzleHttp\Client|null guzzle client
     */
    private $client = null;

    /**
     * API Base constructor
     */
    public function __construct () {
        $this->client = new \GuzzleHttp\Client([
            'base_uri' => $this::ENDPOINT,
            'timeout' => $this::TIMEOUT ?? self::TIMEOUT,
        ]);
    }

    /**
     * Get exchanger class name
     *
     * @return string return exchanger class name
     */
    public function getName () {
        return basename(str_replace('\\', DIRECTORY_SEPARATOR, get_class($this)));
    }

    /**
     * Set credential
     *
     * @param Credential $credential credential
     * @return $this
     */
    public function setCredential (Credential $credential) {
        $this->credential = $credential;
        return $this;
    }

    /**
     * Get currency (Maybe override from exchangers class)
     *
     * @param string $name trade to currency name
     * @return string|null
     */
    public function getCurrency ($name) {
        return null;
    }

    /**
     * Call any APIs
     *
     * @param string $api The API
     * @param string $method GET, POST and other method
     * @param bool $auth if set to true, auto credential, but false not credential (Maybe not use)
     * @param array $extendHeaders extend headers for request (or override)
     * @param array|null $body send body
     * @return Object return json decoded object
     * @throws \GuzzleHttp\Exception\ClientException
     * @throws Exceptions\API
     */
    public function call ($api, $method, $auth = true, array $extendHeaders = [], $body = null) {
        try {

            $options = [
                'headers' => $extendHeaders ?? [],
            ];

            if (is_string($body)) $options['body'] = $body;

            $response = $this->client->request($method, $api, $options);

        } catch (\GuzzleHttp\Exception\ClientException $e) {
            throw new Exceptions\API($e->getMessage());
        }
        $result = @json_decode((string) $response->getBody());
        if ($result === false) {
            throw new Exceptions\API('API is unavailable');
        }
        return $result;
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
        return $this->call($api, 'GET', true, $extendHeaders, $body);
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
        return $this->call($api, 'POST', true, $extendHeaders, $body);
    }

    /**
     * Show realtime streaming
     *
     * @param Streaming $streaming Set callback class
     * @param string $name trade to currency name
     * @param string $from trade from currency name
     * @return void
     */
    public function streaming (Streaming $streaming, $name, $from = Currency::JPY) {
        return null;
    }

}