<?php

namespace Alpixel\Bundle\ShopBundle\Helper\Cart;

use Alpixel\Bundle\ShopBundle\AlpixelShopEvents;
use Alpixel\Bundle\ShopBundle\Entity\Cart;
use Alpixel\Bundle\ShopBundle\Entity\CartProduct;
use Alpixel\Bundle\ShopBundle\Entity\Customer;
use Alpixel\Bundle\ShopBundle\Entity\Order;
use Alpixel\Bundle\ShopBundle\Entity\Product;
use Alpixel\Bundle\ShopBundle\Event\CartDiscountCalculationEvent;
use Alpixel\Bundle\ShopBundle\Event\CartEvent;
use Alpixel\Bundle\ShopBundle\Event\CartProcessEvent;
use Alpixel\Bundle\ShopBundle\Exception\NoProductException;
use Alpixel\Bundle\ShopBundle\Exception\OutOfStockException;
use Doctrine\ORM\EntityManager;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;

class CartManager
{
    protected $entityManager;
    protected $sessionCart;
    protected $customer;
    protected $cartValidity;
    protected $dispatcher;
    protected $priceHelper;

    /**
     * CartManager constructor.
     *
     * @param EntityManager            $entityManager
     * @param TokenStorage             $tokenStorage
     * @param AuthorizationChecker     $authorizationChecker
     * @param EventDispatcherInterface $dispatcher
     * @param SessionCart              $sessionCart
     * @param CartValidity             $cartValidity
     * @param PriceHelper              $helper
     */
    public function __construct(EntityManager $entityManager, TokenStorage $tokenStorage, AuthorizationChecker $authorizationChecker,
                                EventDispatcherInterface $dispatcher, SessionCart $sessionCart, CartValidity $cartValidity, PriceHelper $helper)
    {
        $this->entityManager = $entityManager;
        $this->sessionCart = $sessionCart;
        $this->cartValidity = $cartValidity;
        $this->dispatcher = $dispatcher;
        $this->priceHelper = $helper;

        if ($tokenStorage->getToken() !== null) {
            $user = $tokenStorage->getToken()->getUser();
            if ($user !== null && $authorizationChecker->isGranted('IS_AUTHENTICATED_FULLY') && $user instanceof Customer) {
                $this->customer = $tokenStorage->getToken()->getUser();
                $this->createCart();
            }
        }
    }

    protected function createCart()
    {
        if ($this->getCurrentCart() !== null) {
            return $this->getCurrentCart();
        }
        $cart = $this->newCart();

        return $cart;
    }

    public function getCurrentCart()
    {
        $cart = $this->sessionCart->getCurrent();
        if ($cart !== null) {
            return $this->entityManager->getRepository('AlpixelShopBundle:Cart')
                                       ->find($cart);
        }

        if ($this->customer !== null) {
            $cart = $this->entityManager->getRepository('AlpixelShopBundle:Cart')
                                        ->findOneCurrentCartByCustomer($this->customer);
            if ($cart !== null) {
                $this->sessionCart->setCurrent($cart);
            }
        }

        return $cart;
    }

    public function getTotal($withProductDiscount = true, $withCartDiscount = true)
    {
        $total = 0;
        $cart = $this->getCurrentCart();

        foreach ($cart->getCartProducts() as $cartProduct) {
            $total += $this->priceHelper->getProductPrice($cartProduct->getProduct(), $cartProduct->getQuantity(), $withProductDiscount);
        }

        if ($withCartDiscount) {
            $discount = $this->getCartDiscount();
            $total -= $this->priceHelper->getDiscountAmount($total, $discount);
        }

        return $total;
    }

    public function getCartDiscount()
    {
        $event = new CartDiscountCalculationEvent($this->getCurrentCart());
        $this->dispatcher->dispatch(AlpixelShopEvents::CART_DISCOUNT_CALCULATION, $event);

        return $event->getDiscount();
    }

    private function newCart()
    {
        $cart = new Cart();
        $cart->setCustomer($this->customer);
        $this->saveCart($cart);

        return $cart;
    }

    protected function saveCart(Cart $cart)
    {
        $this->entityManager->persist($cart);
        $this->entityManager->flush();
        $this->sessionCart->setCurrent($cart);
    }

    public function cancelCurrentCart()
    {
        $cart = $this->getCurrentCart();
        if ($cart === null) {
            return $this;
        }

        // To avoid duplicate empty cart in database, we check
        // if the current cart have more than one product
        if (count($cart->getCartProducts()) > 0) {
            $this->sessionCart->removeCurrent();
            $cart = $this->newCart();
            $this->saveCart($cart);
        }

        return $this;
    }

    public function addToCart(Product $product, $quantity = 0)
    {
        $quantity = (int) $quantity;
        if ($quantity <= 0) {
            return false;
        }

        $cartProduct = $this->getCartProductInCartByProduct($product);
        if ($cartProduct !== false) {
            $this->addQuantityToCartProduct($cartProduct, $quantity);
        } else {
            // Create a new CartProduct for Cart if the product doesn't exists or add quantity to CartProduct if exists
            $cartProduct = new CartProduct();
            $cartProduct->setProduct($product);
            $this->addQuantityToCartProduct($cartProduct, $quantity);
            $cart = $this->getCurrentCart();
            $cart->addCartProduct($cartProduct);
            $this->saveCart($cart);
        }

        return true;
    }

    public function getCartProductInCartByProduct(Product $product)
    {
        $cart = $this->getCurrentCart();
        if ($cart !== null) {
            foreach ($cart->getCartProducts() as $cartProduct) {
                if ($cartProduct->getProduct()->getId() === $product->getId()) {
                    return $cartProduct;
                }
            }
        }

        return false;
    }

    public function addQuantityToCartProduct(CartProduct $cartProduct, $quantity = 0)
    {
        $this->setNewQuantityToCartProduct($cartProduct, $cartProduct->getQuantity() + $quantity);

        return $this;
    }

    public function setNewQuantityToCartProduct(CartProduct $cartProduct, $quantity)
    {
        if (!$this->cartValidity->stockIsAvailableByStrategy($cartProduct->getProduct(), $quantity)) {
            return false;
        }

        $cartProduct->setQuantity($quantity);
        $this->entityManager->persist($cartProduct);
        $this->entityManager->flush();

        return $this;
    }

    public function removeProduct(CartProduct $cartProduct)
    {
        $this->removeQuantityFromCartProduct($cartProduct, $cartProduct->getQuantity());
    }

    public function removeQuantityFromCartProduct(CartProduct $cartProduct, $quantity = 0)
    {
        $quantity = (int) $quantity;

        $cartProduct->removeQuantity($quantity);

        if ($cartProduct->getQuantity() <= 0) {
            $this->entityManager->remove($cartProduct);
        } else {
            $this->entityManager->persist($cartProduct);
        }

        $this->entityManager->flush();

        return $this;
    }

    public function getTotalProductsQuantity()
    {
        $cart = $this->getCurrentCart();
        $total = 0;
        foreach ($cart->getCartProducts() as $cartProduct) {
            $total += $cartProduct->getQuantity();
        }

        return $total;
    }

    /**
     * Valid the current cart if is not valid the product.
     */
    public function validateCartAndProcessOrder($orderExtraData)
    {
        $cart = $this->getCurrentCart();
        if ($cart->getCartProducts()->count() <= 0) {
            throw new NoProductException();
        }
        $customer = $cart->getCustomer();

        $event = new CartEvent($cart, $customer);
        $this->dispatcher->dispatch(AlpixelShopEvents::CART_PRE_VALIDATION, $event);

        $validity = $this->cartValidity->checkValidityStockInCart($cart);
        if ($validity === false || !empty($validity)) {
            throw new OutOfStockException();
        }

        $event = new CartProcessEvent($cart, $customer, $orderExtraData);
        $this->dispatcher->dispatch(AlpixelShopEvents::CART_PROCESS, $event);
        $order = $event->getOrder();

        if ($order instanceof Order) {
            $this->customer = $order->getCustomer();
            $this->newCart();
        }

        return $order;
    }

    /**
     * @return Customer
     */
    public function getCustomer()
    {
        return $this->customer;
    }
}
