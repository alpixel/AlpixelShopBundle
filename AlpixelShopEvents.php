<?php

namespace Alpixel\Bundle\ShopBundle;

class AlpixelShopEvents
{
    const PRODUCT_PRICE_CALCULATION = 'alpixel_shop.product.event.price_calculation';

    const CART_DISCOUNT_CALCULATION = 'alpixel_shop.cart.event.discount_calculation';
    const CART_PRE_VALIDATION = 'alpixel_shop.cart.event.pre_cart_validation';
    const CART_PROCESS = 'alpixel_shop.cart.event.process_for_order';

    const ORDER_PRE_PERSIST = 'alpixel_shop.order.event.pre_persist';
    const ORDER_POST_PERSIST = 'alpixel_shop.order.event.post_persist';
}
