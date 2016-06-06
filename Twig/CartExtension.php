<?php

namespace Alpixel\Bundle\ShopBundle\Twig;

use Alpixel\Bundle\ShopBundle\Helper\Cart\CartManager;

/**
 * @author Benjamin HUBERT <benjamin@alpixel.fr>
 */
class CartExtension extends \Twig_Extension implements \Twig_Extension_GlobalsInterface
{
    protected $cartManager;

    public function __construct(CartManager $cartManager)
    {
        $this->cartManager = $cartManager;
    }

    /**
     * @return array
     */
    public function getGlobals()
    {
        return [
            'cart' => $this->cartManager->getCurrentCart(),
        ];
    }

    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('cart_discount', [$this->cartManager, 'getCartDiscount']),
            new \Twig_SimpleFunction('cart_total', [$this->cartManager, 'getTotal']),
        ];
    }

    public function getName()
    {
        return 'alpixel_shop.extension.cart_extension';
    }
}
