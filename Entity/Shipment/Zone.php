<?php

namespace Alpixel\Bundle\ShopBundle\Entity\Shipment;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="alpixel_shop_zone")
 * @ORM\Entity(repositoryClass="Alpixel\Bundle\ShopBundle\Repository\Shipment\ZoneRepository")
 */
class Zone
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
     * @return Zone
     */
    public function setName(string $name)
    {
        $this->name = $name;

        return $this;
    }
}
