<?php

namespace Alpixel\Bundle\ShopBundle\Tests\Unit\Helper\Cart;

use Alpixel\Bundle\ShopBundle\Helper\Cart\CartManager;
use Alpixel\Bundle\ShopBundle\Helper\Cart\CartValidity;
use Alpixel\Bundle\ShopBundle\Helper\Cart\PriceHelper;
use Alpixel\Bundle\ShopBundle\Helper\Cart\SessionCart;
use Doctrine\ORM\EntityManager;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;


/**
 * @author Alexis BUSSIERES <alexis@alpixel.fr>
 */
class CartManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var TokenStorage
     */
    private $tokenStorage;

    /**
     * @var AuthorizationChecker
     */
    private $authorizationChecker;

    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @var SessionCart
     */
    private $sessionCart;

    /**
     * @var CartValidity
     */
    private $cartValidity;

    /**
     * @var PriceHelper
     */
    private $priceHelper;

    public function setUp()
    {
        $this->entityManager = $this->createMock(EntityManager::class);
        $this->tokenStorage = $this->createMock(TokenStorage::class);
        $this->authorizationChecker = $this->createMock(AuthorizationChecker::class);
        $this->dispatcher = $this->createMock(EventDispatcherInterface::class);
        $this->sessionCart = $this->createMock(SessionCart::class);
        $this->cartValidity = $this->createMock(CartValidity::class);
        $this->priceHelper = $this->createMock(PriceHelper::class);
    }

    public function testConstruct()
    {
        $this->assertTrue($this->getCartManager() instanceof CartManager);
    }

    private function getCartManager()
    {
        return new CartManager(
            $this->entityManager,
            $this->tokenStorage,
            $this->authorizationChecker,
            $this->dispatcher,
            $this->sessionCart,
            $this->cartValidity,
            $this->priceHelper
        );
    }
}
