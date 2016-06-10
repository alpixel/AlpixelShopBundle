<?php

namespace Alpixel\Bundle\ShopBundle\Helper\Cart;

use Alpixel\Bundle\ShopBundle\AlpixelShopEvents;
use Alpixel\Bundle\ShopBundle\Entity\Product;
use Alpixel\Bundle\ShopBundle\Entity\ProductPrice;
use Alpixel\Bundle\ShopBundle\Event\ProductCalculationEvent;
use Doctrine\ORM\EntityManager;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;

/**
 * Class PriceHelper.
 */
class PriceHelper
{
    /**
     * @var AuthorizationChecker
     */
    protected $authorizationChecker;

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;
    /**
     * @var EntityManager
     */
    protected $entityManager;
    /**
     * @var TokenStorage
     */
    protected $tokenStorage;
    /**
     * @var
     */
    protected $defaultCurrency;

    /**
     * PriceHelper constructor.
     *
     * @param TokenStorage             $tokenStorage
     * @param AuthorizationChecker     $authorizationChecker
     * @param EntityManager            $entityManager
     * @param EventDispatcherInterface $eventDispatcher
     * @param                          $defaultCurrency
     */
    public function __construct(TokenStorage $tokenStorage, AuthorizationChecker $authorizationChecker, EntityManager $entityManager, EventDispatcherInterface $eventDispatcher, $defaultCurrency)
    {
        $this->authorizationChecker = $authorizationChecker;
        $this->defaultCurrency = $defaultCurrency;
        $this->tokenStorage = $tokenStorage;
        $this->eventDispatcher = $eventDispatcher;
        $this->entityManager = $entityManager;
    }

    /**
     * @param float $initialValue
     * @param float $discount
     *
     * @return float
     */
    public function applyDiscount($initialValue, $discount)
    {
        return $initialValue - $this->getDiscountAmount($initialValue, $discount);
    }

    /**
     * @param float $initialValue
     * @param float $discount
     *
     * @return float
     */
    public function getDiscountAmount($initialValue, $discount)
    {
        return $initialValue * $discount / 100;
    }

    /**
     * @param Product $product
     * @param bool    $withDiscount
     * @param int     $quantity
     *
     * @return float
     */
    public function getProductPrice(Product $product, $quantity = 1, $withDiscount = true)
    {
        $price = 0;
        $token = $this->tokenStorage->getToken();
        $user = null;
        $currency = null;

        if ($token !== null) {
            $user = $token->getUser();
            if ($user !== null && $this->authorizationChecker->isGranted('IS_AUTHENTICATED_FULLY', $user)) {
                $currency = $user->getCurrency();
            } else {
                $user = null;
            }
        }

        //If no currency detected, we check for a default one
        if ($currency === null) {
            $currency = $this->entityManager
                ->getRepository('AlpixelShopBundle:Currency')
                ->findOneByName($this->defaultCurrency);
        }

        $productPrice = $product->getPrice($currency);
        if ($productPrice !== null && $productPrice instanceof ProductPrice) {
            $price = $productPrice->getAmount();
            if ($withDiscount) {
                $event = new ProductCalculationEvent($price, $product, $currency, $user);
                $this->eventDispatcher->dispatch(AlpixelShopEvents::PRODUCT_PRICE_CALCULATION, $event);
                $price = $event->getPrice();
            }
        }

        return $price * $quantity;
    }
}
