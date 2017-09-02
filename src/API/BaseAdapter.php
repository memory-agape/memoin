<?php

namespace Memoin\API;
use Memoin\Enums\Currency;
use Memoin\Exceptions;

class BaseAdapter {

    /**
     * @var BaseController|null
     */
    private $controller = null;

    public function __construct(BaseController $controller) {
        $this->controller = $controller;
    }

    public function getController () {
        return $this->controller;
    }


    public function getBoards () {
        return null;
    }

    public function getTickers () {
        return null;
    }

    public function getAddresses () {
        return null;
    }

    public function getBankAccounts () {
        return null;
    }

    public function withdraws () {
        return null;
    }

    public function order () {
        return null;
    }

    public function orders () {
        return null;
    }

    public function cancelOrder () {
        return null;
    }

    public function getCurrency ($name, $from = Currency::JPY) {
        return null;
    }

}