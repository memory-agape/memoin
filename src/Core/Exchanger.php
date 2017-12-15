<?php
namespace Memoin\Core;
use Memoin\Credentials\Credential;

class Exchanger
{

    private $exchanger = null;

    /**
     * Exchanger constructor.
     * @param $exchangerName
     * @param Credential|null $credential
     */
    public function __construct($exchangerName, Credential $credential = null)
    {
        $path = __DIR__ . '/../Exchangers/' . $exchangerName . '/Controller.php';
        if (!is_file($path)) {
            throw new \RuntimeException('Not found exchanger controller "' . $exchangerName . '"');
        }

        // Load exchanger
        require_once $path;

        $exchangerName = '\\Memoin\\Exchangers\\' . $exchangerName . '\\Controller';
        $this->exchanger = new $exchangerName();

        if ($credential !== null) {
            $this->exchanger->setCredential($credential);
        }
    }

    /**
     * @return \Memoin\API\BaseController
     */
    public function getExchanger () {
        return $this->exchanger;
    }

}