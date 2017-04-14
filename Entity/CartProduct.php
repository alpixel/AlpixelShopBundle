<?php

namespace Alpixel\Bundle\ShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CartProduct.
 *
 * @ORM\Table(name="alpixel_shop_cart_product")
 * @ORM\Entity(repositoryClass="Alpixel\Bundle\ShopBundle\Repository\CartProductRepository")
 */
class CartProduct
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
     * @var int
     *
     * @ORM\ManyToOne(targetEntity="\Alpixel\Bundle\ShopBundle\Entity\Cart",
     *      inversedBy="cartProducts")
     * @ORM\JoinColumn(name="cart_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $cart;

    /**
     * @ORM\ManyToOne(targetEntity="\Alpixel\Bundle\ShopBundle\Entity\Product", cascade={"persist"})
     * @ORM\JoinColumn(name="product_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $product;

    /**
     * @var int
     *
     * @ORM\Column(name="quantity", type="integer")
     */
    private $quantity;

    public function __toString()
    {
        return self::class.' #'.$this->id;
    }

    public function __clone()
    {
        $this->cart = null;
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
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
     * @return CartProduct
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function addQuantity($quantity = 0)
    {
        $quantity = (int) $quantity;
        if ($quantity > 0) {
            $this->quantity += $quantity;
        }

        return $this;
    }

    public function removeQuantity($quantity = 0)
    {
        $quantity = (int) $quantity;
        if ($quantity > 0) {
            $this->quantity -= $quantity;
        }

        return $this;
    }

    /**
     * Get cart.
     *
     * @return \Alpixel\Bundle\ShopBundle\Entity\Cart
     */
    public function getCart()
    {
        return $this->cart;
    }

    /**
     * Set cart.
     *
     * @param \Alpixel\Bundle\ShopBundle\Entity\Cart $cart
     *
     * @return CartProduct
     */
    public function setCart(\Alpixel\Bundle\ShopBundle\Entity\Cart $cart = null)
    {
        $this->cart = $cart;

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
     * Set product.
     *
     * @param \Alpixel\Bundle\ShopBundle\Entity\Product $product
     *
     * @return CartProduct
     */
    public function setProduct(\Alpixel\Bundle\ShopBundle\Entity\Product $product = null)
    {
        $this->product = $product;

        return $this;
    }
}
