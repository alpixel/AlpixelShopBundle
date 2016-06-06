<?php

namespace Alpixel\Bundle\ShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ProductOrder.
 *
 * @ORM\Table(name="alpixel_shop_order_product")
 * @ORM\Entity(repositoryClass="Alpixel\Bundle\ShopBundle\Repository\ProductOrderRepository")
 */
class OrderProduct
{
    use \Gedmo\Timestampable\Traits\TimestampableEntity;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="information", type="text")
     */
    private $information;

    /**
     * @var int
     *
     * @ORM\Column(name="quantity", type="integer")
     */
    private $quantity;

    /**
     * @var string
     *
     * @ORM\Column(name="bareUnitPrice", type="decimal", precision=10, scale=2)
     */
    private $bareUnitPrice;

    /**
     * @var string
     *
     * @ORM\Column(name="unitPrice", type="decimal", precision=10, scale=2)
     */
    private $unitPrice;

    /**
     * @var float
     *
     * @ORM\Column(name="discount_percent", type="decimal", nullable=true, precision=5, scale=2)
     */
    private $discountPercent;

    /**
     * @ORM\ManyToOne(targetEntity="Alpixel\Bundle\ShopBundle\Entity\Product")
     * @ORM\joinColumn(name="product_id", referencedColumnName="id")
     */
    private $product;

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
     * Set information.
     *
     * @param string $information
     *
     * @return OrderProduct
     */
    public function setInformation($information)
    {
        $this->information = $information;

        return $this;
    }

    /**
     * Get information.
     *
     * @return string
     */
    public function getInformation()
    {
        return $this->information;
    }

    /**
     * Set quantity.
     *
     * @param int $quantity
     *
     * @return OrderProduct
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * Get quantity.
     *
     * @return int
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * @param Alpixel\Bundle\ShopBundle\Entity\Product $product
     */
    public function setProduct(\Alpixel\Bundle\ShopBundle\Entity\Product $product)
    {
        $this->product = $product;

        return $this;
    }

    /**
     * @return Alpixel\Bundle\ShopBundle\Entity\Product
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * @return mixed
     */
    public function getDiscountPercent()
    {
        return $this->discountPercent;
    }

    /**
     * @param mixed $discountPercent
     */
    public function setDiscountPercent($discountPercent)
    {
        $this->discountPercent = $discountPercent;

        return $this;
    }

    /**
     * @return string
     */
    public function getUnitPrice()
    {
        return $this->unitPrice;
    }

    /**
     * @param string $unitPrice
     */
    public function setUnitPrice($unitPrice)
    {
        $this->unitPrice = $unitPrice;

        return $this;
    }

    /**
     * @return string
     */
    public function getBareUnitPrice()
    {
        return $this->bareUnitPrice;
    }

    /**
     * @param string $bareUnitPrice
     */
    public function setBareUnitPrice($bareUnitPrice)
    {
        $this->bareUnitPrice = $bareUnitPrice;

        return $this;
    }
}
