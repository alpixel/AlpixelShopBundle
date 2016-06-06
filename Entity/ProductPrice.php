<?php

namespace Alpixel\Bundle\ShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ProductPrice.
 *
 * @ORM\Table(name="alpixel_shop_product_price")
 * @ORM\Entity(repositoryClass="Alpixel\Bundle\ShopBundle\Repository\ProductPriceRepository")
 */
class ProductPrice
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="\Alpixel\Bundle\ShopBundle\Entity\Currency")
     * @ORM\JoinColumn(name="currency_id", referencedColumnName="id")
     */
    private $currency;

    /**
     * @ORM\ManyToOne(targetEntity="\Alpixel\Bundle\ShopBundle\Entity\Product", inversedBy="productPrice")
     * @ORM\JoinColumn(name="product_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $product;

    /**
     * @var string
     *
     * @ORM\Column(name="amount", type="decimal", precision=6, scale=2)
     */
    private $amount;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->currency = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set amount.
     *
     * @param string $amount
     *
     * @return ProductPrice
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Get amount.
     *
     * @return string
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Get currency.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * Set product.
     *
     * @param \Alpixel\Bundle\ShopBundle\Entity\Product $product
     *
     * @return ProductPrice
     */
    public function setProduct(\Alpixel\Bundle\ShopBundle\Entity\Product $product = null)
    {
        $this->product = $product;

        return $this;
    }

    /**
     * Get product.
     *
     * @return \Alpixel\Bundle\ShopBundle\Entity\Product
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * Sets the value of currency.
     *
     * @param mixed $currency the currency
     *
     * @return self
     */
    public function setCurrency(\Alpixel\Bundle\ShopBundle\Entity\Currency $currency)
    {
        $this->currency = $currency;

        return $this;
    }
}
