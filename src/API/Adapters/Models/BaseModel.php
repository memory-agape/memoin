<?php

namespace Memoin\API\Adapters\Models;

use Memoin\Exceptions;

class BaseModel {

    private $variables = [];

    public function __call ($name, $arguments) {
        if (preg_match('/\A(get|set|has)(.+)\z/', $name, $matches)) {
            $type = trim($matches[1]);
            $variableName = strtoupper(ltrim(preg_replace('/([A-Z])/', '_$1', $matches[2]), '_'));
            switch (true) {
                case $type === 'get':
                    return $this->variables[$variableName] ?? null;
                case $type === 'set':
                    $this->variables[$variableName] = $arguments[0] ?? null;
                    break;
                case $type === 'has':
                    return isset($this->variables[$variableName]);
            }
            throw new Exceptions\Model('Undefined operator type "' . $type . '"');
        }
        throw new Exceptions\Model('Unknown operator');
    }

}