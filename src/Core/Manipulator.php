<?php
namespace Memoin\Core;
use Memoin\Credentials\Credential;
use Memoin\Core\Exchanger;
use Memoin\Exceptions;
use Memoin\Enums\Currency;

class Manipulator
{

    /**
     * @var array manipulating exchangers
     */
    private $exchangers = [];

    /**
     * Manipulator constructor.
     * @param array $exchangers
     * @throws Exceptions\Manipulator
     */
    public function __construct (array $exchangers)
    {
        foreach ($exchangers as $exchanger) {
            if (!($exchanger instanceof Exchanger)) throw new Exceptions\Manipulator('Does not instance of Exchanger');
        }
        $this->exchangers = $exchangers;
    }

    /**
     * Call exchangers native APIs
     *
     * @param $name
     * @param $arguments
     * @return array
     * @throws Exceptions\Credential
     * @throws Exceptions\Manipulator
     */
    public function __call ($name, $arguments)
    {

        if (preg_match('/\A(get)([A-Z].*)\z/', $name, $matches)) {
            $action = trim($matches[1]);
            $currency = strtoupper($matches[2]);
            $definedCurrencies = Currency::getNames();
            if (!isset($definedCurrencies[$currency])) throw new Exceptions\Manipulator('Undefined currency "' . $currency . '"');

            // get defined currencies
            switch (true) {
                case $action === 'get':
                    $result = [];
                    foreach ($this->exchangers as $exchanger) {
                        $result[$exchanger->getExchanger()->getName()] = $exchanger->getExchanger()->getAdapter()->getCurrency($definedCurrencies[$currency]);
                    }
                    return $result;
            }

            throw new Exceptions\Credential('Undefined action "' . $action . '"');
        }
    }

}