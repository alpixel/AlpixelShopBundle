<?php

namespace Alpixel\Bundle\ShopBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * Product.
 *
 * @ORM\Table(name="alpixel_shop_product")
 * @ORM\Entity(repositoryClass="Alpixel\Bundle\ShopBundle\Repository\ProductRepository")
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="type", type="string")
 * @ORM\DiscriminatorMap({"product" = "Alpixel\Bundle\ShopBundle\Entity\Product"})
 */
class Product
{
    use TimestampableEntity;

    const STATUS_UNAVAILABLE = 0;
    const STATUS_AVAILABLE = 1;
    const STATUS_REPLENISHMENT = 2;
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    /**
     * @ORM\OneToMany(targetEntity="\Alpixel\Bundle\ShopBundle\Entity\ProductPrice", mappedBy="product")
     */
    private $productPrice;
    /**
     * @var int
     *
     * @ORM\Column(name="quantity", type="integer")
     */
    private $quantity;
    /**
     * @var string
     *
     * @ORM\Column(name="reference", type="string", length=255)
     */
    private $reference;
    /**
     * @ORM\ManyToMany(targetEntity="\Alpixel\Bundle\ShopBundle\Entity\Category")
     * @ORM\JoinTable(name="alpixel_shop_product_to_category",
     *      joinColumns={@ORM\JoinColumn(name="product_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="category_id", referencedColumnName="id")}
     * )
     */
    private $category;
    /**
     * @var int
     *
     * @ORM\Column(name="status", type="integer")
     */
    private $status;
    /**
     * @var int
     *
     * @ORM\Column(name="position", type="integer")
     */
    private $position;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->productPrice = new ArrayCollection();
        $this->category = new ArrayCollection();
        $this->quantity = 0;
        $this->position = 0;
    }

    public function __toString()
    {
        return $this->reference;
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
     * Get quantity.
     *
     * @return int
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * Set quantity.
     *
     * @param int $quantity
     *
     * @return Product
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function removeQuantity($quantity)
    {
        $quantity = (int) $quantity;

        if ($quantity >= 0) {
            $this->quantity = (int) $this->quantity - $quantity;
        }

        return $this;
    }

    public function addQuantity($quantity)
    {
        $quantity = (int) $quantity;

        if ($quantity >= 0) {
            $this->quantity = (int) $this->quantity + $quantity;
        }

        return $this;
    }

    /**
     * Get reference.
     *
     * @return string
     */
    public function getReference()
    {
        return $this->reference;
    }

    /**
     * Set reference.
     *
     * @param string $reference
     *
     * @return Product
     */
    public function setReference($reference)
    {
        $this->reference = $reference;

        return $this;
    }

    /**
     * Get status.
     *
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set status.
     *
     * @param int $status
     *
     * @return Product
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Add productPrice.
     *
     * @param \Alpixel\Bundle\ShopBundle\Entity\ProductPrice $productPrice
     *
     * @return Product
     */
    public function addProductPrice(\Alpixel\Bundle\ShopBundle\Entity\ProductPrice $productPrice)
    {
        $this->productPrice[] = $productPrice;

        return $this;
    }

    /**
     * Remove productPrice.
     *
     * @param \Alpixel\Bundle\ShopBundle\Entity\ProductPrice $productPrice
     */
    public function removeProductPrice(\Alpixel\Bundle\ShopBundle\Entity\ProductPrice $productPrice)
    {
        $this->productPrice->removeElement($productPrice);
    }

    public function getPrice($currency)
    {
        $productPrices = $this->getProductPrice();
        if (!empty($productPrices)) {
            foreach ($productPrices as $productPrice) {
                if ($productPrice->getCurrency() === $currency) {
                    return $productPrice;
                }
            }
        }
    }

    public function getPriceByCurrencyName($currency)
    {
        $productPrices = $this->getProductPrice();
        if (!empty($productPrices)) {
            foreach ($productPrices as $productPrice) {
                if ($productPrice->getCurrency()->getName() === $currency) {
                    return $productPrice;
                }
            }
        }
    }

    /**
     * Get productPrice.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getProductPrice()
    {
        return $this->productPrice;
    }

    /**
     * Get category.
     *
     * @return \Alpixel\Bundle\ShopBundle\Entity\Category
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Add category.
     *
     * @param \Alpixel\Bundle\ShopBundle\Entity\Category $category
     *
     * @return Product
     */
    public function addCategory(\Alpixel\Bundle\ShopBundle\Entity\Category $category)
    {
        $this->category[] = $category;

        return $this;
    }

    /**
     * Remove category.
     *
     * @param \Alpixel\Bundle\ShopBundle\Entity\Category $category
     */
    public function removeCategory(\Alpixel\Bundle\ShopBundle\Entity\Category $category)
    {
        $this->category->removeElement($category);
    }

    /**
     * Gets the value of position.
     *
     * @return int
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Sets the value of position.
     *
     * @param int $position the position
     *
     * @return self
     */
    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }
}
