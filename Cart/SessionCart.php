<?php

namespace Alpixel\Bundle\ShopBundle\Cart;

use Alpixel\Bundle\ShopBundle\Model\CartInterface;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Class SessionCart
 * @package Alpixel\Bundle\ShopBundle\Cart
 */
class SessionCart
{
    const CART_ID = 'alpixel_shop.session.cart_id';

    /**
     * @var Session
     */
    private $session;

    /**
     * SessionCart constructor.
     * @param Session $session
     */
    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    /**
     * @return bool
     */
    public function exists()
    {
        return $this->session->has(self::CART_ID);
    }

    /**
     * @return $this
     */
    public function remove()
    {
        if ($this->exists()) {
            $this->session->remove(self::CART_ID);
        }

        return $this;
    }

    /**
     * @param CartInterface $cart
     * @return $this
     */
    public function set(CartInterface $cart)
    {
        $this->session->set(self::CART_ID, $cart->getId());

        return $this;
    }

    /**
     * Return the current Cart id
     *
     * @return null|int
     */
    public function get()
    {
        if ($this->exists()) {
            return $this->session->get(self::CART_ID);
        }
    }
}
