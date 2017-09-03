<?php

namespace Memoin\API\Adapters;

class BaseCaller {

    /**
     * @var BaseAdapter
     */
    private $adapter = null;

    public function __construct (BaseAdapter $adapter) {
        $this->adapter = $adapter;
    }

}