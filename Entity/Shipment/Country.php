<?php

namespace Alpixel\Bundle\ShopBundle\Entity\Shipment;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="alpixel_shop_country")
 * @ORM\Entity()
 */
class Country
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
     * @var string
     *
     * @ORM\Column(type="string", length=100)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=3)
     */
    private $iso;

    /**
     * @var \Alpixel\Bundle\ShopBundle\Entity\Shipment\Zone
     *
     * @ORM\ManyToOne(targetEntity="Alpixel\Bundle\ShopBundle\Entity\Shipment\Zone")
     * @ORM\JoinColumn(name="zone_id", referencedColumnName="id")
     */
    private $zone;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Country
     */
    public function setName(string $name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getIso()
    {
        return $this->iso;
    }

    /**
     * @param string $iso
     * @return Country
     */
    public function setIso(string $iso)
    {
        $this->iso = strtoupper($iso);

        return $this;
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
     * @return Country
     */
    public function setZone(Zone $zone)
    {
        $this->zone = $zone;

        return $this;
    }
}
