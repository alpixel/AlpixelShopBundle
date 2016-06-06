<?php

namespace Alpixel\Bundle\ShopBundle\Helper\Cart;

use Alpixel\Bundle\ShopBundle\Entity\Cart;
use Symfony\Component\HttpFoundation\Session\Session;

class SessionCart
{
    const SESSION_CART_ID = 'alpixel_shop_current_cart';

    private $session;

    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    public function currentCartSessionExist()
    {
        return $this->session->has(self::SESSION_CART_ID);
    }

    public function removeCurrent()
    {
        if ($this->currentCartSessionExist()) {
            $this->session->remove(self::SESSION_CART_ID);
        }

        return $this;
    }

    public function setCurrent(Cart $cart)
    {
        $this->session->set(self::SESSION_CART_ID, $cart->getId());

        return $this;
    }

    public function getCurrent()
    {
        if ($this->currentCartSessionExist()) {
            return $this->session->get(self::SESSION_CART_ID);
        }
    }
}
