<?php

namespace Alpixel\Bundle\ShopBundle\EventListener;

use Alpixel\Bundle\ShopBundle\Entity\Currency;
use Alpixel\Bundle\ShopBundle\Event\CartProcessEvent;
use Alpixel\Bundle\ShopBundle\Helper\Order\OrderManager;

class OrderListener
{
    protected $orderManager;

    public function __construct(OrderManager $orderManager)
    {
        $this->orderManager = $orderManager;
    }

    public function onProcessForOrder(CartProcessEvent $event)
    {
        $cart = $event->getCart();
        $customer = $event->getCustomer();
        $extraData = $event->getExtraData();
        $currencyOrder = $customer->getCurrency();

        if ($currencyOrder instanceof Currency) {
            $this->orderManager->setCurrencyOrder($currencyOrder);
        }

        $order = $this->orderManager->processOrder($cart, $customer, $extraData);

        $event->setOrder($order);
    }
}
