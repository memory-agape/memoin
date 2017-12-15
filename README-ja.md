# What is memoin
memoin (めもいん) とは多くの仮想通貨取引所をシステムのみで対応するために作られたPHP用のSDKです。
特徴として取引所が公開しているAPIを簡易的に扱い、仮想通貨の取引をシステムで最適化するために作られたものです。

# Requirements

- PHP 7以上
- Composer
- GuzzleHttp

# Installation

下記のコマンドを実行して、インストールしてください。

```
composer require memory-agape/memoin
```

# Supports

- 現在はbitFlyer, CoincheckとZaifのAPIのみ対応しています。（将来的には他の取引所のAPIも対応する予定です。）
- ストリーミングに対応しています。(bitFlyerとZaifのみ)
- See: https://lightning.bitFlyer.jp/docs
- See: https://coincheck.com/ja/documents/exchange/api
- See: https://corp.zaif.jp/api-docs/

# Examples

- examplesを見てください。

# Donations

- 募金ぜひ欲しいです！開発の支えになります。
- 開発者のビットコインアドレスは **321KChd61h3kp7XfkGz6rWiSNdhZiJMxSf** です。

# Methods
MemoinのAPIを示します。Memoinでは極力書くコード量を減らすような仕組みとなっており、
それぞれの取引所において送るパラメータのみが異なる状態となっています。

```php
$exchanger = (new Memoin\Core\Exchanger('Exchanger Name(bitFlyer/Coincheck/Zaif)', new Memoin\Credentials\Credential([
    'API_KEY'   => 'API Key',
    'API_SECRET' => 'API Secret',
])))->getExchanger();

// send with GET method
$exchanger->get($apiUriHere);


// send with POST method
$exchanger->post($apiUriHere, $extendHeadersHere, $bodyHere);

// call by any method for RESTful APIs (PUT, DELETE and so on)
$exchanger->call($apiURIHere, $methodHere, $authHere, $extendHeadersHere, $bodyHere);

```

# Issues and Pull requests

- 追加機能がほしかったり、バグを修正したよというプルリクエストは大歓迎です。ぜひ開発にご協力ください。
