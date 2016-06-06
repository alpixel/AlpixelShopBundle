<?php

namespace Alpixel\Bundle\ShopBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Cart.
 *
 * @ORM\Table(name="alpixel_shop_cart")
 * @ORM\Entity(repositoryClass="Alpixel\Bundle\ShopBundle\Repository\CartRepository")
 */
class Cart
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
     */
    private $customer;

    /**
     * @ORM\OneToMany(
     *      targetEntity="\Alpixel\Bundle\ShopBundle\Entity\CartProduct",
     *      mappedBy="cart", cascade={"remove", "persist"})
     */
    private $cartProducts;

    /**
     * @ORM\OneToOne(targetEntity="\Alpixel\Bundle\ShopBundle\Entity\Order", inversedBy="cart")
     */
    private $order;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->customer = new ArrayCollection();
        $this->cartProducts = new ArrayCollection();
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
     * Get customer.
     *
     * @return \Alpixel\Bundle\ShopBundle\Entity\Customer
     */
    public function getCustomer()
    {
        return $this->customer;
    }

    /**
     * Set customer.
     *
     * @param \Alpixel\Bundle\ShopBundle\Entity\Customer $customer
     *
     * @return Cart
     */
    public function setCustomer(\Alpixel\Bundle\ShopBundle\Entity\Customer $customer = null)
    {
        $this->customer = $customer;

        return $this;
    }

    /**
     * Add cartProduct.
     *
     * @param \Alpixel\Bundle\ShopBundle\Entity\CartProduct $cartProduct
     *
     * @return Cart
     */
    public function addCartProduct(\Alpixel\Bundle\ShopBundle\Entity\CartProduct $cartProduct)
    {
        $cartProduct->setCart($this);
        $this->cartProducts[] = $cartProduct;

        return $this;
    }

    /**
     * Remove cartProduct.
     *
     * @param \Alpixel\Bundle\ShopBundle\Entity\CartProduct $cartProduct
     */
    public function removeCartProduct(\Alpixel\Bundle\ShopBundle\Entity\CartProduct $cartProduct)
    {
        $this->cartProducts->removeElement($cartProduct);
    }

    /**
     * Get cartProducts.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function addProductCart(\Alpixel\Bundle\ShopBundle\Entity\CartProduct $cartProduct)
    {
        $cartProduct->setCart($this);
        $this->cartProducts[] = $cartProduct;

        return $this;
    }

    public function getCartProducts()
    {
        return $this->cartProducts;
    }

    public function removeProductCart(\Alpixel\Bundle\ShopBundle\Entity\CartProduct $cartProduct)
    {
        $this->productCarts->removeElement($cartProduct);
    }

    /**
     * Get order.
     *
     * @return \Alpixel\Bundle\ShopBundle\Entity\Order
     */
    public function getOrder()
    {
        return $this->order;
    }

    public function setOrder(\Alpixel\Bundle\ShopBundle\Entity\Order $order = null)
    {
        $this->order = $order;

        return $this;
    }
}
