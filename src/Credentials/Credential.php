<?php
namespace Memoin\Credentials;
use Memoin\Exceptions;

class Credential {

    /**
     * @var array credential parameters
     */
    private $parameters = [];

    /**
     * Credential constructor.
     * @param array $parameters
     */
    public function __construct (array $parameters) {
        $this->parameters = $parameters;
    }

    /**
     * Call
     *
     * @param $name
     * @param $arguments
     * @return bool|mixed
     * @throws Exceptions\Credential
     */
    public function __call ($name, $arguments) {
        if (preg_match('/\A(get|has)([A-Z].*)\z/', $name, $matches)) {
            $action = trim($matches[1]);
            $value = strtoupper(ltrim(preg_replace('/([A-Z])/', '_$1', $matches[2]), '_'));
            switch (true) {
                case $action === 'has':
                    return isset($this->parameters[$value]);
                case $action === 'get':
                    if (isset($this->parameters[$value])) {
                        return $this->parameters[$value];
                    }
                    throw new Exceptions\Credential('Undefined parameter "' . $value . '"');

            }
            throw new Exceptions\Credential('Undefined action "' . $action . '"');
        }
        throw new Exceptions\Credential('Undefined method "' . $name . '"');
    }

}