<?php

namespace Alpixel\Bundle\ShopBundle\Tests\Unit\Helper\Cart;

use Alpixel\Bundle\ShopBundle\Entity\Cart;
use Alpixel\Bundle\ShopBundle\Entity\CartProduct;
use Alpixel\Bundle\ShopBundle\Entity\Product;
use Alpixel\Bundle\ShopBundle\Helper\Cart\CartValidity;
use Doctrine\Common\Collections\ArrayCollection;


/**
 * @author Alexis BUSSIERES <alexis@alpixel.fr>
 */
class CartValidityTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var array
     */
    private $configuration;

    /**
     * @var Cart
     */
    private $cart;

    public function setUp()
    {
        $this->configuration = ['strategy' => null];
        $this->cart = $this->createMock(Cart::class);
    }

    public function testExceptionConstruct()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->getCartValidity();
    }

    public function testConstruct()
    {
        $this->configuration['strategy'] = 'soft';
        $cartValidity = $this->getCartValidity();
        $this->assertInstanceOf(CartValidity::class, $cartValidity);
    }

    public function testStockIsAvailableByStrategyException()
    {
        $product = $this->createMock(Product::class);
        $this->configuration['strategy'] = 'unknown';
        $this->expectException(\InvalidArgumentException::class);
        $this->getCartValidity();
        $this->getCartValidity()->stockIsAvailableByStrategy($product, 1);
    }

    public function testStockIsAvailableByStrategy()
    {
        $this->configuration['strategy'] = 'soft';
        $product = $this->createMock(Product::class);
        $this->assertNull($this->getCartValidity()->stockIsAvailableByStrategy($product, 0));

        # Strategy soft
        $this->configuration['strategy'] = 'soft';
        $product = $this->createMock(Product::class);
        $product->method('getQuantity')->willReturn(0);
        $this->getCartValidity();
        $this->assertTrue($this->getCartValidity()->stockIsAvailableByStrategy($product, 1));

        $product = $this->createMock(Product::class);
        $product->method('getQuantity')->willReturn(1);
        $this->getCartValidity();
        $this->assertTrue($this->getCartValidity()->stockIsAvailableByStrategy($product, 1));

        # Strategy tolerant
        $this->configuration['strategy'] = 'tolerant';
        $product = $this->createMock(Product::class);
        $product->method('getQuantity')->willReturn(0);
        $this->getCartValidity();
        $this->assertFalse($this->getCartValidity()->stockIsAvailableByStrategy($product, 1));

        $product = $this->createMock(Product::class);
        $product->method('getQuantity')->willReturn(1);
        $this->assertTrue($this->getCartValidity()->stockIsAvailableByStrategy($product, 1));
        $this->assertTrue($this->getCartValidity()->stockIsAvailableByStrategy($product, 2));

        # Strategy tolerant
        $this->configuration['strategy'] = 'strict';
        $product = $this->createMock(Product::class);
        $product->method('getQuantity')->willReturn(0);
        $this->getCartValidity();
        $this->assertFalse($this->getCartValidity()->stockIsAvailableByStrategy($product, 1));

        $product = $this->createMock(Product::class);
        $product->method('getQuantity')->willReturn(2);
        $this->assertFalse($this->getCartValidity()->stockIsAvailableByStrategy($product, 3));
        $this->assertTrue($this->getCartValidity()->stockIsAvailableByStrategy($product, 2));
        $this->assertTrue($this->getCartValidity()->stockIsAvailableByStrategy($product, 1));


    }

    public function testCheckValidityStockInCart()
    {
        /*
         * Stock strategy
         *
         * "soft" (Allow the order, even if the product has not enough stock)
         * "tolerant" (Allow the order if the current stock of the product > 0)
         * "strict" (Allow the order only if the stock has enough quantities)
         */

        # Soft strategy
        $this->configureCartForCheckValidityStockInCart('soft', 0, 0);
        $this->assertEmpty($this->getCartValidity()->checkValidityStockInCart($this->cart));

        $this->configureCartForCheckValidityStockInCart('soft', 0, 1);
        $this->assertEmpty($this->getCartValidity()->checkValidityStockInCart($this->cart));

        $this->configureCartForCheckValidityStockInCart('soft', 1, 0);
        $this->assertEmpty($this->getCartValidity()->checkValidityStockInCart($this->cart));

        $this->configureCartForCheckValidityStockInCart('soft', 1, 1);
        $this->assertEmpty($this->getCartValidity()->checkValidityStockInCart($this->cart));

        $this->configureCartForCheckValidityStockInCart('soft', 1, 2);
        $this->assertEmpty($this->getCartValidity()->checkValidityStockInCart($this->cart));

        # Tolerant strategy
        $this->configureCartForCheckValidityStockInCart('tolerant', 0, 0);
        $this->assertEmpty($this->getCartValidity()->checkValidityStockInCart($this->cart));

        $this->configureCartForCheckValidityStockInCart('tolerant', 0, 1);
        $this->assertNotEmpty($this->getCartValidity()->checkValidityStockInCart($this->cart));

        $this->configureCartForCheckValidityStockInCart('tolerant', 1, 0);
        $this->assertEmpty($this->getCartValidity()->checkValidityStockInCart($this->cart));

        $this->configureCartForCheckValidityStockInCart('tolerant', 1, 1);
        $this->assertEmpty($this->getCartValidity()->checkValidityStockInCart($this->cart));

        $this->configureCartForCheckValidityStockInCart('tolerant', 1, 2);
        $this->assertEmpty($this->getCartValidity()->checkValidityStockInCart($this->cart));

        # Strict strategy
        $this->configureCartForCheckValidityStockInCart('strict', 0, 0);
        $this->assertEmpty($this->getCartValidity()->checkValidityStockInCart($this->cart));

        $this->configureCartForCheckValidityStockInCart('strict', 0, 1);
        $this->assertNotEmpty($this->getCartValidity()->checkValidityStockInCart($this->cart));

        $this->configureCartForCheckValidityStockInCart('strict', 1, 0);
        $this->assertEmpty($this->getCartValidity()->checkValidityStockInCart($this->cart));

        $this->configureCartForCheckValidityStockInCart('strict', 1, 1);
        $this->assertEmpty($this->getCartValidity()->checkValidityStockInCart($this->cart));

        $this->configureCartForCheckValidityStockInCart('strict', 1, 2);
        $this->assertNotEmpty($this->getCartValidity()->checkValidityStockInCart($this->cart));
    }

    private function configureCartForCheckValidityStockInCart($strategy, $productStockQuantity, $cartProductQuantity)
    {
        $this->configuration['strategy'] = $strategy;

        $product = $this->createMock(Product::class);
        $product->method('getQuantity')
            ->willReturn($productStockQuantity);

        $cartProduct = $this->createMock(CartProduct::class);
        $cartProduct->method('getProduct')
            ->willReturn($product);
        $cartProduct->method('getQuantity')
            ->willReturn($cartProductQuantity);

        $collection = new ArrayCollection([$cartProduct, $cartProduct]);

        $this->cart = $this->createMock(Cart::class);
        $this->cart->method('getCartProducts')
            ->willReturn($collection);
    }

    private function getCartValidity()
    {
        return new CartValidity($this->configuration);
    }
}
