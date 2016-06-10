<?php

namespace Alpixel\Bundle\ShopBundle\Tests\Unit\Helper\Cart;

use Alpixel\Bundle\ShopBundle\Entity\Currency;
use Alpixel\Bundle\ShopBundle\Entity\Customer;
use Alpixel\Bundle\ShopBundle\Entity\Product;
use Alpixel\Bundle\ShopBundle\Entity\ProductPrice;
use Alpixel\Bundle\ShopBundle\Helper\Cart\PriceHelper;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;

/**
 * @author Alexis BUSSIERES <alexis@alpixel.fr>
 */
class PriceHelperTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var TokenStorage
     */
    private $tokenStorage;

    /**
     * @var AuthorizationChecker
     */
    private $authorizationChecker;

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @var string
     */
    private $currency;

    /**
     * @var Product
     */
    private $product;

    public function setUp()
    {
        $this->tokenStorage = $this->createMock(TokenStorage::class);
        $this->authorizationChecker = $this->createMock(AuthorizationChecker::class);
        $this->entityManager = $this->createMock(EntityManager::class);
        $this->dispatcher = $this->createMock(EventDispatcherInterface::class);
        $this->currency = 'CHF';
    }

    public function testApplyDiscount()
    {
        $priceHelper = $this->getPriceHelperMockedConstructor();

        $this->assertEquals(0, $priceHelper->applyDiscount(0, 0));
        $this->assertEquals(0, $priceHelper->applyDiscount(0, 10));
        $this->assertEquals(100, $priceHelper->applyDiscount(100, 0));
        $this->assertEquals(90, $priceHelper->applyDiscount(100, 10));
        $this->assertEquals(180, $priceHelper->applyDiscount(200, 10));
    }

    public function testGetDiscount()
    {
        $priceHelper = $this->getPriceHelperMockedConstructor();

        $this->assertEquals(0, $priceHelper->getDiscountAmount(0, 0));
        $this->assertEquals(0, $priceHelper->getDiscountAmount(0, 10));
        $this->assertEquals(0, $priceHelper->getDiscountAmount(100, 0));
        $this->assertEquals(10, $priceHelper->getDiscountAmount(100, 10));
        $this->assertEquals(20, $priceHelper->getDiscountAmount(200, 10));
    }

    public function testGetProductPrice()
    {
        $this->configurePriceHelper(-10);
        $priceHelper = $this->getPriceHelperMockedConstructor();

        $this->assertEquals(-10, $priceHelper->getProductPrice($this->product));
        $this->assertEquals(-0, $priceHelper->getProductPrice($this->product, 0));
        $this->assertEquals(-100, $priceHelper->getProductPrice($this->product, 10));

        $this->configurePriceHelper(0);
        $priceHelper = $this->getPriceHelperMockedConstructor();

        $this->assertEquals(0, $priceHelper->getProductPrice($this->product));
        $this->assertEquals(0, $priceHelper->getProductPrice($this->product, 0));
        $this->assertEquals(0, $priceHelper->getProductPrice($this->product, 10));

        $this->configurePriceHelper(100);
        $priceHelper = $this->getPriceHelperMockedConstructor();

        $this->assertEquals(0, $priceHelper->getProductPrice($this->product, 0));
        $this->assertEquals(100, $priceHelper->getProductPrice($this->product));
        $this->assertEquals(200, $priceHelper->getProductPrice($this->product, 2));

        $this->configurePriceHelper(0, true);
        $priceHelper = $this->getPriceHelperMockedConstructor();

        $this->assertEquals(0, $priceHelper->getProductPrice($this->product));
        $this->assertEquals(0, $priceHelper->getProductPrice($this->product, 0));
        $this->assertEquals(0, $priceHelper->getProductPrice($this->product, 10));

        $this->configurePriceHelper(100, true);
        $priceHelper = $this->getPriceHelperMockedConstructor();

        $this->assertEquals(0, $priceHelper->getProductPrice($this->product, 0));
        $this->assertEquals(100, $priceHelper->getProductPrice($this->product));
        $this->assertEquals(200, $priceHelper->getProductPrice($this->product, 2));
    }

    /**
     * @param $amount float|integer Set the amount of
     * @param bool $user
     */
    private function configurePriceHelper($amount, $user = false)
    {
        $productPrice = $this->createMock(ProductPrice::class);
        $productPrice->method('getAmount')
            ->willReturn($amount);

        $this->product = $this->createMock(Product::class);
        $this->product->method('getPrice')
            ->willReturn($productPrice);

        $currency = $this->createMock(Currency::class);

        $customer = $token = $this->createMock(Customer::class);
        $customer->method('getCurrency')
            ->willReturn($currency);

        $token = $this->createMock(TokenInterface::class);
        $token->method('isAuthenticated')
            ->willReturn(true);
        $token->method('getUser')
            ->willReturn($customer);

        $authentication = $this->createMock(AuthenticationManagerInterface::class);
        $accessDecision = $this->createMock(AccessDecisionManagerInterface::class);
        $accessDecision->method('decide')
            ->willReturn(true);

        $repository = $this
            ->getMockBuilder(EntityRepository::class)
            ->disableOriginalConstructor()
            ->setMethods(array('findOneByName'))
            ->getMock();

        $repository->method('findOneByName')
            ->willReturn($currency);

        $this->entityManager = $this->createMock(EntityManager::class);
        $this->entityManager->method('getRepository')
            ->willReturn($repository);

        $newToken = new TokenStorage();
        $newToken->setToken($token);

        if ($user) {
            $this->tokenStorage = $newToken;
        }

        $this->authorizationChecker = new AuthorizationChecker($newToken, $authentication, $accessDecision);
    }

    private function getPriceHelperMockedConstructor() {
        return new PriceHelper(
            $this->tokenStorage,
            $this->authorizationChecker,
            $this->entityManager,
            $this->dispatcher,
            $this->currency
        );
    }
}
