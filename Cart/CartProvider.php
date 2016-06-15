<?php

namespace Alpixel\Bundle\ShopBundle\Cart;

use Alpixel\Bundle\ShopBundle\Entity\Cart;
use Alpixel\Bundle\ShopBundle\Model\CartInterface;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class CartProvider
{
    /**
     * @var array An array of configuration
     */
    private $configuration;

    /**
     * @var CartAccess
     */
    private $cartAccess;

    /**
     * @var mixed
     */
    private $user;

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var CartInterface
     */
    private $cart = null;

    /**
     * CartProvider constructor.
     * @param $configuration
     * @param EntityManager $entityManager
     * @param CartAccess $cartAccess
     */
    public function __construct($configuration, EntityManager $entityManager, CartAccess $cartAccess)
    {
        if (!$cartAccess->isAuthorized()) {
            throw new AccessDeniedException();
        }
        
        $this->configuration = $configuration;
        $this->cartAccess = $cartAccess;
        $this->entityManager = $entityManager;
        $this->user = $this->cartAccess->getUser();
    }

    public function setCart(CartInterface $cart)
    {
        $this->cart = $cart;
    }

    public function getCart()
    {
        if ($this->cart) {
            return $this->cart;
        }

        $this->cart = $this
            ->entityManager
            ->getRepository('AlpixelShopBundle:Cart')
            ->findOneCurrentCartByCustomer($this->user);

        if ($this->cart === null) {
            $this->cart = $this->createCart();
        }

        return $this->cart;
    }

    public function removeCart()
    {
        if ($this->cart && $this->cart->hasCartProducts()) {
            $this->entityManager->remove($this->cart);
            $this->entityManager->flush();
        }

        return $this;
    }

    protected function createCart()
    {
        $cart = new Cart();
        $cart->setCustomer($this->user);
        $this->entityManager->persist($cart);
        $this->entityManager->flush($cart);

        return $cart;
    }
}