<?php

namespace Alpixel\Bundle\ShopBundle\Entity\Shipment;

use Alpixel\Bundle\ShopBundle\Entity\Currency;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="alpixel_shop_shipment")
 * @ORM\Entity(repositoryClass="Alpixel\Bundle\ShopBundle\Repository\Shipment\ShipmentRepository")
 */
class Shipment
{
    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var \Alpixel\Bundle\ShopBundle\Entity\Shipment\Zone
     *
     * @ORM\ManyToOne(targetEntity="Alpixel\Bundle\ShopBundle\Entity\Shipment\Zone", fetch="EAGER")
     * @ORM\JoinColumn(name="zone_id", referencedColumnName="id")
     */
    private $zone;

    /**
     * @var \Alpixel\Bundle\ShopBundle\Entity\Shipment\Carrier
     *
     * @ORM\ManyToOne(targetEntity="Alpixel\Bundle\ShopBundle\Entity\Shipment\Carrier", fetch="EAGER")
     * @ORM\JoinColumn(name="carrier_id", referencedColumnName="id")
     */
    private $carrier;

    /**
     * @var float
     *
     * @ORM\Column(type="decimal", precision=6, scale=2)
     */
    private $price;

    /**
     * @var float
     *
     * @ORM\Column(type="decimal", precision=6, scale=2)
     */
    private $minPurchase;

    /**
     * @var float
     *
     * @ORM\Column(type="decimal", precision=6, scale=2, nullable=true)
     */
    private $maxPurchase;

    /**
     * @var \Alpixel\Bundle\ShopBundle\Entity\Currency
     *
     * @ORM\ManyToOne(targetEntity="Alpixel\Bundle\ShopBundle\Entity\Currency", fetch="EAGER")
     */
    private $currency;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return \Alpixel\Bundle\ShopBundle\Entity\Shipment\Zone
     */
    public function getZone()
    {
        return $this->zone;
    }

    /**
     * @param \Alpixel\Bundle\ShopBundle\Entity\Shipment\Zone $zone
     * @return Shipment
     */
    public function setZone(Zone $zone)
    {
        $this->zone = $zone;

        return $this;
    }

    /**
     * @return \Alpixel\Bundle\ShopBundle\Entity\Shipment\Carrier
     */
    public function getCarrier()
    {
        return $this->carrier;
    }

    /**
     * @param \Alpixel\Bundle\ShopBundle\Entity\Shipment\Carrier $carrier
     * @return Shipment
     */
    public function setCarrier(Carrier $carrier)
    {
        $this->carrier = $carrier;

        return $this;
    }

    /**
     * @return float
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @param float $price
     * @return Shipment
     */
    public function setPrice(float $price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * @return float
     */
    public function getMinPurchase()
    {
        return $this->minPurchase;
    }

    /**
     * @param float $minPurchase
     * @return Shipment
     */
    public function setMinPurchase(float $minPurchase)
    {
        $this->minPurchase = $minPurchase;

        return $this;
    }

    /**
     * @return float
     */
    public function getMaxPurchase()
    {
        return $this->maxPurchase;
    }

    /**
     * @param float $maxPurchase
     * @return Shipment
     */
    public function setMaxPurchase(float $maxPurchase)
    {
        $this->maxPurchase = $maxPurchase;

        return $this;
    }

    /**
     * @return \Alpixel\Bundle\ShopBundle\Entity\Currency|null
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * @param \Alpixel\Bundle\ShopBundle\Entity\Currency $currency
     * @return \Alpixel\Bundle\ShopBundle\Entity\Shipment\Shipment
     */
    public function setCurrency(Currency $currency)
    {
        $this->currency = $currency;

        return $this;
    }
}
