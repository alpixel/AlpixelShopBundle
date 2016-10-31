<?php

namespace Alpixel\Bundle\ShopBundle\Helper\Order;

use Alpixel\Bundle\ShopBundle\AlpixelShopEvents;
use Alpixel\Bundle\ShopBundle\Entity\Cart;
use Alpixel\Bundle\ShopBundle\Entity\CartProduct;
use Alpixel\Bundle\ShopBundle\Entity\Currency;
use Alpixel\Bundle\ShopBundle\Entity\Customer;
use Alpixel\Bundle\ShopBundle\Entity\Order;
use Alpixel\Bundle\ShopBundle\Entity\OrderProduct;
use Alpixel\Bundle\ShopBundle\Event\OrderEvent;
use Alpixel\Bundle\ShopBundle\Helper\Cart\CartManager;
use Alpixel\Bundle\ShopBundle\Helper\Cart\PriceHelper;
use Doctrine\ORM\EntityManager;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @author Benjamin HUBERT <benjamin@alpixel.fr>
 */
class OrderManager
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $entityManager;
    /**
     * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
     */
    private $dispatcher;
    /**
     * @var
     */
    private $currency;
    /**
     * @var \Alpixel\Bundle\ShopBundle\Helper\Cart\PriceHelper
     */
    private $priceHelper;

    /**
     * OrderManager constructor.
     * @param \Alpixel\Bundle\ShopBundle\Helper\Cart\CartManager $cartManager
     * @param \Doctrine\ORM\EntityManager $entityManager
     * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $dispatcher
     * @param $defaultCurrency
     * @param \Alpixel\Bundle\ShopBundle\Helper\Cart\PriceHelper $priceHelper
     */
    public function __construct(
        CartManager $cartManager,
        EntityManager $entityManager,
        EventDispatcherInterface $dispatcher,
        $defaultCurrency,
        PriceHelper $priceHelper
    ) {
        $this->cartManager = $cartManager;
        $this->entityManager = $entityManager;
        $this->dispatcher = $dispatcher;
        $this->priceHelper = $priceHelper;
        $this->currency = $entityManager
            ->getRepository('AlpixelShopBundle:Currency')
            ->findOneByName($defaultCurrency);
    }

    /**
     * @param \Alpixel\Bundle\ShopBundle\Entity\Cart $cart
     * @param \Alpixel\Bundle\ShopBundle\Entity\Customer $customer
     * @param $extraData
     * @return \Alpixel\Bundle\ShopBundle\Entity\Order
     */
    public function processOrder(Cart $cart, Customer $customer, $extraData)
    {
        $order = $this->newOrder($cart, $customer);
        $order->setExtraData($extraData);

        $event = new OrderEvent($order);
        $this->dispatcher->dispatch(AlpixelShopEvents::ORDER_PRE_PERSIST, $event);

        $this->entityManager->persist($order);
        $this->entityManager->flush();

        $this->dispatcher->dispatch(AlpixelShopEvents::ORDER_POST_PERSIST, $event);

        return $order;
    }

    /**
     * @param \Alpixel\Bundle\ShopBundle\Entity\Cart $cart
     * @param \Alpixel\Bundle\ShopBundle\Entity\Customer $customer
     * @return \Alpixel\Bundle\ShopBundle\Entity\Order
     */
    public function newOrder(Cart $cart, Customer $customer)
    {
        $order = new Order();
        $cartProducts = $cart->getCartProducts();

        foreach ($cartProducts as $cartProduct) {
            $orderProduct = $this->newProductOrderFromCartProduct($cartProduct);
            $order->addProductOrder($orderProduct);
        }

        $order->setDiscount($this->cartManager->getCartDiscount());
        $order->setBareTotalWoTax($this->cartManager->getTotal(true, false));
        $order->setTotalWoTax($this->cartManager->getTotal(true, true));

        return $order
            ->setCart($cart)
            ->setCustomer($customer)
            ->setCurrency($this->getCurrencyOrder())
            ->setCustomerFirstname($customer->getFirstname())
            ->setCustomerLastname($customer->getLastname())
            ->setCustomerEmail($customer->getEmail());
    }

    /**
     * @param \Alpixel\Bundle\ShopBundle\Entity\CartProduct $cartProduct
     * @return \Alpixel\Bundle\ShopBundle\Entity\OrderProduct
     */
    public function newProductOrderFromCartProduct(CartProduct $cartProduct)
    {
        $product = $cartProduct->getProduct();

        $bareProductPrice = $this->priceHelper->getProductPrice($product, 1, false);
        $productPrice = $this->priceHelper->getProductPrice($product);

        if ($bareProductPrice > 0) {
            $discount = 100 - ($productPrice / $bareProductPrice * 100);
        } else {
            $discount = 0;
        }

        $orderProduct = new OrderProduct();

        return $orderProduct
            ->setDiscountPercent($discount)
            ->setProduct($cartProduct->getProduct())
            ->setQuantity($cartProduct->getQuantity())
            ->setBareUnitPrice($bareProductPrice)
            ->setUnitPrice($productPrice)
            ->setInformation($cartProduct->getProduct()->getReference());
    }

    /**
     * @param \Alpixel\Bundle\ShopBundle\Entity\Currency $currency
     */
    public function setCurrencyOrder(Currency $currency)
    {
        $this->currency = $currency;
    }

    /**
     * @return mixed
     */
    public function getCurrencyOrder()
    {
        return $this->currency;
    }
}
