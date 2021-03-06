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

        $this->exchanger = new $exchangerName();

        if ($credential !== null) {
            $this->exchanger->setCredential($credential);
        }
    }

    /**
     * @return \Memoin\API\BaseExchanger
     */
    public function getExchanger () {
        return $this->exchanger;
    }

}