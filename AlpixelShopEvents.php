<?php

namespace Alpixel\Bundle\ShopBundle;

class AlpixelShopEvents
{
    const PRODUCT_PRICE_CALCULATION = 'alpixel_shop.product.event.price_calculation';

    const CART_DISCOUNT_CALCULATION = 'alpixel_shop.cart.event.discount_calculation';
    const CART_PRE_VALIDATION = 'alpixel_shop.cart.event.pre_cart_validation';
    const CART_PROCESS = 'alpixel_shop.cart.event.process_for_order';

    const CART_PRE_MEMORIZE = 'alpixel_shop.cart.event.pre.cart_memorize';
    const CART_POST_MEMORIZE = 'alpixel_shop.cart.event.post.cart_memorize';

    const CART_CREATED = 'alpixel_shop.cart.event.cart_created';
    const CART_UPDATED = 'alpixel_shop.cart.event.cart_updated';

    const CART_PRE_EMPTY = 'alpixel_shop.cart.event.pre.cart_empty';
    const CART_POST_EMPTY = 'alpixel_shop.cart.event.post.cart_empty';

    const CART_PRODUCT_PRE_REMOVE = 'alpixel_shop.cart.event.cart_product.pre.remove';
    const CART_PRODUCT_POST_REMOVE = 'alpixel_shop.cart.event.cart_product.post.remove';
    const CART_PRODUCT_PRE_ADD = 'alpixel_shop.cart.event.cart_product.pre.add';
    const CART_PRODUCT_POST_ADD = 'alpixel_shop.cart.event.cart_product.post.add';

    const ORDER_PRE_PERSIST = 'alpixel_shop.event.order.pre_persist';
    const ORDER_POST_PERSIST = 'alpixel_shop.event.order.post_persist';
}
