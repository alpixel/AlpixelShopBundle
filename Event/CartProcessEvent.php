<?php

namespace Alpixel\Bundle\ShopBundle\Event;

use Alpixel\Bundle\ShopBundle\Entity\Cart;
use Alpixel\Bundle\ShopBundle\Entity\Customer;
use Alpixel\Bundle\ShopBundle\Entity\Order;
use Symfony\Component\EventDispatcher\Event;

class CartProcessEvent extends Event
{
    protected $cart;
    protected $customer;
    protected $order;
    protected $extraData;

    public function __construct(Cart $cart, Customer $customer, $extraData)
    {
        $this->cart = $cart;
        $this->customer = $customer;
        $this->extraData = $extraData;
    }

    public function getCart()
    {
        return $this->cart;
    }

    public function getCustomer()
    {
        return $this->customer;
    }

    /**
     * @return array
     */
    public function getExtraData()
    {
        return $this->extraData;
    }

    /**
     * @param array $extraData
     */
    public function setExtraData($extraData)
    {
        $this->extraData = $extraData;
    }

    public function getOrder()
    {
        return $this->order;
    }

    public function setOrder(Order $order)
    {
        $this->order = $order;
    }
}
