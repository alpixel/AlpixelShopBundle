<?php

namespace Alpixel\Bundle\ShopBundle\Event;

use Alpixel\Bundle\ShopBundle\Entity\Currency;
use Alpixel\Bundle\ShopBundle\Entity\Customer;
use Alpixel\Bundle\ShopBundle\Entity\Product;
use Symfony\Component\EventDispatcher\Event;

class ProductCalculationEvent extends Event
{
    protected $basePrice;
    protected $price;
    protected $customer;
    protected $product;
    protected $currency;

    public function __construct(float $basePrice, Product $product, Currency $currency, Customer $customer = null)
    {
        $this->price = $this->basePrice = $basePrice;
        $this->product = $product;
        $this->currency = $currency;
        $this->customer = $customer;
    }

    /**
     * @return float
     */
    public function getPrice():float
    {
        return $this->price;
    }

    /**
     * @param float $price
     */
    public function setPrice($price)
    {
        $this->price = $price;
    }

    /**
     * @return Customer
     */
    public function getCustomer()
    {
        return $this->customer;
    }

    /**
     * @param Customer $customer
     */
    public function setCustomer($customer)
    {
        $this->customer = $customer;
    }

    /**
     * @return Product
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * @param Product $product
     */
    public function setProduct($product)
    {
        $this->product = $product;
    }

    /**
     * @return Currency
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * @param Currency $currency
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;
    }

    /**
     * @return mixed
     */
    public function getBasePrice()
    {
        return $this->basePrice;
    }

    /**
     * @param mixed $basePrice
     */
    public function setBasePrice($basePrice)
    {
        $this->basePrice = $basePrice;
    }
}
