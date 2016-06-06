<?php

namespace Alpixel\Bundle\ShopBundle\Event;

use Alpixel\Bundle\ShopBundle\Entity\Cart;
use Symfony\Component\EventDispatcher\Event;

/**
 * Event for CartDiscountCalculationEvent.
 */
class CartDiscountCalculationEvent extends Event
{
    /**
     * @var Cart
     */
    protected $cart;
    /**
     * @var
     */
    protected $discount;

    /**
     * CartDiscountCalculationEvent constructor.
     *
     * @param Cart $cart
     */
    public function __construct(Cart $cart)
    {
        $this->discount = 0;
        $this->cart = $cart;
    }

    /**
     * @return Cart
     */
    public function getCart():Cart
    {
        return $this->cart;
    }

    /**
     * @return mixed
     */
    public function getDiscount():float
    {
        return $this->discount;
    }

    /**
     * @param mixed $discount
     *
     * @return CartDiscountCalculationEvent
     */
    public function setDiscount(float $discount):CartDiscountCalculationEvent
    {
        $this->discount = $discount;

        return $this;
    }

    /**
     * @param mixed $discount
     *
     * @return CartDiscountCalculationEvent
     */
    public function addDiscount(float $discount):CartDiscountCalculationEvent
    {
        $this->discount += $discount;

        return $this;
    }
}
