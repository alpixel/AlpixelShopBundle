<?php

namespace Alpixel\Bundle\ShopBundle\Cart;

use Alpixel\Bundle\ShopBundle\Entity\Cart;
use Alpixel\Bundle\ShopBundle\Model\CartInterface;
use Alpixel\Bundle\ShopBundle\Model\CustomerInterface;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class CartProvider
{
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
     * @var string Cart class entity
     */
    protected $cartClass;

    /**
     * @var string Cart repository
     */
    protected $cartRepositoryClass;

    /**
     * @var \Doctrine\Common\Persistence\ObjectRepository
     */
    protected $cartRepository;

    /**
     * @var CartInterface
     */
    private $cart = null;

    /**
     * CartProvider constructor.
     * @param $configuration
     * @param Registry $registry
     * @param CartAccess $cartAccess
     */
    public function __construct($configuration, Registry $registry, CartAccess $cartAccess)
    {
        if (!$cartAccess->isAuthorized()) {
            throw new AccessDeniedException();
        }

        $this->cartClass = $configuration['class'];
        $this->cartRepositoryClass = $configuration['repository'];
        $this->cartAccess = $cartAccess;
        $this->entityManager = $registry->getManagerForClass($this->cartClass);
        $this->cartRepository = $this->entityManager->getRepository($this->cartClass);
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

        $this->cart = $this->getCartRepository()
            ->findCurrentCartByCustomer($this->user);

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
        $cartClass = $this->getCartClass();

        $cart = new $cartClass();
        $cart->setCustomer($this->user);
        $this->entityManager->persist($cart);
        $this->entityManager->flush($cart);

        return $cart;
    }

    public function getCartRepository()
    {
        return $this->cartRepository;
    }

    public function getCartClass()
    {
        return $this->cartClass;
    }
}