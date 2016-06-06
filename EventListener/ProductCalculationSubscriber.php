<?php

namespace Alpixel\Bundle\ShopBundle\EventListener;

use Alpixel\Bundle\ShopBundle\AlpixelShopEvents;
use Alpixel\Bundle\ShopBundle\Event\ProductCalculationEvent;
use Alpixel\Bundle\ShopBundle\Helper\Cart\PriceHelper;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ProductCalculationSubscriber implements EventSubscriberInterface
{
    protected $priceHelper;

    public function __construct(PriceHelper $priceHelper)
    {
        $this->priceHelper = $priceHelper;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            AlpixelShopEvents::PRODUCT_PRICE_CALCULATION => 'onPriceCalculation',
        ];
    }

    public function onPriceCalculation(ProductCalculationEvent $event)
    {
    }
}
