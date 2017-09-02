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

    /**
     * @return BaseController|null
     */
    public function getController () {
        return $this->controller;
    }

    /**
     * @return Adapters\Boards
     */
    public function getBoards () {
        return new Adapters\Boards();
    }

    /**
     * @return Adapters\Tickers
     */
    public function getTickers () {
        return new Adapters\Tickers();
    }

    /**
     * @return Adapters\Addresses
     */
    public function getAddresses () {
        return new Adapters\Addresses();
    }

    /**
     * @return Adapters\BankAccounts
     */
    public function getBankAccounts () {
        return new Adapters\BankAccounts();
    }

    /**
     * @return Adapters\Withdraw
     */
    public function withdraws () {
        return new Adapters\Withdraw();
    }

    /**
     * @return Adapters\Order
     */
    public function order () {
        return new Adapters\Order();
    }

    /**
     * @return Adapters\OrderHistories
     */
    public function orderHistories () {
        return new Adapters\OrderHistories();
    }

    /**
     * @return Adapters\OrderCancel
     */
    public function orderCancel () {
        return new Adapters\OrderCancel();
    }

    /**
     * @param $name
     * @param string $from
     * @return Adapters\Currency
     */
    public function getCurrency ($name, $from = Currency::JPY) {
        return new Adapters\Currency();
    }

}