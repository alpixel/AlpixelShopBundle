<?php

namespace Alpixel\Bundle\ShopBundle\Entity;

use Alpixel\Bundle\UserBundle\Entity\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * Customer.
 *
 * @ORM\MappedSuperclass()
 */
class Customer extends BaseUser
{
    use \Gedmo\Timestampable\Traits\TimestampableEntity;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="firstname", type="string", length=255, nullable=true)
     */
    protected $firstname;

    /**
     * @var string
     *
     * Todo Champ par défaut du nom et prénom de l'utilisateur appliquer les modification sur firstname lors de la V2 du client
     * @ORM\Column(name="lastname", type="string", length=255, nullable=false)
     */
    protected $lastname;

    /**
     * @ORM\ManyToOne(targetEntity="\Alpixel\Bundle\ShopBundle\Entity\Currency")
     * @ORM\JoinColumn(name="currency_id", referencedColumnName="id")
     */
    protected $currency;

    public function __toString()
    {
        return $this->firstname.' '.strtoupper($this->lastname);
    }

    /**
     * Add currency.
     *
     * @param \Alpixel\Bundle\ShopBundle\Entity\Currency $currency
     *
     * @return ProductPrice
     */
    public function setCurrency(\Alpixel\Bundle\ShopBundle\Entity\Currency $currency)
    {
        $this->currency = $currency;

        return $this;
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
     * Gets the value of firstname.
     *
     * @return string
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * Gets the Todo Champ par défaut du nom et prénom de l'utilisateur appliquer les modification sur firstname lors de la V2 du client.
     *
     * @return string
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * Sets the Todo Champ par défaut du nom et prénom de l'utilisateur appliquer les modification sur firstname lors de la V2 du client.
     *
     * @param string $lastname the lastname
     *
     * @return self
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;

        return $this;
    }

    /**
     * Sets the value of firstname.
     *
     * @param string $firstname the firstname
     *
     * @return self
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;

        return $this;
    }
}
