<?php

namespace Alpixel\Bundle\ShopBundle\Helper\Cart;

use Alpixel\Bundle\ShopBundle\Entity\Cart;
use Alpixel\Bundle\ShopBundle\Entity\Product;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * Check validity of cart.
 */
class CartValidity
{
    protected $configuration;
    protected $dispatcher;

    public function __construct($configuration)
    {
        $this->configuration = $configuration;
        $this->dispatcher = new EventDispatcher();
    }

    protected function getConfigurationStockStrategy()
    {
        return $this->configuration['strategy'];
    }

    public function stockIsAvailableByStrategy(Product $product, $quantity = 0)
    {
        $quantity = (int) $quantity;
        if ($quantity <= 0) {
            return;
        }

        switch ($this->getConfigurationStockStrategy()) {
            case 'soft':
                return true;
            case 'tolerant':
                return ($product->getQuantity() > 0) ? true : false;
            case 'strict':
                return ($product->getQuantity() >= $quantity) ? true : false;
        }
    }

    public function checkValidityStockInCart(Cart $cart)
    {
        $cartProducts = $cart->getCartProducts();
        if ($cartProducts->count() <= 0) { // If the number of items in cart <= 0
            return false;
        }

        $invalids = [];
        foreach ($cartProducts as $cartProduct) {
            if ($this->stockIsAvailableByStrategy($cartProduct->getProduct(), $cartProduct->getQuantity()) === false) {
                $invalids[] = $cartProduct;
            }
        }

        return $invalids;
    }
}
