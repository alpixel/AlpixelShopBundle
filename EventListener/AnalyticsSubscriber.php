<?php


namespace Alpixel\Bundle\ShopBundle\EventListener;

use Alpixel\Bundle\ShopBundle\AlpixelShopEvents;
use Alpixel\Bundle\ShopBundle\Entity\OrderProduct;
use Alpixel\Bundle\ShopBundle\Event\CartEvent;
use Alpixel\Bundle\ShopBundle\Event\CartProductEvent;
use Alpixel\Bundle\ShopBundle\Event\OrderEvent;
use Happyr\GoogleAnalyticsBundle\Service\Tracker;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;


/**
 * @author Benjamin HUBERT <benjamin@alpixel.fr>
 */
class AnalyticsSubscriber implements EventSubscriberInterface
{
    /**
     * @var \Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface
     */
    protected $tokenStorage;
    /**
     * @var \Happyr\GoogleAnalyticsBundle\Service\Tracker
     */
    protected $tracker;

    /**
     * AnalyticsSubscriber constructor.
     * @param \Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface $tokenStorage
     * @param \Happyr\GoogleAnalyticsBundle\Service\Tracker $tracker
     */
    public function __construct(TokenStorageInterface $tokenStorage, Tracker $tracker)
    {
        $this->tokenStorage = $tokenStorage;
        $this->tracker = $tracker;
    }

    /**
     * @param \Alpixel\Bundle\ShopBundle\Event\CartProductEvent $event
     */
    public function onCartProductPreRemove(CartProductEvent $event)
    {
        $product = $event->getCartProduct()->getProduct();

        $data = [
            'ec' => 'E-Commerce',
            'ea' => 'Suppression du panier',
            'el' => sprintf('%s (%s)', $product, $product->getReference()),
            'ev' => $event->getCartProduct()->getCart()->getId(),
        ];

        $this->tracker->send($data, 'event');
    }

    /**
     * @param \Alpixel\Bundle\ShopBundle\Event\CartProductEvent $event
     */
    public function onCartProductPostAdd(CartProductEvent $event)
    {
        $product = $event->getCartProduct()->getProduct();

        $data = [
            'ec' => 'E-Commerce',
            'ea' => 'Ajout au panier',
            'el' => sprintf('%s (%s)', $product, $product->getReference()),
            'ev' => $event->getCartProduct()->getCart()->getId(),
        ];

        $this->tracker->send($data, 'event');
    }


    /**
     * @param \Alpixel\Bundle\ShopBundle\Event\CartProductEvent $event
     */
    public function onCartPreEmpty(CartEvent $event)
    {
        $cart = $event->getCart();

        $data = [
            'ec' => 'E-Commerce',
            'ea' => 'Panier abandonné',
            'el' => sprintf(
                'Panier n°%s de %s (%s)',
                $cart->getId(),
                $cart->getCustomer(),
                $cart->getCustomer()->getId()
            ),
            'ev' => $cart->getId(),
        ];

        $this->tracker->send($data, 'event');
    }


    /**
     * @param \Alpixel\Bundle\ShopBundle\Event\OrderEvent $event
     */
    public function onOrderPostPersist(OrderEvent $event)
    {
        $order = $event->getOrder();

        $data = [
            'ti' => $order->getId(),
            'tr' => $order->getTotal(),
            'tt' => 0,
            'cu' => $order->getCurrency(),
        ];

        $this->tracker->send($data, 'transaction');

        foreach ($order->getProductOrders() as $product) {
            if ($product->getProduct() === null) {
                continue;
            }

            /** @var OrderProduct $product */
            $data = [
                'ti' => $order->getId(),
                'in' => $product->getInformation(),
                'ip' => $product->getUnitPrice(),
                'iq' => $product->getQuantity(),
                'ic' => $product->getProduct()->getReference(),
                'cu' => $order->getCurrency(),
            ];

            $this->tracker->send($data, 'item');
        }
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            AlpixelShopEvents::CART_PRODUCT_PRE_REMOVE => "onCartProductPreRemove",
            AlpixelShopEvents::CART_PRODUCT_POST_ADD => "onCartProductPostAdd",
            AlpixelShopEvents::ORDER_POST_PERSIST => "onOrderPostPersist",
            AlpixelShopEvents::CART_PRE_EMPTY => "onCartPreEmpty",
        ];
    }


}
