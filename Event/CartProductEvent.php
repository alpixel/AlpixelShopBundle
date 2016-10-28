<?php

namespace Alpixel\Bundle\ShopBundle\Event;

use Alpixel\Bundle\ShopBundle\Entity\Cart;
use Alpixel\Bundle\ShopBundle\Entity\CartProduct;
use Alpixel\Bundle\ShopBundle\Entity\Customer;
use Alpixel\Bundle\ShopBundle\Entity\Order;
use Symfony\Component\EventDispatcher\Event;


/**
 * @author Benjamin HUBERT <benjamin@alpixel.fr>
 */
class CartProductEvent extends Event
{
    /**
     * @var \Alpixel\Bundle\ShopBundle\Entity\CartProduct
     */
    protected $cartProduct;

    /**
     * CartProductEvent constructor.
     * @param \Alpixel\Bundle\ShopBundle\Entity\CartProduct $cartProduct
     */
    public function __construct(CartProduct $cartProduct)
    {
        $this->cartProduct = $cartProduct;
    }

    /**
     * @return \Alpixel\Bundle\ShopBundle\Entity\CartProduct
     */
    public function getCartProduct()
    {
        return $this->cartProduct;
    }
}
