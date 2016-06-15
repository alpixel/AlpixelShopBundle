<?php

namespace Alpixel\Bundle\ShopBundle\Model;

use Alpixel\Bundle\ShopBundle\Entity\Cart;

/**
 * CartInterface
 *
 * @author Alexis Bussières <alexis@alpixel.fr>
 */
interface CartInterface
{
    /**
     * Get id.
     *
     * @return int
     */
    public function getId();

    /**
     * Get customer.
     *
     * @return CustomerInterface
     */
    public function getCustomer();

    /**
     * Set customer.
     *
     * @param CustomerInterface $customer
     *
     * @return Cart
     */
    public function setCustomer(CustomerInterface $customer = null);

    /**
     * Add cartProduct.
     *
     * @param \Alpixel\Bundle\ShopBundle\Entity\CartProduct $cartProduct
     *
     * @return Cart
     */
    public function addCartProduct(\Alpixel\Bundle\ShopBundle\Entity\CartProduct $cartProduct);

    /**
     * Remove cartProduct.
     *
     * @param \Alpixel\Bundle\ShopBundle\Entity\CartProduct $cartProduct
     */
    public function removeCartProduct(\Alpixel\Bundle\ShopBundle\Entity\CartProduct $cartProduct);

    /**
     * Get cartProducts.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function addProductCart(\Alpixel\Bundle\ShopBundle\Entity\CartProduct $cartProduct);

    public function getCartProducts();

    public function removeProductCart(\Alpixel\Bundle\ShopBundle\Entity\CartProduct $cartProduct);

    /**
     * @return bool
     */
    public function hasCartProducts();

    /**
     * Get order.
     *
     * @return \Alpixel\Bundle\ShopBundle\Entity\Order
     */
    public function getOrder();

    public function setOrder(\Alpixel\Bundle\ShopBundle\Entity\Order $order = null);

    /**
     * Sets createdAt.
     *
     * @param  \DateTime $createdAt
     * @return $this
     */
    public function setCreatedAt(\DateTime $createdAt);

    /**
     * Returns createdAt.
     *
     * @return \DateTime
     */
    public function getCreatedAt();

    /**
     * Sets updatedAt.
     *
     * @param  \DateTime $updatedAt
     * @return $this
     */
    public function setUpdatedAt(\DateTime $updatedAt);

    /**
     * Returns updatedAt.
     *
     * @return \DateTime
     */
    public function getUpdatedAt();
}