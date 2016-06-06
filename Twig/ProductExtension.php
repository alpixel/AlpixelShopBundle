<?php

namespace Alpixel\Bundle\ShopBundle\Twig;

use Alpixel\Bundle\ShopBundle\Entity\Product;
use Alpixel\Bundle\ShopBundle\Helper\Cart\PriceHelper;
use Doctrine\ORM\EntityManager;

class ProductExtension extends \Twig_Extension
{
    protected $entityManager;
    protected $configuration;
    protected $priceHelper;

    public function __construct(EntityManager $entityManager, $configuration, PriceHelper $priceHelper)
    {
        $this->entityManager = $entityManager;
        $this->configuration = $configuration;
        $this->priceHelper = $priceHelper;
    }

    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('alpixel_shop_product_amount', [$this->priceHelper, 'getProductPrice']),
            new \Twig_SimpleFunction('alpixel_shop_get_discount_amount', [$this->priceHelper, 'getDiscountAmount']),
            new \Twig_SimpleFunction('alpixel_shop_stock_available', [$this, 'getProductAvailabilityByStrategy'], [
                'is_safe'           => ['html'],
                'needs_environment' => true,
            ]),
        ];
    }

    public function getProductAvailabilityByStrategy(\Twig_Environment $twig, Product $product)
    {
        $strategy = $this->configuration['stock']['strategy'];
        $quantity = $product->getQuantity();
        $isAvailable = ($strategy === 'soft' || $strategy !== 'soft' && $quantity > 0) ? true : false;

        return $twig->render('AlpixelShopBundle:extension:stock_availability.html.twig', [
            'isAvailable' => $isAvailable,
        ]);
    }

    public function getName()
    {
        return 'alpixel_shop.extension.product_extension';
    }
}
