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
use Alpixel\Bundle\ShopBundle\Event\CartProductEvent;
use Alpixel\Bundle\ShopBundle\Exception\NoProductException;
use Alpixel\Bundle\ShopBundle\Exception\OutOfStockException;
use Doctrine\ORM\EntityManager;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;

/**
 * @author Benjamin HUBERT <benjamin@alpixel.fr>
 */
class CartManager
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $entityManager;
    /**
     * @var \Alpixel\Bundle\ShopBundle\Helper\Cart\SessionCart
     */
    protected $sessionCart;
    /**
     * @var mixed
     */
    protected $customer;
    /**
     * @var \Alpixel\Bundle\ShopBundle\Helper\Cart\CartValidity
     */
    protected $cartValidity;
    /**
     * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
     */
    protected $dispatcher;
    /**
     * @var \Alpixel\Bundle\ShopBundle\Helper\Cart\PriceHelper
     */
    protected $priceHelper;

    /**
     * CartManager constructor.
     *
     * @param EntityManager $entityManager
     * @param TokenStorage $tokenStorage
     * @param AuthorizationChecker $authorizationChecker
     * @param EventDispatcherInterface $dispatcher
     * @param SessionCart $sessionCart
     * @param CartValidity $cartValidity
     * @param PriceHelper $helper
     */
    public function __construct(
        EntityManager $entityManager,
        TokenStorage $tokenStorage,
        AuthorizationChecker $authorizationChecker,
        EventDispatcherInterface $dispatcher,
        SessionCart $sessionCart,
        CartValidity $cartValidity,
        PriceHelper $helper
    ) {
        $this->entityManager = $entityManager;
        $this->sessionCart = $sessionCart;
        $this->cartValidity = $cartValidity;
        $this->dispatcher = $dispatcher;
        $this->priceHelper = $helper;

        if ($tokenStorage->getToken() !== null) {
            $user = $tokenStorage->getToken()->getUser();
            if ($user !== null && $authorizationChecker->isGranted(
                    'IS_AUTHENTICATED_FULLY'
                ) && $user instanceof Customer
            ) {
                $this->customer = $tokenStorage->getToken()->getUser();
                $this->createCart();
            }
        }
    }

    /**
     * @return \Alpixel\Bundle\ShopBundle\Entity\Cart|mixed|null|object
     */
    protected function createCart()
    {
        if ($this->getCurrentCart() !== null) {
            return $this->getCurrentCart();
        }
        $cart = $this->newCart();

        return $cart;
    }

    /**
     * @return \Alpixel\Bundle\ShopBundle\Entity\Cart|mixed|null|object
     */
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

    /**
     * @param bool $withProductDiscount
     * @param bool $withCartDiscount
     * @return float|int
     */
    public function getTotal($withProductDiscount = true, $withCartDiscount = true)
    {
        $total = 0;
        $cart = $this->getCurrentCart();

        foreach ($cart->getCartProducts() as $cartProduct) {
            $total += $this->priceHelper->getProductPrice(
                $cartProduct->getProduct(),
                $cartProduct->getQuantity(),
                $withProductDiscount
            );
        }

        if ($withCartDiscount) {
            $discount = $this->getCartDiscount();
            $total -= $this->priceHelper->getDiscountAmount($total, $discount);
        }

        return $total;
    }

    /**
     * @return float|mixed
     */
    public function getCartDiscount()
    {
        $event = new CartDiscountCalculationEvent($this->getCurrentCart());
        $this->dispatcher->dispatch(AlpixelShopEvents::CART_DISCOUNT_CALCULATION, $event);

        return $event->getDiscount();
    }

    /**
     * @return \Alpixel\Bundle\ShopBundle\Entity\Cart
     */
    private function newCart()
    {
        $cart = new Cart();
        $cart->setCustomer($this->customer);
        $this->saveCart($cart);

        return $cart;
    }

    /**
     * @param \Alpixel\Bundle\ShopBundle\Entity\Cart $cart
     */
    protected function saveCart(Cart $cart)
    {
        $this->entityManager->persist($cart);
        $this->entityManager->flush();
        $this->sessionCart->setCurrent($cart);
    }

    /**
     * @return $this
     */
    public function cancelCurrentCart()
    {
        $cart = $this->getCurrentCart();
        if ($cart === null) {
            return $this;
        }

        // To avoid duplicate empty cart in database, we check
        // if the current cart have more than one product
        $this->dispatcher->dispatch(AlpixelShopEvents::CART_PRE_EMPTY, new CartEvent($cart));

        if (count($cart->getCartProducts()) > 0) {
            $this->sessionCart->removeCurrent();
            $this->dispatcher->dispatch(AlpixelShopEvents::CART_POST_EMPTY, new CartEvent($cart));

            $cart = $this->newCart();
            $this->saveCart($cart);
            $this->dispatcher->dispatch(AlpixelShopEvents::CART_CREATED, new CartEvent($cart));
        } else {
            $this->dispatcher->dispatch(AlpixelShopEvents::CART_POST_EMPTY, new CartEvent($cart));
        }

        return $this;
    }

    /**
     * @param \Alpixel\Bundle\ShopBundle\Entity\Product $product
     * @param int $quantity
     * @return bool
     */
    public function addToCart(Product $product, $quantity = 0)
    {
        $quantity = (int)$quantity;
        if ($quantity <= 0) {
            return false;
        }

        $cartProduct = $this->getCartProductInCartByProduct($product);

        if ($cartProduct !== false) {
            $this->dispatcher->dispatch(AlpixelShopEvents::CART_PRODUCT_PRE_ADD, new CartProductEvent($cartProduct));
            $this->addQuantityToCartProduct($cartProduct, $quantity);
        } else {
            // Create a new CartProduct for Cart if the product doesn't exists or add quantity to CartProduct if exists
            $cartProduct = new CartProduct();
            $cartProduct->setProduct($product);
            $this->addQuantityToCartProduct($cartProduct, $quantity);
            $cart = $this->getCurrentCart();
            $this->dispatcher->dispatch(AlpixelShopEvents::CART_PRODUCT_PRE_ADD, new CartProductEvent($cartProduct));
            $cart->addCartProduct($cartProduct);
            $this->saveCart($cart);
        }

        $this->dispatcher->dispatch(AlpixelShopEvents::CART_PRODUCT_POST_ADD, new CartProductEvent($cartProduct));
        $this->dispatcher->dispatch(AlpixelShopEvents::CART_UPDATED, new CartEvent($cartProduct->getCart()));

        return true;
    }

    /**
     * @param \Alpixel\Bundle\ShopBundle\Entity\Product $product
     * @return bool|mixed
     */
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

    /**
     * @param \Alpixel\Bundle\ShopBundle\Entity\CartProduct $cartProduct
     * @param int $quantity
     * @return $this
     */
    public function addQuantityToCartProduct(CartProduct $cartProduct, $quantity = 0)
    {
        $this->setNewQuantityToCartProduct($cartProduct, $cartProduct->getQuantity() + $quantity);

        return $this;
    }

    /**
     * @param \Alpixel\Bundle\ShopBundle\Entity\CartProduct $cartProduct
     * @param $quantity
     * @return $this|bool
     */
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

    /**
     * @param \Alpixel\Bundle\ShopBundle\Entity\CartProduct $cartProduct
     */
    public function removeProduct(CartProduct $cartProduct)
    {
        $this->dispatcher->dispatch(AlpixelShopEvents::CART_PRODUCT_PRE_REMOVE, new CartProductEvent($cartProduct));
        $this->removeQuantityFromCartProduct($cartProduct, $cartProduct->getQuantity());
        $this->dispatcher->dispatch(AlpixelShopEvents::CART_PRODUCT_POST_REMOVE, new CartProductEvent($cartProduct));
    }

    /**
     * @param \Alpixel\Bundle\ShopBundle\Entity\CartProduct $cartProduct
     * @param int $quantity
     * @return $this
     */
    public function removeQuantityFromCartProduct(CartProduct $cartProduct, $quantity = 0)
    {
        $quantity = (int)$quantity;

        $cartProduct->removeQuantity($quantity);

        $cart = $cartProduct->getCart();
        $cart->removeCartProduct($cartProduct);

        if ($cartProduct->getQuantity() <= 0) {
            $this->entityManager->remove($cartProduct);
        } else {
            $this->entityManager->persist($cartProduct);
        }

        $this->entityManager->flush();

        if(count($cart->getCartProducts()) === 0) {
            $this->cancelCurrentCart();
        }

        return $this;
    }

    /**
     * @return int
     */
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

    /**
     * @return Cart|null
     */
    public function saveKeepCart($name = null)
    {
        $cart = $this->getCurrentCart();

        if ($cart !== null && $cart->getCartProducts()->count() > 0 && $this->getCustomer() !== null) {
            if (is_string($name) && !empty($name)) {
                $cart->setName($name);
                $this->entityManager->persist($cart);
                $this->entityManager->flush();
            }

            return $this->newCart();
        }

        return null;
    }

    /**
     * @param Cart $cart
     * @param Customer|null $customer
     * @return bool
     */
    public function switchCurrentCart(Cart $cart, Customer $customer = null)
    {
        $isSwitched = false;
        if ($cart->getOrder() === null && $this->cartBelongsToCustomer($cart, $customer)) {
            $this->sessionCart->setCurrent($cart);
            $isSwitched = true;
        }

        return $isSwitched;
    }

    /**
     * @return array
     */
    public function getSavedCarts()
    {
        $carts = [];

        if ($this->customer !== null) {
            $carts = $this->entityManager->getRepository('AlpixelShopBundle:Cart')->findSavedCarts($this->customer);
        }

        return $carts;
    }

    /**
     * @param Cart $cart
     * @param Customer|null $customer
     * @return bool
     */
    public function deleteCart(Cart $cart, Customer $customer = null)
    {
        $isDeleted = false;
        if ($this->cartBelongsToCustomer($cart, $customer) && $cart->getOrder() === null) {
            $carts = $this->getSavedCarts();
            // If the customer have only one cart we disallow to delete him, to avoid multiple requests
            if (count($carts) > 1) {
                $this->entityManager->remove($cart);
                $this->entityManager->flush();
                $isDeleted = true;
            }
        }

        return $isDeleted;
    }

    /**
     * @param Cart $cart
     * @param Customer|null $customer
     * @return bool
     */
    public function cartBelongsToCustomer(Cart $cart, Customer $customer = null)
    {
        if ($customer === null) {
            $customer = $this->getCustomer();
        }

        return ($cart->getCustomer() === $customer);
    }
}
