<?php

namespace Alpixel\Bundle\ShopBundle\Tests\Unit\Helper\Cart;

use Alpixel\Bundle\ShopBundle\Entity\Cart;
use Alpixel\Bundle\ShopBundle\Helper\Cart\SessionCart;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * @author Alexis BUSSIERES <alexis@alpixel.fr>
 */
class SessionCartTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Session
     */
    private $session;

    public function setUp()
    {
        $this->session = new Session(new MockArraySessionStorage());
    }

    public function testClassConstant()
    {
        $this->assertEquals('alpixel_shop_current_cart', SessionCart::SESSION_CART_ID);
    }

    public function testCurrentCartSessionExist()
    {
        $this->assertFalse($this->getSessionCart()->currentCartSessionExist(SessionCart::SESSION_CART_ID));
        $this->session->set(SessionCart::SESSION_CART_ID, true);
        $this->assertTrue($this->getSessionCart()->currentCartSessionExist(SessionCart::SESSION_CART_ID));
    }

    public function testRemoveCurrent()
    {
        $this->assertTrue($this->getSessionCart()->removeCurrent(SessionCart::SESSION_CART_ID) instanceof SessionCart);
        $this->session->set(SessionCart::SESSION_CART_ID, true);
        $this->assertTrue($this->getSessionCart()->removeCurrent(SessionCart::SESSION_CART_ID) instanceof SessionCart);
        $this->assertNull($this->session->get(SessionCart::SESSION_CART_ID));
    }

    public function testSetCurrent()
    {
        $this->getSessionCart();
        $this->assertNull($this->session->get(SessionCart::SESSION_CART_ID));

        $cart = $this->createMock(Cart::class);
        $cart->method('getId')->willReturn(1);
        $this->assertTrue($this->getSessionCart()->setCurrent($cart) instanceof SessionCart);
        $this->assertEquals(1, $this->session->get(SessionCart::SESSION_CART_ID));
    }

    public function testGetCurrent()
    {
        $this->getSessionCart();
        $this->assertNull($this->session->get(SessionCart::SESSION_CART_ID));
        $cart = $this->createMock(Cart::class);
        $cart->method('getId')->willReturn(1);
        $this->getSessionCart()->setCurrent($cart);
        $this->assertEquals(1, $this->getSessionCart()->getCurrent());
    }

    private function getSessionCart()
    {
        return new SessionCart($this->session);
    }
}
