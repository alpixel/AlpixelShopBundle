<?php

namespace Alpixel\Bundle\ShopBundle\Event;

use Alpixel\Bundle\ShopBundle\Entity\Cart;
use Alpixel\Bundle\ShopBundle\Entity\Customer;
use Alpixel\Bundle\ShopBundle\Entity\Order;
use Symfony\Component\EventDispatcher\Event;

/**
 * Event for cart.
 */
class CartEvent extends Event
{
    protected $cart;
    protected $customer;
    protected $order;

    public function __construct(Cart $cart, Customer $customer = null)
    {
        $this->cart = $cart;
        $this->customer = $customer;
    }

    public function getCart()
    {
        return $this->cart;
    }

    public function getCustomer()
    {
        return $this->customer;
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
