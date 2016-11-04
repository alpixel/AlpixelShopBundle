<?php

namespace Alpixel\Bundle\ShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Order.
 *
 * @ORM\Table(name="alpixel_shop_order")
 * @ORM\Entity(repositoryClass="Alpixel\Bundle\ShopBundle\Repository\OrderRepository")
 */
class Order
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
     * @ORM\OneToOne(targetEntity="\Alpixel\Bundle\ShopBundle\Entity\Cart", inversedBy="order")
     * @ORM\JoinColumn(name="cart_id", referencedColumnName="id", onDelete="SET NULL")
     */
    private $cart;

    /**
     * @ORM\ManyToMany(targetEntity="\Alpixel\Bundle\ShopBundle\Entity\OrderProduct", cascade={"persist", "remove"})
     * @ORM\JoinTable(name="alpixel_shop_order_to_order_product",
     *      joinColumns={@ORM\JoinColumn(name="order_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="product_order_id", referencedColumnName="id", unique=true)}
     * )
     */
    private $productOrders;

    /**
     * @var string
     *
     * @ORM\Column(name="customer_firstname", type="string", length=255, nullable=true)
     */
    private $customerFirstname;

    /**
     * @var string
     *
     * @ORM\Column(name="customer_lastname", type="string", length=255, nullable=true)
     */
    private $customerLastname;

    /**
     * @var string
     *
     * @ORM\Column(name="customer_email", type="string", length=255, nullable=true)
     */
    private $customerEmail;

    /**
     * @var float
     *
     * @ORM\Column(name="discount", type="decimal", precision=10, scale=2)
     */
    private $discount;

    /**
     * @var float
     *
     * @ORM\Column(name="bare_total_wo_tax", type="decimal", precision=10, scale=2)
     */
    private $bareTotalWoTax;

    /**
     * @var float
     *
     * @ORM\Column(name="total_wo_tax", type="decimal", precision=10, scale=2)
     */
    private $totalWoTax;

    /**
     * @var string
     *
     * @ORM\Column(name="currency", type="string", length=10)
     */
    private $currency;

    /**
     * @var string
     *
     * @ORM\Column(name="extra_data", type="array")
     */
    private $extraData;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->productOrders = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * @return string
     */
    public function getExtraData()
    {
        return $this->extraData;
    }

    /**
     * @param string $extraData
     */
    public function setExtraData($extraData)
    {
        $this->extraData = $extraData;
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
     * Get customerFirstname.
     *
     * @return string
     */
    public function getCustomerFirstname()
    {
        return $this->customerFirstname;
    }

    /**
     * Set customerFirstname.
     *
     * @param string $customerFirstname
     *
     * @return Order
     */
    public function setCustomerFirstname($customerFirstname)
    {
        $this->customerFirstname = $customerFirstname;

        return $this;
    }

    /**
     * Get customerLastname.
     *
     * @return string
     */
    public function getCustomerLastname()
    {
        return $this->customerLastname;
    }

    /**
     * Set customerLastname.
     *
     * @param string $customerLastname
     *
     * @return Order
     */
    public function setCustomerLastname($customerLastname)
    {
        $this->customerLastname = $customerLastname;

        return $this;
    }

    /**
     * Get customerEmail.
     *
     * @return string
     */
    public function getCustomerEmail()
    {
        return $this->customerEmail;
    }

    /**
     * Set customerEmail.
     *
     * @param string $customerEmail
     *
     * @return Order
     */
    public function setCustomerEmail($customerEmail)
    {
        $this->customerEmail = $customerEmail;

        return $this;
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
     * @return Order
     */
    public function setCustomer(\Alpixel\Bundle\ShopBundle\Entity\Customer $customer = null)
    {
        $this->customer = $customer;

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
     * @return Order
     */
    public function setCart(\Alpixel\Bundle\ShopBundle\Entity\Cart $cart = null)
    {
        $this->cart = $cart;

        return $this;
    }

    /**
     * Add productOrder.
     *
     * @param \Alpixel\Bundle\ShopBundle\Entity\OrderProduct $productOrder
     *
     * @return Order
     */
    public function addProductOrder(\Alpixel\Bundle\ShopBundle\Entity\OrderProduct $productOrder)
    {
        $this->productOrders[] = $productOrder;

        return $this;
    }

    /**
     * Remove productOrder.
     *
     * @param \Alpixel\Bundle\ShopBundle\Entity\OrderProduct $productOrder
     */
    public function removeProductOrder(\Alpixel\Bundle\ShopBundle\Entity\OrderProduct $productOrder)
    {
        $this->productOrders->removeElement($productOrder);
    }

    /**
     * Get productOrders.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getProductOrders()
    {
        return $this->productOrders;
    }

    public function getCurrency()
    {
        return $this->currency;
    }

    public function setCurrency($currency)
    {
        $this->currency = $currency;

        return $this;
    }

    /**
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * @param string $comment
     */
    public function setComment($comment)
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * @return float
     */
    public function getBareTotalWoTax()
    {
        return $this->bareTotalWoTax;
    }

    /**
     * @param float $bareTotalWoTax
     */
    public function setBareTotalWoTax($bareTotalWoTax)
    {
        $this->bareTotalWoTax = $bareTotalWoTax;

        return $this;
    }

    /**
     * @return float
     */
    public function getTotalWoTax()
    {
        return $this->totalWoTax;
    }

    /**
     * @param float $totalWoTax
     */
    public function setTotalWoTax($totalWoTax)
    {
        $this->totalWoTax = $totalWoTax;

        return $this;
    }

    /**
     * @return float
     */
    public function getDiscount()
    {
        return $this->discount;
    }

    /**
     * @param float $discount
     */
    public function setDiscount($discount)
    {
        $this->discount = $discount;

        return $this;
    }
}
