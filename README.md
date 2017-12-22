# What is memoin
memoin (this project) is an SDK for PHP which is made to support many virtual
currency exchangers. memoin utilizes API provided by exchangers in order that
the system makes it possible to optimize transactions over virtual currencies.

# Requirements

- PHP >= 7
- Composer
- GuzzleHttp

# Installation

Run the command below to get started.

```
composer require memory-agape/memoin
```

# Supports

- bitFlyer, Coincheck and Zaif (Will support other exchangers)
- Supports Streaming API (bitFlyer, Coincheck and Zaif)
- See: https://lightning.bitFlyer.com/docs?lang=en
- See: https://coincheck.com/ja/documents/exchange/api
- See: https://corp.zaif.jp/api-docs/

# Examples

- See examples

# Donations

- Donations are always welcome. They help me develop.
- Please make remittance to my bitcoin address (**321KChd61h3kp7XfkGz6rWiSNdhZiJMxSf**)

# Methods
Below code are Memoin APIs. Memoin is optimized to coding/typing.

```php
$exchanger = (new Memoin\Core\Exchanger(Memoin\Exchangers\bitFlyer::class, new Memoin\Credentials\Credential([
    'API_KEY'   => 'API Key',
    'API_SECRET' => 'API Secret',
])))->getExchanger();

// send with GET method
$exchanger->get($apiUriHere);

// send with POST method
$exchanger->post($apiUriHere, $bodyHere, $extendHeadersHere);

// call by any method for RESTful APIs (PUT, DELETE and so on)
$exchanger->call($apiURIHere, $methodHere, $authHere, $extendHeadersHere, $bodyHere);

// streaming service
$exchanger->streaming(new class extends Memoin\API\Streaming {
  
      public function receive ($message) {
  
          // received streaming message
          print_r($message);
      }
  
  }, Memoin\Enums\Currency::MONA, Memoin\Enums\Currency::JPY);
```


# Issues and Pull requests

- Have new ideas, or found bugs? Join this project!
