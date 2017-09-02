<?php
namespace Memoin\Core;
use Memoin\Credentials\Credential;

class Exchanger {

    private $exchanger = null;

    public function __construct ($exchangerName, Credential $credential = null) {
        $path = __DIR__ . '/../Exchangers/' . $exchangerName . '.php';
        if (!is_file($path)) throw new \RuntimeException('Not found exchanger "' . $exchangerName . '"');

        // Load exchanger
        require $path;

        $exchangerName = '\\Memoin\\Exchangers\\' . $exchangerName;
        $this->exchanger = new $exchangerName();

        if ($credential !== null) {
            $this->exchanger->setCredential($credential);
        }
    }

    /**
     * @return \Memoin\API\Base
     */
    public function getExchanger () {
        return $this->exchanger;
    }

}