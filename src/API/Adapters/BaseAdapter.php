<?php

namespace Memoin\API\Adapters;
use Memoin\API\BaseController;
use Memoin\Exceptions;

class BaseAdapter {

    /**
     * @var BaseController|null
     */
    private $controller = null;

    public function __construct(BaseController $controller) {
        $this->controller = $controller;
    }

    /**
     * @return BaseController|null
     */
    public function getController () {
        return $this->controller;
    }

    /**
     * @return Boards
     */
    public function getBoards () {
        return new Boards($this);
    }

    /**
     * @return Tickers
     */
    public function getTickers () {
        return new Tickers($this);
    }

    /**
     * @return Addresses
     */
    public function getAddresses () {
        return new Addresses($this);
    }

    /**
     * @return BankAccounts
     */
    public function getBankAccounts () {
        return new BankAccounts($this);
    }

    /**
     * @return Withdraws
     */
    public function getWithdraws () {
        return new Withdraws($this);
    }

    /**
     * @return Orders
     */
    public function getOrders () {
        return new Orders($this);
    }

    /**
     * @param $name
     * @param string $from
     * @return Currencies
     */
    public function getCurrencies ($name, $from = \Memoin\Enums\Currency::JPY) {
        return new Currencies($this);
    }

}